@extends('layouts.app')
@section('titulo', 'Receta — ' . $receta->numero_receta)

@section('contenido')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:.75rem;">
    <div style="display:flex;align-items:center;gap:.75rem;">
        <a href="{{ route('recetas.index') }}" style="color:var(--texto-secundario);text-decoration:none;font-size:.84rem;">
            <i class="bi bi-arrow-left"></i> Recetas
        </a>
        <i class="bi bi-chevron-right" style="font-size:.7rem;color:var(--texto-secundario);"></i>
        <span style="font-size:.84rem;font-weight:600;">{{ $receta->numero_receta }}</span>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
        <a href="{{ route('recetas.pdf', $receta) }}" target="_blank"
           style="display:inline-flex;align-items:center;gap:.3rem;padding:.45rem .9rem;background:#f0fdf4;color:#16a34a;border:1px solid #86efac;border-radius:8px;text-decoration:none;font-size:.83rem;font-weight:600;">
            <i class="bi bi-file-pdf"></i> PDF
        </a>
        <a href="{{ route('recetas.duplicar', $receta) }}"
           style="display:inline-flex;align-items:center;gap:.3rem;padding:.45rem .9rem;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:8px;text-decoration:none;font-size:.83rem;font-weight:600;">
            <i class="bi bi-copy"></i> Duplicar
        </a>
        <a href="{{ route('recetas.edit', $receta) }}"
           style="display:inline-flex;align-items:center;gap:.3rem;padding:.45rem .9rem;background:#fff7ed;color:#ea580c;border:1px solid #fed7aa;border-radius:8px;text-decoration:none;font-size:.83rem;font-weight:600;">
            <i class="bi bi-pencil"></i> Editar
        </a>
    </div>
</div>

@if(session('exito'))
<div style="background:#dcfce7;border:1px solid #86efac;color:#166534;border-radius:8px;padding:.7rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <i class="bi bi-check-circle me-1"></i> {{ session('exito') }}
</div>
@endif

