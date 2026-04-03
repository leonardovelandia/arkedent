@extends('layouts.app')
@section('titulo', $paciente->nombre_completo)

@push('estilos')
<style>
    :root {
        --morado-base: var(--color-principal);
        --morado-claro: var(--color-claro);
        --morado-hover: var(--color-hover);
        --morado-muy-claro: var(--color-muy-claro);
    }

    .btn-morado {
        background: linear-gradient(135deg, var(--color-principal), var(--color-claro));
        color: #fff; border: none; border-radius: 8px;
        padding: 0.5rem 1.1rem; font-size: 0.875rem; font-weight: 500;
        display: inline-flex; align-items: center; gap: 0.4rem;
        transition: filter 0.18s; text-decoration: none; cursor: pointer;
    }
    .btn-morado:hover { filter: brightness(1.12); color: #fff; }

    .btn-outline-morado {
        background: transparent; color: var(--color-principal);
        border: 1px solid var(--color-principal); border-radius: 8px;
        padding: 0.45rem 1rem; font-size: 0.875rem; font-weight: 500;
        display: inline-flex; align-items: center; gap: 0.35rem;
        transition: background 0.15s; text-decoration: none;
    }
    .btn-outline-morado:hover { background: var(--color-muy-claro); color: var(--color-hover); }

    .btn-gris {
        background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;
        border-radius: 8px; padding: 0.45rem 1rem; font-size: 0.875rem; font-weight: 500;
        display: inline-flex; align-items: center; gap: 0.35rem;
        transition: background 0.15s; text-decoration: none;
    }
    .btn-gris:hover { background: #e5e7eb; color: #1f2937; }

    /* Header del paciente */
    .pac-header-card {
        background: linear-gradient(135deg, var(--color-principal) 0%, var(--color-sidebar-2) 60%, var(--color-sidebar) 100%);
        border-radius: 14px;
        padding: 1.75rem;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }
    .pac-avatar-grande {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgba(255,255,255,0.35);
        flex-shrink: 0;
    }
    .pac-iniciales-grande {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: rgba(255,255,255,0.18);
        color: #fff;
        font-size: 1.8rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid rgba(255,255,255,0.35);
        flex-shrink: 0;
    }
    .pac-header-info { flex: 1; }
    .pac-nombre { font-family: var(--fuente-titulos); font-size: 1.5rem; font-weight: 600; margin-bottom: 0.2rem; }
    .pac-meta { display: flex; flex-wrap: wrap; gap: 0.85rem; margin-top: 0.5rem; }
    .pac-meta-item { display: flex; align-items: center; gap: 0.35rem; font-size: 0.82rem; opacity: 0.85; }
    .badge-activo-header {
        background: rgba(74,222,128,0.2);
        color: #86efac;
        border: 1px solid rgba(74,222,128,0.3);
        border-radius: 20px;
        padding: 0.22rem 0.85rem;
        font-size: 0.78rem;
        font-weight: 600;
    }
    .badge-inactivo-header {
        background: rgba(248,113,113,0.2);
        color: #fca5a5;
        border: 1px solid rgba(248,113,113,0.3);
        border-radius: 20px;
        padding: 0.22rem 0.85rem;
        font-size: 0.78rem;
        font-weight: 600;
    }

    /* Tabs */
    .pac-tabs {
        display: flex;
        gap: 0.25rem;
        border-bottom: 2px solid var(--color-muy-claro);
        margin-bottom: 1.25rem;
    }
    .pac-tab {
        padding: 0.6rem 1.1rem;
        font-size: 0.855rem;
        font-weight: 500;
        color: #6b7280;
        cursor: pointer;
        border: none;
        background: none;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: color 0.15s, border-color 0.15s;
        white-space: nowrap;
    }
    .pac-tab:hover { color: var(--color-principal); }
    .pac-tab.activo { color: var(--color-principal); border-bottom-color: var(--color-principal); font-weight: 600; }

    .tab-panel { display: none; }
    .tab-panel.activo { display: block; }

    /* Datos */
    .dato-grupo { margin-bottom: 1.1rem; }
    .dato-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af; margin-bottom: 0.2rem; }
    .dato-valor { font-size: 0.9rem; color: #1c2b22; font-weight: 500; }

    /* Sección vacía (próximas citas, etc.) */
    .seccion-vacia {
        text-align: center;
        padding: 2.5rem 1rem;
        color: #9ca3af;
    }
    .seccion-vacia i { font-size: 2rem; color: var(--color-acento-activo); display: block; margin-bottom: 0.6rem; }

    .card-sistema { background: #fff; border: 1px solid var(--fondo-borde); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: 0 8px 28px var(--sombra-principal), 0 2px 8px rgba(0,0,0,0.12); }
</style>
@endpush

@section('contenido')

{{-- Mensajes flash --}}
@if(session('exito'))
    <div class="alerta-flash" style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0;">
        <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
    </div>
@endif

{{-- Header del paciente --}}
<div class="pac-header-card">
    {{-- Avatar --}}
    @if($paciente->foto_path)
        <img src="{{ $paciente->foto_url }}" alt="{{ $paciente->nombre_completo }}" class="pac-avatar-grande">
    @else
        <div class="pac-iniciales-grande">
            {{ strtoupper(substr($paciente->nombre,0,1)) }}{{ strtoupper(substr($paciente->apellido,0,1)) }}
        </div>
    @endif

    {{-- Datos principales --}}
    <div class="pac-header-info">
        <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
            <span class="pac-nombre">{{ $paciente->nombre_completo }}</span>
            @if($paciente->activo)
                <span class="badge-activo-header"><i class="bi bi-circle-fill" style="font-size:0.45rem;"></i> Activo</span>
            @else
                <span class="badge-inactivo-header"><i class="bi bi-circle-fill" style="font-size:0.45rem;"></i> Inactivo</span>
            @endif
            @if($paciente->tieneAutorizacion())
                <a href="{{ route('autorizacion.show', $paciente->autorizacionDatos->id) }}"
                   style="background:rgba(74,222,128,.2);color:#86efac;border:1px solid rgba(74,222,128,.3);border-radius:20px;padding:.22rem .85rem;font-size:.72rem;font-weight:600;text-decoration:none;">
                    <i class="bi bi-shield-check"></i> Autorización firmada
                </a>
            @elseif($paciente->autorizacionDatos)
                <a href="{{ route('autorizacion.show', $paciente->autorizacionDatos->id) }}"
                   style="background:rgba(251,191,36,.2);color:#fbbf24;border:1px solid rgba(251,191,36,.3);border-radius:20px;padding:.22rem .85rem;font-size:.72rem;font-weight:600;text-decoration:none;">
                    <i class="bi bi-shield-exclamation"></i> Pendiente de firma
                </a>
            @else
                <a href="{{ route('autorizacion.create', ['paciente_id' => $paciente->id]) }}"
                   style="background:rgba(251,191,36,.2);color:#fbbf24;border:1px solid rgba(251,191,36,.3);border-radius:20px;padding:.22rem .85rem;font-size:.72rem;font-weight:600;text-decoration:none;">
                    <i class="bi bi-shield-exclamation"></i> Crear autorización
                </a>
            @endif
        </div>
        <div class="pac-meta">
            <span class="pac-meta-item"><i class="bi bi-journal-medical"></i> {{ $paciente->numero_historia }}</span>
            <span class="pac-meta-item"><i class="bi bi-cake2"></i> {{ $paciente->edad }} años</span>
            <span class="pac-meta-item"><i class="bi bi-person-badge"></i> {{ $paciente->tipo_documento }} {{ $paciente->numero_documento }}</span>
            @if($paciente->telefono)
                <span class="pac-meta-item"><i class="bi bi-telephone"></i> {{ $paciente->telefono }}</span>
            @endif
        </div>
    </div>

    {{-- Botones de acción --}}
    <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
        <a href="{{ route('pacientes.edit', $paciente) }}" class="btn-outline-morado" style="background:rgba(255,255,255,0.12); color:#fff; border-color:rgba(255,255,255,0.3);">
            <i class="bi bi-pencil"></i> Editar
        </a>

        {{-- Botón Historia Clínica --}}
        @if($paciente->historiaClinica)
            <a href="{{ route('historias.show', $paciente->historiaClinica->id) }}"
               class="btn-outline-morado" style="background:rgba(255,255,255,0.12); color:#fff; border-color:rgba(255,255,255,0.3);">
                <i class="bi bi-journal-medical"></i> Ver Historia Clínica
            </a>
        @else
            <a href="{{ route('historias.create', ['paciente_id' => $paciente->id]) }}"
               class="btn-outline-morado" style="background:rgba(255,255,255,0.08); color:#fff; border-color:rgba(255,255,255,0.25);">
                <i class="bi bi-journal-plus"></i> Crear Historia Clínica
            </a>
        @endif

        <a href="{{ route('citas.create', ['paciente_id' => $paciente->id]) }}"
           class="btn-outline-morado" style="background:rgba(255,255,255,0.08); color:#fff; border-color:rgba(255,255,255,0.25);">
            <i class="bi bi-calendar-plus"></i> Nueva Cita
        </a>
        <a href="{{ route('pagos.create', ['paciente_id' => $paciente->id]) }}"
           class="btn-outline-morado" style="background:rgba(255,255,255,0.08); color:#fff; border-color:rgba(255,255,255,0.25);">
            <i class="bi bi-cash-coin"></i> Registrar Pago
        </a>
    </div>
</div>

{{-- Tabs --}}
<div class="pac-tabs">
    <button class="pac-tab activo" onclick="cambiarTab('datos')">
        <i class="bi bi-person"></i> Datos Personales
    </button>
    <button class="pac-tab" onclick="cambiarTab('citas')">
        <i class="bi bi-calendar3"></i> Próximas Citas
    </button>
    <button class="pac-tab" onclick="cambiarTab('pagos')">
        <i class="bi bi-cash-coin"></i> Historial de Pagos
    </button>
    <button class="pac-tab" onclick="cambiarTab('historia')">
        <i class="bi bi-journal-medical"></i> Historia Clínica
    </button>
    <button class="pac-tab" onclick="cambiarTab('evoluciones')">
        <i class="bi bi-clipboard2-pulse"></i> Evoluciones
    </button>
    <button class="pac-tab" onclick="cambiarTab('consentimientos')">
        <i class="bi bi-file-earmark-check"></i> Consentimientos
    </button>
    <button class="pac-tab" onclick="cambiarTab('presupuestos')">
        <i class="bi bi-file-earmark-text"></i> Presupuestos
    </button>
    <button class="pac-tab" onclick="cambiarTab('imagenes')">
        <i class="bi bi-images"></i> Imágenes
    </button>
    <button class="pac-tab" onclick="cambiarTab('valoraciones')">
        <i class="bi bi-clipboard2-pulse"></i> Valoraciones
    </button>
    <button class="pac-tab" onclick="cambiarTab('laboratorio')">
        <i class="bi bi-flask"></i> Laboratorio
    </button>
    @modulo('ortodoncia')
    <button class="pac-tab" onclick="cambiarTab('ortodoncia')">
        <i class="bi bi-braces"></i> Ortodoncia
    </button>
    @endmodulo
    @modulo('recetas')
    <button class="pac-tab" onclick="cambiarTab('recetas')">
        <i class="bi bi-file-medical"></i> Recetas
    </button>
    @endmodulo
    @modulo('periodoncia')
    <button class="pac-tab" onclick="cambiarTab('periodoncia')">
        <i class="bi bi-heart-pulse"></i> Periodoncia
    </button>
    @endmodulo
</div>

{{-- Tab: Datos personales --}}
<div id="tab-datos" class="tab-panel activo">
    <div class="row g-3">
        {{-- Datos personales --}}
        <div class="col-md-6">
            <div class="card-sistema">
                <h6 style="font-size:0.8rem; font-weight:700; text-transform:uppercase; color:var(--color-hover); margin-bottom:1rem; letter-spacing:0.05em;">
                    <i class="bi bi-person-badge me-1"></i> Identificación
                </h6>
                <div class="row g-3">
                    <div class="col-6 dato-grupo">
                        <div class="dato-label">Nombre</div>
                        <div class="dato-valor">{{ $paciente->nombre }}</div>
                    </div>
                    <div class="col-6 dato-grupo">
                        <div class="dato-label">Apellido</div>
                        <div class="dato-valor">{{ $paciente->apellido }}</div>
                    </div>
                    <div class="col-6 dato-grupo">
                        <div class="dato-label">Tipo Documento</div>
                        <div class="dato-valor">{{ $paciente->tipo_documento }}</div>
                    </div>
                    <div class="col-6 dato-grupo">
                        <div class="dato-label">N° Documento</div>
                        <div class="dato-valor">{{ $paciente->numero_documento }}</div>
                    </div>
                    <div class="col-6 dato-grupo">
                        <div class="dato-label">Fecha de Nacimiento</div>
                        <div class="dato-valor">{{ $paciente->fecha_nacimiento->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-6 dato-grupo">
                        <div class="dato-label">Género</div>
                        <div class="dato-valor">{{ ucfirst($paciente->genero) }}</div>
                    </div>
                    <div class="col-6 dato-grupo">
                        <div class="dato-label">Ocupación</div>
                        <div class="dato-valor">{{ $paciente->ocupacion ?? '—' }}</div>
                    </div>
                    <div class="col-6 dato-grupo">
                        <div class="dato-label">Acudiente</div>
                        <div class="dato-valor">{{ $paciente->nombre_acudiente ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contacto --}}
        <div class="col-md-6">
            <div class="card-sistema">
                <h6 style="font-size:0.8rem; font-weight:700; text-transform:uppercase; color:var(--color-hover); margin-bottom:1rem; letter-spacing:0.05em;">
                    <i class="bi bi-telephone me-1"></i> Contacto
                </h6>
                <div class="row g-3">
                    <div class="col-6 dato-grupo">
                        <div class="dato-label">Teléfono</div>
                        <div class="dato-valor">{{ $paciente->telefono }}</div>
                    </div>
                    <div class="col-6 dato-grupo">
                        <div class="dato-label">Tel. Emergencia</div>
                        <div class="dato-valor">{{ $paciente->telefono_emergencia ?? '—' }}</div>
                    </div>
                    <div class="col-12 dato-grupo">
                        <div class="dato-label">Correo Electrónico</div>
                        <div class="dato-valor">{{ $paciente->email ?? '—' }}</div>
                    </div>
                    <div class="col-12 dato-grupo">
                        <div class="dato-label">Dirección</div>
                        <div class="dato-valor">{{ $paciente->direccion ?? '—' }}</div>
                    </div>
                    <div class="col-6 dato-grupo">
                        <div class="dato-label">Ciudad</div>
                        <div class="dato-valor">{{ $paciente->ciudad ?? '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Observaciones --}}
            @if($paciente->observaciones)
            <div class="card-sistema mt-3">
                <h6 style="font-size:0.8rem; font-weight:700; text-transform:uppercase; color:var(--color-hover); margin-bottom:0.75rem; letter-spacing:0.05em;">
                    <i class="bi bi-chat-text me-1"></i> Observaciones
                </h6>
                <p style="font-size:0.875rem; color:#4b5563; margin:0; line-height:1.6;">{{ $paciente->observaciones }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Tab: Próximas citas --}}
<div id="tab-citas" class="tab-panel">
    <div class="card-sistema">
        {{-- Encabezado --}}
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;">
            <h6 style="margin:0;font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);letter-spacing:.04em;">
                <i class="bi bi-calendar3 me-1"></i> Próximas Citas
            </h6>
            <div style="display:flex;gap:.4rem;flex-wrap:wrap;">
                <a href="{{ route('citas.index', ['paciente_id' => $paciente->id]) }}"
                   class="btn-outline-morado" style="font-size:.8rem;padding:.32rem .75rem;">
                    <i class="bi bi-list-ul"></i> Ver todas
                </a>
                <a href="{{ route('citas.create', ['paciente_id' => $paciente->id]) }}"
                   class="btn-morado" style="font-size:.8rem;padding:.32rem .75rem;">
                    <i class="bi bi-calendar-plus"></i> Nueva Cita
                </a>
            </div>
        </div>

        @php
            $coloresCita = \App\Models\Cita::coloresPorEstado();
            $proximasCitas = $paciente->proximasCitas()->with('doctor')->get();
        @endphp

        @if($proximasCitas->isEmpty())
        <div class="seccion-vacia" style="padding:1.5rem .5rem;">
            <i class="bi bi-calendar3" style="font-size:2rem;color:var(--color-acento-activo);display:block;margin-bottom:.5rem;"></i>
            <p style="font-weight:600;color:#4b5563;margin-bottom:.25rem;">Sin próximas citas</p>
            <p style="font-size:.84rem;color:#9ca3af;margin-bottom:.75rem;">No hay citas programadas para este paciente.</p>
        </div>
        @else
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
            <thead>
                <tr style="background:var(--color-muy-claro);">
                    <th style="padding:.55rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--color-hover);text-align:left;border-bottom:2px solid var(--color-muy-claro);">Fecha</th>
                    <th style="padding:.55rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--color-hover);text-align:left;border-bottom:2px solid var(--color-muy-claro);">Hora</th>
                    <th style="padding:.55rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--color-hover);text-align:left;border-bottom:2px solid var(--color-muy-claro);">Procedimiento</th>
                    <th style="padding:.55rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--color-hover);text-align:left;border-bottom:2px solid var(--color-muy-claro);">Estado</th>
                    <th style="padding:.55rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--color-hover);text-align:center;border-bottom:2px solid var(--color-muy-claro);">Acción</th>
                </tr>
            </thead>
            <tbody>
            @foreach($proximasCitas as $cita)
            @php $cc = $coloresCita[$cita->estado] ?? ['bg'=>'#f3f4f6','texto'=>'#374151']; @endphp
            <tr style="border-bottom:1px solid var(--fondo-borde);">
                <td style="padding:.55rem .9rem;color:#4b5563;white-space:nowrap;">
                    {{ $cita->fecha->translatedFormat('d M Y') }}
                </td>
                <td style="padding:.55rem .9rem;color:#4b5563;white-space:nowrap;">
                    {{ $cita->hora_inicio }}
                    @if($cita->hora_fin)<span style="color:#9ca3af;"> – {{ $cita->hora_fin }}</span>@endif
                </td>
                <td style="padding:.55rem .9rem;max-width:200px;">
                    <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $cita->procedimiento }}">
                        {{ $cita->procedimiento }}
                    </span>
                </td>
                <td style="padding:.55rem .9rem;">
                    <span style="display:inline-block;padding:.18rem .55rem;border-radius:20px;font-size:.7rem;font-weight:700;background:{{ $cc['bg'] }};color:{{ $cc['texto'] }};">
                        {{ ucfirst(str_replace('_',' ',$cita->estado)) }}
                    </span>
                </td>
                <td style="padding:.55rem .9rem;text-align:center;">
                    <a href="{{ route('citas.show', $cita) }}"
                       style="color:var(--color-principal);font-size:.82rem;display:inline-flex;align-items:center;gap:.25rem;text-decoration:none;">
                        <i class="bi bi-eye"></i> Ver
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>
</div>

{{-- Tab: Pagos --}}
<div id="tab-pagos" class="tab-panel">
    @php
        $totalPagadoPac  = $paciente->total_pagado;
        $totalDeudaPac   = $paciente->total_deuda;
        $ultimosPagos    = $paciente->pagos()->with('tratamiento')->where('anulado', false)->take(5)->get();
        $tratActivos     = $paciente->tratamientos()->where('estado', 'activo')->get();
    @endphp

    {{-- Cards resumen --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:.75rem;margin-bottom:1rem;">
        <div style="background:#fff;border:1px solid var(--color-muy-claro);border-radius:12px;padding:1rem 1.25rem;text-align:center;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
            <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin-bottom:.3rem;">Total pagado</div>
            <div style="font-size:1.3rem;font-weight:800;color:#166534;">$ {{ number_format($totalPagadoPac, 0, ',', '.') }}</div>
        </div>
        <div style="background:#fff;border:1px solid var(--color-muy-claro);border-radius:12px;padding:1rem 1.25rem;text-align:center;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
            <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin-bottom:.3rem;">Saldo pendiente</div>
            <div style="font-size:1.3rem;font-weight:800;color:{{ $totalDeudaPac > 0 ? '#dc2626' : '#166534' }};">
                $ {{ number_format($totalDeudaPac, 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Botones --}}
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1rem;">
        <a href="{{ route('pagos.create', ['paciente_id' => $paciente->id]) }}" class="btn-morado" style="font-size:.8rem;padding:.35rem .85rem;">
            <i class="bi bi-cash-coin"></i> Registrar Pago
        </a>
        <a href="{{ route('tratamientos.create', ['paciente_id' => $paciente->id]) }}" class="btn-morado"
           style="background:transparent;color:var(--color-principal);border:1px solid var(--color-principal);font-size:.8rem;padding:.35rem .85rem;">
            <i class="bi bi-clipboard2-plus"></i> Nuevo Tratamiento
        </a>
        <a href="{{ route('pagos.index', ['buscar' => $paciente->numero_documento]) }}"
           class="btn-gris" style="font-size:.8rem;padding:.35rem .85rem;">
            <i class="bi bi-list-ul"></i> Ver todos los pagos
        </a>
    </div>

    <div class="row g-3">
        {{-- Últimos pagos --}}
        <div class="col-md-6">
        <div class="card-sistema">
            <h6 style="font-size:.8rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.9rem;letter-spacing:.04em;">
                <i class="bi bi-receipt me-1"></i> Últimos Pagos
            </h6>
            @if($ultimosPagos->isEmpty())
            <div class="seccion-vacia" style="padding:1rem .5rem;">
                <i class="bi bi-inbox" style="font-size:1.5rem;color:var(--color-acento-activo);display:block;margin-bottom:.4rem;"></i>
                <p style="font-size:.84rem;color:#9ca3af;">Sin pagos registrados</p>
            </div>
            @else
            <table style="width:100%;border-collapse:collapse;font-size:.82rem;">
                <thead>
                    <tr style="background:var(--color-muy-claro);">
                        <th style="padding:.45rem .7rem;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:1px solid var(--color-muy-claro);">Fecha</th>
                        <th style="padding:.45rem .7rem;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:1px solid var(--color-muy-claro);">Concepto</th>
                        <th style="padding:.45rem .7rem;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:1px solid var(--color-muy-claro);">Valor</th>
                        <th style="padding:.45rem .7rem;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:1px solid var(--color-muy-claro);text-align:center;"></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($ultimosPagos as $pag)
                <tr style="border-bottom:1px solid var(--fondo-borde);">
                    <td style="padding:.45rem .7rem;color:#6b7280;white-space:nowrap;">{{ $pag->fecha_pago->format('d/m/Y') }}</td>
                    <td style="padding:.45rem .7rem;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $pag->concepto }}">{{ $pag->concepto }}</td>
                    <td style="padding:.45rem .7rem;font-weight:700;color:#166534;white-space:nowrap;">$ {{ number_format($pag->valor, 0, ',', '.') }}</td>
                    <td style="padding:.45rem .7rem;text-align:center;">
                        <a href="{{ route('pagos.show', $pag) }}" style="color:var(--color-principal);font-size:.8rem;text-decoration:none;">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
        </div>

        {{-- Tratamientos activos --}}
        <div class="col-md-6">
        <div class="card-sistema">
            <h6 style="font-size:.8rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);margin-bottom:.9rem;letter-spacing:.04em;">
                <i class="bi bi-clipboard2-pulse me-1"></i> Tratamientos Activos
            </h6>
            @if($tratActivos->isEmpty())
            <div class="seccion-vacia" style="padding:1rem .5rem;">
                <i class="bi bi-clipboard2-x" style="font-size:1.5rem;color:var(--color-acento-activo);display:block;margin-bottom:.4rem;"></i>
                <p style="font-size:.84rem;color:#9ca3af;">Sin tratamientos activos</p>
            </div>
            @else
            @foreach($tratActivos as $tr)
            @php
                $pagTr = $tr->valor_total - $tr->saldo_pendiente;
                $pctTr = $tr->valor_total > 0 ? min(100, round(($pagTr / $tr->valor_total) * 100)) : 0;
            @endphp
            <div style="border:1px solid var(--fondo-borde);border-radius:8px;padding:.75rem;margin-bottom:.6rem;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.35rem;">
                    <span style="font-size:.84rem;font-weight:600;color:#1c2b22;">{{ $tr->nombre }}</span>
                    <a href="{{ route('tratamientos.show', $tr) }}" style="font-size:.75rem;color:var(--color-principal);text-decoration:none;">Ver</a>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:.75rem;color:#6b7280;margin-bottom:.35rem;">
                    <span>Total: $ {{ number_format($tr->valor_total, 0, ',', '.') }}</span>
                    <span style="color:{{ $tr->saldo_pendiente > 0 ? '#dc2626' : '#166534' }};font-weight:700;">
                        Saldo: $ {{ number_format($tr->saldo_pendiente, 0, ',', '.') }}
                    </span>
                </div>
                <div style="background:var(--fondo-borde);border-radius:999px;height:6px;overflow:hidden;">
                    <div style="background:linear-gradient(90deg,var(--color-principal),var(--color-claro));height:100%;border-radius:999px;width:{{ $pctTr }}%;"></div>
                </div>
            </div>
            @endforeach
            @endif
        </div>
        </div>
    </div>
</div>

{{-- Tab: Historia clínica --}}
<div id="tab-historia" class="tab-panel">
    <div class="card-sistema">
        @if($paciente->historiaClinica)
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;">
                <h6 style="margin:0;font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);letter-spacing:.04em;">
                    <i class="bi bi-journal-medical me-1"></i> Historia Clínica
                </h6>
                <a href="{{ route('historias.show', $paciente->historiaClinica) }}" class="btn-morado" style="font-size:.8rem;padding:.35rem .85rem;">
                    <i class="bi bi-eye"></i> Ver Historia Completa
                </a>
            </div>
            <div class="row g-2">
                <div class="col-6">
                    <div class="dato-label">Fecha apertura</div>
                    <div class="dato-valor">{{ $paciente->historiaClinica->fecha_apertura->format('d/m/Y') }}</div>
                </div>
                <div class="col-12">
                    <div class="dato-label">Motivo de consulta</div>
                    <div class="dato-valor">{{ $paciente->historiaClinica->motivo_consulta }}</div>
                </div>
                @if($paciente->historiaClinica->alergias)
                <div class="col-12">
                    <div class="dato-label">Alergias</div>
                    <div class="dato-valor" style="color:#991b1b;">{{ $paciente->historiaClinica->alergias }}</div>
                </div>
                @endif
            </div>
        @else
            <div class="seccion-vacia">
                <i class="bi bi-journal-medical"></i>
                <p style="font-weight:600; color:#4b5563;">Historia clínica no iniciada</p>
                <a href="{{ route('historias.create', ['paciente_id' => $paciente->id]) }}" class="btn-morado mt-2">
                    <i class="bi bi-plus-circle"></i> Crear Historia Clínica
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Tab: Evoluciones --}}
<div id="tab-evoluciones" class="tab-panel">
    <div class="card-sistema">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;">
            <h6 style="margin:0;font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);letter-spacing:.04em;">
                <i class="bi bi-clipboard2-pulse me-1"></i> Evoluciones Recientes
            </h6>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                <a href="{{ route('evoluciones.index', ['paciente_id' => $paciente->id]) }}"
                   class="btn-outline-morado" style="font-size:.8rem;padding:.35rem .85rem;">
                    <i class="bi bi-list-ul"></i> Ver todas
                </a>
                @if($paciente->historiaClinica)
                <a href="{{ route('evoluciones.create', ['paciente_id' => $paciente->id]) }}"
                   class="btn-morado" style="font-size:.8rem;padding:.35rem .85rem;">
                    <i class="bi bi-clipboard2-plus"></i> Nueva Evolución
                </a>
                @endif
            </div>
        </div>

        @php $evoluciones = $paciente->evoluciones()->with('doctor')->take(5)->get(); @endphp

        @if($evoluciones->isEmpty())
            <div class="seccion-vacia">
                <i class="bi bi-clipboard2-pulse"></i>
                <p style="font-weight:600; color:#4b5563;">Sin evoluciones registradas</p>
                @if($paciente->historiaClinica)
                    <a href="{{ route('evoluciones.create', ['paciente_id' => $paciente->id]) }}" class="btn-morado mt-2">
                        <i class="bi bi-clipboard2-plus"></i> Registrar primera evolución
                    </a>
                @else
                    <p style="font-size:.84rem;">Primero debe crear la historia clínica del paciente.</p>
                @endif
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:separate;border-spacing:0;font-size:.85rem;">
                    <thead>
                        <tr>
                            <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.75rem;text-transform:uppercase;padding:.55rem 1rem;border-bottom:2px solid var(--color-muy-claro);">Fecha</th>
                            <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.75rem;text-transform:uppercase;padding:.55rem 1rem;border-bottom:2px solid var(--color-muy-claro);">Procedimiento</th>
                            <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.75rem;text-transform:uppercase;padding:.55rem 1rem;border-bottom:2px solid var(--color-muy-claro);">Dientes</th>
                            <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.75rem;text-transform:uppercase;padding:.55rem 1rem;border-bottom:2px solid var(--color-muy-claro);">Doctor</th>
                            <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.75rem;text-transform:uppercase;padding:.55rem 1rem;border-bottom:2px solid var(--color-muy-claro);text-align:center;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evoluciones as $ev)
                        <tr style="{{ !$loop->last ? 'border-bottom:1px solid var(--fondo-borde);' : '' }}">
                            <td style="padding:.6rem 1rem;color:#4b5563;white-space:nowrap;">{{ $ev->fecha_formateada }}</td>
                            <td style="padding:.6rem 1rem;font-weight:500;color:#1c2b22;">{{ $ev->procedimiento }}</td>
                            <td style="padding:.6rem 1rem;">
                                @if($ev->dientes_tratados)
                                    <span style="background:var(--color-muy-claro);color:var(--color-hover);border-radius:20px;padding:.15rem .55rem;font-size:.72rem;font-weight:600;">{{ $ev->dientes_tratados }}</span>
                                @else
                                    <span style="color:#d1d5db;">—</span>
                                @endif
                            </td>
                            <td style="padding:.6rem 1rem;font-size:.82rem;color:#6b7280;">{{ $ev->doctor ? $ev->doctor->name : '—' }}</td>
                            <td style="padding:.6rem 1rem;text-align:center;">
                                <a href="{{ route('evoluciones.show', $ev) }}"
                                   style="background:none;border:1px solid var(--color-muy-claro);border-radius:6px;width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;color:var(--color-principal);font-size:.85rem;text-decoration:none;"
                                   title="Ver evolución">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Tab: Consentimientos --}}
<div id="tab-consentimientos" class="tab-panel">
    <div class="card-sistema">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;">
            <h6 style="margin:0;font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);letter-spacing:.04em;">
                <i class="bi bi-file-earmark-check me-1"></i> Consentimientos Informados
            </h6>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                <a href="{{ route('consentimientos.index', ['paciente_id' => $paciente->id]) }}"
                   class="btn-outline-morado" style="font-size:.8rem;padding:.32rem .75rem;">
                    <i class="bi bi-list-ul"></i> Ver todos
                </a>
                <a href="{{ route('consentimientos.create', ['paciente_id' => $paciente->id]) }}"
                   class="btn-morado" style="font-size:.8rem;padding:.32rem .75rem;">
                    <i class="bi bi-file-earmark-plus"></i> Nuevo Consentimiento
                </a>
            </div>
        </div>

        @php
            $consentimientos = $paciente->consentimientos()->with('doctor')->take(5)->get();
        @endphp

        @if($consentimientos->isEmpty())
        <div class="seccion-vacia" style="padding:1.5rem .5rem;">
            <i class="bi bi-file-earmark-x" style="font-size:2rem;color:var(--color-acento-activo);display:block;margin-bottom:.5rem;"></i>
            <p style="font-weight:600;color:#4b5563;margin-bottom:.25rem;">Sin consentimientos registrados</p>
            <p style="font-size:.84rem;color:#9ca3af;margin-bottom:.75rem;">No hay consentimientos informados para este paciente.</p>
            <a href="{{ route('consentimientos.create', ['paciente_id' => $paciente->id]) }}" class="btn-morado mt-2" style="display:inline-flex;">
                <i class="bi bi-file-earmark-plus"></i> Crear primer consentimiento
            </a>
        </div>
        @else
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
            <thead>
                <tr style="background:var(--color-muy-claro);">
                    <th style="padding:.55rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:left;">Consentimiento</th>
                    <th style="padding:.55rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:left;">Fecha</th>
                    <th style="padding:.55rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:left;">Estado</th>
                    <th style="padding:.55rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:center;width:80px;">Acción</th>
                </tr>
            </thead>
            <tbody>
            @foreach($consentimientos as $cs)
            <tr style="border-bottom:1px solid var(--fondo-borde);">
                <td style="padding:.55rem .9rem;font-weight:500;color:#1c2b22;max-width:220px;">
                    <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $cs->nombre }}">
                        {{ $cs->nombre }}
                    </span>
                </td>
                <td style="padding:.55rem .9rem;color:#4b5563;white-space:nowrap;font-size:.83rem;">
                    {{ $cs->fecha_generacion->format('d/m/Y') }}
                </td>
                <td style="padding:.55rem .9rem;">
                    @if($cs->firmado)
                    <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.18rem .55rem;border-radius:20px;font-size:.7rem;font-weight:700;background:#D4EDDA;color:#155724;">
                        <i class="bi bi-patch-check-fill"></i> Firmado
                    </span>
                    @else
                    <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.18rem .55rem;border-radius:20px;font-size:.7rem;font-weight:700;background:#FFF3CD;color:#856404;">
                        <i class="bi bi-clock"></i> Pendiente
                    </span>
                    @endif
                </td>
                <td style="padding:.55rem .9rem;text-align:center;">
                    <a href="{{ route('consentimientos.show', $cs) }}"
                       style="color:var(--color-principal);font-size:.82rem;display:inline-flex;align-items:center;gap:.25rem;text-decoration:none;">
                        <i class="bi bi-eye"></i> Ver
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>
</div>

