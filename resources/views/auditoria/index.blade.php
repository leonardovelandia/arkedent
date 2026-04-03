@extends('layouts.app')
@section('titulo', 'Auditoría del Sistema')

@section('contenido')

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo"><i class="bi bi-shield-lock me-2"></i>Auditoría del Sistema</h1>
        <p class="page-subtitulo">Registro de todas las acciones realizadas en el sistema</p>
    </div>
    <a href="{{ route('auditoria.exportar', request()->query()) }}"
       style="background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem 1.1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.4rem;text-decoration:none;">
        <i class="bi bi-download"></i> Exportar CSV
    </a>
</div>

<x-tabla-listado
    :paginacion="$logs"
    placeholder="Buscar usuario..."
    icono-vacio="bi-shield-check"
    mensaje-vacio="No hay registros de auditoría"
>
    <x-slot:filtros>
        <select name="modulo" class="tbl-filtro-select">
            <option value="">Todos los módulos</option>
            @foreach($modulos as $m)
            <option value="{{ $m }}" {{ request('modulo') === $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>
            @endforeach
        </select>
        <select name="accion" class="tbl-filtro-select">
            <option value="">Todas las acciones</option>
            @foreach($acciones as $a)
            <option value="{{ $a }}" {{ request('accion') === $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
        </select>
        <input type="date" name="desde" class="tbl-filtro-date" value="{{ request('desde') }}" title="Desde">
        <input type="date" name="hasta" class="tbl-filtro-date" value="{{ request('hasta') }}" title="Hasta">
    </x-slot:filtros>

    <x-slot:thead>
        <tr>
            <th>Fecha y Hora</th>
            <th>Usuario</th>
            <th>Acción</th>
            <th>Módulo</th>
            <th>Descripción</th>
            <th>IP</th>
        </tr>
    </x-slot:thead>

    @foreach($logs as $log)
    @php
        $accionColors = [
            'VER'      => ['#f3f4f6','#374151'],
            'CREAR'    => ['#d4edda','#155724'],
            'EDITAR'   => ['#cce5ff','#004085'],
            'ELIMINAR' => ['#f8d7da','#721c24'],
            'FIRMAR'   => ['#ede9fe','#5b21b6'],
            'LOGIN'    => ['#d4edda','#155724'],
            'LOGOUT'   => ['#fef3c7','#92400e'],
            'EXPORTAR' => ['#d1ecf1','#0c5460'],
        ];
        $bc = $accionColors[$log->accion] ?? ['#f3f4f6','#374151'];
    @endphp
    <tr>
        <td style="white-space:nowrap;color:#6b7280;font-size:.8rem;">
            {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
        </td>
        <td style="font-weight:500;color:#1c2b22;">{{ $log->user_nombre ?? '—' }}</td>
        <td>
            <span style="background:{{ $bc[0] }};color:{{ $bc[1] }};border-radius:20px;padding:.12rem .65rem;font-size:.7rem;font-weight:700;">
                {{ $log->accion }}
            </span>
        </td>
        <td style="color:#6b7280;font-size:.82rem;">
            {{ ucfirst($log->modulo) }}
            @if($log->registro_id)
            <span style="color:#9ca3af;font-size:.72rem;">#{{ $log->registro_id }}</span>
            @endif
        </td>
        <td style="color:#6b7280;font-size:.82rem;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
            {{ $log->descripcion ?? '—' }}
        </td>
        <td style="color:#9ca3af;font-family:monospace;font-size:.75rem;">{{ $log->ip ?? '—' }}</td>
    </tr>
    @endforeach

</x-tabla-listado>

@endsection
