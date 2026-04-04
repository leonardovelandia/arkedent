@extends('layouts.app')
@section('titulo', 'Editar Tratamiento')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .form-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:1.5rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1rem; }
    .form-label { font-size:.82rem; font-weight:700; color:var(--color-hover); display:block; margin-bottom:.35rem; }
    .form-input { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.5rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; transition:border-color .15s; }
    .form-input:focus { border-color:var(--color-principal); }
    .form-select { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.5rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; transition:border-color .15s; }
    .form-select:focus { border-color:var(--color-principal); }
    .form-group { margin-bottom:1rem; }
    .form-error { font-size:.78rem; color:#dc2626; margin-top:.25rem; }

    /* ── Classic overrides ── */
    body:not([data-ui="glass"]) .form-card  { background:#fff; border:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .form-label { color:var(--color-hover); }
    body:not([data-ui="glass"]) .form-input  { border:1.5px solid var(--color-muy-claro); color:#1c2b22; background:#fff; }
    body:not([data-ui="glass"]) .form-select { border:1.5px solid var(--color-muy-claro); color:#1c2b22; background:#fff; }

    /* ── Aurora Glass overrides ── */
    body[data-ui="glass"] .form-card  { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .form-label  { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .form-input  { background:rgba(255,255,255,0.08) !important; border:1.5px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .form-input:focus  { border-color:rgba(0,234,255,0.70) !important; box-shadow:none !important; }
    body[data-ui="glass"] .form-input::placeholder { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .form-select { background:rgba(255,255,255,0.08) !important; border:1.5px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .form-select option, body[data-ui="glass"] .form-select optgroup { background: #0a2535 !important; color: rgba(255,255,255,0.88) !important; }
    body[data-ui="glass"] .form-select:focus { border-color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .page-title-main { color:rgba(255,255,255,0.90) !important; }
</style>
@endpush

@section('contenido')

<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('tratamientos.show', $tratamiento) }}"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem .9rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
    <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Editar Tratamiento</h4>
</div>

<form method="POST" action="{{ route('tratamientos.update', $tratamiento) }}">
@csrf
@method('PUT')

<div class="form-card">
    <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--color-principal);margin-bottom:1rem;">
        <i class="bi bi-person"></i> Paciente e Historia
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div class="form-group">
            <label class="form-label">Paciente <span style="color:#dc2626;">*</span></label>
            <select name="paciente_id" class="form-select" required>
                <option value="">Seleccionar paciente…</option>
                @foreach($pacientes as $p)
                <option value="{{ $p->id }}" {{ old('paciente_id', $tratamiento->paciente_id) == $p->id ? 'selected' : '' }}>
                    {{ $p->apellido }}, {{ $p->nombre }}
                </option>
                @endforeach
            </select>
            @error('paciente_id')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Historia Clínica (opcional)</label>
            <select name="historia_clinica_id" class="form-select">
                <option value="">Sin historia específica</option>
                @foreach($historias as $h)
                <option value="{{ $h->id }}" {{ old('historia_clinica_id', $tratamiento->historia_clinica_id) == $h->id ? 'selected' : '' }}>
                    Historia #{{ $h->id }} — {{ $h->created_at->format('d/m/Y') }}
                </option>
                @endforeach
            </select>
            @error('historia_clinica_id')<div class="form-error">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="form-card">
    <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--color-principal);margin-bottom:1rem;">
        <i class="bi bi-clipboard2-pulse"></i> Datos del Tratamiento
    </div>

    <div class="form-group">
        <label class="form-label">Nombre del tratamiento <span style="color:#dc2626;">*</span></label>
        <input type="text" name="nombre" class="form-input"
               value="{{ old('nombre', $tratamiento->nombre) }}" required maxlength="255">
        @error('nombre')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
        <div class="form-group">
            <label class="form-label">Valor total (COP) <span style="color:#dc2626;">*</span></label>
            <input type="text" inputmode="numeric" name="valor_total" class="form-input"
                   value="{{ old('valor_total', $tratamiento->valor_total) }}" required data-money>
            @error('valor_total')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Fecha inicio <span style="color:#dc2626;">*</span></label>
            <input type="date" name="fecha_inicio" class="form-input"
                   value="{{ old('fecha_inicio', $tratamiento->fecha_inicio?->format('Y-m-d')) }}" required>
            @error('fecha_inicio')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Fecha fin estimada</label>
            <input type="date" name="fecha_fin" class="form-input"
                   value="{{ old('fecha_fin', $tratamiento->fecha_fin?->format('Y-m-d')) }}">
            @error('fecha_fin')<div class="form-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Estado <span style="color:#dc2626;">*</span></label>
        <select name="estado" class="form-select" style="max-width:200px;" required>
            <option value="activo"     {{ old('estado', $tratamiento->estado) === 'activo'     ? 'selected' : '' }}>Activo</option>
            <option value="completado" {{ old('estado', $tratamiento->estado) === 'completado' ? 'selected' : '' }}>Completado</option>
            <option value="cancelado"  {{ old('estado', $tratamiento->estado) === 'cancelado'  ? 'selected' : '' }}>Cancelado</option>
        </select>
        @error('estado')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label class="form-label">Notas</label>
        <textarea name="notas" class="form-input" rows="3">{{ old('notas', $tratamiento->notas) }}</textarea>
        @error('notas')<div class="form-error">{{ $message }}</div>@enderror
    </div>
</div>

<div style="display:flex;gap:.5rem;flex-wrap:wrap;">
    <button type="submit" class="btn-morado">
        <i class="bi bi-check-circle"></i> Guardar Cambios
    </button>
    <a href="{{ route('tratamientos.show', $tratamiento) }}"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem 1.1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;">
        Cancelar
    </a>
</div>

</form>
@endsection
