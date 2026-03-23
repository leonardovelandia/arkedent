@extends('layouts.app')
@section('titulo', 'Órdenes de Laboratorio')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    .resumen-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.25rem; }
    .res-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1rem 1.25rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .res-label { font-size:.72rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#8fa39a; margin-bottom:.35rem; }
    .res-num { font-family:var(--fuente-titulos); font-size:1.8rem; font-weight:700; color:#1c2b22; line-height:1; }
    .res-sub { font-size:.75rem; color:#8fa39a; margin-top:.25rem; }

    .filtros-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1rem 1.25rem; margin-bottom:1rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .filtros-row { display:flex; flex-wrap:wrap; gap:.75rem; align-items:flex-end; }
    .filtro-group { display:flex; flex-direction:column; gap:.25rem; }
    .filtro-group label { font-size:.75rem; font-weight:600; color:var(--color-hover); }
    .filtro-group input, .filtro-group select { border:1px solid #e5e7eb; border-radius:8px; padding:.4rem .75rem; font-size:.875rem; outline:none; }
    .filtro-group input:focus, .filtro-group select:focus { border-color:var(--color-principal); }

    .tabla-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .tabla-header { padding:.875rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
    .tabla-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:600; color:var(--color-hover); }
    table { width:100%; border-collapse:collapse; }
    thead th { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#8fa39a; padding:.6rem 1rem; border-bottom:1px solid var(--fondo-borde); text-align:left; white-space:nowrap; }
    tbody td { padding:.65rem 1rem; border-bottom:1px solid var(--fondo-borde); font-size:.855rem; color:#1c2b22; vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background:var(--fondo-app); }

    .badge-lab { display:inline-block; padding:.22rem .65rem; border-radius:50px; font-size:.7rem; font-weight:700; white-space:nowrap; }
    .badge-warning  { background:#fff3cd; color:#856404; }
    .badge-info     { background:#d1ecf1; color:#0c5460; }
    .badge-primary  { background:#cce5ff; color:#004085; }
    .badge-success  { background:#d4edda; color:#155724; }
    .badge-dark     { background:#d6d8d9; color:#1b1e21; }
    .badge-danger   { background:#f8d7da; color:#721c24; }
    .badge-morado   { background:var(--color-muy-claro); color:var(--color-principal); }

    .dias-verde   { background:#d4edda; color:#155724; }
    .dias-amarillo{ background:#fff3cd; color:#856404; }
    .dias-rojo    { background:#f8d7da; color:#721c24; }

    .accion-btn { display:inline-flex; align-items:center; gap:.2rem; padding:.3rem .6rem; border-radius:6px; font-size:.78rem; font-weight:500; text-decoration:none; border:none; cursor:pointer; transition:filter .15s; }
    .accion-ver  { background:var(--color-muy-claro); color:var(--color-principal); }
    .accion-edit { background:#e3f2fd; color:#1565c0; }
    .accion-btn:hover { filter:brightness(.92); }

    .vacio { padding:2.5rem; text-align:center; color:#8fa39a; }
    .vacio i { font-size:2rem; display:block; margin-bottom:.5rem; }
    .vacio p { font-size:.85rem; margin:0; }
    #tabla-container { min-height:100px; transition:opacity .15s; }
    #tabla-container.cargando { opacity:.4; pointer-events:none; }

    @media(max-width:900px) { .resumen-grid { grid-template-columns:repeat(2,1fr); } }
    @media(max-width:600px) { .resumen-grid { grid-template-columns:1fr 1fr; } .filtros-row { flex-direction:column; } }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

{{-- Alerta de vencidas --}}
@if($ordenesVencidas > 0)
<div style="background:#F8D7DA; border:1px solid #DC3545; border-radius:10px; padding:.875rem 1.25rem; margin-bottom:1rem; display:flex; align-items:center; gap:.75rem;">
    <i class="bi bi-exclamation-triangle-fill" style="color:#721C24; font-size:1.1rem;"></i>
    <div>
        <strong style="color:#721C24;">{{ $ordenesVencidas }} orden(es) vencida(s)</strong>
        <span style="color:#721C24; font-size:.83rem;"> — La fecha de entrega estimada ya pasó</span>
    </div>
</div>
@endif

{{-- Título de sección --}}
<div style="margin-bottom:1.25rem;">
    <h1 style="font-family:var(--fuente-titulos);font-size:1.5rem;font-weight:700;color:#1c2b22;margin:0;">
        <i class="bi bi-flask me-2" style="color:var(--color-principal);"></i>Órdenes de Laboratorio
    </h1>
    <p style="font-size:.85rem;color:#9ca3af;margin:.2rem 0 0;">Gestión y seguimiento de trabajos de laboratorio dental</p>
</div>

{{-- Cards resumen --}}
<div class="resumen-grid">
    <div class="res-card">
        <div class="res-label">Total Activas</div>
        <div class="res-num">{{ $totalActivas }}</div>
        <div class="res-sub">órdenes registradas</div>
    </div>
    <div class="res-card">
        <div class="res-label">En Proceso</div>
        <div class="res-num">{{ $enProceso }}</div>
        <div class="res-sub">pendientes / enviadas</div>
    </div>
    <div class="res-card" style="border-color:#bbf7d0;">
        <div class="res-label" style="color:#166534;">Recibidas</div>
        <div class="res-num" style="color:#166534;">{{ $recibidas }}</div>
        <div class="res-sub" style="color:#166534;">listas para instalar</div>
    </div>
    <div class="res-card" style="border-color:#fca5a5;">
        <div class="res-label" style="color:#dc2626;">Vencidas</div>
        <div class="res-num" style="color:#dc2626;">{{ $vencidas }}</div>
        <div class="res-sub" style="color:#dc2626;">requieren atención</div>
    </div>
</div>

{{-- Filtros --}}
<div class="filtros-card">
    <form id="form-filtros-lab" method="GET" action="{{ route('laboratorio.index') }}">
        <div class="filtros-row">
            <div class="filtro-group" style="flex:2; min-width:200px;">
                <label>Buscar paciente o N° orden</label>
                <input type="text" name="buscar" id="filtro-buscar" value="{{ request('buscar') }}" placeholder="Nombre o LAB-XXXX...">
            </div>
            <div class="filtro-group" style="flex:1; min-width:150px;">
                <label>Laboratorio</label>
                <select name="laboratorio_id" id="filtro-laboratorio">
                    <option value="">Todos</option>
                    @foreach($laboratorios as $lab)
                        <option value="{{ $lab->id }}" {{ request('laboratorio_id') == $lab->id ? 'selected' : '' }}>{{ $lab->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filtro-group" style="min-width:130px;">
                <label>Estado</label>
                <select name="estado" id="filtro-estado">
                    <option value="">Todos</option>
                    <option value="pendiente"  {{ request('estado') === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                    <option value="enviado"    {{ request('estado') === 'enviado'    ? 'selected' : '' }}>Enviado</option>
                    <option value="en_proceso" {{ request('estado') === 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                    <option value="recibido"   {{ request('estado') === 'recibido'   ? 'selected' : '' }}>Recibido</option>
                    <option value="instalado"  {{ request('estado') === 'instalado'  ? 'selected' : '' }}>Instalado</option>
                    <option value="cancelado"  {{ request('estado') === 'cancelado'  ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="filtro-group" style="min-width:130px;">
                <label>Desde</label>
                <input type="date" name="desde" id="filtro-desde" value="{{ request('desde') }}">
            </div>
            <div class="filtro-group" style="min-width:130px;">
                <label>Hasta</label>
                <input type="date" name="hasta" id="filtro-hasta" value="{{ request('hasta') }}">
            </div>
            <div class="filtro-group" style="justify-content:flex-end;">
                <a href="{{ route('laboratorio.index') }}" id="btn-limpiar-lab"
                   style="display:inline-flex;align-items:center;gap:.3rem;font-size:.83rem;color:var(--color-principal);text-decoration:none;height:35px;">
                    <i class="bi bi-x-circle"></i> Limpiar
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Tabla --}}
<div class="tabla-card" id="tabla-container">
    @include('laboratorio._tabla')
</div>

@push('scripts')
<script>
(function () {
    var baseUrl    = '{{ route('laboratorio.index') }}';
    var form       = document.getElementById('form-filtros-lab');
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

    ['filtro-laboratorio', 'filtro-estado', 'filtro-desde', 'filtro-hasta'].forEach(function(id) {
        document.getElementById(id).addEventListener('change', function() {
            cargarTabla(baseUrl + '?' + getParams());
        });
    });

    buscar.addEventListener('input', function() {
        clearTimeout(timer);
        timer = setTimeout(function() { cargarTabla(baseUrl + '?' + getParams()); }, 500);
    });

    document.getElementById('btn-limpiar-lab').addEventListener('click', function(e) {
        e.preventDefault();
        form.querySelectorAll('select').forEach(function(s) { s.value = ''; });
        form.querySelectorAll('input[type="date"]').forEach(function(d) { d.value = ''; });
        buscar.value = '';
        cargarTabla(baseUrl);
    });

    bindPaginacion();
})();
</script>
@endpush

@endsection
