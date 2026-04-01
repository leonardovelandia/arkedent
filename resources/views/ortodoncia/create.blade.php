@extends('layouts.app')
@section('titulo', 'Nueva Ficha Ortodóntica')

@section('contenido')

<div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <a href="{{ route('ortodoncia.index') }}" style="color:var(--texto-secundario);text-decoration:none;font-size:.84rem;display:inline-flex;align-items:center;gap:.25rem;">
        <i class="bi bi-arrow-left"></i> Ortodoncia
    </a>
    <i class="bi bi-chevron-right" style="font-size:.7rem;color:var(--texto-secundario);"></i>
    <span style="font-size:.84rem;color:var(--texto-principal);font-weight:600;">Nueva Ficha Ortodóntica</span>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#7f1d1d;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <strong><i class="bi bi-exclamation-triangle me-1"></i> Corrige los siguientes errores:</strong>
    <ul style="margin:.4rem 0 0 1.2rem;padding:0;">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('ortodoncia.store') }}" id="form-ficha-orto">
@csrf

<div class="card-sistema">

    {{-- ─── Sección 1: Datos generales ─────────────────────── --}}
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-person-badge me-2"></i>Datos Generales
    </h5>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Paciente <span style="color:#dc2626;">*</span></label>
            <select name="paciente_id" id="sel-paciente" required
                    style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Seleccionar paciente...</option>
                @foreach($pacientes as $p)
                <option value="{{ $p->id }}" {{ (old('paciente_id', $paciente?->id) == $p->id)?'selected':'' }}>
                    {{ $p->nombre_completo }} — {{ $p->numero_documento }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Ortodoncista <span style="color:#dc2626;">*</span></label>
            <select name="user_id" required style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                @foreach($ortodoncistas as $u)
                <option value="{{ $u->id }}" {{ old('user_id', auth()->id()) == $u->id?'selected':'' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Fecha inicio <span style="color:#dc2626;">*</span></label>
            <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', date('Y-m-d')) }}" required
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-2">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Duración est. (meses)</label>
            <input type="number" name="duracion_meses_estimada" value="{{ old('duracion_meses_estimada') }}" min="1" max="120"
                   placeholder="24" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Tipo de ortodoncia</label>
            <select name="tipo_ortodoncia" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Seleccionar...</option>
                <option value="fija_metal" {{ old('tipo_ortodoncia')=='fija_metal'?'selected':'' }}>Fija metálica</option>
                <option value="fija_estetica" {{ old('tipo_ortodoncia')=='fija_estetica'?'selected':'' }}>Fija estética</option>
                <option value="fija_autoligado" {{ old('tipo_ortodoncia')=='fija_autoligado'?'selected':'' }}>Autoligado</option>
                <option value="removible" {{ old('tipo_ortodoncia')=='removible'?'selected':'' }}>Removible</option>
                <option value="alineadores" {{ old('tipo_ortodoncia')=='alineadores'?'selected':'' }}>Alineadores</option>
            </select>
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Marca de brackets</label>
            <input type="text" name="marca_brackets" value="{{ old('marca_brackets') }}" placeholder="Ej: 3M, Ormco, Damon..."
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Costo total del tratamiento</label>
            <input type="number" name="costo_total" value="{{ old('costo_total') }}" min="0" step="1000" placeholder="0"
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
    </div>

    {{-- ─── Sección 2: Análisis Facial ──────────────────────── --}}
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-person-circle me-2"></i>Análisis Facial
    </h5>
    <div style="background:var(--fondo-card-alt);border:1px solid var(--fondo-borde);border-radius:10px;padding:1.25rem;margin-bottom:1.5rem;">
        <div class="row g-3">
            <div class="col-md-4">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Perfil facial</label>
                <select name="perfil" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
                    <option value="">Sin especificar</option>
                    <option value="convexo" {{ old('perfil')=='convexo'?'selected':'' }}>Convexo</option>
                    <option value="recto" {{ old('perfil')=='recto'?'selected':'' }}>Recto</option>
                    <option value="concavo" {{ old('perfil')=='concavo'?'selected':'' }}>Cóncavo</option>
                </select>
            </div>
            <div class="col-md-4">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Simetría facial</label>
                <select name="simetria_facial" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
                    <option value="">Sin especificar</option>
                    <option value="simetrica" {{ old('simetria_facial')=='simetrica'?'selected':'' }}>Simétrica</option>
                    <option value="asimetrica" {{ old('simetria_facial')=='asimetrica'?'selected':'' }}>Asimétrica</option>
                </select>
            </div>
            <div class="col-md-4">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Biotipo facial</label>
                <select name="biotipo_facial" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
                    <option value="">Sin especificar</option>
                    <option value="dolicofacial" {{ old('biotipo_facial')=='dolicofacial'?'selected':'' }}>Dolicofacial</option>
                    <option value="mesofacial" {{ old('biotipo_facial')=='mesofacial'?'selected':'' }}>Mesofacial</option>
                    <option value="braquifacial" {{ old('biotipo_facial')=='braquifacial'?'selected':'' }}>Braquifacial</option>
                </select>
            </div>
            <div class="col-12">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Notas del análisis facial</label>
                <textarea name="analisis_facial_notas" rows="2" placeholder="Observaciones faciales, descripción de asimetrías..."
                          style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;resize:vertical;">{{ old('analisis_facial_notas') }}</textarea>
            </div>
        </div>
    </div>

    {{-- ─── Sección 3: Análisis Dental ──────────────────────── --}}
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-clipboard2-data me-2"></i>Análisis Dental
    </h5>
    <div style="background:var(--fondo-card-alt);border:1px solid var(--fondo-borde);border-radius:10px;padding:1.25rem;margin-bottom:1.5rem;">
        <div class="row g-3">
            {{-- Clase molar / canina --}}
            <div class="col-md-3">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Clase molar derecha</label>
                <select name="clase_molar_derecha" id="sel-clase-molar" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
                    <option value="">—</option>
                    <option value="clase_i" {{ old('clase_molar_derecha')=='clase_i'?'selected':'' }}>Clase I</option>
                    <option value="clase_ii" {{ old('clase_molar_derecha')=='clase_ii'?'selected':'' }}>Clase II</option>
                    <option value="clase_iii" {{ old('clase_molar_derecha')=='clase_iii'?'selected':'' }}>Clase III</option>
                </select>
            </div>
            <div class="col-md-3">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Clase molar izquierda</label>
                <select name="clase_molar_izquierda" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
                    <option value="">—</option>
                    <option value="clase_i" {{ old('clase_molar_izquierda')=='clase_i'?'selected':'' }}>Clase I</option>
                    <option value="clase_ii" {{ old('clase_molar_izquierda')=='clase_ii'?'selected':'' }}>Clase II</option>
                    <option value="clase_iii" {{ old('clase_molar_izquierda')=='clase_iii'?'selected':'' }}>Clase III</option>
                </select>
            </div>
            <div class="col-md-3">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Clase canina derecha</label>
                <select name="clase_canina_derecha" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
                    <option value="">—</option>
                    <option value="clase_i" {{ old('clase_canina_derecha')=='clase_i'?'selected':'' }}>Clase I</option>
                    <option value="clase_ii" {{ old('clase_canina_derecha')=='clase_ii'?'selected':'' }}>Clase II</option>
                    <option value="clase_iii" {{ old('clase_canina_derecha')=='clase_iii'?'selected':'' }}>Clase III</option>
                </select>
            </div>
            <div class="col-md-3">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Clase canina izquierda</label>
                <select name="clase_canina_izquierda" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
                    <option value="">—</option>
                    <option value="clase_i" {{ old('clase_canina_izquierda')=='clase_i'?'selected':'' }}>Clase I</option>
                    <option value="clase_ii" {{ old('clase_canina_izquierda')=='clase_ii'?'selected':'' }}>Clase II</option>
                    <option value="clase_iii" {{ old('clase_canina_izquierda')=='clase_iii'?'selected':'' }}>Clase III</option>
                </select>
            </div>
            {{-- Overjet / Overbite --}}
            <div class="col-md-2">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Overjet (mm)</label>
                <input type="number" name="overjet" id="inp-overjet" value="{{ old('overjet') }}" step="0.5" min="-20" max="30" placeholder="0.0"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Overbite (mm)</label>
                <input type="number" name="overbite" id="inp-overbite" value="{{ old('overbite') }}" step="0.5" min="-20" max="30" placeholder="0.0"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
            </div>
            {{-- Línea media --}}
            <div class="col-md-3">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Línea media superior</label>
                <select name="linea_media_superior" id="sel-linea-media" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
                    <option value="">—</option>
                    <option value="centrada" {{ old('linea_media_superior')=='centrada'?'selected':'' }}>Centrada</option>
                    <option value="desviada_derecha" {{ old('linea_media_superior')=='desviada_derecha'?'selected':'' }}>Desviada a la derecha</option>
                    <option value="desviada_izquierda" {{ old('linea_media_superior')=='desviada_izquierda'?'selected':'' }}>Desviada a la izquierda</option>
                </select>
            </div>
            <div class="col-md-3">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Línea media inferior</label>
                <select name="linea_media_inferior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
                    <option value="">—</option>
                    <option value="centrada" {{ old('linea_media_inferior')=='centrada'?'selected':'' }}>Centrada</option>
                    <option value="desviada_derecha" {{ old('linea_media_inferior')=='desviada_derecha'?'selected':'' }}>Desviada a la derecha</option>
                    <option value="desviada_izquierda" {{ old('linea_media_inferior')=='desviada_izquierda'?'selected':'' }}>Desviada a la izquierda</option>
                </select>
            </div>
            <div class="col-md-2">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Desviación (mm)</label>
                <input type="number" name="desviacion_mm" value="{{ old('desviacion_mm') }}" step="0.5" min="0" max="20"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
            </div>
            {{-- Apiñamiento --}}
            <div class="col-md-3">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Apiñamiento superior</label>
                <select name="apinamiento_superior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
                    <option value="">—</option>
                    <option value="ninguno" {{ old('apinamiento_superior')=='ninguno'?'selected':'' }}>Ninguno</option>
                    <option value="leve" {{ old('apinamiento_superior')=='leve'?'selected':'' }}>Leve</option>
                    <option value="moderado" {{ old('apinamiento_superior')=='moderado'?'selected':'' }}>Moderado</option>
                    <option value="severo" {{ old('apinamiento_superior')=='severo'?'selected':'' }}>Severo</option>
                </select>
            </div>
            <div class="col-md-3">
                <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Apiñamiento inferior</label>
                <select name="apinamiento_inferior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:white;">
                    <option value="">—</option>
                    <option value="ninguno" {{ old('apinamiento_inferior')=='ninguno'?'selected':'' }}>Ninguno</option>
                    <option value="leve" {{ old('apinamiento_inferior')=='leve'?'selected':'' }}>Leve</option>
                    <option value="moderado" {{ old('apinamiento_inferior')=='moderado'?'selected':'' }}>Moderado</option>
                    <option value="severo" {{ old('apinamiento_inferior')=='severo'?'selected':'' }}>Severo</option>
                </select>
            </div>
            {{-- Checkboxes --}}
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
                        <input type="checkbox" name="{{ $campo }}" value="1" {{ old($campo)?'checked':'' }}
                               style="width:15px;height:15px;accent-color:var(--color-principal);">
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Sección 4: Odontograma de Ortodoncia ──────────── --}}
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-grid-3x3-gap me-2"></i>Odontograma Ortodóntico
    </h5>
    <div style="margin-bottom:1.5rem;">
        @include('ortodoncia._odontograma', ['odontogramaData' => old('odontograma_ortodoncia', '{}'), 'inputName' => 'odontograma_ortodoncia'])
    </div>

    {{-- ─── Sección 5: Arcos iniciales ─────────────────────── --}}
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-bezier me-2"></i>Arcos Iniciales
    </h5>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Arco superior inicial</label>
            <select name="arco_inicial_superior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Sin instalar</option>
                @foreach(['Niti 014','Niti 016','Niti 016x022','Niti 018x022','SS 016','SS 016x022','SS 017x025','SS 018x025','TMA','Otro'] as $arco)
                <option value="{{ $arco }}" {{ old('arco_inicial_superior')==$arco?'selected':'' }}>{{ $arco }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Arco inferior inicial</label>
            <select name="arco_inicial_inferior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Sin instalar</option>
                @foreach(['Niti 014','Niti 016','Niti 016x022','Niti 018x022','SS 016','SS 016x022','SS 017x025','SS 018x025','TMA','Otro'] as $arco)
                <option value="{{ $arco }}" {{ old('arco_inicial_inferior')==$arco?'selected':'' }}>{{ $arco }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ─── Sección 6: Diagnóstico y plan ──────────────────── --}}
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-journal-text me-2"></i>Diagnóstico y Plan de Tratamiento
    </h5>
    <div class="row g-3 mb-4">
        <div class="col-12">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Diagnóstico ortodóntico</label>
            <textarea name="diagnostico" rows="3" placeholder="Describe el diagnóstico principal..."
                      style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);resize:vertical;">{{ old('diagnostico') }}</textarea>
        </div>
        <div class="col-12">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Plan de tratamiento</label>
            <textarea name="plan_tratamiento" rows="3" placeholder="Describe el plan de tratamiento paso a paso..."
                      style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);resize:vertical;">{{ old('plan_tratamiento') }}</textarea>
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Pronóstico</label>
            <select name="pronostico" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Sin especificar</option>
                <option value="excelente" {{ old('pronostico')=='excelente'?'selected':'' }}>Excelente</option>
                <option value="bueno" {{ old('pronostico')=='bueno'?'selected':'' }}>Bueno</option>
                <option value="reservado" {{ old('pronostico')=='reservado'?'selected':'' }}>Reservado</option>
            </select>
        </div>
        <div class="col-md-5">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Extracciones indicadas (dientes separados por coma)</label>
            <input type="text" name="extracciones_indicadas"
                   value="{{ old('extracciones_indicadas', is_array(old('extracciones_indicadas')) ? implode(', ', old('extracciones_indicadas')) : '') }}"
                   placeholder="Ej: 14, 24, 34, 44"
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
    </div>

    {{-- Botones --}}
    <div style="display:flex;gap:.75rem;justify-content:flex-end;padding-top:1rem;border-top:1px solid var(--fondo-borde);">
        <a href="{{ route('ortodoncia.index') }}"
           style="padding:.55rem 1.25rem;border:1px solid var(--fondo-borde);border-radius:8px;color:var(--texto-secundario);text-decoration:none;font-size:.84rem;font-weight:500;">
            Cancelar
        </a>
        <button type="submit"
                style="padding:.55rem 1.5rem;background:var(--color-principal);color:white;border:none;border-radius:8px;font-size:.84rem;font-weight:600;cursor:pointer;box-shadow:0 2px 8px var(--sombra-principal);">
            <i class="bi bi-check-lg me-1"></i> Guardar Ficha Ortodóntica
        </button>
    </div>

</div>
</form>

@endsection
