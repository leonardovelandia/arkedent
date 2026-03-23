@extends('layouts.app')
@section('titulo', 'Reporte de Pacientes')

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
    .filtros-grid { display:grid; grid-template-columns:1fr 1fr 1fr 1fr auto; gap:.75rem; align-items:end; }
    @media(max-width:900px){ .filtros-grid{ grid-template-columns:1fr 1fr; } }
    @media(max-width:500px){ .filtros-grid{ grid-template-columns:1fr; } }
    .form-label { font-size:.78rem; font-weight:700; color:var(--color-hover); display:block; margin-bottom:.25rem; }
    .form-input { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .75rem; font-size:.85rem; color:#1c2b22; background:#fff; outline:none; }
    .form-input:focus { border-color:var(--color-principal); }
    .filtros-acciones { display:flex; gap:.5rem; flex-wrap:wrap; }

    .stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.25rem; }
    @media(max-width:700px){ .stats-grid{ grid-template-columns:1fr 1fr; } }

    .metrica-reporte { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1.1rem 1.25rem; display:flex; flex-direction:column; gap:.4rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .metrica-valor { font-family:var(--fuente-titulos); font-size:1.6rem; font-weight:600; color:var(--color-principal); line-height:1; }
    .metrica-label { font-size:.72rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; }

    .panel-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1.25rem; }
    .panel-card-header { padding:.85rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
    .panel-card-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:600; color:var(--color-hover); display:flex; align-items:center; gap:.45rem; }
    .panel-card-titulo i { color:var(--color-principal); }

    .tablas-3col { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; margin-bottom:1.25rem; }
    @media(max-width:900px){ .tablas-3col{ grid-template-columns:1fr 1fr; } }
    @media(max-width:600px){ .tablas-3col{ grid-template-columns:1fr; } }

    .tabla-reporte { width:100%; border-collapse:collapse; font-size:.82rem; }
    .tabla-reporte th { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-principal); padding:.5rem .75rem; border-bottom:2px solid var(--color-muy-claro); text-align:left; }
    .tabla-reporte td { padding:.55rem .75rem; border-bottom:1px solid var(--fondo-borde); color:#374151; vertical-align:middle; }
    .tabla-reporte tr:last-child td { border-bottom:none; }
    .tabla-reporte tr:hover td { background:var(--fondo-card-alt); }

    .barra-progreso-custom { height:6px; background:var(--color-muy-claro); border-radius:50px; overflow:hidden; margin-top:.3rem; }
    .barra-progreso-fill { height:100%; background:var(--color-principal); border-radius:50px; }

    .badge-genero { display:inline-block; font-size:.72rem; font-weight:600; padding:.2rem .6rem; border-radius:50px; }
    .badge-femenino  { background:#fce7f3; color:#be185d; }
    .badge-masculino { background:#dbeafe; color:#1e40af; }
    .badge-otro      { background:#f3f4f6; color:#374151; }

    .pagination-wrapper { padding:.75rem 1.25rem; border-top:1px solid var(--fondo-borde); display:flex; justify-content:flex-end; }

    .btn-accion { display:inline-flex; align-items:center; gap:.3rem; padding:.25rem .6rem; border-radius:6px; font-size:.75rem; font-weight:500; text-decoration:none; background:var(--color-muy-claro); color:var(--color-principal); transition:background .15s; }
    .btn-accion:hover { background:var(--color-muy-claro); color:var(--color-principal); }
</style>
@endpush

@section('contenido')

<div class="reporte-header">
    <a href="{{ route('reportes.index') }}"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Reporte de Pacientes</h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">Análisis de la base de pacientes</p>
    </div>
</div>

{{-- Filtros --}}
<div class="filtros-card">
    <form id="form-filtros-pacientes" method="GET" action="{{ route('reportes.pacientes') }}">
        <div class="filtros-grid">
            <div>
                <label class="form-label">Registro desde</label>
                <input type="date" name="desde" id="filtro-desde" class="form-input" value="{{ $desde->format('Y-m-d') }}">
            </div>
            <div>
                <label class="form-label">Registro hasta</label>
                <input type="date" name="hasta" id="filtro-hasta" class="form-input" value="{{ $hasta->format('Y-m-d') }}">
            </div>
            <div>
                <label class="form-label">Género</label>
                <select name="genero" id="filtro-genero" class="form-input">
                    <option value="">Todos</option>
                    <option value="femenino"  {{ $genero === 'femenino'  ? 'selected' : '' }}>Femenino</option>
                    <option value="masculino" {{ $genero === 'masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="otro"      {{ $genero === 'otro'      ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
            <div>
                <label class="form-label">Ciudad</label>
                <input type="text" name="ciudad" id="filtro-ciudad" class="form-input" placeholder="Filtrar ciudad..." value="{{ $ciudad }}">
            </div>
            <div class="filtros-acciones">
                <a href="{{ route('reportes.pacientes') }}" class="btn-gris"><i class="bi bi-x"></i> Limpiar</a>
                <a id="btn-csv" href="{{ route('reportes.exportar-pacientes', request()->query()) }}" class="btn-verde"><i class="bi bi-download"></i> CSV</a>
            </div>
        </div>
    </form>
</div>

{{-- Stats de período --}}
<div class="stats-grid">
    <div class="metrica-reporte">
        <span class="metrica-label">Pacientes en período</span>
        <div class="metrica-valor">{{ $pacientes->total() }}</div>
        <div style="font-size:.78rem;color:#6b7280;">{{ $desde->locale('es')->isoFormat('D MMM YYYY') }} — {{ $hasta->locale('es')->isoFormat('D MMM YYYY') }}</div>
    </div>
    @php
        $totalGenero = $porGenero->sum('total') ?: 1;
        $fem = $porGenero->where('genero', 'femenino')->first();
        $mas = $porGenero->where('genero', 'masculino')->first();
    @endphp
    <div class="metrica-reporte">
        <span class="metrica-label">Distribución género</span>
        <div style="display:flex;gap:.5rem;align-items:baseline;margin-top:.25rem;">
            <span class="badge-genero badge-femenino">F {{ $fem ? round(($fem->total/$totalGenero)*100) : 0 }}%</span>
            <span class="badge-genero badge-masculino">M {{ $mas ? round(($mas->total/$totalGenero)*100) : 0 }}%</span>
        </div>
        <div style="font-size:.78rem;color:#6b7280;">{{ $porGenero->sum('total') }} pacientes activos</div>
    </div>
    <div class="metrica-reporte">
        <span class="metrica-label">Ciudad principal</span>
        <div class="metrica-valor" style="font-size:1.1rem;margin-top:.25rem;">{{ $porCiudad->first()?->ciudad ?? '—' }}</div>
        <div style="font-size:.78rem;color:#6b7280;">{{ $porCiudad->first()?->total ?? 0 }} pacientes</div>
    </div>
</div>

{{-- Distribuciones --}}
<div class="tablas-3col">

    {{-- Por género --}}
    <div class="panel-card">
        <div class="panel-card-header">
            <div class="panel-card-titulo"><i class="bi bi-gender-ambiguous"></i> Por género</div>
        </div>
        @if($porGenero->isEmpty())
            <div style="padding:1rem;text-align:center;color:#9ca3af;font-size:.82rem;">Sin datos</div>
        @else
        @php $maxG = $porGenero->max('total') ?: 1; @endphp
        <table class="tabla-reporte">
            <thead><tr><th>Género</th><th style="text-align:right;">Total</th><th style="text-align:right;">%</th></tr></thead>
            <tbody>
            @foreach($porGenero as $g)
            @php
                $pct = round(($g->total / $totalGenero) * 100, 1);
                $bc  = match($g->genero) { 'femenino' => 'badge-femenino', 'masculino' => 'badge-masculino', default => 'badge-otro' };
            @endphp
            <tr>
                <td>
                    <span class="badge-genero {{ $bc }}">{{ ucfirst($g->genero) }}</span>
                    <div class="barra-progreso-custom"><div class="barra-progreso-fill" style="width:{{ $pct }}%;background:{{ $g->genero === 'femenino' ? '#be185d' : '#1e40af' }};"></div></div>
                </td>
                <td style="text-align:right;font-weight:600;">{{ $g->total }}</td>
                <td style="text-align:right;color:var(--color-principal);font-weight:600;">{{ $pct }}%</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Por edad --}}
    <div class="panel-card">
        <div class="panel-card-header">
            <div class="panel-card-titulo"><i class="bi bi-bar-chart"></i> Por rango de edad</div>
        </div>
        @php $maxE = max($rangoEdades) ?: 1; $totalE = array_sum($rangoEdades) ?: 1; @endphp
        <table class="tabla-reporte">
            <thead><tr><th>Rango</th><th style="text-align:right;">Total</th><th style="text-align:right;">%</th></tr></thead>
            <tbody>
            @foreach($rangoEdades as $rango => $cantidad)
            @php $pct = round(($cantidad / $totalE) * 100, 1); @endphp
            <tr>
                <td>
                    <span style="font-weight:500;">{{ $rango }} años</span>
                    <div class="barra-progreso-custom"><div class="barra-progreso-fill" style="width:{{ $pct }}%;"></div></div>
                </td>
                <td style="text-align:right;font-weight:600;">{{ $cantidad }}</td>
                <td style="text-align:right;color:var(--color-principal);font-weight:600;">{{ $pct }}%</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Top ciudades --}}
    <div class="panel-card">
        <div class="panel-card-header">
            <div class="panel-card-titulo"><i class="bi bi-geo-alt"></i> Top ciudades</div>
        </div>
        @if($porCiudad->isEmpty())
            <div style="padding:1rem;text-align:center;color:#9ca3af;font-size:.82rem;">Sin datos</div>
        @else
        @php $maxC = $porCiudad->max('total') ?: 1; @endphp
        <table class="tabla-reporte">
            <thead><tr><th>Ciudad</th><th style="text-align:right;">Pacientes</th></tr></thead>
            <tbody>
            @foreach($porCiudad as $c)
            <tr>
                <td>
                    <span style="font-weight:500;">{{ $c->ciudad ?: 'No especificada' }}</span>
                    <div class="barra-progreso-custom"><div class="barra-progreso-fill" style="width:{{ round(($c->total/$maxC)*100) }}%;"></div></div>
                </td>
                <td style="text-align:right;font-weight:600;color:var(--color-principal);">{{ $c->total }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>

{{-- Listado de pacientes --}}
<div class="panel-card">
    <div class="panel-card-header">
        <div class="panel-card-titulo"><i class="bi bi-people"></i> Listado de pacientes</div>
        <span style="font-size:.78rem;color:#9ca3af;">{{ $pacientes->total() }} registros</span>
    </div>
    @if($pacientes->isEmpty())
        <div style="padding:2rem;text-align:center;color:#9ca3af;font-size:.85rem;">
            <i class="bi bi-person-x" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>
            No se encontraron pacientes con los filtros seleccionados.
        </div>
    @else
    <div style="overflow-x:auto;">
    <table class="tabla-reporte">
        <thead>
            <tr>
                <th>N° Historia</th>
                <th>Nombre</th>
                <th>Documento</th>
                <th style="text-align:center;">Edad</th>
                <th style="text-align:center;">Género</th>
                <th>Ciudad</th>
                <th>Registro</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($pacientes as $pac)
        @php
            $bc = match($pac->genero) { 'femenino' => 'badge-femenino', 'masculino' => 'badge-masculino', default => 'badge-otro' };
        @endphp
        <tr>
            <td style="font-weight:600;color:var(--color-principal);">{{ $pac->numero_historia }}</td>
            <td>
                <div style="font-weight:500;">{{ $pac->nombre_completo }}</div>
                <div style="font-size:.75rem;color:#9ca3af;">{{ $pac->telefono }}</div>
            </td>
            <td>{{ $pac->numero_documento }}</td>
            <td style="text-align:center;">{{ $pac->edad ?? '—' }}</td>
            <td style="text-align:center;"><span class="badge-genero {{ $bc }}">{{ ucfirst($pac->genero) }}</span></td>
            <td>{{ $pac->ciudad ?: '—' }}</td>
            <td style="white-space:nowrap;font-size:.78rem;">{{ $pac->created_at->locale('es')->isoFormat('D MMM YYYY') }}</td>
            <td>
                <a href="{{ route('pacientes.show', $pac) }}" class="btn-accion">
                    <i class="bi bi-eye"></i> Ver
                </a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    <div class="pagination-wrapper">
        {{ $pacientes->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    var form   = document.getElementById('form-filtros-pacientes');
    var btnCsv = document.getElementById('btn-csv');
    var timer;

    function actualizarCsv() {
        var params = new URLSearchParams(new FormData(form)).toString();
        btnCsv.href = '{{ route('reportes.exportar-pacientes') }}' + '?' + params;
    }

    function filtrar() {
        actualizarCsv();
        form.submit();
    }

    document.getElementById('filtro-desde').addEventListener('change',  filtrar);
    document.getElementById('filtro-hasta').addEventListener('change',  filtrar);
    document.getElementById('filtro-genero').addEventListener('change', filtrar);

    document.getElementById('filtro-ciudad').addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(filtrar, 500);
    });
})();
</script>
@endpush

@endsection
