{{-- ============================================================
     VISTA: Detalle de Importación
     Sistema: Arkedent
     Layout: layouts.app
     ============================================================ --}}
@extends('layouts.dev')
@section('titulo', 'Importación ' . $importacion->numero_importacion)

@push('estilos')
<style>
    .page-header    { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; gap:1rem; flex-wrap:wrap; }
    .page-titulo    { font-family:var(--fuente-titulos); font-weight:700; color:#1c2b22; margin:0; font-size:1.4rem; }
    .page-subtitulo { font-size:.82rem; color:#9ca3af; margin:.15rem 0 0 0; }

    .metricas-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:1rem; margin-bottom:1.25rem; }
    @media(max-width:1000px){ .metricas-grid{ grid-template-columns:repeat(3,1fr); } }
    @media(max-width:600px) { .metricas-grid{ grid-template-columns:repeat(2,1fr); } }

    .metrica-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1rem 1.1rem; display:flex; flex-direction:column; gap:.5rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .metrica-header { display:flex; align-items:center; justify-content:space-between; }
    .metrica-label  { font-size:.68rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; }
    .metrica-numero { font-family:var(--fuente-titulos); font-size:1.6rem; font-weight:700; line-height:1; }
    .metrica-sub    { font-size:.72rem; color:#8fa39a; }
    .metrica-icono  { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:.9rem; flex-shrink:0; }

    .info-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; margin-bottom:1.25rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.08); }
    .info-header { padding:.75rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem; }
    .info-body   { padding:1.25rem; }

    .badge-estado  { display:inline-block; font-size:.75rem; font-weight:700; padding:.3rem .75rem; border-radius:50px; }
    .estado-pendiente  { background:#f3f4f6; color:#6C757D; }
    .estado-procesando { background:#dbeafe; color:#1e40af; }
    .estado-completado { background:#dcfce7; color:#166534; }
    .estado-error      { background:#fde8e8; color:#DC3545; }
    .estado-revertido  { background:#fff3cd; color:#856404; }

    .info-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
    @media(max-width:700px){ .info-grid{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:450px){ .info-grid{ grid-template-columns:1fr; } }

    .info-campo { }
    .info-campo-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#8fa39a; margin-bottom:.25rem; }
    .info-campo-valor { font-size:.88rem; font-weight:600; color:#1c2b22; }

    .progreso-bar  { height:10px; background:#e5e7eb; border-radius:50px; overflow:hidden; margin-bottom:.4rem; }
    .progreso-fill { height:100%; border-radius:50px; transition:width .5s ease; }

    .filtros-tabs { display:flex; gap:.35rem; flex-wrap:wrap; margin-bottom:1rem; }
    .filtro-tab   { display:inline-flex; align-items:center; gap:.3rem; padding:.3rem .75rem; border-radius:50px; font-size:.78rem; font-weight:600; text-decoration:none; border:1px solid var(--fondo-borde); color:#6b7280; background:#fff; transition:all .15s; cursor:pointer; }
    .filtro-tab:hover  { border-color:var(--color-claro); color:var(--color-principal); background:var(--color-muy-claro); }
    .filtro-tab.activo { background:var(--color-principal); color:#fff; border-color:var(--color-principal); }
    .filtro-tab.verde  { }
    .filtro-tab.verde.activo { background:#166534; border-color:#166534; }
    .filtro-tab.naranja.activo { background:#856404; border-color:#856404; }
    .filtro-tab.rojo.activo    { background:#DC3545; border-color:#DC3545; }
    .filtro-tab.gris.activo    { background:#6C757D; border-color:#6C757D; }

    .tabla-container { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .tabla-det { width:100%; border-collapse:collapse; font-size:.8rem; }
    .tabla-det th { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#8fa39a; padding:.6rem .9rem; border-bottom:2px solid var(--fondo-borde); text-align:left; white-space:nowrap; }
    .tabla-det td { padding:.55rem .9rem; border-bottom:1px solid var(--fondo-borde); color:#374151; vertical-align:middle; }
    .tabla-det tr:last-child td { border-bottom:none; }
    .tabla-det tr:hover td { background:var(--fondo-card-alt,#f9fafb); }

    .badge-fila    { font-size:.7rem; font-weight:700; font-family:monospace; padding:.15rem .5rem; border-radius:5px; background:#f3f4f6; color:#6C757D; }
    .estado-importado  { background:#dcfce7; color:#166534; }
    .estado-duplicado  { background:#fff3cd; color:#856404; }
    .estado-error-b    { background:#fde8e8; color:#DC3545; }
    .estado-omitido    { background:#f3f4f6; color:#6C757D; }

    .dato-col { font-size:.73rem; color:#6b7280; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

    .btn-accion { display:inline-flex; align-items:center; gap:.25rem; padding:.28rem .6rem; border-radius:6px; font-size:.75rem; font-weight:500; text-decoration:none; border:1px solid transparent; cursor:pointer; transition:all .15s; }
    .btn-ver    { background:var(--color-muy-claro); color:var(--color-principal); border-color:var(--color-muy-claro); }
    .btn-accion:hover { filter:brightness(.92); }

    .empty-state   { padding:3rem 1rem; text-align:center; color:#9ca3af; }
    .empty-state i { font-size:2.5rem; display:block; margin-bottom:.75rem; }
    .empty-state p { font-size:.85rem; margin:0; }

    .errores-list { list-style:none; padding:0; margin:0; }
    .errores-list li { display:flex; align-items:flex-start; gap:.5rem; padding:.4rem 0; border-bottom:1px solid var(--fondo-borde); font-size:.8rem; color:#DC3545; }
    .errores-list li:last-child { border-bottom:none; }
</style>
@endpush

@section('contenido')

{{-- Cabecera --}}
<div class="page-header">
    <div>
        <h4 class="page-titulo">
            <i class="bi bi-box-arrow-in-down" style="color:var(--color-principal);margin-right:.6rem;"></i>
            Importación {{ $importacion->numero_importacion }}
        </h4>
        <p class="page-subtitulo">{{ $importacion->fuente_label }} &mdash; {{ $importacion->tipo_datos_label }} &mdash; {{ $importacion->created_at->format('d/m/Y H:i') }}</p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
        @if(in_array($importacion->estado, ['pendiente','error']))
        <form method="POST" action="{{ route('dev.importacion.procesar', $importacion) }}" style="display:inline;">
            @csrf
            <button type="submit"
                style="display:inline-flex;align-items:center;gap:.4rem;background:var(--color-principal);color:#fff;border:none;border-radius:8px;padding:.5rem 1rem;font-size:.85rem;font-weight:600;cursor:pointer;"
                onclick="return confirm('¿Confirmas que deseas iniciar el proceso de importación?')">
                <i class="bi bi-play-circle"></i> Procesar Ahora
            </button>
        </form>
        @endif

        @if($importacion->puede_revertir && $importacion->estado === 'completado')
        <button type="button"
            style="display:inline-flex;align-items:center;gap:.4rem;background:#fff3cd;color:#856404;border:1px solid #fde68a;border-radius:8px;padding:.5rem 1rem;font-size:.85rem;font-weight:600;cursor:pointer;"
            onclick="document.getElementById('modal-revertir').style.display='flex';document.body.style.overflow='hidden'">
            <i class="bi bi-arrow-counterclockwise"></i> Revertir
        </button>
        @endif

        <a href="{{ route('dev.importacion.index') }}"
           style="display:inline-flex;align-items:center;gap:.4rem;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem 1rem;font-size:.85rem;font-weight:600;text-decoration:none;">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

@if(session('exito'))
<div style="background:#dcfce7;border:1px solid #86efac;border-radius:10px;padding:.75rem 1.1rem;margin-bottom:1rem;color:#166534;font-size:.85rem;display:flex;align-items:center;gap:.5rem;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

@if(session('error'))
<div style="background:#fde8e8;border:1px solid #fca5a5;border-radius:10px;padding:.75rem 1.1rem;margin-bottom:1rem;color:#DC3545;font-size:.85rem;display:flex;align-items:center;gap:.5rem;">
    <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
</div>
@endif

{{-- Banner estado pendiente --}}
@if($importacion->estado === 'pendiente')
<div style="background:#dbeafe;border:1px solid #93c5fd;border-radius:10px;padding:.875rem 1.25rem;margin-bottom:1rem;display:flex;align-items:center;gap:.75rem;">
    <i class="bi bi-clock-history" style="color:#1e40af;font-size:1.2rem;flex-shrink:0;"></i>
    <div style="flex:1;">
        <strong style="color:#1e40af;font-size:.88rem;">Importación pendiente de procesamiento</strong>
        <p style="color:#1e40af;font-size:.8rem;margin:.15rem 0 0 0;">El archivo fue cargado exitosamente. Haz clic en "Procesar Ahora" para iniciar la migración de datos.</p>
    </div>
</div>
@endif

@if($importacion->estado === 'error' && $importacion->errores)
<div style="background:#fde8e8;border:1px solid #fca5a5;border-radius:10px;padding:.875rem 1.25rem;margin-bottom:1rem;">
    <strong style="color:#DC3545;font-size:.88rem;display:flex;align-items:center;gap:.4rem;"><i class="bi bi-x-circle-fill"></i> La importación encontró errores</strong>
    <ul class="errores-list" style="margin-top:.5rem;">
        @foreach(array_slice($importacion->errores, 0, 5) as $error)
        <li><i class="bi bi-dot"></i> {{ $error }}</li>
        @endforeach
        @if(count($importacion->errores) > 5)
        <li style="color:#6b7280;"><i class="bi bi-three-dots"></i> y {{ count($importacion->errores) - 5 }} errores más...</li>
        @endif
    </ul>
</div>
@endif

{{-- Tarjetas de contadores --}}
<div class="metricas-grid">
    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Total</span>
            <div class="metrica-icono" style="background:#f3f4f6;color:#6C757D;"><i class="bi bi-stack"></i></div>
        </div>
        <div class="metrica-numero" style="color:#374151;">{{ number_format($importacion->total_registros) }}</div>
        <div class="metrica-sub">Filas en el archivo</div>
    </div>

    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Importados</span>
            <div class="metrica-icono" style="background:#dcfce7;color:#166534;"><i class="bi bi-check-circle"></i></div>
        </div>
        <div class="metrica-numero" style="color:#166534;">{{ number_format($importacion->registros_importados) }}</div>
        <div class="metrica-sub">Creados exitosamente</div>
    </div>

    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Duplicados</span>
            <div class="metrica-icono" style="background:#fff3cd;color:#856404;"><i class="bi bi-files"></i></div>
        </div>
        <div class="metrica-numero" style="color:#856404;">{{ number_format($importacion->registros_duplicados) }}</div>
        <div class="metrica-sub">Ya existían en el sistema</div>
    </div>

    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Errores</span>
            <div class="metrica-icono" style="background:#fde8e8;color:#DC3545;"><i class="bi bi-x-circle"></i></div>
        </div>
        <div class="metrica-numero" style="color:#DC3545;">{{ number_format($importacion->registros_error) }}</div>
        <div class="metrica-sub">Fallaron al procesar</div>
    </div>

    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Omitidos</span>
            <div class="metrica-icono" style="background:#f3f4f6;color:#9ca3af;"><i class="bi bi-skip-forward"></i></div>
        </div>
        <div class="metrica-numero" style="color:#9ca3af;">{{ number_format($importacion->registros_omitidos) }}</div>
        <div class="metrica-sub">Sin datos suficientes</div>
    </div>
</div>

{{-- Barra de progreso --}}
@if($importacion->total_registros > 0)
<div style="background:#fff;border:1px solid var(--fondo-borde);border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.25rem;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.08);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.6rem;">
        <span style="font-size:.8rem;font-weight:700;color:#374151;">Progreso de importación</span>
        <span style="font-size:.8rem;font-weight:700;color:var(--color-principal);">{{ $importacion->porcentaje_exito }}% exitoso</span>
    </div>
    <div style="height:12px;background:#e5e7eb;border-radius:50px;overflow:hidden;display:flex;">
        @if($importacion->registros_importados > 0)
        <div style="width:{{ ($importacion->registros_importados / $importacion->total_registros) * 100 }}%;background:#166534;border-radius:50px 0 0 50px;transition:width .5s;"></div>
        @endif
        @if($importacion->registros_duplicados > 0)
        <div style="width:{{ ($importacion->registros_duplicados / $importacion->total_registros) * 100 }}%;background:#fbbf24;"></div>
        @endif
        @if($importacion->registros_error > 0)
        <div style="width:{{ ($importacion->registros_error / $importacion->total_registros) * 100 }}%;background:#DC3545;"></div>
        @endif
        @if($importacion->registros_omitidos > 0)
        <div style="width:{{ ($importacion->registros_omitidos / $importacion->total_registros) * 100 }}%;background:#d1d5db;"></div>
        @endif
    </div>
    <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:.6rem;">
        <span style="font-size:.72rem;color:#166534;display:flex;align-items:center;gap:.25rem;"><span style="width:10px;height:10px;background:#166534;border-radius:2px;display:inline-block;"></span>Importados</span>
        <span style="font-size:.72rem;color:#856404;display:flex;align-items:center;gap:.25rem;"><span style="width:10px;height:10px;background:#fbbf24;border-radius:2px;display:inline-block;"></span>Duplicados</span>
        <span style="font-size:.72rem;color:#DC3545;display:flex;align-items:center;gap:.25rem;"><span style="width:10px;height:10px;background:#DC3545;border-radius:2px;display:inline-block;"></span>Errores</span>
        <span style="font-size:.72rem;color:#9ca3af;display:flex;align-items:center;gap:.25rem;"><span style="width:10px;height:10px;background:#d1d5db;border-radius:2px;display:inline-block;"></span>Omitidos</span>
    </div>
</div>
@endif

{{-- Información general --}}
<div class="info-card">
    <div class="info-header" style="background:var(--color-principal);color:#fff;border-radius:10px 10px 0 0;padding:.75rem 1.25rem;">
        <span style="font-weight:700;font-size:.9rem;display:flex;align-items:center;gap:.4rem;">
            <i class="bi bi-info-circle-fill"></i> Información de la Importación
        </span>
        <span class="badge-estado estado-{{ $importacion->estado }}" style="background:rgba(255,255,255,.25);color:#fff;">
            {{ $importacion->estado_label }}
        </span>
    </div>
    <div class="info-body">
        <div class="info-grid">
            <div class="info-campo">
                <div class="info-campo-label">N° Importación</div>
                <div class="info-campo-valor" style="font-family:monospace;font-size:.95rem;">{{ $importacion->numero_importacion }}</div>
            </div>
            <div class="info-campo">
                <div class="info-campo-label">Sistema Origen</div>
                <div class="info-campo-valor">{{ $importacion->fuente_label }}</div>
            </div>
            <div class="info-campo">
                <div class="info-campo-label">Tipo de Datos</div>
                <div class="info-campo-valor">{{ $importacion->tipo_datos_label }}</div>
            </div>
            <div class="info-campo">
                <div class="info-campo-label">Archivo</div>
                <div class="info-campo-valor" style="font-size:.8rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $importacion->archivo_nombre }}">
                    <i class="bi bi-paperclip"></i> {{ $importacion->archivo_nombre }}
                </div>
            </div>
            <div class="info-campo">
                <div class="info-campo-label">Registrado por</div>
                <div class="info-campo-valor">{{ $importacion->registradoPor?->name ?? '—' }}</div>
            </div>
            <div class="info-campo">
                <div class="info-campo-label">Fecha de Importación</div>
                <div class="info-campo-valor">
                    {{ $importacion->fecha_importacion ? $importacion->fecha_importacion->format('d/m/Y H:i') : $importacion->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
            @if($importacion->notas)
            <div class="info-campo" style="grid-column:1/-1;">
                <div class="info-campo-label">Notas</div>
                <div class="info-campo-valor" style="font-weight:400;font-size:.83rem;color:#374151;">{{ $importacion->notas }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Tabla de detalles --}}
<div class="tabla-container">
    <div style="padding:.85rem 1.25rem;border-bottom:1px solid var(--fondo-borde);">
        <div style="font-size:.82rem;font-weight:700;color:var(--color-hover);margin-bottom:.65rem;display:flex;align-items:center;gap:.4rem;">
            <i class="bi bi-list-ul" style="color:var(--color-principal);"></i>
            Log de importación — {{ $detalles->total() }} registro(s)
        </div>

        {{-- Filtros por tab --}}
        <div class="filtros-tabs">
            <a href="{{ request()->fullUrlWithQuery(['filtro' => 'todos']) }}"
               class="filtro-tab {{ $filtroEstado === 'todos' ? 'activo' : '' }}">
                <i class="bi bi-stack"></i> Todos ({{ $importacion->total_registros }})
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filtro' => 'importado']) }}"
               class="filtro-tab verde {{ $filtroEstado === 'importado' ? 'activo' : '' }}">
                <i class="bi bi-check-circle"></i> Importados ({{ $importacion->registros_importados }})
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filtro' => 'duplicado']) }}"
               class="filtro-tab naranja {{ $filtroEstado === 'duplicado' ? 'activo' : '' }}">
                <i class="bi bi-files"></i> Duplicados ({{ $importacion->registros_duplicados }})
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filtro' => 'error']) }}"
               class="filtro-tab rojo {{ $filtroEstado === 'error' ? 'activo' : '' }}">
                <i class="bi bi-x-circle"></i> Errores ({{ $importacion->registros_error }})
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filtro' => 'omitido']) }}"
               class="filtro-tab gris {{ $filtroEstado === 'omitido' ? 'activo' : '' }}">
                <i class="bi bi-skip-forward"></i> Omitidos ({{ $importacion->registros_omitidos }})
            </a>
        </div>
    </div>

    @if($detalles->count() > 0)
    <div style="overflow-x:auto;">
    <table class="tabla-det">
        <thead>
            <tr>
                <th>Fila</th>
                <th>Estado</th>
                <th>Mensaje</th>
                <th>Nombre (original)</th>
                <th>Documento</th>
                <th>Datos transformados</th>
                <th style="text-align:center;">Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detalles as $detalle)
            <tr>
                <td>
                    <span class="badge-fila">#{{ $detalle->fila_numero }}</span>
                </td>
                <td>
                    @if($detalle->estado === 'importado')
                    <span class="badge-estado estado-importado" style="font-size:.7rem;padding:.2rem .55rem;border-radius:50px;"><i class="bi bi-check-circle-fill"></i> Importado</span>
                    @elseif($detalle->estado === 'duplicado')
                    <span class="badge-estado estado-duplicado" style="font-size:.7rem;padding:.2rem .55rem;border-radius:50px;"><i class="bi bi-files"></i> Duplicado</span>
                    @elseif($detalle->estado === 'error')
                    <span class="badge-estado estado-error-b" style="font-size:.7rem;padding:.2rem .55rem;border-radius:50px;"><i class="bi bi-x-circle-fill"></i> Error</span>
                    @else
                    <span class="badge-estado estado-omitido" style="font-size:.7rem;padding:.2rem .55rem;border-radius:50px;"><i class="bi bi-dash-circle"></i> Omitido</span>
                    @endif
                </td>
                <td>
                    <span style="font-size:.78rem;color:{{ $detalle->estado === 'error' ? '#DC3545' : ($detalle->estado === 'importado' ? '#166534' : '#856404') }};">
                        {{ $detalle->mensaje ?? '—' }}
                    </span>
                </td>
                <td class="dato-col">
                    @php
                        $orig = $detalle->datos_originales;
                        $nombre = $orig['nombres'] ?? $orig['Nombres'] ?? $orig['nombre'] ?? $orig['Nombre'] ?? $orig['NOMBRES'] ?? '';
                        $apellido = $orig['apellidos'] ?? $orig['Apellidos'] ?? $orig['apellido'] ?? $orig['Apellido'] ?? $orig['APELLIDOS'] ?? '';
                    @endphp
                    <span title="{{ trim($nombre . ' ' . $apellido) ?: 'Sin nombre' }}">{{ trim($nombre . ' ' . $apellido) ?: '—' }}</span>
                </td>
                <td style="font-size:.78rem;font-family:monospace;color:#374151;">
                    @php
                        $doc = $orig['cedula'] ?? $orig['documento'] ?? $orig['Documento'] ?? $orig['CEDULA'] ?? $orig['Numero Documento'] ?? $orig['numero_documento'] ?? '';
                    @endphp
                    {{ $doc ?: '—' }}
                </td>
                <td>
                    @if($detalle->datos_transformados)
                    <button type="button"
                        onclick="verDatos({{ $detalle->id }})"
                        style="background:var(--color-muy-claro);color:var(--color-principal);border:1px solid var(--color-claro);border-radius:6px;padding:.2rem .55rem;font-size:.72rem;cursor:pointer;">
                        <i class="bi bi-eye"></i> Ver
                    </button>
                    <div id="datos-{{ $detalle->id }}" style="display:none;"></div>
                    @else
                    <span style="font-size:.75rem;color:#9ca3af;">—</span>
                    @endif
                </td>
                <td style="text-align:center;">
                    @if($detalle->estado === 'importado' && $detalle->registro_id && $detalle->modelo === 'App\\Models\\Paciente')
                    <a href="{{ route('pacientes.show', $detalle->registro_id) }}" class="btn-accion btn-ver" title="Ver paciente" target="_blank">
                        <i class="bi bi-person"></i>
                    </a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    @if($detalles->hasPages())
    <div style="padding:.75rem 1.25rem;border-top:1px solid var(--fondo-borde);">
        {{ $detalles->links() }}
    </div>
    @endif

    @else
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <p>
            @if($filtroEstado !== 'todos')
                No hay registros con estado "{{ $filtroEstado }}" en esta importación.
            @else
                Esta importación aún no ha sido procesada. Usa el botón "Procesar Ahora" para comenzar.
            @endif
        </p>
    </div>
    @endif
</div>

{{-- Modal Revertir --}}
<div id="modal-revertir" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:460px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative;margin:1rem;">
        <button onclick="document.getElementById('modal-revertir').style.display='none';document.body.style.overflow=''"
            style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;">✕</button>
        <h5 style="font-weight:700;color:#1c2b22;margin-bottom:.5rem;display:flex;align-items:center;gap:.4rem;">
            <i class="bi bi-arrow-counterclockwise" style="color:#856404;"></i> Revertir Importación
        </h5>
        <p style="font-size:.85rem;color:#6b7280;margin-bottom:1rem;line-height:1.5;">
            Vas a revertir la importación <strong>{{ $importacion->numero_importacion }}</strong>.
            Se eliminarán los <strong>{{ $importacion->registros_importados }}</strong> registros creados durante esta sesión.
        </p>
        <div style="background:#fff3cd;border:1px solid #fde68a;border-radius:8px;padding:.75rem 1rem;margin-bottom:1.25rem;font-size:.82rem;color:#856404;">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Esta acción no se puede deshacer.</strong> Solo se eliminarán los registros de esta importación, no los que ya estaban en el sistema.
        </div>
        <form method="POST" action="{{ route('dev.importacion.revertir', $importacion) }}">
            @csrf
            <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('modal-revertir').style.display='none';document.body.style.overflow=''"
                    style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit"
                    style="background:#856404;color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:.3rem;">
                    <i class="bi bi-arrow-counterclockwise"></i> Confirmar Reversión
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal datos transformados --}}
<div id="modal-datos" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:600px;max-height:80vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative;margin:1rem;">
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--fondo-borde);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:#fff;z-index:1;">
            <h5 style="font-weight:700;color:#1c2b22;margin:0;font-size:1rem;"><i class="bi bi-table" style="color:var(--color-principal);"></i> Datos Transformados</h5>
            <button onclick="document.getElementById('modal-datos').style.display='none';document.body.style.overflow=''"
                style="background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;">✕</button>
        </div>
        <div id="modal-datos-body" style="padding:1.25rem;"></div>
    </div>
</div>

{{-- JSON data para JS --}}
<script id="detalles-json" type="application/json">
{!! $detalles->getCollection()->map(function($d) {
    return ['id' => $d->id, 'datos' => $d->datos_transformados ?? $d->datos_originales];
})->toJson() !!}
</script>

@push('scripts')
<script>
const detallesData = JSON.parse(document.getElementById('detalles-json').textContent);
const detallesMap  = {};
detallesData.forEach(d => { detallesMap[d.id] = d.datos; });

function verDatos(id) {
    const datos = detallesMap[id];
    if (!datos) return;
    let html = '<table style="width:100%;border-collapse:collapse;font-size:.82rem;">';
    html += '<thead><tr><th style="background:var(--color-muy-claro);color:var(--color-principal);padding:.4rem .7rem;text-align:left;border:1px solid var(--fondo-borde);font-size:.7rem;text-transform:uppercase;">Campo</th><th style="background:var(--color-muy-claro);color:var(--color-principal);padding:.4rem .7rem;text-align:left;border:1px solid var(--fondo-borde);font-size:.7rem;text-transform:uppercase;">Valor</th></tr></thead><tbody>';
    Object.entries(datos).forEach(([k, v]) => {
        html += `<tr>
            <td style="padding:.35rem .7rem;border:1px solid var(--fondo-borde);font-weight:600;color:#374151;">${escHtml(k)}</td>
            <td style="padding:.35rem .7rem;border:1px solid var(--fondo-borde);color:#1c2b22;">${escHtml(v ?? '—')}</td>
        </tr>`;
    });
    html += '</tbody></table>';
    document.getElementById('modal-datos-body').innerHTML = html;
    document.getElementById('modal-datos').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.getElementById('modal-revertir').style.display = 'none';
        document.getElementById('modal-datos').style.display = 'none';
        document.body.style.overflow = '';
    }
});
['modal-revertir','modal-datos'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
            document.body.style.overflow = '';
        }
    });
});

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endpush

@endsection
