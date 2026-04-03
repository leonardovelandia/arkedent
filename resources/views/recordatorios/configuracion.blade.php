@extends('layouts.app')
@section('titulo', 'Configuración de Recordatorios')

@push('estilos')
<style>
    .conf-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; padding:1.5rem; margin-bottom:1.25rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.12); }
    .conf-titulo { font-size:.95rem; font-weight:700; color:var(--color-hover); margin-bottom:1.25rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid var(--fondo-borde); padding-bottom:.75rem; }
    .form-label { font-size:.8rem; font-weight:600; color:#5c6b62; display:block; margin-bottom:.35rem; }
    .form-input { width:100%; border:1px solid var(--fondo-borde); border-radius:8px; padding:.5rem .75rem; font-size:.875rem; background:var(--fondo-app); color:var(--color-texto); transition:border-color .15s; }
    .form-input:focus { outline:none; border-color:var(--color-principal); }
    .form-error { font-size:.75rem; color:#dc2626; margin-top:.2rem; }
    .toggle-wrap { display:flex; align-items:center; gap:.75rem; }
    .toggle { position:relative; display:inline-block; width:44px; height:24px; }
    .toggle input { opacity:0; width:0; height:0; }
    .toggle-slider { position:absolute; cursor:pointer; inset:0; background:#d1d5db; border-radius:50px; transition:.3s; }
    .toggle-slider::before { content:''; position:absolute; height:18px; width:18px; left:3px; bottom:3px; background:white; border-radius:50%; transition:.3s; }
    .toggle input:checked + .toggle-slider { background:var(--color-principal); }
    .toggle input:checked + .toggle-slider::before { transform:translateX(20px); }
    .variables-box { background:var(--fondo-card-alt); border:1px solid var(--fondo-borde); border-radius:8px; padding:.75rem 1rem; font-size:.78rem; }
    .var-badge { display:inline-block; background:var(--color-muy-claro); color:var(--color-principal); font-size:.72rem; padding:.15rem .55rem; border-radius:4px; cursor:pointer; margin:.15rem; font-family:monospace; border:1px solid var(--color-claro); transition:background .12s; }
    .var-badge:hover { background:var(--color-claro); color:#fff; }
    .btn-morado { background:linear-gradient(135deg,var(--color-principal),var(--color-claro)); color:#fff; border:none; border-radius:8px; padding:.55rem 1.25rem; font-size:.875rem; font-weight:600; display:inline-flex; align-items:center; gap:.4rem; cursor:pointer; transition:filter .15s; }
    .btn-morado:hover { filter:brightness(1.1); }
    .btn-gris { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; cursor:pointer; }
    .btn-gris:hover { background:#e5e7eb; }
    .btn-outline { background:transparent; color:var(--color-principal); border:1.5px solid var(--color-principal); border-radius:8px; padding:.5rem 1.25rem; font-size:.875rem; font-weight:500; display:inline-flex; align-items:center; gap:.4rem; cursor:pointer; transition:background .15s; }
    .btn-outline:hover { background:var(--color-muy-claro); }
    .preview-box { background:#fff; border:1px solid #e0d5f0; border-radius:8px; padding:1rem; font-size:.82rem; line-height:1.6; min-height:80px; color:#374151; white-space:pre-wrap; }
    textarea.form-input { resize:vertical; font-family:monospace; font-size:.82rem; }
    .wa-tab { background:transparent; border:none; padding:.35rem .75rem; font-size:.78rem; font-weight:600; color:#8fa39a; cursor:pointer; border-radius:6px 6px 0 0; transition:background .15s,color .15s; }
    .wa-tab:hover { background:var(--color-muy-claro); color:var(--color-hover); }
    .wa-tab.wa-tab-activo { background:var(--color-muy-claro); color:var(--color-principal); border-bottom:2px solid var(--color-principal); }
    #modo-simple-label:has(input:checked),
    #modo-interactivo-label:has(input:checked) { border-color:var(--color-principal); background:var(--color-muy-claro); }
</style>
@endpush

@section('contenido')

@if(session('exito'))
<div class="alerta-flash" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
    <i class="bi bi-check-circle-fill"></i> {{ session('exito') }}
</div>
@endif
@if(session('error'))
<div class="alerta-flash" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;">
    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
</div>
@endif

{{-- Encabezado --}}
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.75rem;margin-bottom:1.5rem;">
    <div>
        <h2 style="font-family:var(--fuente-titulos);font-size:1.3rem;font-weight:600;color:var(--color-hover);margin:0;">
            <i class="bi bi-gear-fill" style="color:var(--color-principal);"></i> Configuración de Recordatorios
        </h2>
    </div>
    <a href="{{ route('recordatorios.index') }}" class="btn-gris">
        <i class="bi bi-arrow-left"></i> Volver a recordatorios
    </a>
</div>

<form method="POST" action="{{ route('recordatorios.guardar-configuracion') }}" id="form-config">
@csrf

{{-- Sección 1: Canales --}}
<div class="conf-card">
    <div class="conf-titulo"><i class="bi bi-toggles"></i> Activar canales de recordatorio</div>

    <div style="display:grid;gap:1.25rem;">
        <div class="toggle-wrap">
            <label class="toggle">
                <input type="checkbox" name="recordatorios_activos" value="1"
                    {{ $config->recordatorios_activos ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
            <div>
                <div style="font-size:.9rem;font-weight:600;color:var(--color-hover);">Sistema de recordatorios activo</div>
                <div style="font-size:.78rem;color:#8fa39a;">Habilita o deshabilita todos los recordatorios automáticos</div>
            </div>
        </div>

        <div class="toggle-wrap">
            <label class="toggle">
                <input type="checkbox" name="recordatorios_email_activo" value="1"
                    {{ $config->recordatorios_email_activo ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
            <div>
                <div style="font-size:.9rem;font-weight:500;color:var(--color-hover);">
                    <i class="bi bi-envelope-fill" style="color:#1d4ed8;"></i> Recordatorios por Email
                </div>
                <div style="font-size:.78rem;color:#8fa39a;">Envía correos a pacientes con email registrado y autorización</div>
            </div>
        </div>

        <div class="toggle-wrap">
            <label class="toggle">
                <input type="checkbox" name="recordatorios_whatsapp_activo" value="1"
                    {{ $config->recordatorios_whatsapp_activo ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
            <div>
                <div style="font-size:.9rem;font-weight:500;color:var(--color-hover);">
                    <i class="bi bi-whatsapp" style="color:#15803d;"></i> Recordatorios por WhatsApp
                </div>
                <div style="font-size:.78rem;color:#8fa39a;">Envía mensajes WhatsApp vía Ultramsg a pacientes con teléfono registrado</div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;max-width:520px;">
            <div>
                <label class="form-label">Anticipación del recordatorio</label>
                <select name="horas_anticipacion" class="form-input">
                    <option value="1" {{ ($config->horas_anticipacion ?? 1) == 1 ? 'selected' : '' }}>Un día antes</option>
                    <option value="2" {{ ($config->horas_anticipacion ?? 1) == 2 ? 'selected' : '' }}>Dos días antes</option>
                    <option value="3" {{ ($config->horas_anticipacion ?? 1) == 3 ? 'selected' : '' }}>Tres días antes</option>
                </select>
                <div style="font-size:.72rem;color:#8fa39a;margin-top:.25rem;">Días antes de la cita para enviar el aviso</div>
            </div>
            <div>
                <label class="form-label">Hora de envío automático</label>
                <div class="timepicker-wrap">
                    <i class="bi bi-clock timepicker-icon"></i>
                    <input type="text" name="hora_envio_recordatorio" placeholder="HH:MM"
                           class="form-input timepicker"
                           value="{{ old('hora_envio_recordatorio', $config->hora_envio_recordatorio ?? '12:00') }}" autocomplete="off" readonly>
                </div>
                <div style="font-size:.72rem;color:#8fa39a;margin-top:.25rem;">Hora a la que se envían los recordatorios automáticos</div>
            </div>
        </div>
    </div>
</div>

{{-- Sección 2: Email --}}
<div class="conf-card">
    <div class="conf-titulo"><i class="bi bi-envelope-fill"></i> Configuración de Email</div>

    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:.875rem 1rem;font-size:.8rem;color:#166534;margin-bottom:1rem;">
        <strong><i class="bi bi-check-circle-fill"></i> SMTP configurado vía SendGrid:</strong>
        <code style="display:block;margin-top:.35rem;font-size:.78rem;color:#15803d;">
            MAIL_HOST=smtp.sendgrid.net · MAIL_PORT=587 · MAIL_ENCRYPTION=tls<br>
            MAIL_USERNAME=apikey · MAIL_FROM_ADDRESS={{ env('MAIL_FROM_ADDRESS', 'no-reply@arkedent.com') }}
        </code>
        <div style="margin-top:.4rem;color:#166534;">
            El servidor SMTP se configura en <code>.env</code>. El correo del remitente debe usar el dominio autenticado en SendGrid (<strong>arkedent.com</strong>).
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
        <div>
            <label class="form-label">Nombre del remitente</label>
            <input type="text" name="mail_from_name" class="form-input"
                value="{{ old('mail_from_name', $config->mail_from_name ?? $config->nombre_consultorio) }}"
                placeholder="{{ $config->nombre_consultorio }}">
            <div style="font-size:.72rem;color:#8fa39a;margin-top:.25rem;">Si está vacío usa el nombre del consultorio</div>
        </div>
        <div>
            <label class="form-label">Correo del remitente</label>
            <input type="email" name="mail_from_address" class="form-input"
                value="{{ old('mail_from_address', $config->mail_from_address ?? env('MAIL_FROM_ADDRESS')) }}"
                placeholder="{{ env('MAIL_FROM_ADDRESS', 'no-reply@arkedent.com') }}">
            <div style="font-size:.72rem;color:#8fa39a;margin-top:.25rem;">Debe ser un correo del dominio autenticado en SendGrid</div>
        </div>
    </div>

    {{-- Prueba de email --}}
    <div style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;">
        <div>
            <label class="form-label">Email de prueba</label>
            <input type="email" name="email_prueba" form="form-probar-email" class="form-input" style="min-width:240px;"
                placeholder="correo@ejemplo.com" required>
        </div>
        <button type="submit" form="form-probar-email" class="btn-outline"><i class="bi bi-send"></i> Enviar email de prueba</button>
    </div>
</div>

{{-- Sección 3: WhatsApp --}}
<div class="conf-card">
    <div class="conf-titulo"><i class="bi bi-whatsapp"></i> Configuración WhatsApp</div>

    {{-- Selector de proveedor --}}
    <div style="margin-bottom:1.25rem;">
        <label class="form-label">Proveedor de WhatsApp</label>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;padding:.55rem 1rem;border:1.5px solid var(--fondo-borde);border-radius:8px;font-size:.875rem;"
                   id="label-ultramsg">
                <input type="radio" name="whatsapp_provider" value="ultramsg"
                    {{ ($config->whatsapp_provider ?? 'ultramsg') === 'ultramsg' ? 'checked' : '' }}
                    onchange="mostrarProveedor('ultramsg')">
                <strong>UltraMsg</strong> <span style="color:#8fa39a;font-size:.78rem;">— desde $15 USD/mes</span>
            </label>
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;padding:.55rem 1rem;border:1.5px solid var(--fondo-borde);border-radius:8px;font-size:.875rem;"
                   id="label-twilio">
                <input type="radio" name="whatsapp_provider" value="twilio"
                    {{ ($config->whatsapp_provider ?? 'ultramsg') === 'twilio' ? 'checked' : '' }}
                    onchange="mostrarProveedor('twilio')">
                <strong>Twilio</strong> <span style="color:#8fa39a;font-size:.78rem;">— Sandbox gratuito disponible</span>
            </label>
        </div>
    </div>

    {{-- Campos UltraMsg --}}
    <div id="campos-ultramsg" style="display:{{ ($config->whatsapp_provider ?? 'ultramsg') === 'ultramsg' ? 'block' : 'none' }}">
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:.75rem 1rem;font-size:.8rem;color:#166534;margin-bottom:1rem;">
            <strong><i class="bi bi-info-circle"></i> UltraMsg:</strong> Crea una instancia en ultramsg.com, escanea el QR con tu WhatsApp Business y copia el Instance ID y Token.
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div>
                <label class="form-label">Instance ID</label>
                <input type="text" name="ultramsg_instance" class="form-input"
                    value="{{ old('ultramsg_instance', $config->ultramsg_instance) }}"
                    placeholder="Ej: instance12345">
            </div>
            <div>
                <label class="form-label">Token</label>
                <input type="text" name="ultramsg_token" class="form-input"
                    value="{{ old('ultramsg_token', $config->ultramsg_token) }}"
                    placeholder="Token de acceso">
            </div>
        </div>
    </div>

    {{-- Campos Twilio --}}
    <div id="campos-twilio" style="display:{{ ($config->whatsapp_provider ?? 'ultramsg') === 'twilio' ? 'block' : 'none' }}">
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:.75rem 1rem;font-size:.8rem;color:#1e40af;margin-bottom:1rem;">
            <strong><i class="bi bi-info-circle"></i> Twilio Sandbox (gratuito):</strong><br>
            1. Ve a <strong>Twilio Console → Messaging → WhatsApp Sandbox</strong><br>
            2. Cada destinatario debe enviar <code>join &lt;tu-código&gt;</code> al número del sandbox una sola vez<br>
            3. El número "From" del sandbox es <code>whatsapp:+14155238886</code> (o el que aparece en tu consola)<br>
            4. En producción usa tu número aprobado de Twilio
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:.75rem;">
            <div>
                <label class="form-label">Account SID</label>
                <input type="text" name="twilio_account_sid" class="form-input"
                    value="{{ old('twilio_account_sid', $config->twilio_account_sid) }}"
                    placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
            </div>
            <div>
                <label class="form-label">Auth Token</label>
                <input type="password" name="twilio_auth_token" class="form-input"
                    value="{{ old('twilio_auth_token', $config->twilio_auth_token) }}"
                    placeholder="Tu Auth Token de Twilio">
            </div>
        </div>
        <div style="max-width:340px;">
            <label class="form-label">Número WhatsApp "From" (con código de país)</label>
            <input type="text" name="twilio_whatsapp_from" class="form-input"
                value="{{ old('twilio_whatsapp_from', $config->twilio_whatsapp_from) }}"
                placeholder="+14155238886">
            <div style="font-size:.72rem;color:#8fa39a;margin-top:.25rem;">Sandbox: <code>+14155238886</code> · Sin el prefijo <code>whatsapp:</code></div>
        </div>
    </div>

    {{-- Prueba de WhatsApp --}}
    <div style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--fondo-borde);">
        <div>
            <label class="form-label">Teléfono de prueba (Colombia, 10 dígitos)</label>
            <input type="text" name="telefono_prueba" form="form-probar-whatsapp" class="form-input" style="min-width:200px;"
                placeholder="3001234567" required>
        </div>
        <button type="submit" form="form-probar-whatsapp" class="btn-outline"><i class="bi bi-whatsapp"></i> Enviar WhatsApp de prueba</button>
    </div>
</div>

{{-- Sección 4: Plantillas --}}
<div class="conf-card">
    <div class="conf-titulo"><i class="bi bi-file-text"></i> Plantillas de mensajes</div>

    {{-- Modo recordatorio WhatsApp --}}
    <div style="margin-bottom:1.5rem;padding-bottom:1.25rem;border-bottom:1px solid var(--fondo-borde);">
        <label class="form-label" style="margin-bottom:.6rem;">Modo de recordatorio WhatsApp</label>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
            <label style="display:flex;align-items:flex-start;gap:.6rem;cursor:pointer;padding:.75rem 1rem;border:1.5px solid var(--fondo-borde);border-radius:8px;flex:1;min-width:220px;transition:border-color .15s;" id="modo-simple-label">
                <input type="radio" name="modo_recordatorio" value="simple"
                    {{ ($config->modo_recordatorio ?? 'simple') === 'simple' ? 'checked' : '' }}
                    onchange="cambiarModoRecordatorio('simple')">
                <div>
                    <div style="font-size:.875rem;font-weight:600;color:var(--color-hover);">
                        <i class="bi bi-bell"></i> Solo recordatorio
                    </div>
                    <div style="font-size:.75rem;color:#8fa39a;margin-top:.2rem;">
                        Envía el recordatorio sin opciones de respuesta
                    </div>
                </div>
            </label>
            <label style="display:flex;align-items:flex-start;gap:.6rem;cursor:pointer;padding:.75rem 1rem;border:1.5px solid var(--fondo-borde);border-radius:8px;flex:1;min-width:220px;transition:border-color .15s;" id="modo-interactivo-label">
                <input type="radio" name="modo_recordatorio" value="interactivo"
                    {{ ($config->modo_recordatorio ?? 'simple') === 'interactivo' ? 'checked' : '' }}
                    onchange="cambiarModoRecordatorio('interactivo')">
                <div>
                    <div style="font-size:.875rem;font-weight:600;color:var(--color-hover);">
                        <i class="bi bi-chat-dots"></i> Con confirmación / cancelación
                    </div>
                    <div style="font-size:.75rem;color:#8fa39a;margin-top:.2rem;">
                        El paciente responde <strong>1</strong> confirmar · <strong>2</strong> reprogramar · <strong>3</strong> cancelar
                    </div>
                </div>
            </label>
        </div>
        <div id="info-webhook" style="display:{{ ($config->modo_recordatorio ?? 'simple') === 'interactivo' ? 'block' : 'none' }};margin-top:.75rem;">
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:.75rem 1rem;font-size:.8rem;color:#1e40af;">
                <strong><i class="bi bi-info-circle"></i> Webhook requerido:</strong>
                Configura en tu proveedor (UltraMsg/Twilio) la URL de respuestas entrantes:
                <code style="display:block;margin-top:.35rem;background:#dbeafe;padding:.3rem .6rem;border-radius:4px;font-size:.78rem;word-break:break-all;">
                    {{ url('/webhook/whatsapp') }}
                </code>
            </div>
        </div>
    </div>

    {{-- Variables disponibles --}}
    <div class="variables-box" style="margin-bottom:1.25rem;">
        <div style="font-size:.78rem;font-weight:600;color:#5c6b62;margin-bottom:.5rem;">
            Variables disponibles (clic para insertar en la plantilla activa):
        </div>
        @foreach(['{{nombre_paciente}}','{{fecha_cita}}','{{hora_cita}}','{{procedimiento}}','{{nombre_consultorio}}','{{direccion_consultorio}}','{{telefono_consultorio}}'] as $var)
        <span class="var-badge" onclick="insertarVariable('{{ $var }}')" title="{{ $var }}">{{ $var }}</span>
        @endforeach
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

        {{-- ── Email ──────────────────────────────────────────── --}}
        <div>
            <label class="form-label">
                <i class="bi bi-envelope-fill" style="color:#1d4ed8;"></i> Plantilla Email (HTML básico)
            </label>
            <textarea name="plantilla_email" id="plantilla-email" rows="10" class="form-input"
                onfocus="plantillaActiva='email'"
                placeholder="HTML del correo. Usa las variables de arriba.">{{ old('plantilla_email', $config->plantilla_email) }}</textarea>
            <div style="margin-top:.5rem;">
                <div style="font-size:.72rem;color:#8fa39a;margin-bottom:.35rem;">Vista previa:</div>
                <div class="preview-box" id="preview-email">
                    {{ $config->plantilla_email ? strip_tags($config->plantilla_email) : 'Escribe la plantilla para ver el preview.' }}
                </div>
            </div>
        </div>

        {{-- ── WhatsApp ─────────────────────────────────────────── --}}
        <div>
            <label class="form-label">
                <i class="bi bi-whatsapp" style="color:#15803d;"></i> Plantillas WhatsApp
            </label>

            {{-- Modo SIMPLE: una sola plantilla --}}
            <div id="wa-modo-simple" style="display:{{ ($config->modo_recordatorio ?? 'simple') === 'simple' ? 'block' : 'none' }};">
                <div style="font-size:.75rem;color:#5c6b62;margin-bottom:.35rem;font-weight:600;">🔔 Recordatorio</div>
                <textarea name="plantilla_whatsapp" id="plantilla-whatsapp" rows="10" class="form-input"
                    onfocus="plantillaActiva='whatsapp'"
                    placeholder="Mensaje de recordatorio. Usa *negrita* y las variables.">{{ old('plantilla_whatsapp', $config->plantilla_whatsapp) }}</textarea>
                <div style="margin-top:.5rem;">
                    <div style="font-size:.72rem;color:#8fa39a;margin-bottom:.35rem;">Vista previa:</div>
                    <div class="preview-box" id="preview-whatsapp" style="white-space:pre-wrap;">{{ old('plantilla_whatsapp', $config->plantilla_whatsapp) ?? 'Escribe la plantilla para ver el preview.' }}</div>
                </div>
            </div>

            {{-- Modo INTERACTIVO: 4 plantillas con tabs --}}
            <div id="wa-modo-interactivo" style="display:{{ ($config->modo_recordatorio ?? 'simple') === 'interactivo' ? 'block' : 'none' }};">
                {{-- Tabs --}}
                <div style="display:flex;gap:.25rem;flex-wrap:wrap;margin-bottom:.75rem;border-bottom:2px solid var(--fondo-borde);padding-bottom:.5rem;" id="wa-tabs">
                    <button type="button" class="wa-tab wa-tab-activo" id="tab-btn-recordatorio-int" onclick="mostrarTabWA('recordatorio-int')">
                        🔔 Recordatorio
                    </button>
                    <button type="button" class="wa-tab" id="tab-btn-confirmacion" onclick="mostrarTabWA('confirmacion')">
                        ✅ Confirmación
                    </button>
                    <button type="button" class="wa-tab" id="tab-btn-reprogramacion" onclick="mostrarTabWA('reprogramacion')">
                        🔁 Reprogramación
                    </button>
                    <button type="button" class="wa-tab" id="tab-btn-cancelacion" onclick="mostrarTabWA('cancelacion')">
                        ❌ Cancelación
                    </button>
                </div>

                {{-- Tab: Recordatorio interactivo --}}
                <div class="wa-tab-panel" id="panel-recordatorio-int">
                    <div style="font-size:.73rem;color:#8fa39a;margin-bottom:.4rem;">
                        Mensaje inicial enviado al paciente. Debe incluir las opciones 1 / 2 / 3.
                    </div>
                    <textarea name="plantilla_interactiva_whatsapp" id="plantilla-interactiva-whatsapp" rows="9" class="form-input"
                        onfocus="plantillaActiva='interactiva-whatsapp'"
                        placeholder="Recordatorio con opciones de respuesta…">{{ old('plantilla_interactiva_whatsapp', $config->plantilla_interactiva_whatsapp) }}</textarea>
                    <div class="preview-box" id="preview-interactiva-whatsapp" style="margin-top:.4rem;white-space:pre-wrap;font-size:.78rem;">{{ old('plantilla_interactiva_whatsapp', $config->plantilla_interactiva_whatsapp) ?? 'Vista previa aquí.' }}</div>
                </div>

                {{-- Tab: Confirmación --}}
                <div class="wa-tab-panel" id="panel-confirmacion" style="display:none;">
                    <div style="font-size:.73rem;color:#8fa39a;margin-bottom:.4rem;">
                        Auto-enviado cuando el paciente responde <strong>1</strong>. También marca la cita como <em>Confirmada</em>.
                    </div>
                    <textarea name="plantilla_confirmacion_whatsapp" id="plantilla-confirmacion-whatsapp" rows="9" class="form-input"
                        onfocus="plantillaActiva='confirmacion-whatsapp'"
                        placeholder="Mensaje de confirmación…">{{ old('plantilla_confirmacion_whatsapp', $config->plantilla_confirmacion_whatsapp) }}</textarea>
                    <div class="preview-box" id="preview-confirmacion-whatsapp" style="margin-top:.4rem;white-space:pre-wrap;font-size:.78rem;">{{ old('plantilla_confirmacion_whatsapp', $config->plantilla_confirmacion_whatsapp) ?? 'Vista previa aquí.' }}</div>
                </div>

                {{-- Tab: Reprogramación --}}
                <div class="wa-tab-panel" id="panel-reprogramacion" style="display:none;">
                    <div style="font-size:.73rem;color:#8fa39a;margin-bottom:.4rem;">
                        Auto-enviado cuando el paciente responde <strong>2</strong>. El consultorio debe contactar al paciente para coordinar la nueva fecha.
                    </div>
                    <textarea name="plantilla_reprogramacion_whatsapp" id="plantilla-reprogramacion-whatsapp" rows="9" class="form-input"
                        onfocus="plantillaActiva='reprogramacion-whatsapp'"
                        placeholder="Mensaje de reprogramación…">{{ old('plantilla_reprogramacion_whatsapp', $config->plantilla_reprogramacion_whatsapp) }}</textarea>
                    <div class="preview-box" id="preview-reprogramacion-whatsapp" style="margin-top:.4rem;white-space:pre-wrap;font-size:.78rem;">{{ old('plantilla_reprogramacion_whatsapp', $config->plantilla_reprogramacion_whatsapp) ?? 'Vista previa aquí.' }}</div>
                </div>

                {{-- Tab: Cancelación --}}
                <div class="wa-tab-panel" id="panel-cancelacion" style="display:none;">
                    <div style="font-size:.73rem;color:#8fa39a;margin-bottom:.4rem;">
                        Auto-enviado cuando el paciente responde <strong>3</strong>. También cancela la cita automáticamente en el sistema.
                    </div>
                    <textarea name="plantilla_cancelacion_whatsapp" id="plantilla-cancelacion-whatsapp" rows="9" class="form-input"
                        onfocus="plantillaActiva='cancelacion-whatsapp'"
                        placeholder="Mensaje de cancelación…">{{ old('plantilla_cancelacion_whatsapp', $config->plantilla_cancelacion_whatsapp) }}</textarea>
                    <div class="preview-box" id="preview-cancelacion-whatsapp" style="margin-top:.4rem;white-space:pre-wrap;font-size:.78rem;">{{ old('plantilla_cancelacion_whatsapp', $config->plantilla_cancelacion_whatsapp) ?? 'Vista previa aquí.' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Guardar --}}
<div style="display:flex;gap:.75rem;justify-content:flex-end;">
    <button type="submit" class="btn-morado">
        <i class="bi bi-floppy"></i> Guardar configuración
    </button>
</div>

</form>

{{-- Forms de prueba fuera del form principal para evitar anidamiento --}}
<form id="form-probar-email" method="POST" action="{{ route('recordatorios.probar-email') }}">@csrf</form>
<form id="form-probar-whatsapp" method="POST" action="{{ route('recordatorios.probar-whatsapp') }}">@csrf</form>

@endsection

@push('scripts')
<script>
let plantillaActiva = 'email';

// Mapa: clave → id del textarea → id del preview
const _taMap = {
    'email'                  : ['plantilla-email',                 'preview-email'],
    'whatsapp'               : ['plantilla-whatsapp',              'preview-whatsapp'],
    'interactiva-whatsapp'   : ['plantilla-interactiva-whatsapp',  'preview-interactiva-whatsapp'],
    'confirmacion-whatsapp'  : ['plantilla-confirmacion-whatsapp', 'preview-confirmacion-whatsapp'],
    'reprogramacion-whatsapp': ['plantilla-reprogramacion-whatsapp','preview-reprogramacion-whatsapp'],
    'cancelacion-whatsapp'   : ['plantilla-cancelacion-whatsapp',  'preview-cancelacion-whatsapp'],
};

function insertarVariable(variable) {
    const ids = _taMap[plantillaActiva];
    if (!ids) return;
    const ta  = document.getElementById(ids[0]);
    if (!ta) return;
    const pos = ta.selectionStart;
    ta.value  = ta.value.substring(0, pos) + variable + ta.value.substring(ta.selectionEnd);
    ta.selectionStart = ta.selectionEnd = pos + variable.length;
    ta.focus();
    _actualizarPreview(plantillaActiva);
}

function _actualizarPreview(clave) {
    const ids = _taMap[clave];
    if (!ids) return;
    const ta  = document.getElementById(ids[0]);
    const pre = document.getElementById(ids[1]);
    if (!ta || !pre) return;
    const txt = ta.value;
    pre.textContent = txt
        ? (clave === 'email' ? txt.replace(/<[^>]*>/g, '') : txt)
        : 'Vista previa aquí.';
}

// Registrar listeners para todos los textareas
Object.entries(_taMap).forEach(([clave, ids]) => {
    document.getElementById(ids[0])?.addEventListener('input', () => _actualizarPreview(clave));
});

// ── Modo recordatorio (simple / interactivo) ──────────────
function cambiarModoRecordatorio(modo) {
    const esSim = modo === 'simple';
    document.getElementById('wa-modo-simple').style.display      = esSim ? 'block' : 'none';
    document.getElementById('wa-modo-interactivo').style.display = esSim ? 'none'  : 'block';
    document.getElementById('info-webhook').style.display        = esSim ? 'none'  : 'block';
    if (!esSim && plantillaActiva === 'whatsapp') plantillaActiva = 'interactiva-whatsapp';
    if (esSim  && plantillaActiva !== 'email')    plantillaActiva = 'whatsapp';
}

// ── Tabs WhatsApp interactivo ─────────────────────────────
function mostrarTabWA(nombre) {
    document.querySelectorAll('.wa-tab-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.wa-tab').forEach(b => b.classList.remove('wa-tab-activo'));
    document.getElementById('panel-' + nombre).style.display = 'block';
    document.getElementById('tab-btn-' + nombre).classList.add('wa-tab-activo');
    // Actualizar la plantilla activa según el tab
    const mapa = {
        'recordatorio-int': 'interactiva-whatsapp',
        'confirmacion'    : 'confirmacion-whatsapp',
        'reprogramacion'  : 'reprogramacion-whatsapp',
        'cancelacion'     : 'cancelacion-whatsapp',
    };
    plantillaActiva = mapa[nombre] ?? 'interactiva-whatsapp';
}

// ── Proveedor WhatsApp ────────────────────────────────────
function mostrarProveedor(proveedor) {
    document.getElementById('campos-ultramsg').style.display = proveedor === 'ultramsg' ? 'block' : 'none';
    document.getElementById('campos-twilio').style.display   = proveedor === 'twilio'   ? 'block' : 'none';
}
</script>
@endpush
