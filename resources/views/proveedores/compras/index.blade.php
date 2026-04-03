@extends('layouts.app')
@section('titulo', 'Historial de Compras')

@push('estilos')
<style>
    .comp-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; flex-wrap:wrap; gap:.75rem; }
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; transition:filter .18s; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1rem; font-size:.875rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; color:#374151; }

    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:.875rem; margin-bottom:1.25rem; }
    @media(max-width:900px){ .stats-grid{ grid-template-columns:repeat(2,1fr); } }
    .stat-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1rem 1.1rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .stat-valor { font-family:var(--fuente-titulos); font-size:1.4rem; font-weight:600; color:var(--color-principal); }
    .stat-label { font-size:.7rem; font-weight:600; color:#8fa39a; text-transform:uppercase; letter-spacing:.05em; }

    .filtros-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:1rem 1.2rem; margin-bottom:1.1rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .filtros-grid { display:grid; grid-template-columns:2fr 1fr 1fr 1fr 1fr auto; gap:.65rem; align-items:end; }
    @media(max-width:1000px){ .filtros-grid{ grid-template-columns:1fr 1fr; } }
    .form-label { font-size:.76rem; font-weight:700; color:var(--color-hover); display:block; margin-bottom:.2rem; }
    .form-input { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.42rem .75rem; font-size:.84rem; color:#1c2b22; background:#fff; outline:none; }
    .form-input:focus { border-color:var(--color-principal); }

    .panel-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .panel-header { padding:.8rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
    .panel-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:600; color:var(--color-hover); display:flex; align-items:center; gap:.4rem; }
    .panel-titulo i { color:var(--color-principal); }
    .tabla-compras { width:100%; border-collapse:collapse; font-size:.82rem; }
    .tabla-compras th { font-size:.69rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-principal); padding:.5rem .75rem; border-bottom:2px solid var(--color-muy-claro); text-align:left; white-space:nowrap; }
    .tabla-compras td { padding:.5rem .75rem; border-bottom:1px solid var(--fondo-borde); color:#374151; vertical-align:middle; }
    .tabla-compras tr:last-child td { border-bottom:none; }
    .tabla-compras tr:hover td { background:var(--fondo-card-alt); }
    .acc-btn { display:inline-flex; align-items:center; gap:.2rem; padding:.22rem .55rem; border-radius:6px; font-size:.74rem; font-weight:500; text-decoration:none; border:none; cursor:pointer; }
    .acc-ver { background:var(--color-muy-claro); color:var(--color-principal); }
    .pagination-wrapper { padding:.75rem 1.25rem; border-top:1px solid var(--fondo-borde); }
    #tabla-container { min-height:100px; transition:opacity .15s; }
    #tabla-container.cargando { opacity:.4; pointer-events:none; }
</style>
@endpush

@section('contenido')

@if(session('success') || session('exito'))
<div class="alerta-flash" style="background:#dcfce7;color:#166534;border:1px solid #86efac;">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') ?? session('exito') }}
</div>
@endif
@if(session('error'))
<div class="alerta-flash" style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;">
    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
</div>
@endif

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo"><i class="bi bi-cart3 me-2"></i>Historial de Compras</h1>
        <p class="page-subtitulo">Registro de compras a proveedores</p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
        <a href="{{ route('proveedores.index') }}"
           style="background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem 1.1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
            <i class="bi bi-truck"></i> Proveedores
        </a>
        <a href="{{ route('compras.create') }}" class="btn-morado">
            <i class="bi bi-plus-lg"></i> Registrar Compra
        </a>
    </div>
</div>

{{-- Cards resumen --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.4rem;font-weight:800;color:var(--color-principal);">${{ number_format($totalMes, 0, ',', '.') }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Comprado este mes</div>
            <i class="bi bi-calendar-month" style="font-size:1.2rem;color:var(--color-principal);opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.4rem;font-weight:800;color:#0ea5e9;">${{ number_format($totalAnio, 0, ',', '.') }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Comprado este año</div>
            <i class="bi bi-graph-up" style="font-size:1.2rem;color:#0ea5e9;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;{{ $pendiente > 0 ? 'border-color:#fde68a;' : '' }}">
            <div style="font-size:1.4rem;font-weight:800;color:{{ $pendiente > 0 ? '#b45309' : 'var(--color-principal)' }};">${{ number_format($pendiente, 0, ',', '.') }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Pendiente de pago</div>
            <i class="bi bi-clock" style="font-size:1.2rem;color:#f59e0b;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:#8b5cf6;">{{ $numProveedores }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Proveedores activos</div>
            <i class="bi bi-truck" style="font-size:1.2rem;color:#8b5cf6;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
</div>

<x-tabla-listado
    :paginacion="$compras"
    placeholder="Buscar N° compra o proveedor..."
    icono-vacio="bi-inbox"
    mensaje-vacio="No hay compras registradas"
>
    <x-slot:filtros>
        <select name="estado" class="tbl-filtro-select">
            <option value="">Todos los estados</option>
            <option value="pendiente" {{ request('estado')==='pendiente' ? 'selected' : '' }}>Pendiente</option>
            <option value="pagada"    {{ request('estado')==='pagada'    ? 'selected' : '' }}>Pagada</option>
            <option value="cancelada" {{ request('estado')==='cancelada' ? 'selected' : '' }}>Cancelada</option>
        </select>
        <select name="proveedor_id" class="tbl-filtro-select">
            <option value="">Todos los proveedores</option>
            @foreach($proveedoresList as $p)
            <option value="{{ $p->id }}" {{ request('proveedor_id') == $p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>
            @endforeach
        </select>
        <input type="date" name="desde" class="tbl-filtro-date" value="{{ request('desde') }}" title="Desde">
        <input type="date" name="hasta" class="tbl-filtro-date" value="{{ request('hasta') }}" title="Hasta">
    </x-slot:filtros>

    <x-slot:accion-vacio>
        <div class="mt-3">
            <a href="{{ route('compras.create') }}" class="btn-morado">
                <i class="bi bi-plus-circle"></i> Registrar primera compra
            </a>
        </div>
    </x-slot:accion-vacio>

    <x-slot:thead>
        <tr>
            <th>N° Compra</th>
            <th>Fecha</th>
            <th>Proveedor</th>
            <th>Factura</th>
            <th style="text-align:center;">Ítems</th>
            <th style="text-align:right;">Total</th>
            <th>Método</th>
            <th>Estado</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

    @foreach($compras as $compra)
    @php
        $estadoColors = [
            'pendiente' => ['#fff3cd','#856404'],
            'pagada'    => ['#d4edda','#155724'],
            'cancelada' => ['#fee2e2','#7f1d1d'],
        ];
        $bc = $estadoColors[$compra->estado] ?? ['#f3f4f6','#374151'];
    @endphp
    <tr>
        <td>
            <span style="font-family:monospace;font-weight:700;color:var(--color-principal);font-size:.8rem;">
                {{ $compra->numero_formateado }}
            </span>
        </td>
        <td style="white-space:nowrap;font-size:.8rem;color:#4b5563;">{{ $compra->fecha_compra->format('d/m/Y') }}</td>
        <td>
            <a href="{{ route('proveedores.show', $compra->proveedor) }}" style="color:#1c2b22;text-decoration:none;font-weight:500;font-size:.82rem;">
                {{ $compra->proveedor->nombre }}
            </a>
        </td>
        <td style="font-size:.8rem;color:#6b7280;">{{ $compra->numero_factura ?: '—' }}</td>
        <td style="text-align:center;font-size:.8rem;">{{ $compra->items_count }}</td>
        <td style="text-align:right;font-weight:700;color:#166534;white-space:nowrap;">
            ${{ number_format($compra->total, 0, ',', '.') }}
        </td>
        <td style="font-size:.78rem;color:#6b7280;">{{ $compra->metodo_pago_label }}</td>
        <td>
            <span style="background:{{ $bc[0] }};color:{{ $bc[1] }};border-radius:20px;padding:.12rem .65rem;font-size:.7rem;font-weight:700;">
                {{ ucfirst($compra->estado) }}
            </span>
        </td>
        <td>
            <div style="display:flex;justify-content:center;gap:.3rem;">
                <a href="{{ route('compras.show', $compra) }}" class="tbl-btn-accion" title="Ver">
                    <i class="bi bi-eye"></i>
                </a>
            </div>
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

@endsection
