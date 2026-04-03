{{-- ============================================================
     VISTA: Restablecer Contraseña
     Sistema: Arkedent
     ============================================================ --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña — {{ config('app.nombre_consultorio', 'Consultorio Odontológico') }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon.png') }}?v=4">
    <link rel="icon" type="image/png" sizes="32x32"   href="{{ asset('favicon.png') }}?v=4">
    <link rel="icon" type="image/png" sizes="16x16"   href="{{ asset('favicon.png') }}?v=4">
    <link rel="shortcut icon" type="image/png"        href="{{ asset('favicon.png') }}?v=4">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Fuentes --}}
    @php
        $config = \App\Models\Configuracion::first();
        $fuentePrincipal = $config->fuente_principal ?? 'DM Sans';
        $fuenteTitulos   = $config->fuente_titulos   ?? 'Playfair Display';
        $fuentesGoogle = [
            'DM Sans'            => 'DM+Sans:wght@300;400;500',
            'Nunito'             => 'Nunito:wght@300;400;500;600',
            'Inter'              => 'Inter:wght@300;400;500',
            'Source Sans Pro'    => 'Source+Sans+3:wght@300;400;600',
            'Space Grotesk'      => 'Space+Grotesk:wght@300;400;500',
            'Outfit'             => 'Outfit:wght@300;400;500',
            'Playfair Display'   => 'Playfair+Display:ital,wght@0,400;0,600;1,400',
            'Cormorant Garamond' => 'Cormorant+Garamond:ital,wght@0,400;0,600;1,400',
            'Libre Baskerville'  => 'Libre+Baskerville:ital,wght@0,400;0,700;1,400',
            'Merriweather'       => 'Merriweather:ital,wght@0,400;0,700;1,400',
            'Syne'               => 'Syne:wght@400;600;700',
            'Lora'               => 'Lora:ital,wght@0,400;0,600;1,400',
        ];
        $urlFuentePrincipal = $fuentesGoogle[$fuentePrincipal] ?? 'DM+Sans:wght@300;400;500';
        $urlFuenteTitulos   = $fuentesGoogle[$fuenteTitulos]   ?? 'Playfair+Display:ital,wght@0,400;0,600;1,400';
    @endphp
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($urlFuentePrincipal) }}&family={{ urlencode($urlFuenteTitulos) }}&display=swap" rel="stylesheet">

    {{-- CSS de temas --}}
    <link href="{{ asset('css/temas.css') }}" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --color-principal:  #6B21A8;
            --color-claro:      #7C3AED;
            --color-muy-claro:  #F3E8FF;
            --color-hover:      #581C87;
            --fondo-app:        #faf8f4;
            --fondo-borde:      #ede9e0;
            --sombra-principal: rgba(107, 33, 168, 0.15);
            --fuente-principal: '{{ $fuentePrincipal }}', sans-serif;
            --fuente-titulos:   '{{ $fuenteTitulos }}', serif;
            --texto-principal:  #1c2b22;
            --texto-secundario: #5c6b62;
            --texto-muted:      #8fa39a;
            --blanco:           #ffffff;
            --crema:            var(--fondo-app);
            --crema-borde:      var(--fondo-borde);
            --radio-base:       10px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: var(--fuente-principal);
            background-color: var(--fondo-app);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: stretch;
            overflow: hidden;
        }

        .login-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ── Panel izquierdo ── */
        .panel-izquierdo {
            flex: 0 0 45%;
            background: var(--gradiente-sidebar, linear-gradient(155deg, #6B21A8 0%, #4C1D95 60%, #3B0764 100%));
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem;
            overflow: hidden;
        }

        .panel-izquierdo::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 340px; height: 340px;
            border-radius: 50%;
            border: 1.5px solid rgba(255,255,255,0.07);
        }
        .panel-izquierdo::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 280px; height: 280px;
            border-radius: 50%;
            border: 1.5px solid rgba(255,255,255,0.05);
        }

        .deco-circulo-2 {
            position: absolute;
            top: 50%; left: -100px;
            transform: translateY(-50%);
            width: 420px; height: 420px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.04);
        }

        .marca-panel { position: relative; z-index: 2; }

        .marca-icono {
            width: 52px; height: 52px;
            background: rgba(255,255,255,0.12);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            border: 1px solid rgba(255,255,255,0.15);
        }

        .marca-nombre {
            font-family: var(--fuente-titulos);
            font-size: 1.75rem;
            font-weight: 600;
            color: #ffffff;
            line-height: 1.2;
            margin-bottom: 0.4rem;
        }

        .marca-slogan {
            font-size: 0.8rem;
            font-weight: 300;
            color: rgba(255,255,255,0.55);
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .frase-central {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .frase-linea-decorativa {
            width: 36px; height: 2px;
            background: rgba(255,255,255,0.3);
            margin-bottom: 1.5rem;
        }

        .frase-texto {
            font-family: var(--fuente-titulos);
            font-size: 1.55rem;
            font-weight: 400;
            color: rgba(255,255,255,0.92);
            line-height: 1.5;
            font-style: italic;
            margin-bottom: 1rem;
        }

        .frase-autor {
            font-size: 0.78rem;
            font-weight: 400;
            color: rgba(255,255,255,0.4);
            letter-spacing: 0.08em;
        }

        .pasos-panel {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .paso-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .paso-num {
            width: 26px; height: 26px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            font-weight: 600;
            color: #fff;
            flex-shrink: 0;
        }

        .paso-num.activo {
            background: rgba(255,255,255,0.9);
            color: #6B21A8;
        }

        .paso-texto {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.55);
            letter-spacing: 0.04em;
        }

        .paso-texto.activo {
            color: rgba(255,255,255,0.9);
            font-weight: 500;
        }

        /* ── Panel derecho ── */
        .panel-derecho {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
            background-color: var(--crema);
            position: relative;
        }

        .form-card {
            width: 100%;
            max-width: 440px;
        }

        .icono-recuperar {
            width: 64px; height: 64px;
            background: var(--color-muy-claro);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(107,33,168,0.12);
        }

        .icono-recuperar i {
            font-size: 1.8rem;
            color: var(--color-principal);
        }

        .form-encabezado { margin-bottom: 2rem; }

        .form-tag {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--color-principal);
            letter-spacing: 0.14em;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.6rem;
        }

        .form-tag::before {
            content: '';
            display: inline-block;
            width: 18px; height: 1.5px;
            background: var(--color-principal);
        }

        .form-titulo {
            font-family: var(--fuente-titulos);
            font-size: 2rem;
            font-weight: 600;
            color: var(--texto-principal);
            line-height: 1.2;
            margin-bottom: 0.5rem;
        }

        .form-descripcion {
            font-size: 0.88rem;
            font-weight: 300;
            color: var(--texto-secundario);
            line-height: 1.55;
        }

        /* Campos */
        .campo-grupo { margin-bottom: 1.25rem; }

        .campo-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--texto-secundario);
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 0.45rem;
        }

        .campo-input-wrapper { position: relative; }

        .icono-campo {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--texto-muted);
            font-size: 1rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .btn-ojo {
            position: absolute;
            right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--texto-muted);
            font-size: 1rem;
            cursor: pointer;
            padding: 0 4px;
            transition: color 0.2s;
        }
        .btn-ojo:hover { color: var(--color-principal); }

        .campo-input {
            width: 100%;
            height: 48px;
            border: 1.5px solid var(--crema-borde);
            border-radius: var(--radio-base);
            background: var(--blanco);
            font-family: var(--fuente-principal);
            font-size: 0.93rem;
            color: var(--texto-principal);
            padding: 0 42px 0 42px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .campo-input:focus {
            border-color: var(--color-principal);
            box-shadow: 0 0 0 3px rgba(107, 33, 168, 0.08);
        }

        .campo-input-wrapper:focus-within .icono-campo { color: var(--color-principal); }

        .campo-input.is-invalid { border-color: #dc3545; }

        .campo-error {
            font-size: 0.76rem;
            color: #e53e3e;
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Indicador de fortaleza */
        .fortaleza-wrap { margin-top: 0.45rem; }
        .fortaleza-barras { display: flex; gap: 4px; margin-bottom: 4px; }
        .fortaleza-barra {
            flex: 1; height: 4px;
            background: var(--crema-borde);
            border-radius: 2px;
            transition: background 0.3s;
        }
        .fortaleza-txt { font-size: 0.72rem; color: var(--texto-muted); }

        /* Alerta de error */
        .alerta-error {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: var(--radio-base);
            padding: 0.85rem 1rem;
            margin-bottom: 1.25rem;
        }
        .alerta-error i { color: #e53e3e; font-size: 0.95rem; flex-shrink: 0; margin-top: 1px; }
        .alerta-error p { font-size: 0.83rem; color: #c53030; margin: 0; line-height: 1.4; }

        /* Botón principal */
        .btn-enviar {
            width: 100%;
            height: 50px;
            background: var(--color-principal);
            color: var(--blanco);
            border: none;
            border-radius: var(--radio-base);
            font-family: var(--fuente-principal);
            font-size: 0.93rem;
            font-weight: 500;
            letter-spacing: 0.03em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        }

        .btn-enviar:hover {
            background: var(--color-hover);
            box-shadow: 0 8px 28px rgba(107, 33, 168, 0.18), 0 2px 8px rgba(0,0,0,0.12);
            transform: translateY(-1px);
        }

        .btn-enviar:active { transform: translateY(0); }

        .volver-login {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: var(--texto-secundario);
            text-decoration: none;
            transition: color 0.2s;
        }
        .volver-login:hover { color: var(--color-principal); }
        .volver-login i { font-size: 0.9rem; }

        .spinner-btn {
            display: none;
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: white;
            border-radius: 50%;
            animation: girar 0.7s linear infinite;
        }

        @keyframes girar { to { transform: rotate(360deg); } }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeSlideLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .animar-entrada { animation: fadeSlideUp 0.5s ease both; }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.12s; }
        .delay-3 { animation-delay: 0.19s; }
        .delay-4 { animation-delay: 0.26s; }
        .delay-5 { animation-delay: 0.33s; }

        .panel-izquierdo .marca-panel   { animation: fadeSlideLeft 0.6s ease both; }
        .panel-izquierdo .frase-central { animation: fadeSlideLeft 0.6s 0.1s ease both; }
        .panel-izquierdo .pasos-panel   { animation: fadeSlideLeft 0.6s 0.2s ease both; }

        @media (max-width: 900px) {
            .panel-izquierdo { display: none; }
            .panel-derecho { padding: 2rem 1.5rem; }
        }

        .version-badge {
            position: absolute;
            bottom: 1rem; right: 1.25rem;
            font-size: 0.68rem;
            color: var(--texto-muted);
            letter-spacing: 0.04em;
        }
    </style>
</head>
<body data-tema="{{ $config->tema ?? 'morado-elegante' }}">

<div class="login-wrapper">

    {{-- ═══════ PANEL IZQUIERDO ═══════ --}}
    <div class="panel-izquierdo">
        <div class="deco-circulo-2"></div>

        <div class="marca-panel">
            @if($config->logo_path ?? false)
                <div class="marca-icono" style="background:transparent;padding:0;overflow:hidden;">
                    <img src="{{ asset('storage/' . $config->logo_path) }}" alt="Logo"
                         style="width:100%;height:100%;object-fit:contain;display:block;">
                </div>
            @else
                <div class="marca-icono">
                    <svg viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 3C11.5 3 9.5 4.5 8 6C6.5 4.5 4 3 2.5 5C1 7 2.5 10 4 12C4.5 13 5 14.5 5.5 16C6.5 20 7 24 8.5 24C10 24 10.5 21 11 19C11.5 17 12.5 16 14 16C15.5 16 16.5 17 17 19C17.5 21 18 24 19.5 24C21 24 21.5 20 22.5 16C23 14.5 23.5 13 24 12C25.5 10 27 7 25.5 5C24 3 21.5 4.5 20 6C18.5 4.5 16.5 3 14 3Z" fill="rgba(255,255,255,0.85)" stroke="rgba(255,255,255,0.3)" stroke-width="0.5"/>
                    </svg>
                </div>
            @endif
            <div class="marca-nombre">{{ $config->nombre_consultorio ?? 'Consultorio Odontológico' }}</div>
            <div class="marca-slogan">{{ $config->slogan ?? 'Sistema de Gestión Integral' }}</div>
        </div>

        <div class="frase-central">
            <div class="frase-linea-decorativa"></div>
            <p class="frase-texto">"La seguridad de tu cuenta<br>es nuestra prioridad."</p>
            <p class="frase-autor">— Protección de datos garantizada</p>
        </div>

        <div class="pasos-panel">
            <div class="paso-item">
                <div class="paso-num">✓</div>
                <span class="paso-texto">Correo de recuperación enviado</span>
            </div>
            <div class="paso-item">
                <div class="paso-num">✓</div>
                <span class="paso-texto">Enlace verificado</span>
            </div>
            <div class="paso-item">
                <div class="paso-num activo">3</div>
                <span class="paso-texto activo">Establece tu nueva contraseña</span>
            </div>
        </div>
    </div>{{-- /panel-izquierdo --}}

    {{-- ═══════ PANEL DERECHO ═══════ --}}
    <div class="panel-derecho">

        <div class="form-card">

            <div class="icono-recuperar animar-entrada delay-1">
                <i class="bi bi-key"></i>
            </div>

            <div class="form-encabezado animar-entrada delay-1">
                <p class="form-tag">Restablecer acceso</p>
                <h1 class="form-titulo">Nueva contraseña</h1>
                <p class="form-descripcion">
                    Elige una contraseña segura de al menos 8 caracteres para proteger tu cuenta.
                </p>
            </div>

            {{-- Errores --}}
            @if ($errors->any())
                <div class="alerta-error animar-entrada">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <p>
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </p>
                </div>
            @endif

            <form id="form-reset" method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- Email --}}
                <div class="campo-grupo animar-entrada delay-2">
                    <label for="email" class="campo-label">Correo electrónico</label>
                    <div class="campo-input-wrapper">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="campo-input @error('email') is-invalid @enderror"
                            value="{{ old('email', $request->email) }}"
                            required
                            autocomplete="username"
                            readonly
                            style="background:#f9f9f9; color: var(--texto-secundario);"
                        >
                        <i class="bi bi-envelope icono-campo"></i>
                    </div>
                    @error('email')
                        <p class="campo-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Nueva contraseña --}}
                <div class="campo-grupo animar-entrada delay-3">
                    <label for="password" class="campo-label">Nueva contraseña</label>
                    <div class="campo-input-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="campo-input @error('password') is-invalid @enderror"
                            placeholder="Mínimo 8 caracteres"
                            required
                            autocomplete="new-password"
                            autofocus
                            oninput="evaluarFortaleza(this.value)"
                        >
                        <i class="bi bi-lock icono-campo"></i>
                        <button type="button" class="btn-ojo" onclick="togglePass('password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="fortaleza-wrap" id="fortaleza-wrap" style="display:none;">
                        <div class="fortaleza-barras">
                            <div class="fortaleza-barra" id="b1"></div>
                            <div class="fortaleza-barra" id="b2"></div>
                            <div class="fortaleza-barra" id="b3"></div>
                            <div class="fortaleza-barra" id="b4"></div>
                        </div>
                        <span class="fortaleza-txt" id="fortaleza-txt"></span>
                    </div>
                    @error('password')
                        <p class="campo-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmar contraseña --}}
                <div class="campo-grupo animar-entrada delay-4">
                    <label for="password_confirmation" class="campo-label">Confirmar contraseña</label>
                    <div class="campo-input-wrapper">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="campo-input"
                            placeholder="Repite la contraseña"
                            required
                            autocomplete="new-password"
                        >
                        <i class="bi bi-lock-fill icono-campo"></i>
                        <button type="button" class="btn-ojo" onclick="togglePass('password_confirmation', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="animar-entrada delay-5">
                    <button type="submit" class="btn-enviar" id="btn-submit">
                        <span id="texto-btn">
                            <i class="bi bi-check-circle"></i>
                            Guardar nueva contraseña
                        </span>
                        <div class="spinner-btn" id="spinner-enviar"></div>
                    </button>
                </div>
            </form>

            <a href="{{ route('login') }}" class="volver-login animar-entrada delay-5">
                <i class="bi bi-arrow-left"></i>
                Volver al inicio de sesión
            </a>

        </div>{{-- /form-card --}}

        <div class="version-badge">v1.0.0</div>

    </div>{{-- /panel-derecho --}}

</div>{{-- /login-wrapper --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mostrar/ocultar contraseña
    function togglePass(id, btn) {
        const input = document.getElementById(id);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }

    // Indicador de fortaleza de contraseña
    function evaluarFortaleza(val) {
        const wrap = document.getElementById('fortaleza-wrap');
        const txt  = document.getElementById('fortaleza-txt');
        const barras = [document.getElementById('b1'), document.getElementById('b2'),
                        document.getElementById('b3'), document.getElementById('b4')];

        if (!val) { wrap.style.display = 'none'; return; }
        wrap.style.display = 'block';

        let score = 0;
        if (val.length >= 8)  score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const colores = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
        const labels  = ['Muy débil', 'Débil', 'Moderada', 'Segura'];

        barras.forEach((b, i) => {
            b.style.background = i < score ? colores[score - 1] : 'var(--crema-borde)';
        });
        txt.textContent = labels[score - 1] ?? '';
        txt.style.color = colores[score - 1] ?? 'var(--texto-muted)';
    }

    // Spinner al enviar
    document.getElementById('form-reset').addEventListener('submit', function () {
        const btn     = document.getElementById('btn-submit');
        const texto   = document.getElementById('texto-btn');
        const spinner = document.getElementById('spinner-enviar');
        btn.disabled         = true;
        btn.style.opacity    = '0.85';
        texto.style.display  = 'none';
        spinner.style.display = 'block';
    });
</script>

</body>
</html>
