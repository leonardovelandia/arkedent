@extends('layouts.app')
@section('titulo', 'Evolución — ' . $evolucion->paciente->nombre_completo)

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-out { background:transparent; color:var(--color-principal); border:1px solid var(--color-principal); border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-out:hover { background:var(--color-muy-claro); color:var(--color-hover); }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; color:#1f2937; }

    .evol-header { background:linear-gradient(135deg,var(--color-principal) 0%,var(--color-sidebar-2) 60%,var(--color-sidebar) 100%); border-radius:14px; padding:1.75rem; color:#fff; margin-bottom:1.5rem; }
    .evol-titulo { font-family:var(--fuente-titulos); font-size:1.35rem; font-weight:600; margin-bottom:.3rem; }
    .evol-meta { display:flex; flex-wrap:wrap; gap:.85rem; margin-top:.5rem; }
    .evol-meta-item { display:flex; align-items:center; gap:.35rem; font-size:.82rem; opacity:.85; }

    .badge-doc { background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25); border-radius:20px; padding:.22rem .85rem; font-size:.78rem; font-weight:600; display:inline-flex; align-items:center; gap:.35rem; }
    .badge-dientes { background:var(--color-muy-claro); color:var(--color-hover); border-radius:20px; padding:.2rem .7rem; font-size:.78rem; font-weight:600; display:inline-flex; align-items:center; gap:.3rem; }

    .sec-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; margin-bottom:1.25rem; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .sec-header { background:var(--color-muy-claro); padding:.75rem 1.25rem; border-bottom:1px solid var(--color-muy-claro); display:flex; align-items:center; gap:.5rem; }
    .sec-header h6 { margin:0; font-size:.82rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); }
    .sec-body { padding:1.25rem; }

    .dato-lbl { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#9ca3af; margin-bottom:.2rem; }
    .dato-val { font-size:.9rem; color:#1c2b22; font-weight:500; white-space:pre-wrap; line-height:1.6; }

    .vital-box { background:var(--fondo-card-alt); border:1px solid var(--color-muy-claro); border-radius:10px; padding:.85rem 1rem; text-align:center; }
    .vital-val { font-size:1.1rem; font-weight:700; color:var(--color-principal); }
    .vital-lbl { font-size:.72rem; color:#9ca3af; font-weight:600; text-transform:uppercase; margin-top:.15rem; }

    .mat-tabla { width:100%; border-collapse:collapse; font-size:.85rem; }
    .mat-tabla thead th { background:var(--color-muy-claro); color:var(--color-hover); font-weight:700; font-size:.75rem; text-transform:uppercase; padding:.55rem .85rem; border-bottom:1px solid var(--color-muy-claro); }
    .mat-tabla tbody td { padding:.55rem .85rem; border-bottom:1px solid var(--fondo-borde); }
    .mat-tabla tbody tr:last-child td { border-bottom:none; }

    .prox-cita-box { background:linear-gradient(135deg,#f0fdf4,#dcfce7); border:1px solid #bbf7d0; border-radius:10px; padding:1rem 1.25rem; display:flex; align-items:center; gap:1rem; }

    @media print {
        .no-print { display:none !important; }
        .sec-card { box-shadow:none; border:1px solid #e5e7eb; }
    }
</style>
@endpush

@section('contenido')

@if(session('exito'))
    <div class="alerta-flash no-print" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
        <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
    </div>
@endif

{{-- Header --}}
<div class="evol-header">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <div class="evol-titulo">{{ $evolucion->procedimiento }}
                @if($evolucion->numero_evolucion)
                <span style="font-family:monospace;font-size:.78rem;font-weight:700;background:rgba(255,255,255,.2);border-radius:6px;padding:.1rem .6rem;margin-left:.5rem;letter-spacing:.03em;">{{ $evolucion->numero_evolucion }}</span>
                @endif
            </div>
            <div style="font-size:1rem;opacity:.9;margin-bottom:.4rem;">{{ $evolucion->paciente->nombre_completo }}</div>
            <div class="evol-meta">
                <span class="evol-meta-item"><i class="bi bi-calendar3"></i> {{ $evolucion->fecha_formateada }}</span>
                @if($evolucion->hora)
                    <span class="evol-meta-item"><i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($evolucion->hora)->format('h:i A') }}</span>
                @endif
                <span class="evol-meta-item"><i class="bi bi-journal-medical"></i> {{ $evolucion->paciente->numero_historia }}</span>
                @if($evolucion->dientes_tratados)
                    <span class="badge-dientes"><i class="bi bi-tooth"></i> {{ $evolucion->dientes_tratados }}</span>
                @endif
            </div>
            @if($evolucion->doctor)
                <div style="margin-top:.65rem;">
                    <span class="badge-doc"><i class="bi bi-person-badge"></i> Dr(a). {{ $evolucion->doctor->name }}</span>
                </div>
            @endif
        </div>
        <div class="no-print" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:flex-start;">
            @if($evolucion->firmado || $evolucion->created_at->diffInHours(now()) >= 24)
            <a href="{{ route('evoluciones.correccion.vista', $evolucion) }}"
               style="background:#d97706;color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;display:inline-flex;align-items:center;gap:.35rem;text-decoration:none;">
                <i class="bi bi-pencil-square"></i> Agregar Corrección
            </a>
            @else
            <a href="{{ route('evoluciones.edit', $evolucion) }}"
               class="btn-out" style="background:rgba(255,255,255,.12);color:#fff;border-color:rgba(255,255,255,.3);">
                <i class="bi bi-pencil"></i> Editar
                <span style="font-size:.65rem;color:rgba(255,255,255,.6);">
                    ({{ 24 - $evolucion->created_at->diffInHours(now()) }}h restantes)
                </span>
            </a>
            @endif
            <button onclick="window.print()"
                    class="btn-out" style="background:rgba(255,255,255,.08);color:#fff;border-color:rgba(255,255,255,.25);">
                <i class="bi bi-printer"></i> Imprimir
            </button>
            <a href="{{ route('pacientes.show', $evolucion->paciente) }}"
               class="btn-out" style="background:rgba(255,255,255,.08);color:#fff;border-color:rgba(255,255,255,.25);">
                <i class="bi bi-arrow-left"></i> Ver Paciente
            </a>
            @if(!$evolucion->firmado)
            <a href="{{ route('evoluciones.firmar.vista', $evolucion) }}" style="background:#d97706;color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;display:inline-flex;align-items:center;gap:.35rem;text-decoration:none;">
                <i class="bi bi-pen"></i> Firmar Evolución
            </a>
            @else
            <span style="background:#d1fae5;color:#166534;padding:.3rem .75rem;border-radius:8px;font-size:.82rem;font-weight:700;display:inline-flex;align-items:center;gap:.3rem;">
                <i class="bi bi-check-circle-fill"></i> Firmada
            </span>
            @endif
            <a href="{{ route('evoluciones.pdf', $evolucion) }}" target="_blank" style="background:var(--color-principal);color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;display:inline-flex;align-items:center;gap:.35rem;text-decoration:none;">
                <i class="bi bi-file-earmark-pdf"></i> Ver PDF
            </a>
            @modulo('recetas')
            <a href="{{ route('recetas.create', ['paciente_id' => $evolucion->paciente_id, 'evolucion_id' => $evolucion->id]) }}"
               style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;display:inline-flex;align-items:center;gap:.35rem;text-decoration:none;">
                <i class="bi bi-file-medical"></i> Nueva Receta
            </a>
            @endmodulo
        </div>
    </div>
</div>

{{-- UN SOLO CARD tipo hoja de documento --}}
<div style="background:#ffffff; border:1px solid var(--fondo-borde); border-radius:14px; overflow:hidden; margin-bottom:1.5rem; box-shadow: 0 2px 12px rgba(13,110,253,0.06);">

    {{-- SECCIÓN 1: DATOS DE LA SESIÓN --}}
    <div style="padding:1.25rem 1.75rem; border-bottom:1px solid #e8f0fe;">
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
            <i class="bi bi-calendar-check" style="color:#0D6EFD;"></i>
            <span style="font-size:0.72rem; font-weight:600; color:#0D6EFD; letter-spacing:0.1em; text-transform:uppercase;">Datos de la Sesión</span>
        </div>
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem;">
            <div>
                <div style="font-size:0.7rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.25rem;">Fecha</div>
                <div style="font-size:0.92rem; font-weight:500; color:#1c2b22;">{{ $evolucion->fecha->format('d/m/Y') }}</div>
            </div>
            @if($evolucion->hora)
            <div>
                <div style="font-size:0.7rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.25rem;">Hora</div>
                <div style="font-size:0.92rem; font-weight:500; color:#1c2b22;">{{ $evolucion->hora }}</div>
            </div>
            @endif
            @if($evolucion->dientes_tratados)
            <div>
                <div style="font-size:0.7rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.25rem;">Dientes tratados</div>
                <div style="font-size:0.92rem; color:#1c2b22;">{{ $evolucion->dientes_tratados }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- SECCIÓN 2: DESCRIPCIÓN CLÍNICA --}}
    <div style="padding:1.25rem 1.75rem; border-bottom:1px solid #e8f0fe; background:#f0f7ff;">
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.75rem;">
            <i class="bi bi-clipboard2-pulse" style="color:#0D6EFD;"></i>
            <span style="font-size:0.72rem; font-weight:600; color:#0D6EFD; letter-spacing:0.1em; text-transform:uppercase;">Descripción Clínica</span>
        </div>
        <div style="font-size:0.92rem; color:#1c2b22; line-height:1.7; white-space:pre-line;">{{ $evolucion->descripcion }}</div>
    </div>

    {{-- SECCIÓN 3: SIGNOS VITALES --}}
    @if($evolucion->presion_arterial || $evolucion->frecuencia_cardiaca)
    <div style="padding:1.25rem 1.75rem; border-bottom:1px solid #e8f0fe;">
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
            <i class="bi bi-activity" style="color:#0D6EFD;"></i>
            <span style="font-size:0.72rem; font-weight:600; color:#0D6EFD; letter-spacing:0.1em; text-transform:uppercase;">Signos Vitales Pre-procedimiento</span>
        </div>
        <div style="display:grid; grid-template-columns:repeat(2,200px); gap:0.75rem;">
            @if($evolucion->presion_arterial)
            <div style="background:#f0f7ff; border:1px solid #cce5ff; border-radius:10px; padding:0.75rem; text-align:center;">
                <i class="bi bi-heart" style="color:#0D6EFD; font-size:1rem; display:block; margin-bottom:0.3rem;"></i>
                <div style="font-size:1rem; font-weight:600; color:#0D6EFD;">{{ $evolucion->presion_arterial }}</div>
                <div style="font-size:0.65rem; color:#8fa39a; text-transform:uppercase; margin-top:0.2rem;">Presión arterial</div>
            </div>
            @endif
            @if($evolucion->frecuencia_cardiaca)
            <div style="background:#f0f7ff; border:1px solid #cce5ff; border-radius:10px; padding:0.75rem; text-align:center;">
                <i class="bi bi-activity" style="color:#0D6EFD; font-size:1rem; display:block; margin-bottom:0.3rem;"></i>
                <div style="font-size:1rem; font-weight:600; color:#0D6EFD;">{{ $evolucion->frecuencia_cardiaca }}</div>
                <div style="font-size:0.65rem; color:#8fa39a; text-transform:uppercase; margin-top:0.2rem;">Frec. cardiaca</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- SECCIÓN 4: MATERIALES UTILIZADOS --}}
    @if($evolucion->materiales && count($evolucion->materiales) > 0)
    <div style="padding:1.25rem 1.75rem; border-bottom:1px solid #e8f0fe; background:#f0f7ff;">
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
            <i class="bi bi-box-seam" style="color:#0D6EFD;"></i>
            <span style="font-size:0.72rem; font-weight:600; color:#0D6EFD; letter-spacing:0.1em; text-transform:uppercase;">Materiales Utilizados</span>
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
            <thead>
                <tr style="background:#e3f2fd;">
                    <th style="padding:0.5rem 0.75rem; text-align:left; font-size:0.72rem; font-weight:600; color:#0D6EFD; text-transform:uppercase; letter-spacing:0.06em;">Material / Insumo</th>
                    <th style="padding:0.5rem 0.75rem; text-align:left; font-size:0.72rem; font-weight:600; color:#0D6EFD; text-transform:uppercase; letter-spacing:0.06em;">Cantidad</th>
                    <th style="padding:0.5rem 0.75rem; text-align:left; font-size:0.72rem; font-weight:600; color:#0D6EFD; text-transform:uppercase; letter-spacing:0.06em;">Inventario</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evolucion->materiales as $material)
                <tr style="border-bottom:1px solid #e8f0fe;">
                    <td style="padding:0.5rem 0.75rem; color:#1c2b22;">{{ $material['nombre'] ?? '' }}</td>
                    <td style="padding:0.5rem 0.75rem; color:#5c6b62;">{{ $material['cantidad'] ?? '' }}</td>
                    <td style="padding:0.5rem 0.75rem;">
                        @if($evolucion->movimientosInventario->where('concepto', 'like', '%' . ($material['nombre'] ?? '') . '%')->count() > 0)
                            <span style="color:#28A745; font-size:0.75rem;"><i class="bi bi-check-circle"></i> Descontado</span>
                        @else
                            <span style="color:#8fa39a; font-size:0.75rem;"><i class="bi bi-dash-circle"></i> Sin registro</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- SECCIÓN 5: PRÓXIMA CITA SUGERIDA --}}
    @if($evolucion->proxima_cita_fecha || $evolucion->proxima_cita_procedimiento)
    <div style="padding:1.25rem 1.75rem; border-bottom:1px solid #e8f0fe;">
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.75rem;">
            <i class="bi bi-calendar-plus" style="color:#0D6EFD;"></i>
            <span style="font-size:0.72rem; font-weight:600; color:#0D6EFD; letter-spacing:0.1em; text-transform:uppercase;">Próxima Cita Sugerida</span>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
            @if($evolucion->proxima_cita_fecha)
            <div>
                <div style="font-size:0.7rem; color:#8fa39a; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.25rem;">Fecha sugerida</div>
                <div style="font-size:0.92rem; font-weight:500; color:#1c2b22;">{{ $evolucion->proxima_cita_fecha->format('d/m/Y') }}</div>
            </div>
            @endif
            @if($evolucion->proxima_cita_procedimiento)
            <div>
                <div style="font-size:0.7rem; color:#8fa39a; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.25rem;">Procedimiento sugerido</div>
                <div style="font-size:0.92rem; color:#1c2b22;">{{ $evolucion->proxima_cita_procedimiento }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- SECCIÓN 6: OBSERVACIONES --}}
    @if($evolucion->observaciones)
    <div style="padding:1.25rem 1.75rem; border-bottom:1px solid #e8f0fe;">
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.75rem;">
            <i class="bi bi-chat-square-dots" style="color:#0D6EFD;"></i>
            <span style="font-size:0.72rem; font-weight:600; color:#0D6EFD; letter-spacing:0.1em; text-transform:uppercase;">Observaciones</span>
        </div>
        <div style="font-size:0.92rem; color:#1c2b22; line-height:1.6; white-space:pre-line;">{{ $evolucion->observaciones }}</div>
    </div>
    @endif

    {{-- SECCIÓN 7: FIRMA DEL PACIENTE --}}
    <div style="padding:1.25rem 1.75rem; background:#f0f7ff;">
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
            <i class="bi bi-pen" style="color:#0D6EFD;"></i>
            <span style="font-size:0.72rem; font-weight:600; color:#0D6EFD; letter-spacing:0.1em; text-transform:uppercase;">Firma del Paciente</span>
        </div>
        @if($evolucion->firmado && $evolucion->firma_data)
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem; align-items:end;">
                <div>
                    <div style="border:1px solid #cce5ff; border-radius:8px; padding:0.5rem; background:white; display:inline-block; margin-bottom:0.5rem;">
                        <img src="{{ $evolucion->firma_data }}" style="max-height:80px; max-width:250px; display:block;">
                    </div>
                    <div style="border-top:1px solid #333; padding-top:0.4rem;">
                        <div style="font-size:0.85rem; font-weight:500; color:#1c2b22;">{{ $evolucion->paciente->nombre_completo }}</div>
                        <div style="font-size:0.75rem; color:#5c6b62;">{{ $evolucion->paciente->tipo_documento }}: {{ $evolucion->paciente->numero_documento }}</div>
                        <div style="font-size:0.72rem; color:#8fa39a; margin-top:0.2rem;">
                            <i class="bi bi-check-circle-fill" style="color:#28a745;"></i>
                            Firmado el {{ $evolucion->fecha_firma->format('d/m/Y \a \l\a\s H:i') }}
                        </div>
                    </div>
                </div>
                <div>
                    <div style="height:80px;"></div>
                    <div style="border-top:1px solid #333; padding-top:0.4rem;">
                        <div style="font-size:0.85rem; font-weight:500; color:#1c2b22;">{{ $evolucion->doctor->name ?? auth()->user()->name }}</div>
                        <div style="font-size:0.75rem; color:#5c6b62;">Odontólogo(a) — {{ $config->nombre_consultorio ?? '' }}</div>
                    </div>
                </div>
            </div>
        @else
            <div style="display:flex; align-items:center; gap:0.75rem; padding:0.75rem 1rem; background:#cce5ff; border:1px solid #0D6EFD; border-radius:8px;">
                <i class="bi bi-exclamation-triangle-fill" style="color:#004085;"></i>
                <span style="font-size:0.85rem; color:#004085;">
                    Esta evolución aún no ha sido firmada por el paciente.
                </span>
                <a href="{{ route('evoluciones.firmar.vista', $evolucion) }}"
                   style="margin-left:auto; background:#0D6EFD; color:white; padding:4px 14px; border-radius:6px; font-size:0.8rem; text-decoration:none;">
                    <i class="bi bi-pen me-1"></i> Firmar ahora
                </a>
            </div>
        @endif
    </div>

