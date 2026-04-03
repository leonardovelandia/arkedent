<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Enviar recordatorios: corre cada hora y el comando verifica si es la hora configurada
Schedule::command('recordatorios:enviar')->hourly();

// ── Backup automático diario (hora configurable desde el panel) ──
$horaBackup = '02:00';
try {
    $horaBackup = \App\Models\Configuracion::obtener()->hora_backup ?? '02:00';
} catch (\Throwable) {}

Schedule::command('backup:run --only-db')
    ->dailyAt($horaBackup)
    ->onFailure(function () {
        \Log::channel('firmas')->error('BACKUP BD FALLIDO — ' . now());
    })
    ->appendOutputTo(storage_path('logs/backup.log'));

// Backup completo semanal (domingos 3:00 AM)
Schedule::command('backup:run')
    ->weeklyOn(0, '03:00')
    ->onFailure(function () {
        \Log::channel('firmas')->error('BACKUP COMPLETO FALLIDO — ' . now());
    });

// Limpiar backups antiguos diariamente a la 1:00 AM
Schedule::command('backup:clean')->dailyAt('01:00');

// Monitorear que los backups existan (alerta si faltan)
Schedule::command('backup:monitor')->dailyAt('08:00');
