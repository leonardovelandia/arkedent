@extends('layouts.app')
@section('titulo', 'Pacientes')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    /* Estructura sin color */
    .form-card { border-radius:14px; padding:1.75rem; max-width:820px; margin:0 auto; }
    .form-card h5 { font-weight:700; font-size:1rem; margin-bottom:1.25rem; padding-bottom:.6rem; }
    .campo-wrap { margin-bottom:1.1rem; }
    .campo-lbl { font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--color-principal); display:block; margin-bottom:.3rem; }
    .campo-ctrl { width:100%; border-radius:8px; padding:.45rem .8rem; font-size:.9rem; outline:none; transition:border-color .15s; font-family:inherit; }
    .campo-ctrl.is-invalid { border-color:#dc2626; }
    .campo-error { font-size:.75rem; color:#dc2626; margin-top:.2rem; display:block; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media(max-width:540px) { .form-row { grid-template-columns:1fr; } }

    /* Badges tabla */
    .badge-pac-activo, .badge-pac-inactivo, .badge-aut-firmada, .badge-aut-pendiente, .badge-aut-sin {
        border-radius:20px; padding:.2rem .65rem; font-size:.74rem; font-weight:600; white-space:nowrap;
        display:inline-flex; align-items:center; gap:.25rem;
    }

    /* Clásico */
    body:not([data-ui="glass"]) .form-card { background:#fff; border:1px solid var(--color-muy-claro); box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    body:not([data-ui="glass"]) .form-card h5 { color:var(--color-hover); border-bottom:2px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .campo-ctrl { border:1.5px solid var(--color-muy-claro); color:#1c2b22; background:#fff; }
    body:not([data-ui="glass"]) .campo-ctrl:focus { border-color:var(--color-principal); }
    body:not([data-ui="glass"]) .plantilla-chip { background:var(--color-muy-claro); border:1px solid var(--color-muy-claro); color:var(--color-hover); }
    body:not([data-ui="glass"]) .pac-fila-nombre { color:#1c2b22; }
    body:not([data-ui="glass"]) .pac-fila-email  { color:#6b7280; }
    body:not([data-ui="glass"]) .pac-fila-doc-tipo { color:#6b7280; }
    body:not([data-ui="glass"]) .badge-pac-activo   { background:#dcfce7; color:#166534; }
    body:not([data-ui="glass"]) .badge-pac-inactivo { background:#fee2e2; color:#991b1b; }
    body:not([data-ui="glass"]) .badge-aut-firmada  { background:#d1fae5; color:#065f46; }
    body:not([data-ui="glass"]) .badge-aut-pendiente{ background:#fef3c7; color:#92400e; }
    body:not([data-ui="glass"]) .badge-aut-sin      { background:#f3f4f6; color:#6b7280; }

    /* Glass */
    body[data-ui="glass"] .form-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .form-card:hover { box-shadow:0 0 14px rgba(0,234,255,0.45) !important; }
    body[data-ui="glass"] .form-card h5 { color:rgba(0,234,255,0.90) !important; border-bottom:2px solid rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .campo-ctrl { border:1.5px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; background:rgba(255,255,255,0.08) !important; }
    body[data-ui="glass"] .campo-ctrl:focus { border-color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .campo-ctrl::placeholder { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .plantilla-chip { background:rgba(0,234,255,0.08) !important; border:1px solid rgba(0,234,255,0.30) !important; color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .pac-fila-nombre  { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .pac-fila-email   { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .pac-fila-doc-tipo{ color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .badge-pac-activo   { background:rgba(74,222,128,0.20) !important; color:#86efac !important; border:1px solid rgba(74,222,128,0.35) !important; }
    body[data-ui="glass"] .badge-pac-inactivo { background:rgba(248,113,113,0.20) !important; color:#fca5a5 !important; border:1px solid rgba(248,113,113,0.35) !important; }
    body[data-ui="glass"] .badge-aut-firmada  { background:rgba(74,222,128,0.20) !important; color:#86efac !important; border:1px solid rgba(74,222,128,0.35) !important; }
    body[data-ui="glass"] .badge-aut-pendiente{ background:rgba(251,191,36,0.20) !important; color:#fbbf24 !important; border:1px solid rgba(251,191,36,0.35) !important; }
    body[data-ui="glass"] .badge-aut-sin      { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.55) !important; border:1px solid rgba(255,255,255,0.15) !important; }
</style>
@endpush

@section('contenido')

{{-- Flash --}}
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

{{-- Encabezado --}}
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo">Pacientes</h1>
        <p class="page-subtitulo">Gestión del registro de pacientes del consultorio</p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
        <x-boton-exportar
            modulo="pacientes"
            ruta="{{ route('exportar.pacientes') }}"
            :tieneSensibles="true"
            labelSensibles="Incluir datos sensibles (dirección, fecha nacimiento, acudiente)"
            advertenciaSensibles="Incluye datos de contacto e identificación personal protegidos por la Ley 1581 de 2012."
        />
        <a href="{{ route('pacientes.create') }}" class="btn-morado">
            <i class="bi bi-person-plus-fill"></i> Nuevo Paciente
        </a>
    </div>
</div>

{{-- Tabla reutilizable --}}
<x-tabla-listado
    :paginacion="$pacientes"
    placeholder="Nombre, apellido, documento, teléfono..."
    icono-vacio="bi-people"
    mensaje-vacio="No se encontraron pacientes"
>

    {{-- Filtro de estado --}}
    <x-slot:filtros>
        <select name="estado" class="tbl-filtro-select">
            <option value="">Todos los estados</option>
            <option value="activo"   {{ request('estado') === 'activo'   ? 'selected' : '' }}>Activos</option>
            <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
        </select>
    </x-slot:filtros>

    {{-- Botón en estado vacío --}}
    <x-slot:accion-vacio>
        <div class="mt-3">
            <a href="{{ route('pacientes.create') }}" class="btn-morado">
                <i class="bi bi-person-plus-fill"></i> Registrar primer paciente
            </a>
        </div>
    </x-slot:accion-vacio>

    {{-- Cabecera de columnas --}}
    <x-slot:thead>
        <tr>
            <th>Paciente</th>
            <th>Documento</th>
            <th>Teléfono</th>
            <th>Edad</th>
            <th>Historia N°</th>
            <th>Estado</th>
            <th>Autorización</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

    {{-- Filas --}}
    @foreach($pacientes as $p)
    <tr>
        {{-- Paciente --}}
        <td>
            <div style="display:flex;align-items:center;gap:0.6rem;">
                @if($p->foto_path)
                    <img src="{{ $p->foto_url }}" alt="{{ $p->nombre_completo }}"
                         style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid var(--color-muy-claro);">
                @else
                    <span style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--color-principal),var(--color-claro));color:#fff;font-size:.75rem;font-weight:700;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">
                        {{ strtoupper(substr($p->nombre,0,1)) }}{{ strtoupper(substr($p->apellido,0,1)) }}
                    </span>
                @endif
                <div style="min-width:0;">
                    <div class="pac-fila-nombre" style="font-weight:600;white-space:nowrap;">{{ $p->nombre_completo }}</div>
                    <div class="pac-fila-email" style="font-size:.77rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;">{{ $p->email ?? '—' }}</div>
                </div>
            </div>
        </td>

        {{-- Documento --}}
        <td>
            <span class="pac-fila-doc-tipo" style="font-size:.75rem;font-weight:600;display:block;">{{ $p->tipo_documento }}</span>
            <span style="font-weight:500;">{{ $p->numero_documento }}</span>
        </td>

        {{-- Teléfono --}}
        <td style="white-space:nowrap;">{{ $p->telefono ?? '—' }}</td>

        {{-- Edad --}}
        <td style="white-space:nowrap;">{{ $p->edad }} años</td>

        {{-- Historia --}}
        <td>
            <span style="font-family:monospace;font-weight:700;color:var(--color-principal);">
                {{ $p->numero_historia }}
            </span>
        </td>

        {{-- Estado --}}
        <td>
            @if($p->activo)
                <span class="badge-pac-activo">
                    <i class="bi bi-circle-fill" style="font-size:.45rem;vertical-align:middle;"></i> Activo
                </span>
            @else
                <span class="badge-pac-inactivo">
                    <i class="bi bi-circle-fill" style="font-size:.45rem;vertical-align:middle;"></i> Inactivo
                </span>
            @endif
        </td>

        {{-- Autorización --}}
        <td>
            @php $aut = $p->autorizacionDatos; @endphp
            @if($aut && $aut->firmado)
                <span class="badge-aut-firmada">
                    <i class="bi bi-patch-check-fill"></i> Firmada
                </span>
            @elseif($aut)
                <span class="badge-aut-pendiente">
                    <i class="bi bi-pen"></i> Falta firmar
                </span>
            @else
                <span class="badge-aut-sin">
                    <i class="bi bi-file-earmark-x"></i> Sin autorización
                </span>
            @endif
        </td>

        {{-- Acciones --}}
        <td>
            <div style="display:flex;justify-content:center;gap:.3rem;">
                <a href="{{ route('pacientes.show', $p) }}" class="tbl-btn-accion" title="Ver ficha">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('pacientes.edit', $p) }}" class="tbl-btn-accion" title="Editar">
                    <i class="bi bi-pencil"></i>
                </a>

                @if($p->activo)
                    <form method="POST" action="{{ route('pacientes.destroy', $p) }}"
                          onsubmit="return confirm('¿Desactivar a {{ addslashes($p->nombre_completo) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="tbl-btn-accion danger" title="Desactivar">
                            <i class="bi bi-person-x"></i>
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('pacientes.activar', $p->id) }}"
                          onsubmit="return confirm('¿Activar a {{ addslashes($p->nombre_completo) }}?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="tbl-btn-accion success" title="Activar">
                            <i class="bi bi-person-check"></i>
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('pacientes.eliminar', $p->id) }}"
                      onsubmit="return confirm('⚠️ Eliminar permanentemente a {{ addslashes($p->nombre_completo) }}?\n\nEsta acción no se puede deshacer.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="tbl-btn-accion danger" title="Eliminar permanentemente">
                        <i class="bi bi-trash3"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

@endsection
