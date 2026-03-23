@extends('layouts.app')
@section('titulo', 'Editar Presupuesto — ' . $presupuesto->numero_formateado)

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; }
    .form-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:1.5rem; margin-bottom:1.25rem; }
    .form-label { font-size:.8rem; font-weight:700; color:var(--color-hover); display:block; margin-bottom:.3rem; }
    .form-input { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.5rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; box-sizing:border-box; }
    .form-input:focus { border-color:var(--color-principal); }
    .form-select { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.5rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; box-sizing:border-box; }
    .form-textarea { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.5rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; resize:vertical; box-sizing:border-box; }
    .form-group { margin-bottom:1rem; }
    .seccion-titulo { background:var(--color-muy-claro); margin:-1.5rem -1.5rem 1rem; padding:.5rem 1.5rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); border-bottom:1px solid var(--color-muy-claro); padding-bottom:.4rem; margin-bottom:1rem; }
    .tabla-items { width:100%; border-collapse:collapse; font-size:.84rem; }
    .tabla-items th { background:var(--color-muy-claro); padding:.45rem .5rem; font-size:.7rem; font-weight:700; text-transform:uppercase; color:var(--color-hover); border-bottom:2px solid var(--color-muy-claro); white-space:nowrap; }
    .tabla-items td { padding:.35rem .35rem; border-bottom:1px solid var(--fondo-borde); vertical-align:middle; }
    .inp-item { border:1.5px solid var(--color-muy-claro); border-radius:6px; padding:.35rem .5rem; font-size:.84rem; color:#1c2b22; background:#fff; outline:none; width:100%; box-sizing:border-box; }
    .inp-item:focus { border-color:var(--color-principal); }
    .inp-item-readonly { background:var(--fondo-card-alt); color:#4b5563; }
    .totales-card { background:linear-gradient(135deg,var(--color-muy-claro),var(--fondo-card-alt)); border:1px solid var(--color-claro); border-radius:12px; padding:1.1rem 1.25rem; }
    .total-fila { display:flex; justify-content:space-between; align-items:center; padding:.3rem 0; font-size:.875rem; }
    .total-grande { font-size:1.4rem; font-weight:800; color:var(--color-sidebar-2); border-top:2px solid var(--color-claro); margin-top:.5rem; padding-top:.6rem; }
</style>
@endpush

@section('contenido')

<div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <a href="{{ route('presupuestos.show', $presupuesto) }}"
       style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;color:#374151;text-decoration:none;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">
            Editar Presupuesto
            <span style="font-family:monospace;font-size:.75rem;font-weight:700;background:#dbeafe;color:#1d4ed8;border-radius:6px;padding:.1rem .5rem;margin-left:.4rem;">{{ $presupuesto->numero_formateado }}</span>
        </h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">{{ $presupuesto->paciente->nombre_completo }}</p>
    </div>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;">
    <div style="font-weight:600;margin-bottom:.35rem;"><i class="bi bi-exclamation-circle"></i> Corrija los siguientes errores:</div>
    <ul style="margin:0;padding-left:1.2rem;font-size:.84rem;">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('presupuestos.update', $presupuesto) }}" id="form-presupuesto">
@csrf
@method('PUT')

{{-- Paciente (read-only en edición) --}}
<div class="form-card">
    <div class="seccion-titulo"><i class="bi bi-info-circle"></i> Datos Generales</div>
    <div style="margin-bottom:1rem;padding:.75rem 1rem;background:var(--fondo-card-alt);border:1px solid var(--color-muy-claro);border-radius:8px;font-size:.875rem;">
        <i class="bi bi-person-circle" style="color:var(--color-principal);"></i>
        <strong>{{ $presupuesto->paciente->nombre_completo }}</strong>
        <span style="color:#9ca3af;margin-left:.5rem;">{{ $presupuesto->paciente->numero_historia }}</span>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
        <div class="form-group">
            <label class="form-label">Fecha de generación</label>
            <input type="date" name="fecha_generacion" id="fecha-gen" class="form-input"
                   value="{{ old('fecha_generacion', $presupuesto->fecha_generacion->format('Y-m-d')) }}"
                   oninput="calcularVencimiento()" required>
        </div>
        <div class="form-group">
            <label class="form-label">Validez (días)</label>
            <input type="number" name="validez_dias" id="validez-dias" class="form-input"
                   value="{{ old('validez_dias', $presupuesto->validez_dias) }}" min="1" max="365"
                   oninput="calcularVencimiento()">
        </div>
        <div class="form-group">
            <label class="form-label">Fecha de vencimiento</label>
            <input type="text" id="fecha-venc-display" class="form-input inp-item-readonly" readonly
                   value="{{ $presupuesto->fecha_vencimiento->format('d/m/Y') }}">
        </div>
    </div>
