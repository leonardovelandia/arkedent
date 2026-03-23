@extends('layouts.app')
@section('titulo', 'Pacientes')

@push('estilos')
<style>
    :root {
        --morado-base: var(--color-principal);
        --morado-claro: var(--color-claro);
        --morado-hover: var(--color-hover);
        --morado-muy-claro: var(--color-muy-claro);
    }

    .btn-morado {
        background: linear-gradient(135deg, var(--color-principal), var(--color-claro));
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1.1rem;
        font-size: 0.875rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: filter 0.18s;
        text-decoration: none;
    }
    .btn-morado:hover { filter: brightness(1.12); color: #fff; }

    .btn-outline-morado {
        background: transparent;
        color: var(--color-principal);
        border: 1px solid var(--color-principal);
        border-radius: 8px;
        padding: 0.45rem 1rem;
        font-size: 0.82rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: background 0.15s, color 0.15s;
        text-decoration: none;
    }
    .btn-outline-morado:hover {
        background: var(--color-muy-claro);
        color: var(--color-hover);
    }

    .tabla-pacientes {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.875rem;
    }
    .tabla-pacientes thead th {
        background: var(--color-muy-claro);
        color: var(--color-hover);
        font-weight: 600;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.7rem 1rem;
        border-bottom: 2px solid var(--color-muy-claro);
    }
    .tabla-pacientes thead th:first-child { border-radius: 8px 0 0 0; }
    .tabla-pacientes thead th:last-child  { border-radius: 0 8px 0 0; }

    .tabla-pacientes tbody tr {
        transition: background 0.12s;
    }
    .tabla-pacientes tbody tr:hover { background: var(--fondo-card-alt); }
    .tabla-pacientes tbody td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--fondo-borde);
        vertical-align: middle;
    }

    .avatar-pac {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--color-muy-claro);
    }
    .avatar-iniciales {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--color-principal), var(--color-claro));
        color: #fff;
        font-size: 0.78rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        letter-spacing: 0.03em;
        border: 2px solid var(--color-muy-claro);
    }

    .badge-activo {
        background: #dcfce7;
        color: #166534;
        border-radius: 20px;
        padding: 0.22rem 0.7rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-inactivo {
        background: #fee2e2;
        color: #991b1b;
        border-radius: 20px;
        padding: 0.22rem 0.7rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .btn-accion {
        background: none;
        border: 1px solid var(--color-muy-claro);
        border-radius: 7px;
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--color-principal);
        font-size: 0.9rem;
        transition: background 0.13s, color 0.13s;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-accion:hover {
        background: var(--color-muy-claro);
        color: var(--color-hover);
    }
    .btn-accion.danger {
        color: #dc2626;
        border-color: #fecaca;
    }
    .btn-accion.danger:hover {
        background: #fee2e2;
        color: #991b1b;
    }
    .btn-accion.success {
        color: #16a34a;
        border-color: #bbf7d0;
    }
    .btn-accion.success:hover {
        background: #dcfce7;
        color: #15803d;
    }
    .btn-accion.eliminar {
        color: #9f1239;
        border-color: #fecdd3;
    }
    .btn-accion.eliminar:hover {
        background: #ffe4e6;
        color: #881337;
    }

    .search-bar {
        display: flex;
        gap: 0.75rem;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .search-field { display: flex; flex-direction: column; gap: 0.3rem; }
    .search-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--color-hover);
    }
    .search-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .search-input-wrap i {
        position: absolute;
        left: 0.75rem;
        color: #9ca3af;
        font-size: 0.9rem;
        pointer-events: none;
    }
    .search-input {
        border: 1px solid var(--color-muy-claro);
        border-radius: 8px;
        padding: 0.5rem 0.9rem 0.5rem 2.2rem;
        font-size: 0.875rem;
        outline: none;
        width: 300px;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .search-input:focus {
        border-color: var(--color-principal);
        box-shadow: 0 0 0 3px var(--sombra-principal);
    }
    .select-filtro {
        border: 1px solid var(--color-muy-claro);
        border-radius: 8px;
        padding: 0.5rem 0.85rem;
        font-size: 0.875rem;
        outline: none;
        cursor: pointer;
        transition: border-color 0.15s;
    }
    .select-filtro:focus { border-color: var(--color-principal); }

    .vacio-msg {
        text-align: center;
        padding: 3rem 1rem;
        color: #9ca3af;
    }
    .vacio-msg i { font-size: 2.5rem; display: block; margin-bottom: 0.75rem; color: var(--color-acento-activo); }
</style>
@endpush

@section('contenido')

{{-- Mensajes flash --}}
@if(session('exito'))
    <div class="alerta-flash" style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0;">
        <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
    </div>
@endif
@if(session('error'))
    <div class="alerta-flash" style="background:#fef2f2; color:#991b1b; border:1px solid #fecaca;">
        <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
    </div>
@endif

{{-- Encabezado de página --}}
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-titulo">Pacientes</h1>
        <p class="page-subtitulo">Gestión del registro de pacientes del consultorio</p>
    </div>
    <a href="{{ route('pacientes.create') }}" class="btn-morado">
        <i class="bi bi-person-plus-fill"></i> Nuevo Paciente
    </a>
</div>

{{-- Barra de búsqueda y filtros --}}
<div class="card-sistema mb-3">
    <form id="form-buscar" method="GET" action="{{ route('pacientes.index') }}" class="search-bar">
        {{-- Campo de búsqueda --}}
        <div class="search-field">
            <span class="search-label"><i class="bi bi-search"></i> Buscar Paciente</span>
            <div class="search-input-wrap">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    id="input-buscar"
                    name="buscar"
                    class="search-input"
                    placeholder="Nombre, apellido o documento..."
                    value="{{ request('buscar') }}"
                    autocomplete="off"
                >
            </div>
        </div>

        {{-- Filtro estado --}}
        <div class="search-field">
            <span class="search-label">Estado</span>
            <select id="select-estado" name="estado" class="select-filtro">
                <option value="">Todos</option>
                <option value="activo"   {{ request('estado') === 'activo'   ? 'selected' : '' }}>Activos</option>
                <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
            </select>
        </div>

        <div id="wrap-limpiar" class="search-field" style="justify-content:flex-end; display:none;">
            <span class="search-label" style="opacity:0;">—</span>
            <a href="{{ route('pacientes.index') }}" class="btn-outline-morado" onclick="limpiarFiltros(event)">
                <i class="bi bi-x"></i> Limpiar
            </a>
        </div>
    </form>
</div>

{{-- Tabla de pacientes --}}
<div id="contenedor-tabla" class="card-sistema" style="padding:0; overflow:hidden;">
    @if($pacientes->isEmpty())
        <div class="vacio-msg">
            <i class="bi bi-people"></i>
            <p style="font-weight:600; color:#4b5563;">No se encontraron pacientes</p>
            <p style="font-size:0.85rem;">
                @if(request('buscar'))
                    Ningún resultado para <strong>"{{ request('buscar') }}"</strong>
                @else
                    Aún no hay pacientes registrados.
                @endif
            </p>
            <a href="{{ route('pacientes.create') }}" class="btn-morado mt-2">
                <i class="bi bi-person-plus-fill"></i> Registrar primer paciente
            </a>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table class="tabla-pacientes">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Documento</th>
                        <th>Teléfono</th>
                        <th>Edad</th>
                        <th>Historia N°</th>
                        <th>Estado</th>
                        <th style="text-align:center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pacientes as $p)
                    <tr>
                        {{-- Foto / Avatar + nombre --}}
                        <td>
                            <div style="display:flex; align-items:center; gap:0.65rem;">
                                @if($p->foto_path)
                                    <img src="{{ $p->foto_url }}" alt="{{ $p->nombre_completo }}" class="avatar-pac">
                                @else
                                    <span class="avatar-iniciales">
                                        {{ strtoupper(substr($p->nombre,0,1)) }}{{ strtoupper(substr($p->apellido,0,1)) }}
                                    </span>
                                @endif
                                <div>
                                    <div style="font-weight:600; color:#1c2b22;">{{ $p->nombre_completo }}</div>
                                    <div style="font-size:0.78rem; color:#6b7280;">{{ $p->email ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-size:0.78rem; color:#6b7280; font-weight:600;">{{ $p->tipo_documento }}</span><br>
                            {{ $p->numero_documento }}
                        </td>
                        <td>{{ $p->telefono }}</td>
                        <td>{{ $p->edad }} años</td>
                        <td>
                            <span style="font-family:monospace; font-weight:600; color:var(--color-principal);">
                                {{ $p->numero_historia }}
                            </span>
                        </td>
                        <td>
                            @if($p->activo)
                                <span class="badge-activo"><i class="bi bi-circle-fill" style="font-size:0.5rem;"></i> Activo</span>
                            @else
                                <span class="badge-inactivo"><i class="bi bi-circle-fill" style="font-size:0.5rem;"></i> Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; justify-content:center; gap:0.35rem;">
                                <a href="{{ route('pacientes.show', $p) }}" class="btn-accion" title="Ver ficha">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('pacientes.edit', $p) }}" class="btn-accion" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                {{-- Activar / Desactivar --}}
                                @if($p->activo)
                                    <form method="POST" action="{{ route('pacientes.destroy', $p) }}"
                                          onsubmit="return confirm('¿Desactivar a {{ $p->nombre_completo }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-accion danger" title="Desactivar">
                                            <i class="bi bi-person-x"></i>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('pacientes.activar', $p->id) }}"
                                          onsubmit="return confirm('¿Activar a {{ $p->nombre_completo }}?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn-accion success" title="Activar">
                                            <i class="bi bi-person-check"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- Eliminar definitivamente --}}
                                <form method="POST" action="{{ route('pacientes.eliminar', $p->id) }}"
                                      onsubmit="return confirm('⚠️ ¿Eliminar permanentemente a {{ $p->nombre_completo }}?\n\nEsta acción no se puede deshacer.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-accion eliminar" title="Eliminar permanentemente">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($pacientes->hasPages())
        <div style="padding:1rem 1.5rem; border-top:1px solid var(--fondo-borde);">
            {{ $pacientes->links() }}
        </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
