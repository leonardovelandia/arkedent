{{--
    COMPONENTE: tabla-listado
    Uso:
        <x-tabla-listado
            :paginacion="$items"
            placeholder="Buscar..."
            icono-vacio="bi-inbox"
            mensaje-vacio="Sin registros."
        >
            <x-slot:filtros>
                <select name="estado" class="tbl-filtro-select">...</select>
            </x-slot:filtros>

            <x-slot:accion-vacio>
                <a href="..." class="btn-morado mt-3">Crear nuevo</a>
            </x-slot:accion-vacio>

            <x-slot:thead>
                <tr><th>Nombre</th>...</tr>
            </x-slot:thead>

            @foreach($items as $item)
                <tr><td>{{ $item->nombre }}</td></tr>
            @endforeach
        </x-tabla-listado>
--}}
@props([
    'paginacion',
    'placeholder'   => 'Buscar...',
    'iconoVacio'    => 'bi-inbox',
    'mensajeVacio'  => 'No se encontraron registros.',
])

@php
    $perPage   = in_array((int) request('per_page', 10), [10, 25, 50])
                    ? (int) request('per_page', 10) : 10;
    $buscar    = request('buscar', '');
    $total     = $paginacion->total();
    $desde     = $paginacion->firstItem() ?? 0;
    $hasta     = $paginacion->lastItem() ?? 0;
    $hayItems  = $total > 0;
    $hayFiltros = collect(request()->except(['page', 'per_page', '_token']))->filter()->isNotEmpty();
    $uid        = 'tbl' . substr(md5(uniqid('', true)), 0, 7);
@endphp

<div class="tbl-card" id="{{ $uid }}-card">

    {{-- ── Toolbar: búsqueda + filtros + por página ─────────── --}}
    <form
        id="{{ $uid }}-form"
        method="GET"
        action="{{ url()->current() }}"
        data-tbl-uid="{{ $uid }}"
    >
        <div class="tbl-toolbar">

            {{-- Búsqueda --}}
            <div class="tbl-buscar-wrap">
                <i class="bi bi-search tbl-lupa"></i>
                <input
                    type="text"
                    name="buscar"
                    id="{{ $uid }}-buscar"
                    class="tbl-buscar-input"
                    placeholder="{{ $placeholder }}"
                    value="{{ $buscar }}"
                    autocomplete="off"
                >
                <button type="button" class="tbl-clear-btn" id="{{ $uid }}-clear" title="Limpiar búsqueda">
                    <i class="bi bi-x" style="font-size:.85rem;"></i>
                </button>
            </div>

            {{-- Slot de filtros adicionales (selects, fechas, etc.) --}}
            @isset($filtros)
                {{ $filtros }}
            @endisset

            {{-- Por página --}}
            <div class="tbl-perpage-wrap">
                <span>Mostrar</span>
                <select name="per_page" id="{{ $uid }}-perpage" class="tbl-perpage-select">
                    @foreach([10, 25, 50] as $n)
                        <option value="{{ $n }}" {{ $perPage === $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
                <span>por página</span>
            </div>

        </div>
    </form>

    {{-- ── Barra de información ──────────────────────────────── --}}
    @if($hayItems)
    <div class="tbl-infobar">
        <span>
            Mostrando
            <strong>{{ $desde }}</strong> – <strong>{{ $hasta }}</strong>
            de <strong>{{ number_format($total) }}</strong>
            {{ $total === 1 ? 'registro' : 'registros' }}
            @if($buscar)
                &nbsp;·&nbsp; búsqueda: <em>"{{ Str::limit($buscar, 30) }}"</em>
            @endif
        </span>
        @if($hayFiltros)
        <a href="{{ url()->current() }}" class="tbl-limpiar-todo">
            <i class="bi bi-x-circle"></i> Limpiar filtros
        </a>
        @endif
    </div>
    @endif

    {{-- ── Tabla ─────────────────────────────────────────────── --}}
    @if($hayItems)
        <div class="tbl-wrap">
            <table class="tbl-table">
                <thead>{{ $thead }}</thead>
                <tbody>{{ $slot }}</tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($paginacion->hasPages())
        <div class="tbl-paginacion">
            {{ $paginacion->links() }}
        </div>
        @endif

    @else
        {{-- Estado vacío --}}
        <div class="tbl-vacio">
            <i class="bi {{ $iconoVacio }}"></i>
            <div class="tbl-vacio-titulo">{{ $mensajeVacio }}</div>
            @if($buscar)
                <div class="tbl-vacio-sub">
                    Ningún resultado para <strong>"{{ $buscar }}"</strong>.
                    <a href="{{ url()->current() }}" style="color:var(--color-principal);">Ver todos</a>
                </div>
            @endif
            @isset($accionVacio)
                {{ $accionVacio }}
            @endisset
        </div>
    @endif

</div>

{{-- JS: se incluye solo una vez por página aunque haya varias tablas --}}
@once
@push('scripts')
<script>
(function () {
    'use strict';

    function initTbl(uid) {
        var card    = document.getElementById(uid + '-card');
        var form    = document.getElementById(uid + '-form');
        var buscar  = document.getElementById(uid + '-buscar');
        var clear   = document.getElementById(uid + '-clear');
        var perpage = document.getElementById(uid + '-perpage');
        var timer;

        if (!form) return;

        // Evitar submit nativo (usamos auto-submit controlado)
        form.addEventListener('submit', function (e) { e.preventDefault(); });

        function mostrarClear() {
            if (clear) clear.style.display = buscar && buscar.value ? 'block' : 'none';
        }

        function doSubmit(delay) {
            clearTimeout(timer);
            timer = setTimeout(function () {
                // Resetear a página 1 al buscar o cambiar filtro
                var inputs = form.querySelectorAll('[name="page"]');
                inputs.forEach(function (i) { i.remove(); });
                if (card) card.classList.add('tbl-cargando');
                form.submit();
            }, delay);
        }

        if (buscar) {
            mostrarClear();
            buscar.addEventListener('input', function () {
                mostrarClear();
                doSubmit(380);
            });
        }

        if (clear) {
            clear.addEventListener('click', function () {
                if (buscar) buscar.value = '';
                mostrarClear();
                doSubmit(0);
            });
        }

        if (perpage) {
            perpage.addEventListener('change', function () { doSubmit(0); });
        }

        // Cualquier select dentro del form (filtros del slot) también auto-envía
        form.querySelectorAll('select:not(#' + uid + '-perpage)').forEach(function (sel) {
            sel.addEventListener('change', function () { doSubmit(0); });
        });

        // Date inputs en el form
        form.querySelectorAll('input[type="date"]').forEach(function (inp) {
            inp.addEventListener('change', function () { doSubmit(0); });
        });
    }

    // Inicializar todas las tablas al cargar
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-tbl-uid]').forEach(function (form) {
            initTbl(form.getAttribute('data-tbl-uid'));
        });
    });

    // Exponer para uso externo
    window.initTblListado = initTbl;
}());
</script>
@endpush
@endonce
