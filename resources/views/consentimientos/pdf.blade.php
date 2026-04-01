@php $C = '#1a3a6b'; @endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
            color: #111827;
            padding: 35px;
            line-height: 1.65;
        }

        .hdr {
            display: table;
            width: 100%;
            border-bottom: 2.5px solid {{ $C }};
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .hdr-logo img {
            max-height: 65px;
            max-width: 160px;
        }

        .hdr-logo {
            display: table-cell;
            width: 22%;
            vertical-align: middle;
        }

        .hdr-info {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .hdr-right {
            display: table-cell;
            width: 22%;
            vertical-align: middle;
        }

        .hdr-nombre {
            font-size: 15px;
            font-weight: bold;
            color: {{ $C }};
        }

        .hdr-sub {
            font-size: 8px;
            color: #4b5563;
            margin-top: 1px;
        }

        .doc-titulo {
            text-align: center;
            padding: 10px 0 14px;
        }

        .doc-tipo {
            font-size: 13px;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .doc-sub {
            font-size: 9px;
            color: #4b5563;
            margin-top: 3px;
        }

        .info-paciente {
            display: table;
            width: 100%;
            background: #eff6ff;
            border-left: 3px solid {{ $C }};
            padding: 10px 14px;
            margin-bottom: 18px;
        }

        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-lbl {
            font-size: 7px;
            font-weight: bold;
            color: {{ $C }};
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .info-val {
            font-size: 10px;
            color: #111827;
            font-weight: bold;
        }

        .info-det {
            font-size: 8px;
            color: #4b5563;
            margin-top: 1px;
        }

        .contenido-titulo {
            font-size: 8px;
            font-weight: bold;
            color: {{ $C }};
            text-transform: uppercase;
            letter-spacing: .07em;
            border-bottom: 1.5px solid {{ $C }};
            padding-bottom: 3px;
            margin-bottom: 12px;
        }

        .contenido-texto {
            font-size: 10px;
            line-height: 1.8;
            color: #111827;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .firma-wrap {
            margin-top: 28px;
        }

        .firma-tabla {
            display: table;
            width: 100%;
            border-top: 1.5px solid {{ $C }};
            padding-top: 14px;
        }

        .firma-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        .firma-col:last-child {
            padding-right: 0;
            padding-left: 20px;
            border-left: 1px solid #e5e7eb;
        }

        .firma-titulo {
            font-size: 8px;
            font-weight: bold;
            color: {{ $C }};
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-bottom: 6px;
        }

        .firma-img {
            max-height: 70px;
            max-width: 220px;
            display: block;
        }

        .firma-linea {
            border-top: 1px solid #374151;
            padding-top: 4px;
            margin-top: 45px;
            font-size: 8px;
            color: #4b5563;
        }

        .firma-linea-img {
            border-top: 1px solid #374151;
            padding-top: 4px;
            margin-top: 4px;
            font-size: 8px;
            color: #4b5563;
        }

        .sello {
            display: inline-block;
            border: 1.5px solid #166534;
            color: #166534;
            font-weight: bold;
            font-size: 8px;
            padding: 2px 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 35px;
            right: 35px;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
            display: table;
            width: calc(100% - 70px);
        }

        .fl-l {
            display: table-cell;
            font-size: 7px;
            color: #9ca3af;
            text-align: left;
        }

        .fl-r {
            display: table-cell;
            font-size: 7px;
            color: #9ca3af;
            text-align: right;
        }
    </style>
</head>

<body>

    {{-- ── ENCABEZADO ── --}}
    <div class="hdr">
        <div class="hdr-logo">
            @if ($config->logo_path)
                <img src="{{ public_path('storage/' . $config->logo_path) }}" alt="Logo">
            @else
                <div style="font-size:18px;font-weight:bold;color:{{ $C }};">
                    {{ mb_strtoupper(mb_substr($config->nombre_consultorio, 0, 2)) }}</div>
            @endif
        </div>
        <div class="hdr-info">
            <div class="hdr-nombre">{{ $config->nombre_consultorio }}</div>
            @if ($config->slogan)
                <div class="hdr-sub">{{ $config->slogan }}</div>
            @endif
        </div>
        <div class="hdr-right"></div>
    </div>

    {{-- ── TÍTULO ── --}}
    <div class="doc-titulo">
        <div class="doc-tipo">{{ $consentimiento->nombre }}</div>
        <div class="doc-sub">Consentimiento Informado · Documento Oficial</div>
    </div>

    {{-- ── INFO PACIENTE ── --}}
    <div class="info-paciente">
        <div class="info-col">
            <div class="info-lbl">Paciente</div>
            <div class="info-val">{{ $consentimiento->paciente->nombre_completo }}</div>
            <div class="info-det">{{ $consentimiento->paciente->tipo_documento }}
                {{ $consentimiento->paciente->numero_documento }}</div>
        </div>
        <div class="info-col" style="text-align:right;">
            <div class="info-lbl">Fecha de Generación</div>
            <div class="info-val">{{ $consentimiento->fecha_generacion->format('d/m/Y') }}</div>
            <div class="info-det">Dr./Dra. {{ $consentimiento->doctor?->name }}</div>
        </div>
    </div>

    {{-- ── CONTENIDO ── --}}
    <div class="contenido-titulo">Texto del Consentimiento</div>
    <div class="contenido-texto">{{ $consentimiento->contenido }}</div>

    {{-- ── FIRMAS ── --}}
    <div class="firma-wrap">
        <div class="firma-tabla">
            {{-- Firma paciente --}}
            <div class="firma-col">
                <div class="firma-titulo">Firma del Paciente</div>
                @if ($consentimiento->firmado)
                    <img src="{{ $consentimiento->firma_data }}" class="firma-img" alt="Firma del paciente">
                    <div class="firma-linea-img">
                        {{ $consentimiento->paciente->nombre_completo }}<br>
                        {{ $consentimiento->paciente->tipo_documento }}
                        {{ $consentimiento->paciente->numero_documento }}<br>

                        @if ($consentimiento->fecha_firma)
                            Firmado el {{ $consentimiento->fecha_firma->format('d/m/Y H:i') }} hrs<br>
                        @endif

                        IP: {{ request()->ip() }}<br>

                        Ubicación: {{ config('app.name') }}
                    </div>
                    <div class="sello">✓ Firmado digitalmente</div>
                @else
                    <div class="firma-linea">
                        {{ $consentimiento->paciente->nombre_completo }}<br>
                        {{ $consentimiento->paciente->tipo_documento }}
                        {{ $consentimiento->paciente->numero_documento }}
                    </div>
                @endif
            </div>
            {{-- Firma doctor --}}
            <div class="firma-col">
                <div class="firma-titulo">Profesional Tratante</div>
                @if ($config->firma_path)
                    <img src="{{ public_path('storage/' . $config->firma_path) }}" class="firma-img"
                        alt="Firma doctor">
                    <div class="firma-linea-img">
                        {{ $config->firma_nombre_doctor ?? auth()->user()->name }}<br>
                        {{ $config->firma_cargo ?? 'Odontólogo(a)' }}
                        @if ($config->firma_registro)
                            · Reg. {{ $config->firma_registro }}
                        @endif
                    </div>
                @else
                    <div class="firma-linea">
                        {{ $config->firma_nombre_doctor ?? auth()->user()->name }}<br>
                        {{ $config->firma_cargo ?? 'Odontólogo(a)' }}
                        @if ($config->firma_registro)
                            · Reg. {{ $config->firma_registro }}
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── PIE ── --}}
    <div class="footer">
        <div class="fl-l">
            {{ $config->nombre_consultorio }}
            @if ($config->direccion)
                · {{ $config->direccion }}
            @endif
            @if ($config->ciudad)
                · {{ $config->ciudad }}
            @endif
            @if ($config->telefono)
                · Tel: {{ $config->telefono }}
            @endif
        </div>
        <div class="fl-r">
            {{ $consentimiento->numero_consentimiento ?? 'Documento N° ' . $consentimiento->id }} · Generado el
            {{ $consentimiento->fecha_generacion->format('d/m/Y') }}
        </div>
    </div>

    {{-- ── CONSTANCIA DE FIRMA ELECTRÓNICA ── --}}
    @if($consentimiento->firmado && $consentimiento->documento_hash)
        @php
            echo \App\Traits\TrazabilidadFirma::generarConstanciaFirmaPDF(
                [
                    'firma_timestamp'          => $consentimiento->firma_timestamp,
                    'firma_ip'                 => $consentimiento->ip_firma,
                    'firma_dispositivo'        => $consentimiento->firma_dispositivo,
                    'firma_navegador'          => $consentimiento->firma_navegador,
                    'documento_hash'           => $consentimiento->documento_hash,
                    'firma_verificacion_token' => $consentimiento->firma_verificacion_token,
                ],
                $consentimiento->paciente->nombre_completo,
                $consentimiento->paciente->tipo_documento,
                $consentimiento->paciente->numero_documento,
                $C
            );
        @endphp
    @endif

</body>

</html>
