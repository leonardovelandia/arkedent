<?php

namespace App\Providers;

use App\Listeners\LockAccountAfterFailedLogins;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Http\ViewComposers\ConfiguracionComposer;
use App\Models\Configuracion;

/**
 * AppServiceProvider
 * 
 * Registra el View Composer global que inyecta $config
 * en todas las vistas del sistema.
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ─────────────────────────────────────────────────────────
        // Listener: bloquear cuenta tras 5 intentos fallidos
        // ─────────────────────────────────────────────────────────
        Event::listen(Failed::class, LockAccountAfterFailedLogins::class);

        // ─────────────────────────────────────────────────────────
        // Helper de módulos disponible globalmente
        // ─────────────────────────────────────────────────────────
        require_once app_path('Helpers/ModulosHelper.php');

        // ─────────────────────────────────────────────────────────
        // Directivas Blade para verificar módulos en vistas
        // Uso: @modulo('nombre') ... @endmodulo
        // ─────────────────────────────────────────────────────────
        \Illuminate\Support\Facades\Blade::directive('modulo', function ($expression) {
            return "<?php if(\App\Helpers\ModulosHelper::activo({$expression})): ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('endmodulo', function () {
            return '<?php endif; ?>';
        });

        // ─────────────────────────────────────────────────────────
        // View Composer Global
        // Inyecta $config y $nombreConsultorio en TODAS las vistas.
        // ─────────────────────────────────────────────────────────
        View::composer('*', ConfiguracionComposer::class);

        // Comparte módulos activos con todas las vistas
        View::composer('*', function ($view) {
            $view->with('modulosActivos', \App\Helpers\ModulosHelper::modulosActivos());
        });

        // ─────────────────────────────────────────────────────────
        // Nombre del remitente de correo dinámico
        // Toma el nombre del consultorio desde la BD (con caché)
        // para que el "From:" del correo refleje el nombre real.
        // ─────────────────────────────────────────────────────────
        try {
            $cfg = Cache::remember('configuracion_consultorio', 3600, fn () => Configuracion::obtener());
            if ($cfg && $cfg->nombre_consultorio) {
                // Sincroniza TODAS las fuentes con el nombre del consultorio en BD:
                // app.name  → encabezado del template de correo
                // mail.from.name → remitente del correo
                config([
                    'app.name'       => $cfg->nombre_consultorio,
                    'mail.from.name' => $cfg->nombre_consultorio,
                ]);
            }
        } catch (\Throwable $e) {
            // BD no disponible (migraciones, artisan, etc.) → usa valores del .env
        }
    }
}