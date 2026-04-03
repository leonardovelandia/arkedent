@extends('layouts.pdf')

@section('pdf-titulo', 'Historia Clínica ' . ($historia->paciente->numero_historia ?? '—'))
@section('pdf-doc-tipo', 'HISTORIA CLÍNICA')
@section('pdf-doc-num', $historia->numero_historia ?? '—')
@section('pdf-footer-der')
    HC {{ $historia->paciente->numero_historia }}
    @if ($historia->numero_historia)
        · Doc. {{ $historia->numero_historia }}
    @endif
@endsection

@section('pdf-contenido')
    @php $C = '#1a3a6b'; @endphp

    {{-- ── BLOQUE PACIENTE ── --}}
    <div class="pac-blk">
        <div class="pac-grid">
            <div class="pac-cell">
                <div class="pac-lbl">Paciente</div>
                <div class="pac-val">{{ $historia->paciente->nombre_completo }}</div>
                <div class="pac-det">{{ $historia->paciente->tipo_documento }} {{ $historia->paciente->numero_documento }}
                </div>
            </div>
            <div class="pac-cell">
                <div class="pac-lbl">Historia Clínica</div>
                <div class="pac-val">{{ $historia->paciente->numero_historia }}</div>
            </div>
            <div class="pac-cell">
                <div class="pac-lbl">Fecha de Nacimiento</div>
                <div class="pac-val">
                    {{ $historia->paciente->fecha_nacimiento ? $historia->paciente->fecha_nacimiento->format('d/m/Y') : '—' }}
                </div>
                <div class="pac-det">Teléfono: {{ $historia->paciente->telefono ?? '—' }}</div>
            </div>
            <div class="pac-cell">
                <div class="pac-lbl">Fecha Apertura</div>
                <div class="pac-val">{{ $historia->fecha_apertura ? $historia->fecha_apertura->format('d/m/Y') : '—' }}
                </div>
            </div>
        </div>
    </div>

    {{-- ── MOTIVO DE CONSULTA ── --}}
    @if ($historia->motivo_consulta)
        <div class="s">
            <div class="s-titulo">Motivo de Consulta</div>
            <div class="f" style="font-size:9.5px;line-height:1.65;">{{ $historia->motivo_consulta }}</div>
            @if ($historia->enfermedad_actual)
                <div class="f" style="margin-top:5px;"><span class="fl">Enfermedad actual:</span>
                    {{ $historia->enfermedad_actual }}</div>
            @endif
        </div>
    @endif

    {{-- ── ANTECEDENTES ── --}}
    @if (
        $historia->antecedentes_medicos ||
            $historia->medicamentos_actuales ||
            $historia->alergias ||
            $historia->antecedentes_odontologicos ||
            $historia->antecedentes_familiares ||
            $historia->habitos)
        <div class="s">
            <div class="s-titulo">Antecedentes</div>
            <div class="grid">
                <div class="col">
                    @if ($historia->antecedentes_medicos)
                        <div class="f"><span class="fl">Antecedentes médicos:</span><br><span
                                style="font-size:9px;">{{ $historia->antecedentes_medicos }}</span></div>
                    @endif
                    @if ($historia->medicamentos_actuales)
                        <div class="f" style="margin-top:4px;"><span class="fl">Medicamentos:</span><br><span
                                style="font-size:9px;">{{ $historia->medicamentos_actuales }}</span></div>
                    @endif
                    @if ($historia->alergias)
                        <div class="f" style="margin-top:4px;"><span class="fl">Alergias:</span><br><span
                                style="font-size:9px;">{{ $historia->alergias }}</span></div>
                    @endif
                </div>
                <div class="col">
                    @if ($historia->antecedentes_odontologicos)
                        <div class="f"><span class="fl">Antecedentes odontológicos:</span><br><span
                                style="font-size:9px;">{{ $historia->antecedentes_odontologicos }}</span></div>
                    @endif
                    @if ($historia->antecedentes_familiares)
                        <div class="f" style="margin-top:4px;"><span class="fl">Antecedentes
                                familiares:</span><br><span
                                style="font-size:9px;">{{ $historia->antecedentes_familiares }}</span></div>
                    @endif
                    @if ($historia->habitos)
                        <div class="f" style="margin-top:4px;"><span class="fl">Hábitos:</span><br><span
                                style="font-size:9px;">{{ $historia->habitos }}</span></div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ── SIGNOS VITALES ── --}}
    @if (
        $historia->presion_arterial ||
            $historia->frecuencia_cardiaca ||
            $historia->temperatura ||
            $historia->peso ||
            $historia->talla)
        <div class="s">
            <div class="s-titulo">Signos Vitales y Antropometría</div>
            <div class="vitals">
                @if ($historia->presion_arterial)
                    <div class="v-cell">
                        <div class="v-lbl">Presión Arterial</div>
                        <div class="v-val">{{ $historia->presion_arterial }}</div>
                        <div class="v-unit">mmHg</div>
                    </div>
                @endif
                @if ($historia->frecuencia_cardiaca)
                    <div class="v-cell">
                        <div class="v-lbl">Frec. Cardíaca</div>
                        <div class="v-val">{{ $historia->frecuencia_cardiaca }}</div>
                        <div class="v-unit">bpm</div>
                    </div>
                @endif
                @if ($historia->temperatura)
                    <div class="v-cell">
                        <div class="v-lbl">Temperatura</div>
                        <div class="v-val">{{ $historia->temperatura }}</div>
                        <div class="v-unit">°C</div>
                    </div>
                @endif
                @if ($historia->peso)
                    <div class="v-cell">
                        <div class="v-lbl">Peso</div>
                        <div class="v-val">{{ $historia->peso }}</div>
                        <div class="v-unit">kg</div>
                    </div>
                @endif
                @if ($historia->talla)
                    <div class="v-cell">
                        <div class="v-lbl">Talla</div>
                        <div class="v-val">{{ $historia->talla }}</div>
                        <div class="v-unit">m</div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ── OBSERVACIONES ── --}}
    @if ($historia->observaciones_generales)
        <div class="s">
            <div class="s-titulo">Observaciones Generales</div>
            <div class="f" style="font-size:9.5px;line-height:1.65;">{{ $historia->observaciones_generales }}</div>
        </div>
    @endif

    {{-- ── ODONTOGRAMA ── --}}
    @if(!empty($historia->odontograma))
    @php
        $odoData    = $historia->odontograma;
        $odoDientes = $odoData['dientes'] ?? $odoData;
        $odoTipo    = $odoData['tipo'] ?? 'adulto';
        $coloresOdo = \App\Helpers\OdontogramaPdf::colores();
        if ($odoTipo === 'infantil') {
            $arcadasHC = ['sup'=>[[55,54,53,52,51],[61,62,63,64,65]],'inf'=>[[85,84,83,82,81],[71,72,73,74,75]]];
        } else {
            $arcadasHC = ['sup'=>[[18,17,16,15,14,13,12,11],[21,22,23,24,25,26,27,28]],'inf'=>[[48,47,46,45,44,43,42,41],[31,32,33,34,35,36,37,38]]];
        }
        $odoPngHC = \App\Helpers\OdontogramaPdf::imagen($odoDientes, $coloresOdo, $arcadasHC);
    @endphp
    <div class="s" style="page-break-inside:avoid;">
        <div class="s-titulo">Odontograma</div>
        @if($odoPngHC)
        <div style="text-align:center;margin-bottom:6px;">
            <img src="{{ $odoPngHC }}" style="max-width:520px;width:100%;height:auto;" alt="Odontograma">
        </div>
        @endif
        {{-- LEYENDA --}}
        <table style="border-collapse:collapse;margin:4px auto 0;font-size:7px;">
            <tr>
                @foreach(['sano'=>'Sano','caries'=>'Caries','restaurado_resina'=>'Resina','restaurado_amalgama'=>'Amalgama','corona'=>'Corona','endodoncia'=>'Endodoncia','extraccion_indicada'=>'Extr. ind.','extraido'=>'Extraído','implante'=>'Implante','fractura'=>'Fractura','sellante'=>'Sellante','ausente'=>'Ausente','temporal'=>'Temporal'] as $est => $lbl)
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
    @if(!empty($historia->hallazgos) && count($historia->hallazgos) > 0)
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
            @foreach($historia->hallazgos as $h)
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
                <td style="color:#6b7280;font-size:8.5px;">{{ $h['observacion'] ?? $h['nota'] ?? '' }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- ── FIRMAS ── --}}
    <div class="firma-wrap">
        <div class="firma-tabla">
            <div class="firma-col first">
                <div class="firma-tit">Firma del Paciente</div>
                @if ($historia->firmado)
                    <img src="{{ $historia->firma_data }}" class="firma-img" alt="Firma del paciente">
                    <div class="firma-linea-img">
                        {{ $historia->paciente->nombre_completo }}<br>
                        {{ $historia->paciente->tipo_documento }}: {{ $historia->paciente->numero_documento }}<br>
                        <span class="badge-ok">✓ Firmado digitalmente</span>
                    </div>
                @else
                    <div class="firma-linea">
                        {{ $historia->paciente->nombre_completo }}<br>
                        {{ $historia->paciente->tipo_documento }}: {{ $historia->paciente->numero_documento }}
                    </div>
                @endif
            </div>
            <div class="firma-col last">
                <div class="firma-tit">Profesional Tratante</div>
                @if ($config->firma_path)
                    <img src="{{ public_path('storage/' . $config->firma_path) }}" class="firma-img" alt="Firma doctor">
                    <div class="firma-linea-img">
                        {{ $config->firma_nombre_doctor ?? auth()->user()->name }}<br>
                        {{ $config->firma_cargo ?? 'Odontólogo(a)' }}<br>
                        @if ($config->firma_registro)
                            Reg. Prof. {{ $config->firma_registro }}
                        @endif
                    </div>
                @else
                    <div class="firma-linea">
                        {{ $config->firma_nombre_doctor ?? auth()->user()->name }}<br>
                        {{ $config->firma_cargo ?? 'Odontólogo(a)' }}<br>
                        @if ($config->firma_registro)
                            Reg. Prof. {{ $config->firma_registro }}
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @if ($historia->firmado)
            <div class="meta"> Documento firmado electrónicamente el
                {{ $historia->fecha_firma->format('d/m/Y \a \l\a\s H:i') }} · IP: {{ $historia->ip_firma }}</div>
        @endif
    </div>



    {{-- ── CONSTANCIA DE FIRMA ELECTRÓNICA ── --}}
    @if ($historia->firmado && $historia->documento_hash)
        @php
            echo \App\Traits\TrazabilidadFirma::generarConstanciaFirmaPDF(
                [
                    'firma_timestamp' => $historia->firma_timestamp,
                    'firma_ip' => $historia->ip_firma,
                    'firma_dispositivo' => $historia->firma_dispositivo,
                    'firma_navegador' => $historia->firma_navegador,
                    'documento_hash' => $historia->documento_hash,
                    'firma_verificacion_token' => $historia->firma_verificacion_token,
                ],
                $historia->paciente->nombre_completo,
                $historia->paciente->tipo_documento,
                $historia->paciente->numero_documento,
                $C,
            );
        @endphp
    @endif

    {{-- ── CORRECCIONES ── --}}
    @if ($historia->correcciones->count() > 0)
        <div class="corr">
            <div style="font-size:9px;font-weight:bold;color:{{ $C }};margin-bottom:6px;">NOTAS DE CORRECCIÓN
                ANEXAS</div>
            <div style="font-size:8px;color:#666;margin-bottom:8px;font-style:italic;">Correcciones agregadas tras la firma
                del documento original.</div>
            @foreach ($historia->correcciones as $i => $correccion)
                <div class="corr-item">
                    <div style="font-weight:bold;font-size:8px;color:{{ $C }};">
                        {{ $correccion->numero_correccion ?? 'Corrección #' . ($i + 1) }} — {{ $correccion->campo_label }}
                        — {{ $correccion->created_at->format('d/m/Y H:i') }} — Por: {{ $correccion->usuario->name }}
                    </div>
                    <div style="font-size:8px;margin-top:2px;color:#999;text-decoration:line-through;">Anterior:
                        {{ $correccion->valor_anterior }}</div>
                    <div style="font-size:8px;margin-top:2px;">Corrección: {{ $correccion->valor_nuevo }}</div>
                    <div style="font-size:7px;margin-top:2px;color:#666;font-style:italic;">Motivo:
                        {{ $correccion->motivo }}</div>
                    @if ($correccion->firmado)
                        <img src="{{ $correccion->firma_data }}" style="max-height:35px;max-width:130px;margin-top:3px;">
                        <div style="font-size:7px;color:#666;">Firmada el
                            {{ $correccion->fecha_firma->format('d/m/Y H:i') }} — IP: {{ $correccion->ip_firma }}</div>
                    @else
                        <div style="font-size:7px;color:#dc2626;font-weight:bold;margin-top:3px;">⚠ PENDIENTE DE FIRMA DEL
                            PACIENTE</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <x-pdf-pie-profesional :config="$config" />

@endsection
