@extends('layouts.app')
@section('titulo', 'Reportes y Estadísticas')

@push('estilos')
<style>
    .reporte-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; gap:1rem; flex-wrap:wrap; }
    .reporte-header h4 { font-family:var(--fuente-titulos); font-weight:700; color:#1c2b22; margin:0; font-size:1.5rem; }
    .reporte-header p  { font-size:.82rem; color:#9ca3af; margin:0; }

    .metricas-6 { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.25rem; }
    @media(max-width:900px){ .metricas-6{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:500px){ .metricas-6{ grid-template-columns:1fr; } }

    .metrica-reporte { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1.1rem 1.25rem; display:flex; flex-direction:column; gap:.5rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .metrica-reporte-header { display:flex; align-items:center; justify-content:space-between; }
    .metrica-valor { font-family:var(--fuente-titulos); font-size:1.6rem; font-weight:600; color:var(--color-principal); line-height:1; }
    .metrica-valor.naranja { color:#e65100; }
    .metrica-label { font-size:.72rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; }
    .metrica-icono { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:1rem; }
    .icono-morado { background:var(--color-muy-claro); color:var(--color-principal); }
    .icono-verde  { background:#dcfce7; color:#166534; }
    .icono-naranja{ background:#fff3e0; color:#e65100; }
    .icono-azul   { background:#dbeafe; color:#1e40af; }
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

    .accesos-rapidos { display:flex; gap:.75rem; flex-wrap:wrap; margin-bottom:1.25rem; }
    .btn-reporte { display:inline-flex; align-items:center; gap:.4rem; background:#fff; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.55rem 1rem; font-size:.82rem; font-weight:500; color:var(--color-principal); text-decoration:none; transition:all .15s; }
    .btn-reporte:hover { background:var(--color-muy-claro); border-color:var(--color-principal); color:var(--color-principal); }
    .btn-reporte i { font-size:.95rem; }
</style>
@endpush

@section('contenido')

<div class="reporte-header">
    <div>
        <h4><i class="bi bi-graph-up" style="color:#1c2b22;margin-right:.8rem;"></i>Reportes y Estadísticas</h4>
        <p>{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
    </div>
    <div class="accesos-rapidos" style="margin-bottom:0;">
        <a href="{{ route('reportes.ingresos') }}" class="btn-reporte"><i class="bi bi-cash-coin"></i> Ingresos</a>
        <a href="{{ route('reportes.pacientes') }}" class="btn-reporte"><i class="bi bi-people"></i> Pacientes</a>
        <a href="{{ route('reportes.citas') }}"    class="btn-reporte"><i class="bi bi-calendar-check"></i> Citas</a>
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
        <div class="cambio-neutro"><i class="bi bi-dot"></i> Por cobrar a pacientes</div>
    </div>

</div>

{{-- Gráficas principales --}}
<div class="graficas-row">

    <div class="panel-card">
        <div class="panel-card-header">
            <div class="panel-card-titulo"><i class="bi bi-bar-chart-line"></i> Ingresos últimos 12 meses</div>
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
                    <i class="bi bi-calendar-x" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>
                    Sin citas este mes
                </div>
            @else
                <canvas id="graficaCitas"></canvas>
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
            <thead>
                <tr>
                    <th>Método</th>
                    <th style="text-align:center;">Transacciones</th>
                    <th style="text-align:right;">Total</th>
                    <th style="text-align:right;">%</th>
                    <th style="min-width:120px;"></th>
                </tr>
            </thead>
            <tbody>
            @foreach($topMetodosPago as $mp)
            @php
                $pct = round(($mp->suma / $totalPagos) * 100, 1);
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
                <td>
                    <div class="barra-progreso-custom">
                        <div class="barra-progreso-fill" style="width:{{ $pct }}%;"></div>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ingresosPorMes = @json($ingresosPorMes);
const citasPorEstado = @json($citasPorEstado);
const pacientesPorMes = @json($pacientesPorMes);

// Helper: leer colores del tema activo para Chart.js
function temaColor(varName) {
    return getComputedStyle(document.body).getPropertyValue(varName).trim() || '#6B21A8';
}

// Grafica de barras — ingresos
const ctxIngresos = document.getElementById('graficaIngresos');
if (ctxIngresos) {
    new Chart(ctxIngresos.getContext('2d'), {
        type: 'bar',
        data: {
            labels: ingresosPorMes.map(i => i.mes),
            datasets: [{
                label: 'Ingresos',
                data: ingresosPorMes.map(i => i.valor),
                backgroundColor: temaColor('--color-principal'),
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: v => '$ ' + v.toLocaleString('es-CO'), font: { size: 11 } },
                    grid: { color: 'var(--fondo-borde)' }
                },
                x: { ticks: { font: { size: 10 } }, grid: { display: false } }
            }
        }
    });
}

// Grafica dona — citas por estado
const ctxCitas = document.getElementById('graficaCitas');
if (ctxCitas && citasPorEstado.length > 0) {
    const coloresEstado = {
        pendiente:   '#FFC107',
        confirmada:  temaColor('--color-principal'),
        en_proceso:  '#17A2B8',
        atendida:    '#28A745',
        cancelada:   '#DC3545',
        no_asistio:  '#6C757D',
    };
    new Chart(ctxCitas.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: citasPorEstado.map(c => c.estado.replace('_', ' ')),
            datasets: [{
                data: citasPorEstado.map(c => c.total),
                backgroundColor: citasPorEstado.map(c => coloresEstado[c.estado] || '#9ca3af'),
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 10 } }
            }
        }
    });
}

// Grafica linea — pacientes por mes
const ctxPacientes = document.getElementById('graficaPacientes');
if (ctxPacientes) {
    new Chart(ctxPacientes.getContext('2d'), {
        type: 'line',
        data: {
            labels: pacientesPorMes.map(p => p.mes),
            datasets: [{
                label: 'Pacientes nuevos',
                data: pacientesPorMes.map(p => p.total),
                borderColor: temaColor('--color-principal'),
                backgroundColor: temaColor('--sombra-principal'),
                tension: 0.4,
                fill: true,
                pointBackgroundColor: temaColor('--color-principal'),
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: 'var(--fondo-borde)' } },
                x: { ticks: { font: { size: 11 } }, grid: { display: false } }
            }
        }
    });
}
</script>
@endpush
