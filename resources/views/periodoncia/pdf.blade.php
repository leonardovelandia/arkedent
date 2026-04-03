<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Ficha Periodontal {{ $ficha->numero_ficha }}</title>
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

/* ── CONTROLES ────────────────────────────────────────── */
.ctrl-item { border: 1px solid #e0e0e0; padding: 6px 8px; margin-bottom: 6px; }
.ctrl-header { display: table; width: 100%; margin-bottom: 4px; }
.ctrl-h-izq { display: table-cell; font-size: 9px; font-weight: bold; color: {{ $colorPDF }}; }
.ctrl-h-der { display: table-cell; text-align: right; font-size: 8.5px; color: #666; }

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
        @if($config?->telefono) · Tel: {{ $config->telefono }}@endif
        @if($config?->email) · {{ $config->email }}@endif
    </div>
    <div class="pf-c">Generado el {{ now()->format('d/m/Y H:i') }}</div>
    <div class="pf-r">{{ $ficha->numero_ficha }}</div>
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
        <div class="cons-nombre">{{ $config?->firma_nombre_doctor ?: ($config?->nombre_consultorio ?? 'Consultorio') }}</div>
        @if($config?->firma_cargo)<div class="cons-cargo">{{ $config->firma_cargo }}</div>@endif
        @if($config?->nombre_consultorio && $config?->firma_nombre_doctor)
            <div style="font-size:10px;color:#4b5563;margin-top:2px;">{{ $config->nombre_consultorio }}</div>
        @endif
        <div class="cons-datos">
            @if($config?->nit)<span>NIT: {{ $config->nit }}</span>@endif
            @if($config?->firma_registro || $config?->registro_medico)
                <span>Reg. Prof.: {{ $config->firma_registro ?: $config->registro_medico }}</span>
            @endif
            @if($config?->telefono)<span>Tel: {{ $config->telefono }}</span>@endif
            @if($config?->email)<span>{{ $config->email }}</span>@endif
            @if($config?->direccion)<br><span>{{ $config->direccion }}@if($config?->ciudad), {{ $config->ciudad }}@endif</span>@endif
        </div>
    </div>
    <div class="hdr-doc-cell">
        <div class="doc-badge">
            <div class="doc-badge-head">FICHA PERIODONTAL</div>
            <div class="doc-badge-body">
                <span class="doc-badge-num">{{ $ficha->numero_ficha }}</span>
                <span class="doc-badge-fecha">{{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- ── DATOS PACIENTE ── --}}
<div style="margin-bottom:12px;">
    <div class="sec-titulo">Datos del Paciente</div>
    <table style="border-collapse:collapse;width:100%;font-size:10px;">
        <tr>
            <td style="padding:4px 8px;border:1px solid #e0e0e0;width:40%;">
                <div class="dato-label">Paciente</div>
                <div class="dato-val">{{ $ficha->paciente->nombre_completo }}</div>
            </td>
            <td style="padding:4px 8px;border:1px solid #e0e0e0;width:20%;">
                <div class="dato-label">{{ $ficha->paciente->tipo_documento }}</div>
                <div class="dato-val">{{ $ficha->paciente->numero_documento }}</div>
            </td>
            <td style="padding:4px 8px;border:1px solid #e0e0e0;width:20%;">
                <div class="dato-label">N° Historia</div>
                <div class="dato-val">{{ $ficha->paciente->numero_historia }}</div>
            </td>
            <td style="padding:4px 8px;border:1px solid #e0e0e0;width:20%;">
                <div class="dato-label">N° Ficha</div>
                <div class="dato-val" style="font-family:monospace;color:{{ $colorPDF }};">{{ $ficha->numero_ficha }}</div>
            </td>
        </tr>
        <tr>
            <td style="padding:4px 8px;border:1px solid #e0e0e0;">
                <div class="dato-label">Doctor(a)</div>
                <div class="dato-val">{{ $ficha->periodoncista?->name ?? '—' }}</div>
            </td>
            <td style="padding:4px 8px;border:1px solid #e0e0e0;">
                <div class="dato-label">Fecha inicio</div>
                <div class="dato-val">{{ $ficha->fecha_inicio->format('d/m/Y') }}</div>
            </td>
            <td style="padding:4px 8px;border:1px solid #e0e0e0;">
                <div class="dato-label">Estado</div>
                <div class="dato-val">{{ $ficha->estado_label }}</div>
            </td>
            <td style="padding:4px 8px;border:1px solid #e0e0e0;">
                <div class="dato-label">Controles</div>
                <div class="dato-val">{{ $ficha->controles->count() }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ── DIAGNÓSTICO PERIODONTAL ── --}}
<div style="margin-bottom:12px;">
    <div class="sec-titulo">Diagnóstico Periodontal</div>
    <table style="border-collapse:collapse;width:100%;font-size:10px;">
        <tr>
            <td style="padding:4px 8px;border:1px solid #e0e0e0;width:45%;">
                <div class="dato-label">Clasificación</div>
                <div class="dato-val">{{ $ficha->clasificacion_label ?: '—' }}</div>
            </td>
            <td style="padding:4px 8px;border:1px solid #e0e0e0;width:20%;">
                <div class="dato-label">Extensión</div>
                <div class="dato-val">{{ $ficha->extension ? ucfirst($ficha->extension) : '—' }}</div>
            </td>
            <td style="padding:4px 8px;border:1px solid #e0e0e0;width:20%;">
                <div class="dato-label">Severidad</div>
                <div class="dato-val">{{ $ficha->severidad ? ucfirst($ficha->severidad) : '—' }}</div>
            </td>
            <td style="padding:4px 8px;border:1px solid #e0e0e0;width:15%;">
                <div class="dato-label">Pronóstico</div>
                <div class="dato-val">{{ $ficha->pronostico_general ? ucfirst(str_replace('_',' ',$ficha->pronostico_general)) : '—' }}</div>
            </td>
        </tr>
        @if($ficha->factores_riesgo && count($ficha->factores_riesgo))
        <tr>
            <td colspan="4" style="padding:4px 8px;border:1px solid #e0e0e0;">
                <div class="dato-label">Factores de riesgo</div>
                <div class="dato-val">{{ implode(', ', array_map('ucfirst', $ficha->factores_riesgo)) }}</div>
            </td>
        </tr>
        @endif
    </table>
    @if($ficha->diagnostico_texto)
    <div style="margin-top:5px;padding:5px 8px;border:1px solid #e0e0e0;background:#fafafa;">
        <div class="dato-label">Diagnóstico</div>
        <div style="font-size:10px;margin-top:2px;line-height:1.5;">{{ $ficha->diagnostico_texto }}</div>
    </div>
    @endif
</div>

{{-- ── ÍNDICES PERIODONTALES ── --}}
@if($ficha->indice_placa_porcentaje !== null || $ficha->indice_gingival_porcentaje !== null)
<div style="margin-bottom:12px;">
    <div class="sec-titulo">Índices Periodontales</div>
    <table style="border-collapse:collapse;width:100%;font-size:10px;">
        <tr>
            @if($ficha->indice_placa_porcentaje !== null)
            <td style="padding:4px 8px;border:1px solid #e0e0e0;width:33%;">
                <div class="dato-label">Índice de Placa O'Leary</div>
                <div style="font-size:16px;font-weight:800;color:{{ $ficha->indice_placa_porcentaje < 20 ? '#16a34a' : ($ficha->indice_placa_porcentaje < 40 ? '#d97706' : '#dc2626') }};">
                    {{ number_format($ficha->indice_placa_porcentaje, 1) }}%
                </div>
                @if($ficha->fecha_indice_placa)
                <div style="font-size:8.5px;color:#888;">{{ $ficha->fecha_indice_placa->format('d/m/Y') }}</div>
                @endif
            </td>
            @endif
            @if($ficha->indice_gingival_porcentaje !== null)
            <td style="padding:4px 8px;border:1px solid #e0e0e0;width:33%;">
                <div class="dato-label">Placa S&amp;L Modificado</div>
                @php $slPdfColor = $ficha->indice_gingival_porcentaje <= 15 ? '#16a34a' : ($ficha->indice_gingival_porcentaje <= 30 ? '#d97706' : '#dc2626'); @endphp
                <div style="font-size:16px;font-weight:800;color:{{ $slPdfColor }};">
                    {{ number_format($ficha->indice_gingival_porcentaje, 1) }}%
                </div>
                @if($ficha->fecha_indice_gingival)
                <div style="font-size:8.5px;color:#888;">{{ $ficha->fecha_indice_gingival->format('d/m/Y') }}</div>
                @endif
            </td>
            @endif
        </tr>
    </table>
</div>
@endif

{{-- ── PLAN DE TRATAMIENTO ── --}}
@if($ficha->plan_tratamiento)
<div style="margin-bottom:12px;">
    <div class="sec-titulo">Plan de Tratamiento</div>
    <div style="padding:5px 8px;border:1px solid #e0e0e0;font-size:10px;line-height:1.5;">{!! nl2br(e($ficha->plan_tratamiento)) !!}</div>
</div>
@endif

{{-- ── PERIODONTOGRAMA — SONDAJE INICIAL ── --}}
@if($ficha->sondaje_datos && count($ficha->sondaje_datos))
<div style="margin-bottom:12px;">
    <div class="sec-titulo">Periodontograma — Sondaje Inicial
        @if($ficha->fecha_sondaje)({{ $ficha->fecha_sondaje->format('d/m/Y') }})@endif
    </div>
    @php
        $sd   = $ficha->sondaje_datos;
        $dSup = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
        $dInf = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];
        function svPdf($v) {
            if (!is_numeric($v) || $v == 0) return '<span style="color:#ccc;">—</span>';
            $vv = (int)$v;
            if ($vv <= 3) return '<span class="sv1">'.$v.'</span>';
            if ($vv <= 5) return '<span class="sv2">'.$v.'</span>';
            if ($vv <= 7) return '<span class="sv3">'.$v.'</span>';
            return '<span class="sv4">'.$v.'</span>';
        }
    @endphp
    <p style="font-size:8px;font-weight:bold;text-transform:uppercase;color:#888;margin:3px 0 2px;">Superior</p>
    <table class="sond-table" style="margin-bottom:5px;">
        <thead><tr>
            <th style="width:30px;text-align:left;">Cara</th>
            @foreach($dSup as $d)<th style="min-width:15px;">{{ $d }}</th>@endforeach
        </tr></thead>
        <tbody>
        @foreach([['MV','mv'],['V','v'],['DV','dv'],['ML','ml'],['L','l'],['DL','dl']] as $f)
        <tr><td class="sond-lbl">{{ $f[0] }}</td>
            @foreach($dSup as $d)<td>{!! svPdf($sd[$d][$f[1]] ?? '') !!}</td>@endforeach
        </tr>
        @endforeach
        </tbody>
    </table>
    <p style="font-size:8px;font-weight:bold;text-transform:uppercase;color:#888;margin:3px 0 2px;">Inferior</p>
    <table class="sond-table">
        <thead><tr>
            <th style="width:30px;text-align:left;">Cara</th>
            @foreach($dInf as $d)<th style="min-width:15px;">{{ $d }}</th>@endforeach
        </tr></thead>
        <tbody>
        @foreach([['MV','mv'],['V','v'],['DV','dv'],['ML','ml'],['L','l'],['DL','dl']] as $f)
        <tr><td class="sond-lbl">{{ $f[0] }}</td>
            @foreach($dInf as $d)<td>{!! svPdf($sd[$d][$f[1]] ?? '') !!}</td>@endforeach
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- ── HISTORIAL DE CONTROLES ── --}}
@if($ficha->controles->isNotEmpty())
<div style="margin-bottom:12px;">
    <div class="sec-titulo">Historial de Controles ({{ $ficha->controles->count() }})</div>
    @foreach($ficha->controles as $ctrl)
    <div class="ctrl-item">
        <div class="ctrl-header">
            <div class="ctrl-h-izq">Sesión #{{ $ctrl->numero_sesion }} — {{ $ctrl->tipo_sesion_label }}</div>
            <div class="ctrl-h-der">
                {{ $ctrl->fecha_control->format('d/m/Y') }}
                @if($ctrl->periodoncista) &nbsp;·&nbsp; Dr(a). {{ $ctrl->periodoncista->name }}@endif
            </div>
        </div>
        <div style="display:table;width:100%;font-size:9px;">
            @if($ctrl->indice_placa_control !== null || $ctrl->indice_gingival_control !== null)
            <div style="display:table-cell;width:30%;">
                @if($ctrl->indice_placa_control !== null)Placa: <strong>{{ number_format($ctrl->indice_placa_control,1) }}%</strong>@endif
                @if($ctrl->indice_gingival_control !== null) &nbsp; ÍG: <strong>{{ number_format($ctrl->indice_gingival_control,2) }}</strong>@endif
            </div>
            @endif
            @if($ctrl->observaciones)
            <div style="display:table-cell;color:#444;">{{ Str::limit($ctrl->observaciones, 100) }}</div>
            @endif
        </div>
    </div>
    @endforeach
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
            <div class="firma-nombre">{{ $config?->firma_nombre_doctor ?? '' }}</div>
            @if($config?->firma_cargo)<div class="firma-cargo">{{ $config->firma_cargo }}</div>@endif
            @if($config?->firma_registro || $config?->registro_medico)
            <div class="firma-reg">Reg. Prof.: {{ $config->firma_registro ?: $config->registro_medico }}</div>
            @endif
        </div>
    </div>
    <div class="firma-der">
        <div style="border:1.5px solid {{ $colorPDF }};padding:6px 14px;text-align:center;display:inline-block;background:#f0f5fb;">
            <div style="font-size:7px;text-transform:uppercase;letter-spacing:.5px;color:{{ $colorPDF }};font-weight:bold;">Generado</div>
            <div style="font-size:8px;color:#6b7280;margin-top:1px;">{{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</div>

<x-pdf-pie-profesional :config="$config" :colorPDF="$colorPDF" />

</div>
</body>
</html>
