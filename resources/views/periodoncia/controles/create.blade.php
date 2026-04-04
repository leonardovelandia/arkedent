@extends('layouts.app')
@section('titulo', 'Nuevo Control Periodontal')

@push('estilos')
<style>
.ctrl-card-header {
    background: var(--color-principal);
    color: white;
    padding: .75rem 1rem;
    border-radius: 10px 10px 0 0;
    display: flex;
    align-items: center;
    gap: .5rem;
    font-weight: 700;
    font-size: .88rem;
}
.ctrl-card-body { padding: 1.1rem; }
.form-label-ctrl {
    font-size: .77rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--texto-secundario);
    margin-bottom: .3rem;
    display: block;
}
.form-ctrl { width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.42rem .75rem;font-size:.875rem;background:var(--fondo-app);color:var(--texto-principal);transition:border-color .15s; }
.form-ctrl:focus { outline:none;border-color:var(--color-principal); }
.zona-btn {
    padding: .3rem .7rem;
    border-radius: 20px;
    border: 1px solid var(--fondo-borde);
    background: var(--fondo-app);
    font-size: .73rem;
    font-weight: 600;
    cursor: pointer;
    color: var(--texto-secundario);
    transition: all .15s;
}
.zona-btn.activo { background: var(--color-principal); color: white; border-color: var(--color-principal); }
.sondaje-input-ctrl {
    width: 32px;
    border: none;
    border-radius: 4px;
    text-align: center;
    font-size: .72rem;
    font-weight: 700;
    padding: .1rem;
    outline: none;
    background: #f9fafb;
}
.sondaje-input-ctrl.s1 { background: #dcfce7; color: #166534; }
.sondaje-input-ctrl.s2 { background: #fef9c3; color: #854d0e; }
.sondaje-input-ctrl.s3 { background: #ffedd5; color: #9a3412; }
.sondaje-input-ctrl.s4 { background: #fee2e2; color: #7f1d1d; }
.sondaje-mini { border-collapse: collapse; font-size: .7rem; width: 100%; }
.sondaje-mini th, .sondaje-mini td { border: 1px solid var(--fondo-borde); padding: .18rem .25rem; text-align: center; }
.sondaje-mini thead th { background: var(--fondo-card-alt); font-weight: 700; font-size: .62rem; text-transform: uppercase; color: var(--texto-secundario); }

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

{{-- Breadcrumb --}}
<div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1.25rem;font-size:.82rem;flex-wrap:wrap;">
    <a href="{{ route('periodoncia.index') }}" style="color:var(--texto-secundario);text-decoration:none;"><i class="bi bi-heart-pulse me-1"></i>Periodoncia</a>
    <i class="bi bi-chevron-right" style="font-size:.65rem;color:var(--texto-secundario);"></i>
    <a href="{{ route('periodoncia.show', $ficha) }}" style="color:var(--texto-secundario);text-decoration:none;">{{ $ficha->numero_ficha }}</a>
    <i class="bi bi-chevron-right" style="font-size:.65rem;color:var(--texto-secundario);"></i>
    <span style="color:var(--texto-principal);font-weight:600;">Nuevo Control</span>
</div>

{{-- Info ficha --}}
<div style="background:var(--color-muy-claro);border:1px solid var(--color-claro);border-radius:10px;padding:.85rem 1.25rem;margin-bottom:1.25rem;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;">
        <div>
            <span style="font-size:.7rem;font-weight:700;color:var(--color-principal);text-transform:uppercase;letter-spacing:.06em;">
                <i class="bi bi-heart-pulse me-1"></i> Ficha Periodontal
            </span>
            <div style="font-size:.9rem;color:var(--texto-principal);margin-top:.2rem;font-weight:600;">
                {{ $ficha->numero_ficha }} — {{ $ficha->paciente->nombre_completo }}
            </div>
            <div style="font-size:.75rem;color:var(--texto-secundario);margin-top:.1rem;">
                {{ $ficha->clasificacion_label }}
                &nbsp;·&nbsp; {{ $ficha->controles->count() }} controles previos
                &nbsp;·&nbsp; Sesión a registrar: <strong>#{{ $siguienteSesion }}</strong>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#7f1d1d;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <i class="bi bi-exclamation-triangle me-1"></i>
    @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
</div>
@endif

<form method="POST" action="{{ route('periodoncia.controles.store') }}" id="formCtrl">
@csrf
<input type="hidden" name="ficha_periodontal_id" value="{{ $ficha->id }}">
<input type="hidden" name="paciente_id" value="{{ $ficha->paciente_id }}">
<input type="hidden" name="numero_sesion" value="{{ $siguienteSesion }}">

{{-- CARD 1: Datos del control --}}
<div class="card-sistema" style="margin-bottom:1.25rem;">
    <div class="ctrl-card-header">
        <i class="bi bi-calendar-check"></i> Sesión #{{ $siguienteSesion }}
    </div>
    <div class="ctrl-card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label-ctrl">Fecha del control <span style="color:#dc2626;">*</span></label>
                <input type="date" name="fecha_control" class="form-ctrl"
                       value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label-ctrl">Doctor <span style="color:#dc2626;">*</span></label>
                <select name="user_id" class="form-ctrl" required>
                    @foreach($doctores as $doc)
                    <option value="{{ $doc->id }}" {{ auth()->id() == $doc->id ? 'selected' : '' }}>
                        {{ $doc->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label-ctrl">Tipo de sesión <span style="color:#dc2626;">*</span></label>
                <select name="tipo_sesion" class="form-ctrl" required>
                    <option value="">Seleccionar...</option>
                    <option value="raspado_alisado">Raspado y alisado radicular</option>
                    <option value="curetaje">Curetaje</option>
                    <option value="cirugia_periodontal">Cirugía periodontal</option>
                    <option value="mantenimiento">Mantenimiento periodontal</option>
                    <option value="reevaluacion">Reevaluación</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-ctrl">Anestesia</label>
                <input type="text" name="anestesia_utilizada" class="form-ctrl"
                       placeholder="Ej: Lidocaína" value="{{ old('anestesia_utilizada') }}">
            </div>
        </div>
    </div>
</div>

{{-- CARD 2: Zonas tratadas --}}
<div class="card-sistema" style="margin-bottom:1.25rem;">
    <div class="ctrl-card-header">
        <i class="bi bi-geo-alt"></i> Zonas Tratadas
        <span style="margin-left:auto;font-size:.78rem;font-weight:400;opacity:.85;">Seleccione los dientes tratados en esta sesión</span>
    </div>
    <div class="ctrl-card-body">
        <input type="hidden" name="zonas_tratadas" id="zonas_tratadas_hidden">
        @php
            $dSup = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
            $dInf = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];
        @endphp
        <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);margin-bottom:.4rem;">Superior</p>
        <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:.75rem;">
            @foreach($dSup as $d)
            <button type="button" class="zona-btn" data-diente="{{ $d }}"
                    onclick="toggleZona(this)">{{ $d }}</button>
            @endforeach
        </div>
        <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);margin-bottom:.4rem;">Inferior</p>
        <div style="display:flex;flex-wrap:wrap;gap:4px;">
            @foreach($dInf as $d)
            <button type="button" class="zona-btn" data-diente="{{ $d }}"
                    onclick="toggleZona(this)">{{ $d }}</button>
            @endforeach
        </div>
        <div style="margin-top:.5rem;">
            <button type="button" onclick="selAll()" style="font-size:.72rem;color:var(--color-principal);background:none;border:none;cursor:pointer;text-decoration:underline;">Todos</button>
            &nbsp;&bull;&nbsp;
            <button type="button" onclick="desAll()" style="font-size:.72rem;color:var(--texto-secundario);background:none;border:none;cursor:pointer;text-decoration:underline;">Ninguno</button>
        </div>
    </div>
</div>

{{-- CARD 3: Índices de control --}}
<div class="card-sistema" style="margin-bottom:1.25rem;">
    <div class="ctrl-card-header">
        <i class="bi bi-bar-chart"></i> Índices de Control
    </div>
    <div class="ctrl-card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label-ctrl">Índice de Placa %</label>
                <input type="number" name="indice_placa_control" class="form-ctrl"
                       min="0" max="100" step="0.1"
                       placeholder="0 - 100" value="{{ old('indice_placa_control') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-ctrl">Índice Gingival</label>
                <input type="number" name="indice_gingival_control" class="form-ctrl"
                       min="0" max="3" step="0.01"
                       placeholder="0.00 - 3.00" value="{{ old('indice_gingival_control') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-ctrl">Próxima cita (semanas)</label>
                <select name="proxima_cita_semanas" class="form-ctrl">
                    <option value="">Sin definir</option>
                    @foreach([1,2,3,4,6,8,12,24,48] as $s)
                    <option value="{{ $s }}">{{ $s }} semana{{ $s > 1 ? 's' : '' }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

{{-- CARD 4: Sondaje de control (opcional) --}}
<div class="card-sistema" style="margin-bottom:1.25rem;">
    <div class="ctrl-card-header">
        <i class="bi bi-table"></i> Sondaje de Control (Opcional)
        <label style="margin-left:auto;font-size:.78rem;font-weight:400;cursor:pointer;display:flex;align-items:center;gap:.4rem;">
            <input type="checkbox" id="show-sondaje-ctrl" style="accent-color:white;"
                   onchange="document.getElementById('sondaje-ctrl-wrap').style.display=this.checked?'block':'none'">
            Registrar sondaje
        </label>
    </div>
    <div class="ctrl-card-body" id="sondaje-ctrl-wrap" style="display:none;">
        <input type="hidden" name="sondaje_control" id="sondaje_control_json">
        <div style="font-size:.85rem;margin-bottom:.75rem;">
            Profundidad promedio: <strong id="sond-ctrl-prom" style="color:var(--color-principal);">0.0 mm</strong>
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
                           oninput="colorCtrl(this);calcCtrl()"></td>
                @endforeach
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>

{{-- CARD 5: Observaciones --}}
<div class="card-sistema" style="margin-bottom:1.25rem;">
    <div class="ctrl-card-header">
        <i class="bi bi-journal-text"></i> Observaciones e Indicaciones
    </div>
    <div class="ctrl-card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label-ctrl">Instrumentos / Procedimiento</label>
                <textarea name="instrumentos_utilizados" class="form-ctrl" rows="2"
                          placeholder="Instrumentos y materiales utilizados...">{{ old('instrumentos_utilizados') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label-ctrl">Observaciones clínicas</label>
                <textarea name="observaciones" class="form-ctrl" rows="2"
                          placeholder="Evolución, hallazgos clínicos...">{{ old('observaciones') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label-ctrl">Indicaciones para el paciente</label>
                <textarea name="indicaciones_paciente" class="form-ctrl" rows="2"
                          placeholder="Instrucciones de higiene, cuidados post-tratamiento...">{{ old('indicaciones_paciente') }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- Botones --}}
<div style="display:flex;justify-content:flex-end;gap:.75rem;margin-top:.5rem;">
    <a href="{{ route('periodoncia.show', $ficha) }}"
       style="background:var(--fondo-borde);color:var(--texto-principal);padding:.5rem 1.25rem;border-radius:8px;font-size:.88rem;text-decoration:none;font-weight:600;">
        Cancelar
    </a>
    <button type="submit"
            style="background:var(--color-principal);color:white;border:none;padding:.5rem 1.5rem;border-radius:8px;font-size:.88rem;font-weight:700;cursor:pointer;box-shadow:0 2px 8px var(--sombra-principal);">
        <i class="bi bi-save me-1"></i> Registrar Control
    </button>
</div>

</form>

@endsection

@push('scripts')
<script>
var zonasActivas = {};
function toggleZona(btn) {
    var d = btn.getAttribute('data-diente');
    if (btn.classList.contains('activo')) {
        btn.classList.remove('activo');
        delete zonasActivas[d];
    } else {
        btn.classList.add('activo');
        zonasActivas[d] = 1;
    }
    actualizarZonas();
}
function selAll() {
    document.querySelectorAll('.zona-btn').forEach(function(b) {
        b.classList.add('activo');
        zonasActivas[b.getAttribute('data-diente')] = 1;
    });
    actualizarZonas();
}
function desAll() {
    document.querySelectorAll('.zona-btn').forEach(function(b) {
        b.classList.remove('activo');
    });
    zonasActivas = {};
    actualizarZonas();
}
function actualizarZonas() {
    document.getElementById('zonas_tratadas_hidden').value = Object.keys(zonasActivas).join(',');
}

// Sondaje control
function colorCtrl(inp) {
    var v = parseInt(inp.value);
    inp.className = 'sondaje-input-ctrl';
    if (!isNaN(v) && v > 0) {
        if (v <= 3) inp.classList.add('s1');
        else if (v <= 5) inp.classList.add('s2');
        else if (v <= 7) inp.classList.add('s3');
        else inp.classList.add('s4');
    }
}
function calcCtrl() {
    var datos = {};
    var s = 0; var c = 0;
    document.querySelectorAll('.sondaje-input-ctrl').forEach(function(inp) {
        var d = inp.getAttribute('data-dc');
        var cara = inp.getAttribute('data-cc');
        if (!datos[d]) datos[d] = {};
        var v = inp.value !== '' ? parseFloat(inp.value) : null;
        datos[d][cara] = v;
        if (v !== null) { s += v; c++; }
    });
    var prom = c > 0 ? (s/c).toFixed(1) : '0.0';
    document.getElementById('sond-ctrl-prom').textContent = prom + ' mm';
    document.getElementById('sondaje_control_json').value = JSON.stringify(datos);
}

document.getElementById('formCtrl').addEventListener('submit', function() {
    actualizarZonas();
    if (document.getElementById('show-sondaje-ctrl').checked) calcCtrl();
});
</script>
@endpush
