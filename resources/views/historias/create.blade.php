@extends('layouts.app')
@section('titulo', 'Nueva Historia Clínica')

@push('estilos')
<style>
    :root { --m:var(--color-principal); --mc:var(--color-claro); --mh:var(--color-hover); --mm:var(--color-muy-claro); }
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.55rem 1.4rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.55rem 1.4rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:background .15s; text-decoration:none; cursor:pointer; }
    .btn-gris:hover { background:#e5e7eb; color:#1f2937; }

    .sec-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; margin-bottom:1.25rem; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .sec-header { background:var(--color-muy-claro); padding:.75rem 1.25rem; border-bottom:1px solid var(--color-muy-claro); display:flex; align-items:center; gap:.5rem; }
    .sec-header h6 { margin:0; font-size:.82rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); }
    .sec-body { padding:1.25rem; }

    .lbl { font-size:.8rem; font-weight:600; color:#374151; margin-bottom:.3rem; display:block; }
    .ctrl { width:100%; border:1px solid var(--color-muy-claro); border-radius:8px; padding:.5rem .85rem; font-size:.875rem; outline:none; transition:border-color .15s,box-shadow .15s; background:#fff; color:#1c2b22; }
    .ctrl:focus { border-color:var(--color-principal); box-shadow:0 0 0 3px var(--sombra-principal); }
    .ctrl.is-invalid { border-color:#dc2626; }
    .err { font-size:.78rem; color:#dc2626; margin-top:.3rem; }

    /* Clásico */
    body:not([data-ui="glass"]) .sec-card { background:#fff; border:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .sec-header { background:var(--color-muy-claro); border-bottom:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .sec-header h6 { color:var(--color-hover); }
    body:not([data-ui="glass"]) .lbl { color:#374151; }
    body:not([data-ui="glass"]) .ctrl { background:#fff; color:#1c2b22; border:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }
    body:not([data-ui="glass"]) .btn-gris:hover { background:#e5e7eb; color:#1f2937; }

    /* Glass */
    body[data-ui="glass"] .sec-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .sec-header { background:rgba(0,0,0,0.25) !important; border-bottom:1px solid rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .sec-header h6 { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .lbl { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .ctrl { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.90) !important; border:1px solid rgba(0,234,255,0.30) !important; }
    body[data-ui="glass"] .ctrl option, body[data-ui="glass"] .ctrl optgroup { background: #0a2535 !important; color: rgba(255,255,255,0.88) !important; }
    body[data-ui="glass"] .ctrl:focus { border-color:rgba(0,234,255,0.70) !important; box-shadow:none !important; }
    body[data-ui="glass"] .ctrl::placeholder { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .btn-gris { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
    body[data-ui="glass"] .btn-gris:hover { background:rgba(255,255,255,0.14) !important; }
</style>
@endpush

@section('contenido')

<div class="page-header d-flex align-items-center gap-3">
    <a href="{{ route('historias.index') }}" style="color:var(--color-principal);font-size:1.2rem;text-decoration:none;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="page-titulo">Nueva Historia Clínica</h1>
        <p class="page-subtitulo">Completa la información clínica del paciente</p>
    </div>
</div>

@if(session('error'))
    <div class="alerta-flash" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;">
        <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ route('historias.store') }}">
@csrf

{{-- S1: Paciente --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-person-badge" style="color:var(--color-principal);"></i><h6>Paciente</h6></div>
    <div class="sec-body">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="lbl">Paciente <span style="color:#dc2626;">*</span></label>
                @if($paciente)
                    <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">
                    <input type="text" class="ctrl" value="{{ $paciente->nombre_completo }} — {{ $paciente->numero_historia }}" readonly style="background:var(--fondo-card-alt);">
                @else
                    <x-buscador-paciente
                        :pacientes="$pacientes"
                        :valor-inicial="old('paciente_id')"
                        campo-nombre="numero_historia" />
                    @error('paciente_id') <p class="err">{{ $message }}</p> @enderror
                @endif
            </div>
            <div class="col-md-4">
                <label class="lbl">Fecha de Apertura <span style="color:#dc2626;">*</span></label>
                <input type="date" name="fecha_apertura" class="ctrl @error('fecha_apertura') is-invalid @enderror"
                       value="{{ old('fecha_apertura', date('Y-m-d')) }}">
                @error('fecha_apertura') <p class="err">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
</div>

{{-- S2: Motivo y enfermedad actual --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-chat-square-text" style="color:var(--color-principal);"></i><h6>Motivo de Consulta</h6></div>
    <div class="sec-body">
        <div class="row g-3">
            <div class="col-12">
                <label class="lbl">Motivo de consulta <span style="color:#dc2626;">*</span></label>
                <textarea name="motivo_consulta" rows="3" class="ctrl @error('motivo_consulta') is-invalid @enderror"
                          placeholder="Describe el motivo principal de la consulta...">{{ old('motivo_consulta') }}</textarea>
                @error('motivo_consulta') <p class="err">{{ $message }}</p> @enderror
            </div>
            <div class="col-12">
                <label class="lbl">Enfermedad actual</label>
                <textarea name="enfermedad_actual" rows="3" class="ctrl"
                          placeholder="Descripción de la enfermedad o condición actual...">{{ old('enfermedad_actual') }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- S3: Antecedentes médicos --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-heart-pulse" style="color:var(--color-principal);"></i><h6>Antecedentes Médicos</h6></div>
    <div class="sec-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="lbl">Enfermedades previas</label>
                <textarea name="antecedentes_medicos" rows="3" class="ctrl"
                          placeholder="Diabetes, hipertensión, enfermedades cardíacas...">{{ old('antecedentes_medicos') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="lbl">Medicamentos actuales</label>
                <textarea name="medicamentos_actuales" rows="3" class="ctrl"
                          placeholder="Nombre y dosis de medicamentos que consume...">{{ old('medicamentos_actuales') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="lbl">Alergias</label>
                <textarea name="alergias" rows="2" class="ctrl"
                          placeholder="Alergias a medicamentos, materiales dentales, látex...">{{ old('alergias') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="lbl">Antecedentes familiares</label>
                <textarea name="antecedentes_familiares" rows="2" class="ctrl"
                          placeholder="Enfermedades hereditarias o familiares relevantes...">{{ old('antecedentes_familiares') }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- S4: Antecedentes odontológicos y hábitos --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-tooth" style="color:var(--color-principal);"></i><h6>Antecedentes Odontológicos y Hábitos</h6></div>
    <div class="sec-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="lbl">Antecedentes odontológicos</label>
                <textarea name="antecedentes_odontologicos" rows="3" class="ctrl"
                          placeholder="Tratamientos previos, extracciones, ortodoncia...">{{ old('antecedentes_odontologicos') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="lbl">Hábitos</label>
                <textarea name="habitos" rows="3" class="ctrl"
                          placeholder="Bruxismo, onicofagia, tabaquismo, succión digital...">{{ old('habitos') }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- S5: Signos vitales --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-activity" style="color:var(--color-principal);"></i><h6>Signos Vitales</h6></div>
    <div class="sec-body">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <label class="lbl">Presión arterial</label>
                <input type="text" name="presion_arterial" class="ctrl" placeholder="120/80 mmHg"
                       value="{{ old('presion_arterial') }}">
            </div>
            <div class="col-md-3 col-6">
                <label class="lbl">Frecuencia cardíaca</label>
                <input type="text" name="frecuencia_cardiaca" class="ctrl" placeholder="72 lpm"
                       value="{{ old('frecuencia_cardiaca') }}">
            </div>
            <div class="col-md-2 col-6">
                <label class="lbl">Temperatura</label>
                <input type="text" name="temperatura" class="ctrl" placeholder="36.5 °C"
                       value="{{ old('temperatura') }}">
            </div>
            <div class="col-md-2 col-6">
                <label class="lbl">Peso (kg)</label>
                <input type="number" step="0.01" name="peso" class="ctrl" placeholder="65.0"
                       value="{{ old('peso') }}">
            </div>
            <div class="col-md-2 col-6">
                <label class="lbl">Talla (cm)</label>
                <input type="number" step="0.01" name="talla" class="ctrl" placeholder="170.0"
                       value="{{ old('talla') }}">
            </div>
        </div>
    </div>
</div>

{{-- S6: Odontograma --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-grid-3x3" style="color:var(--color-principal);"></i><h6>Odontograma</h6></div>
    <div class="sec-body">
        <x-odontograma :datos="old('odontograma')" :modo="'editar'" />
    </div>
</div>

{{-- S7: Observaciones --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-chat-text" style="color:var(--color-principal);"></i><h6>Observaciones Generales</h6></div>
    <div class="sec-body">
        <textarea name="observaciones_generales" rows="4" class="ctrl"
                  placeholder="Observaciones adicionales relevantes para el tratamiento...">{{ old('observaciones_generales') }}</textarea>
    </div>
</div>

<div style="display:flex;gap:.75rem;justify-content:flex-end;margin-top:.5rem;">
    <a href="{{ route('historias.index') }}" class="btn-gris">
        <i class="bi bi-x"></i> Cancelar
    </a>
    <button type="submit" class="btn-morado">
        <i class="bi bi-floppy"></i> Guardar Historia
    </button>
</div>

</form>


@endsection
