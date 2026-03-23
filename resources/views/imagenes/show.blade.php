@extends('layouts.app')
@section('titulo', $imagen->numero_imagen . ' — ' . $imagen->titulo)

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-out { background:transparent; color:var(--color-principal); border:1px solid var(--color-principal); border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-out:hover { background:var(--color-muy-claro); }
    .btn-rojo { background:#dc2626; color:#fff; border:none; border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; text-decoration:none; cursor:pointer; }
    .btn-rojo:hover { background:#b91c1c; color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; }

    .img-header { background:linear-gradient(135deg,var(--color-principal) 0%,var(--color-sidebar-2) 60%,var(--color-sidebar) 100%); border-radius:14px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; }
    .meta-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1.25rem 1.5rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .meta-row { display:flex; justify-content:space-between; padding:.55rem 0; border-bottom:1px solid var(--fondo-borde); font-size:.875rem; }
    .meta-row:last-child { border-bottom:none; }
    .meta-lbl { color:#9ca3af; font-weight:600; font-size:.75rem; text-transform:uppercase; letter-spacing:.04em; }
    .meta-val { color:#1c2b22; font-weight:500; text-align:right; }

    .img-main { background:#111; border-radius:12px; display:flex; align-items:center; justify-content:center; min-height:300px; overflow:hidden; margin-bottom:1.25rem; }
    .img-main img { max-width:100%; max-height:70vh; object-fit:contain; display:block; }

    .grupo-comp-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(130px,1fr)); gap:.75rem; margin-top:.75rem; }
    .mini-card { border-radius:8px; overflow:hidden; border:1px solid var(--color-muy-claro); }
    .mini-card img { width:100%; aspect-ratio:1; object-fit:cover; display:block; }
    .mini-card .mini-label { font-size:.68rem; font-weight:700; text-align:center; padding:.25rem; background:var(--color-muy-claro); color:var(--color-principal); }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif

{{-- Header --}}
<div class="img-header">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.65;margin-bottom:.3rem;">
                <i class="bi {{ $imagen->tipo_icono }} me-1"></i> {{ $imagen->tipo_label }}
            </div>
            <div style="font-family:var(--fuente-titulos);font-size:1.3rem;font-weight:600;margin-bottom:.3rem;">{{ $imagen->titulo }}</div>
            <div style="font-size:.85rem;opacity:.8;">
                <span style="font-family:monospace;background:rgba(255,255,255,.2);border-radius:6px;padding:.1rem .55rem;font-weight:700;">{{ $imagen->numero_imagen }}</span>
                <span style="margin:0 .6rem;">·</span>
                <span>{{ $imagen->paciente->nombre_completo }}</span>
                <span style="margin:0 .6rem;">·</span>
                <span>{{ $imagen->fecha_toma->format('d/m/Y') }}</span>
            </div>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <a href="{{ $imagen->url }}" download="{{ $imagen->archivo_nombre }}" class="btn-morado">
                <i class="bi bi-download"></i> Descargar
            </a>
            <a href="{{ route('imagenes.edit', $imagen) }}" class="btn-out" style="background:rgba(255,255,255,.1);color:#fff;border-color:rgba(255,255,255,.25);">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="{{ route('imagenes.galeria', $imagen->paciente_id) }}" class="btn-out" style="background:rgba(255,255,255,.08);color:#fff;border-color:rgba(255,255,255,.2);">
                <i class="bi bi-images"></i> Galería
            </a>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        {{-- Imagen en grande --}}
        <div class="img-main">
            <img src="{{ $imagen->url }}" alt="{{ $imagen->titulo }}"
                 onerror="this.style.display='none';document.getElementById('img-error').style.display='flex';">
            <div id="img-error" style="display:none;flex-direction:column;align-items:center;color:#888;gap:.5rem;">
                <i class="bi bi-image-alt" style="font-size:3rem;"></i>
                <span>No se pudo cargar la imagen</span>
            </div>
        </div>

        @if($imagen->descripcion)
        <div style="background:var(--fondo-card-alt);border:1px solid var(--color-muy-claro);border-radius:10px;padding:.85rem 1.1rem;font-size:.9rem;color:#374151;line-height:1.6;">
            {{ $imagen->descripcion }}
        </div>
        @endif

        {{-- Grupo comparativo --}}
        @if($grupoImagenes && $grupoImagenes->count() > 1)
        <div class="meta-card mt-3">
            <div style="font-size:.8rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.04em;margin-bottom:.75rem;">
                <i class="bi bi-layout-split me-1"></i> Grupo Comparativo: {{ $imagen->grupo_comparativo }}
            </div>
            <div class="grupo-comp-grid">
                @foreach($grupoImagenes as $gi)
                <a href="{{ route('imagenes.show', $gi) }}" style="text-decoration:none;">
                    <div class="mini-card {{ $gi->id === $imagen->id ? 'border-morado' : '' }}"
                         style="{{ $gi->id === $imagen->id ? 'border:2px solid var(--color-principal);' : '' }}">
                        <img src="{{ $gi->url }}" alt="{{ $gi->titulo }}"
                             onerror="this.style.background='var(--color-muy-claro)';">
                        <div class="mini-label">{{ ucfirst($gi->orden_comparativo ?? '—') }}</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        {{-- Metadatos --}}
        <div class="meta-card">
            <div style="font-size:.8rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.04em;margin-bottom:.9rem;">
                <i class="bi bi-info-circle me-1"></i> Información
            </div>
            <div class="meta-row">
                <span class="meta-lbl">Número</span>
                <span class="meta-val" style="font-family:monospace;">{{ $imagen->numero_imagen }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-lbl">Paciente</span>
                <span class="meta-val">
                    <a href="{{ route('pacientes.show', $imagen->paciente) }}" style="color:var(--color-principal);text-decoration:none;">{{ $imagen->paciente->nombre_completo }}</a>
                </span>
            </div>
            <div class="meta-row">
                <span class="meta-lbl">Tipo</span>
                <span class="meta-val"><i class="bi {{ $imagen->tipo_icono }} me-1"></i>{{ $imagen->tipo_label }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-lbl">Fecha de toma</span>
                <span class="meta-val">{{ $imagen->fecha_toma->format('d/m/Y') }}</span>
            </div>
            @if($imagen->diente)
            <div class="meta-row">
                <span class="meta-lbl">Diente</span>
                <span class="meta-val">{{ $imagen->diente }}</span>
            </div>
            @endif
            @if($imagen->evolucion)
            <div class="meta-row">
                <span class="meta-lbl">Evolución</span>
                <span class="meta-val">
                    <a href="{{ route('evoluciones.show', $imagen->evolucion) }}" style="color:var(--color-principal);text-decoration:none;">{{ $imagen->evolucion->numero_evolucion ?? '#'.$imagen->evolucion->id }}</a>
                </span>
            </div>
            @endif
            <div class="meta-row">
                <span class="meta-lbl">Tamaño</span>
                <span class="meta-val">{{ $imagen->tamanio_formateado }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-lbl">Formato</span>
                <span class="meta-val">{{ $imagen->archivo_tipo }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-lbl">Registrado por</span>
                <span class="meta-val">{{ $imagen->autor->name ?? '—' }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-lbl">Fecha registro</span>
                <span class="meta-val">{{ $imagen->created_at->format('d/m/Y H:i') }}</span>
            </div>
            @if($imagen->es_comparativo)
            <div class="meta-row">
                <span class="meta-lbl">Comparativo</span>
                <span class="meta-val">
                    <span style="background:#d1fae5;color:#166534;padding:.12rem .5rem;border-radius:20px;font-size:.7rem;font-weight:700;">Sí — {{ ucfirst($imagen->orden_comparativo ?? '') }}</span>
                </span>
            </div>
            @endif
        </div>

        <div style="display:flex;flex-direction:column;gap:.5rem;margin-top:1rem;">
            <a href="{{ $imagen->url }}" download="{{ $imagen->archivo_nombre }}" class="btn-morado" style="justify-content:center;">
                <i class="bi bi-download"></i> Descargar imagen
            </a>
            <a href="{{ route('imagenes.edit', $imagen) }}" class="btn-out" style="justify-content:center;">
                <i class="bi bi-pencil"></i> Editar metadatos
            </a>
            <a href="{{ route('imagenes.galeria', $imagen->paciente_id) }}" class="btn-gris" style="justify-content:center;">
                <i class="bi bi-arrow-left"></i> Volver a galería
            </a>
            <form method="POST" action="{{ route('imagenes.destroy', $imagen) }}" onsubmit="return confirm('¿Eliminar esta imagen? Esta acción no se puede deshacer.');" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-rojo" style="width:100%;justify-content:center;">
                    <i class="bi bi-trash"></i> Eliminar imagen
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
