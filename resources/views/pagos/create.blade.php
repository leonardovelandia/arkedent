@extends('layouts.app')
@section('titulo', 'Registrar Pago')

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

    .resumen-card { background:linear-gradient(135deg,var(--color-muy-claro),var(--fondo-card-alt)); border:1px solid var(--color-claro); border-radius:12px; padding:1.1rem 1.25rem; margin-bottom:1rem; }
    .resumen-row { display:flex; justify-content:space-between; align-items:center; padding:.3rem 0; font-size:.875rem; }
    .resumen-total { font-size:1.1rem; font-weight:800; color:var(--color-sidebar-2); border-top:1px solid var(--color-claro); margin-top:.4rem; padding-top:.5rem; }
    .saldo-hint { font-size:.8rem; margin-top:.3rem; font-weight:600; }
</style>
@endpush

@section('contenido')

<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('pagos.index') }}"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem .9rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
    <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Registrar Pago</h4>
</div>

<form method="POST" action="{{ route('pagos.store') }}">
@csrf

<div class="form-card">
    <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--color-principal);margin-bottom:1rem;">
        <i class="bi bi-person"></i> Datos del Paciente y Tratamiento
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        {{-- Paciente --}}
        <div class="form-group">
            <label class="form-label">Paciente <span style="color:#dc2626;">*</span></label>
            <x-buscador-paciente
                :pacientes="$pacientes"
                :valor-inicial="old('paciente_id', $pacienteSeleccionado?->id)"
                campo-nombre="numero_documento"
                placeholder="Buscar paciente por nombre o documento…" />
            @error('paciente_id')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        {{-- Tratamiento --}}
        <div class="form-group">
            <label class="form-label">Tratamiento</label>
            {{-- Aviso presupuestos aprobados --}}
            <div id="aviso-presupuestos" style="display:none;margin-bottom:.5rem;">
                <div style="background:var(--color-muy-claro);border:1px solid var(--color-principal);border-radius:8px;padding:.65rem .9rem;">
                    <div style="font-size:.78rem;font-weight:600;color:var(--color-principal);margin-bottom:.35rem;">
                        <i class="bi bi-file-earmark-check"></i> Presupuestos aprobados con saldo pendiente
                    </div>
                    <div id="lista-presupuestos-aprobados" style="font-size:.78rem;color:var(--color-sidebar-2);line-height:1.7;"></div>
                </div>
            </div>
            <select name="tratamiento_id" id="sel-tratamiento" class="form-select" onchange="seleccionarTratamiento(this)">
                <option value="">Sin tratamiento específico</option>
                @foreach($tratamientos as $t)
                <option value="{{ $t->id }}"
                    data-saldo="{{ $t->saldo_pendiente }}"
                    data-nombre="{{ $t->nombre }}"
                    {{ old('tratamiento_id', $tratamientoSeleccionado?->id) == $t->id ? 'selected' : '' }}>
                    {{ $t->nombre }} (Saldo: $ {{ number_format($t->saldo_pendiente, 0, ',', '.') }})
                </option>
                @endforeach
            </select>
            <div id="saldo-hint" class="saldo-hint" style="display:none;color:#dc2626;">
                Saldo pendiente: <span id="saldo-val"></span>
            </div>
            @error('tratamiento_id')<div class="form-error">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="form-card">
    <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--color-principal);margin-bottom:1rem;">
        <i class="bi bi-cash-coin"></i> Datos del Pago
    </div>

    {{-- Concepto --}}
    <div class="form-group">
        <label class="form-label">Concepto <span style="color:#dc2626;">*</span></label>
        <input type="text" name="concepto" id="inp-concepto" class="form-input"
               value="{{ old('concepto') }}" placeholder="Descripción del pago…" required maxlength="255">
        @error('concepto')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        {{-- Valor --}}
        <div class="form-group">
            <label class="form-label">Valor (COP) <span style="color:#dc2626;">*</span></label>
            <input type="text" inputmode="numeric" name="valor" id="inp-valor" class="form-input"
                   value="{{ old('valor') }}" placeholder="0" required data-money
                   oninput="calcularSaldoRestante()">
            <div id="saldo-restante-hint" class="saldo-hint" style="display:none;color:#166534;"></div>
            @error('valor')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        {{-- Fecha --}}
        <div class="form-group">
            <label class="form-label">Fecha de pago <span style="color:#dc2626;">*</span></label>
            <input type="date" name="fecha_pago" class="form-input"
                   value="{{ old('fecha_pago', date('Y-m-d')) }}" required>
            @error('fecha_pago')<div class="form-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        {{-- Método de pago --}}
        <div class="form-group">
            <label class="form-label">Método de pago <span style="color:#dc2626;">*</span></label>
            <select name="metodo_pago" id="sel-metodo" class="form-select" required onchange="toggleReferencia(this.value)">
                <option value="">Seleccionar método…</option>
                <option value="efectivo"        {{ old('metodo_pago') === 'efectivo'        ? 'selected' : '' }}>💵 Efectivo</option>
                <option value="transferencia"   {{ old('metodo_pago') === 'transferencia'   ? 'selected' : '' }}>🏦 Transferencia</option>
                <option value="tarjeta_credito" {{ old('metodo_pago') === 'tarjeta_credito' ? 'selected' : '' }}>💳 Tarjeta Crédito</option>
                <option value="tarjeta_debito"  {{ old('metodo_pago') === 'tarjeta_debito'  ? 'selected' : '' }}>💳 Tarjeta Débito</option>
                <option value="datafono"        {{ old('metodo_pago') === 'datafono'        ? 'selected' : '' }}>🖥️ Datáfono</option>
                <option value="cheque"          {{ old('metodo_pago') === 'cheque'         ? 'selected' : '' }}>📄 Cheque</option>
                <option value="otro"            {{ old('metodo_pago') === 'otro'            ? 'selected' : '' }}>🔄 Otro</option>
            </select>
            @error('metodo_pago')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        {{-- Referencia --}}
        <div class="form-group" id="grupo-referencia" style="display:none;">
            <label class="form-label">Referencia / N° transacción</label>
            <input type="text" name="referencia_pago" class="form-input"
                   value="{{ old('referencia_pago') }}" placeholder="Número de referencia…" maxlength="100">
            @error('referencia_pago')<div class="form-error">{{ $message }}</div>@enderror
        </div>
    </div>

    {{-- Observaciones --}}
    <div class="form-group">
        <label class="form-label">Observaciones</label>
        <textarea name="observaciones" class="form-input" rows="2"
                  placeholder="Observaciones adicionales…">{{ old('observaciones') }}</textarea>
        @error('observaciones')<div class="form-error">{{ $message }}</div>@enderror
    </div>
