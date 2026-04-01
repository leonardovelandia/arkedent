@extends('layouts.app')
@section('titulo', 'Nuevo Paciente')

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

    .btn-gris {
        background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;
        border-radius: 8px; padding: 0.55rem 1.4rem; font-size: 0.875rem;
        font-weight: 500; display: inline-flex; align-items: center; gap: 0.4rem;
        transition: background 0.15s; text-decoration: none; cursor: pointer;
    }
    .btn-gris:hover { background: #e5e7eb; color: #1f2937; }

    .seccion-card {
        background: #fff;
        border: 1px solid var(--fondo-borde);
        border-radius: 12px;
        margin-bottom: 1.25rem;
        overflow: hidden;
        box-shadow: 0 8px 28px var(--sombra-principal), 0 2px 8px rgba(0,0,0,0.12);
    }
    .seccion-header {
        background: var(--color-muy-claro);
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid var(--color-muy-claro);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .seccion-header h6 {
        margin: 0;
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--color-hover);
    }
    .seccion-body { padding: 1.25rem; }

    .form-label-pac {
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.3rem;
        display: block;
    }
    .form-control-pac {
        width: 100%;
        border: 1px solid var(--color-muy-claro);
        border-radius: 8px;
        padding: 0.5rem 0.85rem;
        font-size: 0.875rem;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
        background: #fff;
        color: #1c2b22;
    }
    .form-control-pac:focus {
        border-color: var(--color-principal);
        box-shadow: 0 0 0 3px var(--sombra-principal);
    }
    .form-control-pac.is-invalid { border-color: #dc2626; }
    .error-msg {
        font-size: 0.78rem;
        color: #dc2626;
        margin-top: 0.3rem;
    }

    /* Preview de foto */
    .foto-preview-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.85rem;
    }
    #foto-preview {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--color-muy-claro);
        display: none;
    }
    .avatar-placeholder {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--color-principal), var(--color-claro));
        color: #fff;
        font-size: 1.8rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid var(--color-muy-claro);
    }
</style>
@endpush

@section('contenido')

<div class="page-header d-flex align-items-center gap-3">
    <a href="{{ route('pacientes.index') }}" style="color:var(--color-principal); font-size:1.2rem; text-decoration:none;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="page-titulo">Nuevo Paciente</h1>
        <p class="page-subtitulo">Completa los datos para registrar al paciente</p>
    </div>
</div>

