@extends('layouts.app')
@section('titulo', 'Editar Laboratorio')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .form-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); margin-bottom:1rem; }
    .form-card-header { background:linear-gradient(135deg,var(--color-principal),var(--color-sidebar-2)); padding:1rem 1.5rem; }
    .form-card-header h3 { color:#fff; font-size:.95rem; font-weight:600; margin:0; display:flex; align-items:center; gap:.5rem; }
    .form-body { padding:1.25rem 1.5rem; }
    .form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .form-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; }
    .form-group { display:flex; flex-direction:column; gap:.35rem; margin-bottom:.875rem; }
    .form-group label { font-size:.78rem; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.04em; }
    .form-group label span { color:#dc2626; }
    .form-control { border:1px solid #e5e7eb; border-radius:8px; padding:.5rem .875rem; font-size:.9rem; outline:none; transition:border-color .15s; width:100%; font-family:inherit; }
    .form-control:focus { border-color:var(--color-principal); }
    .check-grid { display:grid; grid-template-columns:1fr 1fr; gap:.5rem; }
    .check-item { display:flex; align-items:center; gap:.5rem; }
    .check-item label { font-size:.875rem; color:#374151; font-weight:400; text-transform:none; letter-spacing:0; cursor:pointer; }
    @media(max-width:700px) { .form-grid-2, .form-grid-3 { grid-template-columns:1fr; } }

    /* Clásico */
    body:not([data-ui="glass"]) .form-card { background:#fff; border:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .form-group label { color:#374151; }
    body:not([data-ui="glass"]) .form-control { border:1px solid #e5e7eb; background:#fff; }
    body:not([data-ui="glass"]) .check-item label { color:#374151; }

    /* Glass */
    body[data-ui="glass"] .form-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .form-group label { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .form-control { background:rgba(255,255,255,0.08) !important; border:1px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .form-control:focus { border-color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .form-control::placeholder { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .check-item label { color:rgba(255,255,255,0.88) !important; }
    body[data-ui="glass"] .page-title-main { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .page-title-sub  { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .btn-volver { background:transparent !important; border:1px solid rgba(0,234,255,0.50) !important; color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .btn-gris   { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
</style>
@endpush

@section('contenido')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
    <div>
        <h1 style="font-family:var(--fuente-titulos); font-size:1.3rem; color:#1c2b22; margin:0;">Editar Laboratorio</h1>
        <p style="font-size:.83rem; color:#8fa39a; margin:.2rem 0 0;">{{ $laboratorio->nombre }}</p>
    </div>
    <a href="{{ route('gestion-laboratorios.index') }}"
       style="display:inline-flex;align-items:center;gap:.3rem;font-size:.83rem;color:var(--color-principal);text-decoration:none;border:1px solid var(--color-principal);border-radius:8px;padding:.4rem .9rem;">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:.875rem 1.25rem;margin-bottom:1rem;">
    <ul style="margin:0;padding-left:1.2rem;font-size:.85rem;color:#dc2626;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('gestion-laboratorios.update', $laboratorio) }}">
@csrf
@method('PUT')

<div class="form-card">
    <div class="form-card-header"><h3><i class="bi bi-building"></i> Información del Laboratorio</h3></div>
    <div class="form-body">
        <div class="form-group">
            <label>Nombre del Laboratorio <span>*</span></label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $laboratorio->nombre) }}" required>
        </div>
        <div class="form-grid-2">
            <div class="form-group">
                <label>Nombre del Contacto</label>
                <input type="text" name="contacto" class="form-control" value="{{ old('contacto', $laboratorio->contacto) }}">
            </div>
            <div class="form-group">
                <label>Ciudad</label>
                <input type="text" name="ciudad" class="form-control" value="{{ old('ciudad', $laboratorio->ciudad) }}">
            </div>
        </div>
        <div class="form-grid-3">
            <div class="form-group">
                <label>Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $laboratorio->telefono) }}">
            </div>
            <div class="form-group">
                <label>WhatsApp</label>
                <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $laboratorio->whatsapp) }}">
            </div>
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $laboratorio->email) }}">
            </div>
        </div>
        <div class="form-group">
            <label>Dirección</label>
            <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $laboratorio->direccion) }}">
        </div>
        <div class="form-group">
            <label>Estado</label>
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-weight:400;text-transform:none;letter-spacing:0;">
                <input type="checkbox" name="activo" value="1" {{ $laboratorio->activo ? 'checked' : '' }}>
                Laboratorio activo
            </label>
        </div>
    </div>
</div>

<div class="form-card">
    <div class="form-card-header"><h3><i class="bi bi-tools"></i> Especialidades y Servicios</h3></div>
    <div class="form-body">
        <div class="form-group">
            <label>Especialidades</label>
            <div class="check-grid">
                @php
                    $opciones = [
                        'coronas_puentes'    => 'Coronas y Puentes',
                        'protesis_removible' => 'Prótesis Removible',
                        'protesis_total'     => 'Prótesis Total',
                        'implantologia'      => 'Implantología',
                        'ortodoncia'         => 'Ortodoncia',
                        'estetica'           => 'Estética Dental',
                        'cirugia'            => 'Cirugía',
                    ];
                    $selected = old('especialidades', $laboratorio->especialidades ?? []);
                @endphp
                @foreach($opciones as $val => $texto)
                <div class="check-item">
                    <input type="checkbox" name="especialidades[]" value="{{ $val }}" id="esp-{{ $val }}"
                           {{ in_array($val, $selected) ? 'checked' : '' }}>
                    <label for="esp-{{ $val }}">{{ $texto }}</label>
                </div>
                @endforeach
            </div>
        </div>
        <div class="form-group">
            <label>Tiempo Promedio de Entrega (días)</label>
            <input type="number" name="tiempo_entrega_dias" class="form-control"
                   value="{{ old('tiempo_entrega_dias', $laboratorio->tiempo_entrega_dias) }}"
                   min="1" max="90" style="max-width:200px;">
        </div>
        <div class="form-group">
            <label>Notas</label>
            <textarea name="notas" class="form-control" rows="3">{{ old('notas', $laboratorio->notas) }}</textarea>
        </div>
    </div>
</div>

<div style="display:flex; gap:.75rem; margin-top:.5rem;">
    <button type="submit" class="btn-morado"><i class="bi bi-save"></i> Guardar Cambios</button>
    <a href="{{ route('gestion-laboratorios.index') }}"
       style="display:inline-flex;align-items:center;gap:.3rem;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem 1.1rem;font-size:.875rem;text-decoration:none;">
        Cancelar
    </a>
</div>
</form>
@endsection
