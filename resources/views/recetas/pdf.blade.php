<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Receta {{ $receta->numero_receta }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: white;
            line-height: 1.5;
        }

        .pagina {
            padding: 28px 40px 64px;
            max-width: 210mm;
        }

        /* ── HEADER ─────────────────────────────────────────── */
        .pdf-hdr {
            display: table;
            width: 100%;
            padding-bottom: 12px;
            border-bottom: 3px solid #1a3a6b;
            margin-bottom: 18px;
        }

        .hdr-logo-cell {
            display: table-cell;
            width: 14%;
            vertical-align: middle;
        }

        .hdr-logo-cell img {
            max-height: 60px;
            max-width: 130px;
        }

        .hdr-info-cell {
            display: table-cell;
            vertical-align: middle;
            padding: 0 14px;
        }

        .hdr-doc-cell {
            display: table-cell;
            width: 28%;
            vertical-align: middle;
            text-align: right;
        }

        .cons-nombre {
            font-size: 14px;
            font-weight: bold;
            color: #1a3a6b;
            line-height: 1.2;
        }

        .cons-cargo {
            font-size: 10px;
            color: #1a3a6b;
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
            border: 1.5px solid #1a3a6b;
        }

        .doc-badge-head {
            background: #1a3a6b;
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
            color: #1a3a6b;
            font-family: monospace;
            display: block;
        }

        .doc-badge-fecha {
            font-size: 7.5px;
            color: #6b7280;
            display: block;
            margin-top: 2px;
        }

        /* ── TÍTULO RECETA ───────────────────────────────────── */
        .titulo-receta {
            text-align: center;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: #1a3a6b;
            margin-bottom: 16px;
            padding-bottom: 6px;
            border-bottom: 1px solid #c7d2e0;
        }

        /* ── PACIENTE ────────────────────────────────────────── */
        .pac-blk {
            background: #eff6ff;
            border-left: 4px solid #1a3a6b;
            padding: 9px 14px;
            margin-bottom: 16px;
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
            font-size: 10.5px;
            font-weight: bold;
            color: #111827;
            margin-top: 1px;
        }

        .pac-det {
            font-size: 8px;
            color: #4b5563;
        }

        /* ── DIAGNÓSTICO ─────────────────────────────────────── */
        .diagnostico {
            margin-bottom: 16px;
        }

        .sec-lbl {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6b7280;
            margin-bottom: 3px;
        }

        .diagnostico-texto {
            font-size: 11px;
            color: #1a1a1a;
            font-style: italic;
        }

        /* ── MEDICAMENTOS ────────────────────────────────────── */
        .rp-titulo {
            font-size: 15px;
            font-style: italic;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 12px;
        }

        .med-item {
            margin-bottom: 14px;
            padding-left: 18px;
        }

        .med-linea-1 {
            font-size: 11.5px;
            color: #1a1a1a;
            line-height: 1.5;
        }

        .med-romano {
            font-style: italic;
            color: #666;
            font-size: 10.5px;
            margin-right: 2px;
        }

        .med-negrita {
            font-weight: bold;
        }

        .med-sig {
            font-size: 10.5px;
            color: #333;
            padding-left: 14px;
            line-height: 1.5;
            margin-top: 1px;
        }

        .med-nota {
            font-size: 10px;
            color: #777;
            padding-left: 14px;
            font-style: italic;
            margin-top: 1px;
        }

        .med-hr {
            border: none;
            border-top: 1px dashed #ddd;
            margin: 10px 0 10px 18px;
        }

        /* ── INDICACIONES GENERALES ──────────────────────────── */
        .indic-wrap {
            margin-top: 18px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
        }

        .indic-texto {
            font-size: 10.5px;
            color: #333;
            line-height: 1.6;
        }

        /* ── FIRMA PROFESIONAL ───────────────────────────────── */
        .firma-seccion {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .firma-izq {
            display: table-cell;
            vertical-align: bottom;
            width: 50%;
        }

        .firma-der {
            display: table-cell;
            vertical-align: bottom;
            text-align: right;
            width: 50%;
            padding-left: 30px;
        }

        .firma-canvas-wrap {
            height: 58px;
        }

        .firma-img-doc {
            max-height: 58px;
            max-width: 200px;
        }

        .firma-linea {
            border-top: 1px solid #1a1a1a;
            padding-top: 5px;
            margin-top: 0;
        }

        .firma-nombre {
            font-size: 11px;
            font-weight: bold;
            color: #1a1a1a;
        }

        .firma-cargo {
            font-size: 9.5px;
            color: #666;
            margin-top: 1px;
        }

        .firma-reg {
            font-size: 9px;
            color: #999;
            margin-top: 1px;
        }

        .sello-dig {
            display: inline-block;
            border: 1.5px solid #1a3a6b;
            padding: 7px 14px;
            text-align: center;
            background: #f0f5fb;
        }

        .sello-dig-titulo {
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #1a3a6b;
            font-weight: bold;
        }

        .sello-dig-fecha {
            font-size: 8px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ── FOOTER FIJO ─────────────────────────────────────── */
        .pdf-foot {
            position: fixed;
            bottom: 0;
            left: 40px;
            right: 40px;
            border-top: 1.5px solid #c7d2e0;
            padding: 5px 0 6px;
            background: #fff;
            display: table;
            width: calc(100% - 80px);
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
            font-family: monospace;
        }
    </style>
</head>

<body>
    <div class="pagina">

        {{-- ── FOOTER FIJO ── --}}
        <div class="pdf-foot">
            <div class="pf-l">
                {{ $configuracion?->nombre_consultorio }}
                @if ($configuracion?->direccion)
                    · {{ $configuracion->direccion }}
                @endif
                @if ($configuracion?->ciudad)
                    · {{ $configuracion->ciudad }}
                @endif
                @if ($configuracion?->telefono)
                    · Tel: {{ $configuracion->telefono }}
                @endif
            </div>
            <div class="pf-c">Documento generado el {{ now()->format('d/m/Y H:i') }}</div>
            <div class="pf-r">{{ $receta->numero_receta }}</div>
        </div>

        {{-- ── HEADER ── --}}
        <div class="pdf-hdr">
            <div class="hdr-logo-cell">
                @if ($configuracion?->logo_path)
                    <img src="{{ public_path('storage/' . $configuracion->logo_path) }}" alt="Logo">
                @else
                    <div style="font-size:22px;font-weight:bold;color:#1a3a6b;">
                        {{ mb_strtoupper(mb_substr($configuracion?->nombre_consultorio ?? 'OD', 0, 2)) }}</div>
                @endif
            </div>
            <div class="hdr-info-cell">
                @if ($configuracion?->nombre_consultorio && $configuracion?->firma_nombre_doctor)
                    <div class="cons-nombre">{{ $configuracion->nombre_consultorio }}
                    </div>
                @endif
                <div class="cons-datos">
                    @if ($configuracion?->nit)
                        <span>NIT: {{ $configuracion->nit }}</span>
                    @endif

                   

                    @if ($configuracion?->direccion)
                        <br>
                        <span>
                            {{ $configuracion->direccion }}
                            @if ($configuracion?->ciudad)
                                , {{ $configuracion->ciudad }}
                            @endif
                        </span>
                    @endif
                     @if ($configuracion?->telefono)
                     <br>
                        <span>Tel: {{ $configuracion->telefono }}</span>
                    @endif

                    @if ($configuracion?->email)
                        <br>
                        <span>{{ $configuracion->email }}</span>
                    @endif
                </div>
            </div>
            <div class="hdr-doc-cell">
                <div class="doc-badge">
                    <div class="doc-badge-head">RECETA MÉDICA</div>
                    <div class="doc-badge-body">
                        <span class="doc-badge-num">{{ $receta->numero_receta }}</span>
                        <span
                            class="doc-badge-fecha">{{ $receta->fecha->locale('es')->translatedFormat('d \d\e F \d\e Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TÍTULO ── --}}
        <div class="titulo-receta">Receta Médica Odontológica</div>

        {{-- ── DATOS PACIENTE ── --}}
        <div class="pac-blk">
            <div class="pac-grid">
                <div class="pac-cell">
                    <div class="pac-lbl">Paciente</div>
                    <div class="pac-val">{{ $receta->paciente->nombre_completo }}</div>
                    <div class="pac-det">{{ $receta->paciente->tipo_documento }}:
                        {{ $receta->paciente->numero_documento }}</div>
                </div>
                <div class="pac-cell">
                    <div class="pac-lbl">Edad</div>
                    <div class="pac-val">{{ $receta->paciente->edad }} años</div>
                </div>
                <div class="pac-cell">
                    <div class="pac-lbl">Historia Clínica</div>
                    <div class="pac-val">{{ $receta->paciente->numero_historia }}</div>
                </div>
                <div class="pac-cell">
                    <div class="pac-lbl">Fecha Emisión</div>
                    <div class="pac-val">{{ $receta->fecha->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>

        {{-- ── DIAGNÓSTICO ── --}}
        @if ($receta->diagnostico)
            <div class="diagnostico">
                <div class="sec-lbl">Diagnóstico</div>
                <div class="diagnostico-texto">{{ $receta->diagnostico }}</div>
            </div>
        @endif

        {{-- ── MEDICAMENTOS ── --}}
        <div class="rp-titulo">Rp.</div>
        @php $romanos = ['I','II','III','IV','V','VI','VII','VIII','IX','X']; @endphp

        @forelse($receta->medicamentos ?? [] as $i => $med)
            <div class="med-item">
                <div class="med-linea-1">
                    <span class="med-romano">{{ $romanos[$i] ?? $i + 1 }}.</span>
                    <span class="med-negrita">{{ $med['nombre'] ?? '' }}</span>
                    @if (!empty($med['presentacion']))
                        <span style="font-weight:400;color:#555;"> {{ $med['presentacion'] }}</span>
                    @endif
                    @if (!empty($med['dosis']))
                        <span> &nbsp;{{ $med['dosis'] }}</span>
                    @endif
                    @if (!empty($med['cantidad']))
                        <span style="color:#777;"> &nbsp;– Cantidad: {{ $med['cantidad'] }}</span>
                    @endif
                </div>
                @php
                    $partesSig = [];
                    if (!empty($med['frecuencia'])) {
                        $partesSig[] = $med['frecuencia'];
                    }
                    if (!empty($med['duracion'])) {
                        $partesSig[] = 'por ' . $med['duracion'];
                    }
                    $sigTexto = implode(', ', $partesSig);
                @endphp
                @if ($sigTexto)
                    <div class="med-sig"><em>Indicaciones:</em> {{ $sigTexto }}</div>
                @endif
                @if (!empty($med['indicaciones']))
                    <div class="med-nota">{{ $med['indicaciones'] }}</div>
                @endif
            </div>
            @if (!$loop->last)
                <hr class="med-hr">
            @endif
        @empty
            <p style="color:#aaa;font-size:11px;padding:8px 0 8px 18px;font-style:italic;">Sin medicamentos prescritos.
            </p>
        @endforelse

        {{-- ── INDICACIONES GENERALES ── --}}
        @if ($receta->indicaciones_generales)
            <div class="indic-wrap">
                <div class="sec-lbl">Indicaciones Generales</div>
                <div class="indic-texto">{!! nl2br(e($receta->indicaciones_generales)) !!}</div>
            </div>
        @endif

        {{-- ── FIRMA PROFESIONAL ── --}}
        <div class="firma-seccion">
            <div class="firma-izq">
                <div class="firma-canvas-wrap">
                    @if ($receta->firmado && $configuracion?->firma_path)
                        <img src="{{ public_path('storage/' . $configuracion->firma_path) }}" class="firma-img-doc"
                            alt="Firma">
                    @endif
                </div>
                <div class="firma-linea">
                    <div class="firma-nombre">{{ $configuracion?->firma_nombre_doctor ?: $receta->doctor->name }}</div>
                    @if ($configuracion?->firma_cargo)
                        <div class="firma-cargo">{{ $configuracion->firma_cargo }}</div>
                    @endif
                    @if ($configuracion?->firma_registro)
                        <div class="firma-reg">Reg. Prof.: {{ $configuracion->firma_registro }}</div>
                    @elseif($configuracion?->registro_medico)
                        <div class="firma-reg">Reg. Prof.: {{ $configuracion->registro_medico }}</div>
                    @endif
                </div>
            </div>
            <div class="firma-der">
                @if ($receta->firmado)
                    <div class="sello-dig">
                        <div class="sello-dig-titulo">✓ Firmado digitalmente</div>
                        <div class="sello-dig-fecha">
                            {{ $receta->fecha_firma?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</div>
                    </div>
                @endif
            </div>
        </div>

        <x-pdf-pie-profesional :config="$config" />

    </div>
</body>

</html>
