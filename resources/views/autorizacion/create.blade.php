@extends('layouts.app')
@section('titulo', 'Autorización de Datos Personales')

@push('estilos')
<style>
    .doc-card {
        background: white;
        border: 1px solid #ede9e0;
        border-radius: 14px;
        overflow: hidden;
        max-width: 820px;
        margin: 0 auto;
    }
    .doc-header {
        background: var(--color-principal);
        padding: 1.5rem 2rem;
        text-align: center;
    }
    .doc-body { padding: 2rem; }
    .doc-check-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.6rem 0.75rem;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.12s;
    }
    .doc-check-item:hover { background: var(--color-muy-claro); }
    .doc-check-item input[type=checkbox] {
        width: 18px; height: 18px;
        accent-color: var(--color-principal);
        margin-top: 2px; flex-shrink: 0;
    }
    .doc-check-label { font-size: 0.85rem; color: #1c2b22; line-height: 1.5; }
    .doc-check-label strong { color: var(--color-hover); }
    .derechos-box {
        background: #faf8ff;
        border-left: 3px solid var(--color-principal);
        padding: 1rem 1.25rem;
        border-radius: 0 8px 8px 0;
        margin-bottom: 1.5rem;
    }
    .firma-canvas-wrap {
        position: relative;
        border: 2px dashed var(--color-principal);
        border-radius: 8px;
        background: white;
        overflow: hidden;
    }
    .firma-hint {
        position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        font-size: .78rem; color: #c0b8d0;
        pointer-events: none; text-align: center;
    }
    .btn-morado {
        background: linear-gradient(135deg, var(--color-principal), var(--color-claro));
        color: #fff; border: none; border-radius: 8px;
        padding: 0.6rem 1.5rem; font-size: 0.875rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 0.45rem;
        cursor: pointer; transition: filter 0.15s;
    }
    .btn-morado:hover { filter: brightness(1.1); color: #fff; }
    .btn-outline {
        background: transparent; color: var(--color-principal);
        border: 1.5px solid var(--color-principal); border-radius: 8px;
        padding: 0.6rem 1.5rem; font-size: 0.875rem; font-weight: 500;
        display: inline-flex; align-items: center; gap: 0.45rem;
        cursor: pointer; transition: background 0.15s; text-decoration: none;
    }
    .btn-outline:hover { background: var(--color-muy-claro); }
    .btn-gris {
        background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;
        border-radius: 8px; padding: 0.6rem 1.5rem; font-size: 0.875rem;
        font-weight: 500; display: inline-flex; align-items: center; gap: 0.45rem;
        text-decoration: none; cursor: pointer; transition: background 0.15s;
    }
    .btn-gris:hover { background: #e5e7eb; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;max-width:820px;margin:0 auto 1rem;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

<form method="POST" action="{{ route('autorizacion.store') }}" id="form-autorizacion">
@csrf
<input type="hidden" name="paciente_id" value="{{ $paciente->id }}">
<input type="hidden" name="firma_data"  id="firma-data-input">
<input type="hidden" name="accion"      id="accion-input" value="guardar">

<div class="doc-card">

    {{-- Encabezado --}}
    <div class="doc-header">
        <div style="font-size:1.1rem;font-weight:600;color:white;">{{ $config->nombre_consultorio }}</div>
        @if($config->nit)
        <div style="font-size:0.8rem;color:rgba(255,255,255,0.7);">NIT: {{ $config->nit }}</div>
        @endif
        <div style="font-size:1rem;font-weight:500;color:white;margin-top:0.5rem;letter-spacing:0.05em;">
            AUTORIZACIÓN DE TRATAMIENTO DE DATOS PERSONALES
        </div>
        <div style="font-size:0.75rem;color:rgba(255,255,255,0.6);margin-top:0.25rem;">
            Ley 1581 de 2012 — Decreto 1377 de 2013 — Colombia
        </div>
    </div>

    {{-- Cuerpo --}}
    <div class="doc-body">

        {{-- Texto introductorio --}}
        <p style="font-size:0.9rem;color:#1c2b22;line-height:1.8;margin-bottom:1.25rem;">
            Yo, <strong>{{ $paciente->nombre_completo }}</strong>,
            identificado(a) con
            <strong>{{ $paciente->tipo_documento }} número {{ $paciente->numero_documento }}</strong>,
            en pleno uso de mis facultades mentales, de manera libre, voluntaria,
            previa, expresa e informada, autorizo a
            <strong>{{ $config->nombre_consultorio }}</strong>@if($config->nit), identificado con NIT {{ $config->nit }},@endif
            con domicilio en {{ $config->direccion ?? 'Colombia' }},
            para que realice el tratamiento de mis datos personales
            de acuerdo con lo establecido en la Ley 1581 de 2012 y el Decreto 1377 de 2013.
        </p>

        {{-- Checkboxes --}}
        <div style="margin:1.25rem 0;">
            <p style="font-size:0.85rem;font-weight:600;color:var(--color-principal);margin-bottom:0.75rem;">
                Autorizo expresamente las siguientes actividades:
            </p>
            <div style="display:flex;flex-direction:column;gap:0.25rem;">
                <label class="doc-check-item">
                    <input type="checkbox" name="acepta_almacenamiento" value="1" checked>
                    <span class="doc-check-label">
                        <strong>Recolección y almacenamiento</strong> de mis datos personales,
                        incluyendo datos de salud, para fines exclusivamente médicos y odontológicos.
                    </span>
                </label>
                <label class="doc-check-item">
                    <input type="checkbox" name="acepta_contacto_whatsapp" value="1">
                    <span class="doc-check-label">
                        Autorizo el envío de recordatorios, notificaciones y comunicaciones relacionadas con mis citas médicas y seguimientos a través de WhatsApp.
                        Entiendo que puedo cancelar esta autorización en cualquier momento solicitándolo directamente al consultorio.
                    </span>
                </label>
                <label class="doc-check-item">
                    <input type="checkbox" name="acepta_contacto_email" value="1">
                    <span class="doc-check-label">
                        <strong>Contacto por correo electrónico</strong> para envío de recordatorios,
                        confirmaciones de cita y comunicaciones del consultorio.
                    </span>
                </label>
                <label class="doc-check-item">
                    <input type="checkbox" name="acepta_contacto_llamada" value="1">
                    <span class="doc-check-label">
                        <strong>Contacto telefónico</strong> para confirmación de citas y seguimiento de tratamientos.
                    </span>
                </label>
                <label class="doc-check-item">
                    <input type="checkbox" name="acepta_recordatorios" value="1">
                    <span class="doc-check-label">
                        <strong>Envío de recordatorios automáticos</strong> de citas programadas
                        por los canales autorizados anteriormente.
                    </span>
                </label>
                <label class="doc-check-item">
                    <input type="checkbox" name="acepta_compartir_entidades" value="1">
                    <span class="doc-check-label">
                        <strong>Compartir información con entidades de salud</strong>
                        (EPS, aseguradoras, entidades regulatorias) cuando sea estrictamente necesario para mi atención médica.
                    </span>
                </label>
            </div>
        </div>

        {{-- Derechos --}}
        <div class="derechos-box">
            <p style="font-size:0.8rem;font-weight:600;color:var(--color-principal);margin-bottom:0.5rem;">
                Mis derechos como titular de datos personales:
            </p>
            <ul style="font-size:0.78rem;color:#5c6b62;line-height:1.8;margin:0;padding-left:1.25rem;">
                <li>Conocer, actualizar y rectificar mis datos personales</li>
                <li>Solicitar prueba de la autorización otorgada</li>
                <li>Ser informado sobre el uso que se da a mis datos</li>
                <li>Revocar esta autorización en cualquier momento</li>
                <li>Acceder gratuitamente a mis datos personales</li>
                <li>Presentar quejas ante la Superintendencia de Industria y Comercio</li>
            </ul>
        </div>

        {{-- Texto final --}}
        <p style="font-size:0.85rem;color:#1c2b22;line-height:1.7;margin-bottom:1rem;">
            Declaro que he leído y comprendido el presente documento, que he sido informado(a)
            sobre el tratamiento que se dará a mis datos personales y que otorgo mi consentimiento
            de manera libre, voluntaria y sin ningún tipo de presión.
        </p>
        <div style="font-size:0.85rem;color:#5c6b62;margin-bottom:1.5rem;">
            Firmado en {{ $config->ciudad ?? 'Colombia' }},
            el {{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
        </div>

        {{-- Sección de firmas --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;flex-wrap:wrap;margin-top:1.5rem;">

            {{-- Firma del paciente --}}
            <div>
                <p style="font-size:0.8rem;font-weight:500;color:#5c6b62;margin-bottom:0.5rem;">
                    Firma del paciente:
                </p>
                <div class="firma-canvas-wrap">
                    <canvas id="canvas-firma" style="display:block;width:100%;height:150px;cursor:crosshair;touch-action:none;"></canvas>
                    <div class="firma-hint" id="firma-hint">
                        <i class="bi bi-pencil-square" style="font-size:1.5rem;display:block;margin-bottom:.25rem;"></i>
                        Dibuja tu firma aquí
                    </div>
                </div>
                <div style="margin-top:0.5rem;">
                    <button type="button" onclick="limpiarFirma()"
                        style="font-size:0.75rem;padding:4px 12px;border:1px solid #ccc;border-radius:6px;background:white;cursor:pointer;color:#666;">
                        <i class="bi bi-eraser"></i> Limpiar
                    </button>
                    <span style="font-size:0.72rem;color:#9ca3af;margin-left:0.5rem;">PNG transparente</span>
                </div>
                <div style="border-top:1px solid #333;padding-top:0.4rem;margin-top:0.75rem;">
                    <div style="font-size:0.82rem;font-weight:500;color:#1c2b22;">{{ $paciente->nombre_completo }}</div>
                    <div style="font-size:0.75rem;color:#5c6b62;">{{ $paciente->tipo_documento }}: {{ $paciente->numero_documento }}</div>
                </div>
            </div>

            {{-- Firma del profesional --}}
            <div>
                <p style="font-size:0.8rem;font-weight:500;color:#5c6b62;margin-bottom:0.5rem;">
                    Profesional tratante:
                </p>
                <div style="height:150px;display:flex;align-items:flex-end;">
                    <div style="width:100%;">
                        <div style="border-top:1px solid #333;padding-top:0.4rem;">
                            <div style="font-size:0.82rem;font-weight:500;color:#1c2b22;">{{ auth()->user()->name }}</div>
                            <div style="font-size:0.75rem;color:#5c6b62;">Odontólogo(a) — {{ $config->nombre_consultorio }}</div>
                            @if($config->registro_medico)
                            <div style="font-size:0.72rem;color:#8fa39a;">Reg. {{ $config->registro_medico }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Observaciones --}}
        <div style="margin-top:1.5rem;">
            <label style="font-size:0.8rem;font-weight:500;color:#5c6b62;display:block;margin-bottom:0.4rem;">
                Observaciones (opcional):
            </label>
            <textarea name="observaciones" rows="2"
                style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:0.5rem 0.75rem;font-size:0.85rem;resize:vertical;"
                placeholder="Notas adicionales...">{{ old('observaciones') }}</textarea>
        </div>

    </div>{{-- /doc-body --}}
</div>{{-- /doc-card --}}

{{-- Botones --}}
<div style="display:flex;gap:0.75rem;justify-content:center;margin-top:1.5rem;flex-wrap:wrap;max-width:820px;margin-left:auto;margin-right:auto;">
    <button type="button" class="btn-morado" onclick="guardarYFirmar()">
        <i class="bi bi-pen-fill"></i> Guardar y Firmar digitalmente
    </button>
    <button type="button" class="btn-outline" onclick="guardarSinFirma()">
        <i class="bi bi-printer"></i> Guardar sin firma — imprimir después
    </button>
    <a href="{{ route('pacientes.show', $paciente->id) }}" class="btn-gris">
        <i class="bi bi-x"></i> Cancelar
    </a>
</div>

</form>
@endsection

@push('scripts')
<script>
(function () {
    const canvas  = document.getElementById('canvas-firma');
    const ctx     = canvas.getContext('2d');
    const hint    = document.getElementById('firma-hint');
    let drawing   = false;
    let hayTrazo  = false;

    function escalar() {
        const rect = canvas.getBoundingClientRect();
        canvas.width  = rect.width  * (window.devicePixelRatio || 1);
        canvas.height = rect.height * (window.devicePixelRatio || 1);
        ctx.scale(window.devicePixelRatio || 1, window.devicePixelRatio || 1);
        ctx.strokeStyle = '#1a1a2e';
        ctx.lineWidth   = 2.2;
        ctx.lineCap     = 'round';
        ctx.lineJoin    = 'round';
    }
    escalar();

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const src  = e.touches ? e.touches[0] : e;
        return { x: src.clientX - rect.left, y: src.clientY - rect.top };
    }

    canvas.addEventListener('mousedown', function (e) {
        e.preventDefault(); drawing = true;
        const p = getPos(e);
        ctx.beginPath(); ctx.moveTo(p.x, p.y);
        if (hint) hint.style.display = 'none';
    });
    canvas.addEventListener('mousemove', function (e) {
        e.preventDefault();
        if (!drawing) return;
        hayTrazo = true;
        const p = getPos(e);
        ctx.lineTo(p.x, p.y); ctx.stroke();
    });
    canvas.addEventListener('mouseup',    function (e) { e.preventDefault(); drawing = false; ctx.beginPath(); });
    canvas.addEventListener('mouseleave', function (e) { e.preventDefault(); drawing = false; ctx.beginPath(); });
    canvas.addEventListener('touchstart', function (e) {
        e.preventDefault(); drawing = true;
        const p = getPos(e);
        ctx.beginPath(); ctx.moveTo(p.x, p.y);
        if (hint) hint.style.display = 'none';
    }, { passive: false });
    canvas.addEventListener('touchmove', function (e) {
        e.preventDefault();
        if (!drawing) return;
        hayTrazo = true;
        const p = getPos(e);
        ctx.lineTo(p.x, p.y); ctx.stroke();
    }, { passive: false });
    canvas.addEventListener('touchend', function (e) {
        e.preventDefault(); drawing = false; ctx.beginPath();
    }, { passive: false });

    window.limpiarFirma = function () {
        const rect = canvas.getBoundingClientRect();
        ctx.clearRect(0, 0, rect.width, rect.height);
        hayTrazo = false;
        if (hint) hint.style.display = '';
        document.getElementById('firma-data-input').value = '';
    };

    window.guardarYFirmar = function () {
        if (!hayTrazo) {
            alert('Por favor dibuja la firma del paciente antes de continuar.');
            return;
        }
        document.getElementById('firma-data-input').value = canvas.toDataURL('image/png');
        document.getElementById('accion-input').value = 'firmar';
        document.getElementById('form-autorizacion').submit();
    };

    window.guardarSinFirma = function () {
        document.getElementById('firma-data-input').value = '';
        document.getElementById('accion-input').value = 'guardar';
        document.getElementById('form-autorizacion').submit();
    };
})();
</script>
@endpush
