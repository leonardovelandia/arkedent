@php $C = '#1a3a6b'; @endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autorización {{ $autorizacion->numero_autorizacion }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10.5px; color: #1c2b22; line-height: 1.5; padding: 14px; }

        .header { background: #1E3A5F; color: white; padding: 12px 20px; border-radius: 8px 8px 0 0; text-align: center; margin-bottom: 0; }
        .header h1 { font-size: 13px; font-weight: bold; margin-bottom: 2px; }
        .header .subtitulo { font-size: 10.5px; opacity: 0.8; }
        .header .num { font-size: 9.5px; opacity: 0.6; margin-top: 3px; }

        .cuerpo { border: 1px solid #e0d5f0; border-top: none; border-radius: 0 0 8px 8px; padding: 14px 20px; }

        p { margin-bottom: 7px; }
        .intro { font-size: 10.5px; line-height: 1.6; }

        .seccion-titulo { font-size: 10.5px; font-weight: bold; color: #1E3A5F; margin-bottom: 6px; margin-top: 10px; }

        .check-item { display: block; margin-bottom: 3px; font-size: 10px; }
        .check-item .mark { display: inline-block; width: 13px; height: 13px; border: 1.5px solid #1E3A5F; border-radius: 3px; text-align: center; line-height: 11px; font-size: 8.5px; color: #1E3A5F; font-weight: bold; margin-right: 5px; vertical-align: middle; }
        .check-item .mark.check { background: #1E3A5F; color: white; }

        .derechos-box { background: #EEF3F9; border-left: 3px solid #1E3A5F; padding: 7px 12px; border-radius: 0 6px 6px 0; margin: 8px 0; }
        .derechos-box li { font-size: 9.5px; margin-bottom: 2px; }

        .fecha { font-size: 10px; color: #5c6b62; margin: 8px 0; }

        .firmas-wrap { margin-top: 14px; border-top: 1.5px solid #1E3A5F; padding-top: 10px; }
        .firma-col { display: inline-block; }
        .firma-titulo { font-size: 8px; font-weight: bold; color: #1E3A5F; text-transform: uppercase; letter-spacing: .07em; margin-bottom: 5px; }
        .firma-img { max-height: 60px; max-width: 180px; display: block; }
        .firma-linea { border-top: 1px solid #374151; padding-top: 4px; margin-top: 35px; width: 200px; font-size: 9px; color: #4b5563; }
        .firma-linea-img { border-top: 1px solid #374151; padding-top: 4px; margin-top: 4px; font-size: 9px; color: #4b5563; }
        .firma-nombre { font-size: 10px; font-weight: bold; color: #1c2b22; }

        .pie { margin-top: 16px; padding-top: 8px; border-top: 1px solid #e0d5f0; text-align: center; font-size: 8.5px; color: #9ca3af; }
    </style>
</head>
<body>

<div class="header">
    <h1>{{ $config->nombre_consultorio }}</h1>
    @if($config->nit)<div class="subtitulo">NIT: {{ $config->nit }}</div>@endif
    <div class="subtitulo" style="margin-top:4px;font-size:12px;font-weight:bold;">
        AUTORIZACIÓN DE TRATAMIENTO DE DATOS PERSONALES
    </div>
    <div class="num">Ley 1581 de 2012 — Decreto 1377 de 2013 — Colombia · N° {{ $autorizacion->numero_autorizacion }}</div>
</div>

<div class="cuerpo">

    <p class="intro">
        Yo, <strong>{{ $autorizacion->paciente->nombre_completo }}</strong>,
        identificado(a) con <strong>{{ $autorizacion->paciente->tipo_documento }} N° {{ $autorizacion->paciente->numero_documento }}</strong>,
        en pleno uso de mis facultades mentales, de manera libre, voluntaria, previa, expresa e informada,
        autorizo a <strong>{{ $config->nombre_consultorio }}</strong>@if($config->nit), identificado con NIT {{ $config->nit }},@endif
        con domicilio en {{ $config->direccion ?? 'Colombia' }},
        para que realice el tratamiento de mis datos personales de acuerdo con lo establecido en la Ley 1581 de 2012 y el Decreto 1377 de 2013.
    </p>

    <div class="seccion-titulo">Autorizo expresamente las siguientes actividades:</div>

    @php
        $permisos = [
            'acepta_almacenamiento'      => 'Recolección y almacenamiento de mis datos personales para fines médicos y odontológicos.',
            'acepta_contacto_whatsapp'   => 'Autorizo el envío de recordatorios, notificaciones y comunicaciones relacionadas con mis citas médicas y seguimientos a través de WhatsApp. Entiendo que puedo cancelar esta autorización en cualquier momento solicitándolo directamente al consultorio.',
            'acepta_contacto_email'      => 'Contacto por correo electrónico para confirmaciones y comunicaciones.',
            'acepta_contacto_llamada'    => 'Contacto telefónico para confirmación de citas y seguimiento.',
            'acepta_recordatorios'       => 'Envío de recordatorios automáticos de citas programadas.',
            'acepta_compartir_entidades' => 'Compartir información con entidades de salud cuando sea necesario para mi atención.',
        ];
    @endphp

    @foreach($permisos as $campo => $texto)
    <div class="check-item">
        <span class="mark {{ $autorizacion->$campo ? 'check' : '' }}">{{ $autorizacion->$campo ? '✓' : '' }}</span>
        {{ $texto }}
    </div>
    @endforeach

    <div class="derechos-box">
        <div style="font-size:10px;font-weight:bold;color:#1E3A5F;margin-bottom:5px;">Derechos como titular de datos personales:</div>
        <ul style="padding-left:14px;">
            <li>Conocer, actualizar y rectificar mis datos personales</li>
            <li>Solicitar prueba de la autorización otorgada y revocarla en cualquier momento</li>
            <li>Ser informado sobre el uso de mis datos y acceder gratuitamente a ellos</li>
            <li>Presentar quejas ante la Superintendencia de Industria y Comercio</li>
        </ul>
    </div>

    <p class="intro">
        Declaro que he leído y comprendido el presente documento, que he sido informado(a) sobre el tratamiento
        que se dará a mis datos personales y que otorgo mi consentimiento de manera libre, voluntaria y sin ningún tipo de presión.
    </p>

    <div class="fecha">
        Firmado en {{ $config->ciudad ?? 'Colombia' }},
        el {{ $autorizacion->fecha_autorizacion->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
    </div>

    {{-- Firmas --}}
    <div class="firmas-wrap">
        {{-- Firma paciente --}}
        <div class="firma-col">
                <div class="firma-titulo">Firma del Paciente</div>
                @if($autorizacion->firma_data && str_starts_with($autorizacion->firma_data, 'data:image'))
                    <img src="{{ $autorizacion->firma_data }}" class="firma-img" alt="Firma del paciente">
                    <div class="firma-linea-img">
                        <span class="firma-nombre">{{ $autorizacion->paciente->nombre_completo }}</span><br>
                        {{ $autorizacion->paciente->tipo_documento }}: {{ $autorizacion->paciente->numero_documento }}<br>
                        @if($autorizacion->fecha_firma)Firmado el {{ $autorizacion->fecha_firma->format('d/m/Y H:i') }} hrs @endif
                        @if($autorizacion->ip_firma) · IP: {{ $autorizacion->ip_firma }}@endif
                    </div>
                @else
                    <div class="firma-linea">
                        <span class="firma-nombre">{{ $autorizacion->paciente->nombre_completo }}</span><br>
                        {{ $autorizacion->paciente->tipo_documento }}: {{ $autorizacion->paciente->numero_documento }}
                    </div>
                @endif
        </div>
    </div>

    @if($autorizacion->observaciones)
    <div style="margin-top:12px;font-size:10.5px;">
        <strong>Observaciones:</strong> {{ $autorizacion->observaciones }}
    </div>
    @endif

    {{-- Nota de derechos y contacto --}}
    <div style="margin-top:18px; border-top:1px dashed #c9c0df; padding-top:10px; font-size:8px; color:#8fa39a; line-height:1.5; page-break-inside:avoid; page-break-before:avoid;">
        <p style="margin:0 0 5px;">
            Para ejercer sus derechos puede contactarnos en:
            @if($config->email){{ $config->email }}@endif
            @if($config->email && $config->telefono) o al teléfono @endif
            @if($config->telefono){{ $config->telefono }}@endif.
        </p>
        <p style="margin:0 0 5px;">
            Puede consultar nuestra política de tratamiento de datos personales en <em>[disponible en nuestras instalaciones]</em>.
        </p>
        <p style="margin:0;">
            El titular podrá revocar la autorización y/o solicitar la supresión del dato cuando no se respeten los principios, derechos y garantías constitucionales y legales.
        </p>
    </div>

</div>

<div class="pie">
    {{ $config->nombre_consultorio }}
    @if($config->direccion) · {{ $config->direccion }}@endif
    @if($config->telefono) · Tel: {{ $config->telefono }}@endif
    <br>
    Documento generado el {{ now()->locale('es')->isoFormat('D MMM YYYY HH:mm') }}
    @if($autorizacion->firmado && $autorizacion->ip_firma)
    · Firmado digitalmente desde IP {{ $autorizacion->ip_firma }}
    @endif
</div>

    {{-- ── CONSTANCIA DE FIRMA ELECTRÓNICA ── --}}
    @if($autorizacion->firmado && $autorizacion->documento_hash)
        @php
            echo \App\Traits\TrazabilidadFirma::generarConstanciaFirmaPDF(
                [
                    'firma_timestamp'          => $autorizacion->firma_timestamp,
                    'firma_ip'                 => $autorizacion->ip_firma,
                    'firma_dispositivo'        => $autorizacion->firma_dispositivo,
                    'firma_navegador'          => $autorizacion->firma_navegador,
                    'documento_hash'           => $autorizacion->documento_hash,
                    'firma_verificacion_token' => $autorizacion->firma_verificacion_token,
                ],
                $autorizacion->paciente->nombre_completo,
                $autorizacion->paciente->tipo_documento,
                $autorizacion->paciente->numero_documento,
                $C
            );
        @endphp
    @endif

</body>
</html>
