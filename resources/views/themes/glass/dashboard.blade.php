{{-- ============================================================
     VISTA: Dashboard Principal
     Sistema: Arkedent
     Layout: layouts.app
     ============================================================ --}}
@extends('layouts.app')

@section('titulo', 'Dashboard')

@push('scripts')
<script>
const estadoClases = {
    pendiente:   { bg: 'rgba(255,193,7,0.15)',   color: '#fff' },
    confirmada:  { bg: 'rgba(0,123,255,0.15)',   color: '#fff' },
    en_proceso:  { bg: 'rgba(111,66,193,0.15)',  color: '#fff' },
    atendida:    { bg: 'rgba(40,167,69,0.15)',   color: '#fff' },
    cancelada:   { bg: 'rgba(220,53,69,0.15)',   color: '#fff' },
    no_asistio:  { bg: 'rgba(150,150,150,0.15)', color: '#fff' },
};
const estadoLabels = {
    pendiente:'Pendiente', confirmada:'Confirmada', en_proceso:'En proceso',
    atendida:'Atendida', cancelada:'Cancelada', no_asistio:'No asistió'
};

var _dcCitaId = null;

function cambiarEstadoDash(nuevoEstado) {
    if (!nuevoEstado || !_dcCitaId) return;
    document.getElementById('dc-select-estado').value = '';
    fetch('/citas/' + _dcCitaId + '/estado', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ estado: nuevoEstado }),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.ok) return;
        const ec = estadoClases[nuevoEstado] || { bg: 'rgba(255,255,255,0.1)', color: '#fff' };
        const lbl = estadoLabels[nuevoEstado] || nuevoEstado;
        document.getElementById('dc-estado').innerHTML =
            `<span style="background:${ec.bg};color:${ec.color};font-size:.75rem;font-weight:600;padding:.2rem .7rem;border-radius:50px;border:1px solid rgba(255,255,255,0.2);">${lbl}</span>`;
        var citaItem = document.querySelector('.cita-item[data-cita-id="' + _dcCitaId + '"]');
        if (citaItem) {
            var badge = citaItem.querySelector('.cita-estado');
            if (badge) {
                badge.textContent = lbl;
                badge.className = 'cita-estado estado-' + nuevoEstado;
            }
        }
    })
    .catch(function(e) { console.error('cambiarEstado error', e); });
}

