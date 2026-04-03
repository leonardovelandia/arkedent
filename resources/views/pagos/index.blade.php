@extends('layouts.app')
@section('titulo', 'Abonos y Pagos')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);}
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .resumen-cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:.75rem; margin-bottom:1.25rem; }
    .resumen-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:.9rem 1.1rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .resumen-card-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-principal); margin-bottom:.3rem; }
    .resumen-card-valor { font-size:1.25rem; font-weight:800; color:#1c2b22; }
    .resumen-card-sub { font-size:.75rem; color:#9ca3af; }
    .badge-metodo { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .6rem; border-radius:20px; font-size:.72rem; font-weight:700; white-space:nowrap; }
    .badge-efectivo      { background:#D4EDDA; color:#155724; }
    .badge-transferencia { background:#D1ECF1; color:#0c5460; }
    .badge-tarjeta       { background:#E8D5FF; color:var(--color-sidebar-2); }
    .badge-cheque        { background:#FFE5CC; color:#7C2D12; }
    .badge-otro          { background:#F3F4F6; color:#374151; }
    .tbl-table tr.pago-anulado td { opacity:.55; }
    .tbl-table tr.pago-anulado .recibo-num { text-decoration:line-through; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif
@if(session('error'))
<div class="alerta-flash" style="background:#fef2f2;color:#dc2626;border:1px solid #fecdd3;">
    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
</div>
@endif

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo">Abonos y Pagos</h1>
        <p class="page-subtitulo">Registro de pagos y recibos</p>
    </div>
    <a href="{{ route('pagos.create') }}" class="btn-morado">
        <i class="bi bi-plus-circle"></i> Registrar Pago
    </a>
</div>



{{-- Cards resumen --}}
<div class="resumen-cards">
    <div class="resumen-card">
        <div class="resumen-card-label"><i class="bi bi-calendar-month"></i> Ingresos del mes</div>
        <div class="resumen-card-valor">$ {{ number_format($totalMes, 0, ',', '.') }}</div>
        <div class="resumen-card-sub">Mes actual</div>
    </div>
    <div class="resumen-card">
        <div class="resumen-card-label"><i class="bi bi-calendar-day"></i> Ingresos de hoy</div>
        <div class="resumen-card-valor">$ {{ number_format($totalHoy, 0, ',', '.') }}</div>
        <div class="resumen-card-sub">{{ now()->translatedFormat('d M Y') }}</div>
    </div>
    <div class="resumen-card">
        <div class="resumen-card-label"><i class="bi bi-receipt"></i> Pagos de hoy</div>
        <div class="resumen-card-valor">{{ $pagosHoy }}</div>
        <div class="resumen-card-sub">Transacciones</div>
    </div>
</div>

<x-tabla-listado
    :paginacion="$pagos"
    placeholder="N° recibo, concepto, paciente..."
    icono-vacio="bi-cash-stack"
    mensaje-vacio="Sin pagos registrados"
>
    <x-slot:filtros>
        <select name="metodo_pago" class="tbl-filtro-select">
            <option value="">Todos los métodos</option>
            <option value="efectivo"        {{ request('metodo_pago') === 'efectivo'        ? 'selected' : '' }}>Efectivo</option>
            <option value="transferencia"   {{ request('metodo_pago') === 'transferencia'   ? 'selected' : '' }}>Transferencia</option>
            <option value="tarjeta_credito" {{ request('metodo_pago') === 'tarjeta_credito' ? 'selected' : '' }}>T. Crédito</option>
            <option value="tarjeta_debito"  {{ request('metodo_pago') === 'tarjeta_debito'  ? 'selected' : '' }}>T. Débito</option>
            <option value="cheque"          {{ request('metodo_pago') === 'cheque'          ? 'selected' : '' }}>Cheque</option>
            <option value="otro"            {{ request('metodo_pago') === 'otro'            ? 'selected' : '' }}>Otro</option>
        </select>
        <input type="date" name="fecha_desde" class="tbl-filtro-date" value="{{ request('fecha_desde') }}" title="Desde">
        <input type="date" name="fecha_hasta" class="tbl-filtro-date" value="{{ request('fecha_hasta') }}" title="Hasta">
    </x-slot:filtros>

    <x-slot:accion-vacio>
        @if(!request()->hasAny(['buscar','metodo_pago','fecha_desde','fecha_hasta']))
        <div class="mt-3">
            <a href="{{ route('pagos.create') }}" class="btn-morado">
                <i class="bi bi-plus-circle"></i> Registrar primer pago
            </a>
        </div>
        @endif
    </x-slot:accion-vacio>

    <x-slot:thead>
        <tr>
            <th>N° Recibo</th>
            <th>Paciente</th>
            <th>Concepto</th>
            <th>Valor</th>
            <th>Método</th>
            <th>Fecha</th>
            <th>Registrado por</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

    @foreach($pagos as $p)
    <tr class="{{ $p->anulado ? 'pago-anulado' : '' }}">
        <td>
            <span class="recibo-num" style="font-family:monospace;font-weight:700;color:var(--color-principal);">{{ $p->numero_recibo }}</span>
            @if($p->anulado)
            <span style="display:block;font-size:.72rem;color:#dc2626;font-weight:600;">ANULADO</span>
            @endif
        </td>
        <td>
            <div style="font-weight:600;color:#1c2b22;">{{ $p->paciente->nombre_completo }}</div>
            <div style="font-size:.74rem;color:#9ca3af;">{{ $p->paciente->numero_documento }}</div>
        </td>
        <td style="max-width:200px;">
            <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $p->concepto }}">
                {{ $p->concepto }}
            </span>
            @if($p->tratamiento)
            <span style="font-size:.72rem;color:var(--color-principal);">{{ $p->tratamiento->nombre }}</span>
            @endif
        </td>
        <td style="font-weight:700;color:#166534;white-space:nowrap;">
            $ {{ number_format($p->valor, 0, ',', '.') }}
        </td>
        <td>
            @php
                $badgeClass = match($p->metodo_pago) {
                    'efectivo'        => 'badge-efectivo',
                    'transferencia'   => 'badge-transferencia',
                    'tarjeta_credito','tarjeta_debito' => 'badge-tarjeta',
                    'cheque'          => 'badge-cheque',
                    default           => 'badge-otro',
                };
            @endphp
            <span class="badge-metodo {{ $badgeClass }}">{{ $p->metodo_pago_label }}</span>
        </td>
        <td style="white-space:nowrap;color:#4b5563;font-size:.83rem;">
            {{ $p->fecha_pago->translatedFormat('d M Y') }}
        </td>
        <td style="font-size:.82rem;color:#6b7280;">{{ $p->cajero?->name ?? '—' }}</td>
        <td>
            <div style="display:flex;justify-content:center;gap:.3rem;">
                <a href="{{ route('pagos.show', $p) }}" class="tbl-btn-accion" title="Ver detalle">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('pagos.recibo', $p) }}" class="tbl-btn-accion success" title="Ver recibo PDF" target="_blank">
                    <i class="bi bi-file-pdf"></i>
                </a>
                @if(!$p->anulado)
                <button type="button" class="tbl-btn-accion danger" title="Anular pago"
                        onclick="abrirAnulacion({{ $p->id }}, '{{ $p->numero_recibo }}')">
                    <i class="bi bi-x-circle"></i>
                </button>
                @endif
            </div>
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

{{-- Modal anulación --}}
<div id="modal-anulacion" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;padding:1.75rem;width:100%;max-width:420px;margin:1rem;box-shadow:0 20px 60px rgba(0,0,0,.3);">
        <h5 style="font-family:var(--fuente-titulos);color:#1c2b22;margin:0 0 .3rem;">Anular Pago</h5>
        <p style="font-size:.84rem;color:#9ca3af;margin:0 0 1rem;">Recibo: <strong id="modal-anulacion-recibo" style="color:var(--color-principal);"></strong></p>
        <form id="form-anulacion" method="POST">
            @csrf
            <label style="font-size:.82rem;font-weight:700;color:var(--color-hover);display:block;margin-bottom:.3rem;">
                Motivo de anulación <span style="color:#dc2626;">*</span>
            </label>
            <textarea name="motivo_anulacion" rows="3" required
                style="width:100%;border:1.5px solid var(--color-muy-claro);border-radius:8px;padding:.5rem .75rem;font-size:.875rem;resize:vertical;outline:none;"
                placeholder="Indique el motivo…"></textarea>
            <div style="display:flex;gap:.5rem;margin-top:1rem;justify-content:flex-end;">
                <button type="button" onclick="cerrarAnulacion()"
                    style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit" class="btn-morado" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                    <i class="bi bi-x-circle"></i> Anular pago
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function abrirAnulacion(id, recibo) {
    document.getElementById('modal-anulacion-recibo').textContent = recibo;
    document.getElementById('form-anulacion').action = '/pagos/' + id + '/anular';
    document.getElementById('modal-anulacion').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function cerrarAnulacion() {
    document.getElementById('modal-anulacion').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) { if(e.key === 'Escape') cerrarAnulacion(); });
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modal-anulacion').addEventListener('click', function(e) {
        if(e.target === this) cerrarAnulacion();
    });
});
</script>
@endpush
