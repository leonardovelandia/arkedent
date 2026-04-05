@extends('layouts.app')
@section('titulo', 'Comparativo Antes / Después')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-out { background:transparent; color:var(--color-principal); border:1px solid var(--color-principal); border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-out:hover { background:var(--color-muy-claro); }

    .comp-header { background:linear-gradient(135deg,var(--color-principal) 0%,var(--color-sidebar-2) 60%,var(--color-sidebar) 100%); border-radius:14px; padding:1.5rem 1.75rem; color:#fff; margin-bottom:1.5rem; }

    .comp-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .comp-card-header { background:var(--color-muy-claro); padding:.65rem 1rem; font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); text-align:center; border-bottom:1px solid var(--color-muy-claro); }
    .comp-img { width:100%; aspect-ratio:4/3; object-fit:cover; display:block; background:#f3f4f6; }
    .comp-img-placeholder { width:100%; aspect-ratio:4/3; background:var(--color-muy-claro); display:flex; flex-direction:column; align-items:center; justify-content:center; color:var(--color-acento-activo); font-size:2rem; }
    .comp-img-footer { padding:.6rem .75rem; font-size:.8rem; color:#6b7280; text-align:center; }

    .slider-container { position:relative; width:100%; aspect-ratio:16/9; max-height:480px; overflow:hidden; border-radius:12px; border:1px solid var(--color-muy-claro); margin-bottom:1.5rem; cursor:col-resize; }
    .slider-despues { position:absolute; inset:0; }
    .slider-despues img { position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; object-position:center; }
    .slider-antes { position:absolute; top:0; left:0; bottom:0; overflow:hidden; width:50%; }
    .slider-antes img { position:absolute; top:0; left:0; height:100%; object-fit:cover; object-position:center; }
    .slider-line { position:absolute; top:0; bottom:0; left:50%; width:3px; background:var(--color-principal); z-index:10; transform:translateX(-50%); }
    .slider-handle { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:36px; height:36px; border-radius:50%; background:var(--color-principal); border:3px solid #fff; z-index:11; display:flex; align-items:center; justify-content:center; color:#fff; font-size:.85rem; box-shadow:0 2px 8px rgba(0,0,0,.3); cursor:col-resize; }
    .slider-label { position:absolute; top:.75rem; padding:.2rem .7rem; border-radius:20px; font-size:.7rem; font-weight:700; z-index:12; }
    .slider-label-antes  { left:.75rem;  background:rgba(0,0,0,.5); color:#fff; }
    .slider-label-despues{ right:.75rem; background:var(--color-claro); color:#fff; }

    .grupo-selector { background:#fff; border:1px solid var(--color-muy-claro); border-radius:12px; padding:1rem 1.25rem; margin-bottom:1.25rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .form-ctrl { border:1px solid #d1d5db; border-radius:8px; padding:.45rem .75rem; font-size:.875rem; color:#374151; }
    .form-ctrl:focus { outline:none; border-color:var(--color-principal); }

    .btn-cambiar-img { position:absolute; bottom:.65rem; z-index:13; background:rgba(0,0,0,.55); color:#fff; border:none; border-radius:20px; padding:.28rem .75rem; font-size:.7rem; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; backdrop-filter:blur(4px); }
    .btn-cambiar-img:hover { background:rgba(0,0,0,.8); }
    .btn-cambiar-img-antes  { left:.65rem; }
    .btn-cambiar-img-despues{ right:.65rem; }

    /* Modal picker */
    #picker-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:9999; align-items:center; justify-content:center; }
    #picker-overlay.activo { display:flex; }
    #picker-box { background:#fff; border-radius:14px; padding:1.5rem; max-width:560px; width:94%; max-height:80vh; overflow-y:auto; box-shadow:0 16px 48px rgba(0,0,0,.3); }
    #picker-titulo { font-size:1rem; font-weight:700; color:#1c2b22; margin-bottom:1rem; }
    .picker-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; }
    .picker-item { border:2px solid #e5e7eb; border-radius:10px; overflow:hidden; cursor:pointer; transition:border-color .15s,transform .15s; }
    .picker-item:hover { border-color:var(--color-principal); transform:translateY(-2px); }
    .picker-item.seleccionado { border-color:var(--color-principal); box-shadow:0 0 0 3px rgba(107,33,168,.18); }
    .picker-item img { width:100%; aspect-ratio:4/3; object-fit:cover; display:block; }
    .picker-item-label { font-size:.7rem; font-weight:600; text-align:center; padding:.3rem; color:#4b5563; }
    #picker-cerrar { margin-top:1rem; background:#f3f4f6; border:none; border-radius:8px; padding:.45rem 1.2rem; font-size:.85rem; font-weight:600; color:#374151; cursor:pointer; }
    #picker-cerrar:hover { background:#e5e7eb; }
</style>
@endpush

@section('contenido')

<div class="comp-header">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <div style="font-family:var(--fuente-titulos);font-size:1.3rem;font-weight:600;margin-bottom:.3rem;">
                <i class="bi bi-layout-split me-2"></i>Comparativo Antes / Después
            </div>
            <div style="font-size:.85rem;opacity:.8;">{{ $paciente->nombre_completo }}</div>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <a href="{{ route('imagenes.galeria', $paciente->uuid) }}" style="background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:.45rem 1rem;font-size:.875rem;font-weight:500;display:inline-flex;align-items:center;gap:.35rem;text-decoration:none;">
                <i class="bi bi-images"></i> Galería
            </a>
        </div>
    </div>
</div>

@if($grupos->isEmpty())
<div style="text-align:center;padding:3rem 1rem;background:#fff;border-radius:12px;border:1px solid var(--fondo-borde);">
    <i class="bi bi-layout-split" style="font-size:3rem;color:var(--color-acento-activo);display:block;margin-bottom:1rem;"></i>
    <p style="font-weight:600;color:#4b5563;">Sin imágenes comparativas</p>
    <p style="font-size:.875rem;color:#9ca3af;margin-bottom:1.25rem;">Sube imágenes marcando la opción "Es parte de comparativo" para verlas aquí.</p>
    <a href="{{ route('imagenes.create', ['paciente_id' => $paciente->id]) }}" class="btn-morado">
        <i class="bi bi-cloud-upload"></i> Subir Imágenes
    </a>
</div>
@else

@foreach($grupos as $grupoId => $imgs)
@php
$antes   = $imgs->firstWhere('orden_comparativo', 'antes');
$durante = $imgs->firstWhere('orden_comparativo', 'durante');
$despues = $imgs->firstWhere('orden_comparativo', 'despues');
@endphp

<div style="background:#fff;border:1px solid var(--fondo-borde);border-radius:12px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;">
        <h6 style="margin:0;font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.04em;">
            <i class="bi bi-collection me-1"></i> Grupo: {{ $grupoId ?? 'Sin nombre' }}
        </h6>
        <span style="font-size:.78rem;color:#9ca3af;">{{ $imgs->count() }} imagen(es)</span>
    </div>

    {{-- Slider comparativo si hay antes y después --}}
    @if($antes && $despues)
    @php
        $imgsJson = $todasLasImagenes->map(fn($i) => ['id'=>$i->id,'url'=>$i->url,'titulo'=>$i->titulo,'orden'=>$i->orden_comparativo])->values()->toJson();
    @endphp
    <div style="margin-bottom:1.25rem;">
        <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;color:#9ca3af;letter-spacing:.04em;margin-bottom:.5rem;">Comparador interactivo</div>
        <div class="slider-container" id="slider-{{ $loop->index }}"
             data-imagenes="{{ $imgsJson }}"
             data-grupo="{{ $grupoId }}"
             data-paciente="{{ $paciente->id }}"
             data-antes="{{ $antes->url }}"
             data-despues="{{ $despues->url }}">
            <div class="slider-despues"><img id="img-despues-{{ $loop->index }}" src="{{ $despues->url }}" alt="Después"></div>
            <div class="slider-antes" id="antes-{{ $loop->index }}">
                <img id="img-antes-{{ $loop->index }}" src="{{ $antes->url }}" alt="Antes">
            </div>
            <div class="slider-line" id="line-{{ $loop->index }}"></div>
            <div class="slider-handle" id="handle-{{ $loop->index }}"><i class="bi bi-arrows-expand-vertical"></i></div>
            <span class="slider-label slider-label-antes">ANTES</span>
            <span class="slider-label slider-label-despues">DESPUÉS</span>
            <button type="button" class="btn-cambiar-img btn-cambiar-img-antes"
                    onclick="abrirPicker({{ $loop->index }}, 'antes')">
                <i class="bi bi-arrow-repeat"></i> Cambiar imagen
            </button>
            <button type="button" class="btn-cambiar-img btn-cambiar-img-despues"
                    onclick="abrirPicker({{ $loop->index }}, 'despues')">
                <i class="bi bi-arrow-repeat"></i> Cambiar imagen
            </button>
        </div>
    </div>
    @endif

    {{-- Grid tres columnas --}}
    <div class="row g-3">
        @foreach([['antes','ANTES','bi-arrow-left-circle'], ['durante','DURANTE','bi-play-circle'], ['despues','DESPUÉS','bi-arrow-right-circle']] as [$orden, $label, $icon])
        @php $img = $imgs->firstWhere('orden_comparativo', $orden); @endphp
        <div class="col-md-4">
            <div class="comp-card">
                <div class="comp-card-header"><i class="bi {{ $icon }} me-1"></i> {{ $label }}</div>
                @if($img)
                <a href="{{ route('imagenes.show', $img) }}">
                    <img src="{{ $img->url }}" alt="{{ $img->titulo }}" class="comp-img"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div class="comp-img-placeholder" style="display:none;"><i class="bi {{ $img->tipo_icono }}"></i></div>
                </a>
                <div class="comp-img-footer">
                    <div style="font-weight:600;color:#1c2b22;font-size:.82rem;">{{ $img->titulo }}</div>
                    <div>{{ $img->fecha_toma->format('d/m/Y') }}</div>
                </div>
                @else
                <div class="comp-img-placeholder">
                    <i class="bi bi-plus-circle" style="color:var(--color-acento-activo);"></i>
                    <span style="font-size:.75rem;color:var(--color-acento-activo);margin-top:.4rem;">Sin imagen</span>
                </div>
                <div class="comp-img-footer">
                    <a href="{{ route('imagenes.create', ['paciente_id' => $paciente->id]) }}"
                       style="font-size:.78rem;color:var(--color-principal);text-decoration:none;">
                        <i class="bi bi-plus"></i> Agregar imagen
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endforeach

@endif

{{-- Modal picker de imagen --}}
<div id="picker-overlay">
    <div id="picker-box">
        <div id="picker-titulo">Seleccionar imagen</div>
        <div id="picker-grid" class="picker-grid"></div>
        <div style="text-align:right;margin-top:1rem;">
            <button id="picker-cerrar" onclick="cerrarPicker()">Cancelar</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var sliders = document.querySelectorAll('.slider-container');
    sliders.forEach(function(container) {
        var idx = container.id.split('-')[1];
        var antesEl   = document.getElementById('antes-' + idx);
        var antesImg  = antesEl ? antesEl.querySelector('img') : null;
        var lineEl    = document.getElementById('line-' + idx);
        var handleEl  = document.getElementById('handle-' + idx);
        var arrastrando = false;

        function syncImgWidth() {
            if (antesImg) antesImg.style.width = container.offsetWidth + 'px';
        }
        syncImgWidth();
        window.addEventListener('resize', syncImgWidth);

        function setPct(pct) {
            pct = Math.max(5, Math.min(95, pct));
            antesEl.style.width  = pct + '%';
            lineEl.style.left    = pct + '%';
            handleEl.style.left  = pct + '%';
        }

        function getX(e) {
            return e.touches ? e.touches[0].clientX : e.clientX;
        }

        handleEl.addEventListener('mousedown',  function(e) { arrastrando = true; e.preventDefault(); });
        handleEl.addEventListener('touchstart', function(e) { arrastrando = true; e.preventDefault(); });

        document.addEventListener('mouseup',  function() { arrastrando = false; });
        document.addEventListener('touchend', function() { arrastrando = false; });

        document.addEventListener('mousemove', function(e) {
            if (!arrastrando) return;
            var rect = container.getBoundingClientRect();
            var x    = getX(e) - rect.left;
            setPct((x / rect.width) * 100);
        });
        document.addEventListener('touchmove', function(e) {
            if (!arrastrando) return;
            var rect = container.getBoundingClientRect();
            var x    = getX(e) - rect.left;
            setPct((x / rect.width) * 100);
        });
    });
})();

// ── Picker de imagen ──────────────────────────────────────────────────────────
var _pickerIdx  = null;
var _pickerLado = null;
var _pickerImgId = null;
var _asignarUrl = '{{ route('imagenes.comparativo.asignar') }}';
var _csrf       = '{{ csrf_token() }}';

function abrirPicker(idx, lado) {
    _pickerIdx  = idx;
    _pickerLado = lado;
    _pickerImgId = null;

    var container = document.getElementById('slider-' + idx);
    var imagenes  = JSON.parse(container.dataset.imagenes);
    var imgElActual = lado === 'antes'
        ? document.getElementById('img-antes-' + idx)
        : document.getElementById('img-despues-' + idx);
    var srcActual = imgElActual ? imgElActual.getAttribute('src') : '';

    var titulo = lado === 'antes' ? 'Cambiar imagen — ANTES' : 'Cambiar imagen — DESPUÉS';
    document.getElementById('picker-titulo').textContent = titulo;

    var grid = document.getElementById('picker-grid');
    grid.innerHTML = '';

    imagenes.forEach(function(img) {
        var item = document.createElement('div');
        item.className = 'picker-item' + (img.url === srcActual ? ' seleccionado' : '');
        item.innerHTML =
            '<img src="' + img.url + '" alt="' + (img.titulo || '') + '">' +
            '<div class="picker-item-label">' + (img.titulo || 'Sin título') + '</div>';
        item.addEventListener('click', function() {
            seleccionarImagen(img.id, img.url);
        });
        grid.appendChild(item);
    });

    document.getElementById('picker-overlay').classList.add('activo');
}

function seleccionarImagen(imgId, url) {
    var idx  = _pickerIdx;
    var lado = _pickerLado;
    var container = document.getElementById('slider-' + idx);

    // Actualizar DOM
    if (lado === 'antes') {
        var imgEl = document.getElementById('img-antes-' + idx);
        imgEl.src = url;
        imgEl.style.width = container.offsetWidth + 'px';
    } else {
        document.getElementById('img-despues-' + idx).src = url;
    }

    // Guardar en base de datos
    var payload = new FormData();
    payload.append('_token',      _csrf);
    payload.append('imagen_id',   imgId);
    payload.append('grupo',       container.dataset.grupo || '');
    payload.append('orden',       lado);
    payload.append('paciente_id', container.dataset.paciente);

    fetch(_asignarUrl, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: payload,
    })
    .then(function(r) {
        if (!r.ok) r.text().then(function(t) { console.error('asignar error', r.status, t); });
    })
    .catch(function(e) { console.error('asignar fetch error', e); });

    cerrarPicker();
}

function cerrarPicker() {
    document.getElementById('picker-overlay').classList.remove('activo');
    _pickerIdx  = null;
    _pickerLado = null;
}

// Cerrar al hacer clic fuera del cuadro
document.getElementById('picker-overlay').addEventListener('click', function(e) {
    if (e.target === this) cerrarPicker();
});
</script>
@endpush

@endsection