function abrirDetalleCita(data) {
    _dcCitaId = data.id;
    document.getElementById('dc-select-estado').value = '';
    document.getElementById('dc-paciente').textContent = data.paciente;
    document.getElementById('dc-fecha').textContent = data.fecha;
    document.getElementById('dc-hora').textContent = data.hora_inicio + (data.hora_fin ? ' – ' + data.hora_fin : '');
    document.getElementById('dc-procedimiento').textContent = data.procedimiento;

    const estadoKey = data.estado.replace('-', '_');
    const ec = estadoClases[estadoKey] || { bg: 'rgba(255,255,255,0.1)', color: '#fff' };
    const label = data.estado.replace('_', ' ');
    document.getElementById('dc-estado').innerHTML =
        `<span style="background:${ec.bg};color:#fff;font-size:.75rem;font-weight:600;padding:.2rem .7rem;border-radius:50px;border:1px solid rgba(255,255,255,0.2);">
            ${label.charAt(0).toUpperCase() + label.slice(1)}
        </span>`;

    const notasRow = document.getElementById('dc-notas-row');
    if (data.notas) {
        document.getElementById('dc-notas').textContent = data.notas;
        notasRow.style.display = 'grid';
    } else {
        notasRow.style.display = 'none';
    }

    document.getElementById('dc-btn-editar').href = data.url_editar;

    const formConfirmar = document.getElementById('dc-form-confirmar');
    formConfirmar.action = data.url_confirmar;
    formConfirmar.style.display = (data.estado === 'pendiente') ? 'inline' : 'none';

    const btnCancelar = document.getElementById('dc-btn-cancelar');
    const cancelarOculto = ['cancelada', 'atendida'].includes(data.estado);
    btnCancelar.style.display = cancelarOculto ? 'none' : 'inline-flex';
    document.getElementById('dc-form-cancelar').action = data.url_cancelar;

    const modal = document.getElementById('modal-detalle-cita');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function cerrarDetalleCita() {
    document.getElementById('modal-detalle-cita').style.display = 'none';
    document.body.style.overflow = '';
}

function abrirModalCancelarDash() {
    document.getElementById('modal-cancelar-dash').style.display = 'flex';
}

function cerrarModalCancelarDash() {
    document.getElementById('modal-cancelar-dash').style.display = 'none';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarDetalleCita();
        cerrarModalCancelarDash();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modal-detalle-cita').addEventListener('click', function(e) {
        if (e.target === this) cerrarDetalleCita();
    });
    document.getElementById('modal-cancelar-dash').addEventListener('click', function(e) {
        if (e.target === this) cerrarModalCancelarDash();
    });

    /* ===== HEXÁGONOS ===== */
    const strokeWidth   = 0.85;
    const strokeOpacity = 0.25;
    const strokeColor   = `rgba(0,234,255,${strokeOpacity})`;

    const visibleHexes = [
        true,  true,  true,  true,  true,  true,  true,  false, false, false, false,
        true,  true,  true,  true,  true,  false, false, false, false, false, false,
        true,  true,  true,  true,  false, false, false, false, false, false, true,
        true,  false, false, false, false, false, false, false, true,  true,  true,
        true,  false, false, false, false, false, true,  true,  true,  true,  true,
        false, false, false, false, true,  true,  true,  true,  true,  true,  true,
        false, false, false, false, true,  true,  true,  true,  true,  true,  true,
    ];

    const R       = 70;
    const W       = R * 2;
    const H       = Math.sqrt(3) * R;
    const COLS    = 11;
    const ROWS    = 7;
    const colStep = W * 0.75;
    const rowStep = H;

    function hexPoints(cx, cy, r) {
        const pts = [];
        for (let i = 0; i < 6; i++) {
            const a = (Math.PI / 180) * (60 * i);
            pts.push(`${(cx + r * Math.cos(a)).toFixed(2)},${(cy + r * Math.sin(a)).toFixed(2)}`);
        }
        return pts.join(' ');
    }

    const group = document.getElementById('hexGroup');
    let idx = 0;

    for (let row = 0; row < ROWS; row++) {
        for (let col = 0; col < COLS; col++) {
            const show = visibleHexes[idx++] !== false;
            if (!show) continue;
            const cx = col * colStep + R;
            const cy = row * rowStep + H / 2 + (col % 2 === 1 ? H / 2 : 0);
            const poly = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
            poly.setAttribute('points',       hexPoints(cx, cy, R - 1));
            poly.setAttribute('fill',         'none');
            poly.setAttribute('stroke',       strokeColor);
            poly.setAttribute('stroke-width', strokeWidth);
            poly.setAttribute('filter',       'url(#hexglow)');
            group.appendChild(poly);
        }
    }
});
</script>
@endpush

