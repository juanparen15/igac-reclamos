<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Ciudadano;

class EnsureUserIsCiudadano
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('filament.ciudadano.auth.login');
        }

        // Si el usuario no tiene el rol ciudadano, asignárselo
        if (!auth()->user()->hasRole('ciudadano')) {
            auth()->user()->assignRole('ciudadano');
        }

        // Asegurar que existe el registro de ciudadano
        if (!auth()->user()->ciudadano) {
            Ciudadano::create([
                'user_id' => auth()->user()->id,
                'primer_nombre' => explode(' ', auth()->user()->name)[0] ?? '',
                'primer_apellido' => explode(' ', auth()->user()->name)[1] ?? '',
            ]);
        }

        return $next($request);
    }
}