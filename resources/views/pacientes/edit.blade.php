@extends('layouts.app')
@section('titulo', 'Editar Paciente')

@push('estilos')
<style>
    :root {
        --morado-base: var(--color-principal);
        --morado-claro: var(--color-claro);
        --morado-hover: var(--color-hover);
        --morado-muy-claro: var(--color-muy-claro);
    }
    .btn-morado {
        background: linear-gradient(135deg, var(--color-principal), var(--color-claro));
        color: #fff; border: none; border-radius: 8px;
        padding: 0.55rem 1.4rem; font-size: 0.875rem; font-weight: 500;
        display: inline-flex; align-items: center; gap: 0.4rem;
        transition: filter 0.18s; text-decoration: none; cursor: pointer;
    }
    .btn-morado:hover { filter: brightness(1.12); color: #fff; }
    /* Estructura sin color */
    .btn-gris { border-radius:8px; padding:0.55rem 1.4rem; font-size:0.875rem; font-weight:500; display:inline-flex; align-items:center; gap:0.4rem; transition:background 0.15s; text-decoration:none; cursor:pointer; }
    .seccion-card { border-radius:12px; margin-bottom:1.25rem; overflow:hidden; }
    .seccion-header { padding:0.75rem 1.25rem; display:flex; align-items:center; gap:0.5rem; }
    .seccion-header h6 { margin:0; font-size:0.82rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; }
    .seccion-body { padding:1.25rem; }
    .form-label-pac { font-size:0.8rem; font-weight:600; margin-bottom:0.3rem; display:block; }
    .form-control-pac { width:100%; border-radius:8px; padding:0.5rem 0.85rem; font-size:0.875rem; outline:none; transition:border-color 0.15s, box-shadow 0.15s; }
    .form-control-pac.is-invalid { border-color:#dc2626; }
    .error-msg { font-size:0.78rem; color:#dc2626; margin-top:0.3rem; }
    .foto-preview-wrap { display:flex; flex-direction:column; align-items:center; gap:0.85rem; }
    #foto-preview { width:110px; height:110px; border-radius:50%; object-fit:cover; border:3px solid var(--color-muy-claro); }
    .avatar-placeholder { width:110px; height:110px; border-radius:50%; background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; font-size:1.8rem; font-weight:700; display:flex; align-items:center; justify-content:center; border:3px solid var(--color-muy-claro); }
    .btn-subir-foto { border-radius:8px; padding:.4rem .85rem; font-size:.82rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; cursor:pointer; }

    /* Clásico */
    body:not([data-ui="glass"]) .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }
    body:not([data-ui="glass"]) .btn-gris:hover { background:#e5e7eb; color:#1f2937; }
    body:not([data-ui="glass"]) .seccion-card { background:#fff; border:1px solid var(--fondo-borde); box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,0.12); }
    body:not([data-ui="glass"]) .seccion-header { background:var(--color-muy-claro); border-bottom:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .seccion-header h6 { color:var(--color-hover); }
    body:not([data-ui="glass"]) .form-label-pac { color:#374151; }
    body:not([data-ui="glass"]) .form-control-pac { border:1px solid var(--color-muy-claro); background:#fff; color:#1c2b22; }
    body:not([data-ui="glass"]) .form-control-pac:focus { border-color:var(--color-principal); box-shadow:0 0 0 3px var(--sombra-principal); }
    body:not([data-ui="glass"]) .btn-subir-foto { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }

    /* Glass */
    body[data-ui="glass"] .btn-gris { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
    body[data-ui="glass"] .btn-gris:hover { background:rgba(255,255,255,0.13) !important; color:white !important; }
    body[data-ui="glass"] .seccion-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .seccion-header { background:rgba(0,0,0,0.25) !important; border-bottom:1px solid rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .seccion-header h6 { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .form-label-pac { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .form-control-pac { border:1px solid rgba(0,234,255,0.30) !important; background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .form-control-pac:focus { border-color:rgba(0,234,255,0.70) !important; box-shadow:none !important; }
    body[data-ui="glass"] .form-control-pac::placeholder { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .form-control-pac option,
    body[data-ui="glass"] .form-control-pac optgroup { background: #0a2535 !important; color: rgba(255,255,255,0.88) !important; }
    body[data-ui="glass"] .btn-subir-foto { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
    body[data-ui="glass"] .error-msg { color: #ff8a8a !important; text-shadow: 0 0 8px rgba(255,100,100,0.40); }
    body[data-ui="glass"] .form-control-pac.is-invalid { border-color: rgba(255,120,120,0.80) !important; box-shadow: 0 0 0 2px rgba(255,100,100,0.18) !important; }
    body[data-ui="glass"] span[style*="color:#dc2626"] { color: #ff8a8a !important; }
</style>
@endpush

@section('contenido')

<div class="page-header d-flex align-items-center gap-3">
    <a href="{{ route('pacientes.show', $paciente) }}" style="color:var(--color-principal); font-size:1.2rem; text-decoration:none;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="page-titulo">Editar Paciente</h1>
        <p class="page-subtitulo">{{ $paciente->nombre_completo }} · {{ $paciente->numero_historia }}</p>
    </div>
</div>

<form method="POST" action="{{ route('pacientes.update', $paciente) }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    {{-- ── Sección 1: Foto ── --}}
    <div class="seccion-card">
        <div class="seccion-header">
            <i class="bi bi-camera" style="color:var(--color-principal);"></i>
            <h6>Foto del Paciente</h6>
        </div>
        <div class="seccion-body">
            <div class="foto-preview-wrap">
                @if($paciente->foto_path)
                    <img id="foto-preview" src="{{ $paciente->foto_url }}" alt="{{ $paciente->nombre_completo }}">
                @else
                    <div class="avatar-placeholder" id="avatar-placeholder">
                        {{ strtoupper(substr($paciente->nombre,0,1)) }}{{ strtoupper(substr($paciente->apellido,0,1)) }}
                    </div>
                    <img id="foto-preview" src="" alt="Preview" style="display:none;width:110px;height:110px;border-radius:50%;object-fit:cover;border:3px solid var(--color-muy-claro);">
                @endif

                <div style="display:flex;gap:.5rem;flex-wrap:wrap;justify-content:center;">
                    <label for="foto" class="btn-subir-foto" style="cursor:pointer;">
                        <i class="bi bi-upload"></i> Subir foto
                    </label>
                    <button type="button" onclick="abrirCamara()" style="background:linear-gradient(135deg,var(--color-principal),var(--color-claro));color:#fff;border:none;border-radius:8px;padding:.4rem .85rem;font-size:.82rem;font-weight:500;display:inline-flex;align-items:center;gap:.35rem;cursor:pointer;">
                        <i class="bi bi-camera-video"></i> Usar cámara
                    </button>
                </div>

                <input type="file" id="foto" name="foto" accept="image/*" style="display:none;" onchange="previewFotoArchivo(this)">
                <input type="hidden" id="foto_base64" name="foto_base64">
                <p style="font-size:0.78rem;color:#9ca3af;margin:0;">Dejar vacío para mantener la foto actual</p>
            </div>
            @error('foto') <p class="error-msg text-center">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ── Sección 2: Datos personales ── --}}
    <div class="seccion-card">
        <div class="seccion-header">
            <i class="bi bi-person-badge" style="color:var(--color-principal);"></i>
            <h6>Datos Personales</h6>
        </div>
        <div class="seccion-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-pac">Nombre <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="nombre" class="form-control-pac @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $paciente->nombre) }}">
                    @error('nombre') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label-pac">Apellido <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="apellido" class="form-control-pac @error('apellido') is-invalid @enderror"
                           value="{{ old('apellido', $paciente->apellido) }}">
                    @error('apellido') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-pac">Tipo de Documento <span style="color:#dc2626;">*</span></label>
                    <select name="tipo_documento" class="form-control-pac @error('tipo_documento') is-invalid @enderror">
                        @foreach(['CC' => 'Cédula de Ciudadanía', 'TI' => 'Tarjeta de Identidad', 'CE' => 'Cédula Extranjería', 'PA' => 'Pasaporte', 'RC' => 'Registro Civil'] as $val => $label)
                            <option value="{{ $val }}" {{ old('tipo_documento', $paciente->tipo_documento) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('tipo_documento') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-pac">Número de Documento <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="numero_documento" class="form-control-pac @error('numero_documento') is-invalid @enderror"
                           value="{{ old('numero_documento', $paciente->numero_documento) }}">
                    @error('numero_documento') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-pac">Fecha de Nacimiento <span style="color:#dc2626;">*</span></label>
                    <input type="date" name="fecha_nacimiento" class="form-control-pac @error('fecha_nacimiento') is-invalid @enderror"
                           value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento->format('Y-m-d')) }}"
                           max="{{ date('Y-m-d', strtotime('-1 day')) }}">
                    @error('fecha_nacimiento') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-pac">Género <span style="color:#dc2626;">*</span></label>
                    <select name="genero" class="form-control-pac @error('genero') is-invalid @enderror">
                        <option value="masculino" {{ old('genero', $paciente->genero) === 'masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="femenino"  {{ old('genero', $paciente->genero) === 'femenino'  ? 'selected' : '' }}>Femenino</option>
                        <option value="otro"      {{ old('genero', $paciente->genero) === 'otro'      ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('genero') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- ── Sección 3: Contacto ── --}}
    <div class="seccion-card">
        <div class="seccion-header">
            <i class="bi bi-telephone" style="color:var(--color-principal);"></i>
            <h6>Contacto</h6>
        </div>
        <div class="seccion-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label-pac">Teléfono <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="telefono" class="form-control-pac @error('telefono') is-invalid @enderror"
                           value="{{ old('telefono', $paciente->telefono) }}">
                    @error('telefono') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-pac">Teléfono de Emergencia</label>
                    <input type="text" name="telefono_emergencia" class="form-control-pac @error('telefono_emergencia') is-invalid @enderror"
                           value="{{ old('telefono_emergencia', $paciente->telefono_emergencia) }}">
                    @error('telefono_emergencia') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-pac">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control-pac @error('email') is-invalid @enderror"
                           value="{{ old('email', $paciente->email) }}">
                    @error('email') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-8">
                    <label class="form-label-pac">Dirección</label>
                    <input type="text" name="direccion" class="form-control-pac @error('direccion') is-invalid @enderror"
                           value="{{ old('direccion', $paciente->direccion) }}">
                    @error('direccion') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-pac">Ciudad</label>
                    <input type="text" name="ciudad" class="form-control-pac @error('ciudad') is-invalid @enderror"
                           value="{{ old('ciudad', $paciente->ciudad) }}">
                    @error('ciudad') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- ── Sección 4: Información adicional ── --}}
    <div class="seccion-card">
        <div class="seccion-header">
            <i class="bi bi-info-circle" style="color:var(--color-principal);"></i>
            <h6>Información Adicional</h6>
        </div>
        <div class="seccion-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-pac">Ocupación</label>
                    <input type="text" name="ocupacion" class="form-control-pac @error('ocupacion') is-invalid @enderror"
                           value="{{ old('ocupacion', $paciente->ocupacion) }}">
                    @error('ocupacion') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label-pac">Nombre del Acudiente</label>
                    <input type="text" name="nombre_acudiente" class="form-control-pac @error('nombre_acudiente') is-invalid @enderror"
                           value="{{ old('nombre_acudiente', $paciente->nombre_acudiente) }}">
                    @error('nombre_acudiente') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label-pac">Observaciones</label>
                    <textarea name="observaciones" rows="3"
                              class="form-control-pac @error('observaciones') is-invalid @enderror"
                              placeholder="Notas adicionales...">{{ old('observaciones', $paciente->observaciones) }}</textarea>
                    @error('observaciones') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex; gap:0.75rem; justify-content:flex-end; margin-top:0.5rem;">
        <a href="{{ route('pacientes.show', $paciente) }}" class="btn-gris">
            <i class="bi bi-x"></i> Cancelar
        </a>
        <button type="submit" class="btn-morado">
            <i class="bi bi-floppy"></i> Guardar Cambios
        </button>
    </div>
</form>

<canvas id="cam-canvas" style="display:none;"></canvas>

<div id="modal-camara" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.7);align-items:center;justify-content:center;">
    <div style="background:#1c2b22;border-radius:16px;padding:1.5rem;width:100%;max-width:520px;position:relative;box-shadow:0 25px 60px rgba(0,0,0,.5);">
        <button onclick="cerrarCamara()" style="position:absolute;top:.75rem;right:.75rem;background:rgba(255,255,255,.15);border:none;border-radius:50%;width:32px;height:32px;color:#fff;font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
        <h6 style="color:#fff;font-weight:700;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
            <i class="bi bi-camera-video" style="color:var(--color-acento-activo);"></i> Tomar foto
        </h6>
        <div style="position:relative;background:#000;border-radius:10px;overflow:hidden;aspect-ratio:4/3;">
            <video id="cam-video" style="width:100%;height:100%;object-fit:cover;" playsinline autoplay muted></video>
            <img  id="cam-captura" style="display:none;width:100%;height:100%;object-fit:cover;" alt="Captura">
            <div id="cam-error" style="display:none;position:absolute;inset:0;background:#1c2b22;color:#fca5a5;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:1.5rem;gap:.75rem;">
                <i class="bi bi-exclamation-triangle-fill" style="font-size:2rem;color:#f87171;"></i>
                <div style="font-weight:700;font-size:.95rem;">Cámara no disponible</div>
                <div style="font-size:.8rem;color:#d1d5db;line-height:1.5;">
                    El navegador bloquea la cámara en sitios HTTP.<br>
                    Habilite SSL en Laragon y acceda por <strong>{{ str_replace('http://', 'https://', config('app.url')) }}</strong>
                </div>
                <div style="font-size:.75rem;color:#9ca3af;margin-top:.25rem;">Laragon → Menú → SSL → Habilitar SSL</div>
            </div>
        </div>
        <div style="display:flex;gap:.6rem;justify-content:center;margin-top:1rem;flex-wrap:wrap;">
            <button id="btn-capturar" type="button" onclick="capturarFoto()"
                style="background:linear-gradient(135deg,var(--color-principal),var(--color-claro));color:#fff;border:none;border-radius:8px;padding:.5rem 1.25rem;font-size:.875rem;font-weight:600;display:inline-flex;align-items:center;gap:.4rem;cursor:pointer;">
                <i class="bi bi-camera"></i> Capturar
            </button>
            <button id="btn-retomar" type="button" onclick="retomarFoto()"
                style="display:none;background:#374151;color:#fff;border:none;border-radius:8px;padding:.5rem 1.25rem;font-size:.875rem;font-weight:600;align-items:center;gap:.4rem;cursor:pointer;">
                <i class="bi bi-arrow-repeat"></i> Retomar
            </button>
            <button id="btn-usar" type="button" onclick="usarFoto()"
                style="display:none;background:#166534;color:#fff;border:none;border-radius:8px;padding:.5rem 1.25rem;font-size:.875rem;font-weight:600;align-items:center;gap:.4rem;cursor:pointer;">
                <i class="bi bi-check-lg"></i> Usar esta foto
            </button>
        </div>
    </div>
</div>

<script>
function previewFotoArchivo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            mostrarPreview(e.target.result);
            document.getElementById('foto_base64').value = '';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function mostrarPreview(src) {
    const preview = document.getElementById('foto-preview');
    const placeholder = document.getElementById('avatar-placeholder');
    preview.src = src;
    preview.style.display = 'block';
    if (placeholder) placeholder.style.display = 'none';
}

var streamCamara = null;

function abrirCamara() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        document.getElementById('cam-error').style.display = 'flex';
        document.getElementById('cam-video').style.display = 'none';
        document.getElementById('btn-capturar').style.display = 'none';
    }
    document.getElementById('modal-camara').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        iniciarStream();
    }
}

function cerrarCamara() {
    document.getElementById('modal-camara').style.display = 'none';
    document.body.style.overflow = '';
    detenerStream();
    document.getElementById('cam-captura').style.display = 'none';
    document.getElementById('cam-video').style.display = 'block';
    document.getElementById('btn-capturar').style.display = 'inline-flex';
    document.getElementById('btn-retomar').style.display = 'none';
    document.getElementById('btn-usar').style.display = 'none';
}

function iniciarStream() {
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false })
        .then(function(stream) {
            streamCamara = stream;
            var video = document.getElementById('cam-video');
            video.srcObject = stream;
            video.play();
        })
        .catch(function(err) {
            cerrarCamara();
            alert('No se pudo acceder a la cámara: ' + err.message);
        });
}

function detenerStream() {
    if (streamCamara) { streamCamara.getTracks().forEach(function(t){ t.stop(); }); streamCamara = null; }
}

function capturarFoto() {
    var video = document.getElementById('cam-video');
    var canvas = document.getElementById('cam-canvas');
    var captura = document.getElementById('cam-captura');
    canvas.width = video.videoWidth; canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    var dataUrl = canvas.toDataURL('image/jpeg', 0.9);
    captura.src = dataUrl; captura.style.display = 'block'; video.style.display = 'none';
    document.getElementById('btn-capturar').style.display = 'none';
    document.getElementById('btn-retomar').style.display = 'inline-flex';
    document.getElementById('btn-usar').style.display = 'inline-flex';
    detenerStream();
}

function retomarFoto() {
    document.getElementById('cam-captura').style.display = 'none';
    document.getElementById('cam-video').style.display = 'block';
    document.getElementById('btn-capturar').style.display = 'inline-flex';
    document.getElementById('btn-retomar').style.display = 'none';
    document.getElementById('btn-usar').style.display = 'none';
    iniciarStream();
}

function usarFoto() {
    var dataUrl = document.getElementById('cam-captura').src;
    document.getElementById('foto_base64').value = dataUrl;
    document.getElementById('foto').value = '';
    mostrarPreview(dataUrl);
    cerrarCamara();
}

document.addEventListener('keydown', function(e){ if(e.key==='Escape') cerrarCamara(); });

// Capitaliza mientras se escribe, conservando posición del cursor
['nombre', 'apellido', 'ciudad', 'direccion', 'ocupacion', 'nombre_acudiente'].forEach(function (name) {
    var el = document.querySelector('[name="' + name + '"]');
    if (!el) return;
    el.addEventListener('input', function () {
        var pos = this.selectionStart;
        this.value = this.value.toLowerCase().replace(/\b\w/g, function (l) { return l.toUpperCase(); });
        this.setSelectionRange(pos, pos);
    });
});

['telefono', 'telefono_emergencia', 'numero_documento'].forEach(function (name) {
    var el = document.querySelector('[name="' + name + '"]');
    if (!el) return;
    el.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});
</script>

@endsection
