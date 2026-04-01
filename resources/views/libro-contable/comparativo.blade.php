@extends('layouts.app')

@section('titulo', 'Comparativo 12 Meses')

@section('contenido')

{{-- Tabs --}}
<div style="display:flex; gap:0.5rem; margin-bottom:1.5rem; flex-wrap:wrap;">
    <a href="{{ route('libro-contable.index') }}"
       style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.45rem 1rem; border-radius:8px; font-size:0.85rem; font-weight:600; text-decoration:none;
              background:var(--color-muy-claro); color:var(--color-principal);">
        <i class="bi bi-journal-text"></i> Libro de Caja
    </a>
    <a href="{{ route('libro-contable.estado-resultados') }}"
       style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.45rem 1rem; border-radius:8px; font-size:0.85rem; font-weight:600; text-decoration:none;
              background:var(--color-muy-claro); color:var(--color-principal);">
        <i class="bi bi-bar-chart-line"></i> Estado de Resultados
    </a>
    <a href="{{ route('libro-contable.comparativo') }}"
       style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.45rem 1rem; border-radius:8px; font-size:0.85rem; font-weight:600; text-decoration:none;
              background:var(--color-principal); color:white;">
        <i class="bi bi-graph-up"></i> Comparativo 12 Meses
    </a>
</div>

