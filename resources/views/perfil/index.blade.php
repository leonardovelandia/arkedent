@extends('layouts.app')
@section('titulo', 'Mi Perfil')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .form-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:14px; padding:1.5rem; margin-bottom:1.25rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .seccion-titulo { background:var(--color-muy-claro); margin:-1.5rem -1.5rem 1rem; padding:.5rem 1.5rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-hover); border-bottom:1px solid var(--color-muy-claro); padding-bottom:.4rem; margin-bottom:1rem; }
    .form-label { font-size:.82rem; font-weight:700; color:var(--color-hover); display:block; margin-bottom:.3rem; }
    .form-input { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.5rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; transition:border-color .15s; }
    .form-input:focus { border-color:var(--color-principal); }
    .form-group { margin-bottom:1rem; }
    .form-error { font-size:.78rem; color:#dc2626; margin-top:.25rem; }
    .aviso { background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:.6rem .9rem;font-size:.8rem;color:#92400e;margin-bottom:1rem; }
    .badge-rol { display:inline-flex; align-items:center; gap:.3rem; padding:.3rem .8rem; border-radius:50px; font-size:.8rem; font-weight:600; }
    .rol-doctora { background:var(--color-muy-claro); color:var(--color-principal); }
    .rol-administrador { background:#dbeafe; color:#1d4ed8; }
    .rol-asistente { background:#d1fae5; color:#065f46; }
    .avatar-grande { width:70px; height:70px; border-radius:50%; background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; font-size:1.6rem; font-weight:600; display:flex; align-items:center; justify-content:center; }

    /* ── Classic overrides ── */
    body:not([data-ui="glass"]) .form-card  { background:#fff; border:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .seccion-titulo { background:var(--color-muy-claro); color:var(--color-hover); border-bottom:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .form-label { color:var(--color-hover); }
    body:not([data-ui="glass"]) .form-input { border:1.5px solid var(--color-muy-claro); color:#1c2b22; background:#fff; }

    /* ── Aurora Glass overrides ── */
    body[data-ui="glass"] .form-card  { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .seccion-titulo { background:rgba(0,0,0,0.25) !important; color:rgba(0,234,255,0.90) !important; border-bottom:1px solid rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .form-label  { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .form-input  { background:rgba(255,255,255,0.08) !important; border:1.5px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .form-input:focus { border-color:rgba(0,234,255,0.70) !important; box-shadow:none !important; }
    body[data-ui="glass"] .form-input::placeholder { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .aviso { background:rgba(251,191,36,0.12) !important; border:1px solid rgba(251,191,36,0.35) !important; color:#fde68a !important; }
    body[data-ui="glass"] .rol-doctora     { background:rgba(0,234,255,0.15) !important; color:rgba(0,234,255,0.95) !important; }
    body[data-ui="glass"] .rol-administrador { background:rgba(30,64,175,0.22) !important; color:#93c5fd !important; }
    body[data-ui="glass"] .rol-asistente   { background:rgba(22,101,52,0.22) !important; color:#86efac !important; }
</style>
@endpush

@section('contenido')

<div style="background:linear-gradient(135deg,var(--color-principal),var(--color-sidebar-2));border-radius:16px;padding:1.5rem;margin-bottom:1.25rem;color:#fff;">
    <div style="display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap;">
        <div class="avatar-grande">
            {{ strtoupper(substr($usuario->name,0,1)) }}{{ strtoupper(substr(explode(' ',$usuario->name.' ')[1]??'',0,1)) }}
        </div>
        <div>
            <h4 style="font-family:var(--fuente-titulos);font-weight:700;margin:0;font-size:1.3rem;">{{ $usuario->name }}</h4>
            <div style="font-size:.85rem;opacity:.8;margin-top:.2rem;">{{ $usuario->email }}</div>
            <div style="margin-top:.5rem;display:flex;gap:.4rem;flex-wrap:wrap;">
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
</div>

<form method="POST" action="{{ route('perfil.update') }}">
@csrf
@method('PUT')

<div class="form-card">
    <div class="seccion-titulo"><i class="bi bi-person"></i> Datos personales</div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div class="form-group">
            <label class="form-label">Nombre completo <span style="color:#dc2626;">*</span></label>
            <input type="text" name="name" class="form-input"
                   value="{{ old('name', $usuario->name) }}" required maxlength="255">
            @error('name')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Correo electrónico <span style="color:#dc2626;">*</span></label>
            <input type="email" name="email" class="form-input"
                   value="{{ old('email', $usuario->email) }}" required maxlength="255">
            @error('email')<div class="form-error">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="form-card">
    <div class="seccion-titulo"><i class="bi bi-key"></i> Cambiar contraseña</div>
    <div class="aviso"><i class="bi bi-info-circle"></i> Completa estos campos solo si deseas cambiar tu contraseña.</div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
        <div class="form-group">
            <label class="form-label">Contraseña actual</label>
            <input type="password" name="password_actual" id="inp-actual" class="form-input" placeholder="Tu contraseña actual">
            @error('password_actual')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Nueva contraseña</label>
            <input type="password" name="password" id="inp-nueva" class="form-input" placeholder="Mínimo 8 caracteres">
            @error('password')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Confirmar nueva contraseña</label>
            <input type="password" name="password_confirmation" class="form-input" placeholder="Repite la contraseña">
        </div>
    </div>
    <label style="display:inline-flex;align-items:center;gap:.4rem;font-size:.82rem;color:#6b7280;cursor:pointer;">
        <input type="checkbox" onchange="document.querySelectorAll('#inp-actual,#inp-nueva').forEach(i=>i.type=this.checked?'text':'password');">
        Mostrar contraseñas
    </label>
</div>

<div style="display:flex;gap:.5rem;flex-wrap:wrap;">
    <button type="submit" class="btn-morado">
        <i class="bi bi-check-circle"></i> Guardar cambios
    </button>
</div>

</form>

<div style="margin-top:1.5rem;background:#fff;border:1px solid var(--color-muy-claro);border-radius:14px;padding:1.25rem 1.5rem;">
    <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--color-principal);margin-bottom:.75rem;"><i class="bi bi-info-circle"></i> Información de la cuenta</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.6rem 1.5rem;font-size:.875rem;">
        <div>
            <div style="font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.04em;">Miembro desde</div>
            <div style="margin-top:.1rem;">{{ $usuario->created_at->format('d/m/Y') }}</div>
        </div>
        <div>
            <div style="font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.04em;">Email verificado</div>
            <div style="margin-top:.1rem;">
                @if($usuario->email_verified_at)
                <span style="color:#166534;"><i class="bi bi-check-circle-fill"></i> Sí</span>
                @else
                <span style="color:#dc2626;"><i class="bi bi-x-circle"></i> No</span>
                @endif
            </div>
        </div>
        <div>
            <div style="font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.04em;">Rol asignado</div>
            <div style="margin-top:.1rem;">{{ $usuario->roles->pluck('name')->map('ucfirst')->join(', ') ?: 'Sin rol' }}</div>
        </div>
    </div>
</div>

@endsection
