@extends('layouts.app')
@section('titulo', 'Editar Imagen — ' . $imagen->numero_imagen)

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; }
    .sec-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; margin-bottom:1.25rem; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .sec-header { background:var(--color-muy-claro); padding:.75rem 1.25rem; border-bottom:1px solid var(--color-muy-claro); display:flex; align-items:center; gap:.5rem; }
    .sec-header h6 { margin:0; font-size:.82rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); }
    .sec-body { padding:1.25rem; }
    .form-lbl { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--color-principal); margin-bottom:.3rem; display:block; }
    .form-ctrl { width:100%; border:1px solid #d1d5db; border-radius:8px; padding:.5rem .8rem; font-size:.875rem; color:#374151; background:#fff; transition:border-color .15s; }
    .form-ctrl:focus { outline:none; border-color:var(--color-principal); box-shadow:0 0 0 3px var(--sombra-principal); }
    .error-msg { color:#dc2626; font-size:.78rem; margin-top:.25rem; }
</style>
@endpush

@section('contenido')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h1 style="font-family:var(--fuente-titulos);font-size:1.4rem;font-weight:700;color:var(--color-principal);margin:0;">
            <i class="bi bi-pencil me-2"></i>Editar Metadatos
        </h1>
        <p style="font-size:.85rem;color:#9ca3af;margin:.2rem 0 0;">
            <span style="font-family:monospace;font-weight:700;">{{ $imagen->numero_imagen }}</span> · {{ $imagen->paciente->nombre_completo }}
        </p>
    </div>
    <a href="{{ route('imagenes.show', $imagen) }}" class="btn-gris"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:.85rem 1.1rem;margin-bottom:1rem;color:#991b1b;font-size:.875rem;">
    <i class="bi bi-exclamation-triangle-fill me-1"></i>
    <ul style="margin:.4rem 0 0 1rem;padding:0;">
        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
    </ul>
</div>
@endif

<div class="row g-3">
    <div class="col-lg-4">
        <div class="sec-card">
            <div class="sec-header"><i class="bi bi-image" style="color:var(--color-principal);"></i><h6>Vista previa</h6></div>
            <div class="sec-body" style="padding:.75rem;">
                <img src="{{ $imagen->url }}" alt="{{ $imagen->titulo }}" style="width:100%;border-radius:8px;object-fit:contain;max-height:250px;background:#f3f4f6;">
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <form method="POST" action="{{ route('imagenes.update', $imagen) }}">
        @csrf @method('PUT')

        <div class="sec-card">
            <div class="sec-header"><i class="bi bi-info-circle" style="color:var(--color-principal);"></i><h6>Información</h6></div>
            <div class="sec-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-lbl">Tipo de Imagen *</label>
                        <select name="tipo" class="form-ctrl" required>
                            @foreach($tipos as $val => $label)
                            <option value="{{ $val }}" {{ old('tipo', $imagen->tipo) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tipo')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-lbl">Fecha de Toma *</label>
                        <input type="date" name="fecha_toma" value="{{ old('fecha_toma', $imagen->fecha_toma->format('Y-m-d')) }}" class="form-ctrl" required>
                        @error('fecha_toma')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-lbl">Título *</label>
                        <input type="text" name="titulo" value="{{ old('titulo', $imagen->titulo) }}" class="form-ctrl" required>
                        @error('titulo')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-lbl">Diente (opcional)</label>
                        <input type="text" name="diente" value="{{ old('diente', $imagen->diente) }}" class="form-ctrl" placeholder="Ej: 11">
                    </div>
                    <div class="col-12">
                        <label class="form-lbl">Descripción</label>
                        <textarea name="descripcion" class="form-ctrl" rows="3">{{ old('descripcion', $imagen->descripcion) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.875rem;color:#374151;">
                            <input type="checkbox" name="es_comparativo" id="chk-comp" value="1"
                                   {{ old('es_comparativo', $imagen->es_comparativo) ? 'checked' : '' }}
                                   style="width:16px;height:16px;accent-color:var(--color-principal);" onchange="toggleComp()">
                            Es parte de comparativo antes/después
                        </label>
                    </div>
                    <div id="campos-comp" style="{{ old('es_comparativo', $imagen->es_comparativo) ? '' : 'display:none;' }}" class="col-12">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-lbl">Momento</label>
                                <select name="orden_comparativo" class="form-ctrl">
                                    <option value="">— Seleccione —</option>
                                    @foreach(['antes'=>'Antes','durante'=>'Durante','despues'=>'Después'] as $v => $l)
                                    <option value="{{ $v }}" {{ old('orden_comparativo', $imagen->orden_comparativo) == $v ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-lbl">Grupo comparativo</label>
                                <input type="text" name="grupo_comparativo" value="{{ old('grupo_comparativo', $imagen->grupo_comparativo) }}" class="form-ctrl">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
            <button type="submit" class="btn-morado"><i class="bi bi-check-circle"></i> Guardar Cambios</button>
            <a href="{{ route('imagenes.show', $imagen) }}" class="btn-gris"><i class="bi bi-x-circle"></i> Cancelar</a>
        </div>

        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleComp() {
    document.getElementById('campos-comp').style.display = document.getElementById('chk-comp').checked ? '' : 'none';
}
</script>
@endpush

@endsection
