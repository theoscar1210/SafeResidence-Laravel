<?php

namespace App\Http\Controllers\Vigilante;

use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\Entry;
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
                'id'            => $e->id,
                'full_name'     => $e->full_name,
                'cedula'        => $e->cedula,
                'apartment'     => $e->apartment,
                'type'          => $e->type,
                'vehicle'       => $e->vehicle,
                'entry_at'      => $e->entry_at->format('H:i'),
                'is_inside'     => is_null($e->exit),
                'registered_by' => $e->registered_by,
            ]);

        $stats = [
            'total'       => $entries->count(),
            'inside'      => $entries->where('is_inside', true)->count(),
            'propietario' => $entries->where('type', 'propietario')->where('is_inside', true)->count(),
            'autorizado'  => $entries->where('type', 'autorizado')->where('is_inside', true)->count(),
            'visitante'   => $entries->where('type', 'visitante')->where('is_inside', true)->count(),
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
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'cedula'       => 'required|string|max:20',
            'apartment'    => 'required|string|max:20',
            'type'         => 'required|in:propietario,autorizado,visitante',
            'vehicle'      => 'required|in:automovil,camioneta,moto,bicicleta,ninguno',
            'observations' => 'nullable|string',
        ]);

        // Verificar que no tenga ingreso activo (sin salida)
        $activeEntry = Entry::where('cedula', $data['cedula'])
            ->active()
            ->first();

        if ($activeEntry) {
            return back()->withErrors([
                'cedula' => 'Esta persona ya tiene un ingreso activo sin salida registrada.',
            ]);
        }

        // Marcar autorización como usada si existe
        Authorization::active()
            ->where('cedula', $data['cedula'])
            ->first()
            ?->update(['status' => 'usado']);

        Entry::create([
            ...$data,
            'user_id'       => $request->user()->id,
            'registered_by' => $request->user()->username,
            'entry_at'      => now(),
        ]);

        return redirect()->route('vigilante.entries.index')
            ->with('success', 'Ingreso registrado correctamente.');
    }

    public function lookup(Request $request): JsonResponse
    {
        $cedula = $request->query('cedula', '');

        if (strlen($cedula) < 3) {
            return response()->json(null);
        }

        // Buscar datos de ingreso anterior
        $lastEntry = Entry::where('cedula', $cedula)
            ->latest('entry_at')
            ->first();

        // Buscar autorización activa
        $authorization = Authorization::active()
            ->where('cedula', $cedula)
            ->first();

        if (!$lastEntry && !$authorization) {
            return response()->json(null);
        }

        return response()->json([
            'first_name'    => $lastEntry?->first_name ?? $authorization?->first_name,
            'last_name'     => $lastEntry?->last_name  ?? $authorization?->last_name,
            'apartment'     => $lastEntry?->apartment,
            'type'          => $lastEntry?->type ?? ($authorization ? 'autorizado' : null),
            'authorization' => $authorization ? [
                'type'     => $authorization->type,
                'end_date' => $authorization->end_date?->format('d/m/Y H:i'),
            ] : null,
        ]);
    }
}
