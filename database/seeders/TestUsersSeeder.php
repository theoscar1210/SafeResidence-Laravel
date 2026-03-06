<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'data' => [
                    'first_name' => 'Oscar',
                    'last_name' => 'Fernandez',
                    'cedula' => '1152445689',
                    'phone' => '3172957831',
                    'username' => 'vigilante1',
                    'email' => 'vigilante@saferesidence.com',
                    'password' => Hash::make('Vigilante@123'),
                ],
                'role' => 'Vigilante',
            ],
            [
                'data' => [
                    'first_name' => 'Emma',
                    'last_name' => 'Naranjo',
                    'cedula' => '2222222',
                    'phone' => '3125555555',
                    'username' => 'propietario1',
                    'email' => 'propietario@saferesidence.com',
                    'password' => Hash::make('Propietario@123'),
                ],
                'role' => 'Propietario',
            ],
            [
                'data' => [
                    'first_name' => 'Maria',
                    'last_name' => 'Fernandez',
                    'cedula' => '123654789',
                    'phone' => '3176598745',
                    'username' => 'residente1',
                    'email' => 'residente@saferesidence.com',
                    'password' => Hash::make('Residente@123'),
                ],
                'role' => 'Residente',
            ],
        ];

        foreach ($users as $item) {
            $user = User::firstOrCreate(
                ['username' => $item['data']['username']],
                $item['data']
            );
            $user->assignRole($item['role']);
        }
    }
}
