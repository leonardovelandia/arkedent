@extends('layouts.app')
@section('titulo', 'Periodoncia')

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo"><i class="bi bi-heart-pulse me-2"></i>Periodoncia</h1>
        <p class="page-subtitulo">Gestión de fichas periodontales</p>
    </div>
    <a href="{{ route('periodoncia.create') }}" class="btn-morado">
        <i class="bi bi-plus-lg"></i> Nueva Ficha Periodontal
    </a>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:#22c55e;">{{ $stats['activas'] }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Activas</div>
            <i class="bi bi-clipboard2-heart" style="font-size:1.2rem;color:#22c55e;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:var(--color-principal);">{{ $stats['en_tratamiento'] }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">En tratamiento</div>
            <i class="bi bi-heart-pulse" style="font-size:1.2rem;color:var(--color-principal);opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:#f59e0b;">{{ $stats['mantenimiento'] }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Mantenimiento</div>
            <i class="bi bi-shield-check" style="font-size:1.2rem;color:#f59e0b;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:#8b5cf6;">{{ $stats['controles_mes'] }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Controles este mes</div>
            <i class="bi bi-calendar-check" style="font-size:1.2rem;color:#8b5cf6;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
</div>

<x-tabla-listado
    :paginacion="$fichas"
    placeholder="Buscar paciente..."
    icono-vacio="bi-heart-pulse"
    mensaje-vacio="No hay fichas periodontales registradas"
>
    <x-slot:filtros>
        <select name="estado" class="tbl-filtro-select">
            <option value="">Todos los estados</option>
            <option value="activa"          {{ request('estado')=='activa'?'selected':'' }}>Activa</option>
            <option value="en_tratamiento"  {{ request('estado')=='en_tratamiento'?'selected':'' }}>En tratamiento</option>
            <option value="mantenimiento"   {{ request('estado')=='mantenimiento'?'selected':'' }}>Mantenimiento</option>
            <option value="finalizada"      {{ request('estado')=='finalizada'?'selected':'' }}>Finalizada</option>
            <option value="abandonada"      {{ request('estado')=='abandonada'?'selected':'' }}>Abandonada</option>
        </select>
        <input type="date" name="desde" class="tbl-filtro-date" value="{{ request('desde') }}" title="Desde">
        <input type="date" name="hasta" class="tbl-filtro-date" value="{{ request('hasta') }}" title="Hasta">
    </x-slot:filtros>

    <x-slot:accion-vacio>
        <div class="mt-3">
            <a href="{{ route('periodoncia.create') }}" class="btn-morado">
                <i class="bi bi-plus-circle"></i> Crear primera ficha
            </a>
        </div>
    </x-slot:accion-vacio>

    <x-slot:thead>
        <tr>
            <th>N° Ficha</th>
            <th>Paciente</th>
            <th>Clasificación</th>
            <th style="text-align:center;">Índice Placa</th>
            <th style="text-align:center;">Sesiones</th>
            <th>Estado</th>
            <th>Últ. control</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

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
    <tr>
        <td>
            <span style="font-family:monospace;font-weight:700;color:var(--color-principal);font-size:.78rem;">{{ $ficha->numero_ficha }}</span>
        </td>
        <td>
            <a href="{{ route('pacientes.show', $ficha->paciente_id) }}" style="color:#1c2b22;text-decoration:none;font-weight:600;">
                {{ $ficha->paciente->nombre_completo }}
            </a>
            <div style="font-size:.72rem;color:#9ca3af;">{{ $ficha->paciente->numero_documento }}</div>
        </td>
        <td>
            @if($ficha->clasificacion_periodontal)
            <span style="background:var(--color-muy-claro);color:var(--color-principal);border-radius:20px;padding:.12rem .6rem;font-size:.7rem;font-weight:600;white-space:nowrap;">
                {{ Str::limit($ficha->clasificacion_label, 28) }}
            </span>
            @else
            <span style="color:#9ca3af;">—</span>
            @endif
        </td>
        <td style="text-align:center;">
            @if($placa !== null)
            <span style="font-weight:700;font-size:.82rem;color:{{ $placaColor }};">{{ number_format($placa, 1) }}%</span>
            @else
            <span style="color:#9ca3af;">—</span>
            @endif
        </td>
        <td style="text-align:center;">
            <span style="font-weight:700;font-size:.85rem;color:var(--color-principal);">{{ $ficha->controles->count() }}</span>
        </td>
        <td>
            <span style="background:{{ $bc[0] }};color:{{ $bc[1] }};border-radius:20px;padding:.12rem .65rem;font-size:.7rem;font-weight:700;">
                {{ $ficha->estado_label }}
            </span>
        </td>
        <td style="color:#6b7280;font-size:.78rem;">
            @if($ficha->ultimoControl)
                {{ $ficha->ultimoControl->fecha_control->format('d/m/Y') }}<br>
                <span style="color:var(--color-principal);font-weight:600;">Ses. #{{ $ficha->ultimoControl->numero_sesion }}</span>
            @else
                <span style="color:#9ca3af;">Sin controles</span>
            @endif
        </td>
        <td>
            <div style="display:flex;justify-content:center;gap:.3rem;flex-wrap:nowrap;">
                <a href="{{ route('periodoncia.show', $ficha) }}" class="tbl-btn-accion" title="Ver ficha">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('periodoncia.controles.create', $ficha) }}" class="tbl-btn-accion success" title="Nuevo control">
                    <i class="bi bi-plus-circle"></i>
                </a>
                <a href="{{ route('periodoncia.pdf', $ficha) }}" class="tbl-btn-accion warn" target="_blank" title="PDF">
                    <i class="bi bi-file-pdf"></i>
                </a>
                <form method="POST" action="{{ route('periodoncia.destroy', $ficha) }}"
                      onsubmit="return confirm('¿Eliminar esta ficha periodontal?');" style="margin:0;">
                    @csrf @method('DELETE')
                    <button type="submit" class="tbl-btn-accion danger" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

@endsection
