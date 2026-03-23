@extends('layouts.app')
@section('titulo', 'Galería — ' . $paciente->nombre_completo)

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-out { background:transparent; color:#fff; border:1px solid rgba(255,255,255,.3); border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-out:hover { background:rgba(255,255,255,.12); }

    .gal-header { background:linear-gradient(135deg,var(--color-principal) 0%,var(--color-sidebar-2) 60%,var(--color-sidebar) 100%); border-radius:14px; padding:1.5rem 1.75rem; color:#fff; margin-bottom:1.5rem; }
    .gal-tabs { display:flex; gap:.25rem; border-bottom:2px solid var(--color-muy-claro); margin-bottom:1.25rem; overflow-x:auto; }
    .gal-tab { padding:.55rem 1rem; font-size:.855rem; font-weight:500; color:#6b7280; cursor:pointer; border:none; background:none; border-bottom:2px solid transparent; margin-bottom:-2px; white-space:nowrap; transition:color .15s,border-color .15s; }
    .gal-tab:hover { color:var(--color-principal); }
    .gal-tab.activo { color:var(--color-principal); border-bottom-color:var(--color-principal); font-weight:600; }
    .gal-panel { display:none; }
    .gal-panel.activo { display:block; }

    .img-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:10px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); transition:transform .18s,box-shadow .18s; cursor:pointer; }
    .img-card:hover { transform:translateY(-3px); box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .img-thumb { width:100%; aspect-ratio:4/3; object-fit:cover; background:#f3f4f6; display:block; }
    .img-thumb-placeholder { width:100%; aspect-ratio:4/3; background:var(--color-muy-claro); display:flex; align-items:center; justify-content:center; font-size:2rem; color:var(--color-acento-activo); }
    .img-info { padding:.6rem .75rem; }
    .num-badge { display:inline-block; background:var(--color-principal); color:#fff; border-radius:20px; padding:.12rem .5rem; font-size:.67rem; font-weight:700; font-family:monospace; margin-bottom:.2rem; }

    /* Lightbox */
    #lightbox { display:none; position:fixed; inset:0; background:rgba(0,0,0,.92); z-index:9999; align-items:center; justify-content:center; flex-direction:column; }
    #lightbox-img { max-width:90vw; max-height:75vh; object-fit:contain; border-radius:8px; box-shadow:0 0 40px rgba(0,0,0,.5); }
    #lightbox-info { color:#fff; text-align:center; margin-top:.75rem; max-width:600px; padding:0 1rem; }
    .lb-btn { background:rgba(255,255,255,.12); color:#fff; border:1px solid rgba(255,255,255,.2); border-radius:8px; padding:.45rem 1rem; font-size:.875rem; cursor:pointer; display:inline-flex; align-items:center; gap:.35rem; text-decoration:none; transition:background .15s; }
    .lb-btn:hover { background:rgba(255,255,255,.22); color:#fff; }
    .lb-nav { position:absolute; top:50%; transform:translateY(-50%); background:rgba(255,255,255,.15); border:none; color:#fff; width:48px; height:48px; border-radius:50%; font-size:1.3rem; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .15s; }
    .lb-nav:hover { background:rgba(255,255,255,.28); }
    .lb-prev { left:1.5rem; }
    .lb-next { right:1.5rem; }
    .lb-close { position:absolute; top:1rem; right:1.25rem; background:none; border:none; color:#fff; font-size:1.5rem; cursor:pointer; opacity:.8; }
    .lb-close:hover { opacity:1; }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

{{-- Header --}}
<div class="gal-header">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <div style="font-family:var(--fuente-titulos);font-size:1.3rem;font-weight:600;margin-bottom:.3rem;">
                <i class="bi bi-images me-2"></i>Galería — {{ $paciente->nombre_completo }}
            </div>
            <div style="font-size:.85rem;opacity:.8;">
                <span><i class="bi bi-journal-medical me-1"></i>{{ $paciente->numero_historia }}</span>
                <span style="margin:0 .75rem;">•</span>
                <span>{{ $imagenes->count() }} imagen(es)</span>
            </div>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <a href="{{ route('imagenes.create', ['paciente_id' => $paciente->id]) }}" class="btn-morado">
                <i class="bi bi-cloud-upload"></i> Subir Imágenes
            </a>
            @if($imagenes->where('es_comparativo', true)->count() > 0)
            <a href="{{ route('imagenes.comparativo', $paciente->id) }}" class="btn-out">
                <i class="bi bi-layout-split"></i> Ver Comparativo
            </a>
            @endif
            <a href="{{ route('pacientes.show', $paciente) }}" class="btn-out">
                <i class="bi bi-arrow-left"></i> Paciente
            </a>
        </div>
    </div>
</div>

@if($imagenes->isEmpty())
<div style="text-align:center;padding:3rem 1rem;background:#fff;border-radius:12px;border:1px solid var(--fondo-borde);">
    <i class="bi bi-images" style="font-size:3rem;color:var(--color-acento-activo);display:block;margin-bottom:1rem;"></i>
    <p style="font-weight:600;color:#4b5563;font-size:1rem;">Sin imágenes registradas</p>
    <a href="{{ route('imagenes.create', ['paciente_id' => $paciente->id]) }}" class="btn-morado" style="margin-top:.75rem;">
        <i class="bi bi-cloud-upload"></i> Subir Primera Imagen
    </a>
</div>
@else

{{-- Tabs --}}
<div class="gal-tabs">
    <button class="gal-tab activo" onclick="cambiarTab('todas')">
        <i class="bi bi-grid-3x3-gap"></i> Todas ({{ $imagenes->count() }})
    </button>
    @php
    $gruposTabs = [
        'radiografias' => ['Radiografías', 'bi-film', ['radiografia_periapical','radiografia_panoramica','radiografia_bitewing']],
        'intraoral'    => ['Intraoral', 'bi-camera', ['fotografia_intraoral']],
        'extraoral'    => ['Extraoral', 'bi-person-bounding-box', ['fotografia_extraoral']],
        'antes_despues'=> ['Antes/Durante/Después', 'bi-arrow-left-right', ['foto_antes','foto_durante','foto_despues']],
        'sonrisa'      => ['Sonrisa', 'bi-emoji-smile', ['foto_sonrisa']],
        'otra'         => ['Otras', 'bi-image', ['otra']],
    ];
    @endphp
    @foreach($gruposTabs as $tabId => [$tabLabel, $tabIcon, $tabTipos])
    @php $cnt = $imagenes->whereIn('tipo', $tabTipos)->count(); @endphp
    @if($cnt > 0)
    <button class="gal-tab" onclick="cambiarTab('{{ $tabId }}')">
        <i class="bi {{ $tabIcon }}"></i> {{ $tabLabel }} ({{ $cnt }})
    </button>
    @endif
    @endforeach
</div>

@php
$allImages = $imagenes->values();
$imagenesJs = $allImages->map(fn($img) => [
    'id'    => $img->id,
    'url'   => $img->url,
    'titulo'=> $img->titulo,
    'tipo'  => $img->tipo_label,
    'fecha' => $img->fecha_toma->format('d/m/Y'),
    'desc'  => $img->descripcion,
    'numero'=> $img->numero_imagen,
    'diente'=> $img->diente,
]);
@endphp

{{-- Panel: Todas --}}
<div id="gal-todas" class="gal-panel activo">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(175px,1fr));gap:.9rem;">
        @foreach($allImages as $i => $img)
        <div class="img-card" onclick="abrirLightbox({{ $i }})">
            <img src="{{ $img->url }}" alt="{{ $img->titulo }}" class="img-thumb"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
            <div class="img-thumb-placeholder" style="display:none;"><i class="bi {{ $img->tipo_icono }}"></i></div>
            <div class="img-info">
                <div class="num-badge">{{ $img->numero_imagen }}</div>
                <div style="font-size:.8rem;font-weight:600;color:#1c2b22;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $img->titulo }}">{{ $img->titulo }}</div>
                <div style="font-size:.73rem;color:#9ca3af;"><i class="bi bi-calendar3" style="font-size:.65rem;"></i> {{ $img->fecha_toma->format('d/m/Y') }}</div>
                @if($img->diente)<div style="font-size:.72rem;color:var(--color-principal);font-weight:600;">Diente: {{ $img->diente }}</div>@endif
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Panels por grupo --}}
@foreach($gruposTabs as $tabId => [$tabLabel, $tabIcon, $tabTipos])
@php $imgsFiltradas = $allImages->whereIn('tipo', $tabTipos)->values(); @endphp
@if($imgsFiltradas->count() > 0)
<div id="gal-{{ $tabId }}" class="gal-panel">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(175px,1fr));gap:.9rem;">
        @foreach($imgsFiltradas as $img)
        @php $globalIdx = $allImages->search(fn($i) => $i->id === $img->id); @endphp
        <div class="img-card" onclick="abrirLightbox({{ $globalIdx }})">
            <img src="{{ $img->url }}" alt="{{ $img->titulo }}" class="img-thumb"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
            <div class="img-thumb-placeholder" style="display:none;"><i class="bi {{ $img->tipo_icono }}"></i></div>
            <div class="img-info">
                <div class="num-badge">{{ $img->numero_imagen }}</div>
                <div style="font-size:.8rem;font-weight:600;color:#1c2b22;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $img->titulo }}">{{ $img->titulo }}</div>
                <div style="font-size:.73rem;color:#9ca3af;"><i class="bi bi-calendar3" style="font-size:.65rem;"></i> {{ $img->fecha_toma->format('d/m/Y') }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endforeach

@endif

{{-- Lightbox --}}
<div id="lightbox">
    <button class="lb-close" onclick="cerrarLightbox()"><i class="bi bi-x-lg"></i></button>
    <button class="lb-nav lb-prev" onclick="anteriorImagen()"><i class="bi bi-chevron-left"></i></button>
    <button class="lb-nav lb-next" onclick="siguienteImagen()"><i class="bi bi-chevron-right"></i></button>
    <img id="lightbox-img" src="" alt="">
    <div id="lightbox-info">
        <div id="lb-titulo" style="font-size:1rem;font-weight:600;margin-bottom:.35rem;"></div>
        <div id="lb-meta" style="font-size:.82rem;opacity:.75;margin-bottom:.75rem;"></div>
        <div id="lb-desc" style="font-size:.82rem;opacity:.65;margin-bottom:.85rem;"></div>
        <div style="display:flex;gap:.5rem;justify-content:center;flex-wrap:wrap;">
            <a id="lb-descargar" href="#" download class="lb-btn"><i class="bi bi-download"></i> Descargar</a>
            <a id="lb-ver" href="#" class="lb-btn"><i class="bi bi-eye"></i> Ver detalle</a>
            <button onclick="cerrarLightbox()" class="lb-btn"><i class="bi bi-x-circle"></i> Cerrar</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
var imagenes = @json($imagenesJs);
var imagenActual = 0;

function abrirLightbox(idx) {
    imagenActual = idx;
    mostrarImagen(idx);
    document.getElementById('lightbox').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function cerrarLightbox() {
    document.getElementById('lightbox').style.display = 'none';
    document.body.style.overflow = '';
}

function mostrarImagen(idx) {
    var img = imagenes[idx];
    if (!img) return;
    document.getElementById('lightbox-img').src = img.url;
    document.getElementById('lb-titulo').textContent = img.titulo;
    document.getElementById('lb-meta').textContent = img.tipo + ' · ' + img.fecha + (img.diente ? ' · Diente: ' + img.diente : '') + ' · ' + img.numero;
    document.getElementById('lb-desc').textContent = img.desc || '';
    document.getElementById('lb-descargar').href = img.url;
    document.getElementById('lb-ver').href = '/imagenes/' + img.id;
}

function siguienteImagen() {
    imagenActual = (imagenActual + 1) % imagenes.length;
    mostrarImagen(imagenActual);
}

function anteriorImagen() {
    imagenActual = (imagenActual - 1 + imagenes.length) % imagenes.length;
    mostrarImagen(imagenActual);
}

document.addEventListener('keydown', function(e) {
    if (document.getElementById('lightbox').style.display === 'flex') {
        if (e.key === 'Escape')      cerrarLightbox();
        if (e.key === 'ArrowRight') siguienteImagen();
        if (e.key === 'ArrowLeft')  anteriorImagen();
    }
});

document.getElementById('lightbox').addEventListener('click', function(e) {
    if (e.target === this) cerrarLightbox();
});

function cambiarTab(id) {
    document.querySelectorAll('.gal-tab').forEach(t => t.classList.remove('activo'));
    document.querySelectorAll('.gal-panel').forEach(p => p.classList.remove('activo'));
    document.getElementById('gal-' + id).classList.add('activo');
    event.currentTarget.classList.add('activo');
}
</script>
@endpush

@endsection
