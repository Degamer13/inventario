<?php
// database/seeders/RolePermissionSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // 1 Crear permisos
        $permissions = [
            'view dashboard',
            'create user',
            'list user',
            'show user',
            'edit user',
            'delete user',
            'create role',
            'list role',
            'show role',
            'edit role',
            'delete role',
            'create permission',
            'list permission',
            'show permission',
            'edit permission',
            'delete permission',
            'create responsable',
            'list responsable',
            'show responsable',
            'edit responsable',
            'delete responsable',
            'pdf responsable',
            'create material',
            'list material',
            'show material',
            'edit material',
            'delete material',
            'pdf material',
            'create facilidad',
            'list facilidad',
            'show facilidad',
            'edit facilidad',
            'delete facilidad',
            'pdf facilidad',
            'create maquinaria-fija',
            'list maquinaria-fija',
            'show maquinaria-fija',
            'edit maquinaria-fija',
            'delete maquinaria-fija',
            'pdf maquinaria-fija',
            'create sistema',
            'list sistema',
            'show sistema',
            'edit sistema',
            'delete sistema',
            'pdf sistema',
            'create vehiculo',
            'list vehiculo',
            'show vehiculo',
            'edit vehiculo',
            'delete vehiculo',
            'pdf vehiculo',
            'create salida',
            'list salida',
            'show salida',
            'edit salida',
            'delete salida',
            'pdf salida',
            'list detalle-salida',
            'show detalle-salida',
            'edit detalle-salida',
            'delete detalle-salida',
            'pdf detalle-salida'







        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2 Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole  = Role::firstOrCreate(['name' => 'user']);

        // 3 Asignar permisos a los roles
        $adminRole->syncPermissions($permissions); // Admin tiene todos
       $userRole->syncPermissions([
    'view dashboard',
    'list responsable',
    'list material',
    'list facilidad',
    'list maquinaria-fija',
    'list sistema',
    'list vehiculo',
    'show responsable',
    'show material',
    'show facilidad',
    'show maquinaria-fija',
    'show sistema',
    'show vehiculo',
]);



        $this->command->info('Roles y permisos creados correctamente.');
    }
}
