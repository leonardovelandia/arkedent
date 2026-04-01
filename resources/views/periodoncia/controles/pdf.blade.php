<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Control Periodontal {{ $control->numero_control }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1a1a1a; background: white; line-height: 1.4; }
        .pagina { padding: 26px 38px 34px; max-width: 210mm; }

        /* Membrete */
        .membrete { border-bottom: 2px solid {{ $colorPDF }}; padding-bottom: 12px; margin-bottom: 16px; }
        .membrete-inner { display: table; width: 100%; }
        .membrete-izq { display: table-cell; vertical-align: middle; width: 65%; }
        .membrete-der { display: table-cell; vertical-align: middle; text-align: right; width: 35%; font-size: 9px; color: {{ $colorPDF }}; line-height: 1.7; }
        .logo-td { display: table-cell; vertical-align: middle; padding-right: 12px; width: 1%; white-space: nowrap; }
        .logo-img { width: 48px; height: 48px; object-fit: contain; }
        .texto-td { display: table-cell; vertical-align: middle; }
        .doctor-nombre { font-size: 15px; font-weight: 700; color: {{ $colorPDF }}; line-height: 1.2; }
        .doctor-titulo { font-size: 10px; color: {{ $colorPDF }}; margin-top: 2px; }
        .consultorio-nombre { font-size: 10.5px; color: {{ $colorPDF }}; margin-top: 2px; }
        .registro { font-size: 9px; color: {{ $colorPDF }}; margin-top: 3px; }

        /* Título */
        .titulo-doc { text-align: center; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 2.5px; color: {{ $colorPDF }}; margin-bottom: 14px; padding-bottom: 5px; border-bottom: 1px solid #ccc; }

        /* Sección */
        .sec-titulo { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: {{ $colorPDF }}; background: #f0f4f8; padding: 4px 8px; border-left: 3px solid {{ $colorPDF }}; margin-bottom: 8px; }
        .dato-label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; color: #888; margin-bottom: 2px; }
        .dato-val { font-size: 10.5px; font-weight: 600; color: #1a1a1a; }

        /* Sondaje */
        .sond-table { border-collapse: collapse; width: 100%; font-size: 8px; }
        .sond-table th, .sond-table td { border: 1px solid #ccc; padding: 2px 2px; text-align: center; }
        .sond-table thead th { background: #f0f4f8; font-weight: 700; color: {{ $colorPDF }}; }
        .sond-lbl { background: #f8f8f8; font-weight: 700; color: #555; text-align: left; padding-left: 4px; }
        .sv1 { background: #dcfce7; color: #166534; font-weight: 700; border-radius: 2px; padding: 0 2px; }
        .sv2 { background: #fef9c3; color: #854d0e; font-weight: 700; border-radius: 2px; padding: 0 2px; }
        .sv3 { background: #ffedd5; color: #9a3412; font-weight: 700; border-radius: 2px; padding: 0 2px; }
        .sv4 { background: #fee2e2; color: #7f1d1d; font-weight: 700; border-radius: 2px; padding: 0 2px; }

        /* Firma */
        .firma-sec { margin-top: 36px; display: table; width: 100%; }
        .firma-izq { display: table-cell; vertical-align: bottom; width: 50%; }
        .firma-der { display: table-cell; vertical-align: bottom; text-align: right; width: 50%; }
        .firma-canvas { height: 52px; }
        .firma-img { max-height: 52px; max-width: 180px; }
        .firma-linea { border-top: 1px solid #1a1a1a; padding-top: 4px; }
        .firma-nombre { font-size: 10.5px; font-weight: 700; }
        .firma-cargo { font-size: 9px; color: #666; margin-top: 1px; }
        .firma-reg { font-size: 8.5px; color: #999; margin-top: 1px; }

        /* Pie */
        .pie { margin-top: 18px; padding-top: 7px; border-top: 1px solid #e0e0e0; display: table; width: 100%; }
        .pie-izq { display: table-cell; font-size: 7.5px; color: #bbb; }
        .pie-der { display: table-cell; text-align: right; font-size: 7.5px; color: #bbb; font-family: monospace; }
    </style>
</head>
<body>
<div class="pagina">

    {{-- Membrete --}}
    <div class="membrete">
        <div class="membrete-inner">
            <div class="membrete-izq">
                <table style="border-collapse:collapse;width:auto;">
                    <tr>
                        @if($config?->logo_path)
                        <td class="logo-td">
                            <img src="{{ public_path('storage/' . $config->logo_path) }}" class="logo-img" alt="Logo">
                        </td>
                        @endif
                        <td class="texto-td">
                            <div class="doctor-nombre">{{ $config?->firma_nombre_doctor ?: ($config?->nombre_consultorio ?? '') }}</div>
                            @if($config?->firma_cargo)<div class="doctor-titulo">{{ $config->firma_cargo }}</div>@endif
                            @if($config?->nombre_consultorio)<div class="consultorio-nombre">{{ $config->nombre_consultorio }}</div>@endif
                            @if($config?->firma_registro || $config?->registro_medico)
                            <div class="registro">Reg. Prof.: {{ $config->firma_registro ?: $config->registro_medico }}</div>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="membrete-der">
                @if($config?->direccion)<div>{{ $config->direccion }}</div>@endif
                @if($config?->telefono)<div>Tel. {{ $config->telefono }}</div>@endif
                @if($config?->email)<div>{{ $config->email }}</div>@endif
                <div style="margin-top:3px;">{{ now()->locale('es')->translatedFormat('d \d\e F \d\e Y') }}</div>
            </div>
        </div>
    </div>

    {{-- Título --}}
    <div class="titulo-doc">Control Periodontal — Sesión #{{ $control->numero_sesion }}</div>

    {{-- Datos del control --}}
    <div style="margin-bottom:12px;">
        <div class="sec-titulo">Información del Control</div>
        <table style="border-collapse:collapse;width:100%;font-size:10px;">
            <tr>
                <td style="padding:4px 8px;border:1px solid #e0e0e0;width:40%;">
                    <div class="dato-label">Paciente</div>
                    <div class="dato-val">{{ $control->fichaPeriodontal->paciente->nombre_completo }}</div>
                </td>
                <td style="padding:4px 8px;border:1px solid #e0e0e0;width:20%;">
                    <div class="dato-label">Documento</div>
                    <div class="dato-val">{{ $control->fichaPeriodontal->paciente->numero_documento }}</div>
                </td>
                <td style="padding:4px 8px;border:1px solid #e0e0e0;width:20%;">
                    <div class="dato-label">N° Control</div>
                    <div class="dato-val" style="font-family:monospace;color:{{ $colorPDF }};">{{ $control->numero_control }}</div>
                </td>
                <td style="padding:4px 8px;border:1px solid #e0e0e0;width:20%;">
                    <div class="dato-label">Ficha</div>
                    <div class="dato-val" style="font-family:monospace;">{{ $control->fichaPeriodontal->numero_ficha }}</div>
                </td>
            </tr>
            <tr>
                <td style="padding:4px 8px;border:1px solid #e0e0e0;">
                    <div class="dato-label">Tipo de sesión</div>
                    <div class="dato-val">{{ $control->tipo_sesion_label }}</div>
                </td>
                <td style="padding:4px 8px;border:1px solid #e0e0e0;">
                    <div class="dato-label">Fecha</div>
                    <div class="dato-val">{{ $control->fecha_control->format('d/m/Y') }}</div>
                </td>
                <td style="padding:4px 8px;border:1px solid #e0e0e0;">
                    <div class="dato-label">Doctor(a)</div>
                    <div class="dato-val">{{ $control->periodoncista?->name ?? '—' }}</div>
                </td>
                <td style="padding:4px 8px;border:1px solid #e0e0e0;">
                    <div class="dato-label">Prox. cita</div>
                    <div class="dato-val">{{ $control->proxima_cita_semanas ? $control->proxima_cita_semanas . ' sem.' : '—' }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Índices --}}
    @if($control->indice_placa_control !== null || $control->indice_gingival_control !== null)
    <div style="margin-bottom:12px;">
        <div class="sec-titulo">Índices de Control</div>
        <table style="border-collapse:collapse;width:100%;font-size:10px;">
            <tr>
                @if($control->indice_placa_control !== null)
                <td style="padding:4px 8px;border:1px solid #e0e0e0;width:25%;">
                    <div class="dato-label">Índice de Placa</div>
                    <div style="font-size:18px;font-weight:800;color:{{ $control->indice_placa_control < 20 ? '#16a34a' : ($control->indice_placa_control < 40 ? '#d97706' : '#dc2626') }};">
                        {{ number_format($control->indice_placa_control, 1) }}%
                    </div>
                </td>
                @endif
                @if($control->indice_gingival_control !== null)
                <td style="padding:4px 8px;border:1px solid #e0e0e0;width:25%;">
                    <div class="dato-label">Índice Gingival</div>
                    <div style="font-size:18px;font-weight:800;color:{{ $control->indice_gingival_control < 1 ? '#16a34a' : ($control->indice_gingival_control < 2 ? '#d97706' : '#dc2626') }};">
                        {{ number_format($control->indice_gingival_control, 2) }}
                    </div>
                </td>
                @endif
                @if($control->anestesia_utilizada)
                <td style="padding:4px 8px;border:1px solid #e0e0e0;">
                    <div class="dato-label">Anestesia</div>
                    <div class="dato-val">{{ $control->anestesia_utilizada }}</div>
                </td>
                @endif
            </tr>
        </table>
    </div>
    @endif

    {{-- Zonas tratadas --}}
    @if($control->zonas_tratadas && count($control->zonas_tratadas))
    <div style="margin-bottom:12px;">
        <div class="sec-titulo">Zonas Tratadas</div>
        <div style="padding:5px 8px;border:1px solid #e0e0e0;font-size:10px;">
            @foreach((array)$control->zonas_tratadas as $zona)
            <span style="background:#f0f4f8;border:1px solid #ccc;border-radius:3px;padding:1px 5px;font-weight:700;font-family:monospace;margin:1px;">{{ $zona }}</span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Procedimiento --}}
    @if($control->instrumentos_utilizados)
    <div style="margin-bottom:12px;">
        <div class="sec-titulo">Procedimiento / Instrumentos</div>
        <div style="padding:5px 8px;border:1px solid #e0e0e0;font-size:10px;line-height:1.5;white-space:pre-wrap;">{{ $control->instrumentos_utilizados }}</div>
    </div>
    @endif

    {{-- Observaciones --}}
    @if($control->observaciones)
    <div style="margin-bottom:12px;">
        <div class="sec-titulo">Observaciones Clínicas</div>
        <div style="padding:5px 8px;border:1px solid #e0e0e0;font-size:10px;line-height:1.5;white-space:pre-wrap;">{{ $control->observaciones }}</div>
    </div>
    @endif

    {{-- Indicaciones --}}
    @if($control->indicaciones_paciente)
    <div style="margin-bottom:12px;">
        <div class="sec-titulo">Indicaciones para el Paciente</div>
        <div style="padding:5px 8px;border:1px solid #e0e0e0;font-size:10px;line-height:1.5;background:#fffbf0;white-space:pre-wrap;">{{ $control->indicaciones_paciente }}</div>
    </div>
    @endif

    {{-- Sondaje de control --}}
    @if($control->sondaje_control && count($control->sondaje_control))
    <div style="margin-bottom:12px;">
        <div class="sec-titulo">Sondaje de Control</div>
        @php
            $sc = $control->sondaje_control;
            $dSup = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
            $dInf = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];
            function svCtrlPdf($v) {
                if (!is_numeric($v) || $v == '') return '<span style="color:#ccc;">—</span>';
                $vv = (int)$v;
                if ($vv <= 3) return '<span class="sv1">'.$v.'</span>';
                if ($vv <= 5) return '<span class="sv2">'.$v.'</span>';
                if ($vv <= 7) return '<span class="sv3">'.$v.'</span>';
                return '<span class="sv4">'.$v.'</span>';
            }
        @endphp
        <p style="font-size:8px;font-weight:700;text-transform:uppercase;color:#888;margin:3px 0 2px;">Superior</p>
        <table class="sond-table" style="margin-bottom:5px;">
            <thead><tr>
                <th style="width:28px;text-align:left;">Cara</th>
                @foreach($dSup as $d)<th>{{ $d }}</th>@endforeach
            </tr></thead>
            <tbody>
            @foreach([['MV','mvc'],['V','vc'],['DV','dvc'],['ML','mlc'],['L','lc'],['DL','dlc']] as $f)
            <tr><td class="sond-lbl">{{ $f[0] }}</td>
                @foreach($dSup as $d)<td>{!! svCtrlPdf($sc[$d][$f[1]] ?? '') !!}</td>@endforeach
            </tr>
            @endforeach
            </tbody>
        </table>
        <p style="font-size:8px;font-weight:700;text-transform:uppercase;color:#888;margin:3px 0 2px;">Inferior</p>
        <table class="sond-table">
            <thead><tr>
                <th style="width:28px;text-align:left;">Cara</th>
                @foreach($dInf as $d)<th>{{ $d }}</th>@endforeach
            </tr></thead>
            <tbody>
            @foreach([['MV','mvc'],['V','vc'],['DV','dvc'],['ML','mlc'],['L','lc'],['DL','dlc']] as $f)
            <tr><td class="sond-lbl">{{ $f[0] }}</td>
                @foreach($dInf as $d)<td>{!! svCtrlPdf($sc[$d][$f[1]] ?? '') !!}</td>@endforeach
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Firma --}}
    <div class="firma-sec">
        <div class="firma-izq">
            <div class="firma-canvas">
                @if($config?->firma_path)
                <img src="{{ public_path('storage/' . $config->firma_path) }}" class="firma-img" alt="Firma">
                @endif
            </div>
            <div class="firma-linea">
                <div class="firma-nombre">{{ $config?->firma_nombre_doctor ?? '' }}</div>
                @if($config?->firma_cargo)<div class="firma-cargo">{{ $config->firma_cargo }}</div>@endif
                @if($config?->firma_registro || $config?->registro_medico)
                <div class="firma-reg">Reg. Prof.: {{ $config->firma_registro ?: $config->registro_medico }}</div>
                @endif
            </div>
        </div>
        <div class="firma-der">
            <div style="border:1px solid #bbb;padding:6px 12px;text-align:center;display:inline-block;">
                <div style="font-size:7px;text-transform:uppercase;letter-spacing:0.5px;color:#666;font-weight:700;">Generado</div>
                <div style="font-size:8px;color:#999;margin-top:1px;">{{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>

    {{-- Pie --}}
    <div class="pie">
        <div class="pie-izq">{{ $config?->nombre_consultorio ?? '' }} — Control Periodontal</div>
        <div class="pie-der">{{ $control->numero_control }}</div>
    </div>

</div>
</body>
</html>
