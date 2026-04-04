@extends('layouts.app')
@section('titulo', 'Agenda Semanal')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    .nav-semana { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:.75rem 1.25rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem; margin-bottom:1rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .nav-semana-titulo { font-weight:700; color:#1c2b22; font-size:1rem; }

    .agenda-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:.6rem; }
    @media(max-width:900px) { .agenda-grid { grid-template-columns:repeat(3,1fr); } }
    @media(max-width:540px) { .agenda-grid { grid-template-columns:repeat(2,1fr); } }

    .col-dia { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; overflow:hidden; display:flex; flex-direction:column; min-height:320px; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .col-dia.es-hoy { border-color:var(--color-principal); box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12),0 0 0 2px var(--color-muy-claro); }
    .col-dia-head { padding:.6rem .75rem; border-bottom:1px solid var(--fondo-borde); }
    .col-dia.es-hoy .col-dia-head { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; }
    .dia-nombre { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#9ca3af; }
    .col-dia.es-hoy .dia-nombre { color:rgba(255,255,255,.75); }
    .dia-num { font-size:1.35rem; font-weight:800; color:#1c2b22; line-height:1; }
    .col-dia.es-hoy .dia-num { color:#fff; }
    .col-dia-body { padding:.5rem; flex:1; display:flex; flex-direction:column; gap:.35rem; overflow-y:auto; max-height:340px; }

    .tarjeta-cita { border-radius:8px; padding:.35rem .6rem; cursor:pointer; transition:filter .15s; border:1px solid rgba(0,0,0,.07); }
    .tarjeta-cita:hover { filter:brightness(.93); }
    .tc-hora { font-size:.67rem; font-weight:700; color:inherit; opacity:.75; }
    .tc-pac { font-size:.78rem; font-weight:700; color:inherit; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .tc-proc { font-size:.7rem; color:inherit; opacity:.7; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

    .sin-citas { text-align:center; padding:1.5rem .5rem; color:var(--color-acento-activo); font-size:.78rem; }

    /* Modal */
    #modal-agenda { display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.45); align-items:center; justify-content:center; }
    .modal-agenda-box { background:#fff; border-radius:14px; width:100%; max-width:480px; padding:1.75rem; box-shadow:0 20px 60px rgba(0,0,0,.2); position:relative; max-height:90vh; overflow-y:auto; }
    .modal-agenda-close { position:absolute; top:.75rem; right:.75rem; background:none; border:none; font-size:1.2rem; color:#9ca3af; cursor:pointer; }
    .modal-campo { display:flex; gap:.5rem; margin-bottom:.6rem; align-items:flex-start; }
    .modal-lbl { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#9ca3af; min-width:90px; padding-top:.1rem; }
    .modal-val { font-size:.9rem; color:#1c2b22; font-weight:500; }
    .badge-estado { display:inline-block; padding:.22rem .65rem; border-radius:20px; font-size:.73rem; font-weight:700; white-space:nowrap; }

    /* Clásico */
    body:not([data-ui="glass"]) .nav-semana { background:#fff; border:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .nav-semana-titulo { color:#1c2b22; }
    body:not([data-ui="glass"]) .col-dia { background:#fff; border:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .dia-nombre { color:#9ca3af; }
    body:not([data-ui="glass"]) .dia-num { color:#1c2b22; }
    body:not([data-ui="glass"]) .modal-agenda-box { background:#fff; box-shadow:0 20px 60px rgba(0,0,0,.2); }
    body:not([data-ui="glass"]) .modal-lbl { color:#9ca3af; }
    body:not([data-ui="glass"]) .modal-val { color:#1c2b22; }

    /* Glass */
    body[data-ui="glass"] .nav-semana { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .nav-semana-titulo { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .col-dia { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.35) !important; box-shadow:0 0 8px rgba(0,234,255,0.15) !important; }
    body[data-ui="glass"] .col-dia.es-hoy { border-color:rgba(0,234,255,0.70) !important; box-shadow:0 0 14px rgba(0,234,255,0.45) !important; }
    body[data-ui="glass"] .col-dia-head { border-bottom:1px solid rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .dia-nombre { color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .dia-num { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .sin-citas { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .modal-agenda-box { background:rgba(13,30,50,0.95) !important; backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.35) !important; box-shadow:0 20px 60px rgba(0,0,0,.5) !important; }
    body[data-ui="glass"] .modal-lbl { color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .modal-val { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .modal-agenda-close { color:rgba(255,255,255,0.55) !important; }

    /* Clásico — mini-modal cancelar */
    body:not([data-ui="glass"]) .mini-modal-cancelar { background:#fff; box-shadow:0 20px 60px rgba(0,0,0,.25); }
    body:not([data-ui="glass"]) .mini-modal-title { color:#1c2b22; }
    body:not([data-ui="glass"]) .mini-modal-desc  { color:#6b7280; }

    /* Glass — mini-modal cancelar */
    body[data-ui="glass"] .mini-modal-cancelar { background:rgba(5,40,55,0.90) !important; backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.25) !important; box-shadow:0 20px 60px rgba(0,0,0,.5) !important; }
    body[data-ui="glass"] .mini-modal-title { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .mini-modal-desc  { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .mini-modal-cancelar textarea { background:rgba(255,255,255,0.08) !important; border:1px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .btn-mini-close   { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
    body[data-ui="glass"] .page-title-main { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .page-title-sub  { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .modal-agenda-h5 { color:rgba(255,255,255,0.90) !important; }

    /* Glass — título de página */
    body[data-ui="glass"] h4[style*="color:#1c2b22"] { color:rgba(255,255,255,0.95) !important; text-shadow:0 0 18px rgba(0,234,255,0.40); }
    body[data-ui="glass"] p[style*="color:#9ca3af"] { color:rgba(255,255,255,0.45) !important; }

    /* Glass — tarjetas de cita por estado */
    body[data-ui="glass"] .tarjeta-cita { border-color:rgba(255,255,255,0.18) !important; }
    /* pendiente — amarillo */
    body[data-ui="glass"] .tarjeta-cita[style*="#FFF3CD"] { background:rgba(251,191,36,0.18) !important; color:#fde68a !important; border-color:rgba(251,191,36,0.40) !important; }
    /* confirmada — usa CSS vars, fallback cian */
    body[data-ui="glass"] .tarjeta-cita[style*="var(--color-badge-bg)"] { background:rgba(0,234,255,0.14) !important; color:rgba(0,234,255,0.90) !important; border-color:rgba(0,234,255,0.40) !important; }
    /* en_proceso — azul */
    body[data-ui="glass"] .tarjeta-cita[style*="#CCE5FF"] { background:rgba(59,130,246,0.18) !important; color:#93c5fd !important; border-color:rgba(59,130,246,0.40) !important; }
    /* atendida — verde */
    body[data-ui="glass"] .tarjeta-cita[style*="#D4EDDA"] { background:rgba(74,222,128,0.18) !important; color:#86efac !important; border-color:rgba(74,222,128,0.40) !important; }
    /* cancelada — rojo */
    body[data-ui="glass"] .tarjeta-cita[style*="#F8D7DA"] { background:rgba(248,113,113,0.18) !important; color:#fca5a5 !important; border-color:rgba(248,113,113,0.40) !important; }
    /* no_asistio — gris */
    body[data-ui="glass"] .tarjeta-cita[style*="#E2E3E5"] { background:rgba(148,163,184,0.14) !important; color:#cbd5e1 !important; border-color:rgba(148,163,184,0.30) !important; }
    /* fallback genérico */
    body[data-ui="glass"] .tarjeta-cita[style*="#f3f4f6"] { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.70) !important; border-color:rgba(255,255,255,0.20) !important; }
</style>
@endpush

@section('contenido')

{{-- Encabezado --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Agenda Semanal</h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">Vista semanal de citas — Lunes a Sábado</p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
        <a href="{{ route('citas.index') }}" class="btn-morado" style="background:transparent;color:var(--color-principal);border:1px solid var(--color-principal);box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
            <i class="bi bi-list-ul"></i> Ver listado
        </a>
        <a href="{{ route('citas.create') }}" class="btn-morado">
            <i class="bi bi-plus-circle"></i> Nueva Cita
        </a>
    </div>
</div>

{{-- Navegación semana --}}
<div class="nav-semana">
    <a href="{{ route('citas.agenda', ['fecha' => $semanaAnterior]) }}"
       class="btn-morado" style="background:transparent;color:var(--color-principal);border:1px solid var(--color-principal);">
        <i class="bi bi-chevron-left"></i> Anterior
    </a>
    <span class="nav-semana-titulo">
        <i class="bi bi-calendar3"></i>
        Semana del {{ $lunes->translatedFormat('d M') }} al {{ $sabado->translatedFormat('d M Y') }}
    </span>
    <div style="display:flex;gap:.4rem;">
        <a href="{{ route('citas.agenda') }}"
           class="btn-morado" style="background:transparent;color:var(--color-principal);border:1px solid var(--color-principal);">
            Hoy
        </a>
        <a href="{{ route('citas.agenda', ['fecha' => $semanaSiguiente]) }}"
           class="btn-morado" style="background:transparent;color:var(--color-principal);border:1px solid var(--color-principal);">
            Siguiente <i class="bi bi-chevron-right"></i>
        </a>
    </div>
</div>

{{-- Grid semanal --}}
<div class="agenda-grid">
@foreach($semanas as $fechaStr => $dia)
<div class="col-dia {{ $dia['esHoy'] ? 'es-hoy' : '' }}">
    <div class="col-dia-head">
        <div class="dia-nombre">{{ $dia['fecha']->translatedFormat('l') }}</div>
        <div class="dia-num">{{ $dia['fecha']->format('d') }}</div>
    </div>
    <div class="col-dia-body">
        @forelse($dia['citas'] as $cita)
        @php $color = $colores[$cita->estado] ?? ['bg'=>'#f3f4f6','texto'=>'#374151']; @endphp
        <div class="tarjeta-cita"
             id="cita-card-{{ $cita->id }}"
             data-fecha="{{ $cita->fecha->format('Y-m-d') }}"
             data-hora-inicio="{{ $cita->hora_inicio }}"
             data-hora-fin="{{ $cita->hora_fin ?? '' }}"
             style="background:{{ $color['bg'] }};color:{{ $color['texto'] }};"
             onclick="abrirModal({{ $cita->id }},
                '{{ addslashes($cita->paciente->nombre_completo) }}',
                '{{ $cita->fecha->translatedFormat('d \d\e M \d\e Y') }}',
                '{{ $cita->hora_inicio }}{{ $cita->hora_fin ? ' – '.$cita->hora_fin : '' }}',
                '{{ addslashes($cita->procedimiento) }}',
                '{{ ucfirst(str_replace('_',' ',$cita->estado)) }}',
                '{{ $color['bg'] }}',
                '{{ $color['texto'] }}',
                '{{ addslashes($cita->notas ?? '') }}',
                '{{ $cita->estado }}'
             )">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                <div class="tc-hora">{{ $cita->hora_inicio }}</div>
                <span class="badge-cruce-{{ $cita->id }}" style="display:none;background:#dc2626;color:#fff;border-radius:4px;font-size:.6rem;font-weight:700;padding:1px 4px;">⚠ Cruce</span>
            </div>
            <div class="tc-pac">{{ $cita->paciente->nombre_completo }}</div>
            <div class="tc-proc">{{ $cita->procedimiento }}</div>
        </div>
        @empty
        <div class="sin-citas"><i class="bi bi-calendar3" style="display:block;font-size:1.4rem;margin-bottom:.3rem;"></i>Sin citas</div>
        @endforelse
    </div>
</div>
@endforeach
</div>

{{-- Modal detalle cita --}}
<div id="modal-agenda">
    <div class="modal-agenda-box">
        <button class="modal-agenda-close" onclick="cerrarModal()">✕</button>
        <h5 style="font-weight:700;color:#1c2b22;margin-bottom:1rem;padding-right:1.5rem;">
            <i class="bi bi-calendar-check" style="color:var(--color-principal);"></i>
            Detalle de la cita
        </h5>
        <div class="modal-campo">
            <span class="modal-lbl">Paciente</span>
            <span class="modal-val" id="m-paciente"></span>
        </div>
        <div class="modal-campo">
            <span class="modal-lbl">Fecha</span>
            <span class="modal-val" id="m-fecha"></span>
        </div>
        <div class="modal-campo">
            <span class="modal-lbl">Hora</span>
            <span class="modal-val" id="m-hora"></span>
        </div>
        <div class="modal-campo">
            <span class="modal-lbl">Procedimiento</span>
            <span class="modal-val" id="m-proc"></span>
        </div>
        <div class="modal-campo">
            <span class="modal-lbl">Estado</span>
            <span id="m-estado"></span>
        </div>
        <div class="modal-campo" id="m-notas-wrap" style="display:none;">
            <span class="modal-lbl">Notas</span>
            <span class="modal-val" id="m-notas" style="white-space:pre-line;"></span>
        </div>
        <hr style="border-color:var(--fondo-borde);margin:1rem 0;">
        <div style="display:flex;flex-wrap:wrap;gap:.5rem;">
            <a id="m-btn-editar" href="#" class="btn-morado" style="background:transparent;color:var(--color-principal);border:1px solid var(--color-principal);">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <form id="m-form-confirmar" method="POST" style="display:none;">
                @csrf
                <button type="submit" class="btn-morado" style="background:linear-gradient(135deg,#166534,#15803d);">
                    <i class="bi bi-check-lg"></i> Confirmar
                </button>
            </form>
            <button id="m-btn-cancelar" type="button" onclick="abrirCancelarModal()"
                class="btn-morado" style="background:linear-gradient(135deg,#dc2626,#ef4444);display:none;">
                <i class="bi bi-x-lg"></i> Cancelar
            </button>
            <button type="button" onclick="cerrarModal()"
                class="btn-morado" style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;">
                Cerrar
            </button>
        </div>
    </div>
</div>

{{-- Mini-modal cancelar (dentro de agenda) --}}
<div id="modal-cancelar-agenda" style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:420px;padding:1.5rem;box-shadow:0 20px 60px rgba(0,0,0,.25);position:relative;">
        <button onclick="cerrarCancelarModal()" style="position:absolute;top:.6rem;right:.6rem;background:none;border:none;font-size:1.1rem;color:#9ca3af;cursor:pointer;">✕</button>
        <h6 style="font-weight:700;color:#1c2b22;margin-bottom:.25rem;"><i class="bi bi-x-circle" style="color:#dc2626;"></i> Cancelar cita</h6>
        <p style="font-size:.82rem;color:#6b7280;margin-bottom:.75rem;">Indica el motivo de la cancelación.</p>
        <form id="form-cancelar-agenda" method="POST">
            @csrf
            <textarea name="motivo_cancelacion" rows="3" required
                style="width:100%;border:1px solid var(--color-muy-claro);border-radius:8px;padding:.5rem .75rem;font-size:.875rem;outline:none;resize:vertical;font-family:inherit;margin-bottom:.75rem;"
                placeholder="Motivo de cancelación…"></textarea>
            <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                <button type="button" onclick="cerrarCancelarModal()"
                    style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.4rem .9rem;font-size:.875rem;cursor:pointer;">
                    Cerrar
                </button>
                <button type="submit"
                    style="background:#dc2626;color:#fff;border:none;border-radius:8px;padding:.4rem .9rem;font-size:.875rem;font-weight:600;cursor:pointer;">
                    Confirmar cancelación
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
var citaIdActual = null;
var estadoActual = null;

function abrirModal(id, pac, fecha, hora, proc, estadoLabel, bg, texto, notas, estadoVal) {
    citaIdActual = id;
    estadoActual = estadoVal;
    document.getElementById('m-paciente').textContent = pac;
    document.getElementById('m-fecha').textContent    = fecha;
    document.getElementById('m-hora').textContent     = hora;
    document.getElementById('m-proc').textContent     = proc;
    var est = document.getElementById('m-estado');
    est.innerHTML = '<span class="badge-estado" style="background:'+bg+';color:'+texto+';">'+estadoLabel+'</span>';
    var notasWrap = document.getElementById('m-notas-wrap');
    if (notas && notas.trim()) {
        document.getElementById('m-notas').textContent = notas;
        notasWrap.style.display = 'flex';
    } else {
        notasWrap.style.display = 'none';
    }
    document.getElementById('m-btn-editar').href = '/citas/'+id+'/edit';
    var formConf = document.getElementById('m-form-confirmar');
    formConf.action = '/citas/'+id+'/confirmar';
    formConf.style.display = estadoVal === 'pendiente' ? 'inline' : 'none';
    var btnCanc = document.getElementById('m-btn-cancelar');
    btnCanc.style.display = (estadoVal !== 'cancelada' && estadoVal !== 'atendida') ? 'inline-flex' : 'none';
    document.getElementById('modal-agenda').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function cerrarModal() {
    document.getElementById('modal-agenda').style.display = 'none';
    document.body.style.overflow = '';
}

function abrirCancelarModal() {
    document.getElementById('form-cancelar-agenda').action = '/citas/'+citaIdActual+'/cancelar';
    document.getElementById('modal-cancelar-agenda').style.display = 'flex';
}

function cerrarCancelarModal() {
    document.getElementById('modal-cancelar-agenda').style.display = 'none';
}

// ── Detectar cruces visualmente en la agenda ──
(function() {
    var tarjetas = document.querySelectorAll('.tarjeta-cita[data-hora-inicio]');
    var citasPorFecha = {};

    tarjetas.forEach(function(t) {
        var fecha = t.dataset.fecha;
        if (!citasPorFecha[fecha]) citasPorFecha[fecha] = [];
        citasPorFecha[fecha].push({
            id:         t.id.replace('cita-card-', ''),
            horaInicio: t.dataset.horaInicio,
            horaFin:    t.dataset.horaFin || null
        });
    });

    function toMinutos(hora) {
        if (!hora) return null;
        var parts = hora.split(':');
        return parseInt(parts[0]) * 60 + parseInt(parts[1]);
    }

    Object.keys(citasPorFecha).forEach(function(fecha) {
        var citas = citasPorFecha[fecha];
        for (var i = 0; i < citas.length; i++) {
            for (var j = i + 1; j < citas.length; j++) {
                var a = citas[i];
                var b = citas[j];
                var aIni = toMinutos(a.horaInicio);
                var aFin = a.horaFin ? toMinutos(a.horaFin) : aIni + 30;
                var bIni = toMinutos(b.horaInicio);
                var bFin = b.horaFin ? toMinutos(b.horaFin) : bIni + 30;
                // Cruce si se solapan
                if (aIni < bFin && aFin > bIni) {
                    var badgeA = document.querySelector('.badge-cruce-' + a.id);
                    var badgeB = document.querySelector('.badge-cruce-' + b.id);
                    if (badgeA) badgeA.style.display = 'inline-block';
                    if (badgeB) badgeB.style.display = 'inline-block';
                }
            }
        }
    });
})();

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarCancelarModal();
        cerrarModal();
    }
});
document.getElementById('modal-agenda').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
document.getElementById('modal-cancelar-agenda').addEventListener('click', function(e) {
    if (e.target === this) cerrarCancelarModal();
});
</script>
@endpush