{{-- Tab: Presupuestos --}}
<div id="tab-presupuestos" class="tab-panel">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;">
        <div>
            @php
                $presupuestos = $paciente->presupuestos()->with('doctor')->take(10)->get();
                $totalPresupuestado = $paciente->presupuestos()->where('activo', true)->sum('total');
                $totalAprobado      = $paciente->presupuestos()->where('estado', 'aprobado')->where('activo', true)->sum('total');
                $totalPendiente     = $paciente->presupuestos()->whereIn('estado', ['borrador','enviado'])->where('activo', true)->sum('total');
            @endphp
            <div style="display:flex;gap:1rem;flex-wrap:wrap;font-size:.82rem;">
                <span>Total presupuestado: <strong style="color:var(--color-principal);">$ {{ number_format($totalPresupuestado, 0, ',', '.') }}</strong></span>
                <span>Aprobado: <strong style="color:#166534;">$ {{ number_format($totalAprobado, 0, ',', '.') }}</strong></span>
                <span>Pendiente aprobación: <strong style="color:#92400e;">$ {{ number_format($totalPendiente, 0, ',', '.') }}</strong></span>
            </div>
        </div>
        <a href="{{ route('presupuestos.create', ['paciente_id' => $paciente->id]) }}"
           class="btn-morado" style="font-size:.82rem;padding:.4rem .9rem;">
            <i class="bi bi-plus-lg"></i> Nuevo Presupuesto
        </a>
    </div>

    @if($presupuestos->isEmpty())
    <div class="seccion-vacia" style="padding:1.5rem .5rem;">
        <i class="bi bi-file-earmark-text" style="font-size:2rem;color:var(--color-acento-activo);display:block;margin-bottom:.5rem;"></i>
        <p style="font-weight:600;color:#4b5563;margin-bottom:.25rem;">Sin presupuestos registrados</p>
        <p style="font-size:.84rem;color:#9ca3af;margin-bottom:.75rem;">No hay presupuestos de tratamiento para este paciente.</p>
        <a href="{{ route('presupuestos.create', ['paciente_id' => $paciente->id]) }}" class="btn-morado mt-2" style="display:inline-flex;">
            <i class="bi bi-plus-lg"></i> Crear primer presupuesto
        </a>
    </div>
    @else
    <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
        <thead>
            <tr style="background:var(--color-muy-claro);">
                <th style="padding:.55rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:left;">N° Presupuesto</th>
                <th style="padding:.55rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:left;">Fecha</th>
                <th style="padding:.55rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:right;">Total</th>
                <th style="padding:.55rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:left;">Estado</th>
                <th style="padding:.55rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:center;width:100px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @foreach($presupuestos as $pre)
        @php $colorPre = $pre->estado_color; @endphp
        <tr style="border-bottom:1px solid var(--fondo-borde);">
            <td style="padding:.55rem .9rem;">
                <a href="{{ route('presupuestos.show', $pre) }}"
                   style="font-family:monospace;font-weight:700;color:var(--color-principal);text-decoration:none;font-size:.82rem;">
                    {{ $pre->numero_formateado }}
                </a>
            </td>
            <td style="padding:.55rem .9rem;color:#4b5563;font-size:.83rem;white-space:nowrap;">
                {{ $pre->fecha_generacion->format('d/m/Y') }}
            </td>
            <td style="padding:.55rem .9rem;text-align:right;font-weight:600;color:#1c2b22;white-space:nowrap;">
                $ {{ number_format($pre->total, 0, ',', '.') }}
            </td>
            <td style="padding:.55rem .9rem;">
                <span style="padding:.18rem .55rem;border-radius:20px;font-size:.7rem;font-weight:700;background:{{ $colorPre['bg'] }};color:{{ $colorPre['text'] }};">
                    {{ $colorPre['label'] }}
                </span>
            </td>
            <td style="padding:.55rem .9rem;text-align:center;">
                <div style="display:flex;gap:.4rem;justify-content:center;">
                    <a href="{{ route('presupuestos.show', $pre) }}"
                       style="color:var(--color-principal);font-size:.82rem;display:inline-flex;align-items:center;gap:.2rem;text-decoration:none;" title="Ver">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('presupuestos.pdf', $pre) }}" target="_blank"
                       style="color:#374151;font-size:.82rem;display:inline-flex;align-items:center;gap:.2rem;text-decoration:none;" title="PDF">
                        <i class="bi bi-file-pdf"></i>
                    </a>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    @if($paciente->presupuestos()->where('activo', true)->count() > 10)
    <div style="padding:.6rem .9rem;font-size:.8rem;color:#9ca3af;text-align:center;">
        Mostrando los últimos 10.
        <a href="{{ route('presupuestos.index', ['buscar' => $paciente->nombre_completo]) }}" style="color:var(--color-principal);">Ver todos →</a>
    </div>
    @endif
    @endif
