{{-- ============================================================
     VISTA: Detalle de Egreso
     Sistema: Arkevix Dental ERP
     Layout: layouts.app
     ============================================================ --}}
@extends('layouts.app')
@section('titulo', 'Egreso ' . $egreso->numero_egreso)

@push('estilos')
<style>
    .show-header { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1.5rem; margin-bottom:1.25rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
    .show-header-left h2 { font-family:var(--fuente-titulos); font-size:2rem; font-weight:700; color:#DC3545; margin:0; line-height:1; }
    .show-header-left .numero { font-family:monospace; font-size:.85rem; font-weight:700; color:#6b7280; margin-bottom:.25rem; display:block; }
    .show-header-left .concepto { font-size:1.1rem; font-weight:600; color:#1c2b22; margin-top:.5rem; display:block; }

    .show-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1.25rem; }
    .show-card-header { padding:.85rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; gap:.5rem; background:var(--fondo-app,#f9fafb); }
    .show-card-header h6 { font-size:.82rem; font-weight:700; color:var(--color-hover); margin:0; }
    .show-card-header i { color:#DC3545; }
    .show-card-body { padding:1.25rem; }

    .dato-fila { display:grid; grid-template-columns:180px 1fr; gap:.5rem; padding:.55rem 0; border-bottom:1px solid var(--fondo-borde); align-items:start; }
    .dato-fila:last-child { border-bottom:none; }
    .dato-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#8fa39a; padding-top:.1rem; }
    .dato-valor { font-size:.9rem; color:#1c2b22; font-weight:500; }

    .badge-metodo { display:inline-block; font-size:.78rem; font-weight:600; padding:.2rem .7rem; border-radius:50px; }
    .metodo-efectivo     { background:#dcfce7; color:#166534; }
    .metodo-transferencia{ background:#dbeafe; color:#1e40af; }
    .metodo-tarjeta_credito,.metodo-tarjeta_debito { background:var(--color-muy-claro); color:var(--color-principal); }
    .metodo-cheque       { background:#fff3e0; color:#e65100; }
    .metodo-otro         { background:#f3f4f6; color:#6C757D; }

    .btn-accion { display:inline-flex; align-items:center; gap:.35rem; padding:.5rem 1rem; border-radius:8px; font-size:.875rem; font-weight:600; text-decoration:none; border:none; cursor:pointer; transition:filter .15s; }
    .btn-accion:hover { filter:brightness(.9); }
    .btn-editar  { background:#FD7E14; color:#fff; }
    .btn-anular  { background:#DC3545; color:#fff; }
    .btn-volver  { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }
</style>
@endpush

@section('contenido')

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #86efac;border-radius:10px;padding:.75rem 1.1rem;margin-bottom:1rem;color:#166534;font-size:.85rem;display:flex;align-items:center;gap:.5rem;">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif

{{-- Banner anulado --}}
@if($egreso->anulado)
<div style="background:#fde8e8;border:1px solid #fca5a5;border-radius:10px;padding:.875rem 1.25rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
    <i class="bi bi-slash-circle-fill" style="color:#DC3545;font-size:1.2rem;"></i>
    <div>
        <strong style="color:#DC3545;display:block;">Este egreso ha sido ANULADO</strong>
        <span style="font-size:.82rem;color:#DC3545;">Motivo: {{ $egreso->motivo_anulacion }}</span>
    </div>
</div>
@endif

{{-- Header --}}
<div class="show-header">
    <div class="show-header-left">
        <span class="numero"><i class="bi bi-hash"></i> {{ $egreso->numero_egreso }}</span>
        <div class="metrica-numero h2" style="font-family:var(--fuente-titulos);font-size:2.2rem;font-weight:700;color:#DC3545;line-height:1;">
            {{ $egreso->valor_formateado }}
        </div>
        <span class="concepto">{{ $egreso->concepto }}</span>
        @if($egreso->categoria)
        <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.8rem;font-weight:600;padding:.25rem .7rem;border-radius:50px;background:{{ $egreso->categoria->color }}22;color:{{ $egreso->categoria->color }};margin-top:.5rem;">
            @if($egreso->categoria->icono)<i class="{{ $egreso->categoria->icono }}"></i>@endif
            {{ $egreso->categoria->nombre }}
        </span>
        @endif
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:flex-start;">
        @if(!$egreso->anulado)
        <a href="{{ route('egresos.edit', $egreso) }}" class="btn-accion btn-editar">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <button type="button" class="btn-accion btn-anular"
            onclick="document.getElementById('modal-anular').style.display='flex';document.body.style.overflow='hidden'">
            <i class="bi bi-slash-circle"></i> Anular
        </button>
        @endif
        <a href="{{ route('egresos.index') }}" class="btn-accion btn-volver">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

{{-- Card principal --}}
<div class="show-card">
    <div class="show-card-header">
        <i class="bi bi-info-circle"></i>
        <h6>Información del Egreso</h6>
    </div>
    <div class="show-card-body">

        <div class="dato-fila">
            <span class="dato-label">N° Egreso</span>
            <span class="dato-valor" style="font-family:monospace;font-weight:700;color:#DC3545;">{{ $egreso->numero_egreso }}</span>
        </div>

        <div class="dato-fila">
            <span class="dato-label">Categoría</span>
            <span class="dato-valor">
                @if($egreso->categoria)
                <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.82rem;font-weight:600;padding:.2rem .6rem;border-radius:50px;background:{{ $egreso->categoria->color }}22;color:{{ $egreso->categoria->color }};">
                    @if($egreso->categoria->icono)<i class="{{ $egreso->categoria->icono }}"></i>@endif
                    {{ $egreso->categoria->nombre }}
                </span>
                @else
                <span style="color:#9ca3af;">Sin categoría</span>
                @endif
            </span>
        </div>

        <div class="dato-fila">
            <span class="dato-label">Concepto</span>
            <span class="dato-valor">{{ $egreso->concepto }}</span>
        </div>

        @if($egreso->descripcion)
        <div class="dato-fila">
            <span class="dato-label">Descripción</span>
            <span class="dato-valor" style="white-space:pre-line;">{{ $egreso->descripcion }}</span>
        </div>
        @endif

        <div class="dato-fila">
            <span class="dato-label">Valor</span>
            <span class="dato-valor" style="font-size:1.2rem;font-weight:700;color:#DC3545;">{{ $egreso->valor_formateado }}</span>
        </div>

        <div class="dato-fila">
            <span class="dato-label">Método de Pago</span>
            <span class="dato-valor">
                <span class="badge-metodo metodo-{{ $egreso->metodo_pago }}">{{ $egreso->metodo_pago_label }}</span>
            </span>
        </div>

        <div class="dato-fila">
            <span class="dato-label">Fecha</span>
            <span class="dato-valor">{{ $egreso->fecha_egreso?->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</span>
        </div>

        @if($egreso->numero_comprobante)
        <div class="dato-fila">
            <span class="dato-label">N° Comprobante</span>
            <span class="dato-valor">{{ $egreso->numero_comprobante }}</span>
        </div>
        @endif

        @if($egreso->comprobante_path)
        <div class="dato-fila">
            <span class="dato-label">Comprobante</span>
            <span class="dato-valor">
                @php $ext = strtolower(pathinfo($egreso->comprobante_path, PATHINFO_EXTENSION)); @endphp
                @if(in_array($ext, ['jpg','jpeg','png']))
                <div style="margin-top:.25rem;">
                    <img src="{{ asset('storage/' . $egreso->comprobante_path) }}" alt="Comprobante"
                         style="max-width:300px;max-height:200px;border-radius:8px;border:1px solid var(--fondo-borde);object-fit:contain;">
                    <br>
                    <a href="{{ asset('storage/' . $egreso->comprobante_path) }}" target="_blank"
                       style="font-size:.78rem;color:#1e40af;display:inline-flex;align-items:center;gap:.25rem;margin-top:.3rem;">
                        <i class="bi bi-box-arrow-up-right"></i> Ver en tamaño completo
                    </a>
                </div>
                @else
                <a href="{{ asset('storage/' . $egreso->comprobante_path) }}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:.35rem;background:#dbeafe;color:#1e40af;padding:.3rem .75rem;border-radius:6px;font-size:.82rem;font-weight:600;text-decoration:none;">
                    <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                </a>
                @endif
            </span>
        </div>
        @endif

        @if($egreso->es_recurrente)
        <div class="dato-fila" style="background:#fffbf0;border-radius:8px;padding:.75rem;margin:.5rem 0;display:block;border:1px solid #ffc107;">
            <div style="font-size:.75rem;font-weight:700;color:#856404;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem;">
                <i class="bi bi-arrow-repeat"></i> Gasto Recurrente
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;font-size:.85rem;">
                <div>
                    <div style="font-size:.7rem;color:#9ca3af;margin-bottom:.1rem;">Frecuencia</div>
                    <strong>{{ $egreso->frecuencia_label }}</strong>
                </div>
                @if($egreso->dia_recurrente)
                <div>
                    <div style="font-size:.7rem;color:#9ca3af;margin-bottom:.1rem;">Día del mes</div>
                    <strong>Día {{ $egreso->dia_recurrente }}</strong>
                </div>
                @endif
                @if($egreso->proxima_fecha)
                <div>
                    <div style="font-size:.7rem;color:#9ca3af;margin-bottom:.1rem;">Próximo pago</div>
                    <strong style="color:{{ $egreso->proxima_fecha->isPast() ? '#DC3545' : ($egreso->proxima_fecha->diffInDays() <= 7 ? '#FD7E14' : '#166534') }};">
                        {{ $egreso->proxima_fecha->locale('es')->isoFormat('D [de] MMMM') }}
                    </strong>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($egreso->notas)
        <div class="dato-fila">
            <span class="dato-label">Notas</span>
            <span class="dato-valor" style="white-space:pre-line;font-style:italic;color:#6b7280;">{{ $egreso->notas }}</span>
        </div>
        @endif

        <div class="dato-fila">
            <span class="dato-label">Registrado por</span>
            <span class="dato-valor">{{ $egreso->registradoPor?->name ?? 'Sistema' }}</span>
        </div>

        <div class="dato-fila">
            <span class="dato-label">Fecha de registro</span>
            <span class="dato-valor" style="font-size:.82rem;color:#6b7280;">{{ $egreso->created_at?->locale('es')->isoFormat('D [de] MMMM [de] YYYY, HH:mm') }}</span>
        </div>

    </div>
</div>

{{-- Modal Anular --}}
@if(!$egreso->anulado)
<div id="modal-anular" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:440px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative;margin:1rem;">
        <button onclick="document.getElementById('modal-anular').style.display='none';document.body.style.overflow='';"
            style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;">✕</button>
        <h5 style="font-weight:700;color:#1c2b22;margin-bottom:.25rem;display:flex;align-items:center;gap:.4rem;">
            <i class="bi bi-slash-circle" style="color:#DC3545;"></i> Anular egreso
        </h5>
        <p style="font-size:.85rem;color:#6b7280;margin-bottom:1rem;">{{ $egreso->numero_egreso }} — {{ $egreso->concepto }}</p>
        <form method="POST" action="{{ route('egresos.anular', $egreso) }}">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="font-size:.8rem;font-weight:600;color:#374151;display:block;margin-bottom:.3rem;">
                    Motivo <span style="color:#DC3545;">*</span>
                </label>
                <textarea name="motivo_anulacion" rows="3" required
                    style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.875rem;outline:none;resize:vertical;font-family:inherit;"
                    placeholder="Ej: Registrado por error, duplicado..."></textarea>
            </div>
            <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                <button type="button"
                    onclick="document.getElementById('modal-anular').style.display='none';document.body.style.overflow='';"
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

@push('scripts')
<script>
document.addEventListener('keydown', e => {
    if(e.key==='Escape') {
        document.getElementById('modal-anular').style.display='none';
        document.body.style.overflow='';
    }
});
document.getElementById('modal-anular').addEventListener('click', function(e) {
    if(e.target===this) { this.style.display='none'; document.body.style.overflow=''; }
});
</script>
@endpush
@endif

@endsection
