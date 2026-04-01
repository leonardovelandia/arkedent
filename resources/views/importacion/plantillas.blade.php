{{-- ============================================================
     VISTA: Plantillas de Importación
     Sistema: Arkevix Dental ERP
     Layout: layouts.app
     ============================================================ --}}
@extends('layouts.dev')
@section('titulo', 'Plantillas de Importación')

@push('estilos')
<style>
    .page-header    { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; gap:1rem; flex-wrap:wrap; }
    .page-titulo    { font-family:var(--fuente-titulos); font-weight:700; color:#1c2b22; margin:0; font-size:1.4rem; }
    .page-subtitulo { font-size:.82rem; color:#9ca3af; margin:.15rem 0 0 0; }

    .plantillas-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:1.25rem; margin-bottom:1.5rem; }
    @media(max-width:700px){ .plantillas-grid{ grid-template-columns:1fr; } }

    .plantilla-card {
        background:#fff; border:1px solid var(--fondo-borde); border-radius:14px;
        overflow:hidden; transition:box-shadow .2s;
        box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.08);
    }
    .plantilla-card:hover { box-shadow:0 12px 40px var(--sombra-principal),0 4px 16px rgba(0,0,0,.12); }

    .plantilla-header { padding:1.25rem 1.5rem; display:flex; align-items:center; gap:1rem; }
    .plantilla-icono  { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0; }
    .plantilla-titulo { font-weight:700; font-size:1rem; color:#1c2b22; margin:0; }
    .plantilla-desc   { font-size:.78rem; color:#6b7280; margin:.2rem 0 0 0; }

    .plantilla-columnas { padding:0 1.5rem 1rem; }
    .columnas-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#8fa39a; margin-bottom:.5rem; }
    .columnas-lista { display:flex; flex-wrap:wrap; gap:.35rem; }
    .columna-tag    { display:inline-block; font-size:.7rem; font-weight:600; padding:.15rem .55rem; border-radius:50px; background:var(--color-muy-claro); color:var(--color-principal); }
    .columna-tag.opcional { background:#f3f4f6; color:#6b7280; }

    .plantilla-footer { padding:.85rem 1.5rem; background:var(--fondo-card-alt,#f9fafb); border-top:1px solid var(--fondo-borde); display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap; }
    .plantilla-info   { font-size:.75rem; color:#6b7280; }
    .btn-descargar    { display:inline-flex; align-items:center; gap:.4rem; background:var(--color-principal); color:#fff; border:none; border-radius:8px; padding:.45rem 1rem; font-size:.82rem; font-weight:600; text-decoration:none; transition:filter .15s; }
    .btn-descargar:hover { filter:brightness(.9); color:#fff; }

    .instrucciones-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:14px; overflow:hidden; margin-bottom:1.25rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.08); }
    .instrucciones-header { padding:.75rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; gap:.5rem; font-weight:700; color:var(--color-principal); font-size:.88rem; }
    .instrucciones-body   { padding:1.25rem; }

    .sistema-tabs { display:flex; gap:.35rem; flex-wrap:wrap; margin-bottom:1.25rem; border-bottom:2px solid var(--fondo-borde); padding-bottom:.85rem; }
    .sistema-tab  { padding:.35rem .85rem; border-radius:8px 8px 0 0; font-size:.82rem; font-weight:600; cursor:pointer; color:#6b7280; border:1px solid transparent; background:none; transition:all .15s; }
    .sistema-tab.activo  { background:var(--color-muy-claro); color:var(--color-principal); border-color:var(--color-claro); }
    .sistema-tab:hover:not(.activo) { background:#f3f4f6; color:#374151; }

    .sistema-panel { display:none; }
    .sistema-panel.activo { display:block; }

    .tip-lista { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:.6rem; }
    .tip-item  { display:flex; align-items:flex-start; gap:.65rem; font-size:.83rem; color:#374151; line-height:1.5; }
    .tip-item i { color:var(--color-principal); font-size:.9rem; flex-shrink:0; margin-top:.1rem; }
    .tip-item code { background:var(--color-muy-claro); color:var(--color-principal); padding:.1rem .4rem; border-radius:4px; font-size:.78rem; font-family:monospace; }

    .alert-info { background:#dbeafe; border:1px solid #93c5fd; border-radius:10px; padding:.875rem 1.1rem; display:flex; align-items:flex-start; gap:.65rem; margin-bottom:1.25rem; font-size:.83rem; color:#1e40af; }
    .alert-warn { background:#fff3cd; border:1px solid #fde68a; border-radius:10px; padding:.875rem 1.1rem; display:flex; align-items:flex-start; gap:.65rem; font-size:.83rem; color:#856404; }
</style>
@endpush

@section('contenido')

{{-- Cabecera --}}
<div class="page-header">
    <div>
        <h4 class="page-titulo"><i class="bi bi-file-earmark-arrow-down" style="color:var(--color-principal);margin-right:.6rem;"></i>Plantillas de Importación</h4>
        <p class="page-subtitulo">Descarga nuestras plantillas CSV listas para llenar con tus datos</p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
        <a href="{{ route('dev.importacion.create') }}"
           style="display:inline-flex;align-items:center;gap:.4rem;background:var(--color-principal);color:#fff;border:none;border-radius:8px;padding:.5rem 1rem;font-size:.85rem;font-weight:600;text-decoration:none;">
            <i class="bi bi-box-arrow-in-down"></i> Nueva Importación
        </a>
        <a href="{{ route('dev.importacion.index') }}"
           style="display:inline-flex;align-items:center;gap:.4rem;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem 1rem;font-size:.85rem;font-weight:600;text-decoration:none;">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

{{-- Alerta informativa --}}
<div class="alert-info">
    <i class="bi bi-info-circle-fill" style="flex-shrink:0;font-size:1rem;margin-top:.05rem;"></i>
    <div>
        <strong>¿Cómo usar las plantillas?</strong> Descarga la plantilla correspondiente, llena los datos respetando el formato de cada columna, y luego úsala en <strong>Nueva Importación</strong> seleccionando "Excel Genérico" o "CSV Genérico" como fuente.
    </div>
</div>

{{-- Tarjetas de plantillas --}}
<div class="plantillas-grid">

    {{-- Plantilla Pacientes --}}
    <div class="plantilla-card">
        <div class="plantilla-header">
            <div class="plantilla-icono" style="background:var(--color-muy-claro);color:var(--color-principal);">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <p class="plantilla-titulo">Plantilla de Pacientes</p>
                <p class="plantilla-desc">Importa la base de datos de pacientes completa con datos personales y de contacto</p>
            </div>
        </div>
        <div class="plantilla-columnas">
            <div class="columnas-label">Columnas incluidas</div>
            <div class="columnas-lista">
                <span class="columna-tag">Nombres</span>
                <span class="columna-tag">Apellidos</span>
                <span class="columna-tag">Tipo Documento</span>
                <span class="columna-tag">Numero Documento</span>
                <span class="columna-tag">Fecha Nacimiento</span>
                <span class="columna-tag">Genero</span>
                <span class="columna-tag">Telefono</span>
                <span class="columna-tag opcional">Email</span>
                <span class="columna-tag opcional">Direccion</span>
                <span class="columna-tag opcional">Ciudad</span>
                <span class="columna-tag opcional">Ocupacion</span>
                <span class="columna-tag opcional">Acudiente</span>
                <span class="columna-tag opcional">Telefono Emergencia</span>
            </div>
            <div style="margin-top:.6rem;font-size:.72rem;color:#9ca3af;">
                <span style="background:var(--color-muy-claro);color:var(--color-principal);padding:.1rem .45rem;border-radius:4px;font-weight:600;">Obligatorio</span>
                <span style="background:#f3f4f6;color:#6b7280;padding:.1rem .45rem;border-radius:4px;font-weight:600;margin-left:.3rem;">Opcional</span>
            </div>
        </div>
        <div class="plantilla-footer">
            <div class="plantilla-info">
                <i class="bi bi-file-earmark-text"></i> CSV con separador punto y coma (;)
            </div>
            <a href="{{ route('dev.importacion.plantilla', 'pacientes') }}" class="btn-descargar">
                <i class="bi bi-download"></i> Descargar Plantilla
            </a>
        </div>
    </div>

    {{-- Plantilla Citas --}}
    <div class="plantilla-card">
        <div class="plantilla-header">
            <div class="plantilla-icono" style="background:#dbeafe;color:#1e40af;">
                <i class="bi bi-calendar3"></i>
            </div>
            <div>
                <p class="plantilla-titulo">Plantilla de Citas</p>
                <p class="plantilla-desc">Importa el historial de citas y agendamiento de los pacientes</p>
            </div>
        </div>
        <div class="plantilla-columnas">
            <div class="columnas-label">Columnas incluidas</div>
            <div class="columnas-lista">
                <span class="columna-tag">Numero Documento Paciente</span>
                <span class="columna-tag">Fecha</span>
                <span class="columna-tag">Hora Inicio</span>
                <span class="columna-tag">Hora Fin</span>
                <span class="columna-tag">Procedimiento</span>
                <span class="columna-tag opcional">Estado</span>
                <span class="columna-tag opcional">Notas</span>
            </div>
            <div style="margin-top:.6rem;font-size:.72rem;">
                <span style="background:#fff3cd;color:#856404;padding:.15rem .55rem;border-radius:4px;font-size:.7rem;font-weight:600;">Importación próximamente</span>
            </div>
        </div>
        <div class="plantilla-footer">
            <div class="plantilla-info">
                <i class="bi bi-file-earmark-text"></i> CSV con separador punto y coma (;)
            </div>
            <a href="{{ route('dev.importacion.plantilla', 'citas') }}" class="btn-descargar" style="background:#1e40af;">
                <i class="bi bi-download"></i> Descargar Plantilla
            </a>
        </div>
    </div>

    {{-- Plantilla Pagos --}}
    <div class="plantilla-card">
        <div class="plantilla-header">
            <div class="plantilla-icono" style="background:#dcfce7;color:#166534;">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <p class="plantilla-titulo">Plantilla de Pagos</p>
                <p class="plantilla-desc">Importa el historial de pagos y recibos de caja del consultorio</p>
            </div>
        </div>
        <div class="plantilla-columnas">
            <div class="columnas-label">Columnas incluidas</div>
            <div class="columnas-lista">
                <span class="columna-tag">Numero Documento Paciente</span>
                <span class="columna-tag">Fecha Pago</span>
                <span class="columna-tag">Valor</span>
                <span class="columna-tag">Metodo Pago</span>
                <span class="columna-tag">Concepto</span>
                <span class="columna-tag opcional">Numero Recibo</span>
            </div>
            <div style="margin-top:.6rem;font-size:.72rem;">
                <span style="background:#fff3cd;color:#856404;padding:.15rem .55rem;border-radius:4px;font-size:.7rem;font-weight:600;">Importación próximamente</span>
            </div>
        </div>
        <div class="plantilla-footer">
            <div class="plantilla-info">
                <i class="bi bi-file-earmark-text"></i> CSV con separador punto y coma (;)
            </div>
            <a href="{{ route('dev.importacion.plantilla', 'pagos') }}" class="btn-descargar" style="background:#166534;">
                <i class="bi bi-download"></i> Descargar Plantilla
            </a>
        </div>
    </div>

    {{-- Plantilla Evoluciones --}}
    <div class="plantilla-card">
        <div class="plantilla-header">
            <div class="plantilla-icono" style="background:#fff3e0;color:#e65100;">
                <i class="bi bi-journal-medical"></i>
            </div>
            <div>
                <p class="plantilla-titulo">Plantilla de Evoluciones</p>
                <p class="plantilla-desc">Importa las notas clínicas y evoluciones de tratamiento por paciente</p>
            </div>
        </div>
        <div class="plantilla-columnas">
            <div class="columnas-label">Columnas incluidas</div>
            <div class="columnas-lista">
                <span class="columna-tag">Numero Documento Paciente</span>
                <span class="columna-tag">Fecha</span>
                <span class="columna-tag">Procedimiento</span>
                <span class="columna-tag">Descripcion</span>
                <span class="columna-tag opcional">Materiales</span>
                <span class="columna-tag opcional">Observaciones</span>
            </div>
        </div>
        <div class="plantilla-footer">
            <div class="plantilla-info">
                <i class="bi bi-file-earmark-text"></i> CSV con separador punto y coma (;)
            </div>
            <a href="{{ route('dev.importacion.plantilla', 'evoluciones') }}" class="btn-descargar" style="background:#e65100;">
                <i class="bi bi-download"></i> Descargar Plantilla
            </a>
        </div>
    </div>

    {{-- Plantilla Historia Clínica --}}
    <div class="plantilla-card">
        <div class="plantilla-header">
            <div class="plantilla-icono" style="background:#ede9fe;color:#7c3aed;">
                <i class="bi bi-file-medical"></i>
            </div>
            <div>
                <p class="plantilla-titulo">Plantilla de Historia Clínica</p>
                <p class="plantilla-desc">Importa historias clínicas con antecedentes médicos y odontológicos</p>
            </div>
        </div>
        <div class="plantilla-columnas">
            <div class="columnas-label">Columnas incluidas</div>
            <div class="columnas-lista">
                <span class="columna-tag">Numero Documento Paciente</span>
                <span class="columna-tag">Fecha Apertura</span>
                <span class="columna-tag opcional">Motivo Consulta</span>
                <span class="columna-tag opcional">Antecedentes Medicos</span>
                <span class="columna-tag opcional">Medicamentos</span>
                <span class="columna-tag opcional">Alergias</span>
                <span class="columna-tag opcional">Presion Arterial</span>
                <span class="columna-tag opcional">Observaciones</span>
            </div>
        </div>
        <div class="plantilla-footer">
            <div class="plantilla-info">
                <i class="bi bi-file-earmark-text"></i> CSV con separador punto y coma (;)
            </div>
            <a href="{{ route('dev.importacion.plantilla', 'historia_clinica') }}" class="btn-descargar" style="background:#7c3aed;">
                <i class="bi bi-download"></i> Descargar Plantilla
            </a>
        </div>
    </div>

    {{-- Plantilla Tratamientos --}}
    <div class="plantilla-card">
        <div class="plantilla-header">
            <div class="plantilla-icono" style="background:#dcfce7;color:#166534;">
                <i class="bi bi-clipboard2-pulse"></i>
            </div>
            <div>
                <p class="plantilla-titulo">Plantilla de Tratamientos</p>
                <p class="plantilla-desc">Importa presupuestos y planes de tratamiento con valores y estados</p>
            </div>
        </div>
        <div class="plantilla-columnas">
            <div class="columnas-label">Columnas incluidas</div>
            <div class="columnas-lista">
                <span class="columna-tag">Numero Documento Paciente</span>
                <span class="columna-tag">Nombre Tratamiento</span>
                <span class="columna-tag">Valor Total</span>
                <span class="columna-tag opcional">Saldo Pendiente</span>
                <span class="columna-tag opcional">Estado</span>
                <span class="columna-tag opcional">Fecha Inicio</span>
                <span class="columna-tag opcional">Fecha Fin</span>
                <span class="columna-tag opcional">Notas</span>
            </div>
        </div>
        <div class="plantilla-footer">
            <div class="plantilla-info">
                <i class="bi bi-file-earmark-text"></i> CSV con separador punto y coma (;)
            </div>
            <a href="{{ route('dev.importacion.plantilla', 'tratamientos') }}" class="btn-descargar" style="background:#166534;">
                <i class="bi bi-download"></i> Descargar Plantilla
            </a>
        </div>
    </div>

    {{-- Plantilla Consentimientos --}}
    <div class="plantilla-card">
        <div class="plantilla-header">
            <div class="plantilla-icono" style="background:#dbeafe;color:#1d4ed8;">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <p class="plantilla-titulo">Plantilla de Consentimientos</p>
                <p class="plantilla-desc">Importa autorizaciones de datos y consentimientos informados de pacientes</p>
            </div>
        </div>
        <div class="plantilla-columnas">
            <div class="columnas-label">Columnas incluidas</div>
            <div class="columnas-lista">
                <span class="columna-tag">Numero Documento Paciente</span>
                <span class="columna-tag opcional">Fecha Autorizacion</span>
                <span class="columna-tag opcional">Acepta Almacenamiento</span>
                <span class="columna-tag opcional">Acepta WhatsApp</span>
                <span class="columna-tag opcional">Acepta Email</span>
                <span class="columna-tag opcional">Acepta Llamadas</span>
                <span class="columna-tag opcional">Acepta Recordatorios</span>
                <span class="columna-tag opcional">Firmado</span>
            </div>
        </div>
        <div class="plantilla-footer">
            <div class="plantilla-info">
                <i class="bi bi-file-earmark-text"></i> CSV con separador punto y coma (;)
            </div>
            <a href="{{ route('dev.importacion.plantilla', 'consentimientos') }}" class="btn-descargar" style="background:#1d4ed8;">
                <i class="bi bi-download"></i> Descargar Plantilla
            </a>
        </div>
    </div>

</div>

{{-- Instrucciones por sistema --}}
<div class="instrucciones-card">
    <div class="instrucciones-header" style="background:var(--color-principal);color:#fff;padding:.75rem 1.25rem;border-radius:10px 10px 0 0;">
        <i class="bi bi-book-fill"></i> Guía de Exportación por Sistema
    </div>
    <div class="instrucciones-body">
        <p style="font-size:.83rem;color:#6b7280;margin-bottom:1rem;">Selecciona tu sistema anterior para ver las instrucciones de exportación:</p>

        {{-- Tabs de sistemas --}}
        <div class="sistema-tabs">
            <button class="sistema-tab activo" onclick="mostrarSistema('dentox', this)"><i class="bi bi-tooth"></i> Dentox</button>
            <button class="sistema-tab" onclick="mostrarSistema('odontosof', this)"><i class="bi bi-file-medical"></i> OdontoSoft</button>
            <button class="sistema-tab" onclick="mostrarSistema('dentalpro', this)"><i class="bi bi-clipboard2-pulse"></i> DentalPro</button>
            <button class="sistema-tab" onclick="mostrarSistema('excel', this)"><i class="bi bi-file-earmark-spreadsheet"></i> Excel / CSV</button>
        </div>

        {{-- Panel Dentox --}}
        <div class="sistema-panel activo" id="panel-dentox">
            <ul class="tip-lista">
                <li class="tip-item"><i class="bi bi-1-circle-fill"></i> En Dentox, ve al menú <strong>Reportes → Listado de Pacientes</strong></li>
                <li class="tip-item"><i class="bi bi-2-circle-fill"></i> Aplica los filtros que necesites (activos, todos, por fecha de ingreso)</li>
                <li class="tip-item"><i class="bi bi-3-circle-fill"></i> Haz clic en el botón <strong>"Exportar Excel"</strong> o <strong>"Exportar CSV"</strong></li>
                <li class="tip-item"><i class="bi bi-4-circle-fill"></i> Asegúrate de que el archivo contenga las columnas: <code>cedula</code>, <code>nombres</code>, <code>apellidos</code>, <code>celular</code>, <code>fecha_nacimiento</code></li>
                <li class="tip-item"><i class="bi bi-5-circle-fill"></i> Al importar, selecciona <strong>"Dentox"</strong> como sistema de origen para el mapeo correcto de columnas</li>
            </ul>
        </div>

        {{-- Panel OdontoSoft --}}
        <div class="sistema-panel" id="panel-odontosof">
            <ul class="tip-lista">
                <li class="tip-item"><i class="bi bi-1-circle-fill"></i> En OdontoSoft, accede a <strong>Administración → Pacientes → Exportar</strong></li>
                <li class="tip-item"><i class="bi bi-2-circle-fill"></i> Selecciona el formato <strong>Excel (.xlsx)</strong> o <strong>CSV</strong></li>
                <li class="tip-item"><i class="bi bi-3-circle-fill"></i> Incluye los campos: <code>Nombre</code>, <code>Apellido</code>, <code>Documento</code>, <code>TipoDoc</code>, <code>Telefono</code>, <code>FechaNacimiento</code></li>
                <li class="tip-item"><i class="bi bi-4-circle-fill"></i> Verifica que las fechas estén en formato <code>AAAA-MM-DD</code> o <code>DD/MM/AAAA</code></li>
                <li class="tip-item"><i class="bi bi-5-circle-fill"></i> Al importar, selecciona <strong>"OdontoSoft"</strong> como sistema de origen</li>
            </ul>
        </div>

        {{-- Panel DentalPro --}}
        <div class="sistema-panel" id="panel-dentalpro">
            <ul class="tip-lista">
                <li class="tip-item"><i class="bi bi-1-circle-fill"></i> Accede a DentalPro y ve a <strong>Reportes → Pacientes</strong></li>
                <li class="tip-item"><i class="bi bi-2-circle-fill"></i> Usa la función <strong>"Exportar datos"</strong> en formato Excel o CSV</li>
                <li class="tip-item"><i class="bi bi-3-circle-fill"></i> El sistema buscará columnas con nombres como <code>Nombre</code>, <code>Apellido</code>, <code>Documento</code></li>
                <li class="tip-item"><i class="bi bi-4-circle-fill"></i> Si el archivo usa separador coma (<code>,</code>), el sistema lo detectará automáticamente</li>
                <li class="tip-item"><i class="bi bi-5-circle-fill"></i> Al importar, selecciona <strong>"DentalPro"</strong> para el mapeo más preciso</li>
            </ul>
        </div>

        {{-- Panel Excel/CSV --}}
        <div class="sistema-panel" id="panel-excel">
            <ul class="tip-lista">
                <li class="tip-item"><i class="bi bi-1-circle-fill"></i> Descarga la <strong>Plantilla de Pacientes</strong> desde arriba y llena los datos</li>
                <li class="tip-item"><i class="bi bi-2-circle-fill"></i> La primera fila DEBE contener los nombres de las columnas exactamente como están en la plantilla</li>
                <li class="tip-item"><i class="bi bi-3-circle-fill"></i> Las fechas deben estar en formato <code>AAAA-MM-DD</code> (ej: <code>1990-05-15</code>) o <code>DD/MM/AAAA</code></li>
                <li class="tip-item"><i class="bi bi-4-circle-fill"></i> Para el género usa: <code>Masculino</code>, <code>Femenino</code> o <code>M</code>, <code>F</code></li>
                <li class="tip-item"><i class="bi bi-5-circle-fill"></i> Para tipo de documento usa: <code>CC</code>, <code>TI</code>, <code>CE</code>, <code>PA</code>, <code>RC</code>, <code>NIT</code></li>
                <li class="tip-item"><i class="bi bi-6-circle-fill"></i> Si tienes el archivo en otro sistema, exporta a CSV con separador <code>;</code> (punto y coma)</li>
            </ul>
        </div>

        {{-- Tips generales --}}
        <div class="alert-warn" style="margin-top:1.25rem;">
            <i class="bi bi-lightbulb-fill" style="flex-shrink:0;margin-top:.05rem;"></i>
            <div>
                <strong>Consejos importantes:</strong>
                <ul style="margin:.35rem 0 0 1rem;padding:0;font-size:.8rem;line-height:1.7;">
                    <li>Haz una <strong>copia de seguridad</strong> de tu base de datos actual antes de importar.</li>
                    <li>El sistema <strong>detecta duplicados</strong> por número de documento. Los registros ya existentes no se sobreescriben.</li>
                    <li>Puedes <strong>revertir</strong> una importación si algo salió mal, siempre que no hayas desactivado esa opción.</li>
                    <li>Máximo <strong>10 MB</strong> por archivo. Si tienes más datos, divide el archivo en partes.</li>
                    <li>Los archivos Excel requieren instalar <code>phpoffice/phpspreadsheet</code>. Para archivos grandes usa CSV.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function mostrarSistema(id, btn) {
    document.querySelectorAll('.sistema-panel').forEach(p => p.classList.remove('activo'));
    document.querySelectorAll('.sistema-tab').forEach(b => b.classList.remove('activo'));
    document.getElementById('panel-' + id).classList.add('activo');
    btn.classList.add('activo');
}
</script>
@endpush

@endsection
