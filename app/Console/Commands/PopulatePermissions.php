<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PopulatePermissions extends Command
{
    protected $signature = 'permissions:populate';
    protected $description = 'Populate basic permissions and roles';

    public function handle()
    {
        $this->info('Poblando permisos básicos...');

        // Permisos básicos
        $permissions = [
            // Gestión de usuarios
            'gestionar_usuarios',
            'ver_usuarios',
            'crear_usuarios',
            'editar_usuarios',
            'desactivar_usuarios',

            // Gestión de reclamos
            'gestionar_reclamos',
            'ver_reclamos',
            'crear_reclamos',
            'editar_reclamos',
            'asignar_reclamos',
            'resolver_reclamos',

            // Gestión de roles y permisos
            'gestionar_roles',
            'ver_roles',
            'crear_roles',
            'editar_roles',
            'eliminar_roles',

            // Configuración del sistema
            'gestionar_configuracion',
            'ver_reportes',
            'generar_reportes',

            // Gestión de tipos de reclamo
            'gestionar_tipos_reclamo',
            'ver_tipos_reclamo',
            'crear_tipos_reclamo',
            'editar_tipos_reclamo',

            // Gestión de campos dinámicos
            'gestionar_campos_dinamicos',
            'ver_campos_dinamicos',
            'crear_campos_dinamicos',
            'editar_campos_dinamicos',
            'eliminar_campos_dinamicos',

            // Gestión de ciudadanos
            'gestionar_ciudadanos',
            'ver_ciudadanos',
            'editar_ciudadanos',
            'eliminar_ciudadanos',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->info("Permiso creado: {$permission}");
        }

        // Crear roles básicos si no existen
        $roles = [
            'admin' => $permissions, // Admin tiene todos los permisos
            // 'funcionario' => [
            //     'ver_reclamos',
            //     'crear_reclamos',
            //     'editar_reclamos',
            //     'asignar_reclamos',
            //     'resolver_reclamos',
            //     'ver_usuarios',
            //     'ver_reportes',
            // ],
            'funcionario' => $permissions,
            'ciudadano' => [
                'crear_reclamos',
                'ver_reclamos', // Solo sus propios reclamos
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
            $this->info("Rol configurado: {$roleName} con " . count($rolePermissions) . " permisos");
        }

        $this->info('¡Permisos y roles poblados exitosamente!');
        return 0;
    }
}
