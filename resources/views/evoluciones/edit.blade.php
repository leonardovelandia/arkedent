@extends('layouts.app')
@section('titulo', 'Editar Evolución')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; color:#1f2937; }

    .sec-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; margin-bottom:1.25rem; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .sec-header { background:var(--color-muy-claro); padding:.75rem 1.25rem; border-bottom:1px solid var(--color-muy-claro); display:flex; align-items:center; gap:.5rem; }
    .sec-header h6 { margin:0; font-size:.82rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); }
    .sec-body { padding:1.25rem; }

    .lbl { font-size:.78rem; font-weight:600; color:#374151; margin-bottom:.3rem; display:block; }
    .ctrl { width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:.55rem .85rem; font-size:.875rem; outline:none; transition:border-color .15s,box-shadow .15s; background:#fff; }
    .ctrl:focus { border-color:var(--color-principal); box-shadow:0 0 0 3px var(--sombra-principal); }
    .ctrl[readonly] { background:var(--fondo-card-alt); color:var(--color-hover); font-weight:500; cursor:default; }

    .error-field { border-color:#dc2626 !important; }
    .error-msg { font-size:.75rem; color:#dc2626; margin-top:.25rem; }

    .mat-tabla { width:100%; border-collapse:collapse; font-size:.85rem; }
    .mat-tabla thead th { background:var(--color-muy-claro); color:var(--color-hover); font-weight:700; font-size:.75rem; text-transform:uppercase; padding:.55rem .75rem; border-bottom:1px solid var(--color-muy-claro); }
    .mat-tabla tbody td { padding:.5rem .75rem; border-bottom:1px solid var(--fondo-borde); vertical-align:middle; }
    .mat-tabla tbody tr:last-child td { border-bottom:none; }
    .mat-input { width:100%; border:1px solid #e5e7eb; border-radius:6px; padding:.4rem .65rem; font-size:.84rem; outline:none; background:#fff; }
    .mat-input:focus { border-color:var(--color-principal); }
    .mat-select { width:100%; border:1px solid #e5e7eb; border-radius:6px; padding:.4rem .65rem; font-size:.84rem; outline:none; background:#fff; cursor:pointer; }
    .mat-select:focus { border-color:var(--color-principal); }
    .btn-del-mat { background:none; border:1px solid #fecdd3; border-radius:6px; width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center; color:#dc2626; font-size:.85rem; cursor:pointer; transition:background .12s; }
    .btn-del-mat:hover { background:#fef2f2; }
    .btn-add-mat { background:transparent; color:var(--color-principal); border:1px dashed var(--color-claro); border-radius:7px; padding:.4rem .9rem; font-size:.8rem; font-weight:500; cursor:pointer; display:inline-flex; align-items:center; gap:.35rem; transition:background .12s; margin-top:.65rem; }
    .btn-add-mat:hover { background:var(--color-muy-claro); }
    .stock-badge { font-size:.7rem; padding:.15rem .45rem; border-radius:4px; font-weight:600; margin-top:.2rem; display:inline-block; }
    .stock-ok { background:#dcfce7; color:#166534; }
    .stock-low { background:#fef9c3; color:#854d0e; }
    .stock-crit { background:#fee2e2; color:#dc2626; }

    .pac-info-box { background:var(--fondo-card-alt); border:1px solid var(--color-muy-claro); border-radius:10px; padding:.85rem 1rem; display:flex; align-items:center; gap:.75rem; }
    .pac-avatar-sm { width:42px; height:42px; border-radius:50%; background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; font-size:.9rem; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
</style>
@endpush

@section('contenido')

<div class="page-header d-flex align-items-center gap-3">
    <a href="{{ route('evoluciones.show', $evolucion) }}" style="color:var(--color-principal);font-size:1.2rem;text-decoration:none;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="page-titulo">Editar Evolución</h1>
        <p class="page-subtitulo">{{ $evolucion->paciente->nombre_completo }} · {{ $evolucion->fecha_formateada }}</p>
    </div>
</div>

@if($errors->any())
    <div class="alerta-flash" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;">
        <i class="bi bi-exclamation-circle-fill"></i> Revisa los campos marcados en rojo.
    </div>
@endif

<form method="POST" action="{{ route('evoluciones.update', $evolucion) }}" id="form-evolucion">
@csrf
@method('PUT')

<input type="hidden" name="paciente_id" value="{{ $evolucion->paciente_id }}">
<input type="hidden" name="historia_clinica_id" value="{{ $evolucion->historia_clinica_id }}">
<input type="hidden" name="materiales_json" id="materiales-json">

{{-- S1: Paciente --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-person-badge" style="color:var(--color-principal);"></i><h6>Paciente</h6></div>
    <div class="sec-body">
        <div class="pac-info-box mb-3">
            <div class="pac-avatar-sm">
                {{ strtoupper(substr($evolucion->paciente->nombre,0,1)) }}{{ strtoupper(substr($evolucion->paciente->apellido,0,1)) }}
            </div>
            <div>
                <div style="font-weight:600;color:#1c2b22;font-size:.95rem;">{{ $evolucion->paciente->nombre_completo }}</div>
                <div style="font-size:.78rem;color:#6b7280;">
                    {{ $evolucion->paciente->tipo_documento }} {{ $evolucion->paciente->numero_documento }} · Historia: {{ $evolucion->paciente->numero_historia }}
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="lbl">Fecha <span style="color:#dc2626;">*</span></label>
                <input type="date" name="fecha" class="ctrl @error('fecha') error-field @enderror"
                       value="{{ old('fecha', $evolucion->fecha->format('Y-m-d')) }}" required>
                @error('fecha')<div class="error-msg">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="lbl">Hora</label>
                <div class="timepicker-wrap">
                    <i class="bi bi-clock timepicker-icon"></i>
                    <input type="text" name="hora" placeholder="HH:MM"
                           class="ctrl timepicker" value="{{ old('hora', $evolucion->hora ? \Carbon\Carbon::parse($evolucion->hora)->format('H:i') : '') }}" autocomplete="off" readonly>
                </div>
            </div>
        </div>

        {{-- Horas de atención (Res. 1995/1999) --}}
        <div class="row g-2 mt-2">
            <div class="col-6">
                <label class="lbl" style="color:var(--color-principal);">
                    Hora inicio atención <span style="color:#dc3545;">*</span>
                </label>
                <div class="timepicker-wrap">
                    <i class="bi bi-clock timepicker-icon"></i>
                    <input type="text" name="hora_inicio" placeholder="HH:MM"
                           value="{{ old('hora_inicio', $evolucion->hora_inicio ? \Carbon\Carbon::parse($evolucion->hora_inicio)->format('H:i') : '') }}"
                           class="ctrl timepicker" autocomplete="off" readonly required>
                </div>
            </div>
            <div class="col-6">
                <label class="lbl" style="color:var(--color-principal);">Hora fin atención</label>
                <div class="timepicker-wrap">
                    <i class="bi bi-clock timepicker-icon"></i>
                    <input type="text" name="hora_fin" placeholder="HH:MM"
                           value="{{ old('hora_fin', $evolucion->hora_fin ? \Carbon\Carbon::parse($evolucion->hora_fin)->format('H:i') : '') }}"
                           id="hora_fin_evo_edit"
                           class="ctrl timepicker" autocomplete="off" readonly>
                </div>
            </div>
        </div>
        <div id="duracion-display-evo-edit" style="font-size:.75rem;color:var(--color-principal);margin-top:.25rem;display:none;">
            <i class="bi bi-clock me-1"></i>Duración: <strong id="duracion-texto-evo-edit"></strong>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var hi = document.querySelector('[name="hora_inicio"]');
            var hf = document.getElementById('hora_fin_evo_edit');
            var disp = document.getElementById('duracion-display-evo-edit');
            var txt  = document.getElementById('duracion-texto-evo-edit');
            function calcDur() {
                if (!hi?.value || !hf?.value) { disp.style.display='none'; return; }
                var [h1,m1]=hi.value.split(':').map(Number), [h2,m2]=hf.value.split(':').map(Number);
                var mins=(h2*60+m2)-(h1*60+m1);
                if (mins<=0) { disp.style.display='none'; return; }
                var h=Math.floor(mins/60), m=mins%60;
                txt.textContent = h>0 ? h+'h '+(m>0?m+'min':'') : m+' min';
                disp.style.display='block';
            }
            hi?.addEventListener('change', calcDur);
            hf?.addEventListener('change', calcDur);
            calcDur(); // show on load if values present
        });
        </script>
    </div>
</div>

{{-- S2: Procedimiento --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-tooth" style="color:var(--color-principal);"></i><h6>Procedimiento</h6></div>
    <div class="sec-body">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="lbl">Procedimiento realizado <span style="color:#dc2626;">*</span></label>
                <input type="text" name="procedimiento" class="ctrl @error('procedimiento') error-field @enderror"
                       value="{{ old('procedimiento', $evolucion->procedimiento) }}" required>
                @error('procedimiento')<div class="error-msg">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="lbl">Dientes tratados</label>
                <input type="text" name="dientes_tratados" class="ctrl"
                       placeholder="Ej: 46, 47"
                       value="{{ old('dientes_tratados', $evolucion->dientes_tratados) }}">
                <span style="font-size:.7rem;color:#9ca3af;">Números FDI separados por coma</span>
            </div>
        </div>
    </div>
</div>

{{-- S3: Descripción --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-file-text" style="color:var(--color-principal);"></i><h6>Descripción Clínica</h6></div>
    <div class="sec-body">
        <label class="lbl">Descripción detallada del procedimiento <span style="color:#dc2626;">*</span></label>
        <textarea name="descripcion" rows="5" class="ctrl @error('descripcion') error-field @enderror"
                  required>{{ old('descripcion', $evolucion->descripcion) }}</textarea>
        @error('descripcion')<div class="error-msg">{{ $message }}</div>@enderror
    </div>
</div>

{{-- S4: Signos vitales --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-activity" style="color:var(--color-principal);"></i><h6>Signos Vitales</h6></div>
    <div class="sec-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="lbl">Presión Arterial</label>
                <input type="text" name="presion_arterial" class="ctrl"
                       placeholder="Ej: 120/80 mmHg"
                       value="{{ old('presion_arterial', $evolucion->presion_arterial) }}">
            </div>
            <div class="col-md-4">
                <label class="lbl">Frecuencia Cardíaca</label>
                <input type="text" name="frecuencia_cardiaca" class="ctrl"
                       placeholder="Ej: 72 lpm"
                       value="{{ old('frecuencia_cardiaca', $evolucion->frecuencia_cardiaca) }}">
            </div>
        </div>
    </div>
</div>

{{-- S5: Materiales --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-box-seam" style="color:var(--color-principal);"></i><h6>Materiales Utilizados</h6></div>
    <div class="sec-body">
        <table class="mat-tabla" id="tabla-materiales">
            <thead>
                <tr>
                    <th>Material del inventario</th>
                    <th style="width:180px;">Cantidad</th>
                    <th style="width:48px;"></th>
                </tr>
            </thead>
            <tbody id="cuerpo-materiales"></tbody>
        </table>
        <button type="button" class="btn-add-mat" onclick="agregarMaterial()">
            <i class="bi bi-plus-circle"></i> Agregar material
        </button>
        <p id="mat-vacia" style="font-size:.8rem;color:#9ca3af;margin:.5rem 0 0;display:none;">Sin materiales registrados aún.</p>
    </div>
</div>

{{-- S6: Próxima cita --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-calendar-plus" style="color:var(--color-principal);"></i><h6>Próxima Cita Sugerida</h6></div>
    <div class="sec-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="lbl">Fecha sugerida</label>
                <input type="date" name="proxima_cita_fecha" class="ctrl"
                       value="{{ old('proxima_cita_fecha', $evolucion->proxima_cita_fecha?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-8">
                <label class="lbl">Procedimiento sugerido</label>
                <input type="text" name="proxima_cita_procedimiento" class="ctrl"
                       value="{{ old('proxima_cita_procedimiento', $evolucion->proxima_cita_procedimiento) }}">
            </div>
        </div>
    </div>
</div>

{{-- S7: Observaciones --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-chat-text" style="color:var(--color-principal);"></i><h6>Observaciones</h6></div>
    <div class="sec-body">
        <textarea name="observaciones" rows="3" class="ctrl">{{ old('observaciones', $evolucion->observaciones) }}</textarea>
    </div>
</div>

<div style="display:flex;gap:.75rem;justify-content:flex-end;margin-top:.5rem;">
    <a href="{{ route('evoluciones.show', $evolucion) }}" class="btn-gris">
        <i class="bi bi-x"></i> Cancelar
    </a>
    <button type="submit" class="btn-morado">
        <i class="bi bi-floppy"></i> Actualizar Evolución
    </button>
</div>

</form>

<script>
var catalogoMateriales = @json($materialesInventario);

// Inicializar matData desde la evolución existente, enriqueciendo con datos del catálogo
var matDataRaw = @json($evolucion->materiales ?? []);
var matData = matDataRaw.map(function(m) {
    var inv = catalogoMateriales.find(function(c){ return c.nombre === m.nombre; });
    return {
        nombre:      m.nombre || '',
        cantidad:    m.cantidad || '',
        stock:       inv ? inv.stock_actual : null,
        unidad:      inv ? inv.unidad_medida : '',
        material_id: inv ? inv.id : null,
    };
});

function escHtml(str) {
    return String(str||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function buildOpcionesMaterial(selectedNombre) {
    var opts = '<option value="">— Seleccionar material —</option>';
    catalogoMateriales.forEach(function(m) {
        var stockLabel = parseFloat(m.stock_actual) <= 0
            ? ' ⚠ Sin stock'
            : ' [Stock: ' + parseFloat(m.stock_actual).toFixed(2) + ' ' + m.unidad_medida + ']';
        var sel = (selectedNombre === m.nombre) ? ' selected' : '';
        opts += '<option value="' + escHtml(m.nombre) + '" data-id="' + m.id + '" data-stock="' + m.stock_actual + '" data-unidad="' + escHtml(m.unidad_medida) + '"' + sel + '>' + escHtml(m.nombre) + stockLabel + '</option>';
    });
    return opts;
}

function getStockBadge(stock, unidad) {
    var s = parseFloat(stock);
    if (s <= 0) return '<span class="stock-badge stock-crit"><i class="bi bi-exclamation-triangle"></i> Sin stock</span>';
    if (s <= 5) return '<span class="stock-badge stock-low">Stock: ' + s.toFixed(2) + ' ' + unidad + '</span>';
    return '<span class="stock-badge stock-ok">Stock: ' + s.toFixed(2) + ' ' + unidad + '</span>';
}

function renderMateriales() {
    var tbody = document.getElementById('cuerpo-materiales');
    var vacia = document.getElementById('mat-vacia');
    tbody.innerHTML = '';
    if (!matData || matData.length === 0) { vacia.style.display = 'block'; return; }
    vacia.style.display = 'none';
    matData.forEach(function(m, i) {
        var tr = document.createElement('tr');
        var stockBadge = (m.nombre && m.stock !== null) ? getStockBadge(m.stock, m.unidad) : '';
        tr.innerHTML =
            '<td>' +
                '<select class="mat-select" onchange="onSelectMaterial(this,' + i + ')">' +
                    buildOpcionesMaterial(m.nombre) +
                '</select>' +
                '<div id="badge-stock-' + i + '">' + stockBadge + '</div>' +
            '</td>' +
            '<td>' +
                '<div style="display:flex;align-items:center;gap:.3rem;">' +
                '<input type="number" class="mat-input" style="width:90px;" placeholder="0" min="0.01" step="0.01" value="' + escHtml(m.cantidad) + '" oninput="matData[' + i + '].cantidad=this.value">' +
                '<span id="unidad-' + i + '" style="font-size:.78rem;color:#6b7280;white-space:nowrap;">' + escHtml(m.unidad||'') + '</span>' +
                '</div>' +
            '</td>' +
            '<td><button type="button" class="btn-del-mat" onclick="eliminarMaterial(' + i + ')"><i class="bi bi-trash3"></i></button></td>';
        tbody.appendChild(tr);
    });
}

function onSelectMaterial(sel, i) {
    var opt = sel.options[sel.selectedIndex];
    matData[i].nombre      = opt.value;
    matData[i].stock       = opt.dataset.stock || 0;
    matData[i].unidad      = opt.dataset.unidad || '';
    matData[i].material_id = opt.dataset.id || null;
    var badge = document.getElementById('badge-stock-' + i);
    if (badge) badge.innerHTML = opt.value ? getStockBadge(opt.dataset.stock, opt.dataset.unidad) : '';
    var unidadEl = document.getElementById('unidad-' + i);
    if (unidadEl) unidadEl.textContent = opt.dataset.unidad || '';
}

function agregarMaterial() {
    matData.push({ nombre: '', cantidad: '', stock: null, unidad: '', material_id: null });
    renderMateriales();
}

function eliminarMaterial(i) {
    matData.splice(i, 1);
    renderMateriales();
}

document.getElementById('form-evolucion').addEventListener('submit', function() {
    var limpio = matData.filter(function(m){ return m.nombre && m.nombre.trim() !== ''; });
    document.getElementById('materiales-json').value = JSON.stringify(limpio);
});

['procedimiento','proxima_cita_procedimiento'].forEach(function(n) {
    var el = document.querySelector('[name="'+n+'"]');
    if (!el) return;
    el.addEventListener('input', function() {
        var pos = this.selectionStart;
        this.value = this.value.toLowerCase().replace(/\b\w/g, function(l){ return l.toUpperCase(); });
        this.setSelectionRange(pos, pos);
    });
});

renderMateriales();
</script>
@endsection
