{{-- ============================================================
     VISTA: Listado de Importaciones
     Sistema: Arkevix Dental ERP
     Layout: layouts.app
     ============================================================ --}}
@extends('layouts.dev')
@section('titulo', 'Importación de Datos')

@push('estilos')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; gap:1rem; flex-wrap:wrap; }
    .page-titulo  { font-family:var(--fuente-titulos); font-weight:700; color:#1c2b22; margin:0; font-size:1.4rem; }
    .page-subtitulo { font-size:.82rem; color:#9ca3af; margin:.15rem 0 0 0; }

    .metricas-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.25rem; }
    @media(max-width:900px){ .metricas-grid{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:500px){ .metricas-grid{ grid-template-columns:1fr 1fr; } }

    .metrica-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1.1rem 1.25rem; display:flex; flex-direction:column; gap:.6rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .metrica-header { display:flex; align-items:center; justify-content:space-between; }
    .metrica-label  { font-size:.72rem; font-weight:500; color:#8fa39a; text-transform:uppercase; letter-spacing:.06em; }
    .metrica-numero { font-family:var(--fuente-titulos); font-size:1.5rem; font-weight:600; line-height:1; }
    .metrica-sub    { font-size:.75rem; color:#8fa39a; }
    .metrica-icono  { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:1rem; }
    .icono-verde    { background:var(--color-muy-claro); color:var(--color-principal); }
    .icono-azul     { background:#dbeafe; color:#1e40af; }
    .icono-naranja  { background:#fff3e0; color:#e65100; }
    .icono-gris     { background:#f3f4f6; color:#6C757D; }

    .tabla-container { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .tabla-header    { padding:.85rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
    .tabla-titulo    { font-size:.82rem; font-weight:700; color:var(--color-hover); display:flex; align-items:center; gap:.4rem; }
    .tabla-titulo i  { color:var(--color-principal); }

    .tabla-imp { width:100%; border-collapse:collapse; font-size:.83rem; }
    .tabla-imp th { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#8fa39a; padding:.65rem .9rem; border-bottom:2px solid var(--fondo-borde); text-align:left; white-space:nowrap; }
    .tabla-imp td { padding:.6rem .9rem; border-bottom:1px solid var(--fondo-borde); color:#374151; vertical-align:middle; }
    .tabla-imp tr:last-child td { border-bottom:none; }
    .tabla-imp tr:hover td { background:var(--fondo-card-alt,#f9fafb); }

    .badge-num     { display:inline-block; font-size:.72rem; font-weight:700; font-family:monospace; padding:.2rem .55rem; border-radius:6px; background:var(--color-muy-claro); color:var(--color-principal); }
    .badge-estado  { display:inline-block; font-size:.7rem; font-weight:700; padding:.2rem .55rem; border-radius:50px; }
    .estado-pendiente  { background:#f3f4f6;  color:#6C757D; }
    .estado-procesando { background:#dbeafe;  color:#1e40af; }
    .estado-completado { background:#dcfce7;  color:#166534; }
    .estado-error      { background:#fde8e8;  color:#DC3545; }
    .estado-revertido  { background:#fff3cd;  color:#856404; }

    .progreso-mini { height:5px; background:#e5e7eb; border-radius:50px; overflow:hidden; min-width:80px; }
    .progreso-barra { height:100%; background:var(--color-principal); border-radius:50px; transition:width .3s; }

    .btn-accion { display:inline-flex; align-items:center; gap:.25rem; padding:.28rem .6rem; border-radius:6px; font-size:.75rem; font-weight:500; text-decoration:none; border:1px solid transparent; cursor:pointer; transition:all .15s; }
    .btn-ver    { background:var(--color-muy-claro); color:var(--color-principal); border-color:var(--color-muy-claro); }
    .btn-revert { background:#fff3cd; color:#856404; border-color:#fde68a; }
    .btn-elim   { background:#fde8e8; color:#DC3545; border-color:#fecaca; }
    .btn-accion:hover { filter:brightness(.92); }

    .empty-state   { padding:3rem 1rem; text-align:center; color:#9ca3af; }
    .empty-state i { font-size:2.5rem; display:block; margin-bottom:.75rem; }
    .empty-state p { font-size:.85rem; margin:0; }
</style>
@endpush

@section('contenido')

{{-- Cabecera --}}
<div class="page-header">
    <div>
        <h4 class="page-titulo"><i class="bi bi-box-arrow-in-down" style="color:var(--color-principal);margin-right:.6rem;"></i>Importación de Datos</h4>
        <p class="page-subtitulo">Migración de pacientes y datos desde otros sistemas dentales</p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
        <a href="{{ route('dev.importacion.plantillas') }}"
           style="display:inline-flex;align-items:center;gap:.4rem;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem 1rem;font-size:.85rem;font-weight:600;text-decoration:none;">
            <i class="bi bi-file-earmark-arrow-down"></i> Ver Plantillas
        </a>
        <a href="{{ route('dev.importacion.create') }}"
           style="display:inline-flex;align-items:center;gap:.4rem;background:var(--color-principal);color:#fff;border:none;border-radius:8px;padding:.5rem 1rem;font-size:.85rem;font-weight:600;text-decoration:none;">
            <i class="bi bi-plus-lg"></i> Nueva Importación
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

{{-- Tarjetas de estadísticas --}}
<div class="metricas-grid">
    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Total Importaciones</span>
            <div class="metrica-icono icono-verde"><i class="bi bi-box-arrow-in-down"></i></div>
        </div>
        <div class="metrica-numero" style="color:var(--color-principal);">{{ $stats['total'] }}</div>
        <div class="metrica-sub">Sesiones de importación</div>
    </div>

    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Registros Importados</span>
            <div class="metrica-icono icono-azul"><i class="bi bi-people"></i></div>
        </div>
        <div class="metrica-numero" style="color:#1e40af;">{{ number_format($stats['total_pacientes']) }}</div>
        <div class="metrica-sub">Pacientes y datos migrados</div>
    </div>

    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Con Errores</span>
            <div class="metrica-icono icono-naranja"><i class="bi bi-exclamation-triangle"></i></div>
        </div>
        <div class="metrica-numero" style="color:#DC3545;">{{ $stats['con_errores'] }}</div>
        <div class="metrica-sub">Importaciones con incidencias</div>
    </div>

    <div class="metrica-card">
        <div class="metrica-header">
            <span class="metrica-label">Última Importación</span>
            <div class="metrica-icono icono-gris"><i class="bi bi-clock-history"></i></div>
        </div>
        <div class="metrica-numero" style="color:#6C757D;font-size:1rem;">
            {{ $stats['ultima'] ? $stats['ultima']->created_at->diffForHumans() : 'Ninguna' }}
        </div>
        <div class="metrica-sub">
            {{ $stats['ultima'] ? $stats['ultima']->fuente_label : '—' }}
        </div>
    </div>
</div>

{{-- Tabla --}}
<div class="tabla-container">
    <div class="tabla-header">
        <div class="tabla-titulo">
            <i class="bi bi-list-ul"></i>
            Historial de Importaciones — {{ $importaciones->total() }} registro(s)
        </div>
    </div>

    @if($importaciones->count() > 0)
    <div style="overflow-x:auto;">
    <table class="tabla-imp">
        <thead>
            <tr>
                <th>N° Importación</th>
                <th>Fuente</th>
                <th>Tipo de Datos</th>
                <th>Archivo</th>
                <th style="text-align:center;">Total</th>
                <th style="text-align:center;">Import.</th>
                <th style="text-align:center;">Duplic.</th>
                <th style="text-align:center;">Errores</th>
                <th>Progreso</th>
                <th>Estado</th>
                <th>Registrado por</th>
                <th>Fecha</th>
                <th style="text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($importaciones as $imp)
            <tr>
                <td>
                    <span class="badge-num">{{ $imp->numero_importacion }}</span>
                </td>
                <td>
                    <span style="font-weight:600;font-size:.8rem;">
                        @if($imp->fuente === 'dentox') <i class="bi bi-tooth" style="color:var(--color-principal);"></i>
                        @elseif($imp->fuente === 'odontosof') <i class="bi bi-file-medical" style="color:#1e40af;"></i>
                        @elseif($imp->fuente === 'dentalpro') <i class="bi bi-clipboard2-pulse" style="color:#e65100;"></i>
                        @elseif(str_contains($imp->fuente, 'excel')) <i class="bi bi-file-earmark-spreadsheet" style="color:#166534;"></i>
                        @elseif(str_contains($imp->fuente, 'csv')) <i class="bi bi-filetype-csv" style="color:#856404;"></i>
                        @else <i class="bi bi-database" style="color:#6C757D;"></i>
                        @endif
                        {{ $imp->fuente_label }}
                    </span>
                </td>
                <td style="font-size:.78rem;color:#374151;">{{ $imp->tipo_datos_label }}</td>
                <td style="font-size:.75rem;color:#6b7280;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $imp->archivo_nombre }}">
                    <i class="bi bi-paperclip"></i> {{ $imp->archivo_nombre }}
                </td>
                <td style="text-align:center;font-weight:600;">{{ number_format($imp->total_registros) }}</td>
                <td style="text-align:center;color:#166534;font-weight:600;">{{ number_format($imp->registros_importados) }}</td>
                <td style="text-align:center;color:#856404;font-weight:600;">{{ number_format($imp->registros_duplicados) }}</td>
                <td style="text-align:center;color:#DC3545;font-weight:600;">{{ number_format($imp->registros_error) }}</td>
                <td style="min-width:90px;">
                    <div style="display:flex;align-items:center;gap:.4rem;">
                        <div class="progreso-mini" style="flex:1;">
                            <div class="progreso-barra" style="width:{{ $imp->porcentaje_exito }}%;"></div>
                        </div>
                        <span style="font-size:.7rem;color:#6b7280;white-space:nowrap;">{{ $imp->porcentaje_exito }}%</span>
                    </div>
                </td>
                <td>
                    <span class="badge-estado estado-{{ $imp->estado }}">
                        @if($imp->estado === 'completado') <i class="bi bi-check-circle-fill"></i>
                        @elseif($imp->estado === 'error') <i class="bi bi-x-circle-fill"></i>
                        @elseif($imp->estado === 'procesando') <i class="bi bi-arrow-repeat"></i>
                        @elseif($imp->estado === 'revertido') <i class="bi bi-arrow-counterclockwise"></i>
                        @else <i class="bi bi-clock"></i>
                        @endif
                        {{ $imp->estado_label }}
                    </span>
                </td>
                <td style="font-size:.78rem;color:#374151;">{{ $imp->registradoPor?->name ?? '—' }}</td>
                <td style="white-space:nowrap;font-size:.78rem;color:#6b7280;">
                    {{ $imp->created_at->format('d/m/Y H:i') }}
                </td>
                <td style="text-align:center;white-space:nowrap;">
                    <a href="{{ route('dev.importacion.show', $imp) }}" class="btn-accion btn-ver" title="Ver detalle">
                        <i class="bi bi-eye"></i>
                    </a>
                    @if($imp->puede_revertir && $imp->estado === 'completado')
                    <button type="button" class="btn-accion btn-revert"
                        onclick="confirmarRevertir({{ $imp->id }}, '{{ $imp->numero_importacion }}')"
                        title="Revertir importación">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                    @endif
                    <button type="button" class="btn-accion btn-elim"
                        onclick="confirmarEliminar({{ $imp->id }}, '{{ $imp->numero_importacion }}')"
                        title="Eliminar registro">
                        <i class="bi bi-trash3"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    @if($importaciones->hasPages())
    <div style="padding:.75rem 1.25rem;border-top:1px solid var(--fondo-borde);">
        {{ $importaciones->links() }}
    </div>
    @endif

    @else
    <div class="empty-state">
        <i class="bi bi-box-arrow-in-down"></i>
        <p>No hay importaciones registradas aún.</p>
        <a href="{{ route('dev.importacion.create') }}"
           style="display:inline-flex;align-items:center;gap:.4rem;background:var(--color-principal);color:#fff;border:none;border-radius:8px;padding:.5rem 1.1rem;font-size:.85rem;font-weight:600;text-decoration:none;margin-top:.75rem;">
            <i class="bi bi-plus-lg"></i> Crear primera importación
        </a>
    </div>
    @endif
</div>

{{-- Modal Revertir --}}
<div id="modal-revertir" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:440px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative;margin:1rem;">
        <button onclick="cerrarModal('modal-revertir')" style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;">✕</button>
        <h5 style="font-weight:700;color:#1c2b22;margin-bottom:.5rem;display:flex;align-items:center;gap:.4rem;">
            <i class="bi bi-arrow-counterclockwise" style="color:#856404;"></i> Revertir Importación
        </h5>
        <p id="revertir-texto" style="font-size:.85rem;color:#6b7280;margin-bottom:1.25rem;line-height:1.5;"></p>
        <div style="background:#fff3cd;border:1px solid #fde68a;border-radius:8px;padding:.75rem 1rem;margin-bottom:1.25rem;font-size:.82rem;color:#856404;">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Advertencia:</strong> Esta acción eliminará todos los registros creados durante esta importación. No se puede deshacer.
        </div>
        <form id="form-revertir" method="POST">
            @csrf
            <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                <button type="button" onclick="cerrarModal('modal-revertir')"
                    style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit"
                    style="background:#856404;color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:.3rem;">
                    <i class="bi bi-arrow-counterclockwise"></i> Sí, Revertir
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Eliminar --}}
<div id="modal-eliminar" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:420px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative;margin:1rem;">
        <button onclick="cerrarModal('modal-eliminar')" style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;">✕</button>
        <h5 style="font-weight:700;color:#1c2b22;margin-bottom:.5rem;display:flex;align-items:center;gap:.4rem;">
            <i class="bi bi-trash3" style="color:#DC3545;"></i> Eliminar Registro
        </h5>
        <p id="eliminar-texto" style="font-size:.85rem;color:#6b7280;margin-bottom:1.25rem;"></p>
        <form id="form-eliminar" method="POST">
            @csrf
            @method('DELETE')
            <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                <button type="button" onclick="cerrarModal('modal-eliminar')"
                    style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit"
                    style="background:#DC3545;color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:.3rem;">
                    <i class="bi bi-trash3"></i> Eliminar
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function confirmarRevertir(id, numero) {
    document.getElementById('revertir-texto').textContent = 'Vas a revertir la importación ' + numero + '. Se eliminarán todos los registros creados en esta sesión.';
    document.getElementById('form-revertir').action = '/importacion/' + id + '/revertir';
    document.getElementById('modal-revertir').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function confirmarEliminar(id, numero) {
    document.getElementById('eliminar-texto').textContent = 'Se eliminará el registro histórico de la importación ' + numero + '. Los datos ya importados no se borran.';
    document.getElementById('form-eliminar').action = '/importacion/' + id;
    document.getElementById('modal-eliminar').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function cerrarModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => {
    if(e.key === 'Escape') {
        cerrarModal('modal-revertir');
        cerrarModal('modal-eliminar');
    }
});
['modal-revertir','modal-eliminar'].forEach(id => {
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById(id)?.addEventListener('click', function(e) {
            if(e.target === this) cerrarModal(id);
        });
    });
});
</script>
@endpush

@endsection
