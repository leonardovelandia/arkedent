@extends('layouts.app')
@section('titulo', 'Ficha ' . $ficha->numero_ficha)

@push('estilos')
<style>
.per-tab-nav {
    display: flex;
    gap: .25rem;
    border-bottom: 2px solid var(--fondo-borde);
    margin-bottom: 1.25rem;
    overflow-x: auto;
    flex-wrap: nowrap;
}
.per-tab-btn {
    padding: .5rem 1rem;
    font-size: .82rem;
    font-weight: 600;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    color: var(--texto-secundario);
    white-space: nowrap;
    transition: color .15s, border-color .15s;
    margin-bottom: -2px;
}
.per-tab-btn.activo { color: var(--color-principal); border-bottom-color: var(--color-principal); }
.per-tab-panel { display: none; }
.per-tab-panel.activo { display: block; }

.sondaje-read { border-collapse: collapse; font-size: .72rem; width: 100%; }
.sondaje-read th, .sondaje-read td {
    border: 1px solid var(--fondo-borde);
    padding: .2rem .3rem;
    text-align: center;
}
.sondaje-read thead th { background: var(--fondo-card-alt); font-weight: 700; font-size: .65rem; text-transform: uppercase; color: var(--texto-secundario); }
.sval {
    display: inline-block;
    min-width: 28px;
    border-radius: 4px;
    font-weight: 700;
    font-size: .72rem;
    padding: .1rem .2rem;
    text-align: center;
}
.sval.s1 { background: #dcfce7; color: #166534; }
.sval.s2 { background: #fef9c3; color: #854d0e; }
.sval.s3 { background: #ffedd5; color: #9a3412; }
.sval.s4 { background: #fee2e2; color: #7f1d1d; }

/* Timeline controles */
.ctrl-timeline { position: relative; padding-left: 2rem; }
.ctrl-timeline::before { content: ''; position: absolute; left: .65rem; top: 0; bottom: 0; width: 2px; background: var(--fondo-borde); }
.ctrl-item { position: relative; margin-bottom: 1.25rem; }
.ctrl-dot {
    position: absolute;
    left: -1.65rem;
    top: .45rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px var(--fondo-borde);
}
.ctrl-card { background: var(--fondo-card-alt); border: 1px solid var(--fondo-borde); border-radius: 10px; padding: .85rem 1rem; }

/* ── Classic overrides ── */
body:not([data-ui="glass"]) .per-tab-btn { color:var(--texto-secundario); }
body:not([data-ui="glass"]) .per-tab-btn.activo { color:var(--color-principal); border-bottom-color:var(--color-principal); }
body:not([data-ui="glass"]) .ctrl-card { background:var(--fondo-card-alt); border:1px solid var(--fondo-borde); }

/* ── Aurora Glass overrides ── */
body[data-ui="glass"] .per-tab-btn { color:rgba(255,255,255,0.60) !important; }
body[data-ui="glass"] .per-tab-btn.activo { color:rgba(0,234,255,0.95) !important; border-bottom-color:rgba(0,234,255,0.80) !important; }
body[data-ui="glass"] .sondaje-read thead th { background:rgba(0,0,0,0.25) !important; color:rgba(0,234,255,0.80) !important; }
body[data-ui="glass"] .sondaje-read th,
body[data-ui="glass"] .sondaje-read td { border-color:rgba(0,234,255,0.15) !important; color:rgba(255,255,255,0.80) !important; }
body[data-ui="glass"] .sval.s1 { background:rgba(22,101,52,0.25) !important; color:#86efac !important; }
body[data-ui="glass"] .sval.s2 { background:rgba(133,77,14,0.25) !important; color:#fde68a !important; }
body[data-ui="glass"] .sval.s3 { background:rgba(154,52,18,0.25) !important; color:#fdba74 !important; }
body[data-ui="glass"] .sval.s4 { background:rgba(127,29,29,0.25) !important; color:#fca5a5 !important; }
body[data-ui="glass"] .ctrl-card { background:rgba(0,0,0,0.20) !important; border-color:rgba(0,234,255,0.20) !important; }
body[data-ui="glass"] .ctrl-card h6 { color:rgba(0,234,255,0.90) !important; }
</style>
@endpush

@section('contenido')

{{-- Breadcrumb --}}
<div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1.25rem;font-size:.82rem;flex-wrap:wrap;">
    <a href="{{ route('periodoncia.index') }}" style="color:var(--texto-secundario);text-decoration:none;">
        <i class="bi bi-heart-pulse me-1"></i>Periodoncia
    </a>
    <i class="bi bi-chevron-right" style="font-size:.65rem;color:var(--texto-secundario);"></i>
    <span style="color:var(--texto-principal);font-weight:600;">{{ $ficha->numero_ficha }}</span>
</div>

@if(session('exito'))
<div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;border-radius:8px;padding:.7rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <i class="bi bi-check-circle me-1"></i> {{ session('exito') }}
</div>
@endif

@php
$estadoBadges = [
    'activa'         => ['#d1fae5','#065f46'],
    'en_tratamiento' => ['#dbeafe','#1e40af'],
    'mantenimiento'  => ['#fef3c7','#92400e'],
    'finalizada'     => ['#f3f4f6','#374151'],
    'abandonada'     => ['#fee2e2','#7f1d1d'],
];
$bc = $estadoBadges[$ficha->estado] ?? ['#f3f4f6','#374151'];
@endphp

{{-- Header card --}}
<div class="card-sistema" style="margin-bottom:1.25rem;background:linear-gradient(135deg, var(--color-principal) 0%, var(--color-sidebar-2,#1a4a7a) 100%);color:white;padding:1.4rem 1.5rem;">
    <div style="display:flex;flex-wrap:wrap;gap:1rem;align-items:flex-start;justify-content:space-between;">
        <div style="flex:1;min-width:260px;">
            <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;margin-bottom:.5rem;">
                <span style="font-family:monospace;font-size:.85rem;font-weight:700;background:rgba(255,255,255,.2);padding:.2rem .6rem;border-radius:6px;">
                    {{ $ficha->numero_ficha }}
                </span>
                <span style="background:{{ $bc[0] }};color:{{ $bc[1] }};border-radius:20px;padding:.18rem .65rem;font-size:.72rem;font-weight:700;">
                    {{ $ficha->estado_label }}
                </span>
                @if($ficha->clasificacion_periodontal)
                <span style="background:rgba(255,255,255,.15);color:white;border-radius:20px;padding:.18rem .65rem;font-size:.72rem;font-weight:600;border:1px solid rgba(255,255,255,.3);">
                    {{ $ficha->clasificacion_label }}
                </span>
                @endif
            </div>
            <h2 style="margin:0 0 .25rem 0;font-size:1.2rem;font-weight:700;color:white;">
                <a href="{{ route('pacientes.show', $ficha->paciente) }}" style="color:white;text-decoration:none;">
                    {{ $ficha->paciente->nombre_completo }}
                </a>
            </h2>
            <p style="margin:0;font-size:.82rem;color:rgba(255,255,255,.8);">
                {{ $ficha->paciente->numero_documento }}
                @if($ficha->periodoncista)
                &nbsp;·&nbsp; Dr(a). {{ $ficha->periodoncista->name }}
                @endif
                &nbsp;·&nbsp; Inicio: {{ $ficha->fecha_inicio->format('d/m/Y') }}
                &nbsp;·&nbsp; {{ $ficha->controles->count() }} control(es)
            </p>
            @if($ficha->indice_placa_porcentaje !== null)
            <div style="margin-top:.65rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;font-size:.82rem;color:rgba(255,255,255,.9);">
                <span>Placa: <strong>{{ number_format($ficha->indice_placa_porcentaje,1) }}%</strong></span>
                @if($ficha->indice_gingival_porcentaje !== null)
                <span>S&amp;L: <strong>{{ number_format($ficha->indice_gingival_porcentaje,1) }}%</strong></span>
                @endif
            </div>
            @endif
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:.5rem;align-items:flex-start;">
            <a href="{{ route('periodoncia.controles.create', $ficha) }}"
               style="background:rgba(255,255,255,.95);color:var(--color-principal);text-decoration:none;padding:.45rem 1rem;border-radius:8px;font-size:.82rem;font-weight:700;display:inline-flex;align-items:center;gap:.35rem;">
                <i class="bi bi-plus-circle"></i> Nuevo Control
            </a>
            <a href="{{ route('periodoncia.pdf', $ficha) }}" target="_blank"
               style="background:rgba(255,255,255,.15);color:white;text-decoration:none;padding:.45rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;border:1px solid rgba(255,255,255,.35);">
                <i class="bi bi-file-pdf"></i> PDF
            </a>
            <a href="{{ route('periodoncia.edit', $ficha) }}"
               style="background:rgba(255,255,255,.15);color:white;text-decoration:none;padding:.45rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;border:1px solid rgba(255,255,255,.35);">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <form method="POST" action="{{ route('periodoncia.destroy', $ficha) }}"
                  onsubmit="return confirm('¿Eliminar esta ficha periodontal?');" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit"
                        style="background:rgba(239,68,68,.8);color:white;border:none;padding:.45rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;cursor:pointer;">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Cambiar estado --}}
    <form method="POST" action="{{ route('periodoncia.cambiar-estado', $ficha) }}"
          style="margin-top:1rem;display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
        @csrf
        <span style="font-size:.77rem;color:rgba(255,255,255,.75);font-weight:600;text-transform:uppercase;">Cambiar estado:</span>
        <select name="estado"
                style="border:1px solid rgba(255,255,255,.4);border-radius:8px;padding:.3rem .75rem;font-size:.82rem;background:rgba(255,255,255,.15);color:white;cursor:pointer;">
            @foreach(['activa'=>'Activa','en_tratamiento'=>'En tratamiento','mantenimiento'=>'Mantenimiento','finalizada'=>'Finalizada','abandonada'=>'Abandonada'] as $v => $l)
            <option value="{{ $v }}" {{ $ficha->estado == $v ? 'selected' : '' }}
                    style="background:#1e3a5f;color:white;">{{ $l }}</option>
            @endforeach
        </select>
        <button type="submit"
                style="background:rgba(255,255,255,.25);color:white;border:1px solid rgba(255,255,255,.4);padding:.3rem .85rem;border-radius:8px;font-size:.82rem;font-weight:600;cursor:pointer;">
            Actualizar
        </button>
    </form>
</div>

{{-- Tabs --}}
<div class="per-tab-nav">
    <button type="button" class="per-tab-btn activo" onclick="perTab('resumen', this)"><i class="bi bi-clipboard2-pulse me-1"></i>Resumen</button>
    <button type="button" class="per-tab-btn" onclick="perTab('periodontograma', this)"><i class="bi bi-table me-1"></i>Periodontograma</button>
    <button type="button" class="per-tab-btn" onclick="perTab('indices', this)"><i class="bi bi-bar-chart me-1"></i>Índices</button>
    <button type="button" class="per-tab-btn" onclick="perTab('controles', this)"><i class="bi bi-calendar-check me-1"></i>Controles ({{ $ficha->controles->count() }})</button>
    <button type="button" class="per-tab-btn" onclick="perTab('comparativo', this)"><i class="bi bi-arrow-left-right me-1"></i>Comparativo</button>
</div>

{{-- Tab Resumen --}}
<div id="per-tab-resumen" class="per-tab-panel activo">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card-sistema">
                <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.85rem;letter-spacing:.04em;">
                    <i class="bi bi-clipboard-data me-1"></i> Diagnóstico
                </h6>
                <div class="row g-2" style="font-size:.83rem;">
                    <div class="col-6">
                        <div style="color:var(--texto-secundario);font-size:.72rem;font-weight:700;text-transform:uppercase;margin-bottom:.2rem;">Clasificación</div>
                        <div style="font-weight:600;">{{ $ficha->clasificacion_label ?: '—' }}</div>
                    </div>
                    <div class="col-3">
                        <div style="color:var(--texto-secundario);font-size:.72rem;font-weight:700;text-transform:uppercase;margin-bottom:.2rem;">Extensión</div>
                        <div style="font-weight:600;">{{ $ficha->extension ? ucfirst($ficha->extension) : '—' }}</div>
                    </div>
                    <div class="col-3">
                        <div style="color:var(--texto-secundario);font-size:.72rem;font-weight:700;text-transform:uppercase;margin-bottom:.2rem;">Severidad</div>
                        <div style="font-weight:600;">{{ $ficha->severidad ? ucfirst($ficha->severidad) : '—' }}</div>
                    </div>
                    <div class="col-6">
                        <div style="color:var(--texto-secundario);font-size:.72rem;font-weight:700;text-transform:uppercase;margin-bottom:.2rem;">Pronóstico general</div>
                        <div style="font-weight:600;">{{ $ficha->pronostico_general ? ucfirst(str_replace('_',' ',$ficha->pronostico_general)) : '—' }}</div>
                    </div>
                    <div class="col-6">
                        <div style="color:var(--texto-secundario);font-size:.72rem;font-weight:700;text-transform:uppercase;margin-bottom:.2rem;">Factores de riesgo</div>
                        <div style="font-weight:600;">
                            @if($ficha->factores_riesgo && count($ficha->factores_riesgo))
                                {{ implode(', ', array_map('ucfirst', $ficha->factores_riesgo)) }}
                            @else
                                <span style="color:#9ca3af;">Ninguno</span>
                            @endif
                        </div>
                    </div>
                    @if($ficha->diagnostico_texto)
                    <div class="col-12" style="margin-top:.25rem;">
                        <div style="color:var(--texto-secundario);font-size:.72rem;font-weight:700;text-transform:uppercase;margin-bottom:.2rem;">Diagnóstico</div>
                        <div style="background:var(--fondo-card-alt);border-radius:8px;padding:.65rem;font-size:.82rem;line-height:1.5;">{{ $ficha->diagnostico_texto }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-sistema">
                <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.85rem;letter-spacing:.04em;">
                    <i class="bi bi-list-check me-1"></i> Plan de Tratamiento
                </h6>
                @if($ficha->plan_tratamiento)
                <div style="background:var(--fondo-card-alt);border-radius:8px;padding:.75rem;font-size:.83rem;line-height:1.6;white-space:pre-wrap;">{{ $ficha->plan_tratamiento }}</div>
                @else
                <p style="color:#9ca3af;font-size:.83rem;margin:0;">Sin plan de tratamiento registrado.</p>
                @endif

                @if($ficha->notas)
                <div style="margin-top:.85rem;">
                    <div style="color:var(--texto-secundario);font-size:.72rem;font-weight:700;text-transform:uppercase;margin-bottom:.35rem;">Notas</div>
                    <div style="background:var(--fondo-card-alt);border-radius:8px;padding:.65rem;font-size:.82rem;line-height:1.5;white-space:pre-wrap;">{{ $ficha->notas }}</div>
                </div>
                @endif
            </div>
        </div>
        <div class="col-12">
            <div class="card-sistema">
                <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.85rem;letter-spacing:.04em;">
                    <i class="bi bi-graph-up me-1"></i> Índices Registrados
                </h6>
                <div class="row g-3">
                    <div class="col-md-4 text-center">
                        <div style="font-size:2rem;font-weight:800;
                            color: {{ $ficha->indice_placa_porcentaje !== null ? ($ficha->indice_placa_porcentaje < 20 ? '#16a34a' : ($ficha->indice_placa_porcentaje < 40 ? '#d97706' : '#dc2626')) : '#9ca3af' }};">
                            {{ $ficha->indice_placa_porcentaje !== null ? number_format($ficha->indice_placa_porcentaje,1).'%' : '—' }}
                        </div>
                        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Índice de Placa</div>
                        @if($ficha->fecha_indice_placa)
                        <div style="font-size:.7rem;color:var(--texto-secundario);">{{ $ficha->fecha_indice_placa->format('d/m/Y') }}</div>
                        @endif
                    </div>
                    <div class="col-md-4 text-center">
                        @php
                            $slPct = $ficha->indice_gingival_porcentaje;
                            $slColor = $slPct !== null ? ($slPct <= 15 ? '#4ade80' : ($slPct <= 30 ? '#fbbf24' : '#f87171')) : '#9ca3af';
                        @endphp
                        <div style="font-size:2rem;font-weight:800;color:{{ $slColor }};">
                            {{ $slPct !== null ? number_format($slPct,1).'%' : '—' }}
                        </div>
                        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Placa S&amp;L Mod.</div>
                        @if($ficha->fecha_indice_gingival)
                        <div style="font-size:.7rem;color:var(--texto-secundario);">{{ $ficha->fecha_indice_gingival->format('d/m/Y') }}</div>
                        @endif
                    </div>
                    <div class="col-md-4 text-center">
                        @php $mejora = $ficha->porcentaje_mejora; @endphp
                        <div style="font-size:2rem;font-weight:800;color:{{ $mejora !== null ? ($mejora > 0 ? '#16a34a' : '#dc2626') : '#9ca3af' }};">
                            {{ $mejora !== null ? ($mejora > 0 ? '+' : '') . $mejora . '%' : '—' }}
                        </div>
                        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Mejora Sondaje</div>
                        <div style="font-size:.7rem;color:var(--texto-secundario);">Inicial vs. último control</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tab Periodontograma --}}
<div id="per-tab-periodontograma" class="per-tab-panel">
    @if($ficha->sondaje_datos && count($ficha->sondaje_datos))
    @php
        $dSup = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
        $dInf = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];
        $sondaje = $ficha->sondaje_datos;

        function svalClass($v) {
            if (!is_numeric($v) || $v == 0) return '';
            $v = (int)$v;
            if ($v <= 3) return 's1';
            if ($v <= 5) return 's2';
            if ($v <= 7) return 's3';
            return 's4';
        }
        function sv($datos, $d, $c) {
            $v = $datos[$d][$c] ?? '';
            if ($v === '' || $v === null) return '<span style="color:#ccc;">—</span>';
            $cl = svalClass($v);
            return '<span class="sval ' . $cl . '">' . $v . '</span>';
        }
    @endphp
    <div class="card-sistema">
        <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.75rem;letter-spacing:.04em;">
            Arcada Superior — Fecha: {{ $ficha->fecha_sondaje ? $ficha->fecha_sondaje->format('d/m/Y') : '—' }}
        </h6>
        <div style="overflow-x:auto;margin-bottom:1.5rem;">
        <table class="sondaje-read">
            <thead><tr>
                <th style="text-align:left;min-width:55px;">Cara</th>
                @foreach($dSup as $d)<th>{{ $d }}</th>@endforeach
            </tr></thead>
            <tbody>
            @foreach([['MV','mv'],['V','v'],['DV','dv']] as $f)
            <tr><td style="font-weight:700;font-size:.65rem;color:var(--texto-secundario);">{{ $f[0] }}</td>
                @foreach($dSup as $d)<td>{!! sv($sondaje,$d,$f[1]) !!}</td>@endforeach
            </tr>
            @endforeach
            <tr style="background:var(--fondo-card-alt);">
                <td style="font-weight:700;font-size:.65rem;color:var(--texto-secundario);">Sang.</td>
                @foreach($dSup as $d)
                <td>{!! isset($sondaje[$d]['sangrado']) && $sondaje[$d]['sangrado'] ? '<span style="color:#dc2626;font-weight:800;">●</span>' : '<span style="color:#d1d5db;">○</span>' !!}</td>
                @endforeach
            </tr>
            <tr><td style="font-weight:700;font-size:.65rem;color:var(--texto-secundario);">Furc.</td>
                @foreach($dSup as $d)<td style="font-size:.7rem;font-weight:700;">{{ $sondaje[$d]['furcacion'] ?? '—' }}</td>@endforeach
            </tr>
            <tr><td style="font-weight:700;font-size:.65rem;color:var(--texto-secundario);">Mov.</td>
                @foreach($dSup as $d)<td style="font-size:.7rem;font-weight:700;">{{ $sondaje[$d]['movilidad'] ?? '0' }}</td>@endforeach
            </tr>
            <tr><td style="font-weight:700;font-size:.65rem;color:var(--texto-secundario);">Rec.</td>
                @foreach($dSup as $d)<td style="font-size:.7rem;color:#059669;font-weight:700;">{{ $sondaje[$d]['recesion'] ?? '—' }}</td>@endforeach
            </tr>
            @foreach([['ML','ml'],['L','l'],['DL','dl']] as $f)
            <tr><td style="font-weight:700;font-size:.65rem;color:var(--texto-secundario);">{{ $f[0] }}</td>
                @foreach($dSup as $d)<td>{!! sv($sondaje,$d,$f[1]) !!}</td>@endforeach
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>

        <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.75rem;letter-spacing:.04em;">Arcada Inferior</h6>
        <div style="overflow-x:auto;">
        <table class="sondaje-read">
            <thead><tr>
                <th style="text-align:left;min-width:55px;">Cara</th>
                @foreach($dInf as $d)<th>{{ $d }}</th>@endforeach
            </tr></thead>
            <tbody>
            @foreach([['MV','mv'],['V','v'],['DV','dv']] as $f)
            <tr><td style="font-weight:700;font-size:.65rem;color:var(--texto-secundario);">{{ $f[0] }}</td>
                @foreach($dInf as $d)<td>{!! sv($sondaje,$d,$f[1]) !!}</td>@endforeach
            </tr>
            @endforeach
            <tr style="background:var(--fondo-card-alt);">
                <td style="font-weight:700;font-size:.65rem;color:var(--texto-secundario);">Sang.</td>
                @foreach($dInf as $d)
                <td>{!! isset($sondaje[$d]['sangrado']) && $sondaje[$d]['sangrado'] ? '<span style="color:#dc2626;font-weight:800;">●</span>' : '<span style="color:#d1d5db;">○</span>' !!}</td>
                @endforeach
            </tr>
            <tr><td style="font-weight:700;font-size:.65rem;color:var(--texto-secundario);">Furc.</td>
                @foreach($dInf as $d)<td style="font-size:.7rem;font-weight:700;">{{ $sondaje[$d]['furcacion'] ?? '—' }}</td>@endforeach
            </tr>
            <tr><td style="font-weight:700;font-size:.65rem;color:var(--texto-secundario);">Mov.</td>
                @foreach($dInf as $d)<td style="font-size:.7rem;font-weight:700;">{{ $sondaje[$d]['movilidad'] ?? '0' }}</td>@endforeach
            </tr>
            <tr><td style="font-weight:700;font-size:.65rem;color:var(--texto-secundario);">Rec.</td>
                @foreach($dInf as $d)<td style="font-size:.7rem;color:#059669;font-weight:700;">{{ $sondaje[$d]['recesion'] ?? '—' }}</td>@endforeach
            </tr>
            @foreach([['ML','ml'],['L','l'],['DL','dl']] as $f)
            <tr><td style="font-weight:700;font-size:.65rem;color:var(--texto-secundario);">{{ $f[0] }}</td>
                @foreach($dInf as $d)<td>{!! sv($sondaje,$d,$f[1]) !!}</td>@endforeach
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
    @else
    <div class="card-sistema" style="text-align:center;padding:3rem;color:var(--texto-secundario);">
        <i class="bi bi-table" style="font-size:2rem;display:block;margin-bottom:.75rem;opacity:.3;"></i>
        <p style="margin:0;">No hay datos de sondaje registrados. <a href="{{ route('periodoncia.edit', $ficha) }}" style="color:var(--color-principal);">Editar ficha</a></p>
    </div>
    @endif
</div>

{{-- Tab Índices --}}
<div id="per-tab-indices" class="per-tab-panel">
    <div class="row g-3">
        @if($ficha->indice_placa_datos && count($ficha->indice_placa_datos))
        <div class="col-12">
            <div class="card-sistema">
                <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.75rem;letter-spacing:.04em;">
                    <i class="bi bi-grid-3x3 me-1"></i> Índice de Placa O'Leary
                    <span style="font-weight:400;text-transform:none;color:var(--texto-secundario);margin-left:.5rem;font-size:.82rem;">
                        {{ number_format($ficha->indice_placa_porcentaje ?? 0, 1) }}%
                        @if($ficha->fecha_indice_placa) — {{ $ficha->fecha_indice_placa->format('d/m/Y') }} @endif
                    </span>
                </h6>
                @php
                    $plDatos = $ficha->indice_placa_datos;
                    $dSupOl = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
                    $dInfOl = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];
                @endphp
                <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);margin-bottom:.4rem;">Superior</p>
                <div style="display:flex;flex-wrap:wrap;gap:3px;justify-content:center;margin-bottom:.75rem;">
                @foreach($dSupOl as $d)
                @php $dc = $plDatos[$d] ?? ['v'=>0,'d'=>0,'l'=>0,'m'=>0]; @endphp
                <div style="display:inline-flex;flex-direction:column;align-items:center;margin:0 2px;">
                    <div style="font-size:.6rem;font-weight:700;color:var(--texto-secundario);margin-bottom:2px;">{{ $d }}</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;grid-template-rows:1fr 1fr;width:26px;height:26px;border:1px solid #ccc;gap:1px;padding:1px;background:#ccc;border-radius:3px;">
                        <div style="background:{{ ($dc['v'] ?? 0) ? '#dc2626' : 'white' }};border-radius:1px;" title="Vestibular"></div>
                        <div style="background:{{ ($dc['d'] ?? 0) ? '#dc2626' : 'white' }};border-radius:1px;" title="Distal"></div>
                        <div style="background:{{ ($dc['l'] ?? 0) ? '#dc2626' : 'white' }};border-radius:1px;" title="Lingual"></div>
                        <div style="background:{{ ($dc['m'] ?? 0) ? '#dc2626' : 'white' }};border-radius:1px;" title="Mesial"></div>
                    </div>
                </div>
                @endforeach
                </div>
                <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);margin-bottom:.4rem;">Inferior</p>
                <div style="display:flex;flex-wrap:wrap;gap:3px;justify-content:center;">
                @foreach($dInfOl as $d)
                @php $dc = $plDatos[$d] ?? ['v'=>0,'d'=>0,'l'=>0,'m'=>0]; @endphp
                <div style="display:inline-flex;flex-direction:column;align-items:center;margin:0 2px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;grid-template-rows:1fr 1fr;width:26px;height:26px;border:1px solid #ccc;gap:1px;padding:1px;background:#ccc;border-radius:3px;">
                        <div style="background:{{ ($dc['v'] ?? 0) ? '#dc2626' : 'white' }};border-radius:1px;"></div>
                        <div style="background:{{ ($dc['d'] ?? 0) ? '#dc2626' : 'white' }};border-radius:1px;"></div>
                        <div style="background:{{ ($dc['l'] ?? 0) ? '#dc2626' : 'white' }};border-radius:1px;"></div>
                        <div style="background:{{ ($dc['m'] ?? 0) ? '#dc2626' : 'white' }};border-radius:1px;"></div>
                    </div>
                    <div style="font-size:.6rem;font-weight:700;color:var(--texto-secundario);margin-top:2px;">{{ $d }}</div>
                </div>
                @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($ficha->indice_gingival_datos && count($ficha->indice_gingival_datos))
        <div class="col-12">
            <div class="card-sistema">
                @php
                $slLabels = [
                    'molar1q' => ['label' => 'Último molar 1er cuadrante', 'sups' => ['D','V','O','P','M']],
                    'd11'     => ['label' => '11 / 51',                     'sups' => ['D','V','P','M']],
                    'd23'     => ['label' => '23 / 63',                     'sups' => ['M','V','P','D']],
                    'molar2q' => ['label' => 'Último molar 2° cuadrante',   'sups' => ['M','V','O','P','D']],
                    'molar3q' => ['label' => 'Último molar 3er cuadrante',  'sups' => ['D','V','O','L','M']],
                    'd44'     => ['label' => '44 / 84',                     'sups' => ['M','V','O','L','D']],
                    'molar4q' => ['label' => 'Último molar 4° cuadrante',   'sups' => ['M','V','O','L','D']],
                ];
                $igDatos = $ficha->indice_gingival_datos;
                $esSL = isset($igDatos['molar1q']) || isset($igDatos['d11']);
                @endphp

                @if($esSL)
                {{-- Nuevo formato: Silness & Löe Modificado --}}
                <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.75rem;letter-spacing:.04em;">
                    <i class="bi bi-grid-3x2 me-1"></i> Índice de Placa Silness &amp; Löe Modificado
                    <span style="font-weight:400;text-transform:none;color:var(--texto-secundario);margin-left:.5rem;font-size:.82rem;">
                        {{ number_format($ficha->indice_gingival_porcentaje ?? 0, 1) }}%
                        @if($ficha->fecha_indice_gingival) — {{ $ficha->fecha_indice_gingival->format('d/m/Y') }} @endif
                    </span>
                </h6>
                <div style="overflow-x:auto;">
                <table style="border-collapse:collapse;font-size:.72rem;min-width:100%;">
                    <thead>
                        <tr>
                            <th rowspan="2" style="border:1px solid var(--fondo-borde);padding:.3rem .5rem;background:var(--fondo-card-alt);font-size:.62rem;text-transform:uppercase;writing-mode:vertical-lr;transform:rotate(180deg);color:var(--texto-secundario);">Diente</th>
                            @foreach($slLabels as $gKey => $gInfo)
                            @php $ausente = isset($igDatos[$gKey]) && !empty($igDatos[$gKey]['ausente']); @endphp
                            <th colspan="{{ count($gInfo['sups']) }}" style="border:1px solid var(--fondo-borde);padding:.3rem .25rem;background:var(--fondo-card-alt);font-size:.63rem;font-weight:700;text-align:center;{{ $ausente ? 'opacity:.4;' : '' }}">
                                {{ $gInfo['label'] }}
                                @if($ausente)<br><span style="font-weight:400;font-size:.6rem;color:#9ca3af;">(Ausente)</span>@endif
                            </th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($slLabels as $gKey => $gInfo)
                                @foreach($gInfo['sups'] as $sup)
                                <th style="border:1px solid var(--fondo-borde);padding:.25rem .2rem;background:var(--fondo-card-alt);font-size:.62rem;color:var(--texto-secundario);min-width:24px;text-align:center;">{{ $sup }}</th>
                                @endforeach
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border:1px solid var(--fondo-borde);padding:.3rem .4rem;font-size:.62rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);background:var(--fondo-card-alt);">Código</td>
                            @foreach($slLabels as $gKey => $gInfo)
                                @php $ausente = isset($igDatos[$gKey]) && !empty($igDatos[$gKey]['ausente']); @endphp
                                @foreach($gInfo['sups'] as $sup)
                                @php $val = isset($igDatos[$gKey][$sup]) ? $igDatos[$gKey][$sup] : 0; @endphp
                                <td style="border:1px solid var(--fondo-borde);width:26px;height:24px;text-align:center;position:relative;{{ $ausente ? 'background:var(--fondo-borde);opacity:.4;' : ($val ? 'background:#2a0a0a;' : '') }}">
                                    @if(!$ausente && $val)
                                    <svg width="18" height="18" viewBox="0 0 18 18" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);">
                                        <line x1="2" y1="2" x2="16" y2="16" stroke="#f87171" stroke-width="1.5" stroke-linecap="round"/>
                                        <line x1="16" y1="2" x2="2" y2="16" stroke="#f87171" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                    @endif
                                </td>
                                @endforeach
                            @endforeach
                        </tr>
                    </tbody>
                </table>
                </div>
                @php
                    $supTotal = 0; $posTotal = 0;
                    foreach ($slLabels as $gKey => $gInfo) {
                        if (!empty($igDatos[$gKey]['ausente'])) continue;
                        foreach ($gInfo['sups'] as $sup) { $supTotal++; $posTotal += ($igDatos[$gKey][$sup] ?? 0); }
                    }
                    $pctSL = $supTotal > 0 ? round($posTotal / $supTotal * 100, 1) : 0;
                    $colorSL = $pctSL <= 15 ? '#4ade80' : ($pctSL <= 30 ? '#fbbf24' : '#f87171');
                    $higieneSL = $pctSL <= 15 ? 'Buena (0-15%)' : ($pctSL <= 30 ? 'Regular (16-30%)' : 'Deficiente (31-100%)');
                @endphp
                <div style="margin-top:.75rem;font-size:.78rem;display:flex;flex-wrap:wrap;gap:.5rem 1.5rem;">
                    <span>Superficies examinadas: <strong>{{ $supTotal }}</strong></span>
                    <span>Valores "1": <strong>{{ $posTotal }}</strong></span>
                    <span>Placa: <strong style="color:{{ $colorSL }};">{{ $pctSL }}%</strong></span>
                    <span>Higiene Oral: <strong style="color:{{ $colorSL }};">{{ $higieneSL }}</strong></span>
                </div>

                @else
                {{-- Formato antiguo: Índice Gingival Löe & Silness --}}
                <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.75rem;letter-spacing:.04em;">
                    <i class="bi bi-bar-chart me-1"></i> Índice Gingival
                    <span style="font-weight:400;text-transform:none;color:var(--texto-secundario);margin-left:.5rem;font-size:.82rem;">
                        Promedio: {{ number_format($ficha->indice_gingival_porcentaje ?? 0, 2) }}
                        @if($ficha->fecha_indice_gingival) — {{ $ficha->fecha_indice_gingival->format('d/m/Y') }} @endif
                    </span>
                </h6>
                <div style="overflow-x:auto;">
                <table style="border-collapse:collapse;font-size:.72rem;width:100%;">
                    <thead>
                        <tr style="background:var(--fondo-card-alt);">
                            <th style="padding:.3rem .6rem;text-align:left;font-size:.65rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Diente</th>
                            <th style="padding:.3rem .6rem;font-size:.65rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Vest.</th>
                            <th style="padding:.3rem .6rem;font-size:.65rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Distal</th>
                            <th style="padding:.3rem .6rem;font-size:.65rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Ling.</th>
                            <th style="padding:.3rem .6rem;font-size:.65rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Mesial</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($igDatos as $d => $vals)
                    <tr style="border-bottom:1px solid var(--fondo-borde);">
                        <td style="padding:.3rem .6rem;font-weight:700;color:var(--color-principal);">{{ $d }}</td>
                        @foreach(['v','d','l','m'] as $c)
                        <td style="padding:.3rem .6rem;text-align:center;">{{ isset($vals[$c]) && $vals[$c] !== null ? $vals[$c] : '—' }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if(!$ficha->indice_placa_datos && !$ficha->indice_gingival_datos)
        <div class="col-12">
            <div class="card-sistema" style="text-align:center;padding:3rem;color:var(--texto-secundario);">
                <i class="bi bi-bar-chart" style="font-size:2rem;display:block;margin-bottom:.75rem;opacity:.3;"></i>
                <p style="margin:0;">No hay datos de índices registrados. <a href="{{ route('periodoncia.edit', $ficha) }}" style="color:var(--color-principal);">Editar ficha</a></p>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Tab Controles --}}
