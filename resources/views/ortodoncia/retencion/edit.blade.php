@extends('layouts.app')
@section('titulo', 'Editar Retención')

@section('contenido')

<div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <a href="{{ route('ortodoncia.show', $retencion->fichaOrtodoncia) }}" style="color:var(--texto-secundario);text-decoration:none;font-size:.84rem;">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
    <i class="bi bi-chevron-right" style="font-size:.7rem;color:var(--texto-secundario);"></i>
    <span style="font-size:.84rem;font-weight:600;">Editar Retención</span>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#7f1d1d;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.84rem;">
    <ul style="margin:0;padding-left:1.2rem;">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('retencion.update', $retencion) }}">
@csrf
@method('PUT')

<div class="card-sistema">
    <div class="row g-3">
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Ortodoncista</label>
            <select name="user_id" required style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                @foreach($ortodoncistas as $u)
                <option value="{{ $u->id }}" {{ old('user_id',$retencion->user_id)==$u->id?'selected':'' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Fecha retiro brackets</label>
            <input type="date" name="fecha_retiro_brackets" value="{{ old('fecha_retiro_brackets', $retencion->fecha_retiro_brackets?->format('Y-m-d')) }}"
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Entrega retenedor</label>
            <input type="date" name="fecha_entrega_retenedor" value="{{ old('fecha_entrega_retenedor', $retencion->fecha_entrega_retenedor?->format('Y-m-d')) }}"
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-md-3">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Estado</label>
            <select name="estado" required style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                @foreach(['pendiente'=>'Pendiente','activa'=>'Activa','finalizada'=>'Finalizada'] as $v=>$l)
                <option value="{{ $v }}" {{ old('estado',$retencion->estado)==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Tipo retenedor superior</label>
            <select name="tipo_retenedor_superior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Sin definir</option>
                @foreach(['fijo_alambre'=>'Fijo de alambre','removible_hawley'=>'Hawley removible','alineador_retencion'=>'Alineador de retención','ninguno'=>'Ninguno'] as $v=>$l)
                <option value="{{ $v }}" {{ old('tipo_retenedor_superior',$retencion->tipo_retenedor_superior)==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Tipo retenedor inferior</label>
            <select name="tipo_retenedor_inferior" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                <option value="">Sin definir</option>
                @foreach(['fijo_alambre'=>'Fijo de alambre','removible_hawley'=>'Hawley removible','alineador_retencion'=>'Alineador de retención','ninguno'=>'Ninguno'] as $v=>$l)
                <option value="{{ $v }}" {{ old('tipo_retenedor_inferior',$retencion->tipo_retenedor_inferior)==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Duración (meses)</label>
            <input type="number" name="duracion_retencion_meses" value="{{ old('duracion_retencion_meses',$retencion->duracion_retencion_meses) }}" min="1"
                   style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
        </div>
        <div class="col-12">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Instrucciones de uso</label>
            <textarea name="instrucciones_uso" rows="3" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);resize:vertical;">{{ old('instrucciones_uso',$retencion->instrucciones_uso) }}</textarea>
        </div>
        <div class="col-12">
            <label style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Notas</label>
            <textarea name="notas" rows="2" style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);resize:vertical;">{{ old('notas',$retencion->notas) }}</textarea>
        </div>
    </div>

    <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--fondo-borde);display:flex;gap:.75rem;justify-content:flex-end;">
        <a href="{{ route('ortodoncia.show', $retencion->fichaOrtodoncia) }}"
           style="padding:.55rem 1.25rem;border:1px solid var(--fondo-borde);border-radius:8px;color:var(--texto-secundario);text-decoration:none;font-size:.84rem;">
            Cancelar
        </a>
        <button type="submit"
                style="padding:.55rem 1.5rem;background:var(--color-principal);color:white;border:none;border-radius:8px;font-size:.84rem;font-weight:600;cursor:pointer;">
            <i class="bi bi-check-lg me-1"></i> Guardar
        </button>
    </div>
</div>
</form>

@endsection
