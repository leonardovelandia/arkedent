@extends('layouts.app')
@section('titulo', 'Nueva Ficha Periodontal')

@push('estilos')
    <style>
        .periodo-card {
            margin-bottom: 1.25rem;
        }

        .periodo-card-header {
            background: var(--color-principal);
            color: white;
            padding: .75rem 1rem;
            border-radius: 10px 10px 0 0;
            display: flex;
            align-items: center;
            gap: .5rem;
            font-weight: 700;
            font-size: .88rem;
        }

        .periodo-card-body {
            padding: 1.1rem;
        }

        .form-label-per {
            font-size: .77rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--texto-secundario);
            margin-bottom: .3rem;
            display: block;
        }

        .form-ctrl-per {
            width: 100%;
            border: 1px solid var(--fondo-borde);
            border-radius: 8px;
            padding: .42rem .75rem;
            font-size: .875rem;
            background: var(--fondo-app);
            color: var(--texto-principal);
            transition: border-color .15s;
        }

        .form-ctrl-per:focus {
            outline: none;
            border-color: var(--color-principal);
        }

        /* Odontograma indices */
        .diente-oleary {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            margin: 0 2px;
        }

        .diente-num-oc {
            font-size: .62rem;
            font-weight: 700;
            color: var(--texto-secundario);
            margin-bottom: 2px;
            line-height: 1;
        }

        /* SVG triangular O'Leary */
        .cuadro-svg {
            display: block;
            overflow: visible;
        }

        .cara-svg {
            fill: transparent;
            cursor: pointer;
            transition: fill .1s;
        }

        .cara-svg:hover {
            fill: rgba(220, 38, 38, 0.18);
        }

        .cara-svg.cara-activa {
            fill: #dc2626;
        }

        .diente-oleary.ausente-oc .cuadro-svg {
            opacity: .3;
            pointer-events: none;
        }

        .btn-ausente-oc {
            font-size: .52rem;
            font-weight: 700;
            line-height: 1;
            padding: 1px 3px;
            border: 1px solid var(--fondo-borde);
            border-radius: 3px;
            cursor: pointer;
            background: var(--fondo-app);
            color: var(--texto-secundario);
            transition: background .1s, color .1s;
        }

        .diente-oleary.ausente-oc .btn-ausente-oc {
            background: #7f1d1d;
            color: #fca5a5;
            border-color: #7f1d1d;
        }

        /* Silness & Löe Modificado */
        .sl-tabla {
            border-collapse: collapse;
            font-size: .72rem;
            min-width: 100%;
        }

        .sl-tabla th,
        .sl-tabla td {
            border: 1px solid var(--fondo-borde);
            text-align: center;
            vertical-align: middle;
        }

        .sl-diente-header {
            width: 36px;
            min-width: 36px;
            padding: 0;
            background: var(--fondo-card-alt);
        }

        .sl-diente-rotated {
            writing-mode: vertical-lr;
            transform: rotate(180deg);
            font-size: .6rem;
            font-weight: 700;
            display: block;
            padding: .3rem .1rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--texto-secundario);
        }

        .sl-grupo-header {
            font-size: .65rem;
            font-weight: 700;
            padding: .4rem .25rem;
            background: var(--fondo-card-alt);
            color: var(--texto-principal);
            border-bottom: 2px solid var(--color-principal);
            line-height: 1.3;
        }

        .sl-sup-header {
            font-size: .65rem;
            font-weight: 700;
            padding: .25rem .2rem;
            background: var(--fondo-card-alt);
            color: var(--texto-secundario);
            min-width: 26px;
        }

        .sl-codigo-label {
            font-size: .65rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--texto-secundario);
            padding: .35rem .4rem;
            white-space: nowrap;
            background: var(--fondo-card-alt);
        }

        .sl-cell {
            min-width: 26px;
            width: 28px;
            height: 26px;
            cursor: pointer;
            background: var(--fondo-app);
            padding: 0;
            position: relative;
            transition: background .1s;
        }

        .sl-cell:hover:not(.ausente-sl) {
            background: var(--color-muy-claro);
        }

        .sl-cell.activo-sl {
            background: #2a0a0a;
        }

        .sl-cell.activo-sl::before,
        .sl-cell.activo-sl::after {
            content: '';
            position: absolute;
            width: 80%;
            height: 1.5px;
            background: #f87171;
            top: 50%;
            left: 50%;
            transform-origin: center;
        }

        .sl-cell.activo-sl::before {
            transform: translate(-50%, -50%) rotate(45deg);
        }

        .sl-cell.activo-sl::after {
            transform: translate(-50%, -50%) rotate(-45deg);
        }

        .sl-cell.ausente-sl {
            background: var(--fondo-borde);
            cursor: not-allowed;
            opacity: .45;
            pointer-events: none;
        }

        .sl-grupo-sep,
        .sl-sup-sep {
            border-left: 2.5px solid #6B21A8 !important;
        }

        /* ═══ Periodontograma profesional ═══ */
        /* Quitar flechas spinners de inputs numéricos */
        .pmg::-webkit-inner-spin-button,
        .pmg::-webkit-outer-spin-button,
        .pps::-webkit-inner-spin-button,
        .pps::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .pmg,
        .pps {
            -moz-appearance: textfield;
        }

        .perio-wrap {
            width: 100%;
            text-align: center;
        }

        .perio-tbl {
            table-layout: fixed;
            margin: 0 auto;
            border-collapse: collapse;
        }


        .perio-tbl td {
            width: 19.33px;
            min-width: 19.33px;
            max-width: 19.33px;
            padding: 2px 1px;
            border: 1px solid #9ca3af;

        }

        .perio-tbl th {
            width: 19.33px;
            min-width: 19.33px;
            max-width: 19.33px;
            border: 1px solid #9ca3af;
        }


        .perio-tbl td:first-child {
            min-width: 140px;
            width: 140px;
            text-align: left;
            padding-left: 8px;
        }


        .perio-tbl td[colspan="3"] {
            width: 58px !important;
        }


        .perio-tbl tr td:last-child {
            border-right: 2px solid #6b7280;
        }

        .pl {
            min-width: 110px;
            width: 110px;
            padding: 1px 5px;
            text-align: right;
            font-size: .58rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--texto-secundario);
            background: var(--fondo-card-alt);
            border-right: 2px solid var(--fondo-borde) !important;
            white-space: nowrap;
        }

        .pt {
            font-weight: 800;
            font-size: .7rem;
            color: var(--color-principal);
            background: var(--fondo-card-alt);
            padding: 2px 0;
            min-width: 54px;
        }

        .pt.qs,
        td.qs {
            border-left: 2px solid var(--color-principal) !important;
        }

        .pmg {
            width: 17px;
            border: none;
            background: transparent;
            color: #2563eb;
            font-size: .68rem;
            font-weight: 700;
            text-align: center;
            outline: none;
            padding: 0;
            display: block;
            margin: 0 auto;
        }

        .pps {
            width: 17px;
            border: none;
            background: transparent;
            color: #d97706;
            font-size: .68rem;
            font-weight: 700;
            text-align: center;
            outline: none;
            padding: 0;
            display: block;
            margin: 0 auto;
        }

        .pnci {
            display: block;
            width: 17px;
            font-size: .65rem;
            font-weight: 700;
            color: var(--texto-secundario);
            text-align: center;
            margin: 0 auto;
            min-width: 17px;
        }

        .psq {
            display: inline-block;
            width: 13px;
            height: 13px;
            border: 1px solid var(--fondo-borde);
            cursor: pointer;
            background: transparent;
            border-radius: 1px;
            vertical-align: middle;
            transition: background .1s;
        }

        .psq.on-s {
            background: #dc2626;
            border-color: #dc2626;
        }

        .psq.on-p {
            background: #3b82f6;
            border-color: #2563eb;
        }

        .psel {
            border: 1px solid var(--fondo-borde);
            border-radius: 3px;
            font-size: .58rem;
            background: var(--fondo-app);
            color: var(--texto-principal);
            padding: 0 1px;
            width: 50px;
            height: 17px;
        }

        .paus-cb {
            width: 11px;
            height: 11px;
            accent-color: #7f1d1d;
            cursor: pointer;
        }

        .pimpl-cb {
            width: 11px;
            height: 11px;
            accent-color: #374151;
            cursor: pointer;
        }

        td.td-aus {
            opacity: .2 !important;
            pointer-events: none;
            background: var(--fondo-borde) !important;
        }

        canvas.pcv {
            display: block;
        }

        .perio-section-label {
            font-size: .62rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--texto-secundario);
            padding: .35rem 0 .1rem;
            letter-spacing: .04em;
        }

        .perio-stats-bar {
            display: flex;
            gap: 1.25rem;
            align-items: center;
            flex-wrap: wrap;
            padding: .3rem .75rem;
            background: var(--fondo-card-alt);
            border: 1px solid var(--fondo-borde);
            border-radius: 6px;
            font-size: .78rem;
            margin-bottom: .5rem;
        }

        .perio-mid-bar {
            text-align: center;
            font-size: .72rem;
            color: var(--texto-secundario);
            padding: .3rem;
            border-top: 1px dashed var(--fondo-borde);
            border-bottom: 1px dashed var(--fondo-borde);
            margin: .3rem 0;
        }
    </style>
