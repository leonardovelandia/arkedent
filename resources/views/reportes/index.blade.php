@extends('layouts.app')
@section('titulo', 'Reportes y Estadísticas')

@push('estilos')
<style>
    .reporte-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; gap:1rem; flex-wrap:wrap; }
    .reporte-header h4 { font-family:var(--fuente-titulos); font-weight:700; color:#1c2b22; margin:0; font-size:1.5rem; }
    .reporte-header p  { font-size:.82rem; color:#9ca3af; margin:0; }

    .metricas-6 { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.25rem; }
    @media(max-width:900px){ .metricas-6{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:500px){ .metricas-6{ grid-template-columns:1fr; } }

    .metrica-reporte { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1.1rem 1.25rem; display:flex; flex-direction:column; gap:.5rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .metrica-reporte-header { display:flex; align-items:center; justify-content:space-between; }
    .metrica-valor { font-family:var(--fuente-titulos); font-size:1.6rem; font-weight:600; color:var(--color-principal); line-height:1; }
    .metrica-valor.naranja { color:#e65100; }
    .metrica-valor.rojo    { color:#DC3545; }
    .metrica-valor.verde   { color:#166534; }
    .metrica-label { font-size:.72rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; }
    .metrica-icono { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:1rem; }
    .icono-morado { background:var(--color-muy-claro); color:var(--color-principal); }
    .icono-verde  { background:#dcfce7; color:#166534; }
    .icono-naranja{ background:#fff3e0; color:#e65100; }
    .icono-azul   { background:#dbeafe; color:#1e40af; }
    .icono-rojo   { background:#fde8e8; color:#DC3545; }
    .cambio-positivo { color:#166534; font-size:.78rem; display:flex; align-items:center; gap:.2rem; }
    .cambio-negativo { color:#dc2626; font-size:.78rem; display:flex; align-items:center; gap:.2rem; }
    .cambio-neutro   { color:#8fa39a; font-size:.78rem; display:flex; align-items:center; gap:.2rem; }

    .graficas-row { display:grid; grid-template-columns:2fr 1fr; gap:1rem; margin-bottom:1.25rem; }
    @media(max-width:860px){ .graficas-row{ grid-template-columns:1fr; } }

    .panel-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .panel-card-header { padding:.85rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
    .panel-card-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:600; color:var(--color-hover); display:flex; align-items:center; gap:.45rem; }
    .panel-card-titulo i { color:var(--color-principal); }
    .panel-card-body { padding:1.25rem; }

    .tablas-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.25rem; }
    @media(max-width:860px){ .tablas-row{ grid-template-columns:1fr; } }

    .barra-progreso-custom { height:6px; background:var(--color-muy-claro); border-radius:50px; overflow:hidden; }
    .barra-progreso-fill { height:100%; background:var(--color-principal); border-radius:50px; }
    .barra-progreso-fill.rojo { background:#DC3545; }

    .tabla-reporte { width:100%; border-collapse:collapse; font-size:.82rem; }
    .tabla-reporte th { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-principal); padding:.5rem .75rem; border-bottom:2px solid var(--color-muy-claro); text-align:left; }
    .tabla-reporte td { padding:.55rem .75rem; border-bottom:1px solid var(--fondo-borde); color:#374151; vertical-align:middle; }
    .tabla-reporte tr:last-child td { border-bottom:none; }
    .tabla-reporte tr:hover td { background:var(--fondo-card-alt); }

    .badge-metodo { display:inline-block; font-size:.72rem; font-weight:600; padding:.2rem .6rem; border-radius:50px; }
    .badge-efectivo    { background:#dcfce7; color:#166534; }
    .badge-transferencia { background:#dbeafe; color:#1e40af; }
    .badge-tarjeta     { background:var(--color-muy-claro); color:var(--color-principal); }
    .badge-otro        { background:#f3f4f6; color:#374151; }

    .accesos-rapidos { display:flex; gap:.75rem; flex-wrap:wrap; }
    .btn-reporte { display:inline-flex; align-items:center; gap:.4rem; background:#fff; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.55rem 1rem; font-size:.82rem; font-weight:500; color:var(--color-principal); text-decoration:none; transition:all .15s; }
    .btn-reporte:hover { background:var(--color-muy-claro); border-color:var(--color-principal); color:var(--color-principal); }
    .btn-reporte.rojo { color:#DC3545; border-color:#fecdd3; }
    .btn-reporte.rojo:hover { background:#fef2f2; border-color:#DC3545; }
    .btn-reporte i { font-size:.95rem; }

    .periodo-selector { display:flex; gap:.25rem; background:var(--fondo-card-alt); border:1px solid var(--fondo-borde); border-radius:10px; padding:.25rem; }
    .periodo-btn { border:none; background:none; border-radius:7px; padding:.35rem .85rem; font-size:.8rem; font-weight:600; color:#6b7280; cursor:pointer; transition:all .15s; }
    .periodo-btn.activo { background:#fff; color:var(--color-principal); box-shadow:0 1px 4px rgba(0,0,0,.1); }
    .periodo-btn:hover:not(.activo) { color:var(--color-principal); }
</style>
@endpush

@section('contenido')

<div class="reporte-header">
    <div>
        <h4><i class="bi bi-graph-up" style="color:#1c2b22;margin-right:.8rem;"></i>Reportes y Estadísticas</h4>
        <p>{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
    </div>
    <div class="accesos-rapidos">
        <a href="{{ route('reportes.ingresos') }}" class="btn-reporte" style="box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);"><i class="bi bi-cash-coin"></i> Ingresos</a>
        <a href="{{ route('reportes.egresos') }}"  class="btn-reporte rojo" style="box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);"><i class="bi bi-arrow-down-circle"></i> Egresos</a>
        <a href="{{ route('reportes.pacientes') }}" class="btn-reporte" style="box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);"><i class="bi bi-people"></i> Pacientes</a>
        <a href="{{ route('reportes.citas') }}"    class="btn-reporte" style="box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);"><i class="bi bi-calendar-check"></i> Citas</a>
    </div>
</div>

{{-- Métricas principales --}}
@php
    $cambioIngresos = $ingresosMesAnterior > 0
        ? round((($ingresosMes - $ingresosMesAnterior) / $ingresosMesAnterior) * 100, 1)
        : ($ingresosMes > 0 ? 100 : 0);
    $cambioPacientes = $pacientesNuevosMesAnterior > 0
        ? round((($pacientesNuevosMes - $pacientesNuevosMesAnterior) / $pacientesNuevosMesAnterior) * 100, 1)
        : ($pacientesNuevosMes > 0 ? 100 : 0);
    $porcentajeAtendidas = $citasMes > 0 ? round(($citasAtendidas / $citasMes) * 100) : 0;
@endphp

<div class="metricas-6">
    <div class="metrica-reporte">
        <div class="metrica-reporte-header">
            <span class="metrica-label">Ingresos del Mes</span>
            <div class="metrica-icono icono-morado"><i class="bi bi-cash-coin"></i></div>
        </div>
        <div class="metrica-valor">${{ number_format($ingresosMes, 0, ',', '.') }}</div>
        <div class="{{ $cambioIngresos >= 0 ? 'cambio-positivo' : 'cambio-negativo' }}">
            <i class="bi bi-arrow-{{ $cambioIngresos >= 0 ? 'up' : 'down' }}-short"></i>
            {{ abs($cambioIngresos) }}% vs mes anterior
        </div>
    </div>
    <div class="metrica-reporte">
        <div class="metrica-reporte-header">
            <span class="metrica-label">Ingresos del Año</span>
            <div class="metrica-icono icono-verde"><i class="bi bi-graph-up-arrow"></i></div>
        </div>
        <div class="metrica-valor">${{ number_format($ingresosAno, 0, ',', '.') }}</div>
        <div class="cambio-neutro"><i class="bi bi-dot"></i> Acumulado {{ now()->year }}</div>
    </div>
    <div class="metrica-reporte">
        <div class="metrica-reporte-header">
            <span class="metrica-label">Pacientes Nuevos</span>
            <div class="metrica-icono icono-azul"><i class="bi bi-person-plus"></i></div>
        </div>
        <div class="metrica-valor">{{ $pacientesNuevosMes }}</div>
        <div class="{{ $cambioPacientes >= 0 ? 'cambio-positivo' : 'cambio-negativo' }}">
            <i class="bi bi-arrow-{{ $cambioPacientes >= 0 ? 'up' : 'down' }}-short"></i>
            {{ abs($cambioPacientes) }}% vs mes anterior
        </div>
    </div>
    <div class="metrica-reporte">
        <div class="metrica-reporte-header">
            <span class="metrica-label">Total Pacientes</span>
            <div class="metrica-icono icono-morado"><i class="bi bi-people"></i></div>
        </div>
        <div class="metrica-valor">{{ $totalPacientes }}</div>
        <div class="cambio-neutro"><i class="bi bi-dot"></i> Pacientes activos</div>
    </div>
    <div class="metrica-reporte">
        <div class="metrica-reporte-header">
            <span class="metrica-label">Citas del Mes</span>
            <div class="metrica-icono icono-azul"><i class="bi bi-calendar-check"></i></div>
        </div>
        <div class="metrica-valor">{{ $citasMes }}</div>
        <div class="cambio-positivo"><i class="bi bi-check-circle"></i> {{ $porcentajeAtendidas }}% atendidas</div>
    </div>
    <div class="metrica-reporte">
        <div class="metrica-reporte-header">
            <span class="metrica-label">Saldo Pendiente</span>
            <div class="metrica-icono icono-naranja"><i class="bi bi-clock-history"></i></div>
        </div>
        <div class="metrica-valor naranja">${{ number_format($saldoPendienteTotal, 0, ',', '.') }}</div>
        <div class="cambio-neutro"><i class="bi bi-dot"></i> Por cobrar</div>
    </div>
</div>

{{-- Cards Egresos y Utilidad --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
    <div class="metrica-reporte">
        <div class="metrica-reporte-header">
            <span class="metrica-label">Egresos del Mes</span>
            <div class="metrica-icono icono-rojo"><i class="bi bi-arrow-down-circle"></i></div>
        </div>
        <div class="metrica-valor rojo">${{ number_format($egresosMes, 0, ',', '.') }}</div>
        <div class="cambio-neutro"><i class="bi bi-dot"></i> Gastos del mes actual</div>
    </div>
    <div class="metrica-reporte">
        <div class="metrica-reporte-header">
            <span class="metrica-label">Utilidad Neta</span>
            <div class="metrica-icono" style="background:{{ $utilidadNeta >= 0 ? '#dcfce7' : '#fde8e8' }};color:{{ $utilidadNeta >= 0 ? '#166534' : '#DC3545' }};width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:1rem;">
                <i class="bi bi-{{ $utilidadNeta >= 0 ? 'graph-up-arrow' : 'graph-down-arrow' }}"></i>
            </div>
        </div>
        <div class="metrica-valor {{ $utilidadNeta >= 0 ? 'verde' : 'rojo' }}">
            {{ $utilidadNeta < 0 ? '-' : '' }}${{ number_format(abs($utilidadNeta), 0, ',', '.') }}
        </div>
        <div class="{{ $utilidadNeta >= 0 ? 'cambio-positivo' : 'cambio-negativo' }}">
            <i class="bi bi-arrow-{{ $utilidadNeta >= 0 ? 'up' : 'down' }}-short"></i> Ingresos − Egresos
        </div>
    </div>
</div>

{{-- Selector de período --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.85rem;flex-wrap:wrap;gap:.5rem;">
    <div style="font-size:.78rem;font-weight:700;color:var(--color-hover);text-transform:uppercase;letter-spacing:.05em;">
        <i class="bi bi-bar-chart-line" style="color:var(--color-principal);"></i> Gráficas comparativas
    </div>
    <div class="periodo-selector">
        <button class="periodo-btn activo" data-periodo="dia">Día</button>
        <button class="periodo-btn" data-periodo="mes">Mes</button>
        <button class="periodo-btn" data-periodo="ano">Año</button>
    </div>
</div>

{{-- Gráficas Ingresos --}}
<div class="graficas-row" style="margin-bottom:.75rem;">
    <div class="panel-card">
        <div class="panel-card-header">
            <div class="panel-card-titulo"><i class="bi bi-bar-chart-line"></i> <span id="titulo-grafIngresos">Ingresos — últimos 12 meses</span></div>
        </div>
        <div class="panel-card-body" style="position:relative;height:260px;">
            <canvas id="graficaIngresos"></canvas>
        </div>
    </div>
    <div class="panel-card">
        <div class="panel-card-header">
            <div class="panel-card-titulo"><i class="bi bi-pie-chart"></i> Citas por estado</div>
        </div>
        <div class="panel-card-body" style="position:relative;height:260px;display:flex;align-items:center;justify-content:center;">
            @if($citasPorEstado->isEmpty())
                <div style="text-align:center;color:#9ca3af;font-size:.82rem;">
                    <i class="bi bi-calendar-x" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>Sin citas este mes
                </div>
            @else
                <canvas id="graficaCitas"></canvas>
            @endif
        </div>
    </div>
</div>

{{-- Gráficas Egresos --}}
<div class="graficas-row" style="margin-bottom:1.25rem;">
    <div class="panel-card">
        <div class="panel-card-header">
            <div class="panel-card-titulo"><i class="bi bi-bar-chart-line" style="color:#DC3545;"></i> <span id="titulo-grafEgresos">Egresos — últimos 12 meses</span></div>
        </div>
        <div class="panel-card-body" style="position:relative;height:260px;">
            <canvas id="graficaEgresos"></canvas>
        </div>
    </div>
    <div class="panel-card">
        <div class="panel-card-header">
            <div class="panel-card-titulo"><i class="bi bi-pie-chart" style="color:#DC3545;"></i> Distribución de egresos</div>
        </div>
        <div class="panel-card-body" style="position:relative;height:260px;display:flex;align-items:center;justify-content:center;">
            @if($egresosPorCategoria->isEmpty())
                <div style="text-align:center;color:#9ca3af;font-size:.82rem;">
                    <i class="bi bi-cart-x" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>Sin egresos este mes
                </div>
            @else
                <canvas id="graficaEgresosCats"></canvas>
            @endif
        </div>
    </div>
</div>

{{-- Tablas secundarias --}}
<div class="tablas-row">
    <div class="panel-card">
        <div class="panel-card-header">
            <div class="panel-card-titulo"><i class="bi bi-list-ol"></i> Top 5 procedimientos</div>
        </div>
        <div class="panel-card-body" style="padding:0;">
            @if($topProcedimientos->isEmpty())
                <div style="padding:1.5rem;text-align:center;color:#9ca3af;font-size:.82rem;">Sin datos registrados</div>
            @else
            @php $maxProc = $topProcedimientos->max('total') ?: 1; @endphp
            <table class="tabla-reporte">
                <thead><tr><th>Procedimiento</th><th style="text-align:right;">Total</th></tr></thead>
                <tbody>
                @foreach($topProcedimientos as $proc)
                <tr>
                    <td>
                        <div style="font-weight:500;font-size:.82rem;margin-bottom:.25rem;">{{ $proc->procedimiento }}</div>
                        <div class="barra-progreso-custom">
                            <div class="barra-progreso-fill" style="width:{{ round(($proc->total / $maxProc) * 100) }}%;"></div>
                        </div>
                    </td>
                    <td style="text-align:right;font-weight:600;color:var(--color-principal);">{{ $proc->total }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
    <div class="panel-card">
        <div class="panel-card-header">
            <div class="panel-card-titulo"><i class="bi bi-graph-up"></i> Pacientes nuevos por mes</div>
        </div>
        <div class="panel-card-body" style="position:relative;height:220px;">
            <canvas id="graficaPacientes"></canvas>
        </div>
    </div>
</div>

{{-- Métodos de pago --}}
<div class="panel-card" style="margin-bottom:1.25rem;">
    <div class="panel-card-header">
        <div class="panel-card-titulo"><i class="bi bi-credit-card"></i> Métodos de pago</div>
    </div>
    <div class="panel-card-body" style="padding:0;">
        @if($topMetodosPago->isEmpty())
            <div style="padding:1.5rem;text-align:center;color:#9ca3af;font-size:.82rem;">Sin pagos registrados</div>
        @else
        @php $totalPagos = $topMetodosPago->sum('suma') ?: 1; @endphp
        <table class="tabla-reporte">
            <thead><tr><th>Método</th><th style="text-align:center;">Transacciones</th><th style="text-align:right;">Total</th><th style="text-align:right;">%</th><th style="min-width:120px;"></th></tr></thead>
            <tbody>
            @foreach($topMetodosPago as $mp)
            @php
                $pct = round(($mp->suma / $totalPagos) * 100, 1);
                $badgeClass = match($mp->metodo_pago) { 'efectivo' => 'badge-efectivo', 'transferencia' => 'badge-transferencia', 'tarjeta' => 'badge-tarjeta', default => 'badge-otro' };
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
        @endif
    </div>
</div>

{{-- Egresos por categoría --}}
@if($egresosPorCategoria->isNotEmpty())
<div class="panel-card" style="margin-bottom:1.25rem;">
    <div class="panel-card-header">
        <div class="panel-card-titulo"><i class="bi bi-list-check" style="color:#DC3545;"></i> Egresos por categoría — {{ now()->locale('es')->isoFormat('MMMM YYYY') }}</div>
    </div>
    <div class="panel-card-body" style="padding:0;">
        @php $totalEgresos = $egresosPorCategoria->sum('egresos_sum_valor') ?: 1; @endphp
        <table class="tabla-reporte">
            <thead><tr><th>Categoría</th><th style="text-align:right;">Total</th><th style="text-align:right;">%</th><th style="min-width:120px;"></th></tr></thead>
            <tbody>
            @foreach($egresosPorCategoria->sortByDesc('egresos_sum_valor') as $cat)
            @php $pctCat = round(($cat->egresos_sum_valor / $totalEgresos) * 100, 1); @endphp
            <tr>
                <td>
                    <span style="display:inline-flex;align-items:center;gap:.35rem;font-size:.78rem;font-weight:600;">
                        @if($cat->icono)<i class="{{ $cat->icono }}" style="color:{{ $cat->color }};"></i>@endif
                        {{ $cat->nombre }}
                    </span>
                </td>
                <td style="text-align:right;font-weight:700;color:#DC3545;">${{ number_format($cat->egresos_sum_valor, 0, ',', '.') }}</td>
                <td style="text-align:right;font-weight:600;color:#6C757D;">{{ $pctCat }}%</td>
                <td>
                    <div style="height:6px;background:#f3f4f6;border-radius:50px;overflow:hidden;">
                        <div style="height:100%;width:{{ $pctCat }}%;background:{{ $cat->color ?? '#DC3545' }};border-radius:50px;"></div>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const citasPorEstado = @json($citasPorEstado);
    const pacientesPorMes = @json($pacientesPorMes);
    const egresosCats = @json($egresosPorCategoria->sortByDesc('egresos_sum_valor')->values());
    const urlDatos = '{{ route('reportes.datos-graficas') }}';

    function temaColor(v) { return getComputedStyle(document.body).getPropertyValue(v).trim() || '#6B21A8'; }

    // ── Citas donut ──────────────────────────────────────────────
    const ctxCitas = document.getElementById('graficaCitas');
    if (ctxCitas && citasPorEstado.length > 0) {
        const col = { pendiente:'#FFC107', confirmada:temaColor('--color-principal'), en_proceso:'#17A2B8', atendida:'#28A745', cancelada:'#DC3545', no_asistio:'#6C757D' };
        new Chart(ctxCitas, { type:'doughnut', data:{ labels:citasPorEstado.map(c=>c.estado.replace('_',' ')), datasets:[{ data:citasPorEstado.map(c=>c.total), backgroundColor:citasPorEstado.map(c=>col[c.estado]||'#9ca3af'), borderWidth:2, borderColor:'#fff' }] }, options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom', labels:{ font:{size:11}, padding:10 } } } } });
    }

    // ── Pacientes línea ──────────────────────────────────────────
    const ctxPac = document.getElementById('graficaPacientes');
    if (ctxPac) {
        new Chart(ctxPac, { type:'line', data:{ labels:pacientesPorMes.map(p=>p.mes), datasets:[{ label:'Pacientes nuevos', data:pacientesPorMes.map(p=>p.total), borderColor:temaColor('--color-principal'), backgroundColor:temaColor('--sombra-principal'), tension:0.4, fill:true, pointBackgroundColor:temaColor('--color-principal'), pointRadius:4 }] }, options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{ y:{beginAtZero:true,ticks:{stepSize:1,font:{size:11}},grid:{color:'var(--fondo-borde)'}}, x:{ticks:{font:{size:11}},grid:{display:false}} } } });
    }

    // ── Egresos categorías donut ─────────────────────────────────
    const ctxEgCat = document.getElementById('graficaEgresosCats');
    if (ctxEgCat && egresosCats.length > 0) {
        const totalEg = egresosCats.reduce((a,c)=>a+c.egresos_sum_valor,0)||1;
        new Chart(ctxEgCat, { type:'doughnut', data:{ labels:egresosCats.map(c=>c.nombre), datasets:[{ data:egresosCats.map(c=>c.egresos_sum_valor), backgroundColor:egresosCats.map(c=>c.color||'#ADB5BD'), borderWidth:2, borderColor:'#fff' }] }, options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{position:'bottom',labels:{font:{size:10},padding:8}}, tooltip:{ callbacks:{ label:ctx=>' $ '+ctx.parsed.toLocaleString('es-CO')+' ('+Math.round(ctx.parsed/totalEg*100)+'%)' } } } } });
    }

    // ── Ingresos y Egresos barras (dinámicos) ────────────────────
    let chartIngresos = null, chartEgresos = null;

    function crearGraficaBarra(ctx, datos, color, label) {
        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: datos.map(d => d.label),
                datasets: [{ label, data: datos.map(d => d.valor), backgroundColor: color, borderRadius: 6, borderSkipped: false }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => '$ '+v.toLocaleString('es-CO'), font:{size:11} }, grid: { color:'var(--fondo-borde)' } },
                    x: { ticks: { font:{size:10} }, grid: { display:false } }
                }
            }
        });
    }

    const titulos = {
        dia:  ['Ingresos — últimos 30 días', 'Egresos — últimos 30 días'],
        mes:  ['Ingresos — últimos 12 meses', 'Egresos — últimos 12 meses'],
        ano:  ['Ingresos — últimos 5 años', 'Egresos — últimos 5 años'],
    };

    function cargarDatos(periodo) {
        fetch(urlDatos + '?periodo=' + periodo)
            .then(r => r.json())
            .then(data => {
                // ingresos
                if (chartIngresos) chartIngresos.destroy();
                const ctxI = document.getElementById('graficaIngresos');
                if (ctxI) chartIngresos = crearGraficaBarra(ctxI, data.ingresos, temaColor('--color-principal'), 'Ingresos');

                // egresos
                if (chartEgresos) chartEgresos.destroy();
                const ctxE = document.getElementById('graficaEgresos');
                if (ctxE) chartEgresos = crearGraficaBarra(ctxE, data.egresos, '#DC3545', 'Egresos');

                // titulos
                document.getElementById('titulo-grafIngresos').textContent = titulos[periodo][0];
                document.getElementById('titulo-grafEgresos').textContent  = titulos[periodo][1];
            });
    }

    // Botones periodo
    document.querySelectorAll('.periodo-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.periodo-btn').forEach(b => b.classList.remove('activo'));
            this.classList.add('activo');
            cargarDatos(this.dataset.periodo);
        });
    });

    // Cargar por defecto (dia)
    cargarDatos('dia');
})();
</script>
@endpush
