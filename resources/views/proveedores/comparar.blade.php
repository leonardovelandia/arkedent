@extends('layouts.app')
@section('titulo', 'Comparar Precios')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1rem; font-size:.875rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; }
    .panel-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1.1rem; }
    .panel-header { padding:.75rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
    .panel-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:600; color:var(--color-hover); display:flex; align-items:center; gap:.4rem; }
    .panel-titulo i { color:var(--color-principal); }
    .panel-body { padding:1.25rem; }
    .form-label { font-size:.76rem; font-weight:700; color:var(--color-hover); display:block; margin-bottom:.2rem; }
    .form-input { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .75rem; font-size:.85rem; color:#1c2b22; background:#fff; outline:none; }
    .form-input:focus { border-color:var(--color-principal); }
    .tabla-comp { width:100%; border-collapse:collapse; font-size:.82rem; }
    .tabla-comp th { font-size:.69rem; font-weight:700; text-transform:uppercase; color:var(--color-principal); padding:.5rem .75rem; border-bottom:2px solid var(--color-muy-claro); text-align:left; }
    .tabla-comp td { padding:.5rem .75rem; border-bottom:1px solid var(--fondo-borde); color:#374151; vertical-align:middle; }
    .tabla-comp tr:last-child td { border-bottom:none; }
    .fila-barata { background:#dcfce7 !important; }
    .fila-cara { background:#fee2e2 !important; }

    /* Clásico */
    body:not([data-ui="glass"]) .panel-card { background:#fff; border:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .panel-header { border-bottom:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .panel-titulo { color:var(--color-hover); }
    body:not([data-ui="glass"]) .form-label { color:var(--color-hover); }
    body:not([data-ui="glass"]) .form-input { color:#1c2b22; background:#fff; border:1.5px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .tabla-comp td { color:#374151; border-bottom:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }

    /* Glass */
    body[data-ui="glass"] .panel-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .panel-header { background:rgba(0,0,0,0.25) !important; border-bottom:1px solid rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .panel-titulo { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .panel-titulo i { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .form-label { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .form-input { background:rgba(255,255,255,0.08) !important; border:1.5px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .form-input:focus { border-color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .tabla-comp th { color:rgba(0,234,255,0.90) !important; border-bottom:2px solid rgba(0,234,255,0.30) !important; }
    body[data-ui="glass"] .tabla-comp td { color:rgba(255,255,255,0.88) !important; border-bottom:1px solid rgba(255,255,255,0.06) !important; }
    body[data-ui="glass"] .fila-barata { background:rgba(74,222,128,0.15) !important; }
    body[data-ui="glass"] .fila-cara   { background:rgba(248,113,113,0.15) !important; }
    body[data-ui="glass"] .btn-gris { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
    body[data-ui="glass"] .page-title-main { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .page-title-sub  { color:rgba(255,255,255,0.55) !important; }
</style>
@endpush

@section('contenido')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.2rem; flex-wrap:wrap; gap:.75rem;">
    <div>
        <h4 style="font-family:var(--fuente-titulos); font-weight:700; color:#1c2b22; margin:0;">Comparar Precios</h4>
        <p style="font-size:.8rem; color:#9ca3af; margin:.15rem 0 0;">Compara precios de un material entre distintos proveedores</p>
    </div>
    <a href="{{ route('proveedores.index') }}" class="btn-gris"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

{{-- Selector de material --}}
<div class="panel-card">
    <div class="panel-header">
        <div class="panel-titulo"><i class="bi bi-search"></i> Seleccionar Material</div>
    </div>
    <div class="panel-body">
        <form method="GET" action="{{ route('proveedores.comparar') }}" style="display:flex; gap:.75rem; align-items:flex-end; flex-wrap:wrap;">
            <div style="flex:1; min-width:220px;">
                <label class="form-label">Material a comparar</label>
                <select name="material_id" class="form-input" id="materialSelect">
                    <option value="">— Selecciona un material —</option>
                    @foreach($materiales as $mat)
                    <option value="{{ $mat->id }}" {{ request('material_id') == $mat->id ? 'selected' : '' }}>
                        {{ $mat->nombre }} ({{ $mat->unidad_medida }})
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-morado"><i class="bi bi-bar-chart"></i> Comparar</button>
        </form>
    </div>
</div>

@if($material && $comparacion->count())

{{-- Tabla comparación --}}
<div class="panel-card">
    <div class="panel-header">
        <div class="panel-titulo"><i class="bi bi-table"></i> Comparación para: {{ $material->nombre }}</div>
    </div>
    <div style="overflow-x:auto;">
    <table class="tabla-comp">
        <thead>
            <tr>
                <th>Proveedor</th>
                <th style="text-align:right;">Último precio</th>
                <th style="text-align:right;">Precio promedio</th>
                <th>Última compra</th>
                <th>Calificación</th>
                <th style="text-align:right;">Diferencia vs más barato</th>
            </tr>
        </thead>
        <tbody>
        @php
            $minPrecio = $comparacion->min('ultimo_precio');
            $maxPrecio = $comparacion->max('ultimo_precio');
        @endphp
        @foreach($comparacion as $idx => $row)
        <tr class="{{ $row['ultimo_precio'] == $minPrecio && $comparacion->count() > 1 ? 'fila-barata' : ($row['ultimo_precio'] == $maxPrecio && $comparacion->count() > 1 ? 'fila-cara' : '') }}">
            <td>
                <div style="font-weight:600; color:#1c2b22;">{{ $row['proveedor']->nombre }}</div>
                <div style="font-size:.72rem; color:#9ca3af;">{{ $row['num_compras'] }} compras registradas</div>
            </td>
            <td style="text-align:right; font-weight:700; font-size:.92rem;">
                ${{ number_format($row['ultimo_precio'], 0, ',', '.') }}
            </td>
            <td style="text-align:right; color:#6b7280;">
                ${{ number_format($row['precio_promedio'], 0, ',', '.') }}
            </td>
            <td style="font-size:.8rem;">{{ $row['ultima_compra']?->format('d/m/Y') ?? '—' }}</td>
            <td>
                @if($row['proveedor']->calificacion)
                @for($i = 1; $i <= 5; $i++)
                <i class="bi bi-star{{ $i <= $row['proveedor']->calificacion ? '-fill' : '' }}"
                   style="color:{{ $i <= $row['proveedor']->calificacion ? '#FFC107' : '#DEE2E6' }}; font-size:.8rem;"></i>
                @endfor
                @else —
                @endif
            </td>
            <td style="text-align:right;">
                @if($row['ultimo_precio'] == $minPrecio)
                <span style="color:#166534; font-weight:600; font-size:.8rem;"><i class="bi bi-trophy-fill"></i> Más barato</span>
                @else
                @php $diff = $row['ultimo_precio'] - $minPrecio; $pct = $minPrecio > 0 ? round(($diff/$minPrecio)*100,1) : 0; @endphp
                <span style="color:#dc2626; font-size:.82rem;">+${{ number_format($diff, 0, ',', '.') }} ({{ $pct }}%)</span>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>

{{-- Gráfica --}}
<div class="panel-card">
    <div class="panel-header">
        <div class="panel-titulo"><i class="bi bi-bar-chart"></i> Comparativa de Precios</div>
    </div>
    <div class="panel-body">
        <canvas id="chartPrecios" style="max-height:280px;"></canvas>
    </div>
</div>

{{-- Historial de precios --}}
<div class="panel-card">
    <div class="panel-header">
        <div class="panel-titulo"><i class="bi bi-clock-history"></i> Historial de Precios — {{ $material->nombre }}</div>
    </div>
    <div style="overflow-x:auto;">
    <table class="tabla-comp">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th style="text-align:right;">Precio unitario</th>
                <th style="text-align:right;">Cantidad</th>
            </tr>
        </thead>
        <tbody>
        @forelse($historial as $item)
        <tr>
            <td style="font-size:.8rem; white-space:nowrap;">{{ $item->compra->fecha_compra->format('d/m/Y') }}</td>
            <td style="font-size:.8rem;">{{ $item->compra->proveedor->nombre }}</td>
            <td style="text-align:right; font-weight:600;">${{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
            <td style="text-align:right; font-size:.8rem; color:#6b7280;">{{ number_format($item->cantidad, 2) }} {{ $material->unidad_medida }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center; color:#9ca3af; padding:1.5rem;">Sin historial de compras pagadas.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>

@elseif($material && $comparacion->isEmpty())
<div class="panel-card">
    <div class="panel-body" style="text-align:center; color:#9ca3af; padding:2rem;">
        <i class="bi bi-inbox" style="font-size:2rem; display:block; margin-bottom:.5rem;"></i>
        No hay compras pagadas registradas para <strong>{{ $material->nombre }}</strong>.
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@if($material && $comparacion->count())
<script>
const labels = @json($comparacion->pluck('proveedor.nombre')->values());
const precios = @json($comparacion->pluck('ultimo_precio')->values());
const promedios = @json($comparacion->pluck('precio_promedio')->values());

const ctx = document.getElementById('chartPrecios').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Último precio',
                data: precios,
                backgroundColor: 'var(--sombra-principal)',
                borderColor: 'var(--color-principal)',
                borderWidth: 1,
                borderRadius: 6,
            },
            {
                label: 'Precio promedio',
                data: promedios,
                backgroundColor: 'rgba(124,58,237,0.3)',
                borderColor: 'var(--color-claro)',
                borderWidth: 1,
                borderRadius: 6,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: (ctx) => ' $' + ctx.parsed.y.toLocaleString('es-CO')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: false,
                ticks: {
                    callback: (val) => '$' + val.toLocaleString('es-CO')
                }
            }
        }
    }
});
</script>
@endif
@endpush
