@if($presupuestos->isEmpty())
<div style="text-align:center;padding:3rem 1rem;color:#9ca3af;">
    <i class="bi bi-file-earmark-text" style="font-size:2.5rem;color:var(--color-acento-activo);display:block;margin-bottom:.75rem;"></i>
    <p style="font-weight:600;color:#4b5563;margin-bottom:.25rem;">No hay presupuestos</p>
    <p style="font-size:.84rem;margin-bottom:1rem;">Crea el primer presupuesto para un paciente.</p>
    <a href="{{ route('presupuestos.create') }}" class="btn-morado" style="display:inline-flex;">
        <i class="bi bi-plus-lg"></i> Nuevo Presupuesto
    </a>
</div>
@else
<div style="overflow-x:auto;">
<table style="width:100%;border-collapse:collapse;">
    <thead>
        <tr class="tabla-header">
            <th>N° Presupuesto</th>
            <th>Paciente</th>
            <th>Fecha</th>
            <th>Vencimiento</th>
            <th style="text-align:right;">Total</th>
            <th>Estado</th>
            <th style="text-align:center;width:80px;">Acciones</th>
        </tr>
    </thead>
    <tbody>
    @foreach($presupuestos as $pre)
    @php
        $color = $pre->estado_color;
        $diasRestantes = $pre->dias_restantes;
    @endphp
    <tr class="tabla-fila">
        <td>
            <a href="{{ route('presupuestos.show', $pre) }}"
               style="font-family:monospace;font-weight:700;color:var(--color-principal);text-decoration:none;font-size:.82rem;">
                {{ $pre->numero_formateado }}
            </a>
        </td>
        <td>
            <div style="font-weight:600;color:#1c2b22;font-size:.875rem;">{{ $pre->paciente->nombre_completo }}</div>
            <div style="font-size:.75rem;color:#9ca3af;">{{ $pre->paciente->numero_historia }}</div>
        </td>
        <td style="font-size:.83rem;color:#4b5563;white-space:nowrap;">
            {{ $pre->fecha_generacion->format('d/m/Y') }}
        </td>
        <td style="white-space:nowrap;">
            <div style="font-size:.83rem;color:#4b5563;">{{ $pre->fecha_vencimiento->format('d/m/Y') }}</div>
            @if(in_array($pre->estado, ['borrador','enviado']))
                @if($diasRestantes < 0)
                    <span style="font-size:.7rem;color:#991b1b;font-weight:600;">Vencido</span>
                @elseif($diasRestantes <= 5)
                    <span class="por-vencer"><i class="bi bi-exclamation-triangle-fill"></i> {{ $diasRestantes }}d</span>
                @endif
            @endif
        </td>
        <td style="text-align:right;font-weight:700;color:#1c2b22;white-space:nowrap;">
            $ {{ number_format($pre->total, 0, ',', '.') }}
        </td>
        <td>
            <span class="badge-estado" style="background:{{ $color['bg'] }};color:{{ $color['text'] }};">
                {{ $color['label'] }}
            </span>
        </td>
        <td style="text-align:center;">
            <div style="display:flex;gap:.35rem;justify-content:center;">
                <a href="{{ route('presupuestos.show', $pre) }}"
                   style="color:var(--color-principal);font-size:.82rem;display:inline-flex;align-items:center;gap:.2rem;text-decoration:none;"
                   title="Ver">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('presupuestos.pdf', $pre) }}" target="_blank"
                   style="color:#374151;font-size:.82rem;display:inline-flex;align-items:center;gap:.2rem;text-decoration:none;"
                   title="PDF">
                    <i class="bi bi-file-pdf"></i>
                </a>
            </div>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
</div>

@if($presupuestos->hasPages())
<div style="padding:.75rem 1.25rem;border-top:1px solid var(--fondo-borde);">
    {{ $presupuestos->links() }}
</div>
@endif
@endif
