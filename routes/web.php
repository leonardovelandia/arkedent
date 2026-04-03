<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Rutas Web — Sistema ODONTREX
|--------------------------------------------------------------------------
|
| Todas las rutas del sistema están protegidas por autenticación.
| Los roles se gestionan con Spatie Permission.
|
*/

// ── Verificación pública de documentos firmados (sin autenticación) ──
Route::get('/verificar/{token}', function (string $token) {
    $modelos = [
        \App\Models\Consentimiento::class,
        \App\Models\AutorizacionDatos::class,
        \App\Models\Presupuesto::class,
        \App\Models\Evolucion::class,
    ];

    $documento     = null;
    $tipoDocumento = null;

    foreach ($modelos as $modelo) {
        try {
            $encontrado = $modelo::where('firma_verificacion_token', $token)->first();
            if ($encontrado) {
                $documento     = $encontrado;
                $tipoDocumento = class_basename($modelo);
                break;
            }
        } catch (\Exception $e) {
            continue;
        }
    }

    if (!$documento) {
        return view('verificacion.invalido', ['token' => $token]);
    }

    $config = \App\Models\Configuracion::obtener();

    return view('verificacion.valido', [
        'documento'   => $documento,
        'tipo'        => $tipoDocumento,
        'config'      => $config,
        'paciente'    => $documento->paciente,
        'timestamp'   => $documento->firma_timestamp,
        'ip'          => $documento->ip_firma,
        'dispositivo' => $documento->firma_dispositivo,
        'navegador'   => $documento->firma_navegador,
        'hash'        => $documento->documento_hash,
        'token'       => $token,
    ]);
})->name('verificar.documento');

// ─── Raíz: redirige al dashboard si está autenticado ───
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ─── Rutas de autenticación (Laravel Breeze) ───
require __DIR__ . '/auth.php';

// ─── Panel de desarrollo — rutas públicas de auth ───

Route::get('/dev/login', function () {
    $redirigir = request('redirigir', '/dev/modulos');
    return view('dev.login', compact('redirigir'));
})->middleware(['auth', 'role:administrador'])->name('dev.auth.form');

Route::post('/dev/auth', function (\Illuminate\Http\Request $request) {
    $redirigir   = $request->input('redirigir', '/dev');
    $passwordDev = env('DEV_PASSWORD', 'arkedent2024');
    if ($request->input('dev_password') === $passwordDev) {
        return redirect($redirigir)
            ->cookie('dev_panel_auth', hash_hmac('sha256', $passwordDev, config('app.key')), 60 * 8);
    }
    return redirect()->route('dev.auth.form', ['redirigir' => $redirigir])
        ->with('error', 'Contraseña incorrecta. Intenta de nuevo.');
})->middleware(['auth', 'role:administrador'])->name('dev.auth.login');

Route::get('/dev/logout', function () {
    return redirect()->route('dashboard')
        ->cookie(\Cookie::forget('dev_panel_auth'));
})->middleware(['auth'])->name('dev.auth.logout');