</div>

{{-- Tab: Imágenes Clínicas --}}
<div id="tab-imagenes" class="tab-panel">
    <div class="card-sistema">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;">
            <h6 style="margin:0;font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);letter-spacing:.04em;">
                <i class="bi bi-images me-1"></i> Imágenes Clínicas
            </h6>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                <a href="{{ route('imagenes.galeria', $paciente->id) }}"
                   class="btn-outline-morado" style="font-size:.8rem;padding:.32rem .75rem;">
                    <i class="bi bi-grid-3x3-gap"></i> Ver galería completa
                </a>
                <a href="{{ route('imagenes.create', ['paciente_id' => $paciente->id]) }}"
                   class="btn-morado" style="font-size:.8rem;padding:.32rem .75rem;">
                    <i class="bi bi-cloud-upload"></i> Subir imagen
                </a>
            </div>
        </div>
        @php $ultimasImagenes = $paciente->imagenesClinicas()->take(6)->get(); @endphp
        @if($ultimasImagenes->isEmpty())
        <div class="seccion-vacia" style="padding:1.5rem .5rem;">
            <i class="bi bi-images" style="font-size:2rem;color:var(--color-acento-activo);display:block;margin-bottom:.5rem;"></i>
            <p style="font-weight:600;color:#4b5563;margin-bottom:.25rem;">Sin imágenes registradas</p>
            <p style="font-size:.84rem;color:#9ca3af;">No hay imágenes clínicas para este paciente.</p>
        </div>
        @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:.75rem;">
            @foreach($ultimasImagenes as $img)
            <a href="{{ route('imagenes.show', $img) }}" style="text-decoration:none;">
                <div style="background:var(--fondo-card-alt);border:1px solid var(--color-muy-claro);border-radius:10px;overflow:hidden;transition:transform .15s;display:block;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                    <img src="{{ $img->url }}" alt="{{ $img->titulo }}"
                         style="width:100%;aspect-ratio:1;object-fit:cover;display:block;background:var(--color-muy-claro);"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div style="display:none;width:100%;aspect-ratio:1;align-items:center;justify-content:center;font-size:1.5rem;color:var(--color-acento-activo);background:var(--color-muy-claro);">
                        <i class="bi {{ $img->tipo_icono }}"></i>
                    </div>
                    <div style="padding:.35rem .5rem;font-size:.72rem;color:#6b7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $img->titulo }}">{{ $img->titulo }}</div>
                </div>
            </a>
            @endforeach
        </div>
        @if($paciente->imagenesClinicas()->count() > 6)
        <div style="text-align:center;margin-top:.75rem;">
            <a href="{{ route('imagenes.galeria', $paciente->id) }}" style="font-size:.82rem;color:var(--color-principal);text-decoration:none;">
                Ver todas las imágenes →
            </a>
        </div>
        @endif
        @endif
    </div>
