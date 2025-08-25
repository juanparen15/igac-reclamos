<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Ciudadano;

class UserObserver
{
    public function updated(User $user)
    {
        // Si se actualizan los datos del documento en el usuario
        if ($user->isDirty(['tipo_documento', 'numero_documento'])) {
            $ciudadano = $user->ciudadano;
            
            if ($ciudadano) {
                $ciudadano->update([
                    'tipo_documento' => $user->tipo_documento,
                    'numero_documento' => $user->numero_documento,
                ]);
            }
        }
    }
}