// ─── Panel dev: protegido por sesión Laravel + rol administrador + cookie dev ─
Route::middleware(['auth', 'role:administrador', 'dev.auth'])->group(function () {
    Route::get('/dev', function () {
        return redirect()->route('dev.importacion.index');
    })->name('dev.home');
    Route::get('/dev/modulos', [\App\Http\Controllers\DevController::class, 'modulos'])->name('dev.modulos');
    Route::post('/dev/modulos/guardar', [\App\Http\Controllers\DevController::class, 'guardarModulos'])->name('dev.modulos.guardar');
    Route::get('/dev/password', [\App\Http\Controllers\DevController::class, 'passwordForm'])->name('dev.password');
    Route::post('/dev/password', [\App\Http\Controllers\DevController::class, 'passwordCambiar'])->name('dev.password.cambiar');
    Route::get('/dev/importacion/plantillas', [\App\Http\Controllers\ImportacionController::class, 'plantillas'])->name('dev.importacion.plantillas');
    Route::get('/dev/importacion/plantilla/{tipo}', [\App\Http\Controllers\ImportacionController::class, 'descargarPlantilla'])->name('dev.importacion.plantilla');
    Route::post('/dev/importacion/previsualizar', [\App\Http\Controllers\ImportacionController::class, 'previsualizar'])->name('dev.importacion.previsualizar');
    Route::get('/dev/importacion', [\App\Http\Controllers\ImportacionController::class, 'index'])->name('dev.importacion.index');
    Route::get('/dev/importacion/crear', [\App\Http\Controllers\ImportacionController::class, 'create'])->name('dev.importacion.create');
    Route::post('/dev/importacion', [\App\Http\Controllers\ImportacionController::class, 'store'])->name('dev.importacion.store');
    Route::get('/dev/importacion/{importacion}', [\App\Http\Controllers\ImportacionController::class, 'show'])->name('dev.importacion.show');
    Route::post('/dev/importacion/{importacion}/procesar', [\App\Http\Controllers\ImportacionController::class, 'procesar'])->name('dev.importacion.procesar');
    Route::post('/dev/importacion/{importacion}/revertir', [\App\Http\Controllers\ImportacionController::class, 'revertir'])->name('dev.importacion.revertir');
    Route::delete('/dev/importacion/{importacion}', [\App\Http\Controllers\ImportacionController::class, 'destroy'])->name('dev.importacion.destroy');
});

// ─── Webhook WhatsApp (público, sin auth, sin CSRF) ───────────
Route::post('/webhook/whatsapp', [\App\Http\Controllers\WebhookWhatsappController::class, 'handle'])
    ->name('webhook.whatsapp');

