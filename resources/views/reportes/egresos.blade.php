@extends('layouts.app')
@section('titulo', 'Reporte de Egresos')

@push('estilos')
<style>
    .reporte-header { display:flex; align-items:center; gap:.75rem; margin-bottom:1.5rem; }
    .btn-rojo  { background:#DC3545; color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; transition:filter .18s; }
    .btn-rojo:hover  { filter:brightness(1.1); color:#fff; }
    .btn-verde { background:#166534; color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; transition:filter .18s; }
    .btn-verde:hover { filter:brightness(1.1); color:#fff; }
    .btn-gris  { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; }
    .btn-gris:hover  { background:#e5e7eb; color:#374151; }

    .filtros-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:1.25rem; margin-bottom:1.25rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .filtros-grid { display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:.75rem; align-items:end; }
    @media(max-width:860px){ .filtros-grid{ grid-template-columns:1fr 1fr; } }
    @media(max-width:500px){ .filtros-grid{ grid-template-columns:1fr; } }
    .form-label { font-size:.78rem; font-weight:700; color:var(--color-hover); display:block; margin-bottom:.25rem; }
    .form-input { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .75rem; font-size:.85rem; color:#1c2b22; background:#fff; outline:none; }
    .form-input:focus { border-color:#DC3545; }

    .resumen-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.25rem; }
    @media(max-width:700px){ .resumen-grid{ grid-template-columns:1fr 1fr; } }

    .metrica-reporte { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1.1rem 1.25rem; display:flex; flex-direction:column; gap:.4rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .metrica-valor { font-family:var(--fuente-titulos); font-size:1.6rem; font-weight:600; color:#DC3545; line-height:1; }
    .metrica-label { font-size:.72rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; }
    .metrica-sub   { font-size:.78rem; color:#6b7280; }

    .panel-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1.25rem; }
    .panel-card-header { padding:.85rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
    .panel-card-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:600; color:var(--color-hover); display:flex; align-items:center; gap:.45rem; }

    .tabla-reporte { width:100%; border-collapse:collapse; font-size:.82rem; }
    .tabla-reporte th { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#DC3545; padding:.5rem .75rem; border-bottom:2px solid #fecdd3; text-align:left; }
    .tabla-reporte td { padding:.55rem .75rem; border-bottom:1px solid var(--fondo-borde); color:#374151; vertical-align:middle; }
    .tabla-reporte tr:last-child td { border-bottom:none; }
    .tabla-reporte tr:hover td { background:#fff5f5; }
    .tabla-reporte tfoot td { font-weight:700; color:#DC3545; border-top:2px solid #fecdd3; border-bottom:none; }

    .barra-progreso-custom { height:6px; background:#fde8e8; border-radius:50px; overflow:hidden; margin-top:.3rem; }
    .barra-progreso-fill   { height:100%; background:#DC3545; border-radius:50px; }
    .pagination-wrapper    { padding:.75rem 1.25rem; border-top:1px solid var(--fondo-borde); display:flex; justify-content:flex-end; }

    /* ── Classic overrides ── */
    body:not([data-ui="glass"]) .filtros-card   { background:#fff; border:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .form-label     { color:var(--color-hover); }
    body:not([data-ui="glass"]) .form-input     { border:1.5px solid var(--color-muy-claro); color:#1c2b22; background:#fff; }
    body:not([data-ui="glass"]) .metrica-reporte { background:#fff; border:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .panel-card     { background:#fff; border:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .panel-card-header { border-bottom:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .btn-gris       { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }

    /* ── Aurora Glass overrides ── */
    body[data-ui="glass"] .filtros-card  { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(248,113,113,0.40) !important; box-shadow:0 0 8px rgba(248,113,113,0.20) !important; }
    body[data-ui="glass"] .form-label    { color:rgba(252,165,165,0.90) !important; }
    body[data-ui="glass"] .form-input    { background:rgba(255,255,255,0.08) !important; border:1.5px solid rgba(248,113,113,0.30) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .form-input:focus { border-color:rgba(248,113,113,0.70) !important; }
    body[data-ui="glass"] .form-input::placeholder { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .metrica-reporte { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(248,113,113,0.35) !important; box-shadow:0 0 8px rgba(248,113,113,0.18) !important; }
    body[data-ui="glass"] .metrica-label  { color:rgba(252,165,165,0.75) !important; }
    body[data-ui="glass"] .metrica-valor  { color:#fca5a5 !important; }
    body[data-ui="glass"] .metrica-sub    { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .panel-card     { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(248,113,113,0.35) !important; box-shadow:0 0 8px rgba(248,113,113,0.18) !important; }
    body[data-ui="glass"] .panel-card-header  { background:rgba(0,0,0,0.20) !important; border-bottom:1px solid rgba(248,113,113,0.20) !important; }
    body[data-ui="glass"] .panel-card-titulo  { color:rgba(252,165,165,0.90) !important; }
    body[data-ui="glass"] .tabla-reporte th   { color:#fca5a5 !important; border-bottom:2px solid rgba(252,165,165,0.25) !important; }
    body[data-ui="glass"] .tabla-reporte td   { color:rgba(255,255,255,0.80) !important; border-bottom:1px solid rgba(255,255,255,0.08) !important; }
    body[data-ui="glass"] .tabla-reporte tr:hover td  { background:rgba(248,113,113,0.08) !important; }
    body[data-ui="glass"] .tabla-reporte tfoot td     { color:#fca5a5 !important; border-top:2px solid rgba(252,165,165,0.25) !important; }
    body[data-ui="glass"] .barra-progreso-custom { background:rgba(248,113,113,0.18) !important; }
    body[data-ui="glass"] .btn-gris  { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
    body[data-ui="glass"] .btn-rojo  { filter:brightness(1.15) !important; }
    body[data-ui="glass"] .btn-verde { filter:brightness(1.15) !important; }
    body[data-ui="glass"] .page-title-main { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .page-title-sub  { color:rgba(255,255,255,0.55) !important; }
</style>
@endpush

@section('contenido')

<div class="reporte-header">
    <a href="{{ route('reportes.index') }}"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Reporte de Egresos</h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">Análisis de gastos y egresos</p>
    </div>
</div>

{{-- Filtros --}}
<div class="filtros-card">
    <form method="GET" action="{{ route('reportes.egresos') }}">
        <div class="filtros-grid">
            <div>
                <label class="form-label">Desde</label>
                <input type="date" name="desde" class="form-input" value="{{ $desde->format('Y-m-d') }}">
            </div>
            <div>
                <label class="form-label">Hasta</label>
                <input type="date" name="hasta" class="form-input" value="{{ $hasta->format('Y-m-d') }}">
            </div>
            <div>
                <label class="form-label">Categoría</label>
                <select name="categoria_id" class="form-input">
                    <option value="">Todas</option>
                    @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ $categoriaId == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:.4rem;align-items:flex-end;">
                <button type="submit" class="btn-rojo"><i class="bi bi-search"></i> Filtrar</button>
                <a href="{{ route('reportes.egresos') }}" class="btn-gris"><i class="bi bi-x"></i></a>
            </div>
        </div>
    </form>
</div>

{{-- Resumen --}}
<div class="resumen-grid">
    <div class="metrica-reporte">
        <span class="metrica-label">Total Egresos</span>
        <div class="metrica-valor">${{ number_format($totalFiltrado, 0, ',', '.') }}</div>
        <div class="metrica-sub">{{ $conteoFiltrado }} egreso(s) en el período</div>
    </div>
    <div class="metrica-reporte">
        <span class="metrica-label">Período Desde</span>
        <div class="metrica-valor" style="font-size:1.2rem;">{{ $desde->format('d/m/Y') }}</div>
        <div class="metrica-sub">Fecha de inicio</div>
    </div>
    <div class="metrica-reporte">
        <span class="metrica-label">Período Hasta</span>
        <div class="metrica-valor" style="font-size:1.2rem;">{{ $hasta->format('d/m/Y') }}</div>
        <div class="metrica-sub">Fecha de fin</div>
    </div>
</div>

{{-- Por categoría --}}
@if($porCategoria->isNotEmpty())
<div class="panel-card">
    <div class="panel-card-header">
        <div class="panel-card-titulo"><i class="bi bi-list-check" style="color:#DC3545;"></i> Por categoría</div>
    </div>
    <div style="padding:0;">
        @php $totalCats = $porCategoria->sum('egresos_sum_valor') ?: 1; @endphp
        <table class="tabla-reporte">
            <thead><tr><th>Categoría</th><th style="text-align:right;">Total</th><th style="text-align:right;">%</th><th style="min-width:120px;"></th></tr></thead>
            <tbody>
            @foreach($porCategoria as $cat)
            @php $pct = round(($cat->egresos_sum_valor / $totalCats) * 100, 1); @endphp
            <tr>
                <td><span style="display:inline-flex;align-items:center;gap:.35rem;font-size:.82rem;font-weight:600;">
                    @if($cat->icono)<i class="{{ $cat->icono }}" style="color:{{ $cat->color }};"></i>@endif
                    {{ $cat->nombre }}
                </span></td>
                <td style="text-align:right;font-weight:700;color:#DC3545;">${{ number_format($cat->egresos_sum_valor, 0, ',', '.') }}</td>
                <td style="text-align:right;color:#6C757D;font-weight:600;">{{ $pct }}%</td>
                <td><div class="barra-progreso-custom"><div class="barra-progreso-fill" style="background:{{ $cat->color ?? '#DC3545' }};width:{{ $pct }}%;"></div></div></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Listado --}}
<div class="panel-card">
    <div class="panel-card-header">
        <div class="panel-card-titulo"><i class="bi bi-receipt" style="color:#DC3545;"></i> Detalle de egresos</div>
        <span style="font-size:.75rem;color:#9ca3af;">{{ $egresos->total() }} registro(s)</span>
    </div>
    <div style="overflow-x:auto;">
    <table class="tabla-reporte">
        <thead>
            <tr>
                <th>N° Egreso</th>
                <th>Fecha</th>
                <th>Concepto</th>
                <th>Categoría</th>
                <th>Método Pago</th>
                <th style="text-align:right;">Valor</th>
            </tr>
        </thead>
        <tbody>
        @forelse($egresos as $egreso)
        <tr>
            <td style="font-family:monospace;font-size:.78rem;color:#c2410c;">{{ $egreso->numero_egreso ?? '#'.$egreso->id }}</td>
            <td style="white-space:nowrap;color:#4b5563;font-size:.82rem;">{{ $egreso->fecha_egreso->format('d/m/Y') }}</td>
            <td>
                <div style="font-weight:500;">{{ $egreso->concepto }}</div>
                @if($egreso->descripcion)<div style="font-size:.75rem;color:#9ca3af;">{{ Str::limit($egreso->descripcion, 60) }}</div>@endif
            </td>
            <td>
                @if($egreso->categoria)
                <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.78rem;font-weight:600;background:#fde8e8;color:#DC3545;padding:.18rem .55rem;border-radius:20px;">
                    @if($egreso->categoria->icono)<i class="{{ $egreso->categoria->icono }}"></i>@endif
                    {{ $egreso->categoria->nombre }}
                </span>
                @else<span style="color:#d1d5db;">—</span>@endif
            </td>
            <td style="font-size:.82rem;">{{ $egreso->metodo_pago_label }}</td>
            <td style="text-align:right;font-weight:700;color:#DC3545;">${{ number_format($egreso->valor, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;padding:2rem;color:#9ca3af;">Sin egresos en el período seleccionado.</td></tr>
        @endforelse
        </tbody>
        @if($egresos->isNotEmpty())
        <tfoot>
            <tr>
                <td colspan="5" style="text-align:right;font-size:.8rem;">Total del período:</td>
                <td style="text-align:right;font-size:1rem;">${{ number_format($totalFiltrado, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
    </div>
    @if($egresos->hasPages())
    <div class="pagination-wrapper">{{ $egresos->links() }}</div>
    @endif
</div>

@endsection
