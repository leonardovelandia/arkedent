<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Desarrollador — Arkedent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #0f0f0f;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Courier New', monospace;
        }
        .panel {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 16px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(107,33,168,0.3);
        }
        .logo { text-align: center; margin-bottom: 2rem; }
        .logo-icono { font-size: 3rem; color: #C084FC; display: block; margin-bottom: 0.5rem; }
        .logo-titulo { font-size: 1.1rem; font-weight: 700; color: #C084FC; letter-spacing: 0.05em; }
        .logo-sub { font-size: 0.75rem; color: #666; margin-top: 0.25rem; }
        .form-group { margin-bottom: 1.25rem; }
        .form-label { font-size: 0.72rem; color: #888; text-transform: uppercase; letter-spacing: 0.1em; display: block; margin-bottom: 0.5rem; }
        .form-input {
            width: 100%; padding: 0.75rem 1rem;
            background: #111; border: 1px solid #333; border-radius: 8px;
            color: #C084FC; font-family: 'Courier New', monospace;
            font-size: 0.9rem; letter-spacing: 0.1em; transition: border-color 0.2s;
        }
        .form-input:focus { outline: none; border-color: #6B21A8; box-shadow: 0 0 0 3px rgba(107,33,168,0.2); }
        .btn-acceder {
            width: 100%; padding: 0.875rem;
            background: linear-gradient(135deg, #7C3AED, #6B21A8);
            color: white; border: none; border-radius: 8px;
            font-size: 0.9rem; font-weight: 600; cursor: pointer;
            letter-spacing: 0.05em; transition: opacity 0.2s;
        }
        .btn-acceder:hover { opacity: 0.85; }
        .advertencia {
            margin-top: 1.5rem; padding: 0.75rem 1rem;
            background: #1a0d2e; border: 1px solid #4C1D95; border-radius: 8px;
            font-size: 0.72rem; color: #888; text-align: center; line-height: 1.5;
        }
        .error-msg {
            background: #2d0a0a; border: 1px solid #DC3545; border-radius: 8px;
            padding: 0.75rem 1rem; font-size: 0.78rem; color: #f87171; margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="panel">
    <div class="logo">
        <i class="bi bi-shield-lock logo-icono"></i>
        <div class="logo-titulo">PANEL DESARROLLADOR</div>
        <div class="logo-sub">Arkedent — Acceso restringido</div>
    </div>

    @if(session('error'))
    <div class="error-msg">
        <i class="bi bi-exclamation-triangle me-1"></i>
        {{ session('error') }}
    </div>
    @endif

    <form method="POST" action="{{ route('dev.auth.login') }}">
        @csrf
        <input type="hidden" name="redirigir" value="{{ $redirigir }}">

        <div class="form-group">
            <label class="form-label">Contraseña de desarrollador</label>
            <div style="position:relative;">
                <input type="password" name="dev_password" id="dev_password" class="form-input"
                       placeholder="••••••••••••" autofocus style="padding-right:2.75rem;">
                <button type="button" onclick="togglePass()"
                    style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#666;font-size:1rem;padding:0;line-height:1;">
                    <i class="bi bi-eye" id="ojo-icono"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-acceder">
            <i class="bi bi-unlock me-2"></i>
            Acceder al panel
        </button>
    </form>

    <div class="advertencia">
        <i class="bi bi-info-circle me-1"></i>
        Este panel es exclusivo para el desarrollador del sistema.<br>
    </div>
</div>
<script>
function togglePass() {
    const input = document.getElementById('dev_password');
    const icono = document.getElementById('ojo-icono');
    if (input.type === 'password') {
        input.type = 'text';
        icono.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icono.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
