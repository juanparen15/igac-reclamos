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
