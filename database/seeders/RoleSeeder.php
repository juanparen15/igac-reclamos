<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear roles básicos
        Role::firstOrCreate(
            ['name' => 'ciudadano'],
            ['guard_name' => 'web']
        );

        Role::firstOrCreate(
            ['name' => 'funcionario'],
            ['guard_name' => 'web']
        );

        $this->command->info('✅ Roles creados: ciudadano y funcionario');
    }
}