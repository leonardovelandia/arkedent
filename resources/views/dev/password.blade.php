@extends('layouts.dev')
@section('titulo', 'Cambiar Contraseña Dev')

@section('contenido')
<div style="max-width:480px; margin:2rem auto;">

    <div style="margin-bottom:1.5rem;">
        <div style="font-size:1.2rem;font-weight:700;color:#C084FC;">
            <i class="bi bi-key-fill" style="margin-right:.5rem;"></i>Cambiar Contraseña Dev
        </div>
        <div style="font-size:.78rem;color:#666;margin-top:.25rem;">
            Actualiza la contraseña de acceso al panel de desarrollador
        </div>
    </div>

    @if(session('exito'))
    <div style="background:#0a2a0a;border:1px solid #155724;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.83rem;color:#4ade80;display:flex;align-items:center;gap:.5rem;">
        <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
    </div>
    @endif

    @if(session('error'))
    <div style="background:#2a0a0a;border:1px solid #721c24;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.83rem;color:#f87171;display:flex;align-items:center;gap:.5rem;">
        <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div style="background:#2a0a0a;border:1px solid #721c24;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.83rem;color:#f87171;">
        <i class="bi bi-x-circle-fill"></i>
        <ul style="margin:.4rem 0 0 1.2rem;padding:0;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('dev.password.cambiar') }}"
          style="background:#1a1a1a;border:1px solid #333;border-radius:12px;padding:1.75rem;box-shadow:0 8px 32px rgba(107,33,168,.25);">
        @csrf

        <div style="margin-bottom:1.1rem;">
            <label style="font-size:.72rem;color:#888;text-transform:uppercase;letter-spacing:.1em;display:block;margin-bottom:.45rem;">
                Contraseña actual
            </label>
            <div style="position:relative;">
                <input type="password" name="password_actual" id="pass_actual"
                       style="width:100%;padding:.7rem 2.75rem .7rem 1rem;background:#111;border:1px solid #333;border-radius:8px;color:#e0e0e0;font-family:'Courier New',monospace;font-size:.88rem;"
                       required autofocus>
                <button type="button" onclick="toggle('pass_actual','ojo1')"
                    style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#666;font-size:1rem;padding:0;">
                    <i class="bi bi-eye" id="ojo1"></i>
                </button>
            </div>
        </div>

        <div style="margin-bottom:1.1rem;">
            <label style="font-size:.72rem;color:#888;text-transform:uppercase;letter-spacing:.1em;display:block;margin-bottom:.45rem;">
                Nueva contraseña <span style="color:#555;">(mínimo 8 caracteres)</span>
            </label>
            <div style="position:relative;">
                <input type="password" name="password_nuevo" id="pass_nuevo"
                       style="width:100%;padding:.7rem 2.75rem .7rem 1rem;background:#111;border:1px solid #333;border-radius:8px;color:#e0e0e0;font-family:'Courier New',monospace;font-size:.88rem;"
                       required minlength="8">
                <button type="button" onclick="toggle('pass_nuevo','ojo2')"
                    style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#666;font-size:1rem;padding:0;">
                    <i class="bi bi-eye" id="ojo2"></i>
                </button>
            </div>
        </div>

        <div style="margin-bottom:1.5rem;">
            <label style="font-size:.72rem;color:#888;text-transform:uppercase;letter-spacing:.1em;display:block;margin-bottom:.45rem;">
                Confirmar nueva contraseña
            </label>
            <div style="position:relative;">
                <input type="password" name="password_nuevo_confirmation" id="pass_confirm"
                       style="width:100%;padding:.7rem 2.75rem .7rem 1rem;background:#111;border:1px solid #333;border-radius:8px;color:#e0e0e0;font-family:'Courier New',monospace;font-size:.88rem;"
                       required minlength="8">
                <button type="button" onclick="toggle('pass_confirm','ojo3')"
                    style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#666;font-size:1rem;padding:0;">
                    <i class="bi bi-eye" id="ojo3"></i>
                </button>
            </div>
        </div>

        <button type="submit"
            style="width:100%;padding:.85rem;background:linear-gradient(135deg,#7C3AED,#6B21A8);color:#fff;border:none;border-radius:8px;font-size:.9rem;font-weight:600;cursor:pointer;letter-spacing:.04em;transition:opacity .2s;">
            <i class="bi bi-floppy me-2"></i> Guardar nueva contraseña
        </button>
    </form>

</div>

@push('scripts')
<script>
function toggle(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type    = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type    = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endpush
@endsection
