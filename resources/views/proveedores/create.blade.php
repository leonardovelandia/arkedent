@extends('layouts.app')
@section('titulo', 'Nuevo Proveedor')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.2rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; }
    .panel-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1.1rem; }
    .panel-header { padding:.75rem 1.25rem; border-bottom:1px solid var(--fondo-borde); }
    .panel-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:600; color:var(--color-hover); display:flex; align-items:center; gap:.4rem; }
    .panel-titulo i { color:var(--color-principal); }
    .panel-body { padding:1.25rem; }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media(max-width:700px){ .form-grid{ grid-template-columns:1fr; } }
    .form-label { font-size:.76rem; font-weight:700; color:var(--color-hover); display:block; margin-bottom:.25rem; }
    .form-input { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .75rem; font-size:.85rem; color:#1c2b22; background:#fff; outline:none; box-sizing:border-box; }
    .form-input:focus { border-color:var(--color-principal); }
    .is-invalid { border-color:#dc2626 !important; }
    .error-msg { font-size:.75rem; color:#dc2626; margin-top:.2rem; }
    .cat-grid { display:grid; grid-template-columns:1fr 1fr; gap:.4rem; }
    .cat-item { display:flex; align-items:center; gap:.4rem; font-size:.82rem; color:#374151; padding:.3rem .5rem; border-radius:6px; cursor:pointer; }
    .cat-item:hover { background:var(--fondo-card-alt); }
    .star-preview { display:flex; gap:.15rem; margin-top:.4rem; }

    /* Clásico */
    body:not([data-ui="glass"]) .panel-card { background:#fff; border:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .panel-header { border-bottom:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .panel-titulo { color:var(--color-hover); }
    body:not([data-ui="glass"]) .form-label { color:var(--color-hover); }
    body:not([data-ui="glass"]) .form-input { color:#1c2b22; background:#fff; border:1.5px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .cat-item { color:#374151; }
    body:not([data-ui="glass"]) .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }

    /* Glass */
    body[data-ui="glass"] .panel-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .panel-header { background:rgba(0,0,0,0.25) !important; border-bottom:1px solid rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .panel-titulo { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .panel-titulo i { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .form-label { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .form-input { background:rgba(255,255,255,0.08) !important; border:1.5px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .form-input:focus { border-color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .form-input::placeholder { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .cat-item { color:rgba(255,255,255,0.88) !important; }
    body[data-ui="glass"] .cat-item:hover { background:rgba(0,234,255,0.08) !important; }
    body[data-ui="glass"] .btn-gris { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
    body[data-ui="glass"] .page-title-main { color:rgba(255,255,255,0.90) !important; }
</style>
@endpush

@section('contenido')
<div style="max-width:860px; margin:0 auto;">

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.2rem;">
    <div>
        <h4 style="font-family:var(--fuente-titulos); font-weight:700; color:#1c2b22; margin:0;">Nuevo Proveedor</h4>
    </div>
    <a href="{{ route('proveedores.index') }}" class="btn-gris"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

@if($errors->any())
<div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:.7rem 1rem; margin-bottom:1rem; font-size:.83rem; color:#991b1b;">
    <i class="bi bi-exclamation-triangle"></i> Por favor corrige los errores marcados.
</div>
@endif

<form method="POST" action="{{ route('proveedores.store') }}">
@csrf

{{-- Sección 1: Datos generales --}}
<div class="panel-card">
    <div class="panel-header">
        <div class="panel-titulo"><i class="bi bi-person-vcard"></i> Datos Generales</div>
    </div>
    <div class="panel-body">
        <div class="form-grid" style="grid-template-columns:1fr 1fr 1fr;">
            <div>
                <label class="form-label">Código interno</label>
                <input type="text" name="codigo" class="form-input {{ $errors->has('codigo') ? 'is-invalid' : '' }}"
                       value="{{ old('codigo') }}" placeholder="Ej: PROV-001">
                @error('codigo') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
            <div style="grid-column:span 2;">
                <label class="form-label">Nombre del proveedor *</label>
                <input type="text" name="nombre" class="form-input {{ $errors->has('nombre') ? 'is-invalid' : '' }}"
                       value="{{ old('nombre') }}" required>
                @error('nombre') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-grid" style="margin-top:1rem;">
            <div>
                <label class="form-label">NIT</label>
                <input type="text" name="nit" class="form-input" value="{{ old('nit') }}" placeholder="900123456-1">
            </div>
            <div>
                <label class="form-label">Nombre del contacto</label>
                <input type="text" name="contacto" class="form-input" value="{{ old('contacto') }}">
            </div>
            <div>
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-input" value="{{ old('telefono') }}">
            </div>
            <div>
                <label class="form-label">WhatsApp</label>
                <input type="text" name="whatsapp" class="form-input" value="{{ old('whatsapp') }}">
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       value="{{ old('email') }}">
                @error('email') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="form-label">Ciudad</label>
                <input type="text" name="ciudad" class="form-input" value="{{ old('ciudad') }}">
            </div>
            <div style="grid-column:span 2;">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" class="form-input" value="{{ old('direccion') }}">
            </div>
            <div>
                <label class="form-label">Tiempo de entrega (días)</label>
                <input type="number" name="tiempo_entrega_dias" class="form-input" value="{{ old('tiempo_entrega_dias') }}" min="0">
            </div>
            <div>
                <label class="form-label">Condiciones de pago</label>
                <input type="text" name="condiciones_pago" class="form-input" value="{{ old('condiciones_pago') }}" placeholder="Ej: Contado, 30 días…">
            </div>
            <div>
                <label class="form-label">Calificación (1–5)</label>
                <select name="calificacion" class="form-input" id="calSelect" onchange="mostrarEstrellas(this.value)">
                    <option value="">Sin calificación</option>
                    @foreach([1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5] as $cal)
                    <option value="{{ $cal }}" {{ old('calificacion') == $cal ? 'selected' : '' }}>{{ $cal }}</option>
                    @endforeach
                </select>
                <div class="star-preview" id="starPreview"></div>
            </div>
        </div>
    </div>
</div>

{{-- Sección 2: Categorías --}}
<div class="panel-card">
    <div class="panel-header">
        <div class="panel-titulo"><i class="bi bi-tags"></i> Categorías de productos que vende</div>
    </div>
    <div class="panel-body">
        <div class="cat-grid">
            @foreach($categorias as $key => $label)
            <label class="cat-item">
                <input type="checkbox" name="categorias[]" value="{{ $key }}"
                       {{ in_array($key, old('categorias', [])) ? 'checked' : '' }}
                       style="accent-color:var(--color-principal); width:16px; height:16px;">
                {{ $label }}
            </label>
            @endforeach
        </div>
    </div>
</div>

{{-- Sección 3: Notas --}}
<div class="panel-card">
    <div class="panel-header">
        <div class="panel-titulo"><i class="bi bi-chat-text"></i> Notas</div>
    </div>
    <div class="panel-body">
        <textarea name="notas" class="form-input" rows="3" placeholder="Observaciones, acuerdos especiales…">{{ old('notas') }}</textarea>
    </div>
</div>

<div style="display:flex; gap:.75rem; justify-content:flex-end; margin-top:.5rem;">
    <a href="{{ route('proveedores.index') }}" class="btn-gris">Cancelar</a>
    <button type="submit" class="btn-morado"><i class="bi bi-check-lg"></i> Guardar Proveedor</button>
</div>
</form>
</div>
@endsection

@push('scripts')
<script>
function mostrarEstrellas(val) {
    const preview = document.getElementById('starPreview');
    if (!val) { preview.innerHTML = ''; return; }
    let html = '';
    for (let i = 1; i <= 5; i++) {
        html += `<i class="bi bi-star${i <= parseFloat(val) ? '-fill' : ''}" style="color:${i <= parseFloat(val) ? '#FFC107' : '#DEE2E6'}; font-size:1.1rem;"></i>`;
    }
    preview.innerHTML = html;
}
// Mostrar estrellas al cargar si hay valor
document.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('calSelect');
    if (sel.value) mostrarEstrellas(sel.value);
});
</script>
@endpush