</div>

{{-- Tab: Valoraciones --}}
<div id="tab-valoraciones" class="tab-panel">
    <div class="card-sistema">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;">
            <h6 style="margin:0;font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);letter-spacing:.04em;">
                <i class="bi bi-clipboard2-pulse me-1"></i> Valoraciones
            </h6>
            <a href="{{ route('valoraciones.create', ['paciente_id' => $paciente->id]) }}"
               class="btn-morado" style="font-size:.8rem;padding:.32rem .75rem;">
                <i class="bi bi-plus-lg"></i> Nueva Valoración
            </a>
        </div>
        @php $valoraciones = $paciente->valoraciones()->take(5)->get(); @endphp
        @if($valoraciones->isEmpty())
        <div class="seccion-vacia" style="padding:1.5rem .5rem;">
            <i class="bi bi-clipboard2-pulse" style="font-size:2rem;color:var(--color-acento-activo);display:block;margin-bottom:.5rem;"></i>
            <p style="font-weight:600;color:#4b5563;margin-bottom:.25rem;">Sin valoraciones registradas</p>
            <p style="font-size:.84rem;color:#9ca3af;">No hay valoraciones para este paciente.</p>
        </div>
        @else
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.85rem;">
            <thead>
                <tr style="background:var(--color-muy-claro);">
                    <th style="padding:.5rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);">N° / Fecha</th>
                    <th style="padding:.5rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);">Motivo</th>
                    <th style="padding:.5rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:center;">Dx</th>
                    <th style="padding:.5rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);">Estado</th>
                    <th style="padding:.5rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:center;">Presupuesto</th>
                    <th style="padding:.5rem .9rem;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:center;"></th>
                </tr>
            </thead>
            <tbody>
            @foreach($valoraciones as $val)
            @php $ec = $val->estado_color; @endphp
            <tr style="border-bottom:1px solid var(--fondo-borde);">
                <td style="padding:.5rem .9rem;white-space:nowrap;">
                    <div style="font-family:monospace;font-weight:700;color:var(--color-principal);font-size:.78rem;">{{ $val->numero_valoracion }}</div>
                    <div style="font-size:.73rem;color:#9ca3af;">{{ $val->fecha->format('d/m/Y') }}</div>
                </td>
                <td style="padding:.5rem .9rem;color:#4b5563;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $val->motivo_consulta }}">
                    {{ Str::limit($val->motivo_consulta, 55) }}
                </td>
                <td style="padding:.5rem .9rem;text-align:center;">
                    @if(!empty($val->diagnosticos))
                    <span style="background:var(--color-muy-claro);color:var(--color-principal);border-radius:20px;padding:.1rem .5rem;font-size:.7rem;font-weight:700;">{{ count($val->diagnosticos) }}</span>
                    @else<span style="color:#d1d5db;">—</span>@endif
                </td>
                <td style="padding:.5rem .9rem;">
                    <span style="background:{{ $ec['bg'] }};color:{{ $ec['text'] }};border-radius:20px;padding:.12rem .5rem;font-size:.7rem;font-weight:700;">{{ $ec['label'] }}</span>
                </td>
                <td style="padding:.5rem .9rem;text-align:center;">
                    @if($val->presupuesto_id)
                    <span style="background:#d1fae5;color:#166534;border-radius:20px;padding:.1rem .5rem;font-size:.7rem;font-weight:700;"><i class="bi bi-check-circle"></i> Sí</span>
                    @else<span style="color:#d1d5db;font-size:.75rem;">—</span>@endif
                </td>
                <td style="padding:.5rem .9rem;text-align:center;">
                    <a href="{{ route('valoraciones.show', $val) }}"
                       style="color:var(--color-principal);font-size:.82rem;display:inline-flex;align-items:center;gap:.25rem;text-decoration:none;">
                        <i class="bi bi-eye"></i> Ver
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>
</div>

