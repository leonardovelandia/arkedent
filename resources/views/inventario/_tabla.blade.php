<div class="panel-header">
    <div class="panel-titulo"><i class="bi bi-box-seam"></i> Materiales e Insumos</div>
    <span style="font-size:.78rem; color:#9ca3af;">{{ $materiales->total() }} registros</span>
</div>
@if($materiales->isEmpty())
    <div style="padding:2.5rem; text-align:center; color:#9ca3af; font-size:.85rem;">
        <i class="bi bi-inbox" style="font-size:2rem; display:block; margin-bottom:.5rem;"></i>
        No se encontraron materiales con los filtros aplicados.
    </div>
@else
<div class="tabla-scroll">
<table class="tabla-inv">
    <thead>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th style="text-align:center;">Stock Actual</th>
            <th style="text-align:center;">Mínimo</th>
            <th style="text-align:center;">Estado</th>
            <th style="text-align:right;">Precio Unit.</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach($materiales as $material)
    @php $estadoStock = $material->estado_stock; @endphp
    <tr class="{{ $material->activo ? '' : 'fila-inactiva' }}">
        <td style="color:#9ca3af; font-size:.75rem;">{{ $material->codigo ?: '—' }}</td>
        <td>
            <div style="font-weight:500; color:#1c2b22;">{{ $material->nombre }}</div>
            @if($material->ubicacion)
            <div style="font-size:.72rem; color:#9ca3af;"><i class="bi bi-geo-alt"></i> {{ $material->ubicacion }}</div>
            @endif
        </td>
        <td>
            @if($material->categoria)
            <span class="badge-cat" style="background:{{ $material->categoria->color ?? 'var(--color-principal)' }};">{{ $material->categoria->nombre }}</span>
            @else
            <span style="color:#9ca3af; font-size:.78rem;">—</span>
            @endif
        </td>
        <td style="text-align:center;">
            <div style="font-weight:600; font-size:.9rem; color:{{ $estadoStock === 'critico' ? '#dc2626' : ($estadoStock === 'bajo' ? '#d97706' : '#166534') }};">
                {{ number_format($material->stock_actual, 2) }}
            </div>
            <div style="font-size:.7rem; color:#9ca3af;">{{ $material->unidad_medida }}</div>
            @if($material->porcentaje_stock !== null && $material->activo)
            <div class="barra-stock">
                <div class="barra-fill" style="width:{{ $material->porcentaje_stock }}%; background:{{ $estadoStock === 'critico' ? '#dc2626' : ($estadoStock === 'bajo' ? '#f59e0b' : '#166534') }};"></div>
            </div>
            @endif
        </td>
        <td style="text-align:center; font-size:.82rem; color:#6b7280;">
            {{ number_format($material->stock_minimo, 2) }} {{ $material->unidad_medida }}
        </td>
        <td style="text-align:center;">
            @if(!$material->activo)
            <span class="badge bg-secondary" style="font-size:.7rem; padding:.22rem .6rem;">
                <i class="bi bi-slash-circle"></i> Desactivado
            </span>
            @else
            <span class="badge bg-{{ $material->estado_stock_color }}" style="font-size:.7rem; padding:.22rem .6rem;">
                @if($estadoStock === 'critico') <i class="bi bi-exclamation-triangle"></i> Crítico
                @elseif($estadoStock === 'bajo') <i class="bi bi-arrow-down"></i> Bajo
                @else <i class="bi bi-check-circle"></i> Normal
                @endif
            </span>
            @endif
        </td>
        <td style="text-align:right; font-size:.82rem; white-space:nowrap;">
            {{ $material->precio_unitario ? '$' . number_format($material->precio_unitario, 0, ',', '.') : '—' }}
        </td>
        <td>
            <div style="display:flex; gap:.3rem; flex-wrap:wrap; justify-content:flex-end;">
                <a href="{{ route('inventario.show', $material) }}" class="acc-btn acc-ver"><i class="bi bi-eye"></i> Ver</a>
                @if($material->activo)
                <a href="{{ route('inventario.edit', $material) }}" class="acc-btn acc-edit"><i class="bi bi-pencil"></i></a>
                <a href="{{ route('inventario.show', $material) }}#entrada" class="acc-btn acc-entrada"><i class="bi bi-plus-circle"></i> Entrada</a>
                @else
                <form method="POST" action="{{ route('inventario.activar', $material) }}" style="display:inline;">
                    @csrf @method('PATCH')
                    <button type="submit" class="acc-btn acc-activar"><i class="bi bi-arrow-up-circle"></i> Activar</button>
                </form>
                @endif
            </div>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
</div>
<div class="pagination-wrapper">
    {{ $materiales->links() }}
</div>
@endif
