@extends('layouts.app')
@section('titulo', 'Usuarios y Roles')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.4rem .85rem; font-size:.82rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; color:#374151; }
    .btn-rojo { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; border-radius:8px; padding:.4rem .85rem; font-size:.82rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; cursor:pointer; }
    .btn-rojo:hover { background:#fee2e2; }
    .tabla-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:14px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .tabla-header { background:var(--color-muy-claro); padding:1rem 1.25rem; display:flex; justify-content:space-between; align-items:center; }
    .tabla-search { background:#fff; border-bottom:1px solid var(--color-muy-claro); padding:.75rem 1.25rem; display:flex; gap:.75rem; flex-wrap:wrap; align-items:center; }
    .inp-buscar { border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .75rem; font-size:.875rem; color:#1c2b22; outline:none; }
    .inp-buscar:focus { border-color:var(--color-principal); }
    table { width:100%; border-collapse:collapse; }
    thead tr { background:var(--color-muy-claro); }
    th { padding:.6rem 1rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-principal); text-align:left; border-bottom:2px solid var(--color-muy-claro); }
    td { padding:.65rem 1rem; font-size:.875rem; color:#1c2b22; border-bottom:1px solid var(--fondo-borde); vertical-align:middle; }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:var(--fondo-card-alt); }
    .badge-rol { display:inline-flex; align-items:center; gap:.3rem; padding:.25rem .65rem; border-radius:50px; font-size:.72rem; font-weight:600; }
    .rol-doctora { background:var(--color-muy-claro); color:var(--color-principal); }
    .rol-administrador { background:#dbeafe; color:#1d4ed8; }
    .rol-asistente { background:#d1fae5; color:#065f46; }
    .rol-default { background:#f3f4f6; color:#374151; }
    .avatar-circulo { width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; font-size:.8rem; font-weight:600; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .empty-state { text-align:center; padding:3rem 1rem; color:#9ca3af; }
</style>
@endpush

@section('contenido')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem;">
    <div style="display:flex;align-items:center;gap:.75rem;">
        <div style="width:40px;height:40px;background:linear-gradient(135deg,var(--color-principal),var(--color-claro));border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem;">
            <i class="bi bi-people-fill"></i>
        </div>
        <div>
            <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Usuarios y Roles</h4>
            <p style="font-size:.82rem;color:#9ca3af;margin:0;">Gestión de usuarios del sistema</p>
        </div>
    </div>
    <a href="{{ route('usuarios.create') }}" class="btn-morado">
        <i class="bi bi-plus-lg"></i> Nuevo usuario
    </a>
</div>

<div class="tabla-card">
    <div class="tabla-header">
        <div style="font-family:var(--fuente-principal);font-weight:700;font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;color:var(--color-hover);"><i class="bi bi-people"></i> Usuarios registrados</div>
        <div style="font-size:.82rem;color:#9ca3af;">{{ $usuarios->total() }} usuario(s)</div>
    </div>

    <div class="tabla-search">
        <form method="GET" action="{{ route('usuarios.index') }}" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;width:100%;">
            <input type="text" name="buscar" class="inp-buscar" placeholder="Buscar por nombre o email…"
                   value="{{ $buscar }}" style="flex:1;min-width:200px;">
            <select name="rol" class="inp-buscar" style="min-width:150px;">
                <option value="">Todos los roles</option>
                @foreach($roles as $r)
                <option value="{{ $r }}" {{ $rol == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-morado" style="padding:.45rem .9rem;">
                <i class="bi bi-search"></i> Buscar
            </button>
            @if($buscar || $rol)
            <a href="{{ route('usuarios.index') }}" class="btn-gris">
                <i class="bi bi-x-lg"></i> Limpiar
            </a>
            @endif
        </form>
    </div>

    @if($usuarios->isEmpty())
    <div class="empty-state">
        <i class="bi bi-people" style="font-size:2.5rem;display:block;margin-bottom:.75rem;color:var(--color-claro);"></i>
        <div style="font-weight:600;color:#6b7280;">No hay usuarios registrados</div>
        <a href="{{ route('usuarios.create') }}" class="btn-morado" style="margin-top:1rem;">
            <i class="bi bi-plus-lg"></i> Crear primer usuario
        </a>
    </div>
    @else
    <table>
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Registrado</th>
                <th style="text-align:right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $u)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:.65rem;">
                        <div class="avatar-circulo">{{ strtoupper(substr($u->name,0,1)) }}{{ strtoupper(substr(explode(' ',$u->name.' ')[1]??'',0,1)) }}</div>
                        <div>
                            <div style="font-weight:600;">{{ $u->name }}</div>
                            @if($u->id === auth()->id())
                            <div style="font-size:.72rem;color:var(--color-principal);font-weight:500;">Tú</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td style="color:#6b7280;">{{ $u->email }}</td>
                <td>
                    @forelse($u->roles as $r)
                    @php $labelRol = match($r->name) { 'doctora'=>'Doctor(a)', 'administrador'=>'Administrador', 'asistente'=>'Asistente', default=>ucfirst($r->name) }; @endphp
                    <span class="badge-rol rol-{{ $r->name }}">
                        <i class="bi bi-shield-check"></i> {{ $labelRol }}
                    </span>
                    @empty
                    <span style="font-size:.78rem;color:#9ca3af;">Sin rol</span>
                    @endforelse
                </td>
                <td style="color:#9ca3af;font-size:.82rem;">{{ $u->created_at->format('d/m/Y') }}</td>
                <td>
                    <div style="display:flex;justify-content:flex-end;gap:.35rem;">
                        <a href="{{ route('usuarios.show', $u) }}" class="btn-gris">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('usuarios.edit', $u) }}" class="btn-gris">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if($u->id !== auth()->id())
                        <form method="POST" action="{{ route('usuarios.destroy', $u) }}"
                              onsubmit="return confirm('¿Eliminar el usuario {{ $u->name }}? Esta acción no se puede deshacer.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-rojo">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($usuarios->hasPages())
    <div style="padding:.75rem 1.25rem;border-top:1px solid var(--fondo-borde);">
        {{ $usuarios->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
