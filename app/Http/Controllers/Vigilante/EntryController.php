<?php

namespace App\Http\Controllers\Vigilante;

use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\Entry;
use App\Models\FamilyMember;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EntryController extends Controller
{
    public function index(): Response
    {
        $entries = Entry::with('exit')
            ->today()
            ->orderByDesc('entry_at')
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'full_name' => $e->full_name,
                'cedula' => $e->cedula,
                'apartment' => $e->apartment,
                'type' => $e->type,
                'vehicle' => $e->vehicle,
                'plate' => $e->plate,
                'entry_at' => $e->entry_at->format('H:i'),
                'is_inside' => is_null($e->exit),
                'registered_by' => $e->registered_by,
            ]);

        $stats = [
            'total' => $entries->count(),
            'inside' => $entries->where('is_inside', true)->count(),
            'propietario' => $entries->whereIn('type', ['propietario', 'residente'])->where('is_inside', true)->count(),
            'autorizado' => $entries->where('type', 'autorizado')->where('is_inside', true)->count(),
            'visitante' => $entries->where('type', 'visitante')->where('is_inside', true)->count(),
        ];

        return Inertia::render('vigilante/Entries/Index', compact('entries', 'stats'));
    }

    public function create(): Response
    {
        return Inertia::render('vigilante/Entries/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'cedula' => 'required|string|max:20',
            'apartment' => 'required|string|max:20',
            'type' => 'required|in:propietario,residente,autorizado,visitante',
            'vehicle' => 'required|in:automovil,camioneta,moto,bicicleta,ninguno',
            'plate' => 'nullable|string|max:20',
            'observations' => 'nullable|string',
        ]);

        // Verificar que no tenga ingreso activo (sin salida)
        $activeEntry = Entry::where('cedula', $data['cedula'])
            ->active()
            ->first();

        if ($activeEntry) {
            return back()->withErrors([
                'active_entry' => 'Ya hay un ingreso activo para esta cédula. Registra la salida antes de permitir un nuevo ingreso.',
            ])->withInput();
        }

        // Marcar autorización como usada si existe
        Authorization::active()
            ->where('cedula', $data['cedula'])
            ->first()
            ?->update(['status' => 'usado']);

        Entry::create([
            ...$data,
            'user_id' => $request->user()->id,
            'registered_by' => $request->user()->username,
            'entry_at' => now(),
        ]);

        return redirect()->route('vigilante.entries.index')
            ->with('success', 'Ingreso registrado correctamente.');
    }

    /**
     * Lookup by cedula: prioritizes registered users (Propietario/Residente),
     * then active authorizations, then past entry history.
     */
    public function lookup(Request $request): JsonResponse
    {
        $cedula = trim($request->query('cedula', ''));

        if (strlen($cedula) < 3) {
            return response()->json(null);
        }

        $user = User::where('cedula', $cedula)->first();
        $familyMember = $user ? null : FamilyMember::with('user.ownedProperties', 'user.activeRental.property')->where('cedula', $cedula)->first();
        $authorization = Authorization::active()->where('cedula', $cedula)->first();
        $lastEntry = Entry::where('cedula', $cedula)->latest('entry_at')->first();

        if (! $user && ! $familyMember && ! $authorization && ! $lastEntry) {
            return response()->json(null);
        }

        $type = null;
        $apartment = null;
        $knownInSystem = false;
        $firstName = null;
        $lastName = null;

        if ($user) {
            $knownInSystem = true;
            $firstName = $user->first_name;
            $lastName = $user->last_name;
            $roleName = $user->getRoleNames()->first();
            $type = match ($roleName) {
                'Propietario' => 'propietario',
                'Residente' => 'residente',
                default => null,
            };
            $apartment = $user->property_number;
        } elseif ($familyMember) {
            $knownInSystem = true;
            $firstName = $familyMember->first_name;
            $lastName = $familyMember->last_name;
            $type = 'autorizado';
            $apartment = $familyMember->user->property_number;
        }

        if (! $apartment) {
            $apartment = $lastEntry?->apartment;
        }

        if (! $type) {
            $type = $authorization ? 'autorizado' : ($lastEntry?->type ?? null);
        }

        return response()->json([
            'first_name' => $firstName ?? $lastEntry?->first_name ?? $authorization?->first_name,
            'last_name' => $lastName ?? $lastEntry?->last_name ?? $authorization?->last_name,
            'apartment' => $apartment,
            'type' => $type ?? 'visitante',
            'known_in_system' => $knownInSystem,
            'authorization' => $authorization ? [
                'type' => $authorization->type,
                'end_date' => $authorization->end_date?->format('d/m/Y H:i'),
            ] : null,
        ]);
    }

    /**
     * Lookup by license plate: fills person data from most recent entry with that plate.
     */
    public function lookupByPlate(Request $request): JsonResponse
    {
        $plate = strtoupper(trim($request->query('plate', '')));

        if (strlen($plate) < 3) {
            return response()->json(null);
        }

        $lastEntry = Entry::where('plate', $plate)->latest('entry_at')->first();

        if (! $lastEntry) {
            return response()->json(null);
        }

        $cedula = $lastEntry->cedula;
        $user = User::where('cedula', $cedula)->first();
        $familyMember = $user ? null : FamilyMember::with('user')->where('cedula', $cedula)->first();
        $authorization = Authorization::active()->where('cedula', $cedula)->first();

        $type = null;
        $knownInSystem = false;
        $apartment = $lastEntry->apartment;

        if ($user) {
            $knownInSystem = true;
            $roleName = $user->getRoleNames()->first();
            $type = match ($roleName) {
                'Propietario' => 'propietario',
                'Residente' => 'residente',
                default => null,
            };
            $apartment = $user->property_number ?? $apartment;
        } elseif ($familyMember) {
            $knownInSystem = true;
            $type = 'autorizado';
            $apartment = $familyMember->user->property_number ?? $apartment;
        }

        if (! $type) {
            $type = $authorization ? 'autorizado' : $lastEntry->type;
        }

        return response()->json([
            'cedula' => $cedula,
            'first_name' => $user?->first_name ?? $familyMember?->first_name ?? $lastEntry->first_name,
            'last_name' => $user?->last_name ?? $familyMember?->last_name ?? $lastEntry->last_name,
            'apartment' => $apartment,
            'type' => $type ?? 'visitante',
            'known_in_system' => $knownInSystem,
            'authorization' => $authorization ? [
                'type' => $authorization->type,
                'end_date' => $authorization->end_date?->format('d/m/Y H:i'),
            ] : null,
        ]);
    }
}
