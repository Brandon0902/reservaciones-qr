<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'brandon09@gmail.com'],
            [
                'full_name' => 'System Admin',
                'phone'     => '555-000-0000',
                'role'      => UserRole::ADMIN, // enum
                'password'  => Hash::make('12345678'), // cámbialo luego
            ]
        );

        // Opcionales: cuentas demo para probar redirecciones/roles
        User::firstOrCreate(
            ['email' => 'validator@example.com'],
            [
                'full_name' => 'Event Validator',
                'phone'     => '555-111-1111',
                'role'      => UserRole::VALIDATOR,
                'password'  => Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'full_name' => 'Demo Customer',
                'phone'     => '555-222-2222',
                'role'      => UserRole::CUSTOMER,
                'password'  => Hash::make('password'),
            ]
        );
    }
}
