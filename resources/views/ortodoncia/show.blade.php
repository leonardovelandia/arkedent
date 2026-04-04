@extends('layouts.app')
@section('titulo', 'Ficha ' . $ficha->numero_ficha)

@push('estilos')
<style>
    /* ── Aurora Glass: tabs ── */
    body[data-ui="glass"] .orto-tab { color:rgba(255,255,255,0.70) !important; }
    body[data-ui="glass"] .orto-tab.tab-activo,
    body[data-ui="glass"] .orto-tab[style*="color:var(--color-principal)"] { color:rgba(0,234,255,0.95) !important; border-bottom-color:rgba(0,234,255,0.80) !important; }
    /* ── Aurora Glass: card-sistema data panels ── */
    body[data-ui="glass"] .card-sistema h6 { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .card-sistema [style*="background:var(--fondo-card-alt)"],
    body[data-ui="glass"] .card-sistema [style*="background: var(--fondo-card-alt)"] {
        background:rgba(0,0,0,0.20) !important;
        border-color:rgba(0,234,255,0.20) !important;
    }
    /* menu estado dropdown */
    body[data-ui="glass"] #menu-estado {
        background:rgba(5,40,55,0.92) !important;
        backdrop-filter:blur(20px) saturate(160%) !important;
        border:1px solid rgba(0,234,255,0.30) !important;
        box-shadow:0 8px 24px rgba(0,0,0,.35) !important;
    }
    body[data-ui="glass"] #menu-estado div[style*="font-size:.68rem"] { color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] #menu-estado button { color:rgba(255,255,255,0.85) !important; }
</style>
@endpush

@section('contenido')

{{-- Breadcrumb --}}
<div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1.25rem;font-size:.82rem;flex-wrap:wrap;">
    <a href="{{ route('ortodoncia.index') }}" style="color:var(--texto-secundario);text-decoration:none;"><i class="bi bi-braces me-1"></i>Ortodoncia</a>
    <i class="bi bi-chevron-right" style="font-size:.65rem;color:var(--texto-secundario);"></i>
    <span style="color:var(--texto-principal);font-weight:600;">{{ $ficha->numero_ficha }}</span>
</div>

@if(session('exito'))
<div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;border-radius:8px;padding:.7rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <i class="bi bi-check-circle me-1"></i> {{ session('exito') }}
</div>
@endif

{{-- Header de la ficha --}}
@php
    $estadoBadges = [
        'diagnostico'=> ['#dbeafe','#1e40af'],
        'activo'     => ['#d1fae5','#065f46'],
        'retencion'  => ['#fef3c7','#92400e'],
        'finalizado' => ['#f3f4f6','#374151'],
        'cancelado'  => ['#fee2e2','#7f1d1d'],
    ];
    $bc = $estadoBadges[$ficha->estado] ?? ['#f3f4f6','#374151'];
    $progreso = $ficha->progreso;
@endphp

