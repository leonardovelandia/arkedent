@extends('layouts.pdf')

@section('pdf-titulo', 'Evolución ' . ($evolucion->numero_evolucion ?? '—'))
@section('pdf-doc-tipo', 'EVOLUCIÓN CLÍNICA')
@section('pdf-doc-num', $evolucion->numero_evolucion ?? '—')
@section('pdf-footer-der')
    HC {{ $evolucion->paciente->numero_historia }}
    @if($evolucion->numero_evolucion) · {{ $evolucion->numero_evolucion }}@endif
    · {{ $evolucion->fecha instanceof \Carbon\Carbon ? $evolucion->fecha->format('d/m/Y') : \Carbon\Carbon::parse($evolucion->fecha)->format('d/m/Y') }}
@endsection

@section('pdf-contenido')
@php $C = '#1a3a6b'; @endphp

{{-- ── BLOQUE PACIENTE ── --}}
<div class="pac-blk">
    <div class="pac-grid">
        <div class="pac-cell">
            <div class="pac-lbl">Paciente</div>
            <div class="pac-val">{{ $evolucion->paciente->nombre_completo }}</div>
            <div class="pac-det">{{ $evolucion->paciente->tipo_documento }} {{ $evolucion->paciente->numero_documento }}</div>
        </div>
        <div class="pac-cell">
            <div class="pac-lbl">Historia Clínica</div>
            <div class="pac-val">{{ $evolucion->paciente->numero_historia }}</div>
        </div>
        <div class="pac-cell">
            <div class="pac-lbl">Fecha</div>
            <div class="pac-val">
                {{ $evolucion->fecha instanceof \Carbon\Carbon ? $evolucion->fecha->format('d/m/Y') : \Carbon\Carbon::parse($evolucion->fecha)->format('d/m/Y') }}
                @if($evolucion->hora_inicio ?? null)
                    <br><span style="font-size:8px;font-weight:normal;color:#4b5563;">
                        {{ \Carbon\Carbon::parse($evolucion->hora_inicio)->format('H:i') }}
                        @if($evolucion->hora_fin ?? null) — {{ \Carbon\Carbon::parse($evolucion->hora_fin)->format('H:i') }}@endif
                        @if($evolucion->duracion ?? null) ({{ $evolucion->duracion }})@endif
                    </span>
                @endif
            </div>
        </div>
        <div class="pac-cell">
            <div class="pac-lbl">Profesional</div>
            <div class="pac-val">{{ $evolucion->doctor->name ?? '—' }}</div>
            <div class="pac-det">{{ $config->firma_cargo ?? 'Odontólogo(a)' }}</div>
        </div>
    </div>
</div>

{{-- ── PROCEDIMIENTO ── --}}
<div class="s">
    <div class="s-titulo">Procedimiento Realizado</div>
    <div class="f" style="font-size:11px;font-weight:bold;color:{{ $C }};">{{ $evolucion->procedimiento }}</div>
    @if($evolucion->dientes_tratados)
        <div class="f" style="margin-top:4px;"><span class="fl">Dientes tratados:</span> {{ $evolucion->dientes_tratados }}</div>
    @endif
</div>

{{-- ── DESCRIPCIÓN ── --}}
@if($evolucion->descripcion)
<div class="s">
    <div class="s-titulo">Descripción Clínica</div>
    <div class="f" style="font-size:9.5px;line-height:1.65;">{{ $evolucion->descripcion }}</div>
</div>
@endif

{{-- ── SIGNOS VITALES ── --}}
@if($evolucion->presion_arterial || $evolucion->frecuencia_cardiaca)
<div class="s">
    <div class="s-titulo">Signos Vitales</div>
    <div class="vitals">
        @if($evolucion->presion_arterial)
        <div class="v-cell">
            <div class="v-lbl">Presión Arterial</div>
            <div class="v-val">{{ $evolucion->presion_arterial }}</div>
            <div class="v-unit">mmHg</div>
        </div>
        @endif
        @if($evolucion->frecuencia_cardiaca)
        <div class="v-cell">
            <div class="v-lbl">Frec. Cardíaca</div>
            <div class="v-val">{{ $evolucion->frecuencia_cardiaca }}</div>
            <div class="v-unit">bpm</div>
        </div>
        @endif
    </div>
</div>
@endif

{{-- ── MATERIALES ── --}}
@if($evolucion->materiales && count($evolucion->materiales))
<div class="s">
    <div class="s-titulo">Materiales Utilizados</div>
    <table>
        <tr><th>Material</th><th>Cantidad</th><th>Unidad</th></tr>
        @foreach($evolucion->materiales as $mat)
        <tr>
            <td>{{ $mat['nombre'] ?? $mat['material'] ?? '—' }}</td>
            <td>{{ $mat['cantidad'] ?? '—' }}</td>
            <td>{{ $mat['unidad'] ?? '—' }}</td>
        </tr>
        @endforeach
    </table>
</div>
@endif

{{-- ── PRÓXIMA CITA ── --}}
@if($evolucion->proxima_cita_fecha || $evolucion->proxima_cita_procedimiento)
<div class="s">
    <div class="s-titulo">Próxima Cita Sugerida</div>
    <div class="proxima">
        @if($evolucion->proxima_cita_fecha)
            <div class="f"><span class="fl">Fecha:</span> {{ \Carbon\Carbon::parse($evolucion->proxima_cita_fecha)->format('d/m/Y') }}</div>
        @endif
        @if($evolucion->proxima_cita_procedimiento)
            <div class="f"><span class="fl">Procedimiento:</span> {{ $evolucion->proxima_cita_procedimiento }}</div>
        @endif
    </div>
