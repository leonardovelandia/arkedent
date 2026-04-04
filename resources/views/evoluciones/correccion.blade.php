@extends('layouts.app')
@section('titulo', 'Nota de Corrección — Evolución Clínica')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.2rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.45rem 1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.35rem; transition:background .15s; text-decoration:none; }
    .btn-gris:hover { background:#e5e7eb; color:#1f2937; }

    .banner-bloqueo { border-radius:10px; padding:1rem 1.25rem; margin-bottom:1.5rem; display:flex; gap:.85rem; align-items:flex-start; }
    .banner-firmado  { background:#fffbeb; border:1px solid #fcd34d; }
    .banner-24h      { background:#fff7ed; border:1px solid #fed7aa; }
    .banner-ico      { font-size:1.4rem; flex-shrink:0; margin-top:.05rem; }
    .banner-firmado .banner-ico  { color:#d97706; }
    .banner-24h .banner-ico      { color:#ea580c; }
    .banner-titulo { font-weight:700; font-size:.9rem; margin-bottom:.2rem; }
    .banner-firmado .banner-titulo { color:#92400e; }
    .banner-24h .banner-titulo     { color:#7c2d12; }
    .banner-texto { font-size:.82rem; line-height:1.5; }
    .banner-firmado .banner-texto { color:#78350f; }
    .banner-24h .banner-texto     { color:#9a3412; }

    .card-sec { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; margin-bottom:1.25rem; overflow:hidden; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .card-sec-head { background:var(--color-muy-claro); padding:.75rem 1.25rem; border-bottom:1px solid var(--color-muy-claro); display:flex; align-items:center; gap:.5rem; }
    .card-sec-head h6 { margin:0; font-size:.82rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover); }
    .card-sec-body { padding:1.25rem; }

    .form-lbl { font-size:.8rem; font-weight:600; color:#374151; margin-bottom:.35rem; display:block; }
    .form-ctrl { width:100%; border:1px solid #d1d5db; border-radius:8px; padding:.55rem .85rem; font-size:.9rem; color:#1c2b22; transition:border-color .15s; }
    .form-ctrl:focus { outline:none; border-color:var(--color-principal); box-shadow:0 0 0 3px var(--sombra-principal); }
    select.form-ctrl { background:#fff; }
    .form-error { font-size:.78rem; color:#dc2626; margin-top:.25rem; }

    .valor-actual-box { background:var(--fondo-card-alt); border:1px solid var(--color-muy-claro); border-radius:8px; padding:.65rem .9rem; margin-top:.45rem; font-size:.85rem; color:#4b5563; min-height:2.5rem; }
    .valor-actual-lbl { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#9ca3af; margin-bottom:.3rem; }

    .nota-legal { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:.65rem .9rem; font-size:.78rem; color:#64748b; display:flex; align-items:flex-start; gap:.5rem; margin-top:.75rem; }

    .resumen-dato { display:flex; gap:.5rem; align-items:baseline; margin-bottom:.4rem; }
    .resumen-lbl { font-size:.75rem; font-weight:700; text-transform:uppercase; color:#9ca3af; min-width:110px; }
    .resumen-val { font-size:.88rem; color:#1c2b22; }

    /* ── Classic overrides ── */
    body:not([data-ui="glass"]) .card-sec      { background:#fff; border:1px solid var(--fondo-borde); }
    body:not([data-ui="glass"]) .card-sec-head { background:var(--color-muy-claro); border-bottom:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .card-sec-head h6 { color:var(--color-hover); }
    body:not([data-ui="glass"]) .form-ctrl     { border:1px solid #d1d5db; color:#1c2b22; background:#fff; }
    body:not([data-ui="glass"]) .valor-actual-box { background:var(--fondo-card-alt); }
    body:not([data-ui="glass"]) .nota-legal    { background:#f8fafc; border:1px solid #e2e8f0; color:#64748b; }

    /* ── Aurora Glass overrides ── */
    body[data-ui="glass"] .banner-firmado  { background:rgba(217,119,6,0.12) !important; border-color:rgba(252,211,77,0.40) !important; }
    body[data-ui="glass"] .banner-24h      { background:rgba(234,88,12,0.12) !important; border-color:rgba(254,215,170,0.40) !important; }
    body[data-ui="glass"] .banner-firmado .banner-titulo,
    body[data-ui="glass"] .banner-24h .banner-titulo { color:#fde68a !important; }
    body[data-ui="glass"] .banner-firmado .banner-texto,
    body[data-ui="glass"] .banner-24h .banner-texto { color:rgba(255,255,255,0.75) !important; }
    body[data-ui="glass"] .card-sec      { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.35) !important; box-shadow:0 0 8px rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .card-sec-head { background:rgba(0,0,0,0.25) !important; border-bottom:1px solid rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .card-sec-head h6 { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .form-lbl  { color:rgba(0,234,255,0.85) !important; }
    body[data-ui="glass"] .form-ctrl { background:rgba(255,255,255,0.08) !important; border:1px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .form-ctrl:focus { border-color:rgba(0,234,255,0.70) !important; box-shadow:none !important; }
    body[data-ui="glass"] .form-ctrl::placeholder { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .valor-actual-box { background:rgba(0,0,0,0.25) !important; border-color:rgba(0,234,255,0.20) !important; color:rgba(255,255,255,0.70) !important; }
    body[data-ui="glass"] .nota-legal { background:rgba(0,234,255,0.06) !important; border-color:rgba(0,234,255,0.20) !important; color:rgba(255,255,255,0.65) !important; }
    body[data-ui="glass"] .resumen-val { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .btn-gris { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
</style>
@endpush

@section('contenido')

@if(session('aviso'))
    <div class="alerta-flash" style="background:#fffbeb;color:#92400e;border:1px solid #fcd34d;">
        <i class="bi bi-exclamation-triangle-fill"></i> {{ session('aviso') }}
    </div>
@endif

{{-- Banner informativo según motivo de bloqueo --}}
@if($evolucion->firmado)
<div class="banner-bloqueo banner-firmado">
    <div class="banner-ico"><i class="bi bi-lock-fill"></i></div>
    <div>
        <div class="banner-titulo">Evolución Firmada — No editable</div>
        <div class="banner-texto">
            Esta evolución fue firmada el {{ $evolucion->fecha_firma ? $evolucion->fecha_firma->format('d/m/Y \a \l\a\s H:i') : '—' }}.
            El contenido original <strong>no puede modificarse</strong>.<br>
            Puede agregar una nota de corrección que quedará registrada como anexo.
        </div>
    </div>
</div>
@else
<div class="banner-bloqueo banner-24h">
    <div class="banner-ico"><i class="bi bi-clock-history"></i></div>
    <div>
        <div class="banner-titulo">Período de edición cerrado — más de 24 horas</div>
        <div class="banner-texto">
            Esta evolución fue registrada el {{ $evolucion->created_at->format('d/m/Y \a \l\a\s H:i') }}
            y ya no puede editarse directamente.<br>
            Puede agregar una nota de corrección que quedará registrada como anexo.
        </div>
    </div>
</div>
@endif

{{-- Resumen del documento --}}
<div class="card-sec">
    <div class="card-sec-head"><i class="bi bi-clipboard2-pulse" style="color:var(--color-principal);"></i><h6>Documento Original</h6></div>
    <div class="card-sec-body">
        <div class="resumen-dato"><span class="resumen-lbl">Paciente</span><span class="resumen-val">{{ $evolucion->paciente->nombre_completo }}</span></div>
        <div class="resumen-dato"><span class="resumen-lbl">Fecha</span><span class="resumen-val">{{ $evolucion->fecha_formateada }}</span></div>
        <div class="resumen-dato"><span class="resumen-lbl">Procedimiento</span><span class="resumen-val">{{ $evolucion->procedimiento }}</span></div>
        <div class="resumen-dato"><span class="resumen-lbl">Estado</span>
            @if($evolucion->firmado)
                <span style="background:#d1fae5;color:#166534;padding:.15rem .55rem;border-radius:6px;font-size:.78rem;font-weight:700;"><i class="bi bi-check-circle-fill"></i> Firmada</span>
            @else
                <span style="background:#fff7ed;color:#c2410c;padding:.15rem .55rem;border-radius:6px;font-size:.78rem;font-weight:700;"><i class="bi bi-clock"></i> Sin firmar (+24h)</span>
            @endif
        </div>
    </div>
</div>

{{-- Formulario de corrección --}}
<div class="card-sec">
    <div class="card-sec-head"><i class="bi bi-pencil-square" style="color:var(--color-principal);"></i><h6>Nueva Nota de Corrección</h6></div>
    <div class="card-sec-body">

        @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.85rem;color:#991b1b;">
            <ul style="margin:0;padding-left:1.2rem;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('evoluciones.correccion', $evolucion) }}">
            @csrf

            {{-- Select campo --}}
            <div style="margin-bottom:1rem;">
                <label class="form-lbl" for="campo_corregido">Campo a corregir <span style="color:#dc2626;">*</span></label>
                <select name="campo_corregido" id="campo_corregido" class="form-ctrl" required
                    data-evolucion="{{ json_encode(collect($camposDisponibles)->keys()->mapWithKeys(fn($k) => [$k => $evolucion->$k ?? ''])) }}">
                    <option value="">— Seleccione el campo —</option>
                    @foreach($camposDisponibles as $key => $label)
                    <option value="{{ $key }}" {{ old('campo_corregido') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('campo_corregido')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Valor actual (solo lectura) --}}
            <div style="margin-bottom:1rem;">
                <div class="valor-actual-lbl">Valor actual del campo</div>
                <div class="valor-actual-box" id="valor-actual-texto">Seleccione un campo para ver el valor actual.</div>
            </div>

            {{-- Valor nuevo --}}
            <div style="margin-bottom:1rem;">
                <label class="form-lbl" for="valor_nuevo">Valor correcto <span style="color:#dc2626;">*</span></label>
                <textarea name="valor_nuevo" id="valor_nuevo" class="form-ctrl" rows="4" required
                    placeholder="Escriba el valor correcto para este campo...">{{ old('valor_nuevo') }}</textarea>
                @error('valor_nuevo')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Motivo --}}
            <div style="margin-bottom:1rem;">
                <label class="form-lbl" for="motivo">Motivo de la corrección <span style="color:#dc2626;">*</span></label>
                <textarea name="motivo" id="motivo" class="form-ctrl" rows="3" required minlength="10"
                    placeholder="Explique por qué se realiza esta corrección (mínimo 10 caracteres)...">{{ old('motivo') }}</textarea>
                @error('motivo')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Nota legal --}}
            <div class="nota-legal">
                <i class="bi bi-info-circle" style="color:var(--color-principal);flex-shrink:0;margin-top:.05rem;"></i>
                <span>Esta corrección quedará registrada con su nombre, fecha y hora. <strong>No reemplaza el documento original</strong> — se agrega como nota anexa al expediente.</span>
            </div>

            <div style="display:flex;gap:.75rem;margin-top:1.25rem;flex-wrap:wrap;">
                <button type="submit" class="btn-morado">
                    <i class="bi bi-check-lg"></i> Guardar Corrección
                </button>
                <a href="{{ route('evoluciones.show', $evolucion) }}" class="btn-gris">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Historial de correcciones --}}
<div class="card-sec">
    <div class="card-sec-head"><i class="bi bi-clock-history" style="color:var(--color-principal);"></i><h6>Historial de Correcciones ({{ $evolucion->correcciones->count() }})</h6></div>
    <div class="card-sec-body">
        @if($evolucion->correcciones->isEmpty())
            <div style="text-align:center;padding:1.5rem;color:#9ca3af;">
                <i class="bi bi-check2-circle" style="font-size:1.8rem;color:var(--color-acento-activo);display:block;margin-bottom:.5rem;"></i>
                Sin correcciones registradas
            </div>
        @else
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:.84rem;">
                <thead>
                    <tr>
                        <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.73rem;text-transform:uppercase;padding:.5rem .85rem;border-bottom:1px solid var(--color-muy-claro);white-space:nowrap;">Fecha y hora</th>
                        <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.73rem;text-transform:uppercase;padding:.5rem .85rem;border-bottom:1px solid var(--color-muy-claro);">Campo</th>
                        <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.73rem;text-transform:uppercase;padding:.5rem .85rem;border-bottom:1px solid var(--color-muy-claro);">Valor anterior</th>
                        <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.73rem;text-transform:uppercase;padding:.5rem .85rem;border-bottom:1px solid var(--color-muy-claro);">Valor nuevo</th>
                        <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.73rem;text-transform:uppercase;padding:.5rem .85rem;border-bottom:1px solid var(--color-muy-claro);">Motivo</th>
                        <th style="background:var(--color-muy-claro);color:var(--color-hover);font-weight:700;font-size:.73rem;text-transform:uppercase;padding:.5rem .85rem;border-bottom:1px solid var(--color-muy-claro);">Registrado por</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($evolucion->correcciones as $correccion)
                    <tr>
                        <td style="padding:.55rem .85rem;border-bottom:1px solid var(--fondo-borde);color:#6b7280;white-space:nowrap;font-size:.78rem;">{{ $correccion->created_at->format('d/m/Y H:i') }}</td>
                        <td style="padding:.55rem .85rem;border-bottom:1px solid var(--fondo-borde);font-weight:600;color:var(--color-principal);font-size:.82rem;">{{ $correccion->campo_label }}</td>
                        <td style="padding:.55rem .85rem;border-bottom:1px solid var(--fondo-borde);color:#9ca3af;text-decoration:line-through;font-size:.82rem;max-width:180px;">{{ Str::limit($correccion->valor_anterior, 80) }}</td>
                        <td style="padding:.55rem .85rem;border-bottom:1px solid var(--fondo-borde);color:#1c2b22;font-size:.82rem;max-width:180px;">{{ Str::limit($correccion->valor_nuevo, 80) }}</td>
                        <td style="padding:.55rem .85rem;border-bottom:1px solid var(--fondo-borde);color:#4b5563;font-size:.78rem;max-width:180px;">{{ Str::limit($correccion->motivo, 80) }}</td>
                        <td style="padding:.55rem .85rem;border-bottom:1px solid var(--fondo-borde);color:#6b7280;font-size:.78rem;white-space:nowrap;">{{ $correccion->usuario->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    var select = document.getElementById('campo_corregido');
    var boxTexto = document.getElementById('valor-actual-texto');
    var evolucionData = {};

    try {
        evolucionData = JSON.parse(select.getAttribute('data-evolucion') || '{}');
    } catch (e) {}

    function actualizarValorActual() {
        var campo = select.value;
        if (!campo) {
            boxTexto.textContent = 'Seleccione un campo para ver el valor actual.';
            boxTexto.style.color = '#9ca3af';
            return;
        }
        var val = evolucionData[campo];
        if (val === null || val === undefined || val === '') {
            boxTexto.textContent = '(Sin valor registrado)';
            boxTexto.style.color = '#9ca3af';
            boxTexto.style.fontStyle = 'italic';
        } else {
            boxTexto.textContent = typeof val === 'object' ? JSON.stringify(val) : val;
            boxTexto.style.color = '#374151';
            boxTexto.style.fontStyle = 'normal';
        }
    }

    select.addEventListener('change', actualizarValorActual);
    actualizarValorActual();
})();
</script>
@endpush
