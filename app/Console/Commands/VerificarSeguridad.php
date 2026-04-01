<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VerificarSeguridad extends Command
{
    protected $signature   = 'seguridad:verificar';
    protected $description = 'Verifica la configuración de seguridad del sistema';

    public function handle(): void
    {
        $this->info('═══════════════════════════════════════');
        $this->info('  VERIFICACIÓN DE SEGURIDAD — ArkevixDentalERP');
        $this->info('═══════════════════════════════════════');

        $errores = 0;

        // APP_DEBUG
        if (config('app.debug')) {
            $this->error('❌ APP_DEBUG está en TRUE — CAMBIAR A FALSE en producción');
            $errores++;
        } else {
            $this->info('✅ APP_DEBUG = false');
        }

        // APP_ENV
        if (config('app.env') === 'production') {
            $this->info('✅ APP_ENV = production');
        } else {
            $this->warn('⚠️  APP_ENV = ' . config('app.env') . ' (cambiar a production)');
        }

        // APP_KEY
        if (config('app.key')) {
            $this->info('✅ APP_KEY configurado');
        } else {
            $this->error('❌ APP_KEY no configurado — ejecutar php artisan key:generate');
            $errores++;
        }

        // Base de datos
        if (config('database.connections.mysql.password') === '') {
            $this->warn('⚠️  Contraseña de BD vacía — configurar en producción');
        } else {
            $this->info('✅ Contraseña de BD configurada');
        }

        // DEV_PASSWORD
        if (env('DEV_PASSWORD') === 'arkevix2026') {
            $this->warn('⚠️  DEV_PASSWORD es la contraseña por defecto — cambiar en producción');
        } else {
            $this->info('✅ DEV_PASSWORD personalizada');
        }

        // HTTPS
        if (str_starts_with(config('app.url'), 'https')) {
            $this->info('✅ APP_URL usa HTTPS');
        } else {
            $this->warn('⚠️  APP_URL no usa HTTPS — requerido en producción');
        }

        // Storage link
        if (file_exists(public_path('storage'))) {
            $this->info('✅ Storage link existe');
        } else {
            $this->error('❌ Storage link no existe — ejecutar php artisan storage:link');
            $errores++;
        }

        // SecurityHeaders middleware
        $middlewareRegistrado = str_contains(
            file_get_contents(base_path('bootstrap/app.php')),
            'SecurityHeaders'
        );
        if ($middlewareRegistrado) {
            $this->info('✅ SecurityHeaders middleware registrado');
        } else {
            $this->warn('⚠️  SecurityHeaders middleware no encontrado en bootstrap/app.php');
        }

        // Tabla logs_auditoria
        try {
            \Illuminate\Support\Facades\DB::table('logs_auditoria')->count();
            $this->info('✅ Tabla logs_auditoria existe');
        } catch (\Exception $e) {
            $this->warn('⚠️  Tabla logs_auditoria no existe — ejecutar php artisan migrate');
        }

        $this->info('═══════════════════════════════════════');

        if ($errores === 0) {
            $this->info('✅ Sin errores críticos detectados');
        } else {
            $this->error("❌ {$errores} error(es) crítico(s) encontrado(s) — corregir antes de producción");
        }
    }
}
