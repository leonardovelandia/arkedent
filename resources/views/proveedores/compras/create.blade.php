@extends('layouts.app')
@section('titulo', 'Registrar Compra')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.2rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1rem; font-size:.875rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; }
    .btn-rojo { background:#dc2626; color:#fff; border:none; border-radius:6px; padding:.22rem .5rem; font-size:.75rem; cursor:pointer; display:inline-flex; align-items:center; gap:.2rem; }
    .btn-azul { background:#1e40af; color:#fff; border:none; border-radius:8px; padding:.4rem .8rem; font-size:.82rem; cursor:pointer; display:inline-flex; align-items:center; gap:.3rem; }
    .panel-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1.1rem; }
    .panel-header { padding:.75rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
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
    .tabla-items { width:100%; border-collapse:collapse; }
    .tabla-items th { font-size:.69rem; font-weight:700; text-transform:uppercase; color:var(--color-principal); padding:.4rem .5rem; border-bottom:2px solid var(--color-muy-claro); text-align:left; white-space:nowrap; }
    .tabla-items td { padding:.35rem .4rem; border-bottom:1px solid var(--fondo-borde); vertical-align:middle; }
    .inp-item { border:1.5px solid var(--color-muy-claro); border-radius:6px; padding:.3rem .5rem; font-size:.82rem; color:#1c2b22; background:#fff; outline:none; width:100%; box-sizing:border-box; }
    .inp-item:focus { border-color:var(--color-principal); }
    .totales-box { background:var(--fondo-card-alt); border:1px solid var(--color-muy-claro); border-radius:10px; padding:1rem 1.25rem; }
    .total-row { display:flex; justify-content:space-between; align-items:center; padding:.3rem 0; font-size:.87rem; color:#374151; border-bottom:1px solid var(--color-muy-claro); }
    .total-row:last-child { border-bottom:none; font-size:1.05rem; font-weight:700; color:var(--color-principal); margin-top:.25rem; }
    .info-box { background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:.6rem .9rem; font-size:.8rem; color:#1e40af; display:flex; align-items:flex-start; gap:.5rem; }
</style>
@endpush

@section('contenido')
<div style="max-width:1000px; margin:0 auto;">

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.2rem;">
    <div>
        <h4 style="font-family:var(--fuente-titulos); font-weight:700; color:#1c2b22; margin:0;">Registrar Compra</h4>
    </div>
    <a href="{{ route('compras.index') }}" class="btn-gris"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

@if($errors->any())
<div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:.7rem 1rem; margin-bottom:1rem; font-size:.83rem; color:#991b1b;">
    <i class="bi bi-exclamation-triangle"></i> Por favor corrige los errores.
    <ul style="margin:.4rem 0 0; padding-left:1.2rem;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif
@if(session('error'))
<div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:.7rem 1rem; margin-bottom:1rem; font-size:.83rem; color:#991b1b;">
    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
</div>
@endif

<form method="POST" action="{{ route('compras.store') }}" id="formCompra">
@csrf

{{-- Sección 1: Datos generales --}}
<div class="panel-card">
    <div class="panel-header">
        <div class="panel-titulo"><i class="bi bi-info-circle"></i> Datos Generales</div>
    </div>
    <div class="panel-body">
        <div class="form-grid" style="grid-template-columns:2fr 1fr 1fr 1fr;">
            <div>
                <label class="form-label">Proveedor *</label>
                <select name="proveedor_id" class="form-input {{ $errors->has('proveedor_id') ? 'is-invalid' : '' }}" required>
                    <option value="">— Selecciona —</option>
                    @foreach($proveedores as $prov)
                    <option value="{{ $prov->id }}" {{ (old('proveedor_id', $proveedor?->id) == $prov->id) ? 'selected' : '' }}>
                        {{ $prov->nombre }}
                    </option>
                    @endforeach
                </select>
                @error('proveedor_id') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="form-label">Fecha de compra *</label>
                <input type="date" name="fecha_compra" class="form-input {{ $errors->has('fecha_compra') ? 'is-invalid' : '' }}"
                       value="{{ old('fecha_compra', date('Y-m-d')) }}" required>
                @error('fecha_compra') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="form-label">Número de factura</label>
                <input type="text" name="numero_factura" class="form-input" value="{{ old('numero_factura') }}" placeholder="FAC-0001">
            </div>
            <div>
                <label class="form-label">Método de pago *</label>
                <select name="metodo_pago" class="form-input {{ $errors->has('metodo_pago') ? 'is-invalid' : '' }}"
                        id="metodoPago" onchange="toggleVencimiento()" required>
                    <option value="">— Selecciona —</option>
                    <option value="efectivo"        {{ old('metodo_pago') === 'efectivo'        ? 'selected' : '' }}>Efectivo</option>
                    <option value="transferencia"   {{ old('metodo_pago') === 'transferencia'   ? 'selected' : '' }}>Transferencia</option>
                    <option value="tarjeta_credito" {{ old('metodo_pago') === 'tarjeta_credito' ? 'selected' : '' }}>Tarjeta Crédito</option>
                    <option value="tarjeta_debito"  {{ old('metodo_pago') === 'tarjeta_debito'  ? 'selected' : '' }}>Tarjeta Débito</option>
                    <option value="cheque"          {{ old('metodo_pago') === 'cheque'          ? 'selected' : '' }}>Cheque</option>
                    <option value="credito"         {{ old('metodo_pago') === 'credito'         ? 'selected' : '' }}>Crédito</option>
                    <option value="otro"            {{ old('metodo_pago') === 'otro'            ? 'selected' : '' }}>Otro</option>
                </select>
                @error('metodo_pago') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
        </div>
        <div id="boxVencimiento" style="margin-top:.85rem; display:none;">
            <label class="form-label">Fecha de vencimiento (pago a crédito)</label>
            <input type="date" name="fecha_vencimiento" class="form-input" style="max-width:220px;" value="{{ old('fecha_vencimiento') }}">
        </div>
    </div>
</div>

{{-- Sección 2: Items --}}
<div class="panel-card">
    <div class="panel-header">
        <div class="panel-titulo"><i class="bi bi-list-ul"></i> Items de la Compra</div>
        <button type="button" class="btn-azul" onclick="agregarItem()"><i class="bi bi-plus-lg"></i> Agregar item</button>
    </div>
    <div class="panel-body" style="padding:.75rem;">
        <div style="overflow-x:auto;">
        <table class="tabla-items" id="tablaItems">
            <thead>
                <tr>
                    <th style="min-width:200px;">Descripción del producto</th>
                    <th style="min-width:150px;">Material vinculado</th>
                    <th style="min-width:80px;">Cantidad</th>
                    <th style="min-width:100px;">Unidad</th>
                    <th style="min-width:120px;">Precio unitario</th>
                    <th style="min-width:100px; text-align:right;">Total</th>
                    <th style="width:40px;"></th>
                </tr>
            </thead>
            <tbody id="itemsBody">
            </tbody>
        </table>
        </div>
        <div id="emptyItems" style="padding:1.5rem; text-align:center; color:#9ca3af; font-size:.82rem; display:none;">
            <i class="bi bi-plus-circle" style="font-size:1.5rem; display:block; margin-bottom:.3rem;"></i>
            Haz clic en "Agregar item" para añadir productos.
        </div>
    </div>
</div>

{{-- Datalist de materiales --}}
<datalist id="dlMateriales">
    @foreach($materiales as $mat)
    <option value="{{ $mat->nombre }}">{{ $mat->unidad_medida }}</option>
    @endforeach
</datalist>

{{-- Sección 3: Totales --}}
<div class="panel-card">
    <div class="panel-header">
        <div class="panel-titulo"><i class="bi bi-calculator"></i> Totales</div>
    </div>
    <div class="panel-body">
        <div style="max-width:340px; margin-left:auto;">
            <div class="totales-box">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span id="lblSubtotal">$0</span>
                </div>
                <div class="total-row">
                    <span>Descuento</span>
                    <div style="display:flex; align-items:center; gap:.5rem;">
                        <span style="font-size:.8rem; color:#9ca3af;">$</span>
                        <input type="text" name="descuento_valor" id="descuento" class="inp-item" style="width:110px; text-align:right;"
                               value="{{ old('descuento_valor', '0') }}" oninput="calcularTotales()" placeholder="0">
                    </div>
                </div>
                <div class="total-row" style="padding-top:.5rem;">
                    <span>TOTAL</span>
                    <span id="lblTotal">$0</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Sección 4: Notas --}}
<div class="panel-card">
    <div class="panel-header">
        <div class="panel-titulo"><i class="bi bi-chat-text"></i> Notas</div>
    </div>
    <div class="panel-body">
        <textarea name="notas" class="form-input" rows="2" placeholder="Observaciones sobre esta compra…">{{ old('notas') }}</textarea>
    </div>
</div>

<div class="info-box" style="margin-bottom:1rem;">
    <i class="bi bi-lightbulb-fill" style="margin-top:.1rem;"></i>
    <div>Los items vinculados a un material del inventario actualizarán automáticamente el stock al guardar la compra.</div>
</div>

<div style="display:flex; gap:.75rem; justify-content:flex-end;">
    <a href="{{ route('compras.index') }}" class="btn-gris">Cancelar</a>
    <button type="submit" class="btn-morado"><i class="bi bi-cart-check"></i> Registrar Compra</button>
</div>
</form>
</div>
@endsection

@php
    $materialesJson = $materiales->map(function($m) {
        return ['id' => $m->id, 'nombre' => $m->nombre, 'unidad' => $m->unidad_medida, 'precio' => $m->precio_unitario];
    });
@endphp
@push('scripts')
<script>
// Datos de materiales para autocompletado
const materiales = @json($materialesJson);

let itemCount = 0;

function agregarItem() {
    const tbody = document.getElementById('itemsBody');
    const idx = itemCount++;
    const tr = document.createElement('tr');
    tr.id = 'item-' + idx;

    const optsUnidades = ['unidades','cajas','paquetes','frascos','rollos','pares','litros','mililitros','metros','cm'].map(u =>
        `<option value="${u}">${u}</option>`
    ).join('');

    const optsMateriales = '<option value="">— Sin vincular —</option>' + materiales.map(m =>
        `<option value="${m.id}" data-unidad="${m.unidad}" data-precio="${m.precio}">${m.nombre}</option>`
    ).join('');

    tr.innerHTML = `
        <td>
            <input type="text" name="items[${idx}][descripcion]" class="inp-item" required
                   list="dlMateriales" placeholder="Nombre del producto" oninput="onDescripcion(this, ${idx})">
        </td>
        <td>
            <select name="items[${idx}][material_id]" class="inp-item" id="mat-${idx}" onchange="onMaterial(this, ${idx})">
                ${optsMateriales}
            </select>
        </td>
        <td>
            <input type="number" name="items[${idx}][cantidad]" class="inp-item" id="cant-${idx}"
                   min="0.01" step="0.01" value="1" required oninput="calcularFila(${idx}); calcularTotales();">
        </td>
        <td>
            <select name="items[${idx}][unidad_medida]" class="inp-item" id="unidad-${idx}" required>
                ${optsUnidades}
            </select>
        </td>
        <td>
            <input type="text" name="items[${idx}][precio_unitario]" class="inp-item" id="precio-${idx}"
                   value="0" required oninput="calcularFila(${idx}); calcularTotales();">
        </td>
        <td style="text-align:right; font-weight:600; white-space:nowrap; color:#166534;" id="total-${idx}">$0</td>
        <td>
            <button type="button" class="btn-rojo" onclick="eliminarItem(${idx})"><i class="bi bi-trash"></i></button>
        </td>
    `;
    tbody.appendChild(tr);
    document.getElementById('emptyItems').style.display = 'none';
    calcularTotales();
}

function eliminarItem(idx) {
    document.getElementById('item-' + idx)?.remove();
    calcularTotales();
    if (document.querySelectorAll('#itemsBody tr').length === 0) {
        document.getElementById('emptyItems').style.display = 'block';
    }
}

function onMaterial(sel, idx) {
    const opt = sel.options[sel.selectedIndex];
    if (opt.value) {
        const unidad = opt.dataset.unidad;
        const precio = parseFloat(opt.dataset.precio) || 0;
        // Actualizar unidad
        const unidadSel = document.getElementById('unidad-' + idx);
        if (unidadSel) {
            // Buscar opción o agregarla
            let found = false;
            for (let o of unidadSel.options) {
                if (o.value === unidad) { o.selected = true; found = true; break; }
            }
            if (!found && unidad) {
                const newOpt = new Option(unidad, unidad, true, true);
                unidadSel.add(newOpt);
            }
        }
        // Actualizar precio
        if (precio > 0) {
            const precioInp = document.getElementById('precio-' + idx);
            if (precioInp) precioInp.value = precio.toLocaleString('es-CO');
        }
        // Autocompletar descripción con nombre del material
        const descInp = document.querySelector(`[name="items[${idx}][descripcion]"]`);
        if (descInp && !descInp.value) descInp.value = opt.text.trim();
    }
    calcularFila(idx);
    calcularTotales();
}

function onDescripcion(input, idx) {
    // Autocompletar material si coincide con nombre
    const val = input.value.toLowerCase().trim();
    const matSel = document.getElementById('mat-' + idx);
    if (!matSel) return;
    for (let opt of matSel.options) {
        if (opt.text.toLowerCase().trim() === val) {
            opt.selected = true;
            onMaterial(matSel, idx);
            break;
        }
    }
}

function limpiarNum(str) {
    if (!str) return 0;
    return parseFloat(String(str).replace(/\./g, '').replace(',', '.')) || 0;
}

function calcularFila(idx) {
    const cant   = parseFloat(document.getElementById('cant-' + idx)?.value || 0);
    const precio = limpiarNum(document.getElementById('precio-' + idx)?.value || 0);
    const total  = cant * precio;
    const lbl    = document.getElementById('total-' + idx);
    if (lbl) lbl.textContent = '$' + total.toLocaleString('es-CO', {maximumFractionDigits:0});
}

function calcularTotales() {
    let subtotal = 0;
    document.querySelectorAll('#itemsBody tr').forEach(tr => {
        const idx = tr.id.split('-')[1];
        const cant   = parseFloat(document.getElementById('cant-' + idx)?.value || 0);
        const precio = limpiarNum(document.getElementById('precio-' + idx)?.value || 0);
        subtotal += cant * precio;
    });
    const desc  = limpiarNum(document.getElementById('descuento')?.value || 0);
    const total = subtotal - desc;
    document.getElementById('lblSubtotal').textContent = '$' + subtotal.toLocaleString('es-CO', {maximumFractionDigits:0});
    document.getElementById('lblTotal').textContent    = '$' + total.toLocaleString('es-CO', {maximumFractionDigits:0});
}

function toggleVencimiento() {
    const metodo = document.getElementById('metodoPago').value;
    document.getElementById('boxVencimiento').style.display = metodo === 'credito' ? 'block' : 'none';
}

// Limpiar precios antes de enviar
document.getElementById('formCompra').addEventListener('submit', function() {
    document.querySelectorAll('[id^="precio-"]').forEach(inp => {
        inp.value = limpiarNum(inp.value);
    });
    const desc = document.getElementById('descuento');
    if (desc) desc.value = limpiarNum(desc.value);
});

// Inicializar
document.addEventListener('DOMContentLoaded', () => {
    agregarItem();
    toggleVencimiento();
    document.getElementById('emptyItems').style.display = 'none';
});
</script>
@endpush
