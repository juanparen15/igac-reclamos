<?php

// namespace App\Models;

// use Filament\Models\Contracts\FilamentUser;
// use Filament\Panel;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
// use Spatie\Permission\Traits\HasRoles;

// class User extends Authenticatable implements FilamentUser
// {
//     use HasFactory, Notifiable, HasRoles;

//     protected $fillable = [
//         'name',
//         'tipo_documento',
//         'numero_documento',
//         'email',
//         'password',
//     ];

//     protected $hidden = [
//         'password',
//         'remember_token',
//     ];

//     protected $casts = [
//         'email_verified_at' => 'datetime',
//         'password' => 'hashed',
//     ];

//     public function canAccessPanel(Panel $panel): bool
//     {
//         if ($panel->getId() === 'admin') {
//             return $this->hasAnyRole(['admin', 'funcionario']);
//         }

//         // Para el panel ciudadano, permitir acceso a usuarios con rol ciudadano
//         // o usuarios recién registrados (que aún no tienen rol)
//         if ($panel->getId() === 'ciudadano') {
//             return $this->hasRole('ciudadano') || $this->roles->isEmpty();
//         }

//         return false;
//     }

//     public function ciudadano()
//     {
//         return $this->hasOne(Ciudadano::class);
//     }

//      public function getTipoDocumentoLabelAttribute(): string
//     {
//         return match ($this->tipo_documento) {
//             'CC' => 'Cédula de Ciudadanía',
//             'CE' => 'Cédula de Extranjería',
//             'TI' => 'Tarjeta de Identidad',
//             'PAS' => 'Pasaporte',
//             'NIT' => 'NIT',
//             default => $this->tipo_documento,
//         };
//     }
// }


namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo_documento',
        'numero_documento',
        'avatar',
        'active',
        'notes'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasAnyRole(['admin', 'funcionario']);
        }

        if ($panel->getId() === 'ciudadano') {
            return $this->hasRole('ciudadano');
        }

        return false;
    }

    public function ciudadano()
    {
        return $this->hasOne(Ciudadano::class);
    }

    public function getTipoDocumentoLabelAttribute(): string
    {
        return match ($this->tipo_documento) {
            'CC' => 'Cédula de Ciudadanía',
            'CE' => 'Cédula de Extranjería',
            'TI' => 'Tarjeta de Identidad',
            'PAS' => 'Pasaporte',
            'NIT' => 'NIT',
            default => $this->tipo_documento,
        };
    }
}
