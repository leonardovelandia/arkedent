@extends('layouts.app')
@section('titulo', 'Historia Clínica')

@push('estilos')
<style>
    .btn-morado { background: linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-outline-morado { background:transparent; color:var(--color-principal); border:1px solid var(--color-principal); border-radius:8px; padding:.45rem 1rem; font-size:.82rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-outline-morado:hover { background:var(--color-muy-claro); color:var(--color-hover); }

    .tabla-hist { width:100%; border-collapse:separate; border-spacing:0; font-size:.875rem; }
    .tabla-hist thead th { background:var(--color-muy-claro); color:var(--color-hover); font-weight:700; font-size:.78rem; text-transform:uppercase; letter-spacing:.04em; padding:.7rem 1rem; border-bottom:2px solid var(--color-muy-claro); }
    .tabla-hist thead th:first-child { border-radius:8px 0 0 0; }
    .tabla-hist thead th:last-child  { border-radius:0 8px 0 0; }
    .tabla-hist tbody tr:hover { background:var(--fondo-card-alt); }
    .tabla-hist tbody td { padding:.75rem 1rem; border-bottom:1px solid var(--fondo-borde); vertical-align:middle; }

    .avatar-iniciales { width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; font-size:.75rem; font-weight:700; display:inline-flex; align-items:center; justify-content:center; }

    .btn-accion { background:none; border:1px solid var(--color-muy-claro); border-radius:7px; width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; color:var(--color-principal); font-size:.9rem; transition:background .13s; text-decoration:none; }
    .btn-accion:hover { background:var(--color-muy-claro); color:var(--color-hover); }

    .search-wrap { display:flex; gap:.75rem; align-items:flex-end; flex-wrap:wrap; }
    .search-field { display:flex; flex-direction:column; gap:.3rem; }
    .search-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--color-hover); }
    .search-input-wrap { position:relative; display:flex; align-items:center; }
    .search-input-wrap i { position:absolute; left:.75rem; color:#9ca3af; font-size:.9rem; pointer-events:none; }
    .search-input { border:1px solid var(--color-muy-claro); border-radius:8px; padding:.5rem .9rem .5rem 2.2rem; font-size:.875rem; outline:none; width:300px; transition:border-color .15s,box-shadow .15s; }
    .search-input:focus { border-color:var(--color-principal); box-shadow:0 0 0 3px var(--sombra-principal); }

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
@if(session('info'))
    <div class="alerta-flash" style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;">
        <i class="bi bi-info-circle-fill"></i> {{ session('info') }}
    </div>
@endif

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo">Historia Clínica</h1>
        <p class="page-subtitulo">Registro de historias clínicas de pacientes</p>
    </div>
    <a href="{{ route('historias.create') }}" class="btn-morado">
        <i class="bi bi-journal-plus"></i> Nueva Historia
    </a>
</div>

{{-- Buscador --}}
<div class="card-sistema mb-3">
    <form id="form-buscar" method="GET" action="{{ route('historias.index') }}" class="search-wrap">
        <div class="search-field">
            <span class="search-label"><i class="bi bi-search"></i> Buscar Paciente</span>
            <div class="search-input-wrap">
                <i class="bi bi-search"></i>
                <input type="text" id="input-buscar" name="buscar" class="search-input"
                       placeholder="Nombre, apellido o documento..."
                       value="{{ request('buscar') }}" autocomplete="off">
            </div>
        </div>
        @if(request('buscar'))
            <div class="search-field" style="justify-content:flex-end;">
                <span class="search-label" style="opacity:0">—</span>
                <a href="{{ route('historias.index') }}" class="btn-outline-morado">
                    <i class="bi bi-x"></i> Limpiar
                </a>
            </div>
        @endif
    </form>
</div>

{{-- Tabla --}}
<div id="contenedor-tabla" class="card-sistema" style="padding:0;overflow:hidden;">
    @if($historias->isEmpty())
        <div class="vacio-msg">
            <i class="bi bi-journal-medical"></i>
            <p style="font-weight:600;color:#4b5563;">No se encontraron historias clínicas</p>
            <a href="{{ route('historias.create') }}" class="btn-morado mt-2">
                <i class="bi bi-journal-plus"></i> Crear primera historia
            </a>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table class="tabla-hist">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>N° Historia</th>
                        <th>Fecha Apertura</th>
                        <th>Motivo de Consulta</th>
                        <th style="text-align:center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historias as $h)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:.65rem;">
                                <span class="avatar-iniciales">
                                    {{ strtoupper(substr($h->paciente->nombre,0,1)) }}{{ strtoupper(substr($h->paciente->apellido,0,1)) }}
                                </span>
                                <div>
                                    <div style="font-weight:600;color:#1c2b22;">{{ $h->paciente->nombre_completo }}</div>
                                    <div style="font-size:.78rem;color:#6b7280;">{{ $h->paciente->numero_documento }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-family:monospace;font-weight:700;color:var(--color-principal);background:var(--color-muy-claro);padding:.15rem .5rem;border-radius:6px;font-size:.82rem;">
                                {{ $h->numero_historia ?? ('#'.$h->id) }}
                            </span>
                        </td>
                        <td>{{ $h->fecha_apertura->format('d/m/Y') }}</td>
                        <td style="max-width:250px;">
                            <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;font-size:.84rem;color:#4b5563;">
                                {{ $h->motivo_consulta }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;justify-content:center;gap:.35rem;">
                                <a href="{{ route('historias.show', $h) }}" class="btn-accion" title="Ver historia">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('historias.edit', $h) }}" class="btn-accion" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('historias.pdf', $h) }}" title="Ver PDF" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border:1px solid var(--color-muy-claro);border-radius:6px;color:var(--color-principal);text-decoration:none;">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($historias->hasPages())
            <div style="padding:1rem 1.5rem;border-top:1px solid var(--fondo-borde);">
                {{ $historias->links() }}
            </div>
        @endif
    @endif
</div>

<script>
(function () {
    var input      = document.getElementById('input-buscar');
    var form       = document.getElementById('form-buscar');
    var contenedor = document.getElementById('contenedor-tabla');
    var timer;

    form.addEventListener('submit', function(e){ e.preventDefault(); });

    function buscar(ms) {
        clearTimeout(timer);
        timer = setTimeout(function () {
            var params = new URLSearchParams({ buscar: input.value });
            contenedor.style.opacity = '0.5';
            fetch('{{ route('historias.index') }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.text(); })
            .then(function(html) {
                var doc   = new DOMParser().parseFromString(html, 'text/html');
                var nuevo = doc.getElementById('contenedor-tabla');
                if (nuevo) contenedor.innerHTML = nuevo.innerHTML;
                contenedor.style.opacity = '1';
            })
            .catch(function() { contenedor.style.opacity = '1'; });
        }, ms);
    }

    input.addEventListener('input', function () { buscar(350); });
})();
</script>
@endsection
