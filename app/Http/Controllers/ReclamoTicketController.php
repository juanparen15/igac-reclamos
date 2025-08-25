<?php

namespace App\Http\Controllers;

use App\Models\Reclamo;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReclamoTicketController extends Controller
{
    // public function imprimir(Reclamo $reclamo)
    // {
    //     // Verificar permisos
    //     if (auth()->user()->hasRole('ciudadano')) {
    //         $ciudadano = auth()->user()->ciudadano;
    //         if (!$ciudadano || $reclamo->ciudadano_id !== $ciudadano->id) {
    //             abort(403);
    //         }
    //     }

    //     // Generar código QR
    //     $qrCode = base64_encode(QrCode::format('png')->size(150)->generate($reclamo->numero_ticket));

    //     $pdf = PDF::loadView('pdf.ticket-reclamo', compact('reclamo', 'qrCode'));

    //     return $pdf->stream('ticket-' . $reclamo->numero_ticket . '.pdf');
    // }

    public function imprimir(Reclamo $reclamo)
    {
        // Verificar permisos
        if (auth()->user()->hasRole('ciudadano')) {
            $ciudadano = auth()->user()->ciudadano;
            if (!$ciudadano || $reclamo->ciudadano_id !== $ciudadano->id) {
                abort(403);
            }
        }

        // Sin QR por ahora
        // $qrCode = null;


        // Usar GD en lugar de imagick
        // $qrCode = base64_encode(
        //     QrCode::format('png')
        //         ->encoding('UTF-8')
        //         ->size(150)
        //         ->margin(0)
        //         ->generate($reclamo->numero_ticket)
        // );

        $pdf = PDF::loadView('pdf.ticket-reclamo', compact('reclamo'));
        // $pdf = PDF::loadView('pdf.ticket-reclamo', compact('reclamo', 'qrCode'));

        return $pdf->stream('ticket-' . $reclamo->numero_ticket . '.pdf');
    }
}
