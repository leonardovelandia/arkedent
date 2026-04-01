{{-- ============================================================
     VISTA: Registrar Egreso
     Sistema: Arkevix Dental ERP
     Layout: layouts.app
     ============================================================ --}}
@extends('layouts.app')
@section('titulo', 'Registrar Egreso')

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
        border:1.5px solid var(--fondo-borde);
        border-radius:8px;
        padding:.45rem .75rem;
        font-size:.875rem;
        outline:none;
        font-family:inherit;
        width:100%;
        transition:border-color .15s;
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
            <i class="bi bi-plus-circle" style="color:#DC3545;margin-right:.6rem;"></i>Registrar Egreso
        </h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">Complete los datos del gasto</p>
    </div>
    <a href="{{ route('egresos.index') }}" class="btn-cancelar">
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

<form method="POST" action="{{ route('egresos.store') }}" enctype="multipart/form-data">
@csrf

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
                        {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
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
                    value="{{ old('concepto') }}" required maxlength="255"
                    placeholder="Ej: Arriendo mes de enero, Factura servicios...">
            </div>
        </div>

        <div class="form-grid-2" style="margin-bottom:1rem;">
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control-custom" rows="2"
                    placeholder="Detalles adicionales del gasto...">{{ old('descripcion') }}</textarea>
            </div>
        </div>

        <div class="form-grid-3" style="margin-bottom:1rem;">
            <div class="form-group">
                <label class="form-label">Valor <span class="req">*</span></label>
                <input type="text" name="valor" id="valor-display" class="form-control-custom"
                    value="{{ old('valor') ? number_format(old('valor'), 0, ',', '.') : '' }}"
                    required placeholder="0"
                    oninput="formatearValor(this)"
                    style="font-weight:700;color:#DC3545;font-size:1rem;">
                <input type="hidden" name="valor" id="valor-hidden" value="{{ old('valor') }}">
                <span class="form-hint">Solo números</span>
            </div>
            <div class="form-group">
                <label class="form-label">Método de Pago <span class="req">*</span></label>
                <select name="metodo_pago" class="form-control-custom" required>
                    <option value="">Seleccionar...</option>
                    <option value="efectivo"        {{ old('metodo_pago')=='efectivo'        ? 'selected':'' }}>Efectivo</option>
                    <option value="transferencia"   {{ old('metodo_pago')=='transferencia'   ? 'selected':'' }}>Transferencia</option>
                    <option value="tarjeta_credito" {{ old('metodo_pago')=='tarjeta_credito' ? 'selected':'' }}>Tarjeta Crédito</option>
                    <option value="tarjeta_debito"  {{ old('metodo_pago')=='tarjeta_debito'  ? 'selected':'' }}>Tarjeta Débito</option>
                    <option value="cheque"          {{ old('metodo_pago')=='cheque'          ? 'selected':'' }}>Cheque</option>
                    <option value="otro"            {{ old('metodo_pago')=='otro'            ? 'selected':'' }}>Otro</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Fecha del Egreso <span class="req">*</span></label>
                <input type="date" name="fecha_egreso" class="form-control-custom"
                    value="{{ old('fecha_egreso', date('Y-m-d')) }}" required>
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">N° Comprobante / Factura</label>
                <input type="text" name="numero_comprobante" class="form-control-custom"
                    value="{{ old('numero_comprobante') }}" maxlength="100"
                    placeholder="Ej: FAC-001, REC-2024...">
            </div>
            <div class="form-group">
                <label class="form-label">Comprobante (imagen o PDF, máx. 5MB)</label>
                <input type="file" name="comprobante" class="form-control-custom"
                    accept=".jpg,.jpeg,.png,.pdf"
                    style="padding:.35rem .75rem;font-size:.82rem;">
                <span class="form-hint">Foto de la factura o comprobante de pago</span>
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
                {{ old('es_recurrente') ? 'checked' : '' }}
                style="width:18px;height:18px;accent-color:#DC3545;cursor:pointer;"
                onchange="toggleRecurrente(this)">
            <label for="es_recurrente" style="font-size:.9rem;font-weight:600;color:#374151;cursor:pointer;margin:0;">
                Este gasto se repite periódicamente
            </label>
        </div>
        <p style="font-size:.78rem;color:#9ca3af;margin:0;">
            Actívalo si es un gasto como arriendo, servicios, salario, etc. El sistema generará alertas cuando se acerque la próxima fecha de pago.
        </p>

        <div class="seccion-recurrente {{ old('es_recurrente') ? 'visible' : '' }}" id="seccion-recurrente">
            <div class="form-grid-3">
                <div class="form-group">
                    <label class="form-label">Frecuencia <span class="req">*</span></label>
                    <select name="frecuencia_recurrente" id="frecuencia" class="form-control-custom"
                        onchange="calcularProximaFecha()">
                        <option value="">Seleccionar...</option>
                        <option value="diario"     {{ old('frecuencia_recurrente')=='diario'     ? 'selected':'' }}>Diario</option>
                        <option value="semanal"    {{ old('frecuencia_recurrente')=='semanal'    ? 'selected':'' }}>Semanal</option>
                        <option value="quincenal"  {{ old('frecuencia_recurrente')=='quincenal'  ? 'selected':'' }}>Quincenal</option>
                        <option value="mensual"    {{ old('frecuencia_recurrente')=='mensual'    ? 'selected':'' }}>Mensual</option>
                        <option value="bimestral"  {{ old('frecuencia_recurrente')=='bimestral'  ? 'selected':'' }}>Bimestral</option>
                        <option value="trimestral" {{ old('frecuencia_recurrente')=='trimestral' ? 'selected':'' }}>Trimestral</option>
                        <option value="semestral"  {{ old('frecuencia_recurrente')=='semestral'  ? 'selected':'' }}>Semestral</option>
                        <option value="anual"      {{ old('frecuencia_recurrente')=='anual'      ? 'selected':'' }}>Anual</option>
                    </select>
                </div>
                <div class="form-group" id="grupo-dia">
                    <label class="form-label">Día del mes</label>
                    <input type="number" name="dia_recurrente" id="dia_recurrente" class="form-control-custom"
                        value="{{ old('dia_recurrente') }}" min="1" max="31"
                        placeholder="Ej: 1, 15, 30"
                        onchange="calcularProximaFecha()">
                    <span class="form-hint">Día del mes en que se realiza el pago</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Próxima fecha de pago</label>
                    <input type="date" name="proxima_fecha" id="proxima_fecha" class="form-control-custom"
                        value="{{ old('proxima_fecha') }}">
                    <span class="form-hint">Calculada automáticamente</span>
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
            <textarea name="notas" class="form-control-custom" rows="3"
                placeholder="Observaciones, aclaraciones, referencias...">{{ old('notas') }}</textarea>
        </div>
    </div>
</div>

{{-- Botones --}}
<div style="display:flex;gap:.75rem;align-items:center;">
    <button type="submit" class="btn-guardar">
        <i class="bi bi-floppy"></i> Registrar Egreso
    </button>
    <a href="{{ route('egresos.index') }}" class="btn-cancelar">Cancelar</a>
</div>

</form>

@push('scripts')
<script>
// Formatear valor con puntos de miles
function formatearValor(input) {
    let raw = input.value.replace(/\D/g, '');
    document.getElementById('valor-hidden').value = raw;
    input.value = raw ? parseInt(raw).toLocaleString('es-CO') : '';
}

// Color del select de categoría según selección
function actualizarColorCategoria(select) {
    const opt = select.options[select.selectedIndex];
    const color = opt.dataset.color || '';
    select.style.borderColor = color || 'var(--fondo-borde)';
    select.style.color       = color || '';
}

// Mostrar/ocultar sección recurrente
function toggleRecurrente(cb) {
    const sec = document.getElementById('seccion-recurrente');
    sec.classList.toggle('visible', cb.checked);
    if (!cb.checked) {
        document.getElementById('proxima_fecha').value = '';
    }
}

// Calcular próxima fecha según frecuencia y día
function calcularProximaFecha() {
    const frecuencia = document.getElementById('frecuencia').value;
    const diaInput   = document.getElementById('dia_recurrente').value;
    const fechaBase  = document.querySelector('[name="fecha_egreso"]').value;

    if (!frecuencia || !fechaBase) return;

    let base = new Date(fechaBase + 'T00:00:00');
    let prox;

    const dias = { diario:1, semanal:7, quincenal:15 };
    const meses = { mensual:1, bimestral:2, trimestral:3, semestral:6, anual:12 };

    if (dias[frecuencia]) {
        prox = new Date(base.getTime() + dias[frecuencia] * 86400000);
    } else if (meses[frecuencia]) {
        prox = new Date(base);
        prox.setMonth(prox.getMonth() + meses[frecuencia]);
        if (diaInput) {
            prox.setDate(Math.min(parseInt(diaInput), 28));
        }
    } else {
        return;
    }

    const y = prox.getFullYear();
    const m = String(prox.getMonth()+1).padStart(2,'0');
    const d = String(prox.getDate()).padStart(2,'0');
    document.getElementById('proxima_fecha').value = `${y}-${m}-${d}`;
}

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', () => {
    const catSelect = document.getElementById('categoria_id');
    if (catSelect.value) actualizarColorCategoria(catSelect);

    // Formatear valor si ya tiene valor (old input)
    const vInput = document.getElementById('valor-display');
    if (vInput.value) {
        let raw = vInput.value.replace(/\D/g,'');
        document.getElementById('valor-hidden').value = raw;
        vInput.value = raw ? parseInt(raw).toLocaleString('es-CO') : '';
    }
});

// Sincronizar campo valor antes del submit
document.querySelector('form').addEventListener('submit', function() {
    const display = document.getElementById('valor-display');
    const hidden  = document.getElementById('valor-hidden');
    if (!hidden.value) {
        hidden.value = display.value.replace(/\D/g, '');
    }
    // Deshabilitar el display para que no envíe el campo duplicado
    display.name = '';
});
</script>
@endpush

@endsection
