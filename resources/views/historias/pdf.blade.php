@php $C = '#1a3a6b'; $CL = '#eff6ff'; @endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; padding: 30px; line-height: 1.5; }

/* ── Encabezado ── */
.hdr { display: table; width: 100%; border-bottom: 2.5px solid {{ $C }}; padding-bottom: 12px; margin-bottom: 14px; }
.hdr-logo  { display: table-cell; width: 22%; vertical-align: middle; }
.hdr-logo img { max-height: 65px; max-width: 160px; }
.hdr-info  { display: table-cell; vertical-align: middle; text-align: center; }
.hdr-right { display: table-cell; width: 22%; vertical-align: middle; }
.hdr-nombre { font-size: 15px; font-weight: bold; color: {{ $C }}; }
.hdr-sub    { font-size: 8px; color: #4b5563; margin-top: 1px; }

/* ── Título del documento ── */
.doc-titulo { text-align: center; padding: 8px 0 12px; }
.doc-tipo   { font-size: 11px; font-weight: bold; color: {{ $C }}; text-transform: uppercase; letter-spacing: .08em; }
.doc-num    { font-size: 12px; color: {{ $C }}; font-family: monospace; font-weight: bold; margin-top: 2px; }
.doc-fecha  { font-size: 8px; color: #9ca3af; margin-top: 2px; }

/* ── Secciones sin caja ── */
.s { margin-bottom: 13px; }
.s-titulo { font-size: 8px; font-weight: bold; color: {{ $C }}; text-transform: uppercase; letter-spacing: .07em; border-bottom: 1.5px solid {{ $C }}; padding-bottom: 3px; margin-bottom: 7px; }
.grid { display: table; width: 100%; }
.col { display: table-cell; width: 50%; vertical-align: top; padding-right: 14px; }
.col:last-child { padding-right: 0; }
.f { margin-bottom: 4px; }
.fl { font-weight: bold; color: #374151; }

/* ── Tabla ── */
table { width: 100%; border-collapse: collapse; margin-top: 4px; }
th { background: {{ $C }}; color: #fff; font-size: 8px; padding: 4px 6px; text-align: left; }
td { font-size: 9px; padding: 4px 6px; border-bottom: 1px solid #f3f4f6; }
tr:nth-child(even) td { background: #f9fafb; }

/* ── Firmas ── */
.firma-wrap { margin-top: 22px; border-top: 1.5px solid {{ $C }}; padding-top: 14px; }
.firma-tabla { display: table; width: 100%; }
.firma-col   { display: table-cell; width: 50%; vertical-align: bottom; padding-right: 20px; }
.firma-col:last-child { padding-right: 0; padding-left: 20px; border-left: 1px solid #e5e7eb; }
.firma-titulo { font-size: 9px; font-weight: bold; color: #374151; margin-bottom: 5px; }
.firma-img    { max-height: 65px; max-width: 200px; display: block; }
.firma-linea  { border-top: 1px solid #374151; padding-top: 4px; margin-top: 45px; font-size: 8px; color: #4b5563; }
.firma-linea-img { border-top: 1px solid #374151; padding-top: 4px; margin-top: 4px; font-size: 8px; color: #4b5563; }
.badge-ok { color: #166534; font-weight: bold; }
.meta { font-size: 7px; color: #9ca3af; margin-top: 8px; }

/* ── Correcciones ── */
.corr { margin-top: 18px; border-top: 1px dashed {{ $C }}; padding-top: 12px; }
.corr-item { border-left: 2px solid {{ $C }}; padding: 5px 9px; margin-bottom: 7px; background: #f8faff; }

/* ── Pie ── */
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
            <div style="font-size:18px;font-weight:bold;color:{{ $C }};">
                {{ mb_strtoupper(mb_substr($config->nombre_consultorio, 0, 2)) }}
            </div>
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
    <div class="doc-tipo">Historia Clínica — Documento Oficial</div>
    @if($historia->numero_historia)
    <div class="doc-num">{{ $historia->numero_historia }}</div>
    @endif
    <div class="doc-fecha">Generado el {{ now()->format('d/m/Y H:i') }}</div>
</div>

{{-- ── DATOS PACIENTE ── --}}
<div class="s">
    <div class="s-titulo">Datos del Paciente</div>
    <div class="grid">
        <div class="col">
            <div class="f"><span class="fl">Nombre:</span> {{ $historia->paciente->nombre_completo }}</div>
            <div class="f"><span class="fl">Documento:</span> {{ $historia->paciente->tipo_documento }} {{ $historia->paciente->numero_documento }}</div>
            <div class="f"><span class="fl">Fecha nac.:</span> {{ $historia->paciente->fecha_nacimiento ? $historia->paciente->fecha_nacimiento->format('d/m/Y') : '—' }}</div>
        </div>
        <div class="col">
            <div class="f"><span class="fl">N° Historia:</span> {{ $historia->paciente->numero_historia }}</div>
            @if($historia->numero_historia)<div class="f"><span class="fl">N° Doc. HC:</span> <strong style="color:{{ $C }};font-family:monospace;">{{ $historia->numero_historia }}</strong></div>@endif
            <div class="f"><span class="fl">Teléfono:</span> {{ $historia->paciente->telefono ?? '—' }}</div>
            <div class="f"><span class="fl">Fecha apertura:</span> {{ $historia->fecha_apertura ? $historia->fecha_apertura->format('d/m/Y') : '—' }}</div>
        </div>
    </div>
</div>

{{-- ── MOTIVO ── --}}
@if($historia->motivo_consulta)
<div class="s">
    <div class="s-titulo">Motivo de Consulta</div>
    <div class="f">{{ $historia->motivo_consulta }}</div>
    @if($historia->enfermedad_actual)
    <div class="f" style="margin-top:4px;"><span class="fl">Enfermedad actual:</span> {{ $historia->enfermedad_actual }}</div>
    @endif
</div>
@endif

{{-- ── ANTECEDENTES ── --}}
@if($historia->antecedentes_medicos || $historia->medicamentos_actuales || $historia->alergias || $historia->antecedentes_odontologicos || $historia->antecedentes_familiares || $historia->habitos)
<div class="s">
    <div class="s-titulo">Antecedentes</div>
    <div class="grid">
        <div class="col">
            @if($historia->antecedentes_medicos)<div class="f"><span class="fl">Antecedentes médicos:</span><br>{{ $historia->antecedentes_medicos }}</div>@endif
            @if($historia->medicamentos_actuales)<div class="f" style="margin-top:3px;"><span class="fl">Medicamentos:</span><br>{{ $historia->medicamentos_actuales }}</div>@endif
            @if($historia->alergias)<div class="f" style="margin-top:3px;"><span class="fl">Alergias:</span><br>{{ $historia->alergias }}</div>@endif
        </div>
        <div class="col">
            @if($historia->antecedentes_odontologicos)<div class="f"><span class="fl">Antecedentes odontológicos:</span><br>{{ $historia->antecedentes_odontologicos }}</div>@endif
            @if($historia->antecedentes_familiares)<div class="f" style="margin-top:3px;"><span class="fl">Antecedentes familiares:</span><br>{{ $historia->antecedentes_familiares }}</div>@endif
            @if($historia->habitos)<div class="f" style="margin-top:3px;"><span class="fl">Hábitos:</span><br>{{ $historia->habitos }}</div>@endif
        </div>
    </div>
</div>
@endif

{{-- ── SIGNOS VITALES ── --}}
@if($historia->presion_arterial || $historia->frecuencia_cardiaca || $historia->temperatura || $historia->peso || $historia->talla)
<div class="s">
    <div class="s-titulo">Signos Vitales</div>
    <table>
        <tr>
            <th>Presión Arterial</th><th>Frec. Cardíaca</th><th>Temperatura</th><th>Peso</th><th>Talla</th>
        </tr>
        <tr>
            <td>{{ $historia->presion_arterial ?? '—' }}</td>
            <td>{{ $historia->frecuencia_cardiaca ? $historia->frecuencia_cardiaca.' bpm' : '—' }}</td>
            <td>{{ $historia->temperatura ? $historia->temperatura.' °C' : '—' }}</td>
            <td>{{ $historia->peso ? $historia->peso.' kg' : '—' }}</td>
            <td>{{ $historia->talla ? $historia->talla.' m' : '—' }}</td>
        </tr>
    </table>
</div>
@endif

{{-- ── OBSERVACIONES ── --}}
@if($historia->observaciones_generales)
<div class="s">
    <div class="s-titulo">Observaciones Generales</div>
    <div>{{ $historia->observaciones_generales }}</div>
</div>
@endif

{{-- ── FIRMAS ── --}}
<div class="firma-wrap">
    <div class="firma-tabla">
        <div class="firma-col">
            <div class="firma-titulo">Firma del Paciente</div>
            @if($historia->firmado)
                <img src="{{ $historia->firma_data }}" class="firma-img" alt="Firma del paciente">
                <div class="firma-linea-img">
                    {{ $historia->paciente->nombre_completo }}<br>
                    {{ $historia->paciente->tipo_documento }}: {{ $historia->paciente->numero_documento }}<br>
                    <span class="badge-ok">✓ Firmado digitalmente</span>
                </div>
            @else
                <div class="firma-linea">
                    {{ $historia->paciente->nombre_completo }}<br>
                    {{ $historia->paciente->tipo_documento }}: {{ $historia->paciente->numero_documento }}
                </div>
            @endif
        </div>
        <div class="firma-col">
            <div class="firma-titulo">Profesional Tratante</div>
            @if($config->firma_path)
                <img src="{{ public_path('storage/' . $config->firma_path) }}" class="firma-img" alt="Firma doctor">
                <div class="firma-linea-img">
                    {{ $config->firma_nombre_doctor ?? auth()->user()->name }}<br>
                    {{ $config->firma_cargo ?? 'Odontólogo(a)' }}<br>
                    @if($config->firma_registro)Reg. {{ $config->firma_registro }}@endif
                </div>
            @else
                <div class="firma-linea">
                    {{ $config->firma_nombre_doctor ?? auth()->user()->name }}<br>
                    {{ $config->firma_cargo ?? 'Odontólogo(a)' }}<br>
                    @if($config->firma_registro)Reg. {{ $config->firma_registro }}@endif
                </div>
            @endif
        </div>
    </div>
    @if($historia->firmado)
    <div class="meta">Documento firmado digitalmente el {{ $historia->fecha_firma->format('d/m/Y \a \l\a\s H:i') }} · IP: {{ $historia->ip_firma }}</div>
    @endif
</div>

{{-- ── CORRECCIONES ── --}}
@if($historia->correcciones->count() > 0)
<div class="corr">
    <div style="font-size:9px;font-weight:bold;color:{{ $C }};margin-bottom:6px;">NOTAS DE CORRECCIÓN ANEXAS</div>
    <div style="font-size:8px;color:#666;margin-bottom:8px;font-style:italic;">Correcciones agregadas tras la firma del documento original.</div>
    @foreach($historia->correcciones as $i => $correccion)
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
        Historia {{ $historia->paciente->numero_historia }}@if($historia->numero_historia) · Doc. {{ $historia->numero_historia }}@endif · Pág. 1
    </div>
</div>

{{-- ── CONSTANCIA DE FIRMA ELECTRÓNICA ── --}}
@if($historia->firmado && $historia->documento_hash)
    @php
        echo \App\Traits\TrazabilidadFirma::generarConstanciaFirmaPDF(
            [
                'firma_timestamp'          => $historia->firma_timestamp,
                'firma_ip'                 => $historia->ip_firma,
                'firma_dispositivo'        => $historia->firma_dispositivo,
                'firma_navegador'          => $historia->firma_navegador,
                'documento_hash'           => $historia->documento_hash,
                'firma_verificacion_token' => $historia->firma_verificacion_token,
            ],
            $historia->paciente->nombre_completo,
            $historia->paciente->tipo_documento,
            $historia->paciente->numero_documento,
            $C
        );
    @endphp
@endif

</body>
</html>
