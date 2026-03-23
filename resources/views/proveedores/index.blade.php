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

<div class="prov-header">
    <div>
        <h4 style="font-family:var(--fuente-titulos); font-weight:700; color:#1c2b22; margin:0;">Proveedores</h4>
        <p style="font-size:.8rem; color:#9ca3af; margin:.15rem 0 0;">Gestión de proveedores de materiales e insumos</p>
    </div>
    <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
        <a href="{{ route('compras.index') }}" class="btn-gris"><i class="bi bi-cart"></i> Historial Compras</a>
        <a href="{{ route('proveedores.comparar') }}" class="btn-azul"><i class="bi bi-bar-chart"></i> Comparar Precios</a>
        <a href="{{ route('proveedores.create') }}" class="btn-morado"><i class="bi bi-plus-lg"></i> Nuevo Proveedor</a>
    </div>
</div>

@if(session('success'))
<div style="background:#dcfce7; border:1px solid #86efac; border-radius:8px; padding:.6rem 1rem; margin-bottom:1rem; font-size:.84rem; color:#166534;">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- Resumen --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-valor">{{ $totalActivos }}</div>
        <div class="stat-label">Proveedores activos</div>
    </div>
    <div class="stat-card">
        <div class="stat-valor" style="font-size:1.15rem;">${{ number_format($comprasMes, 0, ',', '.') }}</div>
        <div class="stat-label">Compras este mes</div>
    </div>
    <div class="stat-card">
        <div class="stat-valor" style="font-size:1rem; color:#1c2b22;">{{ $proveedorFrecuente?->proveedor?->nombre ?? '—' }}</div>
        <div class="stat-label">Proveedor más frecuente (mes)</div>
    </div>
    <div class="stat-card" style="border-color:{{ $comprasPendientes > 0 ? '#fde68a' : 'var(--fondo-borde)' }};">
        <div class="stat-valor" style="color:{{ $comprasPendientes > 0 ? '#b45309' : 'var(--color-principal)' }}; font-size:1.15rem;">${{ number_format($comprasPendientes, 0, ',', '.') }}</div>
        <div class="stat-label">Compras pendientes de pago</div>
    </div>
</div>

{{-- Filtros --}}
<div class="filtros-card">
    <form id="form-filtros-prov" method="GET" action="{{ route('proveedores.index') }}">
        <div class="filtros-grid">
            <div>
                <label class="form-label">Buscar</label>
                <input type="text" name="buscar" id="filtro-buscar" class="form-input" placeholder="Nombre, NIT o contacto…" value="{{ request('buscar') }}">
            </div>
            <div>
                <label class="form-label">Categoría</label>
                <select name="categoria" id="filtro-categoria" class="form-input">
                    <option value="">Todas</option>
                    @foreach($categorias as $key => $label)
                        <option value="{{ $key }}" {{ request('categoria') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; align-items:flex-end;">
                <a href="{{ route('proveedores.index') }}" id="btn-limpiar-prov" class="btn-gris" style="height:38px;"><i class="bi bi-x"></i></a>
            </div>
        </div>
    </form>
</div>

{{-- Tabla --}}
<div class="panel-card" id="tabla-container">
    @include('proveedores._tabla')
</div>

@push('scripts')
<script>
(function () {
    var baseUrl    = '{{ route('proveedores.index') }}';
    var form       = document.getElementById('form-filtros-prov');
    var contenedor = document.getElementById('tabla-container');
    var buscar     = document.getElementById('filtro-buscar');
    var timer;

    function getParams() {
        return new URLSearchParams(new FormData(form)).toString();
    }

    function cargarTabla(url) {
        contenedor.classList.add('cargando');
        history.replaceState(null, '', url);
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
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

    document.getElementById('filtro-categoria').addEventListener('change', function() {
        cargarTabla(baseUrl + '?' + getParams());
    });

    buscar.addEventListener('input', function() {
        clearTimeout(timer);
        timer = setTimeout(function() { cargarTabla(baseUrl + '?' + getParams()); }, 500);
    });

    document.getElementById('btn-limpiar-prov').addEventListener('click', function(e) {
        e.preventDefault();
        form.querySelectorAll('select').forEach(function(s) { s.value = ''; });
        buscar.value = '';
        cargarTabla(baseUrl);
    });

    bindPaginacion();
})();
</script>
@endpush

@endsection
