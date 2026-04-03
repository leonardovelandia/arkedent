@extends('layouts.app')
@section('titulo', 'Ortodoncia')

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo"><i class="bi bi-symmetry-horizontal me-2"></i>Ortodoncia</h1>
        <p class="page-subtitulo">Gestión de tratamientos ortodónticos</p>
    </div>
    <a href="{{ route('ortodoncia.create') }}" class="btn-morado">
        <i class="bi bi-plus-lg"></i> Nueva Ficha Ortodóntica
    </a>
</div>

{{-- Cards resumen --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;">
            <div style="font-size:1.8rem;font-weight:800;color:var(--color-principal);">{{ $totalActivos }}</div>
            <div style="font-size:.75rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">En tratamiento</div>
            <i class="bi bi-symmetry-horizontal" style="font-size:1.2rem;color:var(--color-principal);opacity:.4;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;">
            <div style="font-size:1.8rem;font-weight:800;color:#D97706;">{{ $totalRetencion }}</div>
            <div style="font-size:.75rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">En retención</div>
            <i class="bi bi-shield-check" style="font-size:1.2rem;color:#D97706;opacity:.4;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;">
            <div style="font-size:1.8rem;font-weight:800;color:#2563EB;">{{ $finalizadosAnio }}</div>
            <div style="font-size:.75rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Finalizados {{ date('Y') }}</div>
            <i class="bi bi-trophy" style="font-size:1.2rem;color:#2563EB;opacity:.4;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;">
            <div style="font-size:1.8rem;font-weight:800;color:var(--color-principal);">{{ $controlesEsteMes }}</div>
            <div style="font-size:.75rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Controles este mes</div>
            <i class="bi bi-calendar-check" style="font-size:1.2rem;color:var(--color-principal);opacity:.4;"></i>
        </div>
    </div>
</div>

<x-tabla-listado
    :paginacion="$fichas"
    placeholder="Buscar paciente, documento..."
    icono-vacio="bi-symmetry-horizontal"
    mensaje-vacio="No hay fichas ortodónticas registradas"
>
    <x-slot:filtros>
        <select name="estado" class="tbl-filtro-select">
            <option value="">Todos los estados</option>
            <option value="diagnostico" {{ request('estado')=='diagnostico'?'selected':'' }}>Diagnóstico</option>
            <option value="activo"      {{ request('estado')=='activo'?'selected':'' }}>En tratamiento</option>
            <option value="retencion"   {{ request('estado')=='retencion'?'selected':'' }}>Retención</option>
            <option value="finalizado"  {{ request('estado')=='finalizado'?'selected':'' }}>Finalizado</option>
            <option value="cancelado"   {{ request('estado')=='cancelado'?'selected':'' }}>Cancelado</option>
        </select>
        <input type="date" name="desde" class="tbl-filtro-date" value="{{ request('desde') }}" title="Desde">
        <input type="date" name="hasta" class="tbl-filtro-date" value="{{ request('hasta') }}" title="Hasta">
    </x-slot:filtros>

    <x-slot:accion-vacio>
        <div class="mt-3">
            <a href="{{ route('ortodoncia.create') }}" class="btn-morado">
                <i class="bi bi-plus-circle"></i> Crear primera ficha
            </a>
        </div>
    </x-slot:accion-vacio>

    <x-slot:thead>
        <tr>
            <th>N° Ficha</th>
            <th>Paciente</th>
            <th>Tipo</th>
            <th>Clase molar</th>
            <th>Inicio</th>
            <th>Duración</th>
            <th style="min-width:110px;">Progreso</th>
            <th>Estado</th>
            <th>Últ. control</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

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
            @if($ficha->tipo_ortodoncia)
            <span style="background:var(--color-muy-claro);color:var(--color-principal);border-radius:20px;padding:.12rem .6rem;font-size:.7rem;font-weight:600;white-space:nowrap;">
                {{ $ficha->tipo_ortodoncia_label }}
            </span>
            @else <span style="color:#9ca3af;">—</span> @endif
        </td>
        <td style="font-size:.78rem;color:#6b7280;">{{ $ficha->clase_molar_label }}</td>
        <td style="color:#4b5563;white-space:nowrap;">{{ $ficha->fecha_inicio->format('d/m/Y') }}</td>
        <td style="color:#4b5563;font-size:.78rem;">
            @if($ficha->duracion_meses_estimada)
                {{ $ficha->duracion_real }}m / {{ $ficha->duracion_meses_estimada }}m est.
            @else
                {{ $ficha->duracion_real }}m
            @endif
        </td>
        <td>
            <div style="display:flex;align-items:center;gap:.5rem;">
                <div style="flex:1;background:var(--fondo-borde);border-radius:20px;height:7px;min-width:60px;">
                    <div style="width:{{ $progreso }}%;background:var(--color-principal);border-radius:20px;height:7px;"></div>
                </div>
                <span style="font-size:.72rem;font-weight:700;color:var(--color-principal);min-width:28px;">{{ $progreso }}%</span>
            </div>
        </td>
        <td>
            <span style="background:{{ $bc[0] }};color:{{ $bc[1] }};border-radius:20px;padding:.12rem .6rem;font-size:.7rem;font-weight:700;">
                {{ $ficha->estado_label }}
            </span>
        </td>
        <td style="color:#6b7280;font-size:.78rem;">
            @if($ficha->ultimoControl)
                {{ $ficha->ultimoControl->fecha_control->format('d/m/Y') }}<br>
                <span style="color:var(--color-principal);">Ses. #{{ $ficha->ultimoControl->numero_sesion }}</span>
            @else
                <span style="color:#9ca3af;">Sin controles</span>
            @endif
        </td>
        <td>
            <div style="display:flex;justify-content:center;gap:.3rem;">
                <a href="{{ route('ortodoncia.show', $ficha) }}" class="tbl-btn-accion" title="Ver">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('ortodoncia.edit', $ficha) }}" class="tbl-btn-accion" title="Editar">
                    <i class="bi bi-pencil"></i>
                </a>
                @if(in_array($ficha->estado, ['diagnostico','activo']))
                <a href="{{ route('controles.create', ['ficha_ortodontica_id' => $ficha->id]) }}" class="tbl-btn-accion success" title="Nuevo control">
                    <i class="bi bi-plus-circle"></i>
                </a>
                @endif
            </div>
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

@endsection
