<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Configuracion;
use App\Models\Recordatorio;
use App\Services\EmailService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class RecordatorioController extends Controller
{
    public function index(Request $request)
    {
        $query = Recordatorio::with(['paciente', 'cita'])->where('activo', true);

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_programada', $request->input('fecha'));
        }
        if ($request->filled('canal')) {
            $query->where('canal', $request->input('canal'));
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        $recordatorios = $query->orderByDesc('fecha_programada')->paginate(20)->withQueryString();

        $enviadosHoy  = Recordatorio::where('estado', 'enviado')->whereDate('fecha_envio', today())->count();
        $pendientes   = Recordatorio::where('estado', 'pendiente')->where('activo', true)->count();
        $fallidos     = Recordatorio::where('estado', 'fallido')->whereDate('created_at', today())->count();
        $totalMes     = Recordatorio::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        return view('recordatorios.index', compact(
            'recordatorios', 'enviadosHoy', 'pendientes', 'fallidos', 'totalMes'
        ));
    }

    public function enviar($id)
    {
        $recordatorio = Recordatorio::with(['cita.paciente'])->findOrFail($id);

        if ($recordatorio->estado !== 'fallido' && $recordatorio->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden reenviar recordatorios fallidos o pendientes.');
        }

        $cita     = $recordatorio->cita;
        $exito    = false;

        if ($recordatorio->canal === 'email') {
            $exito = (new EmailService())->enviarRecordatorioCita($cita);
        } elseif ($recordatorio->canal === 'whatsapp') {
            $exito = (new WhatsappService())->enviarRecordatorioCita($cita);
        }

        $recordatorio->update([
            'estado'     => $exito ? 'enviado' : 'fallido',
            'fecha_envio'=> $exito ? now() : null,
            'intentos'   => $recordatorio->intentos + 1,
        ]);

        return back()->with('exito', $exito ? 'Recordatorio enviado correctamente.' : 'No se pudo enviar el recordatorio.');
    }

    public function cancelar($id)
    {
        $recordatorio = Recordatorio::findOrFail($id);
        $recordatorio->update(['estado' => 'cancelado', 'activo' => false]);

        return back()->with('exito', 'Recordatorio cancelado.');
    }

    public function destroy($id)
    {
        $recordatorio = Recordatorio::findOrFail($id);
        $recordatorio->update(['activo' => false, 'estado' => 'cancelado']);

        return back()->with('exito', 'Recordatorio eliminado.');
    }

    public function enviarAhora(Request $request)
    {
        Artisan::call('recordatorios:enviar', ['--force' => true]);
        $output = Artisan::output();

        return response()->json([
            'ok'      => true,
            'mensaje' => 'Proceso completado.',
            'detalle' => trim($output),
        ]);
    }

    public function configuracion()
    {
        $config = Configuracion::obtener();
        return view('recordatorios.configuracion', compact('config'));
    }

    public function guardarConfiguracion(Request $request)
    {
        $request->validate([
            'recordatorios_activos'         => 'boolean',
            'recordatorios_email_activo'    => 'boolean',
            'recordatorios_whatsapp_activo' => 'boolean',
            'horas_anticipacion'            => 'required|integer|in:1,2,3',
            'hora_envio_recordatorio'       => 'nullable|date_format:H:i',
            'modo_recordatorio'             => 'nullable|in:simple,interactivo',
            'plantilla_interactiva_whatsapp'   => 'nullable|string',
            'plantilla_confirmacion_whatsapp'  => 'nullable|string',
            'plantilla_cancelacion_whatsapp'   => 'nullable|string',
            'plantilla_reprogramacion_whatsapp'=> 'nullable|string',
            'whatsapp_provider'             => 'nullable|in:ultramsg,twilio',
            'ultramsg_instance'             => 'nullable|string|max:50',
            'ultramsg_token'                => 'nullable|string|max:100',
            'twilio_account_sid'            => 'nullable|string|max:60',
            'twilio_auth_token'             => 'nullable|string|max:60',
            'twilio_whatsapp_from'          => 'nullable|string|max:30',
            'mail_from_name'                => 'nullable|string|max:100',
            'mail_from_address'             => 'nullable|email|max:120',
            'plantilla_email'               => 'nullable|string',
            'plantilla_whatsapp'            => 'nullable|string',
        ]);

        $config = Configuracion::obtener();
        $config->update([
            'recordatorios_activos'         => $request->boolean('recordatorios_activos'),
            'recordatorios_email_activo'    => $request->boolean('recordatorios_email_activo'),
            'recordatorios_whatsapp_activo' => $request->boolean('recordatorios_whatsapp_activo'),
            'horas_anticipacion'            => $request->input('horas_anticipacion', 1),
            'hora_envio_recordatorio'       => $request->input('hora_envio_recordatorio', '12:00'),
            'modo_recordatorio'             => $request->input('modo_recordatorio', 'simple'),
            'plantilla_interactiva_whatsapp'   => $request->input('plantilla_interactiva_whatsapp'),
            'plantilla_confirmacion_whatsapp'  => $request->input('plantilla_confirmacion_whatsapp'),
            'plantilla_cancelacion_whatsapp'   => $request->input('plantilla_cancelacion_whatsapp'),
            'plantilla_reprogramacion_whatsapp'=> $request->input('plantilla_reprogramacion_whatsapp'),
            'whatsapp_provider'             => $request->input('whatsapp_provider', 'ultramsg'),
            'ultramsg_instance'             => $request->input('ultramsg_instance'),
            'ultramsg_token'                => $request->input('ultramsg_token'),
            'twilio_account_sid'            => $request->input('twilio_account_sid'),
            'twilio_auth_token'             => $request->input('twilio_auth_token'),
            'twilio_whatsapp_from'          => $request->input('twilio_whatsapp_from'),
            'mail_from_name'                => $request->input('mail_from_name'),
            'mail_from_address'             => $request->input('mail_from_address'),
            'plantilla_email'               => $request->input('plantilla_email'),
            'plantilla_whatsapp'            => $request->input('plantilla_whatsapp'),
        ]);

        Cache::forget('configuracion_consultorio');

        return back()->with('exito', 'Configuración de recordatorios guardada.');
    }

    public function probarEmail(Request $request)
    {
        $request->validate(['email_prueba' => 'required|email']);

        $config = Configuracion::obtener();

        try {
            Mail::send([], [], function ($mail) use ($request, $config) {
                $mail->to($request->input('email_prueba'))
                     ->subject("Prueba de recordatorio — {$config->nombre_consultorio}")
                     ->html("<p>Este es un correo de prueba del sistema de recordatorios de <strong>{$config->nombre_consultorio}</strong>.</p><p>Si recibes este mensaje, la configuración de correo funciona correctamente.</p>");

                if ($config->mail_from_address) {
                    $mail->from($config->mail_from_address, $config->mail_from_name ?? $config->nombre_consultorio);
                }
            });

            return back()->with('exito', 'Email de prueba enviado correctamente a ' . $request->input('email_prueba'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar: ' . $e->getMessage());
        }
    }

    public function probarWhatsapp(Request $request)
    {
        $request->validate(['telefono_prueba' => 'required|string']);

        $config  = Configuracion::obtener();
        $mensaje = "🦷 Prueba de recordatorio — {$config->nombre_consultorio}\n\nSi recibes este mensaje, la configuración de WhatsApp funciona correctamente.";

        $exito = (new WhatsappService())->enviarMensajePrueba($request->input('telefono_prueba'), $mensaje);

        if ($exito) {
            return back()->with('exito', 'WhatsApp de prueba enviado correctamente.');
        }
        return back()->with('error', 'No se pudo enviar. Verifica las credenciales y el proveedor seleccionado.');
    }
}
