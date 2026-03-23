<div class="tabla-header">
    <span class="tabla-titulo"><i class="bi bi-flask" style="color:var(--color-principal);"></i> Órdenes de Laboratorio</span>
    <div style="display:flex; gap:.5rem;">
        <a href="{{ route('gestion-laboratorios.index') }}"
           style="display:inline-flex;align-items:center;gap:.3rem;font-size:.83rem;color:var(--color-principal);text-decoration:none;border:1px solid var(--color-principal);border-radius:8px;padding:.35rem .8rem;">
            <i class="bi bi-building"></i> Gestionar Laboratorios
        </a>
        <a href="{{ route('laboratorio.create') }}" class="btn-morado">
            <i class="bi bi-plus-lg"></i> Nueva Orden
        </a>
    </div>
</div>

@if($ordenes->count() > 0)
<div style="overflow-x:auto;">
    <table>
        <thead>
            <tr>
                <th>N° Orden</th>
                <th>Paciente</th>
                <th>Laboratorio</th>
                <th>Tipo Trabajo</th>
                <th>Dientes</th>
                <th>Fecha Envío</th>
                <th>Entrega Est.</th>
                <th>Días Rest.</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ordenes as $orden)
            <tr>
                <td>
                    <span class="badge-lab badge-morado">{{ $orden->numero_orden }}</span>
                </td>
                <td style="font-weight:500;">{{ $orden->paciente->nombre_completo ?? '—' }}</td>
                <td style="font-size:.82rem;">{{ $orden->laboratorio->nombre ?? '—' }}</td>
                <td style="font-size:.82rem;">{{ $orden->tipo_trabajo }}</td>
                <td style="font-size:.82rem; color:var(--color-principal);">{{ $orden->dientes ?: '—' }}</td>
                <td style="font-size:.82rem;">{{ $orden->fecha_envio?->format('d/m/Y') ?: '—' }}</td>
                <td style="font-size:.82rem; {{ $orden->esta_vencido ? 'color:#dc2626; font-weight:600;' : '' }}">
                    {{ $orden->fecha_entrega_estimada?->format('d/m/Y') ?: '—' }}
                    @if($orden->esta_vencido)
                        <i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;"></i>
                    @endif
                </td>
                <td>
                    @if($orden->dias_restantes !== null && !in_array($orden->estado, ['recibido','instalado','cancelado']))
                        @php $dias = $orden->dias_restantes; @endphp
                        <span class="badge-lab {{ $dias > 5 ? 'dias-verde' : ($dias >= 1 ? 'dias-amarillo' : 'dias-rojo') }}">
                            {{ $dias > 0 ? '+' . $dias : $dias }} días
                        </span>
                    @else
                        —
                    @endif
                </td>
                <td>
                    <span class="badge-lab badge-{{ $orden->estado_color }}">{{ $orden->estado_label }}</span>
                </td>
                <td>
                    <div style="display:flex; gap:.3rem; flex-wrap:wrap;">
                        <a href="{{ route('laboratorio.show', $orden) }}" class="accion-btn accion-ver">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                        @if(!in_array($orden->estado, ['instalado','cancelado']))
                        <a href="{{ route('laboratorio.edit', $orden) }}" class="accion-btn accion-edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($ordenes->hasPages())
<div style="padding:.875rem 1.25rem; border-top:1px solid var(--fondo-borde);">
    {{ $ordenes->links() }}
</div>
@endif

@else
<div class="vacio">
    <i class="bi bi-flask"></i>
    <p>No hay órdenes de laboratorio registradas</p>
</div>
@endif