(function () {
    const input      = document.getElementById('input-buscar');
    const select     = document.getElementById('select-estado');
    const form       = document.getElementById('form-buscar');
    const contenedor = document.getElementById('contenedor-tabla');
    const wrapLimpiar = document.getElementById('wrap-limpiar');
    let timer;

    // Mostrar/ocultar botón Limpiar según haya filtros activos
    function actualizarLimpiar() {
        wrapLimpiar.style.display = (input.value || select.value) ? 'flex' : 'none';
    }

    form.addEventListener('submit', e => e.preventDefault());

    function buscar(ms) {
        clearTimeout(timer);
        actualizarLimpiar();
        timer = setTimeout(() => {
            const params = new URLSearchParams({
                buscar: input.value,
                estado: select.value
            });

            contenedor.style.opacity = '0.5';
            contenedor.style.transition = 'opacity 0.15s';

            fetch('{{ route('pacientes.index') }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc    = parser.parseFromString(html, 'text/html');
                const nuevo  = doc.getElementById('contenedor-tabla');
                if (nuevo) contenedor.innerHTML = nuevo.innerHTML;
                contenedor.style.opacity = '1';
            })
            .catch(() => { contenedor.style.opacity = '1'; });
        }, ms);
    }

    input.addEventListener('input', function () {
        var pos = this.selectionStart;
        this.value = this.value.toLowerCase().replace(/\b\w/g, function (l) { return l.toUpperCase(); });
        this.setSelectionRange(pos, pos);
        buscar(350);
    });
    select.addEventListener('change', () => buscar(0));

    // Inicializar estado del botón Limpiar
    actualizarLimpiar();
})();

function limpiarFiltros(e) {
    e.preventDefault();
    document.getElementById('input-buscar').value = '';
    document.getElementById('select-estado').value = '';
    document.getElementById('wrap-limpiar').style.display = 'none';
    document.getElementById('input-buscar').dispatchEvent(new Event('input'));
}
</script>
@endpush

@endsection
