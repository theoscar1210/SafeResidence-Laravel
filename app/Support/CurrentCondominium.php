<?php

namespace App\Support;

use App\Models\Condominium;
use Illuminate\Support\Facades\App;

class CurrentCondominium
{
    /**
     * Retorna el condominio del usuario autenticado, o null si no tiene uno asignado.
     */
    public static function get(): ?Condominium
    {
        return App::has('current_condominium')
            ? App::make('current_condominium')
            : null;
    }

    /**
     * Retorna el ID del condominio actual, o null.
     */
    public static function id(): ?int
    {
        return static::get()?->id;
    }

    /**
     * Aplica scope de condominio a una query si el usuario tiene condominio asignado.
     * Uso: CurrentCondominium::scope($query)
     */
    public static function scope(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        $id = static::id();

        return $id ? $query->where('condominium_id', $id) : $query;
    }
}
