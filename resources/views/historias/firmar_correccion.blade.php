@extends('layouts.app')
@section('titulo', 'Firmar Corrección — Historia Clínica')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-outline-morado { background:transparent; color:var(--color-principal); border:1.5px solid var(--color-principal); border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; cursor:pointer; }
    .btn-outline-morado:hover { background:var(--color-muy-claro); }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; }
    .card-firma { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:1.5rem; margin-bottom:1.25rem; }
    .seccion-titulo { background:var(--color-muy-claro); margin:-1.5rem -1.5rem 1rem; padding:.5rem 1.5rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); border-bottom:1px solid var(--color-muy-claro); padding-bottom:.4rem; margin-bottom:.75rem; }
    .canvas-firma { border:2px solid var(--color-principal); border-radius:8px; background:#fff; cursor:crosshair; touch-action:none; display:block; width:100%; height:180px; }
    .texto-legal { background:var(--fondo-card-alt); border-left:3px solid var(--color-principal); border-radius:0 8px 8px 0; padding:1rem 1.25rem; font-size:.875rem; color:#374151; line-height:1.6; margin-bottom:1.25rem; }
    .firma-guardada { border:1px solid #d1fae5; border-radius:8px; padding:1rem; background:#f0fdf4; }
    .alerta-exito { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; border-radius:8px; padding:.75rem 1rem; margin-bottom:1rem; display:none; align-items:center; gap:.5rem; }
    .tabla-detalle { width:100%; border-collapse:collapse; font-size:.875rem; }
    .tabla-detalle td { padding:.55rem .75rem; border-bottom:1px solid var(--fondo-borde); vertical-align:top; }
    .tabla-detalle td:first-child { font-size:.72rem; font-weight:700; text-transform:uppercase; color:#9ca3af; width:160px; }
</style>
@endpush

@section('contenido')

<div id="alerta-exito" class="alerta-exito">
    <i class="bi bi-check-circle-fill"></i>
    <span>Corrección firmada correctamente.</span>
</div>

{{-- Header --}}
<div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <a href="{{ route('historias.show', $correccion->historia_clinica_id) }}"
       style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;color:#374151;text-decoration:none;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Firmar Corrección — Historia Clínica
            @if($correccion->numero_correccion)
            <span style="font-family:monospace;font-size:.75rem;font-weight:700;background:var(--color-muy-claro);color:var(--color-principal);border-radius:6px;padding:.1rem .5rem;margin-left:.4rem;">{{ $correccion->numero_correccion }}</span>
            @endif
        </h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">
            {{ $correccion->historia->paciente->nombre_completo }} · {{ $correccion->historia->paciente->numero_historia }}
        </p>
    </div>
</div>

{{-- Banner informativo --}}
<div style="background:#FFF3CD;border:1px solid #FFC107;border-radius:10px;padding:.85rem 1.1rem;display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem;">
    <i class="bi bi-lock-fill" style="color:#856404;font-size:1.1rem;flex-shrink:0;"></i>
    <div>
        <div style="font-size:.875rem;font-weight:600;color:#856404;">Corrección a Historia Clínica</div>
        <div style="font-size:.8rem;color:#856404;margin-top:.15rem;">
            Paciente: <strong>{{ $correccion->historia->paciente->nombre_completo }}</strong> —
            Campo corregido: <strong>{{ $correccion->campo_label }}</strong> —
            Registrada por: <strong>{{ $correccion->usuario->name }}</strong> el {{ $correccion->created_at->format('d/m/Y H:i') }}
        </div>
    </div>
</div>

{{-- Detalle de la corrección --}}
<div class="card-firma">
    <div class="seccion-titulo"><i class="bi bi-clipboard-check"></i> Detalle de la corrección</div>
    <table class="tabla-detalle">
        <tr>
            <td>Campo corregido</td>
            <td><strong>{{ $correccion->campo_label }}</strong></td>
        </tr>
        <tr>
            <td>Valor anterior</td>
            <td><span style="color:#999;text-decoration:line-through;">{{ $correccion->valor_anterior ?: '(vacío)' }}</span></td>
        </tr>
        <tr>
            <td>Valor corregido</td>
            <td><span style="color:#166534;font-weight:500;">{{ $correccion->valor_nuevo }}</span></td>
        </tr>
        <tr>
            <td>Motivo</td>
            <td>{{ $correccion->motivo }}</td>
        </tr>
        <tr>
            <td>Registrado por</td>
            <td>{{ $correccion->usuario->name }} — {{ $correccion->created_at->format('d/m/Y H:i') }}</td>
        </tr>
    </table>
</div>

{{-- Texto legal --}}
<div class="texto-legal">
    <i class="bi bi-shield-lock" style="color:var(--color-principal);margin-right:.4rem;"></i>
    <strong>Declaración del paciente:</strong><br>
    Yo, <strong>{{ $correccion->historia->paciente->nombre_completo }}</strong>, identificado(a) con
    <strong>{{ $correccion->historia->paciente->tipo_documento }} {{ $correccion->historia->paciente->numero_documento }}</strong>,
    declaro que he leído y estoy de acuerdo con la corrección realizada a mi historia clínica.
    Confirmo que la información corregida es verídica.
</div>

{{-- Sección firma --}}
@if($correccion->firmado)
<div class="card-firma">
    <div class="seccion-titulo"><i class="bi bi-check-circle-fill" style="color:#166534;"></i> Corrección Firmada</div>
    <div class="firma-guardada">
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem;">
            <span style="background:#d1fae5;color:#166534;padding:.2rem .65rem;border-radius:20px;font-size:.75rem;font-weight:700;">
                <i class="bi bi-check-circle-fill"></i> FIRMADO
            </span>
            <span style="font-size:.82rem;color:#6b7280;">{{ $correccion->fecha_firma->format('d/m/Y \a \l\a\s H:i') }}</span>
        </div>
        <img src="{{ $correccion->firma_data }}" alt="Firma del paciente" style="max-width:300px;max-height:100px;border:1px solid #d1fae5;border-radius:6px;background:#fff;padding:.25rem;">
        <p style="font-size:.75rem;color:#9ca3af;margin-top:.5rem;margin-bottom:0;">IP registrada: {{ $correccion->ip_firma }}</p>
    </div>
    <div style="margin-top:1rem;">
        <a href="{{ route('historias.show', $correccion->historia_clinica_id) }}" class="btn-gris">
            <i class="bi bi-arrow-left"></i> Volver a la historia
        </a>
    </div>
</div>
@else
<div class="card-firma">
    <div class="seccion-titulo"><i class="bi bi-pen"></i> Firma del Paciente confirmando la corrección</div>
    <p style="font-size:.82rem;color:#6b7280;margin-bottom:.75rem;">
        <i class="bi bi-info-circle"></i> El paciente debe firmar en el recuadro de abajo usando el dedo o el mouse.
    </p>
    <canvas id="canvas-firma" class="canvas-firma"></canvas>
    <div style="display:flex;gap:.5rem;margin-top:.75rem;flex-wrap:wrap;">
        <button type="button" class="btn-outline-morado" onclick="limpiarCanvas()">
            <i class="bi bi-eraser"></i> Limpiar firma
        </button>
        <button type="button" class="btn-morado" id="btn-confirmar" onclick="confirmarFirma()">
            <i class="bi bi-check-lg"></i> Confirmar firma
        </button>
        <a href="{{ route('historias.show', $correccion->historia_clinica_id) }}" class="btn-gris">
            <i class="bi bi-x-lg"></i> Cancelar
        </a>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
(function() {
    var canvas = document.getElementById('canvas-firma');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');
    var dibujando = false;

    function redimensionar() {
        var ratio = window.devicePixelRatio || 1;
        canvas.width  = canvas.offsetWidth  * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        ctx.scale(ratio, ratio);
        ctx.strokeStyle = '#1c2b22';
        ctx.lineWidth   = 2;
        ctx.lineCap     = 'round';
        ctx.lineJoin    = 'round';
    }
    redimensionar();

    function getPos(e) {
        var r = canvas.getBoundingClientRect();
        var src = e.touches ? e.touches[0] : e;
        return { x: src.clientX - r.left, y: src.clientY - r.top };
    }

    canvas.addEventListener('mousedown',  function(e){ dibujando=true; ctx.beginPath(); var p=getPos(e); ctx.moveTo(p.x,p.y); });
    canvas.addEventListener('mousemove',  function(e){ if(!dibujando) return; var p=getPos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); });
    canvas.addEventListener('mouseup',    function(){ dibujando=false; });
    canvas.addEventListener('mouseleave', function(){ dibujando=false; });
    canvas.addEventListener('touchstart', function(e){ e.preventDefault(); dibujando=true; ctx.beginPath(); var p=getPos(e); ctx.moveTo(p.x,p.y); }, {passive:false});
    canvas.addEventListener('touchmove',  function(e){ e.preventDefault(); if(!dibujando) return; var p=getPos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); }, {passive:false});
    canvas.addEventListener('touchend',   function(){ dibujando=false; });

    window.limpiarCanvas = function() {
        ctx.clearRect(0, 0, canvas.offsetWidth, canvas.offsetHeight);
    };

    window.confirmarFirma = function() {
        var firmaData = canvas.toDataURL('image/png');
        var pixelBuffer = new Uint32Array(ctx.getImageData(0, 0, canvas.width, canvas.height).data.buffer);
        var hasContent = pixelBuffer.some(function(p){ return p !== 0; });
        if (!hasContent) { alert('Por favor dibuje la firma antes de confirmar.'); return; }

        var btn = document.getElementById('btn-confirmar');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Guardando…';

        fetch('{{ route('historias.correccion.firmar', $correccion) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            },
            body: JSON.stringify({ firma_data: firmaData })
        })
        .then(function(r){ return r.json(); })
        .then(function(data) {
            if (data.success) {
                document.getElementById('alerta-exito').style.display = 'flex';
                setTimeout(function(){ window.location.reload(); }, 1200);
            }
        })
        .catch(function(){
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg"></i> Confirmar firma';
            alert('Error al guardar la firma. Intente de nuevo.');
        });
    };
})();
</script>
@endpush
