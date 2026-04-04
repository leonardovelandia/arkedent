@extends('layouts.app')
@section('titulo', 'Configuración')

@push('estilos')
    <style>
        /* ── Estructura (sin color, ambos temas) ─────────────────── */
        .btn-morado {
            background: var(--gradiente-btn, linear-gradient(135deg, #6B21A8, #7C3AED));
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: .5rem 1.1rem;
            font-size: .875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            transition: filter .18s;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-morado:hover { filter: brightness(1.12); color: #fff; }

        .btn-gris {
            border-radius: 8px;
            padding: .5rem 1.1rem;
            font-size: .875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            text-decoration: none;
            cursor: pointer;
        }

        .conf-card {
            border-radius: 14px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .conf-titulo {
            font-family: var(--fuente-principal, 'DM Sans', sans-serif);
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            border-radius: 8px 8px 0 0;
            padding: .5rem .75rem;
            margin: -1.5rem -1.5rem 1.1rem;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .form-label {
            font-size: .82rem;
            font-weight: 700;
            display: block;
            margin-bottom: .3rem;
        }

        .form-input {
            width: 100%;
            border-radius: 8px;
            padding: .5rem .75rem;
            font-size: .875rem;
            outline: none;
            transition: border-color .15s;
        }

        .form-select {
            width: 100%;
            border-radius: 8px;
            padding: .5rem .75rem;
            font-size: .875rem;
            outline: none;
        }

        .form-group { margin-bottom: 1rem; }

        .form-error {
            font-size: .78rem;
            color: #dc2626;
            margin-top: .25rem;
        }

        .dia-check {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            margin-right: .75rem;
            font-size: .84rem;
            cursor: pointer;
        }
        .dia-check input[type="checkbox"] {
            accent-color: var(--color-principal, #6B21A8);
            width: 16px;
            height: 16px;
        }

        .horario-tabla { width: 100%; border-collapse: collapse; }
        .horario-tabla th {
            padding: .55rem 1rem;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            text-align: left;
        }
        .horario-tabla th.col-activo { text-align: center; width: 80px; }
        .horario-tabla td { padding: .45rem .75rem; vertical-align: middle; }
        .horario-tabla tr:last-child td { border-bottom: none; }
        .horario-tabla tr.dia-inactivo .form-input-hora { opacity: .35; }

        .form-input-hora {
            width: 130px;
            border-radius: 8px;
            padding: .4rem .6rem;
            font-size: .875rem;
            outline: none;
            transition: border-color .15s;
        }

        .btn-copiar-horario {
            font-size: .72rem;
            padding: 3px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: all .15s;
        }

        .logo-preview {
            max-width: 160px;
            max-height: 80px;
            border-radius: 8px;
            object-fit: contain;
            padding: 6px;
        }

        .toggle-switch { position: relative; display: inline-block; width: 42px; height: 24px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            border-radius: 24px;
            transition: .2s;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            border-radius: 50%;
            transition: .2s;
        }
        .toggle-switch input:checked+.toggle-slider { background: var(--color-principal, #6B21A8); }
        .toggle-switch input:checked+.toggle-slider:before { transform: translateX(18px); }

        .tema-option { transition: all 0.2s; }
        .tema-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px var(--sombra-principal, rgba(107,33,168,0.2));
        }
        .tema-option.seleccionado {
            border: 2px solid var(--color-principal);
            box-shadow: 0 0 12px var(--color-principal);
            transform: scale(1.04);
        }

        /* ── Tema clásico ────────────────────────────────────────── */
        body:not([data-ui="glass"]) .conf-page-titulo { color: #1c2b22; }

        body:not([data-ui="glass"]) .btn-gris {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        body:not([data-ui="glass"]) .conf-card {
            background: #fff;
            border: 1px solid var(--color-muy-claro, #e9d5ff);
            box-shadow: 0 8px 28px var(--sombra-principal, rgba(107,33,168,.15)), 0 2px 8px rgba(0,0,0,.12);
        }

        body:not([data-ui="glass"]) .conf-titulo {
            color: var(--color-hover, #581C87);
            background: var(--color-muy-claro, #F3E8FF);
        }

        body:not([data-ui="glass"]) .form-label { color: var(--color-hover, #581C87); }

        body:not([data-ui="glass"]) .form-input {
            border: 1.5px solid var(--color-muy-claro, #e9d5ff);
            color: #1c2b22;
            background: #fff;
        }
        body:not([data-ui="glass"]) .form-input:focus { border-color: var(--color-principal, #6B21A8); }

        body:not([data-ui="glass"]) .form-select {
            border: 1.5px solid var(--color-muy-claro, #e9d5ff);
            color: #1c2b22;
            background: #fff;
        }
        body:not([data-ui="glass"]) .form-select:focus { border-color: var(--color-principal, #6B21A8); }

        body:not([data-ui="glass"]) .horario-tabla th {
            color: #64748b;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }
        body:not([data-ui="glass"]) .horario-tabla td { border-bottom: 1px solid #f1f5f9; }
        body:not([data-ui="glass"]) .horario-tabla tr.dia-inactivo .dia-nombre { color: #9ca3af; }

        body:not([data-ui="glass"]) .form-input-hora {
            border: 1.5px solid var(--color-muy-claro, #e9d5ff);
            color: #1c2b22;
            background: #fff;
        }
        body:not([data-ui="glass"]) .form-input-hora:focus { border-color: var(--color-principal, #6B21A8); }
        body:not([data-ui="glass"]) .horario-tabla tr.dia-inactivo .form-input-hora:focus {
            border-color: var(--color-muy-claro, #e9d5ff);
        }

        body:not([data-ui="glass"]) .btn-copiar-horario {
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            color: #64748b;
        }
        body:not([data-ui="glass"]) .btn-copiar-horario:hover {
            border-color: var(--color-principal, #6B21A8);
            color: var(--color-principal, #6B21A8);
        }

        body:not([data-ui="glass"]) .logo-preview {
            border: 1.5px solid var(--color-muy-claro, #e9d5ff);
            background: var(--fondo-card-alt, #faf8ff);
        }

        body:not([data-ui="glass"]) .toggle-slider { background: #d1d5db; }
        body:not([data-ui="glass"]) .toggle-slider:before { background: white; }

        /* ── Tema glass (Aurora) ─────────────────────────────────── */
        body[data-ui="glass"] .conf-page-titulo { color: white !important; }

        body[data-ui="glass"] .btn-gris {
            background: rgba(255,255,255,0.08) !important;
            color: rgba(255,255,255,0.75) !important;
            border: 1px solid rgba(255,255,255,0.15) !important;
        }
        body[data-ui="glass"] .btn-gris:hover {
            background: rgba(255,255,255,0.13) !important;
            color: white !important;
        }

        body[data-ui="glass"] .conf-card {
            background: rgba(255,255,255,0.10) !important;
            backdrop-filter: blur(20px) saturate(160%) !important;
            -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
            border: 1px solid rgba(0,234,255,0.45) !important;
            box-shadow: 0 0 8px rgba(0,234,255,0.25) !important;
        }

        body[data-ui="glass"] .conf-titulo {
            color: rgba(0,234,255,0.90) !important;
            background: rgba(0,0,0,0.25) !important;
            border-bottom: 1px solid rgba(0,234,255,0.20) !important;
        }

        body[data-ui="glass"] .form-label { color: rgba(0,234,255,0.90) !important; }

        body[data-ui="glass"] .form-input {
            border: 1.5px solid rgba(0,234,255,0.30) !important;
            color: rgba(255,255,255,0.90) !important;
            background: rgba(255,255,255,0.08) !important;
        }
        body[data-ui="glass"] .form-input:focus {
            border-color: rgba(0,234,255,0.70) !important;
        }
        body[data-ui="glass"] .form-input::placeholder { color: rgba(255,255,255,0.30) !important; }

        body[data-ui="glass"] .form-select {
            border: 1.5px solid rgba(0,234,255,0.30) !important;
            color: rgba(255,255,255,0.90) !important;
            background: rgba(255,255,255,0.08) !important;
        }
        body[data-ui="glass"] .form-select option, body[data-ui="glass"] .form-select optgroup { background: #0a2535 !important; color: rgba(255,255,255,0.88) !important; }
        body[data-ui="glass"] .form-select:focus {
            border-color: rgba(0,234,255,0.70) !important;
        }

        body[data-ui="glass"] .horario-tabla th {
            color: rgba(0,234,255,0.90) !important;
            background: rgba(0,0,0,0.25) !important;
            border-bottom: 1px solid rgba(0,234,255,0.20) !important;
        }
        body[data-ui="glass"] .horario-tabla td {
            border-bottom: 1px solid rgba(255,255,255,0.06) !important;
        }
        body[data-ui="glass"] .horario-tabla tr.dia-inactivo .dia-nombre {
            color: rgba(255,255,255,0.25) !important;
        }

        body[data-ui="glass"] .form-input-hora {
            border: 1.5px solid rgba(0,234,255,0.30) !important;
            color: rgba(255,255,255,0.90) !important;
            background: rgba(255,255,255,0.08) !important;
        }
        body[data-ui="glass"] .form-input-hora:focus {
            border-color: rgba(0,234,255,0.70) !important;
        }

        body[data-ui="glass"] .btn-copiar-horario {
            border: 1px solid rgba(0,234,255,0.20) !important;
            background: rgba(255,255,255,0.05) !important;
            color: rgba(255,255,255,0.55) !important;
        }
        body[data-ui="glass"] .btn-copiar-horario:hover {
            border-color: rgba(0,234,255,0.50) !important;
            color: rgba(0,234,255,0.90) !important;
            background: rgba(0,234,255,0.08) !important;
        }

        body[data-ui="glass"] .logo-preview {
            border: 1.5px solid rgba(255,255,255,0.15) !important;
            background: rgba(255,255,255,0.05) !important;
        }

        body[data-ui="glass"] .toggle-slider { background: rgba(255,255,255,0.2) !important; }
        body[data-ui="glass"] .toggle-slider:before { background: white !important; }
    </style>
@endpush

@section('contenido')

    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;">
        <div
            style="width:40px;height:40px;background:var(--gradiente-btn,linear-gradient(135deg,#6B21A8,#7C3AED));border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem;flex-shrink:0;">
            <i class="bi bi-gear-fill"></i>
        </div>
        <div>
            <h4 class="conf-page-titulo" style="font-family:var(--fuente-titulos,'Playfair Display',serif);font-weight:700;margin:0;">
                Configuración General</h4>
            <p style="font-size:.82rem;color:#9ca3af;margin:0;">Datos del consultorio, horarios y preferencias del sistema
            </p>
        </div>
    </div>

    {{-- Formulario separado para subir logo (evita form anidado que rompe form-configuracion) --}}
    <form id="form-logo" method="POST" action="{{ route('configuracion.logo') }}" enctype="multipart/form-data">
        @csrf
    </form>

    <form id="form-configuracion" method="POST" action="{{ route('configuracion.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- ── DATOS DEL CONSULTORIO ──────────────────────────────── --}}
        <div class="conf-card">
            <div class="conf-titulo"><i class="bi bi-building"></i> Datos del Consultorio</div>

            <div style="display:grid;grid-template-columns:2fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label class="form-label">Nombre del consultorio <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="nombre_consultorio" class="form-input"
                        value="{{ old('nombre_consultorio', $config->nombre_consultorio) }}" required maxlength="150"
                        oninput="ucwordsInput(this)">
                    @error('nombre_consultorio')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Slogan</label>
                    <input type="text" name="slogan" class="form-input" value="{{ old('slogan', $config->slogan) }}"
                        maxlength="255">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label class="form-label">NIT / RUT</label>
                    <input type="text" name="nit" id="inp-nit" class="form-input"
                        value="{{ old('nit', $config->nit) }}" maxlength="30" placeholder="900123456-7"
                        oninput="filtrarNit(this)">
                </div>
                <div class="form-group">
                    <label class="form-label">Registro médico / RETHUS</label>
                    <input type="text" name="registro_medico" class="form-input"
                        value="{{ old('registro_medico', $config->registro_medico) }}" maxlength="60">
                </div>
            </div>
        </div>

        {{-- ── LOGO DEL CONSULTORIO ────────────────────────────────── --}}
        <div class="conf-card">
            <div class="conf-titulo"><i class="bi bi-image"></i> Logo del Consultorio</div>

            <div style="display:flex;align-items:flex-start;gap:2rem;flex-wrap:wrap;">
                {{-- Preview --}}
                <div style="flex-shrink:0;">
                    <div id="logo-preview-wrap">
                        @if ($config->logo_path)
                            <img id="logo-preview-img" src="{{ asset('storage/' . $config->logo_path) }}"
                                class="logo-preview" alt="Logo actual">
                            <div style="font-size:.75rem;color:#9ca3af;margin-top:.4rem;">Logo actual</div>
                        @else
                            <div id="logo-placeholder"
                                style="width:160px;height:80px;border:2px dashed var(--color-claro);border-radius:8px;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--color-acento-activo);gap:.3rem;">
                                <i class="bi bi-image" style="font-size:1.8rem;"></i>
                                <span style="font-size:.72rem;">Vista previa</span>
                            </div>
                            <img id="logo-preview-img" src="" class="logo-preview" alt="Preview"
                                style="display:none;max-width:160px;max-height:80px;">
                            <div style="font-size:.75rem;color:#9ca3af;margin-top:.4rem;">Sin logo</div>
                        @endif
                    </div>
                </div>

                {{-- Formulario logo (referencia al form externo #form-logo para evitar anidamiento) --}}
                <div style="flex:1;min-width:260px;">
                    <div class="form-group">
                        <label class="form-label">Subir nuevo logo</label>
                        <input type="file" name="logo" id="inp-logo" class="form-input" form="form-logo"
                            accept="image/jpeg,image/png,image/svg+xml,image/webp" required
                            onchange="previsualizarLogo(this)">
                        @error('logo')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div
                        style="background:var(--fondo-card-alt);border:1px solid #e9d5ff;border-radius:8px;padding:.65rem .9rem;font-size:.76rem;margin-bottom:.75rem;color:#6b7280;line-height:1.8;">
                        <strong style="color:var(--color-hover);">Requisitos:</strong>
                        JPG, PNG, SVG o WebP · Máx. 2 MB · Recomendado 300×150 px · Fondo transparente
                    </div>
                    <button type="submit" form="form-logo" class="btn-gris">
                        <i class="bi bi-upload"></i> Subir logo
                    </button>
                </div>
            </div>
        </div>

        {{-- ── CONTACTO ───────────────────────────────────────────── --}}
        <div class="conf-card">
            <div class="conf-titulo"><i class="bi bi-telephone"></i> Contacto y Ubicación</div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-input"
                        value="{{ old('telefono', $config->telefono) }}" maxlength="30" placeholder="3001234567"
                        oninput="filtrarTel(this)">
                </div>
                <div class="form-group">
                    <label class="form-label">WhatsApp</label>
                    <input type="text" name="telefono_whatsapp" class="form-input"
                        value="{{ old('telefono_whatsapp', $config->telefono_whatsapp) }}" maxlength="30"
                        placeholder="3001234567" oninput="filtrarTel(this)">
                </div>
                <div class="form-group">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $config->email) }}"
                        maxlength="120" placeholder="consultorio@ejemplo.com">
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-input"
                        value="{{ old('direccion', $config->direccion) }}" maxlength="255" oninput="ucwordsInput(this)">
                </div>
                <div class="form-group">
                    <label class="form-label">Ciudad</label>
                    <input type="text" name="ciudad" class="form-input"
                        value="{{ old('ciudad', $config->ciudad) }}" maxlength="100" oninput="ucwordsInput(this)">
                </div>
                <div class="form-group">
                    <label class="form-label">País</label>
                    <input type="text" name="pais" class="form-input" value="{{ old('pais', $config->pais) }}"
                        maxlength="80" oninput="ucwordsInput(this)">
                </div>
            </div>
        </div>

        {{-- ── HORARIOS Y CITAS ───────────────────────────────────── --}}
        <div class="conf-card">
            <div class="conf-titulo"><i class="bi bi-clock"></i> Horarios y Citas</div>

            <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:1.25rem;">
                <div style="max-width:200px;">
                    <label class="form-label">Duración de cita (minutos) <span style="color:#dc2626;">*</span></label>
                    <select name="duracion_cita_minutos" class="form-select">
                        @foreach ([15, 20, 30, 45, 60, 90, 120] as $min)
                            <option value="{{ $min }}"
                                {{ old('duracion_cita_minutos', $config->duracion_cita_minutos) == $min ? 'selected' : '' }}>
                                {{ $min }} minutos
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="max-width:200px;">
                    <label class="form-label">Formato de hora</label>
                    <div style="display:flex;gap:.5rem;margin-top:.25rem;">
                        <label
                            style="display:flex;align-items:center;gap:.4rem;cursor:pointer;padding:.45rem .9rem;border:1.5px solid var(--fondo-borde);border-radius:8px;font-size:.875rem;font-weight:600;flex:1;justify-content:center;transition:all .15s;"
                            id="lbl-fmt-12">
                            <input type="radio" name="formato_hora" value="12"
                                {{ old('formato_hora', $config->formato_hora ?? '12') === '12' ? 'checked' : '' }}
                                onchange="aplicarFormatoHora('12')" style="display:none;">
                            🕐 12h
                        </label>
                        <label
                            style="display:flex;align-items:center;gap:.4rem;cursor:pointer;padding:.45rem .9rem;border:1.5px solid var(--fondo-borde);border-radius:8px;font-size:.875rem;font-weight:600;flex:1;justify-content:center;transition:all .15s;"
                            id="lbl-fmt-24">
                            <input type="radio" name="formato_hora" value="24"
                                {{ old('formato_hora', $config->formato_hora ?? '12') === '24' ? 'checked' : '' }}
                                onchange="aplicarFormatoHora('24')" style="display:none;">
                            🕐 24h
                        </label>
                    </div>
                    <div style="font-size:.72rem;color:#8fa39a;margin-top:.3rem;">Afecta todos los selectores de hora</div>
                </div>
            </div>

            @php
                $diasNombres = [
                    1 => 'Lunes',
                    2 => 'Martes',
                    3 => 'Miércoles',
                    4 => 'Jueves',
                    5 => 'Viernes',
                    6 => 'Sábado',
                    7 => 'Domingo',
                ];
                $rawHorario = $config->dias_laborales ?? [];
                $esFormatoViejo = !empty($rawHorario) && array_is_list($rawHorario);
                $horaDefApertura = substr($config->hora_apertura ?? '08:00', 0, 5);
                $horaDefCierre = substr($config->hora_cierre ?? '18:00', 0, 5);

                $horarioPorDia = [];
                foreach ($diasNombres as $num => $nombre) {
                    if ($esFormatoViejo) {
                        $horarioPorDia[$num] = [
                            'activo' => in_array($num, $rawHorario),
                            'apertura' => $horaDefApertura,
                            'cierre' => $horaDefCierre,
                        ];
                    } else {
                        $dia = isset($rawHorario[$num]) ? $rawHorario[$num] : [];
                        $horarioPorDia[$num] = [
                            'activo' => $dia['activo'] ?? $num <= 5,
                            'apertura' => $dia['apertura'] ?? $horaDefApertura,
                            'cierre' => $dia['cierre'] ?? $horaDefCierre,
                        ];
                    }
                }
            @endphp

            <label class="form-label" style="margin-bottom:.6rem;">Horario por día</label>
            <div style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
                <table class="horario-tabla">
                    <thead>
                        <tr>
                            <th>Día</th>
                            <th class="col-activo">Activo</th>
                            <th>Hora apertura</th>
                            <th>Hora cierre</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($diasNombres as $num => $nombre)
                            @php $dc = $horarioPorDia[$num]; @endphp
                            <tr class="horario-row {{ !$dc['activo'] ? 'dia-inactivo' : '' }}"
                                data-dia="{{ $num }}">
                                <td>
                                    <span class="dia-nombre"
                                        style="font-weight:600;font-size:.875rem;">{{ $nombre }}</span>
                                </td>
                                <td style="text-align:center;">
                                    <label class="dia-check" style="margin:0;justify-content:center;">
                                        <input type="checkbox" name="horario[{{ $num }}][activo]"
                                            value="1" {{ $dc['activo'] ? 'checked' : '' }}
                                            onchange="toggleDiaHorario({{ $num }}, this.checked)">
                                    </label>
                                </td>
                                <td>
                                    <div class="timepicker-wrap">
                                        <i class="bi bi-clock timepicker-icon"></i>
                                        <input type="text" name="horario[{{ $num }}][apertura]"
                                            id="apertura_{{ $num }}" placeholder="HH:MM"
                                            class="form-input-hora timepicker"
                                            value="{{ old('horario.' . $num . '.apertura', $dc['apertura']) }}"
                                            autocomplete="off" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="timepicker-wrap">
                                        <i class="bi bi-clock timepicker-icon"></i>
                                        <input type="text" name="horario[{{ $num }}][cierre]"
                                            id="cierre_{{ $num }}" placeholder="HH:MM"
                                            class="form-input-hora timepicker"
                                            value="{{ old('horario.' . $num . '.cierre', $dc['cierre']) }}"
                                            autocomplete="off" readonly>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn-copiar-horario"
                                        onclick="copiarHorario({{ $num }})"
                                        title="Aplicar este horario a todos los días activos">
                                        <i class="bi bi-copy"></i> Establecer este Horario a todos
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p style="font-size:.74rem;color:#9ca3af;margin-top:.5rem;margin-bottom:0;">
                <i class="bi bi-info-circle"></i> Los días desactivados no estarán disponibles para agendar citas.
            </p>

            {{-- Campos globales para compatibilidad --}}
            <input type="hidden" name="hora_apertura" id="hora_apertura_global"
                value="{{ $horarioPorDia[1]['apertura'] }}">
            <input type="hidden" name="hora_cierre" id="hora_cierre_global" value="{{ $horarioPorDia[1]['cierre'] }}">
        </div>

        {{-- ── DATOS DEL DOCTOR Y FIRMA DIGITAL ────────────────────── --}}
        <div class="conf-card">
            <div class="conf-titulo">
                <i class="bi bi-person-badge"></i> Datos del Profesional y Firma Digital
            </div>

            {{-- Inputs ocultos para la firma (parte del form-configuracion) --}}
            <input type="hidden" name="firma_canvas" id="firma-canvas-data">

            <div style="display:flex;gap:2rem;flex-wrap:wrap;align-items:flex-start;">

                {{-- Columna izquierda: datos del doctor --}}
                <div style="flex:1;min-width:220px;">
                    <div class="form-group">
                        <label class="form-label">Nombre del doctor / profesional</label>
                        <input type="text" name="firma_nombre_doctor" class="form-input"
                            value="{{ old('firma_nombre_doctor', $config->firma_nombre_doctor) }}"
                            placeholder="Ej: Dra. Tatiana Velandia">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Especialidad / Cargo</label>
                        <input type="text" name="firma_cargo" class="form-input"
                            value="{{ old('firma_cargo', $config->firma_cargo) }}" placeholder="Ej: Odontóloga General">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tarjeta profesional / Registro médico (RETHUS / TP) <span
                                style="color:#dc2626;">*</span></label>
                        <input type="text" name="firma_registro" class="form-input"
                            value="{{ old('firma_registro', $config->firma_registro ?? $config->registro_medico) }}"
                            placeholder="Ej: TP-12345">

                    </div>
                    <div class="form-group">
                        <label class="form-label">Universidad</label>
                        <input type="text" name="universidad" class="form-input"
                            value="{{ old('universidad', $config->universidad ?? '') }}"
                            placeholder="Universidad Nacional..." maxlength="150">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Tipo de prestador</label>
                        <select name="tipo_prestador" class="form-select" id="sel-tipo-prestador">
                            <option value="consultorio_privado"
                                {{ ($config->tipo_prestador ?? 'consultorio_privado') === 'consultorio_privado' ? 'selected' : '' }}>
                                Consultorio privado</option>
                            <option value="ips" {{ ($config->tipo_prestador ?? '') === 'ips' ? 'selected' : '' }}>IPS
                                habilitada</option>
                            <option value="centro_medico"
                                {{ ($config->tipo_prestador ?? '') === 'centro_medico' ? 'selected' : '' }}>Centro médico
                            </option>
                        </select>
                    </div>
                    <div id="campo-habilitacion-wrap"
                        style="{{ ($config->tipo_prestador ?? 'consultorio_privado') === 'consultorio_privado' ? 'display:none;' : '' }}">
                        <div class="form-group" style="margin-top:.75rem;margin-bottom:0;">
                            <label class="form-label">Código de habilitación REPS</label>
                            <input type="text" name="codigo_habilitacion" class="form-input"
                                value="{{ old('codigo_habilitacion', $config->codigo_habilitacion ?? '') }}"
                                placeholder="Código REPS" maxlength="50">
                            <div style="font-size:.72rem;color:#9ca3af;margin-top:.2rem;">Obligatorio para IPS</div>
                        </div>
                    </div>
                </div>

                {{-- Columna derecha: firma digital --}}
                <div style="flex:1.2;min-width:260px;">
                    {{-- Tabs --}}
                    <div style="display:flex;gap:.5rem;margin-bottom:.75rem;">
                        <button type="button" id="tab-dibujar" onclick="cambiarTab('dibujar')"
                            style="padding:.35rem .85rem;border-radius:6px;border:1.5px solid var(--color-principal);background:var(--color-principal);color:#fff;font-size:.8rem;cursor:pointer;font-weight:500;">
                            <i class="bi bi-pencil"></i> Dibujar firma
                        </button>
                        <button type="button" id="tab-subir" onclick="cambiarTab('subir')"
                            style="padding:.35rem .85rem;border-radius:6px;border:1.5px solid #d1d5db;background:#fff;color:#6b7280;font-size:.8rem;cursor:pointer;">
                            <i class="bi bi-upload"></i> Subir imagen
                        </button>
                    </div>

                    {{-- Panel: dibujar --}}
                    <div id="panel-dibujar">
                        <div
                            style="position:relative;border:2px solid #d1d5db;border-radius:8px;background:#fafafa;overflow:hidden;max-width:380px;">
                            <canvas id="firma-canvas" width="380" height="110"
                                style="display:block;width:100%;cursor:crosshair;touch-action:none;"></canvas>
                            <div id="canvas-hint"
                                style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:.76rem;color:#c0b8d0;pointer-events:none;text-align:center;">
                                <i class="bi bi-pencil-square"
                                    style="font-size:1.3rem;display:block;margin-bottom:.2rem;"></i>
                                Dibuja tu firma aquí
                            </div>
                        </div>
                        <div style="display:flex;gap:.5rem;margin-top:.5rem;align-items:center;">
                            <button type="button" onclick="limpiarCanvas()"
                                style="padding:.3rem .8rem;border-radius:6px;border:1px solid #d1d5db;background:#fff;color:#6b7280;font-size:.78rem;cursor:pointer;">
                                <i class="bi bi-trash"></i> Limpiar
                            </button>
                            <span style="font-size:.72rem;color:#9ca3af;">PNG · 380×110 px</span>
                        </div>
                    </div>

                    {{-- Panel: subir --}}
                    <div id="panel-subir" style="display:none;">
                        <label
                            style="display:flex;align-items:center;gap:.6rem;padding:.65rem .9rem;border:2px dashed #d1d5db;border-radius:8px;cursor:pointer;background:#fafafa;max-width:380px;"
                            id="label-subir-firma">
                            <i class="bi bi-cloud-upload"
                                style="font-size:1.4rem;color:var(--color-principal);flex-shrink:0;"></i>
                            <div>
                                <div id="texto-subir-firma"
                                    style="font-size:.83rem;font-weight:600;color:var(--texto-principal);">
                                    {{ $config->firma_path ? 'Haz clic para reemplazar la firma' : 'Haz clic para subir firma' }}
                                </div>
                                <div style="font-size:.74rem;color:#9ca3af;margin-top:.15rem;">PNG transparente recomendado
                                    · Máx. 2 MB</div>
                            </div>
                            <input type="file" name="firma_imagen" id="inp-firma"
                                accept="image/png,image/jpg,image/jpeg" style="display:none;"
                                onchange="previsualizarFirmaSubida(this)">
                        </label>
                        {{-- Preview de imagen seleccionada --}}
                        <div id="preview-firma-subida"
                            style="display:none;margin-top:.6rem;padding:.5rem;border:1px solid #d1d5db;border-radius:8px;background:#fff;max-width:380px;text-align:center;">
                            <img id="preview-firma-img" src="" style="max-height:60px;max-width:200px;">
                            <div style="font-size:.72rem;color:#16a34a;margin-top:.3rem;font-weight:600;">
                                <i class="bi bi-check-circle me-1"></i><span id="preview-firma-nombre"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>{{-- fin conf-card doctor/firma --}}

    </form>{{-- Fin form-configuracion --}}

    {{-- Firma guardada actualmente (fuera del form principal para evitar form anidado) --}}
    @if ($config->firma_path)
        <div
            style="margin-top:-.9rem;margin-bottom:1.25rem;padding:.85rem 1rem;background:var(--fondo-card-alt);border:1px solid var(--color-muy-claro, #e9d5ff);border-top:none;border-bottom-left-radius:14px;border-bottom-right-radius:14px;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
            <div style="font-size:.75rem;font-weight:600;color:var(--color-principal);white-space:nowrap;">
                <i class="bi bi-check-circle-fill me-1"></i> Firma guardada:
            </div>
            <div
                style="background:#fff;padding:.5rem 1rem;border:1px solid #e0d0f0;border-radius:6px;text-align:center;flex-shrink:0;">
                <img src="{{ asset('storage/' . $config->firma_path) }}" alt="Firma"
                    style="max-height:48px;max-width:160px;display:block;margin:0 auto 4px;">
                <div style="border-top:1px solid #555;padding-top:3px;font-size:.7rem;color:#555;">
                    {{ $config->firma_nombre_doctor ?? '' }}&nbsp;
                    @if ($config->firma_cargo)
                        <span style="color:#888;">{{ $config->firma_cargo }}</span>
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route('configuracion.firma.eliminar') }}"
                onsubmit="return confirm('¿Eliminar la firma guardada?')" style="margin-left:auto;">
                @csrf @method('DELETE')
                <button type="submit"
                    style="padding:.35rem .8rem;border-radius:6px;border:1px solid #fca5a5;background:#fff5f5;color:#dc2626;font-size:.78rem;cursor:pointer;">
                    <i class="bi bi-trash"></i> Eliminar firma
                </button>
            </form>
        </div>
    @endif

    {{-- ── MONEDA Y FORMATO ───────────────────────────────────── --}}
    <div class="conf-card">
        <div class="conf-titulo"><i class="bi bi-currency-dollar"></i> Moneda y Formato</div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group">
                <label class="form-label">Moneda (código)</label>
                <input type="text" name="moneda" class="form-input"
                    value="{{ old('moneda', $config->moneda ?? 'COP') }}" maxlength="10" placeholder="COP">
            </div>
            <div class="form-group">
                <label class="form-label">Símbolo de moneda</label>
                <input type="text" name="simbolo_moneda" class="form-input"
                    value="{{ old('simbolo_moneda', $config->simbolo_moneda ?? '$') }}" maxlength="5" placeholder="$">
            </div>
        </div>
    </div>

    {{-- ── RECORDATORIOS ──────────────────────────────────────── --}}
    <div class="conf-card">
        <div class="conf-titulo"><i class="bi bi-bell"></i> Recordatorios y Notificaciones</div>

        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
            <label class="toggle-switch">
                <input type="checkbox" name="recordatorios_activos" value="1"
                    {{ old('recordatorios_activos', $config->recordatorios_activos) ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
            <span style="font-size:.875rem;color:#374151;font-weight:500;">Activar recordatorios automáticos de
                citas</span>
        </div>

        <div style="max-width:280px;">
            <label class="form-label">Horas de anticipación para recordatorio</label>
            <input type="number" name="horas_anticipacion_recordatorio" class="form-input"
                value="{{ old('horas_anticipacion_recordatorio', $config->horas_anticipacion_recordatorio ?? 24) }}"
                min="1" max="168" placeholder="24">
        </div>
    </div>

    {{-- ── APARIENCIA ─────────────────────────────────────────── --}}
    <div class="conf-card">
        <div class="conf-titulo"><i class="bi bi-palette"></i> Tema Visual</div>

        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:1rem;">
            @php
                $temas = [
                    'morado-elegante' => [
                        'nombre' => 'Morado Elegante',
                        'tipo' => 'Premium',
                        'c1' => '#6B21A8',
                        'c2' => '#7C3AED',
                        'c3' => '#F3E8FF',
                        'sidebar' => '#3B0764',
                    ],

                    'rosa-profesional' => [
                        'nombre' => 'Rosa Pastel',
                        'tipo' => 'Estético',
                        'c1' => '#C2476B',
                        'c2' => '#F472B6',
                        'c3' => '#FFF0F8',
                        'sidebar' => '#9D174D',
                    ],

                    'verde-esmeralda' => [
                        'nombre' => 'Verde Esmeralda',
                        'tipo' => 'Natural',
                        'c1' => '#065F46',
                        'c2' => '#10B981',
                        'c3' => '#D1FAE5',
                        'sidebar' => '#022C22',
                    ],

                    'azul-marino' => [
                        'nombre' => 'Azul Marino',
                        'tipo' => 'Corporativo',
                        'c1' => '#1E3A5F',
                        'c2' => '#2563EB',
                        'c3' => '#DBEAFE',
                        'sidebar' => '#0F1F3D',
                    ],

                    'carbon-moderno' => [
                        'nombre' => 'Carbón Moderno',
                        'tipo' => 'Oscuro',
                        'c1' => '#4F46E5',
                        'c2' => '#6366F1',
                        'c3' => '#EEF2FF',
                        'sidebar' => '#111827',
                    ],

                    'azul-clinico' => [
                        'nombre' => 'Dental Fresh',
                        'tipo' => 'Odontológico',
                        'c1' => '#06B6D4',
                        'c2' => '#A5F3FC',
                        'c3' => '#ECFEFF',
                        'sidebar' => '#0F3546',
                    ],
                ];
                $temaActual = old('tema', $config->tema ?? 'morado-elegante');
            @endphp

            @foreach ($temas as $key => $tema)
                <label style="cursor:pointer;">
                    <input type="radio" name="tema" value="{{ $key }}"
                        {{ $temaActual === $key ? 'checked' : '' }} style="display:none;" class="radio-tema">
                    <div class="tema-option {{ $temaActual === $key ? 'seleccionado' : '' }}"
                        style="border-radius:10px; overflow:hidden; border:2px solid {{ $temaActual === $key ? $tema['c1'] : '#e5e5e5' }};">

                        {{-- Mini preview --}}
                        <div style="display:flex; height:60px;">
                            <div
                                style="width:40px; background:{{ $tema['sidebar'] }}; padding:6px 4px; display:flex; flex-direction:column; gap:3px;">
                                <div style="height:4px; border-radius:2px; background:rgba(255,255,255,0.3);"></div>
                                <div style="height:3px; border-radius:2px; background:rgba(255,255,255,0.6); width:80%;">
                                </div>
                                <div style="height:3px; border-radius:2px; background:rgba(255,255,255,0.2);"></div>
                                <div style="height:3px; border-radius:2px; background:rgba(255,255,255,0.2);"></div>
                            </div>
                            <div
                                style="flex:1; background:{{ $tema['c3'] }}; padding:6px; display:flex; flex-direction:column; gap:3px;">
                                <div style="height:12px; border-radius:3px; background:{{ $tema['c1'] }};"></div>
                                <div style="display:flex; gap:3px;">
                                    <div
                                        style="flex:1; height:20px; border-radius:3px; background:white; border:1px solid {{ $tema['c3'] }};">
                                    </div>
                                    <div
                                        style="flex:1; height:20px; border-radius:3px; background:white; border:1px solid {{ $tema['c3'] }};">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div style="padding:6px 8px; background:white; border-top:1px solid {{ $tema['c3'] }};">
                            <div style="font-size:0.72rem; font-weight:600; color:{{ $tema['c1'] }};">
                                {{ $tema['nombre'] }}</div>
                            <div style="font-size:0.65rem; color:#999;">{{ $tema['tipo'] }}</div>
                        </div>
                    </div>
                </label>
            @endforeach
        </div>
    </div>

    <div class="conf-card">
        <div class="conf-titulo"><i class="bi bi-fonts"></i> Tipografía</div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
            {{-- Fuente principal --}}
            <div>
                <label class="form-label">Fuente del sistema (textos, menús, tablas)</label>
                <select name="fuente_principal" class="form-select">
                    @php
                        $fuentesPrincipales = [
                            'DM Sans' => 'DM Sans — Moderna y limpia (recomendada)',
                            'Nunito' => 'Nunito — Suave y amigable',
                            'Inter' => 'Inter — Técnica y precisa',
                            'Source Sans Pro' => 'Source Sans Pro — Profesional',
                            'Space Grotesk' => 'Space Grotesk — Contemporánea',
                            'Outfit' => 'Outfit — Geométrica y elegante',
                        ];
                    @endphp
                    @foreach ($fuentesPrincipales as $valor => $label)
                        <option value="{{ $valor }}"
                            {{ old('fuente_principal', $config->fuente_principal ?? 'DM Sans') === $valor ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Fuente de títulos --}}
            <div>
                <label class="form-label">Fuente de títulos (encabezados, nombres)</label>
                <select name="fuente_titulos" class="form-select">
                    @php
                        $fuentesTitulos = [
                            'Playfair Display' => 'Playfair Display — Clásica y elegante (recomendada)',
                            'Cormorant Garamond' => 'Cormorant Garamond — Sofisticada',
                            'Libre Baskerville' => 'Libre Baskerville — Formal y seria',
                            'Merriweather' => 'Merriweather — Editorial',
                            'Syne' => 'Syne — Moderna y atrevida',
                            'Lora' => 'Lora — Cálida y literaria',
                        ];
                    @endphp
                    @foreach ($fuentesTitulos as $valor => $label)
                        <option value="{{ $valor }}"
                            {{ old('fuente_titulos', $config->fuente_titulos ?? 'Playfair Display') === $valor ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Preview de fuentes en tiempo real --}}
        <div id="preview-fuentes"
            style="margin-top:1rem; padding:1rem; background:var(--fondo-card-alt,#faf8ff); border-radius:8px; border:1px solid var(--fondo-borde,#ede9e0);">
            <div id="preview-titulo"
                style="font-size:1.2rem; font-weight:600; color:var(--color-principal,#6B21A8); margin-bottom:4px; font-family:'{{ $config->fuente_titulos ?? 'Playfair Display' }}', serif;">
                {{ $config->nombre_consultorio ?? 'Arkedent' }}
            </div>
            <div id="preview-texto"
                style="font-size:0.875rem; color:#5c6b62; font-family:'{{ $config->fuente_principal ?? 'DM Sans' }}', sans-serif;">
                Sistema de gestión odontológica integral — Historia clínica, citas y pagos
            </div>
        </div>

        {{-- Combinaciones sugeridas --}}
        <div
            style="margin-top:0.75rem; padding:0.75rem 1rem; background:#fffbf0; border:1px solid #fde68a; border-radius:8px;">
            <div style="font-size:0.78rem; color:#92400e; font-weight:500;">
                <i class="bi bi-lightbulb me-1"></i>
                Combinaciones sugeridas por tema:
            </div>
            <div style="font-size:0.73rem; color:#92400e; margin-top:4px; line-height:1.8;">
                Morado Elegante → DM Sans + Playfair Display<br>
                Rosa Profesional → Nunito + Cormorant Garamond<br>
                Verde Esmeralda → Inter + Libre Baskerville<br>
                Azul Marino → Source Sans Pro + Merriweather<br>
                Carbón Moderno → Space Grotesk + Syne<br>
                Verde Premium → Outfit + Lora
            </div>
        </div>

        <div id="notif-fuentes"
            style="display:none; margin-top:.5rem; padding:.5rem .75rem; background:var(--color-muy-claro,#F3E8FF); border-radius:6px; font-size:.78rem; color:var(--color-hover,#581C87);">
        </div>
    </div>






    {{-- ── ESTILO DE INTERFAZ ─────────────────────────────────────── --}}
    <div class="conf-card">
        <div class="conf-titulo"><i class="bi bi-layers"></i> Estilo de Interfaz</div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">

            {{-- Opción Clásico --}}
            <label style="cursor:pointer;">
                <input type="radio" name="tema_ui" value="clasico"
                    {{ ($config->tema_ui ?? 'clasico') === 'clasico' ? 'checked' : '' }}
                    style="display:none;" class="radio-tema-ui" form="form-configuracion">
                <div class="tema-ui-opt {{ ($config->tema_ui ?? 'clasico') === 'clasico' ? 'ui-sel' : '' }}"
                    style="border-radius:12px;overflow:hidden;border:2px solid {{ ($config->tema_ui ?? 'clasico') === 'clasico' ? 'var(--color-principal)' : '#e5e5e5' }};transition:all .2s;cursor:pointer;">
                    <div style="height:70px;background:#faf8f4;display:flex;">
                        <div style="width:45px;background:var(--color-sidebar,#3B0764);"></div>
                        <div style="flex:1;padding:8px;display:flex;flex-direction:column;gap:4px;">
                            <div style="height:10px;border-radius:3px;background:var(--color-principal,#6B21A8);width:60%;"></div>
                            <div style="display:flex;gap:4px;">
                                <div style="flex:1;height:18px;border-radius:3px;background:white;border:1px solid #ede9e0;"></div>
                                <div style="flex:1;height:18px;border-radius:3px;background:white;border:1px solid #ede9e0;"></div>
                            </div>
                        </div>
                    </div>
                    <div style="padding:8px 10px;background:white;border-top:1px solid #f0f0f0;">
                        <div style="font-size:.75rem;font-weight:700;color:var(--color-principal,#6B21A8);">Clásico</div>
                        <div style="font-size:.65rem;color:#999;">Colores planos · Actual</div>
                    </div>
                </div>
            </label>

            {{-- Opción Aurora Glass --}}
            <label style="cursor:pointer;">
                <input type="radio" name="tema_ui" value="glass"
                    {{ ($config->tema_ui ?? 'clasico') === 'glass' ? 'checked' : '' }}
                    style="display:none;" class="radio-tema-ui" form="form-configuracion">
                <div class="tema-ui-opt {{ ($config->tema_ui ?? 'clasico') === 'glass' ? 'ui-sel' : '' }}"
                    style="border-radius:12px;overflow:hidden;border:2px solid {{ ($config->tema_ui ?? 'clasico') === 'glass' ? '#7c3aed' : '#e5e5e5' }};transition:all .2s;cursor:pointer;">
                    <div style="height:70px;background:linear-gradient(135deg,#0f0728,#1a0a4e);display:flex;position:relative;overflow:hidden;">
                        <div style="position:absolute;width:80px;height:80px;border-radius:50%;background:rgba(124,58,237,.5);filter:blur(20px);top:-20px;left:-20px;"></div>
                        <div style="position:absolute;width:60px;height:60px;border-radius:50%;background:rgba(6,182,212,.4);filter:blur(15px);top:-10px;right:10px;"></div>
                        <div style="width:45px;background:rgba(10,5,32,.7);border-right:1px solid rgba(167,139,250,.2);z-index:1;"></div>
                        <div style="flex:1;padding:8px;display:flex;flex-direction:column;gap:4px;z-index:1;">
                            <div style="height:10px;border-radius:3px;background:rgba(167,139,250,.6);width:60%;"></div>
                            <div style="display:flex;gap:4px;">
                                <div style="flex:1;height:18px;border-radius:3px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);"></div>
                                <div style="flex:1;height:18px;border-radius:3px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);"></div>
                            </div>
                        </div>
                    </div>
                    <div style="padding:8px 10px;background:linear-gradient(135deg,#1a0a3e,#0f1a3e);border-top:1px solid rgba(167,139,250,.2);">
                        <div style="font-size:.75rem;font-weight:700;color:#c4b5fd;">✨ Aurora Glass</div>
                        <div style="font-size:.65rem;color:#7c6fad;">Moderno · Glassmorphism</div>
                    </div>
                </div>
            </label>

        </div>
        <p style="font-size:.73rem;color:#9ca3af;margin-top:.75rem;margin-bottom:0;">
            <i class="bi bi-info-circle"></i>
            Aurora Glass aplica fondo animado y efecto vidrio a toda la interfaz. Requiere guardar para aplicar.
        </p>
    </div>

    {{-- ── BACKUP DE DATOS ────────────────────────────────────────── --}}
    <div class="conf-card">
        <div class="conf-titulo">
            <i class="bi bi-cloud-arrow-up"></i> Respaldo de Datos (Backup)
        </div>

        @php
            $backupNombre = config('backup.backup.name', config('app.name'));
            $backupDiskRoot = storage_path('app/backups');
            $backupDirPath = $backupDiskRoot . DIRECTORY_SEPARATOR . $backupNombre;
            $ultimoBackup = null;
            $ultimoBackupFecha = null;
            $ultimoBackupSize = null;
            try {
                $backupFiles = \Storage::disk('local_backups')->files($backupNombre);
                if (!empty($backupFiles)) {
                    $ultimoBackup = last($backupFiles);
                    $ultimoBackupFecha = \Carbon\Carbon::createFromTimestamp(
                        \Storage::disk('local_backups')->lastModified($ultimoBackup),
                    )->setTimezone('America/Bogota');
                    $ultimoBackupSize = round(\Storage::disk('local_backups')->size($ultimoBackup) / 1024, 1);
                }
            } catch (\Throwable $e) {
            }
        @endphp

        @if ($ultimoBackup)
            <div
                style="background:#d1fae5;border:1px solid #16a34a;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.82rem;display:flex;flex-wrap:wrap;gap:.5rem 1.5rem;align-items:center;">
                <span><i class="bi bi-check-circle-fill text-success me-1"></i> <strong>Último backup:</strong>
                    {{ $ultimoBackupFecha->format('d/m/Y H:i') }}</span>
                <span style="color:#6b7280;">{{ $ultimoBackupSize }} KB</span>
                <span style="color:#6b7280;font-size:.76rem;word-break:break-all;"><i
                        class="bi bi-folder2 me-1"></i>{{ $backupDirPath }}</span>
            </div>
        @else
            <div
                style="background:#fef9c3;border:1px solid #ca8a04;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.82rem;">
                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                No se encontraron backups en <code>{{ $backupDirPath }}</code>. Ejecuta el primero manualmente.
            </div>
        @endif

        <div style="display:flex;align-items:flex-end;gap:1.5rem;flex-wrap:wrap;margin-top:.25rem;">
            {{-- Hora del backup automático --}}
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Hora del backup automático diario</label>
                <div class="timepicker-wrap">
                    <i class="bi bi-clock timepicker-icon"></i>
                    <input type="text" name="hora_backup" id="hora_backup" class="form-input-hora timepicker"
                        form="form-configuracion" value="{{ old('hora_backup', $config->hora_backup ?? '02:00') }}"
                        placeholder="HH:MM" autocomplete="off" readonly>
                </div>
                <div style="font-size:.72rem;color:#9ca3af;margin-top:.2rem;">Se ejecuta automáticamente cada día a esta
                    hora.</div>
            </div>

            {{-- Botón manual --}}
            <div style="margin-bottom:0;">
                <form method="POST" action="{{ route('configuracion.backup.ejecutar') }}">
                    @csrf
                    <button type="submit" class="btn-morado"
                        onclick="return confirm('¿Ejecutar backup ahora? Puede tardar unos segundos.')">
                        <i class="bi bi-cloud-upload"></i> Hacer backup ahora
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── BOTÓN GUARDAR AL FINAL ───────────────────────────────── --}}
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:2rem;">
        <button type="submit" form="form-configuracion" class="btn-morado">
            <i class="bi bi-check-circle"></i> Guardar configuración
        </button>
    </div>

@endsection

@push('scripts')
    <script>
        // ── Tipo de prestador — mostrar/ocultar habilitación ─────
        document.getElementById('sel-tipo-prestador')?.addEventListener('change', function() {
            var wrap = document.getElementById('campo-habilitacion-wrap');
            if (wrap) wrap.style.display = this.value === 'ips' ? 'block' : 'none';
        });

        // ── Formato de hora ──────────────────────────────────────
        function aplicarFormatoHora(fmt) {
            var lbl12 = document.getElementById('lbl-fmt-12');
            var lbl24 = document.getElementById('lbl-fmt-24');
            var activo =
                'display:flex;align-items:center;gap:.4rem;cursor:pointer;padding:.45rem .9rem;border:1.5px solid var(--color-principal);border-radius:8px;font-size:.875rem;font-weight:600;flex:1;justify-content:center;transition:all .15s;background:var(--color-muy-claro);color:var(--color-principal);';
            var normal =
                'display:flex;align-items:center;gap:.4rem;cursor:pointer;padding:.45rem .9rem;border:1.5px solid var(--fondo-borde);border-radius:8px;font-size:.875rem;font-weight:600;flex:1;justify-content:center;transition:all .15s;';
            if (fmt === '24') {
                lbl24.style.cssText = activo;
                lbl12.style.cssText = normal;
            } else {
                lbl12.style.cssText = activo;
                lbl24.style.cssText = normal;
            }
            if (window.reinitTimepickers) window.reinitTimepickers(fmt === '24');
        }

        // Aplicar estilo inicial al cargar
        document.addEventListener('DOMContentLoaded', function() {
            var checked = document.querySelector('[name="formato_hora"]:checked');
            if (checked) aplicarFormatoHora(checked.value);
        });

        // ── Preview de fuentes en tiempo real ──
        document.querySelector('[name="fuente_principal"]')?.addEventListener('change', function() {
            const fuente = this.value;
            document.getElementById('preview-texto').style.fontFamily = `'${fuente}', sans-serif`;
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href =
                `https://fonts.googleapis.com/css2?family=${fuente.replace(/ /g, '+')}:wght@300;400;500&display=swap`;
            document.head.appendChild(link);
        });

        document.querySelector('[name="fuente_titulos"]')?.addEventListener('change', function() {
            const fuente = this.value;
            document.getElementById('preview-titulo').style.fontFamily = `'${fuente}', serif`;
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href =
                `https://fonts.googleapis.com/css2?family=${fuente.replace(/ /g, '+')}:wght@400;600&display=swap`;
            document.head.appendChild(link);
        });

        // ── Al seleccionar tema, sugerir fuentes automáticamente ──
        const fuentesSugeridas = {
            'morado-elegante': {
                principal: 'DM Sans',
                titulos: 'Playfair Display'
            },
            'rosa-profesional': {
                principal: 'Nunito',
                titulos: 'Cormorant Garamond'
            },
            'verde-esmeralda': {
                principal: 'Inter',
                titulos: 'Libre Baskerville'
            },
            'azul-marino': {
                principal: 'Source Sans Pro',
                titulos: 'Merriweather'
            },
            'carbon-moderno': {
                principal: 'Space Grotesk',
                titulos: 'Syne'
            },
            'azul-clinico': {
                principal: 'Outfit',
                titulos: 'Lora'
            },
        };

        document.querySelectorAll('.radio-tema').forEach(radio => {
            radio.addEventListener('change', function() {
                const tema = this.value;
                const sugeridas = fuentesSugeridas[tema];

                // Actualizar borde de selección visual
                document.querySelectorAll('.tema-option').forEach(opt => {
                    opt.style.border = '2px solid #e5e5e5';
                    opt.classList.remove('seleccionado');
                });
                const optionDiv = this.closest('label').querySelector('.tema-option');
                if (optionDiv) {
                    optionDiv.style.border = '';
                    optionDiv.classList.add('seleccionado');
                }

                if (sugeridas) {
                    const selectPrincipal = document.querySelector('[name="fuente_principal"]');
                    const selectTitulos = document.querySelector('[name="fuente_titulos"]');
                    if (selectPrincipal) selectPrincipal.value = sugeridas.principal;
                    if (selectTitulos) selectTitulos.value = sugeridas.titulos;

                    document.getElementById('preview-texto').style.fontFamily =
                        `'${sugeridas.principal}', sans-serif`;
                    document.getElementById('preview-titulo').style.fontFamily =
                        `'${sugeridas.titulos}', serif`;

                    const notif = document.getElementById('notif-fuentes');
                    if (notif) {
                        notif.textContent =
                            `Fuentes actualizadas a las recomendadas para ${tema.replace(/-/g, ' ')}`;
                        notif.style.display = 'block';
                        setTimeout(() => notif.style.display = 'none', 3000);
                    }
                }
            });
        });

        // ── Horario por día ──
        function toggleDiaHorario(num, activo) {
            const row = document.querySelector(`.horario-row[data-dia="${num}"]`);
            if (activo) {
                row.classList.remove('dia-inactivo');
            } else {
                row.classList.add('dia-inactivo');
            }
        }

        function copiarHorario(num) {
            const apertura = document.getElementById(`apertura_${num}`).value;
            const cierre = document.getElementById(`cierre_${num}`).value;
            document.querySelectorAll('.horario-row').forEach(row => {
                const d = row.dataset.dia;
                const cb = row.querySelector(`input[name="horario[${d}][activo]"]`);
                if (cb && cb.checked) {
                    document.getElementById(`apertura_${d}`).value = apertura;
                    document.getElementById(`cierre_${d}`).value = cierre;
                }
            });
        }

        function ucwordsInput(inp) {
            var pos = inp.selectionStart;
            inp.value = inp.value.replace(/\S+/g, function(w) {
                return w.charAt(0).toUpperCase() + w.slice(1).toLowerCase();
            });
            inp.setSelectionRange(pos, pos);
        }

        function filtrarNit(inp) {
            var pos = inp.selectionStart;
            inp.value = inp.value.replace(/[^0-9\-]/g, '');
            inp.setSelectionRange(pos, pos);
        }

        function filtrarTel(inp) {
            var pos = inp.selectionStart;
            inp.value = inp.value.replace(/[^0-9]/g, '');
            inp.setSelectionRange(pos, pos);
        }

        function previsualizarLogo(input) {
            if (!input.files || !input.files[0]) return;
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.getElementById('logo-preview-img');
                var placeholder = document.getElementById('logo-placeholder');
                img.src = e.target.result;
                img.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }

        // ── Preview imagen de firma subida ──
        function previsualizarFirmaSubida(input) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-firma-img').src = e.target.result;
                document.getElementById('preview-firma-nombre').textContent = file.name;
                document.getElementById('preview-firma-subida').style.display = '';
                document.getElementById('texto-subir-firma').textContent =
                    'Imagen seleccionada — se guardará al presionar Guardar';
            };
            reader.readAsDataURL(file);
            // Limpiar canvas para que no compita
            document.getElementById('firma-canvas-data').value = '';
        }

        // ── Tabs firma ──
        function cambiarTab(tab) {
            const btnDibujar = document.getElementById('tab-dibujar');
            const btnSubir = document.getElementById('tab-subir');
            const panelD = document.getElementById('panel-dibujar');
            const panelS = document.getElementById('panel-subir');
            const activeStyle =
                'padding:.35rem .85rem;border-radius:6px;border:1.5px solid var(--color-principal);background:var(--color-principal);color:#fff;font-size:.8rem;cursor:pointer;font-weight:500;';
            const inactiveStyle =
                'padding:.35rem .85rem;border-radius:6px;border:1.5px solid #d1d5db;background:#fff;color:#6b7280;font-size:.8rem;cursor:pointer;';
            if (tab === 'dibujar') {
                btnDibujar.style.cssText = activeStyle;
                btnSubir.style.cssText = inactiveStyle;
                panelD.style.display = '';
                panelS.style.display = 'none';
                // Limpiar input de archivo para que no interfiera
                const inp = document.getElementById('inp-firma');
                if (inp) inp.value = '';
                document.getElementById('preview-firma-subida').style.display = 'none';
            } else {
                btnSubir.style.cssText = activeStyle;
                btnDibujar.style.cssText = inactiveStyle;
                panelS.style.display = '';
                panelD.style.display = 'none';
                // Limpiar canvas para que no interfiera
                document.getElementById('firma-canvas-data').value = '';
            }
        }

        // ── Canvas de firma ──
        (function() {
            const canvas = document.getElementById('firma-canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const hint = document.getElementById('canvas-hint');
            let dibujando = false;
            let hayTrazo = false;

            // Escalar canvas al tamaño visual
            function escalar() {
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * window.devicePixelRatio;
                canvas.height = rect.height * window.devicePixelRatio;
                ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
                ctx.strokeStyle = '#1a1a2e';
                ctx.lineWidth = 2.2;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
            }
            escalar();

            function getPosicion(e) {
                const rect = canvas.getBoundingClientRect();
                const src = e.touches ? e.touches[0] : e;
                return {
                    x: src.clientX - rect.left,
                    y: src.clientY - rect.top
                };
            }

            function iniciar(e) {
                e.preventDefault();
                dibujando = true;
                const p = getPosicion(e);
                ctx.beginPath();
                ctx.moveTo(p.x, p.y);
                if (hint) hint.style.display = 'none';
            }

            function dibujar(e) {
                e.preventDefault();
                if (!dibujando) return;
                hayTrazo = true;
                const p = getPosicion(e);
                ctx.lineTo(p.x, p.y);
                ctx.stroke();
            }

            function terminar(e) {
                e.preventDefault();
                dibujando = false;
                ctx.beginPath();
                if (hayTrazo) {
                    document.getElementById('firma-canvas-data').value = canvas.toDataURL('image/png');
                }
            }

            canvas.addEventListener('mousedown', iniciar);
            canvas.addEventListener('mousemove', dibujar);
            canvas.addEventListener('mouseup', terminar);
            canvas.addEventListener('mouseleave', terminar);
            canvas.addEventListener('touchstart', iniciar, {
                passive: false
            });
            canvas.addEventListener('touchmove', dibujar, {
                passive: false
            });
            canvas.addEventListener('touchend', terminar, {
                passive: false
            });

            window.limpiarCanvas = function() {
                const rect = canvas.getBoundingClientRect();
                ctx.clearRect(0, 0, rect.width, rect.height);
                hayTrazo = false;
                if (hint) hint.style.display = '';
                document.getElementById('firma-canvas-data').value = '';
            };

            // Al enviar el form principal, capturar canvas si hay trazo y el tab dibujar está activo
            document.getElementById('form-configuracion')?.addEventListener('submit', function() {
                if (hayTrazo && document.getElementById('panel-dibujar').style.display !== 'none') {
                    document.getElementById('firma-canvas-data').value = canvas.toDataURL('image/png');
                }
            });
        })();

        // ── Selección visual Estilo de Interfaz ──
        document.querySelectorAll('.radio-tema-ui').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.tema-ui-opt').forEach(function(opt) {
                    opt.style.border = '2px solid #e5e5e5';
                    opt.classList.remove('ui-sel');
                });
                var opt = this.closest('label').querySelector('.tema-ui-opt');
                if (opt) {
                    opt.style.border = this.value === 'glass'
                        ? '2px solid #7c3aed'
                        : '2px solid var(--color-principal)';
                    opt.classList.add('ui-sel');
                }
            });
        });
    </script>
@endpush