</div>
@endif

{{-- ── OBSERVACIONES ── --}}
@if($evolucion->observaciones)
<div class="s">
    <div class="s-titulo">Observaciones</div>
    <div class="f" style="font-size:9.5px;line-height:1.65;">{{ $evolucion->observaciones }}</div>
</div>
@endif

{{-- ── FIRMAS ── --}}
<div class="firma-wrap">
    <div class="firma-tabla">
        <div class="firma-col first">
            <div class="firma-tit">Firma del Paciente</div>
            @if($evolucion->firmado)
                <img src="{{ $evolucion->firma_data }}" class="firma-img" alt="Firma paciente">
                <div class="firma-linea-img">
                    {{ $evolucion->paciente->nombre_completo }}<br>
                    {{ $evolucion->paciente->tipo_documento }}: {{ $evolucion->paciente->numero_documento }}<br>
                    <span class="badge-ok">✓ Firmado digitalmente</span>
                </div>
            @else
                <div class="firma-linea">
                    {{ $evolucion->paciente->nombre_completo }}<br>
                    {{ $evolucion->paciente->tipo_documento }}: {{ $evolucion->paciente->numero_documento }}
                </div>
            @endif
        </div>
        <div class="firma-col last">
            <div class="firma-tit">Profesional Tratante</div>
            @if($config->firma_path)
                <img src="{{ public_path('storage/' . $config->firma_path) }}" class="firma-img" alt="Firma doctor">
                <div class="firma-linea-img">
                    {{ $config->firma_nombre_doctor ?? ($evolucion->doctor->name ?? '') }}<br>
                    {{ $config->firma_cargo ?? 'Odontólogo(a)' }}<br>
                    @if($config->firma_registro)Reg. Prof. {{ $config->firma_registro }}@endif
                </div>
            @else
                <div class="firma-linea">
                    {{ $config->firma_nombre_doctor ?? ($evolucion->doctor->name ?? '') }}<br>
                    {{ $config->firma_cargo ?? 'Odontólogo(a)' }}<br>
                    @if($config->firma_registro)Reg. Prof. {{ $config->firma_registro }}@endif
                </div>
            @endif
        </div>
    </div>
    @if($evolucion->firmado)
    <div class="meta">Firmado digitalmente el {{ $evolucion->fecha_firma->format('d/m/Y \a \l\a\s H:i') }} · IP: {{ $evolucion->ip_firma }}</div>
    @endif
</div>

{{-- ── CORRECCIONES ── --}}
@if($evolucion->correcciones->count() > 0)
<div class="corr">
    <div style="font-size:9px;font-weight:bold;color:{{ $C }};margin-bottom:6px;">NOTAS DE CORRECCIÓN ANEXAS</div>
    <div style="font-size:8px;color:#666;margin-bottom:8px;font-style:italic;">Correcciones agregadas tras el cierre del documento original.</div>
    @foreach($evolucion->correcciones as $i => $correccion)
    <div class="corr-item">
        <div style="font-weight:bold;font-size:8px;color:{{ $C }};">
            {{ $correccion->numero_correccion ?? ('Corrección #'.($i+1)) }} — {{ $correccion->campo_label }}
            — {{ $correccion->created_at->format('d/m/Y H:i') }} — Por: {{ $correccion->usuario->name }}
        </div>
        <div style="font-size:8px;margin-top:2px;color:#999;text-decoration:line-through;">Anterior: {{ $correccion->valor_anterior }}</div>
        <div style="font-size:8px;margin-top:2px;">Corrección: {{ $correccion->valor_nuevo }}</div>
        <div style="font-size:7px;margin-top:2px;color:#666;font-style:italic;">Motivo: {{ $correccion->motivo }}</div>
        @if($correccion->firmado)
            <img src="{{ $correccion->firma_data }}" style="max-height:35px;max-width:130px;margin-top:3px;">
            <div style="font-size:7px;color:#666;">Firmada el {{ $correccion->fecha_firma->format('d/m/Y H:i') }} — IP: {{ $correccion->ip_firma }}</div>
        @else
            <div style="font-size:7px;color:#dc2626;font-weight:bold;margin-top:3px;">⚠ PENDIENTE DE FIRMA DEL PACIENTE</div>
        @endif
    </div>
    @endforeach
</div>
@endif

{{-- ── CONSTANCIA DE FIRMA ELECTRÓNICA ── --}}
@if($evolucion->firmado && $evolucion->documento_hash)
    @php
        echo \App\Traits\TrazabilidadFirma::generarConstanciaFirmaPDF(
            [
                'firma_timestamp'          => $evolucion->firma_timestamp,
                'firma_ip'                 => $evolucion->ip_firma,
                'firma_dispositivo'        => $evolucion->firma_dispositivo,
                'firma_navegador'          => $evolucion->firma_navegador,
                'documento_hash'           => $evolucion->documento_hash,
                'firma_verificacion_token' => $evolucion->firma_verificacion_token,
            ],
            $evolucion->paciente->nombre_completo,
            $evolucion->paciente->tipo_documento,
            $evolucion->paciente->numero_documento,
            $C
        );
    @endphp
@endif

<x-pdf-pie-profesional :config="$config" :colorPDF="$C" />

@endsection
