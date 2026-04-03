@extends('layouts.app')
@section('titulo', 'Nueva Receta Médica')

@push('estilos')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        #sel-paciente-ts-wrapper .ts-control {
            border: 1px solid var(--fondo-borde) !important;
            border-radius: 8px !important;
            background: var(--fondo-app) !important;
            font-size: .84rem !important;
            box-shadow: none !important;
            padding: .35rem .65rem !important;
            min-height: 38px !important;
        }

        #sel-paciente-ts-wrapper .ts-control.focus {
            border-color: var(--color-principal) !important;
            box-shadow: 0 0 0 3px var(--color-muy-claro) !important;
        }

        #sel-paciente-ts-wrapper .ts-dropdown {
            border-color: var(--fondo-borde) !important;
            font-size: .83rem !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .1) !important;
        }

        #sel-paciente-ts-wrapper .ts-dropdown .option.active,
        #sel-paciente-ts-wrapper .ts-dropdown .option:hover {
            background: var(--color-muy-claro) !important;
            color: var(--color-principal) !important;
        }

        #sel-paciente-ts-wrapper .ts-dropdown .option {
            padding: .45rem .75rem !important;
        }

        #sel-paciente-ts-wrapper .ts-dropdown-content {
            max-height: 220px !important;
        }
    </style>
@endpush

@section('contenido')

    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
        <a href="{{ route('recetas.index') }}" style="color:var(--texto-secundario);text-decoration:none;font-size:.84rem;">
            <i class="bi bi-arrow-left"></i> Recetas
        </a>
        <i class="bi bi-chevron-right" style="font-size:.7rem;color:var(--texto-secundario);"></i>
        <span style="font-size:.84rem;font-weight:600;">Nueva Receta</span>
    </div>

    @if ($errors->any())
        <div
            style="background:#fee2e2;border:1px solid #fca5a5;color:#7f1d1d;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.84rem;">
            <ul style="margin:0;padding-left:1.2rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('recetas.store') }}" id="form-receta">
        @csrf
        <input type="hidden" name="medicamentos" id="campo-medicamentos">
        <input type="hidden" name="firma_tipo" id="campo-firma-tipo" value="sin_firma">

        <div class="card-sistema" style="margin-bottom:1rem;">
            <h5
                style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
                <i class="bi bi-person-circle me-2"></i>Datos generales
            </h5>
            <div class="row g-3">
                <div class="col-md-5">
                    <label
                        style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Paciente
                        <span style="color:#dc2626;">*</span></label>
                    <select name="paciente_id" id="sel-paciente" required>
                        <option value="">Buscar paciente...</option>
                        @foreach ($pacientes as $p)
                            <option value="{{ $p->id }}"
                                data-buscar="{{ $p->nombre }} {{ $p->apellido }} {{ $p->numero_historia }} {{ $p->numero_documento }}"
                                {{ old('paciente_id', $paciente?->id) == $p->id ? 'selected' : '' }}>
                                {{ $p->nombre }} {{ $p->apellido }} — {{ $p->numero_historia }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label
                        style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Doctor
                        <span style="color:#dc2626;">*</span></label>
                    <select name="user_id" required
                        style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                        @foreach ($doctores as $d)
                            <option value="{{ $d->id }}"
                                {{ old('user_id', auth()->id()) == $d->id ? 'selected' : '' }}>
                                {{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label
                        style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Fecha
                        <span style="color:#dc2626;">*</span></label>
                    <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required
                        style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                </div>
                <div class="col-md-6">
                    <label
                        style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Evolución
                        asociada (opcional)</label>
                    <select name="evolucion_id" id="sel-evolucion"
                        style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                        <option value="">Sin evolución</option>
                        @if ($evolucion)
                            <option value="{{ $evolucion->id }}" selected>{{ $evolucion->fecha->format('d/m/Y') }} —
                                {{ Str::limit($evolucion->procedimiento, 40) }}</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-6">
                    <label
                        style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Diagnóstico</label>
                    <input type="text" name="diagnostico" value="{{ old('diagnostico') }}"
                        placeholder="Diagnóstico o motivo de consulta..."
                        style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);">
                </div>
            </div>
        </div>

        {{-- Medicamentos --}}
        <div class="card-sistema" style="margin-bottom:1rem;">
            <div
                style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);">
                <h5
                    style="font-size:.85rem;font-weight:700;text-transform:uppercase;color:var(--color-principal);letter-spacing:.06em;margin:0;">
                    <i class="bi bi-capsule me-2"></i>Medicamentos
                </h5>
                <button type="button" onclick="agregarMedicamento()"
                    style="padding:.4rem .9rem;background:var(--color-principal);color:white;border:none;border-radius:7px;font-size:.8rem;font-weight:600;cursor:pointer;">
                    <i class="bi bi-plus-lg me-1"></i> Agregar
                </button>
            </div>

            {{-- Plantillas rápidas --}}
            <div style="margin-bottom:1rem;">
                <span
                    style="font-size:.72rem;font-weight:600;color:var(--texto-secundario);text-transform:uppercase;letter-spacing:.04em;margin-right:.5rem;">Plantillas:</span>
                @php
                    $plantillas = [
                        'AINE' => [
                            'Ibuprofeno 400mg',
                            'tabletas',
                            '400mg',
                            'Cada 8 horas',
                            '5 días',
                            '15 tabletas',
                            'Tomar con alimentos',
                        ],
                        'Antibiótico' => [
                            'Amoxicilina 500mg',
                            'cápsulas',
                            '500mg',
                            'Cada 8 horas',
                            '7 días',
                            '21 cápsulas',
                            'Completar el tratamiento',
                        ],
                        'Analgésico' => [
                            'Acetaminofén 500mg',
                            'tabletas',
                            '500mg',
                            'Cada 6-8 horas',
                            '3-5 días',
                            '20 tabletas',
                            'Máximo 4 tomas al día',
                        ],
                        'Enjuague' => [
                            'Clorhexidina 0.12%',
                            'solución',
                            '15ml',
                            'Cada 12 horas',
                            '10 días',
                            '2 frascos',
                            'No tragar. Enjuagar 1 minuto',
                        ],
                        'Metronidazol' => [
                            'Metronidazol 500mg',
                            'tabletas',
                            '500mg',
                            'Cada 8 horas',
                            '7 días',
                            '21 tabletas',
                            'Tomar con alimentos. No consumir alcohol',
                        ],
                        'Clindamicina' => [
                            'Clindamicina 300mg',
                            'cápsulas',
                            '300mg',
                            'Cada 6 horas',
                            '7 días',
                            '28 cápsulas',
                            'Tomar con abundante agua',
                        ],
                        'Diclofenaco' => [
                            'Diclofenaco 50mg',
                            'tabletas',
                            '50mg',
                            'Cada 8-12 horas',
                            '5 días',
                            '15 tabletas',
                            'Tomar con alimentos',
                        ],
                        'Dexametasona' => [
                            'Dexametasona 4mg',
                            'tabletas',
                            '4mg',
                            'Cada 8 horas',
                            '3 días',
                            '9 tabletas',
                            'No suspender bruscamente',
                        ],
                    ];
                @endphp
                @foreach ($plantillas as $nombre => $data)
                    <button type="button"
                        onclick='usarPlantilla(@json($nombre), @json($data))'
                        style="padding:.25rem .7rem;border:1px solid var(--fondo-borde);border-radius:20px;font-size:.75rem;background:var(--fondo-app);cursor:pointer;color:var(--texto-secundario);margin-right:.3rem;margin-bottom:.3rem;">
                        + {{ $nombre }}
                    </button>
                @endforeach
            </div>

            <div id="lista-medicamentos">
                <div id="sin-medicamentos"
                    style="text-align:center;padding:2rem;color:var(--texto-secundario);font-size:.84rem;border:2px dashed var(--fondo-borde);border-radius:8px;">
                    <i class="bi bi-capsule" style="font-size:1.5rem;display:block;margin-bottom:.5rem;opacity:.4;"></i>
                    Agregue medicamentos usando el botón o las plantillas
                </div>
            </div>
        </div>

        {{-- Indicaciones generales --}}
        <div class="card-sistema" style="margin-bottom:1rem;">
            <label
                style="font-size:.78rem;font-weight:600;color:var(--texto-secundario);display:block;margin-bottom:.3rem;">Indicaciones
                generales</label>
            <textarea name="indicaciones_generales" rows="3"
                placeholder="Indicaciones generales al paciente, recomendaciones de higiene, dieta, próxima cita..."
                style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.84rem;background:var(--fondo-app);resize:vertical;">{{ old('indicaciones_generales') }}</textarea>
        </div>

        <div style="display:flex;gap:.75rem;justify-content:flex-end;flex-wrap:wrap;">
            <a href="{{ route('recetas.index') }}"
                style="
   padding:.55rem 1.25rem;
   border:1px solid #cbd5e1;
   border-radius:8px;
   color:#334155;
   text-decoration:none;
   font-size:.84rem;
   background:#f8fafc;
   font-weight:500;
   box-shadow:0 1px 2px rgba(0,0,0,0.05);
   transition:all .2s ease;"
                onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f8fafc'">
                Cancelar
            </a>
            <button type="submit"
                onclick="serializarMedicamentos(); document.getElementById('campo-firma-tipo').value='sin_firma';"
                style="
    padding:.55rem 1.5rem;
    background:#f1f5f9;
    color:#0f172a;
    border:1px solid #cbd5e1;
    border-radius:8px;
    font-size:.84rem;
    font-weight:600;
    cursor:pointer;
    box-shadow:0 1px 2px rgba(0,0,0,0.05);
    transition:all .2s ease;"
                onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">

                <i class="bi bi-file-medical me-1"></i> Guardar sin firma digital
            </button>
            <button type="submit"
                onclick="serializarMedicamentos(); document.getElementById('campo-firma-tipo').value='con_firma';"
                style="padding:.55rem 1.5rem;background:var(--color-principal);color:white;border:none;border-radius:8px;font-size:.84rem;font-weight:600;cursor:pointer;box-shadow:0 2px 8px var(--sombra-principal);">
                <i class="bi bi-pen me-1"></i> Guardar y firmar digitalmente
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            // Reemplazar /recetas/create en el historial por /recetas (index)
            // para que "atrás" desde show lleve al index y no al formulario
            if (window.history && window.history.replaceState) {
                history.replaceState(null, '', '{{ route('recetas.index') }}');
            }
            // Si el navegador restaura esta página desde bfcache (botón atrás), redirigir al index
            window.addEventListener('pageshow', function(e) {
                if (e.persisted) {
                    window.location.replace('{{ route('recetas.index') }}');
                }
            });

            let medicamentos = [];

            function renderMedicamentos() {
                const lista = document.getElementById('lista-medicamentos');
                const sinMed = document.getElementById('sin-medicamentos');

                if (medicamentos.length === 0) {
                    lista.innerHTML =
                        '<div id="sin-medicamentos" style="text-align:center;padding:2rem;color:var(--texto-secundario);font-size:.84rem;border:2px dashed var(--fondo-borde);border-radius:8px;"><i class="bi bi-capsule" style="font-size:1.5rem;display:block;margin-bottom:.5rem;opacity:.4;"></i>Agregue medicamentos usando el botón o las plantillas</div>';
                    return;
                }

                lista.innerHTML = medicamentos.map((m, i) => `
    <div style="border:1px solid var(--fondo-borde);border-radius:10px;padding:1rem;margin-bottom:.75rem;background:var(--fondo-app);" id="med-${i}">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
            <span style="font-size:.78rem;font-weight:700;color:var(--color-principal);text-transform:uppercase;letter-spacing:.04em;">Medicamento #${i+1}</span>
            <button type="button" onclick="eliminarMedicamento(${i})"
                    style="padding:.2rem .5rem;background:#fee2e2;color:#dc2626;border:none;border-radius:5px;font-size:.75rem;cursor:pointer;">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="row g-2">
            <div class="col-md-4">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Nombre / Principio activo</label>
                <input type="text" value="${m.nombre||''}" oninput="medicamentos[${i}].nombre=this.value"
                       placeholder="Ej: Ibuprofeno 400mg"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Presentación</label>
                <input type="text" value="${m.presentacion||''}" oninput="medicamentos[${i}].presentacion=this.value"
                       placeholder="tabletas, jarabe..."
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Dosis</label>
                <input type="text" value="${m.dosis||''}" oninput="medicamentos[${i}].dosis=this.value"
                       placeholder="500mg"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Frecuencia</label>
                <input type="text" value="${m.frecuencia||''}" oninput="medicamentos[${i}].frecuencia=this.value"
                       placeholder="Cada 8 horas"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Duración</label>
                <input type="text" value="${m.duracion||''}" oninput="medicamentos[${i}].duracion=this.value"
                       placeholder="5 días"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-2">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Cantidad</label>
                <input type="text" value="${m.cantidad||''}" oninput="medicamentos[${i}].cantidad=this.value"
                       placeholder="15 tabletas"
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
            <div class="col-md-10">
                <label style="font-size:.72rem;color:var(--texto-secundario);">Indicaciones específicas</label>
                <input type="text" value="${m.indicaciones||''}" oninput="medicamentos[${i}].indicaciones=this.value"
                       placeholder="Tomar con alimentos, no tomar si..."
                       style="width:100%;border:1px solid var(--fondo-borde);border-radius:7px;padding:.4rem .65rem;font-size:.83rem;background:white;">
            </div>
        </div>
    </div>
    `).join('');
            }

            function agregarMedicamento() {
                medicamentos.push({
                    nombre: '',
                    presentacion: '',
                    dosis: '',
                    frecuencia: '',
                    duracion: '',
                    cantidad: '',
                    indicaciones: ''
                });
                renderMedicamentos();
            }

            function eliminarMedicamento(i) {
                medicamentos.splice(i, 1);
                renderMedicamentos();
            }

            function usarPlantilla(nombre, data) {
                medicamentos.push({
                    nombre: data[0],
                    presentacion: data[1],
                    dosis: data[2],
                    frecuencia: data[3],
                    duracion: data[4],
                    cantidad: data[5],
                    indicaciones: data[6]
                });
                renderMedicamentos();
            }

            function serializarMedicamentos() {
                document.getElementById('campo-medicamentos').value = JSON.stringify(medicamentos);
            }

            function cargarEvoluciones(pid) {
                const sel = document.getElementById('sel-evolucion');
                if (!pid) {
                    sel.innerHTML = '<option value="">Sin evolución</option>';
                    return;
                }
                sel.innerHTML = '<option value="">Cargando...</option>';
                fetch(`/api/paciente/${pid}/evoluciones`)
                    .then(r => r.json())
                    .then(data => {
                        sel.innerHTML = '<option value="">Sin evolución</option>';
                        data.forEach(e => {
                            sel.innerHTML += `<option value="${e.id}">${e.fecha} — ${e.procedimiento}</option>`;
                        });
                    })
                    .catch(() => {
                        sel.innerHTML = '<option value="">Sin evolución</option>';
                    });
            }
        </script>

        {{-- Tom Select: buscador de pacientes --}}
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            new TomSelect('#sel-paciente', {
                placeholder: 'Buscar por nombre, apellido o cédula...',
                searchField: ['text', 'buscar'],
                render: {
                    option: function(data, escape) {
                        return '<div style="padding:.55rem .9rem;">' + escape(data.text) + '</div>';
                    },
                    item: function(data, escape) {
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    no_results: function() {
                        return '<div style="padding:.5rem .75rem;color:#9ca3af;font-size:.82rem;">Sin resultados</div>';
                    }
                },
                onChange: function(value) {
                    cargarEvoluciones(value);
                },
                onInitialize: function() {
                    var self = this;
                    Object.values(self.options).forEach(function(opt) {
                        var el = document.querySelector('#sel-paciente option[value="' + opt.value + '"]');
                        if (el) {
                            opt.buscar = el.getAttribute('data-buscar') || '';
                        }
                    });
                }
            });
        </script>
    @endpush

@endsection
