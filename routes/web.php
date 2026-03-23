<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Rutas Web — Sistema Tatiana Velandia Odontología
|--------------------------------------------------------------------------
|
| Todas las rutas del sistema están protegidas por autenticación.
| Los roles se gestionan con Spatie Permission.
|
*/

// ─── Raíz: redirige al dashboard si está autenticado ───
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ─── Rutas de autenticación (Laravel Breeze) ───
require __DIR__ . '/auth.php';

// ─── Rutas protegidas (requieren autenticación) ───
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Módulo: Pacientes ──────────────────────────────────
    Route::resource('pacientes', \App\Http\Controllers\PacienteController::class);
    Route::patch('pacientes/{id}/activar', [\App\Http\Controllers\PacienteController::class, 'activar'])->name('pacientes.activar');
    Route::delete('pacientes/{id}/eliminar', [\App\Http\Controllers\PacienteController::class, 'eliminar'])->name('pacientes.eliminar');

    // ── Módulo: Citas y Agenda ─────────────────────────────
    Route::resource('citas', \App\Http\Controllers\CitaController::class);
    Route::post('citas/{cita}/confirmar', [\App\Http\Controllers\CitaController::class, 'confirmar'])->name('citas.confirmar');
    Route::post('citas/{cita}/cancelar', [\App\Http\Controllers\CitaController::class, 'cancelar'])->name('citas.cancelar');
    Route::get('agenda', [\App\Http\Controllers\CitaController::class, 'agenda'])->name('citas.agenda');

    // ── Módulo: Historia Clínica ───────────────────────────
    Route::resource('historias', \App\Http\Controllers\HistoriaClinicaController::class);
    Route::get('historias/{historia}/firmar', [\App\Http\Controllers\HistoriaClinicaController::class, 'firmarVista'])->name('historias.firmar.vista');
    Route::post('historias/{historia}/firmar', [\App\Http\Controllers\HistoriaClinicaController::class, 'firmar'])->name('historias.firmar');
    Route::get('historias/{historia}/pdf', [\App\Http\Controllers\HistoriaClinicaController::class, 'pdf'])->name('historias.pdf');
    Route::get('historias/{historia}/correccion', [\App\Http\Controllers\HistoriaClinicaController::class, 'correccionVista'])->name('historias.correccion.vista');
    Route::post('historias/{historia}/correccion', [\App\Http\Controllers\HistoriaClinicaController::class, 'correccion'])->name('historias.correccion');
    Route::get('historias/correcciones/{correccion}/firmar', [\App\Http\Controllers\HistoriaClinicaController::class, 'firmarCorreccionVista'])->name('historias.correccion.firmar.vista');
    Route::post('historias/correcciones/{correccion}/firmar', [\App\Http\Controllers\HistoriaClinicaController::class, 'firmarCorreccion'])->name('historias.correccion.firmar');

    // ── Módulo: Evoluciones ────────────────────────────────
    Route::resource('evoluciones', \App\Http\Controllers\EvolucionController::class);
    Route::get('evoluciones/{evolucion}/firmar', [\App\Http\Controllers\EvolucionController::class, 'firmarVista'])->name('evoluciones.firmar.vista');
    Route::post('evoluciones/{evolucion}/firmar', [\App\Http\Controllers\EvolucionController::class, 'firmar'])->name('evoluciones.firmar');
    Route::get('evoluciones/{evolucion}/pdf', [\App\Http\Controllers\EvolucionController::class, 'pdf'])->name('evoluciones.pdf');
    Route::get('evoluciones/{evolucion}/correccion', [\App\Http\Controllers\EvolucionController::class, 'correccionVista'])->name('evoluciones.correccion.vista');
    Route::post('evoluciones/{evolucion}/correccion', [\App\Http\Controllers\EvolucionController::class, 'correccion'])->name('evoluciones.correccion');
    Route::get('evoluciones/correcciones/{correccion}/firmar', [\App\Http\Controllers\EvolucionController::class, 'firmarCorreccionVista'])->name('evoluciones.correccion.firmar.vista');
    Route::post('evoluciones/correcciones/{correccion}/firmar', [\App\Http\Controllers\EvolucionController::class, 'firmarCorreccion'])->name('evoluciones.correccion.firmar');

    // ── Módulo: Valoraciones ───────────────────────────────
    Route::resource('valoraciones', \App\Http\Controllers\ValoracionController::class);

    // ── Módulo: Presupuestos ───────────────────────────────
    Route::resource('presupuestos', \App\Http\Controllers\PresupuestoController::class);
    Route::get('presupuestos/{presupuesto}/pdf', [\App\Http\Controllers\PresupuestoController::class, 'pdf'])->name('presupuestos.pdf');
    Route::post('presupuestos/{presupuesto}/enviar', [\App\Http\Controllers\PresupuestoController::class, 'enviar'])->name('presupuestos.enviar');
    Route::post('presupuestos/{presupuesto}/aprobar', [\App\Http\Controllers\PresupuestoController::class, 'aprobar'])->name('presupuestos.aprobar');
    Route::post('presupuestos/{presupuesto}/rechazar', [\App\Http\Controllers\PresupuestoController::class, 'rechazar'])->name('presupuestos.rechazar');
    Route::post('presupuestos/{presupuesto}/firmar', [\App\Http\Controllers\PresupuestoController::class, 'firmar'])->name('presupuestos.firmar');

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
                        'id' => $p->id,
                        'numero' => $p->numero_formateado,
                        'total' => $p->total,
                        'total_formateado' => '$ ' . number_format($p->total, 0, ',', '.'),
                        'saldo_pendiente' => $p->tratamiento->saldo_pendiente ?? 0,
                        'tratamiento_id' => $p->tratamiento_id,
                    ];
                })
        );
    })->name('api.paciente.presupuestos');

    // ── Módulo: Tratamientos ───────────────────────────────
    Route::resource('tratamientos', \App\Http\Controllers\TratamientoController::class);
    Route::post('tratamientos/{tratamiento}/completar', [\App\Http\Controllers\TratamientoController::class, 'completar'])->name('tratamientos.completar');
    Route::post('tratamientos/{tratamiento}/cancelar', [\App\Http\Controllers\TratamientoController::class, 'cancelar'])->name('tratamientos.cancelar');

    // ── Módulo: Pagos ──────────────────────────────────────
    Route::resource('pagos', \App\Http\Controllers\PagoController::class);
    Route::get('pagos/{pago}/recibo', [\App\Http\Controllers\PagoController::class, 'recibo'])->name('pagos.recibo');
    Route::post('pagos/{pago}/anular', [\App\Http\Controllers\PagoController::class, 'anular'])->name('pagos.anular');

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

    // ── Módulo: Valoraciones ──────────────────────────────
    Route::resource('valoraciones', \App\Http\Controllers\ValoracionController::class);
    Route::post('valoraciones/{valoracion}/completar', [\App\Http\Controllers\ValoracionController::class, 'completar'])->name('valoraciones.completar');
    Route::post('valoraciones/{valoracion}/generar-presupuesto', [\App\Http\Controllers\ValoracionController::class, 'generarPresupuesto'])->name('valoraciones.generar-presupuesto');

    // ── Módulo: Imágenes Clínicas ──────────────────────────
    Route::get('imagenes/galeria/{paciente}', [\App\Http\Controllers\ImagenClinicaController::class, 'galeria'])->name('imagenes.galeria');
    Route::get('imagenes/comparativo/{paciente}', [\App\Http\Controllers\ImagenClinicaController::class, 'comparativo'])->name('imagenes.comparativo');
    Route::post('imagenes/capturar', [\App\Http\Controllers\ImagenClinicaController::class, 'capturar'])->name('imagenes.capturar');
    Route::resource('imagenes', \App\Http\Controllers\ImagenClinicaController::class);

    // ── Módulo: Consentimientos ────────────────────────────
    Route::resource('consentimientos', \App\Http\Controllers\ConsentimientoController::class);
    Route::get('consentimientos/{consentimiento}/pdf', [\App\Http\Controllers\ConsentimientoController::class, 'pdf'])->name('consentimientos.pdf');
    Route::post('consentimientos/{consentimiento}/firmar', [\App\Http\Controllers\ConsentimientoController::class, 'firmar'])->name('consentimientos.firmar');

    // ── Módulo: Laboratorio ────────────────────────────────
    Route::resource('laboratorio', \App\Http\Controllers\LaboratorioController::class);
    Route::post('laboratorio/{orden}/enviar', [\App\Http\Controllers\LaboratorioController::class, 'enviar'])->name('laboratorio.enviar');
    Route::post('laboratorio/{orden}/recibir', [\App\Http\Controllers\LaboratorioController::class, 'recibirTrabajo'])->name('laboratorio.recibir');
    Route::post('laboratorio/{orden}/instalar', [\App\Http\Controllers\LaboratorioController::class, 'instalar'])->name('laboratorio.instalar');
    Route::post('laboratorio/{orden}/cancelar', [\App\Http\Controllers\LaboratorioController::class, 'cancelar'])->name('laboratorio.cancelar');
    Route::get('laboratorio/{orden}/pdf',       [\App\Http\Controllers\LaboratorioController::class, 'pdf'])->name('laboratorio.pdf');

    // ── Gestión de laboratorios ────────────────────────────
    Route::resource('gestion-laboratorios', \App\Http\Controllers\GestionLaboratorioController::class);

    // ── Módulo: Inventario (solo admin/doctora) ────────────
    Route::middleware('role:administrador|doctora')->group(function () {
        Route::resource('inventario', \App\Http\Controllers\InventarioController::class);
        Route::post('inventario/{material}/entrada', [\App\Http\Controllers\InventarioController::class, 'entrada'])->name('inventario.entrada');
        Route::post('inventario/{material}/ajuste', [\App\Http\Controllers\InventarioController::class, 'ajuste'])->name('inventario.ajuste');
        Route::patch('inventario/{material}/activar', [\App\Http\Controllers\InventarioController::class, 'activar'])->name('inventario.activar');
        Route::resource('inventario-categorias', \App\Http\Controllers\CategoriaInventarioController::class);

        // ── Módulo: Proveedores ────────────────────────────────
        Route::get('proveedores/comparar-precios', [\App\Http\Controllers\ProveedorController::class, 'comparar'])->name('proveedores.comparar');
        Route::resource('proveedores', \App\Http\Controllers\ProveedorController::class);

        // ── Módulo: Compras ────────────────────────────────────
        Route::resource('compras', \App\Http\Controllers\CompraController::class);
        Route::post('compras/{compra}/pagar', [\App\Http\Controllers\CompraController::class, 'pagar'])->name('compras.pagar');
        Route::post('compras/{compra}/cancelar', [\App\Http\Controllers\CompraController::class, 'cancelar'])->name('compras.cancelar');

        // ── API: precios de material por proveedor ─────────────
        Route::get('api/materiales/{material}/precios', function (\App\Models\Material $material) {
            return response()->json([
                'precio_promedio' => $material->precioPromedio(),
                'ultimo_precio' => $material->ultimoPrecio(),
                'historial' => $material->itemsCompra()
                    ->with('compra.proveedor')
                    ->whereHas('compra', fn($q) => $q->where('estado', 'pagada'))
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get()
                    ->map(fn($item) => [
                        'fecha' => $item->compra->fecha_compra->format('d/m/Y'),
                        'proveedor' => $item->compra->proveedor->nombre,
                        'precio' => $item->precio_unitario,
                        'cantidad' => $item->cantidad,
                    ]),
            ]);
        })->name('api.material.precios');

        Route::get('reportes', [\App\Http\Controllers\ReporteController::class, 'index'])->name('reportes.index');
        Route::get('reportes/ingresos', [\App\Http\Controllers\ReporteController::class, 'ingresos'])->name('reportes.ingresos');
        Route::get('reportes/pacientes', [\App\Http\Controllers\ReporteController::class, 'pacientes'])->name('reportes.pacientes');
        Route::get('reportes/citas', [\App\Http\Controllers\ReporteController::class, 'citas'])->name('reportes.citas');
        Route::get('reportes/exportar-ingresos', [\App\Http\Controllers\ReporteController::class, 'exportarIngresos'])->name('reportes.exportar-ingresos');
        Route::get('reportes/exportar-pacientes', [\App\Http\Controllers\ReporteController::class, 'exportarPacientes'])->name('reportes.exportar-pacientes');
    });

    // ── Módulo: Usuarios (solo admin/doctora) ──────────────
    Route::middleware('role:administrador|doctora')->group(function () {
        Route::resource('usuarios', \App\Http\Controllers\UsuarioController::class);
    });

    // ── Módulo: Configuración (solo admin/doctora) ─────────
    Route::middleware('role:administrador|doctora')->group(function () {
        Route::get('configuracion', [\App\Http\Controllers\ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::put('configuracion', [\App\Http\Controllers\ConfiguracionController::class, 'update'])->name('configuracion.update');
        Route::post('configuracion/logo', [\App\Http\Controllers\ConfiguracionController::class, 'actualizarLogo'])->name('configuracion.logo');
        Route::post('configuracion/firma', [\App\Http\Controllers\ConfiguracionController::class, 'actualizarFirma'])->name('configuracion.firma');
        Route::delete('configuracion/firma', [\App\Http\Controllers\ConfiguracionController::class, 'eliminarFirma'])->name('configuracion.firma.eliminar');
    });

    // ── Perfil del usuario autenticado ────────────────────
    Route::get('perfil', [\App\Http\Controllers\PerfilController::class, 'index'])->name('perfil.index');
    Route::put('perfil', [\App\Http\Controllers\PerfilController::class, 'update'])->name('perfil.update');





});