{{-- Tab: Laboratorio --}}
<div id="tab-laboratorio" class="tab-panel">
    <div class="card-sistema">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;">
            <h6 style="margin:0;font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);letter-spacing:.04em;">
                <i class="bi bi-flask me-1"></i> Órdenes de Laboratorio
            </h6>
            <a href="{{ route('laboratorio.create', ['paciente_id' => $paciente->id]) }}"
               class="btn-morado" style="font-size:.8rem;padding:.32rem .75rem;">
                <i class="bi bi-plus-lg"></i> Nueva Orden
            </a>
        </div>
        @php $ordenesLab = $paciente->ordenesLaboratorio()->with('laboratorio')->get(); @endphp
        @if($ordenesLab->isEmpty())
            <p style="color:#9ca3af;font-size:.83rem;text-align:center;padding:1.5rem 0;margin:0;">
                <i class="bi bi-flask" style="font-size:1.5rem;display:block;margin-bottom:.5rem;"></i>
                No hay órdenes de laboratorio registradas
            </p>
        @else
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.83rem;">
            <thead>
                <tr style="border-bottom:2px solid var(--fondo-borde);">
                    <th style="padding:.4rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;letter-spacing:.04em;">N° Orden</th>
                    <th style="padding:.4rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Laboratorio</th>
                    <th style="padding:.4rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Tipo Trabajo</th>
                    <th style="padding:.4rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Entrega Est.</th>
                    <th style="padding:.4rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Estado</th>
                    <th style="padding:.4rem .9rem;text-align:center;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Ver</th>
                </tr>
            </thead>
            <tbody>
            @foreach($ordenesLab as $ol)
            @php
                $badgesOl = ['pendiente'=>['#fff3cd','#856404'],'enviado'=>['#d1ecf1','#0c5460'],'en_proceso'=>['#cce5ff','#004085'],'recibido'=>['#d4edda','#155724'],'instalado'=>['#d6d8d9','#1b1e21'],'cancelado'=>['#f8d7da','#721c24']];
                $bc = $badgesOl[$ol->estado] ?? ['#f3f4f6','#374151'];
            @endphp
            <tr style="border-bottom:1px solid var(--fondo-borde);">
                <td style="padding:.5rem .9rem;font-family:monospace;font-weight:700;color:var(--color-principal);font-size:.78rem;">{{ $ol->numero_orden }}</td>
                <td style="padding:.5rem .9rem;color:#4b5563;">{{ $ol->laboratorio->nombre ?? '—' }}</td>
                <td style="padding:.5rem .9rem;color:#1c2b22;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $ol->tipo_trabajo }}</td>
                <td style="padding:.5rem .9rem;color:{{ $ol->esta_vencido ? '#dc2626' : '#4b5563' }};font-weight:{{ $ol->esta_vencido ? '700' : '400' }};">
                    {{ $ol->fecha_entrega_estimada?->format('d/m/Y') ?: '—' }}
                </td>
                <td style="padding:.5rem .9rem;">
                    <span style="background:{{ $bc[0] }};color:{{ $bc[1] }};border-radius:20px;padding:.12rem .5rem;font-size:.7rem;font-weight:700;">{{ $ol->estado_label }}</span>
                </td>
                <td style="padding:.5rem .9rem;text-align:center;">
                    <a href="{{ route('laboratorio.show', $ol) }}"
                       style="color:var(--color-principal);font-size:.82rem;display:inline-flex;align-items:center;gap:.25rem;text-decoration:none;">
                        <i class="bi bi-eye"></i> Ver
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>
</div>