</div>

{{-- Items --}}
<div class="form-card">
    <div class="seccion-titulo"><i class="bi bi-list-ul"></i> Procedimientos</div>

    <datalist id="lista-procedimientos">
        @foreach($procedimientosPredefinidos as $proc)
        <option value="{{ $proc }}">
        @endforeach
    </datalist>

    <div style="overflow-x:auto;">
    <table class="tabla-items" id="tabla-items">
        <thead>
            <tr>
                <th style="width:30px;">#</th>
                <th style="min-width:200px;">Procedimiento</th>
                <th style="width:80px;">Diente</th>
                <th style="width:100px;">Cara</th>
                <th style="width:60px;">Cant.</th>
                <th style="width:120px;">Valor Unitario</th>
                <th style="width:120px;">Total</th>
                <th style="width:40px;"></th>
            </tr>
        </thead>
        <tbody id="items-tbody"></tbody>
    </table>
    </div>

    <button type="button" onclick="agregarFila()"
            style="margin-top:.75rem;background:var(--color-muy-claro);color:var(--color-principal);border:1.5px dashed var(--color-claro);border-radius:8px;padding:.5rem 1rem;font-size:.84rem;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;gap:.4rem;">
        <i class="bi bi-plus-circle"></i> Agregar procedimiento
    </button>
</div>

