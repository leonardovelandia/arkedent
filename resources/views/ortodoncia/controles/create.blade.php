@extends('layouts.app')
@section('titulo', 'Nuevo Control — Sesión #' . $numeroSesion)

@section('contenido')

<div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <a href="{{ route('ortodoncia.show', $ficha) }}" style="color:var(--texto-secundario);text-decoration:none;font-size:.84rem;">
        <i class="bi bi-arrow-left"></i> {{ $ficha->numero_ficha }}
    </a>
    <i class="bi bi-chevron-right" style="font-size:.7rem;color:var(--texto-secundario);"></i>
    <span style="font-size:.84rem;font-weight:600;">Nuevo Control — Sesión #{{ $numeroSesion }}</span>
</div>

{{-- Info rápida del paciente --}}
<div style="background:var(--color-muy-claro);border:1px solid var(--color-claro);border-radius:10px;padding:.85rem 1.25rem;margin-bottom:1.25rem;display:flex;flex-wrap:wrap;gap:1rem;align-items:center;">
    <div>
        <div style="font-size:.7rem;color:var(--color-principal);font-weight:700;text-transform:uppercase;">Paciente</div>
        <div style="font-size:.9rem;font-weight:600;">{{ $ficha->paciente->nombre_completo }}</div>
    </div>
    <div style="width:1px;height:32px;background:var(--color-claro);"></div>
    <div>
        <div style="font-size:.7rem;color:var(--color-principal);font-weight:700;text-transform:uppercase;">Tratamiento</div>
        <div style="font-size:.85rem;">{{ $ficha->tipo_ortodoncia_label }} — {{ $ficha->marca_brackets }}</div>
    </div>
    <div style="width:1px;height:32px;background:var(--color-claro);"></div>
    <div>
        <div style="font-size:.7rem;color:var(--color-principal);font-weight:700;text-transform:uppercase;">Progreso actual</div>
        <div style="font-size:.85rem;font-weight:700;">{{ $ficha->progreso }}%</div>
    </div>
    @if($ficha->ultimoControl)
    <div style="width:1px;height:32px;background:var(--color-claro);"></div>
    <div>
        <div style="font-size:.7rem;color:var(--color-principal);font-weight:700;text-transform:uppercase;">Último control</div>
        <div style="font-size:.85rem;">{{ $ficha->ultimoControl->fecha_control->format('d/m/Y') }}</div>
    </div>
    @endif
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#7f1d1d;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <strong><i class="bi bi-exclamation-triangle me-1"></i> Corrige los siguientes errores:</strong>
    <ul style="margin:.4rem 0 0 1.2rem;padding:0;">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('controles.store') }}">
@csrf
<input type="hidden" name="ficha_ortodontica_id" value="{{ $ficha->id }}">
<input type="hidden" name="paciente_id" value="{{ $ficha->paciente_id }}">
<input type="hidden" name="numero_sesion" value="{{ $numeroSesion }}">

