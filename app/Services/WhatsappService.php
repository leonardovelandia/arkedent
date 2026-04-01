<?php

namespace App\Services;

use App\Models\Cita;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    private string $provider;

    // UltraMsg
    private string $ultramsgToken;
    private string $ultramsgInstance;

    // Twilio
    private string $twilioSid;
    private string $twilioToken;
    private string $twilioFrom;

    public function __construct()
    {
        $config = \App\Models\Configuracion::obtener();

        $this->provider = $config->whatsapp_provider ?? 'ultramsg';

        $this->ultramsgToken    = $config->ultramsg_token ?? '';
        $this->ultramsgInstance = $config->ultramsg_instance ?? '';

        $this->twilioSid   = $config->twilio_account_sid ?? '';
        $this->twilioToken = $config->twilio_auth_token ?? '';
        $this->twilioFrom  = $config->twilio_whatsapp_from ?? '';
    }

    /**
     * Envía el recordatorio de cita usando la plantilla correcta según el modo configurado.
     */
    public function enviarRecordatorioCita(Cita $cita): bool
    {
        $config   = \App\Models\Configuracion::obtener();
        $paciente = $cita->paciente;

        if (empty($paciente->telefono)) return false;
        if (!$paciente->tieneAutorizacion()) return false;

        $modo = $config->modo_recordatorio ?? 'simple';

        if ($modo === 'interactivo') {
            $plantilla = $config->plantilla_interactiva_whatsapp ?? $this->plantillaInteractivaDefault();
        } else {
            $plantilla = $config->plantilla_whatsapp ?? $this->plantillaPorDefecto();
        }

        $mensaje  = $this->reemplazarVariables($plantilla, $cita);
        $telefono = $this->formatearTelefono($paciente->telefono);

        return $this->provider === 'twilio'
            ? $this->enviarTwilio($telefono, $mensaje)
            : $this->enviarUltramsg($telefono, $mensaje);
    }

    /**
     * Envía un tipo específico de mensaje (confirmacion, cancelacion, reprogramacion).
     */
    public function enviarPorTipo(Cita $cita, string $tipo): bool
    {
        $config   = \App\Models\Configuracion::obtener();
        $paciente = $cita->paciente;

        if (empty($paciente->telefono)) return false;
        if (!$paciente->tieneAutorizacion()) return false;

        $plantilla = match ($tipo) {
            'confirmacion'   => $config->plantilla_confirmacion_whatsapp   ?? $this->plantillaConfirmacionDefault(),
            'cancelacion'    => $config->plantilla_cancelacion_whatsapp    ?? $this->plantillaCancelacionDefault(),
            'reprogramacion' => $config->plantilla_reprogramacion_whatsapp ?? $this->plantillaReprogramacionDefault(),
            default          => $config->plantilla_whatsapp                ?? $this->plantillaPorDefecto(),
        };

        $mensaje  = $this->reemplazarVariables($plantilla, $cita);
        $telefono = $this->formatearTelefono($paciente->telefono);

        return $this->provider === 'twilio'
            ? $this->enviarTwilio($telefono, $mensaje)
            : $this->enviarUltramsg($telefono, $mensaje);
    }

    public function enviarMensajePrueba(string $telefono, string $mensaje): bool
    {
        $numero = $this->formatearTelefono($telefono);

        return $this->provider === 'twilio'
            ? $this->enviarTwilio($numero, $mensaje)
            : $this->enviarUltramsg($numero, $mensaje);
    }

    private function enviarUltramsg(string $telefono, string $mensaje): bool
    {
        try {
            if (empty($this->ultramsgToken) || empty($this->ultramsgInstance)) return false;

            $response = Http::post("https://api.ultramsg.com/{$this->ultramsgInstance}/messages/chat", [
                'token' => $this->ultramsgToken,
                'to'    => $telefono,
                'body'  => $mensaje,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Error WhatsApp UltraMsg: ' . $e->getMessage());
            return false;
        }
    }

    private function enviarTwilio(string $telefono, string $mensaje): bool
    {
        try {
            if (empty($this->twilioSid) || empty($this->twilioToken) || empty($this->twilioFrom)) return false;

            $from = str_starts_with($this->twilioFrom, 'whatsapp:')
                ? $this->twilioFrom
                : 'whatsapp:' . $this->twilioFrom;

            $to = 'whatsapp:+' . ltrim($telefono, '+');

            $response = Http::withBasicAuth($this->twilioSid, $this->twilioToken)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->twilioSid}/Messages.json", [
                    'From' => $from,
                    'To'   => $to,
                    'Body' => $mensaje,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Error WhatsApp Twilio: ' . $e->getMessage());
            return false;
        }
    }

    public function formatearTelefono(string $telefono): string
    {
        $numero = preg_replace('/[^0-9]/', '', $telefono);
        $numero = ltrim($numero, '0');
        if (strlen($numero) === 10) {
            $numero = '57' . $numero;
        }
        return $numero;
    }

    private function reemplazarVariables(string $plantilla, Cita $cita): string
    {
        // Normalizar formato compilado Blade a formato {{variable}}
        $patternClose = '\?' . '>';
        $plantilla = preg_replace('/<\?php echo e\(([^)]+)\); ' . $patternClose . '/', '{{$1}}', $plantilla);

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

    // ── Plantillas por defecto ────────────────────────────────

    private function plantillaPorDefecto(): string
    {
        return "🦷 *{{nombre_consultorio}}*\n\n"
             . "Hola *{{nombre_paciente}}* 👋\n\n"
             . "Te recordamos tu cita odontológica:\n\n"
             . "📅 Fecha: {{fecha_cita}}\n"
             . "⏰ Hora: {{hora_cita}}\n"
             . "🦷 Procedimiento: {{procedimiento}}\n"
             . "📍 {{nombre_consultorio}}\n"
             . "📌 Dirección: {{direccion_consultorio}}\n\n"
             . "Si necesitas reprogramar, contáctanos al 📞 {{telefono_consultorio}}.\n\n"
             . "¡Te esperamos! 😊";
    }

    private function plantillaInteractivaDefault(): string
    {
        return "Hola {{nombre_paciente}} 👋\n\n"
             . "Te recordamos tu cita:\n\n"
             . "📅 {{fecha_cita}}\n"
             . "⏰ {{hora_cita}}\n"
             . "🦷 {{procedimiento}}\n\n"
             . "Responde con:\n"
             . "✅ *1* para confirmar\n"
             . "🔁 *2* para reprogramar\n"
             . "❌ *3* para cancelar\n\n"
             . "📞 {{telefono_consultorio}}";
    }

    private function plantillaConfirmacionDefault(): string
    {
        return "Hola {{nombre_paciente}} 👋\n\n"
             . "✅ Tu cita ha sido confirmada:\n\n"
             . "📅 {{fecha_cita}}\n"
             . "⏰ {{hora_cita}}\n"
             . "🦷 {{procedimiento}}\n\n"
             . "📍 {{nombre_consultorio}}\n\n"
             . "¡Gracias por confiar en nosotros! 😊";
    }

    private function plantillaCancelacionDefault(): string
    {
        return "Hola {{nombre_paciente}} 👋\n\n"
             . "❌ Tu cita odontológica ha sido cancelada.\n\n"
             . "Si deseas agendar una nueva cita, contáctanos al 📞 {{telefono_consultorio}}.\n\n"
             . "Estamos para ayudarte 😊";
    }

    private function plantillaReprogramacionDefault(): string
    {
        return "Hola {{nombre_paciente}} 👋\n\n"
             . "🔁 Tu solicitud de reprogramación ha sido recibida.\n\n"
             . "📅 Cita actual: {{fecha_cita}}\n"
             . "⏰ Hora: {{hora_cita}}\n\n"
             . "Nos pondremos en contacto contigo para coordinar la nueva fecha.\n\n"
             . "📞 {{telefono_consultorio}}\n\n"
             . "Gracias por tu comprensión.";
    }
}
