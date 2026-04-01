@extends('layouts.app')
@section('titulo', 'Recetas Médicas')

@section('contenido')

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo">Recetas Médicas</h1>
        <p class="page-subtitulo">Gestión de prescripciones médicas del consultorio</p>
    </div>
    <a href="{{ route('recetas.create') }}"
       style="display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.25rem;background:linear-gradient(135deg,var(--color-principal),var(--color-claro));color:white;border-radius:8px;text-decoration:none;font-size:.84rem;font-weight:600;box-shadow:0 2px 8px var(--sombra-principal);">
        <i class="bi bi-plus-lg"></i> Nueva Receta
    </a>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:1.5rem;">
    @foreach([
        ['Hoy', $totalHoy, 'bi-calendar-check', 'var(--color-principal)'],
        ['Este mes', $totalMes, 'bi-calendar-month', '#0ea5e9'],
        ['Total recetas', $recetas->total(), 'bi-file-medical', '#8b5cf6'],
    ] as [$label, $val, $icon, $color])
    <div class="card-sistema" style="padding:.9rem 1rem;display:flex;align-items:center;gap:.9rem;">
        <div style="width:40px;height:40px;border-radius:10px;background:{{ $color }}1a;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi {{ $icon }}" style="font-size:1.1rem;color:{{ $color }};"></i>
        </div>
        <div>
            <div style="font-size:1.35rem;font-weight:700;color:var(--texto-principal);line-height:1.1;">{{ $val }}</div>
            <div style="font-size:.72rem;color:var(--texto-secundario);">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filtros --}}
<div class="card-sistema" style="padding:1rem;margin-bottom:1.25rem;">
    <form method="GET" id="form-filtros" style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:flex-end;">
        {{-- Autocomplete buscar --}}
        <div style="flex:1;min-width:220px;position:relative;">
            <input type="text" name="buscar" id="inp-buscar" value="{{ request('buscar') }}"
                   placeholder="Buscar receta o paciente..."
                   autocomplete="off"
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.45rem .75rem;font-size:.83rem;background:var(--fondo-app);">
            <ul id="ac-lista"
                style="display:none;position:absolute;top:100%;left:0;right:0;z-index:200;background:#fff;border:1px solid var(--fondo-borde);border-top:none;border-radius:0 0 8px 8px;margin:0;padding:0;list-style:none;box-shadow:0 4px 12px rgba(0,0,0,.1);max-height:220px;overflow-y:auto;"></ul>
        </div>
        <div>
            <select name="doctor_id" id="sel-doctor"
                    style="border:1px solid var(--fondo-borde);border-radius:8px;padding:.45rem .65rem;font-size:.83rem;background:var(--fondo-app);">
                <option value="">Todos los doctores</option>
                @foreach($doctores as $d)
                <option value="{{ $d->id }}" {{ request('doctor_id')==$d->id?'selected':'' }}>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;gap:.4rem;">
            <input type="date" name="desde" id="inp-desde" value="{{ request('desde') }}"
                   style="border:1px solid var(--fondo-borde);border-radius:8px;padding:.45rem .65rem;font-size:.83rem;background:var(--fondo-app);">
            <input type="date" name="hasta" id="inp-hasta" value="{{ request('hasta') }}"
                   style="border:1px solid var(--fondo-borde);border-radius:8px;padding:.45rem .65rem;font-size:.83rem;background:var(--fondo-app);">
        </div>
        @if(request()->hasAny(['buscar','doctor_id','desde','hasta']))
        <a href="{{ route('recetas.index') }}"
           style="padding:.45rem .9rem;border:1px solid var(--fondo-borde);border-radius:8px;font-size:.83rem;color:var(--texto-secundario);text-decoration:none;"
           title="Limpiar filtros">
            <i class="bi bi-x-lg"></i>
        </a>
        @endif
    </form>
</div>

@if(session('exito'))
<div style="background:#dcfce7;border:1px solid #86efac;color:#166534;border-radius:8px;padding:.7rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <i class="bi bi-check-circle me-1"></i> {{ session('exito') }}
</div>
@endif