</div>{{-- /card único --}}

{{-- Notas de Corrección --}}
@if(($evolucion->firmado || $evolucion->created_at->diffInHours(now()) >= 24) && $evolucion->correcciones->count() > 0)
<div class="sec-card mt-3">
    <div class="sec-header" style="justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:.5rem;">
            <i class="bi bi-pencil-square" style="color:var(--color-principal);"></i>
            <h6>Notas de Corrección ({{ $evolucion->correcciones->count() }})</h6>
        </div>
        <a href="{{ route('evoluciones.correccion.vista', $evolucion) }}"
           style="font-size:.78rem;color:var(--color-principal);text-decoration:none;">
            + Agregar corrección
        </a>
    </div>
    <div style="padding:1rem 1.25rem;">
        @foreach($evolucion->correcciones as $correccion)
        <div style="border-left:3px solid {{ $correccion->firmado ? '#28a745' : '#ffc107' }};padding:.75rem 1rem;margin-bottom:.75rem;background:var(--fondo-card-alt);border-radius:0 8px 8px 0;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.4rem;flex-wrap:wrap;gap:.3rem;">
                <span style="font-size:.8rem;font-weight:600;color:var(--color-principal);">
                    Campo: {{ $correccion->campo_label }}
                </span>
                <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
                    <span style="font-size:.75rem;color:#9ca3af;">
                        {{ $correccion->created_at->format('d/m/Y H:i') }} — {{ $correccion->usuario->name }}
                    </span>
                    @if($correccion->firmado)
                        <span style="background:#d1fae5;color:#166534;padding:.15rem .55rem;border-radius:20px;font-size:.65rem;font-weight:700;">
                            <i class="bi bi-check-circle"></i> Firmada {{ $correccion->fecha_firma->format('d/m/Y H:i') }}
                        </span>
                    @else
                        <span style="background:#FFF3CD;color:#856404;padding:.15rem .55rem;border-radius:20px;font-size:.65rem;font-weight:700;">
                            <i class="bi bi-clock"></i> Pendiente firma
                        </span>
                        <a href="{{ route('evoluciones.correccion.firmar.vista', $correccion) }}"
                           style="font-size:.72rem;background:var(--color-principal);color:white;padding:2px 8px;border-radius:4px;text-decoration:none;display:inline-flex;align-items:center;gap:.25rem;">
                            <i class="bi bi-pen"></i> Firmar
                        </a>
                    @endif
                </div>
            </div>
            <div style="font-size:.8rem;margin-bottom:.3rem;">
                <span style="color:#999;text-decoration:line-through;">
                    Anterior: {{ Str::limit($correccion->valor_anterior, 100) }}
                </span>
            </div>
            <div style="font-size:.8rem;margin-bottom:.3rem;">
                <span style="color:#1c2b22;">
                    Corrección: {{ Str::limit($correccion->valor_nuevo, 100) }}
                </span>
            </div>
            <div style="font-size:.75rem;color:#5c6b62;">
                Motivo: {{ $correccion->motivo }}
            </div>
            @if($correccion->firmado && $correccion->firma_data)
            <div style="margin-top:.5rem;">
                <img src="{{ $correccion->firma_data }}" style="max-height:50px;border:1px solid #ddd;border-radius:4px;background:#fff;padding:2px;">
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Sección: Órdenes de Laboratorio --}}
@php $ordenesEvolucion = $evolucion->ordenesDeLaboratorio ?? \App\Models\OrdenLaboratorio::where('evolucion_id', $evolucion->id)->where('activo', true)->with('laboratorio')->get(); @endphp
<div style="background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-top:1rem;">
    <div style="padding:.875rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between;">
        <span style="font-size:.9rem; font-weight:600; color:#1c2b22; display:flex; align-items:center; gap:.4rem;">
            <i class="bi bi-flask" style="color:var(--color-principal);"></i> Órdenes de Laboratorio
        </span>
        <a href="{{ route('laboratorio.create', ['evolucion_id' => $evolucion->id, 'paciente_id' => $evolucion->paciente_id]) }}"
           style="display:inline-flex;align-items:center;gap:.3rem;background:linear-gradient(135deg,var(--color-principal),var(--color-claro));color:#fff;border:none;border-radius:8px;padding:.4rem .875rem;font-size:.8rem;font-weight:500;text-decoration:none;">
            <i class="bi bi-plus-lg"></i> Nueva Orden de Laboratorio
        </a>
    </div>
    @if($ordenesEvolucion->isEmpty())
        <div style="padding:1.5rem; text-align:center; color:#8fa39a; font-size:.83rem;">
            <i class="bi bi-flask" style="font-size:1.5rem; display:block; margin-bottom:.4rem;"></i>
            No hay órdenes de laboratorio vinculadas a esta evolución
        </div>
    @else
        @foreach($ordenesEvolucion as $olEv)
        @php
            $bOl = ['pendiente'=>['#fff3cd','#856404'],'enviado'=>['#d1ecf1','#0c5460'],'en_proceso'=>['#cce5ff','#004085'],'recibido'=>['#d4edda','#155724'],'instalado'=>['#d6d8d9','#1b1e21'],'cancelado'=>['#f8d7da','#721c24']];
            $bcOl = $bOl[$olEv->estado] ?? ['#f3f4f6','#374151'];
        @endphp
        <div style="padding:.75rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
            <span style="font-family:monospace; font-weight:700; color:var(--color-principal); font-size:.82rem;">{{ $olEv->numero_orden }}</span>
            <span style="font-size:.85rem; color:#1c2b22; font-weight:500; flex:1;">{{ $olEv->tipo_trabajo }}</span>
            <span style="font-size:.8rem; color:#6b7280;">{{ $olEv->laboratorio->nombre ?? '—' }}</span>
            <span style="background:{{ $bcOl[0] }};color:{{ $bcOl[1] }};border-radius:50px;padding:.15rem .55rem;font-size:.7rem;font-weight:700;">{{ $olEv->estado_label }}</span>
            <a href="{{ route('laboratorio.show', $olEv) }}"
               style="color:var(--color-principal);font-size:.8rem;text-decoration:none;display:inline-flex;align-items:center;gap:.2rem;">
                <i class="bi bi-eye"></i> Ver
            </a>
        </div>
        @endforeach
    @endif
</div>

@endsection
