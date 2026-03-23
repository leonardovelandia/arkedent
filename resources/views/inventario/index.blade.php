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
<div class="alert-banner">
    <i class="bi bi-exclamation-triangle-fill" style="color:#856404; font-size:1.1rem; margin-top:.1rem; flex-shrink:0;"></i>
    <div style="flex:1;">
        <strong style="color:#856404; font-size:.85rem;">
            {{ $alertas->count() }} material(es) con stock bajo o crítico
        </strong>
        <div style="font-size:.77rem; color:#856404; margin-top:.2rem;">
            {{ $alertas->pluck('nombre')->join(', ') }}
        </div>
    </div>
    <a href="{{ route('inventario.index', ['estado' => 'critico']) }}" style="margin-left:auto; font-size:.78rem; color:#856404; font-weight:600; white-space:nowrap; text-decoration:none;">
        Ver críticos →
    </a>
</div>
@endif

<div class="inv-header">
    <div>
        <h4 style="font-family:var(--fuente-titulos); font-weight:700; color:#1c2b22; margin:0;">Inventario</h4>
        <p style="font-size:.82rem; color:#9ca3af; margin:0;">Control de materiales e insumos del consultorio</p>
    </div>
    <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
        <a href="{{ route('inventario-categorias.index') }}" class="btn-gris"><i class="bi bi-tags"></i> Categorías</a>
        <a href="{{ route('compras.create') }}" class="btn-verde"><i class="bi bi-cart-plus"></i> Registrar Compra</a>
        <a href="{{ route('inventario.create') }}" class="btn-morado"><i class="bi bi-plus-lg"></i> Nuevo Material</a>
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

{{-- Filtros --}}
<div class="filtros-card">
    <form id="form-filtros" method="GET" action="{{ route('inventario.index') }}">
        <div class="filtros-grid">
            <div>
                <label class="form-label">Categoría</label>
                <select name="categoria_id" class="form-input">
                    <option value="">Todas</option>
                    @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ $categoriaId == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Estado</label>
                <select name="estado" class="form-input">
                    <option value="">Todos</option>
                    <option value="normal"   {{ $estado === 'normal'   ? 'selected' : '' }}>Normal</option>
                    <option value="bajo"     {{ $estado === 'bajo'     ? 'selected' : '' }}>Bajo</option>
                    <option value="critico"  {{ $estado === 'critico'  ? 'selected' : '' }}>Crítico</option>
                    <option value="inactivo" {{ $estado === 'inactivo' ? 'selected' : '' }}>Desactivados</option>
                </select>
            </div>
            <div>
                <label class="form-label">Buscar</label>
                <input type="text" name="buscar" id="input-buscar" class="form-input" placeholder="Nombre o código…" value="{{ $buscar }}">
            </div>
            <div style="display:flex; align-items:flex-end;">
                <a href="{{ route('inventario.index') }}" id="btn-limpiar" class="btn-gris"><i class="bi bi-x"></i> Limpiar</a>
            </div>
        </div>
    </form>
</div>

{{-- Tabla --}}
<div class="panel-card" id="tabla-container">
    @include('inventario._tabla')
</div>

@push('scripts')
<script>
(function () {
    var baseUrl  = '{{ route('inventario.index') }}';
    var contenedor = document.getElementById('tabla-container');
    var form     = document.getElementById('form-filtros');
    var inputBuscar = document.getElementById('input-buscar');
    var timer;

    function getParams() {
        var data = new FormData(form);
        return new URLSearchParams(data).toString();
    }

    function cargarTabla(url) {
        contenedor.classList.add('cargando');
        history.replaceState(null, '', url);
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.text(); })
        .then(function(html) {
            contenedor.innerHTML = html;
            contenedor.classList.remove('cargando');
            bindPaginacion();
        })
        .catch(function() { contenedor.classList.remove('cargando'); });
    }

    function bindPaginacion() {
        contenedor.querySelectorAll('.pagination a').forEach(function(a) {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                cargarTabla(this.href);
            });
        });
    }

    // Selects → buscar al cambiar
    form.querySelectorAll('select').forEach(function(sel) {
        sel.addEventListener('change', function() {
            cargarTabla(baseUrl + '?' + getParams());
        });
    });

    // Campo buscar → debounce 500ms
    inputBuscar.addEventListener('input', function() {
        clearTimeout(timer);
        timer = setTimeout(function() {
            cargarTabla(baseUrl + '?' + getParams());
        }, 500);
    });

    // Botón limpiar
    document.getElementById('btn-limpiar').addEventListener('click', function(e) {
        e.preventDefault();
        form.querySelectorAll('select').forEach(function(s) { s.value = ''; });
        inputBuscar.value = '';
        cargarTabla(baseUrl);
    });

    bindPaginacion();
})();
</script>
@endpush

@endsection
