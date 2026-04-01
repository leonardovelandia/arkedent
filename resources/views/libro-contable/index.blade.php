@extends('layouts.app')

@section('titulo', 'Libro Contable')

@section('contenido')

{{-- Tabs --}}
<div style="display:flex; gap:0.5rem; margin-bottom:1.5rem; flex-wrap:wrap;">
    <a href="{{ route('libro-contable.index') }}"
       style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.45rem 1rem; border-radius:8px; font-size:0.85rem; font-weight:600; text-decoration:none;
              background:var(--color-principal); color:white;">
        <i class="bi bi-journal-text"></i> Libro de Caja
    </a>
    <a href="{{ route('libro-contable.estado-resultados') }}"
       style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.45rem 1rem; border-radius:8px; font-size:0.85rem; font-weight:600; text-decoration:none;
              background:var(--color-muy-claro); color:var(--color-principal);">
        <i class="bi bi-bar-chart-line"></i> Estado de Resultados
    </a>
    <a href="{{ route('libro-contable.comparativo') }}"
       style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.45rem 1rem; border-radius:8px; font-size:0.85rem; font-weight:600; text-decoration:none;
              background:var(--color-muy-claro); color:var(--color-principal);">
        <i class="bi bi-graph-up"></i> Comparativo 12 Meses
    </a>
</div>