@modulo('ortodoncia')
{{-- Tab: Ortodoncia --}}
<div id="tab-ortodoncia" class="tab-panel">
    <div class="card-sistema">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;">
            <h6 style="margin:0;font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);letter-spacing:.04em;">
                <i class="bi bi-braces me-1"></i> Ortodoncia
            </h6>
            <a href="{{ route('ortodoncia.create', ['paciente_id' => $paciente->id]) }}"
               class="btn-morado" style="font-size:.8rem;padding:.32rem .75rem;">
                <i class="bi bi-plus-lg"></i> Nueva Ficha
            </a>
        </div>

        @if($paciente->fichaOrtodonciaActiva)
        <div style="background:var(--color-muy-claro);border:1px solid var(--color-claro);border-radius:10px;padding:.85rem 1.25rem;margin-bottom:1rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;">
                <div>
                    <span style="font-size:.7rem;font-weight:700;color:var(--color-principal);text-transform:uppercase;letter-spacing:.06em;">
                        <i class="bi bi-braces me-1"></i> Tratamiento activo
                    </span>
                    <div style="font-size:.88rem;color:var(--texto-principal);margin-top:.2rem;font-weight:600;">
                        {{ $paciente->fichaOrtodonciaActiva->numero_ficha }} —
                        {{ $paciente->fichaOrtodonciaActiva->tipo_ortodoncia_label }}
                    </div>
                    <div style="font-size:.75rem;color:var(--texto-secundario);margin-top:.1rem;">
                        Inicio: {{ $paciente->fichaOrtodonciaActiva->fecha_inicio->format('d/m/Y') }}
                        &nbsp;·&nbsp; Progreso: {{ $paciente->fichaOrtodonciaActiva->progreso }}%
                    </div>
                </div>
                <a href="{{ route('ortodoncia.show', $paciente->fichaOrtodonciaActiva) }}"
                   style="background:var(--color-principal);color:white;text-decoration:none;padding:.4rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;">
                    Ver ficha
                </a>
            </div>
        </div>
        @endif

        @php $fichasOrtodoncia = $paciente->fichasOrtodoncia()->with('ultimoControl')->get(); @endphp
        @if($fichasOrtodoncia->isEmpty())
            <p style="color:#9ca3af;font-size:.83rem;text-align:center;padding:1.5rem 0;margin:0;">
                <i class="bi bi-braces" style="font-size:1.5rem;display:block;margin-bottom:.5rem;"></i>
                No hay fichas ortodónticas registradas
            </p>
        @else
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.83rem;">
            <thead>
                <tr style="border-bottom:2px solid var(--fondo-borde);">
                    <th style="padding:.4rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">N° Ficha</th>
                    <th style="padding:.4rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Tipo</th>
                    <th style="padding:.4rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Inicio</th>
                    <th style="padding:.4rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Progreso</th>
                    <th style="padding:.4rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Estado</th>
                    <th style="padding:.4rem .9rem;text-align:center;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Ver</th>
                </tr>
            </thead>
            <tbody>
            @foreach($fichasOrtodoncia as $fo)
            @php
                $badges = ['diagnostico'=>['#dbeafe','#1e40af'],'activo'=>['#d1fae5','#065f46'],'retencion'=>['#fef3c7','#92400e'],'finalizado'=>['#f3f4f6','#374151'],'cancelado'=>['#fee2e2','#7f1d1d']];
                $bc = $badges[$fo->estado] ?? ['#f3f4f6','#374151'];
            @endphp
            <tr style="border-bottom:1px solid var(--fondo-borde);">
                <td style="padding:.5rem .9rem;font-family:monospace;font-weight:700;color:var(--color-principal);font-size:.78rem;">{{ $fo->numero_ficha }}</td>
                <td style="padding:.5rem .9rem;font-size:.8rem;">{{ $fo->tipo_ortodoncia_label }}</td>
                <td style="padding:.5rem .9rem;color:#4b5563;">{{ $fo->fecha_inicio->format('d/m/Y') }}</td>
                <td style="padding:.5rem .9rem;">
                    <div style="display:flex;align-items:center;gap:.4rem;">
                        <div style="width:60px;background:var(--fondo-borde);border-radius:20px;height:6px;">
                            <div style="width:{{ $fo->progreso }}%;background:var(--color-principal);border-radius:20px;height:6px;"></div>
                        </div>
                        <span style="font-size:.72rem;font-weight:700;color:var(--color-principal);">{{ $fo->progreso }}%</span>
                    </div>
                </td>
                <td style="padding:.5rem .9rem;">
                    <span style="background:{{ $bc[0] }};color:{{ $bc[1] }};border-radius:20px;padding:.1rem .5rem;font-size:.7rem;font-weight:700;">{{ $fo->estado_label }}</span>
                </td>
                <td style="padding:.5rem .9rem;text-align:center;">
                    <a href="{{ route('ortodoncia.show', $fo) }}" style="color:var(--color-principal);font-size:.82rem;">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>
