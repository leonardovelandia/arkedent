@extends('layouts.app')
@section('titulo', 'Editar Ficha ' . $ficha->numero_ficha)

@section('contenido')

<div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <a href="{{ route('ortodoncia.show', $ficha) }}" style="color:var(--texto-secundario);text-decoration:none;font-size:.84rem;">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
    <i class="bi bi-chevron-right" style="font-size:.7rem;color:var(--texto-secundario);"></i>
    <span style="font-size:.84rem;font-weight:600;">Editar {{ $ficha->numero_ficha }}</span>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#7f1d1d;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <strong><i class="bi bi-exclamation-triangle me-1"></i> Corrige los siguientes errores:</strong>
    <ul style="margin:.4rem 0 0 1.2rem;padding:0;">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('ortodoncia.update', $ficha) }}">
@csrf
@method('PUT')

<div class="card-sistema">

    {{-- Sección 1: Datos generales --}}
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-person-badge me-2"></i>Datos Generales
    </h5>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Paciente</label>
            <input type="text" value="{{ $ficha->paciente->nombre_completo }}" readonly
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-borde);color:var(--texto-secundario);">
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Ortodoncista <span style="color:#dc2626;">*</span></label>
            <select name="user_id" required style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                @foreach($ortodoncistas as $u)
                <option value="{{ $u->id }}" {{ old('user_id', $ficha->user_id) == $u->id?'selected':'' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Fecha inicio <span style="color:#dc2626;">*</span></label>
            <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $ficha->fecha_inicio->format('Y-m-d')) }}" required
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-2">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Duración est. (meses)</label>
            <input type="number" name="duracion_meses_estimada" value="{{ old('duracion_meses_estimada', $ficha->duracion_meses_estimada) }}" min="1" max="120"
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Tipo de ortodoncia</label>
            <select name="tipo_ortodoncia" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Seleccionar...</option>
                @foreach(['fija_metal'=>'Fija metálica','fija_estetica'=>'Fija estética','fija_autoligado'=>'Autoligado','removible'=>'Removible','alineadores'=>'Alineadores'] as $val=>$lbl)
                <option value="{{ $val }}" {{ old('tipo_ortodoncia',$ficha->tipo_ortodoncia)==$val?'selected':'' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Marca de brackets</label>
            <input type="text" name="marca_brackets" value="{{ old('marca_brackets', $ficha->marca_brackets) }}"
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Costo total</label>
            <input type="number" name="costo_total" value="{{ old('costo_total', $ficha->costo_total) }}" min="0" step="1000"
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
    </div>

    {{-- Análisis Facial --}}
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-person-circle me-2"></i>Análisis Facial
    </h5>
    <div style="background:var(--fondo-card-alt);border:1px solid var(--fondo-borde);border-radius:10px;padding:1.25rem;margin-bottom:1.5rem;">
        <div class="row g-3">
            @foreach([
                ['perfil','Perfil facial',['convexo'=>'Convexo','recto'=>'Recto','concavo'=>'Cóncavo']],
                ['simetria_facial','Simetría facial',['simetrica'=>'Simétrica','asimetrica'=>'Asimétrica']],
                ['biotipo_facial','Biotipo facial',['dolicofacial'=>'Dolicofacial','mesofacial'=>'Mesofacial','braquifacial'=>'Braquifacial']],
            ] as [$campo,$label,$opciones])
            <div class="col-md-4">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">{{ $label }}</label>
                <select name="{{ $campo }}" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
                    <option value="">Sin especificar</option>
                    @foreach($opciones as $v=>$l)
                    <option value="{{ $v }}" {{ old($campo,$ficha->{$campo})==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            @endforeach
            <div class="col-12">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Notas</label>
                <textarea name="analisis_facial_notas" rows="2" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;resize:vertical;">{{ old('analisis_facial_notas',$ficha->analisis_facial_notas) }}</textarea>
            </div>
        </div>
    </div>

    {{-- Análisis Dental --}}
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-clipboard2-data me-2"></i>Análisis Dental
    </h5>
    <div style="background:var(--fondo-card-alt);border:1px solid var(--fondo-borde);border-radius:10px;padding:1.25rem;margin-bottom:1.5rem;">
        <div class="row g-3">
            @foreach([
                ['clase_molar_derecha','Clase molar derecha'],
                ['clase_molar_izquierda','Clase molar izquierda'],
                ['clase_canina_derecha','Clase canina derecha'],
                ['clase_canina_izquierda','Clase canina izquierda'],
            ] as [$campo,$label])
            <div class="col-md-3">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">{{ $label }}</label>
                <select name="{{ $campo }}" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
                    <option value="">—</option>
                    @foreach(['clase_i'=>'Clase I','clase_ii'=>'Clase II','clase_iii'=>'Clase III'] as $v=>$l)
                    <option value="{{ $v }}" {{ old($campo,$ficha->{$campo})==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            @endforeach
            <div class="col-md-2">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Overjet (mm)</label>
                <input type="number" name="overjet" id="inp-overjet" value="{{ old('overjet',$ficha->overjet) }}" step="0.5"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Overbite (mm)</label>
                <input type="number" name="overbite" id="inp-overbite" value="{{ old('overbite',$ficha->overbite) }}" step="0.5"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
            </div>
            @foreach([
                ['linea_media_superior','Línea media superior'],
                ['linea_media_inferior','Línea media inferior'],
            ] as [$campo,$label])
            <div class="col-md-3">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">{{ $label }}</label>
                <select name="{{ $campo }}" id="{{ $campo==='linea_media_superior'?'sel-linea-media':'' }}" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
                    <option value="">—</option>
                    @foreach(['centrada'=>'Centrada','desviada_derecha'=>'Desviada a la derecha','desviada_izquierda'=>'Desviada a la izquierda'] as $v=>$l)
                    <option value="{{ $v }}" {{ old($campo,$ficha->{$campo})==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            @endforeach
            @foreach([
                ['apinamiento_superior','Apiñamiento superior'],
                ['apinamiento_inferior','Apiñamiento inferior'],
            ] as [$campo,$label])
            <div class="col-md-3">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">{{ $label }}</label>
                <select name="{{ $campo }}" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
                    <option value="">—</option>
                    @foreach(['ninguno'=>'Ninguno','leve'=>'Leve','moderado'=>'Moderado','severo'=>'Severo'] as $v=>$l)
                    <option value="{{ $v }}" {{ old($campo,$ficha->{$campo})==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            @endforeach
            <div class="col-12">
                <div style="display:flex;flex-wrap:wrap;gap:.75rem 1.5rem;">
                    @foreach([
                        ['espaciamiento_superior','Espaciamiento superior'],
                        ['espaciamiento_inferior','Espaciamiento inferior'],
                        ['mordida_cruzada_anterior','Mordida cruzada anterior'],
                        ['mordida_cruzada_posterior','Mordida cruzada posterior'],
                        ['mordida_abierta','Mordida abierta'],
                        ['mordida_profunda','Mordida profunda'],
                    ] as [$campo,$label])
                    <label style="display:flex;align-items:center;gap:.4rem;font-size:.83rem;cursor:pointer;">
                        <input type="checkbox" name="{{ $campo }}" value="1"
                               {{ old($campo, $ficha->{$campo})?'checked':'' }}
                               style="width:15px;height:15px;accent-color:var(--color-principal);">
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Odontograma --}}
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-grid-3x3-gap me-2"></i>Odontograma Ortodóntico
    </h5>
    <div style="margin-bottom:1.5rem;">
        @php
            $odontogramaEdit = old('odontograma_ortodoncia',
                $ficha->odontograma_ortodoncia ? json_encode($ficha->odontograma_ortodoncia) : '{}'
            );
        @endphp
        @include('ortodoncia._odontograma', ['odontogramaData' => $odontogramaEdit, 'inputName' => 'odontograma_ortodoncia'])
    </div>

    {{-- Arcos --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Arco superior inicial</label>
            <select name="arco_inicial_superior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Sin instalar</option>
                @foreach(['Niti 014','Niti 016','Niti 016x022','Niti 018x022','SS 016','SS 016x022','SS 017x025','SS 018x025','TMA','Otro'] as $arco)
                <option value="{{ $arco }}" {{ old('arco_inicial_superior',$ficha->arco_inicial_superior)==$arco?'selected':'' }}>{{ $arco }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Arco inferior inicial</label>
            <select name="arco_inicial_inferior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Sin instalar</option>
                @foreach(['Niti 014','Niti 016','Niti 016x022','Niti 018x022','SS 016','SS 016x022','SS 017x025','SS 018x025','TMA','Otro'] as $arco)
                <option value="{{ $arco }}" {{ old('arco_inicial_inferior',$ficha->arco_inicial_inferior)==$arco?'selected':'' }}>{{ $arco }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Diagnóstico y plan --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Diagnóstico</label>
            <textarea name="diagnostico" rows="3" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);resize:vertical;">{{ old('diagnostico',$ficha->diagnostico) }}</textarea>
        </div>
        <div class="col-12">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Plan de tratamiento</label>
            <textarea name="plan_tratamiento" rows="3" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);resize:vertical;">{{ old('plan_tratamiento',$ficha->plan_tratamiento) }}</textarea>
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Pronóstico</label>
            <select name="pronostico" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Sin especificar</option>
                @foreach(['excelente'=>'Excelente','bueno'=>'Bueno','reservado'=>'Reservado'] as $v=>$l)
                <option value="{{ $v }}" {{ old('pronostico',$ficha->pronostico)==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Extracciones indicadas</label>
            <input type="text" name="extracciones_indicadas"
                   value="{{ old('extracciones_indicadas', is_array($ficha->extracciones_indicadas) ? implode(', ', $ficha->extracciones_indicadas) : '') }}"
                   placeholder="Ej: 14, 24, 34, 44"
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-12">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Notas adicionales</label>
            <textarea name="notas" rows="2" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);resize:vertical;">{{ old('notas',$ficha->notas) }}</textarea>
        </div>
    </div>

    <div style="display:flex;gap:.75rem;justify-content:flex-end;padding-top:1rem;border-top:1px solid var(--fondo-borde);">
        <a href="{{ route('ortodoncia.show', $ficha) }}"
           style="padding:.55rem 1.25rem;border:1px solid var(--fondo-borde);border-radius:8px;color:var(--texto-secundario);text-decoration:none;font-size:.84rem;font-weight:500;">
            Cancelar
        </a>
        <button type="submit"
                style="padding:.55rem 1.5rem;background:var(--color-principal);color:white;border:none;border-radius:8px;font-size:.84rem;font-weight:600;cursor:pointer;box-shadow:0 2px 8px var(--sombra-principal);">
            <i class="bi bi-check-lg me-1"></i> Guardar cambios
        </button>
    </div>

</div>
</form>

@endsection
