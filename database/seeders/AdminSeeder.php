<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'first_name' => 'Administrador',
                'last_name' => 'SafeResidence',
                'cedula' => '0000000000',
                'phone' => '3000000000',
                'email' => 'admin@saferesidence.com',
                'password' => Hash::make('Admin@12345'),
            ]
        );

        $admin->assignRole('Administrador');
    }
}
