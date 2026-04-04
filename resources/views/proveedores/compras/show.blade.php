@extends('layouts.app')
@section('titulo', 'Compra — ' . $compra->numero_formateado)

@push('estilos')
<style>
    .btn-verde { background:#166534; color:#fff; border:none; border-radius:8px; padding:.45rem .9rem; font-size:.83rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; cursor:pointer; }
    .btn-rojo { background:#dc2626; color:#fff; border:none; border-radius:8px; padding:.45rem .9rem; font-size:.83rem; display:inline-flex; align-items:center; gap:.3rem; cursor:pointer; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.45rem .9rem; font-size:.83rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; }
    .btn-gray { background:#6b7280; color:#fff; border:none; border-radius:8px; padding:.45rem .9rem; font-size:.83rem; display:inline-flex; align-items:center; gap:.3rem; cursor:pointer; }
    .panel-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1.1rem; }
    .panel-header { padding:.75rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
    .panel-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:600; color:var(--color-hover); display:flex; align-items:center; gap:.4rem; }
    .panel-titulo i { color:var(--color-principal); }
    .panel-body { padding:1.25rem; }
    .dato-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:.75rem 1.5rem; }
    @media(max-width:600px){ .dato-grid{ grid-template-columns:1fr 1fr; } }
    .dato-label { font-size:.7rem; font-weight:700; color:#8fa39a; text-transform:uppercase; }
    .dato-valor { font-size:.85rem; color:#1c2b22; margin-top:.1rem; }
    .tabla-items { width:100%; border-collapse:collapse; font-size:.82rem; }
    .tabla-items th { font-size:.69rem; font-weight:700; text-transform:uppercase; color:var(--color-principal); padding:.45rem .75rem; border-bottom:2px solid var(--color-muy-claro); text-align:left; }
    .tabla-items td { padding:.45rem .75rem; border-bottom:1px solid var(--fondo-borde); color:#374151; vertical-align:middle; }
    .tabla-items tr:last-child td { border-bottom:none; }
    .total-row { display:flex; justify-content:space-between; padding:.3rem .5rem; font-size:.87rem; color:#374151; }
    .total-final { font-size:1.05rem; font-weight:700; color:var(--color-principal); border-top:2px solid var(--color-muy-claro); margin-top:.25rem; padding-top:.5rem; }
    @media print {
        .no-print { display:none !important; }
        .panel-card { box-shadow:none; border:1px solid #ccc; }
    }

    /* Clásico */
    body:not([data-ui="glass"]) .panel-card { background:#fff; border:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .panel-header { border-bottom:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .panel-titulo { color:var(--color-hover); }
    body:not([data-ui="glass"]) .dato-label { color:#8fa39a; }
    body:not([data-ui="glass"]) .dato-valor { color:#1c2b22; }
    body:not([data-ui="glass"]) .tabla-items td { color:#374151; border-bottom:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .total-row { color:#374151; }
    body:not([data-ui="glass"]) .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }

    /* Glass */
    body[data-ui="glass"] .panel-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .panel-header { background:rgba(0,0,0,0.25) !important; border-bottom:1px solid rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .panel-titulo { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .panel-titulo i { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .dato-label { color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .dato-valor { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .tabla-items th { color:rgba(0,234,255,0.90) !important; border-bottom:2px solid rgba(0,234,255,0.30) !important; }
    body[data-ui="glass"] .tabla-items td { color:rgba(255,255,255,0.88) !important; border-bottom:1px solid rgba(255,255,255,0.06) !important; }
    body[data-ui="glass"] .total-row { color:rgba(255,255,255,0.88) !important; }
    body[data-ui="glass"] .total-final { color:rgba(0,234,255,0.90) !important; border-top:2px solid rgba(0,234,255,0.30) !important; }
    body[data-ui="glass"] .btn-gris { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
    body[data-ui="glass"] .page-title-main { color:rgba(255,255,255,0.90) !important; }
</style>
@endpush

@section('contenido')

@if(session('success'))
<div style="background:#dcfce7; border:1px solid #86efac; border-radius:8px; padding:.6rem 1rem; margin-bottom:1rem; font-size:.84rem; color:#166534;">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:.6rem 1rem; margin-bottom:1rem; font-size:.84rem; color:#991b1b;">
    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
</div>
@endif

{{-- Header --}}
<div style="background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); border-radius:14px; padding:1.4rem 1.75rem; color:#fff; margin-bottom:1.25rem;" class="no-print">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem;">
        <div>
            <div style="font-size:.72rem; opacity:.75; font-weight:600; text-transform:uppercase; letter-spacing:.07em;">Compra</div>
            <h3 style="font-family:var(--fuente-titulos); margin:.2rem 0 .3rem; font-size:1.5rem;">{{ $compra->numero_formateado }}</h3>
            <div style="font-size:.83rem; opacity:.85;">{{ $compra->proveedor->nombre }}</div>
            <div style="font-size:.8rem; opacity:.75;">{{ $compra->fecha_compra->format('d/m/Y') }}</div>
        </div>
        <div>
            <span class="badge bg-{{ $compra->estado_color }}" style="font-size:.82rem; padding:.35rem .75rem;">
                {{ ucfirst($compra->estado) }}
            </span>
        </div>
    </div>
    <div style="display:flex; gap:.5rem; margin-top:1.1rem; flex-wrap:wrap;">
        <a href="{{ route('compras.index') }}" style="background:rgba(255,255,255,.15); color:#fff; border:1px solid rgba(255,255,255,.3); border-radius:8px; padding:.38rem .85rem; font-size:.8rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none;">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        @if($compra->estado === 'pendiente')
        <form method="POST" action="{{ route('compras.pagar', $compra) }}" style="display:inline;" class="no-print">
            @csrf
            <button type="submit" class="btn-verde" onclick="return confirm('¿Marcar esta compra como pagada?')">
                <i class="bi bi-check-circle"></i> Marcar como Pagada
            </button>
        </form>
        <form method="POST" action="{{ route('compras.cancelar', $compra) }}" style="display:inline;" class="no-print">
            @csrf
            <button type="submit" class="btn-rojo" onclick="return confirm('¿Cancelar esta compra? Se revertirá el inventario actualizado.')">
                <i class="bi bi-x-circle"></i> Cancelar Compra
            </button>
        </form>
        @endif
        <button type="button" onclick="window.print()" style="background:rgba(255,255,255,.15); color:#fff; border:1px solid rgba(255,255,255,.3); border-radius:8px; padding:.38rem .85rem; font-size:.8rem; display:inline-flex; align-items:center; gap:.3rem; cursor:pointer;" class="no-print">
            <i class="bi bi-printer"></i> Imprimir
        </button>
    </div>
</div>

<div class="panel-card">
    {{-- Sección 1: Datos --}}
    <div class="panel-header">
        <div class="panel-titulo"><i class="bi bi-info-circle"></i> Datos de la Compra</div>
    </div>
    <div class="panel-body">
        <div class="dato-grid">
            <div>
                <div class="dato-label">N° Compra</div>
                <div class="dato-valor" style="font-family:monospace; font-weight:700; color:var(--color-principal); font-size:.92rem;">{{ $compra->numero_formateado }}</div>
            </div>
            <div>
                <div class="dato-label">Proveedor</div>
                <div class="dato-valor">
                    <a href="{{ route('proveedores.show', $compra->proveedor) }}" style="color:var(--color-principal); text-decoration:none;">
                        {{ $compra->proveedor->nombre }}
                    </a>
                </div>
            </div>
            <div>
                <div class="dato-label">Fecha de compra</div>
                <div class="dato-valor">{{ $compra->fecha_compra->format('d/m/Y') }}</div>
            </div>
            <div>
                <div class="dato-label">Número de factura</div>
                <div class="dato-valor">{{ $compra->numero_factura ?: '—' }}</div>
            </div>
            <div>
                <div class="dato-label">Método de pago</div>
                <div class="dato-valor">{{ $compra->metodo_pago_label }}</div>
            </div>
            <div>
                <div class="dato-label">Registrado por</div>
                <div class="dato-valor">{{ $compra->registradoPor->name ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- Sección 2: Items --}}
    <div class="panel-header" style="border-top:1px solid var(--fondo-borde);">
        <div class="panel-titulo"><i class="bi bi-list-ul"></i> Items</div>
    </div>
    <div style="overflow-x:auto;">
    <table class="tabla-items">
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Material vinculado</th>
                <th style="text-align:center;">Cantidad</th>
                <th>Unidad</th>
                <th style="text-align:right;">Precio unitario</th>
                <th style="text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
        @foreach($compra->items as $item)
        <tr>
            <td>
                <div style="font-weight:500;">{{ $item->descripcion }}</div>
                @if($item->actualizo_inventario)
                <span style="display:inline-flex; align-items:center; gap:.2rem; background:#dcfce7; color:#166534; font-size:.68rem; font-weight:600; padding:.1rem .4rem; border-radius:50px; margin-top:.15rem;">
                    <i class="bi bi-check2-circle"></i> Inventario actualizado
                </span>
                @endif
            </td>
            <td style="font-size:.8rem;">
                @if($item->material)
                <a href="{{ route('inventario.show', $item->material) }}" style="color:var(--color-principal); text-decoration:none;">
                    <i class="bi bi-box-seam"></i> {{ $item->material->nombre }}
                </a>
                @else
                <span style="color:#9ca3af;">—</span>
                @endif
            </td>
            <td style="text-align:center;">{{ number_format($item->cantidad, 2) }}</td>
            <td style="font-size:.8rem; color:#6b7280;">{{ $item->unidad_medida }}</td>
            <td style="text-align:right;">${{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
            <td style="text-align:right; font-weight:600;">${{ number_format($item->valor_total, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>

    {{-- Sección 3: Totales --}}
    <div class="panel-header" style="border-top:1px solid var(--fondo-borde);">
        <div class="panel-titulo"><i class="bi bi-calculator"></i> Totales</div>
    </div>
    <div class="panel-body">
        <div style="max-width:280px; margin-left:auto;">
            <div class="total-row">
                <span>Subtotal</span>
                <span>${{ number_format($compra->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($compra->descuento_valor > 0)
            <div class="total-row">
                <span>Descuento</span>
                <span style="color:#dc2626;">-${{ number_format($compra->descuento_valor, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="total-row total-final">
                <span>TOTAL</span>
                <span>${{ number_format($compra->total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Sección 4: Info de pago --}}
    <div class="panel-header" style="border-top:1px solid var(--fondo-borde);">
        <div class="panel-titulo"><i class="bi bi-credit-card"></i> Información de Pago</div>
    </div>
    <div class="panel-body">
        <div class="dato-grid" style="grid-template-columns:1fr 1fr 1fr;">
            <div>
                <div class="dato-label">Estado</div>
                <div class="dato-valor">
                    <span class="badge bg-{{ $compra->estado_color }}" style="font-size:.78rem; padding:.25rem .6rem;">
                        {{ ucfirst($compra->estado) }}
                    </span>
                </div>
            </div>
            @if($compra->fecha_vencimiento)
            <div>
                <div class="dato-label">Fecha de vencimiento</div>
                <div class="dato-valor" style="{{ $compra->fecha_vencimiento < now() && $compra->estado === 'pendiente' ? 'color:#dc2626; font-weight:600;' : '' }}">
                    {{ $compra->fecha_vencimiento->format('d/m/Y') }}
                    @if($compra->fecha_vencimiento < now() && $compra->estado === 'pendiente')
                    <span style="font-size:.72rem;"> (Vencida)</span>
                    @endif
                </div>
            </div>
            @endif
            @if($compra->notas)
            <div style="grid-column:span 3;">
                <div class="dato-label">Notas</div>
                <div class="dato-valor" style="color:#6b7280; font-size:.82rem;">{{ $compra->notas }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
