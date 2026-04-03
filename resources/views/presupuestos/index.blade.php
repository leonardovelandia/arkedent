@extends('layouts.app')
@section('titulo', 'Presupuestos')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .form-input { border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; }
    .form-input:focus { border-color:var(--color-principal); }
    .form-select { border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; }
    .form-select:focus { border-color:var(--color-principal); }
    .tabla-header th { background:var(--color-muy-claro); padding:.55rem .9rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); border-bottom:2px solid var(--color-muy-claro); text-align:left; white-space:nowrap; }
    .tabla-fila td { padding:.6rem .9rem; border-bottom:1px solid var(--fondo-borde); font-size:.875rem; vertical-align:middle; }
    .tabla-fila:hover td { background:var(--fondo-card-alt); }
    .badge-estado { padding:.2rem .65rem; border-radius:20px; font-size:.7rem; font-weight:700; display:inline-flex; align-items:center; gap:.25rem; }
    .por-vencer { background:#fff7ed; border:1px solid #fed7aa; border-radius:6px; padding:.15rem .45rem; font-size:.7rem; font-weight:600; color:#c2410c; }
    #tabla-container { min-height:100px; transition:opacity .15s; }
    #tabla-container.cargando { opacity:.4; pointer-events:none; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif
@if(session('error'))
<div class="alerta-flash" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;">
    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
</div>
@endif

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo">Presupuestos</h1>
        <p class="page-subtitulo">Gestión de presupuestos de tratamiento</p>
    </div>
    <a href="{{ route('presupuestos.create') }}" class="btn-morado">
        <i class="bi bi-plus-lg"></i> Nuevo Presupuesto
    </a>
</div>

<x-tabla-listado
    :paginacion="$presupuestos"
    placeholder="Paciente o N° presupuesto..."
    icono-vacio="bi-file-earmark-text"
    mensaje-vacio="No hay presupuestos"
>
    <x-slot:filtros>
        <select name="estado" class="tbl-filtro-select">
            <option value="">Todos los estados</option>
            @foreach(['borrador','enviado','aprobado','rechazado','vencido'] as $est)
            <option value="{{ $est }}" {{ request('estado') === $est ? 'selected' : '' }}>{{ ucfirst($est) }}</option>
            @endforeach
        </select>
    </x-slot:filtros>

    <x-slot:accion-vacio>
        <div class="mt-3">
            <a href="{{ route('presupuestos.create') }}" class="btn-morado">
                <i class="bi bi-plus-lg"></i> Nuevo Presupuesto
            </a>
        </div>
    </x-slot:accion-vacio>

    <x-slot:thead>
        <tr>
            <th>N° Presupuesto</th>
            <th>Paciente</th>
            <th>Fecha</th>
            <th>Vencimiento</th>
            <th style="text-align:right;">Total</th>
            <th>Estado</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

    @foreach($presupuestos as $pre)
    @php
        $color = $pre->estado_color;
        $diasRestantes = $pre->dias_restantes;
    @endphp
    <tr>
        <td>
            <a href="{{ route('presupuestos.show', $pre) }}"
               style="font-family:monospace;font-weight:700;color:var(--color-principal);text-decoration:none;font-size:.82rem;">
                {{ $pre->numero_formateado }}
            </a>
        </td>
        <td>
            <div style="font-weight:600;color:#1c2b22;">{{ $pre->paciente->nombre_completo }}</div>
            <div style="font-size:.75rem;color:#9ca3af;">{{ $pre->paciente->numero_historia }}</div>
        </td>
        <td style="font-size:.83rem;color:#4b5563;white-space:nowrap;">
            {{ $pre->fecha_generacion->format('d/m/Y') }}
        </td>
        <td style="white-space:nowrap;">
            <div style="font-size:.83rem;color:#4b5563;">{{ $pre->fecha_vencimiento->format('d/m/Y') }}</div>
            @if(in_array($pre->estado, ['borrador','enviado']))
                @if($diasRestantes < 0)
                    <span style="font-size:.7rem;color:#991b1b;font-weight:600;">Vencido</span>
                @elseif($diasRestantes <= 5)
                    <span style="background:#fff7ed;border:1px solid #fed7aa;border-radius:6px;padding:.15rem .45rem;font-size:.7rem;font-weight:600;color:#c2410c;">
                        <i class="bi bi-exclamation-triangle-fill"></i> {{ $diasRestantes }}d
                    </span>
                @endif
            @endif
        </td>
        <td style="text-align:right;font-weight:700;color:#1c2b22;white-space:nowrap;">
            $ {{ number_format($pre->total, 0, ',', '.') }}
        </td>
        <td>
            <span style="padding:.2rem .65rem;border-radius:20px;font-size:.7rem;font-weight:700;display:inline-flex;align-items:center;gap:.25rem;background:{{ $color['bg'] }};color:{{ $color['text'] }};">
                {{ $color['label'] }}
            </span>
        </td>
        <td>
            <div style="display:flex;justify-content:center;gap:.3rem;">
                <a href="{{ route('presupuestos.show', $pre) }}" class="tbl-btn-accion" title="Ver">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('presupuestos.pdf', $pre) }}" class="tbl-btn-accion" target="_blank" title="PDF">
                    <i class="bi bi-file-pdf"></i>
                </a>
            </div>
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

@endsection
