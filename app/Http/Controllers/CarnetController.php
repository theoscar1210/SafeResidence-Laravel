<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CarnetController extends Controller
{
    public function show(Request $request): Response
    {
        $user = $request->user()->load('familyMembers', 'ownedProperties', 'activeRental.property');

        $role = $user->getRoleNames()->first() ?? '';

        // Inmueble principal
        $property = null;
        if ($role === 'Propietario') {
            $prop = $user->ownedProperties->first();
            $property = $prop ? [
                'number'    => $prop->number,
                'block'     => $prop->block,
                'type'      => $prop->type,
                'full_label' => $prop->full_label,
            ] : null;
        } elseif ($role === 'Residente') {
            $rental = $user->activeRental?->property;
            $property = $rental ? [
                'number'    => $rental->number,
                'block'     => $rental->block,
                'type'      => $rental->type,
                'full_label' => $rental->full_label,
            ] : null;
        }

        // Núcleo familiar (solo propietario tiene familia registrada)
        $family = $user->familyMembers->map(fn ($f) => [
            'id'           => $f->id,
            'full_name'    => $f->full_name,
            'cedula'       => $f->cedula,
            'relationship' => $f->relationship,
            'phone'        => $f->phone,
        ])->values();

        $carnet = [
            'id'         => $user->id,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'full_name'  => $user->full_name,
            'cedula'     => $user->cedula,
            'phone'      => $user->phone,
            'username'   => $user->username,
            'email'      => $user->email,
            'role'       => $role,
            'property'   => $property,
            'family'     => $family,
        ];

        return Inertia::render('Carnet', compact('carnet'));
    }
}
