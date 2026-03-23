@extends('layouts.app')
@section('titulo', 'Configuración')

@push('estilos')
<style>
    .btn-morado { background:var(--gradiente-btn, linear-gradient(135deg,#6B21A8,#7C3AED)); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; transition:filter .18s; text-decoration:none; cursor:pointer; }
    .btn-morado:hover { filter:brightness(1.12); color:#fff; }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1.1rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; }
    .conf-card { background:#fff; border:1px solid var(--color-muy-claro, #e9d5ff); border-radius:14px; padding:1.5rem; margin-bottom:1.25rem; box-shadow:0 8px 28px var(--sombra-principal, rgba(107,33,168,.18)),0 2px 8px rgba(0,0,0,.08); }
    .conf-titulo { font-family:var(--fuente-principal,'DM Sans',sans-serif); font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--color-hover,#581C87); background:var(--color-muy-claro,#F3E8FF); border-radius:8px 8px 0 0; padding:.5rem .75rem; margin:-1.5rem -1.5rem 1.1rem; display:flex; align-items:center; gap:.4rem; }
    .form-label { font-size:.82rem; font-weight:700; color:var(--color-hover,#581C87); display:block; margin-bottom:.3rem; }
    .form-input { width:100%; border:1.5px solid var(--color-muy-claro,#e9d5ff); border-radius:8px; padding:.5rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; transition:border-color .15s; }
    .form-input:focus { border-color:var(--color-principal,#6B21A8); }
    .form-select { width:100%; border:1.5px solid var(--color-muy-claro,#e9d5ff); border-radius:8px; padding:.5rem .75rem; font-size:.875rem; color:#1c2b22; background:#fff; outline:none; }
    .form-select:focus { border-color:var(--color-principal,#6B21A8); }
    .form-group { margin-bottom:1rem; }
    .form-error { font-size:.78rem; color:#dc2626; margin-top:.25rem; }
    .dia-check { display:inline-flex; align-items:center; gap:.3rem; margin-right:.75rem; font-size:.84rem; cursor:pointer; }
    .dia-check input[type="checkbox"] { accent-color:var(--color-principal,#6B21A8); width:16px; height:16px; }
    .logo-preview { max-width:160px; max-height:80px; border:1.5px solid var(--color-muy-claro,#e9d5ff); border-radius:8px; object-fit:contain; padding:6px; background:var(--fondo-card-alt,#faf8ff); }
    .toggle-switch { position:relative; display:inline-block; width:42px; height:24px; }
    .toggle-switch input { opacity:0; width:0; height:0; }
    .toggle-slider { position:absolute; cursor:pointer; top:0;left:0;right:0;bottom:0; background:#d1d5db; border-radius:24px; transition:.2s; }
    .toggle-slider:before { position:absolute; content:""; height:18px; width:18px; left:3px; bottom:3px; background:white; border-radius:50%; transition:.2s; }
    .toggle-switch input:checked + .toggle-slider { background:var(--color-principal,#6B21A8); }
    .toggle-switch input:checked + .toggle-slider:before { transform:translateX(18px); }

    /* Selector de temas */
    .tema-option { transition:all 0.2s; }
    .tema-option:hover { transform:translateY(-2px); box-shadow:0 4px 12px var(--sombra-principal,rgba(107,33,168,0.2)); }
    .tema-option.seleccionado { box-shadow:0 4px 16px var(--sombra-principal,rgba(107,33,168,0.3)); }
</style>
@endpush

@section('contenido')

<div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;">
    <div style="width:40px;height:40px;background:var(--gradiente-btn,linear-gradient(135deg,#6B21A8,#7C3AED));border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem;flex-shrink:0;">
        <i class="bi bi-gear-fill"></i>
    </div>
    <div>
        <h4 style="font-family:var(--fuente-titulos,'Playfair Display',serif);font-weight:700;color:#1c2b22;margin:0;">Configuración General</h4>
        <p style="font-size:.82rem;color:#9ca3af;margin:0;">Datos del consultorio, horarios y preferencias del sistema</p>
    </div>
</div>

<form method="POST" action="{{ route('configuracion.update') }}">
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
            @error('nombre_consultorio')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Slogan</label>
            <input type="text" name="slogan" class="form-input"
                   value="{{ old('slogan', $config->slogan) }}" maxlength="255">
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div class="form-group">
            <label class="form-label">NIT / RUT</label>
            <input type="text" name="nit" id="inp-nit" class="form-input"
                   value="{{ old('nit', $config->nit) }}" maxlength="30" placeholder="900123456-7"
                   oninput="filtrarNit(this)">
            <div style="font-size:.75rem;color:#9ca3af;margin-top:.25rem;">Solo números y guion (-).</div>
        </div>
        <div class="form-group">
            <label class="form-label">Registro médico / RETHUS</label>
            <input type="text" name="registro_medico" class="form-input"
                   value="{{ old('registro_medico', $config->registro_medico) }}" maxlength="60">
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
            <div style="font-size:.75rem;color:#9ca3af;margin-top:.25rem;">Solo números.</div>
        </div>
        <div class="form-group">
            <label class="form-label">WhatsApp</label>
            <input type="text" name="telefono_whatsapp" class="form-input"
                   value="{{ old('telefono_whatsapp', $config->telefono_whatsapp) }}" maxlength="30" placeholder="3001234567"
                   oninput="filtrarTel(this)">
            <div style="font-size:.75rem;color:#9ca3af;margin-top:.25rem;">Solo números.</div>
        </div>
        <div class="form-group">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="email" class="form-input"
                   value="{{ old('email', $config->email) }}" maxlength="120" placeholder="consultorio@ejemplo.com">
            @error('email')<div class="form-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:1rem;">
        <div class="form-group">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-input"
                   value="{{ old('direccion', $config->direccion) }}" maxlength="255"
                   oninput="ucwordsInput(this)">
        </div>
        <div class="form-group">
            <label class="form-label">Ciudad</label>
            <input type="text" name="ciudad" class="form-input"
                   value="{{ old('ciudad', $config->ciudad) }}" maxlength="100"
                   oninput="ucwordsInput(this)">
        </div>
        <div class="form-group">
            <label class="form-label">País</label>
            <input type="text" name="pais" class="form-input"
                   value="{{ old('pais', $config->pais) }}" maxlength="80"
                   oninput="ucwordsInput(this)">
        </div>
    </div>
</div>

{{-- ── HORARIOS Y CITAS ───────────────────────────────────── --}}
<div class="conf-card">
    <div class="conf-titulo"><i class="bi bi-clock"></i> Horarios y Citas</div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem;">
        <div class="form-group">
            <label class="form-label">Duración de cita (minutos) <span style="color:#dc2626;">*</span></label>
            <select name="duracion_cita_minutos" class="form-select">
                @foreach([15,20,30,45,60,90,120] as $min)
                <option value="{{ $min }}" {{ old('duracion_cita_minutos', $config->duracion_cita_minutos) == $min ? 'selected' : '' }}>
                    {{ $min }} minutos
                </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Hora apertura <span style="color:#dc2626;">*</span></label>
            <input type="time" name="hora_apertura" class="form-input"
                   value="{{ old('hora_apertura', substr($config->hora_apertura ?? '08:00', 0, 5)) }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">Hora cierre <span style="color:#dc2626;">*</span></label>
            <input type="time" name="hora_cierre" class="form-input"
                   value="{{ old('hora_cierre', substr($config->hora_cierre ?? '18:00', 0, 5)) }}" required>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Días laborales</label>
        <div style="display:flex;flex-wrap:wrap;gap:.25rem;margin-top:.3rem;">
            @php
                $diasLaborales = old('dias_laborales', $config->dias_laborales ?? [1,2,3,4,5]);
                $diasNombres = [1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado',7=>'Domingo'];
            @endphp
            @foreach($diasNombres as $num => $nombre)
            <label class="dia-check">
                <input type="checkbox" name="dias_laborales[]" value="{{ $num }}"
                       {{ in_array($num, $diasLaborales) ? 'checked' : '' }}>
                {{ $nombre }}
            </label>
            @endforeach
        </div>
    </div>
</div>

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
        <span style="font-size:.875rem;color:#374151;font-weight:500;">Activar recordatorios automáticos de citas</span>
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
            'morado-elegante'  => ['nombre'=>'Morado Elegante',  'tipo'=>'Dama',   'c1'=>'#6B21A8','c2'=>'#7C3AED','c3'=>'#F3E8FF','sidebar'=>'#3B0764'],
            'rosa-profesional' => ['nombre'=>'Rosa Profesional', 'tipo'=>'Dama',   'c1'=>'#BE185D','c2'=>'#EC4899','c3'=>'#FCE7F3','sidebar'=>'#831843'],
            'verde-esmeralda'  => ['nombre'=>'Verde Esmeralda',  'tipo'=>'Dama',   'c1'=>'#065F46','c2'=>'#10B981','c3'=>'#D1FAE5','sidebar'=>'#022C22'],
            'azul-marino'      => ['nombre'=>'Azul Marino',      'tipo'=>'Hombre', 'c1'=>'#1E3A5F','c2'=>'#2563EB','c3'=>'#DBEAFE','sidebar'=>'#0F1F3D'],
            'carbon-moderno'   => ['nombre'=>'Carbón Moderno',   'tipo'=>'Hombre', 'c1'=>'#4F46E5','c2'=>'#6366F1','c3'=>'#EEF2FF','sidebar'=>'#111827'],
            'verde-premium'    => ['nombre'=>'Verde Premium',    'tipo'=>'Hombre', 'c1'=>'#14532D','c2'=>'#16A34A','c3'=>'#DCFCE7','sidebar'=>'#052E16'],
        ];
        $temaActual = old('tema', $config->tema ?? 'morado-elegante');
        @endphp

        @foreach($temas as $key => $tema)
        <label style="cursor:pointer;">
            <input type="radio" name="tema" value="{{ $key }}"
                {{ $temaActual === $key ? 'checked' : '' }}
                style="display:none;" class="radio-tema">
            <div class="tema-option {{ $temaActual === $key ? 'seleccionado' : '' }}"
                 style="border-radius:10px; overflow:hidden; border:2px solid {{ $temaActual === $key ? $tema['c1'] : '#e5e5e5' }};">

                {{-- Mini preview --}}
                <div style="display:flex; height:60px;">
                    <div style="width:40px; background:{{ $tema['sidebar'] }}; padding:6px 4px; display:flex; flex-direction:column; gap:3px;">
                        <div style="height:4px; border-radius:2px; background:rgba(255,255,255,0.3);"></div>
                        <div style="height:3px; border-radius:2px; background:rgba(255,255,255,0.6); width:80%;"></div>
                        <div style="height:3px; border-radius:2px; background:rgba(255,255,255,0.2);"></div>
                        <div style="height:3px; border-radius:2px; background:rgba(255,255,255,0.2);"></div>
                    </div>
                    <div style="flex:1; background:{{ $tema['c3'] }}; padding:6px; display:flex; flex-direction:column; gap:3px;">
                        <div style="height:12px; border-radius:3px; background:{{ $tema['c1'] }};"></div>
                        <div style="display:flex; gap:3px;">
                            <div style="flex:1; height:20px; border-radius:3px; background:white; border:1px solid {{ $tema['c3'] }};"></div>
                            <div style="flex:1; height:20px; border-radius:3px; background:white; border:1px solid {{ $tema['c3'] }};"></div>
                        </div>
                    </div>
                </div>

                {{-- Info --}}
                <div style="padding:6px 8px; background:white; border-top:1px solid {{ $tema['c3'] }};">
                    <div style="font-size:0.72rem; font-weight:600; color:{{ $tema['c1'] }};">{{ $tema['nombre'] }}</div>
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
                    'DM Sans'         => 'DM Sans — Moderna y limpia (recomendada)',
                    'Nunito'          => 'Nunito — Suave y amigable',
                    'Inter'           => 'Inter — Técnica y precisa',
                    'Source Sans Pro' => 'Source Sans Pro — Profesional',
                    'Space Grotesk'   => 'Space Grotesk — Contemporánea',
                    'Outfit'          => 'Outfit — Geométrica y elegante',
                ];
                @endphp
                @foreach($fuentesPrincipales as $valor => $label)
                    <option value="{{ $valor }}" {{ old('fuente_principal', $config->fuente_principal ?? 'DM Sans') === $valor ? 'selected' : '' }}>
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
                    'Playfair Display'   => 'Playfair Display — Clásica y elegante (recomendada)',
                    'Cormorant Garamond' => 'Cormorant Garamond — Sofisticada',
                    'Libre Baskerville'  => 'Libre Baskerville — Formal y seria',
                    'Merriweather'       => 'Merriweather — Editorial',
                    'Syne'               => 'Syne — Moderna y atrevida',
                    'Lora'               => 'Lora — Cálida y literaria',
                ];
                @endphp
                @foreach($fuentesTitulos as $valor => $label)
                    <option value="{{ $valor }}" {{ old('fuente_titulos', $config->fuente_titulos ?? 'Playfair Display') === $valor ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Preview de fuentes en tiempo real --}}
    <div id="preview-fuentes" style="margin-top:1rem; padding:1rem; background:var(--fondo-card-alt,#faf8ff); border-radius:8px; border:1px solid var(--fondo-borde,#ede9e0);">
        <div id="preview-titulo" style="font-size:1.2rem; font-weight:600; color:var(--color-principal,#6B21A8); margin-bottom:4px; font-family:'{{ $config->fuente_titulos ?? 'Playfair Display' }}', serif;">
            {{ $config->nombre_consultorio ?? 'Arkevix Dental ERP' }}
        </div>
        <div id="preview-texto" style="font-size:0.875rem; color:#5c6b62; font-family:'{{ $config->fuente_principal ?? 'DM Sans' }}', sans-serif;">
            Sistema de gestión odontológica integral — Historia clínica, citas y pagos
        </div>
    </div>

    {{-- Combinaciones sugeridas --}}
    <div style="margin-top:0.75rem; padding:0.75rem 1rem; background:#fffbf0; border:1px solid #fde68a; border-radius:8px;">
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

    <div id="notif-fuentes" style="display:none; margin-top:.5rem; padding:.5rem .75rem; background:var(--color-muy-claro,#F3E8FF); border-radius:6px; font-size:.78rem; color:var(--color-hover,#581C87);"></div>
</div>

<div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:2rem;">
    <button type="submit" class="btn-morado">
        <i class="bi bi-check-circle"></i> Guardar configuración
    </button>
</div>

</form>

{{-- ── LOGO ────────────────────────────────────────────────── --}}
<div class="conf-card">
    <div class="conf-titulo"><i class="bi bi-image"></i> Logo del Consultorio</div>

    <div style="display:flex;align-items:flex-start;gap:2rem;flex-wrap:wrap;">
        {{-- Preview --}}
        <div style="flex-shrink:0;">
            <div id="logo-preview-wrap">
                @if($config->logo_path)
                <img id="logo-preview-img" src="{{ asset('storage/' . $config->logo_path) }}" class="logo-preview" alt="Logo actual">
                <div style="font-size:.75rem;color:#9ca3af;margin-top:.4rem;">Logo actual</div>
                @else
                <div id="logo-placeholder" style="width:180px;height:90px;border:2px dashed var(--color-claro);border-radius:8px;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--color-acento-activo);gap:.3rem;">
                    <i class="bi bi-image" style="font-size:2rem;"></i>
                    <span style="font-size:.72rem;">Vista previa</span>
                </div>
                <img id="logo-preview-img" src="" class="logo-preview" alt="Preview" style="display:none;max-width:180px;max-height:90px;">
                <div style="font-size:.75rem;color:#9ca3af;margin-top:.4rem;">Sin logo</div>
                @endif
            </div>
        </div>

        {{-- Formulario logo --}}
        <div style="flex:1;min-width:260px;">
            <form method="POST" action="{{ route('configuracion.logo') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Subir nuevo logo</label>
                    <input type="file" name="logo" id="inp-logo" class="form-input" accept="image/jpeg,image/png,image/svg+xml,image/webp" required
                           onchange="previsualizarLogo(this)">
                    @error('logo')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Requisitos del logo --}}
                <div style="background:var(--fondo-card-alt);border:1px solid #e9d5ff;border-radius:8px;padding:.7rem .9rem;font-size:.78rem;margin-bottom:.75rem;">
                    <div style="font-weight:600;color:var(--color-hover);margin-bottom:.4rem;"><i class="bi bi-info-circle"></i> Requisitos del logo:</div>
                    <div style="color:#6b7280;line-height:1.8;">
                        <div><i class="bi bi-check2" style="color:var(--color-principal);"></i> Formato: JPG, PNG, SVG o WebP</div>
                        <div><i class="bi bi-check2" style="color:var(--color-principal);"></i> Tamaño máximo: <strong>2 MB</strong></div>
                        <div><i class="bi bi-check2" style="color:var(--color-principal);"></i> Resolución recomendada: <strong>300 × 150 px</strong> mínimo</div>
                        <div><i class="bi bi-check2" style="color:var(--color-principal);"></i> Fondo transparente (PNG/SVG) para mejor presentación</div>
                        <div><i class="bi bi-check2" style="color:var(--color-principal);"></i> Proporción recomendada: <strong>2:1</strong> (horizontal)</div>
                    </div>
                </div>

                <button type="submit" class="btn-gris">
                    <i class="bi bi-upload"></i> Subir logo
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ── FIRMA DIGITAL DEL DOCTOR ──────────────────────────── --}}
<div class="conf-card">
    <div class="conf-titulo"><i class="bi bi-pen"></i> Firma Digital del Doctor</div>
    <p style="font-size:.85rem;color:var(--color-texto-muted);margin-bottom:1.5rem;">
        Esta firma aparecerá en los documentos PDF (historias clínicas, evoluciones, presupuestos y consentimientos).
    </p>

    <form method="POST" action="{{ route('configuracion.firma') }}" enctype="multipart/form-data" id="form-firma">
        @csrf
        <input type="hidden" name="firma_canvas" id="firma-canvas-data">

        <div style="display:flex;gap:2rem;flex-wrap:wrap;align-items:flex-start;">

            {{-- Columna izquierda: datos del doctor --}}
            <div style="flex:1;min-width:240px;">
                <div class="form-group">
                    <label class="form-label">Nombre completo del doctor</label>
                    <input type="text" name="firma_nombre_doctor" class="form-input"
                           value="{{ old('firma_nombre_doctor', $config->firma_nombre_doctor) }}"
                           placeholder="Ej: Dr. Juan Pérez">
                </div>
                <div class="form-group">
                    <label class="form-label">Cargo / Especialidad</label>
                    <input type="text" name="firma_cargo" class="form-input"
                           value="{{ old('firma_cargo', $config->firma_cargo ?? 'Odontóloga') }}"
                           placeholder="Ej: Odontóloga General">
                </div>
                <div class="form-group">
                    <label class="form-label">Registro médico (RETHUS / TP)</label>
                    <input type="text" name="firma_registro" class="form-input"
                           value="{{ old('firma_registro', $config->firma_registro) }}"
                           placeholder="Ej: TP-12345">
                </div>
            </div>

            {{-- Columna derecha: firma --}}
            <div style="flex:1;min-width:280px;">

                {{-- Tabs --}}
                <div style="display:flex;gap:.5rem;margin-bottom:1rem;">
                    <button type="button" id="tab-dibujar" onclick="cambiarTab('dibujar')"
                        style="padding:.4rem 1rem;border-radius:6px;border:1.5px solid var(--color-principal);background:var(--color-principal);color:#fff;font-size:.82rem;cursor:pointer;font-weight:500;">
                        <i class="bi bi-pencil"></i> Dibujar firma
                    </button>
                    <button type="button" id="tab-subir" onclick="cambiarTab('subir')"
                        style="padding:.4rem 1rem;border-radius:6px;border:1.5px solid #d1d5db;background:#fff;color:#6b7280;font-size:.82rem;cursor:pointer;">
                        <i class="bi bi-upload"></i> Subir imagen
                    </button>
                </div>

                {{-- Panel: dibujar --}}
                <div id="panel-dibujar">
                    <div style="position:relative;border:2px solid #d1d5db;border-radius:8px;background:#fafafa;overflow:hidden;">
                        <canvas id="firma-canvas" width="400" height="130"
                            style="display:block;width:100%;cursor:crosshair;touch-action:none;"></canvas>
                        <div id="canvas-hint" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:.78rem;color:#c0b8d0;pointer-events:none;text-align:center;">
                            <i class="bi bi-pencil-square" style="font-size:1.5rem;display:block;margin-bottom:.3rem;"></i>
                            Dibuja tu firma aquí
                        </div>
                    </div>
                    <div style="display:flex;gap:.5rem;margin-top:.6rem;">
                        <button type="button" onclick="limpiarCanvas()"
                            style="padding:.35rem .9rem;border-radius:6px;border:1px solid #d1d5db;background:#fff;color:#6b7280;font-size:.8rem;cursor:pointer;">
                            <i class="bi bi-trash"></i> Limpiar
                        </button>
                        <span style="font-size:.75rem;color:#9ca3af;align-self:center;">PNG transparente · 400×130 px</span>
                    </div>
                </div>

                {{-- Panel: subir --}}
                <div id="panel-subir" style="display:none;">
                    <input type="file" name="firma_imagen" id="inp-firma" class="form-input"
                           accept="image/png,image/jpg,image/jpeg" onchange="previsualizarFirma(this)">
                    <div style="font-size:.75rem;color:#9ca3af;margin-top:.4rem;">
                        <i class="bi bi-info-circle"></i> PNG con fondo transparente recomendado · Máx. 2 MB
                    </div>
                </div>
            </div>
        </div>

        {{-- Preview firma actual --}}
        @if($config->firma_path)
        <div style="margin-top:1.25rem;padding:1rem;background:var(--fondo-card-alt);border-radius:8px;border:1px solid #e9d5ff;">
            <div style="font-size:.78rem;font-weight:600;color:var(--color-principal);margin-bottom:.6rem;">
                <i class="bi bi-check-circle-fill"></i> Firma guardada actualmente:
            </div>
            <div style="display:flex;align-items:flex-end;gap:2rem;flex-wrap:wrap;">
                <div style="background:#fff;padding:.75rem 1.25rem;border:1px solid #e0d0f0;border-radius:6px;text-align:center;">
                    <img src="{{ asset('storage/' . $config->firma_path) }}"
                         alt="Firma del doctor"
                         style="max-height:80px;max-width:220px;display:block;margin:0 auto .5rem;">
                    <div style="border-top:1px solid #333;padding-top:4px;font-size:.75rem;color:#555;">
                        {{ $config->firma_nombre_doctor ?? auth()->user()->name }}<br>
                        {{ $config->firma_cargo ?? 'Odontóloga' }}
                        @if($config->firma_registro)· Reg. {{ $config->firma_registro }}@endif
                    </div>
                </div>
                <form method="POST" action="{{ route('configuracion.firma.eliminar') }}" style="align-self:center;"
                      onsubmit="return confirm('¿Eliminar la firma guardada?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        style="padding:.4rem .9rem;border-radius:6px;border:1px solid #fca5a5;background:#fff5f5;color:#dc2626;font-size:.8rem;cursor:pointer;">
                        <i class="bi bi-trash"></i> Eliminar firma
                    </button>
                </form>
            </div>
        </div>
        @endif

        <div style="margin-top:1.25rem;">
            <button type="submit" class="btn-gris" id="btn-guardar-firma">
                <i class="bi bi-floppy"></i> Guardar firma digital
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
// ── Preview de fuentes en tiempo real ──
document.querySelector('[name="fuente_principal"]')?.addEventListener('change', function() {
    const fuente = this.value;
    document.getElementById('preview-texto').style.fontFamily = `'${fuente}', sans-serif`;
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = `https://fonts.googleapis.com/css2?family=${fuente.replace(/ /g, '+')}:wght@300;400;500&display=swap`;
    document.head.appendChild(link);
});

document.querySelector('[name="fuente_titulos"]')?.addEventListener('change', function() {
    const fuente = this.value;
    document.getElementById('preview-titulo').style.fontFamily = `'${fuente}', serif`;
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = `https://fonts.googleapis.com/css2?family=${fuente.replace(/ /g, '+')}:wght@400;600&display=swap`;
    document.head.appendChild(link);
});

// ── Al seleccionar tema, sugerir fuentes automáticamente ──
const fuentesSugeridas = {
    'morado-elegante':  { principal: 'DM Sans',        titulos: 'Playfair Display' },
    'rosa-profesional': { principal: 'Nunito',          titulos: 'Cormorant Garamond' },
    'verde-esmeralda':  { principal: 'Inter',           titulos: 'Libre Baskerville' },
    'azul-marino':      { principal: 'Source Sans Pro', titulos: 'Merriweather' },
    'carbon-moderno':   { principal: 'Space Grotesk',   titulos: 'Syne' },
    'verde-premium':    { principal: 'Outfit',          titulos: 'Lora' },
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
            const selectTitulos   = document.querySelector('[name="fuente_titulos"]');
            if (selectPrincipal) selectPrincipal.value = sugeridas.principal;
            if (selectTitulos)   selectTitulos.value   = sugeridas.titulos;

            document.getElementById('preview-texto').style.fontFamily  = `'${sugeridas.principal}', sans-serif`;
            document.getElementById('preview-titulo').style.fontFamily = `'${sugeridas.titulos}', serif`;

            const notif = document.getElementById('notif-fuentes');
            if (notif) {
                notif.textContent = `Fuentes actualizadas a las recomendadas para ${tema.replace(/-/g, ' ')}`;
                notif.style.display = 'block';
                setTimeout(() => notif.style.display = 'none', 3000);
            }
        }
    });
});

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

function previsualizarFirma(input) {
    if (!input.files || !input.files[0]) return;
    // no extra preview needed, the form shows current saved firma
}

// ── Tabs firma ──
function cambiarTab(tab) {
    const btnDibujar = document.getElementById('tab-dibujar');
    const btnSubir   = document.getElementById('tab-subir');
    const panelD     = document.getElementById('panel-dibujar');
    const panelS     = document.getElementById('panel-subir');
    const activeStyle  = 'padding:.4rem 1rem;border-radius:6px;border:1.5px solid var(--color-principal);background:var(--color-principal);color:#fff;font-size:.82rem;cursor:pointer;font-weight:500;';
    const inactiveStyle = 'padding:.4rem 1rem;border-radius:6px;border:1.5px solid #d1d5db;background:#fff;color:#6b7280;font-size:.82rem;cursor:pointer;';
    if (tab === 'dibujar') {
        btnDibujar.style.cssText = activeStyle;
        btnSubir.style.cssText   = inactiveStyle;
        panelD.style.display = '';
        panelS.style.display = 'none';
        document.querySelector('[name="firma_imagen"]').value = '';
    } else {
        btnSubir.style.cssText   = activeStyle;
        btnDibujar.style.cssText = inactiveStyle;
        panelS.style.display = '';
        panelD.style.display = 'none';
        document.getElementById('firma-canvas-data').value = '';
    }
}

// ── Canvas de firma ──
(function() {
    const canvas  = document.getElementById('firma-canvas');
    if (!canvas) return;
    const ctx     = canvas.getContext('2d');
    const hint    = document.getElementById('canvas-hint');
    let dibujando = false;
    let hayTrazo  = false;

    // Escalar canvas al tamaño visual
    function escalar() {
        const rect = canvas.getBoundingClientRect();
        canvas.width  = rect.width  * window.devicePixelRatio;
        canvas.height = rect.height * window.devicePixelRatio;
        ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
        ctx.strokeStyle = '#1a1a2e';
        ctx.lineWidth   = 2.2;
        ctx.lineCap     = 'round';
        ctx.lineJoin    = 'round';
    }
    escalar();

    function getPosicion(e) {
        const rect = canvas.getBoundingClientRect();
        const src  = e.touches ? e.touches[0] : e;
        return { x: src.clientX - rect.left, y: src.clientY - rect.top };
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

    canvas.addEventListener('mousedown',  iniciar);
    canvas.addEventListener('mousemove',  dibujar);
    canvas.addEventListener('mouseup',    terminar);
    canvas.addEventListener('mouseleave', terminar);
    canvas.addEventListener('touchstart', iniciar,  { passive: false });
    canvas.addEventListener('touchmove',  dibujar,  { passive: false });
    canvas.addEventListener('touchend',   terminar, { passive: false });

    window.limpiarCanvas = function() {
        const rect = canvas.getBoundingClientRect();
        ctx.clearRect(0, 0, rect.width, rect.height);
        hayTrazo = false;
        if (hint) hint.style.display = '';
        document.getElementById('firma-canvas-data').value = '';
    };

    // Al enviar el form, capturar canvas si hay trazo
    document.getElementById('form-firma')?.addEventListener('submit', function() {
        if (hayTrazo && document.getElementById('panel-dibujar').style.display !== 'none') {
            document.getElementById('firma-canvas-data').value = canvas.toDataURL('image/png');
        }
    });
})();
</script>
@endpush
