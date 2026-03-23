@extends('layouts.app')
@section('titulo', 'Reporte de Ingresos')

@push('estilos')
<style>
    .reporte-header { display:flex; align-items:center; gap:.75rem; margin-bottom:1.5rem; }
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; transition:filter .18s; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-verde { background:#166534; color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; transition:filter .18s; }
    .btn-verde:hover { filter:brightness(1.1); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; color:#374151; }

    .filtros-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:1.25rem; margin-bottom:1.25rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .filtros-grid { display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:.75rem; align-items:end; }
    @media(max-width:860px){ .filtros-grid{ grid-template-columns:1fr 1fr; } }
    @media(max-width:500px){ .filtros-grid{ grid-template-columns:1fr; } }
    .form-label { font-size:.78rem; font-weight:700; color:var(--color-hover); display:block; margin-bottom:.25rem; }
    .form-input { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .75rem; font-size:.85rem; color:#1c2b22; background:#fff; outline:none; }
    .form-input:focus { border-color:var(--color-principal); }
    .filtros-acciones { display:flex; gap:.5rem; flex-wrap:wrap; }

    .resumen-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.25rem; }
    @media(max-width:700px){ .resumen-grid{ grid-template-columns:1fr 1fr; } }

    .metrica-reporte { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1.1rem 1.25rem; display:flex; flex-direction:column; gap:.4rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .metrica-valor { font-family:var(--fuente-titulos); font-size:1.6rem; font-weight:600; color:var(--color-principal); line-height:1; }
    .metrica-label { font-size:.72rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; }
    .metrica-sub { font-size:.78rem; color:#6b7280; }

    .panel-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1.25rem; }
    .panel-card-header { padding:.85rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
    .panel-card-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:600; color:var(--color-hover); display:flex; align-items:center; gap:.45rem; }
    .panel-card-titulo i { color:var(--color-principal); }

    .tabla-reporte { width:100%; border-collapse:collapse; font-size:.82rem; }
    .tabla-reporte th { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-principal); padding:.5rem .75rem; border-bottom:2px solid var(--color-muy-claro); text-align:left; }
    .tabla-reporte td { padding:.55rem .75rem; border-bottom:1px solid var(--fondo-borde); color:#374151; vertical-align:middle; }
    .tabla-reporte tr:last-child td { border-bottom:none; }
    .tabla-reporte tr:hover td { background:var(--fondo-card-alt); }
    .tabla-reporte tfoot td { font-weight:700; color:var(--color-principal); border-top:2px solid var(--color-muy-claro); border-bottom:none; }

    .badge-metodo { display:inline-block; font-size:.72rem; font-weight:600; padding:.2rem .6rem; border-radius:50px; }
    .badge-efectivo     { background:#dcfce7; color:#166534; }
    .badge-transferencia{ background:#dbeafe; color:#1e40af; }
    .badge-tarjeta      { background:var(--color-muy-claro); color:var(--color-principal); }
    .badge-otro         { background:#f3f4f6; color:#374151; }

    .barra-progreso-custom { height:6px; background:var(--color-muy-claro); border-radius:50px; overflow:hidden; margin-top:.3rem; }
    .barra-progreso-fill { height:100%; background:var(--color-principal); border-radius:50px; }

    .pagination-wrapper { padding:.75rem 1.25rem; border-top:1px solid var(--fondo-borde); display:flex; justify-content:flex-end; }
</style>
@endpush

@section('contenido')

<div class="reporte-header">
    <a href="{{ route('reportes.index') }}"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Reporte de Ingresos</h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">Análisis de pagos recibidos</p>
    </div>
</div>

{{-- Filtros --}}
<div class="filtros-card">
    <form id="form-filtros-ingresos" method="GET" action="{{ route('reportes.ingresos') }}">
        <div class="filtros-grid">
            <div>
                <label class="form-label">Desde</label>
                <input type="date" name="desde" id="filtro-desde" class="form-input" value="{{ $desde->format('Y-m-d') }}">
            </div>
            <div>
                <label class="form-label">Hasta</label>
                <input type="date" name="hasta" id="filtro-hasta" class="form-input" value="{{ $hasta->format('Y-m-d') }}">
            </div>
            <div>
                <label class="form-label">Método de pago</label>
                <select name="metodo_pago" id="filtro-metodo" class="form-input">
                    <option value="">Todos</option>
                    <option value="efectivo"      {{ $metodoPago === 'efectivo'      ? 'selected' : '' }}>Efectivo</option>
                    <option value="transferencia" {{ $metodoPago === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                    <option value="tarjeta"       {{ $metodoPago === 'tarjeta'       ? 'selected' : '' }}>Tarjeta</option>
                </select>
            </div>
            <div class="filtros-acciones">
                <a href="{{ route('reportes.ingresos') }}" class="btn-gris"><i class="bi bi-x"></i> Limpiar</a>
                <a id="btn-csv" href="{{ route('reportes.exportar-ingresos', request()->query()) }}" class="btn-verde"><i class="bi bi-download"></i> CSV</a>
            </div>
        </div>
    </form>
</div>

{{-- Resumen --}}
<div class="resumen-grid">
    <div class="metrica-reporte">
        <span class="metrica-label">Total del período</span>
        <div class="metrica-valor">${{ number_format($totalFiltrado, 0, ',', '.') }}</div>
        <div class="metrica-sub">{{ $desde->locale('es')->isoFormat('D MMM') }} — {{ $hasta->locale('es')->isoFormat('D MMM YYYY') }}</div>
    </div>
    <div class="metrica-reporte">
        <span class="metrica-label">Transacciones</span>
        <div class="metrica-valor">{{ $conteoFiltrado }}</div>
        <div class="metrica-sub">Pagos en el período</div>
    </div>
    <div class="metrica-reporte">
        <span class="metrica-label">Promedio por pago</span>
        <div class="metrica-valor">${{ $conteoFiltrado > 0 ? number_format($totalFiltrado / $conteoFiltrado, 0, ',', '.') : 0 }}</div>
        <div class="metrica-sub">Valor promedio</div>
    </div>
</div>

{{-- Por método de pago --}}
@if($porMetodo->isNotEmpty())
<div class="panel-card">
    <div class="panel-card-header">
        <div class="panel-card-titulo"><i class="bi bi-credit-card"></i> Distribución por método de pago</div>
    </div>
    @php $totalMetodos = $porMetodo->sum('suma') ?: 1; @endphp
    <table class="tabla-reporte">
        <thead>
            <tr>
                <th>Método</th>
                <th style="text-align:center;">Cantidad</th>
                <th style="text-align:right;">Total</th>
                <th style="text-align:right;">%</th>
                <th style="min-width:140px;"></th>
            </tr>
        </thead>
        <tbody>
        @foreach($porMetodo as $mp)
        @php
            $pct = round(($mp->suma / $totalMetodos) * 100, 1);
            $badgeClass = match($mp->metodo_pago) {
                'efectivo'      => 'badge-efectivo',
                'transferencia' => 'badge-transferencia',
                'tarjeta'       => 'badge-tarjeta',
                default         => 'badge-otro'
            };
        @endphp
        <tr>
            <td><span class="badge-metodo {{ $badgeClass }}">{{ ucfirst($mp->metodo_pago) }}</span></td>
            <td style="text-align:center;">{{ $mp->total }}</td>
            <td style="text-align:right;font-weight:600;">${{ number_format($mp->suma, 0, ',', '.') }}</td>
            <td style="text-align:right;color:var(--color-principal);font-weight:600;">{{ $pct }}%</td>
            <td><div class="barra-progreso-custom"><div class="barra-progreso-fill" style="width:{{ $pct }}%;"></div></div></td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Tabla detallada --}}
<div class="panel-card">
    <div class="panel-card-header">
        <div class="panel-card-titulo"><i class="bi bi-table"></i> Detalle de pagos</div>
        <span style="font-size:.78rem;color:#9ca3af;">{{ $pagos->total() }} registros</span>
    </div>
    @if($pagos->isEmpty())
        <div style="padding:2rem;text-align:center;color:#9ca3af;font-size:.85rem;">
            <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>
            No se encontraron pagos con los filtros seleccionados.
        </div>
    @else
    <div style="overflow-x:auto;">
    <table class="tabla-reporte">
        <thead>
            <tr>
                <th>N° Recibo</th>
                <th>Fecha</th>
                <th>Paciente</th>
                <th>Concepto</th>
                <th>Tratamiento</th>
                <th>Método</th>
                <th style="text-align:right;">Valor</th>
            </tr>
        </thead>
        <tbody>
        @foreach($pagos as $pago)
        @php
            $badgeClass = match($pago->metodo_pago) {
                'efectivo'      => 'badge-efectivo',
                'transferencia' => 'badge-transferencia',
                'tarjeta'       => 'badge-tarjeta',
                default         => 'badge-otro'
            };
        @endphp
        <tr>
            <td style="font-weight:600;color:var(--color-principal);">{{ $pago->numero_recibo }}</td>
            <td style="white-space:nowrap;">{{ $pago->fecha_pago->locale('es')->isoFormat('D MMM YYYY') }}</td>
            <td>
                <div style="font-weight:500;">{{ $pago->paciente->nombre_completo ?? '—' }}</div>
                <div style="font-size:.75rem;color:#9ca3af;">{{ $pago->paciente->numero_documento ?? '' }}</div>
            </td>
            <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $pago->concepto }}">
                {{ $pago->concepto }}
            </td>
            <td>{{ $pago->tratamiento->nombre ?? '—' }}</td>
            <td><span class="badge-metodo {{ $badgeClass }}">{{ ucfirst($pago->metodo_pago) }}</span></td>
            <td style="text-align:right;font-weight:600;">${{ number_format($pago->valor, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" style="text-align:right;">Total página:</td>
                <td style="text-align:right;">${{ number_format($pagos->sum('valor'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    </div>
    <div class="pagination-wrapper">
        {{ $pagos->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    var form   = document.getElementById('form-filtros-ingresos');
    var desde  = document.getElementById('filtro-desde');
    var hasta  = document.getElementById('filtro-hasta');
    var metodo = document.getElementById('filtro-metodo');
    var btnCsv = document.getElementById('btn-csv');

    function actualizarCsv() {
        var params = new URLSearchParams(new FormData(form)).toString();
        btnCsv.href = '{{ route('reportes.exportar-ingresos') }}' + '?' + params;
    }

    function filtrar() {
        actualizarCsv();
        form.submit();
    }

    desde.addEventListener('change',  filtrar);
    hasta.addEventListener('change',  filtrar);
    metodo.addEventListener('change', filtrar);
})();
</script>
@endpush

@endsection
