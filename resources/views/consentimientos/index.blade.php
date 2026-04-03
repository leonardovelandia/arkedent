@extends('layouts.app')
@section('titulo', 'Consentimientos Informados')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);}
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    .form-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:14px; padding:1.75rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); max-width:820px; margin:0 auto; }
    .form-card h5 { font-weight:700; color:var(--color-hover); font-size:1rem; margin-bottom:1.25rem; padding-bottom:.6rem; border-bottom:2px solid var(--color-muy-claro); }

    .campo-wrap { margin-bottom:1.1rem; }
    .campo-lbl { font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--color-principal); display:block; margin-bottom:.3rem; }
    .campo-ctrl { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .8rem; font-size:.9rem; color:#1c2b22; background:#fff; outline:none; transition:border-color .15s; font-family:inherit; }
    .campo-ctrl:focus { border-color:var(--color-principal); }
    .campo-ctrl.is-invalid { border-color:#dc2626; }
    .campo-error { font-size:.75rem; color:#dc2626; margin-top:.2rem; display:block; }

    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media(max-width:540px) { .form-row { grid-template-columns:1fr; } }

    .plantilla-chip { display:flex; align-items:center; gap:.5rem; padding:.35rem .75rem; border-radius:8px; background:var(--color-muy-claro); border:1px solid var(--color-muy-claro); font-size:.83rem; color:var(--color-hover); font-weight:600; margin-bottom:.5rem; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif
@if(session('error'))
<div class="alerta-flash" style="background:#fef2f2;color:#dc2626;border:1px solid #fecdd3;">
    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
</div>
@endif

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo"><i class="bi bi-file-earmark-check me-2"></i>Consentimientos Informados</h1>
        <p class="page-subtitulo">Gestión de consentimientos y firmas</p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
        <a href="{{ route('plantillas-consentimiento.index') }}"
           style="background:#fff;color:var(--color-principal);border:1.5px solid var(--color-principal);border-radius:8px;padding:.5rem 1.1rem;font-size:.875rem;font-weight:500;display:inline-flex;align-items:center;gap:.4rem;text-decoration:none;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
            <i class="bi bi-collection"></i> Plantillas
        </a>
        <a href="{{ route('consentimientos.create') }}" class="btn-morado">
            <i class="bi bi-plus-lg"></i> Nuevo Consentimiento
        </a>
    </div>
</div>

<x-tabla-listado
    :paginacion="$consentimientos"
    placeholder="Buscar paciente o consentimiento..."
    icono-vacio="bi-file-earmark-x"
    mensaje-vacio="No hay consentimientos registrados"
>
    <x-slot:filtros>
        <select name="estado" class="tbl-filtro-select">
            <option value="">Todos los estados</option>
            <option value="firmado"   {{ request('estado')==='firmado'   ? 'selected' : '' }}>Firmado</option>
            <option value="pendiente" {{ request('estado')==='pendiente' ? 'selected' : '' }}>Pendiente firma</option>
        </select>
    </x-slot:filtros>

    <x-slot:accion-vacio>
        <div class="mt-3">
            <a href="{{ route('consentimientos.create') }}" class="btn-morado">
                <i class="bi bi-plus-circle"></i> Crear primer consentimiento
            </a>
        </div>
    </x-slot:accion-vacio>

    <x-slot:thead>
        <tr>
            <th>Paciente</th>
            <th>N° CON</th>
            <th>Consentimiento</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Doctor</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

    @foreach($consentimientos as $c)
    <tr>
        <td>
            <div style="font-weight:600;color:#1c2b22;">{{ $c->paciente->nombre_completo }}</div>
            <div style="font-size:.72rem;color:#9ca3af;">{{ $c->paciente->numero_documento }}</div>
        </td>
        <td>
            <span style="font-family:monospace;font-weight:700;color:#c2410c;background:#ffedd5;padding:.15rem .5rem;border-radius:6px;font-size:.82rem;">
                {{ $c->numero_consentimiento ?? ('#'.$c->id) }}
            </span>
        </td>
        <td style="max-width:220px;">
            <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-weight:500;color:#1c2b22;" title="{{ $c->nombre }}">
                {{ $c->nombre }}
            </span>
        </td>
        <td style="white-space:nowrap;color:#4b5563;font-size:.83rem;">
            {{ $c->fecha_generacion->translatedFormat('d M Y') }}
        </td>
        <td>
            @if($c->firmado)
            <span style="background:#d4edda;color:#155724;border-radius:20px;padding:.12rem .65rem;font-size:.7rem;font-weight:700;display:inline-flex;align-items:center;gap:.3rem;">
                <i class="bi bi-patch-check-fill"></i> Firmado
            </span>
            @else
            <span style="background:#fff3cd;color:#856404;border-radius:20px;padding:.12rem .65rem;font-size:.7rem;font-weight:700;display:inline-flex;align-items:center;gap:.3rem;">
                <i class="bi bi-clock"></i> Pendiente
            </span>
            @endif
        </td>
        <td style="font-size:.82rem;color:#6b7280;">{{ $c->doctor?->name ?? '—' }}</td>
        <td>
            <div style="display:flex;justify-content:center;gap:.3rem;">
                <a href="{{ route('consentimientos.show', $c) }}" class="tbl-btn-accion" title="Ver / Firmar">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('consentimientos.pdf', $c) }}" class="tbl-btn-accion warn" title="PDF" target="_blank">
                    <i class="bi bi-file-pdf"></i>
                </a>
                @if(!$c->firmado)
                <form method="POST" action="{{ route('consentimientos.destroy', $c) }}" style="margin:0;"
                      onsubmit="return confirm('¿Eliminar este consentimiento?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="tbl-btn-accion danger" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
                @endif
            </div>
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

@endsection
