@extends('layouts.app')
@section('titulo', 'Editar Receta — ' . $receta->numero_receta)

@push('estilos')
<style>
    /* ── Aurora Glass overrides for card-sistema inline elements ── */
    body[data-ui="glass"] .card-sistema select,
    body[data-ui="glass"] .card-sistema input[type="text"],
    body[data-ui="glass"] .card-sistema input[type="date"],
    body[data-ui="glass"] .card-sistema textarea {
        background:rgba(255,255,255,0.08) !important;
        border:1px solid rgba(0,234,255,0.30) !important;
        color:rgba(255,255,255,0.90) !important;
    }
    body[data-ui="glass"] .card-sistema select:focus,
    body[data-ui="glass"] .card-sistema input:focus,
    body[data-ui="glass"] .card-sistema textarea:focus {
        border-color:rgba(0,234,255,0.70) !important;
        outline:none !important;
    }
    body[data-ui="glass"] .card-sistema label,
    body[data-ui="glass"] .card-sistema > label { color:rgba(0,234,255,0.85) !important; }
    body[data-ui="glass"] .card-sistema h5 { color:rgba(0,234,255,0.90) !important; border-bottom-color:rgba(0,234,255,0.20) !important; }
    /* Medicamento row items (rendered via JS inline styles) */
    body[data-ui="glass"] #lista-medicamentos [style*="background:var(--fondo-app)"] {
        background:rgba(255,255,255,0.06) !important;
        border-color:rgba(0,234,255,0.20) !important;
    }
    /* Plantilla buttons */
    body[data-ui="glass"] .card-sistema [style*="background:var(--fondo-app)"][style*="border-radius:20px"] {
        background:rgba(255,255,255,0.08) !important;
        border-color:rgba(0,234,255,0.25) !important;
        color:rgba(255,255,255,0.70) !important;
    }
</style>
@endpush

@section('contenido')

<div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <a href="{{ route('recetas.show', $receta) }}" style="color:var(--texto-secundario);text-decoration:none;font-size:.84rem;">
        <i class="bi bi-arrow-left"></i> {{ $receta->numero_receta }}
    </a>
    <i class="bi bi-chevron-right" style="font-size:.7rem;color:var(--texto-secundario);"></i>
    <span style="font-size:.84rem;font-weight:600;">Editar Receta</span>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#7f1d1d;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <ul style="margin:0;padding-left:1.2rem;">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('recetas.update', $receta) }}" id="form-receta">
@csrf
@method('PUT')
<input type="hidden" name="medicamentos" id="campo-medicamentos">

