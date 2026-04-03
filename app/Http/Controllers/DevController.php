<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class DevController extends Controller
{
    /** Contraseña de acceso al panel dev */
    private string $passwordDev = '********';

    public function modulos(Request $request)
    {
        return view('dev.modulos');
    }

    public function guardarModulos(Request $request)
    {
        try {
            $plan    = $request->input('plan', 'personalizado');
            $modulos = $request->input('modulos', []);

            $configPath = config_path('modulos.php');
            $contenido  = File::get($configPath);

            // Actualizar plan activo
            $contenido = preg_replace(
                "/'plan_activo'\s*=>\s*env\('PLAN_ACTIVO',\s*'[^']+'\)/",
                "'plan_activo' => env('PLAN_ACTIVO', '{$plan}')",
                $contenido
            );

            // Si es personalizado, actualizar el mapa de módulos activos
            if ($plan === 'personalizado') {
                $todosModulos = array_keys(config('modulos.catalogo'));
                foreach ($todosModulos as $modulo) {
                    $activo    = in_array($modulo, $modulos) ? 'true' : 'false';
                    $contenido = preg_replace(
                        "/'{$modulo}'\s*=>\s*(true|false)/",
                        "'{$modulo}' => {$activo}",
                        $contenido
                    );
                }
            }

            File::put($configPath, $contenido);

            // Sincronizar .env
            $this->actualizarEnv('PLAN_ACTIVO', $plan);

            // Limpiar cachés
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'mensaje' => "Plan '{$plan}' activado correctamente.",
                'comandos' => [
                    '✓ php artisan config:clear',
                    '✓ php artisan cache:clear',
                    '✓ php artisan view:clear',
                    '→ Recargando en 2 segundos...',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function passwordForm()
    {
        return view('dev.password');
    }

    public function passwordCambiar(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'password_actual' => 'required|string',
            'password_nuevo'  => 'required|string|min:8|confirmed',
        ]);

        $actual = env('DEV_PASSWORD', 'arkedent2024');

        if ($request->input('password_actual') !== $actual) {
            return back()->with('error', 'La contraseña actual es incorrecta.');
        }

        $nuevo = $request->input('password_nuevo');
        $this->actualizarEnv('DEV_PASSWORD', $nuevo);

        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        // Renovar cookie con nueva contraseña
        $nuevaToken = hash_hmac('sha256', $nuevo, config('app.key'));

        return redirect()->route('dev.password')
            ->with('exito', 'Contraseña actualizada correctamente.')
            ->cookie('dev_panel_auth', $nuevaToken, 60 * 8);
    }

    private function actualizarEnv(string $key, string $value): void
    {
        $envPath   = base_path('.env');
        $contenido = File::get($envPath);

        if (str_contains($contenido, "{$key}=")) {
            $contenido = preg_replace("/{$key}=.*/", "{$key}={$value}", $contenido);
        } else {
            $contenido .= "\n{$key}={$value}";
        }

        File::put($envPath, $contenido);
    }
}
