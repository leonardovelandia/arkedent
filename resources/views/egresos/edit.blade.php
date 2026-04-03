{{-- ============================================================
     VISTA: Editar Egreso
     Sistema: Arkedent
     Layout: layouts.app
     ============================================================ --}}
@extends('layouts.app')
@section('titulo', 'Editar Egreso')

@push('estilos')
<style>
    .form-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1.25rem; }
    .form-card-header { padding:.85rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; gap:.5rem; }
    .form-card-header h6 { font-family:var(--fuente-principal); font-size:.85rem; font-weight:700; color:var(--color-hover); margin:0; }
    .form-card-header i { color:#DC3545; font-size:1rem; }
    .form-card-body { padding:1.25rem; }

    .form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .form-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; }
    @media(max-width:768px){ .form-grid-2,.form-grid-3{ grid-template-columns:1fr; } }

    .form-group { display:flex; flex-direction:column; gap:.3rem; }
    .form-label { font-size:.78rem; font-weight:600; color:#374151; }
    .form-label .req { color:#DC3545; }
    .form-control-custom {
        border:1.5px solid var(--fondo-borde); border-radius:8px;
        padding:.45rem .75rem; font-size:.875rem; outline:none;
        font-family:inherit; width:100%; transition:border-color .15s;
    }
    .form-control-custom:focus { border-color:#DC3545; }
    .form-hint { font-size:.72rem; color:#9ca3af; }

    .seccion-recurrente { display:none; background:#fffbf0; border:1px solid #ffc107; border-radius:10px; padding:1rem; margin-top:1rem; }
    .seccion-recurrente.visible { display:block; }

    .btn-guardar { background:#DC3545; color:#fff; border:none; border-radius:8px; padding:.55rem 1.5rem; font-size:.9rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:.4rem; transition:filter .15s; }
    .btn-guardar:hover { filter:brightness(.9); }
    .btn-cancelar { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.55rem 1rem; font-size:.9rem; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:.4rem; }
</style>
@endpush

@section('contenido')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;gap:1rem;flex-wrap:wrap;">
    <div>
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;font-size:1.4rem;">
            <i class="bi bi-pencil-square" style="color:#DC3545;margin-right:.6rem;"></i>Editar Egreso
        </h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">{{ $egreso->numero_egreso }} — {{ $egreso->concepto }}</p>
    </div>
    <a href="{{ route('egresos.show', $egreso) }}" class="btn-cancelar">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

@if($errors->any())
<div style="background:#fde8e8;border:1px solid #fca5a5;border-radius:10px;padding:.75rem 1.1rem;margin-bottom:1rem;color:#DC3545;font-size:.85rem;">
    <strong><i class="bi bi-exclamation-triangle-fill"></i> Hay errores en el formulario:</strong>
    <ul style="margin:.35rem 0 0 1rem;padding:0;">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('egresos.update', $egreso) }}" enctype="multipart/form-data">
@csrf
@method('PUT')

{{-- SECCIÓN 1: Datos del egreso --}}
<div class="form-card">
    <div class="form-card-header">
        <i class="bi bi-receipt"></i>
        <h6>Datos del Egreso</h6>
    </div>
    <div class="form-card-body">

        <div class="form-grid-2" style="margin-bottom:1rem;">
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Categoría</label>
                <select name="categoria_id" id="categoria_id" class="form-control-custom" onchange="actualizarColorCategoria(this)">
                    <option value="">— Sin categoría —</option>
                    @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}"
                        data-color="{{ $cat->color }}"
                        {{ (old('categoria_id', $egreso->categoria_id)) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nombre }}{{ $cat->es_fijo ? ' (fijo)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-grid-2" style="margin-bottom:1rem;">
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Concepto <span class="req">*</span></label>
                <input type="text" name="concepto" class="form-control-custom"
                    value="{{ old('concepto', $egreso->concepto) }}" required maxlength="255">
            </div>
        </div>

        <div class="form-grid-2" style="margin-bottom:1rem;">
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control-custom" rows="2">{{ old('descripcion', $egreso->descripcion) }}</textarea>
            </div>
        </div>

        <div class="form-grid-3" style="margin-bottom:1rem;">
            <div class="form-group">
                <label class="form-label">Valor <span class="req">*</span></label>
                <input type="text" id="valor-display" class="form-control-custom"
                    value="{{ number_format(old('valor', $egreso->valor), 0, ',', '.') }}"
                    required placeholder="0"
                    oninput="formatearValor(this)"
                    style="font-weight:700;color:#DC3545;font-size:1rem;">
                <input type="hidden" name="valor" id="valor-hidden" value="{{ old('valor', $egreso->valor) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Método de Pago <span class="req">*</span></label>
                <select name="metodo_pago" class="form-control-custom" required>
                    <option value="efectivo"        {{ old('metodo_pago',$egreso->metodo_pago)=='efectivo'        ? 'selected':'' }}>Efectivo</option>
                    <option value="transferencia"   {{ old('metodo_pago',$egreso->metodo_pago)=='transferencia'   ? 'selected':'' }}>Transferencia</option>
                    <option value="tarjeta_credito" {{ old('metodo_pago',$egreso->metodo_pago)=='tarjeta_credito' ? 'selected':'' }}>Tarjeta Crédito</option>
                    <option value="tarjeta_debito"  {{ old('metodo_pago',$egreso->metodo_pago)=='tarjeta_debito'  ? 'selected':'' }}>Tarjeta Débito</option>
                    <option value="cheque"          {{ old('metodo_pago',$egreso->metodo_pago)=='cheque'          ? 'selected':'' }}>Cheque</option>
                    <option value="otro"            {{ old('metodo_pago',$egreso->metodo_pago)=='otro'            ? 'selected':'' }}>Otro</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Fecha del Egreso <span class="req">*</span></label>
                <input type="date" name="fecha_egreso" class="form-control-custom"
                    value="{{ old('fecha_egreso', $egreso->fecha_egreso?->format('Y-m-d')) }}" required>
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">N° Comprobante / Factura</label>
                <input type="text" name="numero_comprobante" class="form-control-custom"
                    value="{{ old('numero_comprobante', $egreso->numero_comprobante) }}" maxlength="100">
            </div>
            <div class="form-group">
                <label class="form-label">Comprobante (reemplazar)</label>
                @if($egreso->comprobante_path)
                <a href="{{ asset('storage/' . $egreso->comprobante_path) }}" target="_blank"
                   style="font-size:.78rem;color:#1e40af;margin-bottom:.3rem;display:inline-flex;align-items:center;gap:.25rem;">
                    <i class="bi bi-paperclip"></i> Ver comprobante actual
                </a>
                @endif
                <input type="file" name="comprobante" class="form-control-custom"
                    accept=".jpg,.jpeg,.png,.pdf"
                    style="padding:.35rem .75rem;font-size:.82rem;">
                <span class="form-hint">Sube un nuevo archivo para reemplazar el actual</span>
            </div>
        </div>

    </div>
</div>

{{-- SECCIÓN 2: Recurrencia --}}
<div class="form-card">
    <div class="form-card-header">
        <i class="bi bi-arrow-repeat"></i>
        <h6>¿Es un gasto recurrente?</h6>
    </div>
    <div class="form-card-body">
        <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.5rem;">
            <input type="checkbox" name="es_recurrente" id="es_recurrente" value="1"
                {{ old('es_recurrente', $egreso->es_recurrente) ? 'checked' : '' }}
                style="width:18px;height:18px;accent-color:#DC3545;cursor:pointer;"
                onchange="toggleRecurrente(this)">
            <label for="es_recurrente" style="font-size:.9rem;font-weight:600;color:#374151;cursor:pointer;margin:0;">
                Este gasto se repite periódicamente
            </label>
        </div>

        <div class="seccion-recurrente {{ old('es_recurrente', $egreso->es_recurrente) ? 'visible' : '' }}" id="seccion-recurrente">
            <div class="form-grid-3">
                <div class="form-group">
                    <label class="form-label">Frecuencia</label>
                    <select name="frecuencia_recurrente" id="frecuencia" class="form-control-custom">
                        <option value="">Seleccionar...</option>
                        @foreach(['diario'=>'Diario','semanal'=>'Semanal','quincenal'=>'Quincenal','mensual'=>'Mensual','bimestral'=>'Bimestral','trimestral'=>'Trimestral','semestral'=>'Semestral','anual'=>'Anual'] as $val => $lbl)
                        <option value="{{ $val }}" {{ old('frecuencia_recurrente',$egreso->frecuencia_recurrente)==$val ? 'selected':'' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Día del mes</label>
                    <input type="number" name="dia_recurrente" id="dia_recurrente" class="form-control-custom"
                        value="{{ old('dia_recurrente', $egreso->dia_recurrente) }}" min="1" max="31" placeholder="1-31">
                </div>
                <div class="form-group">
                    <label class="form-label">Próxima fecha de pago</label>
                    <input type="date" name="proxima_fecha" id="proxima_fecha" class="form-control-custom"
                        value="{{ old('proxima_fecha', $egreso->proxima_fecha?->format('Y-m-d')) }}">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECCIÓN 3: Notas --}}
<div class="form-card">
    <div class="form-card-header">
        <i class="bi bi-sticky"></i>
        <h6>Notas Adicionales</h6>
    </div>
    <div class="form-card-body">
        <div class="form-group">
            <textarea name="notas" class="form-control-custom" rows="3">{{ old('notas', $egreso->notas) }}</textarea>
        </div>
    </div>
</div>

<div style="display:flex;gap:.75rem;align-items:center;">
    <button type="submit" class="btn-guardar">
        <i class="bi bi-floppy"></i> Guardar Cambios
    </button>
    <a href="{{ route('egresos.show', $egreso) }}" class="btn-cancelar">Cancelar</a>
</div>

</form>

@push('scripts')
<script>
function formatearValor(input) {
    let raw = input.value.replace(/\D/g, '');
    document.getElementById('valor-hidden').value = raw;
    input.value = raw ? parseInt(raw).toLocaleString('es-CO') : '';
}
function actualizarColorCategoria(select) {
    const opt = select.options[select.selectedIndex];
    const color = opt.dataset.color || '';
    select.style.borderColor = color || 'var(--fondo-borde)';
    select.style.color       = color || '';
}
function toggleRecurrente(cb) {
    document.getElementById('seccion-recurrente').classList.toggle('visible', cb.checked);
}
document.addEventListener('DOMContentLoaded', () => {
    const catSelect = document.getElementById('categoria_id');
    if (catSelect.value) actualizarColorCategoria(catSelect);
});
document.querySelector('form').addEventListener('submit', function() {
    document.getElementById('valor-display').name = '';
});
</script>
@endpush

@endsection
