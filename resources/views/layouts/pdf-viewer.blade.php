<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo ?? 'Documento PDF' }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon.png') }}?v=4">
    <link rel="icon" type="image/png" sizes="32x32"   href="{{ asset('favicon.png') }}?v=4">
    <link rel="icon" type="image/png" sizes="16x16"   href="{{ asset('favicon.png') }}?v=4">
    <link rel="shortcut icon" type="image/png"        href="{{ asset('favicon.png') }}?v=4">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; height: 100%; overflow: hidden; background: #404040; }
        embed { display: block; width: 100%; height: 100vh; border: none; }
    </style>
</head>
<body>
    <embed src="{{ $urlPdf }}" type="application/pdf" width="100%" height="100%">
</body>
</html>
