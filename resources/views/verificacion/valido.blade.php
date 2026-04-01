<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento Verificado — {{ $config->nombre_consultorio ?? 'Consultorio' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:Arial,sans-serif; background:#f0fdf4; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1rem; }
        .card { background:#fff; border-radius:16px; max-width:620px; width:100%; box-shadow:0 8px 32px rgba(0,0,0,.1); overflow:hidden; }
        .hdr { background:#16a34a; padding:1.75rem; text-align:center; }
        .hdr i { font-size:3rem; color:#fff; display:block; margin-bottom:.5rem; }
        .hdr-titulo { font-size:1.25rem; font-weight:700; color:#fff; }
        .hdr-sub { font-size:.82rem; color:rgba(255,255,255,.8); margin-top:.25rem; }
        .body { padding:1.5rem; }
        .dato { display:flex; justify-content:space-between; align-items:flex-start; padding:.55rem 0; border-bottom:1px solid #f0f0f0; font-size:.85rem; gap:1rem; }
        .dato:last-child { border-bottom:none; }
        .dato-label { color:#6b7280; font-weight:500; white-space:nowrap; }
        .dato-valor { color:#1c2b22; font-weight:600; text-align:right; }
        .hash-box { font-family:monospace; font-size:.7rem; color:#4c1d95; word-break:break-all; background:#f5f3ff; padding:.5rem; border-radius:6px; margin-top:.75rem; }
        .legal { margin-top:1.25rem; padding:.875rem; background:#f0fdf4; border:1px solid #d1fae5; border-radius:8px; font-size:.75rem; color:#374151; line-height:1.6; }
        .footer { background:#f9fafb; padding:.875rem 1.5rem; text-align:center; font-size:.72rem; color:#9ca3af; border-top:1px solid #f0f0f0; }
        .sello { display:inline-flex; align-items:center; gap:.4rem; background:#dcfce7; color:#15803d; font-weight:700; font-size:.82rem; padding:.4rem 1rem; border-radius:50px; margin-bottom:1rem; }
    </style>
</head>
<body>
<div class="card">
    <div class="hdr">
        <i class="bi bi-shield-check"></i>
        <div class="hdr-titulo">Documento Verificado</div>
        <div class="hdr-sub">{{ $config->nombre_consultorio ?? 'Consultorio' }}</div>
    </div>
    <div class="body">
        <div style="text-align:center; margin-bottom:1rem;">
            <span class="sello"><i class="bi bi-patch-check-fill"></i> Firma electrónica válida</span>
        </div>

        <div class="dato">
            <span class="dato-label">Paciente</span>
            <span class="dato-valor">{{ $paciente->nombre_completo ?? '—' }}</span>
        </div>
        <div class="dato">
            <span class="dato-label">Documento</span>
            <span class="dato-valor">{{ $paciente->tipo_documento ?? '' }} {{ $paciente->numero_documento ?? '—' }}</span>
        </div>
        <div class="dato">
            <span class="dato-label">Tipo</span>
            <span class="dato-valor">{{ $tipo }}</span>
        </div>
        <div class="dato">
            <span class="dato-label">Fecha y hora de firma</span>
            <span class="dato-valor">
                {{ $timestamp ? \Carbon\Carbon::parse($timestamp)->setTimezone('America/Bogota')->format('d/m/Y H:i:s') : '—' }}
                <br><small style="color:#9ca3af;font-weight:400;">(UTC-5 Bogotá)</small>
            </span>
        </div>
        <div class="dato">
            <span class="dato-label">IP de origen</span>
            <span class="dato-valor">{{ $ip ?? '—' }}</span>
        </div>
        <div class="dato">
            <span class="dato-label">Dispositivo</span>
            <span class="dato-valor">{{ $dispositivo ?? '—' }}</span>
        </div>
        <div class="dato">
            <span class="dato-label">Navegador</span>
            <span class="dato-valor">{{ $navegador ?? '—' }}</span>
        </div>

        <div style="margin-top:.75rem;">
            <div style="font-size:.78rem;color:#6b7280;margin-bottom:.3rem;">Hash de integridad:</div>
            <div class="hash-box">{{ $hash }}</div>
        </div>

        <div class="legal">
            <strong>Validez legal:</strong> Este documento fue firmado electrónicamente conforme a la
            <strong>Ley 527 de 1999</strong> (Comercio Electrónico),
            el <strong>Decreto 2364 de 2012</strong> (Firma Electrónica) y la
            <strong>Ley 1581 de 2012</strong> (Habeas Data) de la República de Colombia.
            La firma electrónica tiene plena validez jurídica equivalente a la firma manuscrita.
        </div>
    </div>
    <div class="footer">
        Verificado el {{ now()->format('d/m/Y H:i') }} —
        {{ $config->nombre_consultorio ?? '' }}
        @if($config->email ?? null) · {{ $config->email }}@endif
    </div>
</div>
</body>
</html>
