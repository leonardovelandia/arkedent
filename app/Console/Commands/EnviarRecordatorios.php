<?php

namespace App\Console\Commands;

use App\Models\Cita;
use App\Models\Recordatorio;
use App\Services\EmailService;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EnviarRecordatorios extends Command
{
    protected $signature   = 'recordatorios:enviar {--force : Enviar sin verificar la hora configurada}';
    protected $description = 'Envía recordatorios de citas según la configuración de días y hora de envío';

    public function handle(): int
    {
        $config = \App\Models\Configuracion::obtener();

        if (!$config->recordatorios_activos) {
            $this->info('Recordatorios desactivados en configuración.');
            return self::SUCCESS;
        }

        // Verificar hora de envío (se omite si se llama con --force)
        if (!$this->option('force')) {
            $horaEnvio   = $config->hora_envio_recordatorio ?? '12:00';
            $horaActual  = now()->format('H:i');
            $horaConfigH = (int) substr($horaEnvio, 0, 2);

            if (now()->hour !== $horaConfigH) {
                $this->info("Hora actual ({$horaActual}) no coincide con la hora configurada ({$horaEnvio}). Omitiendo.");
                return self::SUCCESS;
            }
        }

        // Calcular la fecha objetivo según los días de anticipación configurados
        $dias          = (int) ($config->horas_anticipacion ?? 1);
        $fechaObjetivo = Carbon::today()->addDays($dias);

        $this->info("Buscando citas para el {$fechaObjetivo->format('d/m/Y')} ({$dias} día(s) de anticipación)...");

        $citas = Cita::with('paciente')
            ->whereDate('fecha', $fechaObjetivo)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->where('activo', true)
            ->get();

        $this->info("Citas encontradas: {$citas->count()}");

        $emailService    = new EmailService();
        $whatsappService = new WhatsappService();
        $enviados        = 0;
        $omitidos        = 0;
        $fallidos        = 0;

        foreach ($citas as $cita) {
            $paciente = $cita->paciente;

            if (!$paciente->tieneAutorizacion()) {
                $this->warn("  ⚠ {$paciente->nombre_completo} sin autorización firmada — omitido");
                $omitidos++;
                continue;
            }

            // Email
            if ($config->recordatorios_email_activo && !empty($paciente->email)) {
                $yaEnviadoEmail = Recordatorio::where('cita_id', $cita->id)
                    ->where('canal', 'email')
                    ->where('estado', 'enviado')
                    ->whereDate('fecha_envio', today())
                    ->exists();

                if ($yaEnviadoEmail) {
                    $this->line("  — Email ya enviado hoy a {$paciente->nombre_completo} — omitido");
                } else {
                    $exitoEmail = $emailService->enviarRecordatorioCita($cita);
                    Recordatorio::create([
                        'cita_id'          => $cita->id,
                        'paciente_id'      => $paciente->id,
                        'tipo'             => 'email',
                        'canal'            => 'email',
                        'estado'           => $exitoEmail ? 'enviado' : 'fallido',
                        'mensaje'          => "Recordatorio de cita {$cita->fecha->format('d/m/Y')}",
                        'fecha_programada' => now(),
                        'fecha_envio'      => $exitoEmail ? now() : null,
                    ]);
                    if ($exitoEmail) {
                        $enviados++;
                        $this->info("  ✓ Email enviado a {$paciente->nombre_completo}");
                    } else {
                        $fallidos++;
                        $this->error("  ✗ Falló email para {$paciente->nombre_completo}");
                    }
                }
            }

            // WhatsApp
            if ($config->recordatorios_whatsapp_activo && !empty($paciente->telefono)) {
                $yaEnviadoWA = Recordatorio::where('cita_id', $cita->id)
                    ->where('canal', 'whatsapp')
                    ->where('estado', 'enviado')
                    ->whereDate('fecha_envio', today())
                    ->exists();

                if ($yaEnviadoWA) {
                    $this->line("  — WhatsApp ya enviado hoy a {$paciente->nombre_completo} — omitido");
                } else {
                    $exitoWA = $whatsappService->enviarRecordatorioCita($cita);
                    Recordatorio::create([
                        'cita_id'          => $cita->id,
                        'paciente_id'      => $paciente->id,
                        'tipo'             => 'whatsapp',
                        'canal'            => 'whatsapp',
                        'estado'           => $exitoWA ? 'enviado' : 'fallido',
                        'mensaje'          => "Recordatorio WhatsApp cita {$cita->fecha->format('d/m/Y')}",
                        'fecha_programada' => now(),
                        'fecha_envio'      => $exitoWA ? now() : null,
                    ]);
                    if ($exitoWA) {
                        $enviados++;
                        $this->info("  ✓ WhatsApp enviado a {$paciente->nombre_completo}");
                    } else {
                        $fallidos++;
                        $this->error("  ✗ Falló WhatsApp para {$paciente->nombre_completo}");
                    }
                }
            }
        }

        $this->info("Resumen: {$enviados} enviados, {$omitidos} omitidos, {$fallidos} fallidos.");
        return self::SUCCESS;
    }
}
