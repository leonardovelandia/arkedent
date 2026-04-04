@extends('layouts.app')
@section('titulo', 'Nueva Orden de Laboratorio')

@push('estilos')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
.ts-wrapper.single .ts-control { height:42px; border:1px solid #e5e7eb; border-radius:8px; font-size:.9rem; color:#1c2b22; padding:0 12px; cursor:pointer; box-shadow:none; }
.ts-wrapper.single.focus .ts-control, .ts-wrapper.single.input-active .ts-control { border-color:var(--color-principal)!important; box-shadow:0 0 0 3px rgba(107,33,168,.08)!important; }
.ts-dropdown { border:1.5px solid var(--color-principal); border-radius:8px; box-shadow:0 8px 24px rgba(107,33,168,.12); font-size:.88rem; z-index:9999; }
.ts-dropdown .option.selected, .ts-dropdown .active { background:var(--color-muy-claro); color:var(--color-principal); }
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    .form-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1rem; }
    .form-card-header { background:linear-gradient(135deg,var(--color-principal),var(--color-sidebar-2)); padding:1rem 1.5rem; }
    .form-card-header h3 { color:#fff; font-size:.95rem; font-weight:600; margin:0; display:flex; align-items:center; gap:.5rem; }
    .form-body { padding:1.25rem 1.5rem; }
    .form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .form-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; }
    .form-group { display:flex; flex-direction:column; gap:.35rem; }
    .form-group label { font-size:.78rem; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.04em; }
    .form-group label span { color:#dc2626; }
    .form-control { border:1px solid #e5e7eb; border-radius:8px; padding:.5rem .875rem; font-size:.9rem; outline:none; transition:border-color .15s; width:100%; font-family:inherit; }
    .form-control:focus { border-color:var(--color-principal); }
    .form-control.is-invalid { border-color:#dc2626; }
    .error-msg { font-size:.78rem; color:#dc2626; }

    .lab-info-box { background:var(--color-muy-claro); border:1px solid var(--color-acento-activo); border-radius:8px; padding:.65rem 1rem; font-size:.82rem; color:var(--color-principal); display:none; margin-top:.35rem; }

    @media(max-width:700px) { .form-grid-2, .form-grid-3 { grid-template-columns:1fr; } }

    /* Clásico */
    body:not([data-ui="glass"]) .form-card { background:#fff; border:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .form-group label { color:#374151; }
    body:not([data-ui="glass"]) .form-control { border:1px solid #e5e7eb; background:#fff; }
    body:not([data-ui="glass"]) .ts-wrapper.single .ts-control { border:1px solid #e5e7eb; color:#1c2b22; background:#fff; }

    /* Glass */
    body[data-ui="glass"] .form-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .form-group label { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .form-control { background:rgba(255,255,255,0.08) !important; border:1px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .form-control:focus { border-color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .form-control::placeholder { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .ts-wrapper.single .ts-control { background:rgba(255,255,255,0.08) !important; border:1px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .ts-dropdown { background:rgba(5,40,55,0.95) !important; border:1px solid rgba(0,234,255,0.35) !important; }
    body[data-ui="glass"] .ts-dropdown .option { color:rgba(255,255,255,0.88) !important; }
    body[data-ui="glass"] .ts-dropdown .option.selected, body[data-ui="glass"] .ts-dropdown .active { background:rgba(0,234,255,0.10) !important; color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .lab-info-box { background:rgba(0,234,255,0.08) !important; border:1px solid rgba(0,234,255,0.30) !important; color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .page-title-main { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .page-title-sub  { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .btn-volver { background:transparent !important; border:1px solid rgba(0,234,255,0.50) !important; color:rgba(0,234,255,0.90) !important; }
</style>
@endpush

@section('contenido')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
    <div>
        <h1 style="font-family:var(--fuente-titulos); font-size:1.3rem; color:#1c2b22; margin:0;">Nueva Orden de Laboratorio</h1>
        <p style="font-size:.83rem; color:#8fa39a; margin:.2rem 0 0;">Complete los datos del trabajo a enviar al laboratorio</p>
    </div>
    <a href="{{ route('laboratorio.index') }}"
       style="display:inline-flex;align-items:center;gap:.3rem;font-size:.83rem;color:var(--color-principal);text-decoration:none;border:1px solid var(--color-principal);border-radius:8px;padding:.4rem .9rem;">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:.875rem 1.25rem;margin-bottom:1rem;">
    <ul style="margin:0;padding-left:1.2rem;font-size:.85rem;color:#dc2626;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('laboratorio.store') }}" id="form-orden">
@csrf

{{-- Sección 1 — Datos generales --}}
<div class="form-card">
    <div class="form-card-header">
        <h3><i class="bi bi-person-circle"></i> Datos Generales</h3>
    </div>
    <div class="form-body">
        <div class="form-grid-2">
            <div class="form-group">
                <label>Paciente <span>*</span></label>
                <select name="paciente_id" id="sel-paciente" class="form-control {{ $errors->has('paciente_id') ? 'is-invalid' : '' }}" required>
                    <option value="">Seleccionar paciente...</option>
                    @foreach($pacientes as $pac)
                        <option value="{{ $pac->id }}" {{ (old('paciente_id', $pacienteSeleccionado?->id) == $pac->id) ? 'selected' : '' }}>
                            {{ $pac->nombre_completo }}
                        </option>
                    @endforeach
                </select>
                @error('paciente_id')<span class="error-msg">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Laboratorio <span>*</span></label>
                <select name="laboratorio_id" class="form-control {{ $errors->has('laboratorio_id') ? 'is-invalid' : '' }}"
                        id="select-laboratorio" required onchange="onLabChange()">
                    <option value="">Seleccionar laboratorio...</option>
                    @foreach($laboratorios as $lab)
                        <option value="{{ $lab->id }}"
                                data-dias="{{ $lab->tiempo_entrega_dias }}"
                                data-especialidades="{{ $lab->especialidades_label }}"
                                {{ old('laboratorio_id') == $lab->id ? 'selected' : '' }}>
                            {{ $lab->nombre }}
                        </option>
                    @endforeach
                </select>
                <div class="lab-info-box" id="lab-info-box">
                    <i class="bi bi-info-circle"></i>
                    <span id="lab-info-text"></span>
                </div>
                @error('laboratorio_id')<span class="error-msg">{{ $message }}</span>@enderror
                <a href="{{ route('gestion-laboratorios.create') }}" target="_blank"
                   style="font-size:.75rem;color:var(--color-principal);text-decoration:none;margin-top:.25rem;">
                    <i class="bi bi-plus-circle"></i> Agregar nuevo laboratorio
                </a>
            </div>

        </div>
    </div>
</div>

{{-- Sección 2 — Especificaciones --}}
<div class="form-card">
    <div class="form-card-header">
        <h3><i class="bi bi-tools"></i> Especificaciones del Trabajo</h3>
    </div>
    <div class="form-body">
        <div class="form-group" style="margin-bottom:1rem;">
            <label>Tipo de Trabajo <span>*</span></label>
            <input type="text" name="tipo_trabajo" class="form-control {{ $errors->has('tipo_trabajo') ? 'is-invalid' : '' }}"
                   value="{{ old('tipo_trabajo') }}" list="lista-trabajos" placeholder="Ej: Corona en zirconia" required>
            <datalist id="lista-trabajos">
                <option>Corona metal-porcelana</option>
                <option>Corona en zirconia</option>
                <option>Corona en resina</option>
                <option>Puente de 3 unidades</option>
                <option>Puente de 4 unidades o más</option>
                <option>Prótesis parcial removible</option>
                <option>Prótesis total superior</option>
                <option>Prótesis total inferior</option>
                <option>Prótesis sobre implantes</option>
                <option>Carilla de porcelana</option>
                <option>Carilla de resina</option>
                <option>Incrustación</option>
                <option>Placa de descarga nocturna</option>
                <option>Retenedor de ortodoncia</option>
                <option>Aparato ortopédico</option>
                <option>Modelo de estudio</option>
            </datalist>
            @error('tipo_trabajo')<span class="error-msg">{{ $message }}</span>@enderror
        </div>

        <div class="form-grid-3">
            <div class="form-group">
                <label>Dientes Involucrados</label>
                <input type="text" name="dientes" class="form-control" value="{{ old('dientes') }}"
                       placeholder="Ej: 11, 12, 21">
            </div>

            <div class="form-group">
                <label>Color del Diente</label>
                <input type="text" name="color_diente" class="form-control"
                       value="{{ old('color_diente') }}" list="lista-colores" placeholder="Ej: A1, B2, C3...">
                <datalist id="lista-colores">
                    <option>A1 — Muy claro</option><option>A2 — Claro</option>
                    <option>A3 — Medio</option><option>A3.5 — Medio oscuro</option><option>A4 — Oscuro</option>
                    <option>B1</option><option>B2</option><option>B3</option><option>B4</option>
                    <option>C1</option><option>C2</option><option>C3</option><option>C4</option>
                    <option>D2</option><option>D3</option><option>D4</option>
                </datalist>
            </div>

            <div class="form-group">
                <label>Material</label>
                <input type="text" name="material" class="form-control"
                       value="{{ old('material') }}" list="lista-materiales" placeholder="Ej: Zirconia, Resina...">
                <datalist id="lista-materiales">
                    <option>Metal-porcelana</option><option>Zirconia</option>
                    <option>Acrílico</option><option>Resina</option>
                    <option>Porcelana</option><option>Metal</option><option>Fibra de vidrio</option>
                    <option>Disilicato de litio</option><option>PMMA</option>
                </datalist>
            </div>
        </div>

        <div class="form-group" style="margin-top:1rem;">
            <label>Descripción Detallada del Trabajo <span>*</span></label>
            <textarea name="descripcion" class="form-control {{ $errors->has('descripcion') ? 'is-invalid' : '' }}"
                      rows="4" placeholder="Detalle las especificaciones, instrucciones especiales, medidas, etc." required>{{ old('descripcion') }}</textarea>
            @error('descripcion')<span class="error-msg">{{ $message }}</span>@enderror
        </div>
    </div>
</div>

{{-- Sección 3 — Fechas y costos --}}
<div class="form-card">
    <div class="form-card-header">
        <h3><i class="bi bi-calendar3"></i> Fechas y Costos</h3>
    </div>
    <div class="form-body">
        <div class="form-grid-3">
            <div class="form-group">
                <label>Fecha de Envío</label>
                <input type="date" name="fecha_envio" id="fecha-envio" class="form-control"
                       value="{{ old('fecha_envio') }}" onchange="calcularEntrega()">
            </div>
            <div class="form-group">
                <label>Fecha Entrega Estimada</label>
                <input type="date" name="fecha_entrega_estimada" id="fecha-entrega" class="form-control"
                       value="{{ old('fecha_entrega_estimada') }}">
                <span style="font-size:.73rem; color:#8fa39a;">Se calcula automáticamente al seleccionar laboratorio y fecha de envío</span>
            </div>
            <div class="form-group">
                <label>Precio del Laboratorio ($)</label>
                <input type="text" name="precio_laboratorio" id="precio-lab" class="form-control"
                       value="{{ old('precio_laboratorio') }}" placeholder="0" oninput="formatearPrecio(this)">
            </div>
        </div>
    </div>
</div>

{{-- Sección 4 — Observaciones --}}
<div class="form-card">
    <div class="form-card-header">
        <h3><i class="bi bi-chat-left-text"></i> Observaciones de Envío</h3>
    </div>
    <div class="form-body">
        <textarea name="observaciones_envio" class="form-control" rows="3"
                  placeholder="Instrucciones especiales para el laboratorio, anotaciones de envío...">{{ old('observaciones_envio') }}</textarea>
    </div>
</div>

{{-- Botones --}}
<div style="display:flex; gap:.75rem; margin-top:.5rem;">
    <button type="submit" class="btn-morado">
        <i class="bi bi-save"></i> Crear Orden
    </button>
    <a href="{{ route('laboratorio.index') }}"
       style="display:inline-flex;align-items:center;gap:.3rem;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem 1.1rem;font-size:.875rem;text-decoration:none;">
        Cancelar
    </a>
</div>

</form>

@endsection

@push('scripts')
<script>
// ── Laboratorio: mostrar info y calcular fecha ──────────────────────────
let diasLab = 0;

function onLabChange() {
    const sel = document.getElementById('select-laboratorio');
    const opt = sel.options[sel.selectedIndex];
    const infoBox = document.getElementById('lab-info-box');
    const infoText = document.getElementById('lab-info-text');

    diasLab = parseInt(opt.dataset.dias) || 0;
    const especialidades = opt.dataset.especialidades || '';

    if (sel.value) {
        let info = '';
        if (diasLab > 0) info += `Tiempo de entrega: ${diasLab} días. `;
        if (especialidades) info += `Especialidades: ${especialidades}`;
        if (info) {
            infoText.textContent = info;
            infoBox.style.display = 'block';
        } else {
            infoBox.style.display = 'none';
        }
        calcularEntrega();
    } else {
        infoBox.style.display = 'none';
    }
}

function calcularEntrega() {
    if (!diasLab) return;
    const fechaEnvio = document.getElementById('fecha-envio').value;
    if (!fechaEnvio) return;

    const d = new Date(fechaEnvio);
    d.setDate(d.getDate() + diasLab);
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    document.getElementById('fecha-entrega').value = `${yyyy}-${mm}-${dd}`;
}

// ── Formatear precio ───────────────────────────────────────────────────
function formatearPrecio(el) {
    let v = el.value.replace(/\D/g, '');
    el.value = v ? parseInt(v).toLocaleString('es-CO') : '';
}

// Antes de enviar, limpiar precio
document.getElementById('form-orden').addEventListener('submit', function() {
    const p = document.getElementById('precio-lab');
    if (p) p.value = p.value.replace(/\./g, '').replace(/,/g, '');
});

// Inicializar si hay selección previa
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('select-laboratorio');
    if (sel && sel.value) onLabChange();
});
</script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
// Tom Select — Paciente
new TomSelect('#sel-paciente', {
    placeholder: 'Buscar paciente...',
    searchField: ['text'],
    maxOptions: 100,
});

// Tom Select — Laboratorio (con callback onLabChange)
var tsLab = new TomSelect('#select-laboratorio', {
    placeholder: 'Buscar laboratorio...',
    searchField: ['text'],
    maxOptions: 50,
    onChange: function(value) { onLabChange(); }
});
</script>
@endpush
