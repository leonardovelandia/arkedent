@extends('layouts.app')
@section('titulo', 'Egresos')

@push('estilos')
<style>
    .metricas-egresos { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.25rem; }
    @media(max-width:900px){ .metricas-egresos{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:500px){ .metricas-egresos{ grid-template-columns:1fr 1fr; } }
    .metrica-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1.1rem 1.25rem; display:flex; flex-direction:column; gap:.6rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .metrica-header { display:flex; align-items:center; justify-content:space-between; }
    .metrica-label { font-size:.72rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; }
    .metrica-numero { font-family:var(--fuente-titulos); font-size:1.5rem; font-weight:600; line-height:1; }
    .metrica-sub { font-size:.75rem; color:#8fa39a; }
    .metrica-icono { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:1rem; }
    .icono-rojo    { background:#fde8e8; color:#DC3545; }
    .icono-naranja { background:#fff3e0; color:#FD7E14; }
    .icono-azul    { background:#dbeafe; color:#1e40af; }
    .icono-gris    { background:#f3f4f6; color:#6C757D; }
    .badge-metodo { display:inline-block; font-size:.7rem; font-weight:600; padding:.15rem .55rem; border-radius:50px; }
    .metodo-efectivo     { background:#dcfce7; color:#166534; }
    .metodo-transferencia{ background:#dbeafe; color:#1e40af; }
    .metodo-tarjeta_credito,.metodo-tarjeta_debito { background:var(--color-muy-claro); color:var(--color-principal); }
    .metodo-cheque       { background:#fff3e0; color:#e65100; }
    .metodo-otro         { background:#f3f4f6; color:#6C757D; }
    .tbl-table tr.egreso-anulado td { opacity:.55; }
    .tbl-table tr.egreso-anulado .concepto-cell { text-decoration:line-through; }
</style>
@endpush

@section('contenido')

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo"><i class="bi bi-arrow-down-circle" style="color:#DC3545;"></i> Egresos</h1>
        <p class="page-subtitulo">{{ now()->locale('es')->isoFormat('MMMM [de] YYYY') }}</p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
        <a href="{{ route('egresos.recurrentes') }}"
           style="display:inline-flex;align-items:center;gap:.4rem;background:#FD7E14;color:#fff;border:none;border-radius:8px;padding:.5rem 1rem;font-size:.85rem;font-weight:600;text-decoration:none;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
            <i class="bi bi-arrow-repeat"></i> Recurrentes
        </a>
        <a href="{{ route('egresos.create') }}"
           style="display:inline-flex;align-items:center;gap:.4rem;background:#DC3545;color:#fff;border:none;border-radius:8px;padding:.5rem 1rem;font-size:.85rem;font-weight:600;text-decoration:none;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
            <i class="bi bi-plus-lg"></i> Registrar Egreso
        </a>
    </div>
</div>

@if(session('success'))
<div class="alerta-flash" style="background:#dcfce7;color:#166534;border:1px solid #86efac;">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alerta-flash" style="background:#fde8e8;color:#DC3545;border:1px solid #fca5a5;">
    <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
</div>
@endif

@if($recurrentesPendientes > 0)
<div style="background:#fff3cd;border:1px solid #ffc107;border-radius:10px;padding:.875rem 1.25rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
    <i class="bi bi-clock-history" style="color:#856404;font-size:1.1rem;"></i>
    <strong style="color:#856404;">{{ $recurrentesPendientes }} egreso(s) recurrente(s) pendientes de registrar este mes</strong>
    <a href="{{ route('egresos.recurrentes') }}" style="margin-left:.5rem;color:#856404;font-weight:600;">Ver →</a>
</div>
@endif

{{-- Métricas --}}
<div class="metricas-egresos">
    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Total egresos del mes</span>
            <div class="metrica-icono icono-rojo"><i class="bi bi-arrow-down-circle"></i></div>
        </div>
        <div class="metrica-numero" style="color:#DC3545;">$ {{ number_format($totalMes, 0, ',', '.') }}</div>
        <div class="metrica-sub">{{ now()->locale('es')->isoFormat('MMMM YYYY') }}</div>
    </div>
    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Egresos fijos</span>
            <div class="metrica-icono icono-naranja"><i class="bi bi-repeat"></i></div>
        </div>
        <div class="metrica-numero" style="color:#FD7E14;">$ {{ number_format($fijossMes, 0, ',', '.') }}</div>
        <div class="metrica-sub">Gastos recurrentes fijos</div>
    </div>
    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Egresos variables</span>
            <div class="metrica-icono icono-azul"><i class="bi bi-shuffle"></i></div>
        </div>
        <div class="metrica-numero" style="color:#1e40af;">$ {{ number_format($variablesMes, 0, ',', '.') }}</div>
        <div class="metrica-sub">Gastos no recurrentes</div>
    </div>
    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Registros del mes</span>
            <div class="metrica-icono icono-gris"><i class="bi bi-receipt"></i></div>
        </div>
        <div class="metrica-numero" style="color:#6C757D;">{{ $countMes }}</div>
        <div class="metrica-sub">Egresos registrados</div>
    </div>
</div>

<x-tabla-listado
    :paginacion="$egresos"
    placeholder="Buscar por concepto..."
    icono-vacio="bi-inbox"
    mensaje-vacio="No se encontraron egresos"
>
    <x-slot:filtros>
        <select name="categoria_id" class="tbl-filtro-select">
            <option value="">Todas las categorías</option>
            @foreach($categorias as $cat)
            <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
            @endforeach
        </select>
        <select name="metodo_pago" class="tbl-filtro-select">
            <option value="">Todos los métodos</option>
            <option value="efectivo"        {{ request('metodo_pago')=='efectivo'        ? 'selected':'' }}>Efectivo</option>
            <option value="transferencia"   {{ request('metodo_pago')=='transferencia'   ? 'selected':'' }}>Transferencia</option>
            <option value="tarjeta_credito" {{ request('metodo_pago')=='tarjeta_credito' ? 'selected':'' }}>T. Crédito</option>
            <option value="tarjeta_debito"  {{ request('metodo_pago')=='tarjeta_debito'  ? 'selected':'' }}>T. Débito</option>
            <option value="cheque"          {{ request('metodo_pago')=='cheque'          ? 'selected':'' }}>Cheque</option>
            <option value="otro"            {{ request('metodo_pago')=='otro'            ? 'selected':'' }}>Otro</option>
        </select>
        <input type="date" name="desde" class="tbl-filtro-date" value="{{ request('desde') }}" title="Desde">
        <input type="date" name="hasta" class="tbl-filtro-date" value="{{ request('hasta') }}" title="Hasta">
        <label style="display:inline-flex;align-items:center;gap:.35rem;font-size:.82rem;color:#374151;cursor:pointer;white-space:nowrap;">
            <input type="checkbox" name="solo_recurrentes" value="1" {{ request('solo_recurrentes') ? 'checked' : '' }}
                style="width:15px;height:15px;accent-color:#FD7E14;" onchange="this.form.submit()">
            Solo recurrentes
        </label>
    </x-slot:filtros>

    <x-slot:thead>
        <tr>
            <th>N° Egreso</th>
            <th>Categoría</th>
            <th>Concepto</th>
            <th>Valor</th>
            <th>Método</th>
            <th>Fecha</th>
            <th style="text-align:center;">Rec.</th>
            <th style="text-align:center;">Doc.</th>
            <th>Estado</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

    @foreach($egresos as $egreso)
    <tr class="{{ $egreso->anulado ? 'egreso-anulado' : '' }}">
        <td>
            <span style="display:inline-block;font-size:.72rem;font-weight:700;font-family:monospace;padding:.2rem .55rem;border-radius:6px;background:#fde8e8;color:#DC3545;">
                {{ $egreso->numero_egreso }}
            </span>
        </td>
        <td>
            @if($egreso->categoria)
            <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.78rem;font-weight:600;padding:.2rem .6rem;border-radius:50px;background:{{ $egreso->categoria->color }}22;color:{{ $egreso->categoria->color }};">
                @if($egreso->categoria->icono)<i class="{{ $egreso->categoria->icono }}"></i>@endif
                {{ $egreso->categoria->nombre }}
            </span>
            @else
            <span style="color:#9ca3af;font-size:.78rem;">Sin categoría</span>
            @endif
        </td>
        <td class="concepto-cell">
            <span style="font-weight:500;color:#1c2b22;">{{ $egreso->concepto }}</span>
        </td>
        <td style="font-weight:700;color:#DC3545;font-family:var(--fuente-titulos);white-space:nowrap;">
            {{ $egreso->valor_formateado }}
        </td>
        <td>
            <span class="badge-metodo metodo-{{ $egreso->metodo_pago }}">
                {{ $egreso->metodo_pago_label }}
            </span>
        </td>
        <td style="white-space:nowrap;font-size:.78rem;color:#6b7280;">
            {{ $egreso->fecha_egreso?->format('d/m/Y') }}
        </td>
        <td style="text-align:center;">
            @if($egreso->es_recurrente)
            <i class="bi bi-arrow-repeat" style="color:#FD7E14;" title="Recurrente: {{ $egreso->frecuencia_label }}"></i>
            @endif
        </td>
        <td style="text-align:center;">
            @if($egreso->comprobante_path)
            <i class="bi bi-paperclip" style="color:#1e40af;" title="Tiene comprobante"></i>
            @endif
        </td>
        <td>
            @if($egreso->anulado)
            <span style="display:inline-block;font-size:.68rem;font-weight:700;padding:.15rem .5rem;border-radius:50px;background:#fde8e8;color:#DC3545;">
                <i class="bi bi-x-circle"></i> Anulado
            </span>
            @else
            <span style="display:inline-block;font-size:.68rem;font-weight:700;padding:.15rem .5rem;border-radius:50px;background:#dcfce7;color:#166534;">
                <i class="bi bi-check-circle"></i> Activo
            </span>
            @endif
        </td>
        <td>
            <div style="display:flex;justify-content:center;gap:.3rem;flex-wrap:nowrap;">
                <a href="{{ route('egresos.show', $egreso) }}" class="tbl-btn-accion" title="Ver">
                    <i class="bi bi-eye"></i>
                </a>
                @if(!$egreso->anulado)
                <a href="{{ route('egresos.edit', $egreso) }}" class="tbl-btn-accion" title="Editar">
                    <i class="bi bi-pencil"></i>
                </a>
                <button type="button" class="tbl-btn-accion danger"
                    onclick="abrirAnular({{ $egreso->id }}, '{{ addslashes($egreso->concepto) }}')"
                    title="Anular">
                    <i class="bi bi-slash-circle"></i>
                </button>
                @endif
                @if($egreso->comprobante_path)
                <a href="{{ asset('storage/' . $egreso->comprobante_path) }}" target="_blank"
                   class="tbl-btn-accion" title="Ver comprobante">
                    <i class="bi bi-file-earmark"></i>
                </a>
                @endif
            </div>
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

{{-- Modal Anular --}}
<div id="modal-anular" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:440px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative;margin:1rem;">
        <button onclick="cerrarAnular()" style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;">✕</button>
        <h5 style="font-weight:700;color:#1c2b22;margin-bottom:.35rem;display:flex;align-items:center;gap:.4rem;">
            <i class="bi bi-slash-circle" style="color:#DC3545;"></i> Anular egreso
        </h5>
        <p id="anular-concepto" style="font-size:.85rem;color:#6b7280;margin-bottom:1rem;"></p>
        <form id="form-anular" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="font-size:.8rem;font-weight:600;color:#374151;display:block;margin-bottom:.3rem;">
                    Motivo de anulación <span style="color:#DC3545;">*</span>
                </label>
                <textarea name="motivo_anulacion" rows="3" required
                    style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.875rem;outline:none;resize:vertical;font-family:inherit;"
                    placeholder="Ej: Egreso registrado por error..."></textarea>
            </div>
            <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                <button type="button" onclick="cerrarAnular()"
                    style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit"
                    style="background:#DC3545;color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:.3rem;">
                    <i class="bi bi-slash-circle"></i> Confirmar anulación
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function abrirAnular(id, concepto) {
    document.getElementById('anular-concepto').textContent = 'Egreso: ' + concepto;
    document.getElementById('form-anular').action = '/egresos/' + id + '/anular';
    document.getElementById('modal-anular').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function cerrarAnular() {
    document.getElementById('modal-anular').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) { if(e.key==='Escape') cerrarAnular(); });
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modal-anular').addEventListener('click', function(e) {
        if(e.target===this) cerrarAnular();
    });
});
</script>
@endpush
