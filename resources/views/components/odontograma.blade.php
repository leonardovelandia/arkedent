@props(['datos' => null, 'modo' => 'editar', 'hallazgos' => null])

@php
    $uid = 'od_' . substr(md5(uniqid('', true)), 0, 8);
    $esEditable = $modo === 'editar';

    $datosJson = 'null';
    if ($datos) {
        if (is_array($datos)) {
            $datosJson = json_encode($datos);
        } elseif (is_string($datos) && $datos !== '' && $datos !== '{}') {
            $datosJson = $datos;
        }
    }

    $hallazgosArray = [];
    $hallazgosJson  = '[]';
    if ($hallazgos) {
        if (is_array($hallazgos)) {
            $hallazgosArray = $hallazgos;
            $hallazgosJson  = json_encode($hallazgos);
        } elseif (is_string($hallazgos) && $hallazgos !== '' && $hallazgos !== '[]') {
            $decoded = json_decode($hallazgos, true);
            if (is_array($decoded)) {
                $hallazgosArray = $decoded;
                $hallazgosJson  = $hallazgos;
            }
        }
    }
@endphp

<style>
.odo-wrap { font-family: inherit; }
.odo-controles {
    display: flex; gap: .5rem; flex-wrap: wrap; align-items: center;
    margin-bottom: .9rem;
}
.odo-btn {
    background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;
    border-radius: 7px; padding: .35rem .85rem; font-size: .78rem;
    font-weight: 600; cursor: pointer; display: inline-flex; align-items: center;
    gap: .35rem; transition: background .13s, border-color .13s;
    user-select: none;
}
.odo-btn:hover { background: #e5e7eb; }
.odo-btn.activo { background: var(--color-principal); color: #fff; border-color: var(--color-principal); }
.odo-btn.peligro { border-color: #fecdd3; color: #dc2626; }
.odo-btn.peligro:hover { background: #fef2f2; }
.odo-btn.tipo-act { background: var(--color-muy-claro); color: var(--color-hover); border-color: var(--color-claro); }

.odo-tablero {
    overflow-x: auto;
    padding: .5rem 0 1rem;
}
.odo-arcada {
    display: flex;
    justify-content: center;
    align-items: flex-end;
    gap: 0;
    margin: 0;
}
.odo-arcada.inferior { align-items: flex-start; }
.odo-cuadrante { display: flex; gap: 3px; }
.odo-separador-v {
    width: 2px; background: var(--color-claro); margin: 0 4px;
    align-self: stretch; border-radius: 2px;
}
.odo-linea-h {
    height: 2px; background: var(--color-claro); margin: 3px 0;
    border-radius: 2px;
}
.odo-label-arcada {
    text-align: center; font-size: .65rem; color: #9ca3af;
    font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
    margin: 3px 0;
}

/* Diente */
.odo-diente-wrap {
    display: flex; flex-direction: column; align-items: center;
    cursor: pointer; position: relative;
}
.odo-diente-wrap.seleccionado .odo-diente-svg {
    outline: 2px solid var(--color-principal);
    outline-offset: 1px;
    animation: odo-pulso .8s ease-in-out infinite alternate;
}
@keyframes odo-pulso {
    from { outline-color: var(--color-principal); }
    to   { outline-color: var(--color-claro); }
}
.odo-diente-svg {
    display: block;
    width: 44px; height: 44px;
    border-radius: 3px;
    overflow: visible;
}
.odo-diente-svg polygon {
    stroke: #9ca3af;
    stroke-width: 1.2;
    transition: filter .1s;
}
.odo-diente-svg polygon:hover { filter: brightness(.88); }
.odo-ver .odo-diente-svg polygon { pointer-events: none; }

.odo-num {
    font-size: .6rem; font-weight: 700; color: #6b7280;
    margin-top: 2px; user-select: none; line-height: 1;
}
.odo-nota-dot {
    position: absolute; top: 0; right: 0;
    width: 7px; height: 7px; border-radius: 50%;
    background: var(--color-principal); border: 1px solid #fff;
    display: none;
}

/* Panel flotante */
.odo-panel {
    position: fixed; z-index: 10000;
    background: #fff; border: 1px solid var(--color-muy-claro);
    border-radius: 12px;
    box-shadow: 0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);
    min-width: 240px; max-width: 280px;
    padding: 0;
    display: none;
}
.odo-panel.visible { display: block; }
.odo-panel-head {
    background: var(--color-muy-claro); padding: .55rem .85rem;
    border-radius: 12px 12px 0 0;
    border-bottom: 1px solid var(--color-muy-claro);
    display: flex; justify-content: space-between; align-items: center;
}
.odo-panel-titulo {
    font-size: .75rem; font-weight: 700; color: var(--color-hover);
    text-transform: uppercase; letter-spacing: .04em;
}
.odo-panel-cerrar {
    background: none; border: none; cursor: pointer;
    color: #9ca3af; font-size: 1rem; line-height: 1; padding: 0;
}
.odo-panel-cerrar:hover { color: #374151; }
.odo-panel-body { padding: .65rem .85rem; }

.odo-estados-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: .35rem; margin-bottom: .55rem;
}
.odo-estado-btn {
    display: flex; align-items: center; gap: .4rem;
    padding: .3rem .5rem; border-radius: 6px; cursor: pointer;
    font-size: .74rem; font-weight: 500; color: #374151;
    border: 1.5px solid #e5e7eb; background: #fff;
    transition: border-color .12s, background .12s;
    text-align: left;
}
.odo-estado-btn:hover { border-color: var(--color-principal); background: var(--fondo-card-alt); }
.odo-estado-btn.activo { border-color: var(--color-principal); background: var(--color-muy-claro); }
.odo-estado-dot {
    width: 12px; height: 12px; border-radius: 3px; flex-shrink: 0;
    border: 1px solid rgba(0,0,0,.12);
}
.odo-panel-acciones {
    display: flex; gap: .35rem; flex-wrap: wrap;
    border-top: 1px solid var(--fondo-borde); padding-top: .5rem; margin-top: .35rem;
}
.odo-panel-acc {
    font-size: .72rem; font-weight: 600; cursor: pointer; padding: .28rem .6rem;
    border-radius: 5px; border: 1px solid #e5e7eb; background: #f9fafb;
    color: #374151; transition: background .12s;
}
.odo-panel-acc:hover { background: #e5e7eb; }
.odo-panel-acc.danger { border-color: #fecdd3; color: #dc2626; }
.odo-panel-acc.danger:hover { background: #fef2f2; }
.odo-panel-acc.todo { background: var(--color-muy-claro); color: var(--color-principal); border-color: var(--color-claro); }
.odo-panel-acc.todo:hover { background: var(--fondo-card-alt); }

.odo-nota-input {
    width: 100%; border: 1px solid #e5e7eb; border-radius: 6px;
    padding: .3rem .55rem; font-size: .76rem; outline: none;
    resize: none; margin-top: .45rem; font-family: inherit;
    transition: border-color .13s;
}
.odo-nota-input:focus { border-color: var(--color-principal); }

/* Leyenda */
.odo-leyenda {
    display: flex; flex-wrap: wrap; gap: .45rem .85rem;
    margin-top: .85rem; padding-top: .85rem;
    border-top: 1px solid var(--fondo-borde);
}
.odo-ley-item {
    display: flex; align-items: center; gap: .3rem;
    font-size: .72rem; color: #4b5563;
}
.odo-ley-dot {
    width: 14px; height: 14px; border-radius: 3px;
    border: 1.5px solid rgba(0,0,0,.12); flex-shrink: 0;
}

/* Hallazgos */
.odo-hallazgos {
    margin-top: 1.25rem; border-top: 2px solid var(--color-muy-claro);
    padding-top: 1rem;
}
.odo-hall-titulo {
    font-size: .8rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; color: var(--color-hover); margin-bottom: .85rem;
    display: flex; align-items: center; gap: .4rem;
}
.odo-hall-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: .65rem 1.1rem;
    background: var(--fondo-card-alt); border: 1px solid var(--color-muy-claro);
    border-radius: 10px; padding: .85rem 1rem; margin-bottom: .85rem;
}
@media (max-width: 600px) { .odo-hall-grid { grid-template-columns: 1fr; } }
.odo-hall-col { display: flex; flex-direction: column; gap: .55rem; }
.odo-hall-field { display: flex; flex-direction: column; gap: .22rem; }
.odo-hall-lbl {
    font-size: .72rem; font-weight: 700; color: #374151;
    text-transform: uppercase; letter-spacing: .04em;
}
.odo-hall-ctrl {
    border: 1px solid var(--color-acento-activo); border-radius: 7px;
    padding: .42rem .7rem; font-size: .84rem; outline: none;
    background: #fff; transition: border-color .13s, box-shadow .13s;
    font-family: inherit; color: #1c2b22; width: 100%;
}
.odo-hall-ctrl:focus { border-color: var(--color-principal); box-shadow: 0 0 0 3px var(--sombra-principal); }
.odo-hall-ctrl:disabled { background: #f3f4f6; color: #9ca3af; cursor: not-allowed; }
.odo-hall-input-wrap { position: relative; }
.odo-hall-input-wrap i { position: absolute; left: .65rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: .85rem; pointer-events: none; }
.odo-hall-input-wrap .odo-hall-ctrl { padding-left: 2rem; }
.odo-hall-check { display: flex; align-items: center; gap: .45rem; font-size: .82rem; color: #374151; cursor: pointer; margin-top: .15rem; }
.odo-hall-check input[type=checkbox] { width: 15px; height: 15px; accent-color: var(--color-principal); cursor: pointer; }

/* Autocomplete dropdown */
.odo-ac-dropdown {
    position: absolute; z-index: 9001; background: #fff;
    border: 1px solid var(--color-muy-claro); border-radius: 8px;
    box-shadow: 0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);
    max-height: 200px; overflow-y: auto; left: 0; right: 0; top: 100%;
    margin-top: 2px; display: none;
}
.odo-ac-dropdown.abierto { display: block; }
.odo-ac-item {
    padding: .4rem .75rem; font-size: .8rem; cursor: pointer;
    border-bottom: 1px solid var(--fondo-borde); color: #374151;
    transition: background .1s;
}
.odo-ac-item:last-child { border-bottom: none; }
.odo-ac-item:hover, .odo-ac-item.resaltado { background: var(--color-muy-claro); color: var(--color-hover); }
.odo-ac-codigo { font-weight: 700; color: var(--color-principal); margin-right: .3rem; }

/* Botón agregar */
.odo-hall-add-row { display: flex; justify-content: flex-end; margin-bottom: 1rem; }
.odo-btn-agregar {
    background: linear-gradient(135deg,var(--color-principal),var(--color-claro)); color: #fff;
    border: none; border-radius: 8px; padding: .45rem 1.1rem;
    font-size: .82rem; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: .4rem;
    transition: filter .15s;
}
.odo-btn-agregar:hover { filter: brightness(1.12); }

/* Tabla hallazgos */
.odo-hall-tabla-wrap { overflow-x: auto; border-radius: 8px; border: 1px solid var(--color-muy-claro); }
.odo-hall-tabla {
    width: 100%; border-collapse: collapse; font-size: .82rem;
    min-width: 520px;
}
.odo-hall-tabla thead th {
    background: var(--color-muy-claro); color: var(--color-hover); font-weight: 700;
    font-size: .73rem; text-transform: uppercase; letter-spacing: .04em;
    padding: .55rem .85rem; border-bottom: 2px solid var(--color-muy-claro);
    white-space: nowrap;
}
.odo-hall-tabla tbody tr { transition: background .1s; }
.odo-hall-tabla tbody tr:hover { background: var(--fondo-card-alt); }
.odo-hall-tabla tbody td {
    padding: .5rem .85rem; border-bottom: 1px solid var(--fondo-borde);
    vertical-align: middle;
}
.odo-hall-tabla tbody tr:last-child td { border-bottom: none; }
.odo-hall-vacia { text-align: center; padding: 1.5rem; color: #9ca3af; font-size: .82rem; }

.odo-hall-badge-pieza {
    background: var(--color-muy-claro); color: var(--color-hover); border-radius: 20px;
    padding: .18rem .6rem; font-size: .75rem; font-weight: 700;
    display: inline-block;
}
.odo-hall-badge-cara {
    background: #EFF6FF; color: #1d4ed8; border-radius: 20px;
    padding: .18rem .55rem; font-size: .72rem; font-weight: 600;
    display: inline-block;
}
.odo-hall-nota-inline {
    border: 1px solid #e5e7eb; border-radius: 6px; padding: .25rem .5rem;
    font-size: .76rem; outline: none; width: 120px; font-family: inherit;
    transition: border-color .12s;
}
.odo-hall-nota-inline:focus { border-color: var(--color-principal); }
.odo-hall-accion {
    background: none; border: 1px solid var(--color-muy-claro); border-radius: 6px;
    width: 28px; height: 28px; display: inline-flex; align-items: center;
    justify-content: center; cursor: pointer; font-size: .82rem;
    transition: background .12s; color: var(--color-principal);
}
.odo-hall-accion:hover { background: var(--color-muy-claro); }
.odo-hall-accion.del { color: #dc2626; border-color: #fecdd3; }
.odo-hall-accion.del:hover { background: #fef2f2; }
</style>

<div id="{{ $uid }}" class="odo-wrap {{ !$esEditable ? 'odo-ver' : '' }}">

    {{-- Controles --}}
    @if($esEditable)
    <div class="odo-controles">
        <button type="button" id="{{ $uid }}-btn-adulto" class="odo-btn tipo-act" onclick="OD_{{ $uid }}.toggleTipo('adulto')">
            <i class="bi bi-person"></i> Adulto
        </button>
        <button type="button" id="{{ $uid }}-btn-infantil" class="odo-btn" onclick="OD_{{ $uid }}.toggleTipo('infantil')">
            <i class="bi bi-person-fill"></i> Infantil
        </button>
        <button type="button" id="{{ $uid }}-btn-multiple" class="odo-btn" onclick="OD_{{ $uid }}.toggleMultiple()" title="Seleccionar varios dientes y aplicar el mismo estado">
            <i class="bi bi-grid-3x3-gap"></i> Selección múltiple
        </button>
        <button type="button" class="odo-btn peligro" onclick="OD_{{ $uid }}.limpiarTodo()">
            <i class="bi bi-arrow-counterclockwise"></i> Limpiar todo
        </button>
    </div>
    @endif

    {{-- Tablero --}}
    <div class="odo-tablero" id="{{ $uid }}-tablero"></div>

    {{-- Panel flotante --}}
    <div id="{{ $uid }}-panel" class="odo-panel">
        <div class="odo-panel-head">
            <span class="odo-panel-titulo" id="{{ $uid }}-panel-titulo">Diente</span>
            <button type="button" class="odo-panel-cerrar" onclick="OD_{{ $uid }}.cerrarPanel()">✕</button>
        </div>
        <div class="odo-panel-body">
            <div class="odo-estados-grid" id="{{ $uid }}-panel-estados"></div>
            <textarea id="{{ $uid }}-panel-nota" class="odo-nota-input" rows="2"
                      placeholder="Nota sobre este diente..."
                      oninput="OD_{{ $uid }}.guardarNota(this.value)"></textarea>
            <div class="odo-panel-acciones">
                <button type="button" class="odo-panel-acc danger" onclick="OD_{{ $uid }}.limpiarSuperficie()">
                    <i class="bi bi-eraser"></i> Limpiar
                </button>
                <button type="button" class="odo-panel-acc todo" onclick="OD_{{ $uid }}.aplicarTodoDiente()">
                    <i class="bi bi-square-fill"></i> Todo el diente
                </button>
            </div>
        </div>
    </div>

    {{-- Leyenda --}}
    <div class="odo-leyenda" id="{{ $uid }}-leyenda"></div>

    {{-- Hallazgos solo lectura (ver) --}}
    @if(!$esEditable && count($hallazgosArray) > 0)
    <div style="margin-top:1.25rem;border-top:2px solid var(--color-muy-claro);padding-top:1rem;">
        <div style="font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--color-hover);margin-bottom:.85rem;display:flex;align-items:center;gap:.4rem;">
            <i class="bi bi-clipboard2-pulse" style="color:var(--color-principal);"></i> Hallazgos Clínicos
        </div>
        <div style="overflow-x:auto;border-radius:8px;border:1px solid var(--color-muy-claro);">
            <table style="width:100%;border-collapse:collapse;font-size:.82rem;min-width:480px;">
                <thead>
                    <tr>
                        <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.73rem;text-transform:uppercase;letter-spacing:.04em;padding:.55rem .85rem;border-bottom:2px solid var(--color-muy-claro);white-space:nowrap;">Diagnóstico</th>
                        <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.73rem;text-transform:uppercase;letter-spacing:.04em;padding:.55rem .85rem;border-bottom:2px solid var(--color-muy-claro);white-space:nowrap;">Procedimiento</th>
                        <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.73rem;text-transform:uppercase;letter-spacing:.04em;padding:.55rem .85rem;border-bottom:2px solid var(--color-muy-claro);white-space:nowrap;">Pieza</th>
                        <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.73rem;text-transform:uppercase;letter-spacing:.04em;padding:.55rem .85rem;border-bottom:2px solid var(--color-muy-claro);white-space:nowrap;">Cara</th>
                        <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.73rem;text-transform:uppercase;letter-spacing:.04em;padding:.55rem .85rem;border-bottom:2px solid var(--color-muy-claro);white-space:nowrap;">Nota</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hallazgosArray as $h)
                    <tr style="transition:background .1s;" onmouseover="this.style.background='var(--fondo-card-alt)'" onmouseout="this.style.background=''">
                        <td style="padding:.5rem .85rem;border-bottom:1px solid var(--fondo-borde);vertical-align:middle;">
                            @if(!empty($h['diagnostico_codigo']))
                                <span style="font-weight:700;color:var(--color-principal);margin-right:.3rem;">{{ $h['diagnostico_codigo'] }}</span>
                            @endif
                            {{ $h['diagnostico_nombre'] ?? '' }}
                        </td>
                        <td style="padding:.5rem .85rem;border-bottom:1px solid var(--fondo-borde);vertical-align:middle;font-size:.78rem;color:#4b5563;">{{ $h['procedimiento'] ?? '—' }}</td>
                        <td style="padding:.5rem .85rem;border-bottom:1px solid var(--fondo-borde);vertical-align:middle;">
                            @if(!empty($h['pieza']))
                                <span style="background:var(--color-muy-claro);color:var(--color-hover);border-radius:20px;padding:.18rem .6rem;font-size:.75rem;font-weight:700;display:inline-block;">{{ $h['pieza'] }}</span>
                            @else —
                            @endif
                        </td>
                        <td style="padding:.5rem .85rem;border-bottom:1px solid var(--fondo-borde);vertical-align:middle;">
                            @if(!empty($h['ausente']))
                                <span style="color:#dc2626;font-size:.75rem;font-weight:600;">Ausente</span>
                            @elseif(!empty($h['cara']))
                                <span style="background:#EFF6FF;color:#1d4ed8;border-radius:20px;padding:.18rem .55rem;font-size:.72rem;font-weight:600;display:inline-block;">{{ $h['cara'] }}</span>
                            @else —
                            @endif
                        </td>
                        <td style="padding:.5rem .85rem;border-bottom:1px solid var(--fondo-borde);vertical-align:middle;color:#6b7280;font-size:.78rem;">{{ $h['nota'] ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($esEditable)
    {{-- Panel Hallazgos Clínicos --}}
    <div class="odo-hallazgos" id="{{ $uid }}-hallazgos">
        <div class="odo-hall-titulo">
            <i class="bi bi-clipboard2-pulse" style="color:var(--color-principal);"></i>
            Hallazgos Clínicos
        </div>

        {{-- Formulario --}}
        <div class="odo-hall-grid">
            {{-- Col izquierda --}}
            <div class="odo-hall-col">
                <div class="odo-hall-field">
                    <label class="odo-hall-lbl">Pieza dental</label>
                    <select id="{{ $uid }}-h-pieza" class="odo-hall-ctrl">
                        <option value="">— Selecciona una pieza —</option>
                    </select>
                </div>
                <div class="odo-hall-field">
                    <label class="odo-hall-lbl">Cara</label>
                    <select id="{{ $uid }}-h-cara" class="odo-hall-ctrl">
                        <option value="">— Selecciona una cara —</option>
                        <option value="Oclusal">Oclusal</option>
                        <option value="Vestibular">Vestibular</option>
                        <option value="Lingual">Lingual</option>
                        <option value="Mesial">Mesial</option>
                        <option value="Distal">Distal</option>
                    </select>
                </div>
                <label class="odo-hall-check">
                    <input type="checkbox" id="{{ $uid }}-h-ausente" onchange="OD_{{ $uid }}.toggleAusente(this.checked)">
                    <span>Marcar como ausente (pieza faltante)</span>
                </label>
            </div>
            {{-- Col derecha --}}
            <div class="odo-hall-col">
                <div class="odo-hall-field">
                    <label class="odo-hall-lbl">Diagnóstico CIE-10</label>
                    <div class="odo-hall-input-wrap" style="position:relative;">
                        <i class="bi bi-search"></i>
                        <input type="text" id="{{ $uid }}-h-diag-txt" class="odo-hall-ctrl"
                               placeholder="Buscar código o nombre..."
                               autocomplete="off"
                               oninput="OD_{{ $uid }}.filtrarDiag(this.value)"
                               onkeydown="OD_{{ $uid }}.navDiag(event)">
                        <input type="hidden" id="{{ $uid }}-h-diag-cod" value="">
                        <input type="hidden" id="{{ $uid }}-h-diag-nom" value="">
                        <div id="{{ $uid }}-h-diag-dd" class="odo-ac-dropdown"></div>
                    </div>
                </div>
                <div class="odo-hall-field">
                    <label class="odo-hall-lbl">Procedimiento</label>
                    <select id="{{ $uid }}-h-proc" class="odo-hall-ctrl">
                        <option value="">— Selecciona procedimiento —</option>
                        <option value="Sellante de fosas y fisuras">Sellante de fosas y fisuras</option>
                        <option value="Restauración de caries en resina">Restauración de caries en resina</option>
                        <option value="Restauración de caries en amalgama">Restauración de caries en amalgama</option>
                        <option value="Extracción simple">Extracción simple</option>
                        <option value="Extracción quirúrgica">Extracción quirúrgica</option>
                        <option value="Endodoncia unirradicular">Endodoncia unirradicular</option>
                        <option value="Endodoncia birradicular">Endodoncia birradicular</option>
                        <option value="Endodoncia multirradicular">Endodoncia multirradicular</option>
                        <option value="Corona metal porcelana">Corona metal porcelana</option>
                        <option value="Corona zirconia">Corona zirconia</option>
                        <option value="Implante dental">Implante dental</option>
                        <option value="Profilaxis dental">Profilaxis dental</option>
                        <option value="Raspado y alisado radicular">Raspado y alisado radicular</option>
                        <option value="Cirugía periodontal">Cirugía periodontal</option>
                        <option value="Blanqueamiento dental">Blanqueamiento dental</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="odo-hall-add-row">
            <button type="button" class="odo-btn-agregar" onclick="OD_{{ $uid }}.agregarHallazgo()">
                <i class="bi bi-plus-circle"></i> Agregar hallazgo
            </button>
        </div>

        {{-- Tabla --}}
        <div class="odo-hall-tabla-wrap">
            <table class="odo-hall-tabla">
                <thead>
                    <tr>
                        <th>Diagnóstico</th>
                        <th>Procedimiento</th>
                        <th>Pieza</th>
                        <th>Cara</th>
                        <th>Nota</th>
                        <th style="text-align:center;width:60px;"></th>
                    </tr>
                </thead>
                <tbody id="{{ $uid }}-h-tbody">
                    <tr id="{{ $uid }}-h-vacia">
                        <td colspan="6" class="odo-hall-vacia">
                            <i class="bi bi-clipboard2" style="font-size:1.4rem;color:var(--color-acento-activo);display:block;margin-bottom:.3rem;"></i>
                            Sin hallazgos registrados
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Inputs hidden --}}
    <input type="hidden" name="odontograma" id="{{ $uid }}-input" value="">
    @if($esEditable)
    <input type="hidden" name="hallazgos" id="{{ $uid }}-hall-input" value="{{ htmlspecialchars($hallazgosJson, ENT_QUOTES) }}">
    @endif
</div>

<script>
(function(){
var UID = '{{ $uid }}';
var EDITABLE = {{ $esEditable ? 'true' : 'false' }};

var ESTADOS = {
    sano:                { color:'#FFFFFF', borde:'#9ca3af',  label:'Sano' },
    caries:              { color:'#FFC107', borde:'#d97706',  label:'Caries' },
    restaurado_resina:   { color:'#17A2B8', borde:'#0e7490',  label:'Rest. Resina' },
    restaurado_amalgama: { color:'#495057', borde:'#1f2937',  label:'Rest. Amalgama' },
    corona:              { color:'#0D6EFD', borde:'#1d4ed8',  label:'Corona' },
    extraccion_indicada: { color:'#DC3545', borde:'#991b1b',  label:'Extracción ind.' },
    extraido:            { color:'#adb5bd', borde:'#6b7280',  label:'Extraído' },
    implante:            { color:'var(--color-principal)', borde:'#4c1d95',  label:'Implante' },
    fractura:            { color:'#FD7E14', borde:'#c2410c',  label:'Fractura' },
    sellante:            { color:'#28A745', borde:'#166534',  label:'Sellante' },
    ausente:             { color:'#F9FAFB', borde:'#9ca3af',  label:'Ausente' },
};
var COMPLETOS = ['extraido','implante','ausente'];

var ADULTO = {
    sup_der: [18,17,16,15,14,13,12,11],
    sup_izq: [21,22,23,24,25,26,27,28],
    inf_izq: [31,32,33,34,35,36,37,38],
    inf_der: [48,47,46,45,44,43,42,41],
};
var INFANTIL = {
    sup_der: [55,54,53,52,51],
    sup_izq: [61,62,63,64,65],
    inf_izq: [71,72,73,74,75],
    inf_der: [85,84,83,82,81],
};

var data = { tipo:'adulto', dientes:{} };
var selMultiple = false;
var seleccionados = [];
var panelNum = null;
var panelSup = null;
var estadoMultiple = null;

// ── Inicializar ────────────────────────────────────────────────
var rawDatos = {!! $datosJson !!};
if (rawDatos) {
    if (rawDatos.tipo && rawDatos.dientes) {
        // Formato nuevo
        data = rawDatos;
    } else if (typeof rawDatos === 'object') {
        // Formato antiguo plano {"46": "caries"}
        data.dientes = {};
        Object.keys(rawDatos).forEach(function(num) {
            var v = rawDatos[num];
            if (typeof v === 'string') {
                data.dientes[num] = { estado_completo: v };
            } else if (typeof v === 'object') {
                data.dientes[num] = v;
            }
        });
    }
}

// ── Render ─────────────────────────────────────────────────────
function getTablaDientes() {
    return data.tipo === 'infantil' ? INFANTIL : ADULTO;
}

function render() {
    var T = getTablaDientes();
    var html = '';

    html += '<div class="odo-label-arcada">ARCADA SUPERIOR (Derecha → Izquierda)</div>';
    html += '<div class="odo-arcada">';
    html += renderCuadrante(T.sup_der);
    html += '<div class="odo-separador-v"></div>';
    html += renderCuadrante(T.sup_izq);
    html += '</div>';

    html += '<div class="odo-linea-h"></div>';

    html += '<div class="odo-arcada inferior">';
    html += renderCuadrante(T.inf_der);
    html += '<div class="odo-separador-v"></div>';
    html += renderCuadrante(T.inf_izq);
    html += '</div>';
    html += '<div class="odo-label-arcada">ARCADA INFERIOR (Derecha → Izquierda)</div>';

    document.getElementById(UID+'-tablero').innerHTML = html;

    // Actualizar visual de todos los dientes
    var todos = [].concat(T.sup_der, T.sup_izq, T.inf_izq, T.inf_der);
    todos.forEach(function(n){ actualizarVisual(n); });

    if (EDITABLE) adjuntarEventos();
    renderLeyenda();
    serializarJSON();
}

function renderCuadrante(nums) {
    var h = '<div class="odo-cuadrante">';
    nums.forEach(function(n){
        h += '<div class="odo-diente-wrap" id="'+UID+'-dw-'+n+'" data-num="'+n+'">';
        h += '<div class="odo-nota-dot" id="'+UID+'-dot-'+n+'"></div>';
        h += svgDiente(n);
        h += '<span class="odo-num">'+n+'</span>';
        h += '</div>';
    });
    h += '</div>';
    return h;
}

function svgDiente(n) {
    return '<svg class="odo-diente-svg" id="'+UID+'-svg-'+n+'" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">' +
        '<polygon id="'+UID+'-'+n+'-vestibular" data-num="'+n+'" data-sup="vestibular" points="2,2 98,2 70,30 30,30" fill="#FFFFFF" stroke="#9ca3af" stroke-width="1.5"/>' +
        '<polygon id="'+UID+'-'+n+'-lingual"   data-num="'+n+'" data-sup="lingual"   points="30,70 70,70 98,98 2,98" fill="#FFFFFF" stroke="#9ca3af" stroke-width="1.5"/>' +
        '<polygon id="'+UID+'-'+n+'-mesial"    data-num="'+n+'" data-sup="mesial"    points="2,2 30,30 30,70 2,98"   fill="#FFFFFF" stroke="#9ca3af" stroke-width="1.5"/>' +
        '<polygon id="'+UID+'-'+n+'-distal"    data-num="'+n+'" data-sup="distal"    points="70,30 98,2 98,98 70,70" fill="#FFFFFF" stroke="#9ca3af" stroke-width="1.5"/>' +
        '<polygon id="'+UID+'-'+n+'-oclusal"   data-num="'+n+'" data-sup="oclusal"   points="30,30 70,30 70,70 30,70" fill="#FFFFFF" stroke="#9ca3af" stroke-width="1.5"/>' +
        '<line id="'+UID+'-'+n+'-x1" x1="12" y1="12" x2="88" y2="88" stroke="#6b7280" stroke-width="3" stroke-linecap="round" display="none"/>' +
        '<line id="'+UID+'-'+n+'-x2" x1="88" y1="12" x2="12" y2="88" stroke="#6b7280" stroke-width="3" stroke-linecap="round" display="none"/>' +
        '</svg>';
}

function actualizarVisual(n) {
    var d = data.dientes[n] || {};
    var sups = ['vestibular','lingual','mesial','distal','oclusal'];

    if (d.estado_completo && ESTADOS[d.estado_completo]) {
        var e = ESTADOS[d.estado_completo];
        sups.forEach(function(s){
            var p = document.getElementById(UID+'-'+n+'-'+s);
            if (p) { p.setAttribute('fill', e.color); p.setAttribute('stroke', e.borde); }
        });
        var x1 = document.getElementById(UID+'-'+n+'-x1');
        var x2 = document.getElementById(UID+'-'+n+'-x2');
        var showX = (d.estado_completo === 'extraido' || d.estado_completo === 'ausente');
        if (x1) x1.setAttribute('display', showX ? 'block' : 'none');
        if (x2) x2.setAttribute('display', showX ? 'block' : 'none');
        if (showX && x1) {
            x1.setAttribute('stroke', d.estado_completo === 'ausente' ? '#9ca3af' : '#374151');
            x2.setAttribute('stroke', d.estado_completo === 'ausente' ? '#9ca3af' : '#374151');
        }
    } else {
        sups.forEach(function(s){
            var p = document.getElementById(UID+'-'+n+'-'+s);
            if (!p) return;
            var est = d[s] || 'sano';
            var e = ESTADOS[est] || ESTADOS.sano;
            p.setAttribute('fill', e.color);
            p.setAttribute('stroke', e.borde);
        });
        var x1 = document.getElementById(UID+'-'+n+'-x1');
        var x2 = document.getElementById(UID+'-'+n+'-x2');
        if (x1) x1.setAttribute('display', 'none');
        if (x2) x2.setAttribute('display', 'none');
    }

    // Punto de nota
    var dot = document.getElementById(UID+'-dot-'+n);
    if (dot) dot.style.display = d.nota ? 'block' : 'none';
}

// ── Eventos ────────────────────────────────────────────────────
function adjuntarEventos() {
    var tablero = document.getElementById(UID+'-tablero');
    if (!tablero) return;
    tablero.addEventListener('click', function(e) {
        var poly = e.target.closest('polygon[data-num]');
        if (poly) {
            e.stopPropagation();
            var num = parseInt(poly.getAttribute('data-num'));
            var sup = poly.getAttribute('data-sup');
            // Autoseleccionar en form hallazgos
            autoseleccionarHallazgo(num, sup);
            if (selMultiple) {
                toggleSeleccion(num);
            } else {
                abrirPanel(num, sup, e.clientX, e.clientY);
            }
        }
    });
}

function toggleSeleccion(num) {
    var idx = seleccionados.indexOf(num);
    var dw = document.getElementById(UID+'-dw-'+num);
    if (idx === -1) {
        seleccionados.push(num);
        if (dw) dw.classList.add('seleccionado');
    } else {
        seleccionados.splice(idx, 1);
        if (dw) dw.classList.remove('seleccionado');
    }
}

// ── Panel ──────────────────────────────────────────────────────
function abrirPanel(num, sup, cx, cy) {
    panelNum = num;
    panelSup = sup;

    var panel = document.getElementById(UID+'-panel');
    var titulo = document.getElementById(UID+'-panel-titulo');
    var supNombres = {vestibular:'Vestibular',lingual:'Lingual',mesial:'Mesial',distal:'Distal',oclusal:'Oclusal'};
    titulo.textContent = 'Diente ' + num + ' — ' + (supNombres[sup] || sup);

    // Estados grid
    var grid = document.getElementById(UID+'-panel-estados');
    var d = data.dientes[num] || {};
    var estActual = d.estado_completo || d[sup] || 'sano';
    var html = '';
    Object.keys(ESTADOS).forEach(function(k) {
        var e = ESTADOS[k];
        var activo = (k === estActual) ? ' activo' : '';
        html += '<button type="button" class="odo-estado-btn'+activo+'" data-estado="'+k+'" onclick="OD_'+UID+'.setEstado(\''+k+'\')" >' +
            '<span class="odo-estado-dot" style="background:'+e.color+';border-color:'+e.borde+';"></span>' +
            '<span>'+e.label+'</span></button>';
    });
    grid.innerHTML = html;

    // Nota
    var notaEl = document.getElementById(UID+'-panel-nota');
    if (notaEl) notaEl.value = d.nota || '';

    // Posicionar panel
    panel.style.display = 'block';
    panel.classList.add('visible');

    var pw = panel.offsetWidth || 260;
    var ph = panel.offsetHeight || 280;
    var vw = window.innerWidth;
    var vh = window.innerHeight;
    var left = cx + 10;
    var top  = cy + 10;
    if (left + pw > vw - 8) left = cx - pw - 10;
    if (top  + ph > vh - 8) top  = cy - ph - 10;
    if (left < 4) left = 4;
    if (top  < 4) top  = 4;
    panel.style.left = left + 'px';
    panel.style.top  = top  + 'px';

    // Mover panel al body
    document.body.appendChild(panel);
}

function cerrarPanel() {
    var panel = document.getElementById(UID+'-panel');
    if (panel) {
        panel.classList.remove('visible');
        panel.style.display = 'none';
        // Devolver al contenedor
        var wrap = document.getElementById(UID);
        if (wrap) wrap.appendChild(panel);
    }
    panelNum = null;
    panelSup = null;
}

function setEstado(estado) {
    if (panelNum === null) return;

    // Selección múltiple con estado
    if (selMultiple && seleccionados.length > 0) {
        seleccionados.forEach(function(n){
            aplicarEstadoADiente(n, panelSup, estado);
        });
        // Limpiar selección
        seleccionados.forEach(function(n){
            var dw = document.getElementById(UID+'-dw-'+n);
            if (dw) dw.classList.remove('seleccionado');
        });
        seleccionados = [];
        cerrarPanel();
        serializarJSON();
        return;
    }

    aplicarEstadoADiente(panelNum, panelSup, estado);
    cerrarPanel();
    serializarJSON();
}

function aplicarEstadoADiente(num, sup, estado) {
    if (!data.dientes[num]) data.dientes[num] = {};
    if (COMPLETOS.includes(estado)) {
        data.dientes[num] = { estado_completo: estado, nota: data.dientes[num].nota || undefined };
        if (!data.dientes[num].nota) delete data.dientes[num].nota;
    } else if (estado === 'corona') {
        data.dientes[num] = { estado_completo: 'corona', nota: data.dientes[num].nota || undefined };
        if (!data.dientes[num].nota) delete data.dientes[num].nota;
    } else {
        delete data.dientes[num].estado_completo;
        data.dientes[num][sup] = estado;
        if (estado === 'sano') {
            delete data.dientes[num][sup];
        }
    }
    actualizarVisual(num);
}

function limpiarSuperficie() {
    if (panelNum === null) return;
    if (!data.dientes[panelNum]) { cerrarPanel(); return; }
    delete data.dientes[panelNum][panelSup];
    delete data.dientes[panelNum].estado_completo;
    // Si quedó vacío
    var keys = Object.keys(data.dientes[panelNum]).filter(function(k){ return k !== 'nota'; });
    if (keys.length === 0 && !data.dientes[panelNum].nota) {
        delete data.dientes[panelNum];
    }
    actualizarVisual(panelNum);
    cerrarPanel();
    serializarJSON();
}

function aplicarTodoDiente() {
    // Abre un segundo panel o usa el estado actualmente seleccionado
    // Por simplicidad: marca todos las superficies con el estado activo en el panel
    if (panelNum === null) return;
    var activo = document.querySelector('#'+UID+'-panel-estados .odo-estado-btn.activo');
    if (!activo) { cerrarPanel(); return; }
    var estado = activo.getAttribute('data-estado');
    var sups = ['vestibular','lingual','mesial','distal','oclusal'];
    if (!data.dientes[panelNum]) data.dientes[panelNum] = {};
    if (COMPLETOS.includes(estado) || estado === 'corona') {
        var nota = data.dientes[panelNum].nota;
        data.dientes[panelNum] = { estado_completo: estado };
        if (nota) data.dientes[panelNum].nota = nota;
    } else {
        delete data.dientes[panelNum].estado_completo;
        sups.forEach(function(s){
            if (estado === 'sano') {
                delete data.dientes[panelNum][s];
            } else {
                data.dientes[panelNum][s] = estado;
            }
        });
    }
    actualizarVisual(panelNum);
    cerrarPanel();
    serializarJSON();
}

function guardarNota(val) {
    if (panelNum === null) return;
    if (!data.dientes[panelNum]) data.dientes[panelNum] = {};
    if (val.trim()) {
        data.dientes[panelNum].nota = val.trim();
    } else {
        delete data.dientes[panelNum].nota;
        // limpiar objeto vacío si no tiene otros datos
        var keys = Object.keys(data.dientes[panelNum]).filter(function(k){ return k !== 'nota'; });
        if (keys.length === 0) delete data.dientes[panelNum];
    }
    actualizarVisual(panelNum);
    serializarJSON();
}

// ── Controles globales ─────────────────────────────────────────
function toggleTipo(tipo) {
    data.tipo = tipo;
    data.dientes = {};
    var btnA = document.getElementById(UID+'-btn-adulto');
    var btnI = document.getElementById(UID+'-btn-infantil');
    if (btnA) { btnA.classList.toggle('tipo-act', tipo==='adulto'); btnA.classList.toggle('odo-btn', true); }
    if (btnI) { btnI.classList.toggle('tipo-act', tipo==='infantil'); btnI.classList.toggle('odo-btn', true); }
    seleccionados = [];
    render();
    actualizarSelectPiezas();
}

function toggleMultiple() {
    selMultiple = !selMultiple;
    seleccionados = [];
    var btn = document.getElementById(UID+'-btn-multiple');
    if (btn) btn.classList.toggle('activo', selMultiple);
    // Quitar clase seleccionado de todos los dientes
    document.querySelectorAll('#'+UID+'-tablero .odo-diente-wrap').forEach(function(el){
        el.classList.remove('seleccionado');
    });
}

function limpiarTodo() {
    if (!confirm('¿Limpiar todo el odontograma? Esta acción no se puede deshacer.')) return;
    data.dientes = {};
    render();
}

// ── Serialización ─────────────────────────────────────────────
function serializarJSON() {
    var el = document.getElementById(UID+'-input');
    if (el) el.value = JSON.stringify(data);
}

// ── Leyenda ───────────────────────────────────────────────────
function renderLeyenda() {
    var html = '';
    Object.keys(ESTADOS).forEach(function(k){
        var e = ESTADOS[k];
        html += '<div class="odo-ley-item">' +
            '<div class="odo-ley-dot" style="background:'+e.color+';border-color:'+e.borde+';"></div>' +
            '<span>'+e.label+'</span></div>';
    });
    var el = document.getElementById(UID+'-leyenda');
    if (el) el.innerHTML = html;
}

// Cerrar panel al clic fuera
document.addEventListener('click', function(e) {
    var panel = document.getElementById(UID+'-panel');
    if (!panel || !panel.classList.contains('visible')) return;
    if (!panel.contains(e.target) && !e.target.closest('#'+UID+'-tablero')) {
        cerrarPanel();
    }
});

// ── HALLAZGOS ─────────────────────────────────────────────────
var CIE10 = [
    {codigo:'K02.0', nombre:'Caries limitada al esmalte'},
    {codigo:'K02.1', nombre:'Caries de la dentina'},
    {codigo:'K02.2', nombre:'Caries del cemento'},
    {codigo:'K02.3', nombre:'Caries dentaria detenida'},
    {codigo:'K04.0', nombre:'Pulpitis'},
    {codigo:'K04.1', nombre:'Necrosis de la pulpa'},
    {codigo:'K04.5', nombre:'Periodontitis apical crónica'},
    {codigo:'K05.0', nombre:'Gingivitis aguda'},
    {codigo:'K05.1', nombre:'Gingivitis crónica'},
    {codigo:'K05.2', nombre:'Periodontitis aguda'},
    {codigo:'K05.3', nombre:'Periodontitis crónica'},
    {codigo:'K08.1', nombre:'Pérdida de dientes por accidente'},
    {codigo:'K08.2', nombre:'Atrofia del maxilar sin dientes'},
    {codigo:'S02.5', nombre:'Fractura del diente'},
    {codigo:'Z29.8', nombre:'Sellante preventivo'},
];
var acIdx = -1; // índice resaltado en autocomplete
var hallazgos = (function(){
    var raw = {!! $hallazgosJson !!};
    return (Array.isArray(raw) && raw.length > 0) ? raw : [];
}());

function actualizarSelectPiezas() {
    var sel = document.getElementById(UID+'-h-pieza');
    if (!sel) return;
    var T = getTablaDientes();
    var todos = [].concat(T.sup_der, T.sup_izq, T.inf_izq, T.inf_der).sort(function(a,b){return a-b;});
    var prev = sel.value;
    sel.innerHTML = '<option value="">— Selecciona una pieza —</option>';
    todos.forEach(function(n){
        var opt = document.createElement('option');
        opt.value = n;
        opt.textContent = 'Pieza ' + n;
        sel.appendChild(opt);
    });
    if (prev) sel.value = prev;
}

function autoseleccionarHallazgo(num, sup) {
    var selP = document.getElementById(UID+'-h-pieza');
    var selC = document.getElementById(UID+'-h-cara');
    if (selP) selP.value = num;
    if (selC) {
        var mapa = {vestibular:'Vestibular',lingual:'Lingual',mesial:'Mesial',distal:'Distal',oclusal:'Oclusal'};
        selC.value = mapa[sup] || '';
    }
    // Scroll suave al panel
    var hall = document.getElementById(UID+'-hallazgos');
    if (hall) hall.scrollIntoView({behavior:'smooth', block:'nearest'});
}

function toggleAusente(checked) {
    var cara = document.getElementById(UID+'-h-cara');
    var proc = document.getElementById(UID+'-h-proc');
    if (cara) cara.disabled = checked;
    if (proc) proc.disabled = checked;
}

// Autocomplete CIE-10
function filtrarDiag(q) {
    acIdx = -1;
    var dd = document.getElementById(UID+'-h-diag-dd');
    document.getElementById(UID+'-h-diag-cod').value = '';
    document.getElementById(UID+'-h-diag-nom').value = '';
    if (!q || q.length < 1) { dd.classList.remove('abierto'); return; }
    var res = CIE10.filter(function(d){
        return d.codigo.toLowerCase().includes(q.toLowerCase()) ||
               d.nombre.toLowerCase().includes(q.toLowerCase());
    });
    if (res.length === 0) { dd.classList.remove('abierto'); return; }
    dd.innerHTML = res.map(function(d, i){
        return '<div class="odo-ac-item" data-cod="'+d.codigo+'" data-nom="'+escH(d.nombre)+'" data-i="'+i+'" onmousedown="OD_'+UID+'.selDiag(\''+escH(d.codigo)+'\',\''+escH(d.nombre)+'\')">' +
            '<span class="odo-ac-codigo">'+d.codigo+'</span>'+d.nombre+'</div>';
    }).join('');
    dd.classList.add('abierto');
}

function navDiag(e) {
    var dd = document.getElementById(UID+'-h-diag-dd');
    var items = dd.querySelectorAll('.odo-ac-item');
    if (!dd.classList.contains('abierto') || items.length === 0) return;
    if (e.key === 'ArrowDown') { e.preventDefault(); acIdx = Math.min(acIdx+1, items.length-1); resaltarAc(items); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); acIdx = Math.max(acIdx-1, 0); resaltarAc(items); }
    else if (e.key === 'Enter' && acIdx >= 0) { e.preventDefault(); var it = items[acIdx]; selDiag(it.getAttribute('data-cod'), it.getAttribute('data-nom')); }
    else if (e.key === 'Escape') { dd.classList.remove('abierto'); }
}
function resaltarAc(items) {
    items.forEach(function(it, i){ it.classList.toggle('resaltado', i===acIdx); });
    if (acIdx >= 0) items[acIdx].scrollIntoView({block:'nearest'});
}
function selDiag(cod, nom) {
    document.getElementById(UID+'-h-diag-txt').value = cod + ' — ' + nom;
    document.getElementById(UID+'-h-diag-cod').value = cod;
    document.getElementById(UID+'-h-diag-nom').value = nom;
    var dd = document.getElementById(UID+'-h-diag-dd');
    if (dd) dd.classList.remove('abierto');
}

function escH(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

document.addEventListener('click', function(e) {
    var dd = document.getElementById(UID+'-h-diag-dd');
    var inp = document.getElementById(UID+'-h-diag-txt');
    if (dd && inp && !dd.contains(e.target) && e.target !== inp) {
        dd.classList.remove('abierto');
    }
});

// Mapeo procedimiento → estado odontograma
function estadoDesdeHallazgo(proc, ausente) {
    if (ausente) return {tipo:'completo', estado:'ausente'};
    var p = (proc||'').toLowerCase();
    if (p.includes('extracción') || p.includes('extraccion')) return {tipo:'completo', estado:'extraido'};
    if (p.includes('implante'))  return {tipo:'completo', estado:'implante'};
    if (p.includes('corona'))    return {tipo:'completo', estado:'corona'};
    if (p.includes('resina'))    return {tipo:'superficie', estado:'restaurado_resina'};
    if (p.includes('amalgama'))  return {tipo:'superficie', estado:'restaurado_amalgama'};
    if (p.includes('sellante'))  return {tipo:'superficie', estado:'sellante'};
    return null; // sin cambio en odontograma desde procedimiento
}

function estadoDesdeADiag(cod) {
    // Solo diagnósticos de caries marcan la superficie
    if (cod && cod.startsWith('K02')) return {tipo:'superficie', estado:'caries'};
    if (cod && (cod === 'S02.5'))     return {tipo:'superficie', estado:'fractura'};
    return null;
}

function sincronizarOdontograma(pieza, cara, diagCod, proc, ausente) {
    if (!pieza) return;
    var num = parseInt(pieza);
    var sup = (cara||'oclusal').toLowerCase();
    // Prioridad: procedimiento > diagnóstico
    var acc = estadoDesdeHallazgo(proc, ausente) || estadoDesdeADiag(diagCod);
    if (!acc) return;
    if (!data.dientes[num]) data.dientes[num] = {};
    if (acc.tipo === 'completo') {
        var nota = data.dientes[num].nota;
        data.dientes[num] = {estado_completo: acc.estado};
        if (nota) data.dientes[num].nota = nota;
    } else {
        delete data.dientes[num].estado_completo;
        data.dientes[num][sup] = acc.estado;
    }
    actualizarVisual(num);
    serializarJSON();
}

function agregarHallazgo() {
    var pieza   = document.getElementById(UID+'-h-pieza').value;
    var cara    = document.getElementById(UID+'-h-cara').value;
    var diagCod = document.getElementById(UID+'-h-diag-cod').value;
    var diagNom = document.getElementById(UID+'-h-diag-nom').value;
    var proc    = document.getElementById(UID+'-h-proc').value;
    var ausente = document.getElementById(UID+'-h-ausente').checked;
    var diagTxt = document.getElementById(UID+'-h-diag-txt').value.trim();

    // Si escribió texto libre sin seleccionar de la lista, usarlo
    if (!diagCod && diagTxt) { diagNom = diagTxt; }

    if (!pieza) { alert('Selecciona la pieza dental.'); return; }
    if (!diagCod && !diagNom) { alert('Ingresa el diagnóstico.'); return; }

    hallazgos.push({
        pieza: pieza,
        cara: ausente ? '' : (cara || ''),
        diagnostico_codigo: diagCod,
        diagnostico_nombre: diagNom,
        procedimiento: ausente ? '' : (proc || ''),
        ausente: ausente,
        nota: ''
    });

    sincronizarOdontograma(pieza, cara, diagCod, proc, ausente);
    renderTablaHallazgos();
    serializarHallazgos();

    // Limpiar form
    document.getElementById(UID+'-h-pieza').value = '';
    document.getElementById(UID+'-h-cara').value = '';
    document.getElementById(UID+'-h-diag-txt').value = '';
    document.getElementById(UID+'-h-diag-cod').value = '';
    document.getElementById(UID+'-h-diag-nom').value = '';
    document.getElementById(UID+'-h-proc').value = '';
    document.getElementById(UID+'-h-ausente').checked = false;
    toggleAusente(false);
}

function eliminarHallazgo(i) {
    hallazgos.splice(i, 1);
    renderTablaHallazgos();
    serializarHallazgos();
}

function guardarNotaHallazgo(i, val) {
    if (hallazgos[i]) hallazgos[i].nota = val;
    serializarHallazgos();
}

function renderTablaHallazgos() {
    var tbody = document.getElementById(UID+'-h-tbody');
    var vacia = document.getElementById(UID+'-h-vacia');
    if (!tbody) return;
    // Limpiar filas de datos (no la de vacío)
    Array.from(tbody.querySelectorAll('tr[data-idx]')).forEach(function(r){ r.remove(); });

    if (hallazgos.length === 0) {
        if (vacia) vacia.style.display = '';
        return;
    }
    if (vacia) vacia.style.display = 'none';

    hallazgos.forEach(function(h, i) {
        var tr = document.createElement('tr');
        tr.setAttribute('data-idx', i);
        var diagText = h.diagnostico_codigo
            ? '<span class="odo-ac-codigo">'+escH(h.diagnostico_codigo)+'</span>' + escH(h.diagnostico_nombre)
            : escH(h.diagnostico_nombre);
        tr.innerHTML =
            '<td>'+diagText+'</td>' +
            '<td style="font-size:.78rem;color:#4b5563;">'+escH(h.procedimiento||'—')+'</td>' +
            '<td><span class="odo-hall-badge-pieza">'+escH(h.pieza)+'</span></td>' +
            '<td>'+(h.ausente ? '<span style="color:#dc2626;font-size:.75rem;font-weight:600;">Ausente</span>' : (h.cara ? '<span class="odo-hall-badge-cara">'+escH(h.cara)+'</span>' : '—'))+'</td>' +
            '<td><input type="text" class="odo-hall-nota-inline" placeholder="Nota..." value="'+escH(h.nota||'')+'" oninput="OD_'+UID+'.guardarNotaHallazgo('+i+',this.value)"></td>' +
            '<td style="text-align:center;">' +
                '<button type="button" class="odo-hall-accion del" title="Eliminar" onclick="OD_'+UID+'.eliminarHallazgo('+i+')">' +
                    '<i class="bi bi-trash3"></i>' +
                '</button>' +
            '</td>';
        tbody.appendChild(tr);
    });
}

function serializarHallazgos() {
    var el = document.getElementById(UID+'-hall-input');
    if (el) el.value = JSON.stringify(hallazgos);
}

window['OD_'+UID] = {
    toggleTipo: toggleTipo,
    toggleMultiple: toggleMultiple,
    limpiarTodo: limpiarTodo,
    cerrarPanel: cerrarPanel,
    setEstado: setEstado,
    limpiarSuperficie: limpiarSuperficie,
    aplicarTodoDiente: aplicarTodoDiente,
    guardarNota: guardarNota,
    // Hallazgos
    filtrarDiag: filtrarDiag,
    navDiag: navDiag,
    selDiag: selDiag,
    toggleAusente: toggleAusente,
    agregarHallazgo: agregarHallazgo,
    eliminarHallazgo: eliminarHallazgo,
    guardarNotaHallazgo: guardarNotaHallazgo,
};

// Inicializar
render();
if (EDITABLE) {
    actualizarSelectPiezas();
    if (hallazgos.length > 0) renderTablaHallazgos();
}

// Actualizar botón tipo si datos iniciales son infantil
if (data.tipo === 'infantil') {
    var btnA = document.getElementById(UID+'-btn-adulto');
    var btnI = document.getElementById(UID+'-btn-infantil');
    if (btnA) btnA.classList.remove('tipo-act');
    if (btnI) btnI.classList.add('tipo-act');
}

})();
</script>
