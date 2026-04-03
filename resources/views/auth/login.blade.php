{{-- ============================================================
     VISTA: Login Principal
     Sistema: Arkedent
     Descripción: Pantalla de acceso al sistema de gestión
     ============================================================ --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso al Sistema — {{ $nombreConsultorio ?? config('app.nombre_consultorio', 'Consultorio Odontológico') }}
    </title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}?v=3">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}?v=3">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Fuentes dinámicas desde configuración --}}
    @php
        $fuentePrincipal = $config->fuente_principal ?? 'DM Sans';
        $fuenteTitulos = $config->fuente_titulos ?? 'Playfair Display';
        $fuentesGoogle = [
            'DM Sans' => 'DM+Sans:wght@300;400;500',
            'Nunito' => 'Nunito:wght@300;400;500;600',
            'Inter' => 'Inter:wght@300;400;500',
            'Source Sans Pro' => 'Source+Sans+3:wght@300;400;600',
            'Space Grotesk' => 'Space+Grotesk:wght@300;400;500',
            'Outfit' => 'Outfit:wght@300;400;500',
            'Playfair Display' => 'Playfair+Display:ital,wght@0,400;0,600;1,400',
            'Cormorant Garamond' => 'Cormorant+Garamond:ital,wght@0,400;0,600;1,400',
            'Libre Baskerville' => 'Libre+Baskerville:ital,wght@0,400;0,700;1,400',
            'Merriweather' => 'Merriweather:ital,wght@0,400;0,700;1,400',
            'Syne' => 'Syne:wght@400;600;700',
            'Lora' => 'Lora:ital,wght@0,400;0,600;1,400',
        ];
        $urlFuentePrincipal = $fuentesGoogle[$fuentePrincipal] ?? 'DM+Sans:wght@300;400;500';
        $urlFuenteTitulos = $fuentesGoogle[$fuenteTitulos] ?? 'Playfair+Display:ital,wght@0,400;0,600;1,400';
    @endphp
    <link
        href="https://fonts.googleapis.com/css2?family={{ urlencode($urlFuentePrincipal) }}&family={{ urlencode($urlFuenteTitulos) }}&display=swap"
        rel="stylesheet">

    {{-- CSS de temas visuales --}}
    <link href="{{ asset('css/temas.css') }}" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ── Variables del sistema ── */
        :root {
            /* Valores por defecto del tema (se sobreescriben con data-tema en body) */
            --color-principal: #6B21A8;
            --color-claro: #7C3AED;
            --color-muy-claro: #F3E8FF;
            --color-hover: #581C87;
            --fondo-app: #faf8f4;
            --fondo-borde: #ede9e0;
            --sombra-principal: rgba(107, 33, 168, 0.15);

            /* Fuentes dinámicas */
            --fuente-principal: '{{ $fuentePrincipal }}', sans-serif;
            --fuente-titulos: '{{ $fuenteTitulos }}', serif;

            /* Aliases backward-compatible */
            --morado-base: var(--color-principal);
            --morado-claro: var(--color-claro);
            --morado-muy-claro: var(--color-muy-claro);
            --morado-hover: var(--color-hover);

            --crema: var(--fondo-app);
            --crema-borde: var(--fondo-borde);
            --texto-principal: #1c2b22;
            --texto-secundario: #5c6b62;
            --texto-muted: #8fa39a;
            --blanco: #ffffff;
            --sombra-suave: 0 2px 20px var(--sombra-principal);
            --sombra-card: 0 8px 40px var(--sombra-principal);
            --radio-base: 10px;
            --radio-grande: 18px;
        }

        /* ── Reset y base ── */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            font-family: var(--fuente-principal);
            background-color: var(--fondo-app);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: stretch;
            overflow: hidden;
        }

        /* ── Layout de dos columnas ── */
        .login-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ── Panel izquierdo (ilustrativo) ── */
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

        /* Decoración geométrica de fondo */
        .panel-izquierdo::before {
            content: '';
            position: absolute;
            top: -80px;
            right: -80px;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            border: 1.5px solid rgba(255, 255, 255, 0.07);
        }

        .panel-izquierdo::after {
            content: '';
            position: absolute;
            bottom: -60px;
            left: -60px;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            border: 1.5px solid rgba(255, 255, 255, 0.05);
        }

        .deco-circulo-2 {
            position: absolute;
            top: 50%;
            left: -100px;
            transform: translateY(-50%);
            width: 420px;
            height: 420px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.04);
        }

        /* Logo / Marca en panel izquierdo */
        .marca-panel {
            position: relative;
            z-index: 2;
        }

        .marca-icono {
            width: 52px;
            height: 52px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            border: none;
        }

        .marca-icono svg {
            width: 28px;
            height: 28px;
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
            color: rgba(255, 255, 255, 0.55);
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        /* Cita/frase central en el panel */
        .frase-central {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .frase-linea-decorativa {
            width: 36px;
            height: 2px;
            background: rgba(255, 255, 255, 0.3);
            margin-bottom: 1.5rem;
        }

        .frase-texto {
            font-family: var(--fuente-titulos);
            font-size: 1.55rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.92);
            line-height: 1.5;
            font-style: italic;
            margin-bottom: 1rem;
        }

        .frase-autor {
            font-size: 0.78rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.4);
            letter-spacing: 0.08em;
        }

        /* Estadísticas rápidas en la parte baja */
        .stats-panel {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radio-base);
            padding: 0.85rem 0.75rem;
            text-align: center;
        }

        .stat-numero {
            font-family: var(--fuente-titulos);
            font-size: 1.4rem;
            font-weight: 600;
            color: #ffffff;
            display: block;
            line-height: 1;
            margin-bottom: 0.3rem;
        }

        .stat-label {
            font-size: 0.7rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.45);
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        /* ── Panel derecho (formulario) ── */
        .panel-derecho {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
            background-color: var(--crema);
            position: relative;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
        }

        /* Encabezado del formulario */
        .login-encabezado {
            margin-bottom: 2.25rem;
        }

        .login-bienvenida {
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

        .login-bienvenida::before {
            content: '';
            display: inline-block;
            width: 18px;
            height: 1.5px;
            background: var(--color-principal);
        }

        .login-titulo {
            font-family: var(--fuente-titulos);
            font-size: 2rem;
            font-weight: 600;
            color: var(--texto-principal);
            line-height: 1.2;
            margin-bottom: 0.4rem;
        }

        .login-subtitulo {
            font-size: 0.88rem;
            font-weight: 300;
            color: var(--texto-secundario);
        }

        /* Campos del formulario */
        .campo-grupo {
            margin-bottom: 1.1rem;
        }

        .campo-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--texto-secundario);
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 0.45rem;
        }

        .campo-input-wrapper {
            position: relative;
        }

        .campo-input-wrapper .icono-campo {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--texto-muted);
            font-size: 1rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .campo-input {
            width: 100%;
            height: 48px;
            border: 1.5px solid var(--crema-borde);
            border-radius: var(--radio-base);
            background: var(--blanco);
            font-family: var(--fuente-principal);
            font-size: 0.93rem;
            font-weight: 400;
            color: var(--texto-principal);
            padding: 0 14px 0 42px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .campo-input:focus {
            border-color: var(--color-principal);
            box-shadow: 0 0 0 3px rgba(107, 33, 168, 0.08);
        }

        .campo-input:focus+.icono-campo,
        .campo-input-wrapper:focus-within .icono-campo {
            color: var(--color-principal);
        }

        .campo-input.is-invalid {
            border-color: #dc3545;
        }

        /* Toggle para mostrar/ocultar contraseña */
        .btn-toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 4px;
            cursor: pointer;
            color: var(--texto-muted);
            font-size: 0.95rem;
            transition: color 0.2s;
            line-height: 1;
        }

        .btn-toggle-password:hover {
            color: var(--color-principal);
        }

        /* Línea de opciones (recordar + olvidé) */
        .opciones-login {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            margin-top: 0.25rem;
        }

        /* Checkbox personalizado */
        .check-recordar {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .check-recordar input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--color-principal);
            cursor: pointer;
        }

        .check-recordar span {
            font-size: 0.83rem;
            font-weight: 400;
            color: var(--texto-secundario);
        }

        .link-olvide {
            font-size: 0.83rem;
            font-weight: 500;
            color: var(--color-principal);
            text-decoration: none;
            transition: color 0.2s;
        }

        .link-olvide:hover {
            color: var(--color-hover);
            text-decoration: underline;
        }

        /* Botón principal de ingreso */
        .btn-ingresar {
            width: 100%;
            height: 50px;
            background: var(--color-principal);
            color: var(--blanco);
            border: none;
            border-radius: var(--radio-base);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.93rem;
            font-weight: 500;
            letter-spacing: 0.03em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
        }

        .btn-ingresar::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0);
            transition: background 0.2s;
        }

        .btn-ingresar:hover {
            background: var(--color-hover);
            box-shadow: 0 8px 28px rgba(107, 33, 168, 0.18), 0 2px 8px rgba(0, 0, 0, 0.12);
            transform: translateY(-1px);
        }

        .btn-ingresar:active {
            transform: translateY(0);
        }

        .btn-ingresar.cargando {
            pointer-events: none;
            opacity: 0.85;
        }

        /* Divisor de roles */
        .divisor-roles {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.5rem 0 1rem;
        }

        .divisor-roles::before,
        .divisor-roles::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--crema-borde);
        }

        .divisor-roles span {
            font-size: 0.72rem;
            font-weight: 500;
            color: var(--texto-muted);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* Chips de roles de acceso */
        .roles-acceso {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .chip-rol {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border-radius: 50px;
            padding: 0.32rem 0.85rem;
            font-size: 0.75rem;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            line-height: 1;
        }

        button.chip-rol {
            background: #fff;
            border: 1.5px solid #d1d5db;
            color: #374151;
            cursor: pointer;
            transition: border-color .15s, box-shadow .15s, background .15s, color .15s;
        }

        button.chip-rol:hover {
            border-color: var(--color-principal);
            background: var(--fondo-card-alt);
            color: var(--color-principal);
            box-shadow: 0 0 0 3px rgba(107, 33, 168, .1);
        }

        button.chip-rol.activo {
            border-color: var(--color-principal);
            background: var(--color-muy-claro);
            color: var(--color-principal);
            font-weight: 600;
        }

        div.chip-rol {
            background: var(--blanco);
            border: 1px solid var(--crema-borde);
            color: var(--texto-secundario);
            cursor: default;
        }

        .chip-rol-disabled {
            opacity: .4;
        }

        .chip-rol-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .chip-rol-dot.doctora {
            background: var(--color-principal);
        }

        .chip-rol-dot.asistente {
            background: #2196F3;
        }

        .chip-rol-dot.admin {
            background: #9C27B0;
        }

        /* Mensajes de error */
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

        .alerta-error i {
            color: #e53e3e;
            font-size: 0.95rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alerta-error p {
            font-size: 0.83rem;
            color: #c53030;
            margin: 0;
            line-height: 1.4;
        }

        /* Texto de error bajo el campo */
        .campo-error {
            font-size: 0.76rem;
            color: #e53e3e;
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Pie de login */
        .pie-login {
            margin-top: 2rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--crema-borde);
            text-align: center;
        }

        .pie-login p {
            font-size: 0.75rem;
            color: var(--texto-muted);
            margin: 0;
        }

        .pie-login a {
            color: var(--color-principal);
            text-decoration: none;
            font-weight: 500;
        }

        /* Spinner de carga */
        .spinner-btn {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-top-color: white;
            border-radius: 50%;
            animation: girar 0.7s linear infinite;
        }

        @keyframes girar {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Animaciones de entrada ── */
        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeSlideLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animar-entrada {
            animation: fadeSlideUp 0.5s ease both;
        }

        .delay-1 {
            animation-delay: 0.05s;
        }

        .delay-2 {
            animation-delay: 0.12s;
        }

        .delay-3 {
            animation-delay: 0.19s;
        }

        .delay-4 {
            animation-delay: 0.26s;
        }

        .delay-5 {
            animation-delay: 0.33s;
        }

        .panel-izquierdo .marca-panel {
            animation: fadeSlideLeft 0.6s ease both;
        }

        .panel-izquierdo .frase-central {
            animation: fadeSlideLeft 0.6s 0.1s ease both;
        }

        .panel-izquierdo .stats-panel {
            animation: fadeSlideLeft 0.6s 0.2s ease both;
        }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .panel-izquierdo {
                display: none;
            }

            .panel-derecho {
                padding: 2rem 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .panel-derecho {
                padding: 1.5rem 1.25rem;
            }

            .login-titulo {
                font-size: 1.6rem;
            }
        }

        /* Indicador de versión */
        .version-badge {
            position: absolute;
            bottom: 1rem;
            right: 1.25rem;
            font-size: 0.68rem;
            color: var(--texto-muted);
            letter-spacing: 0.04em;
        }
    </style>
</head>

<body data-tema="{{ $config->tema ?? 'morado-elegante' }}">

    <div class="login-wrapper">

        {{-- ═══════════════════════════════════════
         PANEL IZQUIERDO — Imagen / Marca
    ══════════════════════════════════════════ --}}
        <div class="panel-izquierdo">

            {{-- Decoración de fondo --}}
            <div class="deco-circulo-2"></div>

            {{-- Marca del consultorio --}}
            <div class="marca-panel">
                @if ($config->logo_path ?? false)
                    <div class="marca-icono" style="background:transparent;padding:0;overflow:hidden;">
                        <img src="{{ asset('storage/' . $config->logo_path) }}" alt="Logo"
                            style="width:100%;height:100%;object-fit:contain;display:block;">
                    </div>
                @else
                    <div class="marca-icono">
                        <svg viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M14 3C11.5 3 9.5 4.5 8 6C6.5 4.5 4 3 2.5 5C1 7 2.5 10 4 12C4.5 13 5 14.5 5.5 16C6.5 20 7 24 8.5 24C10 24 10.5 21 11 19C11.5 17 12.5 16 14 16C15.5 16 16.5 17 17 19C17.5 21 18 24 19.5 24C21 24 21.5 20 22.5 16C23 14.5 23.5 13 24 12C25.5 10 27 7 25.5 5C24 3 21.5 4.5 20 6C18.5 4.5 16.5 3 14 3Z"
                                fill="rgba(255,255,255,0.85)" stroke="rgba(255,255,255,0.3)" stroke-width="0.5" />
                        </svg>
                    </div>
                @endif
                <div class="marca-nombre">{{ $config->nombre_consultorio ?? 'Consultorio Odontológico' }}</div>
                <div class="marca-slogan">{{ $config->slogan ?? 'Sistema de Gestión Integral' }}</div>
            </div>

            {{-- Frase central --}}
            <div class="frase-central">
                <div class="frase-linea-decorativa"></div>
                <p class="frase-texto">"Cada sonrisa que transformamos<br>es una historia de confianza."</p>
                <p class="frase-autor">— Gestión con cuidado y precisión</p>
            </div>

            {{-- Estadísticas (decorativas / reales en producción) --}}
            <div class="stats-panel">
                <div class="stat-item">
                    <span class="stat-numero">🦷</span>
                    <span class="stat-label">Odontología integral</span>
                </div>
                <div class="stat-item">
                    <span class="stat-numero">✨</span>
                    <span class="stat-label">Blanqueamiento dental</span>
                </div>
                <div class="stat-item">
                    <span class="stat-numero">💎</span>
                    <span class="stat-label">Estética y salud</span>
                </div>
            </div>

        </div>{{-- /panel-izquierdo --}}

        {{-- ═══════════════════════════════════════
         PANEL DERECHO — Formulario de Login
    ══════════════════════════════════════════ --}}
        <div class="panel-derecho">

            <div class="login-card">

                {{-- Encabezado --}}
                <div class="login-encabezado animar-entrada delay-1">
                    <p class="login-bienvenida">Bienvenido de nuevo</p>
                    <h1 class="login-titulo">Iniciar sesión</h1>
                    <p class="login-subtitulo">Ingresa tus credenciales para acceder al sistema</p>
                </div>

                {{-- ─── Alertas de error general ─── --}}
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

                {{-- ─── Formulario de autenticación ─── --}}
                <form id="form-login" method="POST" action="{{ route('login') }}" novalidate>
                    @csrf

                    {{-- Campo: Correo electrónico --}}
                    <div class="campo-grupo animar-entrada delay-2">
                        <label for="email" class="campo-label">Correo electrónico</label>
                        <div class="campo-input-wrapper">
                            <input type="email" id="email" name="email"
                                class="campo-input @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                placeholder="tucorreo@ejemplo.com" autocomplete="email" autofocus required>
                            <i class="bi bi-envelope icono-campo"></i>
                        </div>
                        @error('email')
                            <p class="campo-error">
                                <i class="bi bi-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Campo: Contraseña --}}
                    <div class="campo-grupo animar-entrada delay-3">
                        <label for="password" class="campo-label">Contraseña</label>
                        <div class="campo-input-wrapper">
                            <input type="password" id="password" name="password"
                                class="campo-input @error('password') is-invalid @enderror" placeholder="••••••••••"
                                autocomplete="current-password" required>
                            <i class="bi bi-lock icono-campo"></i>
                            <button type="button" class="btn-toggle-password" id="btn-toggle-pass"
                                aria-label="Mostrar/ocultar contraseña">
                                <i class="bi bi-eye" id="icono-ojo"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="campo-error">
                                <i class="bi bi-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Opciones: Recordar + Olvidé contraseña --}}
                    <div class="opciones-login animar-entrada delay-3">
                        <label class="check-recordar">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>Recordarme</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="link-olvide">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    {{-- Botón de ingreso --}}
                    <div class="animar-entrada delay-4">
                        <button type="submit" class="btn-ingresar" id="btn-submit">
                            <span id="texto-btn">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Ingresar al sistema
                            </span>
                            <div class="spinner-btn" id="spinner-login"></div>
                        </button>
                    </div>

                </form>{{-- /form-login --}}

                {{-- Divisor de roles disponibles --}}
                <div class="divisor-roles animar-entrada delay-5">
                    <span>Acceso por rol</span>
                </div>

                {{-- Chips de roles del sistema --}}
                @php
                    $usuariosPorRol = \App\Models\User::where('activo', true)
                        ->get(['name', 'email', 'rol'])
                        ->groupBy('rol');
                    $rolesConfig = [
                        'doctor' => ['label' => 'Doctor(a)', 'dot' => 'doctora'],
                        'asistente' => ['label' => 'Asistente', 'dot' => 'asistente'],
                        'administrador' => ['label' => 'Administrador', 'dot' => 'admin'],
                    ];
                @endphp
                <div class="roles-acceso animar-entrada delay-5" style="flex-wrap:wrap;">
                    @foreach ($rolesConfig as $rolKey => $rolInfo)
                        @if (isset($usuariosPorRol[$rolKey]) && $usuariosPorRol[$rolKey]->isNotEmpty())
                            @php $primerUsuario = $usuariosPorRol[$rolKey]->first(); @endphp
                            <button type="button" class="chip-rol" data-email="{{ $primerUsuario->email }}"
                                onclick="seleccionarUsuario('{{ $primerUsuario->email }}', '{{ addslashes($primerUsuario->name) }}')"
                                title="{{ $primerUsuario->name }}">
                                <span class="chip-rol-dot {{ $rolInfo['dot'] }}"></span>
                                {{ $rolInfo['label'] }}
                            </button>
                        @else
                            <div class="chip-rol chip-rol-disabled" title="Sin usuarios con este rol">
                                <span class="chip-rol-dot {{ $rolInfo['dot'] }}"></span>
                                {{ $rolInfo['label'] }}
                            </div>
                        @endif
                    @endforeach
                </div>
                <div id="hint-usuario"
                    style="display:none;font-size:.75rem;color:var(--color-principal);text-align:center;margin-top:.4rem;font-weight:500;">
                </div>

                {{-- Pie de página del login --}}
                <div class="pie-login animar-entrada delay-5" style="text-align:center; margin-top:10px;">
                    {{-- Logo --}}
                    <img src="{{ asset('arkedent.png') }}" alt="Arkedent" style="height:50px;">
                    <p style="margin:0; font-size:0.7rem; color:#6b7280;">
                        Dental ERP System v1.0.0
                    </p>

                    <p style="margin:2px 0 0; font-size:0.6rem; color:#9ca3af;">
                        Powered by Arkevix
                    </p>

                </div>



            </div>{{-- /login-card --}}


        </div>{{-- /panel-derecho --}}

    </div>{{-- /login-wrapper --}}

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ─── Toggle para mostrar/ocultar contraseña ───
        document.getElementById('btn-toggle-pass').addEventListener('click', function() {
            const campoPassword = document.getElementById('password');
            const iconoOjo = document.getElementById('icono-ojo');

            if (campoPassword.type === 'password') {
                campoPassword.type = 'text';
                iconoOjo.className = 'bi bi-eye-slash';
            } else {
                campoPassword.type = 'password';
                iconoOjo.className = 'bi bi-eye';
            }
        });

        // ─── Spinner al enviar el formulario ───
        document.getElementById('form-login').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            // Validación mínima antes de enviar
            if (!email || !password) return;

            const btnSubmit = document.getElementById('btn-submit');
            const textBtn = document.getElementById('texto-btn');
            const spinnerBtn = document.getElementById('spinner-login');

            btnSubmit.classList.add('cargando');
            textBtn.style.display = 'none';
            spinnerBtn.style.display = 'block';
        });

        // ─── Enfocar el campo email al cargar ───
        document.addEventListener('DOMContentLoaded', function() {
            const campoEmail = document.getElementById('email');
            if (!campoEmail.value) {
                campoEmail.focus();
            }
        });

        // ─── Seleccionar usuario por rol ───
        function seleccionarUsuario(email, nombre) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = '';
            document.getElementById('password').focus();

            // Marcar chip activo
            document.querySelectorAll('button.chip-rol').forEach(function(b) {
                b.classList.remove('activo');
                if (b.getAttribute('onclick').includes(email)) b.classList.add('activo');
            });

            // Mostrar hint
            var hint = document.getElementById('hint-usuario');
            hint.textContent = '↑ Correo de ' + nombre + ' cargado. Ingresa tu contraseña.';
            hint.style.display = 'block';
        }
    </script>

</body>

</html>
