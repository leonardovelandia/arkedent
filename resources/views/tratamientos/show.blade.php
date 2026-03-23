@extends('layouts.app')
@section('titulo', 'Detalle de Tratamiento')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    .trat-header { background:linear-gradient(135deg,var(--color-principal) 0%,var(--color-sidebar-2) 60%,var(--color-sidebar) 100%); border-radius:14px; padding:1.5rem 1.75rem; color:#fff; margin-bottom:1.25rem; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem; }
    .trat-header h2 { font-family:var(--fuente-titulos); font-size:1.35rem; font-weight:700; margin:0 0 .2rem; }
    .trat-header-sub { font-size:.85rem; opacity:.8; display:flex; flex-wrap:wrap; gap:.75rem; align-items:center; }

    .badge-estado { display:inline-flex; align-items:center; gap:.3rem; padding:.28rem .75rem; border-radius:20px; font-size:.78rem; font-weight:700; }
    .badge-activo     { background:#E8D5FF; color:var(--color-sidebar-2); }
    .badge-completado { background:#D4EDDA; color:#155724; }
    .badge-cancelado  { background:#F3F4F6; color:#374151; }

    .resumen-cards { display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; margin-bottom:1rem; }
    .resumen-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:1rem 1.25rem; text-align:center; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .resumen-card-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#9ca3af; margin-bottom:.3rem; }
    .resumen-card-valor { font-size:1.3rem; font-weight:800; }

    .info-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:1.25rem 1.5rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1rem; }
    .info-card-titulo { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-hover); margin-bottom:.9rem; display:flex; align-items:center; gap:.4rem; }

    .progress-bar-wrap { background:var(--fondo-borde); border-radius:999px; height:10px; overflow:hidden; }
    .progress-bar-fill { background:linear-gradient(90deg,var(--color-principal),var(--color-claro)); height:100%; border-radius:999px; transition:width .4s; }

    .tabla-pagos { width:100%; border-collapse:collapse; font-size:.875rem; }
    .tabla-pagos thead th { background:var(--color-muy-claro); color:var(--color-hover); font-weight:700; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; padding:.55rem .9rem; border-bottom:2px solid var(--color-muy-claro); white-space:nowrap; }
    .tabla-pagos tbody td { padding:.55rem .9rem; border-bottom:1px solid var(--fondo-borde); vertical-align:middle; }
    .tabla-pagos tbody tr:last-child td { border-bottom:none; }

    .badge-metodo { display:inline-flex; align-items:center; padding:.2rem .55rem; border-radius:20px; font-size:.72rem; font-weight:700; }
    .badge-efectivo   { background:#D4EDDA; color:#155724; }
    .badge-transferencia { background:#D1ECF1; color:#0c5460; }
    .badge-tarjeta    { background:#E8D5FF; color:var(--color-sidebar-2); }
    .badge-cheque     { background:#FFE5CC; color:#7C2D12; }
    .badge-otro       { background:#F3F4F6; color:#374151; }

    .accion-btn { background:none; border:1px solid var(--color-muy-claro); border-radius:6px; width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; font-size:.85rem; transition:background .12s; text-decoration:none; color:var(--color-principal); }
    .accion-btn:hover { background:var(--color-muy-claro); }
    .accion-btn.verde { color:#166534; border-color:#bbf7d0; }
    .accion-btn.verde:hover { background:#dcfce7; }
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

{{-- Header --}}
@php
    $badgeClase = match($tratamiento->estado) {
        'completado' => 'badge-completado',
        'cancelado'  => 'badge-cancelado',
        default      => 'badge-activo',
    };
    $pagado = $tratamiento->valor_total - $tratamiento->saldo_pendiente;
    $pct = $tratamiento->valor_total > 0 ? min(100, round(($pagado / $tratamiento->valor_total) * 100)) : 0;
@endphp

<div class="trat-header">
    <div>
        <h2>{{ $tratamiento->nombre }}</h2>
        <div class="trat-header-sub">
            <span><i class="bi bi-person-circle"></i> {{ $tratamiento->paciente->nombre_completo }}</span>
            <span><i class="bi bi-calendar3"></i> {{ $tratamiento->fecha_inicio->translatedFormat('d \d\e F \d\e Y') }}</span>
            <span><i class="bi bi-person-badge"></i> {{ $tratamiento->doctor?->name }}</span>
        </div>
    </div>
    <span class="badge-estado {{ $badgeClase }}" style="font-size:.9rem;padding:.4rem 1rem;">
        {{ ucfirst($tratamiento->estado) }}
    </span>
</div>

{{-- Botones --}}
<div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1.25rem;">
    <a href="{{ route('tratamientos.index') }}"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
    <a href="{{ route('pacientes.show', $tratamiento->paciente) }}"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;">
        <i class="bi bi-person-badge"></i> Ver paciente
    </a>
    @if($tratamiento->estado === 'activo')
    <a href="{{ route('pagos.create', ['paciente_id' => $tratamiento->paciente_id, 'tratamiento_id' => $tratamiento->id]) }}"
       class="btn-morado">
        <i class="bi bi-cash-coin"></i> Registrar Pago
    </a>
    <a href="{{ route('tratamientos.edit', $tratamiento) }}" class="btn-morado"
       style="background:transparent;color:var(--color-principal);border:1px solid var(--color-principal);">
        <i class="bi bi-pencil"></i> Editar
    </a>
    @if($tratamiento->saldo_pendiente <= 0)
    <form method="POST" action="{{ route('tratamientos.completar', $tratamiento) }}" style="display:inline;">
        @csrf
        <button type="submit" class="btn-morado" style="background:linear-gradient(135deg,#166534,#15803d);">
            <i class="bi bi-check-circle"></i> Completar tratamiento
        </button>
    </form>
    @endif
    @endif
</div>

{{-- Cards resumen --}}
<div class="resumen-cards">
    <div class="resumen-card">
        <div class="resumen-card-label">Valor total</div>
        <div class="resumen-card-valor" style="color:#1c2b22;">$ {{ number_format($tratamiento->valor_total, 0, ',', '.') }}</div>
    </div>
    <div class="resumen-card">
        <div class="resumen-card-label">Total pagado</div>
        <div class="resumen-card-valor" style="color:#166534;">$ {{ number_format($pagado, 0, ',', '.') }}</div>
    </div>
    <div class="resumen-card">
        <div class="resumen-card-label">Saldo pendiente</div>
        <div class="resumen-card-valor" style="color:{{ $tratamiento->saldo_pendiente > 0 ? '#dc2626' : '#166534' }};">
            $ {{ number_format($tratamiento->saldo_pendiente, 0, ',', '.') }}
        </div>
    </div>
</div>

{{-- Barra progreso --}}
<div style="background:#fff;border:1px solid var(--color-muy-claro);border-radius:12px;padding:1rem 1.5rem;margin-bottom:1rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;">
        <span style="font-size:.82rem;font-weight:700;color:var(--color-hover);">Progreso de pago</span>
        <span style="font-size:.82rem;font-weight:800;color:var(--color-principal);">{{ $pct }}%</span>
    </div>
    <div class="progress-bar-wrap">
        <div class="progress-bar-fill" style="width:{{ $pct }}%;"></div>
    </div>
</div>

{{-- Tabla de pagos --}}
<div class="info-card">
    <div class="info-card-titulo"><i class="bi bi-cash-stack"></i> Pagos del tratamiento</div>
    @if($tratamiento->pagos->isEmpty())
    <p style="text-align:center;color:#9ca3af;font-size:.875rem;padding:1.5rem 0;">
        <i class="bi bi-inbox" style="font-size:1.5rem;display:block;margin-bottom:.5rem;"></i>
        Sin pagos registrados aún
    </p>
    @else
    <div style="overflow-x:auto;">
    <table class="tabla-pagos">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Recibo</th>
                <th>Concepto</th>
                <th>Valor</th>
                <th>Método</th>
                <th>Registrado por</th>
                <th style="text-align:center;width:80px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @foreach($tratamiento->pagos as $p)
        <tr style="{{ $p->anulado ? 'opacity:.55;' : '' }}">
            <td style="white-space:nowrap;font-size:.82rem;">{{ $p->fecha_pago->translatedFormat('d M Y') }}</td>
            <td style="font-family:monospace;font-weight:700;color:var(--color-principal);">
                {{ $p->numero_recibo }}
                @if($p->anulado)<span style="display:block;font-size:.7rem;color:#dc2626;font-weight:600;">ANULADO</span>@endif
            </td>
            <td>{{ $p->concepto }}</td>
            <td style="font-weight:700;color:{{ $p->anulado ? '#9ca3af' : '#166534' }};white-space:nowrap;">
                $ {{ number_format($p->valor, 0, ',', '.') }}
            </td>
            <td>
                @php
                    $bm = match($p->metodo_pago) {
                        'efectivo'        => 'badge-efectivo',
                        'transferencia'   => 'badge-transferencia',
                        'tarjeta_credito','tarjeta_debito' => 'badge-tarjeta',
                        'cheque'          => 'badge-cheque',
                        default           => 'badge-otro',
                    };
                @endphp
                <span class="badge-metodo {{ $bm }}">{{ $p->metodo_pago_label }}</span>
            </td>
            <td style="font-size:.82rem;color:#6b7280;">{{ $p->cajero?->name ?? '—' }}</td>
            <td style="text-align:center;">
                <a href="{{ route('pagos.show', $p) }}" class="accion-btn" title="Ver">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('pagos.recibo', $p) }}" class="accion-btn verde" title="Recibo PDF" target="_blank">
                    <i class="bi bi-file-pdf"></i>
                </a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    @endif
</div>

@if($tratamiento->notas)
<div class="info-card">
    <div class="info-card-titulo"><i class="bi bi-sticky"></i> Notas</div>
    <p style="font-size:.9rem;color:#1c2b22;white-space:pre-line;margin:0;">{{ $tratamiento->notas }}</p>
</div>
@endif

@endsection
