@extends('layouts.app')
@section('titulo', 'Ortodoncia')

@section('contenido')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h2 class="page-titulo" style="margin:0;font-size:1.4rem;">
            <i class="bi bi-symmetry-horizontal" style="color:var(--color-principal);margin-right:.4rem;"></i>
            Ortodoncia
        </h2>
        <p style="margin:0;color:var(--texto-secundario);font-size:.83rem;">Gestión de tratamientos ortodónticos</p>
    </div>
    <a href="{{ route('ortodoncia.create') }}"
       style="background:var(--color-principal);color:white;border:none;padding:.5rem 1.1rem;border-radius:8px;font-size:.84rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;box-shadow:0 2px 8px var(--sombra-principal);">
        <i class="bi bi-plus-lg"></i> Nueva Ficha Ortodóntica
    </a>
</div>

{{-- Flash --}}
@if(session('exito'))
<div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <i class="bi bi-check-circle me-1"></i> {{ session('exito') }}
</div>
@endif

{{-- Cards resumen --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;">
            <div style="font-size:1.8rem;font-weight:800;color:var(--color-principal);">{{ $totalActivos }}</div>
            <div style="font-size:.75rem;color:var(--texto-secundario);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">En tratamiento</div>
            <i class="bi bi-symmetry-horizontal" style="font-size:1.2rem;color:var(--color-principal);opacity:.4;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;">
            <div style="font-size:1.8rem;font-weight:800;color:#D97706;">{{ $totalRetencion }}</div>
            <div style="font-size:.75rem;color:var(--texto-secundario);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">En retención</div>
            <i class="bi bi-shield-check" style="font-size:1.2rem;color:#D97706;opacity:.4;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;">
            <div style="font-size:1.8rem;font-weight:800;color:#2563EB;">{{ $finalizadosAnio }}</div>
            <div style="font-size:.75rem;color:var(--texto-secundario);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Finalizados {{ date('Y') }}</div>
            <i class="bi bi-trophy" style="font-size:1.2rem;color:#2563EB;opacity:.4;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;">
            <div style="font-size:1.8rem;font-weight:800;color:var(--color-principal);">{{ $controlesEsteMes }}</div>
            <div style="font-size:.75rem;color:var(--texto-secundario);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Controles este mes</div>
            <i class="bi bi-calendar-check" style="font-size:1.2rem;color:var(--color-principal);opacity:.4;"></i>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="card-sistema mb-3">
    <form method="GET" action="{{ route('ortodoncia.index') }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Buscar paciente, documento..."
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.42rem .75rem;font-size:.84rem;background:var(--fondo-app);color:var(--texto-principal);">
            </div>
            <div class="col-md-2">
                <select name="estado" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.42rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                    <option value="">Todos los estados</option>
                    <option value="diagnostico" {{ request('estado')=='diagnostico'?'selected':'' }}>Diagnóstico</option>
                    <option value="activo" {{ request('estado')=='activo'?'selected':'' }}>En tratamiento</option>
                    <option value="retencion" {{ request('estado')=='retencion'?'selected':'' }}>Retención</option>
                    <option value="finalizado" {{ request('estado')=='finalizado'?'selected':'' }}>Finalizado</option>
                    <option value="cancelado" {{ request('estado')=='cancelado'?'selected':'' }}>Cancelado</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="desde" value="{{ request('desde') }}"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.42rem .75rem;font-size:.84rem;background:var(--fondo-app);">
            </div>
            <div class="col-md-2">
                <input type="date" name="hasta" value="{{ request('hasta') }}"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.42rem .75rem;font-size:.84rem;background:var(--fondo-app);">
            </div>
            <div class="col-md-2" style="display:flex;gap:.5rem;">
                <button type="submit" style="flex:1;background:var(--color-principal);color:white;border:none;border-radius:8px;padding:.42rem .75rem;font-size:.84rem;cursor:pointer;">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <a href="{{ route('ortodoncia.index') }}" style="flex:1;background:var(--fondo-borde);color:var(--texto-principal);border-radius:8px;padding:.42rem .75rem;font-size:.84rem;text-align:center;text-decoration:none;">
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
            <i class="bi bi-symmetry-horizontal" style="font-size:2.5rem;display:block;margin-bottom:.75rem;opacity:.3;"></i>
            <p style="margin:0;font-size:.9rem;">No hay fichas ortodónticas registradas</p>
            <a href="{{ route('ortodoncia.create') }}" style="color:var(--color-principal);font-size:.83rem;margin-top:.5rem;display:inline-block;">
                <i class="bi bi-plus-circle me-1"></i> Crear primera ficha
            </a>
        </div>
    @else
    <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:.83rem;">
        <thead>
            <tr style="border-bottom:2px solid var(--fondo-borde);">
                <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;letter-spacing:.04em;">N° Ficha</th>
                <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Paciente</th>
                <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Tipo</th>
                <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Clase molar</th>
                <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Inicio</th>
                <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Duración</th>
                <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;min-width:110px;">Progreso</th>
                <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Estado</th>
                <th style="padding:.45rem .9rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Últ. control</th>
                <th style="padding:.45rem .9rem;text-align:center;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @foreach($fichas as $ficha)
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
        <tr style="border-bottom:1px solid var(--fondo-borde);transition:background .15s;" onmouseover="this.style.background='var(--fondo-card-alt)'" onmouseout="this.style.background='transparent'">
            <td style="padding:.55rem .9rem;">
                <span style="font-family:monospace;font-weight:700;color:var(--color-principal);font-size:.78rem;">{{ $ficha->numero_ficha }}</span>
            </td>
            <td style="padding:.55rem .9rem;">
                <a href="{{ route('pacientes.show', $ficha->paciente_id) }}" style="color:var(--texto-principal);text-decoration:none;font-weight:600;">
                    {{ $ficha->paciente->nombre_completo }}
                </a>
                <div style="font-size:.72rem;color:var(--texto-secundario);">{{ $ficha->paciente->numero_documento }}</div>
            </td>
            <td style="padding:.55rem .9rem;">
                @if($ficha->tipo_ortodoncia)
                <span style="background:var(--color-muy-claro);color:var(--color-principal);border-radius:20px;padding:.12rem .6rem;font-size:.7rem;font-weight:600;white-space:nowrap;">
                    {{ $ficha->tipo_ortodoncia_label }}
                </span>
                @else <span style="color:#9ca3af;">—</span> @endif
            </td>
            <td style="padding:.55rem .9rem;font-size:.78rem;color:var(--texto-secundario);">
                {{ $ficha->clase_molar_label }}
            </td>
            <td style="padding:.55rem .9rem;color:#4b5563;white-space:nowrap;">
                {{ $ficha->fecha_inicio->format('d/m/Y') }}
            </td>
            <td style="padding:.55rem .9rem;color:#4b5563;font-size:.78rem;">
                @if($ficha->duracion_meses_estimada)
                    {{ $ficha->duracion_real }}m / {{ $ficha->duracion_meses_estimada }}m est.
                @else
                    {{ $ficha->duracion_real }}m
                @endif
            </td>
            <td style="padding:.55rem .9rem;">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div style="flex:1;background:var(--fondo-borde);border-radius:20px;height:7px;min-width:60px;">
                        <div style="width:{{ $progreso }}%;background:var(--color-principal);border-radius:20px;height:7px;transition:width .3s;"></div>
                    </div>
                    <span style="font-size:.72rem;font-weight:700;color:var(--color-principal);min-width:28px;">{{ $progreso }}%</span>
                </div>
            </td>
            <td style="padding:.55rem .9rem;">
                <span style="background:{{ $bc[0] }};color:{{ $bc[1] }};border-radius:20px;padding:.12rem .6rem;font-size:.7rem;font-weight:700;">
                    {{ $ficha->estado_label }}
                </span>
            </td>
            <td style="padding:.55rem .9rem;color:var(--texto-secundario);font-size:.78rem;">
                @if($ficha->ultimoControl)
                    {{ $ficha->ultimoControl->fecha_control->format('d/m/Y') }}<br>
                    <span style="color:var(--color-principal);">Ses. #{{ $ficha->ultimoControl->numero_sesion }}</span>
                @else
                    <span style="color:#9ca3af;">Sin controles</span>
                @endif
            </td>
            <td style="padding:.55rem .9rem;text-align:center;">
                <div style="display:flex;gap:.35rem;justify-content:center;">
                    <a href="{{ route('ortodoncia.show', $ficha) }}"
                       style="background:var(--color-muy-claro);color:var(--color-principal);border:none;padding:.3rem .6rem;border-radius:6px;font-size:.75rem;text-decoration:none;display:inline-flex;align-items:center;gap:.25rem;">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('ortodoncia.edit', $ficha) }}"
                       style="background:#f0fdf4;color:#065f46;border:none;padding:.3rem .6rem;border-radius:6px;font-size:.75rem;text-decoration:none;">
                        <i class="bi bi-pencil"></i>
                    </a>
                    @if(in_array($ficha->estado, ['diagnostico','activo']))
                    <a href="{{ route('controles.create', ['ficha_ortodontica_id' => $ficha->id]) }}"
                       style="background:#eff6ff;color:#1e40af;border:none;padding:.3rem .6rem;border-radius:6px;font-size:.75rem;text-decoration:none;">
                        <i class="bi bi-plus-circle"></i>
                    </a>
                    @endif
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
