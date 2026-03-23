@extends('layouts.app')
@section('titulo', 'Editar Historia Clínica')

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

</style>
@endpush

@section('contenido')

<div class="page-header d-flex align-items-center gap-3">
    <a href="{{ route('historias.show', $historia) }}" style="color:var(--color-principal);font-size:1.2rem;text-decoration:none;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="page-titulo">Editar Historia Clínica</h1>
        <p class="page-subtitulo">{{ $historia->paciente->nombre_completo }} · {{ $historia->paciente->numero_historia }}</p>
    </div>
</div>

<form method="POST" action="{{ route('historias.update', $historia) }}">
@csrf @method('PUT')

{{-- S1: Paciente --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-person-badge" style="color:var(--color-principal);"></i><h6>Paciente</h6></div>
    <div class="sec-body">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="lbl">Paciente</label>
                <input type="text" class="ctrl" value="{{ $historia->paciente->nombre_completo }} — {{ $historia->paciente->numero_historia }}" readonly style="background:var(--fondo-card-alt);">
            </div>
            <div class="col-md-4">
                <label class="lbl">Fecha de Apertura <span style="color:#dc2626;">*</span></label>
                <input type="date" name="fecha_apertura" class="ctrl @error('fecha_apertura') is-invalid @enderror"
                       value="{{ old('fecha_apertura', $historia->fecha_apertura->format('Y-m-d')) }}">
                @error('fecha_apertura') <p class="err">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
</div>

{{-- S2: Motivo --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-chat-square-text" style="color:var(--color-principal);"></i><h6>Motivo de Consulta</h6></div>
    <div class="sec-body">
        <div class="row g-3">
            <div class="col-12">
                <label class="lbl">Motivo de consulta <span style="color:#dc2626;">*</span></label>
                <textarea name="motivo_consulta" rows="3" class="ctrl @error('motivo_consulta') is-invalid @enderror">{{ old('motivo_consulta', $historia->motivo_consulta) }}</textarea>
                @error('motivo_consulta') <p class="err">{{ $message }}</p> @enderror
            </div>
            <div class="col-12">
                <label class="lbl">Enfermedad actual</label>
                <textarea name="enfermedad_actual" rows="3" class="ctrl">{{ old('enfermedad_actual', $historia->enfermedad_actual) }}</textarea>
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
                <textarea name="antecedentes_medicos" rows="3" class="ctrl">{{ old('antecedentes_medicos', $historia->antecedentes_medicos) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="lbl">Medicamentos actuales</label>
                <textarea name="medicamentos_actuales" rows="3" class="ctrl">{{ old('medicamentos_actuales', $historia->medicamentos_actuales) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="lbl">Alergias</label>
                <textarea name="alergias" rows="2" class="ctrl">{{ old('alergias', $historia->alergias) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="lbl">Antecedentes familiares</label>
                <textarea name="antecedentes_familiares" rows="2" class="ctrl">{{ old('antecedentes_familiares', $historia->antecedentes_familiares) }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- S4: Odontológicos y hábitos --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-tooth" style="color:var(--color-principal);"></i><h6>Antecedentes Odontológicos y Hábitos</h6></div>
    <div class="sec-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="lbl">Antecedentes odontológicos</label>
                <textarea name="antecedentes_odontologicos" rows="3" class="ctrl">{{ old('antecedentes_odontologicos', $historia->antecedentes_odontologicos) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="lbl">Hábitos</label>
                <textarea name="habitos" rows="3" class="ctrl">{{ old('habitos', $historia->habitos) }}</textarea>
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
                <input type="text" name="presion_arterial" class="ctrl" value="{{ old('presion_arterial', $historia->presion_arterial) }}" placeholder="120/80 mmHg">
            </div>
            <div class="col-md-3 col-6">
                <label class="lbl">Frecuencia cardíaca</label>
                <input type="text" name="frecuencia_cardiaca" class="ctrl" value="{{ old('frecuencia_cardiaca', $historia->frecuencia_cardiaca) }}" placeholder="72 lpm">
            </div>
            <div class="col-md-2 col-6">
                <label class="lbl">Temperatura</label>
                <input type="text" name="temperatura" class="ctrl" value="{{ old('temperatura', $historia->temperatura) }}" placeholder="36.5 °C">
            </div>
            <div class="col-md-2 col-6">
                <label class="lbl">Peso (kg)</label>
                <input type="number" step="0.01" name="peso" class="ctrl" value="{{ old('peso', $historia->peso) }}">
            </div>
            <div class="col-md-2 col-6">
                <label class="lbl">Talla (cm)</label>
                <input type="number" step="0.01" name="talla" class="ctrl" value="{{ old('talla', $historia->talla) }}">
            </div>
        </div>
    </div>
</div>

{{-- S6: Odontograma --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-grid-3x3" style="color:var(--color-principal);"></i><h6>Odontograma</h6></div>
    <div class="sec-body">
        <x-odontograma :datos="old('odontograma', $historia->odontograma)" :modo="'editar'" :hallazgos="old('hallazgos', $historia->hallazgos)" />
    </div>
</div>

{{-- S7: Observaciones --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-chat-text" style="color:var(--color-principal);"></i><h6>Observaciones Generales</h6></div>
    <div class="sec-body">
        <textarea name="observaciones_generales" rows="4" class="ctrl">{{ old('observaciones_generales', $historia->observaciones_generales) }}</textarea>
    </div>
</div>

<div style="display:flex;gap:.75rem;justify-content:flex-end;margin-top:.5rem;">
    <a href="{{ route('historias.show', $historia) }}" class="btn-gris">
        <i class="bi bi-x"></i> Cancelar
    </a>
    <button type="submit" class="btn-morado">
        <i class="bi bi-floppy"></i> Guardar Cambios
    </button>
</div>
</form>

@endsection
