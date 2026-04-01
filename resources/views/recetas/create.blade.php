@extends('layouts.app')
@section('titulo', 'Nueva Receta Médica')

@section('contenido')

<div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <a href="{{ route('recetas.index') }}" style="color:var(--texto-secundario);text-decoration:none;font-size:.84rem;">
        <i class="bi bi-arrow-left"></i> Recetas
    </a>
    <i class="bi bi-chevron-right" style="font-size:.7rem;color:var(--texto-secundario);"></i>
    <span style="font-size:.84rem;font-weight:600;">Nueva Receta</span>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#7f1d1d;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <ul style="margin:0;padding-left:1.2rem;">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('recetas.store') }}" id="form-receta">
@csrf
<input type="hidden" name="medicamentos" id="campo-medicamentos">

<div class="card-sistema" style="margin-bottom:1rem;">
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-person-circle me-2"></i>Datos generales
    </h5>
    <div class="row g-3">
        <div class="col-md-5">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Paciente <span style="color:#dc2626;">*</span></label>
            <select name="paciente_id" id="sel-paciente" required
                    style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Seleccione paciente</option>
                @foreach($pacientes as $p)
                <option value="{{ $p->id }}" {{ (old('paciente_id', $paciente?->id) == $p->id)?'selected':'' }}>
                    {{ $p->apellido }}, {{ $p->nombre }} — {{ $p->numero_historia }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Doctor <span style="color:#dc2626;">*</span></label>
            <select name="user_id" required style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                @foreach($doctores as $d)
                <option value="{{ $d->id }}" {{ old('user_id', auth()->id()) == $d->id?'selected':'' }}>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Fecha <span style="color:#dc2626;">*</span></label>
            <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-6">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Evolución asociada (opcional)</label>
            <select name="evolucion_id" id="sel-evolucion" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Sin evolución</option>
                @if($evolucion)
                <option value="{{ $evolucion->id }}" selected>{{ $evolucion->fecha->format('d/m/Y') }} — {{ Str::limit($evolucion->procedimiento, 40) }}</option>
                @endif
            </select>
        </div>
        <div class="col-md-6">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Diagnóstico</label>
            <input type="text" name="diagnostico" value="{{ old('diagnostico') }}" placeholder="Diagnóstico o motivo de consulta..."
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

    <div id="lista-medicamentos">
        <div id="sin-medicamentos" style="text-align:center;padding:2rem;color:var(--texto-secundario);font-size:.84rem;border:2px dashed var(--fondo-borde);border-radius:8px;">
            <i class="bi bi-capsule" style="font-size:1.5rem;display:block;margin-bottom:.5rem;opacity:.4;"></i>
            Agregue medicamentos usando el botón o las plantillas
        </div>
    </div>
</div>

{{-- Indicaciones generales --}}
<div class="card-sistema" style="margin-bottom:1rem;">
    <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Indicaciones generales</label>
    <textarea name="indicaciones_generales" rows="3"
              placeholder="Indicaciones generales al paciente, recomendaciones de higiene, dieta, próxima cita..."
              style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);resize:vertical;">{{ old('indicaciones_generales') }}</textarea>
</div>

<div style="display:flex;gap:.75rem;justify-content:flex-end;">
    <a href="{{ route('recetas.index') }}"
       style="padding:.55rem 1.25rem;border:1px solid var(--fondo-borde);border-radius:8px;color:var(--texto-secundario);text-decoration:none;font-size:.84rem;">
        Cancelar
    </a>
    <button type="submit" onclick="serializarMedicamentos()"
            style="padding:.55rem 1.5rem;background:var(--color-principal);color:white;border:none;border-radius:8px;font-size:.84rem;font-weight:600;cursor:pointer;box-shadow:0 2px 8px var(--sombra-principal);">
        <i class="bi bi-file-medical me-1"></i> Guardar Receta
    </button>
</div>
</form>

@push('scripts')
<script>
let medicamentos = [];

function renderMedicamentos() {
    const lista = document.getElementById('lista-medicamentos');
    const sinMed = document.getElementById('sin-medicamentos');

    if (medicamentos.length === 0) {
        lista.innerHTML = '<div id="sin-medicamentos" style="text-align:center;padding:2rem;color:var(--texto-secundario);font-size:.84rem;border:2px dashed var(--fondo-borde);border-radius:8px;"><i class="bi bi-capsule" style="font-size:1.5rem;display:block;margin-bottom:.5rem;opacity:.4;"></i>Agregue medicamentos usando el botón o las plantillas</div>';
        return;
    }

    lista.innerHTML = medicamentos.map((m, i) => `
    <div style="border:1px solid var(--fondo-borde);border-radius:10px;padding:1rem;margin-bottom:.75rem;background:var(--fondo-app);" id="med-${i}">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
            <span style="font-size:.78rem;font-weight:700;color:var(--color-principal);text-transform:uppercase;letter-spacing:.04em;">Medicamento #${i+1}</span>
            <button type="button" onclick="eliminarMedicamento(${i})"
                    style="padding:.2rem .5rem;background:#fee2e2;color:#dc2626;border:none;border-radius:5px;font-size:.75rem;cursor:pointer;">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="row g-2">
            <div class="col-md-4">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Nombre / Principio activo</label>
                <input type="text" value="${m.nombre||''}" oninput="medicamentos[${i}].nombre=this.value"
                       placeholder="Ej: Ibuprofeno 400mg"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Presentación</label>
                <input type="text" value="${m.presentacion||''}" oninput="medicamentos[${i}].presentacion=this.value"
                       placeholder="tabletas, jarabe..."
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Dosis</label>
                <input type="text" value="${m.dosis||''}" oninput="medicamentos[${i}].dosis=this.value"
                       placeholder="500mg"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Frecuencia</label>
                <input type="text" value="${m.frecuencia||''}" oninput="medicamentos[${i}].frecuencia=this.value"
                       placeholder="Cada 8 horas"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Duración</label>
                <input type="text" value="${m.duracion||''}" oninput="medicamentos[${i}].duracion=this.value"
                       placeholder="5 días"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Cantidad</label>
                <input type="text" value="${m.cantidad||''}" oninput="medicamentos[${i}].cantidad=this.value"
                       placeholder="15 tabletas"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-10">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Indicaciones específicas</label>
                <input type="text" value="${m.indicaciones||''}" oninput="medicamentos[${i}].indicaciones=this.value"
                       placeholder="Tomar con alimentos, no tomar si..."
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

// Cargar evoluciones al cambiar paciente
document.getElementById('sel-paciente').addEventListener('change', function() {
    const pid = this.value;
    const sel = document.getElementById('sel-evolucion');
    sel.innerHTML = '<option value="">Cargando...</option>';
    if (!pid) { sel.innerHTML = '<option value="">Sin evolución</option>'; return; }
    fetch(`/api/paciente/${pid}/evoluciones`)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">Sin evolución</option>';
            data.forEach(e => {
                sel.innerHTML += `<option value="${e.id}">${e.fecha} — ${e.procedimiento}</option>`;
            });
        })
        .catch(() => { sel.innerHTML = '<option value="">Sin evolución</option>'; });
});
</script>
@endpush

@endsection
