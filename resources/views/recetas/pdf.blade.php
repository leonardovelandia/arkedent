<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=4">
    <title>Receta {{ $receta->numero_receta }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11.5px;
            color: #1a1a1a;
            background: white;
            line-height: 1.5;
        }

        .pagina {
            padding: 28px 42px 36px;
            max-width: 210mm;
        }

        /* ── MEMBRETE ──────────────────────────────────────── */
        .membrete {
            border-bottom: 2px solid #1E3A5F;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }

        .membrete-inner {
            display: table;
            width: 100%;
        }

        .membrete-izq {
            display: table-cell;
            vertical-align: middle;
            width: 68%;
        }

        .membrete-der {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 32%;
            font-size: 9.5px;
            color: #1E3A5F;
            line-height: 1.7;
        }

        .logo-td {
            display: table-cell;
            vertical-align: middle;
            padding-right: 14px;
            width: 1%;
            white-space: nowrap;
        }

        .logo-img {
            width: 52px;
            height: 52px;
            object-fit: contain;
        }

        .texto-td {
            display: table-cell;
            vertical-align: middle;
        }

        .doctor-nombre {
            font-size: 16px;
            font-weight: 700;
            color: #1E3A5F;
            letter-spacing: -0.2px;
            line-height: 1.2;
        }

        .doctor-titulo {
            font-size: 10.5px;
            color: #1E3A5F;
            margin-top: 2px;
        }

        .consultorio-nombre {
            font-size: 11px;
            color: #1E3A5F;
            margin-top: 3px;
        }

        .registro {
            font-size: 9.5px;
            color: #1E3A5F;
            margin-top: 4px;
            letter-spacing: 0.2px;
        }

        /* ── TÍTULO CENTRAL ────────────────────────────────── */
        .titulo-receta {
            text-align: center;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: #1E3A5F;
            margin-bottom: 18px;
            padding-bottom: 6px;
            border-bottom: 1px solid #ccc;
        }

        /* ── DATOS PACIENTE ────────────────────────────────── */
        .datos-paciente {
            display: table;
            width: 100%;
            margin-bottom: 16px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 12px;
        }

        .datos-col {
            display: table-cell;
            vertical-align: top;
        }

        .datos-col-der {
            display: table-cell;
            vertical-align: top;
            text-align: right;
            white-space: nowrap;
            padding-left: 20px;
            width: 1%;
        }

        .dato-label {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #999;
            margin-bottom: 2px;
        }

        .dato-valor {
            font-size: 12px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .dato-sub {
            font-size: 9.5px;
            color: #666;
            margin-top: 1px;
        }

        .receta-num-label {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #999;
            margin-bottom: 2px;
        }

        .receta-num-val {
            font-size: 11px;
            font-weight: 700;
            color: #1a1a1a;
            font-family: monospace;
        }

        /* ── DIAGNÓSTICO ───────────────────────────────────── */
        .diagnostico {
            margin-bottom: 18px;
        }

        .diagnostico-label {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #999;
            margin-bottom: 3px;
        }

        .diagnostico-texto {
            font-size: 11px;
            color: #1a1a1a;
            font-style: italic;
        }

        /* ── MEDICAMENTOS ──────────────────────────────────── */
        .rp-titulo {
            font-size: 15px;
            font-style: italic;
            font-weight: 700;
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
            font-weight: 700;
        }

        .med-sig {
            font-size: 10.5px;
            color: #333;
            padding-left: 14px;
            line-height: 1.5;
            margin-top: 1px;
        }

        .med-sig em {
            color: #777;
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

        /* ── INDICACIONES GENERALES ────────────────────────── */
        .indic-wrap {
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px solid #ddd;
        }

        .indic-label {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #999;
            margin-bottom: 5px;
        }

        .indic-texto {
            font-size: 10.5px;
            color: #333;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        /* ── FIRMA ─────────────────────────────────────────── */
        .firma-seccion {
            margin-top: 44px;
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

        .firma-img {
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
            font-weight: 700;
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
            border: 1px solid #bbb;
            padding: 7px 14px;
            text-align: center;
        }

        .sello-dig-titulo {
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
            font-weight: 700;
        }

        .sello-dig-fecha {
            font-size: 8px;
            color: #999;
            margin-top: 2px;
        }

        /* ── PIE ───────────────────────────────────────────── */
        .pie {
            margin-top: 22px;
            padding-top: 8px;
            border-top: 1px solid #e0e0e0;
            display: table;
            width: 100%;
        }

        .pie-izq {
            display: table-cell;
            font-size: 8px;
            color: #bbb;
        }

        .pie-der {
            display: table-cell;
            text-align: right;
            font-size: 8px;
            color: #bbb;
            font-family: monospace;
        }
    </style>
</head>

<body>
    <div class="pagina">

        {{-- ── MEMBRETE ── --}}
        <div class="membrete">
            <div class="membrete-inner">
                <div class="membrete-izq">
                    <table style="border-collapse:collapse;width:auto;">
                        <tr>
                            @if ($configuracion?->logo_path)
                                <td class="logo-td">
                                    <img src="{{ public_path('storage/' . $configuracion->logo_path) }}"
                                        class="logo-img" alt="Logo">
                                </td>
                            @endif
                            <td class="texto-td">
                                <div class="doctor-nombre">{{ $configuracion?->firma_nombre_doctor ?: $receta->doctor->name }}</div>
                                @if($configuracion?->firma_cargo)
                                <div class="doctor-titulo">{{ $configuracion->firma_cargo }}</div>
                                @endif
                                @if ($configuracion?->nombre_consultorio)
                                    <div class="consultorio-nombre">{{ $configuracion->nombre_consultorio }}</div>
                                @endif
                                @if($configuracion?->firma_registro)
                                    <div class="registro">Reg. Prof.: {{ $configuracion->firma_registro }}</div>
                                @elseif($configuracion?->registro_medico)
                                    <div class="registro">Reg. Prof.: {{ $configuracion->registro_medico }}</div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="membrete-der">
                    @if ($configuracion?->direccion)
                        <div>{{ $configuracion->direccion }}</div>
                    @endif
                    @if ($configuracion?->telefono)
                        <div>Tel. {{ $configuracion->telefono }}</div>
                    @endif
                    @if ($configuracion?->email)
                        <div>{{ $configuracion->email }}</div>
                    @endif
                    <div style="margin-top:4px;color:#1E3A5F;">
                        {{ $receta->fecha->locale('es')->translatedFormat('d \d\e F \d\e Y') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TÍTULO ── --}}
        <div class="titulo-receta">Receta Médica Odontológica</div>

        {{-- ── DATOS PACIENTE ── --}}
        <div class="datos-paciente">
            <div class="datos-col">
                <div class="dato-label">Paciente</div>
                <div class="dato-valor">{{ $receta->paciente->nombre_completo }}</div>
                <div class="dato-sub">
                    {{ $receta->paciente->tipo_documento }}: {{ $receta->paciente->numero_documento }}
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    Edad: {{ $receta->paciente->edad }} años
                </div>
            </div>
            <div class="datos-col-der">
                <div class="receta-num-label">N° Receta</div>
                <div class="receta-num-val">{{ $receta->numero_receta }}</div>
            </div>
        </div>

        {{-- ── DIAGNÓSTICO ── --}}
        @if ($receta->diagnostico)
            <div class="diagnostico">
                <div class="diagnostico-label">Diagnóstico</div>
                <div class="diagnostico-texto">{{ $receta->diagnostico }}</div>
            </div>
        @endif

        {{-- ── MEDICAMENTOS ── --}}
        <div class="rp-titulo">Rp.</div>

        @php $romanos = ['I','II','III','IV','V','VI','VII','VIII','IX','X']; @endphp

        @forelse($receta->medicamentos ?? [] as $i => $med)
            <div class="med-item">

                {{-- Nombre principal --}}
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

                {{-- Posología --}}
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

                {{-- Indicaciones específicas --}}
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
                <div class="indic-label">Indicaciones generales</div>
                <div class="indic-texto">{{ $receta->indicaciones_generales }}</div>
            </div>
        @endif

        {{-- ── FIRMA ── --}}
        <div class="firma-seccion">
            <div class="firma-izq">
                <div class="firma-canvas-wrap">
                    @if ($configuracion?->firma_path)
                        <img src="{{ public_path('storage/' . $configuracion->firma_path) }}" class="firma-img" alt="Firma">
                    @endif
                </div>
                <div class="firma-linea">
                    <div class="firma-nombre">{{ $configuracion?->firma_nombre_doctor ?: $receta->doctor->name }}</div>
                    @if($configuracion?->firma_cargo)
                    <div class="firma-cargo">{{ $configuracion->firma_cargo }}</div>
                    @endif
                    @if($configuracion?->firma_registro)
                    <div class="firma-reg">Reg. Prof.: {{ $configuracion->firma_registro }}</div>
                    @elseif($configuracion?->registro_medico)
                    <div class="firma-reg">Reg. Prof.: {{ $configuracion->registro_medico }}</div>
                    @endif
                </div>
            </div>
            <div class="firma-der">
                <div class="sello-dig">
                    <div class="sello-dig-titulo">Firmado digitalmente</div>
                    <div class="sello-dig-fecha">{{ now()->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>

        {{-- ── PIE ── --}}
        <div class="pie">
            <div class="pie-izq">Generado: {{ now()->format('d/m/Y H:i') }}</div>
            <div class="pie-der">{{ $receta->numero_receta }}</div>
        </div>

    </div>
</body>

</html>
