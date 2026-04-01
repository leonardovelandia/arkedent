{{-- ============================================================
     VISTA: Nueva Importación
     Sistema: Arkevix Dental ERP
     Layout: layouts.app
     ============================================================ --}}
@extends('layouts.dev')
@section('titulo', 'Nueva Importación')

@push('estilos')
<style>
    .page-header    { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; gap:1rem; flex-wrap:wrap; }
    .page-titulo    { font-family:var(--fuente-titulos); font-weight:700; color:#1c2b22; margin:0; font-size:1.4rem; }
    .page-subtitulo { font-size:.82rem; color:#9ca3af; margin:.15rem 0 0 0; }

    /* Stepper */
    .stepper { display:flex; align-items:center; gap:0; margin-bottom:2rem; }
    .step { display:flex; align-items:center; flex:1; }
    .step-circle {
        width:36px; height:36px; border-radius:50%; border:2px solid var(--fondo-borde);
        display:flex; align-items:center; justify-content:center;
        font-weight:700; font-size:.85rem; color:#9ca3af; background:#fff;
        transition:all .25s; flex-shrink:0;
    }
    .step-label { font-size:.75rem; font-weight:500; color:#9ca3af; margin-left:.5rem; transition:all .25s; white-space:nowrap; }
    .step-line  { flex:1; height:2px; background:var(--fondo-borde); margin:0 .5rem; transition:all .25s; }
    .step.activo .step-circle  { background:var(--color-principal); border-color:var(--color-principal); color:#fff; }
    .step.activo .step-label   { color:var(--color-principal); font-weight:700; }
    .step.completado .step-circle { background:var(--color-claro); border-color:var(--color-principal); color:var(--color-principal); }
    .step.completado .step-label  { color:var(--color-principal); }
    .step-line.completado { background:var(--color-principal); }

    /* Source cards */
    .fuentes-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
    @media(max-width:700px){ .fuentes-grid{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:450px){ .fuentes-grid{ grid-template-columns:1fr; } }

    .fuente-card {
        border:2px solid var(--fondo-borde); border-radius:12px; padding:1.25rem;
        cursor:pointer; transition:all .2s; text-align:center; position:relative;
        background:#fff;
    }
    .fuente-card:hover { border-color:var(--color-claro); background:var(--color-muy-claro); }
    .fuente-card.seleccionado { border-color:var(--color-principal); background:var(--color-muy-claro); box-shadow:0 0 0 3px var(--color-muy-claro); }
    .fuente-card input[type=radio] { position:absolute; opacity:0; width:0; height:0; }
    .fuente-icono { font-size:2rem; margin-bottom:.6rem; display:block; }
    .fuente-nombre { font-weight:700; font-size:.9rem; color:#8fa39a; display:block; margin-bottom:.25rem; }
    .fuente-desc   { font-size:.73rem; color:#8fa39a; display:block; line-height:1.4; }
    .fuente-check  { position:absolute; top:.6rem; right:.6rem; font-size:.9rem; color:var(--color-principal); opacity:0; transition:opacity .2s; }
    .fuente-card.seleccionado .fuente-check { opacity:1; }

    /* Tipo datos */
    .tipo-lista { display:flex; flex-direction:column; gap:.5rem; }
    .tipo-item {
        display:flex; align-items:center; gap:.85rem; padding:.75rem 1rem;
        border:2px solid var(--fondo-borde); border-radius:10px; cursor:pointer;
        transition:all .2s; background:#fff;
    }
    .tipo-item:hover { border-color:var(--color-claro); background:var(--color-muy-claro); }
    .tipo-item.seleccionado { border-color:var(--color-principal); background:var(--color-muy-claro); }
    .tipo-item input[type=radio] { display:none; }
    .tipo-radio-visual {
        width:18px; height:18px; border-radius:50%; border:2px solid var(--fondo-borde);
        flex-shrink:0; transition:all .2s; position:relative;
    }
    .tipo-item.seleccionado .tipo-radio-visual { border-color:var(--color-principal); background:var(--color-principal); }
    .tipo-item.seleccionado .tipo-radio-visual::after {
        content:''; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
        width:6px; height:6px; border-radius:50%; background:#fff;
    }
    .tipo-nombre { font-weight:600; font-size:.88rem; color:#1c2b22; }
    .tipo-desc   { font-size:.73rem; color:#8fa39a; margin-top:.1rem; }
    .tipo-badge  { margin-left:auto; font-size:.68rem; font-weight:700; padding:.15rem .55rem; border-radius:50px; }

    /* Dropzone */
    .dropzone {
        border:2px dashed var(--fondo-borde); border-radius:12px; padding:2.5rem 1.5rem;
        text-align:center; cursor:pointer; transition:all .25s; background:#fafafa; position:relative;
    }
    .dropzone:hover, .dropzone.drag-over { border-color:var(--color-principal); background:var(--color-muy-claro); }
    .dropzone input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
    .dropzone-icono { font-size:2.5rem; color:#9ca3af; margin-bottom:.75rem; display:block; transition:color .2s; }
    .dropzone:hover .dropzone-icono, .dropzone.drag-over .dropzone-icono { color:var(--color-principal); }
    .dropzone-texto { font-weight:600; font-size:.9rem; color:#374151; }
    .dropzone-sub   { font-size:.75rem; color:#9ca3af; margin-top:.25rem; }
    .dropzone-file-info { display:none; align-items:center; gap:.75rem; background:var(--color-muy-claro); border-radius:8px; padding:.75rem 1rem; margin-top:1rem; }
    .dropzone-file-info.visible { display:flex; }

    /* Preview tabla */
    .preview-container { display:none; margin-top:1rem; }
    .preview-container.visible { display:block; }
    .tabla-preview { width:100%; border-collapse:collapse; font-size:.75rem; }
    .tabla-preview th { background:var(--color-muy-claro); color:var(--color-principal); font-weight:700; text-transform:uppercase; font-size:.65rem; letter-spacing:.05em; padding:.4rem .65rem; border:1px solid var(--fondo-borde); white-space:nowrap; }
    .tabla-preview td { padding:.35rem .65rem; border:1px solid var(--fondo-borde); color:#374151; vertical-align:top; }
    .tabla-preview tr:nth-child(even) td { background:var(--fondo-card-alt,#f9fafb); }

    .form-card { background:#fff; border:1px solid var(--fondo-borde); border-radius:12px; overflow:hidden; margin-bottom:1rem; box-shadow:0 8px 28px var(--sombra-principal),0 2px 8px rgba(0,0,0,.08); }
    .form-card-header { padding:.75rem 1.25rem; border-bottom:1px solid var(--fondo-borde); display:flex; align-items:center; gap:.5rem; }
    .form-card-body   { padding:1.25rem; }

    .btn-paso { display:inline-flex; align-items:center; gap:.4rem; border:none; border-radius:8px; padding:.55rem 1.25rem; font-size:.88rem; font-weight:600; cursor:pointer; transition:all .15s; }
    .btn-siguiente { background:var(--color-principal); color:#fff; }
    .btn-anterior  { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }
    .btn-paso:disabled { opacity:.5; cursor:not-allowed; }
    .btn-paso:not(:disabled):hover { filter:brightness(.92); }

    .panel-paso { display:none; }
    .panel-paso.activo { display:block; }
</style>
@endpush

@section('contenido')

{{-- Cabecera --}}
<div class="page-header">
    <div>
        <h4 class="page-titulo"><i class="bi bi-box-arrow-in-down" style="color:var(--color-principal);margin-right:.6rem;"></i>Nueva Importación</h4>
        <p class="page-subtitulo">Sigue los pasos para importar datos desde tu sistema anterior</p>
    </div>
    <a href="{{ route('dev.importacion.index') }}"
       style="display:inline-flex;align-items:center;gap:.4rem;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem 1rem;font-size:.85rem;font-weight:600;text-decoration:none;">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

@if($errors->any())
<div style="background:#fde8e8;border:1px solid #fca5a5;border-radius:10px;padding:.75rem 1.1rem;margin-bottom:1rem;color:#DC3545;font-size:.85rem;">
    <i class="bi bi-x-circle-fill"></i>
    <strong>Por favor corrige los siguientes errores:</strong>
    <ul style="margin:.4rem 0 0 1.2rem;padding:0;">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Stepper --}}
<div class="stepper">
    <div class="step activo" id="step-indicator-1">
        <div class="step-circle">1</div>
        <span class="step-label">Origen</span>
    </div>
    <div class="step-line" id="line-1-2"></div>
    <div class="step" id="step-indicator-2">
        <div class="step-circle">2</div>
        <span class="step-label">Tipo de datos</span>
    </div>
    <div class="step-line" id="line-2-3"></div>
    <div class="step" id="step-indicator-3">
        <div class="step-circle">3</div>
        <span class="step-label">Archivo</span>
    </div>
</div>

<form id="form-importacion" method="POST" action="{{ route('dev.importacion.store') }}" enctype="multipart/form-data">
@csrf

{{-- PASO 1: Fuente --}}
<div class="panel-paso activo" id="paso-1">
    <div class="form-card">
        <div class="form-card-header" style="background:var(--color-principal);color:#fff;border-radius:10px 10px 0 0;">
            <i class="bi bi-1-circle-fill"></i>
            <strong>Selecciona el sistema de origen</strong>
        </div>
        <div class="form-card-body">
            <p style="font-size:.82rem;color:#6b7280;margin-bottom:1.25rem;">¿De qué software provienen los datos que vas a importar?</p>

            <div class="fuentes-grid">
                <label class="fuente-card" id="card-dentox">
                    <input type="radio" name="fuente" value="dentox">
                    <i class="bi bi-check-circle-fill fuente-check"></i>
                    <span class="fuente-icono">🦷</span>
                    <span class="fuente-nombre">Dentox</span>
                    <span class="fuente-desc">Software dental colombiano con exportación CSV/Excel</span>
                </label>
                <label class="fuente-card" id="card-odontosof">
                    <input type="radio" name="fuente" value="odontosof">
                    <i class="bi bi-check-circle-fill fuente-check"></i>
                    <span class="fuente-icono">🏥</span>
                    <span class="fuente-nombre">OdontoSoft</span>
                    <span class="fuente-desc">Sistema de gestión odontológica latinoamericano</span>
                </label>
                <label class="fuente-card" id="card-dentalpro">
                    <input type="radio" name="fuente" value="dentalpro">
                    <i class="bi bi-check-circle-fill fuente-check"></i>
                    <span class="fuente-icono">📋</span>
                    <span class="fuente-nombre">DentalPro</span>
                    <span class="fuente-desc">Plataforma de administración dental profesional</span>
                </label>
                <label class="fuente-card" id="card-excel_generico">
                    <input type="radio" name="fuente" value="excel_generico">
                    <i class="bi bi-check-circle-fill fuente-check"></i>
                    <span class="fuente-icono">📊</span>
                    <span class="fuente-nombre">Excel Genérico</span>
                    <span class="fuente-desc">Archivo .xlsx o .xls con columnas estándar</span>
                </label>
                <label class="fuente-card" id="card-csv_generico">
                    <input type="radio" name="fuente" value="csv_generico">
                    <i class="bi bi-check-circle-fill fuente-check"></i>
                    <span class="fuente-icono">📄</span>
                    <span class="fuente-nombre">CSV Genérico</span>
                    <span class="fuente-desc">Archivo CSV o TXT con separador ; , o |</span>
                </label>
                <label class="fuente-card" id="card-sql_dump">
                    <input type="radio" name="fuente" value="sql_dump">
                    <i class="bi bi-check-circle-fill fuente-check"></i>
                    <span class="fuente-icono">🗄️</span>
                    <span class="fuente-nombre">SQL Dump</span>
                    <span class="fuente-desc">Exportación SQL convertida a CSV para importar</span>
                </label>
            </div>

            <div id="alerta-fuente" style="display:none;margin-top:1rem;background:#fde8e8;border:1px solid #fca5a5;border-radius:8px;padding:.65rem 1rem;color:#DC3545;font-size:.82rem;">
                <i class="bi bi-exclamation-circle-fill"></i> Por favor selecciona el sistema de origen.
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:1.5rem;">
                <button type="button" class="btn-paso btn-siguiente" onclick="irPaso(2)">
                    Siguiente <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- PASO 2: Tipo de datos --}}
<div class="panel-paso" id="paso-2">
    <div class="form-card">
        <div class="form-card-header" style="background:var(--color-principal);color:#fff;border-radius:10px 10px 0 0;">
            <i class="bi bi-2-circle-fill"></i>
            <strong>¿Qué datos deseas importar?</strong>
        </div>
        <div class="form-card-body">
            <p style="font-size:.82rem;color:#6b7280;margin-bottom:1.25rem;">Selecciona el tipo de información que contiene el archivo.</p>

            <div class="tipo-lista">
                <label class="tipo-item" id="tipo-pacientes">
                    <input type="radio" name="tipo_datos" value="pacientes">
                    <div class="tipo-radio-visual"></div>
                    <div>
                        <div class="tipo-nombre"><i class="bi bi-people" style="color:var(--color-principal);"></i> Pacientes</div>
                        <div class="tipo-desc">Nombres, documentos, fechas de nacimiento, contactos</div>
                    </div>
                    <span class="tipo-badge" style="background:var(--color-muy-claro);color:var(--color-principal);">Soportado</span>
                </label>

                <label class="tipo-item" id="tipo-citas">
                    <input type="radio" name="tipo_datos" value="citas">
                    <div class="tipo-radio-visual"></div>
                    <div>
                        <div class="tipo-nombre"><i class="bi bi-calendar3" style="color:var(--color-principal);"></i> Citas</div>
                        <div class="tipo-desc">Historial de citas y agendamiento</div>
                    </div>
                    <span class="tipo-badge" style="background:var(--color-muy-claro);color:var(--color-principal);">Soportado</span>
                </label>

                <label class="tipo-item" id="tipo-historia_clinica">
                    <input type="radio" name="tipo_datos" value="historia_clinica">
                    <div class="tipo-radio-visual"></div>
                    <div>
                        <div class="tipo-nombre"><i class="bi bi-file-medical" style="color:var(--color-principal);"></i> Historia Clínica</div>
                        <div class="tipo-desc">Diagnósticos, antecedentes médicos</div>
                    </div>
                    <span class="tipo-badge" style="background:var(--color-muy-claro);color:var(--color-principal);">Soportado</span>
                </label>

                <label class="tipo-item" id="tipo-tratamientos">
                    <input type="radio" name="tipo_datos" value="tratamientos">
                    <div class="tipo-radio-visual"></div>
                    <div>
                        <div class="tipo-nombre"><i class="bi bi-clipboard2-pulse" style="color:var(--color-principal);"></i> Tratamientos</div>
                        <div class="tipo-desc">Presupuestos y planes de tratamiento</div>
                    </div>
                    <span class="tipo-badge" style="background:var(--color-muy-claro);color:var(--color-principal);">Soportado</span>
                </label>

                <label class="tipo-item" id="tipo-pagos">
                    <input type="radio" name="tipo_datos" value="pagos">
                    <div class="tipo-radio-visual"></div>
                    <div>
                        <div class="tipo-nombre"><i class="bi bi-cash-stack" style="color:var(--color-principal);"></i> Pagos</div>
                        <div class="tipo-desc">Historial de pagos y recibos</div>
                    </div>
                    <span class="tipo-badge" style="background:var(--color-muy-claro);color:var(--color-principal);">Soportado</span>
                </label>

                <label class="tipo-item" id="tipo-evoluciones">
                    <input type="radio" name="tipo_datos" value="evoluciones">
                    <div class="tipo-radio-visual"></div>
                    <div>
                        <div class="tipo-nombre"><i class="bi bi-journal-medical" style="color:var(--color-principal);"></i> Evoluciones</div>
                        <div class="tipo-desc">Notas clínicas y evoluciones de tratamiento</div>
                    </div>
                    <span class="tipo-badge" style="background:var(--color-muy-claro);color:var(--color-principal);">Soportado</span>
                </label>

                <label class="tipo-item" id="tipo-consentimientos">
                    <input type="radio" name="tipo_datos" value="consentimientos">
                    <div class="tipo-radio-visual"></div>
                    <div>
                        <div class="tipo-nombre"><i class="bi bi-shield-check" style="color:var(--color-principal);"></i> Consentimientos</div>
                        <div class="tipo-desc">Autorizaciones de datos y consentimientos informados</div>
                    </div>
                    <span class="tipo-badge" style="background:var(--color-muy-claro);color:var(--color-principal);">Soportado</span>
                </label>
            </div>

            <div id="alerta-tipo" style="display:none;margin-top:1rem;background:#fde8e8;border:1px solid #fca5a5;border-radius:8px;padding:.65rem 1rem;color:#DC3545;font-size:.82rem;">
                <i class="bi bi-exclamation-circle-fill"></i> Por favor selecciona el tipo de datos.
            </div>

            <div style="display:flex;justify-content:space-between;margin-top:1.5rem;">
                <button type="button" class="btn-paso btn-anterior" onclick="irPaso(1)">
                    <i class="bi bi-arrow-left"></i> Anterior
                </button>
                <button type="button" class="btn-paso btn-siguiente" onclick="irPaso(3)">
                    Siguiente <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- PASO 3: Archivo --}}
<div class="panel-paso" id="paso-3">
    <div class="form-card">
        <div class="form-card-header" style="background:var(--color-principal);color:#fff;border-radius:10px 10px 0 0;">
            <i class="bi bi-3-circle-fill"></i>
            <strong>Sube el archivo de importación</strong>
        </div>
        <div class="form-card-body">

            {{-- Dropzone --}}
            <div class="dropzone" id="dropzone" ondragover="event.preventDefault();this.classList.add('drag-over')" ondragleave="this.classList.remove('drag-over')" ondrop="soltar(event)">
                <input type="file" name="archivo" id="archivo-input" accept=".csv,.txt,.xlsx,.xls" onchange="archivoSeleccionado(this)">
                <i class="bi bi-cloud-upload dropzone-icono"></i>
                <div class="dropzone-texto">Arrastra tu archivo aquí o haz clic para seleccionar</div>
                <div class="dropzone-sub">Formatos permitidos: CSV, TXT, XLSX, XLS — Máximo 10 MB</div>
            </div>

            {{-- Info del archivo --}}
            <div class="dropzone-file-info" id="file-info">
                <i class="bi bi-file-earmark-check" style="font-size:1.5rem;color:var(--color-principal);flex-shrink:0;"></i>
                <div style="flex:1;min-width:0;">
                    <div id="file-nombre" style="font-weight:600;font-size:.85rem;color:#1c2b22;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></div>
                    <div id="file-size" style="font-size:.75rem;color:#6b7280;margin-top:.1rem;"></div>
                </div>
                <button type="button" onclick="limpiarArchivo()"
                    style="background:#fde8e8;color:#DC3545;border:none;border-radius:6px;padding:.25rem .6rem;font-size:.8rem;cursor:pointer;flex-shrink:0;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- Vista previa --}}
            <div class="preview-container" id="preview-container">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.6rem;">
                    <span style="font-size:.78rem;font-weight:700;color:var(--color-principal);"><i class="bi bi-eye"></i> Vista previa (primeras 10 filas)</span>
                    <span id="preview-count" style="font-size:.75rem;color:#6b7280;"></span>
                </div>
                <div style="overflow-x:auto;border-radius:8px;border:1px solid var(--fondo-borde);">
                    <table class="tabla-preview" id="tabla-preview">
                        <thead id="preview-headers"></thead>
                        <tbody id="preview-body"></tbody>
                    </table>
                </div>
                <div style="margin-top:.5rem;background:#fff3cd;border-radius:6px;padding:.5rem .85rem;font-size:.75rem;color:#856404;">
                    <i class="bi bi-info-circle"></i> Esta es solo una vista previa. El proceso completo se ejecutará al confirmar.
                </div>
            </div>

            {{-- Notas --}}
            <div style="margin-top:1.25rem;">
                <label style="font-size:.8rem;font-weight:600;color:#374151;display:block;margin-bottom:.35rem;">
                    Notas adicionales <span style="font-weight:400;color:#9ca3af;">(opcional)</span>
                </label>
                <textarea name="notas" rows="2" maxlength="1000"
                    style="width:100%;border:1px solid var(--fondo-borde);border-radius:8px;padding:.5rem .75rem;font-size:.85rem;outline:none;resize:vertical;font-family:inherit;box-sizing:border-box;"
                    placeholder="Ej: Migración inicial de pacientes desde consultorio anterior. 250 registros aprox.">{{ old('notas') }}</textarea>
            </div>

            {{-- Resumen antes de enviar --}}
            <div id="resumen-importacion" style="display:none;margin-top:1rem;background:var(--color-muy-claro);border:1px solid var(--color-claro);border-radius:10px;padding:1rem 1.25rem;">
                <div style="font-size:.8rem;font-weight:700;color:var(--color-principal);margin-bottom:.6rem;"><i class="bi bi-check2-all"></i> Resumen de importación</div>
                <div style="display:grid;grid-template-columns:auto 1fr;gap:.3rem .75rem;font-size:.82rem;">
                    <span style="color:#6b7280;">Sistema origen:</span>
                    <span id="resumen-fuente" style="font-weight:600;color:#1c2b22;"></span>
                    <span style="color:#6b7280;">Tipo de datos:</span>
                    <span id="resumen-tipo" style="font-weight:600;color:#1c2b22;"></span>
                    <span style="color:#6b7280;">Archivo:</span>
                    <span id="resumen-archivo" style="font-weight:600;color:#1c2b22;"></span>
                </div>
            </div>

            <div id="alerta-archivo" style="display:none;margin-top:1rem;background:#fde8e8;border:1px solid #fca5a5;border-radius:8px;padding:.65rem 1rem;color:#DC3545;font-size:.82rem;">
                <i class="bi bi-exclamation-circle-fill"></i> Por favor selecciona un archivo para importar.
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:1.5rem;">
                <button type="button" class="btn-paso btn-anterior" onclick="irPaso(2)">
                    <i class="bi bi-arrow-left"></i> Anterior
                </button>
                <button type="submit" id="btn-importar"
                    style="display:inline-flex;align-items:center;gap:.5rem;background:var(--color-principal);color:#fff;border:none;border-radius:8px;padding:.6rem 1.5rem;font-size:.9rem;font-weight:700;cursor:pointer;transition:all .15s;">
                    <i class="bi bi-box-arrow-in-down"></i> Iniciar Importación
                </button>
            </div>
        </div>
    </div>

    {{-- Enlace a plantillas --}}
    <div style="background:#fff;border:1px solid var(--fondo-borde);border-radius:12px;padding:1rem 1.25rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
        <i class="bi bi-file-earmark-arrow-down" style="font-size:1.5rem;color:var(--color-principal);flex-shrink:0;"></i>
        <div style="flex:1;min-width:200px;">
            <div style="font-weight:600;font-size:.88rem;color:#1c2b22;">¿Necesitas una plantilla?</div>
            <div style="font-size:.75rem;color:#6b7280;margin-top:.15rem;">Descarga nuestra plantilla estándar y llénala con tus datos para importar sin errores.</div>
        </div>
        <a href="{{ route('dev.importacion.plantillas') }}"
           style="display:inline-flex;align-items:center;gap:.4rem;background:var(--color-muy-claro);color:var(--color-principal);border:1px solid var(--color-claro);border-radius:8px;padding:.45rem 1rem;font-size:.82rem;font-weight:600;text-decoration:none;white-space:nowrap;">
            <i class="bi bi-download"></i> Ver Plantillas
        </a>
    </div>
</div>

</form>

@push('scripts')
<script>
const FUENTES_LABELS = {
    dentox: 'Dentox', odontosof: 'OdontoSoft', dentalpro: 'DentalPro',
    excel_generico: 'Excel Genérico', csv_generico: 'CSV Genérico', sql_dump: 'SQL Dump'
};
const TIPOS_LABELS = {
    pacientes: 'Pacientes', historia_clinica: 'Historia Clínica', citas: 'Citas',
    tratamientos: 'Tratamientos', pagos: 'Pagos', evoluciones: 'Evoluciones', todo: 'Todo'
};

let pasoActual = 1;

// Source card click
document.querySelectorAll('.fuente-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.fuente-card').forEach(c => c.classList.remove('seleccionado'));
        this.classList.add('seleccionado');
        this.querySelector('input[type=radio]').checked = true;
        document.getElementById('alerta-fuente').style.display = 'none';
    });
});

// Tipo item click
document.querySelectorAll('.tipo-item').forEach(item => {
    const radio = item.querySelector('input[type=radio]');
    if (!radio.disabled) {
        item.addEventListener('click', function() {
            document.querySelectorAll('.tipo-item').forEach(i => i.classList.remove('seleccionado'));
            this.classList.add('seleccionado');
            radio.checked = true;
            document.getElementById('alerta-tipo').style.display = 'none';
        });
    }
});

function irPaso(paso) {
    // Validaciones
    if (paso === 2 && pasoActual === 1) {
        if (!document.querySelector('input[name=fuente]:checked')) {
            document.getElementById('alerta-fuente').style.display = 'block';
            return;
        }
    }
    if (paso === 3 && pasoActual === 2) {
        if (!document.querySelector('input[name=tipo_datos]:checked')) {
            document.getElementById('alerta-tipo').style.display = 'block';
            return;
        }
    }

    // Ocultar paso actual
    document.getElementById('paso-' + pasoActual).classList.remove('activo');

    // Actualizar stepper
    const indicadorActual = document.getElementById('step-indicator-' + pasoActual);
    indicadorActual.classList.remove('activo');
    indicadorActual.classList.add('completado');

    if (pasoActual < paso) {
        const linea = document.getElementById('line-' + pasoActual + '-' + (pasoActual + 1));
        if (linea) linea.classList.add('completado');
    }

    if (paso < pasoActual) {
        const lineaAnterior = document.getElementById('line-' + paso + '-' + (paso + 1));
        if (lineaAnterior) lineaAnterior.classList.remove('completado');
        document.getElementById('step-indicator-' + pasoActual).classList.remove('completado');
        document.getElementById('step-indicator-' + paso).classList.remove('completado');
    }

    pasoActual = paso;
    document.getElementById('paso-' + pasoActual).classList.add('activo');
    document.getElementById('step-indicator-' + pasoActual).classList.add('activo');
    document.getElementById('step-indicator-' + pasoActual).classList.remove('completado');

    // Actualizar resumen en paso 3
    if (paso === 3) actualizarResumen();

    window.scrollTo({top: 0, behavior: 'smooth'});
}

function actualizarResumen() {
    const fuente = document.querySelector('input[name=fuente]:checked')?.value;
    const tipo   = document.querySelector('input[name=tipo_datos]:checked')?.value;
    const archivo = document.getElementById('archivo-input').files[0];

    if (fuente || tipo) {
        document.getElementById('resumen-fuente').textContent  = FUENTES_LABELS[fuente] || fuente || '—';
        document.getElementById('resumen-tipo').textContent    = TIPOS_LABELS[tipo] || tipo || '—';
        document.getElementById('resumen-archivo').textContent = archivo ? archivo.name : 'Pendiente de selección';
        document.getElementById('resumen-importacion').style.display = 'block';
    }
}

function archivoSeleccionado(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    mostrarInfoArchivo(file);
    solicitarPrevisualizacion(file);
}

function mostrarInfoArchivo(file) {
    document.getElementById('file-nombre').textContent = file.name;
    document.getElementById('file-size').textContent = formatBytes(file.size);
    document.getElementById('file-info').classList.add('visible');
    document.getElementById('alerta-archivo').style.display = 'none';
    actualizarResumen();
}

function limpiarArchivo() {
    document.getElementById('archivo-input').value = '';
    document.getElementById('file-info').classList.remove('visible');
    document.getElementById('preview-container').classList.remove('visible');
    document.getElementById('resumen-importacion').style.display = 'none';
}

function soltar(event) {
    event.preventDefault();
    document.getElementById('dropzone').classList.remove('drag-over');
    const file = event.dataTransfer.files[0];
    if (!file) return;

    // Inyectar en el input
    const dt = new DataTransfer();
    dt.items.add(file);
    const input = document.getElementById('archivo-input');
    input.files = dt.files;

    mostrarInfoArchivo(file);
    solicitarPrevisualizacion(file);
}

function solicitarPrevisualizacion(file) {
    const ext = file.name.split('.').pop().toLowerCase();
    if (!['csv', 'txt', 'xlsx', 'xls'].includes(ext)) return;

    const fuente = document.querySelector('input[name=fuente]:checked')?.value || 'csv_generico';
    const formData = new FormData();
    formData.append('archivo', file);
    formData.append('fuente', fuente);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content
        || '{{ csrf_token() }}');

    fetch('{{ route("dev.importacion.previsualizar") }}', {
        method: 'POST',
        body: formData,
    })
    .then(r => r.json())
    .then(data => {
        if (data.headers && data.headers.length > 0) {
            renderizarPreview(data.headers, data.filas);
        }
    })
    .catch(() => {});
}

function renderizarPreview(headers, filas) {
    const thead = document.getElementById('preview-headers');
    const tbody = document.getElementById('preview-body');

    thead.innerHTML = '<tr>' + headers.map(h => `<th>${escHtml(h)}</th>`).join('') + '</tr>';
    tbody.innerHTML = filas.map(fila =>
        '<tr>' + fila.map(cell => `<td>${escHtml(cell)}</td>`).join('') + '</tr>'
    ).join('');

    document.getElementById('preview-count').textContent = `${filas.length} filas mostradas`;
    document.getElementById('preview-container').classList.add('visible');
}

function formatBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Validar antes de enviar
document.getElementById('form-importacion').addEventListener('submit', function(e) {
    const archivo = document.getElementById('archivo-input').files[0];
    if (!archivo) {
        e.preventDefault();
        document.getElementById('alerta-archivo').style.display = 'block';
        return;
    }
    const btn = document.getElementById('btn-importar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Subiendo...';
});
</script>
@endpush

@endsection
