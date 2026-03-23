@extends('layouts.app')
@section('titulo', 'Nuevo Consentimiento')

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
    .campo-ctrl.is-invalid { border-color:#dc2626; }
    .campo-error { font-size:.75rem; color:#dc2626; margin-top:.2rem; display:block; }

    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media(max-width:540px) { .form-row { grid-template-columns:1fr; } }

    .plantilla-chip { display:flex; align-items:center; gap:.5rem; padding:.35rem .75rem; border-radius:8px; background:var(--color-muy-claro); border:1px solid var(--color-muy-claro); font-size:.83rem; color:var(--color-hover); font-weight:600; margin-bottom:.5rem; }
</style>
@endpush

@section('contenido')

<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('consentimientos.index') }}" style="color:var(--color-principal);font-size:1.1rem;"><i class="bi bi-arrow-left-circle-fill"></i></a>
    <div>
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Nuevo Consentimiento</h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">Generar consentimiento informado para el paciente</p>
    </div>
</div>

<div class="form-card">
    <h5><i class="bi bi-file-earmark-text" style="color:var(--color-principal);"></i> Datos del consentimiento</h5>

    <form method="POST" action="{{ route('consentimientos.store') }}">
    @csrf

    {{-- Paciente --}}
    <div class="campo-wrap">
        <label class="campo-lbl">Paciente <span style="color:#dc2626;">*</span></label>
        <x-buscador-paciente
            :pacientes="$pacientes"
            :valor-inicial="old('paciente_id', $paciente?->id)"
            campo-nombre="numero_documento" />
        @error('paciente_id')<span class="campo-error">{{ $message }}</span>@enderror
    </div>

    {{-- Plantilla --}}
    <div class="campo-wrap">
        <label class="campo-lbl">Plantilla <span style="color:#9ca3af;font-size:.7rem;font-weight:400;">(opcional — se cargará el contenido automáticamente)</span></label>
        <select id="select-plantilla" name="plantilla_id" class="campo-ctrl">
            <option value="">— Sin plantilla (redactar manualmente) —</option>
            @foreach($plantillas as $pl)
            <option value="{{ $pl->id }}"
                    data-contenido="{{ $pl->contenido }}"
                    data-nombre="{{ $pl->nombre }}"
                    {{ old('plantilla_id') == $pl->id ? 'selected' : '' }}>
                {{ $pl->nombre }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Nombre y Fecha --}}
    <div class="form-row">
        <div class="campo-wrap">
            <label class="campo-lbl">Nombre del consentimiento <span style="color:#dc2626;">*</span></label>
            <input type="text" id="input-nombre" name="nombre" class="campo-ctrl {{ $errors->has('nombre') ? 'is-invalid' : '' }}"
                   value="{{ old('nombre') }}" placeholder="Ej: Extracción dental simple">
            @error('nombre')<span class="campo-error">{{ $message }}</span>@enderror
        </div>
        <div class="campo-wrap">
            <label class="campo-lbl">Fecha de generación <span style="color:#dc2626;">*</span></label>
            <input type="date" name="fecha_generacion" class="campo-ctrl {{ $errors->has('fecha_generacion') ? 'is-invalid' : '' }}"
                   value="{{ old('fecha_generacion', date('Y-m-d')) }}">
            @error('fecha_generacion')<span class="campo-error">{{ $message }}</span>@enderror
        </div>
    </div>

    {{-- Contenido --}}
    <div class="campo-wrap">
        <label class="campo-lbl">Contenido del consentimiento <span style="color:#dc2626;">*</span></label>
        <p style="font-size:.75rem;color:#9ca3af;margin-bottom:.4rem;">
            <i class="bi bi-info-circle"></i>
            Las variables <code>@{{nombre_paciente}}</code>, <code>@{{apellido_paciente}}</code>, <code>@{{documento_paciente}}</code>, <code>@{{fecha}}</code> y <code>@{{doctor}}</code> se reemplazarán automáticamente al guardar.
        </p>
        <textarea id="textarea-contenido" name="contenido" rows="18"
                  class="campo-ctrl {{ $errors->has('contenido') ? 'is-invalid' : '' }}"
                  style="font-family:monospace;font-size:.82rem;line-height:1.6;resize:vertical;"
                  placeholder="Redacta el consentimiento o selecciona una plantilla arriba…">{{ old('contenido') }}</textarea>
        @error('contenido')<span class="campo-error">{{ $message }}</span>@enderror
    </div>

    {{-- Observaciones --}}
    <div class="campo-wrap">
        <label class="campo-lbl">Observaciones <span style="color:#9ca3af;font-size:.7rem;font-weight:400;">(opcional)</span></label>
        <textarea name="observaciones" rows="2" class="campo-ctrl"
                  placeholder="Notas internas, indicaciones especiales…">{{ old('observaciones') }}</textarea>
    </div>

    {{-- Botones --}}
    <div style="display:flex;gap:.5rem;justify-content:flex-end;padding-top:.5rem;border-top:1px solid var(--fondo-borde);margin-top:.5rem;">
        <a href="{{ route('consentimientos.index') }}"
           style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;">
            <i class="bi bi-x-lg"></i> Cancelar
        </a>
        <button type="submit" class="btn-morado">
            <i class="bi bi-check-lg"></i> Guardar Consentimiento
        </button>
    </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
(function () {
    var selPlantilla = document.getElementById('select-plantilla');
    var inputNombre  = document.getElementById('input-nombre');
    var textarea     = document.getElementById('textarea-contenido');

    selPlantilla.addEventListener('change', function () {
        var opt = this.options[this.selectedIndex];
        var contenido = opt.getAttribute('data-contenido') || '';
        var nombre    = opt.getAttribute('data-nombre')    || '';
        if (contenido) {
            textarea.value    = contenido;
            inputNombre.value = nombre;
        } else {
            textarea.value    = '';
            inputNombre.value = '';
        }
    });
})();
</script>
@endpush
