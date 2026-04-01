{{-- ============================================================
     VISTA: Egresos Recurrentes
     Sistema: Arkevix Dental ERP
     Layout: layouts.app
     ============================================================ --}}
@extends('layouts.app')
@section('titulo', 'Egresos Recurrentes')

@push('estilos')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; gap:1rem; flex-wrap:wrap; }
    .page-header h4 { font-family:var(--fuente-titulos); font-weight:700; color:#1c2b22; margin:0; font-size:1.4rem; }

    .tabla-container { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1.25rem; }
    .tabla-header { padding:.85rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
    .tabla-titulo { font-size:.82rem; font-weight:700; color:var(--color-hover); display:flex; align-items:center; gap:.4rem; }
    .tabla-titulo i { color:#FD7E14; }

    .tabla-rec { width:100%; border-collapse:collapse; font-size:.83rem; }
    .tabla-rec th { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#8fa39a; padding:.65rem .9rem; border-bottom:2px solid var(--fondo-borde); text-align:left; white-space:nowrap; }
    .tabla-rec td { padding:.65rem .9rem; border-bottom:1px solid var(--fondo-borde); color:#374151; vertical-align:middle; }
    .tabla-rec tr:last-child td { border-bottom:none; }
    .tabla-rec tr:hover td { background:var(--fondo-card-alt,#f9fafb); }

    .fecha-vencida  { color:#DC3545; font-weight:700; }
    .fecha-proxima  { color:#FD7E14; font-weight:700; }
    .fecha-futura   { color:#166534; font-weight:600; }

    .badge-frecuencia { display:inline-block; font-size:.72rem; font-weight:600; padding:.2rem .6rem; border-radius:50px; background:#fff3e0; color:#e65100; }

    .btn-registrar { display:inline-flex; align-items:center; gap:.3rem; background:#DC3545; color:#fff; border:none; border-radius:6px; padding:.35rem .75rem; font-size:.78rem; font-weight:600; cursor:pointer; text-decoration:none; transition:filter .15s; }
    .btn-registrar:hover { filter:brightness(.9); color:#fff; }
    .btn-ver { display:inline-flex; align-items:center; gap:.3rem; background:var(--color-muy-claro); color:var(--color-principal); border:none; border-radius:6px; padding:.35rem .6rem; font-size:.78rem; text-decoration:none; }

    .empty-state { padding:3rem 1rem; text-align:center; color:#9ca3af; }
    .empty-state i { font-size:2.5rem; display:block; margin-bottom:.75rem; }
    .empty-state p { font-size:.85rem; margin:0; }
</style>
@endpush

@section('contenido')

<div class="page-header">
    <div>
        <h4><i class="bi bi-arrow-repeat" style="color:#FD7E14;margin-right:.6rem;"></i>Egresos Recurrentes</h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">Gastos periódicos del consultorio — {{ now()->locale('es')->isoFormat('MMMM [de] YYYY') }}</p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
        <a href="{{ route('egresos.index') }}"
           style="display:inline-flex;align-items:center;gap:.4rem;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem 1rem;font-size:.85rem;font-weight:500;text-decoration:none;">
            <i class="bi bi-arrow-left"></i> Volver a Egresos
        </a>
        <a href="{{ route('egresos.create') }}"
           style="display:inline-flex;align-items:center;gap:.4rem;background:#DC3545;color:#fff;border:none;border-radius:8px;padding:.5rem 1rem;font-size:.85rem;font-weight:600;text-decoration:none;">
            <i class="bi bi-plus-lg"></i> Registrar Egreso
        </a>
    </div>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #86efac;border-radius:10px;padding:.75rem 1.1rem;margin-bottom:1rem;color:#166534;font-size:.85rem;display:flex;align-items:center;gap:.5rem;">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif

{{-- Leyenda de colores de fechas --}}
<div style="background:#fff;border:1px solid var(--fondo-borde);border-radius:10px;padding:.75rem 1.25rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;font-size:.78rem;">
    <strong style="color:#374151;">Estado de próximo pago:</strong>
    <span><span class="fecha-vencida">● Vencido</span> — La fecha ya pasó</span>
    <span><span class="fecha-proxima">● Esta semana</span> — Vence en los próximos 7 días</span>
    <span><span class="fecha-futura">● Al día</span> — Aún no vence</span>
</div>

@php
    $pendientes = $recurrentes->filter(fn($r) => !$r->proxima_fecha || $r->proxima_fecha->lte($hoy->copy()->endOfMonth()));
    $alDia      = $recurrentes->filter(fn($r) => $r->proxima_fecha && $r->proxima_fecha->gt($hoy->copy()->endOfMonth()));
@endphp

{{-- Tabla: Pendientes / Por vencer este mes --}}
<div class="tabla-container">
    <div class="tabla-header">
        <div class="tabla-titulo">
            <i class="bi bi-exclamation-triangle"></i>
            Pendientes o por vencer este mes ({{ $pendientes->count() }})
        </div>
    </div>

    @if($pendientes->count() > 0)
    <div style="overflow-x:auto;">
    <table class="tabla-rec">
        <thead>
            <tr>
                <th>Categoría</th>
                <th>Concepto</th>
                <th>Valor</th>
                <th>Frecuencia</th>
                <th>Próxima fecha</th>
                <th style="text-align:center;">Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendientes as $egreso)
            @php
                $proxima     = $egreso->proxima_fecha;
                $claseProx   = !$proxima ? 'fecha-vencida' : ($proxima->isPast() ? 'fecha-vencida' : ($proxima->diffInDays() <= 7 ? 'fecha-proxima' : 'fecha-futura'));
                $textoProx   = $proxima ? $proxima->locale('es')->isoFormat('D [de] MMMM') : 'Sin fecha definida';
            @endphp
            <tr>
                <td>
                    @if($egreso->categoria)
                    <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.75rem;font-weight:600;padding:.15rem .5rem;border-radius:50px;background:{{ $egreso->categoria->color }}22;color:{{ $egreso->categoria->color }};">
                        @if($egreso->categoria->icono)<i class="{{ $egreso->categoria->icono }}"></i>@endif
                        {{ $egreso->categoria->nombre }}
                    </span>
                    @else
                    <span style="color:#9ca3af;font-size:.78rem;">Sin categoría</span>
                    @endif
                </td>
                <td>
                    <span style="font-weight:600;color:#1c2b22;">{{ $egreso->concepto }}</span>
                    <div style="font-size:.72rem;color:#9ca3af;font-family:monospace;">{{ $egreso->numero_egreso }}</div>
                </td>
                <td style="font-weight:700;color:#DC3545;">{{ $egreso->valor_formateado }}</td>
                <td><span class="badge-frecuencia">{{ $egreso->frecuencia_label }}</span></td>
                <td>
                    <span class="{{ $claseProx }}">
                        @if(!$proxima)<i class="bi bi-exclamation-circle"></i>@endif
                        {{ $textoProx }}
                    </span>
                    @if($proxima && $proxima->isPast())
                    <span style="font-size:.7rem;color:#DC3545;display:block;">
                        Vencido hace {{ abs($proxima->diffInDays()) }} día(s)
                    </span>
                    @endif
                </td>
                <td style="text-align:center;white-space:nowrap;">
                    <form method="POST" action="{{ route('egresos.registrar-recurrente', $egreso) }}"
                        style="display:inline;"
                        onsubmit="return confirm('¿Registrar pago de este mes para: {{ addslashes($egreso->concepto) }}?')">
                        @csrf
                        <button type="submit" class="btn-registrar">
                            <i class="bi bi-check-circle"></i> Registrar pago
                        </button>
                    </form>
                    <a href="{{ route('egresos.show', $egreso) }}" class="btn-ver" style="margin-left:.25rem;">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    @else
    <div class="empty-state">
        <i class="bi bi-check-circle" style="color:#166534;"></i>
        <p>No hay egresos recurrentes pendientes para este mes. ¡Todo al día!</p>
    </div>
    @endif
</div>

{{-- Tabla: Al día / Próximos meses --}}
@if($alDia->count() > 0)
<div class="tabla-container">
    <div class="tabla-header">
        <div class="tabla-titulo" style="color:var(--color-hover);">
            <i class="bi bi-calendar-check" style="color:#166534;"></i>
            Próximos meses — Al día ({{ $alDia->count() }})
        </div>
    </div>
    <div style="overflow-x:auto;">
    <table class="tabla-rec">
        <thead>
            <tr>
                <th>Categoría</th>
                <th>Concepto</th>
                <th>Valor</th>
                <th>Frecuencia</th>
                <th>Próxima fecha</th>
                <th style="text-align:center;">Ver</th>
            </tr>
        </thead>
        <tbody>
            @foreach($alDia as $egreso)
            <tr>
                <td>
                    @if($egreso->categoria)
                    <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.75rem;font-weight:600;padding:.15rem .5rem;border-radius:50px;background:{{ $egreso->categoria->color }}22;color:{{ $egreso->categoria->color }};">
                        @if($egreso->categoria->icono)<i class="{{ $egreso->categoria->icono }}"></i>@endif
                        {{ $egreso->categoria->nombre }}
                    </span>
                    @else
                    <span style="color:#9ca3af;font-size:.78rem;">Sin categoría</span>
                    @endif
                </td>
                <td>
                    <span style="font-weight:600;color:#1c2b22;">{{ $egreso->concepto }}</span>
                    <div style="font-size:.72rem;color:#9ca3af;font-family:monospace;">{{ $egreso->numero_egreso }}</div>
                </td>
                <td style="font-weight:700;color:#DC3545;">{{ $egreso->valor_formateado }}</td>
                <td><span class="badge-frecuencia">{{ $egreso->frecuencia_label }}</span></td>
                <td>
                    <span class="fecha-futura">
                        {{ $egreso->proxima_fecha?->locale('es')->isoFormat('D [de] MMMM') }}
                    </span>
                    <span style="font-size:.7rem;color:#166534;display:block;">
                        En {{ $egreso->proxima_fecha?->diffInDays() }} día(s)
                    </span>
                </td>
                <td style="text-align:center;">
                    <a href="{{ route('egresos.show', $egreso) }}" class="btn-ver">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endif

@if($recurrentes->count() === 0)
<div class="tabla-container">
    <div class="empty-state">
        <i class="bi bi-arrow-repeat"></i>
        <p>No hay egresos recurrentes configurados.</p>
        <a href="{{ route('egresos.create') }}"
           style="display:inline-flex;align-items:center;gap:.4rem;background:#DC3545;color:#fff;border:none;border-radius:8px;padding:.5rem 1rem;font-size:.85rem;font-weight:600;text-decoration:none;margin-top:.75rem;">
            <i class="bi bi-plus-lg"></i> Crear primer egreso recurrente
        </a>
    </div>
</div>
@endif

@endsection
