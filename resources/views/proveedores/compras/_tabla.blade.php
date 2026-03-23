<div class="panel-header">
    <div class="panel-titulo"><i class="bi bi-cart3"></i> Compras</div>
    <span style="font-size:.78rem; color:#9ca3af;">{{ $compras->total() }} registros</span>
</div>

@if($compras->isEmpty())
<div style="padding:2.5rem; text-align:center; color:#9ca3af; font-size:.85rem;">
    <i class="bi bi-inbox" style="font-size:2rem; display:block; margin-bottom:.5rem;"></i>
    No hay compras registradas.
    <br><a href="{{ route('compras.create') }}" style="color:var(--color-principal); text-decoration:none;">Registrar la primera →</a>
</div>
@else
<div style="overflow-x:auto;">
<table class="tabla-compras">
    <thead>
        <tr>
            <th>N° Compra</th>
            <th>Fecha</th>
            <th>Proveedor</th>
            <th>Factura</th>
            <th style="text-align:center;">Items</th>
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
            <span style="font-family:monospace; font-weight:700; color:var(--color-principal); font-size:.8rem;">{{ $compra->numero_formateado }}</span>
        </td>
        <td style="white-space:nowrap; font-size:.8rem;">{{ $compra->fecha_compra->format('d/m/Y') }}</td>
        <td>
            <a href="{{ route('proveedores.show', $compra->proveedor) }}" style="color:#1c2b22; text-decoration:none; font-weight:500; font-size:.82rem;">
                {{ $compra->proveedor->nombre }}
            </a>
        </td>
        <td style="font-size:.8rem; color:#6b7280;">{{ $compra->numero_factura ?: '—' }}</td>
        <td style="text-align:center; font-size:.8rem;">{{ $compra->items_count }}</td>
        <td style="text-align:right; font-weight:700; color:#166534; white-space:nowrap;">${{ number_format($compra->total, 0, ',', '.') }}</td>
        <td style="font-size:.78rem;">{{ $compra->metodo_pago_label }}</td>
        <td>
            <span class="badge bg-{{ $compra->estado_color }}" style="font-size:.7rem; padding:.2rem .5rem;">{{ ucfirst($compra->estado) }}</span>
        </td>
        <td>
            <a href="{{ route('compras.show', $compra) }}" class="acc-btn acc-ver"><i class="bi bi-eye"></i> Ver</a>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
</div>
<div class="pagination-wrapper">{{ $compras->links() }}</div>
@endif
