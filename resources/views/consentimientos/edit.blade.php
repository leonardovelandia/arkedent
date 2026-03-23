@extends('layouts.app')
@section('titulo', 'Editar Consentimiento')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    .form-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:14px; padding:1.75rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); max-width:820px; margin:0 auto; }
    .form-card h5 { font-weight:700; color:var(--color-hover); font-size:1rem; margin-bottom:1.25rem; padding-bottom:.6rem; border-bottom:2px solid var(--color-muy-claro); }

    .campo-wrap { margin-bottom:1.1rem; }
    .campo-lbl { font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--color-principal); display:block; margin-bottom:.3rem; }
    .campo-ctrl { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .8rem; font-size:.9rem; color:#1c2b22; background:#fff; outline:none; transition:border-color .15s; font-family:inherit; }
    .campo-ctrl:focus { border-color:var(--color-principal); }
    .campo-error { font-size:.75rem; color:#dc2626; margin-top:.2rem; display:block; }

    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media(max-width:540px) { .form-row { grid-template-columns:1fr; } }
</style>
@endpush

@section('contenido')

<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('consentimientos.show', $consentimiento) }}" style="color:var(--color-principal);font-size:1.1rem;"><i class="bi bi-arrow-left-circle-fill"></i></a>
    <div>
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Editar Consentimiento</h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">{{ $consentimiento->paciente->nombre_completo }}</p>
    </div>
</div>

<div class="form-card">
    <h5><i class="bi bi-pencil-square" style="color:var(--color-principal);"></i> Modificar consentimiento</h5>

    {{-- Cargar nueva plantilla (opcional) --}}
    <div class="campo-wrap" style="background:var(--fondo-card-alt);border:1px solid var(--color-muy-claro);border-radius:10px;padding:.85rem 1rem;margin-bottom:1.25rem;">
        <label class="campo-lbl" style="margin-bottom:.4rem;"><i class="bi bi-files"></i> Cargar otra plantilla</label>
        <select id="select-plantilla" class="campo-ctrl">
            <option value="">— Sin cambio de plantilla —</option>
            @foreach($plantillas as $pl)
            <option value="{{ $pl->id }}" data-contenido="{{ $pl->contenido }}" data-nombre="{{ $pl->nombre }}">
                {{ $pl->nombre }}
            </option>
            @endforeach
        </select>
        <p style="font-size:.74rem;color:#9ca3af;margin-top:.3rem;margin-bottom:0;">Al seleccionar una plantilla se sobreescribirá el contenido actual.</p>
    </div>

    <form method="POST" action="{{ route('consentimientos.update', $consentimiento) }}">
    @csrf @method('PUT')

    {{-- Nombre y Fecha --}}
    <div class="form-row">
        <div class="campo-wrap">
            <label class="campo-lbl">Nombre del consentimiento <span style="color:#dc2626;">*</span></label>
            <input type="text" id="input-nombre" name="nombre" class="campo-ctrl {{ $errors->has('nombre') ? 'is-invalid' : '' }}"
                   value="{{ old('nombre', $consentimiento->nombre) }}">
            @error('nombre')<span class="campo-error">{{ $message }}</span>@enderror
        </div>
        <div class="campo-wrap">
            <label class="campo-lbl">Fecha de generación <span style="color:#dc2626;">*</span></label>
            <input type="date" name="fecha_generacion" class="campo-ctrl {{ $errors->has('fecha_generacion') ? 'is-invalid' : '' }}"
                   value="{{ old('fecha_generacion', $consentimiento->fecha_generacion->format('Y-m-d')) }}">
            @error('fecha_generacion')<span class="campo-error">{{ $message }}</span>@enderror
        </div>
    </div>

    {{-- Contenido --}}
    <div class="campo-wrap">
        <label class="campo-lbl">Contenido <span style="color:#dc2626;">*</span></label>
        <textarea id="textarea-contenido" name="contenido" rows="18"
                  class="campo-ctrl {{ $errors->has('contenido') ? 'is-invalid' : '' }}"
                  style="font-family:monospace;font-size:.82rem;line-height:1.6;resize:vertical;">{{ old('contenido', $consentimiento->contenido) }}</textarea>
        @error('contenido')<span class="campo-error">{{ $message }}</span>@enderror
    </div>

    {{-- Observaciones --}}
    <div class="campo-wrap">
        <label class="campo-lbl">Observaciones <span style="color:#9ca3af;font-size:.7rem;font-weight:400;">(opcional)</span></label>
        <textarea name="observaciones" rows="2" class="campo-ctrl">{{ old('observaciones', $consentimiento->observaciones) }}</textarea>
    </div>

    {{-- Botones --}}
    <div style="display:flex;gap:.5rem;justify-content:flex-end;padding-top:.5rem;border-top:1px solid var(--fondo-borde);margin-top:.5rem;">
        <a href="{{ route('consentimientos.show', $consentimiento) }}"
           style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;">
            <i class="bi bi-x-lg"></i> Cancelar
        </a>
        <button type="submit" class="btn-morado">
            <i class="bi bi-check-lg"></i> Guardar cambios
        </button>
    </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('select-plantilla').addEventListener('change', function () {
    var opt = this.options[this.selectedIndex];
    var contenido = opt.getAttribute('data-contenido') || '';
    var nombre    = opt.getAttribute('data-nombre')    || '';
    if (contenido) {
        if (confirm('¿Sobreescribir el contenido actual con la plantilla "' + nombre + '"?')) {
            document.getElementById('textarea-contenido').value = contenido;
            document.getElementById('input-nombre').value       = nombre;
        } else {
            this.selectedIndex = 0;
        }
    }
});
</script>
@endpush
