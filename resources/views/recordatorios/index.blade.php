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
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:600; display:inline-flex; align-items:center; gap:.4rem; cursor:pointer; transition:filter .15s; text-decoration:none;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
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

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo"><i class="bi bi-bell-fill me-2"></i>Recordatorios</h1>
        <p class="page-subtitulo">Historial de recordatorios automáticos enviados a pacientes</p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
        <a href="{{ route('recordatorios.configuracion') }}"
           style="background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem 1.1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.4rem;text-decoration:none;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
            <i class="bi bi-gear"></i> Configuración
        </a>
        <button type="button" class="btn-morado" id="btn-enviar-ahora">
            <i class="bi bi-send-fill"></i> Enviar recordatorios ahora
        </button>
    </div>
</div>

{{-- Cards resumen --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:#22c55e;">{{ $enviadosHoy }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Enviados hoy</div>
            <i class="bi bi-check-circle" style="font-size:1.2rem;color:#22c55e;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:#f59e0b;">{{ $pendientes }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Pendientes</div>
            <i class="bi bi-clock" style="font-size:1.2rem;color:#f59e0b;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:#dc2626;">{{ $fallidos }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Fallidos hoy</div>
            <i class="bi bi-x-circle" style="font-size:1.2rem;color:#dc2626;opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-sistema" style="text-align:center;padding:1.1rem;">
            <div style="font-size:1.9rem;font-weight:800;color:var(--color-principal);">{{ $totalMes }}</div>
            <div style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem;">Total este mes</div>
            <i class="bi bi-calendar-check" style="font-size:1.2rem;color:var(--color-principal);opacity:.35;margin-top:.25rem;display:block;"></i>
        </div>
    </div>
</div>

<x-tabla-listado
    :paginacion="$recordatorios"
    placeholder="Buscar paciente..."
    icono-vacio="bi-bell-slash"
    mensaje-vacio="No hay recordatorios registrados"
>
    <x-slot:filtros>
        <select name="canal" class="tbl-filtro-select">
            <option value="">Todos los canales</option>
            <option value="email"    {{ request('canal')==='email'    ? 'selected' : '' }}>Email</option>
            <option value="whatsapp" {{ request('canal')==='whatsapp' ? 'selected' : '' }}>WhatsApp</option>
        </select>
        <select name="estado" class="tbl-filtro-select">
            <option value="">Todos los estados</option>
            <option value="enviado"   {{ request('estado')==='enviado'   ? 'selected' : '' }}>Enviado</option>
            <option value="pendiente" {{ request('estado')==='pendiente' ? 'selected' : '' }}>Pendiente</option>
            <option value="fallido"   {{ request('estado')==='fallido'   ? 'selected' : '' }}>Fallido</option>
            <option value="cancelado" {{ request('estado')==='cancelado' ? 'selected' : '' }}>Cancelado</option>
        </select>
        <input type="date" name="fecha" class="tbl-filtro-date" value="{{ request('fecha') }}" title="Fecha">
    </x-slot:filtros>

    <x-slot:thead>
        <tr>
            <th>Paciente</th>
            <th>Cita</th>
            <th>Canal</th>
            <th>Programado</th>
            <th>Estado</th>
            <th>Enviado</th>
            <th style="text-align:center;">Acciones</th>
        </tr>
    </x-slot:thead>

    @foreach($recordatorios as $rec)
    <tr>
        <td>
            <a href="{{ route('pacientes.show', $rec->paciente_id) }}"
               style="color:var(--color-principal);font-weight:500;text-decoration:none;">
                {{ $rec->paciente->nombre_completo ?? '—' }}
            </a>
        </td>
        <td style="font-size:.8rem;color:#4b5563;">
            @if($rec->cita)
            {{ $rec->cita->fecha->format('d/m/Y') }}
            <span style="color:#9ca3af;">{{ date('h:i A', strtotime($rec->cita->hora_inicio)) }}</span>
            @else —@endif
        </td>
        <td>
            @if($rec->canal === 'email')
            <span style="background:#dbeafe;color:#1d4ed8;border-radius:20px;padding:.12rem .6rem;font-size:.7rem;font-weight:700;">
                <i class="bi bi-envelope-fill"></i> Email
            </span>
            @else
            <span style="background:#dcfce7;color:#15803d;border-radius:20px;padding:.12rem .6rem;font-size:.7rem;font-weight:700;">
                <i class="bi bi-whatsapp"></i> WhatsApp
            </span>
            @endif
        </td>
        <td style="font-size:.8rem;color:#5c6b62;">
            {{ $rec->fecha_programada?->format('d/m/Y H:i') ?? '—' }}
        </td>
        <td>
            @php
                $estadoBadges = [
                    'enviado'   => ['#d4edda','#155724'],
                    'pendiente' => ['#fff3cd','#856404'],
                    'fallido'   => ['#f8d7da','#721c24'],
                    'cancelado' => ['#e2e3e5','#383d41'],
                ];
                $bc = $estadoBadges[$rec->estado] ?? ['#f3f4f6','#374151'];
            @endphp
            <span style="background:{{ $bc[0] }};color:{{ $bc[1] }};border-radius:20px;padding:.12rem .65rem;font-size:.7rem;font-weight:700;">
                {{ ucfirst($rec->estado) }}
            </span>
        </td>
        <td style="font-size:.8rem;color:#5c6b62;">
            {{ $rec->fecha_envio?->format('d/m/Y H:i') ?? '—' }}
        </td>
        <td style="text-align:center;">
            @if(in_array($rec->estado, ['fallido', 'pendiente']))
            <form method="POST" action="{{ route('recordatorios.enviar', $rec->id) }}" style="margin:0;display:inline;">
                @csrf
                <button type="submit" class="tbl-btn-accion success" title="Reenviar">
                    <i class="bi bi-arrow-repeat"></i>
                </button>
            </form>
            @endif
        </td>
    </tr>
    @endforeach

</x-tabla-listado>

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