<div class="card-sistema" style="margin-bottom:1.25rem;">
    <div style="display:flex;flex-wrap:wrap;gap:1rem;align-items:flex-start;justify-content:space-between;">
        <div style="flex:1;min-width:260px;">
            <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-bottom:.5rem;">
                <span style="font-family:monospace;font-size:.85rem;font-weight:700;color:var(--color-principal);background:var(--color-muy-claro);padding:.2rem .6rem;border-radius:6px;">
                    {{ $ficha->numero_ficha }}
                </span>
                @if($ficha->tipo_ortodoncia)
                <span style="background:var(--fondo-card-alt);color:var(--texto-secundario);border-radius:20px;padding:.18rem .65rem;font-size:.72rem;font-weight:600;border:1px solid var(--fondo-borde);">
                    {{ $ficha->tipo_ortodoncia_label }}
                </span>
                @endif
                <span style="background:{{ $bc[0] }};color:{{ $bc[1] }};border-radius:20px;padding:.18rem .65rem;font-size:.72rem;font-weight:700;">
                    {{ $ficha->estado_label }}
                </span>
            </div>
            <h2 style="margin:0 0 .25rem 0;font-size:1.2rem;font-weight:700;">
                <a href="{{ route('pacientes.show', $ficha->paciente) }}" style="color:var(--texto-principal);text-decoration:none;">
                    {{ $ficha->paciente->nombre_completo }}
                </a>
            </h2>
            <p style="margin:0;font-size:.82rem;color:var(--texto-secundario);">
                {{ $ficha->paciente->numero_documento }} &nbsp;·&nbsp;
                Inicio: {{ $ficha->fecha_inicio->format('d/m/Y') }}
                @if($ficha->duracion_meses_estimada)
                &nbsp;·&nbsp; Duración estimada: {{ $ficha->duracion_meses_estimada }} meses
                @endif
                @if($ficha->ortodoncista)
                &nbsp;·&nbsp; Dr(a). {{ $ficha->ortodoncista->name }}
                @endif
            </p>
            {{-- Barra de progreso --}}
            <div style="margin-top:.75rem;display:flex;align-items:center;gap:.75rem;">
                <div style="flex:1;background:var(--fondo-borde);border-radius:20px;height:8px;">
                    <div style="width:{{ $progreso }}%;background:var(--color-principal);border-radius:20px;height:8px;transition:width .5s;"></div>
                </div>
                <span style="font-size:.78rem;font-weight:700;color:var(--color-principal);min-width:36px;">{{ $progreso }}%</span>
            </div>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:.5rem;align-items:flex-start;">
            @if(in_array($ficha->estado, ['diagnostico','activo']))
            <a href="{{ route('controles.create', ['ficha_ortodontica_id' => $ficha->id]) }}"
               style="background:var(--color-principal);color:white;text-decoration:none;padding:.45rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;display:inline-flex;align-items:center;gap:.35rem;">
                <i class="bi bi-plus-circle"></i> Nuevo Control
            </a>
            @endif
            @if($ficha->estado === 'activo' && !$ficha->retencion)
            <a href="{{ route('ortodoncia.retencion.create', $ficha) }}"
               style="background:#fef3c7;color:#92400e;text-decoration:none;padding:.45rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;">
                <i class="bi bi-shield-check"></i> Iniciar Retención
            </a>
            @endif
            <a href="{{ route('ortodoncia.edit', $ficha) }}"
               style="background:var(--fondo-card-alt);color:var(--texto-principal);text-decoration:none;padding:.45rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;border:1px solid var(--fondo-borde);">
                <i class="bi bi-pencil"></i> Editar
            </a>
            {{-- Cambiar estado --}}
            <div style="position:relative;">
                <button onclick="document.getElementById('menu-estado').style.display = document.getElementById('menu-estado').style.display==='none'?'block':'none'"
                        style="background:var(--fondo-borde);color:var(--texto-principal);border:none;padding:.45rem .75rem;border-radius:8px;font-size:.82rem;cursor:pointer;">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <div id="menu-estado" style="display:none;position:absolute;right:0;top:calc(100% + 4px);background:white;border:1px solid var(--fondo-borde);border-radius:10px;padding:.5rem;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:999;min-width:180px;">
                    <div style="font-size:.68rem;color:var(--texto-secundario);font-weight:700;text-transform:uppercase;padding:.2rem .5rem;letter-spacing:.06em;">Cambiar estado</div>
                    @foreach(['diagnostico'=>'Diagnóstico','activo'=>'En tratamiento','retencion'=>'Retención','finalizado'=>'Finalizado','cancelado'=>'Cancelado'] as $est => $label)
                    @if($est !== $ficha->estado)
                    <form method="POST" action="{{ route('ortodoncia.cambiar-estado', $ficha) }}">
                        @csrf
                        <input type="hidden" name="estado" value="{{ $est }}">
                        <button type="submit" style="width:100%;text-align:left;background:transparent;border:none;padding:.35rem .5rem;border-radius:6px;font-size:.82rem;cursor:pointer;color:#333;"
                                onmouseover="this.style.background='var(--color-muy-claro)'" onmouseout="this.style.background='transparent'">
                            {{ $label }}
                        </button>
                    </form>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div style="display:flex;gap:.25rem;border-bottom:2px solid var(--fondo-borde);margin-bottom:1.25rem;flex-wrap:wrap;">
    @foreach([
        ['ficha',      'bi-journal-medical',     'Ficha'],
        ['controles',  'bi-calendar-check',       "Controles ({$controles->count()})"],
        ['odontograma','bi-grid-3x3-gap',          'Odontograma actual'],
        ['retencion',  'bi-shield-check',          'Retención'],
    ] as [$tabId, $icono, $label])
    <button class="orto-tab" data-tab="{{ $tabId }}" onclick="cambiarTabOrto('{{ $tabId }}')"
            style="padding:.5rem 1rem;border:none;background:transparent;font-size:.82rem;font-weight:500;color:var(--texto-secundario);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;transition:all .15s;display:inline-flex;align-items:center;gap:.35rem;">
        <i class="bi {{ $icono }}"></i> {{ $label }}
    </button>
    @endforeach
