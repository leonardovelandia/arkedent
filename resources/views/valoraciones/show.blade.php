@extends('layouts.app')
@section('titulo', $valoracion->numero_valoracion . ' — Valoración')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-verde { background:linear-gradient(135deg,#16a34a,#15803d); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; }
    .btn-verde:hover { filter:brightness(1.1); }
    .btn-azul { background:linear-gradient(135deg,#1d4ed8,#1e40af); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; }
    .btn-out { background:transparent; color:#fff; border:1px solid rgba(255,255,255,.3); border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-out:hover { background:rgba(255,255,255,.12); color:#fff; }

    .val-header { background:linear-gradient(135deg,var(--color-principal) 0%,var(--color-sidebar-2) 60%,var(--color-sidebar) 100%); border-radius:14px; padding:1.5rem 1.75rem; color:#fff; margin-bottom:1.5rem; }
    .doc-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:14px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .doc-section { border-bottom:1px solid var(--fondo-borde); }
    .doc-section:last-child { border-bottom:none; }
    .doc-section-header { padding:.65rem 1.25rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); display:flex; align-items:center; gap:.5rem; background:var(--color-muy-claro); border-bottom:1px solid var(--color-muy-claro); }
    .doc-section-body { padding:1.1rem 1.25rem; }

    .campo-lbl { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#9ca3af; margin-bottom:.2rem; }
    .campo-val { font-size:.9rem; color:#1c2b22; font-weight:500; white-space:pre-line; }
    .campo-vacio { color:#d1d5db; font-style:italic; }

    .tabla-val { width:100%; border-collapse:collapse; font-size:.82rem; }
    .tabla-val thead th { background:var(--color-muy-claro); color:var(--color-hover); font-size:.7rem; font-weight:700; text-transform:uppercase; padding:.45rem .75rem; border-bottom:2px solid var(--color-muy-claro); }
    .tabla-val tbody td { padding:.45rem .75rem; border-bottom:1px solid var(--fondo-borde); }
    .tabla-val tbody tr:last-child td { border-bottom:none; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif
@if(session('error'))
<div class="alerta-flash" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;">
    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
</div>
@endif
@if(session('aviso'))
<div class="alerta-flash" style="background:#fef9c3;color:#854d0e;border:1px solid #fde68a;">
    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('aviso') }}
</div>
@endif

@php $ec = $valoracion->estado_color; @endphp

{{-- Header --}}
<div class="val-header">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.65;margin-bottom:.25rem;">
                <i class="bi bi-clipboard2-pulse me-1"></i> Valoración
            </div>
            <div style="font-family:var(--fuente-titulos);font-size:1.35rem;font-weight:600;margin-bottom:.3rem;">
                {{ $valoracion->paciente->nombre_completo }}
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:.75rem;font-size:.82rem;opacity:.85;align-items:center;">
                <span style="font-family:monospace;background:rgba(255,255,255,.2);border-radius:6px;padding:.1rem .55rem;font-weight:700;">{{ $valoracion->numero_valoracion }}</span>
                <span><i class="bi bi-calendar3 me-1"></i>{{ $valoracion->fecha->format('d/m/Y') }}</span>
                <span><i class="bi bi-person-circle me-1"></i>{{ $valoracion->doctor?->name ?? '—' }}</span>
                <span style="background:{{ $ec['bg'] }};color:{{ $ec['text'] }};border-radius:20px;padding:.18rem .6rem;font-size:.7rem;font-weight:700;">{{ $ec['label'] }}</span>
            </div>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:flex-start;">
            @if($valoracion->estado === 'en_proceso')
            <a href="{{ route('valoraciones.edit', $valoracion) }}" class="btn-out">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <form method="POST" action="{{ route('valoraciones.completar', $valoracion) }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn-verde" onclick="return confirm('¿Marcar como completada?');">
                    <i class="bi bi-check-circle"></i> Completar
                </button>
            </form>
            @endif
            @if(!$valoracion->presupuesto_id && !empty($valoracion->plan_tratamiento))
            <form method="POST" action="{{ route('valoraciones.generar-presupuesto', $valoracion) }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn-verde" onclick="return confirm('¿Generar presupuesto desde el plan de tratamiento?');">
                    <i class="bi bi-file-earmark-plus"></i> Generar Presupuesto
                </button>
            </form>
            @elseif($valoracion->presupuesto_id)
            <a href="{{ route('presupuestos.show', $valoracion->presupuesto_id) }}" class="btn-azul">
                <i class="bi bi-file-earmark-text"></i> Ver Presupuesto
            </a>
            @endif
            <a href="{{ route('pacientes.show', $valoracion->paciente) }}" class="btn-out">
                <i class="bi bi-arrow-left"></i> Paciente
            </a>
        </div>
    </div>
</div>

<div class="doc-card">

{{-- ═══ SECCIÓN 1: DATOS GENERALES ═══ --}}
<div class="doc-section">
    <div class="doc-section-header"><i class="bi bi-info-circle"></i> Datos Generales</div>
    <div class="doc-section-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="campo-lbl">Paciente</div>
                <div class="campo-val">
                    <a href="{{ route('pacientes.show', $valoracion->paciente) }}" style="color:var(--color-principal);text-decoration:none;">{{ $valoracion->paciente->nombre_completo }}</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="campo-lbl">Historia Clínica</div>
                <div class="campo-val">{{ $valoracion->historiaClinica?->numero_historia ?? '—' }}</div>
            </div>
            <div class="col-md-2">
                <div class="campo-lbl">Fecha</div>
                <div class="campo-val">{{ $valoracion->fecha->format('d/m/Y') }}</div>
            </div>
            <div class="col-md-3">
                <div class="campo-lbl">Doctor</div>
                <div class="campo-val">{{ $valoracion->doctor?->name ?? '—' }}</div>
            </div>
            @if($valoracion->cita)
            <div class="col-md-4">
                <div class="campo-lbl">Cita asociada</div>
                <div class="campo-val">
                    <a href="{{ route('citas.show', $valoracion->cita) }}" style="color:var(--color-principal);text-decoration:none;">
                        {{ $valoracion->cita->fecha->format('d/m/Y') }} — {{ $valoracion->cita->procedimiento }}
                    </a>
                </div>
            </div>
            @endif
            <div class="col-12">
                <div class="campo-lbl">Motivo de consulta</div>
                <div class="campo-val">{{ $valoracion->motivo_consulta }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ SECCIÓN 2: EXAMEN EXTRAORAL ═══ --}}
@if($valoracion->extraoral_cara || $valoracion->extraoral_atm || $valoracion->extraoral_ganglios || $valoracion->extraoral_labios || $valoracion->extraoral_observaciones)
<div class="doc-section">
    <div class="doc-section-header" style="background:var(--fondo-card-alt);"><i class="bi bi-person-bounding-box"></i> Examen Extraoral</div>
    <div class="doc-section-body" style="background:var(--fondo-card-alt);">
        <div class="row g-3">
            @if($valoracion->extraoral_cara)
            <div class="col-md-6">
                <div class="campo-lbl">Cara — Simetría y proporciones</div>
                <div class="campo-val">{{ $valoracion->extraoral_cara }}</div>
            </div>
            @endif
            @if($valoracion->extraoral_atm)
            <div class="col-md-6">
                <div class="campo-lbl">ATM</div>
                <div class="campo-val">{{ $valoracion->extraoral_atm }}</div>
            </div>
            @endif
            @if($valoracion->extraoral_ganglios)
            <div class="col-md-6">
                <div class="campo-lbl">Ganglios linfáticos</div>
                <div class="campo-val">{{ $valoracion->extraoral_ganglios }}</div>
            </div>
            @endif
            @if($valoracion->extraoral_labios)
            <div class="col-md-6">
                <div class="campo-lbl">Labios y comisuras</div>
                <div class="campo-val">{{ $valoracion->extraoral_labios }}</div>
            </div>
            @endif
            @if($valoracion->extraoral_observaciones)
            <div class="col-12">
                <div class="campo-lbl">Observaciones extraorales</div>
                <div class="campo-val">{{ $valoracion->extraoral_observaciones }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- ═══ SECCIÓN 3: EXAMEN INTRAORAL ═══ --}}
@if($valoracion->intraoral_encias || $valoracion->intraoral_mucosa || $valoracion->intraoral_lengua || $valoracion->intraoral_paladar || $valoracion->intraoral_higiene || $valoracion->intraoral_observaciones)
<div class="doc-section">
    <div class="doc-section-header" style="background:#f0f7ff;"><i class="bi bi-camera"></i> Examen Intraoral</div>
    <div class="doc-section-body" style="background:#f0f7ff;">
        <div class="row g-3">
            @if($valoracion->intraoral_higiene)
            <div class="col-12">
                @php
                $hColors = ['excelente'=>['#d1fae5','#166534'],'buena'=>['#dbeafe','#1d4ed8'],'regular'=>['#fef9c3','#854d0e'],'mala'=>['#fee2e2','#991b1b']];
                $hc = $hColors[$valoracion->intraoral_higiene] ?? ['#f3f4f6','#374151'];
                @endphp
                <div class="campo-lbl">Higiene oral</div>
                <span style="background:{{ $hc[0] }};color:{{ $hc[1] }};border-radius:20px;padding:.2rem .75rem;font-size:.8rem;font-weight:700;">
                    {{ ucfirst($valoracion->intraoral_higiene) }}
                </span>
            </div>
            @endif
            @if($valoracion->intraoral_encias)
            <div class="col-md-6">
                <div class="campo-lbl">Encías y periodonto</div>
                <div class="campo-val">{{ $valoracion->intraoral_encias }}</div>
            </div>
            @endif
            @if($valoracion->intraoral_mucosa)
            <div class="col-md-6">
                <div class="campo-lbl">Mucosa oral</div>
                <div class="campo-val">{{ $valoracion->intraoral_mucosa }}</div>
            </div>
            @endif
            @if($valoracion->intraoral_lengua)
            <div class="col-md-6">
                <div class="campo-lbl">Lengua y piso de boca</div>
                <div class="campo-val">{{ $valoracion->intraoral_lengua }}</div>
            </div>
            @endif
            @if($valoracion->intraoral_paladar)
            <div class="col-md-6">
                <div class="campo-lbl">Paladar</div>
                <div class="campo-val">{{ $valoracion->intraoral_paladar }}</div>
            </div>
            @endif
            @if($valoracion->intraoral_observaciones)
            <div class="col-12">
                <div class="campo-lbl">Observaciones intraorales</div>
                <div class="campo-val">{{ $valoracion->intraoral_observaciones }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- ═══ SECCIÓN 4: DIAGNÓSTICOS ═══ --}}
<div class="doc-section">
    <div class="doc-section-header" style="background:#fffdf0;"><i class="bi bi-search"></i> Diagnósticos</div>
    <div class="doc-section-body" style="background:#fffdf0;">
        @if(empty($valoracion->diagnosticos))
        <p style="color:#9ca3af;font-style:italic;font-size:.875rem;">Sin diagnósticos registrados.</p>
        @else
        <div style="overflow-x:auto;">
        <table class="tabla-val">
            <thead>
                <tr>
                    <th>Código CIE-10</th>
                    <th>Diagnóstico</th>
                    <th>Diente</th>
                    <th>Cara</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
            @foreach($valoracion->diagnosticos as $dx)
            <tr>
                <td><span style="font-family:monospace;font-weight:700;color:var(--color-principal);">{{ $dx['codigo'] ?? '—' }}</span></td>
                <td style="font-weight:500;">{{ $dx['nombre'] ?? '—' }}</td>
                <td>{{ $dx['diente'] ?? '—' }}</td>
                <td>{{ $dx['cara'] ?? '—' }}</td>
                <td style="color:#6b7280;">{{ $dx['observacion'] ?? '' }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>
</div>

{{-- ═══ SECCIÓN 5: PLAN DE TRATAMIENTO ═══ --}}
<div class="doc-section">
    <div class="doc-section-header" style="background:#f0fff4;"><i class="bi bi-list-check"></i> Plan de Tratamiento</div>
    <div class="doc-section-body" style="background:#f0fff4;">
        @if(empty($valoracion->plan_tratamiento))
        <p style="color:#9ca3af;font-style:italic;font-size:.875rem;">Sin plan de tratamiento registrado.</p>
        @else
        @php
        $totalPlan = array_sum(array_map(fn($p) => ($p['valor_unitario']??0) * ($p['cantidad']??1), $valoracion->plan_tratamiento));
        $prioColors = ['Alta'=>['#fee2e2','#991b1b'],'Media'=>['#fef9c3','#854d0e'],'Baja'=>['#d1fae5','#166534']];
        @endphp
        <div style="overflow-x:auto;">
        <table class="tabla-val">
            <thead>
                <tr>
                    <th style="width:35px;">N°</th>
                    <th>Procedimiento</th>
                    <th>Diente</th>
                    <th>Cara</th>
                    <th style="text-align:center;">Cant.</th>
                    <th style="text-align:right;">V. Unit.</th>
                    <th style="text-align:right;">Total</th>
                    <th>Prioridad</th>
                </tr>
            </thead>
            <tbody>
            @foreach($valoracion->plan_tratamiento as $i => $proc)
            @php $pc = $prioColors[$proc['prioridad']??''] ?? ['#f3f4f6','#374151']; @endphp
            <tr>
                <td style="color:#9ca3af;font-size:.75rem;text-align:center;">{{ $i+1 }}</td>
                <td style="font-weight:600;">{{ $proc['procedimiento'] ?? '—' }}</td>
                <td>{{ $proc['diente'] ?? '—' }}</td>
                <td>{{ $proc['cara'] ?? '—' }}</td>
                <td style="text-align:center;">{{ $proc['cantidad'] ?? 1 }}</td>
                <td style="text-align:right;white-space:nowrap;">$ {{ number_format($proc['valor_unitario']??0, 0, ',', '.') }}</td>
                <td style="text-align:right;font-weight:700;color:#166534;white-space:nowrap;">$ {{ number_format(($proc['valor_unitario']??0)*($proc['cantidad']??1), 0, ',', '.') }}</td>
                <td>
                    @if(!empty($proc['prioridad']))
                    <span style="background:{{ $pc[0] }};color:{{ $pc[1] }};border-radius:20px;padding:.12rem .5rem;font-size:.7rem;font-weight:700;">{{ $proc['prioridad'] }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#dcfce7;">
                    <td colspan="6" style="padding:.55rem .75rem;font-weight:700;color:#166534;font-size:.82rem;text-align:right;">TOTAL PLAN DE TRATAMIENTO</td>
                    <td style="padding:.55rem .75rem;font-weight:800;color:#166534;font-size:1rem;text-align:right;white-space:nowrap;">$ {{ number_format($totalPlan, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        </div>

        {{-- Card presupuesto generado --}}
        @if($valoracion->presupuesto)
        <div style="background:#d4edda;border:1px solid #28a745;border-radius:8px;padding:.875rem 1.25rem;margin-top:1rem;">
            <i class="bi bi-check-circle-fill" style="color:#155724;"></i>
            <strong style="color:#155724;">Presupuesto generado:</strong>
            <a href="{{ route('presupuestos.show', $valoracion->presupuesto) }}" style="color:#155724;">
                {{ $valoracion->presupuesto->numero_formateado }} —
                $ {{ number_format($valoracion->presupuesto->total, 0, ',', '.') }}
            </a>
        </div>
        @endif
        @endif
    </div>
</div>

{{-- ═══ SECCIÓN 6: PRONÓSTICO ═══ --}}
@if($valoracion->pronostico || $valoracion->observaciones_generales)
<div class="doc-section">
    <div class="doc-section-header"><i class="bi bi-clipboard2-check"></i> Pronóstico y Observaciones</div>
    <div class="doc-section-body">
        <div class="row g-3">
            @if($valoracion->pronostico)
            <div class="col-md-4">
                @php $pc = $valoracion->pronostico_color; @endphp
                <div class="campo-lbl">Pronóstico</div>
                <span style="background:{{ $pc['bg'] }};color:{{ $pc['text'] }};border-radius:20px;padding:.2rem .75rem;font-size:.82rem;font-weight:700;">
                    {{ $pc['label'] }}
                </span>
            </div>
            @endif
            @if($valoracion->observaciones_generales)
            <div class="col-12">
                <div class="campo-lbl">Observaciones generales</div>
                <div class="campo-val">{{ $valoracion->observaciones_generales }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

</div>{{-- end doc-card --}}

@endsection