<div class="card-sistema" style="padding:0;overflow:hidden;">
    <div style="overflow-y:auto;max-height:calc(10 * 52px + 46px);">
    <table style="width:100%;border-collapse:collapse;font-size:.83rem;">
        <thead>
            <tr style="background:var(--color-muy-claro);">
                <th style="padding:.75rem 1rem;text-align:left;font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--color-hover);">N° Receta</th>
                <th style="padding:.75rem 1rem;text-align:left;font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--color-hover);">Paciente</th>
                <th style="padding:.75rem 1rem;text-align:left;font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--color-hover);">Doctor</th>
                <th style="padding:.75rem 1rem;text-align:left;font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--color-hover);">Fecha</th>
                <th style="padding:.75rem 1rem;text-align:left;font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--color-hover);">Medicamentos</th>
                <th style="padding:.75rem 1rem;text-align:right;font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--color-hover);">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recetas as $receta)
            <tr style="border-bottom:1px solid var(--fondo-borde);" onmouseover="this.style.background='var(--fondo-card-alt)'" onmouseout="this.style.background=''">
                <td style="padding:.7rem 1rem;">
                    <a href="{{ route('recetas.show', $receta) }}" style="color:var(--color-principal);font-weight:600;text-decoration:none;">
                        {{ $receta->numero_receta }}
                    </a>
                </td>
                <td style="padding:.7rem 1rem;">
                    <a href="{{ route('pacientes.show', $receta->paciente) }}" style="color:var(--texto-principal);text-decoration:none;font-weight:500;">
                        {{ $receta->paciente->nombre_completo }}
                    </a>
                    <div style="font-size:.72rem;color:var(--texto-secundario);">{{ $receta->paciente->numero_historia }}</div>
                </td>
                <td style="padding:.7rem 1rem;color:var(--texto-secundario);">{{ $receta->doctor->name }}</td>
                <td style="padding:.7rem 1rem;color:var(--texto-secundario);">{{ $receta->fecha->format('d/m/Y') }}</td>
                <td style="padding:.7rem 1rem;">
                    <span style="background:var(--color-muy-claro);color:var(--color-principal);font-size:.72rem;font-weight:600;padding:.2rem .55rem;border-radius:20px;">
                        {{ $receta->total_medicamentos }} ítem(s)
                    </span>
                </td>
                <td style="padding:.7rem 1rem;text-align:right;">
                    <div style="display:flex;gap:.4rem;justify-content:flex-end;align-items:center;">
                        <a href="{{ route('recetas.show', $receta) }}"
                           style="padding:.3rem .6rem;background:var(--color-muy-claro);color:var(--color-principal);border-radius:6px;text-decoration:none;font-size:.78rem;"
                           title="Ver">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('recetas.edit', $receta) }}"
                           style="padding:.3rem .6rem;background:#fff7ed;color:#ea580c;border-radius:6px;text-decoration:none;font-size:.78rem;"
                           title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('recetas.destroy', $receta) }}"
                              onsubmit="return confirm('¿Anular esta receta?')" style="margin:0;">
                            @csrf @method('DELETE')
                            <button type="submit"
                                style="padding:.3rem .6rem;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;font-size:.78rem;cursor:pointer;"
                                title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        <a href="{{ route('recetas.pdf', $receta) }}" target="_blank"
                           style="padding:.3rem .6rem;background:#f0fdf4;color:#16a34a;border-radius:6px;text-decoration:none;font-size:.78rem;"
                           title="PDF">
                            <i class="bi bi-file-pdf"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding:2.5rem;text-align:center;color:var(--texto-secundario);font-size:.84rem;">
                    <i class="bi bi-file-medical" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.4;"></i>
                    No hay recetas registradas
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>{{-- fin scroll --}}
</div>

@if($recetas->hasPages())
<div style="margin-top:1rem;">{{ $recetas->links() }}</div>
@endif

@push('scripts')
<script>
// ── Autocompletado buscador ───────────────────────────────────
const inpBuscar = document.getElementById('inp-buscar');
const acLista   = document.getElementById('ac-lista');
const formFiltros = document.getElementById('form-filtros');
let acTimer;

inpBuscar.addEventListener('input', function () {
    clearTimeout(acTimer);
    const q = this.value.trim();
    if (q.length < 2) { acLista.style.display = 'none'; return; }
    acTimer = setTimeout(() => {
        fetch(`/api/recetas/buscar?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                if (!data.length) { acLista.style.display = 'none'; return; }
                acLista.innerHTML = data.map(item => `
                    <li data-value="${item.value}"
                        style="padding:.45rem .85rem;cursor:pointer;font-size:.83rem;color:var(--texto-principal);border-bottom:1px solid var(--fondo-borde);">
                        ${item.label}
                    </li>`).join('');
                acLista.querySelectorAll('li').forEach(li => {
                    li.addEventListener('mouseenter', () => li.style.background = 'var(--fondo-card-alt)');
                    li.addEventListener('mouseleave', () => li.style.background = '');
                    li.addEventListener('mousedown', () => {
                        inpBuscar.value = li.dataset.value;
                        acLista.style.display = 'none';
                        formFiltros.submit();
                    });
                });
                acLista.style.display = 'block';
            })
            .catch(() => { acLista.style.display = 'none'; });
    }, 280);
});

inpBuscar.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') { acLista.style.display = 'none'; formFiltros.submit(); }
    if (e.key === 'Escape') { acLista.style.display = 'none'; }
});

document.addEventListener('click', e => {
    if (!inpBuscar.contains(e.target) && !acLista.contains(e.target)) {
        acLista.style.display = 'none';
    }
});

// ── Auto-submit en doctor y fechas ───────────────────────────
['sel-doctor', 'inp-desde', 'inp-hasta'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('change', () => formFiltros.submit());
});
</script>
@endpush

@endsection
