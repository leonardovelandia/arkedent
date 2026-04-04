@extends('layouts.app')
@section('titulo', 'Historia Clínica — ' . $historia->paciente->nombre_completo)

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-out { background:transparent; color:var(--color-principal); border:1px solid var(--color-principal); border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-out:hover { background:var(--color-muy-claro); color:var(--color-hover); }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; color:#1f2937; }

    .hist-header { background:linear-gradient(135deg,var(--color-principal) 0%,var(--color-sidebar-2) 60%,var(--color-sidebar) 100%); border-radius:14px; padding:1.75rem; color:#fff; display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap; margin-bottom:1.5rem; }
    .hist-avatar { width:72px; height:72px; border-radius:50%; background:rgba(255,255,255,.18); color:#fff; font-size:1.5rem; font-weight:700; display:flex; align-items:center; justify-content:center; border:3px solid rgba(255,255,255,.3); flex-shrink:0; }
    .hist-titulo { font-family:var(--fuente-titulos); font-size:1.4rem; font-weight:600; margin-bottom:.35rem; }
    .hist-meta { display:flex; flex-wrap:wrap; gap:.85rem; }
    .hist-meta-item { display:flex; align-items:center; gap:.35rem; font-size:.82rem; opacity:.85; }

    .sec-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; margin-bottom:1.25rem; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .sec-header { background:var(--color-muy-claro); padding:.75rem 1.25rem; border-bottom:1px solid var(--color-muy-claro); display:flex; align-items:center; gap:.5rem; }
    .sec-header h6 { margin:0; font-size:.82rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); }
    .sec-body { padding:1.25rem; }

    .dato-lbl { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#9ca3af; margin-bottom:.2rem; }
    .dato-val { font-size:.9rem; color:#1c2b22; font-weight:500; white-space:pre-line; }
    .dato-vacio { color:#d1d5db; font-style:italic; }

    .vital-box { background:var(--fondo-card-alt); border:1px solid var(--color-muy-claro); border-radius:10px; padding:.85rem 1rem; text-align:center; }
    .vital-val { font-size:1.1rem; font-weight:700; color:var(--color-principal); }
    .vital-lbl { font-size:.72rem; color:#9ca3af; font-weight:600; text-transform:uppercase; margin-top:.15rem; }

    /* Odontograma solo lectura */
    .odonto-wrap { overflow-x:auto; }
    .odonto-cuadrantes { display:flex; justify-content:center; }
    .odonto-cuadrante { display:flex; gap:4px; }
    .odonto-separador { width:2px; background:var(--color-muy-claro); margin:0 6px; border-radius:2px; }
    .odonto-linea-media { border-top:2px dashed var(--color-muy-claro); margin:6px 0; }
    .odonto-label { text-align:center; font-size:.7rem; color:#9ca3af; font-weight:600; margin:4px 0; }

    .diente-ro { width:36px; height:42px; border:2px solid #d1d5db; border-radius:6px; display:flex; flex-direction:column; align-items:center; justify-content:center; background:#fff; font-size:.65rem; font-weight:700; color:#6b7280; }
    .diente-ro.sano       { background:#fff;    border-color:#d1d5db; color:#6b7280; }
    .diente-ro.caries     { background:#FFF3CD; border-color:#FFA500; color:#856404; }
    .diente-ro.extraccion { background:#FECDD3; border-color:#DC3545; color:#991b1b; }
    .diente-ro.extraido   { background:#F3F4F6; border-color:#6C757D; color:#374151; }
    .diente-ro.corona     { background:#DBEAFE; border-color:#0D6EFD; color:#1d4ed8; }
    .diente-ro.tratado    { background:var(--color-muy-claro); border-color:var(--color-principal); color:var(--color-hover); }
    .diente-num { font-size:.62rem; font-weight:700; }
    .diente-ico { font-size:.85rem; margin-top:1px; }

    .evol-vacia { text-align:center; padding:2rem; color:#9ca3af; }
    .evol-vacia i { font-size:2rem; color:var(--color-acento-activo); display:block; margin-bottom:.5rem; }

    /* Clásico */
    body:not([data-ui="glass"]) .sec-card { background:#fff; border:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .sec-header { background:var(--color-muy-claro); border-bottom:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .sec-header h6 { color:var(--color-hover); }
    body:not([data-ui="glass"]) .dato-lbl { color:#9ca3af; }
    body:not([data-ui="glass"]) .dato-val { color:#1c2b22; }
    body:not([data-ui="glass"]) .dato-vacio { color:#d1d5db; }
    body:not([data-ui="glass"]) .vital-box { background:var(--fondo-card-alt); border:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .vital-val { color:var(--color-principal); }
    body:not([data-ui="glass"]) .vital-lbl { color:#9ca3af; }
    body:not([data-ui="glass"]) .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }

    /* Glass */
    body[data-ui="glass"] .sec-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .sec-header { background:rgba(0,0,0,0.25) !important; border-bottom:1px solid rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .sec-header h6 { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .dato-lbl { color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .dato-val { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .dato-vacio { color:rgba(255,255,255,0.25) !important; }
    body[data-ui="glass"] .vital-box { background:rgba(0,234,255,0.06) !important; border:1px solid rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .vital-val { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .vital-lbl { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .evol-vacia { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .btn-gris { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
    body[data-ui="glass"] .diente-ro { background:rgba(255,255,255,0.08) !important; border-color:rgba(255,255,255,0.20) !important; color:rgba(255,255,255,0.70) !important; }
    body[data-ui="glass"] .diente-ro.caries { background:rgba(251,191,36,0.15) !important; border-color:rgba(251,191,36,0.50) !important; color:#fbbf24 !important; }
    body[data-ui="glass"] .diente-ro.extraccion, body[data-ui="glass"] .diente-ro.extraido { background:rgba(248,113,113,0.15) !important; border-color:rgba(248,113,113,0.50) !important; color:#fca5a5 !important; }
    body[data-ui="glass"] .diente-ro.corona { background:rgba(0,234,255,0.12) !important; border-color:rgba(0,234,255,0.40) !important; color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .diente-ro.tratado { background:rgba(74,222,128,0.15) !important; border-color:rgba(74,222,128,0.40) !important; color:#86efac !important; }
    body[data-ui="glass"] .odonto-separador { background:rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .odonto-linea-media { border-top-color:rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .odonto-label { color:rgba(0,234,255,0.70) !important; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
    <div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
        <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
    </div>
@endif

{{-- Header --}}
<div class="hist-header">
    <div class="hist-avatar">
        {{ strtoupper(substr($historia->paciente->nombre,0,1)) }}{{ strtoupper(substr($historia->paciente->apellido,0,1)) }}
    </div>
    <div style="flex:1;">
        <div class="hist-titulo">Historia Clínica
            @if($historia->numero_historia)
            <span style="font-family:monospace;font-size:.82rem;font-weight:700;background:rgba(255,255,255,.2);border-radius:6px;padding:.1rem .6rem;margin-left:.5rem;letter-spacing:.03em;">{{ $historia->numero_historia }}</span>
            @endif
        </div>
        <div style="font-size:1rem;opacity:.85;margin-bottom:.4rem;">{{ $historia->paciente->nombre_completo }}</div>
        <div class="hist-meta">
            <span class="hist-meta-item"><i class="bi bi-journal-medical"></i> {{ $historia->paciente->numero_historia }}</span>
            <span class="hist-meta-item"><i class="bi bi-calendar3"></i> Apertura: {{ $historia->fecha_apertura->format('d/m/Y') }}</span>
            <span class="hist-meta-item"><i class="bi bi-cake2"></i> {{ $historia->paciente->edad }} años</span>
            <span class="hist-meta-item"><i class="bi bi-telephone"></i> {{ $historia->paciente->telefono }}</span>
        </div>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
        @if($historia->firmado)
        <a href="{{ route('historias.correccion.vista', $historia) }}" style="background:#d97706;color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;display:inline-flex;align-items:center;gap:.35rem;text-decoration:none;">
            <i class="bi bi-pencil-square"></i> Agregar Corrección
        </a>
        @else
        <a href="{{ route('historias.edit', $historia) }}" class="btn-out" style="background:rgba(255,255,255,.12);color:#fff;border-color:rgba(255,255,255,.3);">
            <i class="bi bi-pencil"></i> Editar
        </a>
        @endif
        <a href="{{ route('evoluciones.create', ['paciente_id' => $historia->paciente_id]) }}"
           class="btn-out" style="background:rgba(255,255,255,.08);color:#fff;border-color:rgba(255,255,255,.25);">
            <i class="bi bi-clipboard2-plus"></i> Nueva Evolución
        </a>
        <a href="{{ route('pacientes.show', $historia->paciente) }}" class="btn-out" style="background:rgba(255,255,255,.08);color:#fff;border-color:rgba(255,255,255,.25);">
            <i class="bi bi-arrow-left"></i> Ver Paciente
        </a>
        @if(!$historia->firmado)
        <a href="{{ route('historias.firmar.vista', $historia) }}" style="background:#d97706;color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;display:inline-flex;align-items:center;gap:.35rem;text-decoration:none;">
            <i class="bi bi-pen"></i> Firmar Historia
        </a>
        @else
        <span style="background:#d1fae5;color:#166534;padding:.3rem .75rem;border-radius:8px;font-size:.82rem;font-weight:700;display:inline-flex;align-items:center;gap:.3rem;">
            <i class="bi bi-check-circle-fill"></i> Firmada
        </span>
        @endif
        <a href="{{ route('historias.pdf', $historia) }}" target="_blank" style="background:var(--color-principal);color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;display:inline-flex;align-items:center;gap:.35rem;text-decoration:none;">
            <i class="bi bi-file-earmark-pdf"></i> Ver PDF
        </a>
    </div>
</div>

{{-- UN SOLO CARD tipo hoja de documento --}}
<div style="background:#ffffff; border:1px solid var(--fondo-borde); border-radius:14px; overflow:hidden; margin-bottom:1.5rem; box-shadow: 0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">

    {{-- SECCIÓN 1: MOTIVO DE CONSULTA --}}
    <div style="padding:1.25rem 1.75rem; border-bottom:1px solid var(--color-muy-claro);">
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
            <i class="bi bi-chat-square-text" style="color:var(--color-principal);"></i>
            <span style="font-size:0.72rem; font-weight:600; color:var(--color-principal); letter-spacing:0.1em; text-transform:uppercase;">Motivo de Consulta</span>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
            <div>
                <div style="font-size:0.7rem; font-weight:500; color:#8fa39a; letter-spacing:0.06em; text-transform:uppercase; margin-bottom:0.35rem;">Motivo de consulta</div>
                <div style="font-size:0.92rem; color:#1c2b22;">{{ $historia->motivo_consulta }}</div>
            </div>
            @if($historia->enfermedad_actual)
            <div>
                <div style="font-size:0.7rem; font-weight:500; color:#8fa39a; letter-spacing:0.06em; text-transform:uppercase; margin-bottom:0.35rem;">Enfermedad actual</div>
                <div style="font-size:0.92rem; color:#1c2b22;">{{ $historia->enfermedad_actual }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- SECCIÓN 2: ANTECEDENTES MÉDICOS Y ODONTOLÓGICOS --}}
    <div style="padding:1.25rem 1.75rem; border-bottom:1px solid var(--color-muy-claro); background:var(--fondo-card-alt);">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
            {{-- Antecedentes médicos --}}
            <div>
                <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.85rem;">
                    <i class="bi bi-heart-pulse" style="color:var(--color-principal);"></i>
                    <span style="font-size:0.72rem; font-weight:600; color:var(--color-principal); letter-spacing:0.1em; text-transform:uppercase;">Antecedentes Médicos</span>
                </div>
                @if($historia->antecedentes_medicos)
                <div style="margin-bottom:0.75rem;">
                    <div style="font-size:0.7rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.25rem;">Enfermedades previas</div>
                    <div style="font-size:0.88rem; color:#1c2b22;">{{ $historia->antecedentes_medicos }}</div>
                </div>
                @endif
                @if($historia->medicamentos_actuales)
                <div style="margin-bottom:0.75rem;">
                    <div style="font-size:0.7rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.25rem;">Medicamentos actuales</div>
                    <div style="font-size:0.88rem; color:#1c2b22;">{{ $historia->medicamentos_actuales }}</div>
                </div>
                @endif
                @if($historia->alergias)
                <div style="margin-bottom:0.75rem;">
                    <div style="font-size:0.7rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.25rem;">Alergias</div>
                    <div style="font-size:0.88rem; color:#dc3545; font-weight:500;">⚠ {{ $historia->alergias }}</div>
                </div>
                @endif
                @if($historia->antecedentes_familiares)
                <div>
                    <div style="font-size:0.7rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.25rem;">Antecedentes familiares</div>
                    <div style="font-size:0.88rem; color:#1c2b22;">{{ $historia->antecedentes_familiares }}</div>
                </div>
                @endif
            </div>
            {{-- Antecedentes odontológicos --}}
            <div>
                <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.85rem;">
                    <i class="bi bi-clipboard2-pulse" style="color:var(--color-principal);"></i>
                    <span style="font-size:0.72rem; font-weight:600; color:var(--color-principal); letter-spacing:0.1em; text-transform:uppercase;">Antecedentes Odontológicos</span>
                </div>
                @if($historia->antecedentes_odontologicos)
                <div style="margin-bottom:0.75rem;">
                    <div style="font-size:0.7rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.25rem;">Antecedentes odontológicos</div>
                    <div style="font-size:0.88rem; color:#1c2b22;">{{ $historia->antecedentes_odontologicos }}</div>
                </div>
                @endif
                @if($historia->habitos)
                <div>
                    <div style="font-size:0.7rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.25rem;">Hábitos</div>
                    <div style="font-size:0.88rem; color:#1c2b22;">{{ $historia->habitos }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- SECCIÓN 3: SIGNOS VITALES --}}
    @if($historia->presion_arterial || $historia->frecuencia_cardiaca || $historia->temperatura || $historia->peso || $historia->talla)
    <div style="padding:1.25rem 1.75rem; border-bottom:1px solid var(--color-muy-claro);">
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
            <i class="bi bi-activity" style="color:var(--color-principal);"></i>
            <span style="font-size:0.72rem; font-weight:600; color:var(--color-principal); letter-spacing:0.1em; text-transform:uppercase;">Signos Vitales</span>
        </div>
        <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:0.75rem;">
            @if($historia->presion_arterial)
            <div style="background:var(--fondo-card-alt); border:1px solid var(--color-muy-claro); border-radius:10px; padding:0.75rem; text-align:center;">
                <i class="bi bi-heart" style="color:var(--color-principal); font-size:1.1rem; display:block; margin-bottom:0.3rem;"></i>
                <div style="font-size:1rem; font-weight:600; color:var(--color-principal);">{{ $historia->presion_arterial }}</div>
                <div style="font-size:0.65rem; color:#8fa39a; text-transform:uppercase; letter-spacing:0.05em; margin-top:0.2rem;">Presión arterial</div>
            </div>
            @endif
            @if($historia->frecuencia_cardiaca)
            <div style="background:var(--fondo-card-alt); border:1px solid var(--color-muy-claro); border-radius:10px; padding:0.75rem; text-align:center;">
                <i class="bi bi-activity" style="color:var(--color-principal); font-size:1.1rem; display:block; margin-bottom:0.3rem;"></i>
                <div style="font-size:1rem; font-weight:600; color:var(--color-principal);">{{ $historia->frecuencia_cardiaca }}</div>
                <div style="font-size:0.65rem; color:#8fa39a; text-transform:uppercase; letter-spacing:0.05em; margin-top:0.2rem;">Frec. cardiaca</div>
            </div>
            @endif
            @if($historia->temperatura)
            <div style="background:var(--fondo-card-alt); border:1px solid var(--color-muy-claro); border-radius:10px; padding:0.75rem; text-align:center;">
                <i class="bi bi-thermometer-half" style="color:var(--color-principal); font-size:1.1rem; display:block; margin-bottom:0.3rem;"></i>
                <div style="font-size:1rem; font-weight:600; color:var(--color-principal);">{{ $historia->temperatura }}</div>
                <div style="font-size:0.65rem; color:#8fa39a; text-transform:uppercase; letter-spacing:0.05em; margin-top:0.2rem;">Temperatura</div>
            </div>
            @endif
            @if($historia->peso)
            <div style="background:var(--fondo-card-alt); border:1px solid var(--color-muy-claro); border-radius:10px; padding:0.75rem; text-align:center;">
                <i class="bi bi-person" style="color:var(--color-principal); font-size:1.1rem; display:block; margin-bottom:0.3rem;"></i>
                <div style="font-size:1rem; font-weight:600; color:var(--color-principal);">{{ $historia->peso }} kg</div>
                <div style="font-size:0.65rem; color:#8fa39a; text-transform:uppercase; letter-spacing:0.05em; margin-top:0.2rem;">Peso</div>
            </div>
            @endif
            @if($historia->talla)
            <div style="background:var(--fondo-card-alt); border:1px solid var(--color-muy-claro); border-radius:10px; padding:0.75rem; text-align:center;">
                <i class="bi bi-rulers" style="color:var(--color-principal); font-size:1.1rem; display:block; margin-bottom:0.3rem;"></i>
                <div style="font-size:1rem; font-weight:600; color:var(--color-principal);">{{ $historia->talla }} m</div>
                <div style="font-size:0.65rem; color:#8fa39a; text-transform:uppercase; letter-spacing:0.05em; margin-top:0.2rem;">Talla</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- SECCIÓN 4: ODONTOGRAMA --}}
    @if($historia->odontograma)
    <div style="padding:1.25rem 1.75rem; border-bottom:1px solid var(--color-muy-claro); background:var(--fondo-card-alt);">
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
            <i class="bi bi-grid-3x3" style="color:var(--color-principal);"></i>
            <span style="font-size:0.72rem; font-weight:600; color:var(--color-principal); letter-spacing:0.1em; text-transform:uppercase;">Odontograma</span>
        </div>
        <x-odontograma :datos="$historia->odontograma" :modo="'ver'" :hallazgos="$historia->hallazgos" />
    </div>
    @endif

    {{-- SECCIÓN 5: HALLAZGOS CLÍNICOS --}}
    @if($historia->hallazgos && count((array) $historia->hallazgos) > 0)
    <div style="padding:1.25rem 1.75rem; border-bottom:1px solid var(--color-muy-claro);">
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
            <i class="bi bi-search-heart" style="color:var(--color-principal);"></i>
            <span style="font-size:0.72rem; font-weight:600; color:var(--color-principal); letter-spacing:0.1em; text-transform:uppercase;">Hallazgos Clínicos</span>
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
            <thead>
                <tr style="background:var(--color-muy-claro);">
                    <th style="padding:0.5rem 0.75rem; text-align:left; font-size:0.72rem; font-weight:600; color:var(--color-principal); letter-spacing:0.06em; text-transform:uppercase;">Diagnóstico</th>
                    <th style="padding:0.5rem 0.75rem; text-align:left; font-size:0.72rem; font-weight:600; color:var(--color-principal); letter-spacing:0.06em; text-transform:uppercase;">Procedimiento</th>
                    <th style="padding:0.5rem 0.75rem; text-align:left; font-size:0.72rem; font-weight:600; color:var(--color-principal); letter-spacing:0.06em; text-transform:uppercase;">Pieza</th>
                    <th style="padding:0.5rem 0.75rem; text-align:left; font-size:0.72rem; font-weight:600; color:var(--color-principal); letter-spacing:0.06em; text-transform:uppercase;">Cara</th>
                    <th style="padding:0.5rem 0.75rem; text-align:left; font-size:0.72rem; font-weight:600; color:var(--color-principal); letter-spacing:0.06em; text-transform:uppercase;">Nota</th>
                </tr>
            </thead>
            <tbody>
                @foreach((array) $historia->hallazgos as $hallazgo)
                <tr style="border-bottom:1px solid var(--color-muy-claro);">
                    <td style="padding:0.5rem 0.75rem; color:#1c2b22;">
                        {{ $hallazgo['diagnostico_codigo'] ?? '' }}
                        @if(!empty($hallazgo['diagnostico_nombre']))
                            — {{ $hallazgo['diagnostico_nombre'] }}
                        @endif
                    </td>
                    <td style="padding:0.5rem 0.75rem; color:#1c2b22;">{{ $hallazgo['procedimiento'] ?? '—' }}</td>
                    <td style="padding:0.5rem 0.75rem;">
                        <span style="background:var(--color-muy-claro); color:var(--color-principal); font-size:0.75rem; font-weight:500; padding:2px 8px; border-radius:50px;">
                            {{ $hallazgo['pieza'] ?? '—' }}
                        </span>
                    </td>
                    <td style="padding:0.5rem 0.75rem; color:#5c6b62;">{{ $hallazgo['cara'] ?? '—' }}</td>
                    <td style="padding:0.5rem 0.75rem; color:#5c6b62; font-size:0.82rem;">{{ $hallazgo['nota'] ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- SECCIÓN 6: OBSERVACIONES GENERALES --}}
    @if($historia->observaciones_generales)
    <div style="padding:1.25rem 1.75rem; border-bottom:1px solid var(--color-muy-claro);">
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.75rem;">
            <i class="bi bi-chat-square-dots" style="color:var(--color-principal);"></i>
            <span style="font-size:0.72rem; font-weight:600; color:var(--color-principal); letter-spacing:0.1em; text-transform:uppercase;">Observaciones Generales</span>
        </div>
        <div style="font-size:0.92rem; color:#1c2b22; line-height:1.6;">{{ $historia->observaciones_generales }}</div>
    </div>
    @endif

    {{-- SECCIÓN 7: FIRMA DEL PACIENTE --}}
    <div style="padding:1.25rem 1.75rem; background:var(--fondo-card-alt);">
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
            <i class="bi bi-pen" style="color:var(--color-principal);"></i>
            <span style="font-size:0.72rem; font-weight:600; color:var(--color-principal); letter-spacing:0.1em; text-transform:uppercase;">Firma del Paciente</span>
        </div>
        @if($historia->firmado && $historia->firma_data)
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem; align-items:end;">
                <div>
                    <div style="border:1px solid var(--color-muy-claro); border-radius:8px; padding:0.5rem; background:white; display:inline-block; margin-bottom:0.5rem;">
                        <img src="{{ $historia->firma_data }}" style="max-height:80px; max-width:250px; display:block;">
                    </div>
                    <div style="border-top:1px solid #333; padding-top:0.4rem; margin-top:0.25rem;">
                        <div style="font-size:0.85rem; font-weight:500; color:#1c2b22;">{{ $historia->paciente->nombre_completo }}</div>
                        <div style="font-size:0.75rem; color:#5c6b62;">{{ $historia->paciente->tipo_documento }}: {{ $historia->paciente->numero_documento }}</div>
                        <div style="font-size:0.72rem; color:#8fa39a; margin-top:0.2rem;">
                            <i class="bi bi-check-circle-fill" style="color:#28a745;"></i>
                            Firmado el {{ $historia->fecha_firma->format('d/m/Y \a \l\a\s H:i') }}
                        </div>
                    </div>
                </div>
                <div>
                    <div style="height:80px;"></div>
                    <div style="border-top:1px solid #333; padding-top:0.4rem;">
                        <div style="font-size:0.85rem; font-weight:500; color:#1c2b22;">{{ auth()->user()->name }}</div>
                        <div style="font-size:0.75rem; color:#5c6b62;">Odontólogo(a) — {{ $config->nombre_consultorio ?? '' }}</div>
                    </div>
                </div>
            </div>
        @else
            <div style="display:flex; align-items:center; gap:0.75rem; padding:0.75rem 1rem; background:#fff3cd; border:1px solid #ffc107; border-radius:8px;">
                <i class="bi bi-exclamation-triangle-fill" style="color:#856404;"></i>
                <span style="font-size:0.85rem; color:#856404;">
                    Esta historia clínica aún no ha sido firmada por el paciente.
                </span>
                <a href="{{ route('historias.firmar.vista', $historia) }}"
                   style="margin-left:auto; background:var(--color-principal); color:white; padding:4px 14px; border-radius:6px; font-size:0.8rem; text-decoration:none;">
                    <i class="bi bi-pen me-1"></i> Firmar ahora
                </a>
            </div>
        @endif
    </div>

</div>{{-- /card único --}}

{{-- Evoluciones Recientes --}}
<div style="background:#fff; border:1px solid var(--fondo-borde); border-radius:14px; overflow:hidden; margin-bottom:1.5rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
    <div style="background:var(--color-muy-claro); padding:.75rem 1.25rem; border-bottom:1px solid var(--color-muy-claro); display:flex; align-items:center; justify-content:space-between;">
        <div style="display:flex; align-items:center; gap:.5rem;">
            <i class="bi bi-clipboard2-pulse" style="color:var(--color-principal);"></i>
            <span style="font-size:.82rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover);">Evoluciones Recientes</span>
        </div>
        <div style="display:flex; gap:.5rem;">
            <a href="{{ route('evoluciones.index', ['paciente_id' => $historia->paciente_id]) }}"
               style="font-size:.78rem;color:var(--color-principal);text-decoration:none;background:#fff;border:1px solid var(--color-muy-claro);border-radius:6px;padding:.25rem .65rem;display:inline-flex;align-items:center;gap:.3rem;">
                <i class="bi bi-list-ul"></i> Ver todas
            </a>
            <a href="{{ route('evoluciones.create', ['paciente_id' => $historia->paciente_id]) }}"
               style="font-size:.78rem;color:#fff;text-decoration:none;background:linear-gradient(135deg,var(--color-principal),var(--color-claro));border-radius:6px;padding:.25rem .65rem;display:inline-flex;align-items:center;gap:.3rem;">
                <i class="bi bi-clipboard2-plus"></i> Nueva
            </a>
        </div>
    </div>
    @php $evols = $historia->evoluciones()->with('doctor')->take(5)->get(); @endphp
    @if($evols->isEmpty())
        <div class="evol-vacia">
            <i class="bi bi-clipboard2"></i>
            <p style="font-weight:600;color:#4b5563;margin-bottom:.25rem;">Sin evoluciones registradas</p>
            <a href="{{ route('evoluciones.create', ['paciente_id' => $historia->paciente_id]) }}" class="btn-morado mt-2">
                <i class="bi bi-clipboard2-plus"></i> Registrar primera evolución
            </a>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:.85rem;">
                <thead>
                    <tr>
                        <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.74rem;text-transform:uppercase;padding:.55rem 1.25rem;border-bottom:1px solid var(--color-muy-claro);">Fecha</th>
                        <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.74rem;text-transform:uppercase;padding:.55rem 1.25rem;border-bottom:1px solid var(--color-muy-claro);">Procedimiento</th>
                        <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.74rem;text-transform:uppercase;padding:.55rem 1.25rem;border-bottom:1px solid var(--color-muy-claro);">Dientes</th>
                        <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.74rem;text-transform:uppercase;padding:.55rem 1.25rem;border-bottom:1px solid var(--color-muy-claro);">Doctor</th>
                        <th style="background:var(--color-muy-claro);padding:.55rem 1.25rem;border-bottom:1px solid var(--color-muy-claro);text-align:center;width:48px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($evols as $ev)
                    <tr>
                        <td style="padding:.6rem 1.25rem;border-bottom:1px solid var(--fondo-borde);color:#4b5563;white-space:nowrap;">{{ $ev->fecha_formateada }}</td>
                        <td style="padding:.6rem 1.25rem;border-bottom:1px solid var(--fondo-borde);font-weight:500;color:#1c2b22;">{{ $ev->procedimiento }}</td>
                        <td style="padding:.6rem 1.25rem;border-bottom:1px solid var(--fondo-borde);">
                            @if($ev->dientes_tratados)
                                <span style="background:var(--color-muy-claro);color:var(--color-hover);border-radius:20px;padding:.15rem .55rem;font-size:.72rem;font-weight:600;">{{ $ev->dientes_tratados }}</span>
                            @else
                                <span style="color:#d1d5db;">—</span>
                            @endif
                        </td>
                        <td style="padding:.6rem 1.25rem;border-bottom:1px solid var(--fondo-borde);font-size:.82rem;color:#6b7280;">{{ $ev->doctor ? $ev->doctor->name : '—' }}</td>
                        <td style="padding:.6rem 1.25rem;border-bottom:1px solid var(--fondo-borde);text-align:center;">
                            <a href="{{ route('evoluciones.show', $ev) }}"
                               style="background:none;border:1px solid var(--color-muy-claro);border-radius:6px;width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;color:var(--color-principal);font-size:.85rem;text-decoration:none;">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Imágenes Clínicas --}}
@php $imgs = $historia->imagenes()->take(4)->get(); @endphp
<div class="sec-card mt-3">
    <div class="sec-header" style="justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:.5rem;">
            <i class="bi bi-images" style="color:var(--color-principal);"></i>
            <h6>Imágenes Clínicas</h6>
        </div>
        <div style="display:flex;gap:.5rem;">
            <a href="{{ route('imagenes.galeria', $historia->paciente_id) }}"
               style="font-size:.78rem;color:var(--color-principal);text-decoration:none;display:inline-flex;align-items:center;gap:.25rem;">
                <i class="bi bi-grid-3x3-gap"></i> Ver galería
            </a>
            <a href="{{ route('imagenes.create', ['paciente_id' => $historia->paciente_id]) }}"
               style="font-size:.78rem;background:var(--color-principal);color:#fff;padding:.18rem .65rem;border-radius:6px;text-decoration:none;display:inline-flex;align-items:center;gap:.2rem;">
                <i class="bi bi-plus"></i> Subir
            </a>
        </div>
    </div>
    <div class="sec-body">
        @if($imgs->isEmpty())
        <div style="text-align:center;padding:1rem;color:#9ca3af;font-size:.85rem;">
            <i class="bi bi-images" style="font-size:1.5rem;color:var(--color-acento-activo);display:block;margin-bottom:.35rem;"></i>
            Sin imágenes registradas
        </div>
        @else
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:.6rem;">
            @foreach($imgs as $img)
            <a href="{{ route('imagenes.show', $img) }}" style="text-decoration:none;">
                <div style="border:1px solid var(--color-muy-claro);border-radius:8px;overflow:hidden;">
                    <img src="{{ $img->url }}" alt="{{ $img->titulo }}"
                         style="width:100%;aspect-ratio:1;object-fit:cover;display:block;background:var(--color-muy-claro);"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div style="display:none;width:100%;aspect-ratio:1;align-items:center;justify-content:center;background:var(--color-muy-claro);font-size:1.5rem;color:var(--color-acento-activo);">
                        <i class="bi {{ $img->tipo_icono }}"></i>
                    </div>
                    <div style="font-size:.65rem;color:#6b7280;padding:.25rem .35rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $img->titulo }}</div>
                </div>
            </a>
            @endforeach
        </div>
        @if($historia->imagenes()->count() > 4)
        <div style="text-align:center;margin-top:.6rem;">
            <a href="{{ route('imagenes.galeria', $historia->paciente_id) }}" style="font-size:.8rem;color:var(--color-principal);text-decoration:none;">Ver todas →</a>
        </div>
        @endif
        @endif
    </div>
</div>

{{-- Notas de Corrección --}}
@if($historia->firmado && $historia->correcciones->count() > 0)
<div class="sec-card mt-3">
    <div class="sec-header" style="justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:.5rem;">
            <i class="bi bi-pencil-square" style="color:var(--color-principal);"></i>
            <h6>Notas de Corrección ({{ $historia->correcciones->count() }})</h6>
        </div>
        <a href="{{ route('historias.correccion.vista', $historia) }}"
           style="font-size:.78rem;color:var(--color-principal);text-decoration:none;">
            + Agregar corrección
        </a>
    </div>
    <div style="padding:1rem 1.25rem;">
        @foreach($historia->correcciones as $correccion)
        <div style="border-left:3px solid {{ $correccion->firmado ? '#28a745' : '#ffc107' }};padding:.75rem 1rem;margin-bottom:.75rem;background:var(--fondo-card-alt);border-radius:0 8px 8px 0;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.4rem;flex-wrap:wrap;gap:.3rem;">
                <span style="font-size:.8rem;font-weight:600;color:var(--color-principal);">
                    Campo: {{ $correccion->campo_label }}
                </span>
                <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
                    <span style="font-size:.75rem;color:#9ca3af;">
                        {{ $correccion->created_at->format('d/m/Y H:i') }} — {{ $correccion->usuario->name }}
                    </span>
                    @if($correccion->firmado)
                        <span style="background:#d1fae5;color:#166534;padding:.15rem .55rem;border-radius:20px;font-size:.65rem;font-weight:700;">
                            <i class="bi bi-check-circle"></i> Firmada {{ $correccion->fecha_firma->format('d/m/Y H:i') }}
                        </span>
                    @else
                        <span style="background:#FFF3CD;color:#856404;padding:.15rem .55rem;border-radius:20px;font-size:.65rem;font-weight:700;">
                            <i class="bi bi-clock"></i> Pendiente firma
                        </span>
                        <a href="{{ route('historias.correccion.firmar.vista', $correccion) }}"
                           style="font-size:.72rem;background:var(--color-principal);color:white;padding:2px 8px;border-radius:4px;text-decoration:none;display:inline-flex;align-items:center;gap:.25rem;">
                            <i class="bi bi-pen"></i> Firmar
                        </a>
                    @endif
                </div>
            </div>
            <div style="font-size:.8rem;margin-bottom:.3rem;">
                <span style="color:#999;text-decoration:line-through;">
                    Anterior: {{ Str::limit($correccion->valor_anterior, 100) }}
                </span>
            </div>
            <div style="font-size:.8rem;margin-bottom:.3rem;">
                <span style="color:#1c2b22;">
                    Corrección: {{ Str::limit($correccion->valor_nuevo, 100) }}
                </span>
            </div>
            <div style="font-size:.75rem;color:#5c6b62;">
                Motivo: {{ $correccion->motivo }}
            </div>
            @if($correccion->firmado && $correccion->firma_data)
            <div style="margin-top:.5rem;">
                <img src="{{ $correccion->firma_data }}" style="max-height:50px;border:1px solid #ddd;border-radius:4px;background:#fff;padding:2px;">
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
