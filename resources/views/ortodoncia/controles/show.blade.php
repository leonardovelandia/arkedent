@extends('layouts.app')
@section('titulo', 'Control ' . $control->numero_control)

@section('contenido')

<div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <a href="{{ route('ortodoncia.show', $control->ficha_ortodontica_id) }}" style="color:var(--texto-secundario);text-decoration:none;font-size:.84rem;">
        <i class="bi bi-arrow-left"></i> {{ $control->fichaOrtodoncia->numero_ficha }}
    </a>
    <i class="bi bi-chevron-right" style="font-size:.7rem;color:var(--texto-secundario);"></i>
    <span style="font-size:.84rem;font-weight:600;">Control — Sesión #{{ $control->numero_sesion }}</span>
</div>

<div class="card-sistema">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.75rem;margin-bottom:1.25rem;">
        <div>
            <span style="font-family:monospace;font-size:.82rem;font-weight:700;color:var(--color-principal);background:var(--color-muy-claro);padding:.2rem .6rem;border-radius:6px;">
                {{ $control->numero_control }}
            </span>
            <h3 style="margin:.5rem 0 .25rem 0;font-size:1.1rem;font-weight:700;">
                Sesión #{{ $control->numero_sesion }} — {{ $control->fecha_control->format('d \d\e F \d\e Y') }}
            </h3>
            <p style="margin:0;font-size:.83rem;color:var(--texto-secundario);">
                {{ $control->fichaOrtodoncia->paciente->nombre_completo }} &nbsp;·&nbsp;
                Dr(a). {{ $control->ortodoncista->name }}
            </p>
        </div>
        <a href="{{ route('controles.edit', $control) }}"
           style="background:var(--fondo-card-alt);color:var(--texto-principal);text-decoration:none;padding:.45rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;border:1px solid var(--fondo-borde);">
            <i class="bi bi-pencil"></i> Editar
        </a>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <h6 style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.05em;margin-bottom:.75rem;">Arcos instalados</h6>
            <div class="row g-2">
                @foreach([
                    ['Superior','tipo_arco_superior','calibre_superior','ligadura_superior'],
                    ['Inferior','tipo_arco_inferior','calibre_inferior','ligadura_inferior'],
                ] as [$arcada, $tipo, $calibre, $ligadura])
                <div class="col-6">
                    <div style="background:var(--fondo-card-alt);border-radius:8px;padding:.75rem;border:1px solid var(--fondo-borde);">
                        <div style="font-size:.68rem;color:var(--texto-secundario);font-weight:700;text-transform:uppercase;margin-bottom:.3rem;">{{ $arcada }}</div>
                        <div style="font-size:.9rem;font-weight:600;">{{ strtoupper($control->{$tipo} ?? '—') }} {{ $control->{$calibre} }}</div>
                        <div style="font-size:.75rem;color:var(--texto-secundario);">Lig: {{ $control->{$ligadura} ? ucfirst($control->{$ligadura}) : '—' }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="col-md-3">
            <h6 style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.05em;margin-bottom:.75rem;">Progreso</h6>
            @if($control->progreso_porcentaje !== null)
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;">
                <div style="flex:1;background:var(--fondo-borde);border-radius:20px;height:10px;">
                    <div style="width:{{ $control->progreso_porcentaje }}%;background:var(--color-principal);border-radius:20px;height:10px;"></div>
                </div>
                <span style="font-size:1rem;font-weight:800;color:var(--color-principal);">{{ $control->progreso_porcentaje }}%</span>
            </div>
            @endif
            @if($control->color_ligadura)
            <div style="font-size:.8rem;"><span style="color:var(--texto-secundario);">Color ligadura:</span> <strong>{{ $control->color_ligadura }}</strong></div>
            @endif
            @if($control->elasticos)
            <div style="font-size:.8rem;margin-top:.3rem;">
                <span style="background:#eff6ff;color:#1e40af;border-radius:20px;padding:.12rem .5rem;font-size:.72rem;font-weight:600;">
                    <i class="bi bi-circle"></i> {{ $control->tipo_elasticos ?? 'Elásticos' }}
                </span>
            </div>
            @endif
        </div>
        <div class="col-md-3">
            @if($control->proxima_cita_semanas)
            <h6 style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.05em;margin-bottom:.75rem;">Próxima cita</h6>
            <div style="font-size:1.1rem;font-weight:700;color:var(--color-principal);">{{ $control->proxima_cita_semanas }} semanas</div>
            @endif
        </div>
        @if($control->observaciones)
        <div class="col-12">
            <h6 style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.05em;margin-bottom:.5rem;">Observaciones</h6>
            <p style="font-size:.84rem;margin:0;white-space:pre-line;background:var(--fondo-card-alt);border-radius:8px;padding:.75rem;border:1px solid var(--fondo-borde);">{{ $control->observaciones }}</p>
        </div>
        @endif
        @if($control->indicaciones_paciente)
        <div class="col-12">
            <h6 style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.05em;margin-bottom:.5rem;">Indicaciones al paciente</h6>
            <p style="font-size:.84rem;margin:0;white-space:pre-line;background:#fffbeb;border-radius:8px;padding:.75rem;border:1px solid #fde68a;">{{ $control->indicaciones_paciente }}</p>
        </div>
        @endif
        @if($control->odontograma_sesion)
        <div class="col-12">
            <h6 style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.05em;margin-bottom:.75rem;">Odontograma de la sesión</h6>
            @include('ortodoncia._odontograma', [
                'odontogramaData' => json_encode($control->odontograma_sesion),
                'inputName'       => '_readonly_sesion',
                'readonly'        => true,
            ])
        </div>
        @endif
    </div>
</div>

@endsection
