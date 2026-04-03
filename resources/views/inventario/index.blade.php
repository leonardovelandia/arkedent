@extends('layouts.app')
@section('titulo', 'Inventario')

@push('estilos')
<style>
    .inv-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; flex-wrap:wrap; gap:.75rem; }
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; transition:filter .18s; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; color:#374151; }
    .btn-verde { background:#166534; color:#fff; border:none; border-radius:8px; padding:.4rem .85rem; font-size:.8rem; font-weight:500; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; cursor:pointer; }
    .btn-verde:hover { filter:brightness(1.1); color:#fff; }
    .btn-azul { background:#1e40af; color:#fff; border:none; border-radius:8px; padding:.4rem .85rem; font-size:.8rem; font-weight:500; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; cursor:pointer; }
    .btn-azul:hover { filter:brightness(1.1); color:#fff; }

    .stats-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:.875rem; margin-bottom:1.25rem; }
    @media(max-width:1000px){ .stats-grid{ grid-template-columns:repeat(3,1fr); } }
    @media(max-width:600px){ .stats-grid{ grid-template-columns:1fr 1fr; } }

    .metrica-inv { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1rem 1.1rem; display:flex; flex-direction:column; gap:.35rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .metrica-valor { font-family:var(--fuente-titulos); font-size:1.5rem; font-weight:600; color:var(--color-principal); line-height:1; }
    .metrica-label { font-size:.7rem; font-weight:600; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; }

    .filtros-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:1.1rem 1.25rem; margin-bottom:1.1rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .filtros-grid { display:grid; grid-template-columns:1fr 1fr 2fr auto; gap:.75rem; align-items:end; }
    .filtros-grid > div:last-child { min-width:0; }
    @media(max-width:800px){ .filtros-grid{ grid-template-columns:1fr 1fr; } }
    .form-label { font-size:.76rem; font-weight:700; color:var(--color-hover); display:block; margin-bottom:.2rem; }
    .form-input { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.42rem .75rem; font-size:.84rem; color:#1c2b22; background:#fff; outline:none; }
    .form-input:focus { border-color:var(--color-principal); }

    .panel-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .panel-header { padding:.8rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
    .panel-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:600; color:var(--color-hover); display:flex; align-items:center; gap:.4rem; }
    .panel-titulo i { color:var(--color-principal); }

    .tabla-inv { width:100%; border-collapse:collapse; font-size:.82rem; }
    .tabla-inv th { font-size:.69rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-principal); padding:.5rem .75rem; border-bottom:2px solid var(--color-muy-claro); text-align:left; white-space:nowrap; }
    .tabla-inv td { padding:.5rem .75rem; border-bottom:1px solid var(--fondo-borde); color:#374151; vertical-align:middle; }
    .tabla-inv tr:last-child td { border-bottom:none; }
    .tabla-inv tr:hover td { background:var(--fondo-card-alt); }

    .badge-cat { display:inline-block; font-size:.7rem; font-weight:600; padding:.18rem .55rem; border-radius:50px; color:#fff; }
    .barra-stock { height:5px; background:#f3f4f6; border-radius:50px; overflow:hidden; margin-top:.25rem; min-width:60px; }
    .barra-fill { height:100%; border-radius:50px; }

    .acc-btn { display:inline-flex; align-items:center; gap:.25rem; padding:.22rem .55rem; border-radius:6px; font-size:.74rem; font-weight:500; text-decoration:none; border:none; cursor:pointer; }
    .acc-ver { background:var(--color-muy-claro); color:var(--color-principal); }
    .acc-ver:hover { background:var(--color-muy-claro); color:var(--color-principal); }
    .acc-edit { background:#f3f4f6; color:#374151; }
    .acc-edit:hover { background:#e5e7eb; color:#374151; }
    .acc-entrada { background:#dcfce7; color:#166534; }
    .acc-entrada:hover { background:#bbf7d0; color:#166534; }
    .acc-activar { background:#dbeafe; color:#1e40af; }
    .acc-activar:hover { background:#bfdbfe; color:#1e40af; }
    .fila-inactiva td { opacity:.55; }
    .fila-inactiva:hover td { background:#fafafa; opacity:.7; }

    .tabla-scroll { overflow-x:auto; overflow-y:auto; max-height:520px; }
    .tabla-scroll thead th { position:sticky; top:0; background:#fff; z-index:1; }
    .pagination-wrapper { padding:.75rem 1.25rem; border-top:1px solid var(--fondo-borde); display:flex; justify-content:flex-end; }
    .alert-banner { background:#FFF3CD; border:1px solid #FFC107; border-radius:10px; padding:.875rem 1.25rem; margin-bottom:1rem; display:flex; align-items:flex-start; gap:.75rem; }
    #tabla-container { min-height:120px; position:relative; transition:opacity .15s; }
    #tabla-container.cargando { opacity:.45; pointer-events:none; }
</style>
@endpush

@section('contenido')

@if($alertas->count() > 0)
<div style="background:#FFF3CD;border:1px solid #FFC107;border-radius:10px;padding:.875rem 1.25rem;margin-bottom:1rem;display:flex;align-items:flex-start;gap:.75rem;">
    <i class="bi bi-exclamation-triangle-fill" style="color:#856404;font-size:1.1rem;margin-top:.1rem;flex-shrink:0;"></i>
    <div style="flex:1;">
        <strong style="color:#856404;font-size:.85rem;">{{ $alertas->count() }} material(es) con stock bajo o crítico</strong>
        <div style="font-size:.77rem;color:#856404;margin-top:.2rem;">{{ $alertas->pluck('nombre')->join(', ') }}</div>
    </div>
    <a href="{{ route('inventario.index', ['estado' => 'critico']) }}" style="font-size:.78rem;color:#856404;font-weight:600;white-space:nowrap;text-decoration:none;">Ver críticos →</a>
</div>
@endif

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo">Inventario</h1>
        <p class="page-subtitulo">Control de materiales e insumos del consultorio</p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
        <a href="{{ route('inventario-categorias.index') }}" class="btn-morado" style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
            <i class="bi bi-tags"></i> Categorías
        </a>
        <a href="{{ route('compras.create') }}" class="btn-morado" style="background:#166534;">
            <i class="bi bi-cart-plus"></i> Registrar Compra
        </a>
        <a href="{{ route('inventario.create') }}" class="btn-morado">
            <i class="bi bi-plus-lg"></i> Nuevo Material
        </a>
    </div>
</div>

{{-- Métricas --}}
<div class="stats-grid">
    <div class="metrica-inv">
        <span class="metrica-label">Total Materiales</span>
        <div class="metrica-valor">{{ $totalActivos }}</div>
    </div>
    <div class="metrica-inv">
        <span class="metrica-label">Stock Normal</span>
        <div class="metrica-valor" style="color:#166534;">{{ $totalNormal }}</div>
    </div>
    <div class="metrica-inv">
        <span class="metrica-label">Stock Bajo</span>
        <div class="metrica-valor" style="color:#d97706;">{{ $totalBajo }}</div>
    </div>
    <div class="metrica-inv">
        <span class="metrica-label">Stock Crítico</span>
        <div class="metrica-valor" style="color:#dc2626;">{{ $totalCritico }}</div>
    </div>
    <div class="metrica-inv">
        <span class="metrica-label">Valor en Inventario</span>
        <div class="metrica-valor" style="font-size:1.1rem;">${{ number_format($valorTotal, 0, ',', '.') }}</div>
    </div>
</div>

<x-tabla-listado
    :paginacion="$materiales"
    placeholder="Nombre o código..."
    icono-vacio="bi-inbox"
    mensaje-vacio="No se encontraron materiales"
>
    <x-slot:filtros>
        <select name="categoria_id" class="tbl-filtro-select">
            <option value="">Todas las categorías</option>
            @foreach($categorias as $cat)
            <option value="{{ $cat->id }}" {{ $categoriaId == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
            @endforeach
        </select>
        <select name="estado" class="tbl-filtro-select">
            <option value="">Todos los estados</option>
            <option value="normal"   {{ $estado === 'normal'   ? 'selected' : '' }}>Normal</option>
            <option value="bajo"     {{ $estado === 'bajo'     ? 'selected' : '' }}>Bajo</option>
            <option value="critico"  {{ $estado === 'critico'  ? 'selected' : '' }}>Crítico</option>
            <option value="inactivo" {{ $estado === 'inactivo' ? 'selected' : '' }}>Desactivados</option>
        </select>
    </x-slot:filtros>

    <x-slot:thead>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th style="text-align:center;">Stock Actual</th>
            <th style="text-align:center;">Mínimo</th>
            <th style="text-align:center;">Estado</th>
            <th style="text-align:right;">Precio Unit.</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

    @foreach($materiales as $material)
    @php $estadoStock = $material->estado_stock; @endphp
    <tr class="{{ $material->activo ? '' : 'fila-inactiva' }}">
        <td style="color:#9ca3af;font-size:.75rem;">{{ $material->codigo ?: '—' }}</td>
        <td>
            <div style="font-weight:500;color:#1c2b22;">{{ $material->nombre }}</div>
            @if($material->ubicacion)
            <div style="font-size:.72rem;color:#9ca3af;"><i class="bi bi-geo-alt"></i> {{ $material->ubicacion }}</div>
            @endif
        </td>
        <td>
            @if($material->categoria)
            <span class="badge-cat" style="background:{{ $material->categoria->color ?? 'var(--color-principal)' }};">{{ $material->categoria->nombre }}</span>
            @else
            <span style="color:#9ca3af;font-size:.78rem;">—</span>
            @endif
        </td>
        <td style="text-align:center;">
            <div style="font-weight:600;font-size:.9rem;color:{{ $estadoStock === 'critico' ? '#dc2626' : ($estadoStock === 'bajo' ? '#d97706' : '#166534') }};">
                {{ number_format($material->stock_actual, 2) }}
            </div>
            <div style="font-size:.7rem;color:#9ca3af;">{{ $material->unidad_medida }}</div>
            @if($material->porcentaje_stock !== null && $material->activo)
            <div class="barra-stock">
                <div class="barra-fill" style="width:{{ $material->porcentaje_stock }}%;background:{{ $estadoStock === 'critico' ? '#dc2626' : ($estadoStock === 'bajo' ? '#f59e0b' : '#166534') }};"></div>
            </div>
            @endif
        </td>
        <td style="text-align:center;font-size:.82rem;color:#6b7280;">
            {{ number_format($material->stock_minimo, 2) }} {{ $material->unidad_medida }}
        </td>
        <td style="text-align:center;">
            @if(!$material->activo)
            <span style="display:inline-block;font-size:.7rem;padding:.22rem .6rem;border-radius:20px;background:#f3f4f6;color:#6b7280;">
                <i class="bi bi-slash-circle"></i> Desactivado
            </span>
            @else
            <span style="display:inline-block;font-size:.7rem;padding:.22rem .6rem;border-radius:20px;background:{{ $estadoStock === 'critico' ? '#fee2e2' : ($estadoStock === 'bajo' ? '#fef3c7' : '#dcfce7') }};color:{{ $estadoStock === 'critico' ? '#dc2626' : ($estadoStock === 'bajo' ? '#d97706' : '#166534') }};">
                @if($estadoStock === 'critico') <i class="bi bi-exclamation-triangle"></i> Crítico
                @elseif($estadoStock === 'bajo') <i class="bi bi-arrow-down"></i> Bajo
                @else <i class="bi bi-check-circle"></i> Normal
                @endif
            </span>
            @endif
        </td>
        <td style="text-align:right;font-size:.82rem;white-space:nowrap;">
            {{ $material->precio_unitario ? '$' . number_format($material->precio_unitario, 0, ',', '.') : '—' }}
        </td>
        <td>
            <div style="display:flex;justify-content:center;gap:.3rem;flex-wrap:nowrap;">
                <a href="{{ route('inventario.show', $material) }}" class="tbl-btn-accion" title="Ver">
                    <i class="bi bi-eye"></i>
                </a>
                @if($material->activo)
                <a href="{{ route('inventario.edit', $material) }}" class="tbl-btn-accion" title="Editar">
                    <i class="bi bi-pencil"></i>
                </a>
                <a href="{{ route('inventario.show', $material) }}#entrada" class="tbl-btn-accion success" title="Registrar entrada">
                    <i class="bi bi-plus-circle"></i>
                </a>
                @else
                <form method="POST" action="{{ route('inventario.activar', $material) }}" style="display:inline;">
                    @csrf @method('PATCH')
                    <button type="submit" class="tbl-btn-accion success" title="Activar">
                        <i class="bi bi-arrow-up-circle"></i>
                    </button>
                </form>
                @endif
            </div>
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

@endsection
