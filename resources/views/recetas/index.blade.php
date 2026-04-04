@extends('layouts.app')
@section('titulo', 'Recetas Médicas')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);}
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .form-input { border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; }
    .form-input:focus { border-color:var(--color-principal); }
    .form-select { border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; }
    .form-select:focus { border-color:var(--color-principal); }
    .tabla-header th { background:var(--color-muy-claro); padding:.55rem .9rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); border-bottom:2px solid var(--color-muy-claro); text-align:left; white-space:nowrap; }
    .tabla-fila td { padding:.6rem .9rem; border-bottom:1px solid var(--fondo-borde); font-size:.875rem; vertical-align:middle; }
    .tabla-fila:hover td { background:var(--fondo-card-alt); }
    .badge-estado { padding:.2rem .65rem; border-radius:20px; font-size:.7rem; font-weight:700; display:inline-flex; align-items:center; gap:.25rem; }
    .por-vencer { background:#fff7ed; border:1px solid #fed7aa; border-radius:6px; padding:.15rem .45rem; font-size:.7rem; font-weight:600; color:#c2410c; }
    #tabla-container { min-height:100px; transition:opacity .15s; }
    #tabla-container.cargando { opacity:.4; pointer-events:none; }

    /* Aurora Glass */
    body[data-ui="glass"] .form-input { background: rgba(255,255,255,0.08) !important; border-color: rgba(0,234,255,0.30) !important; color: rgba(255,255,255,0.88) !important; }
    body[data-ui="glass"] .form-select { background: rgba(255,255,255,0.08) !important; border-color: rgba(0,234,255,0.30) !important; color: rgba(255,255,255,0.88) !important; }
    body[data-ui="glass"] .form-input::placeholder { color: rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .form-select option { background: #052837; }
    /* Stat cards — inline color overrides */
    body[data-ui="glass"] .card-sistema div[style*="color:#1c2b22"] { color: rgba(255,255,255,0.95) !important; }
    body[data-ui="glass"] .card-sistema div[style*="color:#9ca3af"] { color: rgba(255,255,255,0.45) !important; }
    body[data-ui="glass"] .por-vencer { background: rgba(255,138,76,0.15) !important; border-color: rgba(255,138,76,0.35) !important; color: #fdba74 !important; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#dcfce7;color:#166534;border:1px solid #86efac;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo">Recetas Médicas</h1>
        <p class="page-subtitulo">Gestión de prescripciones médicas del consultorio</p>
    </div>
    <a href="{{ route('recetas.create') }}" class="btn-morado">
        <i class="bi bi-plus-lg"></i> Nueva Receta
    </a>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:1.5rem;">
    @foreach([
        ['Hoy', $totalHoy, 'bi-calendar-check', 'var(--color-principal)'],
        ['Este mes', $totalMes, 'bi-calendar-month', '#0ea5e9'],
        ['Total', $recetas->total(), 'bi-file-medical', '#8b5cf6'],
    ] as [$label, $val, $icon, $color])
    <div class="card-sistema" style="padding:.9rem 1rem;display:flex;align-items:center;gap:.9rem;">
        <div style="width:40px;height:40px;border-radius:10px;background:{{ $color }}1a;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi {{ $icon }}" style="font-size:1.1rem;color:{{ $color }};"></i>
        </div>
        <div>
            <div style="font-size:1.35rem;font-weight:700;color:#1c2b22;line-height:1.1;">{{ $val }}</div>
            <div style="font-size:.72rem;color:#9ca3af;">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

<x-tabla-listado
    :paginacion="$recetas"
    placeholder="Buscar receta o paciente..."
    icono-vacio="bi-file-medical"
    mensaje-vacio="No hay recetas registradas"
>
    <x-slot:filtros>
        <select name="doctor_id" class="tbl-filtro-select">
            <option value="">Todos los doctores</option>
            @foreach($doctores as $d)
            <option value="{{ $d->id }}" {{ request('doctor_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
            @endforeach
        </select>
        <input type="date" name="desde" class="tbl-filtro-date" value="{{ request('desde') }}" title="Desde">
        <input type="date" name="hasta" class="tbl-filtro-date" value="{{ request('hasta') }}" title="Hasta">
    </x-slot:filtros>

    <x-slot:thead>
        <tr>
            <th>N° Receta</th>
            <th>Paciente</th>
            <th>Doctor</th>
            <th>Fecha</th>
            <th>Medicamentos</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

    @foreach($recetas as $receta)
    <tr>
        <td>
            <a href="{{ route('recetas.show', $receta) }}" style="color:var(--color-principal);font-weight:600;text-decoration:none;">
                {{ $receta->numero_receta }}
            </a>
        </td>
        <td>
            <div style="font-weight:500;color:#1c2b22;">{{ $receta->paciente->nombre_completo }}</div>
            <div style="font-size:.72rem;color:#9ca3af;">{{ $receta->paciente->numero_historia }}</div>
        </td>
        <td style="color:#6b7280;font-size:.84rem;">{{ $receta->doctor->name }}</td>
        <td style="color:#6b7280;font-size:.84rem;white-space:nowrap;">{{ $receta->fecha->format('d/m/Y') }}</td>
        <td>
            <span style="background:var(--color-muy-claro);color:var(--color-principal);font-size:.72rem;font-weight:600;padding:.2rem .55rem;border-radius:20px;">
                {{ $receta->total_medicamentos }} ítem(s)
            </span>
        </td>
        <td>
            <div style="display:flex;justify-content:center;gap:.3rem;">
                <a href="{{ route('recetas.show', $receta) }}" class="tbl-btn-accion" title="Ver">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('recetas.edit', $receta) }}" class="tbl-btn-accion" title="Editar">
                    <i class="bi bi-pencil"></i>
                </a>
                <a href="{{ route('recetas.pdf', $receta) }}" class="tbl-btn-accion success" target="_blank" title="PDF">
                    <i class="bi bi-file-pdf"></i>
                </a>
                <form method="POST" action="{{ route('recetas.destroy', $receta) }}"
                      onsubmit="return confirm('¿Anular esta receta?')" style="margin:0;">
                    @csrf @method('DELETE')
                    <button type="submit" class="tbl-btn-accion danger" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

@endsection
