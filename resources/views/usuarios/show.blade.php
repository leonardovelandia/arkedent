@extends('layouts.app')
@section('titulo', 'Usuario: ' . $usuario->name)

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; }
    .btn-rojo { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; cursor:pointer; }
    .doc-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:14px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1.25rem; }
    .doc-seccion { padding:1.25rem 1.5rem; border-bottom:1px solid var(--fondo-borde); }
    .doc-seccion:last-child { border-bottom:none; }
    .seccion-titulo { background:var(--color-muy-claro); margin:-1.25rem -1.5rem 1rem; padding:.5rem 1.5rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-hover); margin-bottom:.9rem; display:flex; align-items:center; gap:.4rem; }
    .campo-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(200px,1fr)); gap:.6rem 1.5rem; }
    .campo { margin-bottom:.2rem; }
    .campo-label { font-size:.72rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.04em; }
    .campo-valor { font-size:.9rem; color:#1c2b22; margin-top:.1rem; }
    .badge-rol { display:inline-flex; align-items:center; gap:.3rem; padding:.3rem .8rem; border-radius:50px; font-size:.8rem; font-weight:600; }
    .rol-doctora { background:var(--color-muy-claro); color:var(--color-principal); }
    .rol-administrador { background:#dbeafe; color:#1d4ed8; }
    .rol-asistente { background:#d1fae5; color:#065f46; }
    .avatar-grande { width:70px; height:70px; border-radius:50%; background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; font-size:1.6rem; font-weight:600; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
</style>
@endpush

@section('contenido')

{{-- Cabecera --}}
<div style="background:linear-gradient(135deg,var(--color-principal),var(--color-sidebar-2));border-radius:16px;padding:1.5rem;margin-bottom:1.25rem;color:#fff;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div style="display:flex;align-items:center;gap:1rem;">
            <a href="{{ route('usuarios.index') }}"
               style="background:rgba(255,255,255,.15);color:#fff;border-radius:8px;width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;flex-shrink:0;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div class="avatar-grande">
                {{ strtoupper(substr($usuario->name,0,1)) }}{{ strtoupper(substr(explode(' ',$usuario->name.' ')[1]??'',0,1)) }}
            </div>
            <div>
                <h4 style="font-family:var(--fuente-titulos);font-weight:700;margin:0;font-size:1.3rem;">{{ $usuario->name }}</h4>
                <div style="font-size:.85rem;opacity:.8;margin-top:.2rem;">{{ $usuario->email }}</div>
                <div style="margin-top:.4rem;">
                    @forelse($usuario->roles as $r)
                    <span class="badge-rol rol-{{ $r->name }}">
                        <i class="bi bi-shield-check"></i> {{ ucfirst($r->name) }}
                    </span>
                    @empty
                    <span style="font-size:.78rem;opacity:.7;">Sin rol asignado</span>
                    @endforelse
                </div>
            </div>
        </div>
        <div style="display:flex;gap:.5rem;">
            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn-morado" style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);">
                <i class="bi bi-pencil"></i> Editar
            </a>
        </div>
    </div>
</div>

<div class="doc-card">
    <div class="doc-seccion">
        <div class="seccion-titulo"><i class="bi bi-info-circle"></i> Información de la cuenta</div>
        <div class="campo-grid">
            <div class="campo">
                <div class="campo-label">Nombre</div>
                <div class="campo-valor">{{ $usuario->name }}</div>
            </div>
            <div class="campo">
                <div class="campo-label">Email</div>
                <div class="campo-valor">{{ $usuario->email }}</div>
            </div>
            <div class="campo">
                <div class="campo-label">Email verificado</div>
                <div class="campo-valor">
                    @if($usuario->email_verified_at)
                    <span style="color:#166534;"><i class="bi bi-check-circle-fill"></i> {{ $usuario->email_verified_at->format('d/m/Y') }}</span>
                    @else
                    <span style="color:#dc2626;"><i class="bi bi-x-circle"></i> No verificado</span>
                    @endif
                </div>
            </div>
            <div class="campo">
                <div class="campo-label">Creado el</div>
                <div class="campo-valor">{{ $usuario->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="campo">
                <div class="campo-label">Última actualización</div>
                <div class="campo-valor">{{ $usuario->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>

    <div class="doc-seccion">
        <div class="seccion-titulo"><i class="bi bi-shield-check"></i> Rol y permisos</div>
        @forelse($usuario->roles as $r)
        @php
            $descripcion = match($r->name) {
                'doctora'       => 'Acceso total al sistema.',
                'administrador' => 'Gestión administrativa.',
                'asistente'     => 'Atención al paciente.',
                default         => ''
            };
        @endphp
        <div style="background:var(--fondo-card-alt);border:1px solid var(--color-muy-claro);border-radius:10px;padding:.75rem 1rem;margin-bottom:.5rem;">
            <div style="display:flex;align-items:center;gap:.5rem;font-weight:600;color:#1c2b22;margin-bottom:.2rem;">
                <i class="bi bi-shield-fill-check" style="color:var(--color-principal);"></i> {{ ucfirst($r->name) }}
            </div>
            @if($descripcion)<div style="font-size:.82rem;color:#6b7280;">{{ $descripcion }}</div>@endif
        </div>
        @empty
        <div style="color:#9ca3af;font-size:.875rem;">Sin rol asignado.</div>
        @endforelse
    </div>

    <div class="doc-seccion">
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn-morado">
                <i class="bi bi-pencil"></i> Editar usuario
            </a>
            @if($usuario->id !== auth()->id())
            <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}"
                  onsubmit="return confirm('¿Eliminar el usuario {{ $usuario->name }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-rojo">
                    <i class="bi bi-trash3"></i> Eliminar
                </button>
            </form>
            @endif
            <a href="{{ route('usuarios.index') }}" class="btn-gris">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

@endsection
