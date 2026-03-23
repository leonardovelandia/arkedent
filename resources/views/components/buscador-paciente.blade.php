@props([
    'pacientes'    => [],     // colección o array de pacientes
    'valorInicial' => null,   // id del paciente preseleccionado
    'textoInicial' => null,   // nombre del paciente preseleccionado
    'campoNombre'  => 'numero_historia', // 'numero_historia' o 'numero_documento'
    'placeholder'  => 'Buscar por nombre, apellido o documento…',
    'claseCtrl'    => '',     // clases extra para el input
    'extraData'    => [],     // array/Collection keyed by paciente_id → any extra value
])

@php
    $uid = 'bp_' . substr(md5(uniqid('', true)), 0, 8);

    $extraMap = collect($extraData)->all();

    // Serializar la lista de pacientes a JSON para JS
    $lista = collect($pacientes)->map(function($p) use ($campoNombre, $extraMap) {
        $sub = $campoNombre === 'numero_historia'
            ? ($p->numero_historia ?? '')
            : ($p->numero_documento ?? '');
        return [
            'id'     => $p->id,
            'nombre' => $p->nombre_completo,
            'sub'    => $sub,
            'busq'   => strtolower($p->nombre . ' ' . $p->apellido . ' ' . $p->numero_documento . ' ' . ($p->numero_historia ?? '')),
            'extra'  => $extraMap[$p->id] ?? null,
        ];
    })->values()->toArray();

    // Si hay valor inicial, buscar su texto
    $textoMostrar = $textoInicial;
    if (!$textoMostrar && $valorInicial) {
        $encontrado = collect($pacientes)->firstWhere('id', $valorInicial);
        if ($encontrado) {
            $sub = $campoNombre === 'numero_historia'
                ? ($encontrado->numero_historia ?? '')
                : ($encontrado->numero_documento ?? '');
            $textoMostrar = $encontrado->nombre_completo . ($sub ? ' — ' . $sub : '');
        }
    }
@endphp

