<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Panel Dev') — Arkedent Dev</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            /* Mantener variables compatibles con las vistas que usan var(--color-principal) */
            --color-principal:  #7C3AED;
            --color-claro:      #9F5FF1;
            --color-muy-claro:  #2a1a3e;
            --color-hover:      #C084FC;
            --fondo-app:        #0f0f0f;
            --fondo-borde:      #2a2a2a;
            --fondo-card-alt:   #1a1a1a;
            --sombra-principal: rgba(107,33,168,0.25);
            --texto-principal:  #e0e0e0;
            --texto-secundario: #888;
            --acento:           #6B21A8;
            --acento-claro:     #C084FC;
            --fuente-principal: 'Courier New', monospace;
            --fuente-titulos:   'Courier New', monospace;
        }

        * { box-sizing: border-box; }

        body {
            background: #0f0f0f;
            color: #e0e0e0;
            font-family: 'Courier New', monospace;
            min-height: 100vh;
        }

        /* ── Topbar dev ── */
        .dev-topbar {
            background: #111;
            border-bottom: 1px solid #2a2a2a;
            padding: 0.6rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .dev-brand {
            font-size: 0.8rem;
            font-weight: 700;
            color: #C084FC;
            letter-spacing: 0.08em;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .dev-brand i { font-size: 1rem; }

        .dev-nav {
            display: flex;
            gap: 0.35rem;
            flex: 1;
        }

        .dev-nav-link {
            font-size: 0.72rem;
            padding: 5px 12px;
            border-radius: 6px;
            text-decoration: none;
            border: 1px solid #333;
            color: #888;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .dev-nav-link:hover { border-color: #555; color: #ccc; }
        .dev-nav-link.activo { background: #6B21A8; color: white; border-color: #6B21A8; }

        .dev-nav-sep {
            width: 1px;
            height: 20px;
            background: #2a2a2a;
            align-self: center;
            margin: 0 0.25rem;
        }

        .dev-logout {
            font-size: 0.72rem;
            padding: 5px 12px;
            border-radius: 6px;
            text-decoration: none;
            border: 1px solid #333;
            color: #666;
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 0.35rem;
            transition: all 0.15s;
        }
        .dev-logout:hover { border-color: #ef4444; color: #ef4444; }

        /* ── Contenido ── */
        .dev-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem 1.25rem 3rem;
        }

        /* ── Cards adaptados al tema oscuro ── */
        .card-sistema {
            background: #1a1a1a !important;
            border: 1px solid #2a2a2a !important;
            color: #e0e0e0 !important;
            box-shadow: 0 4px 20px rgba(107,33,168,0.15) !important;
        }

        /* Override estilos de las vistas de importación para tema oscuro */
        .metrica-card {
            background: #1a1a1a !important;
            border-color: #2a2a2a !important;
            color: #e0e0e0 !important;
        }

        .metrica-label { color: #888 !important; }
        .metrica-numero { color: #C084FC !important; }
        .metrica-sub { color: #666 !important; }

        .tabla-container {
            background: #1a1a1a !important;
            border-color: #2a2a2a !important;
        }

        .tabla-header th {
            background: #111 !important;
            color: #C084FC !important;
            border-color: #2a2a2a !important;
        }

        .tabla-body tr td { border-color: #2a2a2a !important; color: #ccc !important; }
        .tabla-body tr:hover td { background: #222 !important; }

        .page-titulo { color: #C084FC !important; }
        .page-subtitulo { color: #666 !important; }

        /* Inputs en oscuro */
        .form-control, .form-select, input[type="text"], input[type="date"],
        input[type="email"], input[type="number"], textarea, select {
            background: #111 !important;
            border-color: #333 !important;
            color: #e0e0e0 !important;
        }

        .form-control:focus, .form-select:focus {
            border-color: #6B21A8 !important;
            box-shadow: 0 0 0 3px rgba(107,33,168,0.2) !important;
        }

        /* Labels en oscuro */
        label, .form-label { color: #aaa !important; }

        /* Cards blancos → oscuros */
        .bg-white, [style*="background:#fff"], [style*="background: #fff"],
        [style*="background:white"], [style*="background: white"] {
            /* Can't override inline styles easily, handled via JS below */
        }

        /* Alertas */
        .alert-success { background: #0a2a0a; border-color: #155724; color: #4ade80; }
        .alert-danger  { background: #2a0a0a; border-color: #721c24; color: #f87171; }
        .alert-warning { background: #2a1a00; border-color: #856404; color: #fbbf24; }
        .alert-info    { background: #0a1a2a; border-color: #0c5460; color: #93c5fd; }

        /* Zona de upload */
        #zona-upload {
            border-color: #6B21A8 !important;
            background: #1a0d2e !important;
        }

        /* Stepper */
        .step-label { color: #888 !important; }
        .step-circle { border-color: #333 !important; background: #111 !important; color: #666 !important; }
        .step.activo .step-circle { background: #6B21A8 !important; border-color: #6B21A8 !important; color: white !important; }
        .step.completado .step-circle { background: #155724 !important; border-color: #155724 !important; }
        .step-linea { background: #333 !important; }

        /* Fuente card */
        .fuente-card {
            background: #1a1a1a !important;
            border-color: #333 !important;
            color: #e0e0e0 !important;
        }

        .fuente-card div { color: #ccc !important; }
    </style>

    @stack('estilos')
</head>
<body>

{{-- Topbar del panel dev --}}
<div class="dev-topbar">
    <a href="{{ route('dev.home') }}" class="dev-brand">
        <i class="bi bi-terminal"></i>
        DEV PANEL
    </a>

    <div class="dev-nav">
        <a href="{{ route('dev.modulos') }}"
           class="dev-nav-link {{ request()->is('dev/modulos') ? 'activo' : '' }}">
            <i class="bi bi-toggles"></i> Módulos
        </a>
        <a href="{{ route('dev.password') }}"
           class="dev-nav-link {{ request()->is('dev/password') ? 'activo' : '' }}">
            <i class="bi bi-key"></i> Contraseña
        </a>
        <a href="{{ route('dev.importacion.index') }}"
           class="dev-nav-link {{ request()->is('dev/importacion*') ? 'activo' : '' }}">
            <i class="bi bi-database-up"></i> Importar Datos
        </a>
    </div>

    <a href="{{ route('dev.auth.logout') }}" class="dev-logout">
        <i class="bi bi-box-arrow-right"></i> Cerrar sesión dev
    </a>
</div>

{{-- Mensajes flash --}}
@if(session('exito'))
<div style="background:#0a2a0a;border-bottom:1px solid #155724;padding:.65rem 1.5rem;font-size:.82rem;color:#4ade80;display:flex;align-items:center;gap:.5rem;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif
@if(session('error'))
<div style="background:#2a0a0a;border-bottom:1px solid #721c24;padding:.65rem 1.5rem;font-size:.82rem;color:#f87171;display:flex;align-items:center;gap:.5rem;">
    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
</div>
@endif

{{-- Contenido principal --}}
<div class="dev-content">
    @yield('contenido')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

<script>
// Oscurecer elementos con estilos inline blancos
document.querySelectorAll('[style]').forEach(el => {
    const s = el.getAttribute('style');
    if (s && (s.includes('background:#fff') || s.includes('background: #fff') || s.includes('background:white') || s.includes('background: white'))) {
        el.style.background = '#1a1a1a';
        if (s.includes('color:#1c2b22') || s.includes('color: #1c2b22')) {
            el.style.color = '#e0e0e0';
        }
    }
    if (s && (s.includes('color:#1c2b22') || s.includes('color: #1c2b22'))) {
        el.style.color = '#e0e0e0';
    }
    if (s && s.includes('border-color') && (s.includes('#e5e5e5') || s.includes('#e0e0e0') || s.includes('#dee2e6'))) {
        el.style.borderColor = '#333';
    }
});
</script>
</body>
</html>
