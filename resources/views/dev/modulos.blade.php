@extends('layouts.dev')
@section('titulo', 'Panel de Módulos')

@push('estilos')
<style>
    .plan-card { background:#1a1a1a; border:1px solid #333; border-radius:10px; padding:1.25rem; margin-bottom:1rem; cursor:pointer; transition:border-color .2s; }
    .plan-card:hover { border-color:var(--acento); }
    .plan-card.activo { border-color:var(--acento); background:#1a0d2e; }
    .plan-nombre { font-size:1rem; font-weight:600; color:var(--acento-claro); }
    .plan-precio { font-size:.8rem; color:#888; }
    .plan-modulos { font-size:.72rem; color:#666; margin-top:.5rem; }
    .modulo-toggle { display:flex; align-items:center; justify-content:space-between; padding:.5rem .75rem; border-radius:6px; margin-bottom:.35rem; background:#1a1a1a; border:1px solid #2a2a2a; }
    .modulo-nombre { font-size:.82rem; color:#ccc; }
    .modulo-core { font-size:.65rem; color:#888; }
    .toggle-switch { position:relative; width:40px; height:20px; flex-shrink:0; }
    .toggle-switch input { opacity:0; width:0; height:0; }
    .toggle-slider { position:absolute; cursor:pointer; inset:0; background:#333; border-radius:20px; transition:.3s; }
    .toggle-slider:before { position:absolute; content:""; height:14px; width:14px; left:3px; bottom:3px; background:#666; border-radius:50%; transition:.3s; }
    input:checked + .toggle-slider { background:var(--acento); }
    input:checked + .toggle-slider:before { transform:translateX(20px); background:white; }
    .btn-guardar { background:var(--acento); color:white; border:none; padding:.75rem 2rem; border-radius:8px; font-size:.9rem; font-weight:600; cursor:pointer; width:100%; margin-top:1rem; }
    .btn-guardar:hover { background:var(--acento-claro); color:#1a1a1a; }
    .seccion-titulo { font-size:.65rem; font-weight:700; color:var(--acento); letter-spacing:.12em; text-transform:uppercase; margin:1rem 0 .5rem; }
    .badge-plan { font-size:.65rem; padding:2px 8px; border-radius:50px; background:var(--acento); color:white; margin-left:.5rem; }
    .info-box { background:#111; border:1px solid #2a2a2a; border-radius:8px; padding:1rem; margin-bottom:1.5rem; font-size:.78rem; color:#888; }
    .info-box code { color:var(--acento-claro); background:#1a0d2e; padding:2px 6px; border-radius:4px; }
</style>
@endpush

@section('contenido')
<div style="max-width:900px;margin:0 auto;">

    <div style="margin-bottom:1.5rem;">
        <div style="font-size:1.2rem;font-weight:700;color:#C084FC;">
            <i class="bi bi-toggles" style="margin-right:.5rem;"></i>Panel de Módulos
        </div>
        <div style="font-size:.78rem;color:#666;margin-top:.25rem;">Solo para uso del desarrollador — El cliente no ve esta pantalla</div>
    </div>

    <div class="info-box">
        <strong style="color:var(--acento-claro);">Plan activo:</strong>
        <code>{{ \App\Helpers\ModulosHelper::planActivo() }}</code>
        — Para cambiar el plan edita <code>config/modulos.php</code> o el <code>.env</code>
        (variable <code>PLAN_ACTIVO</code>) y ejecuta <code>php artisan config:clear</code>
    </div>

    @php
        $planActivo     = \App\Helpers\ModulosHelper::planActivo();
        $catalogo       = config('modulos.catalogo');
        $planes         = config('modulos.planes');
        $modulosActivos = \App\Helpers\ModulosHelper::modulosActivos();
    @endphp

    <div class="seccion-titulo">Seleccionar Plan</div>
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:.75rem;margin-bottom:1.5rem;">
        @foreach($planes as $key => $plan)
        <div class="plan-card {{ $planActivo === $key ? 'activo' : '' }}" onclick="seleccionarPlan('{{ $key }}', this)">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div class="plan-nombre">
                    {{ $plan['nombre'] }}
                    @if($planActivo === $key)<span class="badge-plan">ACTIVO</span>@endif
                </div>
                @if($plan['precio'] > 0)
                <div class="plan-precio">${{ number_format($plan['precio'], 0, ',', '.') }}/mes</div>
                @endif
            </div>
            <div class="plan-modulos">{{ count($plan['modulos'] ?? []) }} módulos incluidos</div>
        </div>
        @endforeach
    </div>

    <div id="panel-personalizado" style="{{ $planActivo === 'personalizado' ? '' : 'display:none;' }}">
        <div class="seccion-titulo">Módulos Activos — Plan Personalizado</div>

        <div class="seccion-titulo" style="color:#888;">Core (siempre recomendados)</div>
        @foreach($catalogo as $key => $modulo)
            @if($modulo['core'])
            <div class="modulo-toggle">
                <div>
                    <div class="modulo-nombre"><i class="bi {{ $modulo['icono'] }}" style="color:var(--acento);margin-right:6px;"></i>{{ $modulo['nombre'] }}</div>
                    <div class="modulo-core">Core — Plan {{ ucfirst($modulo['plan_minimo']) }}</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" value="{{ $key }}" {{ in_array($key, $modulosActivos) ? 'checked' : '' }} class="modulo-checkbox" data-modulo="{{ $key }}">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            @endif
        @endforeach

        <div class="seccion-titulo" style="color:#888;margin-top:1rem;">Gestión</div>
        @foreach($catalogo as $key => $modulo)
            @if(!$modulo['core'] && $modulo['plan_minimo'] === 'estandar')
            <div class="modulo-toggle">
                <div>
                    <div class="modulo-nombre"><i class="bi {{ $modulo['icono'] }}" style="color:#0D6EFD;margin-right:6px;"></i>{{ $modulo['nombre'] }}</div>
                    <div class="modulo-core">Plan {{ ucfirst($modulo['plan_minimo']) }}</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" value="{{ $key }}" {{ in_array($key, $modulosActivos) ? 'checked' : '' }} class="modulo-checkbox" data-modulo="{{ $key }}">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            @endif
        @endforeach

        <div class="seccion-titulo" style="color:#888;margin-top:1rem;">Premium y Especialidades</div>
        @foreach($catalogo as $key => $modulo)
            @if(!$modulo['core'] && $modulo['plan_minimo'] === 'premium')
            <div class="modulo-toggle">
                <div>
                    <div class="modulo-nombre"><i class="bi {{ $modulo['icono'] }}" style="color:var(--acento-claro);margin-right:6px;"></i>{{ $modulo['nombre'] }}</div>
                    <div class="modulo-core">Plan {{ ucfirst($modulo['plan_minimo']) }}</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" value="{{ $key }}" {{ in_array($key, $modulosActivos) ? 'checked' : '' }} class="modulo-checkbox" data-modulo="{{ $key }}">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            @endif
        @endforeach
    </div>

    <div id="modulos-plan" style="{{ $planActivo !== 'personalizado' ? '' : 'display:none;' }}">
        <div class="seccion-titulo">Módulos incluidos en este plan</div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;">
            @foreach($modulosActivos as $modulo)
            @if(isset($catalogo[$modulo]))
            <div style="background:#1a0d2e;border:1px solid #3a1d6e;border-radius:6px;padding:.4rem .75rem;font-size:.78rem;color:var(--acento-claro);">
                <i class="bi {{ $catalogo[$modulo]['icono'] }} me-1"></i>{{ $catalogo[$modulo]['nombre'] }}
            </div>
            @endif
            @endforeach
        </div>
    </div>

    <button class="btn-guardar" onclick="guardarCambios()">
        <i class="bi bi-floppy me-2"></i> Guardar configuración y limpiar caché
    </button>

    <div id="output" style="display:none;margin-top:1rem;background:#111;border:1px solid #333;border-radius:8px;padding:1rem;font-size:.78rem;color:#4ade80;white-space:pre-line;"></div>

</div>

<input type="hidden" id="plan-seleccionado" value="{{ $planActivo }}">
@endsection

@push('scripts')
<script>
function seleccionarPlan(plan, card) {
    document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('activo'));
    card.classList.add('activo');
    const pp = document.getElementById('panel-personalizado');
    const mp = document.getElementById('modulos-plan');
    if (plan === 'personalizado') { pp.style.display='block'; mp.style.display='none'; }
    else { pp.style.display='none'; mp.style.display='block'; }
    document.getElementById('plan-seleccionado').value = plan;
}

function guardarCambios() {
    const plan    = document.getElementById('plan-seleccionado').value;
    const modulos = [];
    document.querySelectorAll('.modulo-checkbox:checked').forEach(cb => modulos.push(cb.dataset.modulo));

    const output = document.getElementById('output');
    output.style.display = 'block';
    output.style.color   = '#4ade80';
    output.innerHTML     = '⏳ Guardando cambios...';

    fetch('/dev/modulos/guardar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ plan, modulos }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            output.innerHTML = '✅ ' + data.mensaje + '\n\n' + data.comandos.join('\n');
            setTimeout(() => location.reload(), 2000);
        } else {
            output.style.color = '#f87171';
            output.innerHTML   = '❌ Error: ' + data.error;
        }
    })
    .catch(err => {
        output.style.color = '#f87171';
        output.innerHTML   = '❌ Error de conexión: ' + err;
    });
}
</script>
@endpush
