@extends('layouts.app')
@section('titulo', 'Proveedor — ' . $proveedor->nombre)

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.45rem 1rem; font-size:.83rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.45rem .9rem; font-size:.83rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; }
    .btn-verde { background:#166534; color:#fff; border:none; border-radius:8px; padding:.45rem .9rem; font-size:.83rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; cursor:pointer; }
    .panel-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1.1rem; }
    .panel-header { padding:.75rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
    .panel-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:600; color:var(--color-hover); display:flex; align-items:center; gap:.4rem; }
    .panel-titulo i { color:var(--color-principal); }
    .panel-body { padding:1.25rem; }
    .dato-grid { display:grid; grid-template-columns:1fr 1fr; gap:.75rem 1.5rem; }
    @media(max-width:600px){ .dato-grid{ grid-template-columns:1fr; } }
    .dato-label { font-size:.7rem; font-weight:700; color:#8fa39a; text-transform:uppercase; letter-spacing:.05em; }
    .dato-valor { font-size:.85rem; color:#1c2b22; margin-top:.1rem; }
    .stats-row { display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; }
    @media(max-width:600px){ .stats-row{ grid-template-columns:1fr 1fr; } }
    .stat-mini { background:var(--fondo-card-alt); border:1px solid var(--color-muy-claro); border-radius:8px; padding:.7rem 1rem; }
    .stat-mini-valor { font-size:1.1rem; font-weight:700; color:var(--color-principal); }
    .stat-mini-label { font-size:.68rem; color:#8fa39a; text-transform:uppercase; }
    .tabla-comp { width:100%; border-collapse:collapse; font-size:.82rem; }
    .tabla-comp th { font-size:.69rem; font-weight:700; text-transform:uppercase; color:var(--color-principal); padding:.45rem .75rem; border-bottom:2px solid var(--color-muy-claro); text-align:left; }
    .tabla-comp td { padding:.45rem .75rem; border-bottom:1px solid var(--fondo-borde); color:#374151; vertical-align:middle; }
    .tabla-comp tr:last-child td { border-bottom:none; }
    .tabla-comp tr:hover td { background:var(--fondo-card-alt); }
    .acc-btn { display:inline-flex; align-items:center; gap:.2rem; padding:.2rem .5rem; border-radius:6px; font-size:.74rem; text-decoration:none; border:none; cursor:pointer; font-weight:500; }
    .acc-ver { background:var(--color-muy-claro); color:var(--color-principal); }
    .badge-cat { display:inline-block; font-size:.68rem; font-weight:600; padding:.1rem .45rem; border-radius:50px; background:var(--color-badge-bg); color:var(--color-badge-texto); margin:.1rem; }
    .pagination-wrapper { padding:.75rem 1.25rem; border-top:1px solid var(--fondo-borde); }

    /* Clásico */
    body:not([data-ui="glass"]) .panel-card { background:#fff; border:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .panel-header { border-bottom:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .panel-titulo { color:var(--color-hover); }
    body:not([data-ui="glass"]) .dato-label { color:#8fa39a; }
    body:not([data-ui="glass"]) .dato-valor { color:#1c2b22; }
    body:not([data-ui="glass"]) .stat-mini { background:var(--fondo-card-alt); border:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .stat-mini-valor { color:var(--color-principal); }
    body:not([data-ui="glass"]) .stat-mini-label { color:#8fa39a; }
    body:not([data-ui="glass"]) .tabla-comp td { color:#374151; border-bottom:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }

    /* Glass */
    body[data-ui="glass"] .panel-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .panel-header { background:rgba(0,0,0,0.25) !important; border-bottom:1px solid rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .panel-titulo { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .panel-titulo i { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .dato-label { color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .dato-valor { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .stat-mini { background:rgba(0,234,255,0.06) !important; border:1px solid rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .stat-mini-valor { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .stat-mini-label { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .tabla-comp th { color:rgba(0,234,255,0.90) !important; border-bottom:2px solid rgba(0,234,255,0.30) !important; }
    body[data-ui="glass"] .tabla-comp td { color:rgba(255,255,255,0.88) !important; border-bottom:1px solid rgba(255,255,255,0.06) !important; }
    body[data-ui="glass"] .tabla-comp tr:hover td { background:rgba(0,234,255,0.08) !important; }
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

{{-- Header --}}
<div style="background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); border-radius:14px; padding:1.5rem 1.75rem; color:#fff; margin-bottom:1.25rem;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem;">
        <div>
            <div style="font-size:.75rem; opacity:.75; font-weight:600; text-transform:uppercase; letter-spacing:.07em;">Proveedor</div>
            <h3 style="font-family:var(--fuente-titulos); margin:.2rem 0 .5rem; font-size:1.5rem;">{{ $proveedor->nombre }}</h3>
            @if($proveedor->nit)
            <div style="font-size:.82rem; opacity:.85;">NIT: {{ $proveedor->nit }}</div>
            @endif
            @if($proveedor->calificacion)
            <div style="margin-top:.5rem; display:flex; align-items:center; gap:.15rem;">
                @for($i = 1; $i <= 5; $i++)
                <i class="bi bi-star{{ $i <= $proveedor->calificacion ? '-fill' : '' }}"
                   style="color:{{ $i <= $proveedor->calificacion ? '#FFC107' : 'rgba(255,255,255,.35)' }}; font-size:1rem;"></i>
                @endfor
                <span style="font-size:.78rem; margin-left:.3rem; opacity:.85;">{{ $proveedor->calificacion_label }}</span>
            </div>
            @endif
        </div>
        <div style="display:flex; gap:.5rem; flex-wrap:wrap; align-items:flex-start;">
            <span style="background:rgba(255,255,255,.2); padding:.25rem .7rem; border-radius:50px; font-size:.75rem; font-weight:600;">
                {{ $proveedor->activo ? 'Activo' : 'Inactivo' }}
            </span>
        </div>
    </div>
    <div style="display:flex; gap:.5rem; margin-top:1.1rem; flex-wrap:wrap;">
        <a href="{{ route('proveedores.index') }}" style="background:rgba(255,255,255,.15); color:#fff; border:1px solid rgba(255,255,255,.3); border-radius:8px; padding:.38rem .85rem; font-size:.8rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none;">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <a href="{{ route('proveedores.edit', $proveedor) }}" style="background:rgba(255,255,255,.15); color:#fff; border:1px solid rgba(255,255,255,.3); border-radius:8px; padding:.38rem .85rem; font-size:.8rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none;">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <a href="{{ route('compras.create', ['proveedor_id' => $proveedor->id]) }}" style="background:#fff; color:var(--color-principal); border-radius:8px; padding:.38rem .85rem; font-size:.8rem; font-weight:600; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none;">
            <i class="bi bi-cart-plus"></i> Nueva Compra
        </a>
    </div>
</div>

{{-- Grid principal --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.1rem;">

{{-- Card: Datos de contacto --}}
<div class="panel-card">
    <div class="panel-header">
        <div class="panel-titulo"><i class="bi bi-person-lines-fill"></i> Datos de Contacto</div>
    </div>
    <div class="panel-body">
        <div class="dato-grid">
            <div>
                <div class="dato-label">Contacto</div>
                <div class="dato-valor">{{ $proveedor->contacto ?: '—' }}</div>
            </div>
            <div>
                <div class="dato-label">Ciudad</div>
                <div class="dato-valor">{{ $proveedor->ciudad ?: '—' }}</div>
            </div>
            <div>
                <div class="dato-label">Teléfono</div>
                <div class="dato-valor">{{ $proveedor->telefono ?: '—' }}</div>
            </div>
            <div>
                <div class="dato-label">WhatsApp</div>
                <div class="dato-valor">
                    @if($proveedor->whatsapp)
                    <a href="https://wa.me/57{{ $proveedor->whatsapp }}" target="_blank" style="color:#166534; text-decoration:none;">
                        <i class="bi bi-whatsapp"></i> {{ $proveedor->whatsapp }}
                    </a>
                    @else —
                    @endif
                </div>
            </div>
            <div style="grid-column:span 2;">
                <div class="dato-label">Email</div>
                <div class="dato-valor">
                    @if($proveedor->email)
                    <a href="mailto:{{ $proveedor->email }}" style="color:var(--color-principal); text-decoration:none;">{{ $proveedor->email }}</a>
                    @else —
                    @endif
                </div>
            </div>
            <div style="grid-column:span 2;">
                <div class="dato-label">Dirección</div>
                <div class="dato-valor">{{ $proveedor->direccion ?: '—' }}</div>
            </div>
            <div>
                <div class="dato-label">Tiempo entrega</div>
                <div class="dato-valor">{{ $proveedor->tiempo_entrega_dias ? $proveedor->tiempo_entrega_dias . ' días' : '—' }}</div>
            </div>
            <div>
                <div class="dato-label">Condiciones de pago</div>
                <div class="dato-valor">{{ $proveedor->condiciones_pago ?: '—' }}</div>
            </div>
            @if($proveedor->categorias && count($proveedor->categorias))
            <div style="grid-column:span 2;">
                <div class="dato-label">Categorías</div>
                <div style="margin-top:.3rem;">
                    @php $etqs = \App\Models\Proveedor::etiquetasCategorias(); @endphp
                    @foreach($proveedor->categorias as $cat)
                    <span class="badge-cat">{{ $etqs[$cat] ?? $cat }}</span>
                    @endforeach
                </div>
            </div>
            @endif
            @if($proveedor->notas)
            <div style="grid-column:span 2;">
                <div class="dato-label">Notas</div>
                <div class="dato-valor" style="font-size:.8rem; color:#6b7280;">{{ $proveedor->notas }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Card: Estadísticas --}}
<div class="panel-card">
    <div class="panel-header">
        <div class="panel-titulo"><i class="bi bi-graph-up"></i> Estadísticas</div>
    </div>
    <div class="panel-body">
        <div class="stats-row">
            <div class="stat-mini">
                <div class="stat-mini-valor">${{ number_format($totalHistorico, 0, ',', '.') }}</div>
                <div class="stat-mini-label">Total histórico</div>
            </div>
            <div class="stat-mini">
                <div class="stat-mini-valor">${{ number_format($totalAnio, 0, ',', '.') }}</div>
                <div class="stat-mini-label">Este año</div>
            </div>
            <div class="stat-mini">
                <div class="stat-mini-valor">${{ number_format($totalMes, 0, ',', '.') }}</div>
                <div class="stat-mini-label">Este mes</div>
            </div>
            <div class="stat-mini" style="grid-column:span 2;">
                <div class="stat-mini-valor">{{ $numOrdenes }}</div>
                <div class="stat-mini-label">Órdenes de compra</div>
            </div>
            <div class="stat-mini">
                <div class="stat-mini-valor" style="font-size:.9rem;">${{ $promedioPorCompra ? number_format($promedioPorCompra, 0, ',', '.') : '—' }}</div>
                <div class="stat-mini-label">Promedio por compra</div>
            </div>
        </div>

        @if($materiales->count())
        <div style="margin-top:1rem; border-top:1px solid var(--fondo-borde); padding-top:.85rem;">
            <div class="dato-label" style="margin-bottom:.4rem;">Materiales suministrados</div>
            @foreach($materiales->take(5) as $mat)
            <div style="display:flex; justify-content:space-between; align-items:center; padding:.3rem 0; border-bottom:1px solid #f9f7ff; font-size:.8rem;">
                <span style="color:#1c2b22;">{{ $mat->nombre }}</span>
                <span style="color:#6b7280;">
                    @php
                        $ult = $mat->itemsCompra()->whereHas('compra', fn($q)=>$q->where('proveedor_id',$proveedor->id)->where('estado','pagada'))->orderByDesc('created_at')->value('precio_unitario');
                        $prom = $mat->itemsCompra()->whereHas('compra', fn($q)=>$q->where('proveedor_id',$proveedor->id)->where('estado','pagada'))->avg('precio_unitario');
                    @endphp
                    @if($ult) Último: ${{ number_format($ult, 0, ',', '.') }} @endif
                    @if($prom) · Prom: ${{ number_format($prom, 0, ',', '.') }} @endif
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

</div>

{{-- Historial de compras --}}
<div class="panel-card">
    <div class="panel-header">
        <div class="panel-titulo"><i class="bi bi-clock-history"></i> Historial de Compras</div>
        <a href="{{ route('compras.create', ['proveedor_id' => $proveedor->id]) }}" class="btn-morado" style="font-size:.78rem; padding:.3rem .7rem;">
            <i class="bi bi-plus"></i> Nueva Compra
        </a>
    </div>

    @if($compras->isEmpty())
    <div style="padding:2rem; text-align:center; color:#9ca3af; font-size:.85rem;">
        <i class="bi bi-inbox" style="font-size:1.5rem; display:block; margin-bottom:.4rem;"></i>
        Sin compras registradas.
    </div>
    @else
    <div style="overflow-x:auto;">
    <table class="tabla-comp">
        <thead>
            <tr>
                <th>N° Compra</th>
                <th>Fecha</th>
                <th>Factura</th>
                <th style="text-align:right;">Total</th>
                <th>Método pago</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        @foreach($compras as $compra)
        <tr>
            <td>
                <span style="font-family:monospace; font-weight:600; color:var(--color-principal); font-size:.8rem;">{{ $compra->numero_formateado }}</span>
            </td>
            <td style="white-space:nowrap; font-size:.8rem;">{{ $compra->fecha_compra->format('d/m/Y') }}</td>
            <td style="font-size:.8rem; color:#6b7280;">{{ $compra->numero_factura ?: '—' }}</td>
            <td style="text-align:right; font-weight:600; color:#166534;">${{ number_format($compra->total, 0, ',', '.') }}</td>
            <td style="font-size:.8rem;">{{ $compra->metodo_pago_label }}</td>
            <td>
                <span class="badge bg-{{ $compra->estado_color }}" style="font-size:.7rem; padding:.2rem .5rem;">{{ ucfirst($compra->estado) }}</span>
            </td>
            <td>
                <div style="display:flex; gap:.3rem;">
                    <a href="{{ route('compras.show', $compra) }}" class="acc-btn acc-ver"><i class="bi bi-eye"></i> Ver</a>
                    @if($compra->estado === 'pendiente')
                    <form method="POST" action="{{ route('compras.pagar', $compra) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="acc-btn" style="background:#dcfce7; color:#166534;" onclick="return confirm('¿Marcar como pagada?')">
                            <i class="bi bi-check2"></i> Pagar
                        </button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    <div class="pagination-wrapper">{{ $compras->links() }}</div>
    @endif
</div>

@endsection
