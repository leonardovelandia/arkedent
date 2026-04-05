@extends('layouts.app')
@section('titulo', 'Historia Clínica')

@push('estilos')
<style>
    .btn-morado { background: linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);}
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-outline-morado { background:transparent; color:var(--color-principal); border:1px solid var(--color-principal); border-radius:8px; padding:.45rem 1rem; font-size:.82rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-outline-morado:hover { background:var(--color-muy-claro); color:var(--color-hover); }

    .tabla-hist { width:100%; border-collapse:separate; border-spacing:0; font-size:.875rem; }
    .tabla-hist thead th { background:var(--color-muy-claro); color:var(--color-hover); font-weight:700; font-size:.78rem; text-transform:uppercase; letter-spacing:.04em; padding:.7rem 1rem; border-bottom:2px solid var(--color-muy-claro); }
    .tabla-hist thead th:first-child { border-radius:8px 0 0 0; }
    .tabla-hist thead th:last-child  { border-radius:0 8px 0 0; }
    .tabla-hist tbody tr:hover { background:var(--fondo-card-alt); }
    .tabla-hist tbody td { padding:.75rem 1rem; border-bottom:1px solid var(--fondo-borde); vertical-align:middle; }

    .avatar-iniciales { width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; font-size:.75rem; font-weight:700; display:inline-flex; align-items:center; justify-content:center; }

    .btn-accion { background:none; border:1px solid var(--color-muy-claro); border-radius:7px; width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; color:var(--color-principal); font-size:.9rem; transition:background .13s; text-decoration:none; }
    .btn-accion:hover { background:var(--color-muy-claro); color:var(--color-hover); }

    .search-wrap { display:flex; gap:.75rem; align-items:flex-end; flex-wrap:wrap; }
    .search-field { display:flex; flex-direction:column; gap:.3rem; }
    .search-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--color-hover); }
    .search-input-wrap { position:relative; display:flex; align-items:center; }
    .search-input-wrap i { position:absolute; left:.75rem; color:#9ca3af; font-size:.9rem; pointer-events:none; }
    .search-input { border:1px solid var(--color-muy-claro); border-radius:8px; padding:.5rem .9rem .5rem 2.2rem; font-size:.875rem; outline:none; width:300px; transition:border-color .15s,box-shadow .15s; }
    .search-input:focus { border-color:var(--color-principal); box-shadow:0 0 0 3px var(--sombra-principal); }

    .vacio-msg { text-align:center; padding:3rem 1rem; color:#9ca3af; }
    .vacio-msg i { font-size:2.5rem; color:var(--color-acento-activo); display:block; margin-bottom:.75rem; }

    /* Glass */
    body[data-ui="glass"] .tabla-hist thead th { background:rgba(0,0,0,0.30) !important; color:rgba(255,255,255,0.90) !important; border-bottom:2px solid rgba(0,234,255,0.30) !important; }
    body[data-ui="glass"] .tabla-hist tbody td { color:rgba(255,255,255,0.88) !important; border-bottom:1px solid rgba(255,255,255,0.06) !important; }
    body[data-ui="glass"] .tabla-hist tbody tr:hover td { background:rgba(0,234,255,0.08) !important; }
    body[data-ui="glass"] .btn-accion { background:rgba(255,255,255,0.06) !important; border:1px solid rgba(0,234,255,0.25) !important; color:rgba(0,234,255,0.80) !important; }
    body[data-ui="glass"] .btn-accion:hover { background:rgba(0,234,255,0.12) !important; color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .btn-outline-morado { border-color:rgba(0,234,255,0.45) !important; color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .btn-outline-morado:hover { background:rgba(0,234,255,0.10) !important; }
    body[data-ui="glass"] .search-label { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .search-input { border:1px solid rgba(0,234,255,0.30) !important; background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .search-input:focus { border-color:rgba(0,234,255,0.70) !important; box-shadow:none !important; }
    body[data-ui="glass"] .search-input::placeholder { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .vacio-msg { color:rgba(255,255,255,0.30) !important; }

    /* Inline color helpers */
    .hist-pac-nombre { font-weight:600; }
    .hist-pac-doc    { font-size:.78rem; }
    .hist-motivo     { font-size:.84rem; }
    .hist-fecha      { white-space:nowrap; }
    .hist-num-badge  { font-family:monospace; font-weight:700; padding:.15rem .5rem; border-radius:6px; font-size:.82rem; }

    body:not([data-ui="glass"]) .hist-pac-nombre { color:#1c2b22; }
    body:not([data-ui="glass"]) .hist-pac-doc    { color:#6b7280; }
    body:not([data-ui="glass"]) .hist-motivo     { color:#4b5563; }

    body[data-ui="glass"] .hist-pac-nombre { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .hist-pac-doc    { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .hist-motivo     { color:rgba(255,255,255,0.88) !important; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
    <div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
        <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
    </div>
@endif
@if(session('info'))
    <div class="alerta-flash" style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;">
        <i class="bi bi-info-circle-fill"></i> {{ session('info') }}
    </div>
@endif

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo">Historia Clínica</h1>
        <p class="page-subtitulo">Registro de historias clínicas de pacientes</p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
        <x-boton-exportar
            modulo="historias_clinicas"
            ruta="{{ route('exportar.historias') }}"
            :tieneSensibles="true"
            labelSensibles="Incluir historia clínica completa (DATOS MÉDICOS SENSIBLES)"
            advertenciaSensibles="Incluye antecedentes médicos, diagnósticos, alergias y medicamentos. Información protegida por la Resolución 1995 de 1999."
        />
        <a href="{{ route('historias.create') }}" class="btn-morado">
            <i class="bi bi-journal-plus"></i> Nueva Historia
        </a>
    </div>
</div>

<x-tabla-listado
    :paginacion="$historias"
    placeholder="Nombre, apellido o documento..."
    icono-vacio="bi-journal-medical"
    mensaje-vacio="No se encontraron historias clínicas"
>
    <x-slot:accion-vacio>
        <div class="mt-3">
            <a href="{{ route('historias.create') }}" class="btn-morado">
                <i class="bi bi-journal-plus"></i> Crear primera historia
            </a>
        </div>
    </x-slot:accion-vacio>

    <x-slot:thead>
        <tr>
            <th>Paciente</th>
            <th>N° Historia</th>
            <th>Fecha Apertura</th>
            <th>Motivo de Consulta</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

    @foreach($historias as $h)
    <tr>
        <td>
            <div style="display:flex;align-items:center;gap:.65rem;">
                <span style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--color-principal),var(--color-claro));color:#fff;font-size:.75rem;font-weight:700;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">
                    {{ strtoupper(substr($h->paciente->nombre,0,1)) }}{{ strtoupper(substr($h->paciente->apellido,0,1)) }}
                </span>
                <div>
                    <div style="font-weight:600;color:#1c2b22;">{{ $h->paciente->nombre_completo }}</div>
                    <div style="font-size:.78rem;color:#6b7280;">{{ $h->paciente->numero_documento }}</div>
                </div>
            </div>
        </td>
        <td>
            <span style="font-family:monospace;font-weight:700;color:var(--color-principal);background:var(--color-muy-claro);padding:.15rem .5rem;border-radius:6px;font-size:.82rem;">
                {{ $h->numero_historia ?? ('#'.$h->id) }}
            </span>
        </td>
        <td style="white-space:nowrap;">{{ $h->fecha_apertura->format('d/m/Y') }}</td>
        <td style="max-width:250px;">
            <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;font-size:.84rem;color:#4b5563;">
                {{ $h->motivo_consulta }}
            </span>
        </td>
        <td>
            <div style="display:flex;justify-content:center;gap:.3rem;">
                <a href="{{ route('historias.show', $h) }}" class="tbl-btn-accion" title="Ver historia">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('historias.edit', $h) }}" class="tbl-btn-accion" title="Editar">
                    <i class="bi bi-pencil"></i>
                </a>
                <a href="{{ route('historias.pdf', $h) }}" class="tbl-btn-accion" title="Ver PDF" target="_blank">
                    <i class="bi bi-file-earmark-pdf"></i>
                </a>
            </div>
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

@endsection
