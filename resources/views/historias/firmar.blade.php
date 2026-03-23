@extends('layouts.app')
@section('titulo', 'Firmar Historia Clínica')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-outline-morado { background:transparent; color:var(--color-principal); border:1.5px solid var(--color-principal); border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; cursor:pointer; }
    .btn-outline-morado:hover { background:var(--color-muy-claro); }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; }
    .card-firma { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:1.5rem; margin-bottom:1.25rem; }
    .seccion-titulo { background:var(--color-muy-claro); margin:-1.5rem -1.5rem 1rem; padding:.5rem 1.5rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); border-bottom:1px solid var(--color-muy-claro); padding-bottom:.4rem; margin-bottom:.75rem; }
    .dato-row { display:flex; gap:2rem; flex-wrap:wrap; margin-bottom:.4rem; }
    .dato-item { min-width:200px; }
    .dato-lbl { font-size:.72rem; font-weight:700; text-transform:uppercase; color:#9ca3af; }
    .dato-val { font-size:.875rem; color:#1c2b22; font-weight:500; }
    .canvas-firma { border:2px solid var(--color-principal); border-radius:8px; background:#fff; cursor:crosshair; touch-action:none; display:block; width:100%; height:200px; }
    .texto-legal { background:var(--fondo-card-alt); border-left:3px solid var(--color-principal); border-radius:0 8px 8px 0; padding:1rem 1.25rem; font-size:.875rem; color:#374151; line-height:1.6; margin-bottom:1.25rem; }
    .firma-guardada { border:1px solid #d1fae5; border-radius:8px; padding:1rem; background:#f0fdf4; }
    .alerta-exito { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; border-radius:8px; padding:.75rem 1rem; margin-bottom:1rem; display:none; align-items:center; gap:.5rem; }
</style>
@endpush

@section('contenido')

<div id="alerta-exito" class="alerta-exito">
    <i class="bi bi-check-circle-fill"></i>
    <span id="alerta-msg">Historia firmada correctamente.</span>
</div>

{{-- Header --}}
<div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <a href="{{ route('historias.show', $historia) }}" style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;color:#374151;text-decoration:none;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Firma — Historia Clínica</h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">{{ $historia->paciente->nombre_completo }} · {{ $historia->paciente->numero_historia }}</p>
    </div>
    <div style="margin-left:auto;display:flex;gap:.5rem;flex-wrap:wrap;">
        @if($historia->firmado)
        <a href="{{ route('historias.pdf', $historia) }}" class="btn-morado" target="_blank">
            <i class="bi bi-file-earmark-pdf"></i> Ver PDF
        </a>
        @endif
        <a href="{{ route('historias.show', $historia) }}" class="btn-gris">
            <i class="bi bi-arrow-left"></i> Ver Historia
        </a>
    </div>
</div>

{{-- Resumen historia --}}
<div class="card-firma">
    <div class="seccion-titulo"><i class="bi bi-person-lines-fill"></i> Datos del Paciente</div>
    <div class="dato-row">
        <div class="dato-item"><div class="dato-lbl">Nombre completo</div><div class="dato-val">{{ $historia->paciente->nombre_completo }}</div></div>
        <div class="dato-item"><div class="dato-lbl">Documento</div><div class="dato-val">{{ $historia->paciente->tipo_documento }} {{ $historia->paciente->numero_documento }}</div></div>
        <div class="dato-item"><div class="dato-lbl">N° Historia</div><div class="dato-val">{{ $historia->paciente->numero_historia }}</div></div>
        <div class="dato-item"><div class="dato-lbl">Fecha apertura</div><div class="dato-val">{{ $historia->fecha_apertura->format('d/m/Y') }}</div></div>
    </div>
</div>

@if($historia->motivo_consulta)
<div class="card-firma">
    <div class="seccion-titulo"><i class="bi bi-chat-square-text"></i> Motivo de Consulta</div>
    <div style="font-size:.875rem;color:#374151;white-space:pre-line;">{{ $historia->motivo_consulta }}</div>
    @if($historia->enfermedad_actual)
    <div style="margin-top:.75rem;"><span style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;">Enfermedad actual:</span><div style="font-size:.875rem;color:#374151;white-space:pre-line;">{{ $historia->enfermedad_actual }}</div></div>
    @endif
</div>
@endif

@if($historia->antecedentes_medicos || $historia->medicamentos_actuales || $historia->alergias)
<div class="card-firma">
    <div class="seccion-titulo"><i class="bi bi-heart-pulse"></i> Antecedentes Médicos</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;flex-wrap:wrap;">
        @if($historia->antecedentes_medicos)<div><div class="dato-lbl">Enfermedades previas</div><div class="dato-val" style="white-space:pre-line;">{{ $historia->antecedentes_medicos }}</div></div>@endif
        @if($historia->medicamentos_actuales)<div><div class="dato-lbl">Medicamentos actuales</div><div class="dato-val" style="white-space:pre-line;">{{ $historia->medicamentos_actuales }}</div></div>@endif
        @if($historia->alergias)<div><div class="dato-lbl">Alergias</div><div class="dato-val" style="white-space:pre-line;">{{ $historia->alergias }}</div></div>@endif
        @if($historia->antecedentes_odontologicos)<div><div class="dato-lbl">Antecedentes odontológicos</div><div class="dato-val" style="white-space:pre-line;">{{ $historia->antecedentes_odontologicos }}</div></div>@endif
    </div>
</div>
@endif

@if($historia->presion_arterial || $historia->frecuencia_cardiaca || $historia->temperatura || $historia->peso)
<div class="card-firma">
    <div class="seccion-titulo"><i class="bi bi-activity"></i> Signos Vitales</div>
    <div class="dato-row">
        @if($historia->presion_arterial)<div class="dato-item"><div class="dato-lbl">Presión arterial</div><div class="dato-val">{{ $historia->presion_arterial }}</div></div>@endif
        @if($historia->frecuencia_cardiaca)<div class="dato-item"><div class="dato-lbl">Frec. cardíaca</div><div class="dato-val">{{ $historia->frecuencia_cardiaca }} bpm</div></div>@endif
        @if($historia->temperatura)<div class="dato-item"><div class="dato-lbl">Temperatura</div><div class="dato-val">{{ $historia->temperatura }} °C</div></div>@endif
        @if($historia->peso)<div class="dato-item"><div class="dato-lbl">Peso</div><div class="dato-val">{{ $historia->peso }} kg</div></div>@endif
    </div>
</div>
@endif

{{-- Texto legal --}}
<div class="texto-legal">
    <i class="bi bi-shield-lock" style="color:var(--color-principal);margin-right:.4rem;"></i>
    <strong>Declaración del paciente:</strong><br>
    Yo, <strong>{{ $historia->paciente->nombre_completo }}</strong>, identificado(a) con
    <strong>{{ $historia->paciente->tipo_documento }} {{ $historia->paciente->numero_documento }}</strong>,
    declaro que la información consignada en esta historia clínica es verídica y completa.
    Autorizo al consultorio <strong>{{ $nombreConsultorio }}</strong> a utilizar estos datos
    exclusivamente para fines médicos y de tratamiento odontológico.
</div>

{{-- Sección firma --}}
@if($historia->firmado)
<div class="card-firma">
    <div class="seccion-titulo"><i class="bi bi-check-circle-fill" style="color:#166534;"></i> Historia Firmada</div>
    <div class="firma-guardada">
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem;">
            <span style="background:#d1fae5;color:#166534;padding:.2rem .65rem;border-radius:20px;font-size:.75rem;font-weight:700;">
                <i class="bi bi-check-circle-fill"></i> FIRMADO
            </span>
            <span style="font-size:.82rem;color:#6b7280;">{{ $historia->fecha_firma->format('d/m/Y \a \l\a\s H:i') }}</span>
        </div>
        <img src="{{ $historia->firma_data }}" alt="Firma del paciente" style="max-width:300px;max-height:100px;border:1px solid #d1fae5;border-radius:6px;background:#fff;padding:.25rem;">
        <p style="font-size:.75rem;color:#9ca3af;margin-top:.5rem;margin-bottom:0;">IP registrada: {{ $historia->ip_firma }}</p>
    </div>
</div>
@else
<div class="card-firma">
    <div class="seccion-titulo"><i class="bi bi-pen"></i> Firma del Paciente</div>
    <p style="font-size:.82rem;color:#6b7280;margin-bottom:.75rem;">
        <i class="bi bi-info-circle"></i> El paciente debe firmar en el recuadro de abajo usando el dedo o el mouse.
    </p>
    <canvas id="canvas-firma" class="canvas-firma"></canvas>
    <div style="display:flex;gap:.5rem;margin-top:.75rem;flex-wrap:wrap;">
        <button type="button" class="btn-outline-morado" onclick="limpiarCanvas()">
            <i class="bi bi-eraser"></i> Limpiar firma
        </button>
        <button type="button" class="btn-morado" id="btn-confirmar" onclick="confirmarFirma()">
            <i class="bi bi-check-lg"></i> Confirmar y Firmar
        </button>
        <a href="{{ route('historias.show', $historia) }}" class="btn-gris">
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

        fetch('{{ route('historias.firmar', $historia) }}', {
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
                var alerta = document.getElementById('alerta-exito');
                alerta.style.display = 'flex';
                setTimeout(function(){ window.location.reload(); }, 1200);
            }
        })
        .catch(function(){
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg"></i> Confirmar y Firmar';
            alert('Error al guardar la firma. Intente de nuevo.');
        });
    };
})();
</script>
@endpush
