<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            'ver_dashboard',
            'gestionar_ciudadanos',
            'gestionar_reclamos',
            'gestionar_tipos_reclamos',
            'gestionar_campos_dinamicos',
            'gestionar_usuarios',
            'ver_estadisticas',
            'asignar_reclamos',
            'resolver_reclamos',
            'crear_reclamos',
            'ver_mis_reclamos',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles y asignar permisos
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $funcionario = Role::create(['name' => 'funcionario']);
        $funcionario->givePermissionTo([
            'ver_dashboard',
            'gestionar_reclamos',
            'ver_estadisticas',
            'asignar_reclamos',
            'resolver_reclamos',
        ]);

        $ciudadano = Role::create(['name' => 'ciudadano']);
        $ciudadano->givePermissionTo([
            'crear_reclamos',
            'ver_mis_reclamos',
        ]);

        // Crear usuario admin por defecto
        $adminUser = User::create([
            'name' => 'Administrador IGAC',
            'email' => 'admin@igac.gov.co',
            'password' => bcrypt('password'),
        ]);
        $adminUser->assignRole('admin');
    }
}