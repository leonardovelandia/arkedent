{{-- ============================================================
     VISTA: Dashboard Principal
     Sistema: Arkedent
     Layout: layouts.app  (tema glass se aplica desde app.blade.php)
     ============================================================ --}}
@extends('layouts.app')

@section('titulo', 'Dashboard')

@push('scripts')
<script>
const estadoClases = {
    pendiente:  { bg: 'rgba(251,191,36,0.2)',  color: '#fff' },
    confirmada: { bg: 'rgba(6,182,212,0.2)',   color: '#fff' },
    en_proceso: { bg: 'rgba(167,139,250,0.2)', color: '#fff' },
    atendida:   { bg: 'rgba(74,222,128,0.2)',  color: '#fff' },
    cancelada:  { bg: 'rgba(248,113,113,0.2)', color: '#fff' },
    no_asistio: { bg: 'rgba(148,163,184,0.2)', color: '#fff' },
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
            `<span style="background:${ec.bg};color:#fff;font-size:.75rem;font-weight:600;padding:.2rem .7rem;border-radius:50px;border:1px solid rgba(255,255,255,0.2);">${lbl}</span>`;
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
    if (e.key === 'Escape') { cerrarDetalleCita(); cerrarModalCancelarDash(); }
});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modal-detalle-cita').addEventListener('click', function(e) {
        if (e.target === this) cerrarDetalleCita();
    });
    document.getElementById('modal-cancelar-dash').addEventListener('click', function(e) {
        if (e.target === this) cerrarModalCancelarDash();
    });
});
</script>
@endpush

