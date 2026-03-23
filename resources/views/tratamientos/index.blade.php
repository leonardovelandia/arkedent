@extends('layouts.app')
@section('titulo', 'Tratamientos')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    .search-bar { display:flex; gap:.75rem; align-items:flex-end; flex-wrap:wrap; }
    .search-field { display:flex; flex-direction:column; gap:.3rem; }
    .search-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--color-hover); }
    .search-input-wrap { position:relative; display:flex; align-items:center; }
    .search-input-wrap i { position:absolute; left:.75rem; color:#9ca3af; font-size:.9rem; pointer-events:none; }
    .search-input { border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.42rem .75rem .42rem 2.1rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; min-width:240px; transition:border-color .15s; }
    .search-input:focus { border-color:var(--color-principal); }
    .select-filtro { border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.42rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; transition:border-color .15s; }
    .select-filtro:focus { border-color:var(--color-principal); }

    .tabla-wrap { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .tabla-trat { width:100%; border-collapse:collapse; font-size:.875rem; }
    .tabla-trat thead th { background:var(--color-muy-claro); color:var(--color-hover); font-weight:700; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; padding:.65rem 1rem; border-bottom:2px solid var(--color-muy-claro); white-space:nowrap; }
    .tabla-trat tbody tr { transition:background .12s; }
    .tabla-trat tbody tr:hover { background:var(--fondo-card-alt); }
    .tabla-trat tbody td { padding:.6rem 1rem; border-bottom:1px solid var(--fondo-borde); vertical-align:middle; }
    .tabla-trat tbody tr:last-child td { border-bottom:none; }

    .badge-estado { display:inline-flex; align-items:center; gap:.3rem; padding:.22rem .65rem; border-radius:20px; font-size:.73rem; font-weight:700; white-space:nowrap; }
    .badge-activo     { background:#E8D5FF; color:var(--color-sidebar-2); }
    .badge-completado { background:#D4EDDA; color:#155724; }
    .badge-cancelado  { background:#F3F4F6; color:#374151; }

    .progress-bar-wrap { background:var(--fondo-borde); border-radius:999px; height:6px; min-width:80px; overflow:hidden; }
    .progress-bar-fill { background:linear-gradient(90deg,var(--color-principal),var(--color-claro)); height:100%; border-radius:999px; }

    .accion-btn { background:none; border:1px solid var(--color-muy-claro); border-radius:6px; width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; font-size:.85rem; transition:background .12s; text-decoration:none; color:var(--color-principal); }
    .accion-btn:hover { background:var(--color-muy-claro); color:var(--color-hover); }
    .accion-btn.verde { color:#166534; border-color:#bbf7d0; }
    .accion-btn.verde:hover { background:#dcfce7; }

    .empty-state { text-align:center; padding:3rem 1rem; color:#9ca3af; }
    .empty-state i { font-size:2.5rem; color:var(--color-acento-activo); display:block; margin-bottom:.75rem; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif
@if(session('error'))
<div class="alerta-flash" style="background:#fef2f2;color:#dc2626;border:1px solid #fecdd3;">
    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
</div>
@endif

{{-- Encabezado --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Tratamientos</h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">Gestión de tratamientos y pagos</p>
    </div>
    <a href="{{ route('tratamientos.create') }}" class="btn-morado">
        <i class="bi bi-plus-circle"></i> Nuevo Tratamiento
    </a>
</div>

{{-- Filtros --}}
<div style="background:#fff;border:1px solid var(--color-muy-claro);border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.25rem;">
    <form method="GET" action="{{ route('tratamientos.index') }}" class="search-bar">
        <div class="search-field" style="flex:1;min-width:240px;">
            <span class="search-label"><i class="bi bi-search"></i> Buscar</span>
            <div class="search-input-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="buscar" class="search-input" style="width:100%;"
                       placeholder="Paciente o tratamiento…"
                       value="{{ request('buscar') }}" autocomplete="off">
            </div>
        </div>
        <div class="search-field">
            <span class="search-label">Estado</span>
            <select name="estado" class="select-filtro">
                <option value="">Todos</option>
                <option value="activo"     {{ request('estado') === 'activo'     ? 'selected' : '' }}>Activo</option>
                <option value="completado" {{ request('estado') === 'completado' ? 'selected' : '' }}>Completado</option>
                <option value="cancelado"  {{ request('estado') === 'cancelado'  ? 'selected' : '' }}>Cancelado</option>
            </select>
        </div>
        <div class="search-field" style="justify-content:flex-end;">
            <span class="search-label" style="opacity:0;">—</span>
            <div style="display:flex;gap:.4rem;">
                <button type="submit" class="btn-morado" style="padding:.42rem .9rem;">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                @if(request()->hasAny(['buscar','estado']))
                <a href="{{ route('tratamientos.index') }}" class="btn-morado"
                   style="background:transparent;color:var(--color-principal);border:1px solid var(--color-principal);padding:.42rem .9rem;">
                    <i class="bi bi-x"></i> Limpiar
                </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Tabla --}}
<div class="tabla-wrap">
    <div style="overflow-x:auto;">
    <table class="tabla-trat">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>N° TRT</th>
                <th>Tratamiento</th>
                <th>Valor total</th>
                <th>Total pagado</th>
                <th>Saldo pendiente</th>
                <th>Progreso</th>
                <th>Estado</th>
                <th style="text-align:center;width:90px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($tratamientos as $t)
        @php
            $pagado = $t->valor_total - $t->saldo_pendiente;
            $pct = $t->valor_total > 0 ? min(100, round(($pagado / $t->valor_total) * 100)) : 0;
        @endphp
        <tr>
            <td>
                <div style="font-weight:600;color:#1c2b22;">{{ $t->paciente->nombre_completo }}</div>
                <div style="font-size:.74rem;color:#9ca3af;">{{ $t->paciente->numero_documento }}</div>
            </td>
            <td>
                <span style="font-family:monospace;font-weight:700;color:#0f766e;background:#ccfbf1;padding:.15rem .5rem;border-radius:6px;font-size:.82rem;">
                    {{ $t->numero_tratamiento ?? ('#'.$t->id) }}
                </span>
            </td>
            <td style="max-width:200px;">
                <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-weight:500;" title="{{ $t->nombre }}">
                    {{ $t->nombre }}
                </span>
                <span style="font-size:.73rem;color:#9ca3af;">{{ $t->fecha_inicio->translatedFormat('d M Y') }}</span>
            </td>
            <td style="font-weight:600;white-space:nowrap;">$ {{ number_format($t->valor_total, 0, ',', '.') }}</td>
            <td style="font-weight:600;color:#166534;white-space:nowrap;">$ {{ number_format($pagado, 0, ',', '.') }}</td>
            <td style="font-weight:700;color:{{ $t->saldo_pendiente > 0 ? '#dc2626' : '#166534' }};white-space:nowrap;">
                $ {{ number_format($t->saldo_pendiente, 0, ',', '.') }}
            </td>
            <td style="min-width:100px;">
                <div style="display:flex;align-items:center;gap:.4rem;">
                    <div class="progress-bar-wrap" style="flex:1;">
                        <div class="progress-bar-fill" style="width:{{ $pct }}%;"></div>
                    </div>
                    <span style="font-size:.72rem;font-weight:700;color:var(--color-principal);">{{ $pct }}%</span>
                </div>
            </td>
            <td>
                @php
                    $badgeClase = match($t->estado) {
                        'completado' => 'badge-completado',
                        'cancelado'  => 'badge-cancelado',
                        default      => 'badge-activo',
                    };
                @endphp
                <span class="badge-estado {{ $badgeClase }}">{{ ucfirst($t->estado) }}</span>
            </td>
            <td style="text-align:center;">
                <div style="display:inline-flex;gap:.3rem;">
                    <a href="{{ route('tratamientos.show', $t) }}" class="accion-btn" title="Ver detalle">
                        <i class="bi bi-eye"></i>
                    </a>
                    @if($t->estado === 'activo')
                    <a href="{{ route('pagos.create', ['paciente_id' => $t->paciente_id, 'tratamiento_id' => $t->id]) }}"
                       class="accion-btn verde" title="Registrar pago">
                        <i class="bi bi-cash-coin"></i>
                    </a>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9">
                <div class="empty-state">
                    <i class="bi bi-clipboard2-x"></i>
                    <p style="font-weight:600;color:#4b5563;margin-bottom:.25rem;">Sin tratamientos registrados</p>
                    @if(request()->hasAny(['buscar','estado']))
                    <p style="font-size:.84rem;color:#9ca3af;">Ningún resultado para los filtros aplicados.</p>
                    @else
                    <a href="{{ route('tratamientos.create') }}" class="btn-morado mt-2" style="display:inline-flex;">
                        <i class="bi bi-plus-circle"></i> Crear primer tratamiento
                    </a>
                    @endif
                </div>
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
    </div>
    @if($tratamientos->hasPages())
    <div style="padding:.75rem 1rem;border-top:1px solid var(--fondo-borde);">
        {{ $tratamientos->links() }}
    </div>
    @endif
</div>

@endsection
