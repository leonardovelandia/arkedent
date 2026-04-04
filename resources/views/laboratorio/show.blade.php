@extends('layouts.app')
@section('titulo', 'Orden ' . $orden->numero_orden)

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    .orden-header { background:linear-gradient(135deg,var(--color-principal) 0%,var(--color-sidebar-2) 60%,var(--color-sidebar) 100%); border-radius:14px; padding:1.5rem 1.75rem; color:#fff; margin-bottom:1.25rem; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem; }
    .orden-header-main h2 { font-family:var(--fuente-titulos); font-size:1.35rem; font-weight:700; margin:0; }
    .orden-header-sub { font-size:.85rem; opacity:.8; display:flex; gap:.75rem; flex-wrap:wrap; margin-top:.35rem; }

    .badge-est { display:inline-block; padding:.28rem .75rem; border-radius:20px; font-size:.8rem; font-weight:700; white-space:nowrap; }
    .badge-warning  { background:#fff3cd; color:#856404; }
    .badge-info     { background:#d1ecf1; color:#0c5460; }
    .badge-primary  { background:#cce5ff; color:#004085; }
    .badge-success  { background:#d4edda; color:#155724; }
    .badge-dark     { background:#d6d8d9; color:#1b1e21; }
    .badge-danger   { background:#f8d7da; color:#721c24; }

    .doc-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1rem; }
    .doc-section-title { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-hover); padding:.875rem 1.5rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; gap:.4rem; background:var(--fondo-card-alt); }
    .doc-section-body { padding:1.1rem 1.5rem; }
    .dato-row { display:flex; gap:.75rem; margin-bottom:.55rem; align-items:flex-start; }
    .dato-lbl { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#9ca3af; min-width:140px; padding-top:.12rem; flex-shrink:0; }
    .dato-val { font-size:.9rem; color:#1c2b22; font-weight:500; }
    .dato-grid { display:grid; grid-template-columns:1fr 1fr; gap:.5rem 1.5rem; }

    /* ── Timeline ──────────────────────────────────────────────────────── */
    .timeline-wrap { display:flex; align-items:center; justify-content:center; gap:0; padding:1.5rem 1.5rem; overflow-x:auto; }
    .tl-step { display:flex; flex-direction:column; align-items:center; gap:.4rem; min-width:90px; }
    .tl-circle { width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1rem; font-weight:700; border:2px solid; flex-shrink:0; }
    .tl-done   { background:var(--color-principal); border-color:var(--color-principal); color:#fff; }
    .tl-current{ background:var(--color-muy-claro); border-color:var(--color-principal); color:var(--color-principal); }
    .tl-pending{ background:#f3f4f6; border-color:#d1d5db; color:#9ca3af; }
    .tl-cancelled { background:#f8d7da; border-color:#dc2626; color:#dc2626; }
    .tl-label  { font-size:.72rem; font-weight:600; color:#374151; text-align:center; }
    .tl-fecha  { font-size:.68rem; color:#9ca3af; text-align:center; }
    .tl-line   { flex:1; height:2px; background:#e5e7eb; min-width:30px; }
    .tl-line-done { background:var(--color-principal); }

    .acciones-wrap { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1.25rem; }

    @media(max-width:700px) { .dato-grid { grid-template-columns:1fr; } }

    /* Clásico */
    body:not([data-ui="glass"]) .doc-card { background:#fff; border:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .doc-section-title { color:var(--color-hover); border-bottom:1px solid var(--fondo-borde); background:var(--fondo-card-alt); }
    body:not([data-ui="glass"]) .dato-lbl { color:#9ca3af; }
    body:not([data-ui="glass"]) .dato-val { color:#1c2b22; }
    body:not([data-ui="glass"]) .tl-pending { background:#f3f4f6; border-color:#d1d5db; color:#9ca3af; }
    body:not([data-ui="glass"]) .tl-label { color:#374151; }
    body:not([data-ui="glass"]) .tl-fecha { color:#9ca3af; }
    body:not([data-ui="glass"]) .tl-line { background:#e5e7eb; }
    body:not([data-ui="glass"]) .badge-warning { background:#fff3cd; color:#856404; }
    body:not([data-ui="glass"]) .badge-info    { background:#d1ecf1; color:#0c5460; }
    body:not([data-ui="glass"]) .badge-primary { background:#cce5ff; color:#004085; }
    body:not([data-ui="glass"]) .badge-success { background:#d4edda; color:#155724; }
    body:not([data-ui="glass"]) .badge-dark    { background:#d6d8d9; color:#1b1e21; }
    body:not([data-ui="glass"]) .badge-danger  { background:#f8d7da; color:#721c24; }

    /* Glass */
    body[data-ui="glass"] .doc-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .doc-section-title { background:rgba(0,0,0,0.25) !important; color:rgba(0,234,255,0.90) !important; border-bottom:1px solid rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .dato-lbl { color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .dato-val { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .tl-pending { background:rgba(255,255,255,0.06) !important; border-color:rgba(255,255,255,0.20) !important; color:rgba(255,255,255,0.40) !important; }
    body[data-ui="glass"] .tl-label { color:rgba(255,255,255,0.88) !important; }
    body[data-ui="glass"] .tl-fecha { color:rgba(255,255,255,0.45) !important; }
    body[data-ui="glass"] .tl-line { background:rgba(255,255,255,0.10) !important; }
    body[data-ui="glass"] .tl-line-done { background:rgba(0,234,255,0.50) !important; }
    body[data-ui="glass"] .badge-warning { background:rgba(251,191,36,0.20) !important; color:#fbbf24 !important; border:1px solid rgba(251,191,36,0.35) !important; }
    body[data-ui="glass"] .badge-info    { background:rgba(0,234,255,0.12) !important; color:rgba(0,234,255,0.90) !important; border:1px solid rgba(0,234,255,0.30) !important; }
    body[data-ui="glass"] .badge-primary { background:rgba(0,234,255,0.12) !important; color:rgba(0,234,255,0.90) !important; border:1px solid rgba(0,234,255,0.30) !important; }
    body[data-ui="glass"] .badge-success { background:rgba(74,222,128,0.20) !important; color:#86efac !important; border:1px solid rgba(74,222,128,0.35) !important; }
    body[data-ui="glass"] .badge-dark    { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.55) !important; border:1px solid rgba(255,255,255,0.15) !important; }
    body[data-ui="glass"] .badge-danger  { background:rgba(248,113,113,0.20) !important; color:#fca5a5 !important; border:1px solid rgba(248,113,113,0.35) !important; }
    body[data-ui="glass"] .btn-volver    { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif
@if(session('aviso'))
<div class="alerta-flash" style="background:#fff3cd;color:#856404;border:1px solid #ffc107;">
    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('aviso') }}
</div>
@endif

{{-- Header --}}
<div class="orden-header">
    <div class="orden-header-main">
        <h2>{{ $orden->paciente->nombre_completo }}</h2>
        <div class="orden-header-sub">
            <span><i class="bi bi-hash"></i> {{ $orden->numero_orden }}</span>
            <span><i class="bi bi-building"></i> {{ $orden->laboratorio->nombre }}</span>
            <span><i class="bi bi-tools"></i> {{ $orden->tipo_trabajo }}</span>
        </div>
    </div>
    <span class="badge-est badge-{{ $orden->estado_color }}" style="font-size:.9rem; padding:.45rem 1.1rem;">
        {{ $orden->estado_label }}
    </span>
</div>

{{-- Botones de acción --}}
<div class="acciones-wrap">
    <a href="{{ route('laboratorio.index') }}"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;">
        <i class="bi bi-arrow-left"></i> Volver
    </a>

    @if(!in_array($orden->estado, ['instalado','cancelado']))
    <a href="{{ route('laboratorio.edit', $orden) }}"
       style="background:transparent;color:var(--color-principal);border:1px solid var(--color-principal);border-radius:8px;padding:.45rem 1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;">
        <i class="bi bi-pencil"></i> Editar
    </a>
    @endif

    @if($orden->estado === 'pendiente')
    <button type="button" onclick="abrirModal('modal-enviar')" class="btn-morado" style="background:linear-gradient(135deg,#1565c0,#1976d2);">
        <i class="bi bi-send"></i> Marcar como Enviado
    </button>
    @endif

    @if(in_array($orden->estado, ['enviado','en_proceso']))
    <button type="button" onclick="abrirModal('modal-recibir')" class="btn-morado" style="background:linear-gradient(135deg,#166534,#15803d);">
        <i class="bi bi-box-arrow-in-down"></i> Registrar Recepción
    </button>
    @endif

    @if($orden->estado === 'recibido')
    <button type="button" onclick="abrirModal('modal-instalar')" class="btn-morado">
        <i class="bi bi-check-circle"></i> Registrar Instalación
    </button>
    @endif

    @if(!in_array($orden->estado, ['instalado','cancelado']))
    <button type="button" onclick="abrirModal('modal-cancelar')" class="btn-morado" style="background:linear-gradient(135deg,#dc2626,#ef4444);">
        <i class="bi bi-x-circle"></i> Cancelar Orden
    </button>
    @endif

    {{-- PDF e Imprimir --}}
    <a href="{{ route('laboratorio.pdf', $orden) }}" target="_blank"
       style="background:transparent;color:#dc2626;border:1px solid #dc2626;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;">
        <i class="bi bi-filetype-pdf"></i> Ver PDF
    </a>
    <a href="{{ route('laboratorio.pdf', $orden) }}?raw=1" target="_blank"
       style="background:transparent;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;"
       onclick="event.preventDefault(); window.open(this.href).print();">
        <i class="bi bi-printer"></i> Imprimir
    </a>
</div>

{{-- Documento principal --}}
<div class="doc-card">

    {{-- Timeline --}}
    <div class="doc-section-title"><i class="bi bi-diagram-3"></i> Estado de la Orden</div>
    @php
        $pasos = [
            ['estado' => 'pendiente',  'label' => 'Creada',    'icon' => 'bi-plus-circle', 'fecha' => $orden->created_at?->format('d/m/Y')],
            ['estado' => 'enviado',    'label' => 'Enviada',   'icon' => 'bi-send',        'fecha' => $orden->fecha_envio?->format('d/m/Y')],
            ['estado' => 'en_proceso', 'label' => 'En Proc.',  'icon' => 'bi-gear',        'fecha' => null],
            ['estado' => 'recibido',   'label' => 'Recibida',  'icon' => 'bi-box-arrow-in-down', 'fecha' => $orden->fecha_recepcion?->format('d/m/Y')],
            ['estado' => 'instalado',  'label' => 'Instalada', 'icon' => 'bi-check-circle','fecha' => $orden->fecha_instalacion?->format('d/m/Y')],
        ];
        $ordenEstados = ['pendiente', 'enviado', 'en_proceso', 'recibido', 'instalado'];
        $idxActual = array_search($orden->estado, $ordenEstados) ?? 0;
        if ($orden->estado === 'cancelado') $idxActual = -1;
    @endphp
    <div class="timeline-wrap">
        @foreach($pasos as $i => $paso)
            @if($i > 0)
                <div class="tl-line {{ $i <= $idxActual ? 'tl-line-done' : '' }}"></div>
            @endif
            @if($orden->estado === 'cancelado')
                <div class="tl-step">
                    <div class="tl-circle tl-cancelled"><i class="bi {{ $paso['icon'] }}"></i></div>
                    <div class="tl-label">{{ $paso['label'] }}</div>
                    <div class="tl-fecha">{{ $paso['fecha'] ?: '—' }}</div>
                </div>
            @elseif($i < $idxActual)
                <div class="tl-step">
                    <div class="tl-circle tl-done"><i class="bi bi-check-lg"></i></div>
                    <div class="tl-label">{{ $paso['label'] }}</div>
                    <div class="tl-fecha">{{ $paso['fecha'] ?: '✓' }}</div>
                </div>
            @elseif($i == $idxActual)
                <div class="tl-step">
                    <div class="tl-circle tl-current"><i class="bi {{ $paso['icon'] }}"></i></div>
                    <div class="tl-label" style="color:var(--color-principal); font-weight:700;">{{ $paso['label'] }}</div>
                    <div class="tl-fecha" style="color:var(--color-principal);">{{ $paso['fecha'] ?: 'Ahora' }}</div>
                </div>
            @else
                <div class="tl-step">
                    <div class="tl-circle tl-pending"><i class="bi {{ $paso['icon'] }}"></i></div>
                    <div class="tl-label" style="color:#9ca3af;">{{ $paso['label'] }}</div>
                    <div class="tl-fecha">—</div>
                </div>
            @endif
        @endforeach
        @if($orden->estado === 'cancelado')
            <div class="tl-line" style="background:#dc2626;"></div>
            <div class="tl-step">
                <div class="tl-circle tl-cancelled"><i class="bi bi-x-circle"></i></div>
                <div class="tl-label" style="color:#dc2626;">Cancelada</div>
                <div class="tl-fecha">—</div>
            </div>
        @endif
    </div>

    {{-- Datos de la orden --}}
    <div class="doc-section-title"><i class="bi bi-clipboard2"></i> Datos de la Orden</div>
    <div class="doc-section-body">
        <div class="dato-grid">
            <div>
                <div class="dato-row"><span class="dato-lbl">N° Orden</span><span class="dato-val" style="color:var(--color-principal); font-weight:700;">{{ $orden->numero_orden }}</span></div>
                <div class="dato-row"><span class="dato-lbl">Paciente</span><span class="dato-val">{{ $orden->paciente->nombre_completo }}</span></div>
                <div class="dato-row"><span class="dato-lbl">Laboratorio</span><span class="dato-val">{{ $orden->laboratorio->nombre }}</span></div>
                <div class="dato-row"><span class="dato-lbl">Doctor</span><span class="dato-val">{{ $orden->doctor->name ?? '—' }}</span></div>
            </div>
            <div>
                <div class="dato-row"><span class="dato-lbl">Tipo de Trabajo</span><span class="dato-val">{{ $orden->tipo_trabajo }}</span></div>
                <div class="dato-row"><span class="dato-lbl">Dientes</span><span class="dato-val">{{ $orden->dientes ?: '—' }}</span></div>
                <div class="dato-row"><span class="dato-lbl">Color</span><span class="dato-val">{{ $orden->color_diente ?: '—' }}</span></div>
                <div class="dato-row"><span class="dato-lbl">Material</span><span class="dato-val">{{ $orden->material ?: '—' }}</span></div>
            </div>
        </div>
    </div>

    {{-- Descripción --}}
    <div class="doc-section-title"><i class="bi bi-card-text"></i> Descripción del Trabajo</div>
    <div class="doc-section-body">
        <p style="font-size:.9rem; color:#1c2b22; white-space:pre-line; margin:0;">{{ $orden->descripcion }}</p>
    </div>

    {{-- Fechas --}}
    <div class="doc-section-title"><i class="bi bi-calendar3"></i> Fechas</div>
    <div class="doc-section-body">
        <div class="dato-grid">
            <div>
                <div class="dato-row"><span class="dato-lbl">Fecha Creación</span><span class="dato-val">{{ $orden->created_at->format('d/m/Y H:i') }}</span></div>
                <div class="dato-row"><span class="dato-lbl">Fecha Envío</span><span class="dato-val">{{ $orden->fecha_envio?->format('d/m/Y') ?: '—' }}</span></div>
                <div class="dato-row">
                    <span class="dato-lbl">Entrega Estimada</span>
                    <span class="dato-val {{ $orden->esta_vencido ? 'text-danger' : '' }}" style="{{ $orden->esta_vencido ? 'color:#dc2626; font-weight:700;' : '' }}">
                        {{ $orden->fecha_entrega_estimada?->format('d/m/Y') ?: '—' }}
                        @if($orden->esta_vencido) <i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;"></i> VENCIDA @endif
                    </span>
                </div>
            </div>
            <div>
                <div class="dato-row"><span class="dato-lbl">Fecha Recepción</span><span class="dato-val">{{ $orden->fecha_recepcion?->format('d/m/Y') ?: '—' }}</span></div>
                <div class="dato-row"><span class="dato-lbl">Fecha Instalación</span><span class="dato-val">{{ $orden->fecha_instalacion?->format('d/m/Y') ?: '—' }}</span></div>
            </div>
        </div>
    </div>

    {{-- Costos --}}
    @if($orden->precio_laboratorio)
    <div class="doc-section-title"><i class="bi bi-cash-stack"></i> Costos</div>
    <div class="doc-section-body">
        <div class="dato-row">
            <span class="dato-lbl">Precio Laboratorio</span>
            <span class="dato-val" style="font-size:1.1rem; color:var(--color-principal); font-weight:700;">
                $ {{ number_format($orden->precio_laboratorio, 0, ',', '.') }}
            </span>
        </div>
    </div>
    @endif

    {{-- Observaciones --}}
    @if($orden->observaciones_envio || $orden->observaciones_recepcion || $orden->calidad_recibida || $orden->requiere_ajuste || $orden->motivo_cancelacion)
    <div class="doc-section-title"><i class="bi bi-chat-left-text"></i> Observaciones</div>
    <div class="doc-section-body">
        @if($orden->observaciones_envio)
        <div class="dato-row"><span class="dato-lbl">Observ. Envío</span><span class="dato-val" style="white-space:pre-line;">{{ $orden->observaciones_envio }}</span></div>
        @endif
        @if($orden->observaciones_recepcion)
        <div class="dato-row"><span class="dato-lbl">Observ. Recepción</span><span class="dato-val" style="white-space:pre-line;">{{ $orden->observaciones_recepcion }}</span></div>
        @endif
        @if($orden->calidad_recibida)
        <div class="dato-row">
            <span class="dato-lbl">Calidad Recibida</span>
            <span class="dato-val">
                @php
                    $cBadge = match($orden->calidad_recibida) {
                        'excelente' => 'success', 'buena' => 'info',
                        'regular' => 'warning', 'mala' => 'danger', default => 'secondary'
                    };
                @endphp
                <span class="badge-est badge-{{ $cBadge }}">{{ ucfirst($orden->calidad_recibida) }}</span>
            </span>
        </div>
        @endif
        @if($orden->requiere_ajuste)
        <div class="dato-row">
            <span class="dato-lbl">Ajuste</span>
            <span class="dato-val"><span style="background:#fff3cd; color:#856404; padding:.2rem .6rem; border-radius:50px; font-size:.78rem; font-weight:700;"><i class="bi bi-tools"></i> Requiere ajuste antes de instalar</span></span>
        </div>
        @endif
        @if($orden->motivo_cancelacion)
        <div class="dato-row"><span class="dato-lbl">Mot. Cancelación</span><span class="dato-val" style="color:#dc2626;">{{ $orden->motivo_cancelacion }}</span></div>
        @endif
    </div>
    @endif

    {{-- Paciente y evolución --}}
    <div class="doc-section-title"><i class="bi bi-person-circle"></i> Paciente y Evolución</div>
    <div class="doc-section-body">
        <div class="dato-row">
            <span class="dato-lbl">Paciente</span>
            <span class="dato-val">
                {{ $orden->paciente->nombre_completo }}
                <a href="{{ route('pacientes.show', $orden->paciente) }}"
                   style="font-size:.8rem; color:var(--color-principal); text-decoration:none; margin-left:.5rem;">
                    <i class="bi bi-box-arrow-up-right"></i> Ver ficha
                </a>
            </span>
        </div>
        @if($orden->evolucion)
        <div class="dato-row">
            <span class="dato-lbl">Evolución</span>
            <span class="dato-val">
                {{ $orden->evolucion->numero_evolucion }}
                <a href="{{ route('evoluciones.show', $orden->evolucion) }}"
                   style="font-size:.8rem; color:var(--color-principal); text-decoration:none; margin-left:.5rem;">
                    <i class="bi bi-box-arrow-up-right"></i> Ver evolución
                </a>
            </span>
        </div>
        @endif
    </div>

</div>

{{-- ── MODALES ──────────────────────────────────────────────────────── --}}

{{-- Modal: Enviar --}}
<div id="modal-enviar" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:440px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative;">
        <button onclick="cerrarModal('modal-enviar')" style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;">✕</button>
        <h5 style="font-weight:700;color:#1c2b22;margin-bottom:.5rem;"><i class="bi bi-send" style="color:#1565c0;"></i> Marcar como Enviado</h5>
        <p style="font-size:.85rem;color:#6b7280;margin-bottom:1rem;">Confirma la fecha de envío al laboratorio.</p>
        <form method="POST" action="{{ route('laboratorio.enviar', $orden) }}">
            @csrf
            <div style="margin-bottom:.875rem;">
                <label style="font-size:.78rem;font-weight:700;color:#374151;display:block;margin-bottom:.3rem;">Fecha de Envío</label>
                <input type="date" name="fecha_envio" value="{{ date('Y-m-d') }}"
                       style="width:100%;border:1px solid var(--color-muy-claro);border-radius:8px;padding:.45rem .75rem;font-size:.875rem;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="font-size:.78rem;font-weight:700;color:#374151;display:block;margin-bottom:.3rem;">Observaciones de Envío</label>
                <textarea name="observaciones_envio" rows="3"
                          style="width:100%;border:1px solid var(--color-muy-claro);border-radius:8px;padding:.45rem .75rem;font-size:.875rem;resize:vertical;font-family:inherit;"
                          placeholder="Ej: Se envió con mensajero, impresiones adjuntas...">{{ $orden->observaciones_envio }}</textarea>
            </div>
            <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                <button type="button" onclick="cerrarModal('modal-enviar')" style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;cursor:pointer;">Cerrar</button>
                <button type="submit" style="background:linear-gradient(135deg,#1565c0,#1976d2);color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;cursor:pointer;">
                    <i class="bi bi-send"></i> Confirmar Envío
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Recibir --}}
<div id="modal-recibir" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:480px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative;">
        <button onclick="cerrarModal('modal-recibir')" style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;">✕</button>
        <h5 style="font-weight:700;color:#1c2b22;margin-bottom:.5rem;"><i class="bi bi-box-arrow-in-down" style="color:#166534;"></i> Registrar Recepción</h5>
        <p style="font-size:.85rem;color:#6b7280;margin-bottom:1rem;">Confirma que el trabajo llegó al consultorio.</p>
        <form method="POST" action="{{ route('laboratorio.recibir', $orden) }}">
            @csrf
            <div class="form-grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:.875rem;margin-bottom:.875rem;">
                <div>
                    <label style="font-size:.78rem;font-weight:700;color:#374151;display:block;margin-bottom:.3rem;">Fecha de Recepción</label>
                    <input type="date" name="fecha_recepcion" value="{{ date('Y-m-d') }}"
                           style="width:100%;border:1px solid var(--color-muy-claro);border-radius:8px;padding:.45rem .75rem;font-size:.875rem;">
                </div>
                <div>
                    <label style="font-size:.78rem;font-weight:700;color:#374151;display:block;margin-bottom:.3rem;">Calidad Recibida <span style="color:#dc2626;">*</span></label>
                    <select name="calidad_recibida" required style="width:100%;border:1px solid var(--color-muy-claro);border-radius:8px;padding:.45rem .75rem;font-size:.875rem;">
                        <option value="">Seleccionar...</option>
                        <option value="excelente">Excelente</option>
                        <option value="buena">Buena</option>
                        <option value="regular">Regular</option>
                        <option value="mala">Mala</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom:.875rem;">
                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.875rem;color:#374151;">
                    <input type="checkbox" name="requiere_ajuste" value="1">
                    Requiere ajuste antes de instalar
                </label>
            </div>
            <div style="margin-bottom:1rem;">
                <label style="font-size:.78rem;font-weight:700;color:#374151;display:block;margin-bottom:.3rem;">Observaciones de Recepción</label>
                <textarea name="observaciones_recepcion" rows="3"
                          style="width:100%;border:1px solid var(--color-muy-claro);border-radius:8px;padding:.45rem .75rem;font-size:.875rem;resize:vertical;font-family:inherit;"
                          placeholder="Estado del trabajo, notas de calidad..."></textarea>
            </div>
            <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                <button type="button" onclick="cerrarModal('modal-recibir')" style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;cursor:pointer;">Cerrar</button>
                <button type="submit" style="background:linear-gradient(135deg,#166534,#15803d);color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;cursor:pointer;">
                    <i class="bi bi-box-arrow-in-down"></i> Confirmar Recepción
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Instalar --}}
<div id="modal-instalar" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:400px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative;">
        <button onclick="cerrarModal('modal-instalar')" style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;">✕</button>
        <h5 style="font-weight:700;color:#1c2b22;margin-bottom:.5rem;"><i class="bi bi-check-circle" style="color:var(--color-principal);"></i> Registrar Instalación</h5>
        <p style="font-size:.85rem;color:#6b7280;margin-bottom:1rem;">El trabajo ha sido instalado exitosamente en el paciente.</p>
        <form method="POST" action="{{ route('laboratorio.instalar', $orden) }}">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="font-size:.78rem;font-weight:700;color:#374151;display:block;margin-bottom:.3rem;">Fecha de Instalación</label>
                <input type="date" name="fecha_instalacion" value="{{ date('Y-m-d') }}"
                       style="width:100%;border:1px solid var(--color-muy-claro);border-radius:8px;padding:.45rem .75rem;font-size:.875rem;">
            </div>
            <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                <button type="button" onclick="cerrarModal('modal-instalar')" style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;cursor:pointer;">Cerrar</button>
                <button type="submit" class="btn-morado">
                    <i class="bi bi-check-circle"></i> Confirmar Instalación
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Cancelar --}}
<div id="modal-cancelar" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:440px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative;">
        <button onclick="cerrarModal('modal-cancelar')" style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;">✕</button>
        <h5 style="font-weight:700;color:#1c2b22;margin-bottom:.5rem;"><i class="bi bi-x-circle" style="color:#dc2626;"></i> Cancelar Orden</h5>
        <p style="font-size:.85rem;color:#6b7280;margin-bottom:1rem;">Indica el motivo para cancelar esta orden.</p>
        <form method="POST" action="{{ route('laboratorio.cancelar', $orden) }}">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="font-size:.78rem;font-weight:700;color:#374151;display:block;margin-bottom:.3rem;">Motivo <span style="color:#dc2626;">*</span></label>
                <textarea name="motivo_cancelacion" rows="3" required
                          style="width:100%;border:1px solid var(--color-muy-claro);border-radius:8px;padding:.5rem .75rem;font-size:.875rem;resize:vertical;font-family:inherit;"
                          placeholder="Ej: Paciente decidió no realizarse el procedimiento..."></textarea>
            </div>
            <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                <button type="button" onclick="cerrarModal('modal-cancelar')" style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;cursor:pointer;">Cerrar</button>
                <button type="submit" style="background:#dc2626;color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;cursor:pointer;">
                    <i class="bi bi-x-circle"></i> Confirmar Cancelación
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function abrirModal(id) {
    document.getElementById(id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function cerrarModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        ['modal-enviar','modal-recibir','modal-instalar','modal-cancelar'].forEach(cerrarModal);
    }
});
['modal-enviar','modal-recibir','modal-instalar','modal-cancelar'].forEach(function(id) {
    var el = document.getElementById(id);
    if (el) el.addEventListener('click', function(e) { if (e.target === this) cerrarModal(id); });
});
</script>
@endpush