</div>

{{-- Resumen --}}
<div class="resumen-card" id="resumen-pago" style="display:none;">
    <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--color-sidebar-2);margin-bottom:.6rem;">
        <i class="bi bi-receipt"></i> Resumen del pago
    </div>
    <div class="resumen-row">
        <span style="color:#6b7280;">Valor a pagar:</span>
        <span id="res-valor" style="font-weight:700;color:#1c2b22;"></span>
    </div>
    <div class="resumen-row" id="res-saldo-row" style="display:none;">
        <span style="color:#6b7280;">Saldo que quedará:</span>
        <span id="res-saldo" style="font-weight:700;color:#dc2626;"></span>
    </div>
</div>

<div style="display:flex;gap:.5rem;flex-wrap:wrap;">
    <button type="submit" class="btn-morado">
        <i class="bi bi-check-circle"></i> Registrar Pago
    </button>
    <a href="{{ route('pagos.index') }}"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem 1.1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;">
        Cancelar
    </a>
</div>

</form>
@endsection

@push('scripts')
<script>
var saldoActual = 0;
var tratamientosData = @json($tratamientos->keyBy('id'));

function cargarTratamientos(pacienteId) {
    var sel = document.getElementById('sel-tratamiento');
    sel.innerHTML = '<option value="">Sin tratamiento específico</option>';
    document.getElementById('saldo-hint').style.display = 'none';
    document.getElementById('aviso-presupuestos').style.display = 'none';
    document.getElementById('lista-presupuestos-aprobados').innerHTML = '';
    saldoActual = 0;
    calcularSaldoRestante();

    if (!pacienteId) return;

    fetch('/api/pacientes/' + pacienteId + '/tratamientos')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            data.forEach(function(t) {
                var opt = document.createElement('option');
                opt.value = t.id;
                opt.dataset.saldo  = t.saldo_pendiente;
                opt.dataset.nombre = t.nombre;
                opt.textContent = t.nombre + ' (Saldo: $ ' + formatNum(t.saldo_pendiente) + ')';
                sel.appendChild(opt);
            });
        });

    fetch('/api/pacientes/' + pacienteId + '/presupuestos-aprobados')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.length > 0) {
                var lista = document.getElementById('lista-presupuestos-aprobados');
                lista.innerHTML = '';
                data.forEach(function(p) {
                    if (p.saldo_pendiente > 0) {
                        lista.innerHTML += '• <strong>' + p.numero + '</strong> — Total: ' + p.total_formateado +
                            ' — Saldo pendiente: <strong>$ ' + formatNum(p.saldo_pendiente) + '</strong><br>';
                    }
                });
                if (lista.innerHTML) {
                    document.getElementById('aviso-presupuestos').style.display = 'block';
                }
            }
        })
        .catch(function() {});
}

function seleccionarTratamiento(sel) {
    var opt = sel.options[sel.selectedIndex];
    var saldoHint = document.getElementById('saldo-hint');
    var conceptoInp = document.getElementById('inp-concepto');

    if (opt.value && opt.dataset.saldo) {
        saldoActual = parseFloat(opt.dataset.saldo);
        document.getElementById('saldo-val').textContent = '$ ' + formatNum(saldoActual);
        saldoHint.style.display = 'block';
        if (!conceptoInp.value) {
            conceptoInp.value = opt.dataset.nombre;
        }
    } else {
        saldoActual = 0;
        saldoHint.style.display = 'none';
    }
    calcularSaldoRestante();
}

function calcularSaldoRestante() {
    var _vRaw = document.getElementById('inp-valor');
    var valor = parseFloat((_vRaw._moneyHidden ? _vRaw._moneyHidden.value : _vRaw.value.replace(/\./g,'').replace(/[^0-9]/g,''))) || 0;
    var resumen = document.getElementById('resumen-pago');
    var resValor = document.getElementById('res-valor');
    var resSaldoRow = document.getElementById('res-saldo-row');
    var resSaldo = document.getElementById('res-saldo');
    var hint = document.getElementById('saldo-restante-hint');

    if (valor > 0) {
        resumen.style.display = 'block';
        resValor.textContent = '$ ' + formatNum(valor);
    } else {
        resumen.style.display = 'none';
        hint.style.display = 'none';
        return;
    }

    if (saldoActual > 0) {
        var restante = saldoActual - valor;
        resSaldoRow.style.display = 'flex';
        resSaldo.textContent = '$ ' + formatNum(Math.max(0, restante));

        hint.style.display = 'block';
        if (restante < 0) {
            hint.style.color = '#dc2626';
            hint.textContent = '⚠ El valor supera el saldo pendiente.';
        } else if (restante === 0) {
            hint.style.color = '#166534';
            hint.textContent = '✓ El tratamiento quedaría completamente pagado.';
        } else {
            hint.style.color = '#166534';
            hint.textContent = 'Quedará pendiente: $ ' + formatNum(restante);
        }
    } else {
        resSaldoRow.style.display = 'none';
        hint.style.display = 'none';
    }
}

function toggleReferencia(metodo) {
    var grupo = document.getElementById('grupo-referencia');
    grupo.style.display = (metodo && metodo !== 'efectivo') ? 'block' : 'none';
}

function formatNum(n) {
    return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Init
(function() {
    var metodo = document.getElementById('sel-metodo').value;
    toggleReferencia(metodo);

    // Escuchar eventos del buscador-paciente
    document.addEventListener('DOMContentLoaded', function () {
        var hidden = document.querySelector('[name="paciente_id"]');
        if (!hidden) return;
        hidden.addEventListener('bp:select', function (e) {
            cargarTratamientos(e.detail.id);
        });
        hidden.addEventListener('bp:clear', function () {
            cargarTratamientos('');
        });
        // Si ya hay un paciente preseleccionado al cargar la página
        if (hidden.value) {
            cargarTratamientos(hidden.value);
        }
    });

    var tratSel = document.getElementById('sel-tratamiento');
    if (tratSel.options[tratSel.selectedIndex] && tratSel.options[tratSel.selectedIndex].value) {
        seleccionarTratamiento(tratSel);
    }
    calcularSaldoRestante();
})();
</script>
@endpush