<form method="POST" action="{{ route('pacientes.store') }}" enctype="multipart/form-data">
    @csrf

    {{-- ── Sección 1: Foto ── --}}
    <div class="seccion-card">
        <div class="seccion-header">
            <i class="bi bi-camera" style="color:var(--color-principal);"></i>
            <h6>Foto del Paciente</h6>
        </div>
        <div class="seccion-body">
            <div class="foto-preview-wrap">
                <div class="avatar-placeholder" id="avatar-placeholder">
                    <i class="bi bi-person"></i>
                </div>
                <img id="foto-preview" src="" alt="Preview">

                {{-- Botones de acción --}}
                <div style="display:flex;gap:.5rem;flex-wrap:wrap;justify-content:center;">
                    <label for="foto" style="cursor:pointer;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.4rem .85rem;font-size:.82rem;font-weight:500;display:inline-flex;align-items:center;gap:.35rem;">
                        <i class="bi bi-upload"></i> Subir foto
                    </label>
                    <button type="button" onclick="abrirCamara()" style="background:linear-gradient(135deg,var(--color-principal),var(--color-claro));color:#fff;border:none;border-radius:8px;padding:.4rem .85rem;font-size:.82rem;font-weight:500;display:inline-flex;align-items:center;gap:.35rem;cursor:pointer;">
                        <i class="bi bi-camera-video"></i> Usar cámara
                    </button>
                </div>

                <input type="file" id="foto" name="foto" accept="image/*" style="display:none;" onchange="previewFotoArchivo(this)">
                <input type="hidden" id="foto_base64" name="foto_base64">
                <p style="font-size:0.78rem;color:#9ca3af;margin:0;">JPG, PNG. Máx. 2MB</p>
            </div>
            @error('foto')
                <p class="error-msg text-center">{{ $message }}</p>
            @enderror
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
                           value="{{ old('nombre') }}" placeholder="Nombre del paciente">
                    @error('nombre') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label-pac">Apellidos <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="apellido" class="form-control-pac @error('apellido') is-invalid @enderror"
                           value="{{ old('apellido') }}" placeholder="Apellido del paciente">
                    @error('apellido') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-pac">Tipo de Documento <span style="color:#dc2626;">*</span></label>
                    <select name="tipo_documento" class="form-control-pac @error('tipo_documento') is-invalid @enderror">
                        <option value="">-- Seleccionar --</option>
                        @foreach(['CC' => 'Cédula de Ciudadanía', 'TI' => 'Tarjeta de Identidad', 'CE' => 'Cédula Extranjería', 'PA' => 'Pasaporte', 'RC' => 'Registro Civil'] as $val => $label)
                            <option value="{{ $val }}" {{ old('tipo_documento') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('tipo_documento') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-pac">Número de Documento <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="numero_documento" class="form-control-pac @error('numero_documento') is-invalid @enderror"
                           value="{{ old('numero_documento') }}" placeholder="N° de documento">
                    @error('numero_documento') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-pac">Fecha de Nacimiento <span style="color:#dc2626;">*</span></label>
                    <input type="date" name="fecha_nacimiento" class="form-control-pac @error('fecha_nacimiento') is-invalid @enderror"
                           value="{{ old('fecha_nacimiento') }}" max="{{ date('Y-m-d', strtotime('-1 day')) }}">
                    @error('fecha_nacimiento') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-pac">Género <span style="color:#dc2626;">*</span></label>
                    <select name="genero" class="form-control-pac @error('genero') is-invalid @enderror">
                        <option value="">-- Seleccionar --</option>
                        <option value="masculino" {{ old('genero') === 'masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="femenino"  {{ old('genero') === 'femenino'  ? 'selected' : '' }}>Femenino</option>
                        <option value="otro"      {{ old('genero') === 'otro'      ? 'selected' : '' }}>Otro</option>
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
                           value="{{ old('telefono') }}" placeholder="Ej: 3001234567">
                    @error('telefono') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-pac">Teléfono de Emergencia</label>
                    <input type="text" name="telefono_emergencia" class="form-control-pac @error('telefono_emergencia') is-invalid @enderror"
                           value="{{ old('telefono_emergencia') }}" placeholder="Teléfono de contacto">
                    @error('telefono_emergencia') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-pac">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control-pac @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                    @error('email') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-8">
                    <label class="form-label-pac">Dirección</label>
                    <input type="text" name="direccion" class="form-control-pac @error('direccion') is-invalid @enderror"
                           value="{{ old('direccion') }}" placeholder="Dirección de residencia">
                    @error('direccion') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-pac">Ciudad</label>
                    <input type="text" name="ciudad" class="form-control-pac @error('ciudad') is-invalid @enderror"
                           value="{{ old('ciudad') }}" placeholder="Ciudad">
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
                           value="{{ old('ocupacion') }}" placeholder="Ej: Docente, Ingeniero...">
                    @error('ocupacion') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label-pac">Nombre del Acudiente</label>
                    <input type="text" name="nombre_acudiente" class="form-control-pac @error('nombre_acudiente') is-invalid @enderror"
                           value="{{ old('nombre_acudiente') }}" placeholder="Nombre completo del acudiente">
                    @error('nombre_acudiente') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label-pac">Observaciones</label>
                    <textarea name="observaciones" rows="3"
                              class="form-control-pac @error('observaciones') is-invalid @enderror"
                              placeholder="Notas adicionales sobre el paciente...">{{ old('observaciones') }}</textarea>
                    @error('observaciones') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Autorización de datos --}}
    <div style="background:#F3E8FF; border:1px solid var(--color-principal); border-radius:10px; padding:1rem 1.25rem; margin-top:1rem;">
        <div style="display:flex; align-items:center; gap:0.75rem;">
            <input type="checkbox" name="crear_autorizacion" value="1" id="crear-autorizacion"
                   style="width:18px; height:18px; accent-color:var(--color-principal);">
            <label for="crear-autorizacion" style="font-size:0.85rem; color:var(--color-principal); font-weight:500; cursor:pointer;">
                <i class="bi bi-shield-check me-1"></i>
                Generar y firmar la Autorización de Datos Personales al crear el paciente
            </label>
        </div>
        <p style="font-size:0.75rem; color:#5c6b62; margin-top:0.4rem; margin-left:1.75rem; margin-bottom:0;">
            Recomendado. Cumple con la Ley 1581 de 2012 (Habeas Data Colombia).
            Si no lo haces ahora, podrás hacerlo desde la ficha del paciente.
        </p>
    </div>

    {{-- Botones de acción --}}
    <div style="display:flex; gap:0.75rem; justify-content:flex-end; margin-top:0.5rem;">
        <a href="{{ route('pacientes.index') }}" class="btn-gris">
            <i class="bi bi-x"></i> Cancelar
        </a>
        <button type="submit" class="btn-morado">
            <i class="bi bi-floppy"></i> Guardar Paciente
        </button>
    </div>
</form>

{{-- Canvas oculto para captura --}}
<canvas id="cam-canvas" style="display:none;"></canvas>

{{-- Modal cámara --}}
<div id="modal-camara" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.7);align-items:center;justify-content:center;">
    <div style="background:#1c2b22;border-radius:16px;padding:1.5rem;width:100%;max-width:520px;position:relative;box-shadow:0 25px 60px rgba(0,0,0,.5);">
        <button onclick="cerrarCamara()" style="position:absolute;top:.75rem;right:.75rem;background:rgba(255,255,255,.15);border:none;border-radius:50%;width:32px;height:32px;color:#fff;font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
        <h6 style="color:#fff;font-weight:700;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
            <i class="bi bi-camera-video" style="color:var(--color-acento-activo);"></i> Tomar foto
        </h6>
        <div style="position:relative;background:#000;border-radius:10px;overflow:hidden;aspect-ratio:4/3;">
            <video id="cam-video" style="width:100%;height:100%;object-fit:cover;" playsinline autoplay muted></video>
            <img  id="cam-captura" style="display:none;width:100%;height:100%;object-fit:cover;" alt="Captura">
            <div id="cam-error" style="display:none;position:absolute;inset:0;background:#1c2b22;color:#fca5a5;display:none;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:1.5rem;gap:.75rem;">
                <i class="bi bi-exclamation-triangle-fill" style="font-size:2rem;color:#f87171;"></i>
                <div style="font-weight:700;font-size:.95rem;">Cámara no disponible</div>
                <div style="font-size:.8rem;color:#d1d5db;line-height:1.5;">
                    El navegador bloquea la cámara en sitios HTTP.<br>
                    Debe habilitar SSL en Laragon y acceder por <strong>{{ str_replace('http://', 'https://', config('app.url')) }}</strong>
                </div>
                <div style="font-size:.75rem;color:#9ca3af;margin-top:.25rem;">
                    Laragon → Menú → SSL → Habilitar SSL
                </div>
            </div>
        </div>
        <div style="display:flex;gap:.6rem;justify-content:center;margin-top:1rem;flex-wrap:wrap;">
            <button id="btn-capturar" type="button" onclick="capturarFoto()"
                style="background:linear-gradient(135deg,var(--color-principal),var(--color-claro));color:#fff;border:none;border-radius:8px;padding:.5rem 1.25rem;font-size:.875rem;font-weight:600;display:inline-flex;align-items:center;gap:.4rem;cursor:pointer;">
                <i class="bi bi-camera"></i> Capturar
            </button>
            <button id="btn-retomar" type="button" onclick="retomarFoto()"
                style="display:none;background:#374151;color:#fff;border:none;border-radius:8px;padding:.5rem 1.25rem;font-size:.875rem;font-weight:600;display:none;align-items:center;gap:.4rem;cursor:pointer;">
                <i class="bi bi-arrow-repeat"></i> Retomar
            </button>
            <button id="btn-usar" type="button" onclick="usarFoto()"
                style="display:none;background:#166534;color:#fff;border:none;border-radius:8px;padding:.5rem 1.25rem;font-size:.875rem;font-weight:600;display:none;align-items:center;gap:.4rem;cursor:pointer;">
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
            document.getElementById('foto_base64').value = ''; // limpiar cámara
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function mostrarPreview(src) {
    const preview = document.getElementById('foto-preview');
    const placeholder = document.getElementById('avatar-placeholder');
    preview.src = src;
    preview.style.display = 'block';
    placeholder.style.display = 'none';
}

// ── Cámara ────────────────────────────────────────────────
var streamCamara = null;

function abrirCamara() {
    // Verificar contexto seguro (requiere HTTPS o localhost)
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        document.getElementById('cam-error').style.display = 'block';
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
    if (streamCamara) {
        streamCamara.getTracks().forEach(function(t){ t.stop(); });
        streamCamara = null;
    }
}

function capturarFoto() {
    var video  = document.getElementById('cam-video');
    var canvas = document.getElementById('cam-canvas');
    var captura = document.getElementById('cam-captura');

    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    var dataUrl = canvas.toDataURL('image/jpeg', 0.9);
    captura.src = dataUrl;
    captura.style.display = 'block';
    video.style.display   = 'none';

    document.getElementById('btn-capturar').style.display = 'none';
    document.getElementById('btn-retomar').style.display  = 'inline-flex';
    document.getElementById('btn-usar').style.display     = 'inline-flex';
    detenerStream();
}

function retomarFoto() {
    document.getElementById('cam-captura').style.display = 'none';
    document.getElementById('cam-video').style.display   = 'block';
    document.getElementById('btn-capturar').style.display = 'inline-flex';
    document.getElementById('btn-retomar').style.display  = 'none';
    document.getElementById('btn-usar').style.display     = 'none';
    iniciarStream();
}

function usarFoto() {
    var dataUrl = document.getElementById('cam-captura').src;
    document.getElementById('foto_base64').value = dataUrl;
    document.getElementById('foto').value = ''; // limpiar archivo
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
