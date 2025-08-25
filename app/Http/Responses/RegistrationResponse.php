<?php

// namespace App\Http\Responses;

// use Filament\Facades\Filament;
// use Filament\Http\Responses\Auth\Contracts\RegistrationResponse as RegistrationResponseContract;
// use Illuminate\Http\RedirectResponse;
// use Livewire\Features\SupportRedirects\Redirector;

// class RegistrationResponse implements RegistrationResponseContract
// {
//     public function toResponse($request): RedirectResponse | Redirector
//     {
//         // Opción 1: Redirigir al perfil para completarlo
//         // return redirect()->intended(
//         //     Filament::getPanel('ciudadano')->getUrl() . '/perfil-ciudadanos'
//         // );
        
//         // Opción 2: Redirigir al dashboard
//         return redirect()->intended(
//             Filament::getPanel('ciudadano')->getUrl()
//         );
//     }
// }