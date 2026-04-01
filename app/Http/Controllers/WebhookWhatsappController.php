<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Recordatorio;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookWhatsappController extends Controller
{
    public function handle(Request $request)
    {
        try {
            [$telefono, $cuerpo] = $this->parsearMensaje($request);

            if (!$telefono || !$cuerpo) {
                return response()->json(['ok' => false, 'error' => 'payload inválido']);
            }

            $respuesta = trim(strtolower($cuerpo));

            // Solo procesar respuestas 1, 2 o 3
            if (!in_array($respuesta, ['1', '2', '3'])) {
                return response()->json(['ok' => true, 'info' => 'respuesta ignorada']);
            }

            // Buscar paciente por los últimos 10 dígitos del teléfono
            $ultimos10 = substr(preg_replace('/[^0-9]/', '', $telefono), -10);
            $paciente = Paciente::where('telefono', 'like', "%{$ultimos10}")->first();

            if (!$paciente) {
                Log::info("Webhook WhatsApp: paciente no encontrado para {$telefono}");
                return response()->json(['ok' => false, 'error' => 'paciente no encontrado']);
            }

            // Buscar el recordatorio WhatsApp más reciente enviado (últimas 72 horas)
            $recordatorio = Recordatorio::where('paciente_id', $paciente->id)
                ->where('canal', 'whatsapp')
                ->where('estado', 'enviado')
                ->where('fecha_envio', '>=', now()->subHours(72))
                ->orderByDesc('fecha_envio')
                ->first();

            if (!$recordatorio) {
                Log::info("Webhook WhatsApp: no hay recordatorio reciente para paciente {$paciente->id}");
                return response()->json(['ok' => false, 'error' => 'sin recordatorio reciente']);
            }

            $cita = $recordatorio->cita;

            if (!$cita) {
                return response()->json(['ok' => false, 'error' => 'cita no encontrada']);
            }

            $wa = new WhatsappService();

            if ($respuesta === '1') {
                // Confirmar
                $cita->update(['estado' => 'confirmada']);
                $wa->enviarPorTipo($cita, 'confirmacion');
                Log::info("Webhook WhatsApp: cita {$cita->id} confirmada por paciente {$paciente->nombre_completo}");

            } elseif ($respuesta === '2') {
                // Reprogramar (informar, no cambia estado automáticamente)
                $wa->enviarPorTipo($cita, 'reprogramacion');
                Log::info("Webhook WhatsApp: paciente {$paciente->nombre_completo} solicita reprogramar cita {$cita->id}");

            } elseif ($respuesta === '3') {
                // Cancelar
                $cita->update([
                    'estado'             => 'cancelada',
                    'motivo_cancelacion' => 'Cancelada por el paciente vía WhatsApp',
                ]);
                $wa->enviarPorTipo($cita, 'cancelacion');
                Log::info("Webhook WhatsApp: cita {$cita->id} cancelada por paciente {$paciente->nombre_completo}");
            }

            return response()->json(['ok' => true]);

        } catch (\Exception $e) {
            Log::error('Webhook WhatsApp error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'error' => 'error interno'], 200);
        }
    }

    /**
     * Extrae [telefono, cuerpo] del payload de UltraMsg o Twilio.
     */
    private function parsearMensaje(Request $request): array
    {
        // ── UltraMsg ───────────────────────────────────────────
        // Payload: { "data": { "from": "573001234567@c.us", "body": "1", "type": "chat" } }
        if ($request->has('data')) {
            $data  = $request->input('data');
            $from  = $data['from'] ?? null;
            $body  = $data['body'] ?? null;
            $type  = $data['type'] ?? '';

            // Solo mensajes de chat (no grupos, estados, etc.)
            if ($type !== 'chat') return [null, null];

            if ($from) {
                $from = explode('@', $from)[0]; // "573001234567@c.us" → "573001234567"
            }

            return [$from, $body];
        }

        // ── Twilio ─────────────────────────────────────────────
        // Form payload: From=whatsapp:+573001234567 & Body=1
        if ($request->has('From') && $request->has('Body')) {
            $from = preg_replace('/[^0-9]/', '', $request->input('From')); // "+573001234567" → "573001234567"
            $body = $request->input('Body');
            return [$from, $body];
        }

        return [null, null];
    }
}