<div class="row g-3">
    {{-- Info principal --}}
    <div class="col-md-8">
        <div class="card-sistema" style="margin-bottom:1rem;">
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid var(--fondo-borde);">
                <div>
                    <div style="font-size:.72rem;color:var(--texto-secundario);text-transform:uppercase;letter-spacing:.04em;">Paciente</div>
                    <div style="font-weight:600;font-size:.9rem;">
                        <a href="{{ route('pacientes.show', $receta->paciente) }}" style="color:var(--color-principal);text-decoration:none;">
                            {{ $receta->paciente->nombre_completo }}
                        </a>
                    </div>
                    <div style="font-size:.75rem;color:var(--texto-secundario);">{{ $receta->paciente->numero_historia }}</div>
                </div>
                <div>
                    <div style="font-size:.72rem;color:var(--texto-secundario);text-transform:uppercase;letter-spacing:.04em;">Doctor</div>
                    <div style="font-weight:600;font-size:.9rem;">{{ $receta->doctor->name }}</div>
                </div>
                <div>
                    <div style="font-size:.72rem;color:var(--texto-secundario);text-transform:uppercase;letter-spacing:.04em;">Fecha</div>
                    <div style="font-weight:600;font-size:.9rem;">{{ $receta->fecha->format('d/m/Y') }}</div>
                </div>
            </div>
            @if($receta->diagnostico)
            <div style="margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid var(--fondo-borde);">
                <div style="font-size:.72rem;color:var(--texto-secundario);text-transform:uppercase;letter-spacing:.04em;margin-bottom:.3rem;">Diagnóstico</div>
                <div style="font-size:.9rem;color:var(--texto-principal);">{{ $receta->diagnostico }}</div>
            </div>
            @endif
            @if($receta->evolucion)
            <div style="font-size:.78rem;color:var(--texto-secundario);">
                <i class="bi bi-link-45deg me-1"></i> Asociada a evolución
                <a href="{{ route('evoluciones.show', $receta->evolucion) }}" style="color:var(--color-principal);text-decoration:none;">
                    {{ $receta->evolucion->fecha->format('d/m/Y') }} — {{ Str::limit($receta->evolucion->procedimiento, 40) }}
                </a>
            </div>
            @endif
        </div>

        {{-- Medicamentos --}}
        <div class="card-sistema" style="margin-bottom:1rem;">
            <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
                <i class="bi bi-capsule me-2"></i>Medicamentos ({{ count($receta->medicamentos ?? []) }})
            </h5>
            @forelse($receta->medicamentos ?? [] as $i => $med)
            <div style="border:1px solid var(--fondo-borde);border-radius:10px;padding:1rem;margin-bottom:.75rem;background:var(--fondo-app);">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem;">
                    <div style="flex:1;">
                        <div style="font-size:.95rem;font-weight:700;color:var(--texto-principal);margin-bottom:.5rem;">
                            {{ $i+1 }}. {{ $med['nombre'] ?? '—' }}
                            @if(!empty($med['presentacion']))
                            <span style="font-size:.75rem;font-weight:400;color:var(--texto-secundario);">({{ $med['presentacion'] }})</span>
                            @endif
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:.5rem;font-size:.8rem;">
                            @if(!empty($med['dosis']))
                            <span style="background:var(--color-muy-claro);color:var(--color-principal);padding:.2rem .55rem;border-radius:20px;font-weight:500;">
                                <i class="bi bi-droplet me-1"></i>{{ $med['dosis'] }}
                            </span>
                            @endif
                            @if(!empty($med['frecuencia']))
                            <span style="background:#f0f9ff;color:#0369a1;padding:.2rem .55rem;border-radius:20px;font-weight:500;">
                                <i class="bi bi-clock me-1"></i>{{ $med['frecuencia'] }}
                            </span>
                            @endif
                            @if(!empty($med['duracion']))
                            <span style="background:#fef3c7;color:#92400e;padding:.2rem .55rem;border-radius:20px;font-weight:500;">
                                <i class="bi bi-calendar me-1"></i>{{ $med['duracion'] }}
                            </span>
                            @endif
                            @if(!empty($med['cantidad']))
                            <span style="background:#f3f4f6;color:#374151;padding:.2rem .55rem;border-radius:20px;font-weight:500;">
                                <i class="bi bi-bag me-1"></i>{{ $med['cantidad'] }}
                            </span>
                            @endif
                        </div>
                        @if(!empty($med['indicaciones']))
                        <div style="margin-top:.5rem;font-size:.8rem;color:var(--texto-secundario);">
                            <i class="bi bi-info-circle me-1"></i>{{ $med['indicaciones'] }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <p style="color:var(--texto-secundario);font-size:.84rem;text-align:center;padding:1rem 0;">Sin medicamentos</p>
            @endforelse
        </div>

        @if($receta->indicaciones_generales)
        <div class="card-sistema">
            <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:.75rem;">
                <i class="bi bi-card-text me-2"></i>Indicaciones generales
            </h5>
            <p style="font-size:.9rem;color:var(--texto-principal);white-space:pre-wrap;margin:0;">{{ $receta->indicaciones_generales }}</p>
        </div>
        @endif
    </div>

    {{-- Panel lateral --}}
    <div class="col-md-4">
        <div class="card-sistema" style="margin-bottom:1rem;">
            <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);letter-spacing:.04em;margin-bottom:.75rem;">Acciones</h6>
            <div style="display:flex;flex-direction:column;gap:.5rem;">
                <a href="{{ route('recetas.pdf', $receta) }}" target="_blank"
                   style="display:flex;align-items:center;gap:.5rem;padding:.55rem .9rem;background:#f0fdf4;color:#16a34a;border:1px solid #86efac;border-radius:8px;text-decoration:none;font-size:.83rem;font-weight:500;">
                    <i class="bi bi-file-pdf"></i> Descargar PDF
                </a>
                <a href="{{ route('recetas.duplicar', $receta) }}"
                   style="display:flex;align-items:center;gap:.5rem;padding:.55rem .9rem;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:8px;text-decoration:none;font-size:.83rem;font-weight:500;">
                    <i class="bi bi-copy"></i> Duplicar receta
                </a>
                <a href="{{ route('recetas.edit', $receta) }}"
                   style="display:flex;align-items:center;gap:.5rem;padding:.55rem .9rem;background:#fff7ed;color:#ea580c;border:1px solid #fed7aa;border-radius:8px;text-decoration:none;font-size:.83rem;font-weight:500;">
                    <i class="bi bi-pencil"></i> Editar receta
                </a>
                <form method="POST" action="{{ route('recetas.destroy', $receta) }}" onsubmit="return confirm('¿Anular esta receta?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            style="width:100%;display:flex;align-items:center;gap:.5rem;padding:.55rem .9rem;background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;border-radius:8px;font-size:.83rem;font-weight:500;cursor:pointer;">
                        <i class="bi bi-trash"></i> Anular receta
                    </button>
                </form>
            </div>
        </div>

        <div class="card-sistema">
            <h6 style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);letter-spacing:.04em;margin-bottom:.75rem;">Datos del paciente</h6>
            <div style="font-size:.83rem;">
                <div style="margin-bottom:.3rem;"><span style="color:var(--texto-secundario);">Nombre:</span> {{ $receta->paciente->nombre_completo }}</div>
                <div style="margin-bottom:.3rem;"><span style="color:var(--texto-secundario);">Documento:</span> {{ $receta->paciente->tipo_documento }} {{ $receta->paciente->numero_documento }}</div>
                <div style="margin-bottom:.3rem;"><span style="color:var(--texto-secundario);">Edad:</span> {{ $receta->paciente->edad }} años</div>
                <div><span style="color:var(--texto-secundario);">Teléfono:</span> {{ $receta->paciente->telefono }}</div>
            </div>
        </div>
    </div>
</div>


@endsection
