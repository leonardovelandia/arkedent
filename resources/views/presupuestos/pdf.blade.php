@php $C = '#1a3a6b'; @endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; padding: 30px; line-height: 1.5; }
.hdr { display: table; width: 100%; border-bottom: 2.5px solid {{ $C }}; padding-bottom: 12px; margin-bottom: 14px; }
.hdr-logo img { max-height: 65px; max-width: 160px; }
.hdr-logo { display: table-cell; width: 22%; vertical-align: middle; }
.hdr-info { display: table-cell; vertical-align: middle; text-align: center; }
.hdr-right { display: table-cell; width: 22%; vertical-align: middle; }
.hdr-nombre { font-size: 15px; font-weight: bold; color: {{ $C }}; }
.hdr-sub { font-size: 8px; color: #4b5563; margin-top: 1px; }
.doc-titulo { text-align: center; padding: 8px 0 12px; }
.doc-tipo { font-size: 11px; font-weight: bold; color: {{ $C }}; text-transform: uppercase; letter-spacing: .08em; }
.doc-num { font-size: 12px; color: {{ $C }}; font-family: monospace; font-weight: bold; margin-top: 2px; }
.doc-fecha { font-size: 8px; color: #9ca3af; margin-top: 2px; }
.badge { padding: 2px 9px; border-radius: 3px; font-size: 8px; font-weight: bold; }
.badge-aprobado { background: #d1fae5; color: #065f46; }
.badge-enviado  { background: #dbeafe; color: #1e40af; }
.badge-borrador { background: #f3f4f6; color: #374151; }
.badge-rechazado{ background: #fee2e2; color: #991b1b; }
.badge-vencido  { background: #fef3c7; color: #92400e; }
.s { margin-bottom: 13px; }
.s-titulo { font-size: 8px; font-weight: bold; color: {{ $C }}; text-transform: uppercase; letter-spacing: .07em; border-bottom: 1.5px solid {{ $C }}; padding-bottom: 3px; margin-bottom: 7px; }
.grid { display: table; width: 100%; }
.col { display: table-cell; width: 50%; vertical-align: top; padding-right: 14px; }
.col:last-child { padding-right: 0; }
.f { margin-bottom: 4px; }
.fl { font-weight: bold; color: #374151; }
table { width: 100%; border-collapse: collapse; font-size: 9px; }
th { background: {{ $C }}; color: #fff; padding: 5px 7px; text-align: left; font-size: 8px; }
td { padding: 4px 7px; border-bottom: 1px solid #f3f4f6; }
tr:nth-child(even) td { background: #f9fafb; }
.tr-realizado td { background: #f0fdf4; }
.tr-total td { font-weight: bold; background: #eff6ff; font-size: 10px; }
.total-val { font-size: 12px; font-weight: bold; color: {{ $C }}; }
.cond { background: #f8faff; border-left: 3px solid {{ $C }}; padding: 7px 10px; margin-bottom: 8px; font-size: 9px; }
.firma-wrap { margin-top: 22px; border-top: 1.5px solid {{ $C }}; padding-top: 14px; }
.firma-tabla { display: table; width: 100%; }
.firma-col { display: table-cell; width: 50%; vertical-align: bottom; padding-right: 20px; }
.firma-col:last-child { padding-right: 0; padding-left: 20px; border-left: 1px solid #e5e7eb; }
.firma-titulo { font-size: 9px; font-weight: bold; color: #374151; margin-bottom: 5px; }
.firma-img { max-height: 60px; max-width: 190px; display: block; }
.firma-linea { border-top: 1px solid #374151; padding-top: 4px; margin-top: 45px; font-size: 8px; color: #4b5563; }
.firma-linea-img { border-top: 1px solid #374151; padding-top: 4px; margin-top: 4px; font-size: 8px; color: #4b5563; }
.badge-ok { color: #166534; font-weight: bold; }
.footer { margin-top: 18px; border-top: 1px solid #e5e7eb; padding-top: 6px; display: table; width: 100%; }
.fl-l { display: table-cell; font-size: 7px; color: #9ca3af; text-align: left; }
.fl-r { display: table-cell; font-size: 7px; color: #9ca3af; text-align: right; }

</style>
</head>
<body>

{{-- ── ENCABEZADO ── --}}
<div class="hdr">
    <div class="hdr-logo">
        @if($config->logo_path)
            <img src="{{ public_path('storage/' . $config->logo_path) }}" alt="Logo">
        @else
            <div style="font-size:18px;font-weight:bold;color:{{ $C }};">{{ mb_strtoupper(mb_substr($config->nombre_consultorio,0,2)) }}</div>
        @endif
    </div>
    <div class="hdr-info">
        <div class="hdr-nombre">{{ $config->nombre_consultorio }}</div>
        @if($config->slogan)<div class="hdr-sub">{{ $config->slogan }}</div>@endif
    </div>
    <div class="hdr-right"></div>
</div>

{{-- ── TÍTULO ── --}}
<div class="doc-titulo">
    <div class="doc-tipo">Presupuesto de Tratamiento</div>
    <div class="doc-num">{{ $presupuesto->numero_formateado }}</div>
    <div style="margin-top:4px;">
        <span class="badge badge-{{ $presupuesto->estado }}">{{ strtoupper($presupuesto->estado) }}</span>
    </div>
</div>

{{-- ── DATOS ── --}}
<div class="s">
    <div class="s-titulo">Datos del Paciente y Presupuesto</div>
    <div class="grid">
        <div class="col">
            <div class="f"><span class="fl">Paciente:</span> {{ $presupuesto->paciente->nombre_completo }}</div>
            <div class="f"><span class="fl">Documento:</span> {{ $presupuesto->paciente->tipo_documento }} {{ $presupuesto->paciente->numero_documento }}</div>
            <div class="f"><span class="fl">N° Historia:</span> {{ $presupuesto->paciente->numero_historia }}</div>
        </div>
        <div class="col">
            <div class="f"><span class="fl">N° Presupuesto:</span> {{ $presupuesto->numero_formateado }}</div>
            <div class="f"><span class="fl">Fecha:</span> {{ $presupuesto->fecha_generacion->format('d/m/Y') }}</div>
            <div class="f"><span class="fl">Válido hasta:</span> {{ $presupuesto->fecha_vencimiento->format('d/m/Y') }}</div>
            <div class="f"><span class="fl">Doctor(a):</span> {{ $presupuesto->doctor->name }}</div>
        </div>
    </div>
</div>

{{-- ── PROCEDIMIENTOS ── --}}
<div class="s">
    <div class="s-titulo">Detalle de Procedimientos</div>
    <table>
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:38%">Procedimiento</th>
                <th style="width:10%">Diente</th>
                <th style="width:10%">Cara</th>
                <th style="width:8%">Cant.</th>
                <th style="width:14%">Valor Unit.</th>
                <th style="width:15%">Total</th>
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
                <td colspan="6" style="text-align:right;font-weight:bold;">Subtotal:</td>
                <td>$ {{ number_format($presupuesto->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($presupuesto->descuento_valor > 0)
            <tr>
                <td colspan="6" style="text-align:right;font-weight:bold;">Descuento ({{ $presupuesto->descuento_porcentaje }}%):</td>
                <td>- $ {{ number_format($presupuesto->descuento_valor, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="tr-total">
                <td colspan="6" style="text-align:right;">TOTAL:</td>
                <td class="total-val">$ {{ number_format($presupuesto->total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>

@if($presupuesto->condiciones_pago)
<div class="cond"><strong>Condiciones de pago:</strong> {{ $presupuesto->condiciones_pago }}</div>
@endif
@if($presupuesto->observaciones)
<div class="cond"><strong>Observaciones:</strong> {{ $presupuesto->observaciones }}</div>
@endif

{{-- ── FIRMAS ── --}}
<div class="firma-wrap">
    <div class="firma-tabla">
        <div class="firma-col">
            <div class="firma-titulo">Firma de Aprobación del Paciente</div>
            @if($presupuesto->firmado && $presupuesto->firma_data)
                <img src="{{ $presupuesto->firma_data }}" class="firma-img" alt="Firma paciente">
                <div class="firma-linea-img">
                    {{ $presupuesto->paciente->nombre_completo }}<br>
                    {{ $presupuesto->paciente->tipo_documento }}: {{ $presupuesto->paciente->numero_documento }}<br>
                    @if($presupuesto->fecha_aprobacion)<span class="badge-ok">✓ Aprobado el {{ $presupuesto->fecha_aprobacion->format('d/m/Y H:i') }}</span>@endif
                </div>
            @else
                <div class="firma-linea">
                    {{ $presupuesto->paciente->nombre_completo }}<br>
                    {{ $presupuesto->paciente->tipo_documento }}: {{ $presupuesto->paciente->numero_documento }}
                </div>
            @endif
        </div>
        <div class="firma-col">
            <div class="firma-titulo">Profesional Tratante</div>
            @if($config->firma_path)
                <img src="{{ public_path('storage/' . $config->firma_path) }}" class="firma-img" alt="Firma doctor">
                <div class="firma-linea-img">
                    {{ $config->firma_nombre_doctor ?? $presupuesto->doctor->name }}<br>
                    {{ $config->firma_cargo ?? 'Odontólogo(a)' }}<br>
                    @if($config->firma_registro)Reg. {{ $config->firma_registro }}@endif
                </div>
            @else
                <div class="firma-linea">
                    {{ $config->firma_nombre_doctor ?? $presupuesto->doctor->name }}<br>
                    {{ $config->firma_cargo ?? 'Odontólogo(a)' }}<br>
                    @if($config->firma_registro)Reg. {{ $config->firma_registro }}@endif
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ── PIE ── --}}
<div class="footer">
    <div class="fl-l">
        {{ $config->nombre_consultorio }}
        @if($config->direccion) · {{ $config->direccion }}@endif
        @if($config->ciudad) · {{ $config->ciudad }}@endif
        @if($config->telefono) · Tel: {{ $config->telefono }}@endif
    </div>
    <div class="fl-r">
        Validez: {{ $presupuesto->validez_dias }} días · Generado el {{ now()->format('d/m/Y H:i') }} · {{ $presupuesto->numero_formateado }}
    </div>
</div>

</body>
</html>
