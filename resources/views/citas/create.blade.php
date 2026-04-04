@extends('layouts.app')
@section('titulo', 'Nueva Cita')

@push('estilos')
<style>
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }

    .form-card { background:#fff; border:1px solid var(--color-muy-claro); border-radius:14px; padding:1.75rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); max-width:720px; margin:0 auto; }
    .form-card h5 { font-weight:700; color:var(--color-hover); font-size:1rem; margin-bottom:1.25rem; padding-bottom:.6rem; border-bottom:2px solid var(--color-muy-claro); }

    .campo-wrap { margin-bottom:1.1rem; }
    .campo-lbl { font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--color-principal); display:block; margin-bottom:.3rem; }
    .campo-ctrl { width:100%; border:1.5px solid var(--color-muy-claro); border-radius:8px; padding:.45rem .8rem; font-size:.9rem; color:#1c2b22; background:#fff; outline:none; transition:border-color .15s; font-family:inherit; }
    .campo-ctrl:focus { border-color:var(--color-principal); }
    .campo-ctrl.is-invalid { border-color:#dc2626; }
    .campo-hint { font-size:.75rem; color:#9ca3af; margin-top:.2rem; }
    .campo-error { font-size:.75rem; color:#dc2626; margin-top:.2rem; }

    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media(max-width:540px) { .form-row { grid-template-columns:1fr; } }

    /* Clásico */
    body:not([data-ui="glass"]) .form-card { background:#fff; border:1px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .form-card h5 { color:var(--color-hover); border-bottom:2px solid var(--color-muy-claro); }
    body:not([data-ui="glass"]) .campo-ctrl { border:1.5px solid var(--color-muy-claro); color:#1c2b22; background:#fff; }
    body:not([data-ui="glass"]) .campo-ctrl:focus { border-color:var(--color-principal); }

    /* Glass */
    body[data-ui="glass"] .form-card { background:rgba(255,255,255,0.10) !important; backdrop-filter:blur(20px) saturate(160%) !important; -webkit-backdrop-filter:blur(20px) saturate(160%) !important; border:1px solid rgba(0,234,255,0.45) !important; box-shadow:0 0 8px rgba(0,234,255,0.25) !important; }
    body[data-ui="glass"] .form-card h5 { color:rgba(0,234,255,0.90) !important; border-bottom:2px solid rgba(0,234,255,0.20) !important; }
    body[data-ui="glass"] .campo-lbl { color:rgba(0,234,255,0.90) !important; }
    body[data-ui="glass"] .campo-ctrl { border:1.5px solid rgba(0,234,255,0.30) !important; color:rgba(255,255,255,0.90) !important; background:rgba(255,255,255,0.08) !important; }
    body[data-ui="glass"] .campo-ctrl option, body[data-ui="glass"] .campo-ctrl optgroup { background: #0a2535 !important; color: rgba(255,255,255,0.88) !important; }
    body[data-ui="glass"] .campo-ctrl:focus { border-color:rgba(0,234,255,0.70) !important; }
    body[data-ui="glass"] .campo-ctrl::placeholder { color:rgba(255,255,255,0.30) !important; }
    body[data-ui="glass"] .campo-hint { color:rgba(255,255,255,0.55) !important; }
    body[data-ui="glass"] .btn-cancelar-link { background:rgba(255,255,255,0.08) !important; color:rgba(255,255,255,0.85) !important; border:1px solid rgba(255,255,255,0.20) !important; }
    body[data-ui="glass"] .aviso-cruce-box { background:rgba(251,191,36,0.12) !important; border:1px solid rgba(251,191,36,0.35) !important; }
    body[data-ui="glass"] .aviso-cruce-box span, body[data-ui="glass"] .aviso-cruce-box div { color:#fbbf24 !important; }
    /* page title */
    body[data-ui="glass"] .page-title-main { color:rgba(255,255,255,0.90) !important; }
    body[data-ui="glass"] .page-title-sub  { color:rgba(255,255,255,0.55) !important; }
</style>
@endpush

@section('contenido')

<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('citas.index') }}" style="color:var(--color-principal);font-size:1.1rem;"><i class="bi bi-arrow-left-circle-fill"></i></a>
    <div>
        <h4 style="font-family:var(--fuente-titulos);font-weight:700;color:#1c2b22;margin:0;">Nueva Cita</h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">Registrar nueva cita en la agenda</p>
    </div>
</div>

<div class="form-card">
    <h5><i class="bi bi-calendar-plus" style="color:var(--color-principal);"></i> Datos de la cita</h5>

    <form method="POST" action="{{ route('citas.store') }}">
    @csrf

    {{-- Paciente --}}
    <div class="campo-wrap">
        <label class="campo-lbl">Paciente <span style="color:#dc2626;">*</span></label>
        <x-buscador-paciente
            :pacientes="$pacientes"
            :valor-inicial="old('paciente_id', $paciente?->id)"
            campo-nombre="numero_documento" />
        @error('paciente_id')<span class="campo-error" style="font-size:.75rem;color:#dc2626;display:block;margin-top:.2rem;">{{ $message }}</span>@enderror
    </div>

    {{-- Fecha y Procedimiento --}}
    <div class="form-row">
        <div class="campo-wrap">
            <label class="campo-lbl">Fecha <span style="color:#dc2626;">*</span></label>
            <input type="date" id="fecha" name="fecha" class="campo-ctrl {{ $errors->has('fecha') ? 'is-invalid' : '' }}"
                   value="{{ old('fecha', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
            @error('fecha')<span class="campo-error">{{ $message }}</span>@enderror
        </div>
        <div class="campo-wrap">
            <label class="campo-lbl">Procedimiento <span style="color:#dc2626;">*</span></label>
            <input type="text" name="procedimiento" class="campo-ctrl {{ $errors->has('procedimiento') ? 'is-invalid' : '' }}"
                   value="{{ old('procedimiento') }}" placeholder="Ej: Limpieza dental, Extracción…">
            @error('procedimiento')<span class="campo-error">{{ $message }}</span>@enderror
        </div>
    </div>

    {{-- Horas --}}
    <div class="form-row">
        <div class="campo-wrap">
            <label class="campo-lbl">Hora inicio <span style="color:#dc2626;">*</span></label>
            <div class="timepicker-wrap">
                <i class="bi bi-clock timepicker-icon"></i>
                <input type="text" id="hora_inicio" name="hora_inicio" placeholder="HH:MM"
                       class="campo-ctrl timepicker {{ $errors->has('hora_inicio') ? 'is-invalid' : '' }}"
                       value="{{ old('hora_inicio') }}" autocomplete="off" readonly>
            </div>
            @error('hora_inicio')<span class="campo-error">{{ $message }}</span>@enderror
        </div>
        <div class="campo-wrap">
            <label class="campo-lbl">Hora fin <span style="color:#9ca3af;font-size:.7rem;font-weight:400;">(opcional)</span></label>
            <div class="timepicker-wrap">
                <i class="bi bi-clock timepicker-icon"></i>
                <input type="text" id="hora_fin" name="hora_fin" placeholder="HH:MM"
                       class="campo-ctrl timepicker {{ $errors->has('hora_fin') ? 'is-invalid' : '' }}"
                       value="{{ old('hora_fin') }}" autocomplete="off" readonly>
            </div>
            @error('hora_fin')<span class="campo-error">{{ $message }}</span>@enderror
        </div>
    </div>

    {{-- Aviso cruce de citas --}}
    <div id="aviso-cruce" style="display:none;margin-bottom:1rem;">
        <div style="background:#FFF3CD;border:1px solid #FFC107;border-radius:8px;padding:.75rem 1rem;">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.4rem;">
                <i class="bi bi-exclamation-triangle-fill" style="color:#856404;"></i>
                <span style="font-size:.83rem;font-weight:600;color:#856404;">⚠ Esta hora se cruza con otras citas</span>
            </div>
            <div id="lista-cruces" style="font-size:.8rem;color:#856404;"></div>
            <div style="font-size:.75rem;color:#856404;margin-top:.4rem;font-style:italic;">
                Puedes continuar asignando la cita, pero verifica la disponibilidad.
            </div>
        </div>
    </div>

    {{-- Estado --}}
    <div class="campo-wrap">
        <label class="campo-lbl">Estado</label>
        <select name="estado" class="campo-ctrl {{ $errors->has('estado') ? 'is-invalid' : '' }}">
            @foreach(['pendiente'=>'Pendiente','confirmada'=>'Confirmada','en_proceso'=>'En proceso','atendida'=>'Atendida','cancelada'=>'Cancelada','no_asistio'=>'No asistió'] as $val => $lbl)
            <option value="{{ $val }}" {{ old('estado','pendiente') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
        </select>
        @error('estado')<span class="campo-error">{{ $message }}</span>@enderror
    </div>

    {{-- Notas --}}
    <div class="campo-wrap">
        <label class="campo-lbl">Notas <span style="color:#9ca3af;font-size:.7rem;font-weight:400;">(opcional)</span></label>
        <textarea name="notas" rows="3" class="campo-ctrl {{ $errors->has('notas') ? 'is-invalid' : '' }}"
                  placeholder="Observaciones, indicaciones especiales…">{{ old('notas') }}</textarea>
        @error('notas')<span class="campo-error">{{ $message }}</span>@enderror
    </div>

    {{-- Botones --}}
    <div style="display:flex;gap:.5rem;justify-content:flex-end;padding-top:.5rem;border-top:1px solid var(--fondo-borde);margin-top:.5rem;">
        <a href="{{ route('citas.index') }}"
           style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.45rem 1rem;font-size:.875rem;display:inline-flex;align-items:center;gap:.3rem;text-decoration:none;">
            <i class="bi bi-x-lg"></i> Cancelar
        </a>
        <button type="submit" class="btn-morado">
            <i class="bi bi-check-lg"></i> Guardar Cita
        </button>
    </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
(function() {
    function verificarDisponibilidad(incluirHoraFin) {
        var fecha      = document.getElementById('fecha') ? document.getElementById('fecha').value : '';
        var horaInicio = document.getElementById('hora_inicio') ? document.getElementById('hora_inicio').value : '';
        var horaFin    = document.getElementById('hora_fin') ? document.getElementById('hora_fin').value : '';

        if (!fecha || !horaInicio) {
            document.getElementById('aviso-cruce').style.display = 'none';
            return;
        }

        var url = '/api/citas/disponibilidad?fecha=' + encodeURIComponent(fecha) + '&hora_inicio=' + encodeURIComponent(horaInicio);
        if (incluirHoraFin && horaFin) url += '&hora_fin=' + encodeURIComponent(horaFin);

        fetch(url, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            var avisoDiv = document.getElementById('aviso-cruce');
            var listaDiv = document.getElementById('lista-cruces');
            if (data.tiene_cruces) {
                var html = '<ul style="margin:0;padding-left:1rem;">';
                data.cruces.forEach(function(cruce) {
                    html += '<li><strong>' + cruce.paciente + '</strong> — ' +
                            cruce.hora_inicio + (cruce.hora_fin !== '--' ? ' a ' + cruce.hora_fin : '') +
                            ' — ' + cruce.procedimiento +
                            ' <span style="font-size:.7rem;">(' + cruce.estado + ')</span></li>';
                });
                html += '</ul>';
                listaDiv.innerHTML = html;
                avisoDiv.style.display = 'block';
            } else {
                avisoDiv.style.display = 'none';
                listaDiv.innerHTML = '';
            }
        })
        .catch(function() {
            document.getElementById('aviso-cruce').style.display = 'none';
        });
    }

    var campoFecha      = document.getElementById('fecha');
    var campoHoraInicio = document.getElementById('hora_inicio');
    var campoHoraFin    = document.getElementById('hora_fin');

    if (campoFecha)      campoFecha.addEventListener('change', function() { verificarDisponibilidad(false); });
    if (campoHoraInicio) campoHoraInicio.addEventListener('change', function() { verificarDisponibilidad(false); });
    if (campoHoraFin)    campoHoraFin.addEventListener('change', function() { verificarDisponibilidad(true); });
})();
</script>
@endpush
