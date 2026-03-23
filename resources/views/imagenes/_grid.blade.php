@if($imagenes->isEmpty())
<div style="text-align:center;padding:3rem 1rem;background:#fff;border-radius:12px;border:1px solid var(--fondo-borde);">
    <i class="bi bi-images" style="font-size:3rem;color:var(--color-acento-activo);display:block;margin-bottom:1rem;"></i>
    <p style="font-weight:600;color:#4b5563;font-size:1rem;">No hay imágenes registradas</p>
    <p style="font-size:.875rem;color:#9ca3af;margin-bottom:1.25rem;">Comienza subiendo imágenes clínicas de tus pacientes.</p>
    <a href="{{ route('imagenes.create') }}" class="btn-morado">
        <i class="bi bi-cloud-upload"></i> Subir Primera Imagen
    </a>
</div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem;">
    @foreach($imagenes as $img)
    <div class="img-card">
        <a href="{{ route('imagenes.show', $img) }}">
            <img src="{{ $img->url }}" alt="{{ $img->titulo }}" class="img-thumb"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
            <div class="img-thumb-placeholder" style="display:none;">
                <i class="bi {{ $img->tipo_icono }}"></i>
            </div>
        </a>
        <div class="img-meta">
            <div class="img-badge"><i class="bi {{ $img->tipo_icono }}"></i> {{ $img->tipo_label }}</div>
            <div style="font-family:monospace;font-size:.7rem;color:#9ca3af;margin-bottom:.15rem;">{{ $img->numero_imagen }}</div>
            <div class="img-titulo" title="{{ $img->titulo }}">{{ $img->titulo }}</div>
            <div class="img-paciente">{{ $img->paciente->nombre_completo }}</div>
            <div class="img-fecha"><i class="bi bi-calendar3" style="font-size:.7rem;"></i> {{ $img->fecha_toma->format('d/m/Y') }}</div>
        </div>
        <div class="img-acciones">
            <a href="{{ route('imagenes.show', $img) }}" class="btn-out" style="font-size:.75rem;padding:.25rem .6rem;flex:1;justify-content:center;">
                <i class="bi bi-eye"></i>
            </a>
            <a href="{{ route('imagenes.edit', $img) }}" class="btn-gris" style="font-size:.75rem;padding:.25rem .6rem;">
                <i class="bi bi-pencil"></i>
            </a>
            <form method="POST" action="{{ route('imagenes.destroy', $img) }}" onsubmit="return confirm('¿Eliminar esta imagen?');" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;padding:.25rem .5rem;color:#dc2626;cursor:pointer;font-size:.82rem;">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

{{ $imagenes->links() }}
@endif
