<div class="panel-header">
    <div class="panel-titulo"><i class="bi bi-truck"></i> Lista de Proveedores</div>
    <span style="font-size:.78rem; color:#9ca3af;">{{ $proveedores->total() }} registros</span>
</div>

@if($proveedores->isEmpty())
<div style="padding:2.5rem; text-align:center; color:#9ca3af; font-size:.85rem;">
    <i class="bi bi-truck" style="font-size:2rem; display:block; margin-bottom:.5rem;"></i>
    No hay proveedores registrados.
    <br><a href="{{ route('proveedores.create') }}" style="color:var(--color-principal); text-decoration:none;">Registrar el primero →</a>
</div>
@else
<div style="overflow-x:auto;">
<table class="tabla-prov">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>NIT</th>
            <th>Ciudad</th>
            <th>Contacto</th>
            <th>Teléfono</th>
            <th>Categorías</th>
            <th>Calificación</th>
            <th style="text-align:right;">Total Compras</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    @foreach($proveedores as $proveedor)
    <tr>
        <td>
            <div style="font-weight:600; color:#1c2b22;">{{ $proveedor->nombre }}</div>
            @if($proveedor->condiciones_pago)
            <div style="font-size:.72rem; color:#9ca3af;">{{ $proveedor->condiciones_pago }}</div>
            @endif
        </td>
        <td style="font-size:.8rem; color:#6b7280;">{{ $proveedor->nit ?: '—' }}</td>
        <td style="font-size:.8rem;">{{ $proveedor->ciudad ?: '—' }}</td>
        <td style="font-size:.8rem;">{{ $proveedor->contacto ?: '—' }}</td>
        <td style="font-size:.8rem; white-space:nowrap;">
            {{ $proveedor->telefono ?: '—' }}
            @if($proveedor->whatsapp)
            <a href="https://wa.me/57{{ $proveedor->whatsapp }}" target="_blank" style="color:#166534; margin-left:.3rem; font-size:.85rem;"><i class="bi bi-whatsapp"></i></a>
            @endif
        </td>
        <td style="max-width:200px;">
            @if($proveedor->categorias && count($proveedor->categorias))
                @php $etqs = \App\Models\Proveedor::etiquetasCategorias(); @endphp
                @foreach(array_slice($proveedor->categorias, 0, 3) as $cat)
                <span class="badge-cat">{{ $etqs[$cat] ?? $cat }}</span>
                @endforeach
                @if(count($proveedor->categorias) > 3)
                <span class="badge-cat" style="background:#f3f4f6; color:#6b7280;">+{{ count($proveedor->categorias) - 3 }}</span>
                @endif
            @else
            <span style="color:#9ca3af; font-size:.78rem;">—</span>
            @endif
        </td>
        <td>
            @if($proveedor->calificacion)
            <div style="display:flex; gap:.1rem; align-items:center;">
                @for($i = 1; $i <= 5; $i++)
                <i class="bi bi-star{{ $i <= $proveedor->calificacion ? '-fill' : '' }}"
                   style="color:{{ $i <= $proveedor->calificacion ? '#FFC107' : '#DEE2E6' }}; font-size:.85rem;"></i>
                @endfor
            </div>
            <div style="font-size:.7rem; color:#9ca3af;">{{ $proveedor->calificacion }}</div>
            @else
            <span style="color:#9ca3af; font-size:.78rem;">—</span>
            @endif
        </td>
        <td style="text-align:right; font-weight:600; color:#166534;">
            ${{ number_format($proveedor->total_compras, 0, ',', '.') }}
        </td>
        <td>
            <div style="display:flex; gap:.3rem;">
                <a href="{{ route('proveedores.show', $proveedor) }}" class="acc-btn acc-ver"><i class="bi bi-eye"></i> Ver</a>
                <a href="{{ route('proveedores.edit', $proveedor) }}" class="acc-btn acc-edit"><i class="bi bi-pencil"></i></a>
            </div>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
</div>
<div class="pagination-wrapper">{{ $proveedores->links() }}</div>
@endif