</div>
@endmodulo

@modulo('recetas')
{{-- Tab: Recetas Médicas --}}
<div id="tab-recetas" class="tab-panel">
    <div class="card-sistema">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;">
            <h6 style="margin:0;font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);letter-spacing:.04em;">
                <i class="bi bi-file-medical me-1"></i> Recetas Médicas
            </h6>
            <a href="{{ route('recetas.create', ['paciente_id' => $paciente->id]) }}"
               class="btn-morado" style="font-size:.8rem;padding:.32rem .75rem;">
                <i class="bi bi-plus-lg"></i> Nueva Receta
            </a>
        </div>

        @php $recetasPaciente = $paciente->recetasMedicas()->where('activo', true)->with('doctor')->get(); @endphp

        @if($recetasPaciente->isEmpty())
        <div style="text-align:center;padding:2rem;color:var(--texto-secundario);font-size:.84rem;">
            <i class="bi bi-file-medical" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.4;"></i>
            No hay recetas médicas registradas
        </div>
        @else
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.82rem;">
            <thead>
                <tr style="background:var(--fondo-card-alt);">
                    <th style="padding:.5rem .9rem;text-align:left;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">N° Receta</th>
                    <th style="padding:.5rem .9rem;text-align:left;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Fecha</th>
                    <th style="padding:.5rem .9rem;text-align:left;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Doctor</th>
                    <th style="padding:.5rem .9rem;text-align:left;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Diagnóstico</th>
                    <th style="padding:.5rem .9rem;text-align:left;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Items</th>
                    <th style="padding:.5rem .9rem;text-align:left;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Estado</th>
                    <th style="padding:.5rem .9rem;text-align:center;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);">Acc.</th>
                </tr>
            </thead>
            <tbody>
            @foreach($recetasPaciente as $receta)
            <tr style="border-bottom:1px solid var(--fondo-borde);">
                <td style="padding:.5rem .9rem;font-family:monospace;font-weight:700;color:var(--color-principal);font-size:.78rem;">{{ $receta->numero_receta }}</td>
                <td style="padding:.5rem .9rem;color:#4b5563;">{{ $receta->fecha->format('d/m/Y') }}</td>
                <td style="padding:.5rem .9rem;font-size:.8rem;">{{ $receta->doctor->name }}</td>
                <td style="padding:.5rem .9rem;font-size:.8rem;color:var(--texto-secundario);">{{ Str::limit($receta->diagnostico, 35) ?: '—' }}</td>
                <td style="padding:.5rem .9rem;">
                    <span style="background:var(--color-muy-claro);color:var(--color-principal);font-size:.7rem;font-weight:600;padding:.15rem .5rem;border-radius:20px;">
                        {{ $receta->total_medicamentos }}
                    </span>
                </td>
                <td style="padding:.5rem .9rem;">
                    @if($receta->firmado)
                    <span style="background:#dcfce7;color:#166534;border-radius:20px;padding:.1rem .5rem;font-size:.7rem;font-weight:700;">Firmada</span>
                    @else
                    <span style="background:#fef3c7;color:#92400e;border-radius:20px;padding:.1rem .5rem;font-size:.7rem;font-weight:700;">Pendiente</span>
                    @endif
                </td>
                <td style="padding:.5rem .9rem;text-align:center;">
                    <div style="display:flex;gap:.3rem;justify-content:center;">
                        <a href="{{ route('recetas.show', $receta) }}" style="color:var(--color-principal);font-size:.82rem;" title="Ver"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('recetas.pdf', $receta) }}" target="_blank" style="color:#16a34a;font-size:.82rem;" title="PDF"><i class="bi bi-file-pdf"></i></a>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>
