@extends('layouts.app')
@section('titulo', $material->nombre)

@push('estilos')
<style>
    .inv-header { display:flex; align-items:center; gap:.75rem; margin-bottom:1.25rem; }
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; transition:filter .18s; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; color:#374151; }

    .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem; }
    @media(max-width:700px){ .grid-2{ grid-template-columns:1fr; } }
    .grid-4 { display:grid; grid-template-columns:repeat(4,1fr); gap:.875rem; margin-bottom:1.25rem; }
    @media(max-width:900px){ .grid-4{ grid-template-columns:1fr 1fr; } }

    .metrica-inv { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1rem 1.1rem; display:flex; flex-direction:column; gap:.35rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .metrica-valor { font-family:var(--fuente-titulos); font-size:1.5rem; font-weight:600; color:var(--color-principal); line-height:1; }
    .metrica-label { font-size:.7rem; font-weight:600; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; }

    .panel-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1rem; }
    .panel-header { padding:.8rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; gap:.5rem; }
    .panel-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:600; color:var(--color-hover); display:flex; align-items:center; gap:.4rem; }
    .panel-titulo i { color:var(--color-principal); }
    .panel-body { padding:1.25rem; }

    .form-label { font-size:.76rem; font-weight:700; color:var(--color-hover); display:block; margin-bottom:.2rem; }
    .form-input { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.42rem .75rem; font-size:.84rem; color:#1c2b22; background:#fff; outline:none; box-sizing:border-box; }
    .form-input:focus { border-color:var(--color-principal); }

    .tabla-mov { width:100%; border-collapse:collapse; font-size:.81rem; }
    .tabla-mov th { font-size:.69rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-principal); padding:.5rem .75rem; border-bottom:2px solid var(--color-muy-claro); text-align:left; }
    .tabla-mov td { padding:.5rem .75rem; border-bottom:1px solid var(--fondo-borde); color:#374151; vertical-align:middle; }
    .tabla-mov tr:last-child td { border-bottom:none; }
    .tabla-mov tr:hover td { background:var(--fondo-card-alt); }

    .barra-stock { height:8px; background:#f3f4f6; border-radius:50px; overflow:hidden; margin-top:.4rem; }
    .barra-fill { height:100%; border-radius:50px; }
    .badge-cat { display:inline-block; font-size:.7rem; font-weight:600; padding:.18rem .55rem; border-radius:50px; color:#fff; }
    .pagination-wrapper { padding:.75rem 1.25rem; border-top:1px solid var(--fondo-borde); display:flex; justify-content:flex-end; }

    /* Clásico */
    body:not([data-ui="glass"]) .metrica-inv { background:#fff; border:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .metrica-valor { color:var(--color-principal); }
    body:not([data-ui="glass"]) .metrica-label { color:#8fa39a; }
    body:not([data-ui="glass"]) .panel-card { background:#fff; border:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .panel-header { border-bottom:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .panel-titulo { color:var(--color-hover); }
    body:not([data-ui="glass"]) .form-label { color:var(--color-hover); }
    body:not([data-ui="glass"]) .form-input { color:#1c2b22; background:#fff; border:1.5px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .tabla-mov td { color:#374151; border-bottom:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }
    body:not([data-ui="glass"]) .barra-stock { background:#f3f4f6; }

    /* Glass */
    body[data-ui="glass"] .metrica-inv { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .metrica-valor { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .metrica-label { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .panel-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .panel-header { background:rgba(0,0,0,0.25) !important; border-bottom:1px solid rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .panel-titulo { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .panel-titulo i { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .form-label { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .form-input { background:rgba(255,255,255,0.08) !important; border:1.5px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .form-input:focus { border-color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .tabla-mov th { color:rgba(0,234,255,0.90) !important; border-bottom:2px solid rgba(0,234,255,0.30) !important; }
    body[data-ui="glass"] .tabla-mov td { color:rgba(255,255,255,0.88) !important; border-bottom:1px solid rgba(255,255,255,0.06) !important; }
    body[data-ui="glass"] .tabla-mov tr:hover td { background:rgba(0,234,255,0.08) !important; }
    body[data-ui="glass"] .barra-stock { background:rgba(255,255,255,0.10) !important; }
    body[data-ui="glass"] .btn-gris { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
    body[data-ui="glass"] .page-title-main { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .page-title-sub  { color:rgba(255,255,255,0.55) !important; }
</style>
@endpush

@section('contenido')

<div class="inv-header">
    <a href="{{ route('inventario.index') }}"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div style="flex:1;">
        <div style="display:flex; align-items:center; gap:.6rem; flex-wrap:wrap;">
            <h4 style="font-family:var(--fuente-titulos); font-weight:700; color:#1c2b22; margin:0;">{{ $material->nombre }}</h4>
            @if($material->categoria)
            <span class="badge-cat" style="background:{{ $material->categoria->color ?? 'var(--color-principal)' }};">{{ $material->categoria->nombre }}</span>
            @endif
            <span class="badge bg-{{ $material->estado_stock_color }}" style="font-size:.72rem;">
                @if($material->estado_stock === 'critico') <i class="bi bi-exclamation-triangle"></i> Crítico
                @elseif($material->estado_stock === 'bajo') <i class="bi bi-arrow-down"></i> Bajo
                @else <i class="bi bi-check-circle"></i> Normal
                @endif
            </span>
        </div>
        @if($material->codigo)
        <p style="font-size:.78rem; color:#9ca3af; margin:0;">Código: {{ $material->codigo }}</p>
        @endif
    </div>
    <a href="{{ route('inventario.edit', $material) }}" class="btn-gris"><i class="bi bi-pencil"></i> Editar</a>
</div>

{{-- Métricas --}}
<div class="grid-4">
    <div class="metrica-inv">
        <span class="metrica-label">Stock Actual</span>
        <div class="metrica-valor" style="color:{{ $material->estado_stock === 'critico' ? '#dc2626' : ($material->estado_stock === 'bajo' ? '#d97706' : '#166534') }};">
            {{ number_format($material->stock_actual, 2) }}
        </div>
        <div style="font-size:.75rem; color:#6b7280;">{{ $material->unidad_medida }}</div>
        @if($material->porcentaje_stock !== null)
        <div class="barra-stock">
            <div class="barra-fill" style="width:{{ $material->porcentaje_stock }}%; background:{{ $material->estado_stock === 'critico' ? '#dc2626' : ($material->estado_stock === 'bajo' ? '#f59e0b' : '#166534') }};"></div>
        </div>
        <div style="font-size:.7rem; color:#9ca3af;">{{ $material->porcentaje_stock }}% del máximo</div>
        @endif
    </div>
    <div class="metrica-inv">
        <span class="metrica-label">Stock Mínimo</span>
        <div class="metrica-valor" style="font-size:1.2rem; color:#d97706;">{{ number_format($material->stock_minimo, 2) }}</div>
        <div style="font-size:.75rem; color:#6b7280;">{{ $material->unidad_medida }}</div>
    </div>
    <div class="metrica-inv">
        <span class="metrica-label">Precio Unitario</span>
        <div class="metrica-valor" style="font-size:1.1rem;">
            {{ $material->precio_unitario ? '$' . number_format($material->precio_unitario, 0, ',', '.') : '—' }}
        </div>
    </div>
    <div class="metrica-inv">
        <span class="metrica-label">Valor en Stock</span>
        @php $valorStock = $material->precio_unitario ? (float)$material->stock_actual * (float)$material->precio_unitario : null; @endphp
        <div class="metrica-valor" style="font-size:1.1rem;">
            {{ $valorStock ? '$' . number_format($valorStock, 0, ',', '.') : '—' }}
        </div>
    </div>
</div>

{{-- Formularios --}}
<div class="grid-2">
    {{-- Registrar Entrada --}}
    <div class="panel-card" id="entrada">
        <div class="panel-header">
            <div class="panel-titulo"><i class="bi bi-arrow-down-circle" style="color:#166534;"></i> Registrar Entrada</div>
        </div>
        <div class="panel-body">
            @if(session('exito'))
            <div style="background:#dcfce7; color:#166534; border-radius:8px; padding:.6rem .9rem; font-size:.82rem; margin-bottom:1rem;">
                <i class="bi bi-check-circle"></i> {{ session('exito') }}
            </div>
            @endif
            <form method="POST" action="{{ route('inventario.entrada', $material) }}">
                @csrf
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:.75rem; margin-bottom:.75rem;">
                    <div>
                        <label class="form-label">Cantidad *</label>
                        <input type="number" name="cantidad" class="form-input" step="0.01" min="0.01" required placeholder="0">
                    </div>
                    <div>
                        <label class="form-label">Fecha *</label>
                        <input type="date" name="fecha_movimiento" class="form-input" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div>
                        <label class="form-label">Precio unitario</label>
                        <input type="number" name="precio_unitario" class="form-input" step="0.01" min="0" placeholder="{{ $material->precio_unitario ?? '0' }}">
                    </div>
                    <div>
                        <label class="form-label">N° Factura</label>
                        <input type="text" name="numero_factura" class="form-input" placeholder="Opcional">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="form-label">Proveedor</label>
                        <input type="text" name="proveedor" class="form-input" placeholder="{{ $material->proveedor_habitual ?? 'Proveedor' }}">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-input" rows="2" placeholder="Opcional…"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn-morado" style="background:linear-gradient(135deg,#166534,#16a34a); width:100%; justify-content:center;">
                    <i class="bi bi-arrow-down-circle"></i> Registrar Entrada
                </button>
            </form>
        </div>
    </div>

    {{-- Ajuste de Stock --}}
    <div class="panel-card" id="ajuste">
        <div class="panel-header">
            <div class="panel-titulo"><i class="bi bi-pencil-square" style="color:#1e40af;"></i> Ajuste de Stock</div>
        </div>
        <div class="panel-body">
            <p style="font-size:.8rem; color:#6b7280; margin-bottom:1rem;">
                Stock actual: <strong>{{ number_format($material->stock_actual, 2) }} {{ $material->unidad_medida }}</strong>.
                Ingresa la cantidad exacta correcta.
            </p>
            <form method="POST" action="{{ route('inventario.ajuste', $material) }}">
                @csrf
                <div style="margin-bottom:.75rem;">
                    <label class="form-label">Nueva cantidad en stock *</label>
                    <input type="number" name="stock_nuevo" class="form-input" step="0.01" min="0" required
                           placeholder="{{ number_format($material->stock_actual, 2) }}">
                </div>
                <div style="margin-bottom:.75rem;">
                    <label class="form-label">Motivo del ajuste *</label>
                    <textarea name="motivo" class="form-input" rows="3" required
                              placeholder="Ej: Conteo físico realizado el {{ date('d/m/Y') }}…"></textarea>
                </div>
                <button type="submit" class="btn-morado" style="background:linear-gradient(135deg,#1e40af,#2563eb); width:100%; justify-content:center;">
                    <i class="bi bi-pencil-square"></i> Aplicar Ajuste
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Historial --}}
<div class="panel-card">
    <div class="panel-header">
        <div class="panel-titulo"><i class="bi bi-clock-history"></i> Historial de Movimientos</div>
        <span style="font-size:.78rem; color:#9ca3af;">{{ $movimientos->total() }} registros</span>
    </div>
    @if($movimientos->isEmpty())
        <div style="padding:2rem; text-align:center; color:#9ca3af; font-size:.85rem;">
            <i class="bi bi-inbox" style="font-size:1.8rem; display:block; margin-bottom:.4rem;"></i>
            Sin movimientos registrados.
        </div>
    @else
    <div style="overflow-x:auto;">
    <table class="tabla-mov">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Fecha</th>
                <th>Concepto</th>
                <th style="text-align:center;">Cantidad</th>
                <th>Antes → Después</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
        @foreach($movimientos as $mov)
        <tr>
            <td>
                <i class="bi {{ $mov->tipo_icono }}" style="color:{{ $mov->tipo_color }}; font-size:1rem;"></i>
                <span style="font-size:.75rem; font-weight:600; color:{{ $mov->tipo_color }}; margin-left:.2rem;">
                    {{ ucfirst($mov->tipo) }}
                </span>
            </td>
            <td style="white-space:nowrap; font-size:.8rem;">{{ $mov->fecha_movimiento->format('d/m/Y') }}</td>
            <td style="max-width:220px;">
                <div style="font-size:.8rem;">{{ $mov->concepto }}</div>
                @if($mov->evolucion)
                <a href="{{ route('evoluciones.show', $mov->evolucion) }}" style="font-size:.72rem; color:var(--color-principal); text-decoration:none;">
                    <i class="bi bi-link-45deg"></i> {{ $mov->evolucion->paciente->nombre_completo ?? 'Evolución' }}
                </a>
                @endif
                @if($mov->proveedor)
                <div style="font-size:.72rem; color:#9ca3af;"><i class="bi bi-truck"></i> {{ $mov->proveedor }}</div>
                @endif
                @if($mov->tipo === 'entrada' && $mov->numero_factura)
                <span style="font-size:0.72rem; color:#5c6b62;">Factura: {{ $mov->numero_factura }}</span>
                @endif
            </td>
            <td style="text-align:center; font-weight:600; color:{{ $mov->tipo_color }};">
                {{ $mov->tipo === 'salida' ? '-' : '+' }}{{ number_format($mov->cantidad, 2) }}
            </td>
            <td style="white-space:nowrap; font-size:.8rem; color:#6b7280;">
                {{ number_format($mov->stock_anterior, 2) }} → <strong>{{ number_format($mov->stock_posterior, 2) }}</strong>
            </td>
            <td style="font-size:.78rem;">{{ $mov->usuario->name ?? '—' }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    <div class="pagination-wrapper">
        {{ $movimientos->links() }}
    </div>
    @endif
</div>

@endsection
