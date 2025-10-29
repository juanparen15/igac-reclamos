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
