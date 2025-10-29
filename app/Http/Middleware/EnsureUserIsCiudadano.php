<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Ciudadano;
use Symfony\Component\HttpFoundation\Response;
use App\Filament\Ciudadano\Resources\PerfilCiudadanoResource;

class EnsureUserIsCiudadano
{
    // public function handle(Request $request, Closure $next)
    // {
    //     if (!auth()->check()) {
    //         return redirect()->route('filament.ciudadano.auth.login');
    //     }

    //     // Si el usuario no tiene el rol ciudadano, asignárselo
    //     if (!auth()->user()->hasRole('ciudadano')) {
    //         auth()->user()->assignRole('ciudadano');
    //     }

    //     // Asegurar que existe el registro de ciudadano
    //     if (!auth()->user()->ciudadano) {
    //         Ciudadano::create([
    //             'user_id' => auth()->user()->id,
    //             'primer_nombre' => explode(' ', auth()->user()->name)[0] ?? '',
    //             'primer_apellido' => explode(' ', auth()->user()->name)[1] ?? '',
    //         ]);
    //     }

    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Si no tiene ciudadano o el perfil no está completo
        if ($user && $user->ciudadano && !$user->ciudadano->perfil_completo) {
            // Permitir acceso solo a estas rutas
            $allowedRoutes = [
                'filament.ciudadano.resources.perfil-ciudadanos.index',
                'filament.ciudadano.resources.perfil-ciudadanos.edit',
                'filament.ciudadano.auth.logout',
            ];

            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->to(
                    PerfilCiudadanoResource::getUrl('edit', ['record' => $user->ciudadano->id])
                );
            }
        }

        return $next($request);
    }
}
