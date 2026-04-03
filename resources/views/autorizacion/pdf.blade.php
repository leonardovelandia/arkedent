@extends('layouts.pdf')

@section('pdf-titulo', 'Autorización ' . $autorizacion->numero_autorizacion)
@section('pdf-doc-tipo', 'AUTORIZACIÓN DE DATOS')
@section('pdf-doc-num', $autorizacion->numero_autorizacion)
@section('pdf-footer-der')
    Ley 1581/2012 · {{ $autorizacion->numero_autorizacion }}
    @if($autorizacion->firmado) · Firmado@endif
@endsection

@section('pdf-contenido')
@php $C = '#1a3a6b'; @endphp

{{-- ── BLOQUE PACIENTE ── --}}
<div class="pac-blk">
    <div class="pac-grid">
        <div class="pac-cell">
            <div class="pac-lbl">Titular</div>
            <div class="pac-val">{{ $autorizacion->paciente->nombre_completo }}</div>
            <div class="pac-det">{{ $autorizacion->paciente->tipo_documento }} {{ $autorizacion->paciente->numero_documento }}</div>
        </div>
        <div class="pac-cell">
            <div class="pac-lbl">Historia Clínica</div>
            <div class="pac-val">{{ $autorizacion->paciente->numero_historia }}</div>
        </div>
        <div class="pac-cell">
            <div class="pac-lbl">Fecha</div>
            <div class="pac-val">{{ $autorizacion->fecha_autorizacion->format('d/m/Y') }}</div>
        </div>
        <div class="pac-cell">
            <div class="pac-lbl">Registrado por</div>
            <div class="pac-val">{{ $autorizacion->registradoPor?->name ?? 'Sistema' }}</div>
        </div>
    </div>
</div>

{{-- ── MARCO LEGAL ── --}}
<div class="s">
    <div class="s-titulo">Declaración de Autorización — Ley 1581 de 2012</div>
    <div class="f" style="font-size:9.5px;line-height:1.7;">
        Yo, <strong>{{ $autorizacion->paciente->nombre_completo }}</strong>,
        identificado(a) con <strong>{{ $autorizacion->paciente->tipo_documento }} N° {{ $autorizacion->paciente->numero_documento }}</strong>,
        en pleno uso de mis facultades mentales, de manera libre, voluntaria, previa, expresa e informada,
        autorizo a <strong>{{ $config->nombre_consultorio }}</strong>@if($config->nit), NIT {{ $config->nit }},@endif
        @if($config->direccion) con domicilio en {{ $config->direccion }},@endif
        para que realice el tratamiento de mis datos personales conforme a la Ley 1581 de 2012 y el Decreto 1377 de 2013.
    </div>
</div>

{{-- ── AUTORIZACIONES ── --}}
<div class="s">
    <div class="s-titulo">Actividades Autorizadas Expresamente</div>
    @php
        $permisos = [
            'acepta_almacenamiento'      => 'Recolección y almacenamiento de datos personales para fines médicos y odontológicos.',
            'acepta_contacto_whatsapp'   => 'Envío de recordatorios y comunicaciones de citas a través de WhatsApp (revocable en cualquier momento).',
            'acepta_contacto_email'      => 'Contacto por correo electrónico para confirmaciones y comunicaciones.',
            'acepta_contacto_llamada'    => 'Contacto telefónico para confirmación de citas y seguimiento.',
            'acepta_recordatorios'       => 'Envío de recordatorios automáticos de citas programadas.',
            'acepta_compartir_entidades' => 'Compartir información con entidades de salud cuando sea necesario para la atención.',
        ];
    @endphp
    @foreach($permisos as $campo => $texto)
    <div style="display:table;width:100%;margin-bottom:4px;font-size:9.5px;">
        <div style="display:table-cell;width:18px;vertical-align:top;padding-top:1px;">
            <span style="display:inline-block;width:13px;height:13px;border:1.5px solid {{ $C }};border-radius:2px;text-align:center;line-height:11px;font-size:8px;font-weight:bold;{{ $autorizacion->$campo ? 'background:'.$C.';color:#fff;' : 'color:'.$C.';' }}">
                {{ $autorizacion->$campo ? '✓' : '' }}
            </span>
        </div>
        <div style="display:table-cell;vertical-align:top;">{{ $texto }}</div>
    </div>
    @endforeach
