@extends('layouts.app')
@section('titulo', 'Imágenes Clínicas')

@push('estilos')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
.ts-wrapper.single .ts-control { height:38px; border:1px solid #e5e7eb; border-radius:8px; font-size:.88rem; color:#1c2b22; padding:0 10px; cursor:pointer; box-shadow:none; background:#fff; }
.ts-wrapper.single.focus .ts-control, .ts-wrapper.single.input-active .ts-control { border-color:var(--color-principal)!important; box-shadow:0 0 0 3px rgba(107,33,168,.08)!important; }
.ts-dropdown { border:1.5px solid var(--color-principal); border-radius:8px; box-shadow:0 8px 24px rgba(107,33,168,.12); font-size:.85rem; z-index:9999; }
.ts-dropdown .active { background:var(--color-muy-claro); color:var(--color-principal); }
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);s }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-desactivado { opacity:.45; pointer-events:none; cursor:not-allowed; filter:grayscale(.4); }
    .btn-out { background:transparent; color:var(--color-principal); border:1px solid var(--color-principal); border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-out:hover { background:var(--color-muy-claro); color:var(--color-hover); }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; }

    .img-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); transition:transform .18s,box-shadow .18s; }
    .img-card:hover { transform:translateY(-3px); box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .img-thumb { width:100%; aspect-ratio:4/3; object-fit:cover; background:#f3f4f6; display:block; }
    .img-thumb-placeholder { width:100%; aspect-ratio:4/3; background:var(--color-muy-claro); display:flex; align-items:center; justify-content:center; font-size:2.5rem; color:var(--color-acento-activo); }
    .img-meta { padding:.75rem; }
    .img-badge { display:inline-flex; align-items:center; gap:.25rem; background:var(--color-muy-claro); color:var(--color-principal); border-radius:20px; padding:.15rem .55rem; font-size:.68rem; font-weight:700; margin-bottom:.35rem; }
    .img-titulo { font-size:.85rem; font-weight:600; color:#1c2b22; margin-bottom:.2rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .img-paciente { font-size:.78rem; color:#6b7280; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .img-fecha { font-size:.75rem; color:#9ca3af; margin-top:.2rem; }
    .img-acciones { display:flex; gap:.4rem; padding:.6rem .75rem; border-top:1px solid var(--fondo-borde); }

    .filtros-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1rem 1.25rem; margin-bottom:1.25rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .form-ctrl { width:100%; border:1px solid #d1d5db; border-radius:8px; padding:.45rem .75rem; font-size:.875rem; color:#374151; background:#fff; }
    .form-ctrl:focus { outline:none; border-color:var(--color-principal); box-shadow:0 0 0 3px var(--sombra-principal); }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

{{-- Encabezado --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h1 style="font-family:var(--fuente-titulos);font-size:1.5rem;font-weight:700;color:#1c2b22;margin:0;">
            <i class="bi bi-images me-2" style="color:var(--color-principal);"></i>Imágenes Clínicas
        </h1>
        <p style="font-size:.85rem;color:#9ca3af;margin:.2rem 0 0;">Registro fotográfico y radiológico de pacientes</p>
    </div>
    <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
        {{-- Botón Ver Galería --}}
        <a id="btn-galeria"
           href="{{ $pacienteId ? route('imagenes.galeria', $pacienteId) : '#' }}"
           class="{{ $pacienteId ? 'btn-morado' : 'btn-morado btn-desactivado' }}"
           title="{{ $pacienteId ? '' : 'Selecciona un paciente primero' }}">
            <i class="bi bi-grid-3x3-gap"></i> Ver Galería
        </a>
        {{-- Botón Ver Comparativo --}}
        <a id="btn-comparativo"
           href="{{ $pacienteId ? route('imagenes.comparativo', $pacienteId) : '#' }}"
           class="{{ $pacienteId ? 'btn-morado' : 'btn-morado btn-desactivado' }}"
           title="{{ $pacienteId ? '' : 'Selecciona un paciente primero' }}">
            <i class="bi bi-layout-split"></i> Ver Comparativo
        </a>
        {{-- Subir Imágenes --}}
        <a href="{{ route('imagenes.create') }}" class="btn-morado">
            <i class="bi bi-cloud-upload"></i> Subir Imágenes
        </a>
    </div>
</div>

{{-- Filtros --}}
<div class="filtros-card">
    <form id="form-filtros-img" method="GET" action="{{ route('imagenes.index') }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.04em;">Paciente</label>
                <select name="paciente_id" id="filtro-paciente">
                    <option value="">— Todos los pacientes —</option>
                    @foreach($pacientes as $pac)
                        <option value="{{ $pac->id }}" {{ ($pacienteId == $pac->id) ? 'selected' : '' }}>
                            {{ $pac->nombre_completo }} — {{ $pac->numero_historia }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.04em;">Tipo</label>
                <select name="tipo" id="filtro-tipo" class="form-ctrl">
                    <option value="">Todos los tipos</option>
                    @foreach($tipos as $val => $label)
                    <option value="{{ $val }}" {{ $tipo == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.04em;">Desde</label>
                <input type="date" name="desde" id="filtro-desde" value="{{ $desde }}" class="form-ctrl">
            </div>
            <div class="col-md-2">
                <label style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.04em;">Hasta</label>
                <input type="date" name="hasta" id="filtro-hasta" value="{{ $hasta }}" class="form-ctrl">
            </div>
            <div class="col-md-1">
                <a href="{{ route('imagenes.index') }}" id="btn-limpiar-img"
                   style="display:inline-flex;align-items:center;gap:.3rem;font-size:.83rem;color:var(--color-principal);text-decoration:none;height:35px;">
                    <i class="bi bi-x-circle"></i> Limpiar
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Grid de imágenes --}}
<div id="grid-container">
    @include('imagenes._grid')
</div>

@push('scripts')
<script>
(function () {
    var baseUrl    = '{{ route('imagenes.index') }}';
    var form       = document.getElementById('form-filtros-img');
    var contenedor = document.getElementById('grid-container');
    var timer;

    function getParams() {
        return new URLSearchParams(new FormData(form)).toString();
    }

    function cargarGrid(url) {
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
                cargarGrid(this.href);
            });
        });
    }

    ['filtro-tipo', 'filtro-desde', 'filtro-hasta'].forEach(function(id) {
        document.getElementById(id).addEventListener('change', function() {
            cargarGrid(baseUrl + '?' + getParams());
        });
    });

    document.getElementById('btn-limpiar-img').addEventListener('click', function(e) {
        e.preventDefault();
        if (window._tsPaciente) window._tsPaciente.clear();
        form.querySelectorAll('select').forEach(function(s) { s.value = ''; });
        form.querySelectorAll('input[type="date"]').forEach(function(d) { d.value = ''; });
        actualizarBotonesPaciente('');
        cargarGrid(baseUrl);
    });

    bindPaginacion();
})();
</script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
var baseUrl2 = '{{ route('imagenes.index') }}';
var form2    = document.getElementById('form-filtros-img');
var cont2    = document.getElementById('grid-container');

function cargarGrid2(url) {
    cont2.style.opacity = '.4';
    cont2.style.pointerEvents = 'none';
    history.replaceState(null, '', url);
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text()).then(html => {
            cont2.innerHTML = html;
            cont2.style.opacity = '';
            cont2.style.pointerEvents = '';
        });
}

function actualizarBotonesPaciente(pacienteId) {
    var btnGaleria     = document.getElementById('btn-galeria');
    var btnComparativo = document.getElementById('btn-comparativo');
    var baseGaleria    = '{{ url('imagenes/galeria') }}/';
    var baseCompar     = '{{ url('imagenes/comparativo') }}/';

    if (pacienteId) {
        btnGaleria.href     = baseGaleria + pacienteId;
        btnGaleria.title    = '';
        btnComparativo.href  = baseCompar + pacienteId;
        btnComparativo.title = '';
        btnGaleria.classList.remove('btn-desactivado');
        btnComparativo.classList.remove('btn-desactivado');
    } else {
        btnGaleria.href     = '#';
        btnGaleria.title    = 'Selecciona un paciente primero';
        btnComparativo.href  = '#';
        btnComparativo.title = 'Selecciona un paciente primero';
        btnGaleria.classList.add('btn-desactivado');
        btnComparativo.classList.add('btn-desactivado');
    }
}

window._tsPaciente = new TomSelect('#filtro-paciente', {
    placeholder: 'Buscar paciente...',
    searchField: ['text'],
    maxOptions: 200,
    plugins: { clear_button: { title: 'Quitar paciente' } },
    onChange: function(value) {
        actualizarBotonesPaciente(value);
        cargarGrid2(baseUrl2 + '?' + new URLSearchParams(new FormData(form2)).toString());
    }
});
</script>
@endpush

@endsection
