{{-- ============================================================
     LAYOUT: app.blade.php
     Sistema: Arkedent
     Descripción: Layout base para todas las vistas autenticadas
     Usa: @extends('layouts.app') en cada vista del sistema
     ============================================================ --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Título dinámico: página actual + nombre del consultorio --}}
    <title>@yield('titulo', 'Inicio') — {{ $config->nombre_consultorio ?? config('app.nombre_consultorio') }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=5">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=5">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v=5">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=5">

    {{-- Preconnect para CDN (reduce latencia DNS+TCP en primera carga) --}}
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family={{ urlencode($urlFuentePrincipal) }}&family={{ urlencode($urlFuenteTitulos) }}&display=swap"
        rel="stylesheet">

    {{-- CSS de temas visuales --}}
    <link href="{{ asset('css/temas.css') }}" rel="stylesheet">

    {{-- CSS sistema de tablas --}}
    <link href="{{ asset('css/tabla-sistema.css') }}" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ── Variables del sistema ── */
        :root {
            /* Valores por defecto del tema (morado elegante) */
            --color-principal: #6B21A8;
            --color-claro: #7C3AED;
            --color-muy-claro: #F3E8FF;
            --color-hover: #581C87;
            --color-sidebar: #3B0764;
            --color-sidebar-2: #4C1D95;
            --color-acento-activo: #C084FC;
            --color-badge-bg: #F3E8FF;
            --color-badge-texto: #6B21A8;
            --fondo-app: #faf8f4;
            --fondo-borde: #ede9e0;
            --fondo-card-alt: #faf8ff;
            --gradiente-sidebar: linear-gradient(155deg, #6B21A8 0%, #4C1D95 60%, #3B0764 100%);
            --gradiente-btn: linear-gradient(135deg, #7C3AED 0%, #6B21A8 100%);
            --sombra-principal: rgba(107, 33, 168, 0.15);

            /* Variables de fuentes dinámicas */
            --fuente-principal: '{{ $fuentePrincipal }}', sans-serif;
            --fuente-titulos: '{{ $fuenteTitulos }}', serif;

            /* Aliases backward-compatible */
            --morado-base: var(--color-principal);
            --morado-claro: var(--color-claro);
            --morado-muy-claro: var(--color-muy-claro);
            --morado-hover: var(--color-hover);

            /* Variables que no cambian con el tema */
            --sidebar-hover: rgba(255, 255, 255, 0.08);
            --sidebar-activo: rgba(0, 0, 0, 0.25);
            --sidebar-texto: rgba(255, 255, 255, 0.95);
            --sidebar-texto-activo: #ffffff;
            --sidebar-ancho: 280px;
            --navbar-altura: 60px;
            --crema: var(--fondo-app);
            --crema-borde: var(--fondo-borde);
            --texto-principal: #1c2b22;
            --texto-secundario: #5c6b62;
            --blanco: #ffffff;
        }

        /* Fuentes de títulos en elementos específicos */
        .marca-nombre,
        .login-titulo,
        .page-titulo,
        .bienvenida-banner h2,
        .metrica-numero,
        .stat-numero,
        h1,
        h2 {
            font-family: var(--fuente-titulos) !important;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: var(--fuente-principal);
            background-color: var(--fondo-app);
            color: var(--texto-principal);
            margin: 0;
            min-height: 100vh;
        }

        /* ═══════════════════════════════
           SIDEBAR
        ═══════════════════════════════ */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-ancho) !important;
            min-width: var(--sidebar-ancho);
            max-width: var(--sidebar-ancho);
            height: 100vh;
            background: var(--gradiente-sidebar);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;

        }

        /* Logo del consultorio en sidebar */
        .sidebar-logo {
            padding: 1.1rem 1.1rem .9rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            flex-shrink: 0;
        }

        .sidebar-logo-icono {
            width: 42px;
            height: 42px;
            background: transparent;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-logo-icono svg {
            width: 22px;
            height: 22px;
        }

        .sidebar-logo-texto {
            overflow: hidden;
            flex: 1;
            min-width: 0;
        }

        .sidebar-logo-nombre {
            font-family: var(--fuente-titulos);
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }

        .sidebar-logo-fila {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .sidebar-logo-sub {
            font-size: 0.63rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.45);
            letter-spacing: 0.04em;
            text-transform: uppercase;
            white-space: normal;
            word-break: break-word;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            margin-top: 0.1rem;
            line-height: 1.4;
        }

        /* Secciones del menú */
        .sidebar-nav {
            flex: 1;
            padding: 0.75rem 0;
        }

        .nav-seccion-titulo {
            font-size: 0.6rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.3);
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 0.75rem 1.25rem 0.35rem;
            margin-top: 0.25rem;
        }

        .nav-item-sidebar {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.6rem 1.25rem;
            color: var(--sidebar-texto);
            text-decoration: none;
            font-size: 0.845rem;
            font-weight: 400;
            border-radius: 0;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap;
            overflow: hidden;
            cursor: pointer;
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
        }

        .nav-item-sidebar:hover {
            background: var(--sidebar-hover);
            color: var(--sidebar-texto-activo);
            transform: translateX(3px);
        }

        .nav-item-sidebar.activo {
            background: var(--sidebar-activo);
            color: var(--sidebar-texto-activo);
            font-weight: 500;
        }

        .nav-item-sidebar.activo::before {
            content: '';
            position: absolute;
            left: 0;
            width: 3px;
            height: 100%;
            background: var(--color-acento-activo);
            border-radius: 0 2px 2px 0;
        }

        .nav-item-sidebar {
            position: relative;
        }

        .nav-item-icono {
            font-size: 1rem;
            flex-shrink: 0;
            width: 18px;
            text-align: center;
        }

        .nav-badge {
            margin-left: auto;
            background: rgba(255, 255, 255, 0.15);
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.65rem;
            font-weight: 500;
            padding: 1px 6px;
            border-radius: 50px;
        }

        /* Perfil de usuario en el sidebar */
        .sidebar-perfil {
            padding: 0.875rem 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            display: flex;
            align-items: center;
            gap: 0.65rem;
            flex-shrink: 0;
        }

        .perfil-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 500;
            color: white;
            flex-shrink: 0;
            letter-spacing: 0.02em;
        }

        .perfil-info {
            flex: 1;
            overflow: hidden;
        }

        .perfil-nombre {
            font-size: 0.8rem;
            font-weight: 500;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .perfil-rol {
            font-size: 0.68rem;
            color: rgba(255, 255, 255, 0.4);
            text-transform: capitalize;
        }

        .btn-logout-sidebar {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.35);
            font-size: 0.95rem;
            cursor: pointer;
            padding: 4px;
            transition: color 0.15s;
            flex-shrink: 0;
        }

        .btn-logout-sidebar:hover {
            color: rgba(255, 255, 255, 0.75);
        }

        /* ═══════════════════════════════
           NAVBAR SUPERIOR
        ═══════════════════════════════ */
        #navbar-top {
            position: fixed;
            top: 0;
            left: var(--sidebar-ancho);
            right: 0;
            height: var(--navbar-altura);
            background: var(--blanco);
            border-bottom: 1px solid var(--crema-borde);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            gap: 1rem;
            z-index: 999;
        }

        .navbar-toggle {
            background: none;
            border: none;
            font-size: 1.15rem;
            color: var(--texto-secundario);
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            transition: background 0.15s, color 0.15s;
            display: none;
        }

        .navbar-toggle:hover {
            background: var(--crema);
            color: var(--texto-principal);
        }

        .navbar-titulo {
            font-size: 0.93rem;
            font-weight: 500;
            color: var(--texto-principal);
            flex: 1;
        }

        .navbar-titulo span {
            font-size: 0.8rem;
            font-weight: 400;
            color: var(--texto-secundario);
            margin-left: 0.35rem;
        }

        .navbar-acciones {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-navbar-accion {
            background: none;
            border: 1px solid var(--crema-borde);
            border-radius: 8px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            color: var(--texto-secundario);
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s, color 0.15s;
            text-decoration: none;
            position: relative;
        }

        .btn-navbar-accion:hover {
            background: var(--crema);
            border-color: #d0c8b8;
            color: var(--texto-principal);
        }

        .notif-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 7px;
            height: 7px;
            background: #e53e3e;
            border-radius: 50%;
            border: 1.5px solid white;
        }

        /* Avatar del navbar */
        .navbar-usuario {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.65rem 0.35rem 0.35rem;
            border: 1px solid var(--crema-borde);
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.15s;
            text-decoration: none;
        }

        .navbar-usuario:hover {
            background: var(--crema);
        }

        .navbar-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--color-principal);
            color: white;
            font-size: 0.72rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .navbar-usuario-nombre {
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--texto-principal);
        }

        /* ═══════════════════════════════
           CONTENIDO PRINCIPAL
        ═══════════════════════════════ */
        #contenido-principal {
            margin-left: var(--sidebar-ancho);
            padding-top: var(--navbar-altura);
            min-height: 100vh;
        }

        .contenido-inner {
            padding: 1.75rem 1.75rem;
        }

        /* Overlay para móvil */
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* ═══════════════════════════════
           RESPONSIVE
        ═══════════════════════════════ */
        @media (max-width: 900px) {
            #sidebar {
                transform: translateX(-100%);
            }

            #sidebar.abierto {
                transform: translateX(0);
            }

            #sidebar-overlay.visible {
                display: block;
            }

            #navbar-top {
                left: 0;
            }

            #contenido-principal {
                margin-left: 0;
            }

            .navbar-toggle {
                display: flex;
            }
        }

        @media (max-width: 600px) {
            .contenido-inner {
                padding: 1.25rem 1rem;
            }
        }

        /* ═══════════════════════════════
           UTILIDADES COMUNES
        ═══════════════════════════════ */
        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-titulo {
            font-family: var(--fuente-titulos);
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--texto-principal);
            margin-bottom: 0.2rem;
        }

        .page-subtitulo {
            font-size: 0.85rem;
            font-weight: 400;
            color: var(--texto-secundario);
        }

        /* Títulos de cards/paneles — globales */
        .panel-card-titulo,
        .info-card-titulo,
        .panel-titulo,
        .tabla-titulo,
        .seccion-titulo,
        .doc-section-header,
        .doc-section-title {
            font-family: var(--fuente-principal) !important;
            font-size: .72rem !important;
            font-weight: 700 !important;
            color: var(--color-hover) !important;
            text-transform: uppercase !important;
            letter-spacing: .05em !important;
        }

        /* Fondo claro en headers de cards — globales */
        .panel-card-header,
        .panel-header,
        .tabla-header {
            background: var(--color-muy-claro) !important;
        }

        .info-card-titulo {
            background: var(--color-muy-claro) !important;
            margin: -1.25rem -1.5rem .9rem !important;
            padding: .5rem 1.5rem !important;
            border-radius: 0 !important;
        }

        .card-sistema {
            background: var(--blanco);
            border: 1px solid var(--fondo-borde);
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 8px 28px var(--sombra-principal), 0 2px 8px rgba(0, 0, 0, 0.12);
        }

        /* Sombra global uniforme para Bootstrap .card */
        .card {
            box-shadow: 0 8px 28px var(--sombra-principal), 0 2px 8px rgba(0, 0, 0, 0.12) !important;
        }

        /* Alertas flash del sistema */
        .alerta-flash {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
    </style>

    {{-- Flatpickr time picker --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Flatpickr — tema personalizado morado */
        .flatpickr-calendar {
            font-family: var(--fuente-principal, 'Inter', sans-serif);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .18);
            border: 1px solid var(--fondo-borde, #e5e7eb);
        }

        .flatpickr-time {
            border-top: none;
        }

        .flatpickr-time input,
        .flatpickr-time .flatpickr-time-separator {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-hover, #1c2b22);
        }

        .flatpickr-time input:hover,
        .flatpickr-time input:focus {
            background: var(--color-muy-claro, #f3eeff);
        }

        .flatpickr-time .numInputWrapper span.arrowUp:after {
            border-bottom-color: var(--color-principal, #6d28d9);
        }

        .flatpickr-time .numInputWrapper span.arrowDown:after {
            border-top-color: var(--color-principal, #6d28d9);
        }

        .flatpickr-time .flatpickr-am-pm {
            color: var(--color-principal, #6d28d9);
            font-weight: 700;
        }

        .flatpickr-time .flatpickr-am-pm:hover,
        .flatpickr-time .flatpickr-am-pm:focus {
            background: var(--color-muy-claro, #f3eeff);
        }

        .timepicker-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .timepicker-wrap .timepicker-icon {
            position: absolute;
            left: .65rem;
            color: var(--color-principal, #6d28d9);
            pointer-events: none;
            font-size: .95rem;
        }

        .timepicker-wrap input.timepicker {
            padding-left: 2rem !important;
            cursor: pointer;
        }

        .timepicker-wrap input.timepicker::placeholder {
            color: #b0b8c1;
        }

        /* alt input generado por Flatpickr (altInput:true) — hereda estilos del original */
        .timepicker-wrap input.flatpickr-alt-input {
            padding-left: 2rem !important;
            cursor: pointer;
        }
    </style>

    {{-- Estilos específicos de cada vista --}}
    @stack('estilos')
</head>

<body data-tema="{{ $config->tema ?? 'morado-elegante' }}" data-ui="{{ $config->tema_ui ?? 'clasico' }}">

@if(($config->tema_ui ?? 'clasico') === 'glass')
<style>
    body { background: #0d4f6e !important; color: white !important; }
    /* Sobreescribir variables CSS del tema clásico para que var(--color-principal) etc.
       resuelvan a cyan en todo el sistema sin tocar el PHP */
    body[data-ui="glass"] {
        --color-principal:    rgba(0,234,255,0.90) !important;
        --color-hover:        rgba(0,210,240,1.00) !important;
        --color-claro:        rgba(0,180,200,0.90) !important;
        --color-muy-claro:    rgba(0,234,255,0.10) !important;
        --color-acento-activo: rgba(0,234,255,0.80) !important;
        --sombra-principal:   rgba(0,234,255,0.15) !important;
        --fondo-borde:        rgba(0,234,255,0.20) !important;
        --gradiente-btn:      linear-gradient(135deg, rgba(0,180,200,0.8), rgba(0,120,160,0.8)) !important;
        --fondo-app:          rgba(255,255,255,0.06) !important;
        --fondo-card-alt:     rgba(0,234,255,0.05) !important;
        --texto-principal:    rgba(255,255,255,0.90) !important;
        --texto-secundario:   rgba(255,255,255,0.55) !important;
        --input-bg:           rgba(255,255,255,0.08) !important;
        --input-border:       rgba(0,234,255,0.30) !important;
    }
    body::before {
    content: '';
    position: fixed;
    inset: 0;
    z-index: 0;
    background:
        radial-gradient(ellipse 80% 60% at 10% 20%,  rgba(6,182,212,0.35)  0%, transparent 60%),
        radial-gradient(ellipse 60% 50% at 90% 10%,  rgba(20,184,166,0.30) 0%, transparent 55%),
        radial-gradient(ellipse 70% 55% at 80% 85%,  rgba(14,165,233,0.30) 0%, transparent 60%),
        radial-gradient(ellipse 50% 40% at 20% 80%,  rgba(6,182,212,0.25)  0%, transparent 55%),
        radial-gradient(ellipse 90% 70% at 50% 50%,  rgba(8,60,90,0.6)     0%, transparent 100%);
    pointer-events: none;
}
    .aurora-layer { position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden; }
    .aurora-orb   { position:absolute;border-radius:50%;filter:blur(80px);opacity:0.6; }
    .orb-1{width:600px;height:600px;background:radial-gradient(circle,rgba(6,182,212,.5),transparent 70%);top:-150px;left:-100px;animation:af0 22s ease-in-out infinite;}
    .orb-2{width:500px;height:500px;background:radial-gradient(circle,rgba(20,184,166,.4),transparent 70%);top:-80px;right:-80px;animation:af1 29s 3s ease-in-out infinite;}
    .orb-3{width:700px;height:700px;background:radial-gradient(circle,rgba(14,165,233,.4),transparent 70%);bottom:-200px;left:30%;animation:af2 25s 6s ease-in-out infinite;}
    .orb-4{width:400px;height:400px;background:radial-gradient(circle,rgba(103,232,249,.25),transparent 70%);top:40%;right:-100px;animation:af0 36s 2s ease-in-out infinite;}
    .orb-5{width:350px;height:350px;background:radial-gradient(circle,rgba(6,182,212,.35),transparent 70%);bottom:10%;left:-80px;animation:af1 32s 4s ease-in-out infinite;}
    @keyframes af0{0%,100%{transform:translate(0,0) scale(1)}33%{transform:translate(60px,-40px) scale(1.1)}66%{transform:translate(-40px,30px) scale(0.95)}}
    @keyframes af1{0%,100%{transform:translate(0,0) scale(1)}33%{transform:translate(-50px,50px) scale(1.08)}66%{transform:translate(70px,-30px) scale(0.92)}}
    @keyframes af2{0%,100%{transform:translate(0,0) scale(1)}33%{transform:translate(40px,60px) scale(1.12)}66%{transform:translate(-60px,-40px) scale(0.9)}}
    .hex-bg{position:fixed;inset:0;z-index:1;pointer-events:none;}
    .hex-bg svg{width:100%;height:100%;}
    /* Sidebar glass */
    #sidebar { background: rgba(3,25,38,0.82) !important; backdrop-filter: blur(24px) saturate(160%) !important; -webkit-backdrop-filter: blur(24px) saturate(160%) !important; border-right: 1px solid rgba(0,234,255,0.15) !important; }
    .nav-seccion-titulo { color: rgba(0,234,255,0.45) !important; }
    .nav-item-sidebar { color: rgba(255,255,255,0.70) !important; }
    .nav-item-sidebar:hover { background: rgba(0,234,255,0.08) !important; color: white !important; }
    .nav-item-sidebar.activo { background: rgba(0,234,255,0.12) !important; color: white !important; }
    .nav-item-sidebar.activo::before { background: rgba(0,234,255,0.90) !important; box-shadow: 0 0 8px rgba(0,234,255,0.60) !important; }
    .nav-item-icono { color: rgba(0,234,255,0.70) !important; }
    .nav-item-sidebar.activo .nav-item-icono, .nav-item-sidebar:hover .nav-item-icono { color: rgba(0,234,255,0.95) !important; }
    .sidebar-logo-nombre { text-shadow: 0 0 20px rgba(0,234,255,0.50) !important; }
    .sidebar-logo-sub { color: rgba(255,255,255,0.35) !important; }
    .perfil-avatar { background: rgba(0,234,255,0.12) !important; border: 1px solid rgba(0,234,255,0.30) !important; color: rgba(0,234,255,0.95) !important; }
    .perfil-nombre { color: white !important; }
    .perfil-rol { color: rgba(255,255,255,0.35) !important; }
    .btn-logout-sidebar { color: rgba(255,255,255,0.30) !important; }
    .btn-logout-sidebar:hover { color: rgba(248,113,113,0.80) !important; }
    /* Navbar glass */
    #navbar-top { background: rgba(3,25,38,0.75) !important; backdrop-filter: blur(20px) saturate(160%) !important; -webkit-backdrop-filter: blur(20px) saturate(160%) !important; border-bottom: 1px solid rgba(0,234,255,0.15) !important; }
    .navbar-titulo { color: white !important; text-shadow: 0 0 20px rgba(0,234,255,0.40); }
    .navbar-titulo span { color: rgba(255,255,255,0.35) !important; }
    .btn-navbar-accion { background: rgba(255,255,255,0.06) !important; border: 1px solid rgba(0,234,255,0.15) !important; color: rgba(255,255,255,0.60) !important; }
    .btn-navbar-accion:hover { background: rgba(0,234,255,0.12) !important; border-color: rgba(0,234,255,0.40) !important; color: rgba(0,234,255,0.95) !important; }
    .navbar-usuario { background: rgba(255,255,255,0.05) !important; border: 1px solid rgba(0,234,255,0.15) !important; }
    .navbar-usuario:hover { background: rgba(0,234,255,0.10) !important; border-color: rgba(0,234,255,0.35) !important; }
    .navbar-usuario-nombre { color: rgba(255,255,255,0.85) !important; }
    .navbar-avatar { background: linear-gradient(135deg,rgba(0,180,200,.7),rgba(0,120,160,.7)) !important; border: 1px solid rgba(0,234,255,0.40) !important; }
    /* Dropdown glass */
    .dropdown-menu { background: rgba(3,25,38,0.95) !important; backdrop-filter: blur(20px) !important; border: 1px solid rgba(0,234,255,0.25) !important; border-radius: 12px !important; }
    .dropdown-item { color: rgba(255,255,255,0.75) !important; }
    .dropdown-item:hover { background: rgba(0,234,255,0.10) !important; color: white !important; }
    .dropdown-item.text-danger { color: #fca5a5 !important; }
    .dropdown-divider { border-color: rgba(0,234,255,0.10) !important; }
    /* Contenido */
    #contenido-principal { position: relative; z-index: 2; }
    .page-titulo { color: white !important; text-shadow: 0 0 20px rgba(0,234,255,0.40); }
    .page-subtitulo { color: rgba(255,255,255,0.50) !important; }
    /* Glass cards */
    .glass-card, .card-sistema, .card { background: rgba(255,255,255,0.10) !important; backdrop-filter: blur(20px) saturate(160%) !important; -webkit-backdrop-filter: blur(20px) saturate(160%) !important; border: 1px solid rgba(0,234,255,0.45) !important; border-radius: 16px !important; box-shadow: 0 0 8px rgba(0,234,255,0.25) !important; color: white !important; }
    .glass-card:hover, .card-sistema:hover, .card:hover { box-shadow: 0 0 14px rgba(0,234,255,0.45) !important; }
    .card-body { color: white !important; }
    .panel-card-header, .panel-header, .tabla-header { background: rgba(0,0,0,0.25) !important; border-bottom: 1px solid rgba(0,234,255,0.20) !important; }
    .panel-card-titulo, .info-card-titulo, .panel-titulo, .tabla-titulo, .seccion-titulo { color: rgba(0,234,255,0.90) !important; background: transparent !important; }
    /* Textos */
    .text-muted { color: rgba(255,255,255,0.45) !important; }
    label { color: rgba(255,255,255,0.75) !important; }
    small { color: rgba(255,255,255,0.45) !important; }
    /* Inputs */
    .form-control, .form-select { background: rgba(255,255,255,0.08) !important; border: 1px solid rgba(0,234,255,0.30) !important; border-radius: 10px !important; color: white !important; }
    .form-control::placeholder { color: rgba(255,255,255,0.3) !important; }
    .form-control:focus, .form-select:focus { background: rgba(255,255,255,0.12) !important; border-color: rgba(0,234,255,0.70) !important; box-shadow: 0 0 0 3px rgba(0,234,255,0.12) !important; color: white !important; }
    .form-select option { background: #052837; color: white; }
    /* Fix universal: todos los <select> del sistema en glass mode */
    select option, select optgroup { background: #0a2535 !important; color: rgba(255,255,255,0.90) !important; }
    select option:hover, select option:checked { background: rgba(0,234,255,0.25) !important; }
    /* Botones */
    .btn-primary { background: linear-gradient(135deg,rgba(0,180,200,.8),rgba(0,120,160,.8)) !important; border: 1px solid rgba(0,234,255,0.50) !important; color: white !important; }
    .btn-secondary { background: rgba(255,255,255,0.08) !important; border: 1px solid rgba(255,255,255,0.20) !important; color: rgba(255,255,255,0.85) !important; }
    .btn-danger { background: rgba(248,113,113,0.2) !important; border: 1px solid rgba(248,113,113,0.4) !important; color: #fca5a5 !important; }
    .btn-success { background: rgba(74,222,128,0.2) !important; border: 1px solid rgba(74,222,128,0.4) !important; color: #86efac !important; }
    .btn-outline-primary { background: transparent !important; border: 1px solid rgba(0,234,255,0.50) !important; color: rgba(0,234,255,0.90) !important; }
    /* Tablas */
    .table { color: rgba(255,255,255,0.88) !important; border-color: rgba(255,255,255,0.06) !important; }
    .table thead th { background: rgba(0,0,0,0.30) !important; color: white !important; border-bottom: 1px solid rgba(0,234,255,0.30) !important; border-color: rgba(0,234,255,0.20) !important; }
    .table tbody tr { color: rgba(255,255,255,0.88) !important; border-color: rgba(255,255,255,0.06) !important; }
    .table tbody tr:hover { background: rgba(0,234,255,0.08) !important; }
    .table td, .table th { border-color: rgba(255,255,255,0.06) !important; }
    /* Badges */
    .bg-success  { background: rgba(74,222,128,0.20)  !important; color: #86efac !important; border: 1px solid rgba(74,222,128,0.35)  !important; }
    .bg-danger   { background: rgba(248,113,113,0.20) !important; color: #fca5a5 !important; border: 1px solid rgba(248,113,113,0.35) !important; }
    .bg-warning  { background: rgba(251,191,36,0.20)  !important; color: #fbbf24 !important; border: 1px solid rgba(251,191,36,0.35)  !important; }
    .bg-info     { background: rgba(0,234,255,0.15)   !important; color: rgba(0,234,255,0.95) !important; border: 1px solid rgba(0,234,255,0.35) !important; }
    .bg-primary  { background: rgba(0,234,255,0.12)   !important; color: rgba(0,234,255,0.95) !important; border: 1px solid rgba(0,234,255,0.30) !important; }
    .bg-secondary{ background: rgba(148,163,184,0.20) !important; color: #cbd5e1 !important; border: 1px solid rgba(148,163,184,0.35) !important; }
    /* Alertas flash */
    .alert-success { background: rgba(74,222,128,0.12)  !important; border: 1px solid rgba(74,222,128,0.35)  !important; color: #86efac !important; }
    .alert-danger  { background: rgba(248,113,113,0.12) !important; border: 1px solid rgba(248,113,113,0.35) !important; color: #fca5a5 !important; }
    .alert-warning { background: rgba(251,191,36,0.12)  !important; border: 1px solid rgba(251,191,36,0.35)  !important; color: #fbbf24 !important; }
    /* Errores de validación — color contrastante en glass (aplica a todo el sistema) */
    .error-msg, .err, .campo-error, .form-error, .invalid-feedback { color: #ff8a8a !important; text-shadow: 0 0 8px rgba(255,100,100,0.35); }
    .is-invalid, .error-field { border-color: rgba(255,120,120,0.80) !important; box-shadow: 0 0 0 2px rgba(255,100,100,0.15) !important; }
    span[style*="color:#dc2626"] { color: #ff8a8a !important; }
    /* Alertas flash con inline styles (override universal) */
    .alerta-flash { background: rgba(0,234,255,0.08) !important; border-color: rgba(0,234,255,0.30) !important; color: rgba(255,255,255,0.88) !important; }
    .alerta-flash[style*="background:#f0fdf4"],.alerta-flash[style*="background:#dcfce7"] { background: rgba(74,222,128,0.10) !important; border-color: rgba(74,222,128,0.30) !important; color: #86efac !important; }
    .alerta-flash[style*="background:#fef2f2"],.alerta-flash[style*="background:#fee2e2"] { background: rgba(248,113,113,0.10) !important; border-color: rgba(248,113,113,0.30) !important; color: #fca5a5 !important; }
    .alerta-flash[style*="background:#fffbeb"],.alerta-flash[style*="background:#fef9c3"] { background: rgba(251,191,36,0.10) !important; border-color: rgba(251,191,36,0.30) !important; color: #fbbf24 !important; }
    /* Pagination */
    .page-link { background: rgba(255,255,255,0.08) !important; border: 1px solid rgba(0,234,255,0.25) !important; color: rgba(255,255,255,0.75) !important; border-radius: 8px !important; }
    .page-link:hover { background: rgba(0,234,255,0.15) !important; color: white !important; border-color: rgba(0,234,255,0.50) !important; }
    .page-item.active .page-link { background: rgba(0,234,255,0.25) !important; border-color: rgba(0,234,255,0.60) !important; color: white !important; }
    /* Modales */
    .modal-content { background: rgba(5,40,55,0.90) !important; backdrop-filter: blur(32px) saturate(180%) !important; border: 1px solid rgba(0,234,255,0.25) !important; border-radius: 20px !important; color: white !important; }
    .modal-header { background: rgba(0,0,0,0.25) !important; border-bottom: 1px solid rgba(0,234,255,0.20) !important; }
    .modal-title { color: rgba(0,234,255,0.90) !important; }
    .modal-footer { border-top: 1px solid rgba(0,234,255,0.15) !important; }
    .btn-close { filter: invert(1) !important; opacity: 0.6 !important; }
    .notif-dot { border: 1.5px solid rgba(5,40,55,0.8) !important; }
</style>
<div class="aurora-layer">
    <div class="aurora-orb orb-1"></div>
    <div class="aurora-orb orb-2"></div>
    <div class="aurora-orb orb-3"></div>
    <div class="aurora-orb orb-4"></div>
    <div class="aurora-orb orb-5"></div>
</div>
<div class="hex-bg">
    <svg id="hexSvg" viewBox="0 0 1400 900" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
        <g id="hexGroup"></g>
    </svg>
</div>
<script>
(function(){
    const R=70,W=R*2,H=Math.sqrt(3)*R,COLS=13,ROWS=9,colStep=W*0.75,rowStep=H;
    const vis=[1,1,1,1,1,1,1,0,0,0,0,0,0,1,1,1,1,1,0,0,0,0,0,0,0,0,1,1,1,1,0,0,0,0,0,0,0,0,1,1,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,0,0,0,0,0,0,0,1,1,1,1,1,1,1,0,0,0,0,1,1,1,1,1,1,1,1,1,0,0,0,0,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0];
    function hpts(cx,cy,r){let s=[];for(let i=0;i<6;i++){const a=Math.PI/180*60*i;s.push(`${(cx+r*Math.cos(a)).toFixed(1)},${(cy+r*Math.sin(a)).toFixed(1)}`);}return s.join(' ');}
    const g=document.getElementById('hexGroup');
    let idx=0;
    for(let row=0;row<ROWS;row++){for(let col=0;col<COLS;col++){if(!vis[idx++])continue;const cx=col*colStep+R,cy=row*rowStep+H/2+(col%2===1?H/2:0);const p=document.createElementNS('http://www.w3.org/2000/svg','polygon');p.setAttribute('points',hpts(cx,cy,R-1));p.setAttribute('fill','none');p.setAttribute('stroke','rgba(255,255,255,0.08)');p.setAttribute('stroke-width','0.8');g.appendChild(p);}}
})();
</script>
@endif
{{-- ── FIN AURORA GLASS ── --}}

    {{-- ─── Formulario oculto para logout ─── --}}
    <form id="form-logout" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>

    {{-- ─── Overlay para móvil ─── --}}
    <div id="sidebar-overlay"></div>

    {{-- ═══════════════════════════════════════════════════════════
     SIDEBAR DE NAVEGACIÓN
════════════════════════════════════════════════════════════ --}}
    <nav id="sidebar">

        {{-- Logo del consultorio --}}
        @php
            $nombre = $config->nombre_consultorio ?? 'Consultorio';
            $palabras = explode(' ', $nombre);

            if (count($palabras) > 2) {
                $linea1 = implode(' ', array_slice($palabras, 0, ceil(count($palabras) / 2)));
                $linea2 = implode(' ', array_slice($palabras, ceil(count($palabras) / 2)));
            } else {
                $linea1 = $nombre;
                $linea2 = '';
            }
        @endphp

        {{-- Logo del consultorio --}}
        <div class="sidebar-logo">

            {{-- Logo + nombre --}}
            <div style="display:flex; align-items:center; gap:10px;">

                {{-- Logo --}}
                @if ($config->logo_path ?? false)
                    <div style="width:45px; height:45px; flex-shrink:0;">
                        <img src="{{ asset('storage/' . $config->logo_path) }}" alt="Logo"
                            style="width:100%; height:100%; object-fit:contain;">
                    </div>
                @else
                    <div
                        style="width:40px; height:40px; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg viewBox="0 0 20 20" fill="none">
                            <path
                                d="M10 2C8.2 2 6.8 3.1 5.7 4.3C4.6 3.1 2.8 2 1.8 3.6C0.8 5.2 2 7.5 3.2 8.8C3.6 9.4 4 10.5 4.4 11.8C5.1 14.5 5.5 18 6.7 18C7.9 18 8.3 15.5 8.7 13.8C9.1 12.1 9.5 11.5 10 11.5C10.5 11.5 10.9 12.1 11.3 13.8C11.7 15.5 12.1 18 13.3 18C14.5 18 14.9 14.5 15.6 11.8C16 10.5 16.4 9.4 16.8 8.8C18 7.5 19.2 5.2 18.2 3.6C17.2 2 15.4 3.1 14.3 4.3C13.2 3.1 11.8 2 10 2Z"
                                fill="rgba(255,255,255,0.85)" />
                        </svg>
                    </div>
                @endif

                {{-- Nombre dividido --}}
                <div class="sidebar-logo-nombre" style="line-height:1.2;">
                    <div>{{ $linea1 }}</div>
                    @if ($linea2)
                        <div>{{ $linea2 }}</div>
                    @endif
                </div>

            </div>

            {{-- Slogan abajo --}}
            <div class="sidebar-logo-sub" style="margin-top:5px; text-transform:none;">
                {{ $config->slogan ?? 'Sistema de gestión' }}
            </div>

        </div>

        {{-- Menú de navegación --}}
        <div class="sidebar-nav">

            {{-- SECCIÓN: Principal --}}
            <p class="nav-seccion-titulo">Principal</p>

            <a href="{{ route('dashboard') }}"
                class="nav-item-sidebar {{ request()->routeIs('dashboard') ? 'activo' : '' }}">
                <i class="bi bi-grid-1x2 nav-item-icono"></i>
                Dashboard
            </a>

            @modulo('pacientes')
                <a href="{{ route('pacientes.index') }}"
                    class="nav-item-sidebar {{ request()->routeIs('pacientes.*') ? 'activo' : '' }}">
                    <i class="bi bi-people nav-item-icono"></i>
                    Pacientes
                </a>
            @endmodulo

            @modulo('citas')
                <a href="{{ route('citas.agenda') }}"
                    class="nav-item-sidebar {{ request()->routeIs('citas.*') ? 'activo' : '' }}">
                    <i class="bi bi-calendar3 nav-item-icono"></i>
                    Agenda y Citas
                </a>
            @endmodulo

            @modulo('recordatorios')
                <a href="{{ route('recordatorios.index') }}"
                    class="nav-item-sidebar {{ request()->routeIs('recordatorios.*') ? 'activo' : '' }}">
                    <i class="bi bi-bell nav-item-icono"></i>
                    Recordatorios
                    @php $recFallidos = \Illuminate\Support\Facades\Cache::remember('sidebar_rec_fallidos', 300, fn() => \App\Models\Recordatorio::where('estado','fallido')->where('activo',true)->count()); @endphp
                    @if ($recFallidos > 0)
                        <span
                            style="margin-left:auto;background:#ef4444;color:white;font-size:.65rem;font-weight:700;padding:1px 7px;border-radius:50px;">{{ $recFallidos }}</span>
                    @endif
                </a>
            @endmodulo

            {{-- SECCIÓN: Clínica (solo si hay al menos un módulo activo) --}}
            @if (
                \App\Helpers\ModulosHelper::activo('historia_clinica') ||
                    \App\Helpers\ModulosHelper::activo('evoluciones') ||
                    \App\Helpers\ModulosHelper::activo('valoraciones') ||
                    \App\Helpers\ModulosHelper::activo('imagenes') ||
                    \App\Helpers\ModulosHelper::activo('ortodoncia') ||
                    \App\Helpers\ModulosHelper::activo('recetas') ||
                    \App\Helpers\ModulosHelper::activo('periodoncia'))
                <p class="nav-seccion-titulo">Clínica</p>
            @endif

            @modulo('historia_clinica')
                <a href="{{ route('historias.index') }}"
                    class="nav-item-sidebar {{ request()->routeIs('historias.*') ? 'activo' : '' }}">
                    <i class="bi bi-journal-medical nav-item-icono"></i>
                    Historia Clínica
                </a>
            @endmodulo

            @modulo('evoluciones')
                <a href="{{ route('evoluciones.index') }}"
                    class="nav-item-sidebar {{ request()->routeIs('evoluciones.*') ? 'activo' : '' }}">
                    <i class="bi bi-clipboard2-pulse nav-item-icono"></i>
                    Evoluciones
                </a>
            @endmodulo

            @modulo('valoraciones')
                <a href="{{ route('valoraciones.index') }}"
                    class="nav-item-sidebar {{ request()->routeIs('valoraciones.*') ? 'activo' : '' }}">
                    <i class="bi bi-search-heart nav-item-icono"></i>
                    Valoraciones
                </a>
            @endmodulo

            @modulo('imagenes')
                <a href="{{ route('imagenes.index') }}"
                    class="nav-item-sidebar {{ request()->routeIs('imagenes.*') ? 'activo' : '' }}">
                    <i class="bi bi-images nav-item-icono"></i>
                    Imágenes Clínicas
                </a>
            @endmodulo

            @modulo('ortodoncia')
                <a href="{{ route('ortodoncia.index') }}"
                    class="nav-item-sidebar {{ request()->routeIs('ortodoncia.*') || request()->routeIs('controles.*') || request()->routeIs('retencion.*') ? 'activo' : '' }}">
                    <i class="bi bi-symmetry-horizontal nav-item-icono"></i>
                    Ortodoncia
                </a>
            @endmodulo

            @modulo('recetas')
                <a href="{{ route('recetas.index') }}"
                    class="nav-item-sidebar {{ request()->routeIs('recetas.*') ? 'activo' : '' }}">
                    <i class="bi bi-file-medical nav-item-icono"></i>
                    Recetas Médicas
                </a>
            @endmodulo

            @modulo('periodoncia')
                <a href="{{ route('periodoncia.index') }}"
                    class="nav-item-sidebar {{ request()->routeIs('periodoncia.*') ? 'activo' : '' }}">
                    <i class="bi bi-heart-pulse nav-item-icono"></i>
                    Periodoncia
                </a>
            @endmodulo

            {{-- SECCIÓN: Gestión (solo si hay al menos un módulo activo) --}}
            @if (
                \App\Helpers\ModulosHelper::activo('presupuestos') ||
                    \App\Helpers\ModulosHelper::activo('pagos') ||
                    \App\Helpers\ModulosHelper::activo('consentimientos') ||
                    \App\Helpers\ModulosHelper::activo('laboratorio'))
                <p class="nav-seccion-titulo">Gestión</p>
            @endif

            @modulo('presupuestos')
                <a href="{{ route('presupuestos.index') }}"
                    class="nav-item-sidebar {{ request()->routeIs('presupuestos.*') ? 'activo' : '' }}">
                    <i class="bi bi-file-earmark-text nav-item-icono"></i>
                    Presupuestos
                </a>
            @endmodulo

            @modulo('pagos')
                <a href="{{ route('pagos.index') }}"
                    class="nav-item-sidebar {{ request()->routeIs('pagos.*') ? 'activo' : '' }}">
                    <i class="bi bi-cash-coin nav-item-icono"></i>
                    Abonos y Pagos
                </a>
            @endmodulo

            @modulo('consentimientos')
                <a href="{{ route('consentimientos.index') }}"
                    class="nav-item-sidebar {{ request()->routeIs('consentimientos.*') ? 'activo' : '' }}">
                    <i class="bi bi-pen nav-item-icono"></i>
                    Consentimientos
                </a>
            @endmodulo

            @modulo('laboratorio')
                <a href="{{ route('laboratorio.index') }}"
                    class="nav-item-sidebar {{ request()->routeIs('laboratorio.*') || request()->routeIs('gestion-laboratorios.*') ? 'activo' : '' }}">
                    <i class="bi bi-eyedropper nav-item-icono"></i>
                    Laboratorio
                    @php
                        $labVencidas = \Illuminate\Support\Facades\Cache::remember(
                            'sidebar_lab_vencidas',
                            300,
                            fn() => \App\Models\OrdenLaboratorio::where('activo', true)
                                ->whereNotIn('estado', ['recibido', 'instalado', 'cancelado'])
                                ->whereDate('fecha_entrega_estimada', '<', today())
                                ->count(),
                        );
                    @endphp
                    @if ($labVencidas > 0)
                        <span
                            style="margin-left:auto; background:#DC3545; color:white; font-size:.65rem; font-weight:700; padding:.1rem .45rem; border-radius:50px; line-height:1.4;">{{ $labVencidas }}</span>
                    @endif
                </a>
            @endmodulo

            {{-- SECCIÓN: Administración (solo admin/doctora) --}}
            @role('administrador|doctora')
                @if (
                    \App\Helpers\ModulosHelper::activo('inventario') ||
                        \App\Helpers\ModulosHelper::activo('proveedores') ||
                        \App\Helpers\ModulosHelper::activo('egresos') ||
                        \App\Helpers\ModulosHelper::activo('libro_contable') ||
                        \App\Helpers\ModulosHelper::activo('reportes') ||
                        \App\Helpers\ModulosHelper::activo('usuarios') ||
                        \App\Helpers\ModulosHelper::activo('configuracion'))
                    <p class="nav-seccion-titulo">Administración</p>
                @endif

                @modulo('inventario')
                    <a href="{{ route('inventario.index') }}"
                        class="nav-item-sidebar {{ request()->routeIs('inventario.*') ? 'activo' : '' }}">
                        <i class="bi bi-box-seam nav-item-icono"></i>
                        Inventario
                        @php $stockBajo = \Illuminate\Support\Facades\Cache::remember('sidebar_stock_bajo', 300, fn() => \App\Models\Material::where('activo', true)->whereColumn('stock_actual', '<=', 'stock_minimo')->count()); @endphp
                        @if ($stockBajo > 0)
                            <span
                                style="margin-left:auto; background:#DC3545; color:white; font-size:.65rem; font-weight:700; padding:.1rem .45rem; border-radius:50px; line-height:1.4;">{{ $stockBajo }}</span>
                        @endif
                    </a>
                @endmodulo

                @modulo('proveedores')
                    <a href="{{ route('proveedores.index') }}"
                        class="nav-item-sidebar {{ request()->routeIs('proveedores.*') ? 'activo' : '' }}">
                        <i class="bi bi-truck nav-item-icono"></i>
                        Proveedores
                    </a>

                    <a href="{{ route('compras.index') }}"
                        class="nav-item-sidebar {{ request()->routeIs('compras.*') ? 'activo' : '' }}">
                        <i class="bi bi-cart3 nav-item-icono"></i>
                        Compras
                    </a>
                @endmodulo

                @modulo('libro_contable')
                    <a href="{{ route('libro-contable.index') }}"
                        class="nav-item-sidebar {{ request()->routeIs('libro-contable.*') ? 'activo' : '' }}">
                        <i class="bi bi-journal-bookmark nav-item-icono"></i>
                        Libro Contable
                    </a>
                @endmodulo

                @modulo('egresos')
                    <a href="{{ route('egresos.index') }}"
                        class="nav-item-sidebar {{ request()->routeIs('egresos.*') ? 'activo' : '' }}">
                        <i class="bi bi-cash-stack nav-item-icono"></i>
                        Egresos
                    </a>
                @endmodulo

                @modulo('reportes')
                    <a href="{{ route('reportes.index') }}"
                        class="nav-item-sidebar {{ request()->routeIs('reportes.*') ? 'activo' : '' }}">
                        <i class="bi bi-bar-chart-line nav-item-icono"></i>
                        Reportes
                    </a>
                @endmodulo

                @modulo('usuarios')
                    <a href="{{ route('usuarios.index') }}"
                        class="nav-item-sidebar {{ request()->routeIs('usuarios.*') ? 'activo' : '' }}">
                        <i class="bi bi-person-gear nav-item-icono"></i>
                        Usuarios y Roles
                    </a>
                @endmodulo

                @modulo('configuracion')
                    <a href="{{ route('configuracion.index') }}"
                        class="nav-item-sidebar {{ request()->routeIs('configuracion.*') ? 'activo' : '' }}">
                        <i class="bi bi-gear nav-item-icono"></i>
                        Configuración
                    </a>
                @endmodulo

                @role('administrador')
                    <a href="{{ route('auditoria.index') }}"
                        class="nav-item-sidebar {{ request()->routeIs('auditoria.*') ? 'activo' : '' }}">
                        <i class="bi bi-shield-lock nav-item-icono"></i>
                        Auditoría
                    </a>
                @endrole

                {{-- Importación de datos: solo accesible desde el panel dev (/dev/importacion) --}}

            @endrole

        </div>{{-- /sidebar-nav --}}

        {{-- Perfil del usuario al fondo del sidebar --}}
        <div class="sidebar-perfil">
            <div class="perfil-avatar">
                {{-- Iniciales del usuario autenticado --}}
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->name ?? 'U ')[1] ?? '', 0, 1)) }}
            </div>
            <div class="perfil-info">
                <div class="perfil-nombre">{{ auth()->user()->name ?? 'Usuario' }}</div>
                <div class="perfil-rol">{{ auth()->user()->getRoleNames()->first() ?? 'Sin rol' }}</div>
            </div>
            <button class="btn-logout-sidebar" onclick="document.getElementById('form-logout').submit()"
                title="Cerrar sesión">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </div>

    </nav>{{-- /sidebar --}}

    {{-- ═══════════════════════════════════════════════════════════
     NAVBAR SUPERIOR
════════════════════════════════════════════════════════════ --}}
    <header id="navbar-top">
        {{-- Botón de toggle (visible solo en móvil) --}}
        <button class="navbar-toggle" id="btn-toggle-sidebar" aria-label="Abrir menú">
            <i class="bi bi-list"></i>
        </button>

        {{-- Título de la sección actual --}}
        <div class="navbar-titulo">
            @yield('titulo', 'Dashboard')
            <span>/ {{ $config->nombre_consultorio ?? '' }}</span>
        </div>

        {{-- Acciones del navbar --}}
        <div class="navbar-acciones">

            {{-- Botón de búsqueda global --}}
            <a href="{{ route('pacientes.index') }}" class="btn-navbar-accion" title="Buscar paciente">
                <i class="bi bi-search"></i>
            </a>

            {{-- Notificaciones --}}
            <button class="btn-navbar-accion" title="Notificaciones">
                <i class="bi bi-bell"></i>
                {{-- Punto rojo si hay notificaciones --}}
                @if (auth()->user()->unreadNotifications()->count() > 0)
                    <span class="notif-dot"></span>
                @endif
            </button>

            {{-- Usuario --}}
            <div class="dropdown">
                <div class="navbar-usuario" data-bs-toggle="dropdown">
                    <div class="navbar-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="navbar-usuario-nombre">
                        {{ explode(' ', auth()->user()->name ?? 'Usuario')[0] }}
                    </span>
                    <i class="bi bi-chevron-down" style="font-size:0.65rem; color: var(--texto-secundario);"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end" style="font-size: 0.85rem; min-width: 180px;">
                    <li>
                        <a class="dropdown-item" href="{{ route('perfil.index') }}">
                            <i class="bi bi-person me-2"></i> Mi perfil
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('configuracion.index') }}">
                            <i class="bi bi-gear me-2"></i> Configuración
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <button class="dropdown-item text-danger"
                            onclick="document.getElementById('form-logout').submit()">
                            <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                        </button>
                    </li>
                </ul>
            </div>

        </div>
    </header>

    {{-- ═══════════════════════════════════════════════════════════
     CONTENIDO PRINCIPAL DE LA PÁGINA
════════════════════════════════════════════════════════════ --}}
    <main id="contenido-principal">
        <div class="contenido-inner">

            {{-- Mensajes flash del sistema --}}
            @if (session('exito'))
                <div class="alerta-flash alert alert-success d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('exito') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alerta-flash alert alert-danger d-flex align-items-center gap-2">
                    <i class="bi bi-x-circle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if (session('aviso'))
                <div class="alerta-flash alert alert-warning d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ session('aviso') }}
                </div>
            @endif

            @if (session('aviso_cruce'))
                <div class="alerta-flash alert alert-warning d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ session('aviso_cruce') }}
                </div>
            @endif

            {{-- Contenido de cada vista --}}
            @yield('contenido')

        </div>
    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ─── Toggle sidebar en móvil ───
        const btnToggle = document.getElementById('btn-toggle-sidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function abrirSidebar() {
            sidebar.classList.add('abierto');
            overlay.classList.add('visible');
        }

        function cerrarSidebar() {
            sidebar.classList.remove('abierto');
            overlay.classList.remove('visible');
        }

        if (btnToggle) {
            btnToggle.addEventListener('click', function() {
                if (sidebar.classList.contains('abierto')) {
                    cerrarSidebar();
                } else {
                    abrirSidebar();
                }
            });
        }

        if (overlay) {
            overlay.addEventListener('click', cerrarSidebar);
        }

        // ─── Auto-cerrar alertas flash después de 4 segundos ───
        document.querySelectorAll('.alerta-flash').forEach(function(alerta) {
            setTimeout(function() {
                alerta.style.transition = 'opacity 0.4s ease';
                alerta.style.opacity = '0';
                setTimeout(function() {
                    alerta.remove();
                }, 400);
            }, 4000);
        });
    </script>

    {{-- Formateador global de campos monetarios --}}
    <script>
        (function() {
            function fmtNum(n) {
                return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function initMoneyInput(inp) {
                // Crear hidden con el name original
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = inp.name;
                inp.name = inp.getAttribute('data-money-name') || (inp.name + '_display');
                inp.parentNode.insertBefore(hidden, inp.nextSibling);
                inp._moneyHidden = hidden;

                function sync() {
                    var raw = inp.value.replace(/\./g, '').replace(/[^0-9]/g, '');
                    var num = parseInt(raw) || 0;
                    inp.value = num ? fmtNum(num) : '';
                    hidden.value = num || '';
                }
                inp.addEventListener('input', sync);

                // Formatear valor inicial (old() de Laravel)
                if (inp.value) {
                    var raw = inp.value.replace(/\./g, '').replace(/[^0-9]/g, '');
                    var num = parseInt(raw) || 0;
                    inp.value = num ? fmtNum(num) : '';
                    hidden.value = num || '';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('[data-money]').forEach(initMoneyInput);
            });
        })();
    </script>

    {{-- ── Botones de retroceso inteligentes (history.back con fallback) ── --}}
    <script>
        (function() {
            'use strict';

            // Marca automáticamente como "back button" cualquier <a> que:
            // 1. Contenga el ícono bi-arrow-left
            // 2. NO esté dentro del sidebar ni de la nav principal
            function marcarBotonesVolver() {
                document.querySelectorAll('a').forEach(function(link) {
                    if (
                        link.querySelector('.bi-arrow-left') &&
                        !link.closest('#sidebar') &&
                        !link.closest('nav#sidebar') &&
                        !link.classList.contains('nav-item-sidebar') &&
                        !link.hasAttribute('data-back-skip')
                    ) {
                        link.setAttribute('data-back', '1');
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', marcarBotonesVolver);

            // Intercepta el click: usa history.back() solo si hay historial real
            document.addEventListener('click', function(e) {
                var link = e.target.closest('a[data-back="1"]');
                if (!link) return;

                // Solo retroceder si hay historial y hay referrer (no es página de entrada directa)
                if (window.history.length > 1 && document.referrer !== '') {
                    e.preventDefault();
                    history.back();
                }
                // Sin historial → el href original funciona normalmente
            });
        }());
    </script>

    {{-- Scripts específicos de cada vista --}}
    @stack('scripts')

    {{-- Flatpickr time picker --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        (function() {
            // Formato guardado en configuración: '12' o '24'
            var _fmt24 = '{{ $config->formato_hora ?? '12' }}' === '24';

            function buildFpOptions() {
                return {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: 'H:i', // valor real siempre en 24h (pasa validación)
                    altInput: true, // input visible separado del input de valor
                    altFormat: _fmt24 ? 'H:i' : 'h:i K', // formato mostrado al usuario
                    altInputClass: '', // hereda estilo via CSS .flatpickr-alt-input
                    time_24hr: _fmt24,
                    minuteIncrement: 5,
                    disableMobile: true,
                };
            }

            function initTimepickers(root) {
                (root || document).querySelectorAll('input.timepicker:not([data-fp-init])').forEach(function(el) {
                    el.setAttribute('data-fp-init', '1');
                    var fp = flatpickr(el, buildFpOptions());
                    // Copiar clases base del original al alt input para que herede los estilos
                    if (fp.altInput) {
                        var baseClass = el.className.replace(/\btimepicker\b/, '').trim();
                        fp.altInput.className = (baseClass + ' flatpickr-alt-input').trim();
                    }
                });
            }

            // Re-inicializa todos destruyendo primero las instancias existentes
            function reinitTimepickers(is24) {
                _fmt24 = is24;
                document.querySelectorAll('input.timepicker').forEach(function(el) {
                    var valActual = '';
                    if (el._flatpickr) {
                        valActual = el._flatpickr.input.value; // valor H:i del input real (oculto)
                        el._flatpickr.destroy();
                    }
                    el.removeAttribute('data-fp-init');
                    el.value = valActual;
                    var fp = flatpickr(el, buildFpOptions());
                    el.setAttribute('data-fp-init', '1');
                    if (fp.altInput) {
                        var baseClass = el.className.replace(/\btimepicker\b/, '').trim();
                        fp.altInput.className = (baseClass + ' flatpickr-alt-input').trim();
                        if (valActual) fp.setDate(valActual, false, 'H:i');
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                initTimepickers();
            });
            window.initTimepickers = initTimepickers;
            window.reinitTimepickers = reinitTimepickers;
        }());
    </script>

    {{-- Auto-wrap tablas de listado con scroll vertical --}}
    <script>
    (function () {
        // Clases excluidas: tablas de formulario / edición / pequeñas internas
        var EXCLUIR = [
            'tabla-items', 'tabla-dinamica', 'tabla-det', 'tabla-preview',
            'horario-tabla', 'tabla-fila', 'tabla-search', 'tabla-wrap',
            'tabla-container',
        ];

        function necesitaScroll(tabla) {
            if (tabla.closest('.tabla-scroll-auto')) return false;         // ya envuelta
            if (tabla.hasAttribute('data-no-scroll')) return false;        // marcada explícita
            var cls = tabla.className || '';
            for (var i = 0; i < EXCLUIR.length; i++) {
                if (cls.indexOf(EXCLUIR[i]) !== -1) return false;
            }
            // Solo envolver si tiene clase tabla-*
            return /\btabla-/.test(cls);
        }

        function envolver(tabla) {
            var padre = tabla.parentNode;
            // Si el padre ya es overflow-x:auto, reusar añadiendo max-height
            if (padre && padre.tagName !== 'BODY') {
                var st = padre.getAttribute('style') || '';
                if (st.indexOf('overflow-x') !== -1 || st.indexOf('overflow-y') !== -1) {
                    padre.classList.add('tabla-scroll-auto');
                    padre.style.removeProperty('overflow-x');
                    padre.style.removeProperty('overflow-y');
                    return;
                }
            }
            var div = document.createElement('div');
            div.className = 'tabla-scroll-auto';
            padre.insertBefore(div, tabla);
            div.appendChild(tabla);
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('table').forEach(function (tabla) {
                if (necesitaScroll(tabla)) envolver(tabla);
            });
        });
    }());
    </script>

    <script>
        // Scroll sidebar to show active item
        (function() {
            var activo = document.querySelector('#sidebar .nav-item-sidebar.activo');
            if (activo) {
                var sidebar = document.getElementById('sidebar');
                var itemTop = activo.getBoundingClientRect().top;
                var sidebarRect = sidebar.getBoundingClientRect();
                var sidebarH = sidebar.clientHeight;
                var offset = activo.offsetTop - sidebarH / 2 + activo.offsetHeight / 2;
                sidebar.scrollTop = Math.max(0, offset);
            }
        })();
    </script>

</body>

</html>