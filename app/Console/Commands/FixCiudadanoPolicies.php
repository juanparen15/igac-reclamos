<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixCiudadanoPolicies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'policies:arreglar-ciudadano-reclamo
                            {--backup : Crear copia de seguridad de las políticas originales.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reparar CiudadanoPolicy y ReclamoPolicy para usar lógica de negocios en lugar de permisos Shield';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Arreglando políticas Ciudadano y Reclamo...');
        $this->newLine();

        $policies = [
            'CiudadanoPolicy' => $this->getCiudadanoPolicyContent(),
            'ReclamoPolicy' => $this->getReclamoPolicyContent(),
        ];

        foreach ($policies as $policyName => $content) {
            $path = app_path("Policies/{$policyName}.php");

            // Crear backup si se solicita
            if ($this->option('backup')) {
                $this->createBackup($path, $policyName);
            }

            // Escribir la nueva política
            File::put($path, $content);

            $this->info("{$policyName} arreglado exitosamente");
        }

        $this->newLine();
        $this->info('Todas las políticas han sido arregladas.!');
        $this->newLine();
        $this->warn('Recuerda correr: php artisan optimize:clear');
        
        return Command::SUCCESS;
    }

    /**
     * Create backup of original policy
     */
    protected function createBackup(string $path, string $policyName): void
    {
        if (File::exists($path)) {
            $backupPath = app_path("Policies/Backups/{$policyName}.backup.php");
            
            // Crear directorio de backups si no existe
            if (!File::exists(app_path('Policies/Backups'))) {
                File::makeDirectory(app_path('Policies/Backups'), 0755, true);
            }

            File::copy($path, $backupPath);
            $this->line("📦 Backup created: {$backupPath}");
        }
    }

    /**
     * Get CiudadanoPolicy content
     */
    protected function getCiudadanoPolicyContent(): string
    {
        return <<<'PHP'
<?php

namespace App\Policies;

use App\Models\Ciudadano;
use App\Models\User;

class CiudadanoPolicy
{
    /**
     * Ciudadanos pueden ver el listado (solo verán el suyo por el filtro en Resource)
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['ciudadano', 'super_admin', 'funcionario']);
    }

    /**
     * Solo puede ver su propio perfil
     */
    public function view(User $user, Ciudadano $ciudadano): bool
    {
        // Ciudadanos solo ven su perfil
        if ($user->hasRole('ciudadano')) {
            return $user->id === $ciudadano->user_id;
        }

        // Admins pueden ver cualquier perfil
        return $user->hasRole(['super_admin', 'funcionario']);
    }

    /**
     * No puede crear perfiles adicionales (ya se crea automáticamente al registrarse)
     */
    public function create(User $user): bool
    {
        // Solo admins pueden crear perfiles manualmente
        return $user->hasRole(['super_admin', 'funcionario']);
    }

    /**
     * Solo puede editar su propio perfil
     */
    public function update(User $user, Ciudadano $ciudadano): bool
    {
        // Ciudadanos solo editan su perfil
        if ($user->hasRole('ciudadano')) {
            return $user->id === $ciudadano->user_id;
        }

        // Admins pueden editar cualquier perfil
        return $user->hasRole(['super_admin', 'funcionario']);
    }

    /**
     * Los ciudadanos NO pueden eliminar su perfil
     */
    public function delete(User $user, Ciudadano $ciudadano): bool
    {
        // Solo super admin puede eliminar perfiles
        return $user->hasRole('super_admin');
    }

    /**
     * No puede eliminar múltiples perfiles
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * No aplica soft deletes
     */
    public function forceDelete(User $user, Ciudadano $ciudadano): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Ciudadano $ciudadano): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restoreAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * No puede replicar perfiles
     */
    public function replicate(User $user, Ciudadano $ciudadano): bool
    {
        return false;
    }

    /**
     * No necesita reordenar
     */
    public function reorder(User $user): bool
    {
        return false;
    }
}

PHP;
    }

    /**
     * Get ReclamoPolicy content
     */
    protected function getReclamoPolicyContent(): string
    {
        return <<<'PHP'
<?php

namespace App\Policies;

use App\Models\Reclamo;
use App\Models\User;

class ReclamoPolicy
{
    /**
     * Todos pueden ver el listado (ciudadanos verán solo los suyos por el filtro)
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['ciudadano', 'super_admin', 'funcionario']);
    }

    /**
     * Puede ver el reclamo si es suyo o es admin/funcionario
     */
    public function view(User $user, Reclamo $reclamo): bool
    {
        // Ciudadano solo ve sus propios reclamos
        if ($user->hasRole('ciudadano')) {
            return $reclamo->ciudadano->user_id === $user->id;
        }

        // Admin y funcionario ven todos
        return $user->hasRole(['super_admin', 'funcionario']);
    }

    /**
     * Solo ciudadanos con perfil completo pueden crear reclamos
     */
    public function create(User $user): bool
    {
        // Ciudadanos necesitan perfil completo
        if ($user->hasRole('ciudadano')) {
            return $user->ciudadano && $user->ciudadano->perfil_completo;
        }

        // Admins y funcionarios también pueden crear
        return $user->hasRole(['super_admin', 'funcionario']);
    }

    /**
     * Ciudadanos NO pueden editar reclamos después de crearlos
     * Solo admins y funcionarios pueden editar
     */
    public function update(User $user, Reclamo $reclamo): bool
    {
        // Ciudadanos NO editan después de crear
        if ($user->hasRole('ciudadano')) {
            return false;
        }

        // Admin y funcionario pueden editar
        return $user->hasRole(['super_admin', 'funcionario']);
    }

    /**
     * Solo super admin puede eliminar reclamos
     */
    public function delete(User $user, Reclamo $reclamo): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Solo super admin puede eliminar en masa
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * No aplica soft deletes
     */
    public function forceDelete(User $user, Reclamo $reclamo): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Reclamo $reclamo): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restoreAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * No puede replicar reclamos
     */
    public function replicate(User $user, Reclamo $reclamo): bool
    {
        return false;
    }

    /**
     * No necesita reordenar
     */
    public function reorder(User $user): bool
    {
        return false;
    }
}

PHP;
    }
}