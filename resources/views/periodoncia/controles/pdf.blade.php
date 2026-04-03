<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Control Periodontal {{ $control->numero_control }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1a1a1a; background: white; line-height: 1.4; }
.pagina { padding: 26px 36px 64px; max-width: 210mm; }

/* ── HEADER ───────────────────────────────────────────── */
.pdf-hdr { display: table; width: 100%; padding-bottom: 12px; border-bottom: 3px solid {{ $colorPDF }}; margin-bottom: 16px; }
.hdr-logo-cell { display: table-cell; width: 14%; vertical-align: middle; }
.hdr-logo-cell img { max-height: 60px; max-width: 130px; }
.hdr-info-cell { display: table-cell; vertical-align: middle; padding: 0 14px; }
.hdr-doc-cell  { display: table-cell; width: 28%; vertical-align: middle; text-align: right; }

.cons-nombre { font-size: 14px; font-weight: bold; color: {{ $colorPDF }}; line-height: 1.2; }
.cons-cargo  { font-size: 10px; color: {{ $colorPDF }}; margin-top: 2px; }
.cons-datos  { font-size: 7.5px; color: #6b7280; margin-top: 5px; line-height: 1.7; }
.cons-datos span { margin-right: 8px; }

.doc-badge { border: 1.5px solid {{ $colorPDF }}; }
.doc-badge-head { background: {{ $colorPDF }}; color: #fff; font-size: 8px; font-weight: bold; padding: 5px 10px; text-transform: uppercase; letter-spacing: .1em; text-align: center; }
.doc-badge-body { padding: 6px 10px; text-align: center; background: #f0f5fb; }
.doc-badge-num  { font-size: 13px; font-weight: bold; color: {{ $colorPDF }}; font-family: monospace; display: block; }
.doc-badge-fecha { font-size: 7.5px; color: #6b7280; display: block; margin-top: 2px; }

/* ── TÍTULO ───────────────────────────────────────────── */
.titulo-doc { text-align: center; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 2.5px; color: {{ $colorPDF }}; margin-bottom: 14px; padding-bottom: 5px; border-bottom: 1px solid #c7d2e0; }

/* ── PACIENTE ─────────────────────────────────────────── */
.pac-blk  { background: #eff6ff; border-left: 4px solid {{ $colorPDF }}; padding: 9px 14px; margin-bottom: 14px; }
.pac-grid { display: table; width: 100%; }
.pac-cell { display: table-cell; vertical-align: top; padding-right: 10px; }
.pac-cell:last-child { padding-right: 0; }
.pac-lbl  { font-size: 7px; font-weight: bold; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; }
.pac-val  { font-size: 10.5px; font-weight: bold; color: #111827; margin-top: 1px; }
.pac-det  { font-size: 8px; color: #4b5563; }

/* ── SECCIONES ────────────────────────────────────────── */
.sec-titulo { font-size: 8px; font-weight: bold; color: {{ $colorPDF }}; text-transform: uppercase; letter-spacing: .07em; background: #f0f5fb; border-left: 4px solid {{ $colorPDF }}; padding: 4px 8px; margin-bottom: 8px; }
.dato-label { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: .4px; color: #888; margin-bottom: 2px; }
.dato-val   { font-size: 10.5px; font-weight: 600; color: #1a1a1a; }

/* ── SONDAJE ──────────────────────────────────────────── */
.sond-table { border-collapse: collapse; width: 100%; font-size: 8px; }
.sond-table th, .sond-table td { border: 1px solid #ccc; padding: 2px 3px; text-align: center; }
.sond-table thead th { background: #f0f4f8; font-weight: bold; color: {{ $colorPDF }}; }
.sond-lbl { background: #f8f8f8; font-weight: bold; color: #555; text-align: left; padding-left: 4px; }
.sv1 { background: #dcfce7; color: #166534; font-weight: bold; border-radius: 2px; padding: 0 2px; }
.sv2 { background: #fef9c3; color: #854d0e; font-weight: bold; border-radius: 2px; padding: 0 2px; }
.sv3 { background: #ffedd5; color: #9a3412; font-weight: bold; border-radius: 2px; padding: 0 2px; }
.sv4 { background: #fee2e2; color: #7f1d1d; font-weight: bold; border-radius: 2px; padding: 0 2px; }

/* ── FIRMA ────────────────────────────────────────────── */
.firma-sec  { margin-top: 32px; display: table; width: 100%; }
.firma-izq  { display: table-cell; vertical-align: bottom; width: 50%; }
.firma-der  { display: table-cell; vertical-align: bottom; text-align: right; width: 50%; }
.firma-canvas { height: 52px; }
.firma-img  { max-height: 52px; max-width: 180px; }
.firma-linea { border-top: 1px solid #1a1a1a; padding-top: 4px; }
.firma-nombre { font-size: 10.5px; font-weight: bold; color: #1a1a1a; }
.firma-cargo  { font-size: 9px; color: #666; margin-top: 1px; }
.firma-reg    { font-size: 8.5px; color: #999; margin-top: 1px; }

/* ── FOOTER FIJO ──────────────────────────────────────── */
.pdf-foot { position: fixed; bottom: 0; left: 36px; right: 36px; border-top: 1.5px solid #c7d2e0; padding: 5px 0 6px; background: #fff; display: table; width: calc(100% - 72px); }
.pf-l { display: table-cell; font-size: 7px; color: #9ca3af; text-align: left; vertical-align: middle; }
.pf-c { display: table-cell; font-size: 7px; color: #9ca3af; text-align: center; vertical-align: middle; }
.pf-r { display: table-cell; font-size: 7px; color: #9ca3af; text-align: right; vertical-align: middle; font-family: monospace; }
</style>
</head>
<body>
<div class="pagina">

{{-- ── FOOTER FIJO ── --}}
<div class="pdf-foot">
    <div class="pf-l">
        {{ $config?->nombre_consultorio }}
        @if($config?->direccion) · {{ $config->direccion }}@endif
        @if($config?->ciudad) · {{ $config->ciudad }}@endif
        @if($config?->telefono) · Tel: {{ $config->telefono }}@endif
    </div>
    <div class="pf-c">Documento generado el {{ now()->format('d/m/Y H:i') }}</div>
    <div class="pf-r">{{ $control->numero_control }}</div>
</div>

{{-- ── HEADER ── --}}
<div class="pdf-hdr">
    <div class="hdr-logo-cell">
        @if($config?->logo_path)
            <img src="{{ public_path('storage/' . $config->logo_path) }}" alt="Logo">
        @else
            <div style="font-size:22px;font-weight:bold;color:{{ $colorPDF }};">{{ mb_strtoupper(mb_substr($config?->nombre_consultorio ?? 'OD',0,2)) }}</div>
        @endif
    </div>
    <div class="hdr-info-cell">
        <div class="cons-nombre">{{ $config?->firma_nombre_doctor ?: ($config?->nombre_consultorio ?? '') }}</div>
        @if($config?->firma_cargo)<div class="cons-cargo">{{ $config->firma_cargo }}</div>@endif
        @if($config?->nombre_consultorio && $config?->firma_nombre_doctor)
            <div style="font-size:10px;color:#4b5563;margin-top:2px;">{{ $config->nombre_consultorio }}</div>
        @endif
        <div class="cons-datos">
            @if($config?->nit)<span>NIT: {{ $config->nit }}</span>@endif
            @if($config?->firma_registro)<span>Reg. Prof.: {{ $config->firma_registro }}</span>
            @elseif($config?->registro_medico)<span>Reg. Prof.: {{ $config->registro_medico }}</span>@endif
            @if($config?->telefono)<span>Tel: {{ $config->telefono }}</span>@endif
            @if($config?->email)<span>{{ $config->email }}</span>@endif
            @if($config?->direccion)<br><span>{{ $config->direccion }}@if($config?->ciudad), {{ $config->ciudad }}@endif</span>@endif
        </div>
    </div>
    <div class="hdr-doc-cell">
        <div class="doc-badge">
            <div class="doc-badge-head">CONTROL PERIODONTAL</div>
            <div class="doc-badge-body">
                <span class="doc-badge-num">{{ $control->numero_control }}</span>
                <span class="doc-badge-fecha">{{ $control->fecha_control->locale('es')->translatedFormat('d \d\e F \d\e Y') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- ── TÍTULO ── --}}
<div class="titulo-doc">Control Periodontal — Sesión #{{ $control->numero_sesion }}</div>

{{-- ── DATOS PACIENTE ── --}}
<div class="pac-blk">
    <div class="pac-grid">
        <div class="pac-cell">
            <div class="pac-lbl">Paciente</div>
            <div class="pac-val">{{ $control->fichaPeriodontal->paciente->nombre_completo }}</div>
            <div class="pac-det">{{ $control->fichaPeriodontal->paciente->tipo_documento }}: {{ $control->fichaPeriodontal->paciente->numero_documento }}</div>
        </div>
        <div class="pac-cell">
            <div class="pac-lbl">Historia Clínica</div>
            <div class="pac-val">{{ $control->fichaPeriodontal->paciente->numero_historia }}</div>
        </div>
        <div class="pac-cell">
            <div class="pac-lbl">Ficha Periodontal</div>
            <div class="pac-val" style="font-family:monospace;">{{ $control->fichaPeriodontal->numero_ficha }}</div>
        </div>
        <div class="pac-cell">
            <div class="pac-lbl">Profesional</div>
            <div class="pac-val">{{ $control->periodoncista?->name ?? '—' }}</div>
        </div>
    </div>
</div>

{{-- ── INFORMACIÓN DEL CONTROL ── --}}
<div style="margin-bottom:12px;">
    <div class="sec-titulo">Información del Control</div>
    <table style="border-collapse:collapse;width:100%;font-size:10px;">
        <tr>
            <td style="padding:4px 8px;border:1px solid #e0e0e0;width:33%;">
                <div class="dato-label">Tipo de Sesión</div>
                <div class="dato-val">{{ $control->tipo_sesion_label }}</div>
            </td>
            <td style="padding:4px 8px;border:1px solid #e0e0e0;width:33%;">
                <div class="dato-label">Fecha de Control</div>
                <div class="dato-val">{{ $control->fecha_control->format('d/m/Y') }}</div>
            </td>
            <td style="padding:4px 8px;border:1px solid #e0e0e0;width:34%;">
                <div class="dato-label">Próxima Cita</div>
                <div class="dato-val">{{ $control->proxima_cita_semanas ? $control->proxima_cita_semanas . ' semanas' : '—' }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ── ÍNDICES DE CONTROL ── --}}
@if($control->indice_placa_control !== null || $control->indice_gingival_control !== null || $control->anestesia_utilizada)
<div style="margin-bottom:12px;">
    <div class="sec-titulo">Índices de Control</div>
    <table style="border-collapse:collapse;width:100%;font-size:10px;">
        <tr>
            @if($control->indice_placa_control !== null)
            <td style="padding:6px 10px;border:1px solid #e0e0e0;width:25%;text-align:center;">
                <div class="dato-label">Índice de Placa</div>
                <div style="font-size:20px;font-weight:800;color:{{ $control->indice_placa_control < 20 ? '#16a34a' : ($control->indice_placa_control < 40 ? '#d97706' : '#dc2626') }};">
                    {{ number_format($control->indice_placa_control, 1) }}%
                </div>
            </td>
            @endif
            @if($control->indice_gingival_control !== null)
            <td style="padding:6px 10px;border:1px solid #e0e0e0;width:25%;text-align:center;">
                <div class="dato-label">Índice Gingival</div>
                <div style="font-size:20px;font-weight:800;color:{{ $control->indice_gingival_control < 1 ? '#16a34a' : ($control->indice_gingival_control < 2 ? '#d97706' : '#dc2626') }};">
                    {{ number_format($control->indice_gingival_control, 2) }}
                </div>
            </td>
            @endif
            @if($control->anestesia_utilizada)
            <td style="padding:6px 10px;border:1px solid #e0e0e0;">
                <div class="dato-label">Anestesia Utilizada</div>
                <div class="dato-val">{{ $control->anestesia_utilizada }}</div>
            </td>
            @endif
        </tr>
    </table>
</div>
@endif

{{-- ── ZONAS TRATADAS ── --}}
@if($control->zonas_tratadas && count($control->zonas_tratadas))
<div style="margin-bottom:12px;">
    <div class="sec-titulo">Zonas Tratadas</div>
    <div style="padding:6px 10px;border:1px solid #e0e0e0;font-size:10px;background:#f9fafb;">
        @foreach((array)$control->zonas_tratadas as $zona)
        <span style="background:#f0f4f8;border:1px solid #c7d2e0;border-radius:3px;padding:1px 6px;font-weight:bold;font-family:monospace;margin:2px 2px;display:inline-block;">{{ $zona }}</span>
        @endforeach
    </div>
</div>
@endif

{{-- ── PROCEDIMIENTO / INSTRUMENTOS ── --}}
@if($control->instrumentos_utilizados)
<div style="margin-bottom:12px;">
    <div class="sec-titulo">Procedimiento / Instrumentos</div>
    <div style="padding:6px 10px;border:1px solid #e0e0e0;font-size:10px;line-height:1.6;">{!! nl2br(e($control->instrumentos_utilizados)) !!}</div>
</div>
@endif

{{-- ── OBSERVACIONES CLÍNICAS ── --}}
@if($control->observaciones)
<div style="margin-bottom:12px;">
    <div class="sec-titulo">Observaciones Clínicas</div>
    <div style="padding:6px 10px;border:1px solid #e0e0e0;font-size:10px;line-height:1.6;">{!! nl2br(e($control->observaciones)) !!}</div>
</div>
@endif

{{-- ── INDICACIONES AL PACIENTE ── --}}
@if($control->indicaciones_paciente)
<div style="margin-bottom:12px;">
    <div class="sec-titulo">Indicaciones para el Paciente</div>
    <div style="padding:6px 10px;border:1px solid #e0e0e0;font-size:10px;line-height:1.6;background:#fffbf0;">{!! nl2br(e($control->indicaciones_paciente)) !!}</div>
</div>
@endif

{{-- ── SONDAJE DE CONTROL ── --}}
@if($control->sondaje_control && count($control->sondaje_control))
<div style="margin-bottom:12px;">
    <div class="sec-titulo">Sondaje de Control</div>
    @php
        $sc = $control->sondaje_control;
        $dSup = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
        $dInf = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];
        function svCtrlPdf($v) {
            if (!is_numeric($v) || $v == '') return '<span style="color:#ccc;">—</span>';
            $vv = (int)$v;
            if ($vv <= 3) return '<span class="sv1">'.$v.'</span>';
            if ($vv <= 5) return '<span class="sv2">'.$v.'</span>';
            if ($vv <= 7) return '<span class="sv3">'.$v.'</span>';
            return '<span class="sv4">'.$v.'</span>';
        }
    @endphp
    <p style="font-size:8px;font-weight:bold;text-transform:uppercase;color:#6b7280;margin:3px 0 3px;">Superior</p>
    <table class="sond-table" style="margin-bottom:6px;">
        <thead><tr>
            <th style="width:28px;text-align:left;">Cara</th>
            @foreach($dSup as $d)<th>{{ $d }}</th>@endforeach
        </tr></thead>
        <tbody>
        @foreach([['MV','mvc'],['V','vc'],['DV','dvc'],['ML','mlc'],['L','lc'],['DL','dlc']] as $f)
        <tr><td class="sond-lbl">{{ $f[0] }}</td>
            @foreach($dSup as $d)<td>{!! svCtrlPdf($sc[$d][$f[1]] ?? '') !!}</td>@endforeach
        </tr>
        @endforeach
        </tbody>
    </table>
    <p style="font-size:8px;font-weight:bold;text-transform:uppercase;color:#6b7280;margin:3px 0 3px;">Inferior</p>
    <table class="sond-table">
        <thead><tr>
            <th style="width:28px;text-align:left;">Cara</th>
            @foreach($dInf as $d)<th>{{ $d }}</th>@endforeach
        </tr></thead>
        <tbody>
        @foreach([['MV','mvc'],['V','vc'],['DV','dvc'],['ML','mlc'],['L','lc'],['DL','dlc']] as $f)
        <tr><td class="sond-lbl">{{ $f[0] }}</td>
            @foreach($dInf as $d)<td>{!! svCtrlPdf($sc[$d][$f[1]] ?? '') !!}</td>@endforeach
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- ── FIRMA PROFESIONAL ── --}}
<div class="firma-sec">
    <div class="firma-izq">
        <div class="firma-canvas">
            @if($config?->firma_path)
            <img src="{{ public_path('storage/' . $config->firma_path) }}" class="firma-img" alt="Firma">
            @endif
        </div>
        <div class="firma-linea">
            <div class="firma-nombre">{{ $config?->firma_nombre_doctor ?: ($config?->nombre_consultorio ?? '') }}</div>
            @if($config?->firma_cargo)<div class="firma-cargo">{{ $config->firma_cargo }}</div>@endif
            @if($config?->firma_registro || $config?->registro_medico)
            <div class="firma-reg">Reg. Prof.: {{ $config->firma_registro ?: $config->registro_medico }}</div>
            @endif
        </div>
    </div>
    <div class="firma-der">
        <div style="border:1.5px solid {{ $colorPDF }};padding:7px 14px;text-align:center;display:inline-block;background:#f0f5fb;">
            <div style="font-size:7.5px;text-transform:uppercase;letter-spacing:.5px;color:{{ $colorPDF }};font-weight:bold;">Generado</div>
            <div style="font-size:8px;color:#6b7280;margin-top:2px;">{{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</div>

<x-pdf-pie-profesional :config="$config" :colorPDF="$colorPDF" />

</div>
</body>
</html>
