@if($evoluciones->isEmpty())
    <div class="vacio-msg">
        <i class="bi bi-clipboard2-pulse"></i>
        <p style="font-weight:600;color:#4b5563;">No se encontraron evoluciones</p>
        <a href="{{ route('evoluciones.create') }}" class="btn-morado mt-2">
            <i class="bi bi-clipboard2-plus"></i> Registrar primera evolución
        </a>
    </div>
@else
    <div style="overflow-x:auto;">
        <table class="tabla-evol">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>N° EVO</th>
                    <th>Procedimiento</th>
                    <th>Dientes</th>
                    <th>Fecha</th>
                    <th>Doctor</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evoluciones as $e)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.65rem;">
                            <span class="avatar-iniciales">
                                {{ strtoupper(substr($e->paciente->nombre,0,1)) }}{{ strtoupper(substr($e->paciente->apellido,0,1)) }}
                            </span>
                            <div>
                                <div style="font-weight:600;color:#1c2b22;">{{ $e->paciente->nombre_completo }}</div>
                                <div style="font-size:.78rem;color:#6b7280;">{{ $e->paciente->numero_documento }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-family:monospace;font-weight:700;color:#1d4ed8;background:#dbeafe;padding:.15rem .5rem;border-radius:6px;font-size:.82rem;">
                            {{ $e->numero_evolucion ?? ('#'.$e->id) }}
                        </span>
                    </td>
                    <td style="max-width:220px;">
                        <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;font-size:.84rem;font-weight:500;color:#374151;">
                            {{ $e->procedimiento }}
                        </span>
                    </td>
                    <td>
                        @if($e->dientes_tratados)
                            <span class="badge-dientes"><i class="bi bi-tooth"></i> {{ $e->dientes_tratados }}</span>
                        @else
                            <span style="color:#d1d5db;">—</span>
                        @endif
                    </td>
                    <td style="font-size:.84rem;color:#4b5563;white-space:nowrap;">
                        {{ $e->fecha_formateada }}
                        @if($e->hora)
                            <div style="font-size:.72rem;color:#9ca3af;">{{ \Carbon\Carbon::parse($e->hora)->format('h:i A') }}</div>
                        @endif
                    </td>
                    <td style="font-size:.84rem;color:#4b5563;">
                        {{ $e->doctor ? $e->doctor->name : '—' }}
                    </td>
                    <td>
                        <div style="display:flex;justify-content:center;gap:.35rem;">
                            <a href="{{ route('evoluciones.show', $e) }}" class="btn-accion" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('evoluciones.edit', $e) }}" class="btn-accion" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="{{ route('evoluciones.pdf', $e) }}" title="Ver PDF" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border:1px solid var(--color-muy-claro);border-radius:6px;color:var(--color-principal);text-decoration:none;">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($evoluciones->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid var(--fondo-borde);">
            {{ $evoluciones->links() }}
        </div>
    @endif
@endif
