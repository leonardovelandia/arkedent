<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento no encontrado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:Arial,sans-serif; background:#fef2f2; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1rem; }
        .card { background:#fff; border-radius:16px; max-width:480px; width:100%; padding:2.5rem; text-align:center; box-shadow:0 8px 32px rgba(0,0,0,.1); }
        .icono { font-size:3.5rem; color:#dc2626; display:block; margin-bottom:1rem; }
        .titulo { font-size:1.25rem; font-weight:700; color:#1c2b22; margin-bottom:.75rem; }
        .sub { font-size:.875rem; color:#6b7280; line-height:1.7; }
        .token { font-family:monospace; font-size:.72rem; color:#9ca3af; word-break:break-all; margin-top:1rem; background:#f9fafb; padding:.5rem; border-radius:6px; }
    </style>
</head>
<body>
<div class="card">
    <i class="bi bi-shield-x icono"></i>
    <div class="titulo">Documento no encontrado</div>
    <div class="sub">
        El token de verificación no corresponde a ningún documento en el sistema,
        o el documento fue eliminado.<br><br>
        Si crees que esto es un error, contacta directamente al consultorio.
    </div>
    <div class="token">Token: {{ $token }}</div>
</div>
</body>
</html>
