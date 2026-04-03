@extends('layouts.app')
@section('titulo', 'Órdenes de Laboratorio')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    .form-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:14px; padding:1.75rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); max-width:820px; margin:0 auto; }
    .form-card h5 { font-weight:700; color:var(--color-hover); font-size:1rem; margin-bottom:1.25rem; padding-bottom:.6rem; border-bottom:2px solid var(--color-muy-claro); }

    .campo-wrap { margin-bottom:1.1rem; }
    .campo-lbl { font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--color-principal); display:block; margin-bottom:.3rem; }
    .campo-ctrl { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .8rem; font-size:.9rem; color:#1c2b22; background:#fff; outline:none; transition:border-color .15s; font-family:inherit; }
    .campo-ctrl:focus { border-color:var(--color-principal); }
    .campo-ctrl.is-invalid { border-color:#dc2626; }
    .campo-error { font-size:.75rem; color:#dc2626; margin-top:.2rem; display:block; }

    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media(max-width:540px) { .form-row { grid-template-columns:1fr; } }

    .plantilla-chip { display:flex; align-items:center; gap:.5rem; padding:.35rem .75rem; border-radius:8px; background:var(--color-muy-claro); border:1px solid var(--color-muy-claro); font-size:.83rem; color:var(--color-hover); font-weight:600; margin-bottom:.5rem; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

{{-- Alerta de vencidas --}}
@if($ordenesVencidas > 0)
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:.875rem 1.25rem;margin-bottom:1rem;display:flex;align-items:center;gap:.75rem;">
    <i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;font-size:1.1rem;"></i>
    <div>
        <strong style="color:#dc2626;">{{ $ordenesVencidas }} orden(es) vencida(s)</strong>
        <span style="color:#dc2626;font-size:.83rem;"> — La fecha de entrega estimada ya pasó</span>
    </div>
</div>
@endif

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo"><i class="bi bi-flask me-2"></i>Órdenes de Laboratorio</h1>
        <p class="page-subtitulo">Gestión y seguimiento de trabajos de laboratorio dental</p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
        <a href="{{ route('gestion-laboratorios.index') }}"
           style="background:#fff;color:var(--color-principal);border:1.5px solid var(--color-principal);border-radius:8px;padding:.5rem 1.1rem;font-size:.875rem;font-weight:500;display:inline-flex;align-items:center;gap:.4rem;text-decoration:none;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
            <i class="bi bi-building"></i> Gestionar Laboratorios
        </a>
        <a href="{{ route('laboratorio.create') }}" class="btn-morado">
            <i class="bi bi-plus-lg"></i> Nueva Orden
        </a>
    </div>
</div>

{{-- Cards resumen --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:var(--color-principal);">{{ $totalActivas }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Total Activas</div>
            <i class="bi bi-clipboard-data" style="font-size:1.2rem;color:var(--color-principal);opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:#0ea5e9;">{{ $enProceso }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">En Proceso</div>
            <i class="bi bi-arrow-repeat" style="font-size:1.2rem;color:#0ea5e9;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:#22c55e;">{{ $recibidas }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Recibidas</div>
            <i class="bi bi-check-circle" style="font-size:1.2rem;color:#22c55e;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:#dc2626;">{{ $vencidas }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Vencidas</div>
            <i class="bi bi-exclamation-triangle" style="font-size:1.2rem;color:#dc2626;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
</div>

<x-tabla-listado
    :paginacion="$ordenes"
    placeholder="Buscar paciente o N° orden..."
    icono-vacio="bi-flask"
    mensaje-vacio="No hay órdenes de laboratorio registradas"
>
    <x-slot:filtros>
        <select name="laboratorio_id" class="tbl-filtro-select">
            <option value="">Todos los laboratorios</option>
            @foreach($laboratorios as $lab)
            <option value="{{ $lab->id }}" {{ request('laboratorio_id') == $lab->id ? 'selected' : '' }}>{{ $lab->nombre }}</option>
            @endforeach
        </select>
        <select name="estado" class="tbl-filtro-select">
            <option value="">Todos los estados</option>
            <option value="pendiente"  {{ request('estado')==='pendiente'  ? 'selected' : '' }}>Pendiente</option>
            <option value="enviado"    {{ request('estado')==='enviado'    ? 'selected' : '' }}>Enviado</option>
            <option value="en_proceso" {{ request('estado')==='en_proceso' ? 'selected' : '' }}>En Proceso</option>
            <option value="recibido"   {{ request('estado')==='recibido'   ? 'selected' : '' }}>Recibido</option>
            <option value="instalado"  {{ request('estado')==='instalado'  ? 'selected' : '' }}>Instalado</option>
            <option value="cancelado"  {{ request('estado')==='cancelado'  ? 'selected' : '' }}>Cancelado</option>
        </select>
        <input type="date" name="desde" class="tbl-filtro-date" value="{{ request('desde') }}" title="Desde">
        <input type="date" name="hasta" class="tbl-filtro-date" value="{{ request('hasta') }}" title="Hasta">
    </x-slot:filtros>

    <x-slot:accion-vacio>
        <div class="mt-3">
            <a href="{{ route('laboratorio.create') }}" class="btn-morado">
                <i class="bi bi-plus-circle"></i> Crear primera orden
            </a>
        </div>
    </x-slot:accion-vacio>

    <x-slot:thead>
        <tr>
            <th>N° Orden</th>
            <th>Paciente</th>
            <th>Laboratorio</th>
            <th>Tipo Trabajo</th>
            <th>Dientes</th>
            <th>Entrega Est.</th>
            <th>Días Rest.</th>
            <th>Estado</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

    @foreach($ordenes as $orden)
    @php
        $estadoColors = [
            'pendiente'  => ['#fff3cd','#856404'],
            'enviado'    => ['#d1ecf1','#0c5460'],
            'en_proceso' => ['#cce5ff','#004085'],
            'recibido'   => ['#d4edda','#155724'],
            'instalado'  => ['#f3f4f6','#374151'],
            'cancelado'  => ['#fee2e2','#7f1d1d'],
        ];
        $bc = $estadoColors[$orden->estado] ?? ['#f3f4f6','#374151'];
    @endphp
    <tr>
        <td>
            <span style="font-family:monospace;font-weight:700;color:var(--color-principal);background:var(--color-muy-claro);padding:.15rem .5rem;border-radius:6px;font-size:.82rem;">
                {{ $orden->numero_orden }}
            </span>
        </td>
        <td style="font-weight:500;color:#1c2b22;">{{ $orden->paciente->nombre_completo ?? '—' }}</td>
        <td style="font-size:.82rem;color:#6b7280;">{{ $orden->laboratorio->nombre ?? '—' }}</td>
        <td style="font-size:.82rem;">{{ $orden->tipo_trabajo }}</td>
        <td style="font-size:.82rem;color:var(--color-principal);">{{ $orden->dientes ?: '—' }}</td>
        <td style="font-size:.82rem;{{ $orden->esta_vencido ? 'color:#dc2626;font-weight:600;' : 'color:#4b5563;' }}">
            {{ $orden->fecha_entrega_estimada?->format('d/m/Y') ?: '—' }}
            @if($orden->esta_vencido)
            <i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;"></i>
            @endif
        </td>
        <td>
            @if($orden->dias_restantes !== null && !in_array($orden->estado, ['recibido','instalado','cancelado']))
            @php $dias = $orden->dias_restantes; @endphp
            <span style="background:{{ $dias > 5 ? '#d4edda' : ($dias >= 1 ? '#fff3cd' : '#f8d7da') }};color:{{ $dias > 5 ? '#155724' : ($dias >= 1 ? '#856404' : '#721c24') }};border-radius:20px;padding:.12rem .55rem;font-size:.7rem;font-weight:700;">
                {{ $dias > 0 ? '+'.$dias : $dias }} días
            </span>
            @else
            <span style="color:#9ca3af;">—</span>
            @endif
        </td>
        <td>
            <span style="background:{{ $bc[0] }};color:{{ $bc[1] }};border-radius:20px;padding:.12rem .65rem;font-size:.7rem;font-weight:700;">
                {{ $orden->estado_label }}
            </span>
        </td>
        <td>
            <div style="display:flex;justify-content:center;gap:.3rem;">
                <a href="{{ route('laboratorio.show', $orden) }}" class="tbl-btn-accion" title="Ver">
                    <i class="bi bi-eye"></i>
                </a>
                @if(!in_array($orden->estado, ['instalado','cancelado']))
                <a href="{{ route('laboratorio.edit', $orden) }}" class="tbl-btn-accion" title="Editar">
                    <i class="bi bi-pencil"></i>
                </a>
                @endif
            </div>
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

@endsection