</div>
@endmodulo

@modulo('periodoncia')
{{-- Tab: Periodoncia --}}
<div id="tab-periodoncia" class="tab-panel">
    <div class="card-sistema">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;">
            <h6 style="margin:0;font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);letter-spacing:.04em;">
                <i class="bi bi-heart-pulse me-1"></i> Periodoncia
            </h6>
            <a href="{{ route('periodoncia.create', ['paciente_id' => $paciente->id]) }}"
               class="btn-morado" style="font-size:.8rem;padding:.32rem .75rem;">
                <i class="bi bi-plus-lg"></i> Nueva Ficha
            </a>
        </div>

        @php $fichasPeriodoncia = App\Models\FichaPeriodontal::where('paciente_id', $paciente->id)->where('activo', true)->with('ultimoControl')->orderBy('created_at','desc')->get(); @endphp

        @php $fichaActiva = $fichasPeriodoncia->whereIn('estado', ['activa','en_tratamiento'])->first(); @endphp
        @if($fichaActiva)
        <div style="background:var(--color-muy-claro);border:1px solid var(--color-claro);border-radius:10px;padding:.85rem 1.25rem;margin-bottom:1rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;">
                <div>
                    <span style="font-size:.7rem;font-weight:700;color:var(--color-principal);text-transform:uppercase;letter-spacing:.06em;">
                        <i class="bi bi-heart-pulse me-1"></i> Tratamiento activo
                    </span>
                    <div style="font-size:.88rem;color:var(--texto-principal);margin-top:.2rem;font-weight:600;">
                        {{ $fichaActiva->numero_ficha }} — {{ $fichaActiva->clasificacion_label }}
                    </div>
                    <div style="font-size:.75rem;color:var(--texto-secundario);margin-top:.1rem;">
                        Inicio: {{ $fichaActiva->fecha_inicio->format('d/m/Y') }}
                        &nbsp;·&nbsp; Estado: {{ $fichaActiva->estado_label }}
                        &nbsp;·&nbsp; {{ $fichaActiva->controles()->count() }} controles
                    </div>
                </div>
                <a href="{{ route('periodoncia.show', $fichaActiva) }}"
                   style="background:var(--color-principal);color:white;text-decoration:none;padding:.4rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;">
                    Ver ficha
                </a>
            </div>
        </div>
        @endif

        @if($fichasPeriodoncia->isEmpty())
            <p style="color:#9ca3af;font-size:.83rem;text-align:center;padding:1.5rem 0;margin:0;">
                <i class="bi bi-heart-pulse" style="font-size:1.5rem;display:block;margin-bottom:.5rem;"></i>
                No hay fichas periodontales registradas
            </p>
        @else
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.83rem;">
            <thead>
                <tr style="border-bottom:2px solid var(--fondo-borde);">
                    <th style="padding:.4rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">N° Ficha</th>
                    <th style="padding:.4rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Clasificación</th>
                    <th style="padding:.4rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Inicio</th>
                    <th style="padding:.4rem .9rem;text-align:center;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Placa</th>
                    <th style="padding:.4rem .9rem;text-align:center;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Sesiones</th>
                    <th style="padding:.4rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Estado</th>
                    <th style="padding:.4rem .9rem;text-align:center;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Ver</th>
                </tr>
            </thead>
            <tbody>
            @foreach($fichasPeriodoncia as $fp)
            @php
                $badgesPer = ['activa'=>['#d1fae5','#065f46'],'en_tratamiento'=>['#dbeafe','#1e40af'],'mantenimiento'=>['#fef3c7','#92400e'],'finalizada'=>['#f3f4f6','#374151'],'abandonada'=>['#fee2e2','#7f1d1d']];
                $bcp = $badgesPer[$fp->estado] ?? ['#f3f4f6','#374151'];
                $pp = $fp->indice_placa_porcentaje;
            @endphp
            <tr style="border-bottom:1px solid var(--fondo-borde);">
                <td style="padding:.5rem .9rem;font-family:monospace;font-weight:700;color:var(--color-principal);font-size:.78rem;">{{ $fp->numero_ficha }}</td>
                <td style="padding:.5rem .9rem;font-size:.8rem;">{{ Str::limit($fp->clasificacion_label, 30) ?: '—' }}</td>
                <td style="padding:.5rem .9rem;color:#4b5563;">{{ $fp->fecha_inicio->format('d/m/Y') }}</td>
                <td style="padding:.5rem .9rem;text-align:center;font-weight:700;font-size:.78rem;">
                    @if($pp !== null)
                    <span style="color:{{ $pp < 20 ? '#16a34a' : ($pp < 40 ? '#d97706' : '#dc2626') }};">{{ number_format($pp,1) }}%</span>
                    @else <span style="color:#9ca3af;">—</span> @endif
                </td>
                <td style="padding:.5rem .9rem;text-align:center;font-weight:700;color:var(--color-principal);">{{ $fp->controles()->count() }}</td>
                <td style="padding:.5rem .9rem;">
                    <span style="background:{{ $bcp[0] }};color:{{ $bcp[1] }};border-radius:20px;padding:.1rem .5rem;font-size:.7rem;font-weight:700;">{{ $fp->estado_label }}</span>
                </td>
                <td style="padding:.5rem .9rem;text-align:center;">
                    <a href="{{ route('periodoncia.show', $fp) }}" style="color:var(--color-principal);font-size:.82rem;">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>
</div>
@endmodulo

@push('scripts')
<script>
function cambiarTab(id) {
    document.querySelectorAll('.pac-tab').forEach(t => t.classList.remove('activo'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('activo'));
    document.getElementById('tab-' + id).classList.add('activo');
    event.currentTarget.classList.add('activo');
}
</script>
@endpush

@endsection