@push('estilos')
<style>

    /* ══════════════════════════════════════════
       FONDO Y BASE GLASS
    ══════════════════════════════════════════ */
    body {
        background: linear-gradient(160deg, #0a6a9e 0%, #084f7a 50%, #053d5e 100%); !important;
        min-height: 100vh;
        color: white;
    }

    /* Fondo hexagonal fijo */
    .hex-bg {
        position: fixed;
        inset: 0;
        z-index: 0;
        pointer-events: none;
    }
    .hex-bg svg { width: 100%; height: 100%; }

    /* ══════════════════════════════════════════
       GLASS CARD — base reutilizable
    ══════════════════════════════════════════ */
    .glass-card {
        position: relative;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(4px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.25);
        box-shadow:
            0 8px 32px rgba(0, 0, 0, 0.18),
            inset 0 1px 0 rgba(255,255,255,0.3);
        overflow: hidden;
    }

    /* Borde neon */
    .glass-card::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 16px;
        padding: 1px;
        background: linear-gradient(120deg, rgba(0,234,255,0.6), transparent 50%, rgba(0,234,255,0.3));
        -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }

    /* Esquinas neon */
    .glass-corner {
        position: absolute;
        width: 16px;
        height: 16px;
        border: 1.5px solid rgba(0, 234, 255, 0.8);
    }
    .glass-corner.tl { top: 8px;    left: 8px;   border-right: none; border-bottom: none; }
    .glass-corner.tr { top: 8px;    right: 8px;  border-left: none;  border-bottom: none; }
    .glass-corner.bl { bottom: 8px; left: 8px;   border-right: none; border-top: none; }
    .glass-corner.br { bottom: 8px; right: 8px;  border-left: none;  border-top: none; }

    /* ══════════════════════════════════════════
       BIENVENIDA
    ══════════════════════════════════════════ */
    .bienvenida-banner {
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .bienvenida-texto h2 {
        font-size: 1.35rem;
        font-weight: 600;
        color: white;
        margin-bottom: 0.25rem;
        text-shadow: 0 0 10px rgba(0,234,255,0.5);
    }

    .bienvenida-texto p {
        font-size: 0.83rem;
        color: rgba(255,255,255,0.75);
        margin: 0;
    }

    .bienvenida-fecha { text-align: right; flex-shrink: 0; }

    .bienvenida-fecha .dia {
        font-size: 2.5rem;
        font-weight: 600;
        color: white;
        line-height: 1;
        text-shadow: 0 0 20px rgba(0,234,255,0.6);
    }

    .bienvenida-fecha .mes-ano {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.6);
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    /* ══════════════════════════════════════════
       ALERTAS
    ══════════════════════════════════════════ */
    .alerta-glass {
        border-radius: 12px;
        padding: .875rem 1.25rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        backdrop-filter: blur(12px);
        border: 1px solid;
    }

    .alerta-roja {
        background: rgba(220, 53, 69, 0.15);
        border-color: rgba(220, 53, 69, 0.4);
        color: #ffc9ce;
    }

    .alerta-naranja {
        background: rgba(251, 146, 60, 0.15);
        border-color: rgba(251, 146, 60, 0.4);
        color: #ffd8b8;
    }

    .alerta-link {
        font-size: .8rem;
        text-decoration: none;
        border-radius: 6px;
        padding: .25rem .6rem;
        white-space: nowrap;
        border: 1px solid;
    }

    .alerta-roja .alerta-link    { color: #ffc9ce; border-color: rgba(220,53,69,0.4); }
    .alerta-naranja .alerta-link { color: #ffd8b8; border-color: rgba(251,146,60,0.4); }

    /* ══════════════════════════════════════════
       MÉTRICAS
    ══════════════════════════════════════════ */
    .metricas-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .metrica-card {
        padding: 1.1rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .metrica-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .metrica-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: rgba(255,255,255,0.6);
        letter-spacing: 0.07em;
        text-transform: uppercase;
    }

    .metrica-icono {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        background: rgba(255,255,255,0.15);
        color: white;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .metrica-numero {
        font-size: 1.75rem;
        font-weight: 600;
        color: white;
        line-height: 1;
        text-shadow: 0 0 12px rgba(0,234,255,0.4);
    }

    .metrica-cambio {
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        color: rgba(255,255,255,0.65);
    }

    .cambio-positivo { color: #6effc2; }
    .cambio-negativo { color: #ff8fa3; }
    .cambio-neutro   { color: rgba(255,255,255,0.55); }

    /* ══════════════════════════════════════════
       PANEL CITAS + ACCESO RÁPIDO
    ══════════════════════════════════════════ */
    .panel-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .panel-card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .panel-card-titulo {
        font-size: 0.9rem;
        font-weight: 700;
        color: white;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-shadow: 0 0 8px rgba(0,234,255,0.5);
    }

    .panel-card-titulo i { color: #00eaff; }

    .panel-card-accion {
        font-size: 0.78rem;
        font-weight: 500;
        color: rgba(0,234,255,0.9);
        text-decoration: none;
        text-shadow: 0 0 6px rgba(0,234,255,0.4);
    }

    .panel-card-accion:hover { color: #00eaff; text-decoration: underline; }

    /* Citas */
    .cita-item {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        transition: background 0.15s;
        cursor: pointer;
    }

    .cita-item:last-child { border-bottom: none; }
    .cita-item:hover { background: rgba(255,255,255,0.06); }

    .cita-hora {
        font-size: 0.78rem;
        font-weight: 500;
        color: rgba(255,255,255,0.55);
        width: 46px;
        flex-shrink: 0;
    }

    .cita-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(0,234,255,0.2);
        color: #00eaff;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid rgba(0,234,255,0.3);
    }

    .cita-info { flex: 1; }

    .cita-nombre {
        font-size: 0.85rem;
        font-weight: 500;
        color: white;
    }

    .cita-procedimiento {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.5);
    }

    .cita-estado {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 50px;
        border: 1px solid rgba(255,255,255,0.15);
        color: white;
    }

    .estado-confirmada  { background: rgba(0,123,255,0.25); }
    .estado-pendiente   { background: rgba(255,193,7,0.25); }
    .estado-en-proceso,
    .estado-en_proceso  { background: rgba(111,66,193,0.25); }
    .estado-atendida    { background: rgba(40,167,69,0.25); }
    .estado-cancelada   { background: rgba(220,53,69,0.25); }
    .estado-no_asistio  { background: rgba(150,150,150,0.25); }

    /* Acceso rápido */
    .acceso-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.6rem;
        padding: 1rem 1.25rem;
    }

    .acceso-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        padding: 0.875rem 0.5rem;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 10px;
        text-decoration: none;
        transition: background 0.15s, border-color 0.15s, transform 0.15s;
        cursor: pointer;
    }

    .acceso-btn:hover {
        background: rgba(0,234,255,0.15);
        border-color: rgba(0,234,255,0.4);
        transform: translateY(-2px);
    }

    .acceso-btn i {
        font-size: 1.25rem;
        color: #00eaff;
        text-shadow: 0 0 8px rgba(0,234,255,0.6);
    }

    .acceso-btn span {
        font-size: 0.73rem;
        font-weight: 500;
        color: rgba(255,255,255,0.85);
        text-align: center;
    }

    /* Vacío */
    .vacio-citas {
        padding: 2rem 1.25rem;
        text-align: center;
        color: rgba(255,255,255,0.45);
    }

    .vacio-citas i { font-size: 2rem; margin-bottom: 0.5rem; display: block; }
    .vacio-citas p { font-size: 0.83rem; margin: 0; }

    /* ══════════════════════════════════════════
       MODALES GLASS
    ══════════════════════════════════════════ */
    .modal-glass-inner {
        background: rgba(15, 40, 80, 0.75);
        backdrop-filter: blur(24px) saturate(180%);
        -webkit-backdrop-filter: blur(24px) saturate(180%);
        border-radius: 16px;
        width: 100%;
        max-width: 460px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.4), 0 0 0 1px rgba(0,234,255,0.2);
        position: relative;
        margin: 1rem;
    }

    .modal-glass-header {
        background: rgba(0,234,255,0.1);
        border-bottom: 1px solid rgba(0,234,255,0.2);
        padding: 1rem 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-glass-titulo {
        font-weight: 700;
        color: white;
        margin: 0;
        display: flex;
        align-items: center;
        gap: .5rem;
        font-size: 1rem;
        text-shadow: 0 0 8px rgba(0,234,255,0.5);
    }

    .modal-glass-titulo i { color: #00eaff; }

    .modal-close-btn {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: rgba(255,255,255,0.6);
        cursor: pointer;
        line-height: 1;
        transition: color 0.15s;
    }

    .modal-close-btn:hover { color: #00eaff; }

    .modal-glass-body { padding: 1.75rem; }

    .modal-field-label {
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: rgba(0,234,255,0.8);
        padding-top: .15rem;
    }

    .modal-field-value {
        font-size: .92rem;
        color: white;
        font-weight: 500;
    }

    .modal-select {
        border: 1px solid rgba(0,234,255,0.3);
        border-radius: 8px;
        padding: .25rem .5rem;
        font-size: .78rem;
        color: white;
        background: rgba(255,255,255,0.1);
        cursor: pointer;
        outline: none;
    }

    .modal-select option { background: #1a4a6e; color: white; }

    /* Botones modal */
    .btn-glass-primary {
        background: rgba(0,234,255,0.15);
        color: #00eaff;
        border: 1.5px solid rgba(0,234,255,0.5);
        border-radius: 8px;
        padding: .45rem 1rem;
        font-size: .875rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-glass-primary:hover { background: rgba(0,234,255,0.25); }

    .btn-glass-success {
        background: rgba(40,167,69,0.3);
        color: #6effc2;
        border: 1.5px solid rgba(40,167,69,0.5);
        border-radius: 8px;
        padding: .45rem 1rem;
        font-size: .875rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-glass-success:hover { background: rgba(40,167,69,0.45); }

    .btn-glass-danger {
        background: rgba(220,53,69,0.3);
        color: #ff8fa3;
        border: 1.5px solid rgba(220,53,69,0.5);
        border-radius: 8px;
        padding: .45rem 1rem;
        font-size: .875rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-glass-danger:hover { background: rgba(220,53,69,0.45); }

    .btn-glass-neutral {
        background: rgba(255,255,255,0.08);
        color: rgba(255,255,255,0.8);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 8px;
        padding: .45rem 1rem;
        font-size: .875rem;
        cursor: pointer;
        transition: background 0.15s;
        margin-left: auto;
    }
    .btn-glass-neutral:hover { background: rgba(255,255,255,0.15); }

    /* Textarea modal */
    .modal-textarea {
        width: 100%;
        border: 1px solid rgba(0,234,255,0.3);
        border-radius: 8px;
        padding: .5rem .75rem;
        font-size: .875rem;
        outline: none;
        resize: vertical;
        font-family: inherit;
        background: rgba(255,255,255,0.08);
        color: white;
        box-sizing: border-box;
    }

    .modal-textarea::placeholder { color: rgba(255,255,255,0.35); }

    /* ══════════════════════════════════════════
       RESPONSIVE
    ══════════════════════════════════════════ */
    @media (max-width: 1100px) {
        .metricas-grid { grid-template-columns: repeat(3, 1fr); }
    }

    @media (max-width: 750px) {
        .panel-grid { grid-template-columns: 1fr; }
        .metricas-grid { grid-template-columns: repeat(2, 1fr); }
        .bienvenida-fecha { display: none; }
    }

    @media (max-width: 480px) {
        .metricas-grid { grid-template-columns: 1fr 1fr; }
    }
</style>
@endpush

@section('contenido')

{{-- Fondo hexagonal --}}
<div class="hex-bg">
    <svg id="hexSvg" viewBox="0 0 1200 800" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <filter id="hexglow">
                <feGaussianBlur stdDeviation="2.5" result="blur"/>
                <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
            </filter>
        </defs>
        <g id="hexGroup"></g>
    </svg>
</div>

{{-- Banner de bienvenida --}}
<div class="bienvenida-banner glass-card">
    <div class="glass-corner tl"></div>
    <div class="glass-corner tr"></div>
    <div class="glass-corner bl"></div>
    <div class="glass-corner br"></div>
    <div class="bienvenida-texto">
        <h2>Buenas {{ now()->hour < 12 ? 'noches' : (now()->hour < 18 ? 'tardes' : 'noches') }},
            {{ explode(' ', auth()->user()->name ?? 'Dr.')[0] }}</h2>
        <p>Tienes {{ $citasHoy ?? 0 }} citas agendadas para hoy · {{ now()->locale('es')->isoFormat('dddd') }}</p>
    </div>
    <div class="bienvenida-fecha">
        <div class="dia">{{ now()->format('d') }}</div>
        <div class="mes-ano">{{ now()->locale('es')->isoFormat('MMM YYYY') }}</div>
    </div>
</div>

{{-- Alerta órdenes de laboratorio vencidas --}}
@if(($ordenesLaboratorioVencidas ?? 0) > 0)
<div class="alerta-glass alerta-roja">
    <div style="display:flex;align-items:center;gap:.5rem;">
        <i class="bi bi-exclamation-triangle-fill" style="font-size:1.1rem;"></i>
        <span style="font-size:.85rem;">
            <strong>{{ $ordenesLaboratorioVencidas }} orden(es) de laboratorio vencida(s)</strong> — La fecha de entrega estimada ya pasó
        </span>
    </div>
    <a href="{{ route('laboratorio.index', ['estado' => 'enviado']) }}" class="alerta-link">Ver órdenes →</a>
</div>
@endif

{{-- Alerta pacientes sin autorización --}}
@if(($pacientesSinAutorizacion ?? 0) > 0)
<div class="alerta-glass alerta-naranja">
    <div style="display:flex;align-items:center;gap:.5rem;">
        <i class="bi bi-shield-exclamation" style="font-size:1.1rem;"></i>
        <span style="font-size:.85rem;">
            <strong>{{ $pacientesSinAutorizacion }} paciente(s) sin autorización de datos firmada</strong>
            — Requerido por la Ley 1581 de 2012
        </span>
    </div>
    <a href="{{ route('pacientes.index') }}" class="alerta-link">Ver pacientes →</a>
</div>
@endif

{{-- Métricas --}}
<div class="metricas-grid">

    <div class="metrica-card glass-card">
        <div class="metrica-header">
            <span class="metrica-label">Pacientes</span>
            <div class="metrica-icono"><i class="bi bi-people-fill"></i></div>
        </div>
        <div class="metrica-numero">{{ $totalPacientes ?? 0 }}</div>
        <div class="metrica-cambio cambio-positivo">
            <i class="bi bi-arrow-up-short"></i>
            <span>{{ $nuevosEsteMes ?? 0 }} nuevos este mes</span>
        </div>
    </div>

    <div class="metrica-card glass-card">
        <div class="metrica-header">
            <span class="metrica-label">Citas Hoy</span>
            <div class="metrica-icono"><i class="bi bi-calendar-check-fill"></i></div>
        </div>
        <div class="metrica-numero">{{ $citasHoy ?? 0 }}</div>
        <div class="metrica-cambio cambio-neutro">
            <i class="bi bi-dot"></i>
            <span>{{ $citasPendientesHoy ?? 0 }} por confirmar</span>
        </div>
    </div>

    <div class="metrica-card glass-card">
        <div class="metrica-header">
            <span class="metrica-label">Ingresos del Mes</span>
            <div class="metrica-icono"><i class="bi bi-cash-stack"></i></div>
        </div>
        <div class="metrica-numero">${{ number_format($ingresosDelMes ?? 0, 0, ',', '.') }}</div>
        <div class="metrica-cambio {{ ($variacionIngresos ?? 0) >= 0 ? 'cambio-positivo' : 'cambio-negativo' }}">
            <i class="bi bi-arrow-{{ ($variacionIngresos ?? 0) >= 0 ? 'up' : 'down' }}-short"></i>
            <span>vs. mes anterior</span>
        </div>
    </div>

    <div class="metrica-card glass-card">
        <div class="metrica-header">
            <span class="metrica-label">Pendiente Cobrar</span>
            <div class="metrica-icono"><i class="bi bi-clock-history"></i></div>
        </div>
        <div class="metrica-numero">${{ number_format($saldoPendiente ?? 0, 0, ',', '.') }}</div>
        <div class="metrica-cambio cambio-neutro">
            <i class="bi bi-dot"></i>
            <span>{{ $pacientesConSaldo ?? 0 }} pacientes con saldo</span>
        </div>
    </div>

    <div class="metrica-card glass-card">
        <div class="metrica-header">
            <span class="metrica-label">Egresos del Mes</span>
            <div class="metrica-icono" style="color:#ff8fa3;"><i class="bi bi-arrow-down-circle"></i></div>
        </div>
        <div class="metrica-numero" style="color:#ff8fa3;">${{ number_format($egresosMes ?? 0, 0, ',', '.') }}</div>
        <div class="metrica-cambio cambio-neutro">
            <i class="bi bi-dot"></i>
            <span>Gastos del mes</span>
        </div>
    </div>

    <div class="metrica-card glass-card">
        <div class="metrica-header">
            <span class="metrica-label">Utilidad Neta</span>
            <div class="metrica-icono" style="color:{{ ($utilidadNeta ?? 0) >= 0 ? '#6effc2' : '#ff8fa3' }};">
                <i class="bi bi-{{ ($utilidadNeta ?? 0) >= 0 ? 'graph-up-arrow' : 'graph-down-arrow' }}"></i>
            </div>
        </div>
        <div class="metrica-numero" style="color:{{ ($utilidadNeta ?? 0) >= 0 ? '#6effc2' : '#ff8fa3' }};">
            ${{ number_format(abs($utilidadNeta ?? 0), 0, ',', '.') }}
        </div>
        <div class="metrica-cambio {{ ($utilidadNeta ?? 0) >= 0 ? 'cambio-positivo' : 'cambio-negativo' }}">
            <i class="bi bi-arrow-{{ ($utilidadNeta ?? 0) >= 0 ? 'up' : 'down' }}-short"></i>
            <span>Ingresos − Egresos</span>
        </div>
    </div>

</div>

{{-- Panel inferior --}}
<div class="panel-grid">

    {{-- Citas de hoy --}}
    <div class="glass-card">
        <div class="panel-card-header">
            <div class="panel-card-titulo">
                <i class="bi bi-calendar-day"></i>
                Citas de hoy — {{ now()->locale('es')->isoFormat('D [de] MMMM') }}
            </div>
            <a href="{{ route('citas.agenda') }}" class="panel-card-accion">Ver agenda completa →</a>
        </div>

        @if(isset($citasDeHoy) && $citasDeHoy->count() > 0)
            @foreach($citasDeHoy as $cita)
            <div class="cita-item" data-cita-id="{{ $cita->id }}"
                onclick="abrirDetalleCita({
                    id: {{ $cita->id }},
                    paciente: '{{ addslashes($cita->paciente->nombre_completo ?? 'Paciente') }}',
                    fecha: '{{ $cita->fecha ? $cita->fecha->locale('es')->isoFormat('D [de] MMM [de] YYYY') : '' }}',
                    hora_inicio: '{{ $cita->hora_inicio }}',
                    hora_fin: '{{ $cita->hora_fin ?? '' }}',
                    procedimiento: '{{ addslashes($cita->procedimiento ?? 'Consulta general') }}',
                    estado: '{{ $cita->estado ?? 'pendiente' }}',
                    notas: '{{ addslashes($cita->notas ?? '') }}',
                    url_editar: '{{ route('citas.edit', $cita) }}',
                    url_confirmar: '{{ route('citas.confirmar', $cita) }}',
                    url_cancelar: '{{ route('citas.cancelar', $cita) }}'
                })">
                <span class="cita-hora">{{ $cita->hora_inicio }}</span>
                <div class="cita-avatar">
                    {{ strtoupper(substr($cita->paciente->nombre ?? 'P', 0, 1)) }}{{ strtoupper(substr($cita->paciente->apellido ?? '', 0, 1)) }}
                </div>
                <div class="cita-info">
                    <div class="cita-nombre">{{ $cita->paciente->nombre_completo ?? 'Paciente' }}</div>
                    <div class="cita-procedimiento">{{ $cita->procedimiento ?? 'Consulta general' }}</div>
                </div>
                <span class="cita-estado estado-{{ $cita->estado ?? 'pendiente' }}">
                    {{ ucfirst(str_replace('_', ' ', $cita->estado ?? 'pendiente')) }}
                </span>
            </div>
            @endforeach
        @else
            <div class="vacio-citas">
                <i class="bi bi-calendar-x"></i>
                <p>No hay citas programadas para hoy</p>
            </div>
        @endif
    </div>

    {{-- Accesos rápidos --}}
    <div class="glass-card">
        <div class="panel-card-header">
            <div class="panel-card-titulo">
                <i class="bi bi-lightning-charge"></i>
                Acceso rápido
            </div>
        </div>
        <div class="acceso-grid">
            @modulo('pacientes')
            <a href="{{ route('pacientes.create') }}" class="acceso-btn">
                <i class="bi bi-person-plus"></i>
                <span>Nuevo Paciente</span>
            </a>
            @endmodulo

            @modulo('citas')
            <a href="{{ route('citas.create') }}" class="acceso-btn">
                <i class="bi bi-calendar-plus"></i>
                <span>Nueva Cita</span>
            </a>
            @endmodulo

            @modulo('pagos')
            <a href="{{ route('pagos.create') }}" class="acceso-btn">
                <i class="bi bi-cash-coin"></i>
                <span>Registrar Pago</span>
            </a>
            @endmodulo

            @modulo('evoluciones')
            <a href="{{ route('evoluciones.create') }}" class="acceso-btn">
                <i class="bi bi-clipboard2-plus"></i>
                <span>Nueva Evolución</span>
            </a>
            @endmodulo

            @modulo('presupuestos')
            <a href="{{ route('presupuestos.create') }}" class="acceso-btn">
                <i class="bi bi-file-earmark-plus"></i>
                <span>Presupuesto</span>
            </a>
            @endmodulo

            @modulo('valoraciones')
            <a href="{{ route('valoraciones.create') }}" class="acceso-btn">
                <i class="bi bi-clipboard2-pulse"></i>
                <span>Nueva Valoración</span>
            </a>
            @endmodulo

            @modulo('reportes')
            @if(!auth()->user()->hasRole('asistente'))
            <a href="{{ route('reportes.index') }}" class="acceso-btn">
                <i class="bi bi-graph-up"></i>
                <span>Reportes</span>
            </a>
            @endif
            @endmodulo
        </div>
    </div>

</div>

{{-- Modal detalle de cita --}}
<div id="modal-detalle-cita" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);align-items:center;justify-content:center;">
    <div class="modal-glass-inner">
        <div class="modal-glass-header">
            <h5 class="modal-glass-titulo">
                <i class="bi bi-calendar-check"></i> Detalle de la cita
            </h5>
            <button onclick="cerrarDetalleCita()" class="modal-close-btn">✕</button>
        </div>
        <div class="modal-glass-body">

            <div style="display:flex;flex-direction:column;gap:.65rem;margin-bottom:1.5rem;">
                <div style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:start;">
                    <span class="modal-field-label">Paciente</span>
                    <span id="dc-paciente" class="modal-field-value"></span>
                </div>
                <div style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:start;">
                    <span class="modal-field-label">Fecha</span>
                    <span id="dc-fecha" class="modal-field-value"></span>
                </div>
                <div style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:start;">
                    <span class="modal-field-label">Hora</span>
                    <span id="dc-hora" class="modal-field-value"></span>
                </div>
                <div style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:start;">
                    <span class="modal-field-label">Procedimiento</span>
                    <span id="dc-procedimiento" class="modal-field-value"></span>
                </div>
                <div style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:center;">
                    <span class="modal-field-label">Estado</span>
                    <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
                        <span id="dc-estado"></span>
                        <select id="dc-select-estado" class="modal-select" onchange="cambiarEstadoDash(this.value)">
                            <option value="">Cambiar…</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="confirmada">Confirmada</option>
                            <option value="en_proceso">En proceso</option>
                            <option value="atendida">Atendida</option>
                            <option value="cancelada">Cancelada</option>
                            <option value="no_asistio">No asistió</option>
                        </select>
                    </div>
                </div>
                <div id="dc-notas-row" style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:start;">
                    <span class="modal-field-label">Notas</span>
                    <span id="dc-notas" class="modal-field-value" style="white-space:pre-line;"></span>
                </div>
            </div>

            <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                <a id="dc-btn-editar" href="#" class="btn-glass-primary">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <form id="dc-form-confirmar" method="POST" style="display:none;">
                    @csrf
                    <button type="submit" class="btn-glass-success">
                        <i class="bi bi-check-lg"></i> Confirmar
                    </button>
                </form>
                <button id="dc-btn-cancelar" type="button" onclick="abrirModalCancelarDash()" class="btn-glass-danger" style="display:none;">
                    <i class="bi bi-x-lg"></i> Cancelar
                </button>
                <button onclick="cerrarDetalleCita()" class="btn-glass-neutral">Cerrar</button>
            </div>

        </div>
    </div>
</div>

{{-- Modal cancelar --}}
<div id="modal-cancelar-dash" style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.55);align-items:center;justify-content:center;">
    <div class="modal-glass-inner" style="max-width:420px;">
        <div class="modal-glass-header">
            <h5 class="modal-glass-titulo">
                <i class="bi bi-x-circle"></i> Cancelar cita
            </h5>
            <button onclick="cerrarModalCancelarDash()" class="modal-close-btn">✕</button>
        </div>
        <div class="modal-glass-body">
            <p style="font-size:.85rem;color:rgba(255,255,255,0.65);margin-bottom:1rem;">
                Indica el motivo para cancelar esta cita.
            </p>
            <form id="dc-form-cancelar" method="POST">
                @csrf
                <div style="margin-bottom:1rem;">
                    <label style="font-size:.8rem;font-weight:600;color:rgba(0,234,255,0.8);display:block;margin-bottom:.3rem;">
                        Motivo <span style="color:#ff8fa3;">*</span>
                    </label>
                    <textarea name="motivo_cancelacion" rows="3" required class="modal-textarea"
                        placeholder="Ej: Paciente llamó para reagendar…"></textarea>
                </div>
                <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                    <button type="button" onclick="cerrarModalCancelarDash()" class="btn-glass-neutral" style="margin-left:0;">
                        Cerrar
                    </button>
                    <button type="submit" class="btn-glass-danger">
                        <i class="bi bi-x-lg"></i> Confirmar cancelación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection