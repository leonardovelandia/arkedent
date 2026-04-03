@extends('layouts.app')
@section('titulo', 'Editar Valoración — ' . $valoracion->numero_valoracion)

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-verde { background:linear-gradient(135deg,#16a34a,#15803d); color:#fff; border:none; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; cursor:pointer; }
    .btn-verde:hover { filter:brightness(1.1); }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; }
    .doc-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:14px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1.5rem; }
    .doc-section { border-bottom:1px solid var(--fondo-borde); }
    .doc-section:last-child { border-bottom:none; }
    .doc-section-header { font-family:var(--fuente-principal); padding:.7rem 1.25rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); display:flex; align-items:center; gap:.5rem; }
    .doc-section-body { padding:1.1rem 1.25rem; }
    .sec-extraoral { background:var(--fondo-card-alt); }
    .sec-intraoral { background:#f0f7ff; }
    .sec-diagnosticos { background:#fffdf0; }
    .sec-plan { background:#f0fff4; }
    .form-lbl { font-size:.73rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--color-principal); margin-bottom:.25rem; display:block; }
    .form-ctrl { width:100%; border:1px solid #d1d5db; border-radius:8px; padding:.45rem .75rem; font-size:.875rem; color:#374151; background:#fff; transition:border-color .15s; }
    .form-ctrl:focus { outline:none; border-color:var(--color-principal); box-shadow:0 0 0 3px var(--sombra-principal); }
    .tabla-dinamica { width:100%; border-collapse:collapse; font-size:.82rem; }
    .tabla-dinamica thead th { background:var(--color-muy-claro); color:var(--color-hover); font-size:.7rem; font-weight:700; text-transform:uppercase; padding:.45rem .6rem; border-bottom:2px solid var(--color-muy-claro); white-space:nowrap; }
    .tabla-dinamica tbody td { padding:.35rem .45rem; border-bottom:1px solid var(--fondo-borde); vertical-align:middle; }
    .tabla-dinamica .inp { border:1px solid #e5e7eb; border-radius:6px; padding:.32rem .55rem; font-size:.82rem; width:100%; }
    .tabla-dinamica .inp:focus { outline:none; border-color:var(--color-principal); }
    .btn-del-row { background:none; border:none; color:#dc2626; cursor:pointer; font-size:.85rem; padding:.2rem .4rem; border-radius:4px; }
    .btn-del-row:hover { background:#fee2e2; }
    .btn-add-row { display:inline-flex; align-items:center; gap:.35rem; font-size:.8rem; color:var(--color-principal); background:none; border:1px dashed var(--color-claro); border-radius:6px; padding:.3rem .75rem; cursor:pointer; margin-top:.5rem; transition:background .15s; }
    .btn-add-row:hover { background:var(--color-muy-claro); }
    .total-plan { font-size:1.5rem; font-weight:800; color:#166534; }
    .cie10-wrapper { position:relative; }
    .cie10-dropdown { position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid var(--color-muy-claro); border-radius:8px; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); z-index:999; max-height:200px; overflow-y:auto; display:none; }
    .cie10-item { padding:.45rem .75rem; cursor:pointer; font-size:.82rem; }
    .cie10-item:hover { background:var(--color-muy-claro); }
    .cie10-code { font-family:monospace; font-weight:700; color:var(--color-principal); margin-right:.4rem; }
</style>
@endpush

@section('contenido')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h1 style="font-family:var(--fuente-titulos);font-size:1.4rem;font-weight:700;color:var(--color-principal);margin:0;">
            <i class="bi bi-pencil me-2"></i>Editar Valoración
        </h1>
        <p style="font-size:.85rem;color:#9ca3af;margin:.2rem 0 0;">
            <span style="font-family:monospace;font-weight:700;">{{ $valoracion->numero_valoracion }}</span> · {{ $valoracion->paciente->nombre_completo }}
        </p>
    </div>
    <a href="{{ route('valoraciones.show', $valoracion) }}" class="btn-gris"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:.85rem 1.1rem;margin-bottom:1rem;color:#991b1b;font-size:.875rem;">
    <i class="bi bi-exclamation-triangle-fill me-1"></i>
    <ul style="margin:.35rem 0 0 1rem;padding:0;">
        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('valoraciones.update', $valoracion) }}" id="form-valoracion">
@csrf @method('PUT')

<input type="hidden" name="paciente_id"    value="{{ $valoracion->paciente_id }}">
<input type="hidden" name="plan_tratamiento" id="json-plan">
<input type="hidden" name="estado"          id="campo-estado" value="{{ $valoracion->estado }}">

<div class="doc-card">

{{-- Sección 1: Datos generales --}}
<div class="doc-section">
    <div class="doc-section-header" style="background:var(--color-muy-claro);">
        <i class="bi bi-info-circle" style="color:var(--color-principal);"></i> Datos Generales
    </div>
    <div class="doc-section-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div style="font-size:.82rem;font-weight:600;color:#374151;background:var(--color-muy-claro);border-radius:8px;padding:.6rem .9rem;">
                    <i class="bi bi-person me-1" style="color:var(--color-principal);"></i>{{ $valoracion->paciente->nombre_completo }}
                    <span style="font-size:.75rem;color:#9ca3af;"> — {{ $valoracion->paciente->numero_historia }}</span>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-lbl">Fecha *</label>
                <input type="date" name="fecha" value="{{ old('fecha', $valoracion->fecha->format('Y-m-d')) }}" class="form-ctrl" required>
            </div>
            <div class="col-12">
                <label class="form-lbl">Motivo de consulta *</label>
                <textarea name="motivo_consulta" class="form-ctrl" rows="2" required>{{ old('motivo_consulta', $valoracion->motivo_consulta) }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- Sección 2: Examen Extraoral --}}
<div class="doc-section sec-extraoral">
    <div class="doc-section-header"><i class="bi bi-person-bounding-box" style="color:var(--color-principal);"></i> Examen Extraoral</div>
    <div class="doc-section-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-lbl">Cara</label>
                <textarea name="extraoral_cara" class="form-ctrl" rows="2">{{ old('extraoral_cara', $valoracion->extraoral_cara) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-lbl">ATM</label>
                <textarea name="extraoral_atm" class="form-ctrl" rows="2">{{ old('extraoral_atm', $valoracion->extraoral_atm) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-lbl">Ganglios linfáticos</label>
                <textarea name="extraoral_ganglios" class="form-ctrl" rows="2">{{ old('extraoral_ganglios', $valoracion->extraoral_ganglios) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-lbl">Labios y comisuras</label>
                <textarea name="extraoral_labios" class="form-ctrl" rows="2">{{ old('extraoral_labios', $valoracion->extraoral_labios) }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-lbl">Observaciones extraorales</label>
                <textarea name="extraoral_observaciones" class="form-ctrl" rows="2">{{ old('extraoral_observaciones', $valoracion->extraoral_observaciones) }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- Sección 3: Examen Intraoral --}}
<div class="doc-section sec-intraoral">
    <div class="doc-section-header"><i class="bi bi-camera" style="color:var(--color-principal);"></i> Examen Intraoral</div>
    <div class="doc-section-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-lbl">Encías y periodonto</label>
                <textarea name="intraoral_encias" class="form-ctrl" rows="2">{{ old('intraoral_encias', $valoracion->intraoral_encias) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-lbl">Mucosa oral</label>
                <textarea name="intraoral_mucosa" class="form-ctrl" rows="2">{{ old('intraoral_mucosa', $valoracion->intraoral_mucosa) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-lbl">Lengua y piso de boca</label>
                <textarea name="intraoral_lengua" class="form-ctrl" rows="2">{{ old('intraoral_lengua', $valoracion->intraoral_lengua) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-lbl">Paladar</label>
                <textarea name="intraoral_paladar" class="form-ctrl" rows="2">{{ old('intraoral_paladar', $valoracion->intraoral_paladar) }}</textarea>
            </div>
            <div class="col-md-4">
                <label class="form-lbl">Higiene oral</label>
                <select name="intraoral_higiene" class="form-ctrl">
                    <option value="">— Sin evaluar —</option>
                    @foreach(['excelente','buena','regular','mala'] as $h)
                    <option value="{{ $h }}" {{ old('intraoral_higiene', $valoracion->intraoral_higiene) == $h ? 'selected' : '' }}>{{ ucfirst($h) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-lbl">Observaciones intraorales</label>
                <textarea name="intraoral_observaciones" class="form-ctrl" rows="2">{{ old('intraoral_observaciones', $valoracion->intraoral_observaciones) }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- Sección 3B: Odontograma --}}
<div class="doc-section">
    <div class="doc-section-header" style="background:var(--color-muy-claro);">
        <i class="bi bi-grid-3x3" style="color:var(--color-principal);"></i> Odontograma
    </div>
    <div class="doc-section-body">
        <x-odontograma :datos="old('odontograma', $valoracion->odontograma)" :modo="'editar'" :hallazgos="old('hallazgos', $valoracion->hallazgos)" />
    </div>
</div>

{{-- Sección 5: Plan de Tratamiento --}}
<div class="doc-section sec-plan">
    <div class="doc-section-header"><i class="bi bi-list-check" style="color:var(--color-principal);"></i> Plan de Tratamiento</div>
    <div class="doc-section-body">
        <div style="overflow-x:auto;">
        <table class="tabla-dinamica" id="tabla-plan">
            <thead><tr>
                <th style="width:30px;">N°</th>
                <th>Procedimiento</th>
                <th style="width:70px;">Diente</th>
                <th style="width:80px;">Cara</th>
                <th style="width:65px;">Cant.</th>
                <th style="width:110px;">V. Unitario</th>
                <th style="width:110px;">Total</th>
                <th style="width:85px;">Prioridad</th>
                <th style="width:36px;"></th>
            </tr></thead>
            <tbody id="body-plan"></tbody>
        </table>
        </div>
        <button type="button" class="btn-add-row" onclick="addProcedimiento()"><i class="bi bi-plus-circle"></i> Agregar procedimiento</button>
        <div style="margin-top:1rem;padding:1rem;background:#dcfce7;border:1px solid #86efac;border-radius:10px;">
            <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:#166534;">Total</div>
            <div class="total-plan" id="total-plan">$ 0</div>
        </div>
    </div>
</div>

{{-- Sección 6: Diagnóstico Final --}}
<div class="doc-section">
    <div class="doc-section-header" style="background:var(--color-muy-claro);"><i class="bi bi-clipboard2-check" style="color:var(--color-principal);"></i> Pronóstico</div>
    <div class="doc-section-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-lbl">Pronóstico</label>
                <select name="pronostico" class="form-ctrl">
                    <option value="">— Sin definir —</option>
                    @foreach(['excelente','bueno','reservado','malo'] as $p)
                    <option value="{{ $p }}" {{ old('pronostico', $valoracion->pronostico) == $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-lbl">Observaciones generales</label>
                <textarea name="observaciones_generales" class="form-ctrl" rows="3">{{ old('observaciones_generales', $valoracion->observaciones_generales) }}</textarea>
            </div>
        </div>
    </div>
</div>

</div>

<div style="display:flex;gap:.75rem;flex-wrap:wrap;">
    <button type="submit" class="btn-morado" onclick="serializarYEnviar('en_proceso')">
        <i class="bi bi-floppy"></i> Guardar Cambios
    </button>
    <button type="button" class="btn-verde" onclick="serializarYEnviar('completada')">
        <i class="bi bi-check-circle"></i> Guardar y Completar
    </button>
    <a href="{{ route('valoraciones.show', $valoracion) }}" class="btn-gris"><i class="bi bi-x-circle"></i> Cancelar</a>
</div>

</form>

@push('scripts')
<script>
const CIE10 = [
    { codigo: 'K02.0', nombre: 'Caries limitada al esmalte' },
    { codigo: 'K02.1', nombre: 'Caries de la dentina' },
    { codigo: 'K02.2', nombre: 'Caries del cemento' },
    { codigo: 'K02.3', nombre: 'Caries dentaria detenida' },
    { codigo: 'K04.0', nombre: 'Pulpitis' },
    { codigo: 'K04.1', nombre: 'Necrosis de la pulpa' },
    { codigo: 'K04.5', nombre: 'Periodontitis apical crónica' },
    { codigo: 'K05.0', nombre: 'Gingivitis aguda' },
    { codigo: 'K05.1', nombre: 'Gingivitis crónica' },
    { codigo: 'K05.2', nombre: 'Periodontitis aguda' },
    { codigo: 'K05.3', nombre: 'Periodontitis crónica' },
    { codigo: 'K08.1', nombre: 'Pérdida de dientes por accidente' },
    { codigo: 'K08.2', nombre: 'Atrofia del maxilar' },
    { codigo: 'S02.5', nombre: 'Fractura del diente' },
    { codigo: 'Z29.8', nombre: 'Sellante preventivo' },
    { codigo: 'K06.0', nombre: 'Recesión gingival' },
    { codigo: 'K07.0', nombre: 'Anomalías del maxilar' },
    { codigo: 'K07.3', nombre: 'Anomalías de posición dentaria' },
    { codigo: 'K08.0', nombre: 'Pérdida de dientes por extracción' },
    { codigo: 'K09.0', nombre: 'Quiste dentígero' },
];
const CARAS = ['Vestibular','Palatino','Mesial','Distal','Oclusal','Incisal','Lingual','Cervical'];
const PRIORIDADES = ['Alta','Media','Baja'];

var planData = @json($valoracion->plan_tratamiento ?? []);

function addProcedimiento(p) {
    p = p || { procedimiento:'', diente:'', cara:'', cantidad:1, valor_unitario:0, prioridad:'Media', notas:'' };
    planData.push(p);
    renderPlan();
}
function removeProc(i) { planData.splice(i, 1); renderPlan(); calcularTotal(); }

function renderPlan() {
    var tbody = document.getElementById('body-plan');
    tbody.innerHTML = '';
    planData.forEach(function(p, i) {
        var total = (parseFloat(p.valor_unitario)||0) * (parseInt(p.cantidad)||1);
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td style="text-align:center;color:#9ca3af;font-size:.75rem;">' + (i+1) + '</td>' +
            '<td><input class="inp" list="procs-list" value="' + esc(p.procedimiento) + '" oninput="planData[' + i + '].procedimiento=this.value"></td>' +
            '<td><input class="inp" value="' + esc(p.diente) + '" oninput="planData[' + i + '].diente=this.value"></td>' +
            '<td>' + selectCara('planData[' + i + '].cara', p.cara) + '</td>' +
            '<td><input class="inp" type="number" min="1" value="' + (p.cantidad||1) + '" oninput="planData[' + i + '].cantidad=parseInt(this.value)||1;recalcRow(' + i + ')"></td>' +
            '<td><input class="inp" type="number" min="0" value="' + (p.valor_unitario||0) + '" oninput="planData[' + i + '].valor_unitario=parseFloat(this.value)||0;recalcRow(' + i + ')"></td>' +
            '<td id="total-row-' + i + '" style="font-weight:700;color:#166534;text-align:right;white-space:nowrap;">$ ' + fmtMoney(total) + '</td>' +
            '<td><select class="inp" onchange="planData[' + i + '].prioridad=this.value">' + PRIORIDADES.map(function(pr){ return '<option value="' + pr + '" ' + (p.prioridad===pr?'selected':'') + '>' + pr + '</option>'; }).join('') + '</select></td>' +
            '<td><button type="button" class="btn-del-row" onclick="removeProc(' + i + ')"><i class="bi bi-trash3"></i></button></td>';
        tbody.appendChild(tr);
    });
    calcularTotal();
}
function recalcRow(i) {
    var total = (parseFloat(planData[i].valor_unitario)||0) * (parseInt(planData[i].cantidad)||1);
    var el = document.getElementById('total-row-' + i);
    if (el) el.textContent = '$ ' + fmtMoney(total);
    calcularTotal();
}
function calcularTotal() {
    var total = planData.reduce(function(s, p){ return s + ((parseFloat(p.valor_unitario)||0) * (parseInt(p.cantidad)||1)); }, 0);
    document.getElementById('total-plan').textContent = '$ ' + fmtMoney(total);
}
function selectCara(bindExpr, valor) {
    return '<select class="inp" onchange="' + bindExpr + '=this.value"><option value="">—</option>' +
        CARAS.map(function(c){ return '<option value="' + c + '" ' + (valor===c?'selected':'') + '>' + c + '</option>'; }).join('') + '</select>';
}
function fmtMoney(n) { return Math.round(n).toLocaleString('es-CO'); }
function esc(s) { return String(s||'').replace(/"/g, '&quot;'); }

function serializarYEnviar(estado) {
    document.getElementById('campo-estado').value = estado;
    document.getElementById('json-plan').value = JSON.stringify(planData.filter(function(p){ return p.procedimiento; }));
    document.getElementById('form-valoracion').submit();
}

renderPlan();
</script>
<datalist id="procs-list">
    @foreach(['Extracción simple','Extracción quirúrgica','Obturación resina','Amalgama','Endodoncia','Corona PFM','Corona zirconia','Puente','Limpieza','Blanqueamiento','Ortodoncia','Implante','Prótesis parcial removible','Prótesis total','Sellante de fisuras','Radiografía periapical','Radiografía panorámica'] as $proc)
    <option value="{{ $proc }}">
    @endforeach
</datalist>
@endpush

@endsection
