<div class="campo-wrap">
    <label class="campo-lbl">Nombre <span style="color:#dc2626;">*</span></label>
    <input type="text" name="nombre" class="campo-ctrl {{ $errors->has('nombre') ? 'is-invalid' : '' }}"
           value="{{ old('nombre', $plantilla?->nombre) }}" required maxlength="150"
           placeholder="Ej: Extracción dental, Blanqueamiento, Implante…">
    @error('nombre')<span class="campo-error">{{ $message }}</span>@enderror
</div>

<div class="campo-wrap">
    <label class="campo-lbl">Tipo / Categoría <span style="color:#9ca3af;font-size:.7rem;font-weight:400;">(opcional)</span></label>
    <input type="text" name="tipo" class="campo-ctrl {{ $errors->has('tipo') ? 'is-invalid' : '' }}"
           value="{{ old('tipo', $plantilla?->tipo) }}" maxlength="100"
           placeholder="Ej: Cirugía, Ortodoncia, Blanqueamiento, Endodoncia…">
    @error('tipo')<span class="campo-error">{{ $message }}</span>@enderror
</div>

<div class="campo-wrap">
    <div class="variables-hint">
        <i class="bi bi-braces"></i> Variables disponibles — se reemplazarán automáticamente al generar el consentimiento:<br>
        <code>@{{nombre_paciente}}</code>
        <code>@{{apellido_paciente}}</code>
        <code>@{{documento_paciente}}</code>
        <code>@{{fecha}}</code>
        <code>@{{doctor}}</code>
        <code>@{{procedimiento}}</code>
    </div>
    <label class="campo-lbl">Contenido <span style="color:#dc2626;">*</span></label>
    <textarea name="contenido" rows="16" class="campo-ctrl {{ $errors->has('contenido') ? 'is-invalid' : '' }}"
              style="font-family:monospace;font-size:.82rem;line-height:1.6;resize:vertical;" required
              placeholder="Redacta el texto del consentimiento informado. Usa las variables de arriba para datos personalizables…">{{ old('contenido', $plantilla?->contenido) }}</textarea>
    @error('contenido')<span class="campo-error">{{ $message }}</span>@enderror
</div>
