@extends('layouts.app')
@section('titulo', 'Consentimientos Informados')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    .search-bar { display:flex; gap:.75rem; align-items:flex-end; flex-wrap:wrap; }
    .search-field { display:flex; flex-direction:column; gap:.3rem; }
    .search-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--color-hover); }
    .search-input-wrap { position:relative; display:flex; align-items:center; }
    .search-input-wrap i { position:absolute; left:.75rem; color:#9ca3af; font-size:.9rem; pointer-events:none; }
    .search-input { border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.42rem .75rem .42rem 2.1rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; min-width:260px; transition:border-color .15s; }
    .search-input:focus { border-color:var(--color-principal); }
    .select-filtro { border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.42rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; min-width:160px; transition:border-color .15s; }
    .select-filtro:focus { border-color:var(--color-principal); }

    .tabla-wrap { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .tabla-cons { width:100%; border-collapse:collapse; font-size:.875rem; }
    .tabla-cons thead th { background:var(--color-muy-claro); color:var(--color-hover); font-weight:700; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; padding:.65rem 1rem; border-bottom:2px solid var(--color-muy-claro); white-space:nowrap; }
    .tabla-cons tbody tr { transition:background .12s; }
    .tabla-cons tbody tr:hover { background:var(--fondo-card-alt); }
    .tabla-cons tbody td { padding:.6rem 1rem; border-bottom:1px solid var(--fondo-borde); vertical-align:middle; }
    .tabla-cons tbody tr:last-child td { border-bottom:none; }

    .badge-estado { display:inline-flex; align-items:center; gap:.3rem; padding:.22rem .65rem; border-radius:20px; font-size:.73rem; font-weight:700; white-space:nowrap; }
    .badge-firmado  { background:#D4EDDA; color:#155724; }
    .badge-pendiente{ background:#FFF3CD; color:#856404; }

    .pac-nombre { font-weight:600; color:#1c2b22; }
    .pac-doc { font-size:.74rem; color:#9ca3af; }

    .accion-btn { background:none; border:1px solid var(--color-muy-claro); border-radius:6px; width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; font-size:.85rem; transition:background .12s; text-decoration:none; color:var(--color-principal); }
    .accion-btn:hover { background:var(--color-muy-claro); color:var(--color-hover); }
    .accion-btn.rojo { color:#dc2626; border-color:#fecdd3; }
    .accion-btn.rojo:hover { background:#fef2f2; }
    .accion-btn.verde { color:#166534; border-color:#bbf7d0; }
    .accion-btn.verde:hover { background:#dcfce7; }

    .empty-state { text-align:center; padding:3rem 1rem; color:#9ca3af; }
    .empty-state i { font-size:2.5rem; color:var(--color-acento-activo); display:block; margin-bottom:.75rem; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif
@if(session('error'))
<div class="alerta-flash" style="background:#fef2f2;color:#dc2626;border:1px solid #fecdd3;">
    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
</div>
@endif

{{-- Encabezado --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Consentimientos Informados</h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">Gestión de consentimientos y firmas</p>
    </div>
    <a href="{{ route('consentimientos.create') }}" class="btn-morado">
        <i class="bi bi-file-earmark-plus"></i> Nuevo Consentimiento
    </a>
</div>

{{-- Filtros --}}
<div style="background:#fff;border:1px solid var(--color-muy-claro);border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.25rem;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
    <form id="form-buscar" method="GET" action="{{ route('consentimientos.index') }}" class="search-bar">
        <div class="search-field" style="flex:1;min-width:240px;">
            <span class="search-label"><i class="bi bi-search"></i> Buscar</span>
            <div class="search-input-wrap">
                <i class="bi bi-search"></i>
                <input type="text" id="input-buscar" name="buscar" class="search-input" style="width:100%;"
                       placeholder="Paciente o nombre del consentimiento…"
                       value="{{ request('buscar') }}" autocomplete="off">
            </div>
        </div>
        <div class="search-field">
            <span class="search-label">Estado</span>
            <select id="select-estado" name="estado" class="select-filtro">
                <option value="">Todos</option>
                <option value="firmado"   {{ request('estado') === 'firmado'   ? 'selected' : '' }}>Firmado</option>
                <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente firma</option>
            </select>
        </div>
        <div id="wrap-limpiar" class="search-field" style="justify-content:flex-end;display:none;">
            <span class="search-label" style="opacity:0;">—</span>
            <a href="{{ route('consentimientos.index') }}" class="btn-morado"
               style="background:transparent;color:var(--color-principal);border:1px solid var(--color-principal);"
               onclick="limpiarFiltros(event)">
                <i class="bi bi-x"></i> Limpiar
            </a>
        </div>
    </form>
</div>

{{-- Tabla --}}
<div id="contenedor-tabla" class="tabla-wrap">
    <div style="overflow-x:auto;">
    <table class="tabla-cons">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>N° CON</th>
                <th>Consentimiento</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Doctor</th>
                <th style="text-align:center;width:110px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($consentimientos as $c)
        <tr>
            <td>
                <div class="pac-nombre">{{ $c->paciente->nombre_completo }}</div>
                <div class="pac-doc">{{ $c->paciente->numero_documento }}</div>
            </td>
            <td>
                <span style="font-family:monospace;font-weight:700;color:#c2410c;background:#ffedd5;padding:.15rem .5rem;border-radius:6px;font-size:.82rem;">
                    {{ $c->numero_consentimiento ?? ('#'.$c->id) }}
                </span>
            </td>
            <td style="max-width:220px;">
                <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-weight:500;color:#1c2b22;" title="{{ $c->nombre }}">
                    {{ $c->nombre }}
                </span>
            </td>
            <td style="white-space:nowrap;color:#4b5563;font-size:.83rem;">
                {{ $c->fecha_generacion->translatedFormat('d M Y') }}
            </td>
            <td>
                @if($c->firmado)
                <span class="badge-estado badge-firmado"><i class="bi bi-patch-check-fill"></i> Firmado</span>
                @else
                <span class="badge-estado badge-pendiente"><i class="bi bi-clock"></i> Pendiente firma</span>
                @endif
            </td>
            <td style="font-size:.82rem;color:#6b7280;">{{ $c->doctor?->name ?? '—' }}</td>
            <td style="text-align:center;">
                <div style="display:inline-flex;gap:.3rem;">
                    <a href="{{ route('consentimientos.show', $c) }}" class="accion-btn" title="Ver / Firmar">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('consentimientos.pdf', $c) }}" class="accion-btn verde" title="Ver PDF" target="_blank">
                        <i class="bi bi-filetype-pdf"></i>
                    </a>
                    @if(!$c->firmado)
                    <form method="POST" action="{{ route('consentimientos.destroy', $c) }}" style="display:inline;"
                          onsubmit="return confirm('¿Eliminar este consentimiento?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="accion-btn rojo" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7">
                <div class="empty-state">
                    <i class="bi bi-file-earmark-x"></i>
                    <p style="font-weight:600;color:#4b5563;margin-bottom:.25rem;">Sin consentimientos registrados</p>
                    @if(request('buscar') || request('estado'))
                    <p style="font-size:.84rem;color:#9ca3af;">Ningún resultado para los filtros aplicados.</p>
                    @else
                    <a href="{{ route('consentimientos.create') }}" class="btn-morado mt-2" style="display:inline-flex;">
                        <i class="bi bi-plus-circle"></i> Crear primer consentimiento
                    </a>
                    @endif
                </div>
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
    </div>
    @if($consentimientos->hasPages())
    <div style="padding:.75rem 1rem;border-top:1px solid var(--fondo-borde);">
        {{ $consentimientos->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
(function () {
    var input    = document.getElementById('input-buscar');
    var selEstado= document.getElementById('select-estado');
    var form     = document.getElementById('form-buscar');
    var contenedor = document.getElementById('contenedor-tabla');
    var wrapLimp = document.getElementById('wrap-limpiar');
    var timer;

    function hayFiltros() { return input.value.trim() || selEstado.value; }
    function actualizarLimpiar() { wrapLimp.style.display = hayFiltros() ? 'flex' : 'none'; }

    form.addEventListener('submit', function(e) { e.preventDefault(); });

    function buscar(ms) {
        clearTimeout(timer);
        actualizarLimpiar();
        timer = setTimeout(function () {
            var params = new URLSearchParams({ buscar: input.value, estado: selEstado.value });
            contenedor.style.opacity = '0.5';
            fetch('{{ route('consentimientos.index') }}?' + params.toString(), {
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

    input.addEventListener('input', function () {
        var pos = this.selectionStart;
        this.value = this.value.toLowerCase().replace(/\b\w/g, function(l) { return l.toUpperCase(); });
        this.setSelectionRange(pos, pos);
        buscar(350);
    });
    selEstado.addEventListener('change', function () { buscar(0); });
    actualizarLimpiar();
})();

function limpiarFiltros(e) {
    e.preventDefault();
    document.getElementById('input-buscar').value  = '';
    document.getElementById('select-estado').value = '';
    document.getElementById('wrap-limpiar').style.display = 'none';
    document.getElementById('input-buscar').dispatchEvent(new Event('input'));
}
</script>
@endpush
