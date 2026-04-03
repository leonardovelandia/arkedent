@extends('layouts.pdf')

@section('pdf-titulo', 'Valoración ' . $valoracion->numero_valoracion)
@section('pdf-doc-tipo', 'VALORACIÓN ODONTOLÓGICA')
@section('pdf-doc-num', $valoracion->numero_valoracion)
@section('pdf-footer-der')
    {{ $valoracion->numero_valoracion }} · {{ $valoracion->fecha->format('d/m/Y') }}
@endsection

@section('pdf-estilos')
    .dx-table { border-collapse: collapse; width: 100%; font-size: 9px; margin-top: 4px; }
    .dx-table th { background: #1a3a6b; color: #fff; font-size: 7.5px; padding: 4px 6px; text-align: left; }
    .dx-table td { font-size: 9px; padding: 4px 6px; border-bottom: 1px solid #f3f4f6; }
    .dx-table tr:nth-child(even) td { background: #f9fafb; }
    .pt-table { border-collapse: collapse; width: 100%; font-size: 9px; margin-top: 4px; }
    .pt-table th { background: #1a3a6b; color: #fff; font-size: 7.5px; padding: 4px 6px; text-align: left; }
    .pt-table td { font-size: 9px; padding: 4px 6px; border-bottom: 1px solid #f3f4f6; }
    .pt-table tr:nth-child(even) td { background: #f9fafb; }
    .pt-table tfoot td { font-size: 9.5px; font-weight: bold; background: #d4edda; color: #166534; }
    .badge-higiene { border-radius: 20px; padding: 1px 8px; font-size: 8px; font-weight: bold; }
    .sin-presupuesto { border: 1px dashed #d1d5db; border-radius: 6px; padding: 8px 12px; color: #9ca3af; font-size: 8.5px;
    font-style: italic; margin-top: 8px; text-align: center; }
    .pre-table { border-collapse: collapse; width: 100%; font-size: 9px; margin-top: 4px; }
    .pre-table th { background: #1a3a6b; color: #fff; font-size: 7.5px; padding: 4px 6px; text-align: left; }
    .pre-table td { font-size: 9px; padding: 4px 6px; border-bottom: 1px solid #f3f4f6; }
    .pre-table tr:nth-child(even) td { background: #f9fafb; }
    .pre-table tfoot td { font-size: 9.5px; font-weight: bold; background: #eff6ff; color: #1a3a6b; }
@endsection

@section('pdf-contenido')
    @php $C = '#1a3a6b'; @endphp

    {{-- ── BLOQUE PACIENTE ── --}}
    <div class="pac-blk">
        <div class="pac-grid">
            <div class="pac-cell">
                <div class="pac-lbl">Paciente</div>
                <div class="pac-val">{{ $valoracion->paciente->nombre_completo }}</div>
                <div class="pac-det">{{ $valoracion->paciente->tipo_documento }}
                    {{ $valoracion->paciente->numero_documento }}</div>
            </div>
            <div class="pac-cell">
                <div class="pac-lbl">Historia Clínica</div>
                <div class="pac-val">{{ $valoracion->historiaClinica?->numero_historia ?? '—' }}</div>
            </div>
            <div class="pac-cell">
                <div class="pac-lbl">Fecha</div>
                <div class="pac-val">{{ $valoracion->fecha->format('d/m/Y') }}</div>
                @if ($valoracion->hora_inicio)
                    <div class="pac-det">{{ $valoracion->hora_inicio->format('H:i') }}@if ($valoracion->hora_fin)
                            – {{ $valoracion->hora_fin->format('H:i') }}
                        @endif
                    </div>
                @endif
            </div>
            <div class="pac-cell">
                <div class="pac-lbl">Profesional</div>
                <div class="pac-val">{{ $valoracion->doctor?->name ?? '—' }}</div>
                <div class="pac-det">{{ $config?->firma_cargo ?? 'Odontólogo(a)' }}</div>
            </div>
        </div>
    </div>

    {{-- ── MOTIVO DE CONSULTA ── --}}
    <div class="s">
        <div class="s-titulo">Motivo de Consulta</div>
        <div class="f" style="font-size:9.5px;line-height:1.65;">{{ $valoracion->motivo_consulta }}</div>
    </div>

    {{-- ── EXAMEN EXTRAORAL ── --}}
    @if (
        $valoracion->extraoral_cara ||
            $valoracion->extraoral_atm ||
            $valoracion->extraoral_ganglios ||
            $valoracion->extraoral_labios ||
            $valoracion->extraoral_observaciones)
        <div class="s">
            <div class="s-titulo">Examen Extraoral</div>
            <div class="grid">
                <div class="col">
                    @if ($valoracion->extraoral_cara)
                        <div class="f"><span class="fl">Cara / Simetría:</span> {{ $valoracion->extraoral_cara }}
                        </div>
                    @endif
                    @if ($valoracion->extraoral_atm)
                        <div class="f" style="margin-top:3px;"><span class="fl">ATM:</span>
                            {{ $valoracion->extraoral_atm }}</div>
                    @endif
                </div>
                <div class="col">
                    @if ($valoracion->extraoral_ganglios)
                        <div class="f"><span class="fl">Ganglios linfáticos:</span>
                            {{ $valoracion->extraoral_ganglios }}</div>
                    @endif
                    @if ($valoracion->extraoral_labios)
                        <div class="f" style="margin-top:3px;"><span class="fl">Labios y comisuras:</span>
                            {{ $valoracion->extraoral_labios }}</div>
                    @endif
                </div>
            </div>
            @if ($valoracion->extraoral_observaciones)
                <div class="f" style="margin-top:5px;"><span class="fl">Observaciones:</span>
                    {{ $valoracion->extraoral_observaciones }}</div>
            @endif
        </div>
    @endif

    {{-- ── EXAMEN INTRAORAL ── --}}
    @if (
        $valoracion->intraoral_encias ||
            $valoracion->intraoral_mucosa ||
            $valoracion->intraoral_lengua ||
            $valoracion->intraoral_paladar ||
            $valoracion->intraoral_higiene ||
            $valoracion->intraoral_observaciones)
        <div class="s">
            <div class="s-titulo">Examen Intraoral</div>
            @if ($valoracion->intraoral_higiene)
                @php
                    $hColors = [
                        'excelente' => ['#d1fae5', '#166534'],
                        'buena' => ['#dbeafe', '#1d4ed8'],
                        'regular' => ['#fef9c3', '#854d0e'],
                        'mala' => ['#fee2e2', '#991b1b'],
                    ];
                    $hc = $hColors[$valoracion->intraoral_higiene] ?? ['#f3f4f6', '#374151'];
                @endphp
                <div class="f" style="margin-bottom:5px;">
                    <span class="fl">Higiene oral:</span>
                    <span class="badge-higiene"
                        style="background:{{ $hc[0] }};color:{{ $hc[1] }};">{{ ucfirst($valoracion->intraoral_higiene) }}</span>
                </div>
            @endif
            <div class="grid">
                <div class="col">
                    @if ($valoracion->intraoral_encias)
                        <div class="f"><span class="fl">Encías y periodonto:</span><br><span
                                style="font-size:9px;">{{ $valoracion->intraoral_encias }}</span></div>
                    @endif
                    @if ($valoracion->intraoral_mucosa)
                        <div class="f" style="margin-top:3px;"><span class="fl">Mucosa oral:</span><br><span
                                style="font-size:9px;">{{ $valoracion->intraoral_mucosa }}</span></div>
                    @endif
                </div>
                <div class="col">
                    @if ($valoracion->intraoral_lengua)
                        <div class="f"><span class="fl">Lengua y piso de boca:</span><br><span
                                style="font-size:9px;">{{ $valoracion->intraoral_lengua }}</span></div>
                    @endif
                    @if ($valoracion->intraoral_paladar)
                        <div class="f" style="margin-top:3px;"><span class="fl">Paladar:</span><br><span
                                style="font-size:9px;">{{ $valoracion->intraoral_paladar }}</span></div>
                    @endif
                </div>
            </div>
            @if ($valoracion->intraoral_observaciones)
                <div class="f" style="margin-top:5px;"><span class="fl">Observaciones:</span>
                    {{ $valoracion->intraoral_observaciones }}</div>
            @endif
        </div>
    @endif

    {{-- ── ODONTOGRAMA ── --}}
    @if (!empty($valoracion->odontograma))
        @php
            $odoData    = $valoracion->odontograma;
            $odoDientes = $odoData['dientes'] ?? $odoData;
            $odoTipo    = $odoData['tipo'] ?? 'adulto';
            $coloresOdo = \App\Helpers\OdontogramaPdf::colores();
            if ($odoTipo === 'infantil') {
                $arcadas = ['sup'=>[[55,54,53,52,51],[61,62,63,64,65]],'inf'=>[[85,84,83,82,81],[71,72,73,74,75]]];
            } else {
                $arcadas = ['sup'=>[[18,17,16,15,14,13,12,11],[21,22,23,24,25,26,27,28]],'inf'=>[[48,47,46,45,44,43,42,41],[31,32,33,34,35,36,37,38]]];
            }
            $odoPng = \App\Helpers\OdontogramaPdf::imagen($odoDientes, $coloresOdo, $arcadas);
        @endphp
        <div class="s" style="page-break-inside:avoid;">
            <div class="s-titulo">Odontograma</div>
            @if($odoPng)
            <div style="text-align:center;margin-bottom:6px;">
                <img src="{{ $odoPng }}" style="max-width:520px;width:100%;height:auto;" alt="Odontograma">
            </div>
            @endif
            {{-- LEYENDA --}}
            <table style="border-collapse:collapse;margin:4px auto 0;font-size:7px;">
                <tr>
                    @foreach (['sano'=>'Sano','caries'=>'Caries','restaurado_resina'=>'Resina','restaurado_amalgama'=>'Amalgama','corona'=>'Corona','endodoncia'=>'Endodoncia','extraccion_indicada'=>'Extr. ind.','extraido'=>'Extraído','implante'=>'Implante','fractura'=>'Fractura','sellante'=>'Sellante','ausente'=>'Ausente','temporal'=>'Temporal'] as $est => $lbl)
                    @php $lc = $coloresOdo[$est]; @endphp
                    <td style="padding:0 4px 0 0;white-space:nowrap;">
                        <span style="display:inline-block;width:9px;height:9px;background:{{ $lc['fill'] }};border:1px solid {{ $lc['stroke'] }};vertical-align:middle;"></span>
                        <span style="color:#374151;vertical-align:middle;">{{ $lbl }}</span>
                    </td>
                    @endforeach
                </tr>
            </table>
        </div>
    @endif

    {{-- ── HALLAZGOS CLÍNICOS ── --}}
    @php $hallazgosVal = is_array($valoracion->hallazgos) ? $valoracion->hallazgos : []; @endphp
    @if(count($hallazgosVal) > 0)
    <div class="s" style="page-break-inside:avoid;">
        <div class="s-titulo">Hallazgos Clínicos</div>
        <table>
            <thead>
                <tr>
                    <th style="width:8%">Pieza</th>
                    <th style="width:8%">Cara</th>
                    <th style="width:12%">ICDAS</th>
                    <th style="width:25%">Diagnóstico CIE-10</th>
                    <th style="width:30%">Procedimiento</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
            @foreach($hallazgosVal as $h)
            <tr>
                <td style="font-weight:bold;color:{{ $C }};text-align:center;">{{ $h['pieza'] ?? '—' }}</td>
                <td style="text-align:center;">{{ $h['cara'] ?? '—' }}</td>
                <td>
                    @if(!empty($h['icdas_codigo']))
                    <span style="background:#f3e8ff;color:#7c3aed;border-radius:3px;padding:0 4px;font-size:8px;font-weight:bold;">ICDAS {{ $h['icdas_codigo'] }}</span>
                    @else —
                    @endif
                </td>
                <td>
                    @if(!empty($h['diagnostico_codigo']))<span style="font-family:monospace;color:{{ $C }};font-size:8px;font-weight:bold;">{{ $h['diagnostico_codigo'] }}</span>@endif
                    @if(!empty($h['diagnostico_nombre']))<span style="font-size:8.5px;"> {{ $h['diagnostico_nombre'] }}</span>@endif
                    @if(empty($h['diagnostico_codigo']) && empty($h['diagnostico_nombre']) && empty($h['icdas_codigo']))—@endif
                </td>
                <td style="font-size:8.5px;">{{ $h['procedimiento'] ?? '—' }}</td>
                <td style="color:#6b7280;font-size:8.5px;">{{ $h['observacion'] ?? ($h['nota'] ?? '') }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- ── PLAN DE TRATAMIENTO ── --}}
    <div class="s">
        <div class="s-titulo">Plan de Tratamiento</div>
        @if (empty($valoracion->plan_tratamiento))
            <div style="color:#9ca3af;font-style:italic;font-size:9px;padding:4px 0;">Sin plan de tratamiento registrado.
            </div>
        @else
            @php $totalPlan = array_sum(array_map(fn($p) => ($p['valor_unitario']??0) * ($p['cantidad']??1), $valoracion->plan_tratamiento)); @endphp
            <table class="pt-table">
                <thead>
                    <tr>
                        <th style="width:4%">#</th>
                        <th style="width:35%">Procedimiento</th>
                        <th style="width:10%">Diente</th>
                        <th style="width:8%">Cara</th>
                        <th style="width:7%;text-align:center;">Cant.</th>
                        <th style="width:15%;text-align:right;">V. Unit.</th>
                        <th style="width:15%;text-align:right;">Total</th>
                        <th style="width:12%">Prioridad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($valoracion->plan_tratamiento as $i => $proc)
                        <tr>
                            <td style="color:#9ca3af;text-align:center;">{{ $i + 1 }}</td>
                            <td style="font-weight:600;">{{ $proc['procedimiento'] ?? '—' }}</td>
                            <td>{{ $proc['diente'] ?? '—' }}</td>
                            <td>{{ $proc['cara'] ?? '—' }}</td>
                            <td style="text-align:center;">{{ $proc['cantidad'] ?? 1 }}</td>
                            <td style="text-align:right;">$ {{ number_format($proc['valor_unitario'] ?? 0, 0, ',', '.') }}
                            </td>
                            <td style="text-align:right;font-weight:bold;color:#166534;">$
                                {{ number_format(($proc['valor_unitario'] ?? 0) * ($proc['cantidad'] ?? 1), 0, ',', '.') }}
                            </td>
                            <td style="font-size:8px;">{{ $proc['prioridad'] ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align:right;padding-right:8px;">TOTAL PLAN DE TRATAMIENTO</td>
                        <td style="text-align:right;">$ {{ number_format($totalPlan, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        @endif

        {{-- Presupuesto asociado --}}
        @if ($valoracion->presupuesto)
            <div style="margin-top:10px;">
                <div class="s-titulo" style="margin-bottom:6px;">Presupuesto Generado —
                    {{ $valoracion->presupuesto->numero_formateado }}</div>
                <table class="pre-table">
                    <thead>
                        <tr>
                            <th style="width:4%">#</th>
                            <th style="width:37%">Procedimiento</th>
                            <th style="width:10%">Diente</th>
                            <th style="width:8%">Cara</th>
                            <th style="width:7%;text-align:center;">Cant.</th>
                            <th style="width:15%;text-align:right;">V. Unit.</th>
                            <th style="text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($valoracion->presupuesto->items as $item)
                            <tr>
                                <td style="color:#9ca3af;text-align:center;">{{ $item->numero_item }}</td>
                                <td>{{ $item->procedimiento }}</td>
                                <td>{{ $item->diente ?? '—' }}</td>
                                <td>{{ $item->cara ?: '—' }}</td>
                                <td style="text-align:center;">{{ $item->cantidad }}</td>
                                <td style="text-align:right;">$ {{ number_format($item->valor_unitario, 0, ',', '.') }}
                                </td>
                                <td style="text-align:right;font-weight:bold;">$
                                    {{ number_format($item->valor_total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @if ($valoracion->presupuesto->descuento_valor > 0)
                            <tr>
                                <td colspan="6" style="text-align:right;">Subtotal:</td>
                                <td style="text-align:right;">$
                                    {{ number_format($valoracion->presupuesto->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" style="text-align:right;">Descuento
                                    ({{ $valoracion->presupuesto->descuento_porcentaje }}%):</td>
                                <td style="text-align:right;color:#dc2626;">- $
                                    {{ number_format($valoracion->presupuesto->descuento_valor, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="6" style="text-align:right;">TOTAL A PAGAR:</td>
                            <td style="text-align:right;font-size:11px;color:{{ $C }};">$
                                {{ number_format($valoracion->presupuesto->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="sin-presupuesto">
                PRESUPUESTO &nbsp;·&nbsp; No se ha generado un presupuesto para esta valoración.
            </div>
        @endif
    </div>

    {{-- ── PRONÓSTICO Y OBSERVACIONES ── --}}
    @if ($valoracion->pronostico || $valoracion->observaciones_generales)
        <div class="s">
            <div class="s-titulo">Pronóstico y Observaciones</div>
            @if ($valoracion->pronostico)
                @php
                    $pcMap = [
                        'excelente' => ['#d1fae5', '#166534', 'Excelente'],
                        'bueno' => ['#dbeafe', '#1d4ed8', 'Bueno'],
                        'reservado' => ['#fef9c3', '#854d0e', 'Reservado'],
                        'malo' => ['#fee2e2', '#991b1b', 'Malo'],
                    ];
                    $pc = $pcMap[$valoracion->pronostico] ?? ['#f3f4f6', '#374151', ucfirst($valoracion->pronostico)];
                @endphp
                <div class="f">
                    <span class="fl">Pronóstico:</span>
                    <span
                        style="background:{{ $pc[0] }};color:{{ $pc[1] }};border-radius:20px;padding:1px 8px;font-size:8px;font-weight:bold;">{{ $pc[2] }}</span>
                </div>
            @endif
            @if ($valoracion->observaciones_generales)
                <div class="f" style="margin-top:5px;"><span class="fl">Observaciones
                        generales:</span><br>{{ $valoracion->observaciones_generales }}</div>
            @endif
        </div>
    @endif

    {{-- ── FIRMA PROFESIONAL ── --}}
    <div class="firma-wrap">
        <div class="firma-tabla">
            <div class="firma-col first">
                <div class="firma-tit">Profesional Tratante</div>
                @if ($config?->firma_path)
                    <img src="{{ public_path('storage/' . $config->firma_path) }}" class="firma-img" alt="Firma">
                    <div class="firma-linea-img">
                        {{ $config?->firma_nombre_doctor ?? $valoracion->doctor?->name }}<br>
                        {{ $config?->firma_cargo ?? 'Odontólogo(a)' }}<br>
                        @if ($config?->firma_registro)
                            Reg. Prof. {{ $config->firma_registro }}
                        @endif
                    </div>
                @else
                    <div class="firma-linea">
                        {{ $config?->firma_nombre_doctor ?? $valoracion->doctor?->name }}<br>
                        {{ $config?->firma_cargo ?? 'Odontólogo(a)' }}<br>
                        @if ($config?->firma_registro)
                            Reg. Prof. {{ $config->firma_registro }}
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>

    <x-pdf-pie-profesional :config="$config" />

@endsection
