<?php

namespace App\Observers;

use App\Models\Reclamo;
use App\Models\User;
use App\Notifications\NuevoReclamoNotification;
use App\Notifications\ReclamoActualizadoNotification;
use App\Notifications\ReclamoResueltoNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class ReclamoObserver
{
    public function created(Reclamo $reclamo)
    {
        try {
            Log::info('ReclamoObserver::created iniciado', ['reclamo_id' => $reclamo->id]);
            
            // Cargar relaciones
            $reclamo->load('ciudadano.user');
            
            if (!$reclamo->ciudadano || !$reclamo->ciudadano->user) {
                Log::error('Reclamo sin ciudadano o usuario', ['reclamo_id' => $reclamo->id]);
                return;
            }
            
            // Notificar al ciudadano
            Log::info('Enviando notificación al ciudadano', [
                'user_id' => $reclamo->ciudadano->user->id,
                'email' => $reclamo->ciudadano->user->email
            ]);
            
            $reclamo->ciudadano->user->notify(new NuevoReclamoNotification($reclamo, false));
            
            // Notificar a IGAC
            $emailIGAC = config('mail.igac_email', 'jprendon11@gmail.com');
            Log::info('Enviando notificación a IGAC', ['email' => $emailIGAC]);
            
            Notification::route('mail', $emailIGAC)
                ->notify(new NuevoReclamoNotification($reclamo, true));
                
        } catch (\Exception $e) {
            Log::error('Error en ReclamoObserver::created', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    public function updated(Reclamo $reclamo)
    {
        try {
            Log::info('ReclamoObserver::updated iniciado', [
                'reclamo_id' => $reclamo->id,
                'dirty' => $reclamo->getDirty()
            ]);
            
            // Detectar cambio de estado
            if ($reclamo->isDirty('estado')) {
                $estadoAnterior = $reclamo->getOriginal('estado');
                
                Log::info('Cambio de estado detectado', [
                    'reclamo_id' => $reclamo->id,
                    'estado_anterior' => $estadoAnterior,
                    'estado_nuevo' => $reclamo->estado
                ]);
                
                // Cargar relaciones
                $reclamo->load('ciudadano.user');
                
                if (!$reclamo->ciudadano || !$reclamo->ciudadano->user) {
                    Log::error('Reclamo sin ciudadano o usuario en update', ['reclamo_id' => $reclamo->id]);
                    return;
                }
                
                // Notificación general
                $reclamo->ciudadano->user->notify(
                    new ReclamoActualizadoNotification($reclamo, $estadoAnterior)
                );
                
                // Si se resolvió
                if ($reclamo->estado === 'resuelto' && $estadoAnterior !== 'resuelto') {
                    Log::info('Enviando notificación de resolución', ['reclamo_id' => $reclamo->id]);
                    
                    $reclamo->ciudadano->user->notify(
                        new ReclamoResueltoNotification($reclamo)
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Error en ReclamoObserver::updated', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}