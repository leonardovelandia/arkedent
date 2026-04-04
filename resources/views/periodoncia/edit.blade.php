@extends('layouts.app')
@section('titulo', 'Editar ' . $ficha->numero_ficha)

@push('estilos')
<style>
.periodo-card { margin-bottom: 1.25rem; }
.periodo-card-header {
    background: var(--color-principal);
    color: white;
    padding: .75rem 1rem;
    border-radius: 10px 10px 0 0;
    display: flex;
    align-items: center;
    gap: .5rem;
    font-weight: 700;
    font-size: .88rem;
}
.periodo-card-body { padding: 1.1rem; }
.form-label-per {
    font-size: .77rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--texto-secundario);
    margin-bottom: .3rem;
    display: block;
}
.form-ctrl-per {
    width: 100%;
    border: 1px solid var(--fondo-borde);
    border-radius: 8px;
    padding: .42rem .75rem;
    font-size: .875rem;
    background: var(--fondo-app);
    color: var(--texto-principal);
    transition: border-color .15s;
}
.form-ctrl-per:focus { outline: none; border-color: var(--color-principal); }
/* ═══ Periodontograma profesional ═══ */
/* Quitar flechas spinners de inputs numéricos */
.pmg::-webkit-inner-spin-button,.pmg::-webkit-outer-spin-button,
.pps::-webkit-inner-spin-button,.pps::-webkit-outer-spin-button { -webkit-appearance:none; margin:0; }
.pmg,.pps { -moz-appearance:textfield; }
.perio-wrap { overflow-x: auto; }
.perio-tbl { border-collapse: collapse; font-size:.62rem; white-space:nowrap; }
.perio-tbl td { border:1px solid var(--fondo-borde); padding:0; text-align:center; vertical-align:middle; height:19px; }
.pl { min-width:110px; width:110px; padding:1px 5px; text-align:right; font-size:.58rem; font-weight:700; text-transform:uppercase; color:var(--texto-secundario); background:var(--fondo-card-alt); border-right:2px solid var(--fondo-borde)!important; white-space:nowrap; }
.pt { font-weight:800; font-size:.7rem; color:var(--color-principal); background:var(--fondo-card-alt); padding:2px 0; min-width:54px; }
.pt.qs, td.qs { border-left:2px solid var(--color-principal)!important; }
.pmg { width:17px; border:none; background:transparent; color:#2563eb; font-size:.68rem; font-weight:700; text-align:center; outline:none; padding:0; display:block; margin:0 auto; }
.pps { width:17px; border:none; background:transparent; color:#d97706; font-size:.68rem; font-weight:700; text-align:center; outline:none; padding:0; display:block; margin:0 auto; }
.pnci { display:block; width:17px; font-size:.65rem; font-weight:700; color:var(--texto-secundario); text-align:center; margin:0 auto; min-width:17px; }
.psq { display:inline-block; width:13px; height:13px; border:1px solid var(--fondo-borde); cursor:pointer; background:transparent; border-radius:1px; vertical-align:middle; transition:background .1s; }
.psq.on-s { background:#dc2626; border-color:#dc2626; }
.psq.on-p { background:#3b82f6; border-color:#2563eb; }
.psel { border:1px solid var(--fondo-borde); border-radius:3px; font-size:.58rem; background:var(--fondo-app); color:var(--texto-principal); padding:0 1px; width:50px; height:17px; }
.paus-cb { width:11px; height:11px; accent-color:#7f1d1d; cursor:pointer; }
.pimpl-cb { width:11px; height:11px; accent-color:#374151; cursor:pointer; }
td.td-aus { opacity:.2!important; pointer-events:none; background:var(--fondo-borde)!important; }
canvas.pcv { display:block; }
.perio-section-label { font-size:.62rem; font-weight:700; text-transform:uppercase; color:var(--texto-secundario); padding:.35rem 0 .1rem; letter-spacing:.04em; }
.perio-stats-bar { display:flex; gap:1.25rem; align-items:center; flex-wrap:wrap; padding:.3rem .75rem; background:var(--fondo-card-alt); border:1px solid var(--fondo-borde); border-radius:6px; font-size:.78rem; margin-bottom:.5rem; }
.perio-mid-bar { text-align:center; font-size:.72rem; color:var(--texto-secundario); padding:.3rem; border-top:1px dashed var(--fondo-borde); border-bottom:1px dashed var(--fondo-borde); margin:.3rem 0; }
/* SVG triangular O'Leary */
.cuadro-svg { display: block; overflow: visible; }
.cara-svg { fill: transparent; cursor: pointer; transition: fill .1s; }
.cara-svg:hover { fill: rgba(220,38,38,0.18); }
.cara-svg.cara-activa { fill: #dc2626; }
.diente-oleary.ausente-oc .cuadro-svg { opacity: .3; pointer-events: none; }
.btn-ausente-oc {
    font-size: .52rem; font-weight: 700; line-height:1; padding: 1px 3px;
    border: 1px solid var(--fondo-borde); border-radius: 3px; cursor: pointer;
    background: var(--fondo-app); color: var(--texto-secundario);
    transition: background .1s, color .1s;
}
.diente-oleary.ausente-oc .btn-ausente-oc { background: #7f1d1d; color: #fca5a5; border-color: #7f1d1d; }
.sl-tabla { border-collapse:collapse; font-size:.72rem; min-width:100%; }
.sl-tabla th, .sl-tabla td { border:1px solid var(--fondo-borde); text-align:center; vertical-align:middle; }
.sl-diente-header { width:36px; min-width:36px; padding:0; background:var(--fondo-card-alt); }
.sl-diente-rotated { writing-mode:vertical-lr; transform:rotate(180deg); font-size:.6rem; font-weight:700; display:block; padding:.3rem .1rem; text-transform:uppercase; letter-spacing:.05em; color:var(--texto-secundario); }
.sl-grupo-header { font-size:.65rem; font-weight:700; padding:.4rem .25rem; background:var(--fondo-card-alt); color:var(--texto-principal); border-bottom:2px solid var(--color-principal); line-height:1.3; }
.sl-sup-header { font-size:.65rem; font-weight:700; padding:.25rem .2rem; background:var(--fondo-card-alt); color:var(--texto-secundario); min-width:26px; }
.sl-codigo-label { font-size:.65rem; font-weight:700; text-transform:uppercase; color:var(--texto-secundario); padding:.35rem .4rem; white-space:nowrap; background:var(--fondo-card-alt); }
.sl-cell { min-width:26px; width:28px; height:26px; cursor:pointer; background:var(--fondo-app); padding:0; position:relative; transition:background .1s; }
.sl-cell:hover:not(.ausente-sl) { background:var(--color-muy-claro); }
.sl-cell.activo-sl { background:#2a0a0a; }
.sl-cell.activo-sl::before, .sl-cell.activo-sl::after { content:''; position:absolute; width:80%; height:1.5px; background:#f87171; top:50%; left:50%; transform-origin:center; }
.sl-cell.activo-sl::before { transform:translate(-50%,-50%) rotate(45deg); }
.sl-cell.activo-sl::after  { transform:translate(-50%,-50%) rotate(-45deg); }
.sl-cell.ausente-sl { background:var(--fondo-borde); cursor:not-allowed; opacity:.45; pointer-events:none; }
.sl-grupo-sep, .sl-sup-sep { border-left: 2.5px solid #6B21A8 !important; }
.diente-oleary { display: inline-flex; flex-direction: column; align-items: center; margin: 0 2px; }
.diente-num-oc { font-size: .62rem; font-weight: 700; color: var(--texto-secundario); margin-bottom: 2px; line-height:1; }
.furcacion-sel, .movilidad-sel {
    border: 1px solid var(--fondo-borde);
    border-radius: 4px;
    font-size: .65rem;
    padding: .05rem .15rem;
    background: var(--fondo-app);
}
.sangrado-cb { width: 12px; height: 12px; cursor: pointer; accent-color: #dc2626; }

/* ── Classic overrides ── */
body:not([data-ui="glass"]) .periodo-card-body { background:#fff; border:1px solid var(--fondo-borde); border-top:none; }
body:not([data-ui="glass"]) .form-ctrl-per { background:var(--fondo-app); border:1px solid var(--fondo-borde); color:var(--texto-principal); }

/* ── Aurora Glass overrides ── */
body[data-ui="glass"] .periodo-card { border:1px solid rgba(0,234,255,0.35) !important; border-radius:10px !important; overflow:hidden !important; }
body[data-ui="glass"] .periodo-card-header { background:rgba(0,100,120,0.70) !important; }
body[data-ui="glass"] .periodo-card-body { background:rgba(255,255,255,0.08) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.20) !important; border-top:none !important; }
body[data-ui="glass"] .form-label-per { color:rgba(0,234,255,0.85) !important; }
body[data-ui="glass"] .form-ctrl-per  { background:rgba(255,255,255,0.08) !important; border:1px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
body[data-ui="glass"] .form-ctrl-per:focus { border-color:rgba(0,234,255,0.70) !important; }
body[data-ui="glass"] .perio-stats-bar { background:rgba(0,0,0,0.20) !important; border-color:rgba(0,234,255,0.20) !important; }
body[data-ui="glass"] .pl  { background:rgba(0,0,0,0.25) !important; color:rgba(0,234,255,0.80) !important; }
body[data-ui="glass"] .pt  { background:rgba(0,0,0,0.25) !important; color:rgba(0,234,255,0.90) !important; }
body[data-ui="glass"] .pmg { color:rgba(147,197,253,0.95) !important; }
body[data-ui="glass"] .pps { color:rgba(253,186,116,0.95) !important; }
body[data-ui="glass"] .psel { background:rgba(255,255,255,0.08) !important; border-color:rgba(0,234,255,0.25) !important; color:rgba(255,255,255,0.90) !important; }
body[data-ui="glass"] .sl-diente-header,
body[data-ui="glass"] .sl-grupo-header,
body[data-ui="glass"] .sl-sup-header,
body[data-ui="glass"] .sl-codigo-label { background:rgba(0,0,0,0.25) !important; color:rgba(0,234,255,0.80) !important; }
body[data-ui="glass"] .sl-cell { background:rgba(255,255,255,0.06) !important; }
body[data-ui="glass"] .sl-cell:hover:not(.ausente-sl) { background:rgba(0,234,255,0.12) !important; }
body[data-ui="glass"] .furcacion-sel,
body[data-ui="glass"] .movilidad-sel { background:rgba(255,255,255,0.08) !important; border-color:rgba(0,234,255,0.25) !important; color:rgba(255,255,255,0.90) !important; }
body[data-ui="glass"] .perio-tbl td { border-color:rgba(0,234,255,0.12) !important; }
</style>
@endpush

@section('contenido')

{{-- Breadcrumb --}}
<div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1.25rem;font-size:.82rem;flex-wrap:wrap;">
    <a href="{{ route('periodoncia.index') }}" style="color:var(--texto-secundario);text-decoration:none;">
        <i class="bi bi-heart-pulse me-1"></i>Periodoncia
    </a>
    <i class="bi bi-chevron-right" style="font-size:.65rem;color:var(--texto-secundario);"></i>
    <a href="{{ route('periodoncia.show', $ficha) }}" style="color:var(--texto-secundario);text-decoration:none;">{{ $ficha->numero_ficha }}</a>
    <i class="bi bi-chevron-right" style="font-size:.65rem;color:var(--texto-secundario);"></i>
    <span style="color:var(--texto-principal);font-weight:600;">Editar</span>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#7f1d1d;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <i class="bi bi-exclamation-triangle me-1"></i>
    @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
</div>
@endif

<form method="POST" action="{{ route('periodoncia.update', $ficha) }}" id="formPeriodEdit">
@csrf @method('PUT')

{{-- CARD 1: Datos generales --}}
<div class="card-sistema periodo-card">
    <div class="periodo-card-header">
        <i class="bi bi-person-vcard"></i> Datos Generales —
        <span style="font-family:monospace;font-size:.82rem;opacity:.85;">{{ $ficha->numero_ficha }}</span>
    </div>
    <div class="periodo-card-body">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label-per">Paciente</label>
                <x-buscador-paciente
                    :pacientes="$pacientes"
                    :valorInicial="$ficha->paciente_id"
                    :textoInicial="$ficha->paciente->nombre_completo . ' — ' . $ficha->paciente->numero_historia"
                    placeholder="Buscar paciente..."
                />
            </div>
            <div class="col-md-3">
                <label class="form-label-per">Doctor / Periodoncista</label>
                <select name="user_id" class="form-ctrl-per" required>
                    @foreach($doctores as $doc)
                    <option value="{{ $doc->id }}" {{ $ficha->user_id == $doc->id ? 'selected' : '' }}>
                        {{ $doc->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-per">Fecha inicio</label>
                <input type="date" name="fecha_inicio" class="form-ctrl-per"
                       value="{{ $ficha->fecha_inicio->format('Y-m-d') }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label-per">Estado</label>
                <select name="estado" class="form-ctrl-per">
                    @foreach(['activa'=>'Activa','en_tratamiento'=>'En tratamiento','mantenimiento'=>'Mantenimiento','finalizada'=>'Finalizada','abandonada'=>'Abandonada'] as $v => $l)
                    <option value="{{ $v }}" {{ $ficha->estado == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

{{-- CARD 2: Índice de Placa --}}
<div class="card-sistema periodo-card">
    <div class="periodo-card-header">
        <i class="bi bi-grid-3x3"></i> Índice de Placa O'Leary
    </div>
    <div class="periodo-card-body">
        <input type="hidden" name="indice_placa_datos" id="indice_placa_datos_json" value="{{ json_encode($ficha->indice_placa_datos ?? []) }}">
        <input type="hidden" name="fecha_indice_placa" id="fecha_indice_placa_val" value="{{ $ficha->fecha_indice_placa?->format('Y-m-d') ?? date('Y-m-d') }}">
        <input type="hidden" name="indice_placa_porcentaje" id="indice_placa_porcentaje_val" value="{{ $ficha->indice_placa_porcentaje }}">

        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:.75rem;flex-wrap:wrap;">
            <div style="font-size:.85rem;">
                Placa presente: <strong id="placa-count" style="color:#dc2626;">0</strong>
                / Total: <strong id="placa-total">0</strong>
                &nbsp;→&nbsp;
                <span id="placa-pct-badge" style="font-weight:800;font-size:.95rem;color:#16a34a;">
                    {{ $ficha->indice_placa_porcentaje ? number_format($ficha->indice_placa_porcentaje,1).'%' : '0%' }}
                </span>
            </div>
        </div>

        @php
            $dSup = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
            $dInf = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];
            $plaData = $ficha->indice_placa_datos ?? [];
        @endphp

        <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);margin-bottom:.4rem;">Arcada superior — V=Vestibular · D=Distal · L=Lingual/Palatino · M=Mesial</p>
        <div style="display:flex;flex-wrap:nowrap;gap:2px;margin-bottom:1rem;justify-content:center;overflow-x:auto;padding-bottom:2px;">
        @foreach($dSup as $d)
        @php $dc = $plaData[$d] ?? ['v'=>0,'d'=>0,'l'=>0,'m'=>0,'ausente'=>0]; @endphp
        <div class="diente-oleary {{ ($dc['ausente'] ?? 0) ? 'ausente-oc' : '' }}" data-diente="{{ $d }}">
            <div class="diente-num-oc">{{ $d }}</div>
            <svg class="cuadro-svg" width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" fill="var(--fondo-app)" rx="1"/>
                <polygon class="cara-svg {{ ($dc['v'] ?? 0) ? 'cara-activa' : '' }}" data-cara="v" points="0,0 30,0 15,15" title="Vestibular (V)" onclick="toggleCaraSVG(this)"/>
                <polygon class="cara-svg {{ ($dc['d'] ?? 0) ? 'cara-activa' : '' }}" data-cara="d" points="30,0 30,30 15,15" title="Distal (D)" onclick="toggleCaraSVG(this)"/>
                <polygon class="cara-svg {{ ($dc['l'] ?? 0) ? 'cara-activa' : '' }}" data-cara="l" points="30,30 0,30 15,15" title="Lingual/Palatino (L)" onclick="toggleCaraSVG(this)"/>
                <polygon class="cara-svg {{ ($dc['m'] ?? 0) ? 'cara-activa' : '' }}" data-cara="m" points="0,30 0,0 15,15" title="Mesial (M)" onclick="toggleCaraSVG(this)"/>
                <line x1="0" y1="0" x2="30" y2="30" stroke="#555" stroke-width="0.8" pointer-events="none"/>
                <line x1="30" y1="0" x2="0" y2="30" stroke="#555" stroke-width="0.8" pointer-events="none"/>
                <rect width="30" height="30" fill="none" stroke="#666" stroke-width="1" rx="1" pointer-events="none"/>
            </svg>
            <button type="button" class="btn-ausente-oc" title="Marcar diente ausente" onclick="toggleAusenteOc(this.closest('.diente-oleary'))">A</button>
        </div>
        @endforeach
        </div>

        <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);margin-bottom:.4rem;">Arcada inferior</p>
        <div style="display:flex;flex-wrap:nowrap;gap:2px;justify-content:center;overflow-x:auto;padding-bottom:2px;">
        @foreach($dInf as $d)
        @php $dc = $plaData[$d] ?? ['v'=>0,'d'=>0,'l'=>0,'m'=>0,'ausente'=>0]; @endphp
        <div class="diente-oleary {{ ($dc['ausente'] ?? 0) ? 'ausente-oc' : '' }}" data-diente="{{ $d }}">
            <button type="button" class="btn-ausente-oc" title="Marcar diente ausente" onclick="toggleAusenteOc(this.closest('.diente-oleary'))">A</button>
            <svg class="cuadro-svg" width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" fill="var(--fondo-app)" rx="1"/>
                <polygon class="cara-svg {{ ($dc['v'] ?? 0) ? 'cara-activa' : '' }}" data-cara="v" points="0,0 30,0 15,15" title="Vestibular (V)" onclick="toggleCaraSVG(this)"/>
                <polygon class="cara-svg {{ ($dc['d'] ?? 0) ? 'cara-activa' : '' }}" data-cara="d" points="30,0 30,30 15,15" title="Distal (D)" onclick="toggleCaraSVG(this)"/>
                <polygon class="cara-svg {{ ($dc['l'] ?? 0) ? 'cara-activa' : '' }}" data-cara="l" points="30,30 0,30 15,15" title="Lingual (L)" onclick="toggleCaraSVG(this)"/>
                <polygon class="cara-svg {{ ($dc['m'] ?? 0) ? 'cara-activa' : '' }}" data-cara="m" points="0,30 0,0 15,15" title="Mesial (M)" onclick="toggleCaraSVG(this)"/>
                <line x1="0" y1="0" x2="30" y2="30" stroke="#555" stroke-width="0.8" pointer-events="none"/>
                <line x1="30" y1="0" x2="0" y2="30" stroke="#555" stroke-width="0.8" pointer-events="none"/>
                <rect width="30" height="30" fill="none" stroke="#666" stroke-width="1" rx="1" pointer-events="none"/>
            </svg>
            <div class="diente-num-oc">{{ $d }}</div>
        </div>
        @endforeach
        </div>
        <div style="margin-top:.75rem;">
            <label class="form-label-per" style="display:inline;">Fecha índice placa</label>
            <input type="date" class="form-ctrl-per" style="width:auto;display:inline-block;margin-left:.5rem;"
                   value="{{ $ficha->fecha_indice_placa?->format('Y-m-d') ?? date('Y-m-d') }}"
                   oninput="document.getElementById('fecha_indice_placa_val').value=this.value">
        </div>
    </div>
</div>

{{-- CARD 3: Índice de Placa Silness & Löe Modificado --}}
<div class="card-sistema periodo-card">
    <div class="periodo-card-header">
        <i class="bi bi-grid-3x2"></i> Índice de Placa Silness &amp; Löe Modificado
        <span style="margin-left:auto;font-size:.82rem;font-weight:400;opacity:.85;">Clic en cada superficie con placa</span>
    </div>
    <div class="periodo-card-body">
        <input type="hidden" name="indice_gingival_datos" id="indice_gingival_datos_json">
        <input type="hidden" name="fecha_indice_gingival" id="fecha_indice_gingival_val" value="{{ $ficha->fecha_indice_gingival?->format('Y-m-d') ?? date('Y-m-d') }}">
        <input type="hidden" name="indice_gingival_porcentaje" id="indice_gingival_porcentaje_val" value="{{ $ficha->indice_gingival_porcentaje }}">

        @php
        $slGrupos = [
            'molar1q' => ['label' => 'Último molar<br>1er cuadrante', 'sups' => ['D','V','O','P','M']],
            'd11'     => ['label' => '11 / 51',                        'sups' => ['D','V','P','M']],
            'd23'     => ['label' => '23 / 63',                        'sups' => ['M','V','P','D']],
            'molar2q' => ['label' => 'Último molar<br>2° cuadrante',   'sups' => ['M','V','O','P','D']],
            'molar3q' => ['label' => 'Último molar<br>3er cuadrante',  'sups' => ['D','V','O','L','M']],
            'd44'     => ['label' => '44 / 84',                        'sups' => ['M','V','O','L','D']],
            'molar4q' => ['label' => 'Último molar<br>4° cuadrante',   'sups' => ['M','V','O','L','D']],
        ];
        $igExistente = $ficha->indice_gingival_datos ?? [];
        @endphp

        <div style="overflow-x:auto;">
        <table class="sl-tabla">
            <thead>
                <tr>
                    <th rowspan="2" class="sl-diente-header">
                        <span class="sl-diente-rotated">Diente</span>
                    </th>
                    @foreach($slGrupos as $gKey => $gData)
                    @php $ausExist = !empty($igExistente[$gKey]['ausente']); @endphp
                    <th colspan="{{ count($gData['sups']) }}" class="sl-grupo-header">
                        {!! $gData['label'] !!}
                        <div style="font-size:.6rem;font-weight:400;margin-top:.3rem;">
                            <label style="cursor:pointer;display:inline-flex;align-items:center;gap:.25rem;">
                                <input type="checkbox" class="sl-ausente-cb" data-grupo="{{ $gKey }}"
                                       {{ $ausExist ? 'checked' : '' }}
                                       onchange="toggleAusenteSL('{{ $gKey }}', this.checked)">
                                Ausente
                            </label>
                        </div>
                    </th>
                    @endforeach
                </tr>
                <tr>
                    @foreach($slGrupos as $gKey => $gData)
                        @foreach($gData['sups'] as $i => $sup)
                        <th class="sl-sup-header {{ $i === 0 ? 'sl-sup-sep' : '' }}" data-grupo="{{ $gKey }}">{{ $sup }}</th>
                        @endforeach
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="sl-codigo-label">Código</td>
                    @foreach($slGrupos as $gKey => $gData)
                        @php $ausExist = !empty($igExistente[$gKey]['ausente']); @endphp
                        @foreach($gData['sups'] as $i => $sup)
                        @php $valExist = isset($igExistente[$gKey][$sup]) && $igExistente[$gKey][$sup] ? true : false; @endphp
                        <td class="sl-cell {{ $i === 0 ? 'sl-grupo-sep' : '' }} {{ $valExist ? 'activo-sl' : '' }} {{ $ausExist ? 'ausente-sl' : '' }}"
                            data-grupo="{{ $gKey }}" data-cara="{{ $sup }}"
                            onclick="toggleSLCell(this)" title="{{ $sup }}"></td>
                        @endforeach
                    @endforeach
                </tr>
            </tbody>
        </table>
        </div>

        <div style="margin-top:1rem;background:var(--fondo-card-alt);border:1px solid var(--fondo-borde);border-radius:8px;padding:.75rem 1rem;font-size:.82rem;">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:.4rem .75rem;">
                <div>No. de superficies examinadas: <strong id="sl-superficies">33</strong></div>
                <div>Valores "1" encontrados: <strong id="sl-positivos" style="color:var(--color-principal);">0</strong></div>
                <div>Porcentaje de Placa: <strong id="sl-pct" style="font-size:1.05rem;color:#16a34a;">0.0%</strong></div>
                <div>Higiene Oral: <strong id="sl-higiene" style="color:#16a34a;">Buena (0-15%)</strong></div>
            </div>
            <div style="margin-top:.4rem;font-size:.7rem;color:var(--texto-secundario);">
                Buena 0-15% &nbsp;·&nbsp; Regular 16-30% &nbsp;·&nbsp; Deficiente 31-100%
            </div>
        </div>

        <div style="margin-top:.75rem;">
            <label class="form-label-per" style="display:inline;">Fecha índice</label>
            <input type="date" class="form-ctrl-per" style="width:auto;display:inline-block;margin-left:.5rem;"
                   value="{{ $ficha->fecha_indice_gingival?->format('Y-m-d') ?? date('Y-m-d') }}"
                   oninput="document.getElementById('fecha_indice_gingival_val').value=this.value">
        </div>
    </div>
</div>

{{-- CARD 4: Periodontograma --}}
<div class="card-sistema periodo-card">
    <div class="periodo-card-header">
        <i class="bi bi-table"></i> Periodontograma
        <span style="margin-left:auto;font-size:.75rem;font-weight:400;opacity:.9;">
            <span style="color:#DC3545;">─</span> <span style="color:#DC3545;">MG</span> Margen gingival &nbsp;·&nbsp;
            <span style="color:#2563EB;">─</span> <span style="color:#2563EB;">NCI</span> Nivel inserción (PS−MG)
        </span>
    </div>
    <div class="periodo-card-body" style="padding:.6rem;">
        <input type="hidden" name="sondaje_datos" id="sondaje_datos_json" value="{{ json_encode($ficha->sondaje_datos ?? []) }}">
        <input type="hidden" name="fecha_sondaje" id="fecha_sondaje_val" value="{{ $ficha->fecha_sondaje?->format('Y-m-d') ?? date('Y-m-d') }}">

        @php $sData = $ficha->sondaje_datos ?? []; $qsIdx = 8; @endphp

        <div class="perio-stats-bar">
            <span>PS prom: <strong id="sond-promedio">0.0</strong> mm</span>
            <span>NCI prom: <strong id="sond-nci-prom" style="color:#6366f1;">0.0</strong> mm</span>
            <span>BOP: <strong id="sond-bop" style="color:#dc2626;">0%</strong></span>
            <span>PI: <strong id="sond-pi" style="color:#3b82f6;">0%</strong></span>
            <span style="font-size:.65rem;"><span class="psq on-s" style="cursor:default;"></span> Sangrado &nbsp;<span class="psq on-p" style="cursor:default;"></span> Placa</span>
        </div>

        {{-- ═══ ARCADA SUPERIOR ═══ --}}
        <p class="perio-section-label">▲ Arcada Superior</p>
        <div class="perio-wrap">
        <table class="perio-tbl" id="pt-sup"><tbody>
        <tr><td class="pl">Diente</td>@foreach($dSup as $i => $d)<td colspan="3" class="pt {{ $i===$qsIdx?'qs':'' }}">{{ $d }}</td>@endforeach</tr>
        <tr><td class="pl">Movilidad</td>@foreach($dSup as $i => $d)<td colspan="3" class="{{ $i===$qsIdx?'qs':'' }}" data-ausente-for="{{ $d }}"><select class="psel" data-diente="{{ $d }}" data-cara="movilidad" onchange="calcularSondaje()">@foreach(['0','I','II','III'] as $mv)<option value="{{ $mv }}" {{ ($sData[$d]['movilidad']??'0')==$mv?'selected':'' }}>{{ $mv }}</option>@endforeach</select></td>@endforeach</tr>
        <tr><td class="pl">Implante</td>@foreach($dSup as $i => $d)<td colspan="3" class="{{ $i===$qsIdx?'qs':'' }}" data-ausente-for="{{ $d }}"><input type="checkbox" class="pimpl-cb" data-diente="{{ $d }}" data-cara="implante" {{ ($sData[$d]['implante']??0)?'checked':'' }} onchange="calcularSondaje()"></td>@endforeach</tr>
        <tr><td class="pl">Furcación</td>@foreach($dSup as $i => $d)<td colspan="3" class="{{ $i===$qsIdx?'qs':'' }}" data-ausente-for="{{ $d }}"><select class="psel" data-diente="{{ $d }}" data-cara="furcacion" onchange="calcularSondaje()"><option value="">—</option>@foreach(['I','II','III'] as $fv)<option value="{{ $fv }}" {{ ($sData[$d]['furcacion']??'')==$fv?'selected':'' }}>{{ $fv }}</option>@endforeach</select></td>@endforeach</tr>
        <tr><td class="pl" style="color:#dc2626;">Sangrado sondeo</td>@foreach($dSup as $i => $d)@foreach(['sv_mv','sv_v','sv_dv'] as $j => $c)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><span class="psq {{ ($sData[$d][$c]??0)?'on-s':'' }}" data-diente="{{ $d }}" data-cara="{{ $c }}" onclick="togglePSQ(this,'s')"></span></td>@endforeach @endforeach</tr>
        <tr><td class="pl" style="color:#3b82f6;">Placa</td>@foreach($dSup as $i => $d)@foreach(['pv_mv','pv_v','pv_dv'] as $j => $c)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><span class="psq {{ ($sData[$d][$c]??0)?'on-p':'' }}" data-diente="{{ $d }}" data-cara="{{ $c }}" onclick="togglePSQ(this,'p')"></span></td>@endforeach @endforeach</tr>
        <tr><td class="pl" style="color:#2563eb;">Margen gingival</td>@foreach($dSup as $i => $d)@foreach(['mg_mv','mg_v','mg_dv'] as $j => $c)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><input type="number" class="pmg" min="-15" max="10" step="1" data-diente="{{ $d }}" data-cara="{{ $c }}" placeholder="0" value="{{ $sData[$d][$c]??'' }}" oninput="calcularSondaje();dibujarPeriodontograma()"></td>@endforeach @endforeach</tr>
        <tr><td class="pl" style="color:#d97706;">Prof. de sondaje</td>@foreach($dSup as $i => $d)@foreach(['ps_mv','ps_v','ps_dv'] as $j => $c)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><input type="number" class="pps" min="0" max="20" step="1" data-diente="{{ $d }}" data-cara="{{ $c }}" placeholder="0" value="{{ $sData[$d][$c]??'' }}" oninput="calcularSondaje();dibujarPeriodontograma()"></td>@endforeach @endforeach</tr>
        <tr><td class="pl">Nivel de inserción</td>@foreach($dSup as $i => $d)@foreach(['mv','v','dv'] as $j => $pt)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><span class="pnci" id="nci-{{ $d }}-{{ $pt }}">—</span></td>@endforeach @endforeach</tr>
        <tr><td class="pl" style="font-size:.52rem;vertical-align:middle;color:var(--texto-secundario);">V ↑ gráfico ↓ L</td><td colspan="{{ count($dSup)*3 }}" style="padding:0;border:none;border-bottom:1px solid var(--fondo-borde);"><canvas class="pcv" id="pcv-sup" height="190"></canvas></td></tr>
        <tr><td class="pl">Nivel de inserción</td>@foreach($dSup as $i => $d)@foreach(['ml','l','dl'] as $j => $pt)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><span class="pnci" id="nci-{{ $d }}-{{ $pt }}">—</span></td>@endforeach @endforeach</tr>
        <tr><td class="pl" style="color:#d97706;">Prof. de sondaje</td>@foreach($dSup as $i => $d)@foreach(['ps_ml','ps_l','ps_dl'] as $j => $c)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><input type="number" class="pps" min="0" max="20" step="1" data-diente="{{ $d }}" data-cara="{{ $c }}" placeholder="0" value="{{ $sData[$d][$c]??'' }}" oninput="calcularSondaje();dibujarPeriodontograma()"></td>@endforeach @endforeach</tr>
        <tr><td class="pl" style="color:#2563eb;">Margen gingival</td>@foreach($dSup as $i => $d)@foreach(['mg_ml','mg_l','mg_dl'] as $j => $c)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><input type="number" class="pmg" min="-15" max="10" step="1" data-diente="{{ $d }}" data-cara="{{ $c }}" placeholder="0" value="{{ $sData[$d][$c]??'' }}" oninput="calcularSondaje();dibujarPeriodontograma()"></td>@endforeach @endforeach</tr>
        <tr><td class="pl" style="color:#3b82f6;">Placa</td>@foreach($dSup as $i => $d)@foreach(['pl_ml','pl_l','pl_dl'] as $j => $c)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><span class="psq {{ ($sData[$d][$c]??0)?'on-p':'' }}" data-diente="{{ $d }}" data-cara="{{ $c }}" onclick="togglePSQ(this,'p')"></span></td>@endforeach @endforeach</tr>
        <tr><td class="pl" style="color:#dc2626;">Sangrado sondeo</td>@foreach($dSup as $i => $d)@foreach(['sl_ml','sl_l','sl_dl'] as $j => $c)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><span class="psq {{ ($sData[$d][$c]??0)?'on-s':'' }}" data-diente="{{ $d }}" data-cara="{{ $c }}" onclick="togglePSQ(this,'s')"></span></td>@endforeach @endforeach</tr>
        <tr style="background:var(--fondo-card-alt);"><td class="pl">Ausente</td>@foreach($dSup as $i => $d)<td colspan="3" class="{{ $i===$qsIdx?'qs':'' }}"><input type="checkbox" class="paus-cb" data-diente="{{ $d }}" {{ ($sData[$d]['ausente']??0)?'checked':'' }} onchange="togglePerioAusente('{{ $d }}',this.checked)"></td>@endforeach</tr>
        </tbody></table></div>

        <div class="perio-mid-bar">
            Profundidad media de sondaje: <strong id="sond-prom2">0.0</strong> mm &nbsp;·&nbsp;
            Nivel medio de inserción: <strong id="sond-nci2" style="color:#6366f1;">0.0</strong> mm &nbsp;·&nbsp;
            BOP: <strong id="sond-bop2" style="color:#dc2626;">0%</strong> &nbsp;·&nbsp;
            PI: <strong id="sond-pi2" style="color:#3b82f6;">0%</strong>
        </div>

        {{-- ═══ ARCADA INFERIOR ═══ --}}
        <p class="perio-section-label">▼ Arcada Inferior</p>
        <div class="perio-wrap">
        <table class="perio-tbl" id="pt-inf"><tbody>
        <tr style="background:var(--fondo-card-alt);"><td class="pl">Ausente</td>@foreach($dInf as $i => $d)<td colspan="3" class="{{ $i===$qsIdx?'qs':'' }}"><input type="checkbox" class="paus-cb" data-diente="{{ $d }}" {{ ($sData[$d]['ausente']??0)?'checked':'' }} onchange="togglePerioAusente('{{ $d }}',this.checked)"></td>@endforeach</tr>
        <tr><td class="pl" style="color:#dc2626;">Sangrado sondeo</td>@foreach($dInf as $i => $d)@foreach(['sl_ml','sl_l','sl_dl'] as $j => $c)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><span class="psq {{ ($sData[$d][$c]??0)?'on-s':'' }}" data-diente="{{ $d }}" data-cara="{{ $c }}" onclick="togglePSQ(this,'s')"></span></td>@endforeach @endforeach</tr>
        <tr><td class="pl" style="color:#3b82f6;">Placa</td>@foreach($dInf as $i => $d)@foreach(['pl_ml','pl_l','pl_dl'] as $j => $c)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><span class="psq {{ ($sData[$d][$c]??0)?'on-p':'' }}" data-diente="{{ $d }}" data-cara="{{ $c }}" onclick="togglePSQ(this,'p')"></span></td>@endforeach @endforeach</tr>
        <tr><td class="pl" style="color:#2563eb;">Margen gingival</td>@foreach($dInf as $i => $d)@foreach(['mg_ml','mg_l','mg_dl'] as $j => $c)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><input type="number" class="pmg" min="-15" max="10" step="1" data-diente="{{ $d }}" data-cara="{{ $c }}" placeholder="0" value="{{ $sData[$d][$c]??'' }}" oninput="calcularSondaje();dibujarPeriodontograma()"></td>@endforeach @endforeach</tr>
        <tr><td class="pl" style="color:#d97706;">Prof. de sondaje</td>@foreach($dInf as $i => $d)@foreach(['ps_ml','ps_l','ps_dl'] as $j => $c)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><input type="number" class="pps" min="0" max="20" step="1" data-diente="{{ $d }}" data-cara="{{ $c }}" placeholder="0" value="{{ $sData[$d][$c]??'' }}" oninput="calcularSondaje();dibujarPeriodontograma()"></td>@endforeach @endforeach</tr>
        <tr><td class="pl">Nivel de inserción</td>@foreach($dInf as $i => $d)@foreach(['ml','l','dl'] as $j => $pt)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><span class="pnci" id="nci-{{ $d }}-{{ $pt }}">—</span></td>@endforeach @endforeach</tr>
        <tr><td class="pl" style="font-size:.52rem;vertical-align:middle;color:var(--texto-secundario);">L ↑ gráfico ↓ V</td><td colspan="{{ count($dInf)*3 }}" style="padding:0;border:none;border-bottom:1px solid var(--fondo-borde);"><canvas class="pcv" id="pcv-inf" height="190"></canvas></td></tr>
        <tr><td class="pl">Nivel de inserción</td>@foreach($dInf as $i => $d)@foreach(['mv','v','dv'] as $j => $pt)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><span class="pnci" id="nci-{{ $d }}-{{ $pt }}">—</span></td>@endforeach @endforeach</tr>
        <tr><td class="pl" style="color:#d97706;">Prof. de sondaje</td>@foreach($dInf as $i => $d)@foreach(['ps_mv','ps_v','ps_dv'] as $j => $c)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><input type="number" class="pps" min="0" max="20" step="1" data-diente="{{ $d }}" data-cara="{{ $c }}" placeholder="0" value="{{ $sData[$d][$c]??'' }}" oninput="calcularSondaje();dibujarPeriodontograma()"></td>@endforeach @endforeach</tr>
        <tr><td class="pl" style="color:#2563eb;">Margen gingival</td>@foreach($dInf as $i => $d)@foreach(['mg_mv','mg_v','mg_dv'] as $j => $c)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><input type="number" class="pmg" min="-15" max="10" step="1" data-diente="{{ $d }}" data-cara="{{ $c }}" placeholder="0" value="{{ $sData[$d][$c]??'' }}" oninput="calcularSondaje();dibujarPeriodontograma()"></td>@endforeach @endforeach</tr>
        <tr><td class="pl" style="color:#3b82f6;">Placa</td>@foreach($dInf as $i => $d)@foreach(['pv_mv','pv_v','pv_dv'] as $j => $c)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><span class="psq {{ ($sData[$d][$c]??0)?'on-p':'' }}" data-diente="{{ $d }}" data-cara="{{ $c }}" onclick="togglePSQ(this,'p')"></span></td>@endforeach @endforeach</tr>
        <tr><td class="pl" style="color:#dc2626;">Sangrado sondeo</td>@foreach($dInf as $i => $d)@foreach(['sv_mv','sv_v','sv_dv'] as $j => $c)<td class="{{ ($i===$qsIdx&&$j===0)?'qs':'' }}" data-ausente-for="{{ $d }}"><span class="psq {{ ($sData[$d][$c]??0)?'on-s':'' }}" data-diente="{{ $d }}" data-cara="{{ $c }}" onclick="togglePSQ(this,'s')"></span></td>@endforeach @endforeach</tr>
        <tr><td class="pl">Furcación</td>@foreach($dInf as $i => $d)<td colspan="3" class="{{ $i===$qsIdx?'qs':'' }}" data-ausente-for="{{ $d }}"><select class="psel" data-diente="{{ $d }}" data-cara="furcacion" onchange="calcularSondaje()"><option value="">—</option>@foreach(['I','II','III'] as $fv)<option value="{{ $fv }}" {{ ($sData[$d]['furcacion']??'')==$fv?'selected':'' }}>{{ $fv }}</option>@endforeach</select></td>@endforeach</tr>
        <tr><td class="pl">Implante</td>@foreach($dInf as $i => $d)<td colspan="3" class="{{ $i===$qsIdx?'qs':'' }}" data-ausente-for="{{ $d }}"><input type="checkbox" class="pimpl-cb" data-diente="{{ $d }}" data-cara="implante" {{ ($sData[$d]['implante']??0)?'checked':'' }} onchange="calcularSondaje()"></td>@endforeach</tr>
        <tr><td class="pl">Movilidad</td>@foreach($dInf as $i => $d)<td colspan="3" class="{{ $i===$qsIdx?'qs':'' }}" data-ausente-for="{{ $d }}"><select class="psel" data-diente="{{ $d }}" data-cara="movilidad" onchange="calcularSondaje()">@foreach(['0','I','II','III'] as $mv)<option value="{{ $mv }}" {{ ($sData[$d]['movilidad']??'0')==$mv?'selected':'' }}>{{ $mv }}</option>@endforeach</select></td>@endforeach</tr>
        <tr><td class="pl">Diente</td>@foreach($dInf as $i => $d)<td colspan="3" class="pt {{ $i===$qsIdx?'qs':'' }}">{{ $d }}</td>@endforeach</tr>
        </tbody></table></div>

        <div style="margin-top:.6rem;">
            <label class="form-label-per" style="display:inline;">Fecha sondaje</label>
            <input type="date" class="form-ctrl-per" style="width:auto;display:inline-block;margin-left:.5rem;"
                   value="{{ $ficha->fecha_sondaje?->format('Y-m-d') ?? date('Y-m-d') }}"
                   oninput="document.getElementById('fecha_sondaje_val').value=this.value">
        </div>
    </div>
</div>

{{-- CARD 5: Diagnóstico --}}
<div class="card-sistema periodo-card">
    <div class="periodo-card-header">
        <i class="bi bi-clipboard2-pulse"></i> Diagnóstico Periodontal
    </div>
    <div class="periodo-card-body">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label-per">Clasificación periodontal</label>
                <select name="clasificacion_periodontal" class="form-ctrl-per">
                    <option value="">Seleccionar...</option>
                    @foreach([
                        'salud_periodontal'=>'Salud periodontal',
                        'gingivitis_inducida_placa'=>'Gingivitis inducida por placa',
                        'gingivitis_no_inducida_placa'=>'Gingivitis no inducida por placa',
                        'periodontitis_estadio_i'=>'Periodontitis Estadio I',
                        'periodontitis_estadio_ii'=>'Periodontitis Estadio II',
                        'periodontitis_estadio_iii'=>'Periodontitis Estadio III',
                        'periodontitis_estadio_iv'=>'Periodontitis Estadio IV',
                        'periodontitis_necrosante'=>'Periodontitis necrosante',
                        'absceso_periodontal'=>'Absceso periodontal',
                        'lesion_endoperio'=>'Lesión endo-perio',
                        'deformidades_condiciones'=>'Deformidades y condiciones',
                    ] as $v => $l)
                    <option value="{{ $v }}" {{ $ficha->clasificacion_periodontal == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-per">Extensión</label>
                <select name="extension" class="form-ctrl-per">
                    <option value="">—</option>
                    <option value="localizada" {{ $ficha->extension=='localizada'?'selected':'' }}>Localizada</option>
                    <option value="generalizada" {{ $ficha->extension=='generalizada'?'selected':'' }}>Generalizada</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-per">Severidad</label>
                <select name="severidad" class="form-ctrl-per">
                    <option value="">—</option>
                    <option value="leve" {{ $ficha->severidad=='leve'?'selected':'' }}>Leve</option>
                    <option value="moderada" {{ $ficha->severidad=='moderada'?'selected':'' }}>Moderada</option>
                    <option value="severa" {{ $ficha->severidad=='severa'?'selected':'' }}>Severa</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label-per">Pronóstico general</label>
                <select name="pronostico_general" class="form-ctrl-per">
                    <option value="">—</option>
                    @foreach(['excelente'=>'Excelente','bueno'=>'Bueno','regular'=>'Regular','malo'=>'Malo','sin_esperanza'=>'Sin esperanza'] as $v => $l)
                    <option value="{{ $v }}" {{ $ficha->pronostico_general==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label-per">Factores de riesgo</label>
                <div style="display:flex;flex-wrap:wrap;gap:.75rem;margin-top:.25rem;">
                    @foreach(['tabaco'=>'Tabaco','diabetes'=>'Diabetes','estres'=>'Estrés','medicamentos'=>'Medicamentos','genetica'=>'Genética','osteoporosis'=>'Osteoporosis','embarazo'=>'Embarazo'] as $val => $lbl)
                    <label style="display:flex;align-items:center;gap:.35rem;font-size:.83rem;cursor:pointer;">
                        <input type="checkbox" name="factores_riesgo[]" value="{{ $val }}"
                               {{ is_array($ficha->factores_riesgo) && in_array($val, $ficha->factores_riesgo) ? 'checked' : '' }}
                               style="accent-color:var(--color-principal);">
                        {{ $lbl }}
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label-per">Diagnóstico</label>
                <textarea name="diagnostico_texto" class="form-ctrl-per" rows="3">{{ $ficha->diagnostico_texto }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label-per">Plan de tratamiento</label>
                <textarea name="plan_tratamiento" class="form-ctrl-per" rows="3">{{ $ficha->plan_tratamiento }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label-per">Notas adicionales</label>
                <textarea name="notas" class="form-ctrl-per" rows="2">{{ $ficha->notas }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- Botones --}}
<div style="display:flex;justify-content:flex-end;gap:.75rem;margin-top:.5rem;">
    <a href="{{ route('periodoncia.show', $ficha) }}"
       style="background:var(--fondo-borde);color:var(--texto-principal);padding:.5rem 1.25rem;border-radius:8px;font-size:.88rem;text-decoration:none;font-weight:600;">
        Cancelar
    </a>
    <button type="submit"
            style="background:var(--color-principal);color:white;border:none;padding:.5rem 1.5rem;border-radius:8px;font-size:.88rem;font-weight:700;cursor:pointer;box-shadow:0 2px 8px var(--sombra-principal);">
        <i class="bi bi-save me-1"></i> Guardar Cambios
    </button>
</div>

</form>

@endsection

@push('scripts')
<script>
// ── O'Leary — triángulos SVG ──
function toggleCaraSVG(poly) {
    poly.classList.toggle('cara-activa');
    calcularPlaca();
}
function toggleAusenteOc(dw) {
    dw.classList.toggle('ausente-oc');
    if (dw.classList.contains('ausente-oc')) {
        dw.querySelectorAll('.cara-svg').forEach(p => p.classList.remove('cara-activa'));
    }
    calcularPlaca();
}
function calcularPlaca() {
    var total = 0; var activos = 0; var datos = {};
    document.querySelectorAll('.diente-oleary').forEach(function(dw) {
        var d = dw.getAttribute('data-diente');
        var ausente = dw.classList.contains('ausente-oc');
        datos[d] = { ausente: ausente ? 1 : 0 };
        dw.querySelectorAll('.cara-svg').forEach(function(poly) {
            var cara = poly.getAttribute('data-cara');
            var act  = poly.classList.contains('cara-activa') ? 1 : 0;
            datos[d][cara] = act;
            if (!ausente) { total++; activos += act; }
        });
    });
    var pct = total > 0 ? Math.round((activos / total) * 1000) / 10 : 0;
    document.getElementById('placa-count').textContent = activos;
    document.getElementById('placa-total').textContent = total;
    var badge = document.getElementById('placa-pct-badge');
    badge.textContent = pct.toFixed(1) + '%';
    badge.style.color = pct < 15 ? '#16a34a' : (pct <= 30 ? '#d97706' : '#dc2626');
    document.getElementById('indice_placa_porcentaje_val').value = pct.toFixed(2);
    document.getElementById('indice_placa_datos_json').value = JSON.stringify(datos);
}

// ── Silness & Löe Modificado ──
function toggleSLCell(cell) {
    cell.classList.toggle('activo-sl');
    calcularSL();
}
function toggleAusenteSL(grupo, ausente) {
    document.querySelectorAll('.sl-cell[data-grupo="' + grupo + '"]').forEach(function(cell) {
        if (ausente) { cell.classList.add('ausente-sl'); cell.classList.remove('activo-sl'); }
        else { cell.classList.remove('ausente-sl'); }
    });
    calcularSL();
}
function calcularSL() {
    var datos = {};
    document.querySelectorAll('.sl-ausente-cb').forEach(function(cb) {
        datos[cb.dataset.grupo] = { ausente: cb.checked };
    });
    var superficies = 0; var positivos = 0;
    document.querySelectorAll('.sl-cell').forEach(function(cell) {
        var g = cell.dataset.grupo; var cara = cell.dataset.cara;
        var val = cell.classList.contains('activo-sl') ? 1 : 0;
        if (!datos[g]) datos[g] = { ausente: false };
        datos[g][cara] = val;
        if (!datos[g].ausente) { superficies++; positivos += val; }
    });
    var pct = superficies > 0 ? (positivos / superficies * 100) : 0;
    var color = pct <= 15 ? '#16a34a' : (pct <= 30 ? '#d97706' : '#dc2626');
    var higiene = pct <= 15 ? 'Buena (0-15%)' : (pct <= 30 ? 'Regular (16-30%)' : 'Deficiente (31-100%)');
    document.getElementById('sl-superficies').textContent = superficies;
    document.getElementById('sl-positivos').textContent   = positivos;
    var pctEl = document.getElementById('sl-pct');
    pctEl.textContent = pct.toFixed(1) + '%'; pctEl.style.color = color;
    var hEl = document.getElementById('sl-higiene');
    hEl.textContent = higiene; hEl.style.color = color;
    document.getElementById('indice_gingival_porcentaje_val').value = pct.toFixed(2);
    document.getElementById('indice_gingival_datos_json').value     = JSON.stringify(datos);
}

// ── Periodontograma profesional ──
function togglePSQ(el, tipo) {
    el.classList.toggle(tipo === 's' ? 'on-s' : 'on-p');
    calcularSondaje();
}

function togglePerioAusente(diente, ausente) {
    document.querySelectorAll('[data-ausente-for="' + diente + '"]').forEach(function(td) {
        if (ausente) td.classList.add('td-aus');
        else         td.classList.remove('td-aus');
    });
    calcularSondaje(); dibujarPeriodontograma();
}

function calcularSondaje() {
    var datos = {};
    var sumPS = 0; var cntPS = 0;
    var sumNCI = 0; var cntNCI = 0;
    var totS = 0; var cntS = 0;
    var totP = 0; var cntP = 0;
    var psCols = ['ps_mv','ps_v','ps_dv','ps_ml','ps_l','ps_dl'];
    var allD = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28,
                48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];

    // PS values
    document.querySelectorAll('.pps[data-diente]').forEach(function(inp) {
        var d = inp.dataset.diente; var c = inp.dataset.cara;
        if (!datos[d]) datos[d] = {};
        var v = inp.value !== '' ? parseFloat(inp.value) : null;
        datos[d][c] = v;
        if (v !== null && psCols.includes(c)) { sumPS += v; cntPS++; }
    });
    // MG values
    document.querySelectorAll('.pmg[data-diente]').forEach(function(inp) {
        var d = inp.dataset.diente; var c = inp.dataset.cara;
        if (!datos[d]) datos[d] = {};
        datos[d][c] = inp.value !== '' ? parseFloat(inp.value) : null;
    });
    // Calculate NCI = PS - MG for each probing point
    var pairsV = [['ps_mv','mg_mv','mv'],['ps_v','mg_v','v'],['ps_dv','mg_dv','dv']];
    var pairsL = [['ps_ml','mg_ml','ml'],['ps_l','mg_l','l'],['ps_dl','mg_dl','dl']];
    allD.forEach(function(d) {
        pairsV.concat(pairsL).forEach(function(pair) {
            var ps = datos[d] ? datos[d][pair[0]] : null;
            var mg = (datos[d] && datos[d][pair[1]] !== null && datos[d][pair[1]] !== undefined) ? datos[d][pair[1]] : 0;
            var el = document.getElementById('nci-' + d + '-' + pair[2]);
            if (!el) return;
            if (ps !== null) {
                var nci = Math.round((ps - mg) * 10) / 10;
                el.textContent = nci;
                el.style.color = nci >= 5 ? '#dc2626' : nci >= 3 ? '#d97706' : 'var(--texto-secundario)';
                if (!datos[d]) datos[d] = {};
                datos[d]['nci_' + pair[2]] = nci;
                sumNCI += nci; cntNCI++;
            } else { el.textContent = '—'; el.style.color = ''; }
        });
    });
    // Squares (sangrado / placa)
    document.querySelectorAll('.psq[data-diente]').forEach(function(sq) {
        var d = sq.dataset.diente; var c = sq.dataset.cara;
        if (!datos[d]) datos[d] = {};
        var isOn = sq.classList.contains('on-s') || sq.classList.contains('on-p');
        datos[d][c] = isOn ? 1 : 0;
        if (c.startsWith('sv_')||c.startsWith('sl_')) { cntS++; if(isOn) totS++; }
        if (c.startsWith('pv_')||c.startsWith('pl_')) { cntP++; if(isOn) totP++; }
    });
    // Selects + checkboxes
    document.querySelectorAll('.psel[data-diente]').forEach(function(sel) {
        var d = sel.dataset.diente; var c = sel.dataset.cara;
        if (!datos[d]) datos[d] = {};
        datos[d][c] = sel.value;
    });
    document.querySelectorAll('.pimpl-cb,.paus-cb').forEach(function(cb) {
        var d = cb.dataset.diente; var c = cb.dataset.cara || 'ausente';
        if (!datos[d]) datos[d] = {};
        datos[d][c] = cb.checked ? 1 : 0;
    });

    var promPS = cntPS > 0 ? (sumPS/cntPS).toFixed(1) : '0.0';
    var promNCI = cntNCI > 0 ? (sumNCI/cntNCI).toFixed(1) : '0.0';
    var bop = cntS > 0 ? Math.round(totS/cntS*100)+'%' : '0%';
    var pi  = cntP > 0 ? Math.round(totP/cntP*100)+'%' : '0%';
    ['sond-promedio','sond-prom2'].forEach(function(id){var e=document.getElementById(id);if(e)e.textContent=promPS;});
    ['sond-nci-prom','sond-nci2'].forEach(function(id){var e=document.getElementById(id);if(e)e.textContent=promNCI;});
    ['sond-bop','sond-bop2'].forEach(function(id){var e=document.getElementById(id);if(e)e.textContent=bop;});
    ['sond-pi','sond-pi2'].forEach(function(id){var e=document.getElementById(id);if(e)e.textContent=pi;});
    document.getElementById('sondaje_datos_json').value = JSON.stringify(datos);
}

function dibujarDiente(ctx,cx,cejY,toothW,num,cd,aus,impl){
    var n=num%10;
    var hw=Math.min(toothW*0.48,46);
    if(aus){
        ctx.strokeStyle='rgba(160,50,50,0.45)';ctx.lineWidth=1.5;var xs=hw*0.48;
        ctx.beginPath();ctx.moveTo(cx-xs,cejY-cd*xs);ctx.lineTo(cx+xs,cejY+cd*xs);ctx.stroke();
        ctx.beginPath();ctx.moveTo(cx+xs,cejY-cd*xs);ctx.lineTo(cx-xs,cejY+cd*xs);ctx.stroke();
        return;
    }
    var cH,rH,isMol,isPre,isCan;
    if(n===1){cH=32;rH=62;isMol=false;isPre=false;isCan=false;}
    else if(n===2){cH=28;rH=58;isMol=false;isPre=false;isCan=false;}
    else if(n===3){cH=37;rH=64;isMol=false;isPre=false;isCan=true;}
    else if(n===4){cH=28;rH=58;isMol=false;isPre=true;isCan=false;}
    else if(n===5){cH=26;rH=55;isMol=false;isPre=true;isCan=false;}
    else if(n===6){cH=32;rH=52;isMol=true;isPre=false;isCan=false;}
    else if(n===7){cH=29;rH=48;isMol=true;isPre=false;isCan=false;}
    else{cH=25;rH=42;isMol=true;isPre=false;isCan=false;}
    ctx.fillStyle='#dbd3c0';ctx.strokeStyle='rgba(95,82,65,0.5)';ctx.lineWidth=0.85;
    if(isMol){
        var mTip=cejY+cd*rH,dTip=cejY+cd*(rH*0.88);
        var mCx=cx-hw*0.22,dCx=cx+hw*0.20;
        ctx.beginPath();ctx.moveTo(cx-hw*0.43,cejY);
        ctx.bezierCurveTo(cx-hw*0.43,cejY+cd*rH*0.42,mCx-hw*0.18,cejY+cd*rH*0.84,mCx,mTip);
        ctx.bezierCurveTo(mCx+hw*0.10,cejY+cd*rH*0.84,cx-hw*0.05,cejY+cd*rH*0.42,cx-hw*0.03,cejY);
        ctx.closePath();ctx.fill();ctx.stroke();
        ctx.beginPath();ctx.moveTo(cx+hw*0.03,cejY);
        ctx.bezierCurveTo(cx+hw*0.05,cejY+cd*rH*0.42,dCx-hw*0.10,cejY+cd*rH*0.80,dCx,dTip);
        ctx.bezierCurveTo(dCx+hw*0.18,cejY+cd*rH*0.80,cx+hw*0.43,cejY+cd*rH*0.42,cx+hw*0.43,cejY);
        ctx.closePath();ctx.fill();ctx.stroke();
    }else{
        var rTip=cejY+cd*rH,rw=isPre?0.38:0.30;
        ctx.beginPath();ctx.moveTo(cx-hw*rw,cejY);
        ctx.bezierCurveTo(cx-hw*(rw*0.95),cejY+cd*rH*0.48,cx-hw*0.08,cejY+cd*rH*0.88,cx,rTip);
        ctx.bezierCurveTo(cx+hw*0.08,cejY+cd*rH*0.88,cx+hw*(rw*0.95),cejY+cd*rH*0.48,cx+hw*rw,cejY);
        ctx.closePath();ctx.fill();ctx.stroke();
    }
    if(impl){
        var iW=hw*0.54,iTop=Math.min(cejY,cejY+cd*rH*0.92),iBot=Math.max(cejY,cejY+cd*rH*0.92),iLen=iBot-iTop;
        var gImpl=ctx.createLinearGradient(cx-iW/2,0,cx+iW/2,0);
        gImpl.addColorStop(0,'#8a8fa0');gImpl.addColorStop(0.3,'#d0d4de');gImpl.addColorStop(0.7,'#c0c4ce');gImpl.addColorStop(1,'#8a8fa0');
        var taper=iW*0.28;
        ctx.fillStyle=gImpl;ctx.strokeStyle='rgba(70,75,95,0.8)';ctx.lineWidth=0.7;
        ctx.beginPath();
        ctx.moveTo(cx-iW/2,iTop);ctx.lineTo(cx+iW/2,iTop);
        ctx.lineTo(cx+taper,iBot);ctx.lineTo(cx-taper,iBot);
        ctx.closePath();ctx.fill();ctx.stroke();
        var nTh=Math.floor(iLen/3.2);
        for(var t=0;t<nTh;t++){
            var ty2=iTop+2+(t*3.2);
            var prog=(iBot-ty2)/(iBot-iTop);
            var tw=taper+(iW/2-taper)*prog;
            ctx.strokeStyle='rgba(55,60,80,0.35)';ctx.lineWidth=0.6;
            ctx.beginPath();ctx.moveTo(cx-tw-2,ty2);ctx.lineTo(cx+tw+2,ty2);ctx.stroke();
        }
        ctx.fillStyle='rgba(195,200,214,0.95)';ctx.strokeStyle='rgba(75,80,100,0.8)';ctx.lineWidth=0.8;
        ctx.beginPath();ctx.rect(cx-iW/2-1.5,iTop-3,iW+3,5);ctx.fill();ctx.stroke();
    }
    var tipY=cejY-cd*cH;
    var gY0=Math.min(cejY,tipY),gY1=Math.max(cejY,tipY);
    var g=ctx.createLinearGradient(cx-hw,gY0,cx+hw,gY1);
    g.addColorStop(0,'#e2ddd0');g.addColorStop(0.45,'#eeeadc');g.addColorStop(1,'#f4f1e6');
    ctx.fillStyle=impl?'rgba(192,198,212,0.9)':g;
    ctx.strokeStyle='rgba(78,66,50,0.68)';ctx.lineWidth=1.05;
    ctx.beginPath();
    if(isMol){
        var ow=hw*0.93;
        ctx.moveTo(cx-ow,tipY);ctx.lineTo(cx+ow,tipY);
        ctx.bezierCurveTo(cx+hw*1.04,tipY+cd*cH*0.30,cx+hw*1.04,cejY-cd*9,cx+hw*0.48,cejY);
        ctx.lineTo(cx-hw*0.48,cejY);
        ctx.bezierCurveTo(cx-hw*1.04,cejY-cd*9,cx-hw*1.04,tipY+cd*cH*0.30,cx-ow,tipY);
    }else if(isPre){
        var bx=cx-hw*0.10;
        ctx.moveTo(cx-hw*0.90,tipY+cd*9);
        ctx.bezierCurveTo(cx-hw*0.84,tipY+cd*2,bx-hw*0.26,tipY,bx,tipY+cd*2);
        ctx.bezierCurveTo(bx+hw*0.24,tipY+cd*1,cx+hw*0.55,tipY+cd*9,cx+hw*0.90,tipY+cd*15);
        ctx.bezierCurveTo(cx+hw*1.04,tipY+cd*cH*0.37,cx+hw*1.04,cejY-cd*9,cx+hw*0.46,cejY);
        ctx.lineTo(cx-hw*0.46,cejY);
        ctx.bezierCurveTo(cx-hw*1.04,cejY-cd*9,cx-hw*1.04,tipY+cd*cH*0.37,cx-hw*0.90,tipY+cd*9);
    }else if(isCan){
        ctx.moveTo(cx-hw*0.85,tipY+cd*15);
        ctx.bezierCurveTo(cx-hw*0.65,tipY+cd*4,cx-hw*0.20,tipY,cx,tipY+cd*2);
        ctx.bezierCurveTo(cx+hw*0.20,tipY,cx+hw*0.60,tipY+cd*6,cx+hw*0.83,tipY+cd*15);
        ctx.bezierCurveTo(cx+hw*1.04,tipY+cd*cH*0.42,cx+hw*1.04,cejY-cd*8,cx+hw*0.45,cejY);
        ctx.lineTo(cx-hw*0.45,cejY);
        ctx.bezierCurveTo(cx-hw*1.04,cejY-cd*8,cx-hw*1.04,tipY+cd*cH*0.42,cx-hw*0.85,tipY+cd*15);
    }else{
        var iW=n===1?hw*0.84:hw*0.74;
        ctx.moveTo(cx-iW,tipY+cd*5);
        ctx.bezierCurveTo(cx-iW,tipY+cd*1,cx-iW*0.78,tipY,cx-iW*0.48,tipY);
        ctx.lineTo(cx+iW*0.48,tipY);
        ctx.bezierCurveTo(cx+iW*0.78,tipY,cx+iW,tipY+cd*1,cx+iW,tipY+cd*5);
        ctx.bezierCurveTo(cx+hw*1.04,tipY+cd*cH*0.40,cx+hw*1.04,cejY-cd*7,cx+hw*0.46,cejY);
        ctx.lineTo(cx-hw*0.46,cejY);
        ctx.bezierCurveTo(cx-hw*1.04,cejY-cd*7,cx-hw*1.04,tipY+cd*cH*0.40,cx-iW,tipY+cd*5);
    }
    ctx.closePath();ctx.fill();ctx.stroke();
    if(isMol&&!impl){
        ctx.strokeStyle='rgba(78,66,50,0.18)';ctx.lineWidth=0.55;
        ctx.beginPath();ctx.moveTo(cx,tipY+cd*3);ctx.lineTo(cx,tipY+cd*cH*0.38);ctx.stroke();
        ctx.beginPath();ctx.moveTo(cx-hw*0.32,tipY+cd*6);ctx.lineTo(cx+hw*0.32,tipY+cd*6);ctx.stroke();
    }
}
function dibujarPeriodontograma() {
    var cfgs=[
        {id:'sup',dientes:[18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28],cd:-1},
        {id:'inf',dientes:[48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38],cd:1}
    ];
    cfgs.forEach(function(cfg){
        var canvas=document.getElementById('pcv-'+cfg.id);
        if(!canvas)return;
        var tbl=document.getElementById('pt-'+cfg.id);
        var tdCanvas=canvas.parentElement;
        var W=tdCanvas?Math.max(tdCanvas.offsetWidth,300):(tbl?Math.max(tbl.offsetWidth-115,300):640);
        canvas.width=W;
        var H=canvas.height;
        var ctx=canvas.getContext('2d');
        ctx.clearRect(0,0,W,H);
        var n16=cfg.dientes.length;
        var colW=W/(n16*3);
        var toothW=W/n16;
        var midY=cfg.cd<0?70:120;
        var scale=4;
        ctx.fillStyle='#f4f2ee';ctx.fillRect(0,0,W,H);
        ctx.setLineDash([2,3]);
        for(var mm=1;mm<=18;mm++){
            var y1=midY+mm*scale,y2=midY-mm*scale;
            ctx.lineWidth=0.4;ctx.strokeStyle=mm%5===0?'rgba(100,100,100,0.22)':'rgba(150,150,150,0.09)';
            if(y1<H){ctx.beginPath();ctx.moveTo(0,y1);ctx.lineTo(W,y1);ctx.stroke();}
            if(y2>0){ctx.beginPath();ctx.moveTo(0,y2);ctx.lineTo(W,y2);ctx.stroke();}
        }
        ctx.setLineDash([]);
        cfg.dientes.forEach(function(d,di){
            var aus=document.querySelector('.paus-cb[data-diente="'+d+'"]');
            var impl=document.querySelector('.pimpl-cb[data-diente="'+d+'"]');
            dibujarDiente(ctx,(di+0.5)*toothW,midY,toothW,d,cfg.cd,aus&&aus.checked,impl&&impl.checked);
        });
        ctx.strokeStyle='rgba(95,85,72,0.22)';ctx.lineWidth=0.5;
        for(var di=1;di<n16;di++){var sx=di*toothW;ctx.beginPath();ctx.moveTo(sx,3);ctx.lineTo(sx,H-3);ctx.stroke();}
        ctx.strokeStyle='rgba(120,100,80,0.5)';ctx.lineWidth=0.9;ctx.setLineDash([5,3]);
        ctx.beginPath();ctx.moveTo(0,midY);ctx.lineTo(W,midY);ctx.stroke();
        ctx.setLineDash([]);
        function getPts(pairs){
            var pts=[];
            cfg.dientes.forEach(function(d,di){
                var ausEl=document.querySelector('.paus-cb[data-diente="'+d+'"]');
                var aus=ausEl&&ausEl.checked;
                pairs.forEach(function(pair,ci){
                    var psEl=document.querySelector('.pps[data-diente="'+d+'"][data-cara="'+pair[0]+'"]');
                    var mgEl=document.querySelector('.pmg[data-diente="'+d+'"][data-cara="'+pair[1]+'"]');
                    pts.push({x:(di*3+ci)*colW+colW/2,ps:psEl&&psEl.value!==''?parseFloat(psEl.value):null,mg:mgEl&&mgEl.value!==''?parseFloat(mgEl.value):0,aus:aus});
                });
            });
            return pts;
        }
        function psCol(v){return v<=3?'#16a34a':v<=5?'#d97706':v<=7?'#ea580c':'#dc2626';}
        function drawLine(pts,dir){
            for(var i=0;i<pts.length;i++){var pt=pts[i];if(pt.aus||pt.ps===null)continue;var mg=pt.mg||0,ps=pt.ps;var gy=midY+mg*scale*dir,py=midY-(ps-mg)*scale*dir;var y0=Math.min(gy,py),y1=Math.max(gy,py);ctx.fillStyle=ps<=3?'rgba(22,163,74,0.13)':ps<=5?'rgba(217,119,6,0.17)':ps<=7?'rgba(234,88,12,0.21)':'rgba(220,38,38,0.25)';ctx.fillRect(pt.x-colW/2+1,Math.max(0,y0),colW-2,Math.min(H,y1)-Math.max(0,y0));}
            ctx.beginPath();var st=false;pts.forEach(function(pt){if(pt.aus){st=false;return;}var y=midY+(pt.mg||0)*scale*dir;if(!st){ctx.moveTo(pt.x,y);st=true;}else ctx.lineTo(pt.x,y);});ctx.strokeStyle='#DC3545';ctx.lineWidth=2.0;ctx.stroke();
            ctx.beginPath();st=false;pts.forEach(function(pt){if(pt.aus){st=false;return;}var y=midY-((pt.ps||0)-(pt.mg||0))*scale*dir;if(!st){ctx.moveTo(pt.x,y);st=true;}else ctx.lineTo(pt.x,y);});ctx.strokeStyle='#2563EB';ctx.lineWidth=2.2;ctx.stroke();
            pts.forEach(function(pt){if(pt.aus)return;var mg=pt.mg||0,ps=pt.ps||0;var gy=midY+mg*scale*dir;ctx.beginPath();ctx.arc(pt.x,gy,2.2,0,Math.PI*2);ctx.fillStyle='#DC3545';ctx.fill();var ny=midY-(ps-mg)*scale*dir;ctx.beginPath();ctx.arc(pt.x,ny,2.8,0,Math.PI*2);ctx.fillStyle=psCol(ps);ctx.fill();});
        }
        var ptsV=getPts([['ps_mv','mg_mv'],['ps_v','mg_v'],['ps_dv','mg_dv']]);
        var ptsL=getPts([['ps_ml','mg_ml'],['ps_l','mg_l'],['ps_dl','mg_dl']]);
        drawLine(ptsV,1);drawLine(ptsL,-1);
        ctx.font='bold 8px sans-serif';ctx.fillStyle='rgba(80,80,80,0.75)';
        ctx.fillText('V',4,midY+13);ctx.fillText('L',4,midY-5);
    });
}

document.getElementById('formPeriodEdit').addEventListener('submit', function() {
    calcularPlaca(); calcularSL(); calcularSondaje();
});
// Restaurar estado ausente en periodontograma
document.querySelectorAll('.paus-cb:checked').forEach(function(cb) {
    togglePerioAusente(cb.dataset.diente, true);
});
calcularPlaca(); calcularSL();
calcularSondaje();
window.addEventListener('load', function() { dibujarPeriodontograma(); });
window.addEventListener('resize', function() { dibujarPeriodontograma(); });
</script>
@endpush