{{-- Cards resumen --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:1.5rem;">

    <div style="background:white; border-radius:12px; padding:1.1rem 1.25rem; box-shadow:0 1px 4px rgba(0,0,0,.07); border-left:4px solid #28a745;">
        <p style="font-size:0.72rem; font-weight:600; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .3rem;">Total Ingresos</p>
        <p style="font-size:1.3rem; font-weight:700; color:#28a745; margin:0;">$ {{ number_format($totalIngresos, 0, ',', '.') }}</p>
    </div>

    <div style="background:white; border-radius:12px; padding:1.1rem 1.25rem; box-shadow:0 1px 4px rgba(0,0,0,.07); border-left:4px solid #DC3545;">
        <p style="font-size:0.72rem; font-weight:600; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .3rem;">Total Egresos</p>
        <p style="font-size:1.3rem; font-weight:700; color:#DC3545; margin:0;">$ {{ number_format($totalEgresos, 0, ',', '.') }}</p>
    </div>

    <div style="background:white; border-radius:12px; padding:1.1rem 1.25rem; box-shadow:0 1px 4px rgba(0,0,0,.07); border-left:4px solid {{ $utilidad >= 0 ? '#28a745' : '#DC3545' }};">
        <p style="font-size:0.72rem; font-weight:600; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .3rem;">Utilidad Neta</p>
        <p style="font-size:1.5rem; font-weight:800; color:{{ $utilidad >= 0 ? '#28a745' : '#DC3545' }}; margin:0;">$ {{ number_format($utilidad, 0, ',', '.') }}</p>
    </div>

    <div style="background:white; border-radius:12px; padding:1.1rem 1.25rem; box-shadow:0 1px 4px rgba(0,0,0,.07); border-left:4px solid var(--color-principal);">
        <p style="font-size:0.72rem; font-weight:600; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .3rem;">Saldo Inicial Período</p>
        <p style="font-size:1.3rem; font-weight:700; color:var(--color-principal); margin:0;">$ {{ number_format($saldoInicial, 0, ',', '.') }}</p>
    </div>

</div>

{{-- Filtros + Acciones --}}
<div style="background:white; border-radius:12px; padding:1.25rem; box-shadow:0 1px 4px rgba(0,0,0,.07); margin-bottom:1.25rem;">
    <form method="GET" action="{{ route('libro-contable.index') }}" style="display:flex; flex-wrap:wrap; gap:0.75rem; align-items:flex-end;">

        <div style="display:flex; flex-direction:column; gap:.25rem;">
            <label style="font-size:.75rem; font-weight:600; color:var(--color-principal);">Desde</label>
            <input type="date" name="desde" value="{{ $desde->format('Y-m-d') }}"
                   style="border:1.5px solid #e0d9f7; border-radius:8px; padding:.4rem .75rem; font-size:.85rem;">
        </div>

        <div style="display:flex; flex-direction:column; gap:.25rem;">
            <label style="font-size:.75rem; font-weight:600; color:var(--color-principal);">Hasta</label>
            <input type="date" name="hasta" value="{{ $hasta->format('Y-m-d') }}"
                   style="border:1.5px solid #e0d9f7; border-radius:8px; padding:.4rem .75rem; font-size:.85rem;">
        </div>

        <div style="display:flex; flex-direction:column; gap:.25rem;">
            <label style="font-size:.75rem; font-weight:600; color:var(--color-principal);">Tipo</label>
            <select name="tipo" style="border:1.5px solid #e0d9f7; border-radius:8px; padding:.4rem .75rem; font-size:.85rem;">
                <option value="">Todos</option>
                <option value="ingreso" {{ $tipo === 'ingreso' ? 'selected' : '' }}>Solo Ingresos</option>
                <option value="egreso"  {{ $tipo === 'egreso'  ? 'selected' : '' }}>Solo Egresos</option>
            </select>
        </div>

        <label style="display:flex; align-items:center; gap:.4rem; font-size:.82rem; cursor:pointer; padding-bottom:.15rem;">
            <input type="checkbox" name="incluir_excluidos" value="1"
                   {{ request()->boolean('incluir_excluidos') ? 'checked' : '' }}>
            Mostrar excluidos
        </label>

        <button type="submit"
                style="background:var(--color-principal); color:white; border:none; border-radius:8px; padding:.45rem 1rem; font-size:.85rem; font-weight:600; cursor:pointer;">
            <i class="bi bi-search"></i> Filtrar
        </button>

        <a href="{{ route('libro-contable.index') }}"
           style="background:var(--color-muy-claro); color:var(--color-principal); border-radius:8px; padding:.45rem .85rem; font-size:.85rem; font-weight:600; text-decoration:none;">
            <i class="bi bi-x-lg"></i>
        </a>

        <div style="margin-left:auto; display:flex; gap:.5rem; flex-wrap:wrap;">
            <a href="{{ route('libro-contable.exportar', request()->only('desde','hasta','tipo')) }}"
               style="background:#28a745; color:white; border-radius:8px; padding:.45rem 1rem; font-size:.82rem; font-weight:600; text-decoration:none;">
                <i class="bi bi-file-earmark-spreadsheet"></i> Exportar CSV
            </a>
            <button type="button" onclick="document.getElementById('modal-ajuste').style.display='flex'"
                    style="background:#0d6efd; color:white; border:none; border-radius:8px; padding:.45rem 1rem; font-size:.82rem; font-weight:600; cursor:pointer;">
                <i class="bi bi-plus-circle"></i> Ajuste Manual
            </button>
        </div>

    </form>
</div>

{{-- Tabla del libro --}}
<div style="background:white; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.07); overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
            <thead>
                <tr style="background:var(--color-muy-claro);">
                    <th style="padding:.6rem .75rem; text-align:left; font-size:.72rem; font-weight:600; color:var(--color-principal); text-transform:uppercase; letter-spacing:.06em; white-space:nowrap;">Fecha</th>
                    <th style="padding:.6rem .75rem; text-align:left; font-size:.72rem; font-weight:600; color:var(--color-principal); text-transform:uppercase; letter-spacing:.06em;">N° Asiento</th>
                    <th style="padding:.6rem .75rem; text-align:left; font-size:.72rem; font-weight:600; color:var(--color-principal); text-transform:uppercase; letter-spacing:.06em;">Origen</th>
                    <th style="padding:.6rem .75rem; text-align:left; font-size:.72rem; font-weight:600; color:var(--color-principal); text-transform:uppercase; letter-spacing:.06em;">Concepto</th>
                    <th style="padding:.6rem .75rem; text-align:left; font-size:.72rem; font-weight:600; color:var(--color-principal); text-transform:uppercase; letter-spacing:.06em;">Categoría</th>
                    <th style="padding:.6rem .75rem; text-align:left; font-size:.72rem; font-weight:600; color:var(--color-principal); text-transform:uppercase; letter-spacing:.06em;">Referencia</th>
                    <th style="padding:.6rem .75rem; text-align:right; font-size:.72rem; font-weight:600; color:#28a745; text-transform:uppercase; letter-spacing:.06em;">Ingreso</th>
                    <th style="padding:.6rem .75rem; text-align:right; font-size:.72rem; font-weight:600; color:#DC3545; text-transform:uppercase; letter-spacing:.06em;">Egreso</th>
                    <th style="padding:.6rem .75rem; text-align:right; font-size:.72rem; font-weight:600; color:var(--color-principal); text-transform:uppercase; letter-spacing:.06em;">Saldo</th>
                    <th style="padding:.6rem .75rem; text-align:center; font-size:.72rem; font-weight:600; color:var(--color-principal); text-transform:uppercase; letter-spacing:.06em;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movimientosConSaldo as $movimiento)
                <tr style="border-bottom:1px solid #f0ebff; {{ $movimiento->excluido ? 'opacity:.4; text-decoration:line-through;' : '' }}">
                    <td style="padding:.5rem .75rem; white-space:nowrap;">{{ $movimiento->fecha_movimiento->format('d/m/Y') }}</td>
                    <td style="padding:.5rem .75rem;">
                        <span style="font-size:.72rem; background:var(--color-muy-claro); color:var(--color-principal); padding:2px 8px; border-radius:50px; font-weight:600;">
                            {{ $movimiento->numero_formateado }}
                        </span>
                    </td>
                    <td style="padding:.5rem .75rem;">
                        <span style="font-size:.72rem; padding:2px 8px; border-radius:50px; font-weight:500;
                            background:{{ $movimiento->tipo === 'ingreso' ? '#d4edda' : '#f8d7da' }};
                            color:{{ $movimiento->tipo === 'ingreso' ? '#155724' : '#721c24' }};">
                            {{ $movimiento->origen_label }}
                        </span>
                    </td>
                    <td style="padding:.5rem .75rem; max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $movimiento->concepto }}">
                        {{ $movimiento->concepto }}
                    </td>
                    <td style="padding:.5rem .75rem; font-size:.78rem; color:#8fa39a;">{{ $movimiento->categoria ?? '—' }}</td>
                    <td style="padding:.5rem .75rem; font-size:.78rem; color:#8fa39a;">{{ $movimiento->referencia ?? '—' }}</td>
                    <td style="padding:.5rem .75rem; text-align:right; color:#155724; font-weight:500; white-space:nowrap;">
                        @if($movimiento->tipo === 'ingreso' && !$movimiento->excluido)
                            $ {{ number_format($movimiento->valor, 0, ',', '.') }}
                        @endif
                    </td>
                    <td style="padding:.5rem .75rem; text-align:right; color:#721c24; font-weight:500; white-space:nowrap;">
                        @if($movimiento->tipo === 'egreso' && !$movimiento->excluido)
                            $ {{ number_format($movimiento->valor, 0, ',', '.') }}
                        @endif
                    </td>
                    <td style="padding:.5rem .75rem; text-align:right; font-weight:700; white-space:nowrap;
                        color:{{ $movimiento->saldo_acumulado >= 0 ? '#155724' : '#721c24' }};">
                        $ {{ number_format($movimiento->saldo_acumulado, 0, ',', '.') }}
                    </td>
                    <td style="padding:.5rem .75rem; text-align:center; white-space:nowrap;">
                        @if(!$movimiento->excluido)
                            <button onclick="excluirMovimiento({{ $movimiento->id }})"
                                    title="Excluir del libro"
                                    style="background:none; border:none; cursor:pointer; color:#dc3545; font-size:.9rem; padding:2px 4px;">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        @else
                            <button onclick="incluirMovimiento({{ $movimiento->id }})"
                                    title="Volver a incluir"
                                    style="background:none; border:none; cursor:pointer; color:#28a745; font-size:.9rem; padding:2px 4px;">
                                <i class="bi bi-eye"></i>
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="padding:2rem; text-align:center; color:#8fa39a; font-size:.9rem;">
                        <i class="bi bi-journal-x" style="font-size:2rem; display:block; margin-bottom:.5rem;"></i>
                        No hay movimientos en el período seleccionado.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($movimientosConSaldo->count())
            <tfoot>
                <tr style="background:var(--color-muy-claro); font-weight:600;">
                    <td colspan="6" style="padding:.6rem .75rem; text-align:right; color:var(--color-principal); font-size:.82rem; text-transform:uppercase; letter-spacing:.04em;">
                        TOTALES DEL PERÍODO:
                    </td>
                    <td style="padding:.6rem .75rem; text-align:right; color:#155724; white-space:nowrap;">$ {{ number_format($totalIngresos, 0, ',', '.') }}</td>
                    <td style="padding:.6rem .75rem; text-align:right; color:#721c24; white-space:nowrap;">$ {{ number_format($totalEgresos, 0, ',', '.') }}</td>
                    <td style="padding:.6rem .75rem; text-align:right; white-space:nowrap; font-size:1rem;
                        color:{{ $utilidad >= 0 ? '#155724' : '#721c24' }};">
                        $ {{ number_format($utilidad, 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- Modal ajuste manual --}}
<div id="modal-ajuste" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1050; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:16px; padding:1.75rem; width:100%; max-width:480px; box-shadow:0 8px 32px rgba(0,0,0,.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h5 style="margin:0; font-family:var(--fuente-titulos); color:var(--color-principal);">
                <i class="bi bi-plus-circle"></i> Ajuste Manual
            </h5>
            <button onclick="document.getElementById('modal-ajuste').style.display='none'"
                    style="background:none; border:none; font-size:1.25rem; cursor:pointer; color:#8fa39a;">&times;</button>
        </div>
        <form method="POST" action="{{ route('libro-contable.ajuste') }}">
            @csrf
            <div style="display:grid; gap:.75rem;">
                <div>
                    <label style="font-size:.8rem; font-weight:600; color:var(--color-principal); display:block; margin-bottom:.25rem;">Tipo *</label>
                    <select name="tipo" required style="width:100%; border:1.5px solid #e0d9f7; border-radius:8px; padding:.45rem .75rem; font-size:.88rem;">
                        <option value="ingreso">Ingreso</option>
                        <option value="egreso">Egreso</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:.8rem; font-weight:600; color:var(--color-principal); display:block; margin-bottom:.25rem;">Concepto *</label>
                    <input type="text" name="concepto" required maxlength="255"
                           style="width:100%; border:1.5px solid #e0d9f7; border-radius:8px; padding:.45rem .75rem; font-size:.88rem; box-sizing:border-box;">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:.75rem;">
                    <div>
                        <label style="font-size:.8rem; font-weight:600; color:var(--color-principal); display:block; margin-bottom:.25rem;">Valor *</label>
                        <input type="number" name="valor" required min="0.01" step="0.01"
                               style="width:100%; border:1.5px solid #e0d9f7; border-radius:8px; padding:.45rem .75rem; font-size:.88rem; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="font-size:.8rem; font-weight:600; color:var(--color-principal); display:block; margin-bottom:.25rem;">Fecha *</label>
                        <input type="date" name="fecha_movimiento" required value="{{ now()->format('Y-m-d') }}"
                               style="width:100%; border:1.5px solid #e0d9f7; border-radius:8px; padding:.45rem .75rem; font-size:.88rem; box-sizing:border-box;">
                    </div>
                </div>
                <div>
                    <label style="font-size:.8rem; font-weight:600; color:var(--color-principal); display:block; margin-bottom:.25rem;">Descripción</label>
                    <textarea name="descripcion" rows="2"
                              style="width:100%; border:1.5px solid #e0d9f7; border-radius:8px; padding:.45rem .75rem; font-size:.88rem; box-sizing:border-box; resize:vertical;"></textarea>
                </div>
            </div>
            <div style="display:flex; gap:.75rem; margin-top:1.25rem; justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('modal-ajuste').style.display='none'"
                        style="background:var(--color-muy-claro); color:var(--color-principal); border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.88rem; font-weight:600; cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit"
                        style="background:#0d6efd; color:white; border:none; border-radius:8px; padding:.5rem 1.25rem; font-size:.88rem; font-weight:600; cursor:pointer;">
                    <i class="bi bi-check-lg"></i> Registrar Ajuste
                </button>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
<div style="position:fixed; bottom:1.5rem; right:1.5rem; background:#28a745; color:white; padding:.75rem 1.25rem; border-radius:10px; font-size:.88rem; font-weight:600; box-shadow:0 4px 12px rgba(0,0,0,.2); z-index:9999;">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
</div>
@endif

@endsection

@push('scripts')
<script>
function excluirMovimiento(id) {
    if (!confirm('¿Excluir este movimiento del libro contable? Seguirá visible si activas "Mostrar excluidos".')) return;
    fetch(`/libro-contable/${id}/excluir`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ motivo: 'Excluido manualmente' })
    }).then(r => r.json()).then(d => {
        if (d.success) location.reload();
    });
}

function incluirMovimiento(id) {
    if (!confirm('¿Volver a incluir este movimiento en el libro contable?')) return;
    fetch(`/libro-contable/${id}/incluir`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(r => r.json()).then(d => {
        if (d.success) location.reload();
    });
}
</script>
@endpush