<div id="per-tab-controles" class="per-tab-panel">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
        <h6 style="margin:0;font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);letter-spacing:.04em;">
            Historial de Controles
        </h6>
        <a href="{{ route('periodoncia.controles.create', $ficha) }}"
           style="background:var(--color-principal);color:white;text-decoration:none;padding:.4rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;display:inline-flex;align-items:center;gap:.35rem;">
            <i class="bi bi-plus-circle"></i> Nuevo Control
        </a>
    </div>

    @if($ficha->controles->isEmpty())
    <div class="card-sistema" style="text-align:center;padding:3rem;color:var(--texto-secundario);">
        <i class="bi bi-calendar-x" style="font-size:2rem;display:block;margin-bottom:.75rem;opacity:.3;"></i>
        <p style="margin:0;">No hay controles registrados aún.</p>
    </div>
    @else
    <div class="ctrl-timeline">
    @foreach($ficha->controles as $ctrl)
    <div class="ctrl-item">
        <div class="ctrl-dot" style="background:{{ $ctrl->tipo_sesion_color }};box-shadow:0 0 0 2px {{ $ctrl->tipo_sesion_color }}40;"></div>
        <div class="ctrl-card">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.5rem;">
                <div>
                    <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-bottom:.3rem;">
                        <span style="background:{{ $ctrl->tipo_sesion_color }}20;color:{{ $ctrl->tipo_sesion_color }};border-radius:20px;padding:.12rem .65rem;font-size:.72rem;font-weight:700;border:1px solid {{ $ctrl->tipo_sesion_color }}40;">
                            {{ $ctrl->tipo_sesion_label }}
                        </span>
                        <span style="font-size:.77rem;color:var(--texto-secundario);">
                            Sesión #{{ $ctrl->numero_sesion }} &nbsp;·&nbsp; {{ $ctrl->fecha_control->format('d/m/Y') }}
                        </span>
                        @if($ctrl->periodoncista)
                        <span style="font-size:.75rem;color:var(--texto-secundario);">Dr(a). {{ $ctrl->periodoncista->name }}</span>
                        @endif
                    </div>
                    @if($ctrl->observaciones)
                    <p style="margin:0;font-size:.83rem;color:var(--texto-principal);line-height:1.5;">{{ Str::limit($ctrl->observaciones, 120) }}</p>
                    @endif
                    <div style="display:flex;gap:1.25rem;margin-top:.4rem;flex-wrap:wrap;font-size:.78rem;">
                        @if($ctrl->indice_placa_control !== null)
                        <span style="color:var(--texto-secundario);">Placa: <strong style="color:{{ $ctrl->indice_placa_control < 20 ? '#16a34a' : ($ctrl->indice_placa_control < 40 ? '#d97706' : '#dc2626') }};">{{ number_format($ctrl->indice_placa_control,1) }}%</strong></span>
                        @endif
                        @if($ctrl->indice_gingival_control !== null)
                        <span style="color:var(--texto-secundario);">ÍG: <strong style="color:var(--color-principal);">{{ number_format($ctrl->indice_gingival_control,2) }}</strong></span>
                        @endif
                        @if($ctrl->proxima_cita_semanas)
                        <span style="color:var(--texto-secundario);">Próxima cita: <strong>{{ $ctrl->proxima_cita_semanas }} sem.</strong></span>
                        @endif
                    </div>
                </div>
                <div style="display:flex;gap:.35rem;">
                    <a href="{{ route('periodoncia.controles.show', $ctrl) }}"
                       style="background:var(--color-muy-claro);color:var(--color-principal);padding:.3rem .6rem;border-radius:6px;font-size:.75rem;text-decoration:none;">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('periodoncia.controles.edit', $ctrl) }}"
                       style="background:#f0fdf4;color:#065f46;padding:.3rem .6rem;border-radius:6px;font-size:.75rem;text-decoration:none;">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <a href="{{ route('periodoncia.controles.pdf', $ctrl) }}" target="_blank"
                       style="background:#fef3c7;color:#92400e;padding:.3rem .6rem;border-radius:6px;font-size:.75rem;text-decoration:none;">
                        <i class="bi bi-file-pdf"></i>
                    </a>
                    <form method="POST" action="{{ route('periodoncia.controles.destroy', $ctrl) }}"
                          onsubmit="return confirm('¿Eliminar este control?');" style="margin:0;">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:#fee2e2;color:#7f1d1d;border:none;padding:.3rem .6rem;border-radius:6px;font-size:.75rem;cursor:pointer;">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    </div>
    @endif
