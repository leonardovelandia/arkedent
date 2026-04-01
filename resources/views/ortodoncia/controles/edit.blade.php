@extends('layouts.app')
@section('titulo', 'Editar Control — Sesión #' . $control->numero_sesion)

@section('contenido')

<div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <a href="{{ route('ortodoncia.show', $ficha) }}" style="color:var(--texto-secundario);text-decoration:none;font-size:.84rem;">
        <i class="bi bi-arrow-left"></i> {{ $ficha->numero_ficha }}
    </a>
    <i class="bi bi-chevron-right" style="font-size:.7rem;color:var(--texto-secundario);"></i>
    <span style="font-size:.84rem;font-weight:600;">Editar Sesión #{{ $control->numero_sesion }}</span>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#7f1d1d;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <ul style="margin:0;padding-left:1.2rem;">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('controles.update', $control) }}">
@csrf
@method('PUT')

<div class="card-sistema">
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Fecha del control</label>
            <input type="date" name="fecha_control" value="{{ old('fecha_control', $control->fecha_control->format('Y-m-d')) }}" required
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Ortodoncista</label>
            <select name="user_id" required style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                @foreach($ortodoncistas as $u)
                <option value="{{ $u->id }}" {{ old('user_id', $control->user_id) == $u->id?'selected':'' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Cita asociada</label>
            <select name="cita_id" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Sin cita</option>
                @foreach($citas as $cita)
                <option value="{{ $cita->id }}" {{ old('cita_id',$control->cita_id) == $cita->id?'selected':'' }}>
                    {{ $cita->fecha->format('d/m/Y') }} {{ $cita->hora_inicio }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Arcos --}}
    <div class="row g-3 mb-4">
        @foreach([
            ['Superior','tipo_arco_superior','calibre_superior','ligadura_superior'],
            ['Inferior','tipo_arco_inferior','calibre_inferior','ligadura_inferior'],
        ] as [$arcada, $tipo, $calibre, $ligadura])
        <div class="col-md-6">
            <div style="background:var(--fondo-card-alt);border:1px solid var(--fondo-borde);border-radius:10px;padding:1rem;">
                <h6 style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.05em;margin-bottom:.75rem;">Arcada {{ $arcada }}</h6>
                <div class="row g-2">
                    <div class="col-6">
                        <select name="{{ $tipo }}" style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .6rem;font-size:.82rem;background:white;">
                            <option value="">—</option>
                            @foreach(['niti'=>'Niti','acero'=>'SS','tma'=>'TMA','fibra_vidrio'=>'Fibra','ninguno'=>'Ninguno'] as $v=>$l)
                            <option value="{{ $v }}" {{ old($tipo,$control->{$tipo})==$v?'selected':'' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <select name="{{ $calibre }}" style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .6rem;font-size:.82rem;background:white;">
                            <option value="">—</option>
                            @foreach(['014','016','016x022','017x025','018x025','019x025','020','021x025','Otro'] as $c)
                            <option value="{{ $c }}" {{ old($calibre,$control->{$calibre})==$c?'selected':'' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <select name="{{ $ligadura }}" style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .6rem;font-size:.82rem;background:white;">
                            <option value="">—</option>
                            @foreach(['elastica'=>'Elástica','metalica'=>'Metálica','autoligado'=>'Autoligado','ninguna'=>'Ninguna'] as $v=>$l)
                            <option value="{{ $v }}" {{ old($ligadura,$control->{$ligadura})==$v?'selected':'' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Color ligadura</label>
            <input type="text" name="color_ligadura" value="{{ old('color_ligadura', $control->color_ligadura) }}"
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-5">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Elásticos</label>
            <div style="display:flex;align-items:center;gap:.75rem;height:38px;">
                <label style="display:flex;align-items:center;gap:.4rem;font-size:.84rem;cursor:pointer;">
                    <input type="checkbox" name="elasticos" id="chk-elasticos" value="1" {{ old('elasticos',$control->elasticos)?'checked':'' }}
                           style="width:16px;height:16px;accent-color:var(--color-principal);">
                    Usa elásticos
                </label>
                <input type="text" name="tipo_elasticos" id="inp-elasticos" value="{{ old('tipo_elasticos',$control->tipo_elasticos) }}"
                       style="flex:1;border:1px solid var(--fondo-borde);border-radius:8px;padding:.4rem .6rem;font-size:.82rem;background:var(--fondo-app);display:{{ old('elasticos',$control->elasticos)?'block':'none' }};">
            </div>
        </div>
    </div>

    {{-- Odontograma --}}
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-grid-3x3-gap me-2"></i>Odontograma de la Sesión
    </h5>
    <div style="margin-bottom:1.5rem;">
        @php
            $odontogramaEdit = old('odontograma_sesion',
                $control->odontograma_sesion ? json_encode($control->odontograma_sesion) : '{}'
            );
        @endphp
        @include('ortodoncia._odontograma', ['odontogramaData' => $odontogramaEdit, 'inputName' => 'odontograma_sesion'])
    </div>

    {{-- Progreso y observaciones --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">
                Progreso: <span id="display-progreso" style="color:var(--color-principal);font-weight:700;">{{ old('progreso_porcentaje', $control->progreso_porcentaje ?? 0) }}%</span>
            </label>
            <input type="range" name="progreso_porcentaje" id="slider-progreso"
                   value="{{ old('progreso_porcentaje', $control->progreso_porcentaje ?? 0) }}" min="0" max="100" step="5"
                   style="width:100%;accent-color:var(--color-principal);">
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Próxima cita (semanas)</label>
            <select name="proxima_cita_semanas" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Sin definir</option>
                @foreach([3,4,5,6,7,8,10,12,16,20,24] as $sem)
                <option value="{{ $sem }}" {{ old('proxima_cita_semanas',$control->proxima_cita_semanas)==$sem?'selected':'' }}>{{ $sem }} semanas</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Observaciones</label>
            <textarea name="observaciones" rows="3" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);resize:vertical;">{{ old('observaciones',$control->observaciones) }}</textarea>
        </div>
        <div class="col-12">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Indicaciones al paciente</label>
            <textarea name="indicaciones_paciente" rows="2" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);resize:vertical;">{{ old('indicaciones_paciente',$control->indicaciones_paciente) }}</textarea>
        </div>
    </div>

    <div style="display:flex;gap:.75rem;justify-content:flex-end;padding-top:1rem;border-top:1px solid var(--fondo-borde);">
        <a href="{{ route('ortodoncia.show', $ficha) }}"
           style="padding:.55rem 1.25rem;border:1px solid var(--fondo-borde);border-radius:8px;color:var(--texto-secundario);text-decoration:none;font-size:.84rem;">
            Cancelar
        </a>
        <button type="submit"
                style="padding:.55rem 1.5rem;background:var(--color-principal);color:white;border:none;border-radius:8px;font-size:.84rem;font-weight:600;cursor:pointer;box-shadow:0 2px 8px var(--sombra-principal);">
            <i class="bi bi-check-lg me-1"></i> Guardar cambios
        </button>
    </div>
</div>
</form>

@push('scripts')
<script>
const slider  = document.getElementById('slider-progreso');
const display = document.getElementById('display-progreso');
if (slider) slider.addEventListener('input', () => { display.textContent = slider.value + '%'; });

const chkEl = document.getElementById('chk-elasticos');
const inpEl = document.getElementById('inp-elasticos');
if (chkEl && inpEl) {
    chkEl.addEventListener('change', function() {
        inpEl.style.display = this.checked ? 'block' : 'none';
    });
}
</script>
@endpush

@endsection