<div class="card-sistema">

    {{-- Datos del control --}}
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-calendar-check me-2"></i>Datos del Control — Sesión #{{ $numeroSesion }}
    </h5>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Fecha del control <span style="color:#dc2626;">*</span></label>
            <input type="date" name="fecha_control" value="{{ old('fecha_control', date('Y-m-d')) }}" required
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Ortodoncista <span style="color:#dc2626;">*</span></label>
            <select name="user_id" required style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                @foreach($ortodoncistas as $u)
                <option value="{{ $u->id }}" {{ old('user_id', auth()->id()) == $u->id?'selected':'' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Cita asociada (opcional)</label>
            <select name="cita_id" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Sin cita asociada</option>
                @foreach($citas as $cita)
                <option value="{{ $cita->id }}" {{ old('cita_id') == $cita->id?'selected':'' }}>
                    {{ $cita->fecha->format('d/m/Y') }} — {{ $cita->hora_inicio }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Panel de arcos --}}
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-bezier me-2"></i>Arcos Instalados
    </h5>
    <div class="row g-3 mb-4">
        {{-- Superior --}}
        <div class="col-md-6">
            <div style="background:var(--fondo-card-alt);border:1px solid var(--fondo-borde);border-radius:10px;padding:1rem;">
                <h6 style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.05em;margin-bottom:.75rem;">Arcada Superior</h6>
                <div class="row g-2">
                    <div class="col-6">
                        <label style="font-size:.72rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.25rem;">Tipo arco</label>
                        <select name="tipo_arco_superior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .6rem;font-size:.82rem;background:white;">
                            <option value="">—</option>
                            @foreach(['niti'=>'Niti','acero'=>'Acero (SS)','tma'=>'TMA','fibra_vidrio'=>'Fibra de vidrio','ninguno'=>'Ninguno'] as $v=>$l)
                            <option value="{{ $v }}" {{ old('tipo_arco_superior')==$v?'selected':'' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label style="font-size:.72rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.25rem;">Calibre</label>
                        <select name="calibre_superior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .6rem;font-size:.82rem;background:white;">
                            <option value="">—</option>
                            @foreach(['014','016','016x022','017x025','018x025','019x025','020','021x025','Otro'] as $c)
                            <option value="{{ $c }}" {{ old('calibre_superior')==$c?'selected':'' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label style="font-size:.72rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.25rem;">Ligadura</label>
                        <select name="ligadura_superior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .6rem;font-size:.82rem;background:white;">
                            <option value="">—</option>
                            @foreach(['elastica'=>'Elástica','metalica'=>'Metálica','autoligado'=>'Autoligado','ninguna'=>'Ninguna'] as $v=>$l)
                            <option value="{{ $v }}" {{ old('ligadura_superior')==$v?'selected':'' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        {{-- Inferior --}}
        <div class="col-md-6">
            <div style="background:var(--fondo-card-alt);border:1px solid var(--fondo-borde);border-radius:10px;padding:1rem;">
                <h6 style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.05em;margin-bottom:.75rem;">Arcada Inferior</h6>
                <div class="row g-2">
                    <div class="col-6">
                        <label style="font-size:.72rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.25rem;">Tipo arco</label>
                        <select name="tipo_arco_inferior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .6rem;font-size:.82rem;background:white;">
                            <option value="">—</option>
                            @foreach(['niti'=>'Niti','acero'=>'Acero (SS)','tma'=>'TMA','fibra_vidrio'=>'Fibra de vidrio','ninguno'=>'Ninguno'] as $v=>$l)
                            <option value="{{ $v }}" {{ old('tipo_arco_inferior')==$v?'selected':'' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label style="font-size:.72rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.25rem;">Calibre</label>
                        <select name="calibre_inferior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .6rem;font-size:.82rem;background:white;">
                            <option value="">—</option>
                            @foreach(['014','016','016x022','017x025','018x025','019x025','020','021x025','Otro'] as $c)
                            <option value="{{ $c }}" {{ old('calibre_inferior')==$c?'selected':'' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label style="font-size:.72rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.25rem;">Ligadura</label>
                        <select name="ligadura_inferior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .6rem;font-size:.82rem;background:white;">
                            <option value="">—</option>
                            @foreach(['elastica'=>'Elástica','metalica'=>'Metálica','autoligado'=>'Autoligado','ninguna'=>'Ninguna'] as $v=>$l)
                            <option value="{{ $v }}" {{ old('ligadura_inferior')==$v?'selected':'' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        {{-- Color ligadura y elásticos --}}
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Color de ligadura</label>
            <input type="text" name="color_ligadura" value="{{ old('color_ligadura') }}" placeholder="Azul, rojo, transparente..."
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-5">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Elásticos intermaxilares</label>
            <div style="display:flex;align-items:center;gap:.75rem;height:38px;">
                <label style="display:flex;align-items:center;gap:.4rem;font-size:.84rem;cursor:pointer;">
                    <input type="checkbox" name="elasticos" id="chk-elasticos" value="1" {{ old('elasticos')?'checked':'' }}
                           style="width:16px;height:16px;accent-color:var(--color-principal);">
                    Usa elásticos
                </label>
                <input type="text" name="tipo_elasticos" id="inp-elasticos" value="{{ old('tipo_elasticos') }}"
                       placeholder="Clase II, Clase III, triángulo..."
                       style="flex:1;border:1px solid var(--fondo-borde);border-radius:8px;padding:.4rem .6rem;font-size:.82rem;background:var(--fondo-app);display:{{ old('elasticos')?'block':'none' }};">
            </div>
        </div>
    </div>

    {{-- Odontograma de sesión --}}
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-grid-3x3-gap me-2"></i>Odontograma de Esta Sesión
    </h5>
    <p style="font-size:.8rem;color:var(--texto-secundario);margin-bottom:1rem;">
        El odontograma cargado refleja el estado del último control. Marca los cambios de esta sesión.
    </p>
    <div style="margin-bottom:1.5rem;">
        @php
            $odontogramaBase64 = old('odontograma_sesion',
                !empty($odontogramaBase) ? json_encode($odontogramaBase) : '{}'
            );
        @endphp
        @include('ortodoncia._odontograma', ['odontogramaData' => $odontogramaBase64, 'inputName' => 'odontograma_sesion'])
    </div>

    {{-- Progreso y observaciones --}}
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-bar-chart me-2"></i>Progreso y Observaciones
    </h5>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">
                Progreso del tratamiento: <span id="display-progreso" style="color:var(--color-principal);font-weight:700;">{{ old('progreso_porcentaje', $ficha->progreso) }}%</span>
            </label>
            <input type="range" name="progreso_porcentaje" id="slider-progreso"
                   value="{{ old('progreso_porcentaje', $ficha->progreso) }}" min="0" max="100" step="5"
                   style="width:100%;accent-color:var(--color-principal);">
            <div style="display:flex;justify-content:space-between;font-size:.68rem;color:var(--texto-secundario);">
                <span>0%</span><span>50%</span><span>100%</span>
            </div>
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Próxima cita (semanas)</label>
            <select name="proxima_cita_semanas" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Sin definir</option>
                @foreach([3,4,5,6,7,8,10,12,16,20,24] as $sem)
                <option value="{{ $sem }}" {{ old('proxima_cita_semanas')==$sem?'selected':'' }}>{{ $sem }} semanas</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Observaciones del control</label>
            <textarea name="observaciones" rows="3" placeholder="Describe los procedimientos realizados, hallazgos, etc."
                      style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);resize:vertical;">{{ old('observaciones') }}</textarea>
        </div>
        <div class="col-12">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Indicaciones al paciente</label>
            <textarea name="indicaciones_paciente" rows="2" placeholder="Instrucciones para el paciente: higiene, uso de elásticos, dieta..."
                      style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);resize:vertical;">{{ old('indicaciones_paciente') }}</textarea>
        </div>
    </div>

    <div style="display:flex;gap:.75rem;justify-content:flex-end;padding-top:1rem;border-top:1px solid var(--fondo-borde);">
        <a href="{{ route('ortodoncia.show', $ficha) }}"
           style="padding:.55rem 1.25rem;border:1px solid var(--fondo-borde);border-radius:8px;color:var(--texto-secundario);text-decoration:none;font-size:.84rem;">
            Cancelar
        </a>
        <button type="submit"
                style="padding:.55rem 1.5rem;background:var(--color-principal);color:white;border:none;border-radius:8px;font-size:.84rem;font-weight:600;cursor:pointer;box-shadow:0 2px 8px var(--sombra-principal);">
            <i class="bi bi-check-lg me-1"></i> Guardar Control #{{ $numeroSesion }}
        </button>
    </div>

</div>
</form>

@push('scripts')
<script>
// Slider de progreso
const slider   = document.getElementById('slider-progreso');
const display  = document.getElementById('display-progreso');
if (slider) {
    slider.addEventListener('input', () => { display.textContent = slider.value + '%'; });
}

// Toggle elásticos
const chkEl = document.getElementById('chk-elasticos');
const inpEl = document.getElementById('inp-elasticos');
if (chkEl && inpEl) {
    chkEl.addEventListener('change', function() {
        inpEl.style.display = this.checked ? 'block' : 'none';
        if (!this.checked) inpEl.value = '';
    });
}
</script>
@endpush

@endsection
