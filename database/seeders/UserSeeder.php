<?php

// database/seeders/UserSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Crear usuario admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password123')
            ]
        );

        // Asignar rol admin
        $admin->assignRole('admin');

        // Crear usuario normal
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Usuario Normal',
                'password' => Hash::make('password123')
            ]
        );

        // Asignar rol user
        $user->assignRole('user');

        // Crear usuario planificador (aprueba/rechaza requisiciones)
        $planificador = User::firstOrCreate(
            ['email' => 'planificador@example.com'],
            [
                'name' => 'Planificador',
                'password' => Hash::make('password123')
            ]
        );

        // Asignar rol planificador
        $planificador->assignRole('planificador');

        // Crear usuario de procura (edita requisiciones, verifica contra
        // inventario y anota observaciones de lo que hay y no hay)
        $procura = User::firstOrCreate(
            ['email' => 'procura@example.com'],
            [
                'name' => 'Usuario de Procura',
                'password' => Hash::make('password123')
            ]
        );

        // Asignar rol procura
        $procura->assignRole('procura');

        $this->command->info('Usuarios creados y roles asignados correctamente.');
    }
}
