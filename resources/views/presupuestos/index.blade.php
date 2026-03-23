@extends('layouts.app')
@section('titulo', 'Presupuestos')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .form-input { border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; }
    .form-input:focus { border-color:var(--color-principal); }
    .form-select { border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; }
    .form-select:focus { border-color:var(--color-principal); }
    .tabla-header th { background:var(--color-muy-claro); padding:.55rem .9rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); border-bottom:2px solid var(--color-muy-claro); text-align:left; white-space:nowrap; }
    .tabla-fila td { padding:.6rem .9rem; border-bottom:1px solid var(--fondo-borde); font-size:.875rem; vertical-align:middle; }
    .tabla-fila:hover td { background:var(--fondo-card-alt); }
    .badge-estado { padding:.2rem .65rem; border-radius:20px; font-size:.7rem; font-weight:700; display:inline-flex; align-items:center; gap:.25rem; }
    .por-vencer { background:#fff7ed; border:1px solid #fed7aa; border-radius:6px; padding:.15rem .45rem; font-size:.7rem; font-weight:600; color:#c2410c; }
    #tabla-container { min-height:100px; transition:opacity .15s; }
    #tabla-container.cargando { opacity:.4; pointer-events:none; }
</style>
@endpush

@section('contenido')

{{-- Flash --}}
@if(session('exito'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:8px;padding:.7rem 1rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;padding:.7rem 1rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
</div>
@endif

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1.25rem;">
    <div>
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">
            <i class="bi bi-file-earmark-text" style="color:var(--color-principal);"></i> Presupuestos
        </h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">Gestión de presupuestos de tratamiento</p>
    </div>
    <a href="{{ route('presupuestos.create') }}" class="btn-morado">
        <i class="bi bi-plus-lg"></i> Nuevo Presupuesto
    </a>
</div>

{{-- Filtros --}}
<form id="form-filtros-pres" method="GET" style="background:#fff;border:1px solid var(--color-muy-claro);border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.25rem;display:flex;flex-wrap:wrap;gap:.75rem;align-items:flex-end;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
    <div style="flex:1;min-width:200px;">
        <label style="font-size:.75rem;font-weight:600;color:var(--color-hover);display:block;margin-bottom:.3rem;">Buscar</label>
        <input type="text" name="buscar" id="filtro-buscar" class="form-input" style="width:100%;"
               value="{{ request('buscar') }}" placeholder="Paciente o N° presupuesto…">
    </div>
    <div style="min-width:160px;">
        <label style="font-size:.75rem;font-weight:600;color:var(--color-hover);display:block;margin-bottom:.3rem;">Estado</label>
        <select name="estado" id="filtro-estado" class="form-select">
            <option value="">Todos los estados</option>
            @foreach(['borrador','enviado','aprobado','rechazado','vencido'] as $est)
            <option value="{{ $est }}" {{ request('estado') === $est ? 'selected' : '' }}>{{ ucfirst($est) }}</option>
            @endforeach
        </select>
    </div>
    <a href="{{ route('presupuestos.index') }}" id="btn-limpiar-pres"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem .9rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;height:38px;">
        <i class="bi bi-x"></i> Limpiar
    </a>
</form>

{{-- Tabla --}}
<div id="tabla-container" style="background:#fff;border:1px solid var(--color-muy-claro);border-radius:12px;overflow:hidden;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
    @include('presupuestos._tabla')
</div>

@push('scripts')
<script>
(function () {
    var baseUrl    = '{{ route('presupuestos.index') }}';
    var form       = document.getElementById('form-filtros-pres');
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

    document.getElementById('filtro-estado').addEventListener('change', function() {
        cargarTabla(baseUrl + '?' + getParams());
    });

    buscar.addEventListener('input', function() {
        clearTimeout(timer);
        timer = setTimeout(function() { cargarTabla(baseUrl + '?' + getParams()); }, 500);
    });

    document.getElementById('btn-limpiar-pres').addEventListener('click', function(e) {
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
