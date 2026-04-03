@extends('layouts.pdf')

@section('pdf-titulo', 'Consentimiento ' . ($consentimiento->numero_consentimiento ?? 'CI-'.$consentimiento->id))
@section('pdf-doc-tipo', 'CONSENTIMIENTO INFORMADO')
@section('pdf-doc-num', $consentimiento->numero_consentimiento ?? 'CI-'.$consentimiento->id)
@section('pdf-footer-der')
    {{ $consentimiento->numero_consentimiento ?? ('Doc. '.$consentimiento->id) }}
    · {{ $consentimiento->fecha_generacion->format('d/m/Y') }}
@endsection

@section('pdf-contenido')
@php $C = '#1a3a6b'; @endphp

{{-- ── BLOQUE PACIENTE ── --}}
<div class="pac-blk">
    <div class="pac-grid">
        <div class="pac-cell">
            <div class="pac-lbl">Paciente</div>
            <div class="pac-val">{{ $consentimiento->paciente->nombre_completo }}</div>
            <div class="pac-det">{{ $consentimiento->paciente->tipo_documento }} {{ $consentimiento->paciente->numero_documento }}</div>
        </div>
        <div class="pac-cell">
            <div class="pac-lbl">Historia Clínica</div>
            <div class="pac-val">{{ $consentimiento->paciente->numero_historia }}</div>
        </div>
        <div class="pac-cell">
            <div class="pac-lbl">Fecha</div>
            <div class="pac-val">{{ $consentimiento->fecha_generacion->format('d/m/Y') }}</div>
        </div>
        <div class="pac-cell">
            <div class="pac-lbl">Profesional</div>
            <div class="pac-val">{{ $consentimiento->doctor?->name ?? '—' }}</div>
            <div class="pac-det">{{ $config->firma_cargo ?? 'Odontólogo(a)' }}</div>
        </div>
    </div>
</div>

{{-- ── TÍTULO DEL CONSENTIMIENTO ── --}}
<div style="text-align:center;margin-bottom:14px;">
    <div style="font-size:12px;font-weight:bold;color:{{ $C }};text-transform:uppercase;letter-spacing:.07em;">
        {{ $consentimiento->nombre }}
    </div>
    <div style="font-size:8px;color:#6b7280;margin-top:3px;">Consentimiento Informado — Documento Oficial</div>
</div>

{{-- ── CONTENIDO ── --}}
<div class="s">
    
    <div style="font-size:9.5px;line-height:1.8;color:#111827;">{!! nl2br(e($consentimiento->contenido)) !!}</div>
</div>

{{-- ── FIRMAS ── --}}
<div class="firma-wrap">
    <div class="firma-tabla">
        <div class="firma-col first">
            <div class="firma-tit">Firma del Paciente</div>
            @if($consentimiento->firmado)
                <img src="{{ $consentimiento->firma_data }}" class="firma-img" alt="Firma del paciente">
                <div class="firma-linea-img">
                    <strong>{{ $consentimiento->paciente->nombre_completo }}</strong><br>
                    {{ $consentimiento->paciente->tipo_documento }} {{ $consentimiento->paciente->numero_documento }}<br>
                    @if($consentimiento->fecha_firma)
                        Firmado el {{ $consentimiento->fecha_firma->format('d/m/Y H:i') }} hrs<br>
                    @endif
                    @if($consentimiento->ip_firma)IP: {{ $consentimiento->ip_firma }}<br>@endif
                    <span style="display:inline-block;border:1.5px solid #166534;color:#166534;font-weight:bold;font-size:7.5px;padding:1px 7px;text-transform:uppercase;letter-spacing:.5px;margin-top:3px;">✓ Firmado digitalmente</span>
                </div>
            @else
                <div class="firma-linea">
                    {{ $consentimiento->paciente->nombre_completo }}<br>
                    {{ $consentimiento->paciente->tipo_documento }} {{ $consentimiento->paciente->numero_documento }}
                </div>
            @endif
        </div>
        <div class="firma-col last">
            <div class="firma-tit">Profesional Tratante</div>
            @if($config->firma_path)
                <img src="{{ public_path('storage/' . $config->firma_path) }}" class="firma-img" alt="Firma doctor">
                <div class="firma-linea-img">
                    {{ $config->firma_nombre_doctor ?? auth()->user()->name }}<br>
                    {{ $config->firma_cargo ?? 'Odontólogo(a)' }}<br>
                    @if($config->firma_registro)Reg. Prof. {{ $config->firma_registro }}@endif
                </div>
            @else
                <div class="firma-linea">
                    {{ $config->firma_nombre_doctor ?? auth()->user()->name }}<br>
                    {{ $config->firma_cargo ?? 'Odontólogo(a)' }}<br>
                    @if($config->firma_registro)Reg. Prof. {{ $config->firma_registro }}@endif
                </div>
            @endif
        </div>
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

<x-pdf-pie-profesional :config="$config" />

@endsection
