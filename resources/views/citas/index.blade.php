@extends('layouts.app')
@section('titulo', 'Citas')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    /* ── Barra de búsqueda (igual que pacientes) ── */
    .search-bar { display:flex; gap:.75rem; align-items:flex-end; flex-wrap:wrap; }
    .search-field { display:flex; flex-direction:column; gap:.3rem; }
    .search-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--color-hover); }
    .search-input-wrap { position:relative; display:flex; align-items:center; }
    .search-input-wrap i { position:absolute; left:.75rem; color:#9ca3af; font-size:.9rem; pointer-events:none; }
    .search-input { border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.42rem .75rem .42rem 2.1rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; min-width:240px; transition:border-color .15s; }
    .search-input:focus { border-color:var(--color-principal); }
    .select-filtro { border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.42rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; min-width:160px; transition:border-color .15s; }
    .select-filtro:focus { border-color:var(--color-principal); }
    .input-fecha { border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.42rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; min-width:150px; transition:border-color .15s; }
    .input-fecha:focus { border-color:var(--color-principal); }

    /* ── Tabla ── */
    .tabla-wrap { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .tabla-citas { width:100%; border-collapse:collapse; font-size:.875rem; }
    .tabla-citas thead th { background:var(--color-muy-claro); color:var(--color-hover); font-weight:700; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; padding:.65rem 1rem; border-bottom:2px solid var(--color-muy-claro); white-space:nowrap; }
    .tabla-citas tbody tr { transition:background .12s; }
    .tabla-citas tbody tr:hover { background:var(--fondo-card-alt); }
    .tabla-citas tbody td { padding:.6rem 1rem; border-bottom:1px solid var(--fondo-borde); vertical-align:middle; }
    .tabla-citas tbody tr:last-child td { border-bottom:none; }

    .badge-estado { display:inline-block; padding:.22rem .65rem; border-radius:20px; font-size:.73rem; font-weight:700; white-space:nowrap; }
    .pac-nombre { font-weight:600; color:#1c2b22; font-size:.875rem; }
    .pac-doc { font-size:.74rem; color:#9ca3af; }

    .accion-btn { background:none; border:1px solid var(--color-muy-claro); border-radius:6px; width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; font-size:.85rem; transition:background .12s; text-decoration:none; color:var(--color-principal); }
    .accion-btn:hover { background:var(--color-muy-claro); color:var(--color-hover); }
    .accion-btn.verde { color:#166534; border-color:#bbf7d0; }
    .accion-btn.verde:hover { background:#dcfce7; }
    .accion-btn.rojo { color:#dc2626; border-color:#fecdd3; }
    .accion-btn.rojo:hover { background:#fef2f2; }

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

{{-- Encabezado --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Citas</h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">Gestión de citas y agenda</p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
        <a href="{{ route('citas.agenda') }}" class="btn-morado" style="background:transparent;color:var(--color-principal);border:1px solid var(--color-principal);">
            <i class="bi bi-calendar3-week"></i> Ver Agenda
        </a>
        <a href="{{ route('citas.create') }}" class="btn-morado">
            <i class="bi bi-plus-circle"></i> Nueva Cita
        </a>
    </div>
</div>

{{-- Barra de búsqueda y filtros --}}
<div style="background:#fff;border:1px solid var(--color-muy-claro);border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.25rem;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
    <form id="form-buscar" method="GET" action="{{ route('citas.index') }}" class="search-bar">
        {{-- Buscar paciente --}}
        <div class="search-field" style="flex:1;min-width:220px;">
            <span class="search-label"><i class="bi bi-search"></i> Buscar paciente</span>
            <div class="search-input-wrap">
                <i class="bi bi-search"></i>
                <input type="text"
                       id="input-buscar"
                       name="buscar"
                       class="search-input"
                       style="width:100%;"
                       placeholder="Nombre, apellido o documento…"
                       value="{{ request('buscar') }}"
                       autocomplete="off">
            </div>
        </div>

        {{-- Fecha --}}
        <div class="search-field">
            <span class="search-label"><i class="bi bi-calendar3"></i> Fecha</span>
            <input type="date"
                   id="input-fecha"
                   name="fecha"
                   class="input-fecha"
                   value="{{ request('fecha') }}">
        </div>

        {{-- Estado --}}
        <div class="search-field">
            <span class="search-label">Estado</span>
            <select id="select-estado" name="estado" class="select-filtro">
                <option value="">Todos los estados</option>
                @foreach(['pendiente'=>'Pendiente','confirmada'=>'Confirmada','en_proceso'=>'En proceso','atendida'=>'Atendida','cancelada'=>'Cancelada','no_asistio'=>'No asistió'] as $val => $lbl)
                <option value="{{ $val }}" {{ request('estado') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>

        {{-- Limpiar (aparece solo con filtros activos) --}}
        <div id="wrap-limpiar" class="search-field" style="justify-content:flex-end;display:none;">
            <span class="search-label" style="opacity:0;">—</span>
            <a href="{{ route('citas.index') }}" class="btn-morado" style="background:transparent;color:var(--color-principal);border:1px solid var(--color-principal);" onclick="limpiarFiltros(event)">
                <i class="bi bi-x"></i> Limpiar
            </a>
        </div>
    </form>
</div>

{{-- Tabla (con ID para AJAX) --}}
<div id="contenedor-tabla" class="tabla-wrap">
    <div style="overflow-x:auto;">
    <table class="tabla-citas">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>N° Cita</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Procedimiento</th>
                <th>Estado</th>
                <th>Doctor</th>
                <th style="text-align:center;width:120px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($citas as $cita)
        @php $color = $colores[$cita->estado] ?? ['bg'=>'#f3f4f6','texto'=>'#374151']; @endphp
        <tr>
            <td>
                <div class="pac-nombre">{{ $cita->paciente->nombre_completo }}</div>
                <div class="pac-doc">{{ $cita->paciente->numero_documento }}</div>
            </td>
            <td>
                <span style="font-family:monospace;font-weight:700;color:#166534;background:#dcfce7;padding:.15rem .5rem;border-radius:6px;font-size:.82rem;">
                    {{ $cita->numero_cita ?? ('#'.$cita->id) }}
                </span>
            </td>
            <td style="white-space:nowrap;color:#4b5563;">
                {{ $cita->fecha->translatedFormat('d M Y') }}
            </td>
            <td style="white-space:nowrap;color:#4b5563;">
                {{ $cita->hora_inicio }}
                @if($cita->hora_fin) <span style="color:#9ca3af;">– {{ $cita->hora_fin }}</span> @endif
            </td>
            <td style="max-width:200px;">
                <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $cita->procedimiento }}">
                    {{ $cita->procedimiento }}
                </span>
            </td>
            <td>
                <span class="badge-estado" style="background:{{ $color['bg'] }};color:{{ $color['texto'] }};">
                    {{ ucfirst(str_replace('_',' ',$cita->estado)) }}
                </span>
            </td>
            <td style="font-size:.82rem;color:#6b7280;">{{ $cita->doctor ? $cita->doctor->name : '—' }}</td>
            <td style="text-align:center;">
                <div style="display:inline-flex;gap:.3rem;">
                    <a href="{{ route('citas.show', $cita) }}" class="accion-btn" title="Ver detalle">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('citas.edit', $cita) }}" class="accion-btn" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </a>
                    @if($cita->estado === 'pendiente')
                    <form method="POST" action="{{ route('citas.confirmar', $cita) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="accion-btn verde" title="Confirmar">
                            <i class="bi bi-check-lg"></i>
                        </button>
                    </form>
                    @endif
                    @if(!in_array($cita->estado, ['cancelada','atendida']))
                    <button type="button" class="accion-btn rojo" title="Cancelar"
                        onclick="abrirModalCancelar({{ $cita->id }})">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8">
                <div class="empty-state">
                    <i class="bi bi-calendar-x"></i>
                    <p style="font-weight:600;color:#4b5563;margin-bottom:.25rem;">Sin citas registradas</p>
                    @if(request('buscar') || request('fecha') || request('estado'))
                    <p style="font-size:.84rem;color:#9ca3af;">Ningún resultado para los filtros aplicados.</p>
                    @else
                    <a href="{{ route('citas.create') }}" class="btn-morado mt-2" style="display:inline-flex;">
                        <i class="bi bi-plus-circle"></i> Agendar primera cita
                    </a>
                    @endif
                </div>
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
    </div>
    @if($citas->hasPages())
    <div style="padding:.75rem 1rem;border-top:1px solid var(--fondo-borde);">
        {{ $citas->links() }}
    </div>
    @endif
</div>

{{-- Modal Cancelar --}}
<div id="modal-cancelar" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:440px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative;">
        <button onclick="cerrarModalCancelar()" style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;">✕</button>
        <h5 style="font-weight:700;color:#1c2b22;margin-bottom:.35rem;"><i class="bi bi-x-circle" style="color:#dc2626;"></i> Cancelar cita</h5>
        <p style="font-size:.85rem;color:#6b7280;margin-bottom:1rem;">Indica el motivo de cancelación.</p>
        <form id="form-cancelar" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="font-size:.8rem;font-weight:600;color:#374151;display:block;margin-bottom:.3rem;">Motivo <span style="color:#dc2626;">*</span></label>
                <textarea name="motivo_cancelacion" rows="3" required style="width:100%;border:1px solid var(--color-muy-claro);border-radius:8px;padding:.5rem .75rem;font-size:.875rem;outline:none;resize:vertical;font-family:inherit;" placeholder="Ej: Paciente llamó para reagendar…"></textarea>
            </div>
            <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                <button type="button" onclick="cerrarModalCancelar()" style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;cursor:pointer;">Cerrar</button>
                <button type="submit" style="background:#dc2626;color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;cursor:pointer;font-weight:600;">
                    <i class="bi bi-x-lg"></i> Confirmar cancelación
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    var input     = document.getElementById('input-buscar');
    var selEstado = document.getElementById('select-estado');
    var inputFecha= document.getElementById('input-fecha');
    var form      = document.getElementById('form-buscar');
    var contenedor= document.getElementById('contenedor-tabla');
    var wrapLimp  = document.getElementById('wrap-limpiar');
    var timer;

    function hayFiltros() {
        return input.value.trim() || selEstado.value || inputFecha.value;
    }

    function actualizarLimpiar() {
        wrapLimp.style.display = hayFiltros() ? 'flex' : 'none';
    }

    // Evitar submit tradicional del form
    form.addEventListener('submit', function(e) { e.preventDefault(); });

    function buscar(ms) {
        clearTimeout(timer);
        actualizarLimpiar();
        timer = setTimeout(function () {
            var params = new URLSearchParams({
                buscar: input.value,
                estado: selEstado.value,
                fecha : inputFecha.value
            });
            contenedor.style.opacity    = '0.5';
            contenedor.style.transition = 'opacity 0.15s';

            fetch('{{ route('citas.index') }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.text(); })
            .then(function(html) {
                var parser = new DOMParser();
                var doc    = parser.parseFromString(html, 'text/html');
                var nuevo  = doc.getElementById('contenedor-tabla');
                if (nuevo) contenedor.innerHTML = nuevo.innerHTML;
                contenedor.style.opacity = '1';
            })
            .catch(function() { contenedor.style.opacity = '1'; });
        }, ms);
    }

    // Búsqueda de texto — formato title-case igual que pacientes
    input.addEventListener('input', function () {
        var pos = this.selectionStart;
        this.value = this.value.toLowerCase().replace(/\b\w/g, function(l) { return l.toUpperCase(); });
        this.setSelectionRange(pos, pos);
        buscar(350);
    });

    selEstado.addEventListener('change', function () { buscar(0); });
    inputFecha.addEventListener('change', function () { buscar(0); });

    actualizarLimpiar();
})();

function limpiarFiltros(e) {
    e.preventDefault();
    document.getElementById('input-buscar').value  = '';
    document.getElementById('select-estado').value = '';
    document.getElementById('input-fecha').value   = '';
    document.getElementById('wrap-limpiar').style.display = 'none';
    document.getElementById('input-buscar').dispatchEvent(new Event('input'));
}

// Modal cancelar
function abrirModalCancelar(id) {
    document.getElementById('form-cancelar').action = '/citas/' + id + '/cancelar';
    document.getElementById('modal-cancelar').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function cerrarModalCancelar() {
    document.getElementById('modal-cancelar').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) { if(e.key === 'Escape') cerrarModalCancelar(); });
document.getElementById('modal-cancelar').addEventListener('click', function(e) { if(e.target === this) cerrarModalCancelar(); });
</script>
@endpush
