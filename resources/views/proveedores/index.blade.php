@extends('layouts.app')
@section('titulo', 'Proveedores')

@push('estilos')
<style>
    .prov-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; flex-wrap:wrap; gap:.75rem; }
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; transition:filter .18s; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-azul { background:#1e40af; color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; }
    .btn-azul:hover { filter:brightness(1.1); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; color:#374151; }

    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:.875rem; margin-bottom:1.25rem; }
    @media(max-width:900px){ .stats-grid{ grid-template-columns:repeat(2,1fr); } }
    .stat-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1rem 1.1rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .stat-valor { font-family:var(--fuente-titulos); font-size:1.5rem; font-weight:600; color:var(--color-principal); }
    .stat-label { font-size:.7rem; font-weight:600; color:#8fa39a; text-transform:uppercase; letter-spacing:.05em; }

    .filtros-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:1rem 1.2rem; margin-bottom:1.1rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .filtros-grid { display:grid; grid-template-columns:2fr 1fr auto; gap:.75rem; align-items:end; }
    @media(max-width:700px){ .filtros-grid{ grid-template-columns:1fr; } }
    .form-label { font-size:.76rem; font-weight:700; color:var(--color-hover); display:block; margin-bottom:.2rem; }
    .form-input { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.42rem .75rem; font-size:.84rem; color:#1c2b22; background:#fff; outline:none; }
    .form-input:focus { border-color:var(--color-principal); }

    .panel-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .panel-header { padding:.8rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
    .panel-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:600; color:var(--color-hover); display:flex; align-items:center; gap:.4rem; }
    .panel-titulo i { color:var(--color-principal); }
    .tabla-prov { width:100%; border-collapse:collapse; font-size:.82rem; }
    .tabla-prov th { font-size:.69rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-principal); padding:.5rem .75rem; border-bottom:2px solid var(--color-muy-claro); text-align:left; white-space:nowrap; }
    .tabla-prov td { padding:.5rem .75rem; border-bottom:1px solid var(--fondo-borde); color:#374151; vertical-align:middle; }
    .tabla-prov tr:last-child td { border-bottom:none; }
    .tabla-prov tr:hover td { background:var(--fondo-card-alt); }
    .acc-btn { display:inline-flex; align-items:center; gap:.2rem; padding:.22rem .55rem; border-radius:6px; font-size:.74rem; font-weight:500; text-decoration:none; border:none; cursor:pointer; }
    .acc-ver { background:var(--color-muy-claro); color:var(--color-principal); }
    .acc-edit { background:#f3f4f6; color:#374151; }
    .badge-cat { display:inline-block; font-size:.68rem; font-weight:600; padding:.1rem .45rem; border-radius:50px; background:var(--color-badge-bg); color:var(--color-badge-texto); margin:.1rem; }
    .pagination-wrapper { padding:.75rem 1.25rem; border-top:1px solid var(--fondo-borde); }
    #tabla-container { min-height:100px; transition:opacity .15s; }
    #tabla-container.cargando { opacity:.4; pointer-events:none; }
</style>
@endpush

@section('contenido')

@if(session('exito') || session('success'))
<div class="alerta-flash" style="background:#dcfce7;color:#166534;border:1px solid #86efac;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') ?? session('success') }}
</div>
@endif

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo"><i class="bi bi-truck me-2"></i>Proveedores</h1>
        <p class="page-subtitulo">Gestión de proveedores de materiales e insumos</p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
        <a href="{{ route('compras.index') }}"
           style="background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem 1.1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
            <i class="bi bi-cart"></i> Historial Compras
        </a>
        <a href="{{ route('proveedores.comparar') }}"
           style="background:#1e40af;color:#fff;border:none;border-radius:8px;padding:.5rem 1.1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.4rem;text-decoration:none;">
            <i class="bi bi-bar-chart"></i> Comparar Precios
        </a>
        <a href="{{ route('proveedores.create') }}" class="btn-morado">
            <i class="bi bi-plus-lg"></i> Nuevo Proveedor
        </a>
    </div>
</div>

{{-- Cards resumen --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:var(--color-principal);">{{ $totalActivos }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Proveedores activos</div>
            <i class="bi bi-truck" style="font-size:1.2rem;color:var(--color-principal);opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.5rem;font-weight:800;color:#0ea5e9;">${{ number_format($comprasMes, 0, ',', '.') }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Compras este mes</div>
            <i class="bi bi-calendar-month" style="font-size:1.2rem;color:#0ea5e9;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1rem;font-weight:700;color:#1c2b22;margin-top:.3rem;">{{ $proveedorFrecuente?->proveedor?->nombre ?? '—' }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Más frecuente (mes)</div>
            <i class="bi bi-star" style="font-size:1.2rem;color:#f59e0b;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;{{ $comprasPendientes > 0 ? 'border-color:#fde68a;' : '' }}">
            <div style="font-size:1.5rem;font-weight:800;color:{{ $comprasPendientes > 0 ? '#b45309' : 'var(--color-principal)' }};">${{ number_format($comprasPendientes, 0, ',', '.') }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Compras pendientes pago</div>
            <i class="bi bi-clock" style="font-size:1.2rem;color:#f59e0b;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
</div>

<x-tabla-listado
    :paginacion="$proveedores"
    placeholder="Buscar nombre, NIT o contacto..."
    icono-vacio="bi-truck"
    mensaje-vacio="No hay proveedores registrados"
>
    <x-slot:filtros>
        <select name="categoria" class="tbl-filtro-select">
            <option value="">Todas las categorías</option>
            @foreach($categorias as $key => $label)
            <option value="{{ $key }}" {{ request('categoria') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </x-slot:filtros>

    <x-slot:accion-vacio>
        <div class="mt-3">
            <a href="{{ route('proveedores.create') }}" class="btn-morado">
                <i class="bi bi-plus-circle"></i> Registrar primer proveedor
            </a>
        </div>
    </x-slot:accion-vacio>

    <x-slot:thead>
        <tr>
            <th>Nombre</th>
            <th>NIT</th>
            <th>Ciudad</th>
            <th>Contacto</th>
            <th>Teléfono</th>
            <th>Categorías</th>
            <th>Calificación</th>
            <th style="text-align:right;">Total Compras</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

    @foreach($proveedores as $proveedor)
    <tr>
        <td>
            <div style="font-weight:600;color:#1c2b22;">{{ $proveedor->nombre }}</div>
            @if($proveedor->condiciones_pago)
            <div style="font-size:.72rem;color:#9ca3af;">{{ $proveedor->condiciones_pago }}</div>
            @endif
        </td>
        <td style="font-size:.8rem;color:#6b7280;">{{ $proveedor->nit ?: '—' }}</td>
        <td style="font-size:.8rem;">{{ $proveedor->ciudad ?: '—' }}</td>
        <td style="font-size:.8rem;">{{ $proveedor->contacto ?: '—' }}</td>
        <td style="font-size:.8rem;white-space:nowrap;">
            {{ $proveedor->telefono ?: '—' }}
            @if($proveedor->whatsapp)
            <a href="https://wa.me/57{{ $proveedor->whatsapp }}" target="_blank" style="color:#166534;margin-left:.3rem;font-size:.85rem;"><i class="bi bi-whatsapp"></i></a>
            @endif
        </td>
        <td style="max-width:200px;">
            @if($proveedor->categorias && count($proveedor->categorias))
            @php $etqs = \App\Models\Proveedor::etiquetasCategorias(); @endphp
            @foreach(array_slice($proveedor->categorias, 0, 3) as $cat)
            <span style="display:inline-block;font-size:.68rem;font-weight:600;padding:.1rem .45rem;border-radius:50px;background:var(--color-muy-claro);color:var(--color-principal);margin:.1rem;">{{ $etqs[$cat] ?? $cat }}</span>
            @endforeach
            @if(count($proveedor->categorias) > 3)
            <span style="display:inline-block;font-size:.68rem;font-weight:600;padding:.1rem .45rem;border-radius:50px;background:#f3f4f6;color:#6b7280;margin:.1rem;">+{{ count($proveedor->categorias) - 3 }}</span>
            @endif
            @else
            <span style="color:#9ca3af;font-size:.78rem;">—</span>
            @endif
        </td>
        <td>
            @if($proveedor->calificacion)
            <div style="display:flex;gap:.1rem;align-items:center;">
                @for($i = 1; $i <= 5; $i++)
                <i class="bi bi-star{{ $i <= $proveedor->calificacion ? '-fill' : '' }}"
                   style="color:{{ $i <= $proveedor->calificacion ? '#FFC107' : '#DEE2E6' }};font-size:.85rem;"></i>
                @endfor
            </div>
            @else
            <span style="color:#9ca3af;font-size:.78rem;">—</span>
            @endif
        </td>
        <td style="text-align:right;font-weight:600;color:#166534;">
            ${{ number_format($proveedor->total_compras ?? 0, 0, ',', '.') }}
        </td>
        <td>
            <div style="display:flex;justify-content:center;gap:.3rem;">
                <a href="{{ route('proveedores.show', $proveedor) }}" class="tbl-btn-accion" title="Ver">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('proveedores.edit', $proveedor) }}" class="tbl-btn-accion" title="Editar">
                    <i class="bi bi-pencil"></i>
                </a>
            </div>
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

@endsection
