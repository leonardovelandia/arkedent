{{-- ============================================================
     VISTA: Dashboard Principal
     Sistema: Tatiana Velandia Odontología
     Layout: layouts.app
     ============================================================ --}}
@extends('layouts.app')

@section('titulo', 'Dashboard')

@push('scripts')
<script>
const estadoClases = {
    pendiente:   { bg: '#fff3e0', color: '#e65100' },
    confirmada:  { bg: 'var(--color-muy-claro)', color: 'var(--color-principal)' },
    en_proceso:  { bg: '#e3f2fd', color: '#1565c0' },
    atendida:    { bg: '#d4edda', color: '#155724' },
    cancelada:   { bg: '#f8d7da', color: '#721c24' },
    no_asistio:  { bg: '#e2e3e5', color: '#383d41' },
};

function abrirDetalleCita(data) {
    document.getElementById('dc-paciente').textContent = data.paciente;
    document.getElementById('dc-fecha').textContent = data.fecha;
    document.getElementById('dc-hora').textContent = data.hora_inicio + (data.hora_fin ? ' – ' + data.hora_fin : '');
    document.getElementById('dc-procedimiento').textContent = data.procedimiento;

    const estadoKey = data.estado.replace('-', '_');
    const ec = estadoClases[estadoKey] || { bg: '#f3f4f6', color: '#374151' };
    const label = data.estado.replace('_', ' ');
    document.getElementById('dc-estado').innerHTML =
        `<span style="background:${ec.bg};color:${ec.color};font-size:.75rem;font-weight:600;padding:.2rem .7rem;border-radius:50px;">
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
});
</script>
@endpush

@push('estilos')
<style>
    /* ── Bienvenida ── */
    .bienvenida-banner {
        background: linear-gradient(135deg, var(--color-principal) 0%, var(--color-sidebar-2) 100%);
        border-radius: 14px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        position: relative;
        overflow: hidden;
    }

    .bienvenida-banner::after {
        content: '';
        position: absolute;
        right: -40px;
        top: -40px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        border: 1.5px solid rgba(255,255,255,0.07);
    }

    .bienvenida-texto h2 {
        font-family: var(--fuente-titulos);
        font-size: 1.35rem;
        font-weight: 600;
        color: white;
        margin-bottom: 0.25rem;
    }

    .bienvenida-texto p {
        font-size: 0.83rem;
        color: rgba(255,255,255,0.65);
        margin: 0;
    }

    .bienvenida-fecha {
        text-align: right;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }

    .bienvenida-fecha .dia {
        font-family: var(--fuente-titulos);
        font-size: 2.5rem;
        font-weight: 600;
        color: white;
        line-height: 1;
    }

    .bienvenida-fecha .mes-ano {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.55);
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    /* ── Cards de métricas ── */
    .metricas-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .metrica-card {
        background: white;
        border: 1px solid var(--fondo-borde);
        border-radius: 12px;
        padding: 1.1rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        box-shadow: 0 8px 28px var(--sombra-principal), 0 2px 8px rgba(0,0,0,0.12);
    }

    .metrica-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .metrica-label {
        font-size: 0.75rem;
        font-weight: 500;
        color: #8fa39a;
        letter-spacing: 0.05em;
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
    }

    .icono-morado   { background: var(--color-muy-claro); color: var(--color-principal); }
    .icono-azul    { background: #e3f2fd; color: #1565c0; }
    .icono-naranja { background: #fff3e0; color: #e65100; }
    .icono-morado  { background: #f3e5f5; color: #6a1b9a; }

    .metrica-numero {
        font-family: var(--fuente-titulos);
        font-size: 1.75rem;
        font-weight: 600;
        color: #1c2b22;
        line-height: 1;
    }

    .metrica-cambio {
        font-size: 0.75rem;
        font-weight: 400;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .cambio-positivo { color: var(--color-principal); }
    .cambio-negativo { color: #e53e3e; }
    .cambio-neutro   { color: #8fa39a; }

    /* ── Panel de citas hoy ── */
    .panel-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .panel-card {
        background: white;
        border: 1px solid var(--fondo-borde);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 28px var(--sombra-principal), 0 2px 8px rgba(0,0,0,0.12);
    }

    .panel-card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--fondo-borde);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .panel-card-titulo {
        font-family: var(--fuente-principal) !important;
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--color-hover) !important;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .panel-card-titulo i { color: var(--color-principal); }

    .panel-card-accion {
        font-size: 0.78rem;
        font-weight: 500;
        color: var(--color-principal);
        text-decoration: none;
    }

    .panel-card-accion:hover { text-decoration: underline; }

    /* Lista de citas del día */
    .cita-item {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid var(--fondo-borde);
        transition: background 0.15s;
    }

    .cita-item:last-child { border-bottom: none; }
    .cita-item:hover { background: var(--fondo-app); }

    .cita-hora {
        font-size: 0.78rem;
        font-weight: 500;
        color: #8fa39a;
        width: 46px;
        flex-shrink: 0;
    }

    .cita-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--color-muy-claro);
        color: var(--color-principal);
        font-size: 0.75rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .cita-info { flex: 1; }

    .cita-nombre {
        font-size: 0.85rem;
        font-weight: 500;
        color: #1c2b22;
    }

    .cita-procedimiento {
        font-size: 0.75rem;
        color: #8fa39a;
    }

    .cita-estado {
        font-size: 0.7rem;
        font-weight: 500;
        padding: 2px 9px;
        border-radius: 50px;
    }

    .estado-confirmada  { background: var(--color-muy-claro); color: var(--color-principal); }
    .estado-pendiente   { background: #fff3e0; color: #e65100; }
    .estado-en-proceso  { background: #e3f2fd; color: #1565c0; }
    .estado-atendida    { background: #f3e5f5; color: #6a1b9a; }

    /* Panel de acceso rápido */
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
        background: var(--fondo-app);
        border: 1px solid var(--fondo-borde);
        border-radius: 10px;
        text-decoration: none;
        transition: background 0.15s, border-color 0.15s, transform 0.15s;
        cursor: pointer;
    }

    .acceso-btn:hover {
        background: var(--color-muy-claro);
        border-color: #b8dbc8;
        transform: translateY(-2px);
    }

    .acceso-btn i {
        font-size: 1.25rem;
        color: var(--color-principal);
    }

    .acceso-btn span {
        font-size: 0.73rem;
        font-weight: 500;
        color: #5c6b62;
        text-align: center;
    }

    /* Estado vacío */
    .vacio-citas {
        padding: 2rem 1.25rem;
        text-align: center;
        color: #8fa39a;
    }

    .vacio-citas i { font-size: 2rem; margin-bottom: 0.5rem; display: block; }
    .vacio-citas p { font-size: 0.83rem; margin: 0; }

    /* ── Responsive ── */
    @media (max-width: 1100px) {
        .metricas-grid { grid-template-columns: repeat(2, 1fr); }
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

{{-- Banner de bienvenida --}}
<div class="bienvenida-banner">
    <div class="bienvenida-texto">
        <h2>Buenas {{ now()->hour < 12 ? 'noches' : (now()->hour < 18 ? 'tardes' : 'noches') }},
            {{ explode(' ', auth()->user()->name ?? 'Dr.')[0] }} 👋</h2>
        <p>Tienes {{ $citasHoy ?? 0 }} citas agendadas para hoy · {{ now()->locale('es')->isoFormat('dddd') }}</p>
    </div>
    <div class="bienvenida-fecha">
        <div class="dia">{{ now()->format('d') }}</div>
        <div class="mes-ano">{{ now()->locale('es')->isoFormat('MMM YYYY') }}</div>
    </div>
</div>

{{-- Alerta órdenes de laboratorio vencidas --}}
@if(($ordenesLaboratorioVencidas ?? 0) > 0)
<div style="background:#F8D7DA; border:1px solid #DC3545; border-radius:10px; padding:.875rem 1.25rem; margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between; gap:.75rem;">
    <div style="display:flex; align-items:center; gap:.5rem;">
        <i class="bi bi-exclamation-triangle-fill" style="color:#721C24; font-size:1.1rem;"></i>
        <span style="color:#721C24; font-size:.85rem;">
            <strong>{{ $ordenesLaboratorioVencidas }} orden(es) de laboratorio vencida(s)</strong> — La fecha de entrega estimada ya pasó
        </span>
    </div>
    <a href="{{ route('laboratorio.index', ['estado' => 'enviado']) }}"
       style="font-size:.8rem; color:#721C24; text-decoration:none; border:1px solid #f5c6cb; border-radius:6px; padding:.25rem .6rem; white-space:nowrap;">
        Ver órdenes →
    </a>
</div>
@endif

{{-- Métricas principales --}}
<div class="metricas-grid">

    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Pacientes</span>
            <div class="metrica-icono icono-morado"><i class="bi bi-people-fill"></i></div>
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
            <div class="metrica-icono icono-azul"><i class="bi bi-calendar-check-fill"></i></div>
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
            <div class="metrica-icono icono-naranja"><i class="bi bi-cash-stack"></i></div>
        </div>
        <div class="metrica-numero">
            ${{ number_format($ingresosDelMes ?? 0, 0, ',', '.') }}
        </div>
        <div class="metrica-cambio {{ ($variacionIngresos ?? 0) >= 0 ? 'cambio-positivo' : 'cambio-negativo' }}">
            <i class="bi bi-arrow-{{ ($variacionIngresos ?? 0) >= 0 ? 'up' : 'down' }}-short"></i>
            <span>vs. mes anterior</span>
        </div>
    </div>

    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Pendiente Cobrar</span>
            <div class="metrica-icono icono-morado"><i class="bi bi-clock-history"></i></div>
        </div>
        <div class="metrica-numero">
            ${{ number_format($saldoPendiente ?? 0, 0, ',', '.') }}
        </div>
        <div class="metrica-cambio cambio-neutro">
            <i class="bi bi-dot"></i>
            <span>{{ $pacientesConSaldo ?? 0 }} pacientes con saldo</span>
        </div>
    </div>

</div>{{-- /metricas-grid --}}

{{-- Panel inferior --}}
<div class="panel-grid">

    {{-- Citas de hoy --}}
    <div class="panel-card">
        <div class="panel-card-header">
            <div class="panel-card-titulo">
                <i class="bi bi-calendar-day"></i>
                Citas de hoy — {{ now()->locale('es')->isoFormat('D [de] MMMM') }}
            </div>
            <a href="{{ route('citas.index') }}" class="panel-card-accion">
                Ver agenda completa →
            </a>
        </div>

        @if(isset($citasDeHoy) && $citasDeHoy->count() > 0)
            @foreach($citasDeHoy as $cita)
            <div class="cita-item" style="cursor:pointer;"
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
                    {{ ucfirst($cita->estado ?? 'pendiente') }}
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
            <a href="{{ route('pacientes.create') }}" class="acceso-btn">
                <i class="bi bi-person-plus"></i>
                <span>Nuevo Paciente</span>
            </a>
            <a href="{{ route('citas.create') }}" class="acceso-btn">
                <i class="bi bi-calendar-plus"></i>
                <span>Nueva Cita</span>
            </a>
            <a href="{{ route('pagos.create') }}" class="acceso-btn">
                <i class="bi bi-cash-coin"></i>
                <span>Registrar Pago</span>
            </a>
            <a href="{{ route('evoluciones.create') }}" class="acceso-btn">
                <i class="bi bi-clipboard2-plus"></i>
                <span>Nueva Evolución</span>
            </a>
            <a href="{{ route('presupuestos.create') }}" class="acceso-btn">
                <i class="bi bi-file-earmark-plus"></i>
                <span>Presupuesto</span>
            </a>
            <a href="{{ route('valoraciones.create') }}" class="acceso-btn">
                <i class="bi bi-clipboard2-pulse"></i>
                <span>Nueva Valoración</span>
            </a>
            @if(!auth()->user()->hasRole('asistente'))
            <a href="{{ route('reportes.index') }}" class="acceso-btn">
                <i class="bi bi-graph-up"></i>
                <span>Reportes</span>
            </a>
            @endif
        </div>
    </div>

</div>{{-- /panel-grid --}}



{{-- Modal detalle de cita --}}
<div id="modal-detalle-cita" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:460px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative;margin:1rem;">
        <div style="background:var(--color-muy-claro);padding:1rem 1.75rem;display:flex;align-items:center;justify-content:space-between;">
            <h5 style="font-family:var(--fuente-principal);font-weight:700;color:var(--color-hover);margin:0;display:flex;align-items:center;gap:.5rem;font-size:1rem;">
                <i class="bi bi-calendar-check"></i> Detalle de la cita
            </h5>
            <button onclick="cerrarDetalleCita()" style="background:none;border:none;font-size:1.2rem;color:var(--color-hover);cursor:pointer;line-height:1;">✕</button>
        </div>
        <div style="padding:1.75rem;">

        <div style="display:flex;flex-direction:column;gap:.65rem;margin-bottom:1.5rem;">
            <div style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:start;">
                <span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--color-hover);padding-top:.15rem;">Paciente</span>
                <span id="dc-paciente" style="font-size:.92rem;color:#1c2b22;font-weight:500;"></span>
            </div>
            <div style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:start;">
                <span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--color-hover);padding-top:.15rem;">Fecha</span>
                <span id="dc-fecha" style="font-size:.92rem;color:#1c2b22;font-weight:500;"></span>
            </div>
            <div style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:start;">
                <span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--color-hover);padding-top:.15rem;">Hora</span>
                <span id="dc-hora" style="font-size:.92rem;color:#1c2b22;font-weight:500;"></span>
            </div>
            <div style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:start;">
                <span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--color-hover);padding-top:.15rem;">Procedimiento</span>
                <span id="dc-procedimiento" style="font-size:.92rem;color:#1c2b22;font-weight:500;"></span>
            </div>
            <div style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:start;">
                <span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--color-hover);padding-top:.15rem;">Estado</span>
                <span id="dc-estado"></span>
            </div>
            <div id="dc-notas-row" style="display:grid;grid-template-columns:130px 1fr;gap:.35rem;align-items:start;">
                <span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--color-hover);padding-top:.15rem;">Notas</span>
                <span id="dc-notas" style="font-size:.92rem;color:#1c2b22;font-weight:500;white-space:pre-line;"></span>
            </div>
        </div>

        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <a id="dc-btn-editar" href="#"
               style="background:transparent;color:var(--color-principal);border:1.5px solid var(--color-principal);border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:500;display:inline-flex;align-items:center;gap:.35rem;text-decoration:none;">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <form id="dc-form-confirmar" method="POST" style="display:none;">
                @csrf
                <button type="submit"
                    style="background:linear-gradient(135deg,#166534,#15803d);color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:500;display:inline-flex;align-items:center;gap:.35rem;cursor:pointer;">
                    <i class="bi bi-check-lg"></i> Confirmar
                </button>
            </form>
            <button id="dc-btn-cancelar" type="button" onclick="abrirModalCancelarDash()"
                style="background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:500;display:inline-flex;align-items:center;gap:.35rem;cursor:pointer;display:none;">
                <i class="bi bi-x-lg"></i> Cancelar
            </button>
            <button onclick="cerrarDetalleCita()"
                style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;cursor:pointer;margin-left:auto;">
                Cerrar
            </button>
        </div>
        </div>{{-- /padding --}}
    </div>
</div>

{{-- Mini modal cancelar desde dashboard --}}
<div id="modal-cancelar-dash" style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.5);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:420px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative;margin:1rem;">
        <button onclick="cerrarModalCancelarDash()" style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;">✕</button>
        <h5 style="font-weight:700;color:#1c2b22;margin-bottom:.35rem;"><i class="bi bi-x-circle" style="color:#dc2626;"></i> Cancelar cita</h5>
        <p style="font-size:.85rem;color:#6b7280;margin-bottom:1rem;">Indica el motivo para cancelar esta cita.</p>
        <form id="dc-form-cancelar" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="font-size:.8rem;font-weight:600;color:#374151;display:block;margin-bottom:.3rem;">
                    Motivo <span style="color:#dc2626;">*</span>
                </label>
                <textarea name="motivo_cancelacion" rows="3" required
                    style="width:100%;border:1px solid var(--color-muy-claro);border-radius:8px;padding:.5rem .75rem;font-size:.875rem;outline:none;resize:vertical;font-family:inherit;"
                    placeholder="Ej: Paciente llamó para reagendar…"></textarea>
            </div>
            <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                <button type="button" onclick="cerrarModalCancelarDash()"
                    style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;cursor:pointer;">
                    Cerrar
                </button>
                <button type="submit"
                    style="background:#dc2626;color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;cursor:pointer;">
                    <i class="bi bi-x-lg"></i> Confirmar cancelación
                </button>
            </div>
        </form>
    </div>
</div>

@endsection