</div>

{{-- Tab: Ficha diagnóstica --}}
<div id="tab-orto-ficha" class="orto-panel">
    <div class="row g-3">
        {{-- Análisis Facial --}}
        <div class="col-md-6">
            <div class="card-sistema">
                <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.05em;margin-bottom:1rem;"><i class="bi bi-person-circle me-1"></i>Análisis Facial</h6>
                <div class="row g-2">
                    <div class="col-6">
                        <div style="font-size:.72rem;color:var(--texto-secundario);">Perfil</div>
                        <div style="font-size:.85rem;font-weight:600;">{{ $ficha->perfil ? ucfirst($ficha->perfil) : '—' }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:.72rem;color:var(--texto-secundario);">Simetría</div>
                        <div style="font-size:.85rem;font-weight:600;">{{ $ficha->simetria_facial ? ucfirst($ficha->simetria_facial) : '—' }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:.72rem;color:var(--texto-secundario);">Biotipo</div>
                        <div style="font-size:.85rem;font-weight:600;">{{ $ficha->biotipo_facial ? ucfirst($ficha->biotipo_facial) : '—' }}</div>
                    </div>
                    @if($ficha->analisis_facial_notas)
                    <div class="col-12 mt-1">
                        <div style="font-size:.72rem;color:var(--texto-secundario);">Notas</div>
                        <div style="font-size:.83rem;">{{ $ficha->analisis_facial_notas }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Análisis Dental --}}
        <div class="col-md-6">
            <div class="card-sistema">
                <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.05em;margin-bottom:1rem;"><i class="bi bi-clipboard2-data me-1"></i>Análisis Dental</h6>
                <div class="row g-2">
                    <div class="col-6">
                        <div style="font-size:.72rem;color:var(--texto-secundario);">Clase molar D/I</div>
                        <div style="font-size:.83rem;font-weight:600;">{{ $ficha->clase_molar_label }}</div>
                    </div>
                    <div class="col-3">
                        <div style="font-size:.72rem;color:var(--texto-secundario);">Overjet</div>
                        <div style="font-size:.85rem;font-weight:600;">{{ $ficha->overjet ? $ficha->overjet.' mm' : '—' }}</div>
                    </div>
                    <div class="col-3">
                        <div style="font-size:.72rem;color:var(--texto-secundario);">Overbite</div>
                        <div style="font-size:.85rem;font-weight:600;">{{ $ficha->overbite ? $ficha->overbite.' mm' : '—' }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:.72rem;color:var(--texto-secundario);">Apiñamiento Sup/Inf</div>
                        <div style="font-size:.83rem;font-weight:600;">
                            {{ $ficha->apinamiento_superior ? ucfirst($ficha->apinamiento_superior) : '—' }} /
                            {{ $ficha->apinamiento_inferior ? ucfirst($ficha->apinamiento_inferior) : '—' }}
                        </div>
                    </div>
                    <div class="col-12">
                        <div style="display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.25rem;">
                            @foreach([
                                [$ficha->mordida_cruzada_anterior,'Mord. cruzada ant.'],
                                [$ficha->mordida_cruzada_posterior,'Mord. cruzada post.'],
                                [$ficha->mordida_abierta,'Mordida abierta'],
                                [$ficha->mordida_profunda,'Mordida profunda'],
                                [$ficha->espaciamiento_superior,'Espac. superior'],
                                [$ficha->espaciamiento_inferior,'Espac. inferior'],
                            ] as [$val, $nombre])
                            @if($val)
                            <span style="background:var(--color-muy-claro);color:var(--color-principal);border-radius:20px;padding:.1rem .5rem;font-size:.68rem;font-weight:600;">{{ $nombre }}</span>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Diagnóstico y plan --}}
        @if($ficha->diagnostico || $ficha->plan_tratamiento)
        <div class="col-12">
            <div class="card-sistema">
                <div class="row g-3">
                    @if($ficha->diagnostico)
                    <div class="col-md-6">
                        <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.05em;margin-bottom:.5rem;">Diagnóstico</h6>
                        <p style="font-size:.84rem;margin:0;white-space:pre-line;">{{ $ficha->diagnostico }}</p>
                    </div>
                    @endif
                    @if($ficha->plan_tratamiento)
                    <div class="col-md-6">
                        <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.05em;margin-bottom:.5rem;">Plan de tratamiento</h6>
                        <p style="font-size:.84rem;margin:0;white-space:pre-line;">{{ $ficha->plan_tratamiento }}</p>
                    </div>
                    @endif
                </div>
                @if($ficha->pronostico || $ficha->costo_total || ($ficha->extracciones_indicadas && count($ficha->extracciones_indicadas)))
                <div style="margin-top:.75rem;padding-top:.75rem;border-top:1px solid var(--fondo-borde);display:flex;gap:1.5rem;flex-wrap:wrap;">
                    @if($ficha->pronostico)
                    <div>
                        <div style="font-size:.68rem;color:var(--texto-secundario);">Pronóstico</div>
                        <div style="font-size:.84rem;font-weight:600;">{{ ucfirst($ficha->pronostico) }}</div>
                    </div>
                    @endif
                    @if($ficha->costo_total)
                    <div>
                        <div style="font-size:.68rem;color:var(--texto-secundario);">Costo total</div>
                        <div style="font-size:.84rem;font-weight:600;">$ {{ number_format($ficha->costo_total, 0, ',', '.') }}</div>
                    </div>
                    @endif
                    @if($ficha->extracciones_indicadas && count($ficha->extracciones_indicadas))
                    <div>
                        <div style="font-size:.68rem;color:var(--texto-secundario);">Extracciones indicadas</div>
                        <div style="font-size:.84rem;font-weight:600;">Dientes: {{ implode(', ', $ficha->extracciones_indicadas) }}</div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Tab: Controles --}}
