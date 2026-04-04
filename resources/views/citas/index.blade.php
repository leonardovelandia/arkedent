@extends('layouts.app')
@section('titulo', 'Citas')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);}
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    .form-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:14px; padding:1.75rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); max-width:820px; margin:0 auto; }
    .form-card h5 { font-weight:700; color:var(--color-hover); font-size:1rem; margin-bottom:1.25rem; padding-bottom:.6rem; border-bottom:2px solid var(--color-muy-claro); }

    .campo-wrap { margin-bottom:1.1rem; }
    .campo-lbl { font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--color-principal); display:block; margin-bottom:.3rem; }
    .campo-ctrl { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .8rem; font-size:.9rem; color:#1c2b22; background:#fff; outline:none; transition:border-color .15s; font-family:inherit; }
    .campo-ctrl:focus { border-color:var(--color-principal); }
    .campo-ctrl.is-invalid { border-color:#dc2626; }
    .campo-error { font-size:.75rem; color:#dc2626; margin-top:.2rem; display:block; }

    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media(max-width:540px) { .form-row { grid-template-columns:1fr; } }

    .plantilla-chip { display:flex; align-items:center; gap:.5rem; padding:.35rem .75rem; border-radius:8px; background:var(--color-muy-claro); border:1px solid var(--color-muy-claro); font-size:.83rem; color:var(--color-hover); font-weight:600; margin-bottom:.5rem; }

    /* Clásico – form-card */
    body:not([data-ui="glass"]) .form-card { background:#fff; border:1px solid var(--color-muy-claro); box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    body:not([data-ui="glass"]) .form-card h5 { color:var(--color-hover); border-bottom:2px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .campo-ctrl { border:1.5px solid var(--color-muy-claro); color:#1c2b22; background:#fff; }
    body:not([data-ui="glass"]) .campo-ctrl:focus { border-color:var(--color-principal); }

    /* Inline text colors — classic helpers */
    .cita-pac-nombre { font-weight:600; }
    .cita-pac-doc    { font-size:.74rem; }
    .cita-fecha      { white-space:nowrap; }
    .cita-doctor     { font-size:.82rem; }
    body:not([data-ui="glass"]) .cita-pac-nombre { color:#1c2b22; }
    body:not([data-ui="glass"]) .cita-pac-doc    { color:#9ca3af; }
    body:not([data-ui="glass"]) .cita-fecha      { color:#4b5563; }
    body:not([data-ui="glass"]) .cita-doctor     { color:#6b7280; }

    /* Modal cancelar */
    .modal-cancelar-box { border-radius:14px; padding:1.75rem; width:100%; max-width:440px; position:relative; }
    body:not([data-ui="glass"]) .modal-cancelar-box { background:#fff; box-shadow:0 20px 60px rgba(0,0,0,.2); }

    /* Glass */
    body[data-ui="glass"] .form-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .form-card h5 { color:rgba(0,234,255,0.90) !important; border-bottom:2px solid rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .campo-ctrl { border:1.5px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; background:rgba(255,255,255,0.08) !important; }
    body[data-ui="glass"] .campo-ctrl:focus { border-color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .campo-ctrl::placeholder { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .cita-pac-nombre { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .cita-pac-doc    { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .cita-fecha      { color:rgba(255,255,255,0.88) !important; }
    body[data-ui="glass"] .cita-doctor     { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .modal-cancelar-box { background:rgba(13,30,50,0.92) !important; backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.35) !important; box-shadow:0 20px 60px rgba(0,0,0,.5) !important; }
    body[data-ui="glass"] .modal-cancelar-box h5 { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .modal-cancelar-box p  { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .modal-cancelar-box label { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .modal-cancelar-box textarea { background:rgba(255,255,255,0.08) !important; border:1px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }

    /* Glass — inline badge/button helpers */
    body[data-ui="glass"] .cita-num-badge { background:rgba(0,234,255,0.12) !important; color:rgba(0,234,255,0.95) !important; border:1px solid rgba(0,234,255,0.30) !important; }
    body[data-ui="glass"] .btn-cancelar-close { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
    body[data-ui="glass"] .btn-cancelar-submit { background:linear-gradient(135deg,rgba(220,38,38,0.8),rgba(185,28,28,0.8)) !important; }

    /* Estado menu base styles */
    .estado-menu { display:none; position:absolute; top:calc(100% + 4px); left:0; background:#fff; border:1px solid #e5e7eb; border-radius:10px; box-shadow:0 4px 20px rgba(0,0,0,.12); z-index:9999; min-width:160px; overflow:hidden; }
    .estado-menu button { display:block; width:100%; text-align:left; padding:.42rem .85rem; border:none; background:none; font-size:.8rem; cursor:pointer; color:#374151; }
    .estado-menu button:hover { background:#f3f4f6; }
    .estado-menu .em-activo { font-weight:700; color:var(--color-principal); }
    .estado-menu.open { display:block; }
    /* Cita número badge */
    .cita-num-badge { font-family:monospace; font-weight:700; background:#dcfce7; color:#166534; padding:.15rem .5rem; border-radius:6px; font-size:.82rem; display:inline-block; }

    /* Glass — estado button override (overrides inline PHP colors) */
    body[data-ui="glass"] button[onclick*="toggleEstadoMenu"] { background:rgba(0,234,255,0.12) !important; color:rgba(0,234,255,0.90) !important; border:1px solid rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .estado-menu { background:rgba(5,40,55,0.96) !important; border-color:rgba(0,234,255,0.25) !important; box-shadow:0 8px 24px rgba(0,234,255,0.15) !important; }
    body[data-ui="glass"] .estado-menu button { color:rgba(255,255,255,0.78) !important; }
    body[data-ui="glass"] .estado-menu button:hover { background:rgba(0,234,255,0.10) !important; color:rgba(0,234,255,0.95) !important; }
    body[data-ui="glass"] .estado-menu .em-activo { color:rgba(0,234,255,0.95) !important; background:rgba(0,234,255,0.08) !important; }
    body[data-ui="glass"] .cita-num-badge { background:rgba(74,222,128,0.15) !important; color:#86efac !important; border:1px solid rgba(74,222,128,0.30) !important; }
    /* Número cita inline badge */
    body[data-ui="glass"] span[style*="background:#dcfce7"] { background:rgba(74,222,128,0.15) !important; color:#86efac !important; border:1px solid rgba(74,222,128,0.25) !important; }
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
        <h1 class="page-titulo">Citas</h1>
        <p class="page-subtitulo">Gestión de citas y agenda</p>
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

<x-tabla-listado
    :paginacion="$citas"
    placeholder="Nombre, apellido o documento..."
    icono-vacio="bi-calendar-x"
    mensaje-vacio="Sin citas registradas"
>
    {{-- Filtros adicionales --}}
    <x-slot:filtros>
        <input type="date" name="fecha" class="tbl-filtro-date" value="{{ request('fecha') }}">
        <select name="estado" class="tbl-filtro-select">
            <option value="">Todos los estados</option>
            @foreach(['pendiente'=>'Pendiente','confirmada'=>'Confirmada','en_proceso'=>'En proceso','atendida'=>'Atendida','cancelada'=>'Cancelada','no_asistio'=>'No asistió'] as $val => $lbl)
            <option value="{{ $val }}" {{ request('estado') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
        </select>
    </x-slot:filtros>

    <x-slot:accion-vacio>
        @if(!request('buscar') && !request('fecha') && !request('estado'))
        <div class="mt-3">
            <a href="{{ route('citas.create') }}" class="btn-morado">
                <i class="bi bi-plus-circle"></i> Agendar primera cita
            </a>
        </div>
        @endif
    </x-slot:accion-vacio>

    <x-slot:thead>
        <tr>
            <th>Paciente</th>
            <th>N° Cita</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Procedimiento</th>
            <th>Estado</th>
            <th>Doctor</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

    @foreach($citas as $cita)
    @php $color = $colores[$cita->estado] ?? ['bg'=>'#f3f4f6','texto'=>'#374151']; @endphp
    <tr>
        <td>
            <div class="cita-pac-nombre">{{ $cita->paciente->nombre_completo }}</div>
            <div class="cita-pac-doc" style="font-size:.74rem;">{{ $cita->paciente->numero_documento }}</div>
        </td>
        <td>
            <span style="font-family:monospace;font-weight:700;color:#166534;background:#dcfce7;padding:.15rem .5rem;border-radius:6px;font-size:.82rem;">
                {{ $cita->numero_cita ?? ('#'.$cita->id) }}
            </span>
        </td>
        <td class="cita-fecha">{{ $cita->fecha->translatedFormat('d M Y') }}</td>
        <td class="cita-fecha">
            {{ $cita->hora_inicio }}
            @if($cita->hora_fin) <span style="color:#9ca3af;">– {{ $cita->hora_fin }}</span> @endif
        </td>
        <td style="max-width:200px;">
            <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $cita->procedimiento }}">
                {{ $cita->procedimiento }}
            </span>
        </td>
        <td>
            <div style="position:relative;display:inline-block;">
                <button type="button"
                    style="border:none;cursor:pointer;padding:.22rem .65rem;border-radius:20px;font-size:.73rem;font-weight:700;white-space:nowrap;display:inline-flex;align-items:center;gap:.3rem;background:{{ $color['bg'] }};color:{{ $color['texto'] }};"
                    onclick="toggleEstadoMenu(event, {{ $cita->id }})">
                    <span class="estado-lbl">{{ ucfirst(str_replace('_',' ',$cita->estado)) }}</span>
                    <i class="bi bi-chevron-down" style="font-size:.55rem;"></i>
                </button>
                <div class="estado-menu" id="em-{{ $cita->id }}">
                    @foreach(['pendiente'=>'Pendiente','confirmada'=>'Confirmada','en_proceso'=>'En proceso','atendida'=>'Atendida','cancelada'=>'Cancelada','no_asistio'=>'No asistió'] as $val => $lbl)
                    <button type="button"
                            class="{{ $cita->estado === $val ? 'em-activo' : '' }}"
                            onclick="cambiarEstadoCita({{ $cita->id }}, '{{ $val }}')">
                        {{ $lbl }}
                    </button>
                    @endforeach
                </div>
            </div>
        </td>
        <td class="cita-doctor">{{ $cita->doctor ? $cita->doctor->name : '—' }}</td>
        <td>
            <div style="display:flex;justify-content:center;gap:.3rem;">
                <a href="{{ route('citas.show', $cita) }}" class="tbl-btn-accion" title="Ver detalle">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('citas.edit', $cita) }}" class="tbl-btn-accion" title="Editar">
                    <i class="bi bi-pencil"></i>
                </a>
                @if($cita->estado === 'pendiente')
                <form method="POST" action="{{ route('citas.confirmar', $cita) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="tbl-btn-accion success" title="Confirmar">
                        <i class="bi bi-check-lg"></i>
                    </button>
                </form>
                @endif
                @if(!in_array($cita->estado, ['cancelada','atendida']))
                <button type="button" class="tbl-btn-accion danger" title="Cancelar"
                    onclick="abrirModalCancelar({{ $cita->id }})">
                    <i class="bi bi-x-lg"></i>
                </button>
                @endif
            </div>
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

{{-- Modal Cancelar --}}
<div id="modal-cancelar" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div class="modal-cancelar-box">
        <button onclick="cerrarModalCancelar()" style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;">✕</button>
        <h5 style="font-weight:700;margin-bottom:.35rem;"><i class="bi bi-x-circle" style="color:#dc2626;"></i> Cancelar cita</h5>
        <p style="font-size:.85rem;margin-bottom:1rem;">Indica el motivo de cancelación.</p>
        <form id="form-cancelar" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="font-size:.8rem;font-weight:600;display:block;margin-bottom:.3rem;">Motivo <span style="color:#dc2626;">*</span></label>
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
<style>
    /* estado-menu styles are now in @push('estilos') above */
</style>
<script>
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

// Cambio inline de estado
var _emAbierto = null;
var _estadoColores = {
    pendiente:  { bg:'#FFF3CD', texto:'#856404' },
    confirmada: { bg:'var(--color-badge-bg)', texto:'var(--color-badge-texto)' },
    en_proceso: { bg:'#CCE5FF', texto:'#004085' },
    atendida:   { bg:'#D4EDDA', texto:'#155724' },
    cancelada:  { bg:'#F8D7DA', texto:'#721C24' },
    no_asistio: { bg:'#E2E3E5', texto:'#383D41' },
};
var _estadoLabels = {
    pendiente:'Pendiente', confirmada:'Confirmada', en_proceso:'En proceso',
    atendida:'Atendida', cancelada:'Cancelada', no_asistio:'No asistió'
};

function toggleEstadoMenu(e, id) {
    e.stopPropagation();
    var menu = document.getElementById('em-' + id);
    if (!menu) return;
    if (_emAbierto && _emAbierto !== menu) { _emAbierto.style.display = 'none'; }
    var abierto = menu.style.display === 'block';
    menu.style.display = abierto ? 'none' : 'block';
    _emAbierto = abierto ? null : menu;
}

document.addEventListener('click', function() {
    if (_emAbierto) { _emAbierto.style.display = 'none'; _emAbierto = null; }
});

function cambiarEstadoCita(id, estado) {
    if (_emAbierto) { _emAbierto.style.display = 'none'; _emAbierto = null; }
    fetch('/citas/' + id + '/estado', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ estado: estado }),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.ok) return;
        var menu = document.getElementById('em-' + id);
        var btn  = menu ? menu.previousElementSibling : null;
        if (btn) {
            var col = _estadoColores[estado] || { bg:'#f3f4f6', texto:'#374151' };
            btn.style.background = col.bg;
            btn.style.color      = col.texto;
            btn.querySelector('.estado-lbl').textContent = _estadoLabels[estado] || estado;
        }
        if (menu) {
            menu.querySelectorAll('button').forEach(function(b) {
                b.classList.toggle('em-activo', b.getAttribute('onclick').includes("'" + estado + "'"));
            });
        }
    })
    .catch(function(e) { console.error('cambiarEstado error', e); });
}
</script>
@endpush
