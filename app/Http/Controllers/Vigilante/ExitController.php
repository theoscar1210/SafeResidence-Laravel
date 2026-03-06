<?php

namespace App\Http\Controllers\Vigilante;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\ExitRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExitController extends Controller
{
    public function index(): Response
    {
        $inside = Entry::with('exit')
            ->active()
            ->orderByDesc('entry_at')
            ->get()
            ->map(fn($e) => [
                'id'        => $e->id,
                'full_name' => $e->full_name,
                'cedula'    => $e->cedula,
                'apartment' => $e->apartment,
                'type'      => $e->type,
                'vehicle'   => $e->vehicle,
                'entry_at'  => $e->entry_at->format('d/m/Y H:i'),
            ]);

        return Inertia::render('vigilante/Exits/Index', compact('inside'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'entry_ids'   => 'required|array|min:1',
            'entry_ids.*' => 'exists:entries,id',
            'observations' => 'nullable|string',
        ]);

        foreach ($request->entry_ids as $entryId) {
            $entry = Entry::active()->find($entryId);

            if (!$entry) continue;

            ExitRecord::create([
                'entry_id'     => $entry->id,
                'exited_at'    => now(),
                'exited_by'    => $request->user()->username,
                'observations' => $request->observations,
            ]);
        }

        $count = count($request->entry_ids);

        return redirect()->route('vigilante.exits.index')
            ->with('success', "{$count} salida(s) registrada(s) correctamente.");
    }
}