{{-- Totales --}}
<div class="form-card">
    <div class="seccion-titulo"><i class="bi bi-calculator"></i> Totales</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start;">
        <div>
            <div class="form-group">
                <label class="form-label">Descuento (%)</label>
                <input type="number" name="descuento_porcentaje" id="inp-descuento" class="form-input"
                       value="{{ old('descuento_porcentaje', $presupuesto->descuento_porcentaje) }}"
                       min="0" max="100" step="0.01" oninput="recalcularTotales()">
            </div>
        </div>
        <div class="totales-card">
            <div class="total-fila">
                <span style="color:#6b7280;">Subtotal:</span>
                <span id="display-subtotal" style="font-weight:600;color:#1c2b22;">$ {{ number_format($presupuesto->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="total-fila">
                <span style="color:#6b7280;">Descuento:</span>
                <span id="display-descuento" style="font-weight:600;color:#dc2626;">- $ {{ number_format($presupuesto->descuento_valor, 0, ',', '.') }}</span>
            </div>
            <div class="total-fila total-grande">
                <span>TOTAL:</span>
                <span id="display-total">$ {{ number_format($presupuesto->total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Condiciones --}}
<div class="form-card">
    <div class="seccion-titulo"><i class="bi bi-file-text"></i> Condiciones y Observaciones</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div class="form-group">
            <label class="form-label">Condiciones de pago</label>
            <textarea name="condiciones_pago" class="form-textarea" rows="3">{{ old('condiciones_pago', $presupuesto->condiciones_pago) }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Observaciones</label>
            <textarea name="observaciones" class="form-textarea" rows="3">{{ old('observaciones', $presupuesto->observaciones) }}</textarea>
        </div>
    </div>
</div>

<div style="display:flex;gap:.5rem;flex-wrap:wrap;">
    <button type="submit" class="btn-morado">
        <i class="bi bi-floppy"></i> Guardar cambios
    </button>
    <a href="{{ route('presupuestos.show', $presupuesto) }}" class="btn-gris">
        <i class="bi bi-x-lg"></i> Cancelar
    </a>
</div>

</form>
@endsection

@push('scripts')
<script>
var filaIdx = 0;
var itemsExistentes = @json($presupuesto->items);
var carasOpciones = '<option value="">N/A</option><option value="Oclusal">Oclusal</option><option value="Vestibular">Vestibular</option><option value="Lingual">Lingual</option><option value="Mesial">Mesial</option><option value="Distal">Distal</option>';

function agregarFila(data) {
    data = data || {};
    var idx = filaIdx++;
    var tr = document.createElement('tr');
    tr.innerHTML =
        '<td style="text-align:center;color:#9ca3af;font-size:.8rem;">' + (filaIdx) + '</td>' +
        '<td><input type="text" name="items[' + idx + '][procedimiento]" class="inp-item" list="lista-procedimientos" placeholder="Procedimiento…" required value="' + esc(data.procedimiento || '') + '"></td>' +
        '<td><input type="text" name="items[' + idx + '][diente]" class="inp-item" placeholder="Ej: 11" maxlength="20" value="' + esc(data.diente || '') + '"></td>' +
        '<td><select name="items[' + idx + '][cara]" class="inp-item">' + carasOpciones + '</select></td>' +
        '<td><input type="number" name="items[' + idx + '][cantidad]" class="inp-item" value="' + (data.cantidad || 1) + '" min="1" oninput="calcularFilaTotal(this)"></td>' +
        '<td><input type="text" name="items[' + idx + '][valor_unitario_fmt]" class="inp-item" placeholder="0" oninput="formatearValor(this)" value="' + (data.valor_unitario ? formatNum(data.valor_unitario) : '') + '">' +
             '<input type="hidden" name="items[' + idx + '][valor_unitario]" value="' + (data.valor_unitario || 0) + '"></td>' +
        '<td><input type="text" class="inp-item inp-item-readonly" readonly value="' + (data.valor_total ? '$ ' + formatNum(data.valor_total) : '$ 0') + '"></td>' +
        '<td><button type="button" onclick="eliminarFila(this)" style="background:none;border:none;color:#dc2626;cursor:pointer;font-size:1rem;padding:.2rem .4rem;"><i class="bi bi-trash3"></i></button></td>';

    if (data.cara) {
        var sel = tr.querySelector('select[name*="cara"]');
        for (var i = 0; i < sel.options.length; i++) {
            if (sel.options[i].value === data.cara) sel.selectedIndex = i;
        }
    }
    document.getElementById('items-tbody').appendChild(tr);
    recalcularTotales();
}

function esc(s) { return String(s).replace(/"/g, '&quot;'); }

function eliminarFila(btn) {
    btn.closest('tr').remove();
    renumerarFilas();
    recalcularTotales();
}

function renumerarFilas() {
    var filas = document.querySelectorAll('#items-tbody tr');
    filas.forEach(function(tr, i) { tr.cells[0].textContent = i + 1; });
}

function formatearValor(inp) {
    var raw = inp.value.replace(/\./g, '').replace(/[^0-9]/g, '');
    var num = parseInt(raw) || 0;
    inp.value = num ? formatNum(num) : '';
    var hidden = inp.nextElementSibling;
    hidden.value = num;
    calcularFilaTotal(inp.closest('tr').querySelector('input[name*="cantidad"]'));
}

function calcularFilaTotal(cantInp) {
    var tr = cantInp.closest('tr');
    var cant = parseInt(cantInp.value) || 0;
    var hidden = tr.querySelector('input[name*="valor_unitario"][type="hidden"]');
    var vu = parseFloat(hidden.value) || 0;
    var total = cant * vu;
    tr.querySelector('input[readonly]').value = '$ ' + formatNum(total);
    recalcularTotales();
}

function recalcularTotales() {
    var filas = document.querySelectorAll('#items-tbody tr');
    var subtotal = 0;
    filas.forEach(function(tr) {
        var cant = parseInt(tr.querySelector('input[name*="cantidad"]').value) || 0;
        var vu = parseFloat(tr.querySelector('input[name*="valor_unitario"][type="hidden"]').value) || 0;
        subtotal += cant * vu;
    });
    var descPct = parseFloat(document.getElementById('inp-descuento').value) || 0;
    var descVal = subtotal * descPct / 100;
    var total = subtotal - descVal;
    document.getElementById('display-subtotal').textContent = '$ ' + formatNum(subtotal);
    document.getElementById('display-descuento').textContent = '- $ ' + formatNum(descVal);
    document.getElementById('display-total').textContent = '$ ' + formatNum(total);
}

function calcularVencimiento() {
    var fechaStr = document.getElementById('fecha-gen').value;
    var dias = parseInt(document.getElementById('validez-dias').value) || 30;
    if (!fechaStr) return;
    var fecha = new Date(fechaStr + 'T00:00:00');
    fecha.setDate(fecha.getDate() + dias);
    var d = String(fecha.getDate()).padStart(2,'0');
    var m = String(fecha.getMonth()+1).padStart(2,'0');
    var y = fecha.getFullYear();
    document.getElementById('fecha-venc-display').value = d + '/' + m + '/' + y;
}

function formatNum(n) {
    return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

document.addEventListener('DOMContentLoaded', function() {
    itemsExistentes.forEach(function(item) { agregarFila(item); });
    if (itemsExistentes.length === 0) agregarFila();
    recalcularTotales();
});
</script>
@endpush
