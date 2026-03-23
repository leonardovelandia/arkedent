@extends('layouts.app')
@section('titulo', 'Editar Material')

@push('estilos')
<style>
    .form-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); overflow:hidden; }
    .form-card-header { padding:.9rem 1.5rem; border-bottom:1px solid var(--fondo-borde); background:var(--fondo-card-alt); }
    .form-card-body { padding:1.5rem; }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media(max-width:600px){ .form-grid{ grid-template-columns:1fr; } }
    .form-group { display:flex; flex-direction:column; gap:.25rem; }
    .form-label { font-size:.78rem; font-weight:700; color:var(--color-hover); }
    .form-input { border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.5rem .85rem; font-size:.875rem; color:#1c2b22; outline:none; width:100%; box-sizing:border-box; }
    .form-input:focus { border-color:var(--color-principal); }
    .form-input.is-invalid { border-color:#dc2626; }
    .invalid-feedback { font-size:.75rem; color:#dc2626; margin-top:.2rem; }
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.55rem 1.5rem; font-size:.9rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; cursor:pointer; transition:filter .18s; }
    .btn-morado:hover { filter:brightness(1.12); }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.55rem 1.2rem; font-size:.9rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; }
</style>
@endpush

@section('contenido')

<div style="display:flex; align-items:center; gap:.75rem; margin-bottom:1.25rem;">
    <a href="{{ route('inventario.show', $material) }}"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 style="font-family:var(--fuente-titulos); font-weight:700; color:#1c2b22; margin:0;">Editar Material</h4>
        <p style="font-size:.82rem; color:#9ca3af; margin:0;">{{ $material->nombre }}</p>
    </div>
</div>

<div class="form-card">
    <div class="form-card-header">
        <span style="font-size:.88rem; font-weight:600; color:#1c2b22; display:flex; align-items:center; gap:.4rem;">
            <i class="bi bi-pencil" style="color:var(--color-principal);"></i> Datos del material
        </span>
    </div>
    <div class="form-card-body">
        <form id="form-editar-material" method="POST" action="{{ route('inventario.update', $material) }}">
            @csrf
            @method('PUT')
            <div class="form-grid" style="margin-bottom:1rem;">
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="nombre" id="input-nombre" class="form-input @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $material->nombre) }}" required autocomplete="off">
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Código interno</label>
                    <input type="text" name="codigo" class="form-input @error('codigo') is-invalid @enderror"
                           value="{{ old('codigo', $material->codigo) }}" placeholder="Opcional">
                    @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Categoría</label>
                    <select name="categoria_id" class="form-input">
                        <option value="">Sin categoría</option>
                        @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ old('categoria_id', $material->categoria_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Unidad de medida *</label>
                    <select name="unidad_medida" class="form-input @error('unidad_medida') is-invalid @enderror" required>
                        @foreach(['unidades','carpules','ml','litros','gramos','kg','juegos','cajas','metros','rollos'] as $u)
                        <option value="{{ $u }}" {{ old('unidad_medida', $material->unidad_medida) === $u ? 'selected' : '' }}>{{ ucfirst($u) }}</option>
                        @endforeach
                    </select>
                    @error('unidad_medida')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group" style="background:#f9f9f9; border-radius:8px; padding:.75rem;">
                    <label class="form-label" style="color:#6b7280;">Stock actual (solo lectura)</label>
                    <div style="font-size:1rem; font-weight:600; color:var(--color-principal);">
                        {{ number_format($material->stock_actual, 2) }} {{ $material->unidad_medida }}
                    </div>
                    <div style="font-size:.72rem; color:#9ca3af;">Para modificarlo usa Entrada o Ajuste de Stock</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Stock mínimo *</label>
                    <input type="number" name="stock_minimo" class="form-input @error('stock_minimo') is-invalid @enderror"
                           value="{{ old('stock_minimo', $material->stock_minimo) }}" step="0.01" min="0" required>
                    @error('stock_minimo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Stock máximo</label>
                    <input type="number" name="stock_maximo" class="form-input @error('stock_maximo') is-invalid @enderror"
                           value="{{ old('stock_maximo', $material->stock_maximo) }}" step="0.01" min="0" placeholder="Opcional">
                    @error('stock_maximo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Precio unitario</label>
                    <input type="number" name="precio_unitario" class="form-input @error('precio_unitario') is-invalid @enderror"
                           value="{{ old('precio_unitario', $material->precio_unitario) }}" step="0.01" min="0">
                    @error('precio_unitario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Proveedor habitual</label>
                    <input type="text" name="proveedor_habitual" class="form-input"
                           value="{{ old('proveedor_habitual', $material->proveedor_habitual) }}" placeholder="Opcional">
                </div>
                <div class="form-group">
                    <label class="form-label">Ubicación en consultorio</label>
                    <input type="text" name="ubicacion" class="form-input"
                           value="{{ old('ubicacion', $material->ubicacion) }}" placeholder="Ej: Cajón 3, Estante A…">
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-input" rows="3">{{ old('descripcion', $material->descripcion) }}</textarea>
                </div>
            </div>
        </form>

        <div style="display:flex; gap:.75rem; justify-content:space-between; align-items:center; margin-top:1rem;">
            <form method="POST" action="{{ route('inventario.destroy', $material) }}" onsubmit="return confirm('¿Desactivar este material?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:none; border:none; font-size:.8rem; color:#dc2626; cursor:pointer; text-decoration:underline;">
                    <i class="bi bi-trash"></i> Desactivar material
                </button>
            </form>
            <div style="display:flex; gap:.75rem;">
                <a href="{{ route('inventario.show', $material) }}" class="btn-gris"><i class="bi bi-x"></i> Cancelar</a>
                <button type="submit" form="form-editar-material" class="btn-morado"><i class="bi bi-check-lg"></i> Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    function toTitleCase(str) {
        return str.replace(/\S+/g, function(word) {
            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        });
    }
    var campo = document.getElementById('input-nombre');
    campo.addEventListener('input', function () {
        var pos = this.selectionStart;
        this.value = toTitleCase(this.value);
        this.setSelectionRange(pos, pos);
    });
    campo.addEventListener('blur', function () {
        this.value = toTitleCase(this.value);
    });
})();
</script>
@endpush

@endsection
