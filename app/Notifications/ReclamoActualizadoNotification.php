<?php

// namespace App\Notifications;

// use App\Models\Reclamo;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Notifications\Messages\MailMessage;
// use Illuminate\Notifications\Notification;

// class ReclamoActualizadoNotification extends Notification implements ShouldQueue
// {
//     use Queueable;

//     protected $reclamo;

//     public function __construct(Reclamo $reclamo)
//     {
//         $this->reclamo = $reclamo;
//     }

//     public function via($notifiable)
//     {
//         return ['mail'];
//     }

//     public function toMail($notifiable)
//     {
//         $ciudadano = $this->reclamo->ciudadano;
        
//         return (new MailMessage)
//             ->subject('Actualización de su Reclamo - ' . $this->reclamo->numero_ticket)
//             ->greeting('Estimado/a ' . $ciudadano->primer_nombre)
//             ->line('Su reclamo ha sido actualizado:')
//             ->line('**Número de Ticket:** ' . $this->reclamo->numero_ticket)
//             ->line('**Nuevo Estado:** ' . $this->reclamo->estado_label)
//             ->line('**Asunto:** ' . $this->reclamo->asunto)
//             ->when($this->reclamo->estado === 'resuelto', function ($message) {
//                 return $message->line('**Su reclamo ha sido resuelto. Ya puede imprimir su ticket.**');
//             })
//             ->action('Ver Reclamo', url('/ciudadano/mis-reclamos/' . $this->reclamo->id))
//             ->line('Gracias por utilizar nuestro sistema de reclamos.');
//     }
// }


namespace App\Notifications;

use App\Models\Reclamo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReclamoActualizadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reclamo;
    protected $estadoAnterior;

    public function __construct(Reclamo $reclamo, string $estadoAnterior = null)
    {
        $this->reclamo = $reclamo;
        $this->estadoAnterior = $estadoAnterior;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $ciudadano = $this->reclamo->ciudadano;
        $estadoTexto = $this->getEstadoTexto($this->reclamo->estado);
        
        $message = (new MailMessage)
            ->subject('Actualización de su Reclamo - ' . $this->reclamo->numero_ticket)
            ->greeting('Estimado/a ' . $ciudadano->primer_nombre)
            ->line('Le informamos que su reclamo ha sido actualizado.')
            ->line('**Número de Ticket:** ' . $this->reclamo->numero_ticket)
            ->line('**Estado Actual:** ' . $estadoTexto)
            ->line('**Asunto:** ' . $this->reclamo->asunto);
        
        if ($this->reclamo->asignadoA) {
            $message->line('**Asignado a:** ' . $this->reclamo->asignadoA->name);
        }
        
        if ($this->reclamo->estado === 'resuelto') {
            $message->line('**Su reclamo ha sido resuelto satisfactoriamente.**')
                ->line('Ya puede descargar e imprimir su ticket de resolución.');
        }
        
        return $message
            ->action('Ver Detalles del Reclamo', url('/ciudadano/mis-reclamos/' . $this->reclamo->id))
            ->line('Gracias por su paciencia.')
            ->salutation('Atentamente, IGAC');
    }

    public function toDatabase($notifiable)
    {
        return [
            'reclamo_id' => $this->reclamo->id,
            'numero_ticket' => $this->reclamo->numero_ticket,
            'asunto' => $this->reclamo->asunto,
            'tipo' => 'reclamo_actualizado',
            'estado_anterior' => $this->estadoAnterior,
            'estado_actual' => $this->reclamo->estado,
            'mensaje' => 'Su reclamo ' . $this->reclamo->numero_ticket . ' ha cambiado a: ' . $this->getEstadoTexto($this->reclamo->estado),
        ];
    }

    private function getEstadoTexto($estado)
    {
        return match($estado) {
            'nuevo' => 'Nuevo',
            'en_proceso' => 'En Proceso',
            'resuelto' => 'Resuelto',
            'cerrado' => 'Cerrado',
            default => $estado
        };
    }
}