@endpush

@section('contenido')

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1.25rem;font-size:.82rem;flex-wrap:wrap;">
        <a href="{{ route('periodoncia.index') }}" style="color:var(--texto-secundario);text-decoration:none;">
            <i class="bi bi-heart-pulse me-1"></i>Periodoncia
        </a>
        <i class="bi bi-chevron-right" style="font-size:.65rem;color:var(--texto-secundario);"></i>
        <span style="color:var(--texto-principal);font-weight:600;">Nueva Ficha</span>
    </div>

    @if ($errors->any())
        <div
            style="background:#fee2e2;border:1px solid #fca5a5;color:#7f1d1d;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.84rem;">
            <i class="bi bi-exclamation-triangle me-1"></i>
            @foreach ($errors->all() as $e)
                {{ $e }}<br>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('periodoncia.store') }}" id="formPeriodoncia">
        @csrf

        {{-- CARD 1: Datos generales --}}
        <div class="card-sistema periodo-card">
            <div class="periodo-card-header">
                <i class="bi bi-person-vcard"></i> Datos Generales
            </div>
            <div class="periodo-card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label-per">Paciente <span style="color:#dc2626;">*</span></label>
                        <x-buscador-paciente :pacientes="$pacientes" :valorInicial="$pacienteSeleccionado?->id" placeholder="Buscar paciente..." />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-per">Doctor / Periodoncista <span style="color:#dc2626;">*</span></label>
                        <select name="user_id" class="form-ctrl-per" required>
                            <option value="">Seleccionar...</option>
                            @foreach ($doctores as $doc)
                                <option value="{{ $doc->id }}"
                                    {{ old('user_id', auth()->id()) == $doc->id ? 'selected' : '' }}>
                                    {{ $doc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-per">Fecha inicio <span style="color:#dc2626;">*</span></label>
                        <input type="date" name="fecha_inicio" class="form-ctrl-per"
                            value="{{ old('fecha_inicio', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-per">Estado</label>
                        <select name="estado" class="form-ctrl-per">
                            <option value="activa" {{ old('estado', 'activa') == 'activa' ? 'selected' : '' }}>Activa
                            </option>
                            <option value="en_tratamiento" {{ old('estado') == 'en_tratamiento' ? 'selected' : '' }}>En
                                tratamiento</option>
                            <option value="mantenimiento" {{ old('estado') == 'mantenimiento' ? 'selected' : '' }}>
                                Mantenimiento
                            </option>
                            <option value="finalizada" {{ old('estado') == 'finalizada' ? 'selected' : '' }}>Finalizada
                            </option>
                            <option value="abandonada" {{ old('estado') == 'abandonada' ? 'selected' : '' }}>Abandonada
                            </option>
                        </select>
                    </div>
                </div>

                {{-- Info paciente --}}
                <div id="info-paciente"
                    style="display:none;margin-top:1rem;background:var(--fondo-card-alt);border-radius:8px;padding:.75rem 1rem;border:1px solid var(--fondo-borde);">
                    <div class="row g-2" style="font-size:.82rem;">
                        <div class="col-md-3">
                            <span
                                style="color:var(--texto-secundario);font-size:.72rem;font-weight:700;text-transform:uppercase;">Edad</span><br>
                            <span id="pac-edad" style="font-weight:600;color:var(--texto-principal);">—</span>
                        </div>
                        <div class="col-md-3">
                            <span
                                style="color:var(--texto-secundario);font-size:.72rem;font-weight:700;text-transform:uppercase;">N°
                                Historia</span><br>
                            <span id="pac-historia" style="font-weight:600;color:var(--texto-principal);">—</span>
                        </div>
                        <div class="col-md-3">
                            <span
                                style="color:var(--texto-secundario);font-size:.72rem;font-weight:700;text-transform:uppercase;">Documento</span><br>
                            <span id="pac-doc" style="font-weight:600;color:var(--texto-principal);">—</span>
                        </div>
                        <div class="col-md-3">
                            <span
                                style="color:var(--texto-secundario);font-size:.72rem;font-weight:700;text-transform:uppercase;">Alergias</span><br>
                            <span id="pac-alergias" style="font-weight:600;color:#dc2626;">—</span>
                        </div>
                    </div>
                </div>
                @if ($pacienteSeleccionado)
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            document.getElementById('info-paciente').style.display = 'block';
                            document.getElementById('pac-edad').textContent = '{{ $pacienteSeleccionado->edad }} años';
                            document.getElementById('pac-historia').textContent = '{{ $pacienteSeleccionado->numero_historia }}';
                            document.getElementById('pac-doc').textContent =
                                '{{ $pacienteSeleccionado->tipo_documento }}: {{ $pacienteSeleccionado->numero_documento }}';
                            document.getElementById('pac-alergias').textContent =
                                '{{ $historiaClinica?->alergias ?: 'Ninguna conocida' }}';
                        });
                    </script>
                @endif
            </div>
        </div>

        {{-- CARD 2: Índice de Placa O'Leary --}}
        <div class="card-sistema periodo-card">
            <div class="periodo-card-header">
                <i class="bi bi-grid-3x3"></i> Índice de Placa O'Leary
                <span style="margin-left:auto;font-size:.82rem;font-weight:400;opacity:.85;">Haga clic en las superficies
                    con placa</span>
            </div>
            <div class="periodo-card-body">
                <input type="hidden" name="indice_placa_datos" id="indice_placa_datos_json">
                <input type="hidden" name="fecha_indice_placa" id="fecha_indice_placa_val" value="{{ date('Y-m-d') }}">

                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:.75rem;flex-wrap:wrap;">
                    <div style="font-size:.85rem;">
                        Placa presente: <strong id="placa-count" style="color:#dc2626;">0</strong>
                        / Total: <strong id="placa-total">0</strong>
                        &nbsp;→&nbsp;
                        <span id="placa-pct-badge" style="font-weight:800;font-size:.95rem;color:#16a34a;">0%</span>
                        <input type="hidden" name="indice_placa_porcentaje" id="indice_placa_porcentaje_val">
                    </div>
                    <div style="display:flex;gap:.75rem;font-size:.72rem;flex-wrap:wrap;">
                        <span style="display:flex;align-items:center;gap:.25rem;"><span
                                style="width:12px;height:12px;background:#16a34a;border-radius:2px;display:inline-block;"></span>
                            &lt;15% Bajo riesgo</span>
                        <span style="display:flex;align-items:center;gap:.25rem;"><span
                                style="width:12px;height:12px;background:#d97706;border-radius:2px;display:inline-block;"></span>
                            16-30% Mediano riesgo</span>
                        <span style="display:flex;align-items:center;gap:.25rem;"><span
                                style="width:12px;height:12px;background:#dc2626;border-radius:2px;display:inline-block;"></span>
                            31-100% Alto riesgo</span>
                    </div>
                </div>

                {{-- Arcada superior --}}
                <p
                    style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);margin-bottom:.4rem;">
                    Arcada superior — V=Vestibular · D=Distal · L=Lingual/Palatino · M=Mesial</p>
                <div id="oleary-sup"
                    style="display:flex;flex-wrap:nowrap;gap:2px;margin-bottom:1rem;justify-content:center;overflow-x:auto;padding-bottom:2px;">
                    @php $dSup = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28]; @endphp
                    @foreach ($dSup as $d)
                        <div class="diente-oleary" data-diente="{{ $d }}">
                            <div class="diente-num-oc">{{ $d }}</div>
                            <svg class="cuadro-svg" width="30" height="30" viewBox="0 0 30 30"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect width="30" height="30" fill="var(--fondo-app)" rx="1" />
                                <polygon class="cara-svg" data-cara="v" points="0,0 30,0 15,15" title="Vestibular (V)"
                                    onclick="toggleCaraSVG(this)" />
                                <polygon class="cara-svg" data-cara="d" points="30,0 30,30 15,15" title="Distal (D)"
                                    onclick="toggleCaraSVG(this)" />
                                <polygon class="cara-svg" data-cara="l" points="30,30 0,30 15,15"
                                    title="Lingual/Palatino (L)" onclick="toggleCaraSVG(this)" />
                                <polygon class="cara-svg" data-cara="m" points="0,30 0,0 15,15" title="Mesial (M)"
                                    onclick="toggleCaraSVG(this)" />
                                <line x1="0" y1="0" x2="30" y2="30" stroke="#555"
                                    stroke-width="0.8" pointer-events="none" />
                                <line x1="30" y1="0" x2="0" y2="30" stroke="#555"
                                    stroke-width="0.8" pointer-events="none" />
                                <rect width="30" height="30" fill="none" stroke="#666" stroke-width="1"
                                    rx="1" pointer-events="none" />
                            </svg>
                            <button type="button" class="btn-ausente-oc" title="Marcar diente ausente"
                                onclick="toggleAusenteOc(this.closest('.diente-oleary'))">A</button>
                        </div>
                    @endforeach
                </div>

                {{-- Arcada inferior --}}
                <p
                    style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);margin-bottom:.4rem;">
                    Arcada inferior</p>
                <div id="oleary-inf"
                    style="display:flex;flex-wrap:nowrap;gap:2px;justify-content:center;overflow-x:auto;padding-bottom:2px;">
                    @php $dInf = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38]; @endphp
                    @foreach ($dInf as $d)
                        <div class="diente-oleary" data-diente="{{ $d }}">
                            <button type="button" class="btn-ausente-oc" title="Marcar diente ausente"
                                onclick="toggleAusenteOc(this.closest('.diente-oleary'))">A</button>
                            <svg class="cuadro-svg" width="30" height="30" viewBox="0 0 30 30"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect width="30" height="30" fill="var(--fondo-app)" rx="1" />
                                <polygon class="cara-svg" data-cara="v" points="0,0 30,0 15,15" title="Vestibular (V)"
                                    onclick="toggleCaraSVG(this)" />
                                <polygon class="cara-svg" data-cara="d" points="30,0 30,30 15,15" title="Distal (D)"
                                    onclick="toggleCaraSVG(this)" />
                                <polygon class="cara-svg" data-cara="l" points="30,30 0,30 15,15" title="Lingual (L)"
                                    onclick="toggleCaraSVG(this)" />
                                <polygon class="cara-svg" data-cara="m" points="0,30 0,0 15,15" title="Mesial (M)"
                                    onclick="toggleCaraSVG(this)" />
                                <line x1="0" y1="0" x2="30" y2="30" stroke="#555"
                                    stroke-width="0.8" pointer-events="none" />
                                <line x1="30" y1="0" x2="0" y2="30" stroke="#555"
                                    stroke-width="0.8" pointer-events="none" />
                                <rect width="30" height="30" fill="none" stroke="#666" stroke-width="1"
                                    rx="1" pointer-events="none" />
                            </svg>
                            <div class="diente-num-oc">{{ $d }}</div>
                        </div>
                    @endforeach
                </div>

                <div style="margin-top:.75rem;">
                    <label class="form-label-per" style="display:inline;">Fecha índice placa</label>
                    <input type="date" id="fecha_indice_placa_input" class="form-ctrl-per"
                        style="width:auto;display:inline-block;margin-left:.5rem;" value="{{ date('Y-m-d') }}"
                        oninput="document.getElementById('fecha_indice_placa_val').value=this.value">
                </div>
            </div>
        </div>

        {{-- CARD 3: Índice de Placa Silness & Löe Modificado --}}
        <div class="card-sistema periodo-card">
            <div class="periodo-card-header">
                <i class="bi bi-grid-3x2"></i> Índice de Placa Silness &amp; Löe Modificado
                <span style="margin-left:auto;font-size:.82rem;font-weight:400;opacity:.85;">Clic en cada superficie con
                    placa — marque "Ausente" si el diente no está presente</span>
            </div>
            <div class="periodo-card-body">
                <input type="hidden" name="indice_gingival_datos" id="indice_gingival_datos_json">
                <input type="hidden" name="fecha_indice_gingival" id="fecha_indice_gingival_val"
                    value="{{ date('Y-m-d') }}">
                <input type="hidden" name="indice_gingival_porcentaje" id="indice_gingival_porcentaje_val">

                @php
                    $slGrupos = [
                        'molar1q' => ['label' => 'Último molar<br>1er cuadrante', 'sups' => ['D', 'V', 'O', 'P', 'M']],
                        'd11' => ['label' => '11 / 51', 'sups' => ['D', 'V', 'P', 'M']],
                        'd23' => ['label' => '23 / 63', 'sups' => ['M', 'V', 'P', 'D']],
                        'molar2q' => ['label' => 'Último molar<br>2° cuadrante', 'sups' => ['M', 'V', 'O', 'P', 'D']],
                        'molar3q' => ['label' => 'Último molar<br>3er cuadrante', 'sups' => ['D', 'V', 'O', 'L', 'M']],
                        'd44' => ['label' => '44 / 84', 'sups' => ['M', 'V', 'O', 'L', 'D']],
                        'molar4q' => ['label' => 'Último molar<br>4° cuadrante', 'sups' => ['M', 'V', 'O', 'L', 'D']],
                    ];
                @endphp

                <div style="overflow-x:auto;">
                    <table class="sl-tabla">
                        <thead>
                            <tr>
                                <th rowspan="2" class="sl-diente-header">
                                    <span class="sl-diente-rotated">Diente</span>
                                </th>
                                @foreach ($slGrupos as $gKey => $gData)
                                    <th colspan="{{ count($gData['sups']) }}" class="sl-grupo-header">
                                        {!! $gData['label'] !!}
                                        <div style="font-size:.6rem;font-weight:400;margin-top:.3rem;">
                                            <label
                                                style="cursor:pointer;display:inline-flex;align-items:center;gap:.25rem;">
                                                <input type="checkbox" class="sl-ausente-cb"
                                                    data-grupo="{{ $gKey }}"
                                                    onchange="toggleAusenteSL('{{ $gKey }}', this.checked)">
                                                Ausente
                                            </label>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach ($slGrupos as $gKey => $gData)
                                    @foreach ($gData['sups'] as $i => $sup)
                                        <th class="sl-sup-header {{ $i === 0 ? 'sl-sup-sep' : '' }}"
                                            data-grupo="{{ $gKey }}">{{ $sup }}</th>
                                    @endforeach
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="sl-codigo-label">Código</td>
                                @foreach ($slGrupos as $gKey => $gData)
                                    @foreach ($gData['sups'] as $i => $sup)
                                        <td class="sl-cell {{ $i === 0 ? 'sl-grupo-sep' : '' }}"
                                            data-grupo="{{ $gKey }}" data-cara="{{ $sup }}"
                                            onclick="toggleSLCell(this)" title="{{ $sup }}"></td>
                                    @endforeach
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    style="margin-top:1rem;background:var(--fondo-card-alt);border:1px solid var(--fondo-borde);border-radius:8px;padding:.75rem 1rem;font-size:.82rem;">
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:.4rem .75rem;">
                        <div>No. de superficies examinadas: <strong id="sl-superficies">33</strong></div>
                        <div>Valores "1" encontrados: <strong id="sl-positivos"
                                style="color:var(--color-principal);">0</strong></div>
                        <div>Porcentaje de Placa: <strong id="sl-pct"
                                style="font-size:1.05rem;color:#16a34a;">0.0%</strong></div>
                        <div>Higiene Oral: <strong id="sl-higiene" style="color:#16a34a;">Buena (0-15%)</strong></div>
                    </div>
                    <div style="margin-top:.4rem;font-size:.7rem;color:var(--texto-secundario);">
                        Buena 0-15% &nbsp;·&nbsp; Regular 16-30% &nbsp;·&nbsp; Deficiente 31-100%
                    </div>
                </div>

                <div style="margin-top:.75rem;">
                    <label class="form-label-per" style="display:inline;">Fecha índice</label>
                    <input type="date" class="form-ctrl-per"
                        style="width:auto;display:inline-block;margin-left:.5rem;" value="{{ date('Y-m-d') }}"
                        oninput="document.getElementById('fecha_indice_gingival_val').value=this.value">
                </div>
            </div>
        </div>

        {{-- CARD 4: Periodontograma --}}
        <div class="card-sistema periodo-card">
            <div class="periodo-card-header">
                <i class="bi bi-table"></i> Periodontograma
                <span style="margin-left:auto;font-size:.75rem;font-weight:400;opacity:.9;">
                    <span style="color:#DC3545;">─</span> <span style="color:#DC3545;">MG</span> Margen gingival
                    &nbsp;·&nbsp;
                    <span style="color:#2563EB;">─</span> <span style="color:#2563EB;">NCI</span> Nivel inserción (PS−MG)
                </span>
            </div>
            <div class="periodo-card-body" style="padding:.6rem;">
                <input type="hidden" name="sondaje_datos" id="sondaje_datos_json">
                <input type="hidden" name="fecha_sondaje" id="fecha_sondaje_val" value="{{ date('Y-m-d') }}">

                <div class="perio-stats-bar">
                    <span>PS prom: <strong id="sond-promedio">0.0</strong> mm</span>
                    <span>NCI prom: <strong id="sond-nci-prom" style="color:#6366f1;">0.0</strong> mm</span>
                    <span>BOP: <strong id="sond-bop" style="color:#dc2626;">0%</strong></span>
                    <span>PI: <strong id="sond-pi" style="color:#3b82f6;">0%</strong></span>
                    <span style="font-size:.65rem;"><span class="psq on-s" style="cursor:default;"></span> Sangrado
                        &nbsp;<span class="psq on-p" style="cursor:default;"></span> Placa</span>
                </div>

                @php $qsIdx = 8; @endphp

                {{-- ═══ ARCADA SUPERIOR ═══ --}}
                <p class="perio-section-label">▲ Arcada Superior</p>
                <div class="perio-wrap">
                    <table class="perio-tbl" id="pt-sup">
                        <tbody>
                            {{-- Tooth numbers --}}
                            <tr>
                                <td class="pl">Diente</td>
                                @foreach ($dSup as $i => $d)
                                    <td colspan="3" class="pt {{ $i === $qsIdx ? 'qs' : '' }}">{{ $d }}
                                    </td>
                                @endforeach
                            </tr>
                            {{-- Movilidad --}}
                            <tr>
                                <td class="pl">Movilidad</td>
                                @foreach ($dSup as $i => $d)
                                    <td colspan="3" class="{{ $i === $qsIdx ? 'qs' : '' }}"
                                        data-ausente-for="{{ $d }}"><select class="psel"
                                            data-diente="{{ $d }}" data-cara="movilidad"
                                            onchange="calcularSondaje()">
                                            <option value="0">0</option>
                                            <option>I</option>
                                            <option>II</option>
                                            <option>III</option>
                                        </select></td>
                                @endforeach
                            </tr>
                            {{-- Implante --}}
                            <tr>
                                <td class="pl">Implante</td>
                                @foreach ($dSup as $i => $d)
                                    <td colspan="3" class="{{ $i === $qsIdx ? 'qs' : '' }}"
                                        data-ausente-for="{{ $d }}"><input type="checkbox" class="pimpl-cb"
                                            data-diente="{{ $d }}" data-cara="implante"
                                            onchange="calcularSondaje()"></td>
                                @endforeach
                            </tr>
                            {{-- Furcación --}}
                            <tr>
                                <td class="pl">Furcación</td>
                                @foreach ($dSup as $i => $d)
                                    <td colspan="3" class="{{ $i === $qsIdx ? 'qs' : '' }}"
                                        data-ausente-for="{{ $d }}"><select class="psel"
                                            data-diente="{{ $d }}" data-cara="furcacion"
                                            onchange="calcularSondaje()">
                                            <option value="">—</option>
                                            <option>I</option>
                                            <option>II</option>
                                            <option>III</option>
                                        </select></td>
                                @endforeach
                            </tr>
                            {{-- Sangrado V --}}
                            <tr>
                                <td class="pl" style="color:#dc2626;">Sangrado sondeo</td>
                                @foreach ($dSup as $i => $d)
                                    @foreach (['sv_mv', 'sv_v', 'sv_dv'] as $j => $c)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><span class="psq"
                                                data-diente="{{ $d }}" data-cara="{{ $c }}"
                                                onclick="togglePSQ(this,'s')"></span></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- Placa V --}}
                            <tr>
                                <td class="pl" style="color:#3b82f6;">Placa</td>
                                @foreach ($dSup as $i => $d)
                                    @foreach (['pv_mv', 'pv_v', 'pv_dv'] as $j => $c)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><span class="psq"
                                                data-diente="{{ $d }}" data-cara="{{ $c }}"
                                                onclick="togglePSQ(this,'p')"></span></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- MG V --}}
                            <tr>
                                <td class="pl" style="color:#2563eb;">Margen gingival</td>
                                @foreach ($dSup as $i => $d)
                                    @foreach (['mg_mv', 'mg_v', 'mg_dv'] as $j => $c)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><input type="number" class="pmg"
                                                min="-15" max="10" step="1"
                                                data-diente="{{ $d }}" data-cara="{{ $c }}"
                                                placeholder="0" oninput="calcularSondaje();dibujarPeriodontograma()"></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- PS V --}}
                            <tr>
                                <td class="pl" style="color:#d97706;">Prof. de sondaje</td>
                                @foreach ($dSup as $i => $d)
                                    @foreach (['ps_mv', 'ps_v', 'ps_dv'] as $j => $c)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><input type="number" class="pps"
                                                min="0" max="20" step="1"
                                                data-diente="{{ $d }}" data-cara="{{ $c }}"
                                                placeholder="0" oninput="calcularSondaje();dibujarPeriodontograma()"></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- NCI V --}}
                            <tr>
                                <td class="pl">Nivel de inserción</td>
                                @foreach ($dSup as $i => $d)
                                    @foreach (['mv', 'v', 'dv'] as $j => $pt)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><span class="pnci"
                                                id="nci-{{ $d }}-{{ $pt }}">—</span></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- Canvas --}}
                            <tr>
                                <td class="pl" style="vertical-align:top;padding:0;text-align:right;position:relative;height:310px;">
                                    <span style="position:absolute;top:85px;right:6px;transform:translateY(-50%);font-size:.58rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);white-space:nowrap;">Vestibular</span>
                                    <span style="position:absolute;top:225px;right:6px;transform:translateY(-50%);font-size:.58rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);white-space:nowrap;">Palatino</span>
                                </td>
                                <td colspan="{{ count($dSup) * 3 }}"
                                    style="padding:0;border:none;border-bottom:1px solid var(--fondo-borde);"><canvas
                                        class="pcv" id="pcv-sup" height="190"></canvas></td>
                            </tr>
                            {{-- NCI L --}}
                            <tr>
                                <td class="pl">Nivel de inserción</td>
                                @foreach ($dSup as $i => $d)
                                    @foreach (['ml', 'l', 'dl'] as $j => $pt)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><span class="pnci"
                                                id="nci-{{ $d }}-{{ $pt }}">—</span></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- PS L --}}
                            <tr>
                                <td class="pl" style="color:#d97706;">Prof. de sondaje</td>
                                @foreach ($dSup as $i => $d)
                                    @foreach (['ps_ml', 'ps_l', 'ps_dl'] as $j => $c)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><input type="number" class="pps"
                                                min="0" max="20" step="1"
                                                data-diente="{{ $d }}" data-cara="{{ $c }}"
                                                placeholder="0" oninput="calcularSondaje();dibujarPeriodontograma()"></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- MG L --}}
                            <tr>
                                <td class="pl" style="color:#2563eb;">Margen gingival</td>
                                @foreach ($dSup as $i => $d)
                                    @foreach (['mg_ml', 'mg_l', 'mg_dl'] as $j => $c)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><input type="number" class="pmg"
                                                min="-15" max="10" step="1"
                                                data-diente="{{ $d }}" data-cara="{{ $c }}"
                                                placeholder="0" oninput="calcularSondaje();dibujarPeriodontograma()"></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- Placa L --}}
                            <tr>
                                <td class="pl" style="color:#3b82f6;">Placa</td>
                                @foreach ($dSup as $i => $d)
                                    @foreach (['pl_ml', 'pl_l', 'pl_dl'] as $j => $c)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><span class="psq"
                                                data-diente="{{ $d }}" data-cara="{{ $c }}"
                                                onclick="togglePSQ(this,'p')"></span></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- Sangrado L --}}
                            <tr>
                                <td class="pl" style="color:#dc2626;">Sangrado sondeo</td>
                                @foreach ($dSup as $i => $d)
                                    @foreach (['sl_ml', 'sl_l', 'sl_dl'] as $j => $c)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><span class="psq"
                                                data-diente="{{ $d }}" data-cara="{{ $c }}"
                                                onclick="togglePSQ(this,'s')"></span></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- Ausente --}}
                            <tr style="background:var(--fondo-card-alt);">
                                <td class="pl">Ausente</td>
                                @foreach ($dSup as $i => $d)
                                    <td colspan="3" class="{{ $i === $qsIdx ? 'qs' : '' }}"><input type="checkbox"
                                            class="paus-cb" data-diente="{{ $d }}"
                                            onchange="togglePerioAusente('{{ $d }}',this.checked)"></td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="perio-mid-bar">
                    Profundidad media de sondaje: <strong id="sond-prom2">0.0</strong> mm &nbsp;·&nbsp;
                    Nivel medio de inserción: <strong id="sond-nci2" style="color:#6366f1;">0.0</strong> mm &nbsp;·&nbsp;
                    BOP: <strong id="sond-bop2" style="color:#dc2626;">0%</strong> &nbsp;·&nbsp;
                    PI: <strong id="sond-pi2" style="color:#3b82f6;">0%</strong>
                </div>

                {{-- ═══ ARCADA INFERIOR ═══ --}}
                <p class="perio-section-label">▼ Arcada Inferior</p>
                <div class="perio-wrap">
                    <table class="perio-tbl" id="pt-inf">
                        <tbody>
                            {{-- Ausente --}}
                            <tr style="background:var(--fondo-card-alt);">
                                <td class="pl">Ausente</td>
                                @foreach ($dInf as $i => $d)
                                    <td colspan="3" class="{{ $i === $qsIdx ? 'qs' : '' }}"><input type="checkbox"
                                            class="paus-cb" data-diente="{{ $d }}"
                                            onchange="togglePerioAusente('{{ $d }}',this.checked)"></td>
                                @endforeach
                            </tr>
                            {{-- Sangrado L (lingual = top for lower arch) --}}
                            <tr>
                                <td class="pl" style="color:#dc2626;">Sangrado sondeo</td>
                                @foreach ($dInf as $i => $d)
                                    @foreach (['sl_ml', 'sl_l', 'sl_dl'] as $j => $c)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><span class="psq"
                                                data-diente="{{ $d }}" data-cara="{{ $c }}"
                                                onclick="togglePSQ(this,'s')"></span></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- Placa L --}}
                            <tr>
                                <td class="pl" style="color:#3b82f6;">Placa</td>
                                @foreach ($dInf as $i => $d)
                                    @foreach (['pl_ml', 'pl_l', 'pl_dl'] as $j => $c)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><span class="psq"
                                                data-diente="{{ $d }}" data-cara="{{ $c }}"
                                                onclick="togglePSQ(this,'p')"></span></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- MG L --}}
                            <tr>
                                <td class="pl" style="color:#2563eb;">Margen gingival</td>
                                @foreach ($dInf as $i => $d)
                                    @foreach (['mg_ml', 'mg_l', 'mg_dl'] as $j => $c)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><input type="number" class="pmg"
                                                min="-15" max="10" step="1"
                                                data-diente="{{ $d }}" data-cara="{{ $c }}"
                                                placeholder="0" oninput="calcularSondaje();dibujarPeriodontograma()"></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- PS L --}}
                            <tr>
                                <td class="pl" style="color:#d97706;">Prof. de sondaje</td>
                                @foreach ($dInf as $i => $d)
                                    @foreach (['ps_ml', 'ps_l', 'ps_dl'] as $j => $c)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><input type="number" class="pps"
                                                min="0" max="20" step="1"
                                                data-diente="{{ $d }}" data-cara="{{ $c }}"
                                                placeholder="0" oninput="calcularSondaje();dibujarPeriodontograma()"></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- NCI L --}}
                            <tr>
                                <td class="pl">Nivel de inserción</td>
                                @foreach ($dInf as $i => $d)
                                    @foreach (['ml', 'l', 'dl'] as $j => $pt)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><span class="pnci"
                                                id="nci-{{ $d }}-{{ $pt }}">—</span></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- Canvas --}}
                            <tr>
                                <td class="pl" style="vertical-align:top;padding:0;text-align:right;position:relative;height:310px;">
                                    <span style="position:absolute;top:85px;right:6px;transform:translateY(-50%);font-size:.58rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);white-space:nowrap;">Lingual</span>
                                    <span style="position:absolute;top:225px;right:6px;transform:translateY(-50%);font-size:.58rem;font-weight:700;text-transform:uppercase;color:var(--texto-secundario);white-space:nowrap;">Vestibular</span>
                                </td>
                                <td colspan="{{ count($dInf) * 3 }}"
                                    style="padding:0;border:none;border-bottom:1px solid var(--fondo-borde);"><canvas
                                        class="pcv" id="pcv-inf" height="190"></canvas></td>
                            </tr>
                            {{-- NCI V --}}
                            <tr>
                                <td class="pl">Nivel de inserción</td>
                                @foreach ($dInf as $i => $d)
                                    @foreach (['mv', 'v', 'dv'] as $j => $pt)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><span class="pnci"
                                                id="nci-{{ $d }}-{{ $pt }}">—</span></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- PS V --}}
                            <tr>
                                <td class="pl" style="color:#d97706;">Prof. de sondaje</td>
                                @foreach ($dInf as $i => $d)
                                    @foreach (['ps_mv', 'ps_v', 'ps_dv'] as $j => $c)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><input type="number" class="pps"
                                                min="0" max="20" step="1"
                                                data-diente="{{ $d }}" data-cara="{{ $c }}"
                                                placeholder="0" oninput="calcularSondaje();dibujarPeriodontograma()"></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- MG V --}}
                            <tr>
                                <td class="pl" style="color:#2563eb;">Margen gingival</td>
                                @foreach ($dInf as $i => $d)
                                    @foreach (['mg_mv', 'mg_v', 'mg_dv'] as $j => $c)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><input type="number" class="pmg"
                                                min="-15" max="10" step="1"
                                                data-diente="{{ $d }}" data-cara="{{ $c }}"
                                                placeholder="0" oninput="calcularSondaje();dibujarPeriodontograma()"></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- Placa V --}}
                            <tr>
                                <td class="pl" style="color:#3b82f6;">Placa</td>
                                @foreach ($dInf as $i => $d)
                                    @foreach (['pv_mv', 'pv_v', 'pv_dv'] as $j => $c)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><span class="psq"
                                                data-diente="{{ $d }}" data-cara="{{ $c }}"
                                                onclick="togglePSQ(this,'p')"></span></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- Sangrado V --}}
                            <tr>
                                <td class="pl" style="color:#dc2626;">Sangrado sondeo</td>
                                @foreach ($dInf as $i => $d)
                                    @foreach (['sv_mv', 'sv_v', 'sv_dv'] as $j => $c)
                                        <td class="{{ $i === $qsIdx && $j === 0 ? 'qs' : '' }}"
                                            data-ausente-for="{{ $d }}"><span class="psq"
                                                data-diente="{{ $d }}" data-cara="{{ $c }}"
                                                onclick="togglePSQ(this,'s')"></span></td>
                                    @endforeach
                                @endforeach
                            </tr>
                            {{-- Furcación --}}
                            <tr>
                                <td class="pl">Furcación</td>
                                @foreach ($dInf as $i => $d)
                                    <td colspan="3" class="{{ $i === $qsIdx ? 'qs' : '' }}"
                                        data-ausente-for="{{ $d }}"><select class="psel"
                                            data-diente="{{ $d }}" data-cara="furcacion"
                                            onchange="calcularSondaje()">
                                            <option value="">—</option>
                                            <option>I</option>
                                            <option>II</option>
                                            <option>III</option>
                                        </select></td>
                                @endforeach
                            </tr>
                            {{-- Implante --}}
                            <tr>
                                <td class="pl">Implante</td>
                                @foreach ($dInf as $i => $d)
                                    <td colspan="3" class="{{ $i === $qsIdx ? 'qs' : '' }}"
                                        data-ausente-for="{{ $d }}"><input type="checkbox" class="pimpl-cb"
                                            data-diente="{{ $d }}" data-cara="implante"
                                            onchange="calcularSondaje()"></td>
                                @endforeach
                            </tr>
                            {{-- Movilidad --}}
                            <tr>
                                <td class="pl">Movilidad</td>
                                @foreach ($dInf as $i => $d)
                                    <td colspan="3" class="{{ $i === $qsIdx ? 'qs' : '' }}"
                                        data-ausente-for="{{ $d }}"><select class="psel"
                                            data-diente="{{ $d }}" data-cara="movilidad"
                                            onchange="calcularSondaje()">
                                            <option value="0">0</option>
                                            <option>I</option>
                                            <option>II</option>
                                            <option>III</option>
                                        </select></td>
                                @endforeach
                            </tr>
                            {{-- Tooth numbers footer --}}
                            <tr>
                                <td class="pl">Diente</td>
                                @foreach ($dInf as $i => $d)
                                    <td colspan="3" class="pt {{ $i === $qsIdx ? 'qs' : '' }}">{{ $d }}
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:.6rem;">
                    <label class="form-label-per" style="display:inline;">Fecha sondaje</label>
                    <input type="date" class="form-ctrl-per"
                        style="width:auto;display:inline-block;margin-left:.5rem;" value="{{ date('Y-m-d') }}"
                        oninput="document.getElementById('fecha_sondaje_val').value=this.value">
                </div>
            </div>
        </div>

        {{-- CARD 5: Diagnóstico Periodontal --}}
        <div class="card-sistema periodo-card">
            <div class="periodo-card-header">
                <i class="bi bi-clipboard2-pulse"></i> Diagnóstico Periodontal
            </div>
            <div class="periodo-card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label-per">Clasificación periodontal</label>
                        <select name="clasificacion_periodontal" class="form-ctrl-per">
                            <option value="">Seleccionar...</option>
                            <option value="salud_periodontal"
                                {{ old('clasificacion_periodontal') == 'salud_periodontal' ? 'selected' : '' }}>Salud
                                periodontal</option>
                            <option value="gingivitis_inducida_placa"
                                {{ old('clasificacion_periodontal') == 'gingivitis_inducida_placa' ? 'selected' : '' }}>
                                Gingivitis inducida por placa</option>
                            <option value="gingivitis_no_inducida_placa"
                                {{ old('clasificacion_periodontal') == 'gingivitis_no_inducida_placa' ? 'selected' : '' }}>
                                Gingivitis no inducida por placa</option>
                            <option value="periodontitis_estadio_i"
                                {{ old('clasificacion_periodontal') == 'periodontitis_estadio_i' ? 'selected' : '' }}>
                                Periodontitis Estadio I</option>
                            <option value="periodontitis_estadio_ii"
                                {{ old('clasificacion_periodontal') == 'periodontitis_estadio_ii' ? 'selected' : '' }}>
                                Periodontitis Estadio II</option>
                            <option value="periodontitis_estadio_iii"
                                {{ old('clasificacion_periodontal') == 'periodontitis_estadio_iii' ? 'selected' : '' }}>
                                Periodontitis Estadio III</option>
                            <option value="periodontitis_estadio_iv"
                                {{ old('clasificacion_periodontal') == 'periodontitis_estadio_iv' ? 'selected' : '' }}>
                                Periodontitis Estadio IV</option>
                            <option value="periodontitis_necrosante"
                                {{ old('clasificacion_periodontal') == 'periodontitis_necrosante' ? 'selected' : '' }}>
                                Periodontitis necrosante</option>
                            <option value="absceso_periodontal"
                                {{ old('clasificacion_periodontal') == 'absceso_periodontal' ? 'selected' : '' }}>Absceso
                                periodontal</option>
                            <option value="lesion_endoperio"
                                {{ old('clasificacion_periodontal') == 'lesion_endoperio' ? 'selected' : '' }}>Lesión
                                endo-perio
                            </option>
                            <option value="deformidades_condiciones"
                                {{ old('clasificacion_periodontal') == 'deformidades_condiciones' ? 'selected' : '' }}>
                                Deformidades y condiciones</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-per">Extensión</label>
                        <select name="extension" class="form-ctrl-per">
                            <option value="">—</option>
                            <option value="localizada" {{ old('extension') == 'localizada' ? 'selected' : '' }}>
                                Localizada
                            </option>
                            <option value="generalizada" {{ old('extension') == 'generalizada' ? 'selected' : '' }}>
                                Generalizada</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-per">Severidad</label>
                        <select name="severidad" class="form-ctrl-per">
                            <option value="">—</option>
                            <option value="leve" {{ old('severidad') == 'leve' ? 'selected' : '' }}>Leve</option>
                            <option value="moderada" {{ old('severidad') == 'moderada' ? 'selected' : '' }}>Moderada
                            </option>
                            <option value="severa" {{ old('severidad') == 'severa' ? 'selected' : '' }}>Severa</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-per">Pronóstico general</label>
                        <select name="pronostico_general" class="form-ctrl-per">
                            <option value="">—</option>
                            <option value="excelente" {{ old('pronostico_general') == 'excelente' ? 'selected' : '' }}>
                                Excelente</option>
                            <option value="bueno" {{ old('pronostico_general') == 'bueno' ? 'selected' : '' }}>Bueno
                            </option>
                            <option value="regular" {{ old('pronostico_general') == 'regular' ? 'selected' : '' }}>
                                Regular
                            </option>
                            <option value="malo" {{ old('pronostico_general') == 'malo' ? 'selected' : '' }}>Malo
                            </option>
                            <option value="sin_esperanza"
                                {{ old('pronostico_general') == 'sin_esperanza' ? 'selected' : '' }}>
                                Sin esperanza</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label-per">Factores de riesgo</label>
                        <div style="display:flex;flex-wrap:wrap;gap:.75rem;margin-top:.25rem;">
                            @foreach (['tabaco' => 'Tabaco', 'diabetes' => 'Diabetes', 'estres' => 'Estrés', 'medicamentos' => 'Medicamentos', 'genetica' => 'Genética', 'osteoporosis' => 'Osteoporosis', 'embarazo' => 'Embarazo'] as $val => $lbl)
                                <label style="display:flex;align-items:center;gap:.35rem;font-size:.83rem;cursor:pointer;">
                                    <input type="checkbox" name="factores_riesgo[]" value="{{ $val }}"
                                        {{ is_array(old('factores_riesgo')) && in_array($val, old('factores_riesgo')) ? 'checked' : '' }}
                                        style="accent-color:var(--color-principal);">
                                    {{ $lbl }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-per">Diagnóstico</label>
                        <textarea name="diagnostico_texto" class="form-ctrl-per" rows="3"
                            placeholder="Descripción del diagnóstico periodontal...">{{ old('diagnostico_texto') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-per">Plan de tratamiento</label>
                        <textarea name="plan_tratamiento" class="form-ctrl-per" rows="3"
                            placeholder="Plan de tratamiento periodontal...">{{ old('plan_tratamiento') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label-per">Notas adicionales</label>
                        <textarea name="notas" class="form-ctrl-per" rows="2" placeholder="Observaciones adicionales...">{{ old('notas') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div style="display:flex;justify-content:flex-end;gap:.75rem;margin-top:.5rem;">
            <a href="{{ route('periodoncia.index') }}"
                style="background:var(--fondo-borde);color:var(--texto-principal);padding:.5rem 1.25rem;border-radius:8px;font-size:.88rem;text-decoration:none;font-weight:600;">
                Cancelar
            </a>
            <button type="submit"
                style="background:var(--color-principal);color:white;border:none;padding:.5rem 1.5rem;border-radius:8px;font-size:.88rem;font-weight:700;cursor:pointer;box-shadow:0 2px 8px var(--sombra-principal);">
                <i class="bi bi-save me-1"></i> Guardar Ficha Periodontal
            </button>
        </div>

    </form>

@endsection

@push('scripts')
    <script>
        // ── Buscador paciente: cargar datos del paciente seleccionado ──
        document.addEventListener('bp:select', function(e) {
            var pac = e.detail;
            fetch('/api/paciente-info/' + pac.id)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('info-paciente').style.display = 'block';
                    document.getElementById('pac-edad').textContent = data.edad ? data.edad + ' años' : '—';
                    document.getElementById('pac-historia').textContent = data.numero_historia || '—';
                    document.getElementById('pac-doc').textContent = (data.tipo_documento || '') + ': ' + (data
                        .numero_documento || '');
                    document.getElementById('pac-alergias').textContent = data.alergias || 'Ninguna conocida';
                })
                .catch(() => {
                    document.getElementById('info-paciente').style.display = 'block';
                    document.getElementById('pac-historia').textContent = pac.sub || '—';
                });
        });
        document.addEventListener('bp:clear', function() {
            document.getElementById('info-paciente').style.display = 'none';
        });

        // ── O'Leary — triángulos SVG ──
        function toggleCaraSVG(poly) {
            poly.classList.toggle('cara-activa');
            calcularPlaca();
        }

        function toggleAusenteOc(dw) {
            dw.classList.toggle('ausente-oc');
            // Limpiar caras activas si se marca ausente
            if (dw.classList.contains('ausente-oc')) {
                dw.querySelectorAll('.cara-svg').forEach(p => p.classList.remove('cara-activa'));
            }
            calcularPlaca();
        }

        function calcularPlaca() {
            var total = 0;
            var activos = 0;
            var datos = {};
            document.querySelectorAll('.diente-oleary').forEach(function(dw) {
                var d = dw.getAttribute('data-diente');
                var ausente = dw.classList.contains('ausente-oc');
                datos[d] = {
                    ausente: ausente ? 1 : 0
                };
                dw.querySelectorAll('.cara-svg').forEach(function(poly) {
                    var cara = poly.getAttribute('data-cara');
                    var act = poly.classList.contains('cara-activa') ? 1 : 0;
                    datos[d][cara] = act;
                    if (!ausente) {
                        total++;
                        activos += act;
                    }
                });
            });
            var pct = total > 0 ? Math.round((activos / total) * 1000) / 10 : 0;
            document.getElementById('placa-count').textContent = activos;
            document.getElementById('placa-total').textContent = total;
            var badge = document.getElementById('placa-pct-badge');
            badge.textContent = pct.toFixed(1) + '%';
            badge.style.color = pct < 15 ? '#16a34a' : (pct <= 30 ? '#d97706' : '#dc2626');
            document.getElementById('indice_placa_porcentaje_val').value = pct.toFixed(2);
            document.getElementById('indice_placa_datos_json').value = JSON.stringify(datos);
        }

        // ── Silness & Löe Modificado ──
        function toggleSLCell(cell) {
            cell.classList.toggle('activo-sl');
            calcularSL();
        }

        function toggleAusenteSL(grupo, ausente) {
            document.querySelectorAll('.sl-cell[data-grupo="' + grupo + '"]').forEach(function(cell) {
                if (ausente) {
                    cell.classList.add('ausente-sl');
                    cell.classList.remove('activo-sl');
                } else {
                    cell.classList.remove('ausente-sl');
                }
            });
            calcularSL();
        }

        function calcularSL() {
            var datos = {};
            // Registrar ausentes
            document.querySelectorAll('.sl-ausente-cb').forEach(function(cb) {
                datos[cb.dataset.grupo] = {
                    ausente: cb.checked
                };
            });
            // Recorrer células
            var superficies = 0;
            var positivos = 0;
            document.querySelectorAll('.sl-cell').forEach(function(cell) {
                var g = cell.dataset.grupo;
                var cara = cell.dataset.cara;
                var val = cell.classList.contains('activo-sl') ? 1 : 0;
                if (!datos[g]) datos[g] = {
                    ausente: false
                };
                datos[g][cara] = val;
                if (!datos[g].ausente) {
                    superficies++;
                    positivos += val;
                }
            });

            var pct = superficies > 0 ? (positivos / superficies * 100) : 0;
            var color = pct <= 15 ? '#16a34a' : (pct <= 30 ? '#d97706' : '#dc2626');
            var higiene = pct <= 15 ? 'Buena (0-15%)' : (pct <= 30 ? 'Regular (16-30%)' : 'Deficiente (31-100%)');

            document.getElementById('sl-superficies').textContent = superficies;
            document.getElementById('sl-positivos').textContent = positivos;
            var pctEl = document.getElementById('sl-pct');
            pctEl.textContent = pct.toFixed(1) + '%';
            pctEl.style.color = color;
            var hEl = document.getElementById('sl-higiene');
            hEl.textContent = higiene;
            hEl.style.color = color;

            document.getElementById('indice_gingival_porcentaje_val').value = pct.toFixed(2);
            document.getElementById('indice_gingival_datos_json').value = JSON.stringify(datos);
        }

        // ── Periodontograma profesional ──
        function togglePSQ(el, tipo) {
            el.classList.toggle(tipo === 's' ? 'on-s' : 'on-p');
            calcularSondaje();
        }

        function togglePerioAusente(diente, ausente) {
            document.querySelectorAll('[data-ausente-for="' + diente + '"]').forEach(function(td) {
                if (ausente) td.classList.add('td-aus');
                else td.classList.remove('td-aus');
            });
            calcularSondaje();
            dibujarPeriodontograma();
        }

        function calcularSondaje() {
            var datos = {};
            var sumPS = 0;
            var cntPS = 0;
            var sumNCI = 0;
            var cntNCI = 0;
            var totS = 0;
            var cntS = 0;
            var totP = 0;
            var cntP = 0;
            var psCols = ['ps_mv', 'ps_v', 'ps_dv', 'ps_ml', 'ps_l', 'ps_dl'];
            var allD = [18, 17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27, 28,
                48, 47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37, 38
            ];

            // PS values
            document.querySelectorAll('.pps[data-diente]').forEach(function(inp) {
                var d = inp.dataset.diente;
                var c = inp.dataset.cara;
                if (!datos[d]) datos[d] = {};
                var v = inp.value !== '' ? parseFloat(inp.value) : null;
                datos[d][c] = v;
                if (v !== null && psCols.includes(c)) {
                    sumPS += v;
                    cntPS++;
                }
            });
            // MG values
            document.querySelectorAll('.pmg[data-diente]').forEach(function(inp) {
                var d = inp.dataset.diente;
                var c = inp.dataset.cara;
                if (!datos[d]) datos[d] = {};
                datos[d][c] = inp.value !== '' ? parseFloat(inp.value) : null;
            });
            // Calculate NCI = PS - MG for each probing point
            var pairsV = [
                ['ps_mv', 'mg_mv', 'mv'],
                ['ps_v', 'mg_v', 'v'],
                ['ps_dv', 'mg_dv', 'dv']
            ];
            var pairsL = [
                ['ps_ml', 'mg_ml', 'ml'],
                ['ps_l', 'mg_l', 'l'],
                ['ps_dl', 'mg_dl', 'dl']
            ];
            allD.forEach(function(d) {
                pairsV.concat(pairsL).forEach(function(pair) {
                    var ps = datos[d] ? datos[d][pair[0]] : null;
                    var mg = (datos[d] && datos[d][pair[1]] !== null && datos[d][pair[1]] !== undefined) ?
                        datos[d][pair[1]] : 0;
                    var el = document.getElementById('nci-' + d + '-' + pair[2]);
                    if (!el) return;
                    if (ps !== null) {
                        var nci = Math.round((ps - mg) * 10) / 10;
                        el.textContent = nci;
                        el.style.color = nci >= 5 ? '#dc2626' : nci >= 3 ? '#d97706' :
                            'var(--texto-secundario)';
                        if (!datos[d]) datos[d] = {};
                        datos[d]['nci_' + pair[2]] = nci;
                        sumNCI += nci;
                        cntNCI++;
                    } else {
                        el.textContent = '—';
                        el.style.color = '';
                    }
                });
            });
            // Squares (sangrado / placa)
            document.querySelectorAll('.psq[data-diente]').forEach(function(sq) {
                var d = sq.dataset.diente;
                var c = sq.dataset.cara;
                if (!datos[d]) datos[d] = {};
                var isOn = sq.classList.contains('on-s') || sq.classList.contains('on-p');
                datos[d][c] = isOn ? 1 : 0;
                if (c.startsWith('sv_') || c.startsWith('sl_')) {
                    cntS++;
                    if (isOn) totS++;
                }
                if (c.startsWith('pv_') || c.startsWith('pl_')) {
                    cntP++;
                    if (isOn) totP++;
                }
            });
            // Selects + checkboxes
            document.querySelectorAll('.psel[data-diente]').forEach(function(sel) {
                var d = sel.dataset.diente;
                var c = sel.dataset.cara;
                if (!datos[d]) datos[d] = {};
                datos[d][c] = sel.value;
            });
            document.querySelectorAll('.pimpl-cb,.paus-cb').forEach(function(cb) {
                var d = cb.dataset.diente;
                var c = cb.dataset.cara || 'ausente';
                if (!datos[d]) datos[d] = {};
                datos[d][c] = cb.checked ? 1 : 0;
            });

            var promPS = cntPS > 0 ? (sumPS / cntPS).toFixed(1) : '0.0';
            var promNCI = cntNCI > 0 ? (sumNCI / cntNCI).toFixed(1) : '0.0';
            var bop = cntS > 0 ? Math.round(totS / cntS * 100) + '%' : '0%';
            var pi = cntP > 0 ? Math.round(totP / cntP * 100) + '%' : '0%';
            ['sond-promedio', 'sond-prom2'].forEach(function(id) {
                var e = document.getElementById(id);
                if (e) e.textContent = promPS;
            });
            ['sond-nci-prom', 'sond-nci2'].forEach(function(id) {
                var e = document.getElementById(id);
                if (e) e.textContent = promNCI;
            });
            ['sond-bop', 'sond-bop2'].forEach(function(id) {
                var e = document.getElementById(id);
                if (e) e.textContent = bop;
            });
            ['sond-pi', 'sond-pi2'].forEach(function(id) {
                var e = document.getElementById(id);
                if (e) e.textContent = pi;
            });
            document.getElementById('sondaje_datos_json').value = JSON.stringify(datos);
        }



        // Cache de imágenes: se carga una sola vez al inicio
        var _imgCache = {};
        var _imgListos = false;
        var _pendienteDibujo = false;

        (function() {
            var tipos = ['incisivo', 'canino', 'premolar', 'molar'];
            var cargadas = 0;
            tipos.forEach(function(tipo) {
                var img = new Image();
                img.onload = function() {
                    _imgCache[tipo] = img;
                    cargadas++;
                    if (cargadas === tipos.length) {
                        _imgListos = true;
                        if (_pendienteDibujo) dibujarPeriodontograma();
                    }
                };
                img.onerror = function() {
                    _imgCache[tipo] = null;
                    cargadas++;
                    if (cargadas === tipos.length) {
                        _imgListos = true;
                        if (_pendienteDibujo) dibujarPeriodontograma();
                    }
                };
                img.src = '/img/dientes/' + tipo + '.png';
            });
        })();

        function dibujarPeriodontograma() {

            if (!_imgListos) {
                _pendienteDibujo = true;
                return;
            }
            _pendienteDibujo = false;

            var cfgs = [{
                    id: 'sup',
                    dientes: [18, 17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27, 28],
                    cd: -1
                },
                {
                    id: 'inf',
                    dientes: [48, 47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37, 38],
                    cd: 1
                }
            ];

            cfgs.forEach(function(cfg) {

                var canvas = document.getElementById('pcv-' + cfg.id);
                if (!canvas) return;

                var toothW = 58;
                var W = toothW * cfg.dientes.length;

                canvas.width = W;
                canvas.height = 310;

                var ctx = canvas.getContext('2d');
                var H = canvas.height;

                ctx.clearRect(0, 0, W, H);

                var scale = 6;
                var gap = 140;

                var centerY = H / 2;
                var midY1 = centerY - (gap / 2);
                var midY2 = centerY + (gap / 2);

                ctx.fillStyle = '#eef2f7';
                ctx.fillRect(0, 0, W, H);

                var colW = toothW / 3;

                // =========================
                // 🔹 HELPERS 
                // =========================
                function getPts(pairs) {
                    var pts = [];
                    cfg.dientes.forEach(function(d, di) {
                        var ausEl = document.querySelector('.paus-cb[data-diente="' + d + '"]');
                        var aus = ausEl && ausEl.checked;

                        pairs.forEach(function(pair, ci) {
                            var psEl = document.querySelector('.pps[data-diente="' + d +
                                '"][data-cara="' + pair[0] + '"]');
                            var mgEl = document.querySelector('.pmg[data-diente="' + d +
                                '"][data-cara="' + pair[1] + '"]');

                            pts.push({
                                x: (di * 3 + ci) * colW + colW / 2,
                                ps: psEl && psEl.value !== '' ? parseFloat(psEl.value) :
                                    null,
                                mg: mgEl && mgEl.value !== '' ? parseFloat(mgEl.value) : 0,
                                aus: aus
                            });
                        });
                    });
                    return pts;
                }

                function drawLine(pts, midY, dir) {

                    // Azul primero (queda detrás)
                    ctx.beginPath();
                    var st = false;

                    pts.forEach(function(pt) {
                        if (pt.aus) {
                            st = false;
                            return;
                        }

                        var ps = pt.ps !== null ? pt.ps : 0;
                        var mg = pt.ps !== null ? (pt.mg || 0) : 0;

                        var y = midY - (ps - mg) * scale * dir;

                        if (!st) {
                            ctx.moveTo(pt.x, y);
                            st = true;
                        } else ctx.lineTo(pt.x, y);
                    });

                    ctx.strokeStyle = '#2563EB';
                    ctx.lineWidth = 2.2;
                    ctx.stroke();

                    // Roja al final (queda al frente)
                    ctx.beginPath();
                    st = false;

                    pts.forEach(function(pt) {
                        if (pt.aus) {
                            st = false;
                            return;
                        }

                        var mg = pt.ps !== null ? (pt.mg || 0) : 0;
                        var y = midY + mg * scale * dir;

                        if (!st) {
                            ctx.moveTo(pt.x, y);
                            st = true;
                        } else ctx.lineTo(pt.x, y);
                    });

                    ctx.strokeStyle = '#DC3545';
                    ctx.lineWidth = 2;
                    ctx.stroke();
                }

                // =========================
                // 🔹 DIENTES
                // =========================
                function drawDientes(midY, invert) {

                    var offsets = {
                        18: 53,
                        17: 53,
                        16: 53,
                        15: 46,
                        14: 46,
                        13: 40,
                        12: 47,
                        11: 47,
                        21: 47,
                        22: 47,
                        23: 40,
                        24: 46,
                        25: 46,
                        26: 53,
                        27: 53,
                        28: 53,
                        48: 53,
                        47: 53,
                        46: 53,
                        45: 46,
                        44: 46,
                        43: 40,
                        42: 47,
                        41: 47,
                        31: 47,
                        32: 40,
                        33: 40,
                        34: 46,
                        35: 46,
                        36: 53,
                        37: 53,
                        38: 53
                    };

                    cfg.dientes.forEach(function(d, i) {

                        var tipo =
                            (d % 10 <= 2) ? 'incisivo' :
                            (d % 10 == 3) ? 'canino' :
                            (d % 10 <= 5) ? 'premolar' : 'molar';

                        var img = _imgCache[tipo];
                        if (!img) return;

                        var ancho = toothW * 0.98;
                        var x = i * toothW + (toothW - ancho) / 2;
                        var offsetY = offsets[d] || 60;

                        ctx.save();

                        if (invert) {
                            ctx.translate(x + ancho / 2, midY);
                            ctx.scale(1, -1);
                            ctx.drawImage(img, -ancho / 2, -offsetY, ancho, 120);
                        } else {
                            ctx.drawImage(img, x, midY - offsetY, ancho, 120);
                        }

                        ctx.restore();
                    });
                }

                function drawGrid(midY, haciaArriba) {
                    for (var mm = 1; mm <= 18; mm++) {
                        var y = haciaArriba ? midY - mm * scale : midY + mm * scale;
                        if (y > 0 && y < H) {
                            ctx.beginPath();
                            ctx.moveTo(0, y);
                            ctx.lineTo(W, y);
                            ctx.strokeStyle = 'rgba(156,163,175,0.5)';
                            ctx.stroke();
                        }
                    }
                }

                function drawOverlay(midY) {
                    for (var i = 1; i < cfg.dientes.length; i++) {
                        var x = i * toothW;
                        ctx.beginPath();
                        ctx.moveTo(x, midY - 80);
                        ctx.lineTo(x, midY + 80);
                        ctx.strokeStyle = 'rgba(95,85,72,0.25)';
                        ctx.stroke();
                    }

                    ctx.setLineDash([5, 3]);
                    ctx.beginPath();
                    ctx.moveTo(0, midY);
                    ctx.lineTo(W, midY);
                    ctx.strokeStyle = 'rgba(120,100,80,0.5)';
                    ctx.stroke();
                    ctx.setLineDash([]);
                }

                // =========================
                //  DIBUJO
                // =========================
                var pairsV = [
                    ['ps_mv', 'mg_mv'],
                    ['ps_v', 'mg_v'],
                    ['ps_dv', 'mg_dv']
                ];
                var pairsL = [
                    ['ps_ml', 'mg_ml'],
                    ['ps_l', 'mg_l'],
                    ['ps_dl', 'mg_dl']
                ];

                // pairs: solo un lado (V o L) por fila para evitar sobreescritura
                // dir=1  → fila invertida (raíces arriba): línea desviación va hacia arriba (y < midY)
                // dir=-1 → fila normal  (raíces abajo):  línea desviación va hacia abajo  (y > midY)
                function drawFila(midY, invert, haciaArriba, dir, pairs) {
                    drawDientes(midY, invert);
                    drawGrid(midY, haciaArriba);
                    drawOverlay(midY);
                    drawLine(getPts(pairs), midY, dir);
                }

                if (cfg.cd < 0) {
                    // Superior: fila invertida = vestibular (corona abajo, raíz arriba → dir=1)
                    //           fila normal    = lingual/palatino (corona arriba → dir=-1)
                    drawFila(midY1, true, true, 1, pairsV);
                    drawFila(midY2, false, false, -1, pairsL);
                } else {
                    // Inferior: fila normal    = vestibular (corona arriba → dir=-1)
                    //           fila invertida = lingual    (corona abajo, raíz arriba → dir=1)
                    drawFila(midY2, false, false, -1, pairsV);
                    drawFila(midY1, true, true, 1, pairsL);
                }

            });
        }

        // ── Submit ──
        document.getElementById('formPeriodoncia').addEventListener('submit', function() {
            calcularPlaca();
            calcularSL();
            calcularSondaje();
        });
        calcularPlaca();
        calcularSL();
        window.addEventListener('load', function() {
            dibujarPeriodontograma();
        });
        window.addEventListener('resize', function() {
            dibujarPeriodontograma();
        });
    </script>
@endpush
