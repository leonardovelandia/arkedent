@extends('layouts.app')
@section('titulo', 'Editar Orden ' . $orden->numero_orden)

@push('estilos')
<style>
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
</style>
@endpush

@section('contenido')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
    <div>
        <h1 style="font-family:var(--fuente-titulos); font-size:1.3rem; color:#1c2b22; margin:0;">Editar Orden {{ $orden->numero_orden }}</h1>
        <p style="font-size:.83rem; color:#8fa39a; margin:.2rem 0 0;">{{ $orden->paciente->nombre_completo }} · {{ $orden->tipo_trabajo }}</p>
    </div>
    <a href="{{ route('laboratorio.show', $orden) }}"
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

<form method="POST" action="{{ route('laboratorio.update', $orden) }}" id="form-orden">
@csrf
@method('PUT')

<div class="form-card">
    <div class="form-card-header"><h3><i class="bi bi-person-circle"></i> Datos Generales</h3></div>
    <div class="form-body">
        <div class="form-grid-2">
            <div class="form-group">
                <label>Paciente <span>*</span></label>
                <select name="paciente_id" class="form-control" required>
                    @foreach($pacientes as $pac)
                        <option value="{{ $pac->id }}" {{ $orden->paciente_id == $pac->id ? 'selected' : '' }}>{{ $pac->nombre_completo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Laboratorio <span>*</span></label>
                <select name="laboratorio_id" class="form-control" id="select-laboratorio" required onchange="onLabChange()">
                    @foreach($laboratorios as $lab)
                        <option value="{{ $lab->id }}" data-dias="{{ $lab->tiempo_entrega_dias }}" data-especialidades="{{ $lab->especialidades_label }}"
                                {{ $orden->laboratorio_id == $lab->id ? 'selected' : '' }}>
                            {{ $lab->nombre }}
                        </option>
                    @endforeach
                </select>
                <div class="lab-info-box" id="lab-info-box"><i class="bi bi-info-circle"></i> <span id="lab-info-text"></span></div>
            </div>
        </div>
    </div>
</div>

<div class="form-card">
    <div class="form-card-header"><h3><i class="bi bi-tools"></i> Especificaciones del Trabajo</h3></div>
    <div class="form-body">
        <div class="form-group" style="margin-bottom:1rem;">
            <label>Tipo de Trabajo <span>*</span></label>
            <input type="text" name="tipo_trabajo" class="form-control" value="{{ old('tipo_trabajo', $orden->tipo_trabajo) }}" list="lista-trabajos" required>
            <datalist id="lista-trabajos">
                <option>Corona metal-porcelana</option><option>Corona en zirconia</option>
                <option>Corona en resina</option><option>Puente de 3 unidades</option>
                <option>Puente de 4 unidades o más</option><option>Prótesis parcial removible</option>
                <option>Prótesis total superior</option><option>Prótesis total inferior</option>
                <option>Prótesis sobre implantes</option><option>Carilla de porcelana</option>
                <option>Carilla de resina</option><option>Incrustación</option>
                <option>Placa de descarga nocturna</option><option>Retenedor de ortodoncia</option>
                <option>Aparato ortopédico</option><option>Modelo de estudio</option>
            </datalist>
        </div>
        <div class="form-grid-3">
            <div class="form-group">
                <label>Dientes</label>
                <input type="text" name="dientes" class="form-control" value="{{ old('dientes', $orden->dientes) }}" placeholder="Ej: 11, 12">
            </div>
            <div class="form-group">
                <label>Color (Vita)</label>
                <select name="color_diente" class="form-control">
                    <option value="">Seleccionar...</option>
                    @foreach(['A1','A2','A3','A3.5','A4','B1','B2','B3','B4','C1','C2','C3','C4','D2','D3','D4'] as $c)
                        <option {{ old('color_diente', $orden->color_diente) === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Material</label>
                <select name="material" class="form-control">
                    <option value="">Seleccionar...</option>
                    @foreach(['Metal-porcelana','Zirconia','Acrílico','Resina','Porcelana','Metal','Fibra de vidrio'] as $m)
                        <option {{ old('material', $orden->material) === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group" style="margin-top:1rem;">
            <label>Descripción <span>*</span></label>
            <textarea name="descripcion" class="form-control" rows="4" required>{{ old('descripcion', $orden->descripcion) }}</textarea>
        </div>
    </div>
</div>

<div class="form-card">
    <div class="form-card-header"><h3><i class="bi bi-calendar3"></i> Fechas y Costos</h3></div>
    <div class="form-body">
        <div class="form-grid-3">
            <div class="form-group">
                <label>Fecha de Envío</label>
                <input type="date" name="fecha_envio" id="fecha-envio" class="form-control"
                       value="{{ old('fecha_envio', $orden->fecha_envio?->format('Y-m-d')) }}" onchange="calcularEntrega()">
            </div>
            <div class="form-group">
                <label>Entrega Estimada</label>
                <input type="date" name="fecha_entrega_estimada" id="fecha-entrega" class="form-control"
                       value="{{ old('fecha_entrega_estimada', $orden->fecha_entrega_estimada?->format('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label>Precio del Laboratorio ($)</label>
                <input type="text" name="precio_laboratorio" id="precio-lab" class="form-control"
                       value="{{ old('precio_laboratorio', $orden->precio_laboratorio ? number_format($orden->precio_laboratorio, 0, ',', '.') : '') }}"
                       oninput="formatearPrecio(this)">
            </div>
        </div>
    </div>
</div>

<div class="form-card">
    <div class="form-card-header"><h3><i class="bi bi-chat-left-text"></i> Observaciones de Envío</h3></div>
    <div class="form-body">
        <textarea name="observaciones_envio" class="form-control" rows="3">{{ old('observaciones_envio', $orden->observaciones_envio) }}</textarea>
    </div>
</div>

<div style="display:flex; gap:.75rem; margin-top:.5rem;">
    <button type="submit" class="btn-morado"><i class="bi bi-save"></i> Guardar Cambios</button>
    <a href="{{ route('laboratorio.show', $orden) }}"
       style="display:inline-flex;align-items:center;gap:.3rem;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem 1.1rem;font-size:.875rem;text-decoration:none;">
        Cancelar
    </a>
</div>
</form>

@endsection

@push('scripts')
<script>
let diasLab = 0;
function onLabChange() {
    const sel = document.getElementById('select-laboratorio');
    const opt = sel.options[sel.selectedIndex];
    diasLab = parseInt(opt.dataset.dias) || 0;
    const infoBox = document.getElementById('lab-info-box');
    const infoText = document.getElementById('lab-info-text');
    if (sel.value && diasLab > 0) {
        infoText.textContent = `Tiempo de entrega: ${diasLab} días.`;
        infoBox.style.display = 'block';
    } else { infoBox.style.display = 'none'; }
}
function calcularEntrega() {
    if (!diasLab) return;
    const fe = document.getElementById('fecha-envio').value;
    if (!fe) return;
    const d = new Date(fe);
    d.setDate(d.getDate() + diasLab);
    const y = d.getFullYear(), m = String(d.getMonth()+1).padStart(2,'0'), day = String(d.getDate()).padStart(2,'0');
    document.getElementById('fecha-entrega').value = `${y}-${m}-${day}`;
}
function formatearPrecio(el) {
    let v = el.value.replace(/\D/g,'');
    el.value = v ? parseInt(v).toLocaleString('es-CO') : '';
}
document.getElementById('form-orden').addEventListener('submit', function() {
    const p = document.getElementById('precio-lab');
    if (p) p.value = p.value.replace(/\./g,'').replace(/,/g,'');
});
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('select-laboratorio');
    if (sel && sel.value) onLabChange();
});
</script>
@endpush
