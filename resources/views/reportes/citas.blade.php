@extends('layouts.app')
@section('titulo', 'Reporte de Citas')

@push('estilos')
<style>
    .reporte-header { display:flex; align-items:center; gap:.75rem; margin-bottom:1.5rem; }
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; transition:filter .18s; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
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

    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.25rem; }
    @media(max-width:900px){ .stats-grid{ grid-template-columns:1fr 1fr; } }

    .metrica-reporte { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1.1rem 1.25rem; display:flex; flex-direction:column; gap:.4rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .metrica-valor { font-family:var(--fuente-titulos); font-size:1.6rem; font-weight:600; color:var(--color-principal); line-height:1; }
    .metrica-label { font-size:.72rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; }

    .panel-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1.25rem; }
    .panel-card-header { padding:.85rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
    .panel-card-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:600; color:var(--color-hover); display:flex; align-items:center; gap:.45rem; }
    .panel-card-titulo i { color:var(--color-principal); }

    .tabla-reporte { width:100%; border-collapse:collapse; font-size:.82rem; }
    .tabla-reporte th { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-principal); padding:.5rem .75rem; border-bottom:2px solid var(--color-muy-claro); text-align:left; }
    .tabla-reporte td { padding:.55rem .75rem; border-bottom:1px solid var(--fondo-borde); color:#374151; vertical-align:middle; }
    .tabla-reporte tr:last-child td { border-bottom:none; }
    .tabla-reporte tr:hover td { background:var(--fondo-card-alt); }

    .barra-progreso-custom { height:6px; background:var(--color-muy-claro); border-radius:50px; overflow:hidden; margin-top:.3rem; }
    .barra-progreso-fill { height:100%; background:var(--color-principal); border-radius:50px; }

    .badge-estado { display:inline-block; font-size:.72rem; font-weight:600; padding:.22rem .65rem; border-radius:50px; }
    .estado-pendiente   { background:#fff3e0; color:#e65100; }
    .estado-confirmada  { background:var(--color-muy-claro); color:var(--color-principal); }
    .estado-en_proceso  { background:#dbeafe; color:#1e40af; }
    .estado-atendida    { background:#dcfce7; color:#166534; }
    .estado-cancelada   { background:#fee2e2; color:#dc2626; }
    .estado-no_asistio  { background:#f3f4f6; color:#6b7280; }

    .pagination-wrapper { padding:.75rem 1.25rem; border-top:1px solid var(--fondo-borde); display:flex; justify-content:flex-end; }
    .btn-accion { display:inline-flex; align-items:center; gap:.3rem; padding:.25rem .6rem; border-radius:6px; font-size:.75rem; font-weight:500; text-decoration:none; background:var(--color-muy-claro); color:var(--color-principal); transition:background .15s; }
    .btn-accion:hover { background:var(--color-muy-claro); color:var(--color-principal); }

    /* ── Classic overrides ── */
    body:not([data-ui="glass"]) .filtros-card { background:#fff; border:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .form-label   { color:var(--color-hover); }
    body:not([data-ui="glass"]) .form-input   { border:1.5px solid var(--color-muy-claro); color:#1c2b22; background:#fff; }
    body:not([data-ui="glass"]) .metrica-reporte { background:#fff; border:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .panel-card   { background:#fff; border:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .panel-card-header { border-bottom:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .btn-gris     { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }

    /* ── Aurora Glass overrides ── */
    body[data-ui="glass"] .filtros-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .form-label   { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .form-input   { background:rgba(255,255,255,0.08) !important; border:1.5px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .form-input:focus { border-color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .form-input::placeholder { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .metrica-reporte { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.35) !important; box-shadow:0 0 8px rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .metrica-label  { color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .metrica-valor  { color:rgba(0,234,255,0.95) !important; }
    body[data-ui="glass"] .panel-card     { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.35) !important; box-shadow:0 0 8px rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .panel-card-header  { background:rgba(0,0,0,0.20) !important; border-bottom:1px solid rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .panel-card-titulo  { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .panel-card-titulo i { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .tabla-reporte th   { color:rgba(0,234,255,0.90) !important; border-bottom:2px solid rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .tabla-reporte td   { color:rgba(255,255,255,0.80) !important; border-bottom:1px solid rgba(255,255,255,0.08) !important; }
    body[data-ui="glass"] .tabla-reporte tr:hover td { background:rgba(0,234,255,0.06) !important; }
    body[data-ui="glass"] .barra-progreso-custom { background:rgba(255,255,255,0.12) !important; }
    body[data-ui="glass"] .estado-pendiente  { background:rgba(230,81,0,0.18) !important; color:#fb923c !important; }
    body[data-ui="glass"] .estado-confirmada { background:rgba(0,234,255,0.15) !important; color:rgba(0,234,255,0.95) !important; }
    body[data-ui="glass"] .estado-en_proceso { background:rgba(30,64,175,0.20) !important; color:#93c5fd !important; }
    body[data-ui="glass"] .estado-atendida   { background:rgba(22,101,52,0.22) !important; color:#86efac !important; }
    body[data-ui="glass"] .estado-cancelada  { background:rgba(220,38,38,0.18) !important; color:#fca5a5 !important; }
    body[data-ui="glass"] .estado-no_asistio { background:rgba(255,255,255,0.10) !important; color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .btn-gris  { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
    body[data-ui="glass"] .btn-accion { background:rgba(0,234,255,0.12) !important; color:rgba(0,234,255,0.95) !important; border:1px solid rgba(0,234,255,0.25) !important; }
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
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Reporte de Citas</h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">Análisis de citas y asistencia</p>
    </div>
</div>

{{-- Filtros --}}
<div class="filtros-card">
    <form id="form-filtros-citas" method="GET" action="{{ route('reportes.citas') }}">
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
                <label class="form-label">Estado</label>
                <select name="estado" id="filtro-estado" class="form-input">
                    <option value="">Todos los estados</option>
                    <option value="pendiente"   {{ $estado === 'pendiente'   ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmada"  {{ $estado === 'confirmada'  ? 'selected' : '' }}>Confirmada</option>
                    <option value="en_proceso"  {{ $estado === 'en_proceso'  ? 'selected' : '' }}>En proceso</option>
                    <option value="atendida"    {{ $estado === 'atendida'    ? 'selected' : '' }}>Atendida</option>
                    <option value="cancelada"   {{ $estado === 'cancelada'   ? 'selected' : '' }}>Cancelada</option>
                    <option value="no_asistio"  {{ $estado === 'no_asistio'  ? 'selected' : '' }}>No asistió</option>
                </select>
            </div>
            <div class="filtros-acciones">
                <a href="{{ route('reportes.citas') }}" class="btn-gris"><i class="bi bi-x"></i> Limpiar</a>
            </div>
        </div>
    </form>
</div>

{{-- Métricas por estado --}}
@php
    $estadoMap = ['pendiente' => 0, 'confirmada' => 0, 'atendida' => 0, 'cancelada' => 0, 'no_asistio' => 0, 'en_proceso' => 0];
    foreach($porEstado as $e) $estadoMap[$e->estado] = $e->total;
    $pctAtendidas = $totalCitas > 0 ? round(($estadoMap['atendida'] / $totalCitas) * 100) : 0;
    $pctCanceladas = $totalCitas > 0 ? round((($estadoMap['cancelada'] + $estadoMap['no_asistio']) / $totalCitas) * 100) : 0;
@endphp

<div class="stats-grid">
    <div class="metrica-reporte">
        <span class="metrica-label">Total citas</span>
        <div class="metrica-valor">{{ $totalCitas }}</div>
        <div style="font-size:.78rem;color:#6b7280;">{{ $desde->locale('es')->isoFormat('D MMM') }} — {{ $hasta->locale('es')->isoFormat('D MMM YYYY') }}</div>
    </div>
    <div class="metrica-reporte">
        <span class="metrica-label">Atendidas</span>
        <div class="metrica-valor" style="color:#166534;">{{ $estadoMap['atendida'] }}</div>
        <div style="font-size:.78rem;color:#6b7280;">{{ $pctAtendidas }}% del total</div>
    </div>
    <div class="metrica-reporte">
        <span class="metrica-label">Canceladas / No asistió</span>
        <div class="metrica-valor" style="color:#dc2626;">{{ $estadoMap['cancelada'] + $estadoMap['no_asistio'] }}</div>
        <div style="font-size:.78rem;color:#6b7280;">{{ $pctCanceladas }}% del total</div>
    </div>
    <div class="metrica-reporte">
        <span class="metrica-label">Pendientes</span>
        <div class="metrica-valor" style="color:#e65100;">{{ $estadoMap['pendiente'] + $estadoMap['confirmada'] }}</div>
        <div style="font-size:.78rem;color:#6b7280;">Por atender</div>
    </div>
</div>

{{-- Distribución por estado --}}
@if($porEstado->isNotEmpty())
<div class="panel-card">
    <div class="panel-card-header">
        <div class="panel-card-titulo"><i class="bi bi-pie-chart"></i> Distribución por estado</div>
    </div>
    @php $maxEst = $porEstado->max('total') ?: 1; @endphp
    <table class="tabla-reporte">
        <thead>
            <tr>
                <th>Estado</th>
                <th style="text-align:center;">Total</th>
                <th style="text-align:right;">%</th>
                <th style="min-width:200px;"></th>
            </tr>
        </thead>
        <tbody>
        @foreach($porEstado as $est)
        @php
            $pct = $totalCitas > 0 ? round(($est->total / $totalCitas) * 100, 1) : 0;
            $badgeClass = 'estado-' . $est->estado;
            $estadoLabel = match($est->estado) {
                'pendiente'  => 'Pendiente',
                'confirmada' => 'Confirmada',
                'en_proceso' => 'En proceso',
                'atendida'   => 'Atendida',
                'cancelada'  => 'Cancelada',
                'no_asistio' => 'No asistió',
                default      => ucfirst($est->estado)
            };
            $barColor = match($est->estado) {
                'atendida'   => '#166534',
                'cancelada'  => '#dc2626',
                'no_asistio' => '#6b7280',
                'pendiente'  => '#e65100',
                'en_proceso' => '#1e40af',
                default      => 'var(--color-principal)'
            };
        @endphp
        <tr>
            <td><span class="badge-estado {{ $badgeClass }}">{{ $estadoLabel }}</span></td>
            <td style="text-align:center;font-weight:600;">{{ $est->total }}</td>
            <td style="text-align:right;font-weight:600;color:var(--color-principal);">{{ $pct }}%</td>
            <td>
                <div class="barra-progreso-custom">
                    <div class="barra-progreso-fill" style="width:{{ $pct }}%;background:{{ $barColor }};"></div>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Listado --}}
<div class="panel-card">
    <div class="panel-card-header">
        <div class="panel-card-titulo"><i class="bi bi-calendar-range"></i> Listado de citas</div>
        <span style="font-size:.78rem;color:#9ca3af;">{{ $citas->total() }} registros</span>
    </div>
    @if($citas->isEmpty())
        <div style="padding:2rem;text-align:center;color:#9ca3af;font-size:.85rem;">
            <i class="bi bi-calendar-x" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>
            No se encontraron citas con los filtros seleccionados.
        </div>
    @else
    <div style="overflow-x:auto;">
    <table class="tabla-reporte">
        <thead>
            <tr>
                <th>N° Cita</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Paciente</th>
                <th>Procedimiento</th>
                <th>Doctor(a)</th>
                <th style="text-align:center;">Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($citas as $cita)
        @php
            $badgeClass = 'estado-' . $cita->estado;
            $estadoLabel = match($cita->estado) {
                'pendiente'  => 'Pendiente',
                'confirmada' => 'Confirmada',
                'en_proceso' => 'En proceso',
                'atendida'   => 'Atendida',
                'cancelada'  => 'Cancelada',
                'no_asistio' => 'No asistió',
                default      => ucfirst($cita->estado)
            };
        @endphp
        <tr>
            <td style="font-weight:600;color:var(--color-principal);">{{ $cita->numero_cita }}</td>
            <td style="white-space:nowrap;">{{ $cita->fecha->locale('es')->isoFormat('D MMM YYYY') }}</td>
            <td style="white-space:nowrap;font-size:.8rem;">{{ $cita->hora_inicio }}{{ $cita->hora_fin ? ' — '.$cita->hora_fin : '' }}</td>
            <td>
                <div style="font-weight:500;">{{ $cita->paciente->nombre_completo ?? '—' }}</div>
            </td>
            <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $cita->procedimiento }}">
                {{ $cita->procedimiento ?: '—' }}
            </td>
            <td>{{ $cita->doctor->name ?? '—' }}</td>
            <td style="text-align:center;"><span class="badge-estado {{ $badgeClass }}">{{ $estadoLabel }}</span></td>
            <td>
                <a href="{{ route('citas.show', $cita) }}" class="btn-accion"><i class="bi bi-eye"></i> Ver</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    <div class="pagination-wrapper">
        {{ $citas->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    var form = document.getElementById('form-filtros-citas');

    function filtrar() { form.submit(); }

    document.getElementById('filtro-desde').addEventListener('change',  filtrar);
    document.getElementById('filtro-hasta').addEventListener('change',  filtrar);
    document.getElementById('filtro-estado').addEventListener('change', filtrar);
})();
</script>
@endpush

@endsection
