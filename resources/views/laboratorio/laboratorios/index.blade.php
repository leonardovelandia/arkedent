@extends('layouts.app')
@section('titulo', 'Laboratorios')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .tabla-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .tabla-header { padding:.875rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
    .tabla-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:600; color:var(--color-hover); }
    table { width:100%; border-collapse:collapse; }
    thead th { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#8fa39a; padding:.6rem 1rem; border-bottom:1px solid var(--fondo-borde); text-align:left; }
    tbody td { padding:.65rem 1rem; border-bottom:1px solid var(--fondo-borde); font-size:.855rem; color:#1c2b22; vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background:var(--fondo-app); }
    .badge-esp { display:inline-block; background:var(--color-muy-claro); color:var(--color-principal); padding:.15rem .55rem; border-radius:50px; font-size:.68rem; font-weight:600; margin:.1rem; }
    .accion-btn { display:inline-flex; align-items:center; gap:.2rem; padding:.3rem .6rem; border-radius:6px; font-size:.78rem; font-weight:500; text-decoration:none; border:none; cursor:pointer; transition:filter .15s; }
    .accion-ver  { background:var(--color-muy-claro); color:var(--color-principal); }
    .accion-edit { background:#e3f2fd; color:#1565c0; }
    .accion-btn:hover { filter:brightness(.92); }
    .vacio { padding:2.5rem; text-align:center; color:#8fa39a; }
    .vacio i { font-size:2rem; display:block; margin-bottom:.5rem; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
    <div>
        <h1 style="font-family:var(--fuente-titulos); font-size:1.3rem; color:#1c2b22; margin:0;">Gestión de Laboratorios</h1>
        <p style="font-size:.83rem; color:#8fa39a; margin:.2rem 0 0;">Administra los laboratorios dentales externos</p>
    </div>
    <a href="{{ route('laboratorio.index') }}"
       style="display:inline-flex;align-items:center;gap:.3rem;font-size:.83rem;color:var(--color-principal);text-decoration:none;border:1px solid var(--color-principal);border-radius:8px;padding:.4rem .9rem;">
        <i class="bi bi-arrow-left"></i> Volver a Órdenes
    </a>
</div>

<div class="tabla-card">
    <div class="tabla-header">
        <span class="tabla-titulo"><i class="bi bi-building" style="color:var(--color-principal);"></i> Laboratorios Registrados</span>
        <a href="{{ route('gestion-laboratorios.create') }}" class="btn-morado">
            <i class="bi bi-plus-lg"></i> Nuevo Laboratorio
        </a>
    </div>

    @if($laboratorios->count() > 0)
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Teléfono</th>
                    <th>WhatsApp</th>
                    <th>Especialidades</th>
                    <th>Entrega</th>
                    <th>Órdenes Activas</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laboratorios as $lab)
                <tr>
                    <td style="font-weight:600;">{{ $lab->nombre }}</td>
                    <td>{{ $lab->contacto ?: '—' }}</td>
                    <td>{{ $lab->telefono ?: '—' }}</td>
                    <td>
                        @if($lab->whatsapp)
                            <a href="https://wa.me/57{{ $lab->whatsapp }}" target="_blank" style="color:#25D366; text-decoration:none;">
                                <i class="bi bi-whatsapp"></i> {{ $lab->whatsapp }}
                            </a>
                        @else —
                        @endif
                    </td>
                    <td>
                        @if($lab->especialidades)
                            @foreach(array_slice($lab->especialidades, 0, 3) as $esp)
                                @php $labEsp = ['coronas_puentes'=>'Coronas','protesis_removible'=>'Prótesis Rem.','protesis_total'=>'Prótesis Tot.','implantologia'=>'Implantes','ortodoncia'=>'Ortodoncia','estetica'=>'Estética','cirugia'=>'Cirugía']; @endphp
                                <span class="badge-esp">{{ $labEsp[$esp] ?? $esp }}</span>
                            @endforeach
                            @if(count($lab->especialidades) > 3)
                                <span class="badge-esp">+{{ count($lab->especialidades) - 3 }}</span>
                            @endif
                        @else —
                        @endif
                    </td>
                    <td>
                        @if($lab->tiempo_entrega_dias)
                            <span style="background:var(--color-muy-claro); color:var(--color-principal); padding:.2rem .6rem; border-radius:50px; font-size:.75rem; font-weight:600;">
                                {{ $lab->tiempo_entrega_dias }} días
                            </span>
                        @else —
                        @endif
                    </td>
                    <td>
                        <span style="font-size:.9rem; font-weight:700; color:{{ $lab->ordenes_activas_count > 0 ? 'var(--color-principal)' : '#9ca3af' }};">
                            {{ $lab->ordenes_activas_count }}
                        </span>
                    </td>
                    <td>
                        @if($lab->activo)
                            <span style="background:#d4edda; color:#155724; padding:.2rem .6rem; border-radius:50px; font-size:.72rem; font-weight:700;">Activo</span>
                        @else
                            <span style="background:#f3f4f6; color:#6b7280; padding:.2rem .6rem; border-radius:50px; font-size:.72rem; font-weight:700;">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; gap:.3rem;">
                            <a href="{{ route('gestion-laboratorios.edit', $lab) }}" class="accion-btn accion-edit">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="vacio">
        <i class="bi bi-building"></i>
        <p>No hay laboratorios registrados</p>
    </div>
    @endif
</div>

@endsection