<div id="tab-orto-controles" class="orto-panel" style="display:none;">
    @if($controles->isEmpty())
    <div class="card-sistema" style="text-align:center;padding:2.5rem 1rem;color:var(--texto-secundario);">
        <i class="bi bi-calendar-x" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.3;"></i>
        <p style="margin:0 0 1rem 0;font-size:.9rem;">No hay controles registrados</p>
        @if(in_array($ficha->estado, ['diagnostico','activo']))
        <a href="{{ route('controles.create', ['ficha_ortodontica_id' => $ficha->id]) }}"
           style="background:var(--color-principal);color:white;text-decoration:none;padding:.45rem 1.1rem;border-radius:8px;font-size:.82rem;font-weight:600;">
            <i class="bi bi-plus-circle me-1"></i> Registrar primer control
        </a>
        @endif
    </div>
    @else
    <div class="card-sistema">
        {{-- Timeline vertical --}}
        <div style="position:relative;padding-left:2rem;">
            <div style="position:absolute;left:.65rem;top:0;bottom:0;width:2px;background:var(--fondo-borde);"></div>

            @foreach($controles as $idx => $ctrl)
            @php
                $esUltimo = $idx === 0;
                $tiposArco = ['niti'=>'Niti','acero'=>'SS','tma'=>'TMA','fibra_vidrio'=>'Fibra','ninguno'=>'Ninguno'];
                $tipoArcoSup = $tiposArco[$ctrl->tipo_arco_superior] ?? '';
                $tipoArcoInf = $tiposArco[$ctrl->tipo_arco_inferior] ?? '';
            @endphp
            <div style="position:relative;margin-bottom:1.5rem;">
                {{-- Dot --}}
                <div style="position:absolute;left:-2rem;top:.3rem;width:14px;height:14px;border-radius:50%;background:{{ $esUltimo ? 'var(--color-principal)' : 'var(--fondo-borde)' }};border:2px solid {{ $esUltimo ? 'var(--color-claro)' : '#ccc' }};box-shadow:{{ $esUltimo ? '0 0 0 4px var(--color-muy-claro)' : 'none' }};"></div>

                <div style="background:{{ $esUltimo ? 'var(--color-muy-claro)' : 'var(--fondo-app)' }};border:1px solid {{ $esUltimo ? 'var(--color-claro)' : 'var(--fondo-borde)' }};border-radius:10px;padding:1rem 1.25rem;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.5rem;margin-bottom:.6rem;">
                        <div>
                            <span style="font-weight:700;font-size:.9rem;color:var(--texto-principal);">
                                Sesión #{{ $ctrl->numero_sesion }}
                            </span>
                            @if($esUltimo)
                            <span style="background:var(--color-principal);color:white;border-radius:20px;padding:.1rem .5rem;font-size:.65rem;font-weight:700;margin-left:.4rem;">ÚLTIMA</span>
                            @endif
                            <span style="color:var(--texto-secundario);font-size:.8rem;margin-left:.5rem;">
                                — {{ $ctrl->fecha_control->format('d/m/Y') }}
                            </span>
                        </div>
                        <div style="display:flex;gap:.35rem;">
                            <a href="{{ route('controles.show', $ctrl) }}" style="background:var(--fondo-borde);color:var(--texto-principal);text-decoration:none;padding:.25rem .6rem;border-radius:6px;font-size:.75rem;">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('controles.edit', $ctrl) }}" style="background:var(--fondo-borde);color:var(--texto-principal);text-decoration:none;padding:.25rem .6rem;border-radius:6px;font-size:.75rem;">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </div>

                    <div style="display:flex;flex-wrap:wrap;gap:.75rem 1.5rem;font-size:.82rem;">
                        @if($ctrl->tipo_arco_superior || $ctrl->calibre_superior)
                        <div>
                            <span style="color:var(--texto-secundario);font-size:.72rem;">Arco Sup:</span>
                            <strong>{{ $tipoArcoSup }} {{ $ctrl->calibre_superior }}</strong>
                        </div>
                        @endif
                        @if($ctrl->tipo_arco_inferior || $ctrl->calibre_inferior)
                        <div>
                            <span style="color:var(--texto-secundario);font-size:.72rem;">Arco Inf:</span>
                            <strong>{{ $tipoArcoInf }} {{ $ctrl->calibre_inferior }}</strong>
                        </div>
                        @endif
                        @if($ctrl->ligadura_superior)
                        <div>
                            <span style="color:var(--texto-secundario);font-size:.72rem;">Ligadura:</span>
                            <strong>{{ ucfirst($ctrl->ligadura_superior) }}{{ $ctrl->color_ligadura ? ' - '.$ctrl->color_ligadura : '' }}</strong>
                        </div>
                        @endif
                        @if($ctrl->elasticos)
                        <div>
                            <span style="background:#eff6ff;color:#1e40af;border-radius:20px;padding:.1rem .5rem;font-size:.7rem;font-weight:600;">
                                <i class="bi bi-circle"></i> {{ $ctrl->tipo_elasticos ?? 'Elásticos' }}
                            </span>
                        </div>
                        @endif
                        @if($ctrl->progreso_porcentaje !== null)
                        <div style="display:flex;align-items:center;gap:.4rem;">
                            <div style="background:var(--fondo-borde);border-radius:20px;height:6px;width:60px;">
                                <div style="width:{{ $ctrl->progreso_porcentaje }}%;background:var(--color-principal);border-radius:20px;height:6px;"></div>
                            </div>
                            <span style="font-weight:700;color:var(--color-principal);font-size:.78rem;">{{ $ctrl->progreso_porcentaje }}%</span>
                        </div>
                        @endif
                    </div>

                    @if($ctrl->observaciones)
                    <p style="margin:.5rem 0 0;font-size:.8rem;color:var(--texto-secundario);font-style:italic;">
                        {{ Str::limit($ctrl->observaciones, 160) }}
                    </p>
                    @endif

                    @if($ctrl->proxima_cita_semanas)
                    <p style="margin:.4rem 0 0;font-size:.75rem;color:var(--color-principal);">
                        <i class="bi bi-calendar2-plus me-1"></i> Próxima cita en {{ $ctrl->proxima_cita_semanas }} semana(s)
                    </p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Tab: Odontograma actual --}}
