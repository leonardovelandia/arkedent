@extends('layouts.app')
@section('titulo', 'Periodoncia')

@section('contenido')

{{-- Header --}}
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h2 class="page-titulo" style="margin:0;font-size:1.4rem;">
            <i class="bi bi-heart-pulse" style="color:var(--color-principal);margin-right:.4rem;"></i>
            Periodoncia
        </h2>
        <p class="page-subtitulo" style="margin:0;color:var(--texto-secundario);font-size:.83rem;">Gestión de fichas periodontales</p>
    </div>
    <a href="{{ route('periodoncia.create') }}"
       style="background:var(--color-principal);color:white;border:none;padding:.5rem 1.1rem;border-radius:8px;font-size:.84rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;box-shadow:0 2px 8px var(--sombra-principal);">
        <i class="bi bi-plus-lg"></i> Nueva Ficha Periodontal
    </a>
</div>

{{-- Flash --}}
@if(session('exito'))
<div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <i class="bi bi-check-circle me-1"></i> {{ session('exito') }}
</div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:#22c55e;">{{ $stats['activas'] }}</div>
            <div style="font-size:.73rem;color:var(--texto-secundario);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Activas</div>
            <i class="bi bi-clipboard2-heart" style="font-size:1.2rem;color:#22c55e;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:var(--color-principal);">{{ $stats['en_tratamiento'] }}</div>
            <div style="font-size:.73rem;color:var(--texto-secundario);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">En tratamiento</div>
            <i class="bi bi-heart-pulse" style="font-size:1.2rem;color:var(--color-principal);opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:#f59e0b;">{{ $stats['mantenimiento'] }}</div>
            <div style="font-size:.73rem;color:var(--texto-secundario);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Mantenimiento</div>
            <i class="bi bi-shield-check" style="font-size:1.2rem;color:#f59e0b;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:#8b5cf6;">{{ $stats['controles_mes'] }}</div>
            <div style="font-size:.73rem;color:var(--texto-secundario);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Controles este mes</div>
            <i class="bi bi-calendar-check" style="font-size:1.2rem;color:#8b5cf6;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="card-sistema mb-3">
    <form method="GET" action="{{ route('periodoncia.index') }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <x-buscador-paciente
                    :pacientes="$pacientes"
                    :valorInicial="request('paciente_id')"
                    placeholder="Buscar paciente..."
                />
            </div>
            <div class="col-md-2">
                <select name="estado" onchange="this.form.submit()"
                        style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.42rem .75rem;font-size:.84rem;background:var(--fondo-app);color:var(--texto-principal);">
                    <option value="">Todos los estados</option>
                    <option value="activa" {{ request('estado')=='activa'?'selected':'' }}>Activa</option>
                    <option value="en_tratamiento" {{ request('estado')=='en_tratamiento'?'selected':'' }}>En tratamiento</option>
                    <option value="mantenimiento" {{ request('estado')=='mantenimiento'?'selected':'' }}>Mantenimiento</option>
                    <option value="finalizada" {{ request('estado')=='finalizada'?'selected':'' }}>Finalizada</option>
                    <option value="abandonada" {{ request('estado')=='abandonada'?'selected':'' }}>Abandonada</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="desde" value="{{ request('desde') }}" onchange="this.form.submit()"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.42rem .75rem;font-size:.84rem;background:var(--fondo-app);color:var(--texto-principal);">
            </div>
            <div class="col-md-2">
                <input type="date" name="hasta" value="{{ request('hasta') }}" onchange="this.form.submit()"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.42rem .75rem;font-size:.84rem;background:var(--fondo-app);color:var(--texto-principal);">
            </div>
            <div class="col-md-2" style="display:flex;gap:.5rem;">
                <button type="submit"
                        style="flex:1;background:var(--color-principal);color:white;border:none;border-radius:8px;padding:.42rem .75rem;font-size:.84rem;cursor:pointer;">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <a href="{{ route('periodoncia.index') }}"
                   style="flex:1;background:var(--fondo-borde);color:var(--texto-principal);border-radius:8px;padding:.42rem .75rem;font-size:.84rem;text-align:center;text-decoration:none;">
                    Limpiar
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Tabla --}}
<div class="card-sistema">
    @if($fichas->isEmpty())
        <div style="text-align:center;padding:3rem 1rem;color:var(--texto-secundario);">
            <i class="bi bi-heart-pulse" style="font-size:2.5rem;display:block;margin-bottom:.75rem;opacity:.3;"></i>
            <p style="margin:0;font-size:.9rem;">No hay fichas periodontales registradas</p>
            <a href="{{ route('periodoncia.create') }}" style="color:var(--color-principal);font-size:.83rem;margin-top:.5rem;display:inline-block;">
                <i class="bi bi-plus-circle me-1"></i> Crear primera ficha
            </a>
        </div>
    @else
    <div style="overflow-x:auto;max-height:520px;overflow-y:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:.83rem;">
        <thead style="position:sticky;top:0;z-index:2;background:var(--fondo-app);">
            <tr style="border-bottom:2px solid var(--fondo-borde);">
                <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;letter-spacing:.04em;">N° Ficha</th>
                <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Paciente</th>
                <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Clasificación</th>
                <th style="padding:.45rem .9rem;text-align:center;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Índice Placa</th>
                <th style="padding:.45rem .9rem;text-align:center;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Sesiones</th>
                <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Estado</th>
                <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Últ. control</th>
                <th style="padding:.45rem .9rem;text-align:center;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @foreach($fichas as $ficha)
        @php
            $estadoBadges = [
                'activa'         => ['#d1fae5','#065f46'],
                'en_tratamiento' => ['#dbeafe','#1e40af'],
                'mantenimiento'  => ['#fef3c7','#92400e'],
                'finalizada'     => ['#f3f4f6','#374151'],
                'abandonada'     => ['#fee2e2','#7f1d1d'],
            ];
            $bc = $estadoBadges[$ficha->estado] ?? ['#f3f4f6','#374151'];
            $placa = $ficha->indice_placa_porcentaje;
            $placaColor = $placa === null ? '#9ca3af' : ($placa < 20 ? '#16a34a' : ($placa < 40 ? '#d97706' : '#dc2626'));
        @endphp
        <tr style="border-bottom:1px solid var(--fondo-borde);transition:background .15s;"
            onmouseover="this.style.background='var(--fondo-card-alt)'"
            onmouseout="this.style.background='transparent'">
            <td style="padding:.55rem .9rem;">
                <span style="font-family:monospace;font-weight:700;color:var(--color-principal);font-size:.78rem;">{{ $ficha->numero_ficha }}</span>
            </td>
            <td style="padding:.55rem .9rem;">
                <a href="{{ route('pacientes.show', $ficha->paciente_id) }}"
                   style="color:var(--texto-principal);text-decoration:none;font-weight:600;">
                    {{ $ficha->paciente->nombre_completo }}
                </a>
                <div style="font-size:.72rem;color:var(--texto-secundario);">{{ $ficha->paciente->numero_documento }}</div>
            </td>
            <td style="padding:.55rem .9rem;">
                @if($ficha->clasificacion_periodontal)
                <span style="background:var(--color-muy-claro);color:var(--color-principal);border-radius:20px;padding:.12rem .6rem;font-size:.7rem;font-weight:600;white-space:nowrap;">
                    {{ Str::limit($ficha->clasificacion_label, 28) }}
                </span>
                @else
                <span style="color:#9ca3af;">—</span>
                @endif
            </td>
            <td style="padding:.55rem .9rem;text-align:center;">
                @if($placa !== null)
                <span style="font-weight:700;font-size:.82rem;color:{{ $placaColor }};">{{ number_format($placa, 1) }}%</span>
                @else
                <span style="color:#9ca3af;">—</span>
                @endif
            </td>
            <td style="padding:.55rem .9rem;text-align:center;">
                <span style="font-weight:700;font-size:.85rem;color:var(--color-principal);">{{ $ficha->controles->count() }}</span>
            </td>
            <td style="padding:.55rem .9rem;">
                <span style="background:{{ $bc[0] }};color:{{ $bc[1] }};border-radius:20px;padding:.12rem .65rem;font-size:.7rem;font-weight:700;">
                    {{ $ficha->estado_label }}
                </span>
            </td>
            <td style="padding:.55rem .9rem;color:var(--texto-secundario);font-size:.78rem;">
                @if($ficha->ultimoControl)
                    {{ $ficha->ultimoControl->fecha_control->format('d/m/Y') }}<br>
                    <span style="color:var(--color-principal);font-weight:600;">Ses. #{{ $ficha->ultimoControl->numero_sesion }}</span>
                @else
                    <span style="color:#9ca3af;">Sin controles</span>
                @endif
            </td>
            <td style="padding:.55rem .9rem;text-align:center;">
                <div style="display:flex;gap:.35rem;justify-content:center;flex-wrap:nowrap;">
                    <a href="{{ route('periodoncia.show', $ficha) }}"
                       title="Ver ficha"
                       style="background:var(--color-muy-claro);color:var(--color-principal);padding:.3rem .6rem;border-radius:6px;font-size:.75rem;text-decoration:none;">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('periodoncia.controles.create', $ficha) }}"
                       title="Nuevo control"
                       style="background:#eff6ff;color:#1e40af;padding:.3rem .6rem;border-radius:6px;font-size:.75rem;text-decoration:none;">
                        <i class="bi bi-plus-circle"></i>
                    </a>
                    <a href="{{ route('periodoncia.pdf', $ficha) }}"
                       target="_blank"
                       title="PDF"
                       style="background:#fef3c7;color:#92400e;padding:.3rem .6rem;border-radius:6px;font-size:.75rem;text-decoration:none;">
                        <i class="bi bi-file-pdf"></i>
                    </a>
                    <form method="POST" action="{{ route('periodoncia.destroy', $ficha) }}"
                          onsubmit="return confirm('¿Eliminar esta ficha periodontal?');" style="margin:0;">
                        @csrf @method('DELETE')
                        <button type="submit"
                                title="Eliminar"
                                style="background:#fee2e2;color:#7f1d1d;border:none;padding:.3rem .6rem;border-radius:6px;font-size:.75rem;cursor:pointer;">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    <div style="margin-top:1rem;">
        {{ $fichas->links() }}
    </div>
    @endif
</div>

@endsection
