@extends('layouts.pdf')

@section('pdf-titulo', 'Presupuesto ' . $presupuesto->numero_formateado)
@section('pdf-doc-tipo', 'PRESUPUESTO DE TRATAMIENTO')
@section('pdf-doc-num', $presupuesto->numero_formateado)
@section('pdf-footer-der')
    {{ $presupuesto->numero_formateado }} · Validez: {{ $presupuesto->validez_dias }} días
@endsection

@section('pdf-estilos')
.badge { padding: 2px 9px; font-size: 8px; font-weight: bold; border-radius: 2px; }
.badge-aprobado  { background: #d1fae5; color: #065f46; }
.badge-enviado   { background: #dbeafe; color: #1e40af; }
.badge-borrador  { background: #f3f4f6; color: #374151; }
.badge-rechazado { background: #fee2e2; color: #991b1b; }
.badge-vencido   { background: #fef3c7; color: #92400e; }
.tr-realizado td { background: #f0fdf4; }
.tr-total td     { font-weight: bold; background: #eff6ff; font-size: 10px; }
.total-val       { font-size: 13px; font-weight: bold; color: #1a3a6b; }
.cond { background: #f8faff; border-left: 3px solid #1a3a6b; padding: 7px 10px; margin-bottom: 8px; font-size: 9px; }
@endsection

@section('pdf-contenido')
@php $C = '#1a3a6b'; @endphp

{{-- ── ESTADO BADGE ── --}}
<div style="text-align:center;margin-bottom:12px;">
    <span class="badge badge-{{ $presupuesto->estado }}">{{ strtoupper($presupuesto->estado) }}</span>
</div>

{{-- ── BLOQUE PACIENTE ── --}}
<div class="pac-blk">
    <div class="pac-grid">
        <div class="pac-cell">
            <div class="pac-lbl">Paciente</div>
            <div class="pac-val">{{ $presupuesto->paciente->nombre_completo }}</div>
            <div class="pac-det">{{ $presupuesto->paciente->tipo_documento }} {{ $presupuesto->paciente->numero_documento }}</div>
        </div>
        <div class="pac-cell">
            <div class="pac-lbl">Historia Clínica</div>
            <div class="pac-val">{{ $presupuesto->paciente->numero_historia }}</div>
        </div>
        <div class="pac-cell">
            <div class="pac-lbl">Fecha Emisión / Vencimiento</div>
            <div class="pac-val">{{ $presupuesto->fecha_generacion->format('d/m/Y') }}</div>
            <div class="pac-det">Válido hasta: {{ $presupuesto->fecha_vencimiento->format('d/m/Y') }}</div>
        </div>
        <div class="pac-cell">
            <div class="pac-lbl">Profesional</div>
            <div class="pac-val">{{ $presupuesto->doctor->name }}</div>
            <div class="pac-det">{{ $config->firma_cargo ?? 'Odontólogo(a)' }}</div>
        </div>
    </div>
</div>

{{-- ── PROCEDIMIENTOS ── --}}
<div class="s">
    <div class="s-titulo">Detalle de Procedimientos</div>
    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:37%">Procedimiento</th>
                <th style="width:10%">Diente</th>
                <th style="width:9%">Cara</th>
                <th style="width:7%">Cant.</th>
                <th style="width:15%">Valor Unit.</th>
                <th style="width:18%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($presupuesto->items as $item)
            <tr class="{{ $item->completado ? 'tr-realizado' : '' }}">
                <td>{{ $item->numero_item }}</td>
                <td>{{ $item->procedimiento }}{{ $item->completado ? ' ✓' : '' }}</td>
                <td>{{ $item->diente ?? '—' }}</td>
                <td>{{ $item->cara ?: '—' }}</td>
                <td>{{ $item->cantidad }}</td>
                <td>$ {{ number_format($item->valor_unitario, 0, ',', '.') }}</td>
                <td>$ {{ number_format($item->valor_total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" style="text-align:right;font-weight:bold;background:#f9fafb;">Subtotal:</td>
                <td style="background:#f9fafb;">$ {{ number_format($presupuesto->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($presupuesto->descuento_valor > 0)
            <tr>
                <td colspan="6" style="text-align:right;font-weight:bold;background:#f9fafb;">Descuento ({{ $presupuesto->descuento_porcentaje }}%):</td>
                <td style="background:#f9fafb;color:#dc2626;">- $ {{ number_format($presupuesto->descuento_valor, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="tr-total">
                <td colspan="6" style="text-align:right;padding-right:10px;">TOTAL A PAGAR:</td>
                <td class="total-val">$ {{ number_format($presupuesto->total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>

@if($presupuesto->condiciones_pago)
<div class="cond"><span class="fl">Condiciones de pago:</span> {{ $presupuesto->condiciones_pago }}</div>
@endif
@if($presupuesto->observaciones)
<div class="cond"><span class="fl">Observaciones:</span> {{ $presupuesto->observaciones }}</div>
@endif

{{-- ── FIRMAS ── --}}
<div class="firma-wrap">
    <div class="firma-tabla">
        <div class="firma-col first">
            <div class="firma-tit">Firma de Aprobación del Paciente</div>
            @if($presupuesto->firmado && $presupuesto->firma_data)
                <img src="{{ $presupuesto->firma_data }}" class="firma-img" alt="Firma paciente">
                <div class="firma-linea-img">
                    {{ $presupuesto->paciente->nombre_completo }}<br>
                    {{ $presupuesto->paciente->tipo_documento }}: {{ $presupuesto->paciente->numero_documento }}<br>
                    @if($presupuesto->fecha_aprobacion)
                        <span class="badge-ok">✓ Aprobado el {{ $presupuesto->fecha_aprobacion->format('d/m/Y H:i') }}</span>
                    @endif
                </div>
            @else
                <div class="firma-linea">
                    {{ $presupuesto->paciente->nombre_completo }}<br>
                    {{ $presupuesto->paciente->tipo_documento }}: {{ $presupuesto->paciente->numero_documento }}
                </div>
            @endif
        </div>
        <div class="firma-col last">
            <div class="firma-tit">Profesional Tratante</div>
            @if($config->firma_path)
                <img src="{{ public_path('storage/' . $config->firma_path) }}" class="firma-img" alt="Firma doctor">
                <div class="firma-linea-img">
                    {{ $config->firma_nombre_doctor ?? $presupuesto->doctor->name }}<br>
                    {{ $config->firma_cargo ?? 'Odontólogo(a)' }}<br>
                    @if($config->firma_registro)Reg. Prof. {{ $config->firma_registro }}@endif
                </div>
            @else
                <div class="firma-linea">
                    {{ $config->firma_nombre_doctor ?? $presupuesto->doctor->name }}<br>
                    {{ $config->firma_cargo ?? 'Odontólogo(a)' }}<br>
                    @if($config->firma_registro)Reg. Prof. {{ $config->firma_registro }}@endif
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ── CONSTANCIA DE FIRMA ELECTRÓNICA ── --}}
@if($presupuesto->firmado && $presupuesto->documento_hash)
    @php
        echo \App\Traits\TrazabilidadFirma::generarConstanciaFirmaPDF(
            [
                'firma_timestamp'          => $presupuesto->firma_timestamp,
                'firma_ip'                 => $presupuesto->ip_firma,
                'firma_dispositivo'        => $presupuesto->firma_dispositivo,
                'firma_navegador'          => $presupuesto->firma_navegador,
                'documento_hash'           => $presupuesto->documento_hash,
                'firma_verificacion_token' => $presupuesto->firma_verificacion_token,
            ],
            $presupuesto->paciente->nombre_completo,
            $presupuesto->paciente->tipo_documento,
            $presupuesto->paciente->numero_documento,
            $C
        );
    @endphp
@endif

<x-pdf-pie-profesional :config="$config" />

@endsection
