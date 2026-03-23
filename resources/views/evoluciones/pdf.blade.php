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
.s { margin-bottom: 13px; }
.s-titulo { font-size: 8px; font-weight: bold; color: {{ $C }}; text-transform: uppercase; letter-spacing: .07em; border-bottom: 1.5px solid {{ $C }}; padding-bottom: 3px; margin-bottom: 7px; }
.grid { display: table; width: 100%; }
.col { display: table-cell; width: 50%; vertical-align: top; padding-right: 14px; }
.col:last-child { padding-right: 0; }
.f { margin-bottom: 4px; }
.fl { font-weight: bold; color: #374151; }
table { width: 100%; border-collapse: collapse; margin-top: 4px; }
th { background: {{ $C }}; color: #fff; font-size: 8px; padding: 4px 6px; text-align: left; }
td { font-size: 9px; padding: 4px 6px; border-bottom: 1px solid #f3f4f6; }
tr:nth-child(even) td { background: #f9fafb; }
.proxima { background: #f0fdf4; border-left: 3px solid #16a34a; padding: 7px 10px; margin-top: 4px; font-size: 9px; }
.firma-wrap { margin-top: 22px; border-top: 1.5px solid {{ $C }}; padding-top: 14px; }
.firma-tabla { display: table; width: 100%; }
.firma-col { display: table-cell; width: 50%; vertical-align: bottom; padding-right: 20px; }
.firma-col:last-child { padding-right: 0; padding-left: 20px; border-left: 1px solid #e5e7eb; }
.firma-titulo { font-size: 9px; font-weight: bold; color: #374151; margin-bottom: 5px; }
.firma-img { max-height: 65px; max-width: 200px; display: block; }
.firma-linea { border-top: 1px solid #374151; padding-top: 4px; margin-top: 45px; font-size: 8px; color: #4b5563; }
.firma-linea-img { border-top: 1px solid #374151; padding-top: 4px; margin-top: 4px; font-size: 8px; color: #4b5563; }
.badge-ok { color: #166534; font-weight: bold; }
.meta { font-size: 7px; color: #9ca3af; margin-top: 8px; }
.corr { margin-top: 18px; border-top: 1px dashed {{ $C }}; padding-top: 12px; }
.corr-item { border-left: 2px solid {{ $C }}; padding: 5px 9px; margin-bottom: 7px; background: #f8faff; }
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
    <div class="doc-tipo">Evolución Clínica — Documento Oficial</div>
    @if($evolucion->numero_evolucion)
    <div class="doc-num">{{ $evolucion->numero_evolucion }}</div>
    @endif
    <div class="doc-fecha">Generado el {{ now()->format('d/m/Y H:i') }}</div>
</div>

{{-- ── DATOS ── --}}
<div class="s">
    <div class="s-titulo">Datos del Paciente y Evolución</div>
    <div class="grid">
        <div class="col">
            <div class="f"><span class="fl">Paciente:</span> {{ $evolucion->paciente->nombre_completo }}</div>
            <div class="f"><span class="fl">Documento:</span> {{ $evolucion->paciente->tipo_documento }} {{ $evolucion->paciente->numero_documento }}</div>
            <div class="f"><span class="fl">N° Historia:</span> {{ $evolucion->paciente->numero_historia }}</div>
            @if($evolucion->numero_evolucion)<div class="f"><span class="fl">N° Doc. EVO:</span> <strong style="color:{{ $C }};font-family:monospace;">{{ $evolucion->numero_evolucion }}</strong></div>@endif
        </div>
        <div class="col">
            <div class="f"><span class="fl">Fecha:</span> {{ $evolucion->fecha instanceof \Carbon\Carbon ? $evolucion->fecha->format('d/m/Y') : \Carbon\Carbon::parse($evolucion->fecha)->format('d/m/Y') }}</div>
            @if($evolucion->hora_inicio ?? null)<div class="f"><span class="fl">Hora:</span> {{ $evolucion->hora_inicio }}</div>@endif
            <div class="f"><span class="fl">Doctor:</span> {{ $evolucion->doctor->name ?? '—' }}</div>
        </div>
    </div>
</div>

{{-- ── PROCEDIMIENTO ── --}}
<div class="s">
    <div class="s-titulo">Procedimiento Realizado</div>
    <div class="f" style="font-size:11px;font-weight:bold;color:{{ $C }};">{{ $evolucion->procedimiento }}</div>
    @if($evolucion->dientes_tratados)<div class="f"><span class="fl">Dientes tratados:</span> {{ $evolucion->dientes_tratados }}</div>@endif
</div>

{{-- ── DESCRIPCIÓN ── --}}
@if($evolucion->descripcion)
<div class="s">
    <div class="s-titulo">Descripción Clínica</div>
    <div>{{ $evolucion->descripcion }}</div>
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

{{-- ── SIGNOS VITALES ── --}}
@if($evolucion->presion_arterial || $evolucion->frecuencia_cardiaca)
<div class="s">
    <div class="s-titulo">Signos Vitales</div>
    <div class="grid">
        @if($evolucion->presion_arterial)<div class="col"><div class="f"><span class="fl">Presión arterial:</span> {{ $evolucion->presion_arterial }}</div></div>@endif
        @if($evolucion->frecuencia_cardiaca)<div class="col"><div class="f"><span class="fl">Frec. cardíaca:</span> {{ $evolucion->frecuencia_cardiaca }} bpm</div></div>@endif
    </div>
</div>
@endif

{{-- ── PRÓXIMA CITA ── --}}
@if($evolucion->proxima_cita_fecha || $evolucion->proxima_cita_procedimiento)
<div class="s">
    <div class="s-titulo">Próxima Cita Sugerida</div>
    <div class="proxima">
        @if($evolucion->proxima_cita_fecha)<div class="f"><span class="fl">Fecha:</span> {{ \Carbon\Carbon::parse($evolucion->proxima_cita_fecha)->format('d/m/Y') }}</div>@endif
        @if($evolucion->proxima_cita_procedimiento)<div class="f"><span class="fl">Procedimiento:</span> {{ $evolucion->proxima_cita_procedimiento }}</div>@endif
    </div>
</div>
@endif

{{-- ── OBSERVACIONES ── --}}
@if($evolucion->observaciones)
<div class="s">
    <div class="s-titulo">Observaciones</div>
    <div>{{ $evolucion->observaciones }}</div>
</div>
@endif

{{-- ── FIRMAS ── --}}
<div class="firma-wrap">
    <div class="firma-tabla">
        <div class="firma-col">
            <div class="firma-titulo">Firma del Paciente</div>
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
        <div class="firma-col">
            <div class="firma-titulo">Profesional Tratante</div>
            @if($config->firma_path)
                <img src="{{ public_path('storage/' . $config->firma_path) }}" class="firma-img" alt="Firma doctor">
                <div class="firma-linea-img">
                    {{ $config->firma_nombre_doctor ?? ($evolucion->doctor->name ?? auth()->user()->name) }}<br>
                    {{ $config->firma_cargo ?? 'Odontólogo(a)' }}<br>
                    @if($config->firma_registro)Reg. {{ $config->firma_registro }}@endif
                </div>
            @else
                <div class="firma-linea">
                    {{ $config->firma_nombre_doctor ?? ($evolucion->doctor->name ?? auth()->user()->name) }}<br>
                    {{ $config->firma_cargo ?? 'Odontólogo(a)' }}<br>
                    @if($config->firma_registro)Reg. {{ $config->firma_registro }}@endif
                </div>
            @endif
        </div>
    </div>
    @if($evolucion->firmado)
    <div class="meta">Documento firmado digitalmente el {{ $evolucion->fecha_firma->format('d/m/Y \a \l\a\s H:i') }} · IP: {{ $evolucion->ip_firma }}</div>
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

{{-- ── PIE ── --}}
<div class="footer">
    <div class="fl-l">
        {{ $config->nombre_consultorio }}
        @if($config->direccion) · {{ $config->direccion }}@endif
        @if($config->ciudad) · {{ $config->ciudad }}@endif
        @if($config->telefono) · Tel: {{ $config->telefono }}@endif
    </div>
    <div class="fl-r">
        Evolución {{ $evolucion->paciente->numero_historia }}@if($evolucion->numero_evolucion) · Doc. {{ $evolucion->numero_evolucion }}@endif · {{ $evolucion->fecha instanceof \Carbon\Carbon ? $evolucion->fecha->format('d/m/Y') : \Carbon\Carbon::parse($evolucion->fecha)->format('d/m/Y') }}
    </div>
</div>

</body>
</html>
