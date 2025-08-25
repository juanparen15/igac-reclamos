<?php

namespace App\Notifications;

use App\Models\Reclamo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReclamoResueltoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reclamo;

    public function __construct(Reclamo $reclamo)
    {
        $this->reclamo = $reclamo;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $ciudadano = $this->reclamo->ciudadano;

        return (new MailMessage)
            ->subject('¡Su Reclamo ha sido Resuelto! - ' . $this->reclamo->numero_ticket)
            ->greeting('Estimado/a ' . $ciudadano->primer_nombre)
            ->line('Nos complace informarle que su reclamo ha sido resuelto satisfactoriamente.')
            ->line('**Número de Ticket:** ' . $this->reclamo->numero_ticket)
            ->line('**Asunto:** ' . $this->reclamo->asunto)
            ->line('**Fecha de Resolución:** ' . $this->reclamo->fecha_resolucion->format('d/m/Y'))
            ->line('**Resuelto por:** ' . ($this->reclamo->asignadoA ? $this->reclamo->asignadoA->name : 'Sistema IGAC'))
            ->line('Puede descargar e imprimir su ticket de resolución desde nuestra plataforma.')
            ->action('Descargar Ticket', url('/ciudadano/mis-reclamos/' . $this->reclamo->id))
            ->line('Si tiene alguna pregunta adicional, no dude en contactarnos.')
            ->line('Gracias por utilizar nuestros servicios.')
            ->salutation('Atentamente, IGAC');
    }

    public function toDatabase($notifiable)
    {
        return [
            'reclamo_id' => $this->reclamo->id,
            'numero_ticket' => $this->reclamo->numero_ticket,
            'asunto' => $this->reclamo->asunto,
            'tipo' => 'reclamo_resuelto',
            'fecha_resolucion' => $this->reclamo->fecha_resolucion,
            'mensaje' => '¡Su reclamo ' . $this->reclamo->numero_ticket . ' ha sido resuelto!',
        ];
    }
}
