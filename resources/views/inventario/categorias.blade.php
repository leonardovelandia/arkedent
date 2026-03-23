@extends('layouts.app')
@section('titulo', 'Categorías de Inventario')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; transition:filter .18s; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; display:inline-flex; align-items:center; gap:.3rem; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; color:#374151; }

    .grid-2 { display:grid; grid-template-columns:3fr 2fr; gap:1.25rem; align-items:start; }
    @media(max-width:800px){ .grid-2{ grid-template-columns:1fr; } }

    .panel-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .panel-header { padding:.8rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; }
    .panel-titulo { font-family:var(--fuente-principal); font-size:.72rem; font-weight:600; color:var(--color-hover); display:flex; align-items:center; gap:.4rem; }
    .panel-titulo i { color:var(--color-principal); }

    .tabla-cat { width:100%; border-collapse:collapse; font-size:.82rem; }
    .tabla-cat th { font-size:.69rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-principal); padding:.5rem .75rem; border-bottom:2px solid var(--color-muy-claro); text-align:left; }
    .tabla-cat td { padding:.55rem .75rem; border-bottom:1px solid var(--fondo-borde); color:#374151; vertical-align:middle; }
    .tabla-cat tr:last-child td { border-bottom:none; }
    .tabla-cat tr:hover td { background:var(--fondo-card-alt); }

    .form-label { font-size:.76rem; font-weight:700; color:var(--color-hover); display:block; margin-bottom:.2rem; }
    .form-input { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .75rem; font-size:.84rem; color:#1c2b22; background:#fff; outline:none; box-sizing:border-box; }
    .form-input:focus { border-color:var(--color-principal); }
    .is-invalid { border-color:#dc2626; }
    .invalid-feedback { font-size:.73rem; color:#dc2626; margin-top:.15rem; }
    .acc-btn { display:inline-flex; align-items:center; gap:.25rem; padding:.22rem .55rem; border-radius:6px; font-size:.74rem; font-weight:500; text-decoration:none; border:none; cursor:pointer; }
    .acc-edit { background:#f3f4f6; color:#374151; }
    .acc-edit:hover { background:#e5e7eb; }
    .acc-del { background:#fee2e2; color:#dc2626; }
    .acc-del:hover { background:#fecaca; }
</style>
@endpush

@section('contenido')

<div style="display:flex; align-items:center; gap:.75rem; margin-bottom:1.25rem; flex-wrap:wrap;">
    <a href="{{ route('inventario.index') }}"
       style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 style="font-family:var(--fuente-titulos); font-weight:700; color:#1c2b22; margin:0;">Categorías de Inventario</h4>
        <p style="font-size:.82rem; color:#9ca3af; margin:0;">Gestiona las categorías de materiales</p>
    </div>
</div>

@if(session('exito'))
<div style="background:#dcfce7; color:#166534; border:1px solid #bbf7d0; border-radius:8px; padding:.65rem 1rem; margin-bottom:1rem; font-size:.84rem;">
    <i class="bi bi-check-circle"></i> {{ session('exito') }}
</div>
@endif
@if(session('error'))
<div style="background:#fee2e2; color:#dc2626; border:1px solid #fecaca; border-radius:8px; padding:.65rem 1rem; margin-bottom:1rem; font-size:.84rem;">
    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<div class="grid-2">
    {{-- Lista de categorías --}}
    <div class="panel-card">
        <div class="panel-header">
            <div class="panel-titulo"><i class="bi bi-tags"></i> Categorías existentes</div>
            <span style="font-size:.78rem; color:#9ca3af;">{{ $categorias->count() }} categorías</span>
        </div>
        @if($categorias->isEmpty())
            <div style="padding:2rem; text-align:center; color:#9ca3af; font-size:.85rem;">
                <i class="bi bi-tags" style="font-size:1.8rem; display:block; margin-bottom:.4rem;"></i>
                No hay categorías creadas.
            </div>
        @else
        <table class="tabla-cat">
            <thead>
                <tr>
                    <th>Color</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th style="text-align:center;">Materiales</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($categorias as $cat)
            <tr>
                <td>
                    <span style="display:inline-block; width:20px; height:20px; border-radius:50%; background:{{ $cat->color ?? 'var(--color-principal)' }}; border:1px solid rgba(0,0,0,.1);"></span>
                </td>
                <td style="font-weight:500;">{{ $cat->nombre }}</td>
                <td style="font-size:.78rem; color:#6b7280; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $cat->descripcion }}">
                    {{ $cat->descripcion ?: '—' }}
                </td>
                <td style="text-align:center;">
                    <span style="font-weight:600; color:var(--color-principal);">{{ $cat->materiales_count }}</span>
                </td>
                <td>
                    <div style="display:flex; gap:.3rem;">
                        <a href="{{ route('inventario-categorias.edit', $cat) }}" class="acc-btn acc-edit"><i class="bi bi-pencil"></i></a>
                        @if($cat->materiales_count === 0)
                        <form method="POST" action="{{ route('inventario-categorias.destroy', $cat) }}"
                              onsubmit="return confirm('¿Eliminar la categoría {{ $cat->nombre }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="acc-btn acc-del"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Formulario nueva/editar categoría --}}
    <div class="panel-card">
        <div class="panel-header">
            <div class="panel-titulo">
                <i class="bi bi-{{ isset($categoria) ? 'pencil' : 'plus-circle' }}"></i>
                {{ isset($categoria) ? 'Editar categoría' : 'Nueva categoría' }}
            </div>
            @if(isset($categoria))
            <a href="{{ route('inventario-categorias.index') }}" class="btn-gris" style="font-size:.78rem; padding:.25rem .6rem;">Cancelar</a>
            @endif
        </div>
        <div style="padding:1.25rem;">
            @if(isset($categoria))
            <form method="POST" action="{{ route('inventario-categorias.update', $categoria) }}">
                @csrf @method('PUT')
            @else
            <form method="POST" action="{{ route('inventario-categorias.store') }}">
                @csrf
            @endif
                <div style="margin-bottom:.75rem;">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="nombre" class="form-input @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $categoria->nombre ?? '') }}" required placeholder="Ej: Anestesia">
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom:.75rem;">
                    <label class="form-label">Color identificador</label>
                    <div style="display:flex; align-items:center; gap:.75rem;">
                        <input type="color" name="color" class="form-input" style="height:40px; padding:.2rem; width:60px; cursor:pointer;"
                               value="{{ old('color', $categoria->color ?? 'var(--color-principal)') }}">
                        <span style="font-size:.78rem; color:#6b7280;">Selecciona el color para identificar la categoría</span>
                    </div>
                </div>
                <div style="margin-bottom:1rem;">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-input" rows="3"
                              placeholder="Descripción opcional…">{{ old('descripcion', $categoria->descripcion ?? '') }}</textarea>
                </div>
                <button type="submit" class="btn-morado" style="width:100%; justify-content:center;">
                    <i class="bi bi-check-lg"></i>
                    {{ isset($categoria) ? 'Guardar Cambios' : 'Crear Categoría' }}
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
