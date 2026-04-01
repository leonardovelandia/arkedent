@extends('layouts.app')
@section('titulo', 'Iniciar Retención')

@section('contenido')

<div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <a href="{{ route('ortodoncia.show', $ficha) }}" style="color:var(--texto-secundario);text-decoration:none;font-size:.84rem;">
        <i class="bi bi-arrow-left"></i> {{ $ficha->numero_ficha }}
    </a>
    <i class="bi bi-chevron-right" style="font-size:.7rem;color:var(--texto-secundario);"></i>
    <span style="font-size:.84rem;font-weight:600;">Iniciar Retención</span>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#7f1d1d;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <ul style="margin:0;padding-left:1.2rem;">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('ortodoncia.retencion.store', $ficha) }}">
@csrf
<input type="hidden" name="ficha_ortodontica_id" value="{{ $ficha->id }}">
<input type="hidden" name="paciente_id" value="{{ $ficha->paciente_id }}">

<div class="card-sistema">
    <h5 style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
        <i class="bi bi-shield-check me-2"></i>Fase de Retención — {{ $ficha->paciente->nombre_completo }}
    </h5>

    <div class="row g-3">
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Ortodoncista <span style="color:#dc2626;">*</span></label>
            <select name="user_id" required style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                @foreach($ortodoncistas as $u)
                <option value="{{ $u->id }}" {{ old('user_id', auth()->id()) == $u->id?'selected':'' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Fecha retiro de brackets</label>
            <input type="date" name="fecha_retiro_brackets" value="{{ old('fecha_retiro_brackets', date('Y-m-d')) }}"
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Entrega del retenedor</label>
            <input type="date" name="fecha_entrega_retenedor" value="{{ old('fecha_entrega_retenedor') }}"
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Duración retención (meses)</label>
            <input type="number" name="duracion_retencion_meses" value="{{ old('duracion_retencion_meses', 24) }}" min="1"
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-6">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Tipo retenedor superior</label>
            <select name="tipo_retenedor_superior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Sin definir</option>
                @foreach(['fijo_alambre'=>'Fijo de alambre','removible_hawley'=>'Hawley removible','alineador_retencion'=>'Alineador de retención','ninguno'=>'Ninguno'] as $v=>$l)
                <option value="{{ $v }}" {{ old('tipo_retenedor_superior')==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Tipo retenedor inferior</label>
            <select name="tipo_retenedor_inferior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Sin definir</option>
                @foreach(['fijo_alambre'=>'Fijo de alambre','removible_hawley'=>'Hawley removible','alineador_retencion'=>'Alineador de retención','ninguno'=>'Ninguno'] as $v=>$l)
                <option value="{{ $v }}" {{ old('tipo_retenedor_inferior')==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Instrucciones de uso del retenedor</label>
            <textarea name="instrucciones_uso" rows="3" placeholder="Indicaciones de uso, limpieza, duración diaria..."
                      style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);resize:vertical;">{{ old('instrucciones_uso') }}</textarea>
        </div>
        <div class="col-12">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Notas adicionales</label>
            <textarea name="notas" rows="2" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);resize:vertical;">{{ old('notas') }}</textarea>
        </div>
    </div>

    <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--fondo-borde);display:flex;gap:.75rem;justify-content:flex-end;">
        <a href="{{ route('ortodoncia.show', $ficha) }}"
           style="padding:.55rem 1.25rem;border:1px solid var(--fondo-borde);border-radius:8px;color:var(--texto-secundario);text-decoration:none;font-size:.84rem;">
            Cancelar
        </a>
        <button type="submit"
                style="padding:.55rem 1.5rem;background:var(--color-principal);color:white;border:none;border-radius:8px;font-size:.84rem;font-weight:600;cursor:pointer;box-shadow:0 2px 8px var(--sombra-principal);">
            <i class="bi bi-shield-check me-1"></i> Registrar Retención
        </button>
    </div>
</div>
</form>

@endsection
