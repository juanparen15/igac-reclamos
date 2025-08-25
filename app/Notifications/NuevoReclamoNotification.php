<?php

// namespace App\Notifications;

// use App\Models\Reclamo;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Notifications\Messages\MailMessage;
// use Illuminate\Notifications\Notification;

// class NuevoReclamoNotification extends Notification implements ShouldQueue
// {
//     use Queueable;

//     protected $reclamo;
//     protected $paraIGAC;

//     public function __construct(Reclamo $reclamo, bool $paraIGAC = false)
//     {
//         $this->reclamo = $reclamo;
//         $this->paraIGAC = $paraIGAC;
//     }

//     public function via($notifiable)
//     {
//         return ['mail'];
//     }

//     public function toMail($notifiable)
//     {
//         $ciudadano = $this->reclamo->ciudadano;

//         if ($this->paraIGAC) {
//             return (new MailMessage)
//                 ->subject('Nuevo Reclamo Recibido - ' . $this->reclamo->numero_ticket)
//                 ->greeting('Nuevo Reclamo Recibido')
//                 ->line('Se ha recibido un nuevo reclamo con los siguientes datos:')
//                 ->line('**Número de Ticket:** ' . $this->reclamo->numero_ticket)
//                 ->line('**Ciudadano:** ' . $ciudadano->nombre_completo)
//                 ->line('**Documento:** ' . $ciudadano->tipo_documento . ' ' . $ciudadano->numero_documento)
//                 ->line('**Asunto:** ' . $this->reclamo->asunto)
//                 ->line('**Tipos de Reclamo:** ' . implode(', ', $this->reclamo->tipos_reclamo->pluck('nombre')->toArray()))
//                 ->line('**Mensaje:**')
//                 ->line($this->reclamo->mensaje)
//                 ->action('Ver Reclamo', url('/admin/reclamos/' . $this->reclamo->id))
//                 ->line('Por favor, asigne este reclamo a un funcionario para su atención.');
//         }

//         return (new MailMessage)
//             ->subject('Reclamo Registrado - ' . $this->reclamo->numero_ticket)
//             ->greeting('Estimado/a ' . $ciudadano->primer_nombre)
//             ->line('Su reclamo ha sido registrado exitosamente con el siguiente número de ticket:')
//             ->line('**' . $this->reclamo->numero_ticket . '**')
//             ->line('**Asunto:** ' . $this->reclamo->asunto)
//             ->line('Le notificaremos cuando su reclamo sea atendido.')
//             ->action('Ver Estado del Reclamo', url('/ciudadano/mis-reclamos/' . $this->reclamo->id))
//             ->line('Gracias por utilizar nuestro sistema de reclamos.');
//     }
// }



namespace App\Notifications;

use App\Models\Reclamo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevoReclamoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reclamo;
    protected $paraIGAC;

    public function __construct(Reclamo $reclamo, bool $paraIGAC = false)
    {
        $this->reclamo = $reclamo;
        $this->paraIGAC = $paraIGAC;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $ciudadano = $this->reclamo->ciudadano;

        if ($this->paraIGAC) {
            return (new MailMessage)
                ->subject('Nuevo Reclamo Recibido - ' . $this->reclamo->numero_ticket)
                ->greeting('Nuevo Reclamo Recibido')
                ->line('Se ha recibido un nuevo reclamo con los siguientes datos:')
                ->line('**Número de Ticket:** ' . $this->reclamo->numero_ticket)
                ->line('**Ciudadano:** ' . $ciudadano->nombre_completo)
                ->line('**Documento:** ' . $ciudadano->documento_completo)
                ->line('**Celular:** ' . $ciudadano->numero_celular)
                ->line('**Asunto:** ' . $this->reclamo->asunto)
                ->line('**Tipos de Reclamo:** ' . implode(', ', $this->reclamo->tipos_reclamo->pluck('nombre')->toArray()))
                ->line('**Mensaje:**')
                ->line($this->reclamo->mensaje)
                ->action('Ver Reclamo', url('/admin/reclamos/' . $this->reclamo->id))
                ->line('Por favor, asigne este reclamo a un funcionario para su atención.')
                ->salutation('Sistema IGAC');
        }

        return (new MailMessage)
            ->subject('Reclamo Registrado - ' . $this->reclamo->numero_ticket)
            ->greeting('Estimado/a ' . $ciudadano->primer_nombre)
            ->line('Su reclamo ha sido registrado exitosamente.')
            ->line('**Número de Ticket:** ' . $this->reclamo->numero_ticket)
            ->line('**Asunto:** ' . $this->reclamo->asunto)
            ->line('**Fecha:** ' . $this->reclamo->created_at->format('d/m/Y H:i'))
            ->line('Le notificaremos por correo electrónico cuando su reclamo sea atendido.')
            ->action('Ver Estado del Reclamo', url('/ciudadano/mis-reclamos/' . $this->reclamo->id))
            ->line('Gracias por utilizar nuestro sistema de reclamos.')
            ->salutation('Atentamente, IGAC');
    }

    public function toDatabase($notifiable)
    {
        return [
            'reclamo_id' => $this->reclamo->id,
            'numero_ticket' => $this->reclamo->numero_ticket,
            'asunto' => $this->reclamo->asunto,
            'tipo' => 'nuevo_reclamo',
            'mensaje' => $this->paraIGAC
                ? 'Nuevo reclamo recibido: ' . $this->reclamo->numero_ticket
                : 'Su reclamo ' . $this->reclamo->numero_ticket . ' ha sido registrado',
        ];
    }
}
