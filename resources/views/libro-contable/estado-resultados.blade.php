@extends('layouts.app')

@section('titulo', 'Estado de Resultados')

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
              background:var(--color-principal); color:white;">
        <i class="bi bi-bar-chart-line"></i> Estado de Resultados
    </a>
    <a href="{{ route('libro-contable.comparativo') }}"
       style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.45rem 1rem; border-radius:8px; font-size:0.85rem; font-weight:600; text-decoration:none;
              background:var(--color-muy-claro); color:var(--color-principal);">
        <i class="bi bi-graph-up"></i> Comparativo 12 Meses
    </a>
</div>

{{-- Selector mes/año --}}
<div style="background:white; border-radius:12px; padding:1.1rem 1.25rem; box-shadow:0 1px 4px rgba(0,0,0,.07); margin-bottom:1.25rem;">
    <form method="GET" action="{{ route('libro-contable.estado-resultados') }}"
          style="display:flex; flex-wrap:wrap; gap:.75rem; align-items:flex-end;">
        <div style="display:flex; flex-direction:column; gap:.25rem;">
            <label style="font-size:.75rem; font-weight:600; color:var(--color-principal);">Mes</label>
            <select name="mes" style="border:1.5px solid #e0d9f7; border-radius:8px; padding:.4rem .75rem; font-size:.85rem;">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromDate(2000,$m,1)->locale('es')->isoFormat('MMMM') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div style="display:flex; flex-direction:column; gap:.25rem;">
            <label style="font-size:.75rem; font-weight:600; color:var(--color-principal);">Año</label>
            <select name="ano" style="border:1.5px solid #e0d9f7; border-radius:8px; padding:.4rem .75rem; font-size:.85rem;">
                @foreach(range(now()->year, now()->year - 4, -1) as $y)
                    <option value="{{ $y }}" {{ $ano == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                style="background:var(--color-principal); color:white; border:none; border-radius:8px; padding:.45rem 1rem; font-size:.85rem; font-weight:600; cursor:pointer;">
            <i class="bi bi-arrow-clockwise"></i> Ver
        </button>
        <a href="{{ route('libro-contable.exportar', ['desde' => $fecha->startOfMonth()->format('Y-m-d'), 'hasta' => $fecha->copy()->endOfMonth()->format('Y-m-d')]) }}"
           style="background:#28a745; color:white; border-radius:8px; padding:.45rem 1rem; font-size:.82rem; font-weight:600; text-decoration:none; margin-left:auto;">
            <i class="bi bi-file-earmark-spreadsheet"></i> Exportar CSV
        </a>
    </form>
</div>

<div style="display:grid; grid-template-columns:1fr 360px; gap:1.25rem; align-items:start;">

    {{-- Documento contable --}}
    <div style="background:white; border-radius:12px; padding:2rem; box-shadow:0 1px 4px rgba(0,0,0,.07);">

        {{-- Encabezado --}}
        <div style="text-align:center; margin-bottom:1.5rem; border-bottom:2px solid var(--color-muy-claro); padding-bottom:1rem;">
            <p style="font-size:.75rem; font-weight:600; color:#8fa39a; text-transform:uppercase; letter-spacing:.1em; margin:0 0 .2rem;">Estado de Resultados</p>
            <h4 style="font-family:var(--fuente-titulos); color:var(--color-principal); margin:0 0 .2rem; font-size:1.2rem;">
                {{ config('app.name', 'Consultorio Dental') }}
            </h4>
            <p style="font-size:.82rem; color:#8fa39a; margin:0;">
                Período: {{ $fecha->locale('es')->isoFormat('MMMM [de] YYYY') }}
            </p>
        </div>

        {{-- INGRESOS --}}
        <div style="margin-bottom:1.5rem;">
            <p style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:#155724; margin:0 0 .75rem; border-bottom:1px solid #d4edda; padding-bottom:.4rem;">
                <i class="bi bi-arrow-down-circle"></i> INGRESOS
            </p>
            @forelse($ingresosPorOrigen as $ingreso)
            <div style="display:flex; justify-content:space-between; padding:.35rem 0; font-size:.88rem; border-bottom:1px dotted #f0f0f0;">
                <span style="color:#555;">{{ \App\Models\LibroContable::make(['origen' => $ingreso->origen])->origen_label }}</span>
                <span style="font-weight:500; color:#155724;">$ {{ number_format($ingreso->total, 0, ',', '.') }}</span>
            </div>
            @empty
            <p style="color:#8fa39a; font-size:.85rem; font-style:italic;">Sin ingresos registrados en este período.</p>
            @endforelse
            <div style="display:flex; justify-content:space-between; margin-top:.75rem; padding:.5rem .75rem; background:#d4edda; border-radius:8px; font-weight:700; font-size:1rem;">
                <span style="color:#155724;">TOTAL INGRESOS</span>
                <span style="color:#155724;">$ {{ number_format($totalIngresos, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- EGRESOS --}}
        <div style="margin-bottom:1.5rem;">
            <p style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:#721c24; margin:0 0 .75rem; border-bottom:1px solid #f8d7da; padding-bottom:.4rem;">
                <i class="bi bi-arrow-up-circle"></i> EGRESOS
            </p>
            @forelse($egresosPorCategoria as $egreso)
            <div style="display:flex; justify-content:space-between; padding:.35rem 0; font-size:.88rem; border-bottom:1px dotted #f0f0f0;">
                <span style="color:#555;">{{ $egreso->categoria ?? $egreso->origen_label ?? 'Sin categoría' }}</span>
                <span style="font-weight:500; color:#721c24;">$ {{ number_format($egreso->total, 0, ',', '.') }}</span>
            </div>
            @empty
            <p style="color:#8fa39a; font-size:.85rem; font-style:italic;">Sin egresos registrados en este período.</p>
            @endforelse
            <div style="display:flex; justify-content:space-between; margin-top:.75rem; padding:.5rem .75rem; background:#f8d7da; border-radius:8px; font-weight:700; font-size:1rem;">
                <span style="color:#721c24;">TOTAL EGRESOS</span>
                <span style="color:#721c24;">$ {{ number_format($totalEgresos, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Resultado final --}}
        <div style="border-top:3px double {{ $utilidad >= 0 ? '#28a745' : '#DC3545' }}; padding-top:1rem; margin-top:.5rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; padding:.75rem 1rem;
                background:{{ $utilidad >= 0 ? '#d4edda' : '#f8d7da' }}; border-radius:10px; margin-bottom:.5rem;">
                <span style="font-size:1rem; font-weight:700; color:{{ $utilidad >= 0 ? '#155724' : '#721c24' }};">UTILIDAD NETA</span>
                <span style="font-size:1.5rem; font-weight:800; color:{{ $utilidad >= 0 ? '#155724' : '#721c24' }};">
                    {{ $utilidad < 0 ? '-' : '' }} $ {{ number_format(abs($utilidad), 0, ',', '.') }}
                </span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:.3rem 1rem;">
                <span style="font-size:.82rem; color:#8fa39a;">Margen sobre ingresos</span>
                <span style="font-size:.88rem; font-weight:600; color:{{ $margen >= 0 ? '#155724' : '#721c24' }};">
                    {{ number_format($margen, 1) }}%
                </span>
            </div>
        </div>

    </div>

    {{-- Panel lateral: comparativa + dona --}}
    <div style="display:flex; flex-direction:column; gap:1rem;">

        {{-- Comparativa mes anterior --}}
        <div style="background:white; border-radius:12px; padding:1.25rem; box-shadow:0 1px 4px rgba(0,0,0,.07);">
            <p style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--color-principal); margin:0 0 .75rem;">
                Vs. mes anterior
            </p>
            @php
                $varIngresos = $ingresosMesAnterior > 0 ? (($totalIngresos - $ingresosMesAnterior) / $ingresosMesAnterior) * 100 : 0;
                $varEgresos  = $egresosMesAnterior  > 0 ? (($totalEgresos  - $egresosMesAnterior)  / $egresosMesAnterior)  * 100 : 0;
                $utilMesAnt  = $ingresosMesAnterior - $egresosMesAnterior;
                $varUtilidad = $utilMesAnt != 0 ? (($utilidad - $utilMesAnt) / abs($utilMesAnt)) * 100 : 0;
            @endphp
            <div style="display:flex; flex-direction:column; gap:.6rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:.85rem;">
                    <span style="color:#555;">Ingresos</span>
                    <span style="font-weight:600; color:{{ $varIngresos >= 0 ? '#28a745' : '#DC3545' }};">
                        <i class="bi bi-arrow-{{ $varIngresos >= 0 ? 'up' : 'down' }}-short"></i>
                        {{ number_format(abs($varIngresos), 1) }}%
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:.85rem;">
                    <span style="color:#555;">Egresos</span>
                    <span style="font-weight:600; color:{{ $varEgresos <= 0 ? '#28a745' : '#DC3545' }};">
                        <i class="bi bi-arrow-{{ $varEgresos >= 0 ? 'up' : 'down' }}-short"></i>
                        {{ number_format(abs($varEgresos), 1) }}%
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:.85rem; border-top:1px solid var(--color-muy-claro); padding-top:.5rem; margin-top:.1rem;">
                    <span style="font-weight:600; color:#555;">Utilidad</span>
                    <span style="font-weight:700; font-size:.95rem; color:{{ $varUtilidad >= 0 ? '#28a745' : '#DC3545' }};">
                        <i class="bi bi-arrow-{{ $varUtilidad >= 0 ? 'up' : 'down' }}-short"></i>
                        {{ number_format(abs($varUtilidad), 1) }}%
                    </span>
                </div>
            </div>
        </div>

        {{-- Donut egresos --}}
        @if($egresosPorCategoria->count())
        <div style="background:white; border-radius:12px; padding:1.25rem; box-shadow:0 1px 4px rgba(0,0,0,.07);">
            <p style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--color-principal); margin:0 0 .75rem;">
                Distribución egresos
            </p>
            <canvas id="donutEgresos" style="max-height:220px;"></canvas>
        </div>
        @endif

    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if($egresosPorCategoria->count())
const donutData = {
    labels: {!! json_encode($egresosPorCategoria->pluck('categoria')->map(fn($c) => $c ?? 'Sin categoría')->toArray()) !!},
    datasets: [{
        data: {!! json_encode($egresosPorCategoria->pluck('total')->map(fn($v) => (float)$v)->toArray()) !!},
        backgroundColor: [
            '#DC3545','#fd7e14','#ffc107','#6f42c1','#0d6efd',
            '#20c997','#0dcaf0','#6c757d','#198754','#d63384'
        ],
        borderWidth: 2,
        borderColor: '#fff',
    }]
};
new Chart(document.getElementById('donutEgresos'), {
    type: 'doughnut',
    data: donutData,
    options: {
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } } },
        cutout: '65%',
    }
});
@endif
</script>
@endpush
