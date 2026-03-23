@extends('layouts.app')
@section('titulo', 'Subir Imágenes Clínicas')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; cursor:pointer; }
    .btn-gris:hover { background:#e5e7eb; }

    .sec-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; margin-bottom:1.25rem; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .sec-header { background:var(--color-muy-claro); padding:.75rem 1.25rem; border-bottom:1px solid var(--color-muy-claro); display:flex; align-items:center; gap:.5rem; }
    .sec-header h6 { margin:0; font-size:.82rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); }
    .sec-body { padding:1.25rem; }

    .form-lbl { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--color-principal); margin-bottom:.3rem; display:block; }
    .form-ctrl { width:100%; border:1px solid #d1d5db; border-radius:8px; padding:.5rem .8rem; font-size:.875rem; color:#374151; background:#fff; transition:border-color .15s; }
    .form-ctrl:focus { outline:none; border-color:var(--color-principal); box-shadow:0 0 0 3px var(--sombra-principal); }
    .is-invalid { border-color:#dc2626 !important; }
    .error-msg { color:#dc2626; font-size:.78rem; margin-top:.25rem; }

    /* Zona drag & drop */
    .drop-zone { border:2px dashed var(--color-acento-activo); border-radius:12px; padding:2rem; text-align:center; cursor:pointer; transition:background .2s,border-color .2s; background:var(--fondo-card-alt); }
    .drop-zone:hover, .drop-zone.dragover { background:var(--color-muy-claro); border-color:var(--color-principal); }
    .drop-zone-icon { font-size:2.5rem; color:var(--color-acento-activo); margin-bottom:.5rem; }

    .preview-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(110px,1fr)); gap:.65rem; margin-top:1rem; }
    .preview-item { position:relative; border-radius:8px; overflow:hidden; border:1px solid var(--color-muy-claro); }
    .preview-item img { width:100%; aspect-ratio:1; object-fit:cover; display:block; }
    .preview-item .rem-btn { position:absolute; top:4px; right:4px; background:rgba(220,38,38,.85); color:#fff; border:none; border-radius:50%; width:22px; height:22px; font-size:.7rem; cursor:pointer; display:flex; align-items:center; justify-content:center; }
    .preview-item .file-name { font-size:.65rem; color:#6b7280; padding:.2rem .4rem; background:var(--fondo-card-alt); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

    /* Cámara */
    .cam-area { background:#1a1a1a; border-radius:12px; overflow:hidden; }
    #video-camara { width:100%; max-height:320px; display:block; }
    #canvas-captura { display:none; }
    #preview-captura { width:100%; max-height:280px; object-fit:contain; display:none; margin-top:.5rem; border-radius:8px; border:2px solid var(--color-principal); }
</style>
@endpush

@section('contenido')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
    <h1 style="font-family:var(--fuente-titulos);font-size:1.4rem;font-weight:700;color:var(--color-principal);margin:0;">
        <i class="bi bi-cloud-upload me-2"></i>Subir Imágenes Clínicas
    </h1>
    <a href="{{ route('imagenes.index') }}" class="btn-gris"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:.85rem 1.1rem;margin-bottom:1rem;color:#991b1b;font-size:.875rem;">
    <i class="bi bi-exclamation-triangle-fill me-1"></i>
    <strong>Corrige los errores antes de continuar:</strong>
    <ul style="margin:.4rem 0 0 1rem;padding:0;">
        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('imagenes.store') }}" enctype="multipart/form-data" id="form-subida">
@csrf

{{-- Sección 1: Datos generales --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-info-circle" style="color:var(--color-principal);"></i><h6>Datos Generales</h6></div>
    <div class="sec-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-lbl">Paciente *</label>
                <select name="paciente_id" class="form-ctrl {{ $errors->has('paciente_id') ? 'is-invalid' : '' }}" id="sel-paciente" required>
                    <option value="">— Seleccione paciente —</option>
                    @foreach($pacientes as $pac)
                    <option value="{{ $pac->id }}"
                        data-historia="{{ $pac->historiaClinica->id ?? '' }}"
                        {{ (old('paciente_id', $paciente?->id) == $pac->id) ? 'selected' : '' }}>
                        {{ $pac->nombre_completo }} — {{ $pac->numero_historia }}
                    </option>
                    @endforeach
                </select>
                @error('paciente_id')<div class="error-msg">{{ $message }}</div>@enderror
                <input type="hidden" name="historia_clinica_id" id="historia-id" value="{{ $historia?->id }}">
            </div>
            <div class="col-md-6">
                <label class="form-lbl">Tipo de Imagen *</label>
                <select name="tipo" class="form-ctrl {{ $errors->has('tipo') ? 'is-invalid' : '' }}" required>
                    <option value="">— Seleccione tipo —</option>
                    @foreach($tipos as $val => $label)
                    <option value="{{ $val }}" {{ old('tipo') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('tipo')<div class="error-msg">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-lbl">Título *</label>
                <input type="text" name="titulo" value="{{ old('titulo') }}" class="form-ctrl {{ $errors->has('titulo') ? 'is-invalid' : '' }}" placeholder="Ej: Radiografía periapical diente 11" required>
                @error('titulo')<div class="error-msg">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-lbl">Diente (opcional)</label>
                <input type="text" name="diente" value="{{ old('diente') }}" class="form-ctrl" placeholder="Ej: 11, 21…">
            </div>
            <div class="col-md-3">
                <label class="form-lbl">Fecha de Toma *</label>
                <input type="date" name="fecha_toma" value="{{ old('fecha_toma', date('Y-m-d')) }}" class="form-ctrl {{ $errors->has('fecha_toma') ? 'is-invalid' : '' }}" required>
                @error('fecha_toma')<div class="error-msg">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-lbl">Descripción</label>
                <textarea name="descripcion" class="form-ctrl" rows="2" placeholder="Observaciones sobre la imagen…">{{ old('descripcion') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-lbl">Evolución asociada (opcional)</label>
                <select name="evolucion_id" class="form-ctrl" id="sel-evolucion">
                    <option value="">— Ninguna —</option>
                    @if($evolucion)
                    <option value="{{ $evolucion->id }}" selected>{{ $evolucion->numero_evolucion }} — {{ $evolucion->procedimiento }}</option>
                    @endif
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-lbl" style="visibility:hidden;">.</label>
                <div style="display:flex;align-items:center;gap:.75rem;padding:.5rem 0;">
                    <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.875rem;color:#374151;">
                        <input type="checkbox" name="es_comparativo" id="chk-comp" value="1" {{ old('es_comparativo') ? 'checked' : '' }}
                               style="width:16px;height:16px;accent-color:var(--color-principal);" onchange="toggleComparativo()">
                        Es parte de comparativo antes/después
                    </label>
                </div>
            </div>
            <div id="campos-comp" style="{{ old('es_comparativo') ? '' : 'display:none;' }}" class="col-12">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-lbl">Momento</label>
                        <select name="orden_comparativo" class="form-ctrl">
                            <option value="">— Seleccione —</option>
                            <option value="antes"   {{ old('orden_comparativo') == 'antes'   ? 'selected' : '' }}>Antes</option>
                            <option value="durante" {{ old('orden_comparativo') == 'durante' ? 'selected' : '' }}>Durante</option>
                            <option value="despues" {{ old('orden_comparativo') == 'despues' ? 'selected' : '' }}>Después</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-lbl">Grupo comparativo (ID para agrupar el set)</label>
                        <input type="text" name="grupo_comparativo" value="{{ old('grupo_comparativo') }}" class="form-ctrl" placeholder="Ej: tratamiento-blanqueamiento-2024">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Sección 2A: Subir desde archivo --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-folder2-open" style="color:var(--color-principal);"></i><h6>Subir Desde Archivo</h6></div>
    <div class="sec-body">
        <div class="drop-zone" id="drop-zone" onclick="document.getElementById('file-input').click()">
            <div class="drop-zone-icon"><i class="bi bi-cloud-arrow-up"></i></div>
            <div style="font-weight:600;color:#4b5563;margin-bottom:.3rem;">Arrastra imágenes aquí o haz clic para seleccionar</div>
            <div style="font-size:.8rem;color:#9ca3af;">JPG, PNG, WEBP · Máximo 10 imágenes · 10 MB por imagen</div>
        </div>
        <input type="file" id="file-input" name="imagenes[]" multiple accept=".jpg,.jpeg,.png,.webp,.gif,.bmp" style="display:none;" onchange="manejarArchivos(this.files)">
        <div id="preview-grid" class="preview-grid"></div>
        <div id="contador-files" style="font-size:.78rem;color:#6b7280;margin-top:.5rem;"></div>
    </div>
</div>

{{-- Sección 2B: Cámara --}}
<div class="sec-card">
    <div class="sec-header"><i class="bi bi-camera-video" style="color:var(--color-principal);"></i><h6>Capturar Desde Cámara (requiere HTTPS)</h6></div>
    <div class="sec-body">
        <div id="btn-activar">
            <button type="button" class="btn-morado" onclick="activarCamara()">
                <i class="bi bi-camera-video"></i> Activar Cámara
            </button>
            <p style="font-size:.8rem;color:#9ca3af;margin:.5rem 0 0;">Funciona solo con HTTPS. Asegúrate de permitir el acceso a la cámara.</p>
        </div>
        <div id="camara-activa" style="display:none;">
            <div class="cam-area">
                <video id="video-camara" autoplay playsinline></video>
            </div>
            <canvas id="canvas-captura"></canvas>
            <div style="display:flex;gap:.5rem;margin-top:.75rem;flex-wrap:wrap;">
                <button type="button" class="btn-morado" onclick="capturarFoto()">
                    <i class="bi bi-camera"></i> Capturar Foto
                </button>
                <button type="button" class="btn-gris" onclick="detenerCamara()">
                    <i class="bi bi-stop-circle"></i> Detener Cámara
                </button>
            </div>
            <img id="preview-captura">
            <div id="acciones-captura" style="display:none;margin-top:.5rem;">
                <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                    <button type="button" class="btn-morado" onclick="usarFoto()">
                        <i class="bi bi-check-circle"></i> Usar esta foto
                    </button>
                    <button type="button" class="btn-gris" onclick="retomar()">
                        <i class="bi bi-arrow-counterclockwise"></i> Retomar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display:flex;gap:.75rem;flex-wrap:wrap;">
    <button type="submit" class="btn-morado" id="btn-submit">
        <i class="bi bi-cloud-upload"></i> Subir Imágenes
    </button>
    <a href="{{ route('imagenes.index') }}" class="btn-gris">
        <i class="bi bi-x-circle"></i> Cancelar
    </a>
</div>

</form>

@push('scripts')
<script>
var archivosSeleccionados = [];
var fotoCapturada = null;
var streamCamara  = null;

var selPaciente = document.getElementById('sel-paciente');
selPaciente.addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    document.getElementById('historia-id').value = opt.dataset.historia || '';
});

function toggleComparativo() {
    document.getElementById('campos-comp').style.display = document.getElementById('chk-comp').checked ? '' : 'none';
}

// Drag & Drop
var dropZone = document.getElementById('drop-zone');
dropZone.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('dragover'); });
dropZone.addEventListener('dragleave', function() { this.classList.remove('dragover'); });
dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
    manejarArchivos(e.dataTransfer.files);
});

function manejarArchivos(files) {
    var arr = Array.from(files);
    arr.forEach(function(f) {
        if (archivosSeleccionados.length >= 10) return;
        if (!f.type.match(/^image\//)) return;
        archivosSeleccionados.push(f);
    });
    renderPreviews();
    sincronizarInput();
}

function renderPreviews() {
    var grid = document.getElementById('preview-grid');
    grid.innerHTML = '';
    archivosSeleccionados.forEach(function(f, i) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var div = document.createElement('div');
            div.className = 'preview-item';
            div.innerHTML = '<img src="' + e.target.result + '">' +
                '<button type="button" class="rem-btn" onclick="removerArchivo(' + i + ')"><i class="bi bi-x"></i></button>' +
                '<div class="file-name">' + f.name + '</div>';
            grid.appendChild(div);
        };
        reader.readAsDataURL(f);
    });
    document.getElementById('contador-files').textContent = archivosSeleccionados.length > 0 ?
        archivosSeleccionados.length + ' imagen(es) seleccionada(s)' : '';
}

function removerArchivo(i) {
    archivosSeleccionados.splice(i, 1);
    renderPreviews();
    sincronizarInput();
}

function sincronizarInput() {
    var dt = new DataTransfer();
    archivosSeleccionados.forEach(function(f) { dt.items.add(f); });
    document.getElementById('file-input').files = dt.files;
}

// Cámara
async function activarCamara() {
    try {
        var stream = await navigator.mediaDevices.getUserMedia({
            video: { width: 1280, height: 720, facingMode: 'environment' }
        });
        streamCamara = stream;
        var video = document.getElementById('video-camara');
        video.srcObject = stream;
        video.play();
        document.getElementById('btn-activar').style.display   = 'none';
        document.getElementById('camara-activa').style.display = 'block';
    } catch (err) {
        alert('No se pudo acceder a la cámara. Verifique que usa HTTPS y que dio permiso.');
    }
}

function detenerCamara() {
    if (streamCamara) { streamCamara.getTracks().forEach(function(t) { t.stop(); }); }
    document.getElementById('btn-activar').style.display   = 'block';
    document.getElementById('camara-activa').style.display = 'none';
}

function capturarFoto() {
    var video  = document.getElementById('video-camara');
    var canvas = document.getElementById('canvas-captura');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    fotoCapturada = canvas.toDataURL('image/jpeg', 0.9);
    var prev = document.getElementById('preview-captura');
    prev.src   = fotoCapturada;
    prev.style.display = 'block';
    document.getElementById('acciones-captura').style.display = 'block';
}

function retomar() {
    fotoCapturada = null;
    document.getElementById('preview-captura').style.display   = 'none';
    document.getElementById('acciones-captura').style.display  = 'none';
}

async function usarFoto() {
    if (!fotoCapturada) return;
    var pacienteId = document.getElementById('sel-paciente').value;
    if (!pacienteId) { alert('Seleccione un paciente primero.'); return; }
    var tipo   = document.querySelector('[name="tipo"]').value;
    var titulo = document.querySelector('[name="titulo"]').value;
    var fecha  = document.querySelector('[name="fecha_toma"]').value;
    if (!tipo || !titulo || !fecha) { alert('Complete tipo, título y fecha antes de capturar.'); return; }

    try {
        var resp = await fetch('{{ route("imagenes.capturar") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                paciente_id:    pacienteId,
                historia_clinica_id: document.getElementById('historia-id').value,
                imagen_base64:  fotoCapturada,
                tipo:           tipo,
                titulo:         titulo,
                descripcion:    document.querySelector('[name="descripcion"]').value,
                diente:         document.querySelector('[name="diente"]').value,
                fecha_toma:     fecha,
                es_comparativo: document.getElementById('chk-comp').checked ? 1 : 0,
                orden_comparativo: document.querySelector('[name="orden_comparativo"]') ? document.querySelector('[name="orden_comparativo"]').value : null,
            })
        });
        var data = await resp.json();
        if (data.success) {
            alert('Foto capturada y guardada: ' + data.imagen.numero);
            retomar();
        } else {
            alert('Error al guardar la foto.');
        }
    } catch(e) {
        alert('Error de conexión: ' + e.message);
    }
}
</script>
@endpush

@endsection
