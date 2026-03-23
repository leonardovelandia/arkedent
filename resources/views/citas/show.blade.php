@extends('layouts.app')
@section('titulo', 'Detalle de Cita')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    .cita-header { background:linear-gradient(135deg,var(--color-principal) 0%,var(--color-sidebar-2) 60%,var(--color-sidebar) 100%); border-radius:14px; padding:1.5rem 1.75rem; color:#fff; margin-bottom:1.25rem; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem; }
    .cita-header-main { display:flex; flex-direction:column; gap:.25rem; }
    .cita-header h2 { font-family:var(--fuente-titulos); font-size:1.4rem; font-weight:700; margin:0; }
    .cita-header-sub { font-size:.88rem; opacity:.8; display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; }

    .badge-estado { display:inline-block; padding:.28rem .75rem; border-radius:20px; font-size:.78rem; font-weight:700; white-space:nowrap; }

    .info-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:1.25rem 1.5rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1rem; }
    .info-card-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-hover); margin-bottom:.9rem; display:flex; align-items:center; gap:.4rem; }
    .dato-row { display:flex; gap:.5rem; margin-bottom:.55rem; align-items:flex-start; }
    .dato-lbl { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#9ca3af; min-width:110px; padding-top:.12rem; }
    .dato-val { font-size:.9rem; color:#1c2b22; font-weight:500; }

    .historial-item { display:flex; align-items:center; gap:.75rem; padding:.55rem 0; border-bottom:1px solid var(--fondo-borde); }
    .historial-item:last-child { border-bottom:none; }
    .historial-fecha { font-size:.78rem; color:#6b7280; min-width:90px; }
    .historial-proc { font-size:.85rem; color:#1c2b22; font-weight:500; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

    .acciones-wrap { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1.25rem; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

@php $color = $colores[$cita->estado] ?? ['bg'=>'#f3f4f6','texto'=>'#374151']; @endphp

{{-- Header --}}
<div class="cita-header">
    <div class="cita-header-main">
        <h2>{{ $cita->paciente->nombre_completo }}</h2>
        <div class="cita-header-sub">
            <span><i class="bi bi-calendar3"></i> {{ $cita->fecha->translatedFormat('l, d \d\e F \d\e Y') }}</span>
            <span><i class="bi bi-clock"></i> {{ $cita->hora_inicio }}{{ $cita->hora_fin ? ' – '.$cita->hora_fin : '' }}</span>
        </div>
    </div>
    <span class="badge-estado" style="background:{{ $color['bg'] }};color:{{ $color['texto'] }};font-size:.9rem;padding:.4rem 1rem;">
        {{ ucfirst(str_replace('_',' ',$cita->estado)) }}
    </span>
</div>

{{-- Botones de acción --}}
<div class="acciones-wrap">
    <a href="{{ route('citas.index') }}"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
    <a href="{{ route('citas.edit', $cita) }}" class="btn-morado" style="background:transparent;color:var(--color-principal);border:1px solid var(--color-principal);">
        <i class="bi bi-pencil"></i> Editar
    </a>
    @if($cita->estado === 'pendiente')
    <form method="POST" action="{{ route('citas.confirmar', $cita) }}" style="display:inline;">
        @csrf
        <button type="submit" class="btn-morado" style="background:linear-gradient(135deg,#166534,#15803d);">
            <i class="bi bi-check-lg"></i> Confirmar
        </button>
    </form>
    @endif
    @if(!in_array($cita->estado, ['cancelada','atendida']))
    <button type="button" onclick="abrirModalCancelar()"
        class="btn-morado" style="background:linear-gradient(135deg,#dc2626,#ef4444);">
        <i class="bi bi-x-lg"></i> Cancelar cita
    </button>
    @endif
    @if(!$cita->valoracion)
    <a href="{{ route('valoraciones.create', ['cita_id' => $cita->id, 'paciente_id' => $cita->paciente_id]) }}"
       class="btn-morado">
        <i class="bi bi-clipboard2-pulse"></i> Registrar Valoración
    </a>
    @else
    <a href="{{ route('valoraciones.show', $cita->valoracion) }}"
       class="btn-morado" style="background:transparent;color:var(--color-principal);border:1px solid var(--color-principal);">
        <i class="bi bi-eye"></i> Ver Valoración
    </a>
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">

{{-- Datos de la cita --}}
<div>
    <div class="info-card">
        <div class="info-card-titulo"><i class="bi bi-clipboard2-pulse"></i> Datos de la cita</div>
        <div class="dato-row">
            <span class="dato-lbl">Procedimiento</span>
            <span class="dato-val">{{ $cita->procedimiento }}</span>
        </div>
        <div class="dato-row">
            <span class="dato-lbl">Doctor</span>
            <span class="dato-val">{{ $cita->doctor ? $cita->doctor->name : '—' }}</span>
        </div>
        @if($cita->notas)
        <div class="dato-row">
            <span class="dato-lbl">Notas</span>
            <span class="dato-val" style="white-space:pre-line;">{{ $cita->notas }}</span>
        </div>
        @endif
        @if($cita->estado === 'cancelada' && $cita->motivo_cancelacion)
        <div class="dato-row">
            <span class="dato-lbl">Motivo cancelación</span>
            <span class="dato-val" style="color:#dc2626;">{{ $cita->motivo_cancelacion }}</span>
        </div>
        @endif
    </div>

    {{-- Historial del paciente --}}
    <div class="info-card">
        <div class="info-card-titulo"><i class="bi bi-clock-history"></i> Últimas citas del paciente</div>
        @forelse($otrasCitas as $oc)
        @php $oc_color = $colores[$oc->estado] ?? ['bg'=>'#f3f4f6','texto'=>'#374151']; @endphp
        <div class="historial-item">
            <span class="historial-fecha">{{ $oc->fecha->format('d/m/Y') }}</span>
            <span class="historial-proc">{{ $oc->procedimiento }}</span>
            <span class="badge-estado" style="background:{{ $oc_color['bg'] }};color:{{ $oc_color['texto'] }};font-size:.68rem;">
                {{ ucfirst(str_replace('_',' ',$oc->estado)) }}
            </span>
        </div>
        @empty
        <p style="font-size:.83rem;color:#9ca3af;margin:0;">Sin citas anteriores registradas.</p>
        @endforelse
    </div>
</div>

{{-- Datos del paciente --}}
<div>
    <div class="info-card">
        <div class="info-card-titulo"><i class="bi bi-person-circle"></i> Datos del paciente</div>
        <div class="dato-row">
            <span class="dato-lbl">Nombre</span>
            <span class="dato-val">{{ $cita->paciente->nombre_completo }}</span>
        </div>
        <div class="dato-row">
            <span class="dato-lbl">Documento</span>
            <span class="dato-val">{{ $cita->paciente->tipo_documento }} {{ $cita->paciente->numero_documento }}</span>
        </div>
        @if($cita->paciente->telefono)
        <div class="dato-row">
            <span class="dato-lbl">Teléfono</span>
            <span class="dato-val">{{ $cita->paciente->telefono }}</span>
        </div>
        @endif
        @if($cita->paciente->email)
        <div class="dato-row">
            <span class="dato-lbl">Email</span>
            <span class="dato-val">{{ $cita->paciente->email }}</span>
        </div>
        @endif
        <div style="margin-top:.75rem;">
            <a href="{{ route('pacientes.show', $cita->paciente) }}"
               style="font-size:.82rem;color:var(--color-principal);display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;">
                <i class="bi bi-box-arrow-up-right"></i> Ver ficha completa
            </a>
        </div>
    </div>
</div>

</div>

{{-- Modal cancelar --}}
<div id="modal-cancelar" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:440px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative;">
        <button onclick="cerrarModalCancelar()" style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer;">✕</button>
        <h5 style="font-weight:700;color:#1c2b22;margin-bottom:.35rem;"><i class="bi bi-x-circle" style="color:#dc2626;"></i> Cancelar cita</h5>
        <p style="font-size:.85rem;color:#6b7280;margin-bottom:1rem;">Indica el motivo para cancelar esta cita.</p>
        <form method="POST" action="{{ route('citas.cancelar', $cita) }}">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="font-size:.8rem;font-weight:600;color:#374151;display:block;margin-bottom:.3rem;">
                    Motivo <span style="color:#dc2626;">*</span>
                </label>
                <textarea name="motivo_cancelacion" rows="3" required
                    style="width:100%;border:1px solid var(--color-muy-claro);border-radius:8px;padding:.5rem .75rem;font-size:.875rem;outline:none;resize:vertical;font-family:inherit;"
                    placeholder="Ej: Paciente llamó para reagendar…"></textarea>
            </div>
            <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                <button type="button" onclick="cerrarModalCancelar()"
                    style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;cursor:pointer;">
                    Cerrar
                </button>
                <button type="submit"
                    style="background:#dc2626;color:#fff;border:none;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:600;cursor:pointer;">
                    <i class="bi bi-x-lg"></i> Confirmar cancelación
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function abrirModalCancelar() {
    document.getElementById('modal-cancelar').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function cerrarModalCancelar() {
    document.getElementById('modal-cancelar').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e){ if(e.key==='Escape') cerrarModalCancelar(); });
document.getElementById('modal-cancelar').addEventListener('click', function(e){ if(e.target===this) cerrarModalCancelar(); });
</script>
@endpush