<div id="tab-orto-odontograma" class="orto-panel" style="display:none;">
    <div class="card-sistema">
        <h6 style="font-size:.8rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.05em;margin-bottom:1rem;">
            Estado actual del odontograma
            @if($ficha->ultimoControl)
            <span style="font-weight:400;color:var(--texto-secundario);text-transform:none;font-size:.75rem;">
                — según sesión #{{ $ficha->ultimoControl->numero_sesion }} ({{ $ficha->ultimoControl->fecha_control->format('d/m/Y') }})
            </span>
            @endif
        </h6>
        @php
            $odontogramaActual = $ficha->odontograma_ortodoncia
                ? json_encode($ficha->odontograma_ortodoncia)
                : '{}';
        @endphp
        @include('ortodoncia._odontograma', [
            'odontogramaData' => $odontogramaActual,
            'inputName'       => '_odontograma_readonly',
            'readonly'        => true,
        ])
    </div>
</div>

{{-- Tab: Retención --}}
<div id="tab-orto-retencion" class="orto-panel" style="display:none;">
    @if($ficha->retencion)
    <div class="card-sistema">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <h6 style="margin:0;font-size:.8rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.05em;">
                <i class="bi bi-shield-check me-1"></i> Fase de Retención
            </h6>
            <a href="{{ route('retencion.edit', $ficha->retencion) }}"
               style="color:var(--color-principal);font-size:.8rem;text-decoration:none;">
                <i class="bi bi-pencil me-1"></i> Editar
            </a>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div style="font-size:.72rem;color:var(--texto-secundario);">Retiro de brackets</div>
                <div style="font-size:.85rem;font-weight:600;">{{ $ficha->retencion->fecha_retiro_brackets?->format('d/m/Y') ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div style="font-size:.72rem;color:var(--texto-secundario);">Retenedor superior</div>
                <div style="font-size:.85rem;font-weight:600;">{{ $ficha->retencion->retenedor_superior_label }}</div>
            </div>
            <div class="col-md-4">
                <div style="font-size:.72rem;color:var(--texto-secundario);">Retenedor inferior</div>
                <div style="font-size:.85rem;font-weight:600;">{{ $ficha->retencion->retenedor_inferior_label }}</div>
            </div>
            <div class="col-md-4">
                <div style="font-size:.72rem;color:var(--texto-secundario);">Entrega retenedor</div>
                <div style="font-size:.85rem;font-weight:600;">{{ $ficha->retencion->fecha_entrega_retenedor?->format('d/m/Y') ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div style="font-size:.72rem;color:var(--texto-secundario);">Duración retención</div>
                <div style="font-size:.85rem;font-weight:600;">{{ $ficha->retencion->duracion_retencion_meses ? $ficha->retencion->duracion_retencion_meses.' meses' : '—' }}</div>
            </div>
            <div class="col-md-4">
                <div style="font-size:.72rem;color:var(--texto-secundario);">Estado retención</div>
                <div style="font-size:.85rem;font-weight:600;">{{ $ficha->retencion->estado_label }}</div>
            </div>
            @if($ficha->retencion->instrucciones_uso)
            <div class="col-12">
                <div style="font-size:.72rem;color:var(--texto-secundario);">Instrucciones de uso</div>
                <div style="font-size:.83rem;white-space:pre-line;">{{ $ficha->retencion->instrucciones_uso }}</div>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="card-sistema" style="text-align:center;padding:2.5rem 1rem;color:var(--texto-secundario);">
        <i class="bi bi-shield" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.3;"></i>
        <p style="margin:0 0 1rem 0;font-size:.9rem;">No se ha registrado fase de retención</p>
        @if($ficha->estado === 'activo')
        <a href="{{ route('ortodoncia.retencion.create', $ficha) }}"
           style="background:var(--color-principal);color:white;text-decoration:none;padding:.45rem 1.1rem;border-radius:8px;font-size:.82rem;font-weight:600;">
            <i class="bi bi-plus-circle me-1"></i> Registrar retención
        </a>
        @endif
    </div>
    @endif
</div>

@push('scripts')
<script>
function cambiarTabOrto(id) {
    document.querySelectorAll('.orto-tab').forEach(t => {
        const activo = t.dataset.tab === id;
        t.style.color         = activo ? 'var(--color-principal)' : 'var(--texto-secundario)';
        t.style.borderBottom  = activo ? '2px solid var(--color-principal)' : '2px solid transparent';
        t.style.fontWeight    = activo ? '600' : '500';
    });
    document.querySelectorAll('.orto-panel').forEach(p => p.style.display = 'none');
    const panel = document.getElementById('tab-orto-' + id);
    if (panel) panel.style.display = 'block';
}

// Activar primera tab al cargar
document.addEventListener('DOMContentLoaded', () => cambiarTabOrto('ficha'));

// Cerrar menú estado al hacer clic fuera
document.addEventListener('click', function(e) {
    const menu = document.getElementById('menu-estado');
    if (menu && !menu.contains(e.target) && !e.target.closest('[onclick*="menu-estado"]')) {
        menu.style.display = 'none';
    }
});
</script>
@endpush

@endsection
