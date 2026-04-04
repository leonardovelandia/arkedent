@extends('layouts.app')
@section('titulo', 'Editar Usuario')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; }
    .form-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:14px; padding:1.5rem; margin-bottom:1.25rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .seccion-titulo { background:var(--color-muy-claro); margin:-1.25rem -1.5rem 1rem; padding:.5rem 1.5rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-hover); border-bottom:1px solid var(--color-muy-claro); padding-bottom:.4rem; margin-bottom:1rem; }
    .form-label { font-size:.82rem; font-weight:700; color:var(--color-hover); display:block; margin-bottom:.3rem; }
    .form-input { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.5rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; transition:border-color .15s; }
    .form-input:focus { border-color:var(--color-principal); }
    .form-group { margin-bottom:1rem; }
    .form-error { font-size:.78rem; color:#dc2626; margin-top:.25rem; }
    .rol-card { border:2px solid var(--color-muy-claro); border-radius:10px; padding:.75rem 1rem; cursor:pointer; transition:all .15s; margin-bottom:.5rem; }
    .rol-card:has(input:checked) { border-color:var(--color-principal); background:var(--fondo-card-alt); }
    .rol-card input[type="radio"] { accent-color:var(--color-principal); }
    .aviso-pass { background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:.6rem .9rem;font-size:.8rem;color:#92400e;margin-bottom:1rem; }

    /* Clásico */
    body:not([data-ui="glass"]) .form-card { background:#fff; border:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .seccion-titulo { background:var(--color-muy-claro); color:var(--color-hover); }
    body:not([data-ui="glass"]) .form-label { color:var(--color-hover); }
    body:not([data-ui="glass"]) .form-input { color:#1c2b22; background:#fff; border:1.5px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .rol-card { border-color:var(--color-muy-claro); }
    body:not([data-ui="glass"]) .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }
    body:not([data-ui="glass"]) .aviso-pass { background:#fffbeb; border-color:#fde68a; color:#92400e; }

    /* Glass */
    body[data-ui="glass"] .form-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .seccion-titulo { background:rgba(0,0,0,0.25) !important; color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .form-label { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .form-input { background:rgba(255,255,255,0.08) !important; border:1.5px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .form-input:focus { border-color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .form-input::placeholder { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .rol-card { border-color:rgba(0,234,255,0.30) !important; background:rgba(255,255,255,0.04) !important; }
    body[data-ui="glass"] .rol-card:has(input:checked) { border-color:rgba(0,234,255,0.70) !important; background:rgba(0,234,255,0.08) !important; }
    body[data-ui="glass"] .aviso-pass { background:rgba(251,191,36,0.12) !important; border-color:rgba(251,191,36,0.35) !important; color:#fbbf24 !important; }
    body[data-ui="glass"] .btn-gris { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
    body[data-ui="glass"] .page-title-main { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .btn-back { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
</style>
@endpush

@section('contenido')

<div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;">
    <a href="{{ route('usuarios.show', $usuario) }}"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Editar: {{ $usuario->name }}</h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">Modificar datos de acceso y rol</p>
    </div>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;">
    <div style="font-weight:600;margin-bottom:.35rem;"><i class="bi bi-exclamation-circle"></i> Corrija los errores:</div>
    <ul style="margin:0;padding-left:1.2rem;font-size:.84rem;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('usuarios.update', $usuario) }}">
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
    <div class="aviso-pass"><i class="bi bi-info-circle"></i> Deja los campos en blanco si no deseas cambiar la contraseña.</div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div class="form-group">
            <label class="form-label">Nueva contraseña</label>
            <div style="position:relative;">
                <input type="password" name="password" id="inp-pass" class="form-input"
                       placeholder="Nueva contraseña segura" oninput="validarPass(this.value)"
                       style="padding-right:2.5rem;">
                <button type="button" onclick="togglePass('inp-pass','ojo-pass')"
                        style="position:absolute;right:.65rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:0;line-height:1;">
                    <i class="bi bi-eye" id="ojo-pass"></i>
                </button>
            </div>
            @error('password')<div class="form-error">{{ $message }}</div>@enderror
            <div id="req-pass" style="margin-top:.5rem;background:var(--fondo-card-alt);border:1px solid var(--color-muy-claro);border-radius:8px;padding:.6rem .8rem;font-size:.78rem;display:none;">
                <div style="font-weight:600;color:var(--color-hover);margin-bottom:.35rem;">Requisitos de la contraseña:</div>
                <div id="req-len" style="color:#9ca3af;"><i class="bi bi-x-circle-fill"></i> Mínimo 8 caracteres</div>
                <div id="req-let" style="color:#9ca3af;"><i class="bi bi-x-circle-fill"></i> Al menos una letra</div>
                <div id="req-num" style="color:#9ca3af;"><i class="bi bi-x-circle-fill"></i> Al menos un número</div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Confirmar nueva contraseña</label>
            <div style="position:relative;">
                <input type="password" name="password_confirmation" id="inp-pass-conf" class="form-input"
                       placeholder="Repite la contraseña" style="padding-right:2.5rem;">
                <button type="button" onclick="togglePass('inp-pass-conf','ojo-pass-conf')"
                        style="position:absolute;right:.65rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:0;line-height:1;">
                    <i class="bi bi-eye" id="ojo-pass-conf"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="form-card">
    <div class="seccion-titulo"><i class="bi bi-shield-check"></i> Rol del sistema</div>

    @php $rolActual = $usuario->roles->first()?->name; @endphp

    @foreach($roles as $r)
    @php
        $labelRol = match($r->name) {
            'doctora'       => 'Doctor(a)',
            'administrador' => 'Administrador',
            'asistente'     => 'Asistente',
            default         => ucfirst($r->name)
        };
        $descripcion = match($r->name) {
            'doctora'       => 'Acceso total al sistema: pacientes, historias, citas, pagos, reportes y configuración.',
            'administrador' => 'Gestión administrativa: pacientes, citas, pagos, inventario, usuarios y configuración.',
            'asistente'     => 'Atención al paciente: agendamiento, historias clínicas básicas y pagos.',
            default         => 'Rol del sistema.'
        };
        $icono = match($r->name) {
            'doctora'       => 'bi-person-badge',
            'administrador' => 'bi-person-gear',
            'asistente'     => 'bi-person-check',
            default         => 'bi-person'
        };
    @endphp
    <label class="rol-card" style="display:flex;align-items:flex-start;gap:.75rem;">
        <input type="radio" name="rol" value="{{ $r->name }}"
               {{ old('rol', $rolActual) === $r->name ? 'checked' : '' }} style="margin-top:.2rem;" required>
        <div>
            <div style="display:flex;align-items:center;gap:.4rem;font-weight:600;color:#1c2b22;">
                <i class="bi {{ $icono }}" style="color:var(--color-principal);"></i> {{ $labelRol }}
            </div>
            <div style="font-size:.8rem;color:#6b7280;margin-top:.2rem;">{{ $descripcion }}</div>
        </div>
    </label>
    @endforeach
    @error('rol')<div class="form-error">{{ $message }}</div>@enderror
</div>

<div style="display:flex;gap:.5rem;flex-wrap:wrap;">
    <button type="submit" class="btn-morado">
        <i class="bi bi-check-circle"></i> Guardar cambios
    </button>
    <a href="{{ route('usuarios.show', $usuario) }}" class="btn-gris">Cancelar</a>
</div>

</form>
@endsection

@push('scripts')
<script>
function togglePass(inputId, iconId) {
    var inp = document.getElementById(inputId);
    var ico = document.getElementById(iconId);
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        ico.className = 'bi bi-eye';
    }
}
function validarPass(val) {
    var box = document.getElementById('req-pass');
    if (!val) { box.style.display = 'none'; return; }
    box.style.display = 'block';
    function check(id, ok) {
        var el = document.getElementById(id);
        el.style.color = ok ? '#166534' : '#9ca3af';
        el.innerHTML = (ok ? '<i class="bi bi-check-circle-fill"></i>' : '<i class="bi bi-x-circle-fill"></i>') + el.innerHTML.replace(/<[^>]+>/g,'').trim();
    }
    check('req-len', val.length >= 8);
    check('req-let', /[a-zA-Z]/.test(val));
    check('req-num', /[0-9]/.test(val));
}
</script>
@endpush
