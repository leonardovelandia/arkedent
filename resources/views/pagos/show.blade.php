@extends('layouts.app')
@section('titulo', 'Detalle de Pago')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    .pago-header { background:linear-gradient(135deg,var(--color-principal) 0%,var(--color-sidebar-2) 60%,var(--color-sidebar) 100%); border-radius:14px; padding:1.5rem 1.75rem; color:#fff; margin-bottom:1.25rem; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem; }
    .pago-header-recibo { font-family:monospace; font-size:1rem; opacity:.85; margin-bottom:.2rem; }
    .pago-header-valor { font-size:2rem; font-weight:800; }
    .pago-header-sub { font-size:.85rem; opacity:.8; }

    .info-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:1.25rem 1.5rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1rem; }
    .info-card-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-hover); margin-bottom:.9rem; display:flex; align-items:center; gap:.4rem; }
    .dato-row { display:flex; justify-content:space-between; padding:.4rem 0; border-bottom:1px solid var(--fondo-card-alt); font-size:.875rem; }
    .dato-row:last-child { border-bottom:none; }
    .dato-key { color:#6b7280; }
    .dato-val { font-weight:600; color:#1c2b22; }

    .progress-bar-wrap { background:var(--fondo-borde); border-radius:999px; height:10px; margin-top:.4rem; overflow:hidden; }
    .progress-bar-fill { background:linear-gradient(90deg,var(--color-principal),var(--color-claro)); height:100%; border-radius:999px; transition:width .4s; }

    .badge-metodo { display:inline-flex; align-items:center; gap:.3rem; padding:.28rem .75rem; border-radius:20px; font-size:.8rem; font-weight:700; }
    .badge-efectivo   { background:#D4EDDA; color:#155724; }
    .badge-transferencia { background:#D1ECF1; color:#0c5460; }
    .badge-tarjeta    { background:#E8D5FF; color:var(--color-sidebar-2); }
    .badge-cheque     { background:#FFE5CC; color:#7C2D12; }
    .badge-otro       { background:#F3F4F6; color:#374151; }

    /* Clásico */
    body:not([data-ui="glass"]) .info-card { background:#fff; border:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .info-card-titulo { color:var(--color-hover); }
    body:not([data-ui="glass"]) .dato-key { color:#6b7280; }
    body:not([data-ui="glass"]) .dato-val { color:#1c2b22; }
    body:not([data-ui="glass"]) .badge-efectivo   { background:#D4EDDA; color:#155724; }
    body:not([data-ui="glass"]) .badge-transferencia { background:#D1ECF1; color:#0c5460; }
    body:not([data-ui="glass"]) .badge-tarjeta    { background:#E8D5FF; color:var(--color-sidebar-2); }
    body:not([data-ui="glass"]) .badge-cheque     { background:#FFE5CC; color:#7C2D12; }
    body:not([data-ui="glass"]) .badge-otro       { background:#F3F4F6; color:#374151; }
    body:not([data-ui="glass"]) .btn-volver       { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }

    /* Glass */
    body[data-ui="glass"] .info-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .info-card-titulo { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .dato-key { color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .dato-val { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .dato-row { border-bottom-color:rgba(255,255,255,0.06) !important; }
    body[data-ui="glass"] .progress-bar-wrap { background:rgba(255,255,255,0.10) !important; }
    body[data-ui="glass"] .badge-efectivo   { background:rgba(74,222,128,0.20) !important; color:#86efac !important; border:1px solid rgba(74,222,128,0.35) !important; }
    body[data-ui="glass"] .badge-transferencia { background:rgba(0,234,255,0.12) !important; color:rgba(0,234,255,0.90) !important; border:1px solid rgba(0,234,255,0.30) !important; }
    body[data-ui="glass"] .badge-tarjeta    { background:rgba(167,139,250,0.15) !important; color:#c4b5fd !important; border:1px solid rgba(167,139,250,0.30) !important; }
    body[data-ui="glass"] .badge-cheque     { background:rgba(251,191,36,0.20) !important; color:#fbbf24 !important; border:1px solid rgba(251,191,36,0.35) !important; }
    body[data-ui="glass"] .badge-otro       { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.55) !important; border:1px solid rgba(255,255,255,0.15) !important; }
    body[data-ui="glass"] .btn-volver       { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
    body[data-ui="glass"] .saldo-label      { color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .saldo-valor      { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .saldo-pagado     { color:#86efac !important; }
    body[data-ui="glass"] .saldo-pendiente  { color:#fca5a5 !important; }
    body[data-ui="glass"] .modal-anular-inner { background:rgba(5,40,55,0.90) !important; backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.25) !important; box-shadow:0 20px 60px rgba(0,0,0,.5) !important; }
    body[data-ui="glass"] .modal-anular-inner h5 { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .modal-anular-inner p  { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .modal-anular-inner label { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .modal-anular-inner textarea { background:rgba(255,255,255,0.08) !important; border:1px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .btn-modal-close  { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

@if($pago->anulado)
<div style="background:#fef2f2;color:#dc2626;border:1px solid #fecdd3;border-radius:10px;padding:.9rem 1.25rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:.6rem;font-weight:700;">
    <i class="bi bi-x-octagon-fill" style="font-size:1.2rem;"></i>
    PAGO ANULADO — {{ $pago->motivo_anulacion }}
</div>
@endif

{{-- Header --}}
<div class="pago-header">
    <div>
        <div class="pago-header-recibo"><i class="bi bi-receipt"></i> {{ $pago->numero_recibo }}</div>
        <div class="pago-header-valor">$ {{ number_format($pago->valor, 0, ',', '.') }}</div>
        <div class="pago-header-sub">{{ $pago->paciente->nombre_completo }} · {{ $pago->fecha_pago->translatedFormat('d \d\e F \d\e Y') }}</div>
    </div>
    @php
        $badgeClass = match($pago->metodo_pago) {
            'efectivo'        => 'badge-efectivo',
            'transferencia'   => 'badge-transferencia',
            'tarjeta_credito','tarjeta_debito' => 'badge-tarjeta',
            'cheque'          => 'badge-cheque',
            default           => 'badge-otro',
        };
    @endphp
    <span class="badge-metodo {{ $badgeClass }}" style="font-size:.9rem;padding:.4rem 1rem;">
        {{ $pago->metodo_pago_label }}
    </span>
</div>

{{-- Botones --}}
<div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1.25rem;">
    <a href="{{ route('pagos.index') }}"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
    <a href="{{ route('pagos.recibo', $pago) }}" class="btn-morado"
       style="background:linear-gradient(135deg,#166534,#15803d);" target="_blank">
        <i class="bi bi-file-pdf"></i> Ver Recibo PDF
    </a>
    @if(!$pago->anulado)
    <button type="button" class="btn-morado" style="background:linear-gradient(135deg,#dc2626,#b91c1c);"
            onclick="document.getElementById('modal-anulacion').style.display='flex';document.body.style.overflow='hidden'">
        <i class="bi bi-x-circle"></i> Anular pago
    </button>
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
    {{-- Datos del pago --}}
    <div class="info-card">
        <div class="info-card-titulo"><i class="bi bi-cash-coin"></i> Datos del Pago</div>
        <div class="dato-row"><span class="dato-key">Concepto</span><span class="dato-val">{{ $pago->concepto }}</span></div>
        <div class="dato-row"><span class="dato-key">Método</span><span class="dato-val">{{ $pago->metodo_pago_label }}</span></div>
        @if($pago->referencia_pago)
        <div class="dato-row"><span class="dato-key">Referencia</span><span class="dato-val">{{ $pago->referencia_pago }}</span></div>
        @endif
        <div class="dato-row"><span class="dato-key">Fecha</span><span class="dato-val">{{ $pago->fecha_pago->translatedFormat('d \d\e F \d\e Y') }}</span></div>
        <div class="dato-row"><span class="dato-key">Registrado por</span><span class="dato-val">{{ $pago->cajero?->name ?? '—' }}</span></div>
        @if($pago->tratamiento)
        <div class="dato-row"><span class="dato-key">Tratamiento</span><span class="dato-val">{{ $pago->tratamiento->nombre }}</span></div>
        @endif
        @if($pago->observaciones)
        <div class="dato-row"><span class="dato-key">Observaciones</span><span class="dato-val">{{ $pago->observaciones }}</span></div>
        @endif
    </div>

    {{-- Datos del paciente --}}
    <div class="info-card">
        <div class="info-card-titulo"><i class="bi bi-person-circle"></i> Datos del Paciente</div>
        <div class="dato-row"><span class="dato-key">Nombre</span><span class="dato-val">{{ $pago->paciente->nombre_completo }}</span></div>
        <div class="dato-row"><span class="dato-key">Documento</span><span class="dato-val">{{ $pago->paciente->tipo_documento }} {{ $pago->paciente->numero_documento }}</span></div>
        <div class="dato-row"><span class="dato-key">Teléfono</span><span class="dato-val">{{ $pago->paciente->telefono }}</span></div>
        <div style="margin-top:.75rem;">
            <a href="{{ route('pacientes.show', $pago->paciente) }}"
               style="font-size:.82rem;color:var(--color-principal);text-decoration:none;font-weight:600;">
                <i class="bi bi-person-badge"></i> Ver ficha del paciente
            </a>
        </div>
    </div>
</div>

{{-- Saldo del tratamiento --}}
@if($pago->tratamiento)
<div class="info-card">
    <div class="info-card-titulo"><i class="bi bi-clipboard2-pulse"></i> Saldo del Tratamiento</div>
    @php
        $t = $pago->tratamiento;
        $pagado = $t->valor_total - $t->saldo_pendiente;
        $pct = $t->valor_total > 0 ? min(100, round(($pagado / $t->valor_total) * 100)) : 0;
    @endphp
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:.75rem;">
        <div>
            <div style="font-size:.72rem;color:#9ca3af;font-weight:700;text-transform:uppercase;">Valor total</div>
            <div style="font-size:1rem;font-weight:700;color:#1c2b22;">$ {{ number_format($t->valor_total, 0, ',', '.') }}</div>
        </div>
        <div>
            <div style="font-size:.72rem;color:#9ca3af;font-weight:700;text-transform:uppercase;">Total pagado</div>
            <div style="font-size:1rem;font-weight:700;color:#166534;">$ {{ number_format($pagado, 0, ',', '.') }}</div>
        </div>
        <div>
            <div style="font-size:.72rem;color:#9ca3af;font-weight:700;text-transform:uppercase;">Saldo pendiente</div>
            <div style="font-size:1rem;font-weight:700;color:{{ $t->saldo_pendiente > 0 ? '#dc2626' : '#166534' }};">
                $ {{ number_format($t->saldo_pendiente, 0, ',', '.') }}
            </div>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:.75rem;">
        <div class="progress-bar-wrap" style="flex:1;">
            <div class="progress-bar-fill" style="width:{{ $pct }}%;"></div>
        </div>
        <span style="font-size:.8rem;font-weight:700;color:var(--color-principal);">{{ $pct }}%</span>
    </div>
</div>
@endif

{{-- Modal anulación --}}
@if(!$pago->anulado)
<div id="modal-anulacion" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;padding:1.75rem;width:100%;max-width:420px;margin:1rem;box-shadow:0 20px 60px rgba(0,0,0,.3);">
        <h5 style="font-family:var(--fuente-titulos);color:#1c2b22;margin:0 0 .3rem;">Anular Pago</h5>
        <p style="font-size:.84rem;color:#9ca3af;margin:0 0 1rem;">Recibo: <strong style="color:var(--color-principal);">{{ $pago->numero_recibo }}</strong></p>
        <form method="POST" action="{{ route('pagos.anular', $pago) }}">
            @csrf
            <label style="font-size:.82rem;font-weight:700;color:var(--color-hover);display:block;margin-bottom:.3rem;">
                Motivo de anulación <span style="color:#dc2626;">*</span>
            </label>
            <textarea name="motivo_anulacion" rows="3" required
                style="width:100%;border:1.5px solid var(--color-muy-claro);border-radius:8px;padding:.5rem .75rem;font-size:.875rem;resize:vertical;outline:none;"
                placeholder="Indique el motivo…"></textarea>
            <div style="display:flex;gap:.5rem;margin-top:1rem;justify-content:flex-end;">
                <button type="button"
                    onclick="document.getElementById('modal-anulacion').style.display='none';document.body.style.overflow='';"
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
@endif

@endsection