@push('estilos')
<style>
    /* ══════════════════════════════════════
       ESTILOS BASE — funcionan en ambos temas
    ══════════════════════════════════════ */

    /* Bienvenida */
    .bienvenida-banner {
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        position: relative;
        overflow: hidden;
    }

    .bienvenida-texto h2 {
        font-family: var(--fuente-titulos);
        font-size: 1.4rem;
        font-weight: 700;
        color: white;
        margin-bottom: 0.25rem;
    }

    .bienvenida-texto p {
        font-size: 0.83rem;
        color: rgba(255,255,255,0.65);
        margin: 0;
    }

    .bienvenida-fecha { text-align: right; flex-shrink: 0; position: relative; z-index: 1; }

    .bienvenida-fecha .dia {
        font-family: var(--fuente-titulos);
        font-size: 2.8rem;
        font-weight: 700;
        color: white;
        line-height: 1;
    }

    .bienvenida-fecha .mes-ano {
        font-size: 0.72rem;
        color: rgba(255,255,255,0.55);
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    /* Métricas */
    .metricas-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .metrica-card {
        border-radius: 16px;
        padding: 1.25rem 1.4rem;
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
        position: relative;
        overflow: hidden;
    }

    .metrica-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .metrica-label {
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .metrica-icono {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .metrica-numero {
        font-family: var(--fuente-titulos);
        font-size: 1.85rem;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -0.02em;
    }

    .metrica-cambio {
        font-size: 0.73rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Panel inferior */
    .panel-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .panel-card {
        border-radius: 16px;
        overflow: hidden;
        position: relative;
    }

    .panel-card-header {
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Citas */
    .cita-item {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 0.75rem 1.25rem;
        transition: background 0.15s;
        cursor: pointer;
    }

    .cita-hora {
        font-size: 0.75rem;
        font-weight: 500;
        width: 46px;
        flex-shrink: 0;
    }

    .cita-info { flex: 1; }
    .cita-nombre  { font-size: 0.84rem; font-weight: 500; }
    .cita-procedimiento { font-size: 0.73rem; }

    .cita-estado {
        font-size: 0.68rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 50px;
        border: 1px solid;
        color: white;
    }

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
        padding: 0.9rem 0.5rem;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .acceso-btn span {
        font-size: 0.72rem;
        font-weight: 500;
        text-align: center;
    }

    /* Vacío */
    .vacio-citas {
        padding: 2.5rem 1.25rem;
        text-align: center;
    }

    .vacio-citas i { font-size: 2rem; margin-bottom: 0.5rem; display: block; }
    .vacio-citas p { font-size: 0.83rem; margin: 0; }

    /* ══════════════════════════════════════
       TEMA CLÁSICO (colores planos)
    ══════════════════════════════════════ */
    body:not([data-ui="glass"]) .bienvenida-banner {
        background: linear-gradient(135deg, var(--color-principal) 0%, var(--color-sidebar-2) 100%);
        box-shadow: 0 8px 28px var(--sombra-principal), 0 2px 8px rgba(0,0,0,.12);
    }

    body:not([data-ui="glass"]) .metrica-card {
        background: white;
        border: 1px solid var(--fondo-borde);
        box-shadow: 0 8px 28px var(--sombra-principal), 0 2px 8px rgba(0,0,0,0.12);
    }

    body:not([data-ui="glass"]) .metrica-label { color: #8fa39a; }
    body:not([data-ui="glass"]) .metrica-icono { background: var(--color-muy-claro); color: var(--color-principal); }
    body:not([data-ui="glass"]) .metrica-numero { color: #1c2b22; }
    body:not([data-ui="glass"]) .cambio-positivo { color: var(--color-principal); }
    body:not([data-ui="glass"]) .cambio-negativo { color: #e53e3e; }
    body:not([data-ui="glass"]) .cambio-neutro   { color: #8fa39a; }

    body:not([data-ui="glass"]) .panel-card {
        background: white;
        border: 1px solid var(--fondo-borde);
        box-shadow: 0 8px 28px var(--sombra-principal), 0 2px 8px rgba(0,0,0,0.12);
    }

    body:not([data-ui="glass"]) .panel-card-header {
        border-bottom: 1px solid var(--fondo-borde);
        background: var(--color-muy-claro);
    }

    body:not([data-ui="glass"]) .panel-card-titulo {
        font-size: 0.78rem !important;
        font-weight: 700 !important;
        color: var(--color-hover) !important;
        display: flex; align-items: center; gap: 0.5rem;
        letter-spacing: 0.04em; text-transform: uppercase;
    }

    body:not([data-ui="glass"]) .panel-card-titulo i { color: var(--color-principal); }
    body:not([data-ui="glass"]) .panel-card-accion { color: var(--color-principal); }
    body:not([data-ui="glass"]) .panel-card-accion:hover { text-decoration: underline; }

    body:not([data-ui="glass"]) .cita-item { border-bottom: 1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .cita-item:last-child { border-bottom: none; }
    body:not([data-ui="glass"]) .cita-item:hover { background: var(--fondo-app); }
    body:not([data-ui="glass"]) .cita-hora { color: #8fa39a; }
    body:not([data-ui="glass"]) .cita-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        background: var(--color-muy-claro); color: var(--color-principal);
        font-size: 0.75rem; font-weight: 500;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    body:not([data-ui="glass"]) .cita-nombre { color: #1c2b22; }
    body:not([data-ui="glass"]) .cita-procedimiento { color: #8fa39a; }

    body:not([data-ui="glass"]) .estado-confirmada  { background: var(--color-muy-claro); border-color: var(--color-muy-claro); color: var(--color-principal); }
    body:not([data-ui="glass"]) .estado-pendiente   { background: #fff3e0; border-color: #ffe0b2; color: #e65100; }
    body:not([data-ui="glass"]) .estado-en-proceso,
    body:not([data-ui="glass"]) .estado-en_proceso  { background: #e3f2fd; border-color: #bbdefb; color: #1565c0; }
    body:not([data-ui="glass"]) .estado-atendida    { background: #f3e5f5; border-color: #e1bee7; color: #6a1b9a; }
    body:not([data-ui="glass"]) .estado-cancelada   { background: #fde8e8; border-color: #fca5a5; color: #dc2626; }
    body:not([data-ui="glass"]) .estado-no_asistio  { background: #e2e3e5; border-color: #d1d5db; color: #383d41; }

    body:not([data-ui="glass"]) .acceso-btn {
        background: var(--fondo-app);
        border: 1px solid var(--fondo-borde);
    }
    body:not([data-ui="glass"]) .acceso-btn:hover {
        background: var(--color-muy-claro);
        border-color: #b8dbc8;
        transform: translateY(-2px);
    }
    body:not([data-ui="glass"]) .acceso-btn i   { font-size: 1.25rem; color: var(--color-principal); }
    body:not([data-ui="glass"]) .acceso-btn span { color: #5c6b62; }
    body:not([data-ui="glass"]) .vacio-citas { color: #8fa39a; }

    /* ══════════════════════════════════════
       TEMA GLASS (aurora)
    ══════════════════════════════════════ */
    body[data-ui="glass"] .bienvenida-banner {
        background: linear-gradient(135deg, rgba(0,120,160,0.55) 0%, rgba(0,80,120,0.45) 100%);
        backdrop-filter: blur(24px) saturate(160%);
        -webkit-backdrop-filter: blur(24px) saturate(160%);
        border: 1px solid rgba(0,234,255,0.40);
        box-shadow: 0 0 18px rgba(0,234,255,0.20), inset 0 1px 0 rgba(255,255,255,0.10);
    }

    body[data-ui="glass"] .bienvenida-texto h2 { text-shadow: 0 0 30px rgba(0,234,255,0.50); }
    body[data-ui="glass"] .bienvenida-fecha .dia { text-shadow: 0 0 30px rgba(0,234,255,0.70); }

    body[data-ui="glass"] .metrica-card {
        background: rgba(255,255,255,0.10);
        backdrop-filter: blur(20px) saturate(160%);
        -webkit-backdrop-filter: blur(20px) saturate(160%);
        border: 1px solid rgba(0,234,255,0.45);
        box-shadow: 0 0 8px rgba(0,234,255,0.25);
        transition: box-shadow 0.2s;
    }
    body[data-ui="glass"] .metrica-card:hover {
        box-shadow: 0 0 16px rgba(0,234,255,0.45);
    }

    body[data-ui="glass"] .metrica-card::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 16px;
        padding: 1px;
        background: linear-gradient(135deg, rgba(0,234,255,0.45), rgba(0,180,200,0.20) 40%, transparent 60%, rgba(0,120,160,0.20));
        -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }

    body[data-ui="glass"] .metrica-label   { color: rgba(255,255,255,0.50); }
    body[data-ui="glass"] .metrica-icono   { background: rgba(0,234,255,0.10); color: rgba(0,234,255,0.90); border: 1px solid rgba(0,234,255,0.25); }
    body[data-ui="glass"] .metrica-numero  { color: white; }
    body[data-ui="glass"] .cambio-positivo { color: #86efac; }
    body[data-ui="glass"] .cambio-negativo { color: #fca5a5; }
    body[data-ui="glass"] .cambio-neutro   { color: rgba(255,255,255,0.45); }

    body[data-ui="glass"] .panel-card {
        background: rgba(255,255,255,0.10);
        backdrop-filter: blur(20px) saturate(160%);
        -webkit-backdrop-filter: blur(20px) saturate(160%);
        border: 1px solid rgba(0,234,255,0.45);
        box-shadow: 0 0 8px rgba(0,234,255,0.25);
        transition: box-shadow 0.2s;
    }
    body[data-ui="glass"] .panel-card:hover {
        box-shadow: 0 0 16px rgba(0,234,255,0.45);
    }

    body[data-ui="glass"] .panel-card::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 16px;
        padding: 1px;
        background: linear-gradient(135deg, rgba(0,234,255,0.45), rgba(0,180,200,0.20) 40%, transparent 60%, rgba(0,120,160,0.20));
        -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }

    body[data-ui="glass"] .panel-card-header {
        border-bottom: 1px solid rgba(0,234,255,0.20);
        background: rgba(0,0,0,0.25);
    }

    body[data-ui="glass"] .panel-card-titulo {
        font-size: 0.78rem !important;
        font-weight: 700 !important;
        color: rgba(0,234,255,0.90) !important;
        display: flex; align-items: center; gap: 0.5rem;
        letter-spacing: 0.05em; text-transform: uppercase;
    }

    body[data-ui="glass"] .panel-card-titulo i { color: rgba(0,234,255,0.90); }
    body[data-ui="glass"] .panel-card-accion { color: rgba(0,234,255,0.80); }
    body[data-ui="glass"] .panel-card-accion:hover { color: rgba(0,234,255,1); }

    body[data-ui="glass"] .cita-item { border-bottom: 1px solid rgba(0,234,255,0.08); }
    body[data-ui="glass"] .cita-item:last-child { border-bottom: none; }
    body[data-ui="glass"] .cita-item:hover { background: rgba(0,234,255,0.06); }
    body[data-ui="glass"] .cita-hora { color: rgba(255,255,255,0.40); }
    body[data-ui="glass"] .cita-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        background: linear-gradient(135deg, rgba(0,120,160,0.6), rgba(0,180,200,0.6));
        color: white; font-size: 0.73rem; font-weight: 600;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        border: 1px solid rgba(0,234,255,0.30);
    }
    body[data-ui="glass"] .cita-nombre { color: rgba(255,255,255,0.90); }
    body[data-ui="glass"] .cita-procedimiento { color: rgba(255,255,255,0.42); }

    body[data-ui="glass"] .estado-confirmada  { background: rgba(0,234,255,0.15);  border-color: rgba(0,234,255,0.35); }
    body[data-ui="glass"] .estado-pendiente   { background: rgba(251,191,36,0.20); border-color: rgba(251,191,36,0.35); }
    body[data-ui="glass"] .estado-en-proceso,
    body[data-ui="glass"] .estado-en_proceso  { background: rgba(0,180,200,0.20);  border-color: rgba(0,180,200,0.40); }
    body[data-ui="glass"] .estado-atendida    { background: rgba(74,222,128,0.20); border-color: rgba(74,222,128,0.35); }
    body[data-ui="glass"] .estado-cancelada   { background: rgba(248,113,113,0.20);border-color: rgba(248,113,113,0.35); }
    body[data-ui="glass"] .estado-no_asistio  { background: rgba(148,163,184,0.20);border-color: rgba(148,163,184,0.35); }

    body[data-ui="glass"] .acceso-btn {
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(0,234,255,0.30);
        transition: all 0.2s;
    }
    body[data-ui="glass"] .acceso-btn:hover {
        background: rgba(0,234,255,0.12);
        border-color: rgba(0,234,255,0.55);
        transform: translateY(-2px);
        box-shadow: 0 0 12px rgba(0,234,255,0.25);
    }
    body[data-ui="glass"] .acceso-btn i    { font-size: 1.3rem; color: rgba(0,234,255,0.85); }
    body[data-ui="glass"] .acceso-btn:hover i { color: rgba(0,234,255,1); }
    body[data-ui="glass"] .acceso-btn span { color: rgba(255,255,255,0.70); }
    body[data-ui="glass"] .vacio-citas { color: rgba(255,255,255,0.30); }

    /* Alertas dashboard */
    .alerta-dash {
        border-radius: 12px;
        padding: .875rem 1.25rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        border: 1px solid;
    }

    body:not([data-ui="glass"]) .alerta-dash-roja    { background: #F8D7DA; border-color: #DC3545; color: #721C24; }
    body:not([data-ui="glass"]) .alerta-dash-naranja { background: #FFF7ED; border-color: #FB923C; color: #C2410C; }
    body:not([data-ui="glass"]) .alerta-dash-link    { font-size:.8rem; text-decoration:none; border-radius:6px; padding:.25rem .6rem; white-space:nowrap; border:1px solid; }
    body:not([data-ui="glass"]) .alerta-dash-roja .alerta-dash-link    { color:#721C24; border-color:#f5c6cb; }
    body:not([data-ui="glass"]) .alerta-dash-naranja .alerta-dash-link { color:#C2410C; border-color:#FDBA74; }

    body[data-ui="glass"] .alerta-dash { backdrop-filter: blur(16px); }
    body[data-ui="glass"] .alerta-dash-roja {
        background: rgba(220,38,38,0.26) !important;
        border-color: rgba(248,113,113,0.80) !important;
        color: #fca5a5 !important;
        box-shadow: 0 0 16px rgba(220,38,38,0.35), inset 0 0 40px rgba(220,38,38,0.08);
    }
    body[data-ui="glass"] .alerta-dash-naranja {
        background: rgba(234,88,12,0.24) !important;
        border-color: rgba(251,146,60,0.80) !important;
        color: #fdba74 !important;
        box-shadow: 0 0 16px rgba(234,88,12,0.30), inset 0 0 40px rgba(234,88,12,0.06);
    }
    body[data-ui="glass"] .alerta-dash-link { font-size:.8rem; text-decoration:none; border-radius:6px; padding:.28rem .8rem; white-space:nowrap; border:1px solid; transition:background .15s,box-shadow .15s; font-weight:600; }
    body[data-ui="glass"] .alerta-dash-roja .alerta-dash-link    { color:#fca5a5 !important; border-color:rgba(248,113,113,0.60) !important; background:rgba(220,38,38,0.22) !important; }
    body[data-ui="glass"] .alerta-dash-naranja .alerta-dash-link { color:#fdba74 !important; border-color:rgba(251,146,60,0.60) !important; background:rgba(234,88,12,0.20) !important; }
    body[data-ui="glass"] .alerta-dash-roja .alerta-dash-link:hover    { background:rgba(220,38,38,0.40) !important; box-shadow:0 0 8px rgba(248,113,113,0.35); }
    body[data-ui="glass"] .alerta-dash-naranja .alerta-dash-link:hover { background:rgba(234,88,12,0.38) !important; box-shadow:0 0 8px rgba(251,146,60,0.35); }

    /* Responsive */
    @media (max-width: 1100px) { .metricas-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 750px)  { .panel-grid { grid-template-columns: 1fr; } .metricas-grid { grid-template-columns: repeat(2, 1fr); } .bienvenida-fecha { display: none; } }
    @media (max-width: 480px)  { .metricas-grid { grid-template-columns: 1fr 1fr; } }
</style>
@endpush

@section('contenido')

{{-- Banner bienvenida --}}
<div class="bienvenida-banner">
    <div class="bienvenida-texto">
        <h2>Buenas {{ now()->hour < 12 ? 'mañanas' : (now()->hour < 18 ? 'tardes' : 'noches') }},
            {{ explode(' ', auth()->user()->name ?? 'Dr.')[0] }}</h2>
        <p>Tienes {{ $citasHoy ?? 0 }} citas agendadas para hoy · {{ now()->locale('es')->isoFormat('dddd') }}</p>
    </div>
    <div class="bienvenida-fecha">
        <div class="dia">{{ now()->format('d') }}</div>
        <div class="mes-ano">{{ now()->locale('es')->isoFormat('MMM YYYY') }}</div>
    </div>
</div>

{{-- Alerta órdenes --}}
@if(($ordenesLaboratorioVencidas ?? 0) > 0)
<div class="alerta-dash alerta-dash-roja">
    <div style="display:flex;align-items:center;gap:.5rem;">
        <i class="bi bi-exclamation-triangle-fill" style="font-size:1.1rem;"></i>
        <span style="font-size:.85rem;">
            <strong>{{ $ordenesLaboratorioVencidas }} orden(es) de laboratorio vencida(s)</strong> — La fecha de entrega estimada ya pasó
        </span>
    </div>
    <a href="{{ route('laboratorio.index', ['estado' => 'enviado']) }}" class="alerta-dash-link">Ver órdenes →</a>
</div>
@endif

{{-- Alerta pacientes --}}
@if(($pacientesSinAutorizacion ?? 0) > 0)
<div class="alerta-dash alerta-dash-naranja">
    <div style="display:flex;align-items:center;gap:.5rem;">
        <i class="bi bi-shield-exclamation" style="font-size:1.1rem;"></i>
        <span style="font-size:.85rem;">
            <strong>{{ $pacientesSinAutorizacion }} paciente(s) sin autorización de datos firmada</strong>
            — Requerido por la Ley 1581 de 2012
        </span>
    </div>
    <a href="{{ route('pacientes.index') }}" class="alerta-dash-link">Ver pacientes →</a>
</div>
@endif

{{-- Métricas --}}
<div class="metricas-grid">

    <div class="metrica-card">
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

    <div class="metrica-card">
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

    <div class="metrica-card">
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

    <div class="metrica-card">
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

    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Egresos del Mes</span>
            <div class="metrica-icono" style="color:#fca5a5 !important;">
                <i class="bi bi-arrow-down-circle"></i>
            </div>
        </div>
        <div class="metrica-numero" style="color:#fca5a5 !important;">
            ${{ number_format($egresosMes ?? 0, 0, ',', '.') }}
        </div>
        <div class="metrica-cambio cambio-neutro">
            <i class="bi bi-dot"></i>
            <span>Gastos del mes</span>
        </div>
    </div>

    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Utilidad Neta</span>
            <div class="metrica-icono" style="color:{{ ($utilidadNeta ?? 0) >= 0 ? '#86efac' : '#fca5a5' }} !important;">
                <i class="bi bi-{{ ($utilidadNeta ?? 0) >= 0 ? 'graph-up-arrow' : 'graph-down-arrow' }}"></i>
            </div>
        </div>
        <div class="metrica-numero" style="color:{{ ($utilidadNeta ?? 0) >= 0 ? '#86efac' : '#fca5a5' }} !important;">
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
    <div class="panel-card">
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
    <div class="panel-card">
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

{{-- Modal detalle cita --}}
<div id="modal-detalle-cita" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.6);align-items:center;justify-content:center;">
    <div style="background:rgba(15,7,40,0.85);backdrop-filter:blur(32px) saturate(180%);-webkit-backdrop-filter:blur(32px) saturate(180%);border-radius:20px;width:100%;max-width:460px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,.5),0 0 0 1px rgba(167,139,250,0.2);position:relative;margin:1rem;">
        <div style="background:linear-gradient(135deg,rgba(124,58,237,0.2),rgba(6,182,212,0.1));border-bottom:1px solid rgba(255,255,255,0.08);padding:1rem 1.75rem;display:flex;align-items:center;justify-content:space-between;">
            <h5 style="font-weight:700;color:white;margin:0;display:flex;align-items:center;gap:.5rem;font-size:1rem;">
                <i class="bi bi-calendar-check" style="color:rgba(167,139,250,0.9);"></i> Detalle de la cita
            </h5>
            <button onclick="cerrarDetalleCita()" style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.1);border-radius:8px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:.9rem;color:rgba(255,255,255,0.6);cursor:pointer;">✕</button>
        </div>
        <div style="padding:1.75rem;">
            <div style="display:flex;flex-direction:column;gap:.7rem;margin-bottom:1.5rem;">
                <div style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:start;">
                    <span style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(167,139,250,0.8);padding-top:.15rem;">Paciente</span>
                    <span id="dc-paciente" style="font-size:.92rem;color:rgba(255,255,255,0.9);font-weight:500;"></span>
                </div>
                <div style="height:1px;background:rgba(255,255,255,0.06);"></div>
                <div style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:start;">
                    <span style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(167,139,250,0.8);padding-top:.15rem;">Fecha</span>
                    <span id="dc-fecha" style="font-size:.92rem;color:rgba(255,255,255,0.9);font-weight:500;"></span>
                </div>
                <div style="height:1px;background:rgba(255,255,255,0.06);"></div>
                <div style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:start;">
                    <span style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(167,139,250,0.8);padding-top:.15rem;">Hora</span>
                    <span id="dc-hora" style="font-size:.92rem;color:rgba(255,255,255,0.9);font-weight:500;"></span>
                </div>
                <div style="height:1px;background:rgba(255,255,255,0.06);"></div>
                <div style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:start;">
                    <span style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(167,139,250,0.8);padding-top:.15rem;">Procedimiento</span>
                    <span id="dc-procedimiento" style="font-size:.92rem;color:rgba(255,255,255,0.9);font-weight:500;"></span>
                </div>
                <div style="height:1px;background:rgba(255,255,255,0.06);"></div>
                <div style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:center;">
                    <span style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(167,139,250,0.8);">Estado</span>
                    <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
                        <span id="dc-estado"></span>
                        <select id="dc-select-estado" onchange="cambiarEstadoDash(this.value)"
                            style="border:1px solid rgba(167,139,250,0.3);border-radius:8px;padding:.3rem .6rem;font-size:.78rem;color:white;background:rgba(124,58,237,0.15);cursor:pointer;outline:none;">
                            <option value="" style="background:#1a0a3e;">Cambiar…</option>
                            <option value="pendiente"  style="background:#1a0a3e;">Pendiente</option>
                            <option value="confirmada" style="background:#1a0a3e;">Confirmada</option>
                            <option value="en_proceso" style="background:#1a0a3e;">En proceso</option>
                            <option value="atendida"   style="background:#1a0a3e;">Atendida</option>
                            <option value="cancelada"  style="background:#1a0a3e;">Cancelada</option>
                            <option value="no_asistio" style="background:#1a0a3e;">No asistió</option>
                        </select>
                    </div>
                </div>
                <div id="dc-notas-row" style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:start;">
                    <span style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(167,139,250,0.8);padding-top:.15rem;">Notas</span>
                    <span id="dc-notas" style="font-size:.92rem;color:rgba(255,255,255,0.9);font-weight:500;white-space:pre-line;"></span>
                </div>
            </div>

            <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                <a id="dc-btn-editar" href="#"
                   style="background:rgba(167,139,250,0.15);color:#c4b5fd;border:1px solid rgba(167,139,250,0.4);border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:500;display:inline-flex;align-items:center;gap:.35rem;text-decoration:none;">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <form id="dc-form-confirmar" method="POST" style="display:none;">
                    @csrf
                    <button type="submit"
                        style="background:rgba(74,222,128,0.2);color:#86efac;border:1px solid rgba(74,222,128,0.4);border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:500;display:inline-flex;align-items:center;gap:.35rem;cursor:pointer;">
                        <i class="bi bi-check-lg"></i> Confirmar
                    </button>
                </form>
                <button id="dc-btn-cancelar" type="button" onclick="abrirModalCancelarDash()"
                    style="background:rgba(248,113,113,0.2);color:#fca5a5;border:1px solid rgba(248,113,113,0.4);border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:500;display:none;align-items:center;gap:.35rem;cursor:pointer;">
                    <i class="bi bi-x-lg"></i> Cancelar
                </button>
                <button onclick="cerrarDetalleCita()"
                    style="background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.7);border:1px solid rgba(255,255,255,0.12);border-radius:8px;padding:.45rem 1rem;font-size:.875rem;cursor:pointer;margin-left:auto;">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal cancelar --}}
<div id="modal-cancelar-dash" style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.65);align-items:center;justify-content:center;">
    <div style="background:rgba(15,7,40,0.85);backdrop-filter:blur(32px) saturate(180%);-webkit-backdrop-filter:blur(32px) saturate(180%);border-radius:20px;width:100%;max-width:420px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,.5),0 0 0 1px rgba(167,139,250,0.2);position:relative;margin:1rem;">
        <div style="background:linear-gradient(135deg,rgba(124,58,237,0.2),rgba(6,182,212,0.1));border-bottom:1px solid rgba(255,255,255,0.08);padding:1rem 1.75rem;display:flex;align-items:center;justify-content:space-between;">
            <h5 style="font-weight:700;color:white;margin:0;display:flex;align-items:center;gap:.5rem;font-size:1rem;">
                <i class="bi bi-x-circle" style="color:#fca5a5;"></i> Cancelar cita
            </h5>
            <button onclick="cerrarModalCancelarDash()" style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.1);border-radius:8px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:.9rem;color:rgba(255,255,255,0.6);cursor:pointer;">✕</button>
        </div>
        <div style="padding:1.75rem;">
            <p style="font-size:.85rem;color:rgba(255,255,255,0.55);margin-bottom:1rem;">Indica el motivo para cancelar esta cita.</p>
            <form id="dc-form-cancelar" method="POST">
                @csrf
                <div style="margin-bottom:1rem;">
                    <label style="font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:rgba(167,139,250,0.8);display:block;margin-bottom:.4rem;">
                        Motivo <span style="color:#fca5a5;">*</span>
                    </label>
                    <textarea name="motivo_cancelacion" rows="3" required
                        style="width:100%;border:1px solid rgba(167,139,250,0.3);border-radius:8px;padding:.5rem .75rem;font-size:.875rem;outline:none;resize:vertical;font-family:inherit;background:rgba(255,255,255,0.06);color:white;box-sizing:border-box;"
                        placeholder="Ej: Paciente llamó para reagendar…"></textarea>
                </div>
                <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                    <button type="button" onclick="cerrarModalCancelarDash()"
                        style="background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.7);border:1px solid rgba(255,255,255,0.12);border-radius:8px;padding:.45rem 1rem;font-size:.875rem;cursor:pointer;">
                        Cerrar
                    </button>
                    <button type="submit"
                        style="background:rgba(248,113,113,0.2);color:#fca5a5;border:1px solid rgba(248,113,113,0.4);border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:.35rem;">
                        <i class="bi bi-x-lg"></i> Confirmar cancelación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection