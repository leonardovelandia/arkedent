{{-- ============================================================
     PARTIAL: Odontograma Ortodóntico Interactivo
     Variables esperadas:
       $odontogramaData — JSON string del estado previo (default '{}')
       $inputName       — nombre del input hidden (default 'odontograma_ortodoncia')
       $readonly        — booleano para modo solo lectura (default false)
     ============================================================ --}}
@php
    $odontogramaData = $odontogramaData ?? '{}';
    $inputName       = $inputName ?? 'odontograma_ortodoncia';
    $readonly        = $readonly ?? false;
    $dentesSuperiores = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
    $dentesInferiores = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];
@endphp

<div id="odontograma-ortodoncia-wrap" style="background:white;border-radius:16px;padding:1.5rem;border:1px solid var(--fondo-borde);box-shadow:0 4px 24px var(--sombra-principal);">

    {{-- Leyenda --}}
    <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1.25rem;padding-bottom:1rem;border-bottom:1px solid var(--fondo-borde);">
        @foreach([
            ['bracket',  'var(--color-principal)', 'rect',   'Bracket'],
            ['banda',    '#2563EB',                'rect',   'Banda molar'],
            ['tubo',     '#059669',                'circle', 'Tubo'],
            ['boton',    '#D97706',                'circle', 'Botón'],
            ['excluido', '#DC3545',                'x',      'Excluido'],
            ['ninguno',  '#E5E7EB',                'rect',   'Sin aparato'],
        ] as [$tipo, $color, $forma, $label])
        <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.75rem;color:var(--texto-principal);">
            <span style="background:{{ $color }};width:14px;height:14px;border-radius:{{ $forma==='circle'?'50%':'3px' }};display:inline-block;flex-shrink:0;"></span>
            {{ $label }}
        </span>
        @endforeach
    </div>

    @unless($readonly)
    <p style="font-size:.75rem;color:var(--texto-secundario);margin-bottom:1rem;text-align:center;">
        <i class="bi bi-cursor-fill"></i> Clic para ciclar estados — Clic derecho para opciones
    </p>
    @endunless

    {{-- Arcada Superior --}}
    <div style="margin-bottom:.75rem;">
        <div style="font-size:.68rem;font-weight:700;color:var(--color-principal);text-transform:uppercase;letter-spacing:.08em;text-align:center;margin-bottom:.5rem;">Arcada Superior</div>
        <div style="display:flex;justify-content:center;gap:3px;flex-wrap:nowrap;overflow-x:auto;padding-bottom:4px;">
            @foreach($dentesSuperiores as $diente)
            <div class="diente-orto{{ $readonly ? '' : '' }}" data-diente="{{ $diente }}" data-arcada="superior"
                 style="width:36px;display:flex;flex-direction:column;align-items:center;gap:2px;{{ $readonly ? '' : 'cursor:pointer;' }}flex-shrink:0;">
                <div style="font-size:.58rem;color:var(--texto-secundario);font-weight:500;line-height:1;">{{ $diente }}</div>
                <div class="diente-corona-orto" style="width:28px;height:32px;border-radius:6px 6px 10px 10px;background:#F9FAFB;border:2px solid #E5E7EB;position:relative;transition:all .2s;display:flex;align-items:center;justify-content:center;">
                    <div class="aparato-indicador" style="width:14px;height:9px;border-radius:2px;background:transparent;border:2px solid transparent;transition:all .2s;display:flex;align-items:center;justify-content:center;"></div>
                </div>
                <div style="width:9px;height:14px;background:linear-gradient(180deg,#E5E7EB 0%,#F9FAFB 100%);border-radius:0 0 50% 50%;border:1px solid #E5E7EB;border-top:none;"></div>
            </div>
            @endforeach
        </div>
        <div id="linea-arco-superior" style="height:4px;background:linear-gradient(90deg,transparent 5%,var(--color-claro) 15%,var(--color-principal) 50%,var(--color-claro) 85%,transparent 95%);border-radius:50px;margin:6px 20px;opacity:.6;display:none;"></div>
    </div>

    {{-- Centro oclusal --}}
    <div style="display:flex;align-items:center;justify-content:center;gap:1.5rem;padding:.6rem;background:var(--fondo-card-alt);border-radius:10px;margin:.5rem 0;">
        <div style="text-align:center;">
            <div style="font-size:.62rem;color:var(--texto-secundario);text-transform:uppercase;letter-spacing:.06em;">Overjet</div>
            <div id="display-overjet" style="font-size:.95rem;font-weight:700;color:var(--color-principal);">— mm</div>
        </div>
        <div style="width:1px;height:28px;background:var(--fondo-borde);"></div>
        <div style="text-align:center;">
            <div style="font-size:.62rem;color:var(--texto-secundario);text-transform:uppercase;letter-spacing:.06em;">Overbite</div>
            <div id="display-overbite" style="font-size:.95rem;font-weight:700;color:var(--color-principal);">— mm</div>
        </div>
        <div style="width:1px;height:28px;background:var(--fondo-borde);"></div>
        <div style="text-align:center;">
            <div style="font-size:.62rem;color:var(--texto-secundario);text-transform:uppercase;letter-spacing:.06em;">Línea media</div>
            <div id="display-linea-media" style="font-size:.78rem;font-weight:600;color:var(--color-principal);">—</div>
        </div>
        <div style="width:1px;height:28px;background:var(--fondo-borde);"></div>
        <div style="text-align:center;">
            <div style="font-size:.62rem;color:var(--texto-secundario);text-transform:uppercase;letter-spacing:.06em;">Clase molar</div>
            <div id="display-clase" style="font-size:.78rem;font-weight:600;color:var(--color-principal);">—</div>
        </div>
    </div>

    {{-- Arcada Inferior --}}
    <div style="margin-top:.75rem;">
        <div id="linea-arco-inferior" style="height:4px;background:linear-gradient(90deg,transparent 5%,var(--color-claro) 15%,var(--color-principal) 50%,var(--color-claro) 85%,transparent 95%);border-radius:50px;margin:6px 20px;opacity:.6;display:none;"></div>
        <div style="display:flex;justify-content:center;gap:3px;flex-wrap:nowrap;overflow-x:auto;padding-top:4px;">
            @foreach($dentesInferiores as $diente)
            <div class="diente-orto" data-diente="{{ $diente }}" data-arcada="inferior"
                 style="width:36px;display:flex;flex-direction:column-reverse;align-items:center;gap:2px;{{ $readonly ? '' : 'cursor:pointer;' }}flex-shrink:0;">
                <div style="font-size:.58rem;color:var(--texto-secundario);font-weight:500;line-height:1;">{{ $diente }}</div>
                <div class="diente-corona-orto" style="width:28px;height:32px;border-radius:10px 10px 6px 6px;background:#F9FAFB;border:2px solid #E5E7EB;position:relative;transition:all .2s;display:flex;align-items:center;justify-content:center;">
                    <div class="aparato-indicador" style="width:14px;height:9px;border-radius:2px;background:transparent;border:2px solid transparent;transition:all .2s;display:flex;align-items:center;justify-content:center;"></div>
                </div>
                <div style="width:9px;height:14px;background:linear-gradient(0deg,#E5E7EB 0%,#F9FAFB 100%);border-radius:50% 50% 0 0;border:1px solid #E5E7EB;border-bottom:none;"></div>
            </div>
            @endforeach
        </div>
        <div style="font-size:.68rem;font-weight:700;color:var(--color-principal);text-transform:uppercase;letter-spacing:.08em;text-align:center;margin-top:.5rem;">Arcada Inferior</div>
    </div>

    {{-- Menú contextual --}}
    @unless($readonly)
    <div id="menu-diente-orto" style="display:none;position:fixed;background:white;border:1px solid var(--fondo-borde);border-radius:10px;padding:.5rem;box-shadow:0 8px 24px rgba(0,0,0,.15);z-index:9999;min-width:160px;">
        <div id="menu-diente-numero" style="font-size:.68rem;color:var(--texto-secundario);font-weight:700;text-transform:uppercase;padding:.25rem .5rem;letter-spacing:.06em;">Diente</div>
        <div style="height:1px;background:var(--fondo-borde);margin:.3rem 0;"></div>
        @foreach([
            ['bracket',  'var(--color-principal)', 'bi-square-fill',  'Bracket'],
            ['banda',    '#2563EB',                'bi-square-fill',  'Banda molar'],
            ['tubo',     '#059669',                'bi-circle-fill',  'Tubo'],
            ['boton',    '#D97706',                'bi-circle-fill',  'Botón'],
            ['excluido', '#DC3545',                'bi-x-square-fill','Excluido'],
            ['ninguno',  '#9CA3AF',                'bi-square',       'Sin aparato'],
        ] as [$tipo, $color, $icono, $label])
        <div class="menu-opcion-orto" data-tipo="{{ $tipo }}"
             style="display:flex;align-items:center;gap:.5rem;padding:.35rem .5rem;border-radius:6px;cursor:pointer;font-size:.8rem;color:#333;transition:background .12s;"
             onmouseover="this.style.background='var(--color-muy-claro)'" onmouseout="this.style.background='transparent'">
            <i class="bi {{ $icono }}" style="color:{{ $color }};"></i> {{ $label }}
        </div>
        @endforeach
    </div>
    @endunless

    <input type="hidden" name="{{ $inputName }}" id="odontograma-data" value="{{ $odontogramaData }}">
