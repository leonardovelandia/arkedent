@extends('layouts.app')
@section('titulo', 'Autorización ' . $autorizacion->numero_autorizacion)

@push('estilos')
<style>
    .doc-card {
        background: white;
        border: 1px solid var(--fondo-borde);
        border-radius: 14px;
        overflow: hidden;
        max-width: 820px;
        margin: 0 auto;
        box-shadow: 0 8px 28px var(--sombra-principal), 0 2px 8px rgba(0,0,0,0.12);
    }
    .doc-header { background: var(--color-principal); padding: 1.5rem 2rem; text-align: center; }
    .doc-body { padding: 2rem; }

    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.55rem 1.25rem; font-size:.875rem; font-weight:600; display:inline-flex; align-items:center; gap:.4rem; cursor:pointer; transition:filter .15s; text-decoration:none; }
    .btn-morado:hover { filter:brightness(1.1); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.55rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; transition:background .15s; }
    .btn-gris:hover { background:#e5e7eb; }
    .firma-canvas-wrap { position:relative; border:2px dashed var(--color-principal); border-radius:8px; background:white; overflow:hidden; }
    .firma-hint { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-size:.78rem; color:#c0b8d0; pointer-events:none; text-align:center; }

    /* Contenido legal (estilo similar al PDF) */
    .doc-intro { font-size: .88rem; line-height: 1.75; color: #2d3748; margin-bottom: 1rem; }
    .doc-seccion-titulo { font-size: .78rem; font-weight: 700; color: var(--color-principal); text-transform: uppercase; letter-spacing: .07em; margin: 1.25rem 0 .6rem; border-bottom: 1px solid var(--color-muy-claro); padding-bottom: .35rem; }
    .check-item { display: flex; align-items: flex-start; gap: .55rem; margin-bottom: .45rem; font-size: .86rem; color: #374151; }
    .check-mark { flex-shrink: 0; width: 17px; height: 17px; border: 1.5px solid var(--color-principal); border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: .7rem; font-weight: 700; margin-top: 1px; }
    .check-mark.activo { background: var(--color-principal); color: white; }
    .derechos-box { background: #EEF3F9; border-left: 3px solid var(--color-principal); padding: .75rem 1rem; border-radius: 0 8px 8px 0; margin: 1rem 0; }
    .derechos-box li { font-size: .82rem; color: #374151; margin-bottom: .3rem; }
    .doc-fecha { font-size: .83rem; color: #5c6b62; margin: 1rem 0 .5rem; }
    .doc-divider { border: none; border-top: 1px solid var(--color-muy-claro); margin: 1.25rem 0; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;max-width:820px;margin:0 auto 1rem;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

<div class="doc-card">

    {{-- Encabezado --}}
    <div class="doc-header">
        <div style="font-size:1.1rem;font-weight:700;color:white;">{{ $config->nombre_consultorio }}</div>
        @if($config->nit)<div style="font-size:.8rem;color:rgba(255,255,255,.7);">NIT: {{ $config->nit }}</div>@endif
        <div style="font-size:1rem;font-weight:500;color:white;margin-top:.5rem;letter-spacing:.05em;">
            AUTORIZACIÓN DE TRATAMIENTO DE DATOS PERSONALES
        </div>
        <div style="font-size:.72rem;color:rgba(255,255,255,.55);margin-top:.2rem;">
            Ley 1581 de 2012 — Decreto 1377 de 2013 — Colombia
        </div>
        <div style="font-size:.75rem;color:rgba(255,255,255,.6);margin-top:.35rem;">
            {{ $autorizacion->numero_autorizacion }} ·
            @if($autorizacion->firmado)
                <span style="background:rgba(74,222,128,.3);padding:2px 10px;border-radius:50px;">Firmada digitalmente</span>
            @else
                <span style="background:rgba(251,191,36,.3);padding:2px 10px;border-radius:50px;">Pendiente de firma</span>
            @endif
        </div>
    </div>

    <div class="doc-body">

        {{-- Párrafo introductorio --}}
        <p class="doc-intro">
            Yo, <strong>{{ $autorizacion->paciente->nombre_completo }}</strong>,
            identificado(a) con <strong>{{ $autorizacion->paciente->tipo_documento }} N° {{ $autorizacion->paciente->numero_documento }}</strong>,
            en pleno uso de mis facultades mentales, de manera libre, voluntaria, previa, expresa e informada,
            autorizo a <strong>{{ $config->nombre_consultorio }}</strong>@if($config->nit), identificado con NIT {{ $config->nit }},@endif
            con domicilio en {{ $config->direccion ?? 'Colombia' }},
            para que realice el tratamiento de mis datos personales de acuerdo con lo establecido en la Ley 1581 de 2012 y el Decreto 1377 de 2013.
        </p>

        {{-- Autorizaciones --}}
        <div class="doc-seccion-titulo"><i class="bi bi-check2-square"></i> Autorizo expresamente las siguientes actividades:</div>
        @php
            $permisos = [
                'acepta_almacenamiento'      => 'Recolección y almacenamiento de mis datos personales para fines médicos y odontológicos.',
                'acepta_contacto_whatsapp'   => 'Autorizo el envío de recordatorios, notificaciones y comunicaciones relacionadas con mis citas médicas y seguimientos a través de WhatsApp. Entiendo que puedo cancelar esta autorización en cualquier momento solicitándolo directamente al consultorio.',
                'acepta_contacto_email'      => 'Contacto por correo electrónico para confirmaciones y comunicaciones.',
                'acepta_contacto_llamada'    => 'Contacto telefónico para confirmación de citas y seguimiento.',
                'acepta_recordatorios'       => 'Envío de recordatorios automáticos de citas programadas.',
                'acepta_compartir_entidades' => 'Compartir información con entidades de salud cuando sea necesario para mi atención.',
            ];
        @endphp
        @foreach($permisos as $campo => $texto)
        <div class="check-item">
            <span class="check-mark {{ $autorizacion->$campo ? 'activo' : '' }}">
                @if($autorizacion->$campo)<i class="bi bi-check"></i>@endif
            </span>
            <span>{{ $texto }}</span>
        </div>
        @endforeach

        {{-- Derechos --}}
        <div class="derechos-box" style="margin-top:1rem;">
            <div style="font-size:.78rem;font-weight:700;color:var(--color-principal);margin-bottom:.4rem;">Derechos como titular de datos personales:</div>
            <ul style="padding-left:1.1rem;margin:0;">
                <li>Conocer, actualizar y rectificar mis datos personales</li>
                <li>Solicitar prueba de la autorización otorgada y revocarla en cualquier momento</li>
                <li>Ser informado sobre el uso de mis datos y acceder gratuitamente a ellos</li>
                <li>Presentar quejas ante la Superintendencia de Industria y Comercio</li>
            </ul>
        </div>

        {{-- Declaración --}}
        <p class="doc-intro" style="margin-top:1rem;">
            Declaro que he leído y comprendido el presente documento, que he sido informado(a) sobre el tratamiento
            que se dará a mis datos personales y que otorgo mi consentimiento de manera libre, voluntaria y sin ningún tipo de presión.
        </p>

        <p class="doc-fecha">
            Firmado en {{ $config->ciudad ?? 'Colombia' }},
            el {{ $autorizacion->fecha_autorizacion->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
        </p>

        <hr class="doc-divider">

        {{-- Firma del paciente --}}
        @if($autorizacion->firmado && $autorizacion->firma_data)
        <div style="margin-bottom:1.25rem;">
            <p style="font-size:.78rem;font-weight:600;color:#5c6b62;margin-bottom:.5rem;text-transform:uppercase;letter-spacing:.06em;">Firma del paciente</p>
            <div style="display:inline-block;background:#fafafa;border:1px solid #e0d0f0;border-radius:8px;padding:.75rem 1.25rem;text-align:center;">
                <img src="{{ $autorizacion->firma_data }}" alt="Firma"
                     style="max-height:80px;max-width:220px;display:block;margin:0 auto .5rem;">
                <div style="border-top:1px solid #333;padding-top:4px;font-size:.75rem;color:#555;">
                    {{ $autorizacion->paciente->nombre_completo }}<br>
                    Firmado el {{ $autorizacion->fecha_firma?->locale('es')->isoFormat('D MMM YYYY HH:mm') }}
                    @if($autorizacion->ip_firma) · IP: {{ $autorizacion->ip_firma }}@endif
                </div>
            </div>
        </div>
        @elseif(!$autorizacion->firmado)
        {{-- Formulario para agregar firma --}}
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:1.25rem;margin-bottom:1.5rem;">
            <p style="font-size:.85rem;font-weight:600;color:#92400e;margin-bottom:1rem;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                Esta autorización aún no tiene firma digital. El paciente puede firmar ahora:
            </p>
            <form method="POST" action="{{ route('autorizacion.firmar', $autorizacion->uuid) }}" id="form-firmar">
                @csrf
                <input type="hidden" name="firma_data" id="firma-data-firmar">
                <input type="hidden" name="metodo_firma" value="digital">
                <div style="max-width:400px;">
                    <div class="firma-canvas-wrap">
                        <canvas id="canvas-firma-show" style="display:block;width:100%;height:130px;cursor:crosshair;touch-action:none;"></canvas>
                        <div class="firma-hint" id="hint-firma-show">
                            <i class="bi bi-pencil-square" style="font-size:1.2rem;display:block;margin-bottom:.2rem;"></i>
                            Dibuja la firma aquí
                        </div>
                    </div>
                    <div style="display:flex;gap:.5rem;margin-top:.5rem;">
                        <button type="button" onclick="limpiarFirmaShow()"
                            style="font-size:.75rem;padding:4px 12px;border:1px solid #ccc;border-radius:6px;background:white;cursor:pointer;color:#666;">
                            <i class="bi bi-eraser"></i> Limpiar
                        </button>
                        <button type="button" class="btn-morado" onclick="guardarFirmaShow()" style="font-size:.8rem;padding:.35rem .9rem;">
                            <i class="bi bi-pen-fill"></i> Registrar firma
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endif

        {{-- Meta --}}
        <div style="display:flex;gap:2rem;flex-wrap:wrap;font-size:.8rem;color:#5c6b62;border-top:1px solid var(--color-muy-claro);padding-top:1rem;">
            <div><strong>Fecha:</strong> {{ $autorizacion->fecha_autorizacion->locale('es')->isoFormat('D MMM YYYY') }}</div>
            <div><strong>Registrado por:</strong> {{ $autorizacion->registradoPor?->name ?? 'Sistema' }}</div>
            <div><strong>Número:</strong> {{ $autorizacion->numero_autorizacion }}</div>
        </div>

        @if($autorizacion->observaciones)
        <div style="margin-top:1rem;font-size:.85rem;color:#5c6b62;">
            <strong>Observaciones:</strong> {{ $autorizacion->observaciones }}
        </div>
        @endif

    </div>
</div>

{{-- Botones de acción --}}
<div style="display:flex;gap:.75rem;justify-content:center;margin-top:1.25rem;max-width:820px;margin-left:auto;margin-right:auto;flex-wrap:wrap;">
    <a href="{{ route('autorizacion.pdf', $autorizacion->uuid) }}" target="_blank" class="btn-morado">
        <i class="bi bi-file-earmark-pdf"></i> Descargar PDF
    </a>
    <a href="{{ route('pacientes.show', $autorizacion->paciente_id) }}" class="btn-gris">
        <i class="bi bi-arrow-left"></i> Volver al paciente
    </a>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const canvas = document.getElementById('canvas-firma-show');
    if (!canvas) return;
    const ctx   = canvas.getContext('2d');
    const hint  = document.getElementById('hint-firma-show');
    let drawing = false, hayTrazo = false;

    function escalar() {
        const rect = canvas.getBoundingClientRect();
        canvas.width  = rect.width  * (window.devicePixelRatio || 1);
        canvas.height = rect.height * (window.devicePixelRatio || 1);
        ctx.scale(window.devicePixelRatio || 1, window.devicePixelRatio || 1);
        ctx.strokeStyle = '#1a1a2e'; ctx.lineWidth = 2.2;
        ctx.lineCap = 'round'; ctx.lineJoin = 'round';
    }
    escalar();

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const src  = e.touches ? e.touches[0] : e;
        return { x: src.clientX - rect.left, y: src.clientY - rect.top };
    }

    canvas.addEventListener('mousedown',  function(e){ e.preventDefault(); drawing=true; const p=getPos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); if(hint) hint.style.display='none'; });
    canvas.addEventListener('mousemove',  function(e){ e.preventDefault(); if(!drawing) return; hayTrazo=true; const p=getPos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); });
    canvas.addEventListener('mouseup',    function(e){ e.preventDefault(); drawing=false; ctx.beginPath(); });
    canvas.addEventListener('mouseleave', function(e){ e.preventDefault(); drawing=false; ctx.beginPath(); });
    canvas.addEventListener('touchstart', function(e){ e.preventDefault(); drawing=true; const p=getPos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); if(hint) hint.style.display='none'; },{ passive:false });
    canvas.addEventListener('touchmove',  function(e){ e.preventDefault(); if(!drawing) return; hayTrazo=true; const p=getPos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); },{ passive:false });
    canvas.addEventListener('touchend',   function(e){ e.preventDefault(); drawing=false; ctx.beginPath(); },{ passive:false });

    window.limpiarFirmaShow = function() {
        const rect = canvas.getBoundingClientRect();
        ctx.clearRect(0,0,rect.width,rect.height);
        hayTrazo=false; if(hint) hint.style.display='';
        document.getElementById('firma-data-firmar').value='';
    };
    window.guardarFirmaShow = function() {
        if (!hayTrazo) { alert('Por favor dibuja la firma antes de guardar.'); return; }
        document.getElementById('firma-data-firmar').value = canvas.toDataURL('image/png');
        document.getElementById('form-firmar').submit();
    };
})();
</script>
@endpush
