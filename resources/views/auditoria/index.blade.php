@extends('layouts.app')

@section('titulo', 'Auditoría del Sistema')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-semibold mb-0" style="color:var(--texto-principal)">
                <i class="bi bi-shield-lock me-2" style="color:var(--color-principal)"></i>
                Auditoría del Sistema
            </h4>
            <p class="text-muted mb-0" style="font-size:.83rem">
                Registro de todas las acciones realizadas en el sistema
            </p>
        </div>
        <a href="{{ route('auditoria.exportar', request()->query()) }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-download me-1"></i> Exportar CSV
        </a>
    </div>

    {{-- Filtros --}}
    <div class="card mb-4" style="border-radius:12px;border:1px solid var(--crema-borde)">
        <div class="card-header py-2 px-3" style="background:var(--color-principal);border-radius:11px 11px 0 0">
            <span style="color:#fff;font-size:.85rem;font-weight:600">
                <i class="bi bi-funnel me-1"></i> Filtros
            </span>
        </div>
        <div class="card-body py-3">
            <form method="GET" action="{{ route('auditoria.index') }}" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label mb-1" style="font-size:.78rem">Desde</label>
                    <input type="date" name="desde" class="form-control form-control-sm"
                           value="{{ request('desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1" style="font-size:.78rem">Hasta</label>
                    <input type="date" name="hasta" class="form-control form-control-sm"
                           value="{{ request('hasta') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1" style="font-size:.78rem">Usuario</label>
                    <input type="text" name="usuario" class="form-control form-control-sm"
                           placeholder="Nombre de usuario" value="{{ request('usuario') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1" style="font-size:.78rem">Módulo</label>
                    <select name="modulo" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($modulos as $m)
                            <option value="{{ $m }}" {{ request('modulo') === $m ? 'selected' : '' }}>
                                {{ ucfirst($m) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1" style="font-size:.78rem">Acción</label>
                    <select name="accion" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($acciones as $a)
                            <option value="{{ $a }}" {{ request('accion') === $a ? 'selected' : '' }}>
                                {{ $a }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-sm w-100" style="background:var(--color-principal);color:#fff">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card" style="border-radius:12px;border:1px solid var(--crema-borde)">
        <div class="card-body p-0">
            @if($logs->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-shield-check" style="font-size:2.5rem;opacity:.3"></i>
                    <p class="mt-2 mb-0">No hay registros de auditoría</p>
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:.82rem">
                    <thead style="background:var(--crema);border-bottom:1px solid var(--crema-borde)">
                        <tr>
                            <th class="px-3 py-2 fw-semibold text-muted">Fecha y Hora</th>
                            <th class="px-3 py-2 fw-semibold text-muted">Usuario</th>
                            <th class="px-3 py-2 fw-semibold text-muted">Acción</th>
                            <th class="px-3 py-2 fw-semibold text-muted">Módulo</th>
                            <th class="px-3 py-2 fw-semibold text-muted">Descripción</th>
                            <th class="px-3 py-2 fw-semibold text-muted">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td class="px-3 py-2" style="white-space:nowrap;color:var(--texto-secundario)">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-3 py-2" style="color:var(--texto-principal);font-weight:500">
                                {{ $log->user_nombre ?? '—' }}
                            </td>
                            <td class="px-3 py-2">
                                @php
                                    $badgeClass = match($log->accion) {
                                        'VER'     => 'bg-secondary',
                                        'CREAR'   => 'bg-success',
                                        'EDITAR'  => 'bg-primary',
                                        'ELIMINAR'=> 'bg-danger',
                                        'FIRMAR'  => 'bg-purple',
                                        'LOGIN'   => 'bg-success',
                                        'LOGOUT'  => 'bg-warning text-dark',
                                        'EXPORTAR'=> 'bg-info text-dark',
                                        default   => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}" style="font-size:.72rem">
                                    {{ $log->accion }}
                                </span>
                            </td>
                            <td class="px-3 py-2" style="color:var(--texto-secundario)">
                                {{ ucfirst($log->modulo) }}
                                @if($log->registro_id)
                                    <span class="text-muted" style="font-size:.72rem">#{{ $log->registro_id }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2" style="color:var(--texto-secundario);max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                {{ $log->descripcion ?? '—' }}
                            </td>
                            <td class="px-3 py-2" style="color:var(--texto-secundario);font-family:monospace;font-size:.75rem">
                                {{ $log->ip ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($logs->hasPages())
            <div class="px-3 py-3 border-top" style="border-color:var(--crema-borde)!important">
                {{ $logs->links() }}
            </div>
            @endif
            @endif
        </div>
    </div>

</div>

<style>
.bg-purple { background-color: #7c3aed !important; color: #fff; }
</style>
@endsection
