@extends('layouts.app')
@section('titulo', 'Consentimiento Informado')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    .cons-header { background:linear-gradient(135deg,var(--color-principal) 0%,var(--color-sidebar-2) 60%,var(--color-sidebar) 100%); border-radius:14px; padding:1.5rem 1.75rem; color:#fff; margin-bottom:1.25rem; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem; }
    .cons-header h2 { font-family:var(--fuente-titulos); font-size:1.35rem; font-weight:700; margin:0 0 .2rem; }
    .cons-header-sub { font-size:.85rem; opacity:.8; display:flex; flex-wrap:wrap; gap:.75rem; align-items:center; }

    .badge-firmado   { background:#D4EDDA; color:#155724; }
    .badge-pendiente { background:#FFF3CD; color:#856404; }
    .badge-estado { display:inline-flex; align-items:center; gap:.3rem; padding:.28rem .75rem; border-radius:20px; font-size:.78rem; font-weight:700; }

    .info-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:1.25rem 1.5rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1rem; }
    .info-card-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-hover); margin-bottom:.9rem; display:flex; align-items:center; gap:.4rem; }

    .contenido-texto { font-family:'Georgia',serif; font-size:.92rem; line-height:1.8; color:#1c2b22; white-space:pre-line; padding:1.25rem; background:var(--fondo-card-alt); border-radius:10px; border:1px solid var(--color-muy-claro); }

    /* Canvas de firma */
    #canvas-firma { border:2px dashed var(--color-principal); border-radius:10px; background:#fff; cursor:crosshair; touch-action:none; display:block; width:100%; max-width:600px; }
    .firma-imagen { border:2px solid var(--color-muy-claro); border-radius:10px; max-width:400px; background:#fff; padding:.5rem; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

{{-- Header --}}
<div class="cons-header">
    <div>
        <h2>{{ $consentimiento->nombre }}</h2>
        <div class="cons-header-sub">
            <span><i class="bi bi-person-circle"></i> {{ $consentimiento->paciente->nombre_completo }}</span>
            <span><i class="bi bi-calendar3"></i> {{ $consentimiento->fecha_generacion->translatedFormat('d \d\e F \d\e Y') }}</span>
            <span><i class="bi bi-person-badge"></i> {{ $consentimiento->doctor?->name }}</span>
        </div>
    </div>
    @if($consentimiento->firmado)
    <span class="badge-estado badge-firmado" style="font-size:.9rem;padding:.4rem 1rem;">
        <i class="bi bi-patch-check-fill"></i> Firmado
    </span>
    @else
    <span class="badge-estado badge-pendiente" style="font-size:.9rem;padding:.4rem 1rem;">
        <i class="bi bi-clock"></i> Pendiente firma
    </span>
    @endif
</div>

{{-- Botones de acción --}}
<div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1.25rem;">
    <button type="button"
    onclick="window.location.href='{{ route('consentimientos.index') }}'"
    style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;cursor:pointer;">
    
    <i class="bi bi-arrow-left"></i> Volver
</button>
    @if(!$consentimiento->firmado)
    <a href="{{ route('consentimientos.edit', $consentimiento) }}"
       class="btn-morado" style="background:transparent;color:var(--color-principal);border:1px solid var(--color-principal);">
        <i class="bi bi-pencil"></i> Editar
    </a>
    @endif
    <a href="{{ route('consentimientos.pdf', $consentimiento) }}" class="btn-morado"
       style="background:linear-gradient(135deg,#166534,#15803d);" target="_blank">
        <i class="bi bi-file-pdf"></i> Ver PDF
    </a>
</div>

{{-- Contenido del consentimiento --}}
<div class="info-card">
    <div class="info-card-titulo"><i class="bi bi-file-text"></i> Contenido del Consentimiento</div>
    <div class="contenido-texto">{{ $consentimiento->contenido }}</div>
</div>

{{-- Sección firma --}}
@if($consentimiento->firmado)
{{-- Firma ya realizada --}}
<div class="info-card">
    <div class="info-card-titulo"><i class="bi bi-patch-check-fill" style="color:#166534;"></i> Firma del Paciente</div>
    <div style="display:flex;flex-wrap:wrap;gap:1.5rem;align-items:flex-start;">
        <div>
            <p style="font-size:.78rem;color:#9ca3af;margin-bottom:.4rem;text-transform:uppercase;letter-spacing:.04em;font-weight:700;">Firma registrada</p>
            <img src="{{ $consentimiento->firma_data }}" alt="Firma" class="firma-imagen">
        </div>
        <div>
            <p style="font-size:.78rem;color:#9ca3af;margin-bottom:.3rem;text-transform:uppercase;letter-spacing:.04em;font-weight:700;">Datos de firma</p>
            <div style="font-size:.875rem;color:#1c2b22;">
                <div style="margin-bottom:.3rem;"><i class="bi bi-calendar-check" style="color:var(--color-principal);"></i>
                    <strong>Fecha y hora:</strong> {{ $consentimiento->fecha_firma?->translatedFormat('d \d\e F \d\e Y H:i') }}
                </div>
                @if($consentimiento->ip_firma)
                <div><i class="bi bi-shield-check" style="color:var(--color-principal);"></i>
                    <strong>IP:</strong> {{ $consentimiento->ip_firma }}
                </div>
                @endif
            </div>
            <div class="badge-estado badge-firmado" style="margin-top:.75rem;display:inline-flex;">
                <i class="bi bi-patch-check-fill"></i> Documento firmado digitalmente
            </div>
        </div>
    </div>
</div>

@else
{{-- Pendiente de firma --}}
<div class="info-card">
    <div class="info-card-titulo"><i class="bi bi-pen"></i> Firma del Paciente</div>
    <p style="font-size:.875rem;color:#4b5563;margin-bottom:.9rem;">
        <i class="bi bi-info-circle" style="color:var(--color-principal);"></i>
        El paciente debe firmar en el recuadro de abajo usando el dedo (en pantalla táctil) o el mouse.
    </p>
    <canvas id="canvas-firma" width="600" height="200"></canvas>
    <div id="msg-firma-vacia" style="display:none;color:#dc2626;font-size:.82rem;margin-top:.35rem;">
        <i class="bi bi-exclamation-circle"></i> Por favor dibuja la firma antes de confirmar.
    </div>
    <div style="display:flex;gap:.5rem;margin-top:.75rem;flex-wrap:wrap;">
        <button type="button" id="btn-limpiar-firma"
            style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;cursor:pointer;">
            <i class="bi bi-eraser"></i> Limpiar firma
        </button>
        <button type="button" id="btn-confirmar-firma" class="btn-morado">
            <i class="bi bi-check-lg"></i> Confirmar firma
        </button>
    </div>
    <div id="msg-firmando" style="display:none;margin-top:.5rem;font-size:.83rem;color:var(--color-principal);">
        <i class="bi bi-hourglass-split"></i> Guardando firma…
    </div>
</div>
@endif

{{-- Trazabilidad legal --}}
@if($consentimiento->firmado && $consentimiento->documento_hash)
<div class="info-card">
    <div class="info-card-titulo"><i class="bi bi-shield-check" style="color:#15803d;"></i> Trazabilidad Legal — Ley 527/1999</div>
    {!! \App\Traits\TrazabilidadFirma::generarConstanciaFirmaHTML([
        'firma_timestamp'          => $consentimiento->firma_timestamp,
        'firma_ip'                 => $consentimiento->ip_firma,
        'firma_dispositivo'        => $consentimiento->firma_dispositivo,
        'firma_navegador'          => $consentimiento->firma_navegador,
        'documento_hash'           => $consentimiento->documento_hash,
        'firma_verificacion_token' => $consentimiento->firma_verificacion_token,
    ]) !!}
</div>
@endif

@if($consentimiento->observaciones)
<div class="info-card">
    <div class="info-card-titulo"><i class="bi bi-sticky"></i> Observaciones</div>
    <p style="font-size:.9rem;color:#1c2b22;white-space:pre-line;margin:0;">{{ $consentimiento->observaciones }}</p>
</div>
@endif

@endsection

@push('scripts')
@if(!$consentimiento->firmado)
<script>
(function () {
    var canvas  = document.getElementById('canvas-firma');
    var ctx     = canvas.getContext('2d');
    var dibujando = false;
    var hasDraw = false;

    // Ajustar tamaño real del canvas al ancho del contenedor
    function ajustarCanvas() {
        var w = canvas.parentElement.offsetWidth;
        w = Math.min(w - 2, 600);
        canvas.width  = w;
        canvas.height = 200;
        ctx.strokeStyle = '#1c2b22';
        ctx.lineWidth   = 2;
        ctx.lineCap     = 'round';
        ctx.lineJoin    = 'round';
    }
    ajustarCanvas();
    window.addEventListener('resize', ajustarCanvas);

    function getPos(e) {
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width  / rect.width;
        var scaleY = canvas.height / rect.height;
        if (e.touches) {
            return {
                x: (e.touches[0].clientX - rect.left) * scaleX,
                y: (e.touches[0].clientY - rect.top)  * scaleY,
            };
        }
        return {
            x: (e.clientX - rect.left) * scaleX,
            y: (e.clientY - rect.top)  * scaleY,
        };
    }

    canvas.addEventListener('mousedown',  function(e) { dibujando = true; ctx.beginPath(); var p = getPos(e); ctx.moveTo(p.x, p.y); });
    canvas.addEventListener('mousemove',  function(e) { if (!dibujando) return; var p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); hasDraw = true; });
    canvas.addEventListener('mouseup',    function()  { dibujando = false; });
    canvas.addEventListener('mouseleave', function()  { dibujando = false; });
    canvas.addEventListener('touchstart', function(e) { e.preventDefault(); dibujando = true; ctx.beginPath(); var p = getPos(e); ctx.moveTo(p.x, p.y); }, {passive:false});
    canvas.addEventListener('touchmove',  function(e) { e.preventDefault(); if (!dibujando) return; var p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); hasDraw = true; }, {passive:false});
    canvas.addEventListener('touchend',   function()  { dibujando = false; });

    document.getElementById('btn-limpiar-firma').addEventListener('click', function () {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasDraw = false;
        document.getElementById('msg-firma-vacia').style.display = 'none';
    });

    document.getElementById('btn-confirmar-firma').addEventListener('click', function () {
        if (!hasDraw) {
            document.getElementById('msg-firma-vacia').style.display = 'block';
            return;
        }
        document.getElementById('msg-firma-vacia').style.display = 'none';
        document.getElementById('msg-firmando').style.display    = 'block';
        this.disabled = true;

        var firmaData = canvas.toDataURL('image/png');

        fetch('{{ route('consentimientos.firmar', $consentimiento) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ firma_data: firmaData }),
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.ok) {
                window.location.href = '{{ route('consentimientos.show', $consentimiento) }}';
            } else {
                alert(data.error || 'Error al guardar la firma.');
                document.getElementById('btn-confirmar-firma').disabled = false;
                document.getElementById('msg-firmando').style.display = 'none';
            }
        })
        .catch(function() {
            alert('Error de conexión al guardar la firma.');
            document.getElementById('btn-confirmar-firma').disabled = false;
            document.getElementById('msg-firmando').style.display = 'none';
        });
    });
})();
</script>
@endif
@endpush
