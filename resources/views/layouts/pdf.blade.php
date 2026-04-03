@php $C = '#1a3a6b'; @endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@yield('pdf-titulo', 'Documento Clínico')</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #111827;
            padding: 28px 32px 60px;
            line-height: 1.5;
        }

        /* ── HEADER ───────────────────────────────────────────────── */
        .pdf-hdr {
            display: table;
            width: 100%;
            padding-bottom: 12px;
            border-bottom: 3px solid {{ $C }};
            margin-bottom: 16px;
        }

        .pdf-hdr-logo {
            display: table-cell;
            width: 14%;
            vertical-align: middle;
        }

        .pdf-hdr-logo img {
            max-height: 62px;
            max-width: 130px;
        }

        .pdf-hdr-logo .ini {
            font-size: 22px;
            font-weight: bold;
            color: {{ $C }};
        }

        .pdf-hdr-info {
            display: table-cell;
            vertical-align: middle;
            padding: 0 14px;
        }

        .pdf-hdr-doc {
            display: table-cell;
            width: 30%;
            vertical-align: middle;
            text-align: right;
        }

        .cons-nombre {
            font-size: 14px;
            font-weight: bold;
            color: {{ $C }};
            line-height: 1.2;
        }

        .cons-slogan {
            font-size: 8px;
            color: #4b5563;
            font-style: italic;
            margin-top: 2px;
        }

        .cons-datos {
            font-size: 7.5px;
            color: #6b7280;
            margin-top: 5px;
            line-height: 1.7;
        }

        .cons-datos span {
            margin-right: 8px;
        }

        .doc-badge {
            border: 1.5px solid {{ $C }};
        }

        .doc-badge-head {
            background: {{ $C }};
            color: #fff;
            font-size: 8px;
            font-weight: bold;
            padding: 5px 10px;
            text-transform: uppercase;
            letter-spacing: .1em;
            text-align: center;
        }

        .doc-badge-body {
            padding: 6px 10px;
            text-align: center;
            background: #f0f5fb;
        }

        .doc-badge-num {
            font-size: 13px;
            font-weight: bold;
            color: {{ $C }};
            font-family: monospace;
            display: block;
        }

        .doc-badge-fecha {
            font-size: 7.5px;
            color: #6b7280;
            display: block;
            margin-top: 2px;
        }

        /* ── BLOQUE PACIENTE ──────────────────────────────────────── */
        .pac-blk {
            background: #eff6ff;
            border-left: 4px solid {{ $C }};
            padding: 9px 14px;
            margin-bottom: 15px;
        }

        .pac-grid {
            display: table;
            width: 100%;
        }

        .pac-cell {
            display: table-cell;
            vertical-align: top;
            padding-right: 12px;
        }

        .pac-cell:last-child {
            padding-right: 0;
        }

        .pac-lbl {
            font-size: 7px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .pac-val {
            font-size: 10px;
            font-weight: bold;
            color: #111827;
            margin-top: 1px;
        }

        .pac-det {
            font-size: 8px;
            color: #4b5563;
        }

        /* ── SECCIONES ────────────────────────────────────────────── */
        .s {
            margin-bottom: 14px;
        }

        .s-titulo {
            font-size: 8px;
            font-weight: bold;
            color: {{ $C }};
            text-transform: uppercase;
            letter-spacing: .07em;
            background: #f0f5fb;
            border-left: 4px solid {{ $C }};
            padding: 4px 8px;
            margin-bottom: 8px;
        }

        .grid {
            display: table;
            width: 100%;
        }

        .col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 14px;
        }

        .col:last-child {
            padding-right: 0;
        }

        .f {
            margin-bottom: 4px;
            font-size: 9.5px;
        }

        .fl {
            font-weight: bold;
            color: #374151;
        }

        /* ── SIGNOS VITALES ───────────────────────────────────────── */
        .vitals {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .v-cell {
            display: table-cell;
            text-align: center;
            padding: 7px 5px;
            border: 1px solid #c7d2e0;
            background: #f8fbff;
        }

        .v-lbl {
            font-size: 7px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: .4px;
        }

        .v-val {
            font-size: 11px;
            font-weight: bold;
            color: {{ $C }};
            margin-top: 2px;
        }

        .v-unit {
            font-size: 7.5px;
            color: #9ca3af;
        }

        /* ── TABLAS ───────────────────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        th {
            background: {{ $C }};
            color: #fff;
            font-size: 8px;
            padding: 5px 7px;
            text-align: left;
        }

        td {
            font-size: 9px;
            padding: 4px 7px;
            border-bottom: 1px solid #f3f4f6;
        }

        tr:nth-child(even) td {
            background: #f9fafb;
        }

        /* ── PRÓXIMA CITA ─────────────────────────────────────────── */
        .proxima {
            background: #f0fdf4;
            border-left: 3px solid #16a34a;
            padding: 7px 10px;
            margin-top: 4px;
            font-size: 9px;
        }

        /* ── FIRMAS ───────────────────────────────────────────────── */
        .firma-wrap {
            margin-top: 24px;
            border-top: 2.5px solid {{ $C }};
            padding-top: 16px;
            page-break-inside: avoid;
        }

        .firma-tabla {
            display: table;
            width: 100%;
        }

        .firma-col {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
            padding: 0 16px;
        }

        .firma-col.first {
            padding-left: 0;
        }

        .firma-col.last {
            border-left: 1px solid #e2e8f0;
            padding-right: 0;
        }

        .firma-tit {
            font-size: 8px;
            font-weight: bold;
            color: {{ $C }};
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-bottom: 8px;
            padding-bottom: 3px;
            border-bottom: 1px dashed #c7d2e0;
        }

        .firma-img {
            max-height: 65px;
            max-width: 200px;
            display: block;
        }

        .firma-linea {
            border-top: 1px solid #374151;
            padding-top: 5px;
            margin-top: 20px;
            font-size: 8px;
            color: #4b5563;
            width: 200px;
        }

        .firma-linea-img {
            border-top: 1px solid #374151;
            padding-top: 5px;
            margin-top: 5px;
            font-size: 8px;
            color: #4b5563;
            width: 200px;
        }

        .badge-ok {
            color: #166534;
            font-weight: bold;
        }

        .meta {
            font-size: 7px;
            color: #9ca3af;
            margin-top: 8px;
        }

        /* ── CORRECCIONES ─────────────────────────────────────────── */
        .corr {
            margin-top: 18px;
            border-top: 1px dashed {{ $C }};
            padding-top: 12px;
        }

        .corr-item {
            border-left: 2px solid {{ $C }};
            padding: 5px 9px;
            margin-bottom: 7px;
            background: #f8faff;
        }

        /* ── FOOTER FIJO ──────────────────────────────────────────── */
        .pdf-foot {
            position: fixed;
            bottom: 0;
            left: 32px;
            right: 32px;
            border-top: 1.5px solid #c7d2e0;
            padding-top: 5px;
            padding-bottom: 6px;
            background: #fff;
            display: table;
            width: calc(100% - 64px);
        }

        .pf-l {
            display: table-cell;
            font-size: 7px;
            color: #9ca3af;
            text-align: left;
            vertical-align: middle;
        }

        .pf-c {
            display: table-cell;
            font-size: 7px;
            color: #9ca3af;
            text-align: center;
            vertical-align: middle;
        }

        .pf-r {
            display: table-cell;
            font-size: 7px;
            color: #9ca3af;
            text-align: right;
            vertical-align: middle;
        }

        @yield('pdf-estilos')
    </style>
</head>

<body>

    {{-- ── FOOTER FIJO ── --}}
    <div class="pdf-foot">
        <div class="pf-l">
            {{ $config->nombre_consultorio }}
            @if ($config->direccion)
                · {{ $config->direccion }}
            @endif
            @if ($config->ciudad)
                · {{ $config->ciudad }}
            @endif
            @if ($config->telefono)
                · Tel: {{ $config->telefono }}
            @endif
            @if ($config->email)
                · {{ $config->email }}
            @endif
        </div>
        <div class="pf-c">Documento generado el {{ now()->format('d/m/Y H:i') }}</div>
        <div class="pf-r">@yield('pdf-footer-der')</div>
    </div>

    {{-- ── HEADER ── --}}
    <div class="pdf-hdr">
        <div class="pdf-hdr-logo">
            @if ($config->logo_path)
                <img src="{{ public_path('storage/' . $config->logo_path) }}" alt="Logo">
            @else
                <div class="ini">{{ mb_strtoupper(mb_substr($config->nombre_consultorio, 0, 2)) }}</div>
            @endif
        </div>
        <div class="pdf-hdr-info">
            <div class="cons-nombre">{{ $config->nombre_consultorio }}</div>
            @if ($config->slogan)
                <div class="cons-slogan">{{ $config->slogan }}</div>
            @endif
            <div class="cons-datos">
                @if ($config->nit)
                    <span>NIT: {{ $config->nit }}</span>
                @endif
                @if ($config->telefono)
                    <span>Tel: {{ $config->telefono }}</span>
                @endif

                @if ($config->direccion)
                    <br>
                    <span>
                        {{ $config->direccion }}
                        @if ($config->ciudad)
                            , {{ $config->ciudad }}
                        @endif
                    </span>
                @endif

                @if ($config->email)
                    <br>
                    <span>{{ $config->email }}</span>
                @endif
            </div>
        </div>
        <div class="pdf-hdr-doc">
            <div class="doc-badge">
                <div class="doc-badge-head">@yield('pdf-doc-tipo', 'DOCUMENTO CLÍNICO')</div>
                <div class="doc-badge-body">
                    <span class="doc-badge-num">@yield('pdf-doc-num')</span>
                    <span class="doc-badge-fecha">Generado el {{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── CONTENIDO ── --}}
    @yield('pdf-contenido')

</body>

</html>
