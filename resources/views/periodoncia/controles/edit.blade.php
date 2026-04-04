@extends('layouts.app')
@section('titulo', 'Editar ' . $control->numero_control)

@push('estilos')
<style>
.ctrl-card-header { background:var(--color-principal);color:white;padding:.75rem 1rem;border-radius:10px 10px 0 0;display:flex;align-items:center;gap:.5rem;font-weight:700;font-size:.88rem; }
.ctrl-card-body { padding:1.1rem; }
.form-label-ctrl { font-size:.77rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--texto-secundario);margin-bottom:.3rem;display:block; }
.form-ctrl { width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.42rem .75rem;font-size:.875rem;background:var(--fondo-app);color:var(--texto-principal);transition:border-color .15s; }
.form-ctrl:focus { outline:none;border-color:var(--color-principal); }
.zona-btn { padding:.3rem .7rem;border-radius:20px;border:1px solid var(--fondo-borde);background:var(--fondo-app);font-size:.73rem;font-weight:600;cursor:pointer;color:var(--texto-secundario);transition:all .15s; }
.zona-btn.activo { background:var(--color-principal);color:white;border-color:var(--color-principal); }
.sondaje-input-ctrl { width:32px;border:none;border-radius:4px;text-align:center;font-size:.72rem;font-weight:700;padding:.1rem;outline:none;background:#f9fafb; }
.sondaje-input-ctrl.s1 { background:#dcfce7;color:#166534; }
.sondaje-input-ctrl.s2 { background:#fef9c3;color:#854d0e; }
.sondaje-input-ctrl.s3 { background:#ffedd5;color:#9a3412; }
.sondaje-input-ctrl.s4 { background:#fee2e2;color:#7f1d1d; }
.sondaje-mini { border-collapse:collapse;font-size:.7rem;width:100%; }
.sondaje-mini th,.sondaje-mini td { border:1px solid var(--fondo-borde);padding:.18rem .25rem;text-align:center; }
.sondaje-mini thead th { background:var(--fondo-card-alt);font-weight:700;font-size:.62rem;text-transform:uppercase;color:var(--texto-secundario); }

/* ── Classic overrides ── */
body:not([data-ui="glass"]) .ctrl-card-body { background:#fff; border:1px solid var(--fondo-borde); border-top:none; }
body:not([data-ui="glass"]) .form-ctrl { background:var(--fondo-app); border:1px solid var(--fondo-borde); color:var(--texto-principal); }

/* ── Aurora Glass overrides ── */
body[data-ui="glass"] .ctrl-card-header { background:rgba(0,100,120,0.70) !important; }
body[data-ui="glass"] .ctrl-card-body { background:rgba(255,255,255,0.08) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.20) !important; border-top:none !important; }
body[data-ui="glass"] .form-label-ctrl { color:rgba(0,234,255,0.85) !important; }
body[data-ui="glass"] .form-ctrl { background:rgba(255,255,255,0.08) !important; border:1px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
body[data-ui="glass"] .form-ctrl:focus { border-color:rgba(0,234,255,0.70) !important; }
body[data-ui="glass"] .zona-btn { background:rgba(255,255,255,0.08) !important; border-color:rgba(0,234,255,0.25) !important; color:rgba(255,255,255,0.70) !important; }
body[data-ui="glass"] .zona-btn.activo { background:rgba(0,234,255,0.20) !important; color:rgba(0,234,255,0.95) !important; border-color:rgba(0,234,255,0.50) !important; }
body[data-ui="glass"] .sondaje-input-ctrl { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; }
body[data-ui="glass"] .sondaje-input-ctrl.s1 { background:rgba(22,101,52,0.25) !important; color:#86efac !important; }
body[data-ui="glass"] .sondaje-input-ctrl.s2 { background:rgba(133,77,14,0.25) !important; color:#fde68a !important; }
body[data-ui="glass"] .sondaje-input-ctrl.s3 { background:rgba(154,52,18,0.25) !important; color:#fdba74 !important; }
body[data-ui="glass"] .sondaje-input-ctrl.s4 { background:rgba(127,29,29,0.25) !important; color:#fca5a5 !important; }
body[data-ui="glass"] .sondaje-mini thead th { background:rgba(0,0,0,0.25) !important; color:rgba(0,234,255,0.80) !important; }
body[data-ui="glass"] .sondaje-mini th,
body[data-ui="glass"] .sondaje-mini td { border-color:rgba(0,234,255,0.15) !important; color:rgba(255,255,255,0.80) !important; }
</style>
@endpush

@section('contenido')

<div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1.25rem;font-size:.82rem;flex-wrap:wrap;">
    <a href="{{ route('periodoncia.index') }}" style="color:var(--texto-secundario);text-decoration:none;"><i class="bi bi-heart-pulse me-1"></i>Periodoncia</a>
    <i class="bi bi-chevron-right" style="font-size:.65rem;color:var(--texto-secundario);"></i>
    <a href="{{ route('periodoncia.show', $control->fichaPeriodontal) }}" style="color:var(--texto-secundario);text-decoration:none;">{{ $control->fichaPeriodontal->numero_ficha }}</a>
    <i class="bi bi-chevron-right" style="font-size:.65rem;color:var(--texto-secundario);"></i>
    <a href="{{ route('periodoncia.controles.show', $control) }}" style="color:var(--texto-secundario);text-decoration:none;">{{ $control->numero_control }}</a>
    <i class="bi bi-chevron-right" style="font-size:.65rem;color:var(--texto-secundario);"></i>
    <span style="color:var(--texto-principal);font-weight:600;">Editar</span>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#7f1d1d;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <i class="bi bi-exclamation-triangle me-1"></i>
    @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
</div>
@endif

<form method="POST" action="{{ route('periodoncia.controles.update', $control) }}" id="formCtrlEdit">
@csrf @method('PUT')

@php
    $dSup = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
    $dInf = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];
    $zonasActuales = is_array($control->zonas_tratadas) ? $control->zonas_tratadas : (is_string($control->zonas_tratadas) ? explode(',', $control->zonas_tratadas) : []);
    $scData = $control->sondaje_control ?? [];
@endphp

{{-- Card datos --}}
<div class="card-sistema" style="margin-bottom:1.25rem;">
    <div class="ctrl-card-header">
        <i class="bi bi-calendar-check"></i> Control — Sesión #{{ $control->numero_sesion }} ({{ $control->numero_control }})
    </div>
    <div class="ctrl-card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label-ctrl">Fecha del control</label>
                <input type="date" name="fecha_control" class="form-ctrl"
                       value="{{ $control->fecha_control->format('Y-m-d') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label-ctrl">Doctor</label>
                <select name="user_id" class="form-ctrl">
                    @foreach($doctores as $doc)
                    <option value="{{ $doc->id }}" {{ $control->user_id == $doc->id ? 'selected' : '' }}>{{ $doc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label-ctrl">Tipo de sesión</label>
                <select name="tipo_sesion" class="form-ctrl" required>
                    @foreach(['raspado_alisado'=>'Raspado y alisado radicular','curetaje'=>'Curetaje','cirugia_periodontal'=>'Cirugía periodontal','mantenimiento'=>'Mantenimiento periodontal','reevaluacion'=>'Reevaluación','otro'=>'Otro'] as $v => $l)
                    <option value="{{ $v }}" {{ $control->tipo_sesion == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-ctrl">Anestesia</label>
                <input type="text" name="anestesia_utilizada" class="form-ctrl"
                       value="{{ $control->anestesia_utilizada }}">
            </div>
        </div>
    </div>
</div>

{{-- Zonas tratadas --}}
<div class="card-sistema" style="margin-bottom:1.25rem;">
    <div class="ctrl-card-header"><i class="bi bi-geo-alt"></i> Zonas Tratadas</div>
    <div class="ctrl-card-body">
        <input type="hidden" name="zonas_tratadas" id="zonas_tratadas_hidden"
               value="{{ implode(',', $zonasActuales) }}">
        <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);margin-bottom:.4rem;">Superior</p>
        <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:.75rem;">
            @foreach($dSup as $d)
            <button type="button" class="zona-btn {{ in_array((string)$d, array_map('strval', $zonasActuales)) ? 'activo' : '' }}"
                    data-diente="{{ $d }}" onclick="toggleZona(this)">{{ $d }}</button>
            @endforeach
        </div>
        <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);margin-bottom:.4rem;">Inferior</p>
        <div style="display:flex;flex-wrap:wrap;gap:4px;">
            @foreach($dInf as $d)
            <button type="button" class="zona-btn {{ in_array((string)$d, array_map('strval', $zonasActuales)) ? 'activo' : '' }}"
                    data-diente="{{ $d }}" onclick="toggleZona(this)">{{ $d }}</button>
            @endforeach
        </div>
    </div>
</div>

{{-- Índices --}}
<div class="card-sistema" style="margin-bottom:1.25rem;">
    <div class="ctrl-card-header"><i class="bi bi-bar-chart"></i> Índices de Control</div>
    <div class="ctrl-card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label-ctrl">Índice de Placa %</label>
                <input type="number" name="indice_placa_control" class="form-ctrl"
                       min="0" max="100" step="0.1" value="{{ $control->indice_placa_control }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-ctrl">Índice Gingival</label>
                <input type="number" name="indice_gingival_control" class="form-ctrl"
                       min="0" max="3" step="0.01" value="{{ $control->indice_gingival_control }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-ctrl">Próxima cita (semanas)</label>
                <select name="proxima_cita_semanas" class="form-ctrl">
                    <option value="">Sin definir</option>
                    @foreach([1,2,3,4,6,8,12,24,48] as $s)
                    <option value="{{ $s }}" {{ $control->proxima_cita_semanas == $s ? 'selected' : '' }}>{{ $s }} semana{{ $s > 1 ? 's' : '' }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

{{-- Sondaje --}}
<div class="card-sistema" style="margin-bottom:1.25rem;">
    <div class="ctrl-card-header">
        <i class="bi bi-table"></i> Sondaje de Control
        <label style="margin-left:auto;font-size:.78rem;font-weight:400;cursor:pointer;display:flex;align-items:center;gap:.4rem;">
            <input type="checkbox" id="show-sondaje-ctrl" style="accent-color:white;"
                   {{ count($scData) > 0 ? 'checked' : '' }}
                   onchange="document.getElementById('sondaje-ctrl-wrap').style.display=this.checked?'block':'none'">
            Registrar sondaje
        </label>
    </div>
    <div class="ctrl-card-body" id="sondaje-ctrl-wrap" style="display:{{ count($scData) > 0 ? 'block' : 'none' }};">
        <input type="hidden" name="sondaje_control" id="sondaje_control_json" value="{{ json_encode($scData) }}">
        <div style="font-size:.85rem;margin-bottom:.75rem;">
            Promedio: <strong id="sond-ctrl-prom" style="color:var(--color-principal);">0.0 mm</strong>
        </div>
        <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);margin:.3rem 0;">Superior</p>
        <div style="overflow-x:auto;margin-bottom:.75rem;">
        <table class="sondaje-mini">
            <thead><tr>
                <th style="text-align:left;min-width:45px;">Cara</th>
                @foreach($dSup as $d)<th>{{ $d }}</th>@endforeach
            </tr></thead>
            <tbody>
            @foreach([['MV','mvc'],['V','vc'],['DV','dvc'],['ML','mlc'],['L','lc'],['DL','dlc']] as $fila)
            <tr>
                <td style="font-weight:700;font-size:.62rem;color:var(--texto-secundario);">{{ $fila[0] }}</td>
                @foreach($dSup as $d)
                <td><input type="number" class="sondaje-input-ctrl" min="0" max="20"
                           data-dc="{{ $d }}" data-cc="{{ $fila[1] }}"
                           value="{{ $scData[$d][$fila[1]] ?? '' }}"
                           oninput="colorCtrl(this);calcCtrl()"></td>
                @endforeach
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);margin:.3rem 0;">Inferior</p>
        <div style="overflow-x:auto;">
        <table class="sondaje-mini">
            <thead><tr>
                <th style="text-align:left;min-width:45px;">Cara</th>
                @foreach($dInf as $d)<th>{{ $d }}</th>@endforeach
            </tr></thead>
            <tbody>
            @foreach([['MV','mvc'],['V','vc'],['DV','dvc'],['ML','mlc'],['L','lc'],['DL','dlc']] as $fila)
            <tr>
                <td style="font-weight:700;font-size:.62rem;color:var(--texto-secundario);">{{ $fila[0] }}</td>
                @foreach($dInf as $d)
                <td><input type="number" class="sondaje-input-ctrl" min="0" max="20"
                           data-dc="{{ $d }}" data-cc="{{ $fila[1] }}"
                           value="{{ $scData[$d][$fila[1]] ?? '' }}"
                           oninput="colorCtrl(this);calcCtrl()"></td>
                @endforeach
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>

{{-- Observaciones --}}
<div class="card-sistema" style="margin-bottom:1.25rem;">
    <div class="ctrl-card-header"><i class="bi bi-journal-text"></i> Observaciones e Indicaciones</div>
    <div class="ctrl-card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label-ctrl">Instrumentos / Procedimiento</label>
                <textarea name="instrumentos_utilizados" class="form-ctrl" rows="2">{{ $control->instrumentos_utilizados }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label-ctrl">Observaciones clínicas</label>
                <textarea name="observaciones" class="form-ctrl" rows="2">{{ $control->observaciones }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label-ctrl">Indicaciones para el paciente</label>
                <textarea name="indicaciones_paciente" class="form-ctrl" rows="2">{{ $control->indicaciones_paciente }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- Botones --}}
<div style="display:flex;justify-content:flex-end;gap:.75rem;">
    <a href="{{ route('periodoncia.controles.show', $control) }}"
       style="background:var(--fondo-borde);color:var(--texto-principal);padding:.5rem 1.25rem;border-radius:8px;font-size:.88rem;text-decoration:none;font-weight:600;">
        Cancelar
    </a>
    <button type="submit"
            style="background:var(--color-principal);color:white;border:none;padding:.5rem 1.5rem;border-radius:8px;font-size:.88rem;font-weight:700;cursor:pointer;">
        <i class="bi bi-save me-1"></i> Guardar Cambios
    </button>
</div>

</form>

@endsection

@push('scripts')
<script>
var zonasActivas = {};
document.querySelectorAll('.zona-btn.activo').forEach(function(b) {
    zonasActivas[b.getAttribute('data-diente')] = 1;
});
function toggleZona(btn) {
    var d = btn.getAttribute('data-diente');
    if (btn.classList.contains('activo')) { btn.classList.remove('activo'); delete zonasActivas[d]; }
    else { btn.classList.add('activo'); zonasActivas[d] = 1; }
    document.getElementById('zonas_tratadas_hidden').value = Object.keys(zonasActivas).join(',');
}
function colorCtrl(inp) {
    var v = parseInt(inp.value); inp.className = 'sondaje-input-ctrl';
    if (!isNaN(v) && v > 0) { if (v<=3) inp.classList.add('s1'); else if(v<=5) inp.classList.add('s2'); else if(v<=7) inp.classList.add('s3'); else inp.classList.add('s4'); }
}
function calcCtrl() {
    var datos = {}; var s = 0; var c = 0;
    document.querySelectorAll('.sondaje-input-ctrl').forEach(function(inp) {
        var d = inp.getAttribute('data-dc'); var cara = inp.getAttribute('data-cc');
        if (!datos[d]) datos[d] = {};
        var v = inp.value !== '' ? parseFloat(inp.value) : null;
        datos[d][cara] = v;
        if (v !== null) { s += v; c++; }
    });
    document.getElementById('sond-ctrl-prom').textContent = (c > 0 ? (s/c).toFixed(1) : '0.0') + ' mm';
    document.getElementById('sondaje_control_json').value = JSON.stringify(datos);
}
document.getElementById('formCtrlEdit').addEventListener('submit', function() {
    document.getElementById('zonas_tratadas_hidden').value = Object.keys(zonasActivas).join(',');
    if (document.getElementById('show-sondaje-ctrl').checked) calcCtrl();
});
document.querySelectorAll('.sondaje-input-ctrl').forEach(function(inp) { if (inp.value) colorCtrl(inp); });
</script>
@endpush
