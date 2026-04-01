@extends('layouts.app')
@section('titulo', 'Recordatorios')

@push('estilos')
<style>
    .card-sistema { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1.25rem 1.5rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .resumen-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem; }
    .resumen-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1.1rem 1.25rem; text-align:center; }
    .resumen-numero { font-size:1.75rem; font-weight:700; line-height:1; margin-bottom:.25rem; }
    .resumen-label { font-size:.75rem; color:#8fa39a; text-transform:uppercase; letter-spacing:.05em; }
    .badge-canal { font-size:.72rem; padding:.2rem .65rem; border-radius:50px; font-weight:600; }
    .badge-email { background:#dbeafe; color:#1d4ed8; }
    .badge-whatsapp { background:#dcfce7; color:#15803d; }
    .badge-estado { font-size:.72rem; padding:.2rem .65rem; border-radius:50px; font-weight:600; }
    .badge-enviado  { background:#d4edda; color:#155724; }
    .badge-pendiente{ background:#fff3cd; color:#856404; }
    .badge-fallido  { background:#f8d7da; color:#721c24; }
    .badge-cancelado{ background:#e2e3e5; color:#383d41; }
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:600; display:inline-flex; align-items:center; gap:.4rem; cursor:pointer; transition:filter .15s; text-decoration:none; }
    .btn-morado:hover { filter:brightness(1.1); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; }
    .btn-gris:hover { background:#e5e7eb; }
    .filtro-input { border:1px solid var(--fondo-borde); border-radius:8px; padding:.4rem .75rem; font-size:.85rem; background:var(--fondo-app); color:var(--color-texto); }
    .filtro-input:focus { outline:none; border-color:var(--color-principal); }
    table { width:100%; border-collapse:collapse; }
    th { font-size:.72rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#8fa39a; padding:.75rem 1rem; text-align:left; border-bottom:1px solid var(--fondo-borde); }
    td { padding:.75rem 1rem; font-size:.85rem; color:#1c2b22; border-bottom:1px solid var(--fondo-borde); vertical-align:middle; }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:var(--fondo-card-alt); }
    @media(max-width:768px) { .resumen-grid { grid-template-columns:1fr 1fr; } }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif
@if(session('error'))
<div class="alerta-flash" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;">
    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
</div>
@endif

{{-- Encabezado --}}
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.75rem;margin-bottom:1.5rem;">
    <div>
        <h2 style="font-family:var(--fuente-titulos);font-size:1.3rem;font-weight:600;color:var(--color-hover);margin:0;">
            <i class="bi bi-bell-fill" style="color:var(--color-principal);"></i> Recordatorios
        </h2>
        <p style="font-size:.82rem;color:#8fa39a;margin:0;">Historial de recordatorios automáticos enviados a pacientes</p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
        <button type="button" class="btn-morado" id="btn-enviar-ahora">
            <i class="bi bi-send-fill"></i> Enviar recordatorios ahora
        </button>
        <a href="{{ route('recordatorios.configuracion') }}" class="btn-gris">
            <i class="bi bi-gear"></i> Configuración
        </a>
    </div>
</div>

{{-- Cards resumen --}}
<div class="resumen-grid">
    <div class="resumen-card">
        <div class="resumen-numero" style="color:#155724;">{{ $enviadosHoy }}</div>
        <div class="resumen-label">Enviados hoy</div>
    </div>
    <div class="resumen-card">
        <div class="resumen-numero" style="color:#856404;">{{ $pendientes }}</div>
        <div class="resumen-label">Pendientes</div>
    </div>
    <div class="resumen-card">
        <div class="resumen-numero" style="color:#721c24;">{{ $fallidos }}</div>
        <div class="resumen-label">Fallidos hoy</div>
    </div>
    <div class="resumen-card">
        <div class="resumen-numero" style="color:var(--color-principal);">{{ $totalMes }}</div>
        <div class="resumen-label">Total este mes</div>
    </div>
</div>

{{-- Filtros --}}
<div class="card-sistema" style="margin-bottom:1rem;">
    <form method="GET" action="{{ route('recordatorios.index') }}" style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:flex-end;">
        <div>
            <label style="font-size:.75rem;color:#8fa39a;display:block;margin-bottom:.25rem;">Fecha</label>
            <input type="date" name="fecha" value="{{ request('fecha') }}" class="filtro-input">
        </div>
        <div>
            <label style="font-size:.75rem;color:#8fa39a;display:block;margin-bottom:.25rem;">Canal</label>
            <select name="canal" class="filtro-input">
                <option value="">Todos</option>
                <option value="email"     {{ request('canal') === 'email'     ? 'selected' : '' }}>Email</option>
                <option value="whatsapp"  {{ request('canal') === 'whatsapp'  ? 'selected' : '' }}>WhatsApp</option>
            </select>
        </div>
        <div>
            <label style="font-size:.75rem;color:#8fa39a;display:block;margin-bottom:.25rem;">Estado</label>
            <select name="estado" class="filtro-input">
                <option value="">Todos</option>
                <option value="enviado"   {{ request('estado') === 'enviado'   ? 'selected' : '' }}>Enviado</option>
                <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="fallido"   {{ request('estado') === 'fallido'   ? 'selected' : '' }}>Fallido</option>
                <option value="cancelado" {{ request('estado') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
            </select>
        </div>
        <button type="submit" class="btn-gris"><i class="bi bi-search"></i> Filtrar</button>
        <a href="{{ route('recordatorios.index') }}" class="btn-gris"><i class="bi bi-x"></i> Limpiar</a>
    </form>
</div>

{{-- Tabla --}}
<div class="card-sistema">
    @if($recordatorios->isEmpty())
    <div style="text-align:center;padding:3rem 1rem;color:#9ca3af;">
        <i class="bi bi-bell-slash" style="font-size:2.5rem;display:block;margin-bottom:.75rem;color:var(--color-acento-activo);"></i>
        No hay recordatorios que coincidan con los filtros.
    </div>
    @else
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Cita</th>
                    <th>Canal</th>
                    <th>Programado</th>
                    <th>Estado</th>
                    <th>Enviado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recordatorios as $rec)
                <tr>
                    <td>
                        <a href="{{ route('pacientes.show', $rec->paciente_id) }}"
                           style="color:var(--color-principal);font-weight:500;text-decoration:none;">
                            {{ $rec->paciente->nombre_completo ?? '—' }}
                        </a>
                    </td>
                    <td style="font-size:.8rem;">
                        @if($rec->cita)
                            {{ $rec->cita->fecha->format('d/m/Y') }}
                            <span style="color:#8fa39a;">{{ date('h:i A', strtotime($rec->cita->hora_inicio)) }}</span>
                        @else —@endif
                    </td>
                    <td>
                        <span class="badge-canal badge-{{ $rec->canal }}">
                            <i class="bi bi-{{ $rec->canal === 'email' ? 'envelope-fill' : 'whatsapp' }}"></i>
                            {{ ucfirst($rec->canal) }}
                        </span>
                    </td>
                    <td style="font-size:.8rem;color:#5c6b62;">
                        {{ $rec->fecha_programada?->format('d/m/Y H:i') ?? '—' }}
                    </td>
                    <td>
                        <span class="badge-estado badge-{{ $rec->estado }}">{{ ucfirst($rec->estado) }}</span>
                    </td>
                    <td style="font-size:.8rem;color:#5c6b62;">
                        {{ $rec->fecha_envio?->format('d/m/Y H:i') ?? '—' }}
                    </td>
                    <td>
                        @if(in_array($rec->estado, ['fallido', 'pendiente']))
                        <form method="POST" action="{{ route('recordatorios.enviar', $rec->id) }}" style="display:inline;">
                            @csrf
                            <button type="submit"
                                style="font-size:.75rem;padding:.25rem .7rem;border:1px solid var(--color-principal);border-radius:6px;background:var(--color-muy-claro);color:var(--color-principal);cursor:pointer;">
                                <i class="bi bi-arrow-repeat"></i> Reenviar
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem;">
        {{ $recordatorios->links() }}
    </div>
    @endif
</div>

{{-- Toast resultado envío masivo --}}
<div id="toast-envio" style="display:none;position:fixed;bottom:1.5rem;right:1.5rem;background:#1c2b22;color:white;padding:.875rem 1.25rem;border-radius:10px;font-size:.85rem;z-index:9999;max-width:340px;box-shadow:0 8px 24px rgba(0,0,0,.25);">
    <span id="toast-msg"></span>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('btn-enviar-ahora').addEventListener('click', function () {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span style="opacity:.7;">Procesando...</span>';

    fetch('{{ route('recordatorios.enviar-ahora') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        const toast = document.getElementById('toast-envio');
        document.getElementById('toast-msg').textContent = data.detalle || data.mensaje;
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; window.location.reload(); }, 3500);
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill"></i> Enviar recordatorios ahora';
        alert('Error al procesar. Verifica la conexión.');
    });
});
</script>
@endpush
