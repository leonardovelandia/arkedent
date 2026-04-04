{{-- ============================================================
     LAYOUT: app-glass.blade.php
     Sistema: Arkedent
     Tema: Aurora + Glassmorphism
     Uso: @extends('layouts.app-glass') en cada vista
     ============================================================ --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titulo', 'Inicio') — {{ $config->nombre_consultorio ?? config('app.nombre_consultorio') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=5">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=5">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v=5">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=5">

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    @php
        $fuentePrincipal = $config->fuente_principal ?? 'DM Sans';
        $fuenteTitulos   = $config->fuente_titulos   ?? 'Playfair Display';
        $fuentesGoogle   = [
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($urlFuentePrincipal) }}&family={{ urlencode($urlFuenteTitulos) }}&display=swap" rel="stylesheet">

    <link href="{{ asset('css/temas.css') }}" rel="stylesheet">
    <link href="{{ asset('css/tabla-sistema.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        /* ══════════════════════════════════════════
           VARIABLES — iguales al layout original
           para que los componentes no se rompan
        ══════════════════════════════════════════ */
        :root {
            --color-principal:    #6B21A8;
            --color-claro:        #7C3AED;
            --color-muy-claro:    #F3E8FF;
            --color-hover:        #581C87;
            --color-sidebar:      #3B0764;
            --color-sidebar-2:    #4C1D95;
            --color-acento-activo:#C084FC;
            --color-badge-bg:     #F3E8FF;
            --color-badge-texto:  #6B21A8;
            --fondo-app:          #faf8f4;
            --fondo-borde:        #ede9e0;
            --fondo-card-alt:     #faf8ff;
            --gradiente-sidebar:  linear-gradient(155deg, #1a0a3e 0%, #0f0728 60%, #0a0520 100%);
            --gradiente-btn:      linear-gradient(135deg, #7C3AED 0%, #6B21A8 100%);
            --sombra-principal:   rgba(107, 33, 168, 0.15);
            --fuente-principal:   '{{ $fuentePrincipal }}', sans-serif;
            --fuente-titulos:     '{{ $fuenteTitulos }}', serif;
            --morado-base:        var(--color-principal);
            --morado-claro:       var(--color-claro);
            --morado-muy-claro:   var(--color-muy-claro);
            --morado-hover:       var(--color-hover);
            --sidebar-hover:      rgba(255,255,255,0.08);
            --sidebar-activo:     rgba(0,0,0,0.3);
            --sidebar-texto:      rgba(255,255,255,0.85);
            --sidebar-texto-activo: #ffffff;
            --sidebar-ancho:      280px;
            --navbar-altura:      60px;
            --crema:              var(--fondo-app);
            --crema-borde:        var(--fondo-borde);
            --texto-principal:    #1c2b22;
            --texto-secundario:   #5c6b62;
            --blanco:             #ffffff;

            /* ── Variables glass (usables en vistas) ── */
            --glass-bg:           rgba(255,255,255,0.07);
            --glass-border:       rgba(255,255,255,0.12);
            --glass-blur:         blur(24px) saturate(160%);
            --aurora-purple:      rgba(124,58,237,0.8);
            --aurora-cyan:        rgba(6,182,212,0.7);
            --aurora-violet:      rgba(99,59,221,0.6);
            --neon-purple:        rgba(167,139,250,0.9);
            --neon-cyan:          rgba(6,182,212,0.9);
        }

        /* Fuentes títulos */
        .marca-nombre, .login-titulo, .page-titulo,
        .bienvenida-banner h2, .metrica-numero,
        .stat-numero, h1, h2 {
            font-family: var(--fuente-titulos) !important;
        }

        * { box-sizing: border-box; }

        /* ══════════════════════════════════════════
           FONDO AURORA
        ══════════════════════════════════════════ */
        body {
            font-family: var(--fuente-principal);
            background: linear-gradient(160deg, #0a6a9e 0%, #084f7a 50%, #053d5e 100%)!important;
            color: white;
            margin: 0;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse 80% 60% at 10% 20%,  rgba(99,59,221,0.55)  0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 90% 10%,  rgba(6,182,212,0.45)  0%, transparent 55%),
                radial-gradient(ellipse 70% 55% at 80% 85%,  rgba(124,58,237,0.5)  0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 20% 80%,  rgba(14,165,233,0.4)  0%, transparent 55%),
                radial-gradient(ellipse 90% 70% at 50% 50%,  rgba(15,7,40,0.8)     0%, transparent 100%);
            pointer-events: none;
        }

        /* Orbes animados */
        .aurora-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .aurora-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
        }

        .orb-1 { width:600px;height:600px;background:radial-gradient(circle,rgba(124,58,237,.8),transparent 70%);top:-150px;left:-100px;animation:af0 22s ease-in-out infinite; }
        .orb-2 { width:500px;height:500px;background:radial-gradient(circle,rgba(6,182,212,.7),transparent 70%);top:-80px;right:-80px;animation:af1 29s 3s ease-in-out infinite; }
        .orb-3 { width:700px;height:700px;background:radial-gradient(circle,rgba(99,59,221,.6),transparent 70%);bottom:-200px;left:30%;animation:af2 25s 6s ease-in-out infinite; }
        .orb-4 { width:400px;height:400px;background:radial-gradient(circle,rgba(240,171,252,.4),transparent 70%);top:40%;right:-100px;animation:af0 36s 2s ease-in-out infinite; }
        .orb-5 { width:350px;height:350px;background:radial-gradient(circle,rgba(14,165,233,.5),transparent 70%);bottom:10%;left:-80px;animation:af1 32s 4s ease-in-out infinite; }

        @keyframes af0 { 0%,100%{transform:translate(0,0) scale(1)} 33%{transform:translate(60px,-40px) scale(1.1)} 66%{transform:translate(-40px,30px) scale(0.95)} }
        @keyframes af1 { 0%,100%{transform:translate(0,0) scale(1)} 33%{transform:translate(-50px,50px) scale(1.08)} 66%{transform:translate(70px,-30px) scale(0.92)} }
        @keyframes af2 { 0%,100%{transform:translate(0,0) scale(1)} 33%{transform:translate(40px,60px) scale(1.12)} 66%{transform:translate(-60px,-40px) scale(0.9)} }

        /* Hexágonos */
        .hex-bg { position:fixed;inset:0;z-index:1;pointer-events:none; }
        .hex-bg svg { width:100%;height:100%; }

        /* ══════════════════════════════════════════
           GLASS CARD — componente base global
        ══════════════════════════════════════════ */
        .glass-card {
            position: relative;
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(24px) saturate(160%);
            -webkit-backdrop-filter: blur(24px) saturate(160%);
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.12);
            box-shadow:
                0 8px 32px rgba(0,0,0,0.25),
                inset 0 1px 0 rgba(255,255,255,0.15);
            overflow: hidden;
            transition: box-shadow 0.3s, border-color 0.3s;
        }

        .glass-card:hover {
            border-color: rgba(255,255,255,0.2);
            box-shadow: 0 12px 40px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.2);
        }

        .glass-card::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 16px;
            padding: 1px;
            background: linear-gradient(135deg, rgba(167,139,250,0.5), rgba(6,182,212,0.3) 40%, transparent 60%, rgba(124,58,237,0.3));
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        /* Esquinas neon */
        .glass-corner {
            position: absolute;
            width: 14px;
            height: 14px;
            border: 1.5px solid rgba(167,139,250,0.7);
            z-index: 1;
        }
        .glass-corner.tl { top:8px;  left:8px;  border-right:none; border-bottom:none; }
        .glass-corner.tr { top:8px;  right:8px; border-left:none;  border-bottom:none; }
        .glass-corner.bl { bottom:8px; left:8px;  border-right:none; border-top:none; }
        .glass-corner.br { bottom:8px; right:8px; border-left:none;  border-top:none; }

        /* ══════════════════════════════════════════
           SIDEBAR GLASS
        ══════════════════════════════════════════ */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-ancho) !important;
            min-width: var(--sidebar-ancho);
            max-width: var(--sidebar-ancho);
            height: 100vh;
            background: rgba(10, 5, 32, 0.75);
            backdrop-filter: blur(24px) saturate(160%);
            -webkit-backdrop-filter: blur(24px) saturate(160%);
            border-right: 1px solid rgba(167,139,250,0.15);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Logo */
        .sidebar-logo {
            padding: 1.1rem 1.1rem .9rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            flex-shrink: 0;
        }

        .sidebar-logo-nombre {
            font-family: var(--fuente-titulos);
            font-size: 14px;
            font-weight: 700;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
            text-shadow: 0 0 20px rgba(167,139,250,0.5);
        }

        .sidebar-logo-fila {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .sidebar-logo-sub {
            font-size: 0.63rem;
            font-weight: 400;
            color: rgba(255,255,255,0.35);
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

        /* Nav */
        .sidebar-nav { flex:1; padding:0.75rem 0; }

        .nav-seccion-titulo {
            font-size: 0.58rem;
            font-weight: 600;
            color: rgba(167,139,250,0.5);
            letter-spacing: 0.14em;
            text-transform: uppercase;
            padding: 0.75rem 1.25rem 0.35rem;
            margin-top: 0.25rem;
        }

        .nav-item-sidebar {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.6rem 1.25rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 0.845rem;
            font-weight: 400;
            border-radius: 0;
            transition: background 0.15s, color 0.15s, transform 0.15s;
            white-space: nowrap;
            overflow: hidden;
            cursor: pointer;
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            position: relative;
        }

        .nav-item-sidebar:hover {
            background: rgba(167,139,250,0.1);
            color: white;
            transform: translateX(3px);
        }

        .nav-item-sidebar.activo {
            background: rgba(167,139,250,0.15);
            color: white;
            font-weight: 500;
        }

        .nav-item-sidebar.activo::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 3px;
            height: 100%;
            background: rgba(167,139,250,0.9);
            border-radius: 0 2px 2px 0;
            box-shadow: 0 0 8px rgba(167,139,250,0.6);
        }

        .nav-item-icono {
            font-size: 1rem;
            flex-shrink: 0;
            width: 18px;
            text-align: center;
            color: rgba(167,139,250,0.8);
        }

        .nav-item-sidebar.activo .nav-item-icono { color: #c4b5fd; }
        .nav-item-sidebar:hover .nav-item-icono  { color: #c4b5fd; }

        .nav-badge {
            margin-left: auto;
            background: rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.7);
            font-size: 0.65rem;
            font-weight: 500;
            padding: 1px 6px;
            border-radius: 50px;
        }

        /* Perfil sidebar */
        .sidebar-perfil {
            padding: 0.875rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,0.07);
            display: flex;
            align-items: center;
            gap: 0.65rem;
            flex-shrink: 0;
        }

        .perfil-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(167,139,250,0.2);
            border: 1px solid rgba(167,139,250,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 500;
            color: #c4b5fd;
            flex-shrink: 0;
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
            color: rgba(255,255,255,0.35);
            text-transform: capitalize;
        }

        .perfil-info { flex:1; overflow:hidden; }

        .btn-logout-sidebar {
            background: none;
            border: none;
            color: rgba(255,255,255,0.3);
            font-size: 0.95rem;
            cursor: pointer;
            padding: 4px;
            transition: color 0.15s;
            flex-shrink: 0;
        }

        .btn-logout-sidebar:hover { color: rgba(248,113,113,0.8); }

        /* ══════════════════════════════════════════
           NAVBAR GLASS
        ══════════════════════════════════════════ */
        #navbar-top {
            position: fixed;
            top: 0;
            left: var(--sidebar-ancho);
            right: 0;
            height: var(--navbar-altura);
            background: rgba(10,5,32,0.6);
            backdrop-filter: blur(20px) saturate(160%);
            -webkit-backdrop-filter: blur(20px) saturate(160%);
            border-bottom: 1px solid rgba(255,255,255,0.07);
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
            color: rgba(255,255,255,0.6);
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            transition: background 0.15s, color 0.15s;
            display: none;
        }

        .navbar-toggle:hover { background: rgba(255,255,255,0.08); color: white; }

        .navbar-titulo {
            font-size: 0.93rem;
            font-weight: 500;
            color: white;
            flex: 1;
            text-shadow: 0 0 20px rgba(167,139,250,0.4);
        }

        .navbar-titulo span {
            font-size: 0.8rem;
            font-weight: 400;
            color: rgba(255,255,255,0.35);
            margin-left: 0.35rem;
        }

        .navbar-acciones { display:flex; align-items:center; gap:0.5rem; }

        .btn-navbar-accion {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            color: rgba(255,255,255,0.6);
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
            position: relative;
        }

        .btn-navbar-accion:hover {
            background: rgba(167,139,250,0.15);
            border-color: rgba(167,139,250,0.3);
            color: #c4b5fd;
        }

        .notif-dot {
            position: absolute;
            top: 6px; right: 6px;
            width: 7px; height: 7px;
            background: #f87171;
            border-radius: 50%;
            border: 1.5px solid rgba(15,7,40,0.8);
        }

        .navbar-usuario {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.65rem 0.35rem 0.35rem;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
            background: rgba(255,255,255,0.05);
        }

        .navbar-usuario:hover {
            background: rgba(167,139,250,0.12);
            border-color: rgba(167,139,250,0.3);
        }

        .navbar-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(124,58,237,0.6), rgba(6,182,212,0.6));
            color: white;
            font-size: 0.72rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(167,139,250,0.3);
        }

        .navbar-usuario-nombre {
            font-size: 0.82rem;
            font-weight: 500;
            color: rgba(255,255,255,0.85);
        }

        /* Dropdown glass */
        .dropdown-menu {
            background: rgba(15,7,40,0.9) !important;
            backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(167,139,250,0.2) !important;
            border-radius: 12px !important;
            box-shadow: 0 16px 40px rgba(0,0,0,0.4) !important;
        }

        .dropdown-item {
            color: rgba(255,255,255,0.75) !important;
            font-size: 0.85rem !important;
            border-radius: 8px;
            margin: 2px 4px;
        }

        .dropdown-item:hover {
            background: rgba(167,139,250,0.15) !important;
            color: white !important;
        }

        .dropdown-item.text-danger { color: #fca5a5 !important; }
        .dropdown-item.text-danger:hover { background: rgba(248,113,113,0.15) !important; }

        .dropdown-divider { border-color: rgba(255,255,255,0.08) !important; }

        /* ══════════════════════════════════════════
           CONTENIDO PRINCIPAL
        ══════════════════════════════════════════ */
        #contenido-principal {
            margin-left: var(--sidebar-ancho);
            padding-top: var(--navbar-altura);
            min-height: 100vh;
            position: relative;
            z-index: 2;
        }

        .contenido-inner { padding: 1.75rem; }

        /* Overlay móvil */
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 999;
        }

        /* ══════════════════════════════════════════
           PÁGINA — títulos y headers
        ══════════════════════════════════════════ */
        .page-header { margin-bottom: 1.5rem; }

        .page-titulo {
            font-family: var(--fuente-titulos);
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
            margin-bottom: 0.2rem;
            text-shadow: 0 0 20px rgba(167,139,250,0.5);
        }

        .page-subtitulo {
            font-size: 0.85rem;
            font-weight: 400;
            color: rgba(255,255,255,0.5);
        }

        /* Títulos de paneles/cards — override para glass */
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
            color: rgba(167,139,250,0.9) !important;
            text-transform: uppercase !important;
            letter-spacing: .06em !important;
        }

        /* Headers de cards */
        .panel-card-header,
        .panel-header,
        .tabla-header {
            background: rgba(167,139,250,0.08) !important;
            border-bottom: 1px solid rgba(255,255,255,0.07) !important;
        }

        .info-card-titulo {
            background: rgba(167,139,250,0.08) !important;
            margin: -1.25rem -1.5rem .9rem !important;
            padding: .5rem 1.5rem !important;
            border-radius: 0 !important;
        }

        /* card-sistema glass */
        .card-sistema {
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(24px) saturate(160%);
            -webkit-backdrop-filter: blur(24px) saturate(160%);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            color: white;
        }

        /* Bootstrap .card override glass */
        .card {
            background: rgba(255,255,255,0.07) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(255,255,255,0.12) !important;
            border-radius: 14px !important;
            box-shadow: 0 8px 28px rgba(0,0,0,0.25) !important;
            color: white !important;
        }

        .card-body { color: white !important; }

        /* Textos Bootstrap override */
        .text-muted { color: rgba(255,255,255,0.45) !important; }
        label       { color: rgba(255,255,255,0.75) !important; }
        small       { color: rgba(255,255,255,0.45) !important; }

        /* Inputs glass */
        .form-control, .form-select {
            background: rgba(255,255,255,0.07) !important;
            border: 1px solid rgba(255,255,255,0.15) !important;
            border-radius: 10px !important;
            color: white !important;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control::placeholder { color: rgba(255,255,255,0.3) !important; }

        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.1) !important;
            border-color: rgba(167,139,250,0.5) !important;
            box-shadow: 0 0 0 3px rgba(167,139,250,0.15) !important;
            color: white !important;
        }

        .form-select option { background: #1a0a3e; color: white; }

        /* Botones glass */
        .btn-primary {
            background: linear-gradient(135deg, rgba(124,58,237,0.8), rgba(99,59,221,0.8)) !important;
            border: 1px solid rgba(167,139,250,0.4) !important;
            border-radius: 10px !important;
            color: white !important;
            box-shadow: 0 4px 15px rgba(124,58,237,0.3) !important;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, rgba(124,58,237,1), rgba(99,59,221,1)) !important;
            box-shadow: 0 6px 20px rgba(124,58,237,0.45) !important;
        }

        .btn-secondary {
            background: rgba(255,255,255,0.08) !important;
            border: 1px solid rgba(255,255,255,0.15) !important;
            border-radius: 10px !important;
            color: rgba(255,255,255,0.8) !important;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.15) !important;
            color: white !important;
        }

        .btn-danger {
            background: rgba(248,113,113,0.2) !important;
            border: 1px solid rgba(248,113,113,0.4) !important;
            border-radius: 10px !important;
            color: #fca5a5 !important;
        }

        .btn-danger:hover {
            background: rgba(248,113,113,0.35) !important;
            color: white !important;
        }

        .btn-success {
            background: rgba(74,222,128,0.2) !important;
            border: 1px solid rgba(74,222,128,0.4) !important;
            border-radius: 10px !important;
            color: #86efac !important;
        }

        .btn-success:hover {
            background: rgba(74,222,128,0.35) !important;
            color: white !important;
        }

        .btn-outline-primary {
            background: transparent !important;
            border: 1px solid rgba(167,139,250,0.5) !important;
            border-radius: 10px !important;
            color: #c4b5fd !important;
        }

        .btn-outline-primary:hover {
            background: rgba(167,139,250,0.15) !important;
            color: white !important;
        }

        /* Tablas glass */
        .table {
            color: rgba(255,255,255,0.85) !important;
            border-color: rgba(255,255,255,0.08) !important;
        }

        .table thead th {
            background: rgba(167,139,250,0.1) !important;
            color: rgba(167,139,250,0.9) !important;
            border-color: rgba(255,255,255,0.08) !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.06em !important;
            text-transform: uppercase !important;
        }

        .table tbody tr:hover {
            background: rgba(255,255,255,0.04) !important;
        }

        .table td, .table th {
            border-color: rgba(255,255,255,0.06) !important;
            vertical-align: middle;
        }

        /* Badges */
        .badge {
            border-radius: 50px !important;
            font-weight: 600 !important;
            font-size: 0.7rem !important;
        }

        .bg-success { background: rgba(74,222,128,0.2)   !important; color: #86efac !important; border: 1px solid rgba(74,222,128,0.3)   !important; }
        .bg-danger  { background: rgba(248,113,113,0.2)  !important; color: #fca5a5 !important; border: 1px solid rgba(248,113,113,0.3)  !important; }
        .bg-warning { background: rgba(251,191,36,0.2)   !important; color: #fde68a !important; border: 1px solid rgba(251,191,36,0.3)   !important; }
        .bg-info    { background: rgba(6,182,212,0.2)    !important; color: #67e8f9 !important; border: 1px solid rgba(6,182,212,0.3)    !important; }
        .bg-primary { background: rgba(167,139,250,0.2)  !important; color: #c4b5fd !important; border: 1px solid rgba(167,139,250,0.3)  !important; }
        .bg-secondary{ background: rgba(148,163,184,0.2) !important; color: #cbd5e1 !important; border: 1px solid rgba(148,163,184,0.3)  !important; }

        /* Alertas flash */
        .alerta-flash {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            backdrop-filter: blur(12px);
        }

        .alert-success {
            background: rgba(74,222,128,0.12)  !important;
            border: 1px solid rgba(74,222,128,0.3)  !important;
            color: #86efac !important;
        }

        .alert-danger {
            background: rgba(248,113,113,0.12) !important;
            border: 1px solid rgba(248,113,113,0.3) !important;
            color: #fca5a5 !important;
        }

        .alert-warning {
            background: rgba(251,191,36,0.12)  !important;
            border: 1px solid rgba(251,191,36,0.3)  !important;
            color: #fde68a !important;
        }

        /* Pagination glass */
        .page-link {
            background: rgba(255,255,255,0.06) !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            color: rgba(255,255,255,0.7) !important;
            border-radius: 8px !important;
        }

        .page-link:hover {
            background: rgba(167,139,250,0.15) !important;
            color: white !important;
        }

        .page-item.active .page-link {
            background: rgba(167,139,250,0.3) !important;
            border-color: rgba(167,139,250,0.5) !important;
            color: white !important;
        }

        /* Modal glass */
        .modal-content {
            background: rgba(15,7,40,0.85) !important;
            backdrop-filter: blur(32px) saturate(180%) !important;
            -webkit-backdrop-filter: blur(32px) saturate(180%) !important;
            border: 1px solid rgba(167,139,250,0.2) !important;
            border-radius: 20px !important;
            color: white !important;
            box-shadow: 0 25px 60px rgba(0,0,0,0.5) !important;
        }

        .modal-header {
            background: rgba(167,139,250,0.08) !important;
            border-bottom: 1px solid rgba(255,255,255,0.08) !important;
            border-radius: 20px 20px 0 0 !important;
        }

        .modal-title { color: white !important; }

        .modal-footer {
            border-top: 1px solid rgba(255,255,255,0.08) !important;
        }

        .btn-close {
            filter: invert(1) !important;
            opacity: 0.6 !important;
        }

        .btn-close:hover { opacity: 1 !important; }

        /* Flatpickr glass */
        .flatpickr-calendar {
            background: rgba(15,7,40,0.92) !important;
            border: 1px solid rgba(167,139,250,0.3) !important;
            border-radius: 14px !important;
            box-shadow: 0 16px 40px rgba(0,0,0,0.5) !important;
            color: white !important;
        }

        .flatpickr-time input,
        .flatpickr-time .flatpickr-time-separator {
            font-size: 1.5rem;
            font-weight: 700;
            color: white !important;
            background: transparent !important;
        }

        .flatpickr-time input:hover,
        .flatpickr-time input:focus {
            background: rgba(167,139,250,0.15) !important;
        }

        .timepicker-wrap { position:relative; display:flex; align-items:center; }
        .timepicker-wrap .timepicker-icon { position:absolute; left:.65rem; color:rgba(167,139,250,0.8); pointer-events:none; font-size:.95rem; }
        .timepicker-wrap input.timepicker { padding-left:2rem !important; cursor:pointer; }
        .timepicker-wrap input.flatpickr-alt-input { padding-left:2rem !important; cursor:pointer; }

        /* ══════════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════════ */
        @media (max-width: 900px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.abierto { transform: translateX(0); }
            #sidebar-overlay.visible { display: block; }
            #navbar-top { left: 0; }
            #contenido-principal { margin-left: 0; }
            .navbar-toggle { display: flex; }
        }

        @media (max-width: 600px) {
            .contenido-inner { padding: 1.25rem 1rem; }
        }
    </style>

    {{-- Estilos específicos de cada vista --}}
    @stack('estilos')
</head>

<body data-tema="{{ $config->tema ?? 'morado-elegante' }}">

    <form id="form-logout" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>

    {{-- Aurora --}}
    <div class="aurora-layer">
        <div class="aurora-orb orb-1"></div>
        <div class="aurora-orb orb-2"></div>
        <div class="aurora-orb orb-3"></div>
        <div class="aurora-orb orb-4"></div>
        <div class="aurora-orb orb-5"></div>
    </div>

    {{-- Hexágonos --}}
    <div class="hex-bg">
        <svg id="hexSvg" viewBox="0 0 1400 900" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
            <g id="hexGroup"></g>
        </svg>
    </div>

    {{-- Overlay móvil --}}
    <div id="sidebar-overlay"></div>

    {{-- ═══ SIDEBAR ═══ --}}
    <nav id="sidebar">

        @php
            $nombre   = $config->nombre_consultorio ?? 'Consultorio';
            $palabras = explode(' ', $nombre);
            if (count($palabras) > 2) {
                $linea1 = implode(' ', array_slice($palabras, 0, ceil(count($palabras) / 2)));
                $linea2 = implode(' ', array_slice($palabras, ceil(count($palabras) / 2)));
            } else {
                $linea1 = $nombre;
                $linea2 = '';
            }
        @endphp

        <div class="sidebar-logo">
            <div style="display:flex;align-items:center;gap:10px;">
                @if($config->logo_path ?? false)
                    <div style="width:45px;height:45px;flex-shrink:0;">
                        <img src="{{ asset('storage/'.$config->logo_path) }}" alt="Logo" style="width:100%;height:100%;object-fit:contain;">
                    </div>
                @else
                    <div style="width:40px;height:40px;background:rgba(167,139,250,0.15);border:1px solid rgba(167,139,250,0.3);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg viewBox="0 0 20 20" fill="none" style="width:22px;height:22px;">
                            <path d="M10 2C8.2 2 6.8 3.1 5.7 4.3C4.6 3.1 2.8 2 1.8 3.6C0.8 5.2 2 7.5 3.2 8.8C3.6 9.4 4 10.5 4.4 11.8C5.1 14.5 5.5 18 6.7 18C7.9 18 8.3 15.5 8.7 13.8C9.1 12.1 9.5 11.5 10 11.5C10.5 11.5 10.9 12.1 11.3 13.8C11.7 15.5 12.1 18 13.3 18C14.5 18 14.9 14.5 15.6 11.8C16 10.5 16.4 9.4 16.8 8.8C18 7.5 19.2 5.2 18.2 3.6C17.2 2 15.4 3.1 14.3 4.3C13.2 3.1 11.8 2 10 2Z" fill="rgba(167,139,250,0.9)"/>
                        </svg>
                    </div>
                @endif
                <div class="sidebar-logo-nombre" style="line-height:1.2;">
                    <div>{{ $linea1 }}</div>
                    @if($linea2)<div>{{ $linea2 }}</div>@endif
                </div>
            </div>
            <div class="sidebar-logo-sub" style="margin-top:5px;text-transform:none;">
                {{ $config->slogan ?? 'Sistema de gestión' }}
            </div>
        </div>

        <div class="sidebar-nav">

            <p class="nav-seccion-titulo">Principal</p>

            <a href="{{ route('dashboard') }}" class="nav-item-sidebar {{ request()->routeIs('dashboard') ? 'activo' : '' }}">
                <i class="bi bi-grid-1x2 nav-item-icono"></i> Dashboard
            </a>

            @modulo('pacientes')
            <a href="{{ route('pacientes.index') }}" class="nav-item-sidebar {{ request()->routeIs('pacientes.*') ? 'activo' : '' }}">
                <i class="bi bi-people nav-item-icono"></i> Pacientes
            </a>
            @endmodulo

            @modulo('citas')
            <a href="{{ route('citas.agenda') }}" class="nav-item-sidebar {{ request()->routeIs('citas.*') ? 'activo' : '' }}">
                <i class="bi bi-calendar3 nav-item-icono"></i> Agenda y Citas
            </a>
            @endmodulo

            @modulo('recordatorios')
            <a href="{{ route('recordatorios.index') }}" class="nav-item-sidebar {{ request()->routeIs('recordatorios.*') ? 'activo' : '' }}">
                <i class="bi bi-bell nav-item-icono"></i> Recordatorios
                @php $recFallidos = \Illuminate\Support\Facades\Cache::remember('sidebar_rec_fallidos', 300, fn() => \App\Models\Recordatorio::where('estado','fallido')->where('activo',true)->count()); @endphp
                @if($recFallidos > 0)
                    <span style="margin-left:auto;background:rgba(248,113,113,0.3);color:#fca5a5;font-size:.65rem;font-weight:700;padding:1px 7px;border-radius:50px;border:1px solid rgba(248,113,113,0.4);">{{ $recFallidos }}</span>
                @endif
            </a>
            @endmodulo

            @if(\App\Helpers\ModulosHelper::activo('historia_clinica') || \App\Helpers\ModulosHelper::activo('evoluciones') || \App\Helpers\ModulosHelper::activo('valoraciones') || \App\Helpers\ModulosHelper::activo('imagenes') || \App\Helpers\ModulosHelper::activo('ortodoncia') || \App\Helpers\ModulosHelper::activo('recetas') || \App\Helpers\ModulosHelper::activo('periodoncia'))
                <p class="nav-seccion-titulo">Clínica</p>
            @endif

            @modulo('historia_clinica')
            <a href="{{ route('historias.index') }}" class="nav-item-sidebar {{ request()->routeIs('historias.*') ? 'activo' : '' }}">
                <i class="bi bi-journal-medical nav-item-icono"></i> Historia Clínica
            </a>
            @endmodulo

            @modulo('evoluciones')
            <a href="{{ route('evoluciones.index') }}" class="nav-item-sidebar {{ request()->routeIs('evoluciones.*') ? 'activo' : '' }}">
                <i class="bi bi-clipboard2-pulse nav-item-icono"></i> Evoluciones
            </a>
            @endmodulo

            @modulo('valoraciones')
            <a href="{{ route('valoraciones.index') }}" class="nav-item-sidebar {{ request()->routeIs('valoraciones.*') ? 'activo' : '' }}">
                <i class="bi bi-search-heart nav-item-icono"></i> Valoraciones
            </a>
            @endmodulo

            @modulo('imagenes')
            <a href="{{ route('imagenes.index') }}" class="nav-item-sidebar {{ request()->routeIs('imagenes.*') ? 'activo' : '' }}">
                <i class="bi bi-images nav-item-icono"></i> Imágenes Clínicas
            </a>
            @endmodulo

            @modulo('ortodoncia')
            <a href="{{ route('ortodoncia.index') }}" class="nav-item-sidebar {{ request()->routeIs('ortodoncia.*') || request()->routeIs('controles.*') || request()->routeIs('retencion.*') ? 'activo' : '' }}">
                <i class="bi bi-symmetry-horizontal nav-item-icono"></i> Ortodoncia
            </a>
            @endmodulo

            @modulo('recetas')
            <a href="{{ route('recetas.index') }}" class="nav-item-sidebar {{ request()->routeIs('recetas.*') ? 'activo' : '' }}">
                <i class="bi bi-file-medical nav-item-icono"></i> Recetas Médicas
            </a>
            @endmodulo

            @modulo('periodoncia')
            <a href="{{ route('periodoncia.index') }}" class="nav-item-sidebar {{ request()->routeIs('periodoncia.*') ? 'activo' : '' }}">
                <i class="bi bi-heart-pulse nav-item-icono"></i> Periodoncia
            </a>
            @endmodulo

            @if(\App\Helpers\ModulosHelper::activo('presupuestos') || \App\Helpers\ModulosHelper::activo('pagos') || \App\Helpers\ModulosHelper::activo('consentimientos') || \App\Helpers\ModulosHelper::activo('laboratorio'))
                <p class="nav-seccion-titulo">Gestión</p>
            @endif

            @modulo('presupuestos')
            <a href="{{ route('presupuestos.index') }}" class="nav-item-sidebar {{ request()->routeIs('presupuestos.*') ? 'activo' : '' }}">
                <i class="bi bi-file-earmark-text nav-item-icono"></i> Presupuestos
            </a>
            @endmodulo

            @modulo('pagos')
            <a href="{{ route('pagos.index') }}" class="nav-item-sidebar {{ request()->routeIs('pagos.*') ? 'activo' : '' }}">
                <i class="bi bi-cash-coin nav-item-icono"></i> Abonos y Pagos
            </a>
            @endmodulo

            @modulo('consentimientos')
            <a href="{{ route('consentimientos.index') }}" class="nav-item-sidebar {{ request()->routeIs('consentimientos.*') ? 'activo' : '' }}">
                <i class="bi bi-pen nav-item-icono"></i> Consentimientos
            </a>
            @endmodulo

            @modulo('laboratorio')
            <a href="{{ route('laboratorio.index') }}" class="nav-item-sidebar {{ request()->routeIs('laboratorio.*') || request()->routeIs('gestion-laboratorios.*') ? 'activo' : '' }}">
                <i class="bi bi-eyedropper nav-item-icono"></i> Laboratorio
                @php $labVencidas = \Illuminate\Support\Facades\Cache::remember('sidebar_lab_vencidas', 300, fn() => \App\Models\OrdenLaboratorio::where('activo',true)->whereNotIn('estado',['recibido','instalado','cancelado'])->whereDate('fecha_entrega_estimada','<',today())->count()); @endphp
                @if($labVencidas > 0)
                    <span style="margin-left:auto;background:rgba(248,113,113,0.3);color:#fca5a5;font-size:.65rem;font-weight:700;padding:.1rem .45rem;border-radius:50px;border:1px solid rgba(248,113,113,0.4);line-height:1.4;">{{ $labVencidas }}</span>
                @endif
            </a>
            @endmodulo

            @role('administrador|doctora')
                @if(\App\Helpers\ModulosHelper::activo('inventario') || \App\Helpers\ModulosHelper::activo('proveedores') || \App\Helpers\ModulosHelper::activo('egresos') || \App\Helpers\ModulosHelper::activo('libro_contable') || \App\Helpers\ModulosHelper::activo('reportes') || \App\Helpers\ModulosHelper::activo('usuarios') || \App\Helpers\ModulosHelper::activo('configuracion'))
                    <p class="nav-seccion-titulo">Administración</p>
                @endif

                @modulo('inventario')
                <a href="{{ route('inventario.index') }}" class="nav-item-sidebar {{ request()->routeIs('inventario.*') ? 'activo' : '' }}">
                    <i class="bi bi-box-seam nav-item-icono"></i> Inventario
                    @php $stockBajo = \Illuminate\Support\Facades\Cache::remember('sidebar_stock_bajo', 300, fn() => \App\Models\Material::where('activo',true)->whereColumn('stock_actual','<=','stock_minimo')->count()); @endphp
                    @if($stockBajo > 0)
                        <span style="margin-left:auto;background:rgba(248,113,113,0.3);color:#fca5a5;font-size:.65rem;font-weight:700;padding:.1rem .45rem;border-radius:50px;border:1px solid rgba(248,113,113,0.4);line-height:1.4;">{{ $stockBajo }}</span>
                    @endif
                </a>
                @endmodulo

                @modulo('proveedores')
                <a href="{{ route('proveedores.index') }}" class="nav-item-sidebar {{ request()->routeIs('proveedores.*') ? 'activo' : '' }}">
                    <i class="bi bi-truck nav-item-icono"></i> Proveedores
                </a>
                <a href="{{ route('compras.index') }}" class="nav-item-sidebar {{ request()->routeIs('compras.*') ? 'activo' : '' }}">
                    <i class="bi bi-cart3 nav-item-icono"></i> Compras
                </a>
                @endmodulo

                @modulo('libro_contable')
                <a href="{{ route('libro-contable.index') }}" class="nav-item-sidebar {{ request()->routeIs('libro-contable.*') ? 'activo' : '' }}">
                    <i class="bi bi-journal-bookmark nav-item-icono"></i> Libro Contable
                </a>
                @endmodulo

                @modulo('egresos')
                <a href="{{ route('egresos.index') }}" class="nav-item-sidebar {{ request()->routeIs('egresos.*') ? 'activo' : '' }}">
                    <i class="bi bi-cash-stack nav-item-icono"></i> Egresos
                </a>
                @endmodulo

                @modulo('reportes')
                <a href="{{ route('reportes.index') }}" class="nav-item-sidebar {{ request()->routeIs('reportes.*') ? 'activo' : '' }}">
                    <i class="bi bi-bar-chart-line nav-item-icono"></i> Reportes
                </a>
                @endmodulo

                @modulo('usuarios')
                <a href="{{ route('usuarios.index') }}" class="nav-item-sidebar {{ request()->routeIs('usuarios.*') ? 'activo' : '' }}">
                    <i class="bi bi-person-gear nav-item-icono"></i> Usuarios y Roles
                </a>
                @endmodulo

                @modulo('configuracion')
                <a href="{{ route('configuracion.index') }}" class="nav-item-sidebar {{ request()->routeIs('configuracion.*') ? 'activo' : '' }}">
                    <i class="bi bi-gear nav-item-icono"></i> Configuración
                </a>
                @endmodulo

                @role('administrador')
                <a href="{{ route('auditoria.index') }}" class="nav-item-sidebar {{ request()->routeIs('auditoria.*') ? 'activo' : '' }}">
                    <i class="bi bi-shield-lock nav-item-icono"></i> Auditoría
                </a>
                @endrole
            @endrole

        </div>

        <div class="sidebar-perfil">
            <div class="perfil-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->name ?? 'U ')[1] ?? '', 0, 1)) }}
            </div>
            <div class="perfil-info">
                <div class="perfil-nombre">{{ auth()->user()->name ?? 'Usuario' }}</div>
                <div class="perfil-rol">{{ auth()->user()->getRoleNames()->first() ?? 'Sin rol' }}</div>
            </div>
            <button class="btn-logout-sidebar" onclick="document.getElementById('form-logout').submit()" title="Cerrar sesión">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </div>

    </nav>

    {{-- ═══ NAVBAR ═══ --}}
    <header id="navbar-top">
        <button class="navbar-toggle" id="btn-toggle-sidebar" aria-label="Abrir menú">
            <i class="bi bi-list"></i>
        </button>

        <div class="navbar-titulo">
            @yield('titulo', 'Dashboard')
            <span>/ {{ $config->nombre_consultorio ?? '' }}</span>
        </div>

        <div class="navbar-acciones">
            <a href="{{ route('pacientes.index') }}" class="btn-navbar-accion" title="Buscar paciente">
                <i class="bi bi-search"></i>
            </a>
            <button class="btn-navbar-accion" title="Notificaciones">
                <i class="bi bi-bell"></i>
                @if(auth()->user()->unreadNotifications()->count() > 0)
                    <span class="notif-dot"></span>
                @endif
            </button>
            <div class="dropdown">
                <div class="navbar-usuario" data-bs-toggle="dropdown">
                    <div class="navbar-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="navbar-usuario-nombre">
                        {{ explode(' ', auth()->user()->name ?? 'Usuario')[0] }}
                    </span>
                    <i class="bi bi-chevron-down" style="font-size:0.65rem;color:rgba(255,255,255,0.4);"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end" style="font-size:0.85rem;min-width:180px;">
                    <li><a class="dropdown-item" href="{{ route('perfil.index') }}"><i class="bi bi-person me-2"></i> Mi perfil</a></li>
                    <li><a class="dropdown-item" href="{{ route('configuracion.index') }}"><i class="bi bi-gear me-2"></i> Configuración</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><button class="dropdown-item text-danger" onclick="document.getElementById('form-logout').submit()"><i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión</button></li>
                </ul>
            </div>
        </div>
    </header>

    {{-- ═══ CONTENIDO ═══ --}}
    <main id="contenido-principal">
        <div class="contenido-inner">

            @if(session('exito'))
                <div class="alerta-flash alert alert-success d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alerta-flash alert alert-danger d-flex align-items-center gap-2">
                    <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('aviso'))
                <div class="alerta-flash alert alert-warning d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('aviso') }}
                </div>
            @endif

            @if(session('aviso_cruce'))
                <div class="alerta-flash alert alert-warning d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('aviso_cruce') }}
                </div>
            @endif

            @yield('contenido')

        </div>
    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        /* ═══ Toggle sidebar móvil ═══ */
        const btnToggle = document.getElementById('btn-toggle-sidebar');
        const sidebar   = document.getElementById('sidebar');
        const overlay   = document.getElementById('sidebar-overlay');

        function abrirSidebar()  { sidebar.classList.add('abierto');    overlay.classList.add('visible'); }
        function cerrarSidebar() { sidebar.classList.remove('abierto'); overlay.classList.remove('visible'); }

        if (btnToggle) btnToggle.addEventListener('click', () => sidebar.classList.contains('abierto') ? cerrarSidebar() : abrirSidebar());
        if (overlay)   overlay.addEventListener('click', cerrarSidebar);

        /* ═══ Auto-cerrar alertas flash ═══ */
        document.querySelectorAll('.alerta-flash').forEach(function(alerta) {
            setTimeout(function() {
                alerta.style.transition = 'opacity 0.4s ease';
                alerta.style.opacity = '0';
                setTimeout(() => alerta.remove(), 400);
            }, 4000);
        });

        /* ═══ Scroll sidebar al item activo ═══ */
        (function() {
            var activo = document.querySelector('#sidebar .nav-item-sidebar.activo');
            if (activo) {
                var sb = document.getElementById('sidebar');
                sb.scrollTop = Math.max(0, activo.offsetTop - sb.clientHeight / 2 + activo.offsetHeight / 2);
            }
        })();

        /* ═══ Hexágonos ═══ */
        (function() {
            const R=70, W=R*2, H=Math.sqrt(3)*R, COLS=13, ROWS=9;
            const colStep=W*0.75, rowStep=H;
            const vis=[
                1,1,1,1,1,1,1,0,0,0,0,0,0,
                1,1,1,1,1,0,0,0,0,0,0,0,0,
                1,1,1,1,0,0,0,0,0,0,0,0,1,
                1,0,0,0,0,0,0,0,0,1,1,1,1,
                1,0,0,0,0,0,0,1,1,1,1,1,1,
                0,0,0,0,0,1,1,1,1,1,1,1,1,
                0,0,0,0,1,1,1,1,1,1,1,1,1,
                0,0,0,0,1,1,1,1,1,1,1,1,1,
                0,0,0,1,1,1,1,1,1,1,1,1,1,
            ];
            function hpts(cx,cy,r){
                let s=[];
                for(let i=0;i<6;i++){const a=Math.PI/180*60*i;s.push(`${(cx+r*Math.cos(a)).toFixed(1)},${(cy+r*Math.sin(a)).toFixed(1)}`);}
                return s.join(' ');
            }
            const g=document.getElementById('hexGroup');
            let idx=0;
            for(let row=0;row<ROWS;row++){
                for(let col=0;col<COLS;col++){
                    const show=vis[idx++];
                    if(!show) continue;
                    const cx=col*colStep+R;
                    const cy=row*rowStep+H/2+(col%2===1?H/2:0);
                    const p=document.createElementNS('http://www.w3.org/2000/svg','polygon');
                    p.setAttribute('points', hpts(cx,cy,R-1));
                    p.setAttribute('fill','none');
                    p.setAttribute('stroke','rgba(255,255,255,0.08)');
                    p.setAttribute('stroke-width','0.8');
                    g.appendChild(p);
                }
            }
        })();
    </script>

    {{-- Formateador monetario --}}
    <script>
        (function() {
            function fmtNum(n) { return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
            function initMoneyInput(inp) {
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

    {{-- Botones retroceso inteligentes --}}
    <script>
        (function() {
            function marcarBotonesVolver() {
                document.querySelectorAll('a').forEach(function(link) {
                    if (link.querySelector('.bi-arrow-left') && !link.closest('#sidebar') && !link.classList.contains('nav-item-sidebar') && !link.hasAttribute('data-back-skip')) {
                        link.setAttribute('data-back', '1');
                    }
                });
            }
            document.addEventListener('DOMContentLoaded', marcarBotonesVolver);
            document.addEventListener('click', function(e) {
                var link = e.target.closest('a[data-back="1"]');
                if (!link) return;
                if (window.history.length > 1 && document.referrer !== '') {
                    e.preventDefault();
                    history.back();
                }
            });
        }());
    </script>

    {{-- Auto-wrap tablas --}}
    <script>
        (function() {
            var EXCLUIR = ['tabla-items','tabla-dinamica','tabla-det','tabla-preview','horario-tabla','tabla-fila','tabla-search','tabla-wrap','tabla-container'];
            function necesitaScroll(tabla) {
                if (tabla.closest('.tabla-scroll-auto')) return false;
                if (tabla.hasAttribute('data-no-scroll')) return false;
                var cls = tabla.className || '';
                for (var i = 0; i < EXCLUIR.length; i++) { if (cls.indexOf(EXCLUIR[i]) !== -1) return false; }
                return /\btabla-/.test(cls);
            }
            function envolver(tabla) {
                var padre = tabla.parentNode;
                var div = document.createElement('div');
                div.className = 'tabla-scroll-auto';
                padre.insertBefore(div, tabla);
                div.appendChild(tabla);
            }
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('table').forEach(function(tabla) { if (necesitaScroll(tabla)) envolver(tabla); });
            });
        }());
    </script>

    {{-- Scripts específicos de cada vista --}}
    @stack('scripts')

    {{-- Flatpickr --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        (function() {
            var _fmt24 = '{{ $config->formato_hora ?? '12' }}' === '24';
            function buildFpOptions() {
                return {
                    enableTime: true, noCalendar: true,
                    dateFormat: 'H:i',
                    altInput: true,
                    altFormat: _fmt24 ? 'H:i' : 'h:i K',
                    altInputClass: '',
                    time_24hr: _fmt24,
                    minuteIncrement: 5,
                    disableMobile: true,
                };
            }
            function initTimepickers(root) {
                (root || document).querySelectorAll('input.timepicker:not([data-fp-init])').forEach(function(el) {
                    el.setAttribute('data-fp-init', '1');
                    var fp = flatpickr(el, buildFpOptions());
                    if (fp.altInput) {
                        var baseClass = el.className.replace(/\btimepicker\b/, '').trim();
                        fp.altInput.className = (baseClass + ' flatpickr-alt-input').trim();
                    }
                });
            }
            function reinitTimepickers(is24) {
                _fmt24 = is24;
                document.querySelectorAll('input.timepicker').forEach(function(el) {
                    var valActual = '';
                    if (el._flatpickr) { valActual = el._flatpickr.input.value; el._flatpickr.destroy(); }
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
            document.addEventListener('DOMContentLoaded', function() { initTimepickers(); });
            window.initTimepickers  = initTimepickers;
            window.reinitTimepickers = reinitTimepickers;
        }());
    </script>

</body>
</html>