{{-- Cards resumen --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1rem; margin-bottom:1.5rem;">

    <div style="background:white; border-radius:12px; padding:1.1rem 1.25rem; box-shadow:0 1px 4px rgba(0,0,0,.07); border-left:4px solid #28a745;">
        <p style="font-size:.72rem; font-weight:600; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .2rem;">Mejor Mes</p>
        <p style="font-size:.95rem; font-weight:700; color:#28a745; margin:0 0 .1rem;">{{ $mejorMes['mes'] }}</p>
        <p style="font-size:.82rem; color:#155724; margin:0;">$ {{ number_format($mejorMes['utilidad'], 0, ',', '.') }}</p>
    </div>

    <div style="background:white; border-radius:12px; padding:1.1rem 1.25rem; box-shadow:0 1px 4px rgba(0,0,0,.07); border-left:4px solid #DC3545;">
        <p style="font-size:.72rem; font-weight:600; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .2rem;">Peor Mes</p>
        <p style="font-size:.95rem; font-weight:700; color:#DC3545; margin:0 0 .1rem;">{{ $peorMes['mes'] }}</p>
        <p style="font-size:.82rem; color:#721c24; margin:0;">$ {{ number_format($peorMes['utilidad'], 0, ',', '.') }}</p>
    </div>

    <div style="background:white; border-radius:12px; padding:1.1rem 1.25rem; box-shadow:0 1px 4px rgba(0,0,0,.07); border-left:4px solid #0d6efd;">
        <p style="font-size:.72rem; font-weight:600; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .2rem;">Promedio Ingresos</p>
        <p style="font-size:1.1rem; font-weight:700; color:#0d6efd; margin:0;">$ {{ number_format($promedioIngresos, 0, ',', '.') }}</p>
    </div>

    <div style="background:white; border-radius:12px; padding:1.1rem 1.25rem; box-shadow:0 1px 4px rgba(0,0,0,.07); border-left:4px solid #fd7e14;">
        <p style="font-size:.72rem; font-weight:600; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .2rem;">Promedio Egresos</p>
        <p style="font-size:1.1rem; font-weight:700; color:#fd7e14; margin:0;">$ {{ number_format($promedioEgresos, 0, ',', '.') }}</p>
    </div>

</div>

{{-- Gráfica --}}
<div style="background:white; border-radius:12px; padding:1.5rem; box-shadow:0 1px 4px rgba(0,0,0,.07); margin-bottom:1.25rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap:wrap; gap:.5rem;">
        <h6 style="font-family:var(--fuente-titulos); color:var(--color-principal); margin:0;">
            <i class="bi bi-bar-chart-grouped"></i> Ingresos, Egresos y Utilidad — Últimos 12 Meses
        </h6>
        <a href="{{ route('libro-contable.exportar', ['desde' => now()->subMonths(11)->startOfMonth()->format('Y-m-d'), 'hasta' => now()->endOfMonth()->format('Y-m-d')]) }}"
           style="background:#28a745; color:white; border-radius:8px; padding:.35rem .85rem; font-size:.78rem; font-weight:600; text-decoration:none;">
            <i class="bi bi-download"></i> Exportar CSV
        </a>
    </div>
    <canvas id="graficaComparativo" style="max-height:320px;"></canvas>
</div>

{{-- Tabla detalle --}}
<div style="background:white; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.07); overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:.85rem;">
            <thead>
                <tr style="background:var(--color-muy-claro);">
                    <th style="padding:.6rem .9rem; text-align:left; font-size:.72rem; font-weight:600; color:var(--color-principal); text-transform:uppercase; letter-spacing:.06em;">Mes</th>
                    <th style="padding:.6rem .9rem; text-align:right; font-size:.72rem; font-weight:600; color:#28a745; text-transform:uppercase; letter-spacing:.06em;">Ingresos</th>
                    <th style="padding:.6rem .9rem; text-align:right; font-size:.72rem; font-weight:600; color:#DC3545; text-transform:uppercase; letter-spacing:.06em;">Egresos</th>
                    <th style="padding:.6rem .9rem; text-align:right; font-size:.72rem; font-weight:600; color:var(--color-principal); text-transform:uppercase; letter-spacing:.06em;">Utilidad</th>
                    <th style="padding:.6rem .9rem; text-align:right; font-size:.72rem; font-weight:600; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em;">Margen %</th>
                    <th style="padding:.6rem .9rem; text-align:right; font-size:.72rem; font-weight:600; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em;">Var. Utilidad</th>
                </tr>
            </thead>
            <tbody>
                @php $prevUtilidad = null; @endphp
                @foreach($meses as $i => $mes)
                @php
                    $margen = $mes['ingresos'] > 0 ? ($mes['utilidad'] / $mes['ingresos']) * 100 : 0;
                    $varUtilidad = ($prevUtilidad !== null && $prevUtilidad != 0)
                        ? (($mes['utilidad'] - $prevUtilidad) / abs($prevUtilidad)) * 100
                        : null;
                    $esMesActual = $i === count($meses) - 1;
                    $prevUtilidad = $mes['utilidad'];
                @endphp
                <tr style="border-bottom:1px solid #f0ebff; {{ $esMesActual ? 'background:#f5f0ff;' : '' }}">
                    <td style="padding:.5rem .9rem; font-weight:{{ $esMesActual ? '700' : '400' }}; color:{{ $esMesActual ? 'var(--color-principal)' : 'inherit' }};">
                        {{ $mes['mes'] }}
                        @if($esMesActual)
                            <span style="font-size:.65rem; background:var(--color-principal); color:white; padding:1px 6px; border-radius:50px; margin-left:4px;">Actual</span>
                        @endif
                    </td>
                    <td style="padding:.5rem .9rem; text-align:right; color:#155724; font-weight:500; white-space:nowrap;">$ {{ number_format($mes['ingresos'], 0, ',', '.') }}</td>
                    <td style="padding:.5rem .9rem; text-align:right; color:#721c24; font-weight:500; white-space:nowrap;">$ {{ number_format($mes['egresos'], 0, ',', '.') }}</td>
                    <td style="padding:.5rem .9rem; text-align:right; font-weight:600; white-space:nowrap;
                        color:{{ $mes['utilidad'] >= 0 ? '#155724' : '#721c24' }};">
                        {{ $mes['utilidad'] < 0 ? '-' : '' }}$ {{ number_format(abs($mes['utilidad']), 0, ',', '.') }}
                    </td>
                    <td style="padding:.5rem .9rem; text-align:right; font-size:.82rem; color:{{ $margen >= 0 ? '#155724' : '#721c24' }};">
                        {{ number_format($margen, 1) }}%
                    </td>
                    <td style="padding:.5rem .9rem; text-align:right; font-size:.82rem;">
                        @if($varUtilidad !== null)
                            <span style="color:{{ $varUtilidad >= 0 ? '#28a745' : '#DC3545' }}; font-weight:600;">
                                <i class="bi bi-arrow-{{ $varUtilidad >= 0 ? 'up' : 'down' }}-short"></i>
                                {{ number_format(abs($varUtilidad), 1) }}%
                            </span>
                        @else
                            <span style="color:#8fa39a;">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const mesesLabels = {!! json_encode($meses->pluck('mes')->toArray()) !!};
const mesesIngresos = {!! json_encode($meses->pluck('ingresos')->toArray()) !!};
const mesesEgresos  = {!! json_encode($meses->pluck('egresos')->toArray()) !!};
const mesesUtilidad = {!! json_encode($meses->pluck('utilidad')->toArray()) !!};

new Chart(document.getElementById('graficaComparativo'), {
    data: {
        labels: mesesLabels,
        datasets: [
            {
                type: 'bar',
                label: 'Ingresos',
                data: mesesIngresos,
                backgroundColor: 'rgba(40,167,69,.7)',
                borderColor: '#28a745',
                borderWidth: 1,
                order: 2,
            },
            {
                type: 'bar',
                label: 'Egresos',
                data: mesesEgresos,
                backgroundColor: 'rgba(220,53,69,.7)',
                borderColor: '#DC3545',
                borderWidth: 1,
                order: 3,
            },
            {
                type: 'line',
                label: 'Utilidad Neta',
                data: mesesUtilidad,
                borderColor: '#6f42c1',
                backgroundColor: 'rgba(111,66,193,.15)',
                borderWidth: 2.5,
                pointRadius: 4,
                pointBackgroundColor: '#6f42c1',
                fill: false,
                tension: 0.3,
                order: 1,
            },
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'top', labels: { font: { size: 12 }, boxWidth: 14 } },
            tooltip: {
                callbacks: {
                    label: ctx => `${ctx.dataset.label}: $ ${Number(ctx.raw).toLocaleString('es-CO')}`
                }
            }
        },
        scales: {
            y: {
                ticks: {
                    callback: val => '$ ' + Number(val).toLocaleString('es-CO')
                }
            }
        }
    }
});
</script>
@endpush
