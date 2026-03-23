<?php

namespace App\Http\ViewComposers;

use App\Models\Configuracion;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

/**
 * ConfiguracionComposer
 * 
 * Inyecta automáticamente la variable $config y $nombreConsultorio
 * en TODAS las vistas del sistema.
 * 
 * Esto garantiza que ninguna vista tenga el nombre hardcodeado:
 * siempre se obtiene desde la base de datos.
 * 
 * Se registra en AppServiceProvider.
 */
class ConfiguracionComposer
{
    /**
     * Construye la variable $config para la vista.
     * Usa caché de 60 minutos para no consultar la BD en cada request.
     */
    public function compose(View $view): void
    {
        $config = Cache::remember('configuracion_consultorio', 3600, function () {
            return Configuracion::obtener();
        });

        $view->with('config', $config);
        $view->with('nombreConsultorio', $config->nombre_consultorio);
        $view->with('slogan', $config->slogan);
    }
}