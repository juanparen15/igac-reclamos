<?php

use App\Http\Controllers\ReclamoTicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/ciudadano/login');
});

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['auth'])->group(function () {
    Route::get('/reclamo/{reclamo}/ticket', [ReclamoTicketController::class, 'imprimir'])
        ->name('reclamo.ticket.imprimir');
});