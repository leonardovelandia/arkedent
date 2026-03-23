@if($valoraciones->isEmpty())
<div style="text-align:center;padding:3rem 1rem;">
    <i class="bi bi-clipboard2-pulse" style="font-size:2.5rem;color:var(--color-acento-activo);display:block;margin-bottom:.75rem;"></i>
    <p style="font-weight:600;color:#4b5563;">No hay valoraciones registradas</p>
    <a href="{{ route('valoraciones.create') }}" class="btn-morado" style="margin-top:.5rem;">
        <i class="bi bi-plus-lg"></i> Crear primera valoración
    </a>
</div>
@else
<div style="overflow-x:auto;">
<table style="width:100%;border-collapse:collapse;font-size:.855rem;">
    <thead>
        <tr style="background:var(--color-muy-claro);">
            <th style="padding:.6rem 1rem;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);text-align:left;border-bottom:2px solid var(--color-muy-claro);">N° / Fecha</th>
            <th style="padding:.6rem 1rem;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);text-align:left;border-bottom:2px solid var(--color-muy-claro);">Paciente</th>
            <th style="padding:.6rem 1rem;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);text-align:left;border-bottom:2px solid var(--color-muy-claro);">Motivo de consulta</th>
            <th style="padding:.6rem 1rem;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);text-align:center;border-bottom:2px solid var(--color-muy-claro);">Dx</th>
            <th style="padding:.6rem 1rem;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);text-align:center;border-bottom:2px solid var(--color-muy-claro);">Plan</th>
            <th style="padding:.6rem 1rem;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);text-align:left;border-bottom:2px solid var(--color-muy-claro);">Estado</th>
            <th style="padding:.6rem 1rem;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);text-align:center;border-bottom:2px solid var(--color-muy-claro);">Presupuesto</th>
            <th style="padding:.6rem 1rem;font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--color-hover);text-align:center;border-bottom:2px solid var(--color-muy-claro);">Acciones</th>
        </tr>
    </thead>
    <tbody>
    @foreach($valoraciones as $val)
    @php $ec = $val->estado_color; @endphp
    <tr style="border-bottom:1px solid var(--fondo-borde);">
        <td style="padding:.6rem 1rem;white-space:nowrap;">
            <div style="font-family:monospace;font-weight:700;color:var(--color-principal);font-size:.8rem;">{{ $val->numero_valoracion }}</div>
            <div style="font-size:.75rem;color:#9ca3af;">{{ $val->fecha->format('d/m/Y') }}</div>
        </td>
        <td style="padding:.6rem 1rem;">
            <div style="font-weight:600;color:#1c2b22;font-size:.875rem;">{{ $val->paciente->nombre_completo }}</div>
            <div style="font-size:.75rem;color:#9ca3af;">{{ $val->paciente->numero_historia }}</div>
        </td>
        <td style="padding:.6rem 1rem;color:#4b5563;max-width:220px;">
            {{ Str::limit($val->motivo_consulta, 65) }}
        </td>
        <td style="padding:.6rem 1rem;text-align:center;">
            @if(!empty($val->diagnosticos))
            <span style="background:var(--color-muy-claro);color:var(--color-principal);border-radius:20px;padding:.15rem .55rem;font-size:.72rem;font-weight:700;">{{ count($val->diagnosticos) }}</span>
            @else
            <span style="color:#d1d5db;">—</span>
            @endif
        </td>
        <td style="padding:.6rem 1rem;text-align:center;">
            @if(!empty($val->plan_tratamiento))
            <span style="background:#d1fae5;color:#166534;border-radius:20px;padding:.15rem .55rem;font-size:.72rem;font-weight:700;">{{ count($val->plan_tratamiento) }}</span>
            @else
            <span style="color:#d1d5db;">—</span>
            @endif
        </td>
        <td style="padding:.6rem 1rem;">
            <span style="display:inline-block;padding:.18rem .55rem;border-radius:20px;font-size:.7rem;font-weight:700;background:{{ $ec['bg'] }};color:{{ $ec['text'] }};">
                {{ $ec['label'] }}
            </span>
        </td>
        <td style="padding:.6rem 1rem;text-align:center;">
            @if($val->presupuesto_id)
            <a href="{{ route('presupuestos.show', $val->presupuesto_id) }}"
               style="background:#d1fae5;color:#166534;border-radius:20px;padding:.15rem .6rem;font-size:.7rem;font-weight:700;text-decoration:none;">
                <i class="bi bi-check-circle"></i> Ver
            </a>
            @elseif($val->estado === 'completada' && !empty($val->plan_tratamiento))
            <a href="{{ route('valoraciones.generar-presupuesto', $val) }}"
               onclick="return confirm('¿Generar presupuesto desde el plan de tratamiento?');"
               style="background:#fef9c3;color:#854d0e;border-radius:20px;padding:.15rem .6rem;font-size:.7rem;font-weight:700;text-decoration:none;">
                <i class="bi bi-plus-circle"></i> Generar
            </a>
            @else
            <span style="color:#d1d5db;font-size:.75rem;">—</span>
            @endif
        </td>
        <td style="padding:.6rem 1rem;text-align:center;">
            <div style="display:flex;gap:.35rem;justify-content:center;">
                <a href="{{ route('valoraciones.show', $val) }}" class="btn-out" style="padding:.22rem .55rem;font-size:.78rem;">
                    <i class="bi bi-eye"></i>
                </a>
                @if($val->estado === 'en_proceso')
                <a href="{{ route('valoraciones.edit', $val) }}" class="btn-gris" style="padding:.22rem .55rem;font-size:.78rem;">
                    <i class="bi bi-pencil"></i>
                </a>
                @endif
                @if(!$val->presupuesto_id && !empty($val->plan_tratamiento))
                <form method="POST" action="{{ route('valoraciones.generar-presupuesto', $val) }}" style="margin:0;">
                    @csrf
                    <button type="submit" onclick="return confirm('¿Generar presupuesto?');"
                            style="background:#d1fae5;color:#166534;border:1px solid #6ee7b7;border-radius:6px;padding:.22rem .5rem;font-size:.78rem;cursor:pointer;">
                        <i class="bi bi-file-earmark-plus"></i>
                    </button>
                </form>
                @endif
            </div>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
</div>
<div style="padding:.75rem 1rem;border-top:1px solid var(--fondo-borde);">
    {{ $valoraciones->links() }}
</div>
@endif
