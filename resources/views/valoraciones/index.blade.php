@extends('layouts.app')
@section('titulo', 'Valoraciones')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-out { background:transparent; color:var(--color-principal); border:1px solid var(--color-principal); border-radius:8px; padding:.4rem .85rem; font-size:.82rem; font-weight:500; display:inline-flex; align-items:center; gap:.3rem; transition:background .15s; text-decoration:none; }
    .btn-out:hover { background:var(--color-muy-claro); }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.4rem .85rem; font-size:.82rem; font-weight:500; display:inline-flex; align-items:center; gap:.3rem; transition:background .15s; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; }
    .filtros-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:.9rem 1.25rem; margin-bottom:1.25rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .form-ctrl { border:1px solid #d1d5db; border-radius:8px; padding:.42rem .75rem; font-size:.875rem; color:#374151; background:#fff; width:100%; }
    .form-ctrl:focus { outline:none; border-color:var(--color-principal); }
    .tabla-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    #tabla-container { min-height:100px; transition:opacity .15s; }
    #tabla-container.cargando { opacity:.4; pointer-events:none; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h1 style="font-family:var(--fuente-titulos);font-size:1.5rem;font-weight:700;color:#1c2b22;margin:0;">
            <i class="bi bi-clipboard2-pulse me-2"></i>Valoraciones
        </h1>
        <p style="font-size:.85rem;color:#9ca3af;margin:.2rem 0 0;">Evaluaciones diagnósticas iniciales</p>
    </div>
    <a href="{{ route('valoraciones.create') }}" class="btn-morado">
        <i class="bi bi-plus-lg"></i> Nueva Valoración
    </a>
</div>

{{-- Filtros --}}
<div class="filtros-card">
    <form id="form-filtros-val" method="GET" action="{{ route('valoraciones.index') }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label style="font-size:.73rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.04em;">Paciente</label>
                <input type="text" name="buscar" id="filtro-buscar" value="{{ $buscar }}" class="form-ctrl" placeholder="Nombre, apellido, documento…">
            </div>
            <div class="col-md-2">
                <label style="font-size:.73rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.04em;">Estado</label>
                <select name="estado" id="filtro-estado" class="form-ctrl">
                    <option value="">Todos</option>
                    <option value="en_proceso"  {{ $estado == 'en_proceso'  ? 'selected' : '' }}>En proceso</option>
                    <option value="completada"  {{ $estado == 'completada'  ? 'selected' : '' }}>Completada</option>
                    <option value="cancelada"   {{ $estado == 'cancelada'   ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="col-md-2">
                <label style="font-size:.73rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.04em;">Desde</label>
                <input type="date" name="desde" id="filtro-desde" value="{{ $desde }}" class="form-ctrl">
            </div>
            <div class="col-md-2">
                <label style="font-size:.73rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.04em;">Hasta</label>
                <input type="date" name="hasta" id="filtro-hasta" value="{{ $hasta }}" class="form-ctrl">
            </div>
            <div class="col-md-2">
                <a href="{{ route('valoraciones.index') }}" id="btn-limpiar-val"
                   style="display:inline-flex;align-items:center;gap:.3rem;font-size:.83rem;color:var(--color-principal);text-decoration:none;height:35px;">
                    <i class="bi bi-x-circle"></i> Limpiar
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Tabla --}}
<div class="tabla-card" id="tabla-container">
    @include('valoraciones._tabla')
</div>

@push('scripts')
<script>
(function () {
    var baseUrl    = '{{ route('valoraciones.index') }}';
    var form       = document.getElementById('form-filtros-val');
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

    ['filtro-estado', 'filtro-desde', 'filtro-hasta'].forEach(function(id) {
        document.getElementById(id).addEventListener('change', function() {
            cargarTabla(baseUrl + '?' + getParams());
        });
    });

    buscar.addEventListener('input', function() {
        clearTimeout(timer);
        timer = setTimeout(function() { cargarTabla(baseUrl + '?' + getParams()); }, 500);
    });

    document.getElementById('btn-limpiar-val').addEventListener('click', function(e) {
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
