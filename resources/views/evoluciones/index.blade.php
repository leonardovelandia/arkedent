@extends('layouts.app')
@section('titulo', 'Evoluciones')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-outline-morado { background:transparent; color:var(--color-principal); border:1px solid var(--color-principal); border-radius:8px; padding:.45rem 1rem; font-size:.82rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-outline-morado:hover { background:var(--color-muy-claro); color:var(--color-hover); }

    .tabla-evol { width:100%; border-collapse:separate; border-spacing:0; font-size:.875rem; }
    .tabla-evol thead th { background:var(--color-muy-claro); color:var(--color-hover); font-weight:700; font-size:.78rem; text-transform:uppercase; letter-spacing:.04em; padding:.7rem 1rem; border-bottom:2px solid var(--color-muy-claro); }
    .tabla-evol thead th:first-child { border-radius:8px 0 0 0; }
    .tabla-evol thead th:last-child  { border-radius:0 8px 0 0; }
    .tabla-evol tbody tr:hover { background:var(--fondo-card-alt); }
    .tabla-evol tbody td { padding:.75rem 1rem; border-bottom:1px solid var(--fondo-borde); vertical-align:middle; }

    .avatar-iniciales { width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; font-size:.75rem; font-weight:700; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; }

    .badge-dientes { background:var(--color-muy-claro); color:var(--color-hover); border-radius:20px; padding:.18rem .65rem; font-size:.72rem; font-weight:600; }

    .btn-accion { background:none; border:1px solid var(--color-muy-claro); border-radius:7px; width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; color:var(--color-principal); font-size:.9rem; transition:background .13s; text-decoration:none; }
    .btn-accion:hover { background:var(--color-muy-claro); color:var(--color-hover); }

    .search-wrap { display:flex; gap:.75rem; align-items:flex-end; flex-wrap:wrap; }
    .search-field { display:flex; flex-direction:column; gap:.3rem; }
    .search-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--color-hover); }
    .search-input-wrap { position:relative; display:flex; align-items:center; }
    .search-input-wrap i { position:absolute; left:.75rem; color:#9ca3af; font-size:.9rem; pointer-events:none; }
    .search-input { border:1px solid var(--color-muy-claro); border-radius:8px; padding:.5rem .9rem .5rem 2.2rem; font-size:.875rem; outline:none; width:300px; transition:border-color .15s,box-shadow .15s; }
    .search-input:focus { border-color:var(--color-principal); box-shadow:0 0 0 3px var(--sombra-principal); }

    .filtro-pac { background:var(--color-muy-claro); border:1px solid var(--color-muy-claro); border-radius:8px; padding:.4rem .85rem; font-size:.8rem; color:var(--color-hover); display:inline-flex; align-items:center; gap:.4rem; }

    .vacio-msg { text-align:center; padding:3rem 1rem; color:#9ca3af; }
    .vacio-msg i { font-size:2.5rem; color:var(--color-acento-activo); display:block; margin-bottom:.75rem; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
    <div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
        <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
    </div>
@endif

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo">Evoluciones</h1>
        <p class="page-subtitulo">Registro de evoluciones clínicas por sesión</p>
    </div>
    <a href="{{ route('evoluciones.create') }}" class="btn-morado">
        <i class="bi bi-clipboard2-plus"></i> Nueva Evolución
    </a>
</div>

{{-- Buscador --}}
<div class="card-sistema mb-3">
    <form id="form-buscar" method="GET" action="{{ route('evoluciones.index') }}" class="search-wrap">
        @if($pacienteFiltro)
            <input type="hidden" name="paciente_id" value="{{ $pacienteFiltro->id }}">
        @endif
        <div class="search-field">
            <span class="search-label"><i class="bi bi-search"></i> Buscar Evolución</span>
            <div class="search-input-wrap">
                <i class="bi bi-search"></i>
                <input type="text" id="input-buscar" name="buscar" class="search-input"
                       placeholder="Paciente o procedimiento..."
                       value="{{ $buscar }}" autocomplete="off">
            </div>
        </div>
        @if($buscar || $pacienteFiltro)
            <div class="search-field" style="justify-content:flex-end;">
                <span class="search-label" style="opacity:0">—</span>
                <a href="{{ route('evoluciones.index') }}" class="btn-outline-morado">
                    <i class="bi bi-x"></i> Limpiar
                </a>
            </div>
        @endif
    </form>
    @if($pacienteFiltro)
        <div style="margin-top:.65rem;">
            <span class="filtro-pac">
                <i class="bi bi-person-fill"></i>
                Mostrando evoluciones de: <strong>{{ $pacienteFiltro->nombre_completo }}</strong>
            </span>
        </div>
    @endif
</div>

{{-- Tabla --}}
<div class="card-sistema" id="tabla-container" style="padding:0;overflow:hidden;">
    @include('evoluciones._tabla')
</div>

@push('scripts')
<script>
(function () {
    var baseUrl    = '{{ route('evoluciones.index') }}';
    var form       = document.getElementById('form-buscar');
    var contenedor = document.getElementById('tabla-container');
    var input      = document.getElementById('input-buscar');
    var timer;

    function getParams() {
        return new URLSearchParams(new FormData(form)).toString();
    }

    function cargarTabla(url) {
        contenedor.style.opacity = '.4';
        contenedor.style.pointerEvents = 'none';
        history.replaceState(null, '', url);
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.text(); })
            .then(function(html) {
                contenedor.innerHTML = html;
                contenedor.style.opacity = '';
                contenedor.style.pointerEvents = '';
                bindPaginacion();
            })
            .catch(function() {
                contenedor.style.opacity = '';
                contenedor.style.pointerEvents = '';
            });
    }

    function bindPaginacion() {
        contenedor.querySelectorAll('.pagination a').forEach(function(a) {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                cargarTabla(this.href);
            });
        });
    }

    form.addEventListener('submit', function(e) { e.preventDefault(); });

    input.addEventListener('input', function () {
        var pos = this.selectionStart;
        this.value = this.value.toLowerCase().replace(/\b\w/g, function(l){ return l.toUpperCase(); });
        this.setSelectionRange(pos, pos);
        clearTimeout(timer);
        timer = setTimeout(function() { cargarTabla(baseUrl + '?' + getParams()); }, 400);
    });

    var btnLimpiar = document.querySelector('a[href="{{ route('evoluciones.index') }}"].btn-outline-morado');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function(e) {
            e.preventDefault();
            input.value = '';
            cargarTabla(baseUrl);
        });
    }

    bindPaginacion();
})();
</script>
@endpush

@endsection