</div>

{{-- ── DERECHOS ── --}}
<div class="s">
    <div class="s-titulo">Derechos como Titular de Datos Personales</div>
    <div style="background:#f0f5fb;border-left:3px solid {{ $C }};padding:8px 12px;">
        <ul style="padding-left:14px;font-size:9px;line-height:1.8;">
            <li>Conocer, actualizar y rectificar sus datos personales</li>
            <li>Solicitar prueba de la autorización otorgada y revocarla en cualquier momento</li>
            <li>Ser informado sobre el uso de sus datos y acceder gratuitamente a ellos</li>
            <li>Presentar quejas ante la Superintendencia de Industria y Comercio (SIC)</li>
        </ul>
    </div>
</div>

{{-- ── DECLARACIÓN FINAL ── --}}
<div class="s">
    <div style="font-size:9.5px;line-height:1.7;font-style:italic;color:#374151;">
        Declaro que he leído y comprendido el presente documento, que he sido informado(a) sobre el tratamiento que se dará a mis datos personales y que otorgo mi consentimiento de manera libre, voluntaria y sin ningún tipo de presión.
    </div>
    <div style="margin-top:6px;font-size:9px;color:#6b7280;">
        Firmado en {{ $config->ciudad ?? 'Colombia' }}, el {{ $autorizacion->fecha_autorizacion->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
    </div>
</div>

{{-- ── FIRMA PACIENTE ── --}}
<div class="firma-wrap">
    <div class="firma-tabla">
        <div class="firma-col first">
            <div class="firma-tit">Firma del Titular</div>
            @if($autorizacion->firma_data && str_starts_with($autorizacion->firma_data, 'data:image'))
                <img src="{{ $autorizacion->firma_data }}" class="firma-img" alt="Firma del paciente">
                <div class="firma-linea-img">
                    <strong>{{ $autorizacion->paciente->nombre_completo }}</strong><br>
                    {{ $autorizacion->paciente->tipo_documento }}: {{ $autorizacion->paciente->numero_documento }}<br>
                    @if($autorizacion->fecha_firma)Firmado el {{ $autorizacion->fecha_firma->format('d/m/Y H:i') }} hrs @endif
                    @if($autorizacion->ip_firma) · IP: {{ $autorizacion->ip_firma }}@endif<br>
                    <span class="badge-ok">✓ Firma digital verificada</span>
                </div>
            @else
                <div class="firma-linea">
                    {{ $autorizacion->paciente->nombre_completo }}<br>
                    {{ $autorizacion->paciente->tipo_documento }}: {{ $autorizacion->paciente->numero_documento }}
                </div>
            @endif
        </div>
        <div class="firma-col last">
            <div class="firma-tit">Responsable del Tratamiento</div>
            <div class="firma-linea" style="margin-top:48px;">
                <strong>{{ $config->nombre_consultorio }}</strong><br>
                @if($config->nit)NIT: {{ $config->nit }}<br>@endif
                @if($config->email){{ $config->email }}@endif
                @if($config->telefono) · Tel: {{ $config->telefono }}@endif
            </div>
        </div>
    </div>
</div>

@if($autorizacion->observaciones)
<div style="margin-top:12px;font-size:9px;background:#fafafa;border:1px solid #e5e7eb;padding:7px 10px;">
    <strong>Observaciones:</strong> {{ $autorizacion->observaciones }}
</div>
@endif

{{-- ── NOTA LEGAL ── --}}
<div style="margin-top:14px;border-top:1px dashed #c7d2e0;padding-top:9px;font-size:7.5px;color:#9ca3af;line-height:1.6;">
    Para ejercer sus derechos puede contactarnos en:
    @if($config->email){{ $config->email }}@endif
    @if($config->telefono) · Tel: {{ $config->telefono }}@endif.
    Puede consultar nuestra política de tratamiento de datos en nuestras instalaciones.
    El titular podrá revocar la autorización cuando no se respeten los principios, derechos y garantías constitucionales y legales.
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

<x-pdf-pie-profesional :config="$config" />

@endsection