</div>

@unless($readonly)
@push('scripts')
<script>
(function() {
    const estadoDientes = {};
    const readonly = {{ $readonly ? 'true' : 'false' }};
    if (readonly) return;

    const coloresAparato = {
        'bracket':  { bg: 'var(--color-principal)', border: 'var(--color-hover)',   forma: 'rect'   },
        'banda':    { bg: '#2563EB',                border: '#1E40AF',              forma: 'rect'   },
        'tubo':     { bg: '#059669',                border: '#047857',              forma: 'circle' },
        'boton':    { bg: '#D97706',                border: '#B45309',              forma: 'circle' },
        'excluido': { bg: '#FEE2E2',                border: '#DC3545',              forma: 'x'      },
        'ninguno':  { bg: '#F9FAFB',                border: '#E5E7EB',              forma: 'none'   },
    };

    let dienteSeleccionado = null;

    function actualizarDiente(numDiente, tipo) {
        estadoDientes[numDiente] = tipo;
        const diente    = document.querySelector('.diente-orto[data-diente="' + numDiente + '"]');
        if (!diente) return;
        const corona    = diente.querySelector('.diente-corona-orto');
        const indicador = diente.querySelector('.aparato-indicador');
        const config    = coloresAparato[tipo] || coloresAparato['ninguno'];

        if (tipo === 'ninguno') {
            corona.style.background    = '#F9FAFB';
            corona.style.borderColor   = '#E5E7EB';
            indicador.style.background = 'transparent';
            indicador.style.borderColor = 'transparent';
            indicador.innerHTML        = '';
        } else if (tipo === 'excluido') {
            corona.style.background    = '#FEE2E2';
            corona.style.borderColor   = '#DC3545';
            indicador.style.background = 'transparent';
            indicador.style.borderColor = 'transparent';
            indicador.innerHTML        = '<i class="bi bi-x" style="color:#DC3545;font-size:11px;line-height:1;"></i>';
        } else {
            corona.style.background    = 'white';
            corona.style.borderColor   = config.border;
            indicador.style.borderRadius = config.forma === 'circle' ? '50%' : '2px';
            indicador.style.background = config.bg;
            indicador.style.borderColor = config.border;
            indicador.innerHTML        = '';
        }

        corona.style.transform = 'scale(1.1)';
        setTimeout(() => { corona.style.transform = 'scale(1)'; }, 150);
    }

    function guardarEstado() {
        document.getElementById('odontograma-data').value = JSON.stringify(estadoDientes);
        const tieneArcos = Object.values(estadoDientes).some(e => ['bracket','banda','tubo'].includes(e));
        const ls = document.getElementById('linea-arco-superior');
        const li = document.getElementById('linea-arco-inferior');
        if (ls) ls.style.display = tieneArcos ? 'block' : 'none';
        if (li) li.style.display = tieneArcos ? 'block' : 'none';
    }

    function cerrarMenu() {
        const menu = document.getElementById('menu-diente-orto');
        if (menu) menu.style.display = 'none';
        dienteSeleccionado = null;
    }

    // Inicializar dientes
    document.querySelectorAll('.diente-orto').forEach(function(diente) {
        const numDiente = diente.dataset.diente;
        estadoDientes[numDiente] = 'ninguno';

        diente.addEventListener('click', function(e) {
            e.stopPropagation();
            const estados = ['bracket','banda','tubo','boton','excluido','ninguno'];
            const actual  = estadoDientes[numDiente] || 'ninguno';
            const idx     = estados.indexOf(actual);
            const nuevo   = estados[(idx + 1) % estados.length];
            actualizarDiente(numDiente, nuevo);
            guardarEstado();
        });

        diente.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dienteSeleccionado = numDiente;
            const menu = document.getElementById('menu-diente-orto');
            if (!menu) return;
            const numEl = document.getElementById('menu-diente-numero');
            if (numEl) numEl.textContent = 'Diente ' + numDiente;
            menu.style.display = 'block';
            menu.style.left    = (e.clientX + 5) + 'px';
            menu.style.top     = (e.clientY + 5) + 'px';
        });

        diente.addEventListener('mouseenter', function() {
            const corona = this.querySelector('.diente-corona-orto');
            corona.style.transform  = 'scale(1.08)';
            corona.style.boxShadow  = '0 4px 12px var(--sombra-principal)';
        });
        diente.addEventListener('mouseleave', function() {
            const corona = this.querySelector('.diente-corona-orto');
            corona.style.transform = 'scale(1)';
            corona.style.boxShadow = 'none';
        });
    });

    // Opciones del menú contextual
    document.querySelectorAll('.menu-opcion-orto').forEach(function(opcion) {
        opcion.addEventListener('click', function() {
            if (dienteSeleccionado) {
                actualizarDiente(dienteSeleccionado, this.dataset.tipo);
                guardarEstado();
            }
            cerrarMenu();
        });
    });

    document.addEventListener('click', cerrarMenu);
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') cerrarMenu(); });

    // Cargar estado previo
    const inputData = document.getElementById('odontograma-data');
    if (inputData && inputData.value && inputData.value !== '{}' && inputData.value !== '') {
        try {
            const datos = JSON.parse(inputData.value);
            Object.entries(datos).forEach(function([diente, tipo]) {
                actualizarDiente(String(diente), tipo);
            });
            guardarEstado();
        } catch(e) {}
    }

    // Sincronizar displays con campos del formulario
    const inpOverjet  = document.querySelector('[name="overjet"]');
    const inpOverbite = document.querySelector('[name="overbite"]');
    const selClase    = document.getElementById('sel-clase-molar');
    const selLinea    = document.getElementById('sel-linea-media');

    if (inpOverjet) {
        const upd = () => {
            const el = document.getElementById('display-overjet');
            if (el) el.textContent = inpOverjet.value ? inpOverjet.value + ' mm' : '— mm';
        };
        inpOverjet.addEventListener('input', upd);
        upd();
    }
    if (inpOverbite) {
        const upd = () => {
            const el = document.getElementById('display-overbite');
            if (el) el.textContent = inpOverbite.value ? inpOverbite.value + ' mm' : '— mm';
        };
        inpOverbite.addEventListener('input', upd);
        upd();
    }
    if (selClase) {
        selClase.addEventListener('change', function() {
            const el = document.getElementById('display-clase');
            if (el) el.textContent = this.options[this.selectedIndex].text || '—';
        });
    }
    if (selLinea) {
        selLinea.addEventListener('change', function() {
            const el = document.getElementById('display-linea-media');
            if (el) el.textContent = this.options[this.selectedIndex].text || '—';
        });
    }
})();
</script>
@endpush
@endunless