</div>

{{-- Tab Comparativo --}}
<div id="per-tab-comparativo" class="per-tab-panel">
    <div class="card-sistema">
        <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.85rem;letter-spacing:.04em;">
            <i class="bi bi-arrow-left-right me-1"></i> Comparativo de Índices por Sesión
        </h6>
        @php
            $tieneControlesConDatos = $ficha->controles->filter(fn($c) => $c->indice_placa_control !== null || $c->indice_gingival_control !== null)->isNotEmpty();
        @endphp
        @if($tieneControlesConDatos || $ficha->indice_placa_porcentaje !== null)
        <div style="overflow-x:auto;">
        <table style="border-collapse:collapse;font-size:.82rem;width:100%;">
            <thead>
                <tr style="background:var(--fondo-card-alt);">
                    <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Sesión</th>
                    <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Fecha</th>
                    <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Tipo</th>
                    <th style="padding:.45rem .9rem;text-align:center;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Índice Placa</th>
                    <th style="padding:.45rem .9rem;text-align:center;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Índice Gingival</th>
                </tr>
            </thead>
            <tbody>
            {{-- Registro inicial --}}
            @if($ficha->indice_placa_porcentaje !== null || $ficha->indice_gingival_porcentaje !== null)
            <tr style="border-bottom:1px solid var(--fondo-borde);background:var(--color-muy-claro);">
                <td style="padding:.45rem .9rem;font-weight:700;color:var(--color-principal);">Inicial</td>
                <td style="padding:.45rem .9rem;">{{ $ficha->fecha_inicio->format('d/m/Y') }}</td>
                <td style="padding:.45rem .9rem;color:var(--texto-secundario);">Diagnóstico inicial</td>
                <td style="padding:.45rem .9rem;text-align:center;">
                    @if($ficha->indice_placa_porcentaje !== null)
                    @php $p = $ficha->indice_placa_porcentaje; @endphp
                    <strong style="color:{{ $p < 20 ? '#16a34a' : ($p < 40 ? '#d97706' : '#dc2626') }};">{{ number_format($p,1) }}%</strong>
                    @else <span style="color:#9ca3af;">—</span> @endif
                </td>
                <td style="padding:.45rem .9rem;text-align:center;">
                    @if($ficha->indice_gingival_porcentaje !== null)
                    @php $ig = $ficha->indice_gingival_porcentaje; @endphp
                    <strong style="color:{{ $ig < 1 ? '#16a34a' : ($ig < 2 ? '#d97706' : '#dc2626') }};">{{ number_format($ig,2) }}</strong>
                    @else <span style="color:#9ca3af;">—</span> @endif
                </td>
            </tr>
            @endif
            @foreach($ficha->controles as $ctrl)
            <tr style="border-bottom:1px solid var(--fondo-borde);">
                <td style="padding:.45rem .9rem;font-weight:700;color:var(--color-principal);">#{{ $ctrl->numero_sesion }}</td>
                <td style="padding:.45rem .9rem;">{{ $ctrl->fecha_control->format('d/m/Y') }}</td>
                <td style="padding:.45rem .9rem;">
                    <span style="font-size:.72rem;background:{{ $ctrl->tipo_sesion_color }}20;color:{{ $ctrl->tipo_sesion_color }};border-radius:20px;padding:.1rem .5rem;font-weight:600;">
                        {{ $ctrl->tipo_sesion_label }}
                    </span>
                </td>
                <td style="padding:.45rem .9rem;text-align:center;">
                    @if($ctrl->indice_placa_control !== null)
                    @php $p = $ctrl->indice_placa_control; @endphp
                    <strong style="color:{{ $p < 20 ? '#16a34a' : ($p < 40 ? '#d97706' : '#dc2626') }};">{{ number_format($p,1) }}%</strong>
                    @else <span style="color:#9ca3af;">—</span> @endif
                </td>
                <td style="padding:.45rem .9rem;text-align:center;">
                    @if($ctrl->indice_gingival_control !== null)
                    @php $ig = $ctrl->indice_gingival_control; @endphp
                    <strong style="color:{{ $ig < 1 ? '#16a34a' : ($ig < 2 ? '#d97706' : '#dc2626') }};">{{ number_format($ig,2) }}</strong>
                    @else <span style="color:#9ca3af;">—</span> @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @else
        <p style="color:#9ca3af;font-size:.83rem;text-align:center;padding:2rem 0;margin:0;">
            No hay suficientes datos para mostrar comparativo. Registre controles con índices para ver la evolución.
        </p>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
function perTab(nombre, btn) {
    document.querySelectorAll('.per-tab-panel').forEach(p => p.classList.remove('activo'));
    document.querySelectorAll('.per-tab-btn').forEach(b => b.classList.remove('activo'));
    document.getElementById('per-tab-' + nombre).classList.add('activo');
    btn.classList.add('activo');
}
</script>
@endpush
