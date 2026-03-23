@extends('layouts.app')
@section('titulo', 'Nueva Valoración')

@push('estilos')
{{-- Tom Select --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
/* Tom Select personalizado */
.ts-wrapper.single .ts-control {
    height: 42px;
    border: 1.5px solid var(--fondo-borde);
    border-radius: 8px;
    font-size: .9rem;
    color: var(--texto-principal);
    padding: 0 12px;
    cursor: pointer;
    box-shadow: none;
}
.ts-wrapper.single.focus .ts-control,
.ts-wrapper.single.input-active .ts-control {
    border-color: var(--color-principal) !important;
    box-shadow: 0 0 0 3px rgba(107,33,168,.08) !important;
}
.ts-dropdown {
    border: 1.5px solid var(--color-principal);
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(107,33,168,.12);
    font-size: .88rem;
}
.ts-dropdown .option.selected,
.ts-dropdown .option:hover,
.ts-dropdown .active {
    background: var(--color-muy-claro);
    color: var(--color-principal);
}
.ts-dropdown-content { max-height: 220px; }
</style>
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
    .is-invalid { border-color:#dc2626 !important; }
    .error-msg { color:#dc2626; font-size:.75rem; margin-top:.2rem; }

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

    .pac-info-box { background:var(--color-muy-claro); border:1px solid var(--color-muy-claro); border-radius:10px; padding:.75rem 1rem; font-size:.85rem; }

    /* Autocompletado CIE10 */
    .cie10-wrapper { position:relative; }
    .cie10-dropdown { position:fixed; background:#fff; border:1px solid var(--color-muy-claro); border-radius:8px; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.18); z-index:9999; max-height:220px; overflow-y:auto; display:none; min-width:260px; }
    .cie10-item { padding:.45rem .75rem; cursor:pointer; font-size:.82rem; }
    .cie10-item:hover { background:var(--color-muy-claro); }
    .cie10-code { font-family:monospace; font-weight:700; color:var(--color-principal); margin-right:.4rem; }
</style>
@endpush

@section('contenido')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
    <h1 style="font-family:var(--fuente-titulos);font-size:1.4rem;font-weight:700;color:#1c2b22;margin:0;">
        <i class="bi bi-clipboard2-plus me-2"></i>Nueva Valoración
    </h1>
    <a href="{{ route('valoraciones.index') }}" class="btn-gris"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:.85rem 1.1rem;margin-bottom:1rem;color:#991b1b;font-size:.875rem;">
    <i class="bi bi-exclamation-triangle-fill me-1"></i>
    <ul style="margin:.35rem 0 0 1rem;padding:0;">
        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('valoraciones.store') }}" id="form-valoracion">
@csrf

<input type="hidden" name="diagnosticos"     id="json-diagnosticos">
<input type="hidden" name="plan_tratamiento" id="json-plan">
<input type="hidden" name="estado"           id="campo-estado" value="en_proceso">

<div class="doc-card">

{{-- ═══ SECCIÓN 1: DATOS GENERALES ═══ --}}
<div class="doc-section">
    <div class="doc-section-header" style="background:var(--color-muy-claro);">
        <i class="bi bi-info-circle" style="color:var(--color-principal);"></i> Datos Generales
    </div>
    <div class="doc-section-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-lbl">Paciente *</label>
                <select name="paciente_id" id="sel-paciente" class="form-ctrl {{ $errors->has('paciente_id') ? 'is-invalid' : '' }}" required onchange="cargarPaciente(this.value)">
                    <option value="">— Seleccione paciente —</option>
                    @foreach($pacientes as $pac)
                    <option value="{{ $pac->id }}"
                        data-historia="{{ $pac->historiaClinica->numero_historia ?? '' }}"
                        data-alergias="{{ $pac->historiaClinica->alergias ?? '' }}"
                        data-edad="{{ $pac->edad }}"
                        {{ (old('paciente_id', $paciente?->id) == $pac->id) ? 'selected' : '' }}>
                        {{ $pac->nombre_completo }} — {{ $pac->numero_historia }}
                    </option>
                    @endforeach
                </select>
                @error('paciente_id')<div class="error-msg">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-lbl">Fecha *</label>
                <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" class="form-ctrl" required>
            </div>
            <div class="col-12" id="pac-info" style="{{ $paciente ? '' : 'display:none;' }}">
                <div class="pac-info-box">
                    <div style="display:flex;flex-wrap:wrap;gap:1rem;">
                        <span style="font-size:.8rem;"><strong>Historia:</strong> <span id="pac-historia">{{ $paciente?->historiaClinica?->numero_historia ?? '—' }}</span></span>
                        <span style="font-size:.8rem;"><strong>Edad:</strong> <span id="pac-edad">{{ $paciente?->edad ?? '—' }}</span> años</span>
                        <span id="pac-alergia-wrap" style="{{ ($paciente?->historiaClinica?->alergias) ? '' : 'display:none;' }}">
                            <span style="background:#fee2e2;color:#991b1b;border-radius:20px;padding:.15rem .6rem;font-size:.72rem;font-weight:700;">
                                <i class="bi bi-exclamation-triangle-fill"></i> Alergias: <span id="pac-alergias">{{ $paciente?->historiaClinica?->alergias ?? '' }}</span>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <label class="form-lbl">Motivo de consulta *</label>
                <textarea name="motivo_consulta" class="form-ctrl {{ $errors->has('motivo_consulta') ? 'is-invalid' : '' }}" rows="2" required placeholder="Describa el motivo principal de la consulta…">{{ old('motivo_consulta') }}</textarea>
                @error('motivo_consulta')<div class="error-msg">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

{{-- ═══ SECCIÓN 2: EXAMEN EXTRAORAL ═══ --}}
<div class="doc-section sec-extraoral">
    <div class="doc-section-header">
        <i class="bi bi-person-bounding-box" style="color:var(--color-principal);"></i> Examen Extraoral
    </div>
    <div class="doc-section-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-lbl">Cara — Simetría y proporciones</label>
                <textarea name="extraoral_cara" class="form-ctrl" rows="2" placeholder="Simetría facial, proporciones tercios…">{{ old('extraoral_cara') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-lbl">ATM — Articulación temporomandibular</label>
                <textarea name="extraoral_atm" class="form-ctrl" rows="2" placeholder="Ruidos, dolor, limitación de apertura…">{{ old('extraoral_atm') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-lbl">Ganglios linfáticos</label>
                <textarea name="extraoral_ganglios" class="form-ctrl" rows="2" placeholder="Ganglios cervicales y submandibulares…">{{ old('extraoral_ganglios') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-lbl">Labios y comisuras</label>
                <textarea name="extraoral_labios" class="form-ctrl" rows="2" placeholder="Labios, comisuras, filtrum…">{{ old('extraoral_labios') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-lbl">Observaciones extraorales</label>
                <textarea name="extraoral_observaciones" class="form-ctrl" rows="2" placeholder="Observaciones adicionales del examen extraoral…">{{ old('extraoral_observaciones') }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- ═══ SECCIÓN 3: EXAMEN INTRAORAL ═══ --}}
<div class="doc-section sec-intraoral">
    <div class="doc-section-header">
        <i class="bi bi-camera" style="color:var(--color-principal);"></i> Examen Intraoral
    </div>
    <div class="doc-section-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-lbl">Encías y periodonto</label>
                <textarea name="intraoral_encias" class="form-ctrl" rows="2" placeholder="Color, textura, sangrado, bolsas periodontales…">{{ old('intraoral_encias') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-lbl">Mucosa oral</label>
                <textarea name="intraoral_mucosa" class="form-ctrl" rows="2" placeholder="Mucosa de carrillos, vestíbulo, lesiones…">{{ old('intraoral_mucosa') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-lbl">Lengua y piso de boca</label>
                <textarea name="intraoral_lengua" class="form-ctrl" rows="2" placeholder="Dorso lingual, frenillo, piso de boca…">{{ old('intraoral_lengua') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-lbl">Paladar duro y blando</label>
                <textarea name="intraoral_paladar" class="form-ctrl" rows="2" placeholder="Paladar, úvula, istmo de las fauces…">{{ old('intraoral_paladar') }}</textarea>
            </div>
            <div class="col-md-4">
                <label class="form-lbl">Higiene oral</label>
                <select name="intraoral_higiene" class="form-ctrl">
                    <option value="">— Sin evaluar —</option>
                    <option value="excelente" {{ old('intraoral_higiene') == 'excelente' ? 'selected' : '' }}>Excelente</option>
                    <option value="buena"     {{ old('intraoral_higiene') == 'buena'     ? 'selected' : '' }}>Buena</option>
                    <option value="regular"   {{ old('intraoral_higiene') == 'regular'   ? 'selected' : '' }}>Regular</option>
                    <option value="mala"      {{ old('intraoral_higiene') == 'mala'      ? 'selected' : '' }}>Mala</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-lbl">Observaciones intraorales</label>
                <textarea name="intraoral_observaciones" class="form-ctrl" rows="2" placeholder="Observaciones adicionales del examen intraoral…">{{ old('intraoral_observaciones') }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- ═══ SECCIÓN 4: DIAGNÓSTICOS ═══ --}}
<div class="doc-section sec-diagnosticos">
    <div class="doc-section-header">
        <i class="bi bi-search" style="color:var(--color-principal);"></i> Diagnósticos (ICDAS / CIE-10)
    </div>
    <div class="doc-section-body">
        <div style="overflow-x:auto;">
        <table class="tabla-dinamica" id="tabla-dx">
            <thead>
                <tr>
                    <th style="width:110px;">ICDAS / CIE-10</th>
                    <th>Diagnóstico</th>
                    <th style="width:80px;">Diente</th>
                    <th style="width:90px;">Cara</th>
                    <th>Observación</th>
                    <th style="width:36px;"></th>
                </tr>
            </thead>
            <tbody id="body-dx"></tbody>
        </table>
        </div>
        <button type="button" class="btn-add-row" onclick="addDiagnostico()">
            <i class="bi bi-plus-circle"></i> Agregar diagnóstico
        </button>
    </div>
</div>

{{-- ═══ SECCIÓN 5: PLAN DE TRATAMIENTO ═══ --}}
<div class="doc-section sec-plan">
    <div class="doc-section-header">
        <i class="bi bi-list-check" style="color:var(--color-principal);"></i> Plan de Tratamiento
    </div>
    <div class="doc-section-body">
        <div style="overflow-x:auto;">
        <table class="tabla-dinamica" id="tabla-plan">
            <thead>
                <tr>
                    <th style="width:30px;">N°</th>
                    <th>Procedimiento</th>
                    <th style="width:70px;">Diente</th>
                    <th style="width:80px;">Cara</th>
                    <th style="width:65px;">Cant.</th>
                    <th style="width:110px;">V. Unitario</th>
                    <th style="width:110px;">Total</th>
                    <th style="width:85px;">Prioridad</th>
                    <th style="width:36px;"></th>
                </tr>
            </thead>
            <tbody id="body-plan"></tbody>
        </table>
        </div>
        <button type="button" class="btn-add-row" onclick="addProcedimiento()">
            <i class="bi bi-plus-circle"></i> Agregar procedimiento
        </button>

        <div style="margin-top:1.25rem;padding:1rem;background:#dcfce7;border:1px solid #86efac;border-radius:10px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
            <div>
                <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:#166534;letter-spacing:.04em;">Total del plan de tratamiento</div>
                <div class="total-plan" id="total-plan">$ 0</div>
            </div>
            <div style="font-size:.78rem;color:#166534;max-width:280px;text-align:right;">
                <i class="bi bi-info-circle me-1"></i>Al completar la valoración podrás generar el presupuesto automáticamente desde este plan.
            </div>
        </div>
    </div>
</div>

{{-- ═══ SECCIÓN 6: DIAGNÓSTICO FINAL ═══ --}}
<div class="doc-section">
    <div class="doc-section-header" style="background:var(--color-muy-claro);">
        <i class="bi bi-clipboard2-check" style="color:var(--color-principal);"></i> Diagnóstico Final y Pronóstico
    </div>
    <div class="doc-section-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-lbl">Pronóstico</label>
                <select name="pronostico" class="form-ctrl">
                    <option value="">— Sin definir —</option>
                    <option value="excelente" {{ old('pronostico') == 'excelente' ? 'selected' : '' }}>Excelente</option>
                    <option value="bueno"     {{ old('pronostico') == 'bueno'     ? 'selected' : '' }}>Bueno</option>
                    <option value="reservado" {{ old('pronostico') == 'reservado' ? 'selected' : '' }}>Reservado</option>
                    <option value="malo"      {{ old('pronostico') == 'malo'      ? 'selected' : '' }}>Malo</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-lbl">Observaciones generales</label>
                <textarea name="observaciones_generales" class="form-ctrl" rows="3" placeholder="Conclusiones, recomendaciones, notas importantes…">{{ old('observaciones_generales') }}</textarea>
            </div>
        </div>
    </div>
</div>

</div>{{-- end doc-card --}}

<div style="display:flex;gap:.75rem;flex-wrap:wrap;">
    <button type="submit" class="btn-morado" onclick="serializarYEnviar('en_proceso')">
        <i class="bi bi-floppy"></i> Guardar Valoración
    </button>
    <button type="button" class="btn-verde" onclick="serializarYEnviar('completada')">
        <i class="bi bi-check-circle"></i> Guardar y Completar
    </button>
    <a href="{{ route('valoraciones.index') }}" class="btn-gris">
        <i class="bi bi-x-circle"></i> Cancelar
    </a>
</div>

</form>

@push('scripts')
<script>
const CIE10 = [
    // ── ICDAS ──────────────────────────────────────────────────
    { codigo: '0',     nombre: 'ICDAS 0 — Superficie sana' },
    { codigo: '1',     nombre: 'ICDAS 1 — Primer cambio visual en esmalte (al secar)' },
    { codigo: '2',     nombre: 'ICDAS 2 — Cambio visual distinto en esmalte (húmedo)' },
    { codigo: '3',     nombre: 'ICDAS 3 — Ruptura localizada del esmalte' },
    { codigo: '4',     nombre: 'ICDAS 4 — Sombra oscura subyacente de dentina' },
    { codigo: '5',     nombre: 'ICDAS 5 — Cavidad con dentina visible' },
    { codigo: '6',     nombre: 'ICDAS 6 — Cavidad extensa con dentina visible' },
    // ── CIE-10 ─────────────────────────────────────────────────
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
    { codigo: 'K06.0', nombre: 'Recesión gingival' },
    { codigo: 'K07.0', nombre: 'Anomalías del maxilar' },
    { codigo: 'K07.3', nombre: 'Anomalías de posición dentaria' },
    { codigo: 'K08.0', nombre: 'Pérdida de dientes por extracción' },
    { codigo: 'K08.1', nombre: 'Pérdida de dientes por accidente' },
    { codigo: 'K08.2', nombre: 'Atrofia del maxilar' },
    { codigo: 'K09.0', nombre: 'Quiste dentígero' },
    { codigo: 'S02.5', nombre: 'Fractura del diente' },
    { codigo: 'Z29.8', nombre: 'Sellante preventivo' },
];

const CARAS = ['Vestibular','Palatino','Mesial','Distal','Oclusal','Incisal','Lingual','Cervical'];
const PROCS = ['Extracción simple','Extracción quirúrgica','Obturación resina','Amalgama','Endodoncia','Corona PFM','Corona zirconia','Puente','Limpieza','Blanqueamiento','Ortodoncia','Implante','Prótesis parcial removible','Prótesis total','Sellante de fisuras','Radiografía periapical','Radiografía panorámica'];
const PRIORIDADES = ['Alta','Media','Baja'];

var dxData   = [];
var planData = [];

// ── DIAGNÓSTICOS ────────────────────────
function addDiagnostico(d) {
    d = d || { codigo:'', nombre:'', diente:'', cara:'', observacion:'' };
    dxData.push(d);
    renderDx();
}

function removeDx(i) {
    dxData.splice(i, 1);
    renderDx();
}

function renderDx() {
    var tbody = document.getElementById('body-dx');
    tbody.innerHTML = '';
    dxData.forEach(function(d, i) {
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td>' +
                '<div class="cie10-wrapper">' +
                    '<input class="inp" value="' + esc(d.codigo) + '" placeholder="0–6 / K02.1" oninput="filtroCIE10(this,' + i + ')" onblur="ocultarCIE10(' + i + ')">' +
                    '<div class="cie10-dropdown" id="dd-' + i + '"></div>' +
                '</div>' +
            '</td>' +
            '<td><input class="inp" value="' + esc(d.nombre) + '" id="dx-nombre-' + i + '" oninput="dxData[' + i + '].nombre=this.value" placeholder="Descripción del diagnóstico"></td>' +
            '<td><input class="inp" value="' + esc(d.diente) + '" oninput="dxData[' + i + '].diente=this.value" placeholder="11"></td>' +
            '<td>' + selectCara('dxData[' + i + '].cara', d.cara) + '</td>' +
            '<td><input class="inp" value="' + esc(d.observacion) + '" oninput="dxData[' + i + '].observacion=this.value" placeholder="Observación…"></td>' +
            '<td><button type="button" class="btn-del-row" onclick="removeDx(' + i + ')"><i class="bi bi-trash3"></i></button></td>';
        tbody.appendChild(tr);
    });
}

function filtroCIE10(inp, i) {
    dxData[i].codigo = inp.value;
    var q = inp.value.toUpperCase();
    if (q.length < 1) { ocultarCIE10(i); return; }
    var filtrados = CIE10.filter(function(c) {
        return c.codigo.toUpperCase().includes(q) || c.nombre.toUpperCase().includes(q);
    }).slice(0, 10);
    var dd = document.getElementById('dd-' + i);
    if (filtrados.length === 0) { dd.style.display = 'none'; return; }
    dd.innerHTML = filtrados.map(function(c) {
        return '<div class="cie10-item" onmousedown="selCIE10(' + i + ',\'' + c.codigo + '\',\'' + esc(c.nombre) + '\')">' +
               '<span class="cie10-code">' + c.codigo + '</span>' + c.nombre + '</div>';
    }).join('');
    // Posicionar con fixed relativo al input (evita clipping por overflow:auto)
    var rect = inp.getBoundingClientRect();
    dd.style.top   = (rect.bottom + 4) + 'px';
    dd.style.left  = rect.left + 'px';
    dd.style.width = Math.max(rect.width, 260) + 'px';
    dd.style.display = 'block';
}

function selCIE10(i, codigo, nombre) {
    dxData[i].codigo = codigo;
    dxData[i].nombre = nombre;
    renderDx();
}

function ocultarCIE10(i) {
    setTimeout(function() {
        var dd = document.getElementById('dd-' + i);
        if (dd) dd.style.display = 'none';
    }, 200);
}

// ── PLAN DE TRATAMIENTO ─────────────────
function addProcedimiento(p) {
    p = p || { procedimiento:'', diente:'', cara:'', cantidad:1, valor_unitario:0, prioridad:'Media', notas:'' };
    planData.push(p);
    renderPlan();
}

function removeProc(i) {
    planData.splice(i, 1);
    renderPlan();
    calcularTotal();
}

function renderPlan() {
    var tbody = document.getElementById('body-plan');
    tbody.innerHTML = '';
    planData.forEach(function(p, i) {
        var tr = document.createElement('tr');
        var total = (parseFloat(p.valor_unitario)||0) * (parseInt(p.cantidad)||1);
        tr.innerHTML =
            '<td style="text-align:center;color:#9ca3af;font-size:.75rem;">' + (i+1) + '</td>' +
            '<td>' +
                '<input class="inp" list="procs-list" value="' + esc(p.procedimiento) + '" oninput="planData[' + i + '].procedimiento=this.value" placeholder="Procedimiento">' +
            '</td>' +
            '<td><input class="inp" value="' + esc(p.diente) + '" oninput="planData[' + i + '].diente=this.value" placeholder="11"></td>' +
            '<td>' + selectCara('planData[' + i + '].cara', p.cara) + '</td>' +
            '<td><input class="inp" type="number" min="1" value="' + (p.cantidad||1) + '" oninput="planData[' + i + '].cantidad=parseInt(this.value)||1;recalcRow(' + i + ')"></td>' +
            '<td><input class="inp" type="number" min="0" value="' + (p.valor_unitario||0) + '" id="vu-' + i + '" oninput="planData[' + i + '].valor_unitario=parseFloat(this.value)||0;recalcRow(' + i + ')"></td>' +
            '<td id="total-row-' + i + '" style="font-weight:700;color:#166534;text-align:right;white-space:nowrap;">$ ' + fmtMoney(total) + '</td>' +
            '<td><select class="inp" onchange="planData[' + i + '].prioridad=this.value">' +
                PRIORIDADES.map(function(pr) { return '<option value="' + pr + '" ' + (p.prioridad===pr?'selected':'') + '>' + pr + '</option>'; }).join('') +
            '</select></td>' +
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
    var total = planData.reduce(function(s, p) {
        return s + ((parseFloat(p.valor_unitario)||0) * (parseInt(p.cantidad)||1));
    }, 0);
    document.getElementById('total-plan').textContent = '$ ' + fmtMoney(total);
}

// ── HELPERS ─────────────────────────────
function selectCara(bindExpr, valor) {
    return '<select class="inp" onchange="' + bindExpr + '=this.value">' +
        '<option value="">—</option>' +
        CARAS.map(function(c) { return '<option value="' + c + '" ' + (valor===c?'selected':'') + '>' + c + '</option>'; }).join('') +
        '</select>';
}

function fmtMoney(n) {
    return Math.round(n).toLocaleString('es-CO');
}

function esc(s) {
    return String(s||'').replace(/"/g, '&quot;');
}

// ── CARGAR DATOS PACIENTE ────────────────
function cargarPaciente(pacienteId) {
    var opt = document.getElementById('sel-paciente').options[document.getElementById('sel-paciente').selectedIndex];
    if (!pacienteId || !opt) { document.getElementById('pac-info').style.display='none'; return; }
    document.getElementById('pac-historia').textContent = opt.dataset.historia || '—';
    document.getElementById('pac-edad').textContent     = opt.dataset.edad || '—';
    var alergias = opt.dataset.alergias || '';
    document.getElementById('pac-alergias').textContent = alergias;
    document.getElementById('pac-alergia-wrap').style.display = alergias ? '' : 'none';
    document.getElementById('pac-info').style.display = '';

    // Cargar citas del paciente
    fetch('/api/pacientes/' + pacienteId + '/citas-pendientes', { headers: { 'Accept': 'application/json' } })
        .then(function(r) { return r.json(); })
        .catch(function() { return []; })
        .then(function(citas) {
            var sel = document.getElementById('sel-cita');
            var current = sel.value;
            sel.innerHTML = '<option value="">— Ninguna —</option>';
            citas.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.fecha + ' — ' + c.procedimiento;
                if (String(c.id) === current) opt.selected = true;
                sel.appendChild(opt);
            });
        });
}

// ── SERIALIZAR Y ENVIAR ──────────────────
function serializarYEnviar(estado) {
    document.getElementById('campo-estado').value = estado;
    document.getElementById('json-diagnosticos').value = JSON.stringify(dxData.filter(function(d){ return d.codigo||d.nombre; }));
    document.getElementById('json-plan').value = JSON.stringify(planData.filter(function(p){ return p.procedimiento; }));
    document.getElementById('form-valoracion').submit();
}

// ── INIT ─────────────────────────────────
// Si hay paciente preseleccionado, disparar carga
@if($paciente)
(function() {
    document.getElementById('pac-info').style.display = '';
    document.getElementById('pac-historia').textContent = '{{ $historia?->numero_historia ?? "—" }}';
    document.getElementById('pac-edad').textContent     = '{{ $paciente->edad }}';
    @if($historia?->alergias)
    document.getElementById('pac-alergias').textContent = '{{ $historia->alergias }}';
    document.getElementById('pac-alergia-wrap').style.display = '';
    @endif
})();
@endif
</script>
<datalist id="procs-list">
    @foreach(['Extracción simple','Extracción quirúrgica','Obturación resina','Amalgama','Endodoncia','Corona PFM','Corona zirconia','Puente','Limpieza','Blanqueamiento','Ortodoncia','Implante','Prótesis parcial removible','Prótesis total','Sellante de fisuras','Radiografía periapical','Radiografía panorámica'] as $proc)
    <option value="{{ $proc }}">
    @endforeach
</datalist>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
new TomSelect('#sel-paciente', {
    placeholder: '— Buscar paciente —',
    searchField: ['text'],
    maxOptions: 100,
    render: {
        option: function(data, escape) {
            const parts = escape(data.text).split(' — ');
            return '<div><span style="font-weight:500;">' + (parts[0]||'') + '</span>'
                 + (parts[1] ? ' <span style="font-size:.78rem;color:#6b7280;">— ' + parts[1] + '</span>' : '')
                 + '</div>';
        }
    },
    onChange: function(value) {
        // Disparar el evento change del select original para que funcione cargarPaciente()
        const select = document.getElementById('sel-paciente');
        cargarPaciente(value);
    }
});
</script>
@endpush

@endsection
