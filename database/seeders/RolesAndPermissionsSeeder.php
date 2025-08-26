<?php

// namespace Database\Seeders;

// use Illuminate\Database\Seeder;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;
// use App\Models\User;

// class RolesAndPermissionsSeeder extends Seeder
// {
//     public function run()
//     {
//         // Reset cached roles and permissions
//         app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

//         // Crear permisos
//         $permissions = [
//             'ver_dashboard',
//             'gestionar_ciudadanos',
//             'gestionar_reclamos',
//             'gestionar_tipos_reclamos',
//             'gestionar_campos_dinamicos',
//             'gestionar_usuarios',
//             'ver_estadisticas',
//             'asignar_reclamos',
//             'resolver_reclamos',
//             'crear_reclamos',
//             'ver_mis_reclamos',
//             'gestionar_roles_y_permisos',
//         ];

//         foreach ($permissions as $permission) {
//             Permission::create(['name' => $permission]);
//         }

//         // Crear roles y asignar permisos
//         $admin = Role::create(['name' => 'admin']);
//         $admin->givePermissionTo(Permission::all());

//         $funcionario = Role::create(['name' => 'funcionario']);
//         $funcionario->givePermissionTo([
//             'ver_dashboard',
//             'gestionar_reclamos',
//             'ver_estadisticas',
//             'asignar_reclamos',
//             'resolver_reclamos',
//         ]);

//         $ciudadano = Role::create(['name' => 'ciudadano']);
//         $ciudadano->givePermissionTo([
//             'crear_reclamos',
//             'ver_mis_reclamos',
//         ]);

//         // Crear usuario admin por defecto
//         $adminUser = User::create([
//             'name' => 'Administrador IGAC',
//             'email' => 'admin@igac.gov.co',
//             'password' => bcrypt('password'),
//         ]);
//         $adminUser->assignRole('admin');
//     }
// }

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

        // Crear permisos organizados por grupos
        $permissionGroups = [
            // Gestión de usuarios
            'usuarios' => [
                'ver_usuarios' => 'Ver listado de usuarios',
                'crear_usuarios' => 'Crear nuevos usuarios',
                'editar_usuarios' => 'Editar información de usuarios',
                'eliminar_usuarios' => 'Eliminar usuarios del sistema',
            ],

            // Gestión de roles y permisos
            'roles' => [
                'ver_roles' => 'Ver roles y permisos',
                'crear_roles' => 'Crear nuevos roles',
                'editar_roles' => 'Editar roles existentes',
                'eliminar_roles' => 'Eliminar roles',
                'asignar_roles' => 'Asignar roles a usuarios',
            ],

            // Gestión de reclamos
            'reclamos' => [
                'ver_reclamos' => 'Ver listado de reclamos',
                'crear_reclamos' => 'Crear nuevos reclamos',
                'editar_reclamos' => 'Editar información de reclamos',
                'eliminar_reclamos' => 'Eliminar reclamos del sistema',
                'gestionar_reclamos' => 'Gestión completa de reclamos',
                'asignar_reclamos' => 'Asignar reclamos a funcionarios',
                'resolver_reclamos' => 'Resolver y cerrar reclamos',
            ],

            // Gestión de ciudadanos
            'ciudadanos' => [
                'ver_ciudadanos' => 'Ver información de ciudadanos',
                'editar_ciudadanos' => 'Editar perfiles de ciudadanos',
                'eliminar_ciudadanos' => 'Eliminar ciudadanos',
            ],

            // Configuración del sistema
            'configuracion' => [
                'ver_configuracion' => 'Ver configuración del sistema',
                'editar_configuracion' => 'Modificar configuración del sistema',
                'gestionar_tipos_reclamo' => 'Gestionar tipos de reclamo',
                'gestionar_campos_dinamicos' => 'Gestionar campos dinámicos',
            ],

            // Reportes y estadísticas
            'reportes' => [
                'ver_reportes' => 'Ver reportes y estadísticas',
                'exportar_datos' => 'Exportar datos del sistema',
            ],

            // Control de acceso a paneles
            'acceso' => [
                'acceso_panel_admin' => 'Acceso al panel de administración',
                'acceso_panel_ciudadano' => 'Acceso al panel de ciudadanos',
            ],
        ];

        // Crear todos los permisos
        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permission => $description) {
                Permission::updateOrCreate([
                    'name' => $permission
                ], [
                    'guard_name' => 'web'
                ]);
            }
        }

        // Crear roles y asignar permisos

        // Rol Admin - Acceso completo
        $admin = Role::updateOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // Rol Funcionario - Gestión de reclamos y ciudadanos
        $funcionario = Role::updateOrCreate(['name' => 'funcionario']);
        $funcionario->syncPermissions([
            'ver_reclamos',
            'editar_reclamos',
            'gestionar_reclamos',
            'asignar_reclamos',
            'resolver_reclamos',
            'ver_ciudadanos',
            'editar_ciudadanos',
            'ver_reportes',
            'acceso_panel_admin',
        ]);

        // Rol Ciudadano - Solo sus propios reclamos
        $ciudadano = Role::updateOrCreate(['name' => 'ciudadano']);
        $ciudadano->syncPermissions([
            'crear_reclamos',
            'ver_reclamos', // Solo los propios (se controla en el QueryBuilder)
            'acceso_panel_ciudadano',
        ]);

        // Crear usuario admin por defecto si no existe
        $adminUser = User::updateOrCreate([
            'email' => 'admin@igac.gov.co'
        ], [
            'name' => 'Administrador IGAC',
            'password' => bcrypt('admin123'),
            'tipo_documento' => 'CC',
            'numero_documento' => '12345678',
            'email_verified_at' => now(),
            'active' => true,
        ]);
        $adminUser->syncRoles(['admin']);

        // Crear usuario funcionario de ejemplo
        $funcionarioUser = User::updateOrCreate([
            'email' => 'funcionario@igac.gov.co'
        ], [
            'name' => 'Funcionario IGAC',
            'password' => bcrypt('funcionario123'),
            'tipo_documento' => 'CC',
            'numero_documento' => '87654321',
            'email_verified_at' => now(),
            'active' => true,
        ]);
        $funcionarioUser->syncRoles(['funcionario']);

        $this->command->info('✅ Roles y permisos creados exitosamente');
        $this->command->info('👨‍💼 Usuario admin: admin@igac.gov.co / admin123');
        $this->command->info('👨‍💻 Usuario funcionario: funcionario@igac.gov.co / funcionario123');
        $this->command->info('🔄 Cache de permisos limpiado');
    }
}