<div id="{{ $uid }}-wrap" style="position:relative;" x-data>
    {{-- Input hidden con el valor real --}}
    <input type="hidden" name="paciente_id" id="{{ $uid }}-hidden" value="{{ $valorInicial ?? old('paciente_id') }}">

    {{-- Chip de seleccionado --}}
    <div id="{{ $uid }}-chip" style="display:{{ ($textoMostrar || old('paciente_id')) ? 'flex' : 'none' }};align-items:center;gap:.5rem;border:1.5px solid var(--color-principal);border-radius:8px;padding:.4rem .75rem;background:var(--color-muy-claro);cursor:default;">
        <i class="bi bi-person-check-fill" style="color:var(--color-principal);font-size:.95rem;"></i>
        <span id="{{ $uid }}-chip-text" style="font-size:.88rem;font-weight:600;color:var(--color-hover);flex:1;">
            {{ $textoMostrar ?? '' }}
        </span>
        <button type="button" id="{{ $uid }}-clear"
            style="background:none;border:none;color:var(--color-principal);font-size:1rem;cursor:pointer;padding:0;line-height:1;"
            title="Cambiar paciente">
            <i class="bi bi-x-circle-fill"></i>
        </button>
    </div>

    {{-- Input de búsqueda --}}
    <div id="{{ $uid }}-search-wrap" style="position:relative;display:{{ ($textoMostrar || old('paciente_id')) ? 'none' : 'block' }};">
        <i class="bi bi-search" style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:.9rem;pointer-events:none;z-index:1;"></i>
        <input type="text"
               id="{{ $uid }}-input"
               class="{{ $claseCtrl }}"
               placeholder="{{ $placeholder }}"
               autocomplete="off"
               style="width:100%;border:1.5px solid var(--color-muy-claro);border-radius:8px;padding:.42rem .75rem .42rem 2.1rem;font-size:.875rem;color:#1c2b22;background:#fff;outline:none;transition:border-color .15s;">

        {{-- Dropdown (se adjunta al body para escapar overflow:hidden) --}}
        <div id="{{ $uid }}-drop"
             style="display:none;position:fixed;background:#fff;border:1.5px solid var(--color-principal);border-radius:10px;box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12);z-index:99999;max-height:260px;overflow-y:auto;min-width:260px;">
            <div id="{{ $uid }}-lista"></div>
            <div id="{{ $uid }}-empty" style="display:none;padding:.85rem 1rem;font-size:.85rem;color:#9ca3af;text-align:center;">
                <i class="bi bi-person-x"></i> Sin resultados
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var UID      = '{{ $uid }}';
    var datos    = {!! json_encode($lista) !!};
    var input    = document.getElementById(UID + '-input');
    var hidden   = document.getElementById(UID + '-hidden');
    var drop     = document.getElementById(UID + '-drop');
    var lista    = document.getElementById(UID + '-lista');
    var empty    = document.getElementById(UID + '-empty');
    var chip     = document.getElementById(UID + '-chip');
    var chipText = document.getElementById(UID + '-chip-text');
    var clear    = document.getElementById(UID + '-clear');
    var swrap    = document.getElementById(UID + '-search-wrap');
    var foco     = -1;

    function renderItems(filtrados) {
        lista.innerHTML = '';
        foco = -1;
        if (filtrados.length === 0) {
            empty.style.display = 'block';
            return;
        }
        empty.style.display = 'none';
        filtrados.forEach(function (p, i) {
            var li = document.createElement('div');
            li.setAttribute('data-i', i);
            li.style.cssText = 'padding:.6rem 1rem;cursor:pointer;display:flex;align-items:center;gap:.6rem;border-bottom:1px solid var(--fondo-borde);transition:background .1s;';
            li.innerHTML =
                '<i class="bi bi-person-circle" style="color:var(--color-principal);font-size:1rem;flex-shrink:0;"></i>' +
                '<div style="min-width:0;">' +
                    '<div style="font-size:.875rem;font-weight:600;color:#1c2b22;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + p.nombre + '</div>' +
                    '<div style="font-size:.73rem;color:#9ca3af;">' + p.sub + '</div>' +
                '</div>';
            li.addEventListener('mouseenter', function () { setFoco(i, filtrados); });
            li.addEventListener('mouseleave', function () { clearFoco(); });
            li.addEventListener('mousedown', function (e) {
                e.preventDefault();
                seleccionar(p);
            });
            lista.appendChild(li);
        });
    }

    function setFoco(i, filtrados) {
        clearFoco();
        foco = i;
        var items = lista.children;
        if (items[i]) items[i].style.background = 'var(--color-muy-claro)';
    }

    function clearFoco() {
        var items = lista.children;
        for (var j = 0; j < items.length; j++) {
            items[j].style.background = '';
        }
    }

    function filtrar(q) {
        if (!q.trim()) return datos;
        var t = q.toLowerCase();
        return datos.filter(function (p) { return p.busq.indexOf(t) !== -1; });
    }

    // Mover dropdown al body para escapar overflow:hidden
    document.body.appendChild(drop);

    function posicionarDrop() {
        var rect = input.getBoundingClientRect();
        drop.style.left  = rect.left + 'px';
        drop.style.top   = (rect.bottom + 4) + 'px';
        drop.style.width = rect.width + 'px';
    }

    function abrir() {
        var q = input.value;
        var res = filtrar(q);
        renderItems(res);
        posicionarDrop();
        drop.style.display = 'block';
    }

    function cerrar() {
        drop.style.display = 'none';
    }

    window.addEventListener('scroll', function() { if (drop.style.display !== 'none') posicionarDrop(); }, true);
    window.addEventListener('resize', function() { if (drop.style.display !== 'none') posicionarDrop(); });

    function seleccionar(p) {
        hidden.value = p.id;
        chipText.textContent = p.nombre + (p.sub ? ' — ' + p.sub : '');
        chip.style.display  = 'flex';
        swrap.style.display = 'none';
        input.value = '';
        cerrar();
        // Emitir evento personalizado
        hidden.dispatchEvent(new CustomEvent('bp:select', {
            detail: { id: p.id, nombre: p.nombre, sub: p.sub, extra: p.extra || null },
            bubbles: true
        }));
    }

    function deseleccionar() {
        hidden.value = '';
        chip.style.display  = 'none';
        swrap.style.display = 'block';
        input.value = '';
        input.focus();
        hidden.dispatchEvent(new CustomEvent('bp:clear', { bubbles: true }));
    }

    input.addEventListener('focus', function () { abrir(); });
    input.addEventListener('input', function () {
        abrir();
    });

    input.addEventListener('keydown', function (e) {
        var items = lista.children;
        var filtrados = filtrar(input.value);
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            foco = Math.min(foco + 1, items.length - 1);
            clearFoco();
            if (items[foco]) items[foco].style.background = 'var(--color-muy-claro)';
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            foco = Math.max(foco - 1, 0);
            clearFoco();
            if (items[foco]) items[foco].style.background = 'var(--color-muy-claro)';
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (foco >= 0 && filtrados[foco]) seleccionar(filtrados[foco]);
        } else if (e.key === 'Escape') {
            cerrar();
        }
    });

    input.addEventListener('blur', function () {
        setTimeout(cerrar, 150);
    });

    clear.addEventListener('click', function () { deseleccionar(); });

    // Focus visual
    input.addEventListener('focus', function () { this.style.borderColor = 'var(--color-principal)'; });
    input.addEventListener('blur',  function () { this.style.borderColor = 'var(--color-muy-claro)'; });
})();
</script>
@endpush
