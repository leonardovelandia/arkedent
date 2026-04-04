@extends('layouts.app')
@section('titulo', 'Valoraciones')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);}
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-out { background:transparent; color:var(--color-principal); border:1px solid var(--color-principal); border-radius:8px; padding:.4rem .85rem; font-size:.82rem; font-weight:500; display:inline-flex; align-items:center; gap:.3rem; transition:background .15s; text-decoration:none; }
    .btn-out:hover { background:var(--color-muy-claro); }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.4rem .85rem; font-size:.82rem; font-weight:500; display:inline-flex; align-items:center; gap:.3rem; transition:background .15s; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; }
    .filtros-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:.9rem 1.25rem; margin-bottom:1.25rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .form-ctrl { border:1px solid #d1d5db; border-radius:8px; padding:.42rem .75rem; font-size:.875rem; color:#374151; background:#fff; width:100%; }
    .form-ctrl:focus { outline:none; border-color:var(--color-principal); }
    .tabla-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    #tabla-container { min-height:100px; transition:opacity .15s; }
    #tabla-container.cargando { opacity:.4; pointer-events:none; }

    /* Aurora Glass */
    body[data-ui="glass"] .filtros-card { background: rgba(255,255,255,0.08) !important; border-color: rgba(0,234,255,0.30) !important; box-shadow: 0 0 8px rgba(0,234,255,0.15) !important; }
    body[data-ui="glass"] .tabla-card { background: rgba(255,255,255,0.08) !important; border-color: rgba(0,234,255,0.30) !important; box-shadow: 0 0 8px rgba(0,234,255,0.15) !important; }
    body[data-ui="glass"] .form-ctrl { background: rgba(255,255,255,0.08) !important; border-color: rgba(0,234,255,0.30) !important; color: rgba(255,255,255,0.88) !important; }
    body[data-ui="glass"] .form-ctrl::placeholder { color: rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .btn-gris { background: rgba(255,255,255,0.08) !important; border-color: rgba(255,255,255,0.15) !important; color: rgba(255,255,255,0.75) !important; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo"><i class="bi bi-clipboard2-pulse me-2"></i>Valoraciones</h1>
        <p class="page-subtitulo">Evaluaciones diagnósticas iniciales</p>
    </div>
    <a href="{{ route('valoraciones.create') }}" class="btn-morado">
        <i class="bi bi-plus-lg"></i> Nueva Valoración
    </a>
</div>

<x-tabla-listado
    :paginacion="$valoraciones"
    placeholder="Nombre, apellido o documento..."
    icono-vacio="bi-clipboard2-pulse"
    mensaje-vacio="No hay valoraciones registradas"
>
    <x-slot:filtros>
        <select name="estado" class="tbl-filtro-select">
            <option value="">Todos los estados</option>
            <option value="en_proceso" {{ $estado == 'en_proceso' ? 'selected' : '' }}>En proceso</option>
            <option value="completada" {{ $estado == 'completada' ? 'selected' : '' }}>Completada</option>
            <option value="cancelada"  {{ $estado == 'cancelada'  ? 'selected' : '' }}>Cancelada</option>
        </select>
        <input type="date" name="desde" class="tbl-filtro-date" value="{{ $desde }}" title="Desde">
        <input type="date" name="hasta" class="tbl-filtro-date" value="{{ $hasta }}" title="Hasta">
    </x-slot:filtros>

    <x-slot:accion-vacio>
        <div class="mt-3">
            <a href="{{ route('valoraciones.create') }}" class="btn-morado">
                <i class="bi bi-plus-lg"></i> Crear primera valoración
            </a>
        </div>
    </x-slot:accion-vacio>

    <x-slot:thead>
        <tr>
            <th>N° / Fecha</th>
            <th>Paciente</th>
            <th>Motivo de consulta</th>
            <th style="text-align:center;">Dx</th>
            <th style="text-align:center;">Plan</th>
            <th>Estado</th>
            <th style="text-align:center;">Presupuesto</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

    @foreach($valoraciones as $val)
    @php $ec = $val->estado_color; @endphp
    <tr>
        <td style="white-space:nowrap;">
            <div style="font-family:monospace;font-weight:700;color:var(--color-principal);font-size:.8rem;">{{ $val->numero_valoracion }}</div>
            <div style="font-size:.75rem;color:#9ca3af;">{{ $val->fecha->format('d/m/Y') }}</div>
        </td>
        <td>
            <div style="font-weight:600;color:#1c2b22;">{{ $val->paciente->nombre_completo }}</div>
            <div style="font-size:.75rem;color:#9ca3af;">{{ $val->paciente->numero_historia }}</div>
        </td>
        <td style="color:#4b5563;max-width:220px;">{{ Str::limit($val->motivo_consulta, 65) }}</td>
        <td style="text-align:center;">
            @if(!empty($val->diagnosticos))
            <span style="background:var(--color-muy-claro);color:var(--color-principal);border-radius:20px;padding:.15rem .55rem;font-size:.72rem;font-weight:700;">{{ count($val->diagnosticos) }}</span>
            @else
            <span style="color:#d1d5db;">—</span>
            @endif
        </td>
        <td style="text-align:center;">
            @if(!empty($val->plan_tratamiento))
            <span style="background:#d1fae5;color:#166534;border-radius:20px;padding:.15rem .55rem;font-size:.72rem;font-weight:700;">{{ count($val->plan_tratamiento) }}</span>
            @else
            <span style="color:#d1d5db;">—</span>
            @endif
        </td>
        <td>
            <span style="display:inline-block;padding:.18rem .55rem;border-radius:20px;font-size:.7rem;font-weight:700;background:{{ $ec['bg'] }};color:{{ $ec['text'] }};">
                {{ $ec['label'] }}
            </span>
        </td>
        <td style="text-align:center;">
            @if($val->presupuesto_id)
            <a href="{{ route('presupuestos.show', $val->presupuesto_id) }}"
               style="background:#d1fae5;color:#166534;border-radius:20px;padding:.15rem .6rem;font-size:.7rem;font-weight:700;text-decoration:none;">
                <i class="bi bi-check-circle"></i> Ver
            </a>
            @elseif($val->estado === 'completada' && !empty($val->plan_tratamiento))
            <a href="{{ route('valoraciones.generar-presupuesto', $val) }}"
               onclick="return confirm('¿Generar presupuesto desde el plan de tratamiento?');"
               style="background:#fef9c3;color:#854d0e;border-radius:20px;padding:.15rem .6rem;font-size:.7rem;font-weight:700;text-decoration:none;">
                <i class="bi bi-plus-circle"></i> Generar
            </a>
            @else
            <span style="color:#d1d5db;font-size:.75rem;">—</span>
            @endif
        </td>
        <td>
            <div style="display:flex;justify-content:center;gap:.3rem;">
                <a href="{{ route('valoraciones.show', $val) }}" class="tbl-btn-accion" title="Ver detalle">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('valoraciones.pdf', $val) }}" class="tbl-btn-accion" target="_blank" title="PDF">
                    <i class="bi bi-file-earmark-pdf"></i>
                </a>
                @if($val->estado === 'en_proceso')
                <a href="{{ route('valoraciones.edit', $val) }}" class="tbl-btn-accion" title="Editar">
                    <i class="bi bi-pencil"></i>
                </a>
                @endif
                <form method="POST" action="{{ route('valoraciones.destroy', $val) }}" style="margin:0;">
                    @csrf @method('DELETE')
                    <button type="submit" class="tbl-btn-accion danger" title="Eliminar"
                            onclick="return confirm('¿Cancelar y eliminar esta valoración?');">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

@endsection