<div class="card-sistema" style="margin-bottom:1rem;">
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-person-circle me-2"></i>Datos generales
    </h5>
    <div class="row g-3">
        <div class="col-md-5">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Paciente <span style="color:#dc2626;">*</span></label>
            <select name="paciente_id" required style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                @foreach($pacientes as $p)
                <option value="{{ $p->id }}" {{ old('paciente_id', $receta->paciente_id) == $p->id?'selected':'' }}>
                    {{ $p->apellido }}, {{ $p->nombre }} — {{ $p->numero_historia }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Doctor <span style="color:#dc2626;">*</span></label>
            <select name="user_id" required style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                @foreach($doctores as $d)
                <option value="{{ $d->id }}" {{ old('user_id', $receta->user_id) == $d->id?'selected':'' }}>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Fecha <span style="color:#dc2626;">*</span></label>
            <input type="date" name="fecha" value="{{ old('fecha', $receta->fecha->format('Y-m-d')) }}" required
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-6">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Evolución asociada (opcional)</label>
            <select name="evolucion_id" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Sin evolución</option>
                @foreach($evoluciones as $evo)
                <option value="{{ $evo->id }}" {{ old('evolucion_id', $receta->evolucion_id) == $evo->id?'selected':'' }}>
                    {{ $evo->fecha->format('d/m/Y') }} — {{ Str::limit($evo->procedimiento, 50) }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Diagnóstico</label>
            <input type="text" name="diagnostico" value="{{ old('diagnostico', $receta->diagnostico) }}"
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
    </div>
</div>

{{-- Medicamentos --}}
<div class="card-sistema" style="margin-bottom:1rem;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin:0;">
            <i class="bi bi-capsule me-2"></i>Medicamentos
        </h5>
        <button type="button" onclick="agregarMedicamento()"
                style="padding:.4rem .9rem;background:var(--color-principal);color:white;border:none;border-radius:7px;font-size:.8rem;font-weight:600;cursor:pointer;">
            <i class="bi bi-plus-lg me-1"></i> Agregar
        </button>
    </div>
    {{-- Plantillas rápidas --}}
    <div style="margin-bottom:1rem;">
        <span style="font-size:.72rem;font-weight:600;color:var(--texto-secundario);text-transform:uppercase;letter-spacing:.04em;margin-right:.5rem;">Plantillas:</span>
        @php
        $plantillas = [
            'AINE'          => ['Ibuprofeno 400mg',      'tabletas',   '400mg',  'Cada 8 horas',    '5 días',   '15 tabletas', 'Tomar con alimentos'],
            'Antibiótico'   => ['Amoxicilina 500mg',     'cápsulas',   '500mg',  'Cada 8 horas',    '7 días',   '21 cápsulas', 'Completar el tratamiento'],
            'Analgésico'    => ['Acetaminofén 500mg',    'tabletas',   '500mg',  'Cada 6-8 horas',  '3-5 días', '20 tabletas', 'Máximo 4 tomas al día'],
            'Enjuague'      => ['Clorhexidina 0.12%',    'solución',   '15ml',   'Cada 12 horas',   '10 días',  '2 frascos',   'No tragar. Enjuagar 1 minuto'],
            'Metronidazol'  => ['Metronidazol 500mg',    'tabletas',   '500mg',  'Cada 8 horas',    '7 días',   '21 tabletas', 'Tomar con alimentos. No consumir alcohol'],
            'Clindamicina'  => ['Clindamicina 300mg',    'cápsulas',   '300mg',  'Cada 6 horas',    '7 días',   '28 cápsulas', 'Tomar con abundante agua'],
            'Diclofenaco'   => ['Diclofenaco 50mg',      'tabletas',   '50mg',   'Cada 8-12 horas', '5 días',   '15 tabletas', 'Tomar con alimentos'],
            'Dexametasona'  => ['Dexametasona 4mg',      'tabletas',   '4mg',    'Cada 8 horas',    '3 días',   '9 tabletas',  'No suspender bruscamente'],
        ];
        @endphp
        @foreach($plantillas as $nombre => $data)
        <button type="button" onclick='usarPlantilla(@json($nombre), @json($data))'
                style="padding:.25rem .7rem;border:1px solid var(--fondo-borde);border-radius:20px;font-size:.75rem;background:var(--fondo-app);cursor:pointer;color:var(--texto-secundario);margin-right:.3rem;margin-bottom:.3rem;">
            + {{ $nombre }}
        </button>
        @endforeach
    </div>

    <div id="lista-medicamentos"></div>
</div>

<div class="card-sistema" style="margin-bottom:1rem;">
    <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Indicaciones generales</label>
    <textarea name="indicaciones_generales" rows="3"
              style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);resize:vertical;">{{ old('indicaciones_generales', $receta->indicaciones_generales) }}</textarea>
</div>

<div style="display:flex;gap:.75rem;justify-content:flex-end;">
    <a href="{{ route('recetas.show', $receta) }}"
       style="padding:.55rem 1.25rem;border:1px solid var(--fondo-borde);border-radius:8px;color:var(--texto-secundario);text-decoration:none;font-size:.84rem;">
        Cancelar
    </a>
    <button type="submit" onclick="serializarMedicamentos()"
            style="padding:.55rem 1.5rem;background:var(--color-principal);color:white;border:none;border-radius:8px;font-size:.84rem;font-weight:600;cursor:pointer;box-shadow:0 2px 8px var(--sombra-principal);">
        <i class="bi bi-check-lg me-1"></i> Guardar cambios
    </button>
</div>
</form>

@push('scripts')
<script>
let medicamentos = @json($receta->medicamentos ?? []);

function renderMedicamentos() {
    const lista = document.getElementById('lista-medicamentos');
    if (medicamentos.length === 0) {
        lista.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--texto-secundario);font-size:.84rem;border:2px dashed var(--fondo-borde);border-radius:8px;"><i class="bi bi-capsule" style="font-size:1.5rem;display:block;margin-bottom:.5rem;opacity:.4;"></i>Sin medicamentos</div>';
        return;
    }
    lista.innerHTML = medicamentos.map((m, i) => `
    <div style="border:1px solid var(--fondo-borde);border-radius:10px;padding:1rem;margin-bottom:.75rem;background:var(--fondo-app);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
            <span style="font-size:.78rem;font-weight:700;color:var(--color-principal);text-transform:uppercase;">Medicamento #${i+1}</span>
            <button type="button" onclick="eliminarMedicamento(${i})"
                    style="padding:.2rem .5rem;background:#fee2e2;color:#dc2626;border:none;border-radius:5px;font-size:.75rem;cursor:pointer;">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="row g-2">
            <div class="col-md-4">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Nombre</label>
                <input type="text" value="${m.nombre||''}" oninput="medicamentos[${i}].nombre=this.value"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Presentación</label>
                <input type="text" value="${m.presentacion||''}" oninput="medicamentos[${i}].presentacion=this.value"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Dosis</label>
                <input type="text" value="${m.dosis||''}" oninput="medicamentos[${i}].dosis=this.value"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Frecuencia</label>
                <input type="text" value="${m.frecuencia||''}" oninput="medicamentos[${i}].frecuencia=this.value"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Duración</label>
                <input type="text" value="${m.duracion||''}" oninput="medicamentos[${i}].duracion=this.value"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Cantidad</label>
                <input type="text" value="${m.cantidad||''}" oninput="medicamentos[${i}].cantidad=this.value"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-10">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Indicaciones</label>
                <input type="text" value="${m.indicaciones||''}" oninput="medicamentos[${i}].indicaciones=this.value"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
        </div>
    </div>
    `).join('');
}

function agregarMedicamento() {
    medicamentos.push({nombre:'',presentacion:'',dosis:'',frecuencia:'',duracion:'',cantidad:'',indicaciones:''});
    renderMedicamentos();
}

function eliminarMedicamento(i) {
    medicamentos.splice(i, 1);
    renderMedicamentos();
}

function usarPlantilla(nombre, data) {
    medicamentos.push({
        nombre: data[0], presentacion: data[1], dosis: data[2],
        frecuencia: data[3], duracion: data[4], cantidad: data[5], indicaciones: data[6]
    });
    renderMedicamentos();
}

function serializarMedicamentos() {
    document.getElementById('campo-medicamentos').value = JSON.stringify(medicamentos);
}

renderMedicamentos();
</script>
@endpush

@endsection
