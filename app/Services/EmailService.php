<?php

namespace App\Services;

use App\Models\Cita;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function enviarRecordatorioCita(Cita $cita): bool
    {
        try {
            $config   = \App\Models\Configuracion::obtener();
            $paciente = $cita->paciente;

            if (empty($paciente->email)) return false;
            if (!$paciente->tieneAutorizacion()) return false;

            $mensaje = $this->reemplazarVariables(
                $config->plantilla_email ?? $this->plantillaPorDefecto(),
                $cita
            );

            Mail::send([], [], function ($mail) use ($paciente, $mensaje, $config, $cita) {
                $mail->to($paciente->email, $paciente->nombre_completo)
                     ->subject("Recordatorio de cita — {$config->nombre_consultorio}")
                     ->html($mensaje);

                if ($config->mail_from_address) {
                    $mail->from(
                        $config->mail_from_address,
                        $config->mail_from_name ?? $config->nombre_consultorio
                    );
                }
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Error enviando email recordatorio: ' . $e->getMessage());
            return false;
        }
    }

    private function reemplazarVariables(string $plantilla, Cita $cita): string
    {
        $config = \App\Models\Configuracion::obtener();
        return str_replace([
            '{{nombre_paciente}}',
            '{{fecha_cita}}',
            '{{hora_cita}}',
            '{{procedimiento}}',
            '{{nombre_consultorio}}',
            '{{direccion_consultorio}}',
            '{{telefono_consultorio}}',
        ], [
            $cita->paciente->nombre_completo,
            $cita->fecha->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY'),
            date('h:i A', strtotime($cita->hora_inicio)),
            $cita->procedimiento,
            $config->nombre_consultorio,
            $config->direccion ?? '',
            $config->telefono ?? '',
        ], $plantilla);
    }

    private function plantillaPorDefecto(): string
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="background: #1E3A5F; padding: 20px; border-radius: 8px 8px 0 0; text-align: center;">
                <h2 style="color: white; margin: 0;">{{nombre_consultorio}}</h2>
                <p style="color: rgba(255,255,255,0.8); margin: 5px 0 0;">Recordatorio de Cita</p>
            </div>
            <div style="background: #f9f9f9; padding: 25px; border-radius: 0 0 8px 8px; border: 1px solid #eee;">
                <p style="font-size: 16px; color: #333;">Hola, <strong>{{nombre_paciente}}</strong></p>
                <p style="color: #555;">Te recordamos que tienes una cita programada:</p>
                <div style="background: white; border-left: 4px solid #1E3A5F; padding: 15px; margin: 15px 0; border-radius: 0 8px 8px 0;">
                    <p style="margin: 5px 0; color: #333;"><strong>📅 Fecha:</strong> {{fecha_cita}}</p>
                    <p style="margin: 5px 0; color: #333;"><strong>🕐 Hora:</strong> {{hora_cita}}</p>
                    <p style="margin: 5px 0; color: #333;"><strong>🦷 Procedimiento:</strong> {{procedimiento}}</p>
                    <p style="margin: 5px 0; color: #333;"><strong>📍 Dirección:</strong> {{direccion_consultorio}}</p>
                </div>
                <p style="color: #555;">Si necesitas cancelar o reprogramar tu cita, por favor contáctanos con anticipación.</p>
                <p style="color: #555;"><strong>📞 Teléfono:</strong> {{telefono_consultorio}}</p>
                <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
                <p style="font-size: 12px; color: #999; text-align: center;">
                    {{nombre_consultorio}} — Este es un mensaje automático, por favor no responder a este correo.
                </p>
            </div>
        </div>';
    }
}
