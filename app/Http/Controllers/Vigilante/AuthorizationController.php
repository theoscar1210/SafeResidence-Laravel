<?php

namespace App\Http\Controllers\Vigilante;

use App\Http\Controllers\Controller;
use App\Models\Authorization;
use Inertia\Inertia;
use Inertia\Response;

class AuthorizationController extends Controller
{
    public function index(): Response
    {
        $authorizations = Authorization::with('owner')
            ->active()
            ->orderBy('end_date')
            ->get()
            ->map(fn($a) => [
                'id'         => $a->id,
                'full_name'  => $a->full_name,
                'cedula'     => $a->cedula,
                'type'       => $a->type,
                'end_date'   => $a->end_date?->format('d/m/Y H:i'),
                'owner'      => $a->owner->full_name,
                'observations' => $a->observations,
            ]);

        return Inertia::render('vigilante/Authorizations/Index', compact('authorizations'));
    }
}
