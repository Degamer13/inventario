<?php

// database/seeders/RequisicionModuleSeeder.php
// Seeder idempotente: puede ejecutarse sobre una base de datos ya existente.
// Ejecutar con:  php artisan db:seed --class=RequisicionModuleSeeder

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RequisicionModuleSeeder extends Seeder
{
    public function run()
    {
        // 1) Permisos del módulo de requisiciones
        $permisos = [
            'create requisicion',
            'list requisicion',
            'show requisicion',
            'edit requisicion',
            'delete requisicion',
            'verify requisicion',
            'approve requisicion',
            'reject requisicion',
            'pdf requisicion',
            'entregar requisicion',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        // 2) Administrador: recibe las requisiciones y las verifica contra inventario
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo($permisos);

        // 3) Usuario normal: registra sus requisiciones (lo único que registran)
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $user->givePermissionTo([
            'create requisicion',
            'list requisicion',
            'show requisicion',
            'edit requisicion',
            'pdf requisicion',
        ]);

        // Los usuarios normales no ven el módulo de Responsables
        $user->revokePermissionTo(['list responsable', 'show responsable']);

        // 4) Planificador: se encarga de aprobar y rechazar las requisiciones
        $planificador = Role::firstOrCreate(['name' => 'planificador', 'guard_name' => 'web']);

        $permisosPlanificador = [
            'view dashboard',
            'list material',
            'show material',
            'create requisicion',
            'list requisicion',
            'show requisicion',
            'edit requisicion',
            'pdf requisicion',
            'verify requisicion',
            'approve requisicion',
            'reject requisicion',
        ];

        // Solo se sincronizan los permisos que ya existen en la base de datos,
        // para que el seeder funcione también sobre bases recién creadas.
        $existentes = Permission::whereIn('name', $permisosPlanificador)->pluck('name')->all();
        $planificador->syncPermissions(array_intersect($permisosPlanificador, $existentes));

        // 5) Usuario planificador de ejemplo
        $planUser = User::firstOrCreate(
            ['email' => 'planificador@example.com'],
            [
                'name' => 'Planificador',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $planUser->assignRole('planificador');

        // 6) Analista / Coordinador de PROCURA:
        //     edita las requisiciones, trabaja con los materiales existentes
        //     del inventario y anota qué HAY y qué NO HAY por renglón.
        $procura = Role::firstOrCreate(['name' => 'procura', 'guard_name' => 'web']);

        $permisosProcura = [
            'view dashboard',
            'list material',
            'show material',
            'create requisicion',
            'list requisicion',
            'show requisicion',
            'edit requisicion',
            'pdf requisicion',
            'verify requisicion',
            'entregar requisicion',
        ];

        // Solo se sincronizan los permisos que ya existen en la base de datos.
        $existentesProcura = Permission::whereIn('name', $permisosProcura)->pluck('name')->all();
        $procura->syncPermissions(array_intersect($permisosProcura, $existentesProcura));

        // 7) Usuario de procura de ejemplo
        $procUser = User::firstOrCreate(
            ['email' => 'procura@example.com'],
            [
                'name' => 'Usuario de Procura',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $procUser->assignRole('procura');

        $this->command->info('Módulo de Requisiciones: permisos, roles "planificador" y "procura" y usuarios creados correctamente.');
    }
}