// ─── Rutas protegidas (requieren autenticación) ───
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Módulo: Pacientes ──────────────────────────────────
    Route::middleware(['modulo:pacientes'])->group(function () {
        Route::resource('pacientes', \App\Http\Controllers\PacienteController::class);
        Route::patch('pacientes/{id}/activar', [\App\Http\Controllers\PacienteController::class, 'activar'])->name('pacientes.activar');
        Route::delete('pacientes/{id}/eliminar', [\App\Http\Controllers\PacienteController::class, 'eliminar'])->name('pacientes.eliminar');
    });

    // ── Módulo: Citas y Agenda ─────────────────────────────
    Route::middleware(['modulo:citas'])->group(function () {
        Route::resource('citas', \App\Http\Controllers\CitaController::class);
        Route::post('citas/{cita}/confirmar', [\App\Http\Controllers\CitaController::class, 'confirmar'])->name('citas.confirmar');
        Route::post('citas/{cita}/cancelar', [\App\Http\Controllers\CitaController::class, 'cancelar'])->name('citas.cancelar');
        Route::patch('citas/{cita}/estado', [\App\Http\Controllers\CitaController::class, 'cambiarEstado'])->name('citas.cambiarEstado');
        Route::get('agenda', [\App\Http\Controllers\CitaController::class, 'agenda'])->name('citas.agenda');
    });

    // ── Módulo: Historia Clínica ───────────────────────────
    Route::middleware(['modulo:historia_clinica'])->group(function () {
        Route::resource('historias', \App\Http\Controllers\HistoriaClinicaController::class);
        Route::get('historias/{historia}/firmar', [\App\Http\Controllers\HistoriaClinicaController::class, 'firmarVista'])->name('historias.firmar.vista');
        Route::post('historias/{historia}/firmar', [\App\Http\Controllers\HistoriaClinicaController::class, 'firmar'])->name('historias.firmar');
        Route::get('historias/{historia}/pdf', [\App\Http\Controllers\HistoriaClinicaController::class, 'pdf'])->name('historias.pdf');
        Route::get('historias/{historia}/correccion', [\App\Http\Controllers\HistoriaClinicaController::class, 'correccionVista'])->name('historias.correccion.vista');
        Route::post('historias/{historia}/correccion', [\App\Http\Controllers\HistoriaClinicaController::class, 'correccion'])->name('historias.correccion');
        Route::get('historias/correcciones/{correccion}/firmar', [\App\Http\Controllers\HistoriaClinicaController::class, 'firmarCorreccionVista'])->name('historias.correccion.firmar.vista');
        Route::post('historias/correcciones/{correccion}/firmar', [\App\Http\Controllers\HistoriaClinicaController::class, 'firmarCorreccion'])->name('historias.correccion.firmar');
    });

    // ── Módulo: Evoluciones ────────────────────────────────
    Route::middleware(['modulo:evoluciones'])->group(function () {
        Route::resource('evoluciones', \App\Http\Controllers\EvolucionController::class);
        Route::get('evoluciones/{evolucion}/firmar', [\App\Http\Controllers\EvolucionController::class, 'firmarVista'])->name('evoluciones.firmar.vista');
        Route::post('evoluciones/{evolucion}/firmar', [\App\Http\Controllers\EvolucionController::class, 'firmar'])->name('evoluciones.firmar');
        Route::get('evoluciones/{evolucion}/pdf', [\App\Http\Controllers\EvolucionController::class, 'pdf'])->name('evoluciones.pdf');
        Route::get('evoluciones/{evolucion}/correccion', [\App\Http\Controllers\EvolucionController::class, 'correccionVista'])->name('evoluciones.correccion.vista');
        Route::post('evoluciones/{evolucion}/correccion', [\App\Http\Controllers\EvolucionController::class, 'correccion'])->name('evoluciones.correccion');
        Route::get('evoluciones/correcciones/{correccion}/firmar', [\App\Http\Controllers\EvolucionController::class, 'firmarCorreccionVista'])->name('evoluciones.correccion.firmar.vista');
        Route::post('evoluciones/correcciones/{correccion}/firmar', [\App\Http\Controllers\EvolucionController::class, 'firmarCorreccion'])->name('evoluciones.correccion.firmar');
    });

    // ── Módulo: Valoraciones ───────────────────────────────
    Route::middleware(['modulo:valoraciones'])->group(function () {
        Route::resource('valoraciones', \App\Http\Controllers\ValoracionController::class);
        Route::post('valoraciones/{valoracion}/completar', [\App\Http\Controllers\ValoracionController::class, 'completar'])->name('valoraciones.completar');
        Route::post('valoraciones/{valoracion}/generar-presupuesto', [\App\Http\Controllers\ValoracionController::class, 'generarPresupuesto'])->name('valoraciones.generar-presupuesto');
        Route::get('valoraciones/{valoracion}/pdf', [\App\Http\Controllers\ValoracionController::class, 'pdf'])->name('valoraciones.pdf');
    });

    // ── Módulo: Presupuestos ───────────────────────────────
    Route::middleware(['modulo:presupuestos'])->group(function () {
        Route::resource('presupuestos', \App\Http\Controllers\PresupuestoController::class);
        Route::get('presupuestos/{presupuesto}/pdf', [\App\Http\Controllers\PresupuestoController::class, 'pdf'])->name('presupuestos.pdf');
        Route::post('presupuestos/{presupuesto}/enviar', [\App\Http\Controllers\PresupuestoController::class, 'enviar'])->name('presupuestos.enviar');
        Route::post('presupuestos/{presupuesto}/aprobar', [\App\Http\Controllers\PresupuestoController::class, 'aprobar'])->name('presupuestos.aprobar');
        Route::post('presupuestos/{presupuesto}/rechazar', [\App\Http\Controllers\PresupuestoController::class, 'rechazar'])->name('presupuestos.rechazar');
        Route::post('presupuestos/{presupuesto}/firmar', [\App\Http\Controllers\PresupuestoController::class, 'firmar'])->name('presupuestos.firmar');
    });

    // API: presupuestos aprobados de un paciente
    Route::get('api/pacientes/{paciente}/presupuestos-aprobados', function (\App\Models\Paciente $paciente) {
        return response()->json(
            $paciente->presupuestos()
                ->where('estado', 'aprobado')
                ->where('activo', true)
                ->with('tratamiento')
                ->get()
                ->map(function ($p) {
                    return [
                        'id'                 => $p->id,
                        'numero'             => $p->numero_formateado,
                        'total'              => $p->total,
                        'total_formateado'   => '$ ' . number_format($p->total, 0, ',', '.'),
                        'saldo_pendiente'    => $p->tratamiento->saldo_pendiente ?? 0,
                        'tratamiento_id'     => $p->tratamiento_id,
                    ];
                })
        );
    })->name('api.paciente.presupuestos');

    // ── Módulo: Tratamientos (parte del módulo pagos) ──────
    Route::middleware(['modulo:pagos'])->group(function () {
        Route::resource('tratamientos', \App\Http\Controllers\TratamientoController::class);
        Route::post('tratamientos/{tratamiento}/completar', [\App\Http\Controllers\TratamientoController::class, 'completar'])->name('tratamientos.completar');
        Route::post('tratamientos/{tratamiento}/cancelar', [\App\Http\Controllers\TratamientoController::class, 'cancelar'])->name('tratamientos.cancelar');

        Route::resource('pagos', \App\Http\Controllers\PagoController::class);
        Route::get('pagos/{pago}/recibo', [\App\Http\Controllers\PagoController::class, 'recibo'])->name('pagos.recibo');
        Route::post('pagos/{pago}/anular', [\App\Http\Controllers\PagoController::class, 'anular'])->name('pagos.anular');
    });

    // ── API interna ────────────────────────────────────────
    Route::get('api/citas/disponibilidad', [\App\Http\Controllers\CitaController::class, 'verificarDisponibilidad'])->name('api.citas.disponibilidad');

    Route::get('api/pacientes/{paciente}/tratamientos', function (\App\Models\Paciente $paciente) {
        return response()->json(
            $paciente->tratamientos()
                ->where('estado', 'activo')
                ->where('saldo_pendiente', '>', 0)
                ->select('id', 'nombre', 'valor_total', 'saldo_pendiente')
                ->get()
        );
    })->name('api.paciente.tratamientos');

    // ── Módulo: Imágenes Clínicas ──────────────────────────
    Route::middleware(['modulo:imagenes'])->group(function () {
        Route::get('imagenes/galeria/{paciente}', [\App\Http\Controllers\ImagenClinicaController::class, 'galeria'])->name('imagenes.galeria');
        Route::get('imagenes/comparativo/{paciente}', [\App\Http\Controllers\ImagenClinicaController::class, 'comparativo'])->name('imagenes.comparativo');
        Route::post('imagenes/comparativo/asignar', [\App\Http\Controllers\ImagenClinicaController::class, 'asignarComparativo'])->name('imagenes.comparativo.asignar');
        Route::post('imagenes/capturar', [\App\Http\Controllers\ImagenClinicaController::class, 'capturar'])->name('imagenes.capturar');
        Route::resource('imagenes', \App\Http\Controllers\ImagenClinicaController::class);
    });

    // ── Módulo: Consentimientos ────────────────────────────
    Route::middleware(['modulo:consentimientos'])->group(function () {
        Route::resource('consentimientos', \App\Http\Controllers\ConsentimientoController::class);
        Route::get('consentimientos/{consentimiento}/pdf', [\App\Http\Controllers\ConsentimientoController::class, 'pdf'])->name('consentimientos.pdf');
        Route::post('consentimientos/{consentimiento}/firmar', [\App\Http\Controllers\ConsentimientoController::class, 'firmar'])->name('consentimientos.firmar');
        Route::resource('plantillas-consentimiento', \App\Http\Controllers\PlantillaConsentimientoController::class)
             ->except(['create', 'show']);
    });

    // ── Módulo: Laboratorio ────────────────────────────────
    Route::middleware(['modulo:laboratorio'])->group(function () {
        Route::resource('laboratorio', \App\Http\Controllers\LaboratorioController::class);
        Route::post('laboratorio/{orden}/enviar', [\App\Http\Controllers\LaboratorioController::class, 'enviar'])->name('laboratorio.enviar');
        Route::post('laboratorio/{orden}/recibir', [\App\Http\Controllers\LaboratorioController::class, 'recibirTrabajo'])->name('laboratorio.recibir');
        Route::post('laboratorio/{orden}/instalar', [\App\Http\Controllers\LaboratorioController::class, 'instalar'])->name('laboratorio.instalar');
        Route::post('laboratorio/{orden}/cancelar', [\App\Http\Controllers\LaboratorioController::class, 'cancelar'])->name('laboratorio.cancelar');
        Route::get('laboratorio/{orden}/pdf', [\App\Http\Controllers\LaboratorioController::class, 'pdf'])->name('laboratorio.pdf');
        Route::resource('gestion-laboratorios', \App\Http\Controllers\GestionLaboratorioController::class);
    });

    // ── Módulo: Inventario, Proveedores, Reportes (solo admin/doctora) ──
    Route::middleware('role:administrador|doctora')->group(function () {

        Route::middleware(['modulo:inventario'])->group(function () {
            Route::resource('inventario', \App\Http\Controllers\InventarioController::class);
            Route::post('inventario/{material}/entrada', [\App\Http\Controllers\InventarioController::class, 'entrada'])->name('inventario.entrada');
            Route::post('inventario/{material}/ajuste', [\App\Http\Controllers\InventarioController::class, 'ajuste'])->name('inventario.ajuste');
            Route::patch('inventario/{material}/activar', [\App\Http\Controllers\InventarioController::class, 'activar'])->name('inventario.activar');
            Route::resource('inventario-categorias', \App\Http\Controllers\CategoriaInventarioController::class);
        });

        Route::middleware(['modulo:proveedores'])->group(function () {
            Route::get('proveedores/comparar-precios', [\App\Http\Controllers\ProveedorController::class, 'comparar'])->name('proveedores.comparar');
            Route::resource('proveedores', \App\Http\Controllers\ProveedorController::class);
            Route::resource('compras', \App\Http\Controllers\CompraController::class);
            Route::post('compras/{compra}/pagar', [\App\Http\Controllers\CompraController::class, 'pagar'])->name('compras.pagar');
            Route::post('compras/{compra}/cancelar', [\App\Http\Controllers\CompraController::class, 'cancelar'])->name('compras.cancelar');

            // API: precios de material por proveedor
            Route::get('api/materiales/{material}/precios', function (\App\Models\Material $material) {
                return response()->json([
                    'precio_promedio' => $material->precioPromedio(),
                    'ultimo_precio'   => $material->ultimoPrecio(),
                    'historial'       => $material->itemsCompra()
                        ->with('compra.proveedor')
                        ->whereHas('compra', fn($q) => $q->where('estado', 'pagada'))
                        ->orderByDesc('created_at')
                        ->limit(10)
                        ->get()
                        ->map(fn($item) => [
                            'fecha'      => $item->compra->fecha_compra->format('d/m/Y'),
                            'proveedor'  => $item->compra->proveedor->nombre,
                            'precio'     => $item->precio_unitario,
                            'cantidad'   => $item->cantidad,
                        ]),
                ]);
            })->name('api.material.precios');
        });

        Route::middleware(['modulo:reportes'])->group(function () {
            Route::get('reportes', [\App\Http\Controllers\ReporteController::class, 'index'])->name('reportes.index');
            Route::get('reportes/ingresos', [\App\Http\Controllers\ReporteController::class, 'ingresos'])->name('reportes.ingresos');
            Route::get('reportes/pacientes', [\App\Http\Controllers\ReporteController::class, 'pacientes'])->name('reportes.pacientes');
            Route::get('reportes/citas', [\App\Http\Controllers\ReporteController::class, 'citas'])->name('reportes.citas');
            Route::get('reportes/egresos', [\App\Http\Controllers\ReporteController::class, 'egresos'])->name('reportes.egresos');
            Route::get('reportes/datos-graficas', [\App\Http\Controllers\ReporteController::class, 'datosGraficas'])->name('reportes.datos-graficas');
            Route::get('reportes/exportar-ingresos', [\App\Http\Controllers\ReporteController::class, 'exportarIngresos'])->name('reportes.exportar-ingresos');
            Route::get('reportes/exportar-pacientes', [\App\Http\Controllers\ReporteController::class, 'exportarPacientes'])->name('reportes.exportar-pacientes');
        });
    });

    // ── Módulo: Libro Contable (solo admin/doctora) ───────
    Route::middleware(['role:administrador|doctora', 'modulo:libro_contable'])->group(function () {
        Route::get('libro-contable', [\App\Http\Controllers\LibroContableController::class, 'index'])->name('libro-contable.index');
        Route::get('libro-contable/estado-resultados', [\App\Http\Controllers\LibroContableController::class, 'estadoResultados'])->name('libro-contable.estado-resultados');
        Route::get('libro-contable/comparativo', [\App\Http\Controllers\LibroContableController::class, 'comparativo'])->name('libro-contable.comparativo');
        Route::get('libro-contable/exportar', [\App\Http\Controllers\LibroContableController::class, 'exportar'])->name('libro-contable.exportar');
        Route::post('libro-contable/{asiento}/excluir', [\App\Http\Controllers\LibroContableController::class, 'excluir'])->name('libro-contable.excluir');
        Route::post('libro-contable/{asiento}/incluir', [\App\Http\Controllers\LibroContableController::class, 'incluir'])->name('libro-contable.incluir');
        Route::post('libro-contable/ajuste', [\App\Http\Controllers\LibroContableController::class, 'ajuste'])->name('libro-contable.ajuste');
    });

    // ── Módulo: Egresos (solo admin/doctora) ──────────────
    Route::middleware(['role:administrador|doctora', 'modulo:egresos'])->group(function () {
        Route::resource('egresos', \App\Http\Controllers\EgresoController::class);
        Route::post('egresos/{egreso}/anular', [\App\Http\Controllers\EgresoController::class, 'anular'])->name('egresos.anular');
        Route::get('egresos-recurrentes', [\App\Http\Controllers\EgresoController::class, 'recurrentes'])->name('egresos.recurrentes');
        Route::post('egresos-recurrentes/{egreso}/registrar', [\App\Http\Controllers\EgresoController::class, 'registrarRecurrente'])->name('egresos.registrar-recurrente');
    });

    // ── Módulo: Usuarios (solo admin/doctora) ──────────────
    Route::middleware(['role:administrador|doctora', 'modulo:usuarios'])->group(function () {
        Route::resource('usuarios', \App\Http\Controllers\UsuarioController::class);
    });

    // ── Auditoría (solo administrador) ─────────────────────
    Route::middleware(['role:administrador'])->group(function () {
        Route::get('auditoria', [\App\Http\Controllers\AuditoriaController::class, 'index'])->name('auditoria.index');
        Route::get('auditoria/exportar', [\App\Http\Controllers\AuditoriaController::class, 'exportar'])->name('auditoria.exportar');
    });

    // ── Módulo: Ortodoncia ────────────────────────────────
    Route::middleware(['modulo:ortodoncia'])->group(function () {
        Route::resource('ortodoncia', \App\Http\Controllers\OrtodonciaController::class);
        Route::post('ortodoncia/{ortodoncia}/cambiar-estado', [\App\Http\Controllers\OrtodonciaController::class, 'cambiarEstado'])->name('ortodoncia.cambiar-estado');

        // Controles de ortodoncia (shallow)
        Route::get('ortodoncia/{ortodoncia}/controles/create', [\App\Http\Controllers\ControlOrtodonciaController::class, 'create'])->name('controles.create');
        Route::post('controles', [\App\Http\Controllers\ControlOrtodonciaController::class, 'store'])->name('controles.store');
        Route::get('controles/{control}', [\App\Http\Controllers\ControlOrtodonciaController::class, 'show'])->name('controles.show');
        Route::get('controles/{control}/edit', [\App\Http\Controllers\ControlOrtodonciaController::class, 'edit'])->name('controles.edit');
        Route::put('controles/{control}', [\App\Http\Controllers\ControlOrtodonciaController::class, 'update'])->name('controles.update');

        // Retención
        Route::get('ortodoncia/{ortodoncia}/retencion/create', [\App\Http\Controllers\RetencionOrtodonciaController::class, 'create'])->name('ortodoncia.retencion.create');
        Route::post('ortodoncia/{ortodoncia}/retencion', [\App\Http\Controllers\RetencionOrtodonciaController::class, 'store'])->name('ortodoncia.retencion.store');
        Route::get('retencion/{retencion}/edit', [\App\Http\Controllers\RetencionOrtodonciaController::class, 'edit'])->name('retencion.edit');
        Route::put('retencion/{retencion}', [\App\Http\Controllers\RetencionOrtodonciaController::class, 'update'])->name('retencion.update');
        Route::delete('retencion/{retencion}', [\App\Http\Controllers\RetencionOrtodonciaController::class, 'destroy'])->name('retencion.destroy');
    });

    // ── Módulo: Recetas Médicas ───────────────────────────
    Route::middleware(['modulo:recetas'])->group(function () {
        Route::resource('recetas', \App\Http\Controllers\RecetaMedicaController::class);
        Route::get('recetas/{receta}/pdf', [\App\Http\Controllers\RecetaMedicaController::class, 'pdf'])->name('recetas.pdf');
        Route::post('recetas/{receta}/firmar', [\App\Http\Controllers\RecetaMedicaController::class, 'firmar'])->name('recetas.firmar');
        Route::get('recetas/{receta}/duplicar', [\App\Http\Controllers\RecetaMedicaController::class, 'duplicar'])->name('recetas.duplicar');
    });

    // API: autocompletado para buscador de recetas
    Route::get('api/recetas/buscar', function (\Illuminate\Http\Request $request) {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) return response()->json([]);
        return response()->json(
            \App\Models\RecetaMedica::with('paciente')
                ->where('activo', true)
                ->where(function ($sq) use ($q) {
                    $sq->where('numero_receta', 'like', "%{$q}%")
                       ->orWhereHas('paciente', fn($p) => $p->whereRaw("CONCAT(nombre,' ',apellido) LIKE ?", ["%{$q}%"]));
                })
                ->orderBy('fecha', 'desc')
                ->limit(10)
                ->get()
                ->map(fn($r) => [
                    'label' => $r->paciente->nombre_completo . ' — ' . $r->numero_receta,
                    'value' => $r->paciente->nombre_completo,
                ])
                ->unique('label')->values()
        );
    })->name('api.recetas.buscar');

    // API: evoluciones de un paciente (para selector en recetas)
    Route::get('api/paciente/{paciente}/evoluciones', function (\App\Models\Paciente $paciente) {
        return response()->json(
            $paciente->evoluciones()
                ->where('activo', true)
                ->orderBy('fecha', 'desc')
                ->limit(20)
                ->get()
                ->map(fn($e) => [
                    'id'          => $e->id,
                    'fecha'       => $e->fecha->format('d/m/Y'),
                    'procedimiento' => \Illuminate\Support\Str::limit($e->procedimiento, 50),
                ])
        );
    })->name('api.paciente.evoluciones');

    // ── Módulo: Autorización de Datos Personales ──────────
    Route::get('autorizacion/create', [\App\Http\Controllers\AutorizacionDatosController::class, 'create'])->name('autorizacion.create');
    Route::post('autorizacion', [\App\Http\Controllers\AutorizacionDatosController::class, 'store'])->name('autorizacion.store');
    Route::get('autorizacion/{autorizacion}', [\App\Http\Controllers\AutorizacionDatosController::class, 'show'])->name('autorizacion.show');
    Route::post('autorizacion/{autorizacion}/firmar', [\App\Http\Controllers\AutorizacionDatosController::class, 'firmar'])->name('autorizacion.firmar');
    Route::get('autorizacion/{autorizacion}/pdf', [\App\Http\Controllers\AutorizacionDatosController::class, 'pdf'])->name('autorizacion.pdf');
    Route::delete('autorizacion/{autorizacion}', [\App\Http\Controllers\AutorizacionDatosController::class, 'destroy'])->name('autorizacion.destroy');

    // ── Módulo: Recordatorios (solo admin/doctora) ─────────
    Route::middleware(['role:administrador|doctora', 'modulo:recordatorios'])->group(function () {
        Route::get('recordatorios', [\App\Http\Controllers\RecordatorioController::class, 'index'])->name('recordatorios.index');
        Route::delete('recordatorios/{recordatorio}', [\App\Http\Controllers\RecordatorioController::class, 'destroy'])->name('recordatorios.destroy');
        Route::post('recordatorios/{recordatorio}/enviar', [\App\Http\Controllers\RecordatorioController::class, 'enviar'])->name('recordatorios.enviar');
        Route::post('recordatorios/enviar-ahora', [\App\Http\Controllers\RecordatorioController::class, 'enviarAhora'])->name('recordatorios.enviar-ahora');
        Route::get('recordatorios/configuracion', [\App\Http\Controllers\RecordatorioController::class, 'configuracion'])->name('recordatorios.configuracion');
        Route::post('recordatorios/configuracion', [\App\Http\Controllers\RecordatorioController::class, 'guardarConfiguracion'])->name('recordatorios.guardar-configuracion');
        Route::post('recordatorios/probar-email', [\App\Http\Controllers\RecordatorioController::class, 'probarEmail'])->name('recordatorios.probar-email');
        Route::post('recordatorios/probar-whatsapp', [\App\Http\Controllers\RecordatorioController::class, 'probarWhatsapp'])->name('recordatorios.probar-whatsapp');
        Route::post('recordatorios/{recordatorio}/cancelar', [\App\Http\Controllers\RecordatorioController::class, 'cancelar'])->name('recordatorios.cancelar');
    });

    // ── Módulo: Configuración (solo admin/doctora) ─────────
    Route::middleware(['role:administrador|doctora', 'modulo:configuracion'])->group(function () {
        Route::get('configuracion', [\App\Http\Controllers\ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::put('configuracion', [\App\Http\Controllers\ConfiguracionController::class, 'update'])->name('configuracion.update');
        Route::post('configuracion/logo', [\App\Http\Controllers\ConfiguracionController::class, 'actualizarLogo'])->name('configuracion.logo');
        Route::post('configuracion/firma', [\App\Http\Controllers\ConfiguracionController::class, 'actualizarFirma'])->name('configuracion.firma');
        Route::delete('configuracion/firma', [\App\Http\Controllers\ConfiguracionController::class, 'eliminarFirma'])->name('configuracion.firma.eliminar');
        // Backup manual (Res. 1995/1999)
        Route::post('configuracion/backup/ejecutar', function () {
            try {
                \Artisan::call('backup:run', ['--only-db' => true]);
                return back()->with('exito', 'Backup de base de datos realizado exitosamente.');
            } catch (\Exception $e) {
                return back()->with('error', 'Error al ejecutar backup: ' . $e->getMessage());
            }
        })->name('configuracion.backup.ejecutar');
    });

    // ── Perfil del usuario autenticado ────────────────────
    Route::get('perfil', [\App\Http\Controllers\PerfilController::class, 'index'])->name('perfil.index');
    Route::put('perfil', [\App\Http\Controllers\PerfilController::class, 'update'])->name('perfil.update');

    // ── Módulo: Periodoncia ───────────────────────────────
    Route::middleware(['modulo:periodoncia'])->group(function () {
        Route::resource('periodoncia', \App\Http\Controllers\PeriodonciaController::class);
        Route::post('periodoncia/{ficha}/cambiar-estado', [\App\Http\Controllers\PeriodonciaController::class, 'cambiarEstado'])
            ->name('periodoncia.cambiar-estado');
        Route::get('periodoncia/{ficha}/pdf', [\App\Http\Controllers\PeriodonciaController::class, 'pdf'])
            ->name('periodoncia.pdf');

        // Controles periodontales
        Route::get('periodoncia/{ficha}/controles/create', [\App\Http\Controllers\ControlPeriodontalController::class, 'create'])
            ->name('periodoncia.controles.create');
        Route::post('periodoncia-controles', [\App\Http\Controllers\ControlPeriodontalController::class, 'store'])
            ->name('periodoncia.controles.store');
        Route::get('periodoncia-controles/{control}', [\App\Http\Controllers\ControlPeriodontalController::class, 'show'])
            ->name('periodoncia.controles.show');
        Route::get('periodoncia-controles/{control}/edit', [\App\Http\Controllers\ControlPeriodontalController::class, 'edit'])
            ->name('periodoncia.controles.edit');
        Route::put('periodoncia-controles/{control}', [\App\Http\Controllers\ControlPeriodontalController::class, 'update'])
            ->name('periodoncia.controles.update');
        Route::delete('periodoncia-controles/{control}', [\App\Http\Controllers\ControlPeriodontalController::class, 'destroy'])
            ->name('periodoncia.controles.destroy');
        Route::get('periodoncia-controles/{control}/pdf', [\App\Http\Controllers\ControlPeriodontalController::class, 'pdf'])
            ->name('periodoncia.controles.pdf');
    });

    // Importación de datos movida al panel de desarrollador (/dev/importacion)

});
