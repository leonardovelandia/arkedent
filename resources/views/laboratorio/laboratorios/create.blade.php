@extends('layouts.app')
@section('titulo', 'Nuevo Laboratorio')

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
    .form-control.is-invalid { border-color:#dc2626; }
    .error-msg { font-size:.78rem; color:#dc2626; }
    .check-grid { display:grid; grid-template-columns:1fr 1fr; gap:.5rem; }
    .check-item { display:flex; align-items:center; gap:.5rem; }
    .check-item label { font-size:.875rem; color:#374151; font-weight:400; text-transform:none; letter-spacing:0; cursor:pointer; }
    @media(max-width:700px) { .form-grid-2, .form-grid-3 { grid-template-columns:1fr; } }
</style>
@endpush

@section('contenido')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
    <div>
        <h1 style="font-family:var(--fuente-titulos); font-size:1.3rem; color:#1c2b22; margin:0;">Nuevo Laboratorio</h1>
        <p style="font-size:.83rem; color:#8fa39a; margin:.2rem 0 0;">Registra un nuevo laboratorio dental externo</p>
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

<form method="POST" action="{{ route('gestion-laboratorios.store') }}">
@csrf

<div class="form-card">
    <div class="form-card-header"><h3><i class="bi bi-building"></i> Información del Laboratorio</h3></div>
    <div class="form-body">
        <div class="form-group">
            <label>Nombre del Laboratorio <span>*</span></label>
            <input type="text" name="nombre" class="form-control {{ $errors->has('nombre') ? 'is-invalid' : '' }}"
                   value="{{ old('nombre') }}" required placeholder="Ej: Laboratorio Dental Sonrisas">
            @error('nombre')<span class="error-msg">{{ $message }}</span>@enderror
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label>Nombre del Contacto</label>
                <input type="text" name="contacto" class="form-control" value="{{ old('contacto') }}" placeholder="Ej: Carlos Mendoza">
            </div>
            <div class="form-group">
                <label>Ciudad</label>
                <input type="text" name="ciudad" class="form-control" value="{{ old('ciudad') }}" placeholder="Ej: Medellín">
            </div>
        </div>

        <div class="form-grid-3">
            <div class="form-group">
                <label>Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}" placeholder="3001234567">
            </div>
            <div class="form-group">
                <label>WhatsApp</label>
                <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}" placeholder="3001234567">
            </div>
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="info@laboratorio.com">
            </div>
        </div>

        <div class="form-group">
            <label>Dirección</label>
            <input type="text" name="direccion" class="form-control" value="{{ old('direccion') }}" placeholder="Calle, número, barrio">
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
                    $selected = old('especialidades', []);
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
            <input type="number" name="tiempo_entrega_dias" class="form-control" value="{{ old('tiempo_entrega_dias') }}"
                   min="1" max="90" placeholder="Ej: 7" style="max-width:200px;">
        </div>

        <div class="form-group">
            <label>Notas</label>
            <textarea name="notas" class="form-control" rows="3" placeholder="Observaciones generales, recomendaciones...">{{ old('notas') }}</textarea>
        </div>
    </div>
</div>

<div style="display:flex; gap:.75rem; margin-top:.5rem;">
    <button type="submit" class="btn-morado"><i class="bi bi-save"></i> Guardar Laboratorio</button>
    <a href="{{ route('gestion-laboratorios.index') }}"
       style="display:inline-flex;align-items:center;gap:.3rem;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem 1.1rem;font-size:.875rem;text-decoration:none;">
        Cancelar
    </a>
</div>

</form>
@endsection
