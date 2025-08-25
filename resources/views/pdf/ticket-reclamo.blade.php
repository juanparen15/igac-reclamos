{{-- resources/views/pdf/ticket-reclamo.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Ticket {{ $reclamo->numero_ticket }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header h2 {
            margin: 10px 0;
            font-size: 18px;
            color: #666;
        }

        .ticket-box {
            border: 2px solid #333;
            padding: 20px;
            margin: 20px 0;
            position: relative;
            background: #f9f9f9;
        }

        .ticket-number {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            color: #2563eb;
        }

        .info-row {
            margin: 15px 0;
            display: flex;
            border-bottom: 1px solid #e5e5e5;
            padding-bottom: 10px;
        }

        .label {
            font-weight: bold;
            width: 200px;
            color: #555;
        }

        .value {
            flex: 1;
        }

        .qr-code {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px dashed #ccc;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        @media print {
            body {
                margin: 0;
            }

            .ticket-box {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>INSTITUTO GEOGRÁFICO AGUSTÍN CODAZZI</h1>
            <h2>TICKET DE RECLAMO</h2>
        </div>

        <div class="ticket-box" id="ticket-box">
            <div class="ticket-number">
                {{ $reclamo->numero_ticket }}
            </div>

            <div class="info-row">
                <span class="label">Fecha de Registro:</span>
                <span class="value">{{ $reclamo->created_at->format('d/m/Y H:i') }}</span>
            </div>

            <div class="info-row">
                <span class="label">Ciudadano:</span>
                <span class="value">{{ $reclamo->ciudadano->nombre_completo }}</span>
            </div>

            <div class="info-row">
                <span class="label">Documento:</span>
                <span class="value">{{ $reclamo->ciudadano->tipo_documento }}
                    {{ $reclamo->ciudadano->numero_documento }}</span>
            </div>

            <div class="info-row">
                <span class="label">Celular:</span>
                <span class="value">{{ $reclamo->ciudadano->numero_celular }}</span>
            </div>

            <div class="info-row">
                <span class="label">Asunto:</span>
                <span class="value">{{ $reclamo->asunto }}</span>
            </div>

            <div class="info-row">
                <span class="label">Estado:</span>
                <span class="value">{{ $reclamo->estado_label }}</span>
            </div>

            <div class="info-row">
                <span class="label">Tipos de Reclamo:</span>
                <span class="value">{{ implode(', ', $reclamo->tipos_reclamo->pluck('nombre')->toArray()) }}</span>
            </div>

            @if ($reclamo->fecha_resolucion)
                <div class="info-row">
                    <span class="label">Fecha de Resolución:</span>
                    <span class="value">{{ $reclamo->fecha_resolucion->format('d/m/Y') }}</span>
                </div>
            @endif

            @if ($reclamo->asignadoA)
                <div class="info-row">
                    <span class="label">Atendido por:</span>
                    <span class="value">{{ $reclamo->asignadoA->name }}</span>
                </div>
            @endif

            {{-- <div class="qr-code">
                <p style="margin-bottom: 10px;">Código QR del Ticket:</p>
                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
            </div> --}}
            
            {{-- @if ($qrCode)
                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
            @endif --}}
        </div>

        <div class="footer">
            <p>Este ticket es su comprobante oficial del reclamo presentado ante el IGAC.</p>
            <p>Conserve este documento para futuras consultas.</p>
        </div>
    </div>
</body>

</html>
