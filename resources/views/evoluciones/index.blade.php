@extends('layouts.app')
@section('titulo', 'Evoluciones')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-outline-morado { background:transparent; color:var(--color-principal); border:1px solid var(--color-principal); border-radius:8px; padding:.45rem 1rem; font-size:.82rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-outline-morado:hover { background:var(--color-muy-claro); color:var(--color-hover); }

    .tabla-evol { width:100%; border-collapse:separate; border-spacing:0; font-size:.875rem; }
    .tabla-evol thead th { background:var(--color-muy-claro); color:var(--color-hover); font-weight:700; font-size:.78rem; text-transform:uppercase; letter-spacing:.04em; padding:.7rem 1rem; border-bottom:2px solid var(--color-muy-claro); }
    .tabla-evol thead th:first-child { border-radius:8px 0 0 0; }
    .tabla-evol thead th:last-child  { border-radius:0 8px 0 0; }
    .tabla-evol tbody tr:hover { background:var(--fondo-card-alt); }
    .tabla-evol tbody td { padding:.75rem 1rem; border-bottom:1px solid var(--fondo-borde); vertical-align:middle; }

    .avatar-iniciales { width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; font-size:.75rem; font-weight:700; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; }

    .badge-dientes { background:var(--color-muy-claro); color:var(--color-hover); border-radius:20px; padding:.18rem .65rem; font-size:.72rem; font-weight:600; }

    .btn-accion { background:none; border:1px solid var(--color-muy-claro); border-radius:7px; width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; color:var(--color-principal); font-size:.9rem; transition:background .13s; text-decoration:none; }
    .btn-accion:hover { background:var(--color-muy-claro); color:var(--color-hover); }

    .search-wrap { display:flex; gap:.75rem; align-items:flex-end; flex-wrap:wrap; }
    .search-field { display:flex; flex-direction:column; gap:.3rem; }
    .search-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--color-hover); }
    .search-input-wrap { position:relative; display:flex; align-items:center; }
    .search-input-wrap i { position:absolute; left:.75rem; color:#9ca3af; font-size:.9rem; pointer-events:none; }
    .search-input { border:1px solid var(--color-muy-claro); border-radius:8px; padding:.5rem .9rem .5rem 2.2rem; font-size:.875rem; outline:none; width:300px; transition:border-color .15s,box-shadow .15s; }
    .search-input:focus { border-color:var(--color-principal); box-shadow:0 0 0 3px var(--sombra-principal); }

    .filtro-pac { background:var(--color-muy-claro); border:1px solid var(--color-muy-claro); border-radius:8px; padding:.4rem .85rem; font-size:.8rem; color:var(--color-hover); display:inline-flex; align-items:center; gap:.4rem; }

    .vacio-msg { text-align:center; padding:3rem 1rem; color:#9ca3af; }
    .vacio-msg i { font-size:2.5rem; color:var(--color-acento-activo); display:block; margin-bottom:.75rem; }
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
        <h1 class="page-titulo">Evoluciones</h1>
        <p class="page-subtitulo">Registro de evoluciones clínicas por sesión</p>
    </div>
    <a href="{{ route('evoluciones.create') }}" class="btn-morado">
        <i class="bi bi-clipboard2-plus"></i> Nueva Evolución
    </a>
</div>

@if($pacienteFiltro)
<div style="margin-bottom:.75rem;">
    <span style="background:var(--color-muy-claro);border:1px solid var(--color-muy-claro);border-radius:8px;padding:.4rem .85rem;font-size:.8rem;color:var(--color-hover);display:inline-flex;align-items:center;gap:.4rem;">
        <i class="bi bi-person-fill"></i>
        Mostrando evoluciones de: <strong>{{ $pacienteFiltro->nombre_completo }}</strong>
        <a href="{{ route('evoluciones.index') }}" style="color:var(--color-hover);margin-left:.25rem;"><i class="bi bi-x"></i></a>
    </span>
</div>
@endif

<x-tabla-listado
    :paginacion="$evoluciones"
    placeholder="Paciente o procedimiento..."
    icono-vacio="bi-clipboard2-pulse"
    mensaje-vacio="No se encontraron evoluciones"
>
    @if($pacienteFiltro)
    <x-slot:filtros>
        <input type="hidden" name="paciente_id" value="{{ $pacienteFiltro->id }}">
    </x-slot:filtros>
    @endif

    <x-slot:accion-vacio>
        <div class="mt-3">
            <a href="{{ route('evoluciones.create') }}" class="btn-morado">
                <i class="bi bi-clipboard2-plus"></i> Registrar primera evolución
            </a>
        </div>
    </x-slot:accion-vacio>

    <x-slot:thead>
        <tr>
            <th>Paciente</th>
            <th>N° EVO</th>
            <th>Procedimiento</th>
            <th>Dientes</th>
            <th>Fecha</th>
            <th>Doctor</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

    @foreach($evoluciones as $e)
    <tr>
        <td>
            <div style="display:flex;align-items:center;gap:.65rem;">
                <span style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--color-principal),var(--color-claro));color:#fff;font-size:.75rem;font-weight:700;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">
                    {{ strtoupper(substr($e->paciente->nombre,0,1)) }}{{ strtoupper(substr($e->paciente->apellido,0,1)) }}
                </span>
                <div>
                    <div style="font-weight:600;color:#1c2b22;">{{ $e->paciente->nombre_completo }}</div>
                    <div style="font-size:.78rem;color:#6b7280;">{{ $e->paciente->numero_documento }}</div>
                </div>
            </div>
        </td>
        <td>
            <span style="font-family:monospace;font-weight:700;color:#1d4ed8;background:#dbeafe;padding:.15rem .5rem;border-radius:6px;font-size:.82rem;">
                {{ $e->numero_evolucion ?? ('#'.$e->id) }}
            </span>
        </td>
        <td style="max-width:220px;">
            <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;font-size:.84rem;font-weight:500;color:#374151;">
                {{ $e->procedimiento }}
            </span>
        </td>
        <td>
            @if($e->dientes_tratados)
                <span style="background:var(--color-muy-claro);color:var(--color-hover);border-radius:20px;padding:.18rem .65rem;font-size:.72rem;font-weight:600;">
                    <i class="bi bi-tooth"></i> {{ $e->dientes_tratados }}
                </span>
            @else
                <span style="color:#d1d5db;">—</span>
            @endif
        </td>
        <td style="font-size:.84rem;color:#4b5563;white-space:nowrap;">
            {{ $e->fecha_formateada }}
            @if($e->hora)
                <div style="font-size:.72rem;color:#9ca3af;">{{ \Carbon\Carbon::parse($e->hora)->format('h:i A') }}</div>
            @endif
        </td>
        <td style="font-size:.84rem;color:#4b5563;">{{ $e->doctor ? $e->doctor->name : '—' }}</td>
        <td>
            <div style="display:flex;justify-content:center;gap:.3rem;">
                <a href="{{ route('evoluciones.show', $e) }}" class="tbl-btn-accion" title="Ver detalle">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('evoluciones.edit', $e) }}" class="tbl-btn-accion" title="Editar">
                    <i class="bi bi-pencil"></i>
                </a>
                <a href="{{ route('evoluciones.pdf', $e) }}" class="tbl-btn-accion" target="_blank" title="PDF">
                    <i class="bi bi-file-earmark-pdf"></i>
                </a>
            </div>
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

@endsection
