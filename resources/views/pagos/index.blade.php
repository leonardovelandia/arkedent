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

    /* Inline text helpers */
    .pago-pac-nombre { font-weight:600; }
    .pago-pac-doc    { font-size:.74rem; }
    .pago-valor      { font-weight:700; white-space:nowrap; }
    .pago-fecha      { white-space:nowrap; font-size:.83rem; }
    .pago-cajero     { font-size:.82rem; }
    body:not([data-ui="glass"]) .pago-pac-nombre { color:#1c2b22; }
    body:not([data-ui="glass"]) .pago-pac-doc    { color:#9ca3af; }
    body:not([data-ui="glass"]) .pago-valor      { color:#166534; }
    body:not([data-ui="glass"]) .pago-fecha      { color:#4b5563; }
    body:not([data-ui="glass"]) .pago-cajero     { color:#6b7280; }

    /* Clásico – summary cards */
    body:not([data-ui="glass"]) .resumen-card { background:#fff; border:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .resumen-card-valor { color:#1c2b22; }
    body:not([data-ui="glass"]) .badge-efectivo      { background:#D4EDDA; color:#155724; }
    body:not([data-ui="glass"]) .badge-transferencia { background:#D1ECF1; color:#0c5460; }
    body:not([data-ui="glass"]) .badge-tarjeta       { background:#E8D5FF; color:var(--color-sidebar-2); }
    body:not([data-ui="glass"]) .badge-cheque        { background:#FFE5CC; color:#7C2D12; }
    body:not([data-ui="glass"]) .badge-otro          { background:#F3F4F6; color:#374151; }

    /* Modal anulación */
    .modal-anulacion-box { border-radius:14px; padding:1.75rem; width:100%; max-width:420px; margin:1rem; }
    body:not([data-ui="glass"]) .modal-anulacion-box { background:#fff; box-shadow:0 20px 60px rgba(0,0,0,.3); }

    /* Glass */
    body[data-ui="glass"] .resumen-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .resumen-card-label { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .resumen-card-valor { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .resumen-card-sub   { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .pago-pac-nombre { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .pago-pac-doc    { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .pago-valor      { color:#86efac !important; }
    body[data-ui="glass"] .pago-fecha      { color:rgba(255,255,255,0.88) !important; }
    body[data-ui="glass"] .pago-cajero     { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .badge-efectivo      { background:rgba(74,222,128,0.20) !important; color:#86efac !important; border:1px solid rgba(74,222,128,0.35) !important; }
    body[data-ui="glass"] .badge-transferencia { background:rgba(0,234,255,0.12) !important; color:rgba(0,234,255,0.90) !important; border:1px solid rgba(0,234,255,0.30) !important; }
    body[data-ui="glass"] .badge-tarjeta       { background:rgba(167,139,250,0.15) !important; color:#c4b5fd !important; border:1px solid rgba(167,139,250,0.30) !important; }
    body[data-ui="glass"] .badge-cheque        { background:rgba(251,191,36,0.15) !important; color:#fbbf24 !important; border:1px solid rgba(251,191,36,0.30) !important; }
    body[data-ui="glass"] .badge-otro          { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.55) !important; border:1px solid rgba(255,255,255,0.15) !important; }
    body[data-ui="glass"] .modal-anulacion-box { background:rgba(13,30,50,0.95) !important; backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.35) !important; box-shadow:0 20px 60px rgba(0,0,0,.5) !important; }
    body[data-ui="glass"] .modal-anulacion-box h5 { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .modal-anulacion-box p  { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .modal-anulacion-box label { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .modal-anulacion-box textarea { background:rgba(255,255,255,0.08) !important; border:1px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
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
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
        <x-boton-exportar
            modulo="pagos"
            ruta="{{ route('exportar.pagos') }}"
            :tieneSensibles="true"
            labelSensibles="Incluir nombre del paciente, detalle del tratamiento e historial completo"
            advertenciaSensibles="Incluye información financiera y datos personales del paciente."
        />
        <a href="{{ route('pagos.create') }}" class="btn-morado">
            <i class="bi bi-plus-circle"></i> Registrar Pago
        </a>
    </div>
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
            <div class="pago-pac-nombre">{{ $p->paciente->nombre_completo }}</div>
            <div class="pago-pac-doc">{{ $p->paciente->numero_documento }}</div>
        </td>
        <td style="max-width:200px;">
            <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $p->concepto }}">
                {{ $p->concepto }}
            </span>
            @if($p->tratamiento)
            <span style="font-size:.72rem;color:var(--color-principal);">{{ $p->tratamiento->nombre }}</span>
            @endif
        </td>
        <td class="pago-valor">
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
        <td class="pago-fecha">
            {{ $p->fecha_pago->translatedFormat('d M Y') }}
        </td>
        <td class="pago-cajero">{{ $p->cajero?->name ?? '—' }}</td>
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
    <div class="modal-anulacion-box">
        <h5 style="font-family:var(--fuente-titulos);margin:0 0 .3rem;">Anular Pago</h5>
        <p style="font-size:.84rem;margin:0 0 1rem;">Recibo: <strong id="modal-anulacion-recibo" style="color:var(--color-principal);"></strong></p>
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
