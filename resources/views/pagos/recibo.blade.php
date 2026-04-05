<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size:10px; color:#1c2b22; background:#fff; padding:12px; }

    .header { text-align:center; border-bottom:2px solid var(--color-principal); padding-bottom:8px; margin-bottom:8px; }
    .consultorio-nombre { font-size:14px; font-weight:700; color:var(--color-principal); }
    .consultorio-info { font-size:9px; color:#6b7280; margin-top:2px; }

    .titulo-recibo { text-align:center; margin:8px 0 4px; }
    .titulo-recibo h1 { font-size:13px; font-weight:700; color:#1c2b22; letter-spacing:.08em; }
    .num-recibo { text-align:center; font-size:11px; font-weight:700; color:var(--color-principal); font-family:monospace; margin-bottom:8px; }

    .anulado-banner { text-align:center; background:#fef2f2; color:#dc2626; border:1px solid #fecdd3; border-radius:4px; padding:4px 8px; font-weight:700; font-size:11px; margin-bottom:8px; }

    table.datos { width:100%; border-collapse:collapse; margin-bottom:8px; }
    table.datos td { padding:4px 6px; border-bottom:1px solid var(--fondo-borde); font-size:9.5px; }
    table.datos td:first-child { font-weight:700; color:var(--color-hover); width:38%; }

    .valor-total-box { background:var(--color-muy-claro); border:1px solid var(--color-claro); border-radius:6px; text-align:center; padding:8px 6px; margin:8px 0; }
    .valor-total-label { font-size:8.5px; font-weight:700; color:var(--color-principal); text-transform:uppercase; letter-spacing:.06em; }
    .valor-total-num { font-size:18px; font-weight:800; color:var(--color-sidebar-2); margin:2px 0; }

    .saldos { display:flex; gap:8px; margin-bottom:8px; }
    .saldo-box { flex:1; border:1px solid var(--color-muy-claro); border-radius:4px; padding:5px 6px; text-align:center; }
    .saldo-box-label { font-size:8px; color:#9ca3af; font-weight:700; text-transform:uppercase; }
    .saldo-box-val { font-size:10px; font-weight:700; color:#1c2b22; }

    .obs-box { background:var(--fondo-card-alt); border-radius:4px; padding:5px 7px; margin-bottom:8px; font-size:9px; color:#4b5563; }
    .obs-label { font-size:8px; font-weight:700; color:var(--color-principal); margin-bottom:2px; }

    .firmas { width:100%; border-collapse:collapse; margin:10px 0 6px; }
    .firmas td { padding:4px 8px; width:50%; text-align:center; vertical-align:bottom; }
    .linea-firma { border-top:1px solid #1c2b22; padding-top:3px; font-size:8.5px; font-weight:700; }

    .footer { text-align:center; font-size:8px; color:#9ca3af; border-top:1px solid var(--color-muy-claro); padding-top:5px; margin-top:6px; }
    .nota-conserve { text-align:center; font-size:8.5px; color:var(--color-principal); font-weight:700; margin-bottom:4px; }
</style>
</head>
<body>

{{-- Encabezado consultorio --}}
<div class="header">
    @if($configuracion)
    <div class="consultorio-nombre">{{ $configuracion->nombre_consultorio ?? 'Consultorio Odontológico' }}</div>
    <div class="consultorio-info">
        {{ $configuracion->direccion ?? '' }}
        @if($configuracion->telefono) · Tel: {{ $configuracion->telefono }}@endif
        @if($configuracion->nit) · NIT: {{ $configuracion->nit }}@endif
    </div>
    @else
    <div class="consultorio-nombre">Consultorio Odontológico</div>
    @endif
</div>

{{-- Título --}}
<div class="titulo-recibo"><h1>RECIBO DE PAGO</h1></div>
<div class="num-recibo">{{ $pago->numero_recibo }}</div>

@if($pago->anulado)
<div class="anulado-banner">⚠ PAGO ANULADO — {{ $pago->motivo_anulacion }}</div>
@endif

{{-- Datos --}}
<table class="datos">
    <tr>
        <td>Fecha:</td>
        <td>{{ $pago->fecha_pago->translatedFormat('d \d\e F \d\e Y') }}</td>
    </tr>
    <tr>
        <td>Paciente:</td>
        <td>{{ $pago->paciente->nombre_completo }}</td>
    </tr>
    <tr>
        <td>Documento:</td>
        <td>{{ $pago->paciente->tipo_documento }} {{ $pago->paciente->numero_documento }}</td>
    </tr>
    <tr>
        <td>Concepto:</td>
        <td>{{ $pago->concepto }}</td>
    </tr>
    @if($pago->tratamiento)
    <tr>
        <td>Tratamiento:</td>
        <td>{{ $pago->tratamiento->nombre }}
            @if($pago->tratamiento->numero_tratamiento)
            <span style="font-family:monospace;font-size:9px;color:var(--color-principal);"> ({{ $pago->tratamiento->numero_tratamiento }})</span>
            @endif
        </td>
    </tr>
    @endif
    <tr>
        <td>Método de pago:</td>
        <td>{{ $pago->metodo_pago_label }}</td>
    </tr>
    @if($pago->referencia_pago)
    <tr>
        <td>Referencia:</td>
        <td>{{ $pago->referencia_pago }}</td>
    </tr>
    @endif
    <tr>
        <td>Registrado por:</td>
        <td>{{ $pago->cajero?->name ?? '—' }}</td>
    </tr>
</table>

{{-- Valor total --}}
<div class="valor-total-box">
    <div class="valor-total-label">Valor pagado</div>
    <div class="valor-total-num">$ {{ number_format($pago->valor, 0, ',', '.') }}</div>
</div>

{{-- Saldos del tratamiento --}}
@if($pago->tratamiento)
@php
    $t = $pago->tratamiento;
    $pagadoTotal = $t->valor_total - $t->saldo_pendiente;
@endphp
<table style="width:100%;border-collapse:collapse;margin-bottom:8px;">
    <tr>
        <td style="width:50%;padding:4px;text-align:center;border:1px solid var(--color-muy-claro);border-radius:4px;">
            <div style="font-size:8px;color:#9ca3af;font-weight:700;text-transform:uppercase;">Valor total tratamiento</div>
            <div style="font-size:10px;font-weight:700;">$ {{ number_format($t->valor_total, 0, ',', '.') }}</div>
        </td>
        <td style="width:50%;padding:4px;text-align:center;border:1px solid var(--color-muy-claro);border-radius:4px;">
            <div style="font-size:8px;color:#9ca3af;font-weight:700;text-transform:uppercase;">Saldo restante</div>
            <div style="font-size:10px;font-weight:700;color:{{ $t->saldo_pendiente > 0 ? '#dc2626' : '#166534' }};">
                $ {{ number_format($t->saldo_pendiente, 0, ',', '.') }}
            </div>
        </td>
    </tr>
</table>
@endif

{{-- Observaciones --}}
@if($pago->observaciones)
<div class="obs-box">
    <div class="obs-label">Observaciones:</div>
    {{ $pago->observaciones }}
</div>
@endif


<div class="nota-conserve">Conserve este recibo como soporte de su pago</div>
<div class="footer">
    Este documento es un comprobante de pago válido · Generado el {{ now()->translatedFormat('d \d\e F \d\e Y H:i') }}
</div>

</body>
</html>
