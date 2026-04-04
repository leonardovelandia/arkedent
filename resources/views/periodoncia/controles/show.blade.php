@extends('layouts.app')
@section('titulo', 'Control ' . $control->numero_control)

@push('estilos')
<style>
    /* ── Aurora Glass: card-sistema show view ── */
    body[data-ui="glass"] .card-sistema h6 { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .card-sistema p  { color:rgba(255,255,255,0.80) !important; }
    body[data-ui="glass"] .card-sistema [style*="background:var(--fondo-card-alt)"],
    body[data-ui="glass"] .card-sistema [style*="background: var(--fondo-card-alt)"] {
        background:rgba(0,0,0,0.20) !important;
        border-color:rgba(0,234,255,0.20) !important;
    }
    body[data-ui="glass"] .card-sistema [style*="background:#fffbeb"],
    body[data-ui="glass"] .card-sistema [style*="background: #fffbeb"] {
        background:rgba(251,191,36,0.10) !important;
        border-color:rgba(251,191,36,0.30) !important;
    }
</style>
@endpush

@section('contenido')

{{-- Breadcrumb --}}
<div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1.25rem;font-size:.82rem;flex-wrap:wrap;">
    <a href="{{ route('periodoncia.index') }}" style="color:var(--texto-secundario);text-decoration:none;"><i class="bi bi-heart-pulse me-1"></i>Periodoncia</a>
    <i class="bi bi-chevron-right" style="font-size:.65rem;color:var(--texto-secundario);"></i>
    <a href="{{ route('periodoncia.show', $control->fichaPeriodontal) }}" style="color:var(--texto-secundario);text-decoration:none;">{{ $control->fichaPeriodontal->numero_ficha }}</a>
    <i class="bi bi-chevron-right" style="font-size:.65rem;color:var(--texto-secundario);"></i>
    <span style="color:var(--texto-principal);font-weight:600;">{{ $control->numero_control }}</span>
</div>

@if(session('exito'))
<div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;border-radius:8px;padding:.7rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <i class="bi bi-check-circle me-1"></i> {{ session('exito') }}
</div>
@endif

{{-- Header --}}
<div class="card-sistema" style="margin-bottom:1.25rem;">
    <div style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:flex-start;gap:1rem;">
        <div>
            <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;margin-bottom:.5rem;">
                <span style="font-family:monospace;font-size:.85rem;font-weight:700;color:var(--color-principal);background:var(--color-muy-claro);padding:.2rem .6rem;border-radius:6px;">
                    {{ $control->numero_control }}
                </span>
                <span style="background:{{ $control->tipo_sesion_color }}20;color:{{ $control->tipo_sesion_color }};border-radius:20px;padding:.18rem .65rem;font-size:.72rem;font-weight:700;border:1px solid {{ $control->tipo_sesion_color }}40;">
                    {{ $control->tipo_sesion_label }}
                </span>
                <span style="font-size:.8rem;color:var(--texto-secundario);">Sesión #{{ $control->numero_sesion }}</span>
            </div>
            <h2 style="margin:0 0 .25rem;font-size:1.15rem;font-weight:700;">
                {{ $control->fichaPeriodontal->paciente->nombre_completo }}
            </h2>
            <p style="margin:0;font-size:.82rem;color:var(--texto-secundario);">
                Fecha: {{ $control->fecha_control->format('d/m/Y') }}
                @if($control->periodoncista) &nbsp;·&nbsp; Dr(a). {{ $control->periodoncista->name }} @endif
                @if($control->proxima_cita_semanas) &nbsp;·&nbsp; Próxima cita: {{ $control->proxima_cita_semanas }} sem. @endif
            </p>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <a href="{{ route('periodoncia.controles.edit', $control) }}"
               style="background:var(--color-muy-claro);color:var(--color-principal);text-decoration:none;padding:.4rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;">
                <i class="bi bi-pencil me-1"></i> Editar
            </a>
            <a href="{{ route('periodoncia.controles.pdf', $control) }}" target="_blank"
               style="background:#fef3c7;color:#92400e;text-decoration:none;padding:.4rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;">
                <i class="bi bi-file-pdf me-1"></i> PDF
            </a>
            <a href="{{ route('periodoncia.show', $control->fichaPeriodontal) }}"
               style="background:var(--fondo-borde);color:var(--texto-principal);text-decoration:none;padding:.4rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;">
                <i class="bi bi-arrow-left me-1"></i> Volver a ficha
            </a>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Índices --}}
    <div class="col-md-4">
        <div class="card-sistema" style="height:100%;">
            <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.85rem;letter-spacing:.04em;">
                <i class="bi bi-bar-chart me-1"></i> Índices de Control
            </h6>
            <div style="text-align:center;margin-bottom:1rem;">
                @if($control->indice_placa_control !== null)
                @php $p = $control->indice_placa_control; @endphp
                <div style="font-size:2rem;font-weight:800;color:{{ $p < 20 ? '#16a34a' : ($p < 40 ? '#d97706' : '#dc2626') }};">
                    {{ number_format($p, 1) }}%
                </div>
                <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Índice de Placa</div>
                @else
                <div style="font-size:1.5rem;color:#9ca3af;">—</div>
                <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Índice de Placa</div>
                @endif
            </div>
            <div style="text-align:center;margin-bottom:1rem;">
                @if($control->indice_gingival_control !== null)
                @php $ig = $control->indice_gingival_control; @endphp
                <div style="font-size:2rem;font-weight:800;color:{{ $ig < 1 ? '#16a34a' : ($ig < 2 ? '#d97706' : '#dc2626') }};">
                    {{ number_format($ig, 2) }}
                </div>
                <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Índice Gingival</div>
                @else
                <div style="font-size:1.5rem;color:#9ca3af;">—</div>
                <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Índice Gingival</div>
                @endif
            </div>
            @if($control->anestesia_utilizada)
            <div style="font-size:.82rem;margin-top:.5rem;">
                <span style="color:var(--texto-secundario);font-size:.72rem;font-weight:700;text-transform:uppercase;">Anestesia:</span><br>
                {{ $control->anestesia_utilizada }}
            </div>
            @endif
        </div>
    </div>

    {{-- Zonas tratadas --}}
    <div class="col-md-4">
        <div class="card-sistema" style="height:100%;">
            <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.85rem;letter-spacing:.04em;">
                <i class="bi bi-geo-alt me-1"></i> Zonas Tratadas
            </h6>
            @if($control->zonas_tratadas && count($control->zonas_tratadas))
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                @foreach((array)$control->zonas_tratadas as $zona)
                <span style="background:var(--color-muy-claro);color:var(--color-principal);border-radius:6px;padding:.2rem .55rem;font-size:.75rem;font-weight:700;font-family:monospace;">
                    {{ $zona }}
                </span>
                @endforeach
            </div>
            @else
            <p style="color:#9ca3af;font-size:.83rem;margin:0;">No se especificaron zonas.</p>
            @endif
        </div>
    </div>

    {{-- Procedimiento --}}
    <div class="col-md-4">
        <div class="card-sistema" style="height:100%;">
            <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.85rem;letter-spacing:.04em;">
                <i class="bi bi-tools me-1"></i> Procedimiento
            </h6>
            @if($control->instrumentos_utilizados)
            <div style="font-size:.83rem;line-height:1.5;white-space:pre-wrap;">{{ $control->instrumentos_utilizados }}</div>
            @else
            <p style="color:#9ca3af;font-size:.83rem;margin:0;">No registrado.</p>
            @endif
        </div>
    </div>

    {{-- Observaciones --}}
    @if($control->observaciones)
    <div class="col-md-6">
        <div class="card-sistema">
            <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.75rem;letter-spacing:.04em;">
                <i class="bi bi-journal-text me-1"></i> Observaciones Clínicas
            </h6>
            <div style="font-size:.83rem;line-height:1.6;white-space:pre-wrap;">{{ $control->observaciones }}</div>
        </div>
    </div>
    @endif

    {{-- Indicaciones --}}
    @if($control->indicaciones_paciente)
    <div class="col-md-6">
        <div class="card-sistema">
            <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.75rem;letter-spacing:.04em;">
                <i class="bi bi-person-check me-1"></i> Indicaciones para el Paciente
            </h6>
            <div style="font-size:.83rem;line-height:1.6;background:var(--fondo-card-alt);border-radius:8px;padding:.75rem;white-space:pre-wrap;">{{ $control->indicaciones_paciente }}</div>
        </div>
    </div>
    @endif

    {{-- Sondaje de control --}}
    @if($control->sondaje_control && count($control->sondaje_control))
    <div class="col-12">
        <div class="card-sistema">
            <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.75rem;letter-spacing:.04em;">
                <i class="bi bi-table me-1"></i> Sondaje de Control
            </h6>
            @php
                $sc = $control->sondaje_control;
                function scClass($v) {
                    if (!is_numeric($v) || $v == 0) return '';
                    $v = (int)$v;
                    if ($v <= 3) return 'background:#dcfce7;color:#166534;';
                    if ($v <= 5) return 'background:#fef9c3;color:#854d0e;';
                    if ($v <= 7) return 'background:#ffedd5;color:#9a3412;';
                    return 'background:#fee2e2;color:#7f1d1d;';
                }
                $dSup2 = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
                $dInf2 = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];
            @endphp
            <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);margin-bottom:.3rem;">Superior</p>
            <div style="overflow-x:auto;margin-bottom:.75rem;">
            <table style="border-collapse:collapse;font-size:.7rem;width:100%;">
                <thead><tr>
                    <th style="padding:.2rem .4rem;text-align:left;min-width:50px;background:var(--fondo-card-alt);font-size:.62rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Cara</th>
                    @foreach($dSup2 as $d)<th style="padding:.2rem .3rem;text-align:center;background:var(--fondo-card-alt);font-size:.62rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">{{ $d }}</th>@endforeach
                </tr></thead>
                <tbody>
                @foreach([['MV','mvc'],['V','vc'],['DV','dvc'],['ML','mlc'],['L','lc'],['DL','dlc']] as $f)
                <tr style="border-bottom:1px solid var(--fondo-borde);">
                    <td style="padding:.2rem .4rem;font-weight:700;font-size:.62rem;color:var(--texto-secundario);">{{ $f[0] }}</td>
                    @foreach($dSup2 as $d)
                    @php $v = $sc[$d][$f[1]] ?? ''; @endphp
                    <td style="padding:.15rem;text-align:center;">
                        @if($v !== '' && $v !== null)
                        <span style="display:inline-block;min-width:26px;border-radius:3px;font-weight:700;padding:.1rem;{{ scClass($v) }}">{{ $v }}</span>
                        @else <span style="color:#d1d5db;font-size:.65rem;">—</span> @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
                </tbody>
            </table>
            </div>
            <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);margin-bottom:.3rem;">Inferior</p>
            <div style="overflow-x:auto;">
            <table style="border-collapse:collapse;font-size:.7rem;width:100%;">
                <thead><tr>
                    <th style="padding:.2rem .4rem;text-align:left;min-width:50px;background:var(--fondo-card-alt);font-size:.62rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Cara</th>
                    @foreach($dInf2 as $d)<th style="padding:.2rem .3rem;text-align:center;background:var(--fondo-card-alt);font-size:.62rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">{{ $d }}</th>@endforeach
                </tr></thead>
                <tbody>
                @foreach([['MV','mvc'],['V','vc'],['DV','dvc'],['ML','mlc'],['L','lc'],['DL','dlc']] as $f)
                <tr style="border-bottom:1px solid var(--fondo-borde);">
                    <td style="padding:.2rem .4rem;font-weight:700;font-size:.62rem;color:var(--texto-secundario);">{{ $f[0] }}</td>
                    @foreach($dInf2 as $d)
                    @php $v = $sc[$d][$f[1]] ?? ''; @endphp
                    <td style="padding:.15rem;text-align:center;">
                        @if($v !== '' && $v !== null)
                        <span style="display:inline-block;min-width:26px;border-radius:3px;font-weight:700;padding:.1rem;{{ scClass($v) }}">{{ $v }}</span>
                        @else <span style="color:#d1d5db;font-size:.65rem;">—</span> @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection
