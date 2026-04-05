@props([
    'modulo',
    'ruta',
    'tieneSensibles'       => true,
    'labelSensibles'       => 'Incluir datos sensibles',
    'advertenciaSensibles' => 'Esta exportación incluirá información protegida.',
    'todoSensible'         => false,
])

<div class="exportar-wrapper" style="position:relative; display:inline-block;">
    <button
        type="button"
        onclick="abrirModalExportar('{{ $modulo }}')"
        style="
            display:inline-flex; align-items:center; gap:.4rem;
            padding:.45rem 1rem;
            background:#16a34a; color:white; border:none; border-radius:8px;
            font-size:.82rem; font-weight:600; cursor:pointer;
            font-family:var(--fuente-principal); transition:background .2s;
            box-shadow:0 4px 12px rgba(22,163,74,.30);
        "
        onmouseover="this.style.background='#15803d'"
        onmouseout="this.style.background='#16a34a'"
    >
        <i class="bi bi-download"></i> Exportar
    </button>
</div>

{{-- Modal --}}
<div id="modal-exportar-{{ $modulo }}"
     style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center;"
     onclick="if(event.target===this)cerrarModalExportar('{{ $modulo }}')">
    <div style="background:white;border-radius:16px;width:100%;max-width:460px;margin:1rem;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.35);font-family:var(--fuente-principal);">

        {{-- Header --}}
        <div style="background:var(--color-principal);padding:1rem 1.25rem;display:flex;align-items:center;gap:.75rem;">
            <i class="bi bi-download" style="color:white;font-size:1.1rem;"></i>
            <span style="color:white;font-weight:600;font-size:.95rem;">Exportar datos</span>
            <button onclick="cerrarModalExportar('{{ $modulo }}')"
                    style="margin-left:auto;background:none;border:none;color:white;font-size:1.2rem;cursor:pointer;opacity:.8;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div style="padding:1.25rem;">

            @if($todoSensible)
            <div style="background:#fef2f2;border:1px solid #dc2626;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.82rem;color:#991b1b;">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <strong>Atención:</strong> Este módulo contiene únicamente información sensible y documentos legales.
                La exportación quedará registrada en el log de auditoría.
            </div>
            @endif

            {{-- Formato --}}
            <div style="margin-bottom:1.25rem;">
                <div style="font-size:.78rem;font-weight:600;color:var(--color-principal);margin-bottom:.5rem;text-transform:uppercase;letter-spacing:.05em;">
                    Formato de exportación
                </div>
                <div style="display:flex;gap:.75rem;">
                    @foreach([['excel','bi-file-earmark-excel','#16a34a','Excel'],['csv','bi-filetype-csv','#0284c7','CSV'],['pdf','bi-file-earmark-pdf','#dc2626','PDF']] as $fmt)
                    <label style="flex:1;cursor:pointer;">
                        <input type="radio" name="formato_{{ $modulo }}" value="{{ $fmt[0] }}"
                               style="display:none;" class="radio-formato-{{ $modulo }}"
                               onchange="seleccionarFormato('{{ $modulo }}','{{ $fmt[0] }}','{{ $fmt[2] }}')">
                        <div id="btn-formato-{{ $modulo }}-{{ $fmt[0] }}"
                             style="border:2px solid #e5e7eb;border-radius:10px;padding:.75rem .5rem;text-align:center;transition:all .2s;">
                            <i class="bi {{ $fmt[1] }}" style="font-size:1.5rem;color:#9ca3af;display:block;margin-bottom:.25rem;"></i>
                            <span style="font-size:.78rem;font-weight:600;color:#6b7280;">{{ $fmt[3] }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Datos sensibles --}}
            @if($tieneSensibles && !$todoSensible)
            <div style="margin-bottom:1.25rem;">
                <div style="font-size:.78rem;font-weight:600;color:var(--color-principal);margin-bottom:.5rem;text-transform:uppercase;letter-spacing:.05em;">
                    Datos a incluir
                </div>
                <label style="display:flex;align-items:flex-start;gap:.75rem;padding:.75rem;border:1px solid #e5e7eb;border-radius:8px;cursor:pointer;background:#f9fafb;">
                    <input type="checkbox" id="check-sensibles-{{ $modulo }}"
                           style="margin-top:2px;accent-color:var(--color-principal);width:16px;height:16px;flex-shrink:0;"
                           onchange="toggleAdvertenciaSensibles('{{ $modulo }}')">
                    <div>
                        <div style="font-size:.82rem;font-weight:600;color:#374151;">{{ $labelSensibles }}</div>
                        <div style="font-size:.72rem;color:#9ca3af;margin-top:2px;">Requiere confirmación adicional</div>
                    </div>
                </label>
                <div id="advertencia-sensibles-{{ $modulo }}"
                     style="display:none;background:#fefce8;border:1px solid #ca8a04;border-radius:8px;padding:.625rem .875rem;margin-top:.5rem;font-size:.78rem;color:#92400e;">
                    <i class="bi bi-shield-exclamation me-1"></i>
                    {{ $advertenciaSensibles }}
                    Conforme a la Ley 1581 de 2012, el responsable del uso de esta información es el usuario que realiza la exportación.
                </div>
            </div>
            @endif

            {{-- Info auditoría --}}
            <div style="background:#f0fdf4;border:1px solid #16a34a;border-radius:8px;padding:.625rem .875rem;margin-bottom:1.25rem;font-size:.75rem;color:#166534;">
                <i class="bi bi-info-circle me-1"></i>
                Esta exportación quedará registrada en el log de auditoría con tu usuario, fecha, hora e IP.
            </div>

            {{-- Formulario oculto --}}
            <form id="form-exportar-{{ $modulo }}" method="POST" action="{{ $ruta }}">
                @csrf
                <input type="hidden" name="formato"           id="input-formato-{{ $modulo }}"   value="">
                <input type="hidden" name="incluir_sensibles" id="input-sensibles-{{ $modulo }}" value="0">
                <input type="hidden" name="modulo"            value="{{ $modulo }}">
                <div id="filtros-extra-{{ $modulo }}"></div>
            </form>

            {{-- Botones --}}
            <div style="display:flex;gap:.75rem;">
                <button type="button" onclick="cerrarModalExportar('{{ $modulo }}')"
                        style="flex:1;padding:.625rem;border:1px solid #e5e7eb;border-radius:8px;background:white;font-size:.85rem;cursor:pointer;color:#6b7280;">
                    Cancelar
                </button>
                <button type="button" id="btn-confirmar-exportar-{{ $modulo }}"
                        onclick="confirmarExportacion('{{ $modulo }}',{{ $todoSensible ? 'true' : 'false' }})"
                        disabled
                        style="flex:2;padding:.625rem;border:none;border-radius:8px;background:#9ca3af;color:white;font-size:.85rem;font-weight:600;cursor:not-allowed;transition:all .2s;">
                    <i class="bi bi-download me-1"></i> Exportar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function abrirModalExportar(m) {
    const modal = document.getElementById('modal-exportar-' + m);
    if (modal) { modal.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
}
function cerrarModalExportar(m) {
    const modal = document.getElementById('modal-exportar-' + m);
    if (!modal) return;
    modal.style.display = 'none';
    document.body.style.overflow = '';
    document.querySelectorAll('.radio-formato-' + m).forEach(r => r.checked = false);
    document.querySelectorAll('[id^="btn-formato-' + m + '-"]').forEach(b => {
        b.style.border = '2px solid #e5e7eb';
        b.querySelector('i').style.color = '#9ca3af';
        b.querySelector('span').style.color = '#6b7280';
    });
    const cs = document.getElementById('check-sensibles-' + m);
    if (cs) cs.checked = false;
    const adv = document.getElementById('advertencia-sensibles-' + m);
    if (adv) adv.style.display = 'none';
    const si = document.getElementById('input-sensibles-' + m);
    if (si) si.value = '0';
    const btn = document.getElementById('btn-confirmar-exportar-' + m);
    if (btn) { btn.disabled = true; btn.style.background = '#9ca3af'; btn.style.cursor = 'not-allowed'; }
    const fe = document.getElementById('filtros-extra-' + m);
    if (fe) fe.innerHTML = '';
}
function seleccionarFormato(m, formato, color) {
    document.querySelectorAll('[id^="btn-formato-' + m + '-"]').forEach(b => {
        b.style.border = '2px solid #e5e7eb';
        b.querySelector('i').style.color = '#9ca3af';
        b.querySelector('span').style.color = '#6b7280';
    });
    const ba = document.getElementById('btn-formato-' + m + '-' + formato);
    if (ba) {
        ba.style.border = '2px solid ' + color;
        ba.querySelector('i').style.color = color;
        ba.querySelector('span').style.color = color;
    }
    document.getElementById('input-formato-' + m).value = formato;
    const btn = document.getElementById('btn-confirmar-exportar-' + m);
    if (btn) { btn.disabled = false; btn.style.background = '#16a34a'; btn.style.cursor = 'pointer'; }
}
function toggleAdvertenciaSensibles(m) {
    const cs  = document.getElementById('check-sensibles-' + m);
    const adv = document.getElementById('advertencia-sensibles-' + m);
    const si  = document.getElementById('input-sensibles-' + m);
    if (cs && adv && si) { adv.style.display = cs.checked ? 'block' : 'none'; si.value = cs.checked ? '1' : '0'; }
}
function confirmarExportacion(m, todoSensible) {
    const formato = document.getElementById('input-formato-' + m)?.value;
    if (!formato) { alert('Selecciona un formato de exportación.'); return; }
    const incluyeSensibles = document.getElementById('input-sensibles-' + m)?.value === '1';
    let msg = '¿Confirmas la exportación?\n\nFormato: ' + formato.toUpperCase();
    if (todoSensible || incluyeSensibles) {
        msg += '\n\n⚠ INCLUYE DATOS SENSIBLES\nEsta acción quedará registrada en el log de auditoría.';
    }
    if (confirm(msg)) {
        document.getElementById('form-exportar-' + m).submit();
    }
}
</script>
