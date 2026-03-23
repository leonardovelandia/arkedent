@extends('layouts.app')
@section('titulo', 'Presupuesto — ' . $presupuesto->numero_formateado)

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; }
    .btn-gris:hover { background:#e5e7eb; }
    .btn-verde { background:linear-gradient(135deg,#16a34a,#15803d); color:#fff; border:none; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; cursor:pointer; }
    .btn-rojo { background:linear-gradient(135deg,#dc2626,#b91c1c); color:#fff; border:none; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; cursor:pointer; }
    .seccion-titulo { background:var(--color-muy-claro); margin:-1.5rem -1.5rem 1rem; padding:.5rem 1.5rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); border-bottom:1px solid var(--color-muy-claro); padding-bottom:.4rem; margin-bottom:.9rem; }
    .canvas-firma { border:2px solid var(--color-principal); border-radius:8px; background:#fff; cursor:crosshair; touch-action:none; display:block; width:100%; height:180px; }
    .alerta-exito { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; border-radius:8px; padding:.75rem 1rem; margin-bottom:1rem; display:none; align-items:center; gap:.5rem; }
    .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1000; display:none; align-items:center; justify-content:center; }
    .modal-box { background:#fff; border-radius:12px; padding:1.5rem; max-width:420px; width:90%; }
</style>
@endpush

@section('contenido')

<div id="alerta-exito" class="alerta-exito">
    <i class="bi bi-check-circle-fill"></i>
    <span id="alerta-texto">Operación realizada correctamente.</span>
</div>

@if(session('exito'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:8px;padding:.7rem 1rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;padding:.7rem 1rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
</div>
@endif

@if($presupuesto->valoracion)
<div style="background:var(--color-muy-claro);border:1px solid var(--color-principal);border-radius:8px;padding:.875rem 1.25rem;margin-bottom:1rem;">
    <i class="bi bi-clipboard2-pulse" style="color:var(--color-principal);"></i>
    <strong style="color:var(--color-principal);">Generado desde valoración:</strong>
    <a href="{{ route('valoraciones.show', $presupuesto->valoracion) }}" style="color:var(--color-principal);">
        {{ $presupuesto->valoracion->numero_formateado }} —
        {{ $presupuesto->valoracion->fecha->format('d/m/Y') }}
    </a>
</div>
@endif

@php
    $color = $presupuesto->estado_color;
    $diasRestantes = $presupuesto->dias_restantes;
@endphp

{{-- Header morado --}}
<div style="background:linear-gradient(135deg,var(--color-principal) 0%,var(--color-sidebar-2) 60%,var(--color-sidebar) 100%);border-radius:14px;padding:1.5rem 1.75rem;color:#fff;margin-bottom:1.5rem;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div style="display:flex;align-items:center;gap:1rem;">
            <a href="{{ route('presupuestos.index') }}"
               style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);border-radius:8px;width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;color:#fff;text-decoration:none;flex-shrink:0;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
                    <h4 style="font-family:var(--fuente-titulos);font-weight:700;margin:0;font-size:1.2rem;">
                        Presupuesto {{ $presupuesto->numero_formateado }}
                    </h4>
                    <span style="background:{{ $color['bg'] }};color:{{ $color['text'] }};padding:.2rem .65rem;border-radius:20px;font-size:.72rem;font-weight:700;">
                        {{ $color['label'] }}
                    </span>
                </div>
                <div style="font-size:.84rem;opacity:.85;margin-top:.35rem;">
                    <i class="bi bi-person"></i> {{ $presupuesto->paciente->nombre_completo }}
                    &nbsp;·&nbsp;
                    <i class="bi bi-journal-medical"></i> {{ $presupuesto->paciente->numero_historia }}
                </div>
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:1.75rem;font-weight:800;font-family:var(--fuente-titulos);">
                $ {{ number_format($presupuesto->total, 0, ',', '.') }}
            </div>
            <div style="font-size:.78rem;opacity:.75;">Total del presupuesto</div>
        </div>
    </div>
</div>

{{-- Documento único --}}
<div style="background:#ffffff;border:1px solid var(--fondo-borde);border-radius:14px;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);overflow:hidden;margin-bottom:1.25rem;">

    {{-- SECCIÓN 1: Datos generales --}}
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--fondo-borde);">
        <div class="seccion-titulo"><i class="bi bi-info-circle"></i> Datos Generales</div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem 1.5rem;">
            <div>
                <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin-bottom:.2rem;">Paciente</div>
                <div style="font-size:.9rem;font-weight:600;color:#1c2b22;">{{ $presupuesto->paciente->nombre_completo }}</div>
                <div style="font-size:.78rem;color:#6b7280;">{{ $presupuesto->paciente->tipo_documento }} {{ $presupuesto->paciente->numero_documento }}</div>
            </div>
            <div>
                <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin-bottom:.2rem;">Historia</div>
                <div style="font-size:.9rem;color:#1c2b22;font-weight:500;">{{ $presupuesto->paciente->numero_historia }}</div>
            </div>
            <div>
                <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin-bottom:.2rem;">Doctor</div>
                <div style="font-size:.9rem;color:#1c2b22;font-weight:500;">{{ $presupuesto->doctor->name }}</div>
            </div>
            <div>
                <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin-bottom:.2rem;">Fecha de generación</div>
                <div style="font-size:.9rem;color:#1c2b22;">{{ $presupuesto->fecha_generacion->format('d/m/Y') }}</div>
            </div>
            <div>
                <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin-bottom:.2rem;">Fecha de vencimiento</div>
                <div style="font-size:.9rem;color:#1c2b22;">{{ $presupuesto->fecha_vencimiento->format('d/m/Y') }}</div>
            </div>
            <div>
                <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;margin-bottom:.2rem;">Días restantes</div>
                <div style="font-size:.9rem;font-weight:600;
                    color:{{ $diasRestantes < 0 ? '#991b1b' : ($diasRestantes <= 5 ? '#c2410c' : ($diasRestantes <= 10 ? '#92400e' : '#166534')) }};">
                    @if($diasRestantes < 0)
                        <i class="bi bi-exclamation-circle-fill"></i> Vencido
                    @else
                        {{ $diasRestantes }} días
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 2: Items --}}
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--fondo-borde);">
        <div class="seccion-titulo"><i class="bi bi-list-ul"></i> Detalle de Procedimientos</div>
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
            <thead>
                <tr style="background:var(--color-muy-claro);">
                    <th style="padding:.5rem .75rem;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);width:30px;">#</th>
                    <th style="padding:.5rem .75rem;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:left;">Procedimiento</th>
                    <th style="padding:.5rem .75rem;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:center;">Diente</th>
                    <th style="padding:.5rem .75rem;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:center;">Cara</th>
                    <th style="padding:.5rem .75rem;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:center;">Cant.</th>
                    <th style="padding:.5rem .75rem;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:right;">Valor Unit.</th>
                    <th style="padding:.5rem .75rem;font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);border-bottom:2px solid var(--color-muy-claro);text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
            @foreach($presupuesto->items as $item)
            <tr style="border-bottom:1px solid var(--fondo-borde);{{ $item->completado ? 'background:#f0fdf4;' : '' }}">
                <td style="padding:.5rem .75rem;text-align:center;color:#9ca3af;font-size:.82rem;">{{ $item->numero_item }}</td>
                <td style="padding:.5rem .75rem;">
                    <div style="font-weight:500;color:#1c2b22;">{{ $item->procedimiento }}</div>
                    @if($item->notas)
                    <div style="font-size:.75rem;color:#9ca3af;">{{ $item->notas }}</div>
                    @endif
                    @if($item->completado)
                    <span style="background:#d4edda;color:#155724;border-radius:20px;padding:.1rem .5rem;font-size:.68rem;font-weight:700;display:inline-block;margin-top:.2rem;">
                        <i class="bi bi-check-circle-fill"></i> Realizado
                    </span>
                    @endif
                </td>
                <td style="padding:.5rem .75rem;text-align:center;color:#4b5563;font-size:.84rem;">{{ $item->diente ?? '—' }}</td>
                <td style="padding:.5rem .75rem;text-align:center;color:#4b5563;font-size:.84rem;">{{ $item->cara ?: '—' }}</td>
                <td style="padding:.5rem .75rem;text-align:center;color:#4b5563;">{{ $item->cantidad }}</td>
                <td style="padding:.5rem .75rem;text-align:right;color:#4b5563;">$ {{ number_format($item->valor_unitario, 0, ',', '.') }}</td>
                <td style="padding:.5rem .75rem;text-align:right;font-weight:600;color:#1c2b22;">$ {{ number_format($item->valor_total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>

    {{-- SECCIÓN 3: Resumen financiero --}}
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--fondo-borde);">
        <div class="seccion-titulo"><i class="bi bi-calculator"></i> Resumen Financiero</div>
        <div style="max-width:320px;margin-left:auto;">
            <div style="display:flex;justify-content:space-between;padding:.4rem 0;font-size:.875rem;color:#6b7280;">
                <span>Subtotal</span>
                <span style="font-weight:600;color:#1c2b22;">$ {{ number_format($presupuesto->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($presupuesto->descuento_valor > 0)
            <div style="display:flex;justify-content:space-between;padding:.4rem 0;font-size:.875rem;color:#6b7280;">
                <span>Descuento ({{ $presupuesto->descuento_porcentaje }}%)</span>
                <span style="font-weight:600;color:#dc2626;">- $ {{ number_format($presupuesto->descuento_valor, 0, ',', '.') }}</span>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;padding:.65rem 0;font-size:1.2rem;font-weight:800;color:var(--color-sidebar-2);border-top:2px solid var(--color-principal);margin-top:.4rem;">
                <span>TOTAL</span>
                <span>$ {{ number_format($presupuesto->total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 4: Condiciones --}}
    @if($presupuesto->condiciones_pago || $presupuesto->observaciones)
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--fondo-borde);">
        <div class="seccion-titulo"><i class="bi bi-file-text"></i> Condiciones y Observaciones</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
            @if($presupuesto->condiciones_pago)
            <div>
                <div style="font-size:.78rem;font-weight:600;text-transform:uppercase;color:#9ca3af;margin-bottom:.4rem;">Condiciones de pago</div>
                <div style="font-size:.875rem;color:#374151;line-height:1.6;background:var(--fondo-card-alt);border-left:3px solid var(--color-principal);border-radius:0 8px 8px 0;padding:.75rem 1rem;">{{ $presupuesto->condiciones_pago }}</div>
            </div>
            @endif
            @if($presupuesto->observaciones)
            <div>
                <div style="font-size:.78rem;font-weight:600;text-transform:uppercase;color:#9ca3af;margin-bottom:.4rem;">Observaciones</div>
                <div style="font-size:.875rem;color:#374151;line-height:1.6;background:var(--fondo-card-alt);border-left:3px solid var(--color-principal);border-radius:0 8px 8px 0;padding:.75rem 1rem;">{{ $presupuesto->observaciones }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- SECCIÓN 5: Estado y acciones --}}
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--fondo-borde);">
        <div class="seccion-titulo"><i class="bi bi-lightning"></i> Acciones</div>

        @if($presupuesto->estado === 'borrador')
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('presupuestos.edit', $presupuesto) }}" class="btn-gris">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <form method="POST" action="{{ route('presupuestos.enviar', $presupuesto) }}" style="display:inline;">
                @csrf
                <button type="submit" style="background:linear-gradient(135deg,#0D6EFD,#0a58ca);color:#fff;border:none;border-radius:8px;padding:.5rem 1.25rem;font-size:.875rem;font-weight:500;display:inline-flex;align-items:center;gap:.4rem;cursor:pointer;">
                    <i class="bi bi-send"></i> Enviar al paciente
                </button>
            </form>
            <a href="{{ route('presupuestos.pdf', $presupuesto) }}" target="_blank" class="btn-gris">
                <i class="bi bi-file-pdf"></i> Ver PDF
            </a>
            <form method="POST" action="{{ route('presupuestos.destroy', $presupuesto) }}" style="display:inline;"
                  onsubmit="return confirm('¿Eliminar este presupuesto borrador?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-rojo">
                    <i class="bi bi-trash3"></i> Eliminar
                </button>
            </form>
        </div>

        @elseif($presupuesto->estado === 'enviado')
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
            <button type="button" class="btn-rojo" onclick="document.getElementById('modal-rechazar').style.display='flex'">
                <i class="bi bi-x-circle"></i> Rechazar
            </button>
            <a href="{{ route('presupuestos.pdf', $presupuesto) }}" target="_blank" class="btn-gris">
                <i class="bi bi-file-pdf"></i> Ver PDF
            </a>
        </div>

        @elseif($presupuesto->estado === 'aprobado')
        <div>
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:1rem 1.25rem;margin-bottom:.75rem;">
                <div style="font-weight:600;color:#166534;margin-bottom:.4rem;"><i class="bi bi-check-circle-fill"></i> Presupuesto aprobado</div>
                @if($presupuesto->tratamiento)
                <div style="font-size:.875rem;color:#374151;">
                    Tratamiento creado:
                    <a href="{{ route('tratamientos.show', $presupuesto->tratamiento) }}"
                       style="color:#166534;font-weight:700;text-decoration:none;">
                        {{ $presupuesto->tratamiento->numero_formateado ?? $presupuesto->tratamiento->nombre }}
                    </a>
                    — Saldo pendiente: <strong>$ {{ number_format($presupuesto->tratamiento->saldo_pendiente, 0, ',', '.') }}</strong>
                </div>
                @endif
                @if($presupuesto->fecha_aprobacion)
                <div style="font-size:.8rem;color:#6b7280;margin-top:.3rem;">Aprobado el {{ $presupuesto->fecha_aprobacion->format('d/m/Y H:i') }}</div>
                @endif
            </div>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                <a href="{{ route('presupuestos.pdf', $presupuesto) }}" target="_blank" class="btn-gris">
                    <i class="bi bi-file-pdf"></i> Ver PDF
                </a>
                @if($presupuesto->tratamiento)
                <a href="{{ route('pagos.create', ['paciente_id' => $presupuesto->paciente_id, 'tratamiento_id' => $presupuesto->tratamiento_id]) }}"
                   class="btn-morado">
                    <i class="bi bi-cash-coin"></i> Registrar Pago
                </a>
                @endif
            </div>
        </div>

        @elseif($presupuesto->estado === 'rechazado')
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:1rem 1.25rem;">
            <div style="font-weight:600;color:#991b1b;margin-bottom:.4rem;"><i class="bi bi-x-circle-fill"></i> Presupuesto rechazado</div>
            @if($presupuesto->motivo_rechazo)
            <div style="font-size:.875rem;color:#374151;">Motivo: {{ $presupuesto->motivo_rechazo }}</div>
            @endif
        </div>

        @elseif($presupuesto->estado === 'vencido')
        <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:1rem 1.25rem;">
            <div style="font-weight:600;color:#c2410c;margin-bottom:.2rem;"><i class="bi bi-clock-history"></i> Presupuesto vencido</div>
            <div style="font-size:.84rem;color:#6b7280;">Venció el {{ $presupuesto->fecha_vencimiento->format('d/m/Y') }}.</div>
        </div>
        @endif
    </div>

    {{-- SECCIÓN 6: Firma --}}
    <div style="padding:1.25rem 1.5rem;">
        <div class="seccion-titulo"><i class="bi bi-pen"></i> Firma del Paciente</div>

        @if($presupuesto->firmado)
        <div style="background:#f0fdf4;border:1px solid #d1fae5;border-radius:8px;padding:1rem;">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem;">
                <span style="background:#d1fae5;color:#166534;padding:.2rem .65rem;border-radius:20px;font-size:.75rem;font-weight:700;">
                    <i class="bi bi-check-circle-fill"></i> FIRMADO
                </span>
                @if($presupuesto->fecha_aprobacion)
                <span style="font-size:.82rem;color:#6b7280;">{{ $presupuesto->fecha_aprobacion->format('d/m/Y \a \l\a\s H:i') }}</span>
                @endif
            </div>
            <img src="{{ $presupuesto->firma_data }}" alt="Firma" style="max-width:300px;max-height:100px;border:1px solid #d1fae5;border-radius:6px;background:#fff;padding:.25rem;">
            @if($presupuesto->ip_firma)
            <p style="font-size:.75rem;color:#9ca3af;margin-top:.5rem;margin-bottom:0;">IP: {{ $presupuesto->ip_firma }}</p>
            @endif
        </div>

        @elseif($presupuesto->estado === 'enviado')
        <div>
            <div style="background:#FFF3CD;border:1px solid #FFC107;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.84rem;color:#856404;">
                <i class="bi bi-info-circle-fill"></i>
                <strong>El paciente debe firmar</strong> para aprobar el presupuesto.
                Yo, <strong>{{ $presupuesto->paciente->nombre_completo }}</strong>, confirmo que he revisado
                el presupuesto {{ $presupuesto->numero_formateado }} por un total de
                <strong>$ {{ number_format($presupuesto->total, 0, ',', '.') }}</strong>
                y doy mi aprobación para iniciar el tratamiento.
            </div>
            <canvas id="canvas-firma" class="canvas-firma"></canvas>
            <div style="display:flex;gap:.5rem;margin-top:.75rem;flex-wrap:wrap;">
                <button type="button" class="btn-gris" onclick="limpiarCanvas()">
                    <i class="bi bi-eraser"></i> Limpiar
                </button>
                <button type="button" class="btn-morado" id="btn-firmar" onclick="confirmarFirma()">
                    <i class="bi bi-check-lg"></i> Firmar y aprobar presupuesto
                </button>
            </div>
        </div>

        @else
        <div style="color:#9ca3af;font-size:.875rem;font-style:italic;">
            <i class="bi bi-info-circle"></i>
            La firma estará disponible cuando el presupuesto sea enviado al paciente.
        </div>
        @endif
    </div>

</div>

{{-- Modal rechazar --}}
<div id="modal-rechazar" class="modal-overlay">
    <div class="modal-box">
        <div style="font-weight:700;font-size:1rem;color:#1c2b22;margin-bottom:1rem;"><i class="bi bi-x-circle" style="color:#dc2626;"></i> Rechazar Presupuesto</div>
        <form method="POST" action="{{ route('presupuestos.rechazar', $presupuesto) }}">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="font-size:.82rem;font-weight:600;color:#374151;display:block;margin-bottom:.35rem;">Motivo del rechazo *</label>
                <textarea name="motivo_rechazo" rows="3" required
                    style="width:100%;border:1.5px solid #e5e7eb;border-radius:8px;padding:.5rem .75rem;font-size:.875rem;resize:vertical;box-sizing:border-box;"
                    placeholder="Explique el motivo del rechazo…"></textarea>
            </div>
            <div style="display:flex;gap:.5rem;">
                <button type="submit" class="btn-rojo">
                    <i class="bi bi-x-circle"></i> Confirmar rechazo
                </button>
                <button type="button" class="btn-gris" onclick="document.getElementById('modal-rechazar').style.display='none'">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    var canvas = document.getElementById('canvas-firma');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');
    var dibujando = false;

    function redimensionar() {
        var ratio = window.devicePixelRatio || 1;
        canvas.width  = canvas.offsetWidth  * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        ctx.scale(ratio, ratio);
        ctx.strokeStyle = '#1c2b22';
        ctx.lineWidth   = 2;
        ctx.lineCap     = 'round';
        ctx.lineJoin    = 'round';
    }
    redimensionar();

    function getPos(e) {
        var r = canvas.getBoundingClientRect();
        var src = e.touches ? e.touches[0] : e;
        return { x: src.clientX - r.left, y: src.clientY - r.top };
    }

    canvas.addEventListener('mousedown',  function(e){ dibujando=true; ctx.beginPath(); var p=getPos(e); ctx.moveTo(p.x,p.y); });
    canvas.addEventListener('mousemove',  function(e){ if(!dibujando) return; var p=getPos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); });
    canvas.addEventListener('mouseup',    function(){ dibujando=false; });
    canvas.addEventListener('mouseleave', function(){ dibujando=false; });
    canvas.addEventListener('touchstart', function(e){ e.preventDefault(); dibujando=true; ctx.beginPath(); var p=getPos(e); ctx.moveTo(p.x,p.y); }, {passive:false});
    canvas.addEventListener('touchmove',  function(e){ e.preventDefault(); if(!dibujando) return; var p=getPos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); }, {passive:false});
    canvas.addEventListener('touchend',   function(){ dibujando=false; });

    window.limpiarCanvas = function() {
        ctx.clearRect(0, 0, canvas.offsetWidth, canvas.offsetHeight);
    };

    window.confirmarFirma = function() {
        var firmaData = canvas.toDataURL('image/png');
        var pixelBuffer = new Uint32Array(ctx.getImageData(0, 0, canvas.width, canvas.height).data.buffer);
        var hasContent = pixelBuffer.some(function(p){ return p !== 0; });
        if (!hasContent) { alert('Por favor dibuje la firma antes de confirmar.'); return; }

        var btn = document.getElementById('btn-firmar');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Guardando…';

        fetch('{{ route('presupuestos.firmar', $presupuesto) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            },
            body: JSON.stringify({ firma_data: firmaData })
        })
        .then(function(r){ return r.json(); })
        .then(function(data) {
            if (data.success) {
                document.getElementById('alerta-texto').textContent = 'Presupuesto firmado y aprobado. Se creó el tratamiento automáticamente.';
                document.getElementById('alerta-exito').style.display = 'flex';
                setTimeout(function(){ window.location.reload(); }, 1500);
            }
        })
        .catch(function(){
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg"></i> Firmar y aprobar presupuesto';
            alert('Error al guardar la firma. Intente de nuevo.');
        });
    };
})();
</script>
@endpush
