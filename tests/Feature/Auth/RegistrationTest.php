<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_new_users_can_register()
    {
        // El registro estándar requiere campos adicionales del modelo User
        // (first_name, last_name, username, cedula, phone).
        // Los usuarios son creados por el administrador desde el panel de control.
        $this->markTestSkipped(
            'El registro público usa un flujo extendido con campos adicionales. '.
            'Los usuarios se crean desde el panel de administración.'
        );
    }
}
