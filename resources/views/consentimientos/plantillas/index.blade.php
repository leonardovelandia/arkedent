@extends('layouts.app')
@section('titulo', 'Plantillas de Consentimiento')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    .tabla-wrap { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .tabla-pl { width:100%; border-collapse:collapse; font-size:.875rem; }
    .tabla-pl thead th { background:var(--color-muy-claro); color:var(--color-hover); font-weight:700; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; padding:.65rem 1rem; border-bottom:2px solid var(--color-muy-claro); }
    .tabla-pl tbody tr { transition:background .12s; }
    .tabla-pl tbody tr:hover { background:var(--fondo-card-alt); }
    .tabla-pl tbody td { padding:.65rem 1rem; border-bottom:1px solid var(--fondo-borde); vertical-align:middle; }
    .tabla-pl tbody tr:last-child td { border-bottom:none; }

    .badge-activa    { background:#d1fae5; color:#065f46; font-size:.72rem; font-weight:700; padding:.2rem .55rem; border-radius:20px; }
    .badge-inactiva  { background:#f3f4f6; color:#6b7280; font-size:.72rem; font-weight:700; padding:.2rem .55rem; border-radius:20px; }

    .accion-btn { background:none; border:1px solid var(--color-muy-claro); border-radius:6px; width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; font-size:.85rem; transition:background .12s; text-decoration:none; color:var(--color-principal); }
    .accion-btn:hover { background:var(--color-muy-claro); color:var(--color-hover); }
    .accion-btn.rojo { color:#dc2626; border-color:#fecdd3; }
    .accion-btn.rojo:hover { background:#fef2f2; }

    /* Modal */
    .modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:1050; align-items:center; justify-content:center; padding:1rem; }
    .modal-bg.show { display:flex; }
    .modal-box { background:#fff; border-radius:14px; padding:1.75rem; width:100%; max-width:760px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.25); }
    .modal-box h5 { font-weight:700; color:var(--color-hover); font-size:1rem; margin:0 0 1.25rem; padding-bottom:.6rem; border-bottom:2px solid var(--color-muy-claro); }

    .campo-wrap { margin-bottom:1rem; }
    .campo-lbl { font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--color-principal); display:block; margin-bottom:.3rem; }
    .campo-ctrl { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .8rem; font-size:.9rem; color:#1c2b22; background:#fff; outline:none; transition:border-color .15s; font-family:inherit; box-sizing:border-box; }
    .campo-ctrl:focus { border-color:var(--color-principal); }
    .campo-error { font-size:.75rem; color:#dc2626; margin-top:.2rem; display:block; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media(max-width:540px){ .form-row { grid-template-columns:1fr; } }

    .variables-hint { background:var(--fondo-card-alt); border:1px solid var(--fondo-borde); border-radius:8px; padding:.6rem .9rem; font-size:.78rem; color:var(--texto-secundario); margin-bottom:.75rem; line-height:1.6; }
    .variables-hint code { background:rgba(var(--color-principal-rgb),.1); color:var(--color-principal); padding:.05rem .3rem; border-radius:4px; font-size:.76rem; }

    .preview-contenido { background:#fafafa; border:1px solid var(--fondo-borde); border-radius:8px; padding:.75rem 1rem; font-size:.8rem; line-height:1.7; white-space:pre-wrap; color:#374151; max-height:120px; overflow-y:auto; cursor:pointer; font-family:monospace; }
    .preview-contenido:hover { background:#f3f4f6; }

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
    <div style="display:flex;align-items:center;gap:.75rem;">
        <a href="{{ route('consentimientos.index') }}"
           style="color:var(--color-principal);font-size:1.2rem;"><i class="bi bi-arrow-left-circle-fill"></i></a>
        <div>
            <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Plantillas de Consentimiento</h4>
            <p style="font-size:.82rem;color:#9ca3af;margin:0;">Gestiona los modelos reutilizables para consentimientos informados</p>
        </div>
    </div>
    <button type="button" class="btn-morado" onclick="abrirModalCrear()">
        <i class="bi bi-plus-lg"></i> Nueva Plantilla
    </button>
</div>

{{-- Tabla --}}
@if($plantillas->isEmpty())
    <div class="tabla-wrap">
        <div class="empty-state">
            <i class="bi bi-file-earmark-text"></i>
            <p style="font-weight:600;margin-bottom:.3rem;">No hay plantillas todavía</p>
            <p style="font-size:.85rem;">Crea la primera plantilla para agilizar la generación de consentimientos.</p>
            <button type="button" class="btn-morado" onclick="abrirModalCrear()" style="margin-top:.5rem;">
                <i class="bi bi-plus-lg"></i> Nueva Plantilla
            </button>
        </div>
    </div>
@else
    <div class="tabla-wrap">
        <table class="tabla-pl">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo / Categoría</th>
                    <th>Contenido</th>
                    <th>Usos</th>
                    <th>Estado</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($plantillas as $pl)
                <tr>
                    <td>
                        <span style="font-weight:600;color:#1c2b22;">{{ $pl->nombre }}</span>
                    </td>
                    <td>
                        @if($pl->tipo)
                            <span style="background:var(--color-muy-claro);color:var(--color-hover);font-size:.73rem;font-weight:600;padding:.18rem .5rem;border-radius:6px;">
                                {{ $pl->tipo }}
                            </span>
                        @else
                            <span style="color:#d1d5db;">—</span>
                        @endif
                    </td>
                    <td style="max-width:320px;">
                        <div class="preview-contenido"
                             title="Clic para editar"
                             onclick="abrirModalEditar({{ $pl->id }}, {{ json_encode($pl->nombre) }}, {{ json_encode($pl->tipo ?? '') }}, {{ json_encode($pl->contenido) }}, {{ $pl->activo ? 'true' : 'false' }})">
                            {{ Str::limit($pl->contenido, 120) }}
                        </div>
                    </td>
                    <td>
                        <span style="font-size:.8rem;color:#6b7280;">
                            {{ $pl->consentimientos()->count() }} uso(s)
                        </span>
                    </td>
                    <td>
                        @if($pl->activo)
                            <span class="badge-activa"><i class="bi bi-circle-fill" style="font-size:.45rem;"></i> Activa</span>
                        @else
                            <span class="badge-inactiva"><i class="bi bi-circle-fill" style="font-size:.45rem;"></i> Inactiva</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;justify-content:center;gap:.35rem;">
                            <button type="button" class="accion-btn" title="Editar plantilla"
                                    onclick="abrirModalEditar({{ $pl->id }}, {{ json_encode($pl->nombre) }}, {{ json_encode($pl->tipo ?? '') }}, {{ json_encode($pl->contenido) }}, {{ $pl->activo ? 'true' : 'false' }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST"
                                  action="{{ route('plantillas-consentimiento.destroy', $pl) }}"
                                  onsubmit="return confirm('¿Eliminar la plantilla «{{ $pl->nombre }}»?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="accion-btn rojo" title="Eliminar plantilla">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

{{-- ── MODAL CREAR ── --}}
<div id="modal-crear" class="modal-bg" onclick="cerrarModalCrear(event)">
    <div class="modal-box" onclick="event.stopPropagation()">
        <h5><i class="bi bi-file-earmark-plus" style="color:var(--color-principal);"></i> Nueva Plantilla</h5>
        <form method="POST" action="{{ route('plantillas-consentimiento.store') }}">
            @csrf
            @include('consentimientos.plantillas._form', ['plantilla' => null])
            <div style="display:flex;gap:.5rem;justify-content:flex-end;padding-top:.75rem;border-top:1px solid var(--fondo-borde);margin-top:.5rem;">
                <button type="button" onclick="cerrarModal('modal-crear')"
                        style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;cursor:pointer;">
                    <i class="bi bi-x-lg"></i> Cancelar
                </button>
                <button type="submit" class="btn-morado">
                    <i class="bi bi-check-lg"></i> Guardar Plantilla
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL EDITAR ── --}}
<div id="modal-editar" class="modal-bg" onclick="cerrarModalEditar(event)">
    <div class="modal-box" onclick="event.stopPropagation()">
        <h5><i class="bi bi-pencil-square" style="color:var(--color-principal);"></i> Editar Plantilla</h5>
        <form id="form-editar" method="POST" action="">
            @csrf @method('PUT')
            <div class="campo-wrap">
                <label class="campo-lbl">Nombre <span style="color:#dc2626;">*</span></label>
                <input id="edit-nombre" type="text" name="nombre" class="campo-ctrl" required maxlength="150">
            </div>
            <div class="campo-wrap">
                <label class="campo-lbl">Tipo / Categoría <span style="color:#9ca3af;font-size:.7rem;font-weight:400;">(opcional)</span></label>
                <input id="edit-tipo" type="text" name="tipo" class="campo-ctrl" maxlength="100"
                       placeholder="Ej: Cirugía, Ortodoncia, Blanqueamiento…">
            </div>
            <div class="campo-wrap">
                <div class="variables-hint">
                    <i class="bi bi-braces"></i> Variables disponibles:
                    <code>@{{nombre_paciente}}</code>
                    <code>@{{apellido_paciente}}</code>
                    <code>@{{documento_paciente}}</code>
                    <code>@{{fecha}}</code>
                    <code>@{{doctor}}</code>
                    <code>@{{procedimiento}}</code>
                </div>
                <label class="campo-lbl">Contenido <span style="color:#dc2626;">*</span></label>
                <textarea id="edit-contenido" name="contenido" rows="16" class="campo-ctrl"
                          style="font-family:monospace;font-size:.82rem;line-height:1.6;resize:vertical;" required></textarea>
            </div>
            <div class="campo-wrap">
                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.875rem;font-weight:600;color:#374151;">
                    <input id="edit-activo" type="checkbox" name="activo" value="1" style="accent-color:var(--color-principal);width:16px;height:16px;">
                    Plantilla activa (disponible para usar en nuevos consentimientos)
                </label>
            </div>
            <div style="display:flex;gap:.5rem;justify-content:flex-end;padding-top:.75rem;border-top:1px solid var(--fondo-borde);margin-top:.5rem;">
                <button type="button" onclick="cerrarModal('modal-editar')"
                        style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;cursor:pointer;">
                    <i class="bi bi-x-lg"></i> Cancelar
                </button>
                <button type="submit" class="btn-morado">
                    <i class="bi bi-check-lg"></i> Actualizar Plantilla
                </button>
            </div>
        </form>
    </div>
</div>

@if($errors->any())
<script>document.addEventListener('DOMContentLoaded', function(){ abrirModalCrear(); });</script>
@endif

@endsection

@push('scripts')
<script>
function abrirModalCrear() {
    document.getElementById('modal-crear').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function cerrarModalCrear(e) {
    if (!e || e.target === document.getElementById('modal-crear')) {
        cerrarModal('modal-crear');
    }
}
function cerrarModalEditar(e) {
    if (!e || e.target === document.getElementById('modal-editar')) {
        cerrarModal('modal-editar');
    }
}
function cerrarModal(id) {
    document.getElementById(id).classList.remove('show');
    document.body.style.overflow = '';
}
function abrirModalEditar(id, nombre, tipo, contenido, activo) {
    var baseUrl = '{{ url("plantillas-consentimiento") }}';
    document.getElementById('form-editar').action = baseUrl + '/' + id;
    document.getElementById('edit-nombre').value   = nombre;
    document.getElementById('edit-tipo').value     = tipo || '';
    document.getElementById('edit-contenido').value = contenido;
    document.getElementById('edit-activo').checked  = activo;
    document.getElementById('modal-editar').classList.add('show');
    document.body.style.overflow = 'hidden';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal('modal-crear');
        cerrarModal('modal-editar');
    }
});
</script>
@endpush
