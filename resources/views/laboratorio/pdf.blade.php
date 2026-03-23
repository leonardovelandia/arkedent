@php $C = '#1a3a6b'; @endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #111827; padding: 35px; line-height: 1.65; }

/* ── ENCABEZADO ── */
.hdr { display: table; width: 100%; border-bottom: 2.5px solid {{ $C }}; padding-bottom: 12px; margin-bottom: 18px; }
.hdr-logo { display: table-cell; width: 22%; vertical-align: middle; }
.hdr-logo img { max-height: 65px; max-width: 150px; }
.hdr-info { display: table-cell; vertical-align: middle; text-align: center; }
.hdr-right { display: table-cell; width: 22%; vertical-align: middle; }
.hdr-nombre { font-size: 15px; font-weight: bold; color: {{ $C }}; }
.hdr-sub { font-size: 8px; color: #4b5563; margin-top: 2px; }

/* ── TÍTULO DOCUMENTO ── */
.doc-titulo { text-align: center; padding: 10px 0 16px; }
.doc-tipo { font-size: 14px; font-weight: bold; color: #111827; text-transform: uppercase; letter-spacing: .08em; }
.doc-numero { font-size: 11px; color: {{ $C }}; font-weight: bold; margin-top: 3px; }
.doc-sub { font-size: 9px; color: #4b5563; margin-top: 2px; }

/* ── SECCIONES ── */
.seccion-titulo { font-size: 8px; font-weight: bold; color: {{ $C }}; text-transform: uppercase; letter-spacing: .07em; border-bottom: 1.5px solid {{ $C }}; padding-bottom: 3px; margin: 14px 0 10px; }
.dato-tabla { display: table; width: 100%; margin-bottom: 4px; }
.dato-fila { display: table-row; }
.dato-lbl { display: table-cell; width: 36%; font-size: 8px; font-weight: bold; color: #6b7280; text-transform: uppercase; letter-spacing: .04em; padding: 2px 8px 2px 0; vertical-align: top; }
.dato-val { display: table-cell; font-size: 10px; color: #111827; padding: 2px 0; vertical-align: top; }

/* ── FIRMAS ── */
.firma-tabla { display: table; width: 100%; margin-top: 30px; border-top: 1.5px solid {{ $C }}; padding-top: 14px; }
.firma-col { display: table-cell; width: 50%; vertical-align: top; padding-right: 20px; }
.firma-col:last-child { padding-right: 0; padding-left: 20px; border-left: 1px solid #e5e7eb; }
.firma-titulo { font-size: 8px; font-weight: bold; color: {{ $C }}; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px; }
.firma-img { max-height: 65px; max-width: 200px; display: block; }
.firma-linea { border-top: 1px solid #374151; padding-top: 4px; margin-top: 75px; font-size: 8px; color: #4b5563; }
.firma-linea-img { border-top: 1px solid #374151; padding-top: 4px; margin-top: 4px; font-size: 8px; color: #4b5563; }

/* ── PIE ── */
.footer { position: fixed; bottom: 20px; left: 35px; right: 35px; border-top: 1px solid #e5e7eb; padding-top: 5px; display: table; width: calc(100% - 70px); }
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
    <div class="doc-tipo">Orden de Laboratorio</div>
    <div class="doc-numero">{{ $orden->numero_orden }}</div>
    <div class="doc-sub">Documento Oficial · {{ $orden->created_at->format('d/m/Y') }}</div>
</div>

{{-- ── DATOS GENERALES ── --}}
<div class="seccion-titulo">Datos Generales</div>
<div class="dato-tabla">
    <div class="dato-fila">
        <div class="dato-lbl">Paciente</div>
        <div class="dato-val">{{ $orden->paciente->nombre_completo }}</div>
    </div>
    <div class="dato-fila">
        <div class="dato-lbl">Documento</div>
        <div class="dato-val">{{ $orden->paciente->tipo_documento }} {{ $orden->paciente->numero_documento }}</div>
    </div>
    <div class="dato-fila">
        <div class="dato-lbl">Laboratorio</div>
        <div class="dato-val">{{ $orden->laboratorio->nombre }}</div>
    </div>
    <div class="dato-fila">
        <div class="dato-lbl">Doctor</div>
        <div class="dato-val">{{ $orden->doctor->name ?? '—' }}</div>
    </div>
    
</div>

{{-- ── ESPECIFICACIONES ── --}}
<div class="seccion-titulo">Especificaciones del Trabajo</div>
<div class="dato-tabla">
    <div class="dato-fila">
        <div class="dato-lbl">Tipo de Trabajo</div>
        <div class="dato-val">{{ $orden->tipo_trabajo }}</div>
    </div>
    @if($orden->dientes)
    <div class="dato-fila">
        <div class="dato-lbl">Dientes</div>
        <div class="dato-val">{{ $orden->dientes }}</div>
    </div>
    @endif
    @if($orden->color_diente)
    <div class="dato-fila">
        <div class="dato-lbl">Color (Guía Vita)</div>
        <div class="dato-val">{{ $orden->color_diente }}</div>
    </div>
    @endif
    @if($orden->material)
    <div class="dato-fila">
        <div class="dato-lbl">Material</div>
        <div class="dato-val">{{ $orden->material }}</div>
    </div>
    @endif
</div>
<div style="margin-top:6px; font-size:8px; font-weight:bold; color:#6b7280; text-transform:uppercase; letter-spacing:.04em;">Descripción Detallada</div>
<div class="">{{ $orden->descripcion }}</div>

{{-- ── FECHAS ── --}}
<div class="seccion-titulo">Fechas</div>
<div class="dato-tabla">
    @if($orden->fecha_envio)
    <div class="dato-fila">
        <div class="dato-lbl">Fecha de Envío</div>
        <div class="dato-val">{{ $orden->fecha_envio->format('d/m/Y') }}</div>
    </div>
    @endif
    @if($orden->fecha_entrega_estimada)
    <div class="dato-fila">
        <div class="dato-lbl">Entrega Estimada</div>
        <div class="dato-val">{{ $orden->fecha_entrega_estimada->format('d/m/Y') }}</div>
    </div>
    @endif
    @if($orden->fecha_recepcion)
    <div class="dato-fila">
        <div class="dato-lbl">Fecha Recepción</div>
        <div class="dato-val">{{ $orden->fecha_recepcion->format('d/m/Y') }}</div>
    </div>
    @endif
    @if($orden->precio_laboratorio)
    <div class="dato-fila">
        <div class="dato-lbl">Precio Laboratorio</div>
        <div class="dato-val">${{ number_format($orden->precio_laboratorio, 0, ',', '.') }}</div>
    </div>
    @endif
</div>

@if($orden->observaciones_envio)
<div class="seccion-titulo">Observaciones</div>
<div class="">{{ $orden->observaciones_envio }}</div>
@endif

{{-- ── FIRMAS ── --}}
<div class="firma-tabla">
    <div class="firma-col">
        <div class="firma-titulo">Firma del Doctor</div>
        @if($config->firma_path)
            <img src="{{ public_path('storage/' . $config->firma_path) }}" class="firma-img" alt="Firma">
            <div class="firma-linea-img">
                {{ $config->firma_nombre_doctor ?? ($orden->doctor->name ?? '') }}<br>
                {{ $config->firma_cargo ?? 'Odontólogo(a)' }}
                @if($config->firma_registro) · Reg. {{ $config->firma_registro }}@endif
            </div>
        @else
            <div class="firma-linea">
                {{ $config->firma_nombre_doctor ?? ($orden->doctor->name ?? '') }}<br>
                {{ $config->firma_cargo ?? 'Odontólogo(a)' }}
            </div>
        @endif
    </div>
    <div class="firma-col">
        <div class="firma-titulo">Recibido por el Laboratorio</div>
        <div class="firma-linea">
            {{ $orden->laboratorio->nombre }}<br>
            @if($orden->laboratorio->contacto ?? false){{ $orden->laboratorio->contacto }}@endif
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
    <div class="fl-r">{{ $orden->numero_orden }} · Generado el {{ now()->format('d/m/Y') }}</div>
</div>

</body>
</html>
