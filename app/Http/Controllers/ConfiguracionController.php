<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $config = Configuracion::obtener();
        return view('configuracion.index', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nombre_consultorio'              => 'required|string|max:150',
            'slogan'                          => 'nullable|string|max:255',
            'nit'                             => 'nullable|string|max:30',
            'registro_medico'                 => 'nullable|string|max:60',
            'telefono'                        => 'nullable|string|max:30',
            'telefono_whatsapp'               => 'nullable|string|max:30',
            'email'                           => 'nullable|email|max:120',
            'direccion'                       => 'nullable|string|max:255',
            'ciudad'                          => 'nullable|string|max:100',
            'pais'                            => 'nullable|string|max:80',
            'duracion_cita_minutos'           => 'required|integer|min:10|max:240',
            'formato_hora'                    => 'nullable|in:12,24',
            'horario'                         => 'nullable|array',
            'horario.*.apertura'              => 'nullable|date_format:H:i',
            'horario.*.cierre'                => 'nullable|date_format:H:i',
            'moneda'                          => 'nullable|string|max:10',
            'simbolo_moneda'                  => 'nullable|string|max:5',
            'recordatorios_activos'           => 'boolean',
            'horas_anticipacion_recordatorio' => 'nullable|integer|min:1|max:168',
            'tema'                            => 'nullable|in:morado-elegante,rosa-profesional,verde-esmeralda,azul-marino,carbon-moderno,azul-clinico',
            'fuente_principal'                => 'nullable|string|max:50',
            'fuente_titulos'                  => 'nullable|string|max:50',
            'firma_nombre_doctor'             => 'nullable|string|max:120',
            'firma_cargo'                     => 'nullable|string|max:80',
            'firma_registro'                  => 'nullable|string|max:60',
            'firma_imagen'                    => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'firma_canvas'                    => 'nullable|string',
            // Datos del profesional (Res. 1995/1999)
            'nombre_doctor'                   => 'nullable|string|max:120',
            'tarjeta_profesional'             => 'nullable|string|max:50',
            'especialidad_medica'             => 'nullable|string|max:100',
            'universidad'                     => 'nullable|string|max:150',
            'codigo_habilitacion'             => 'nullable|string|max:50',
            'tipo_prestador'                  => 'nullable|in:consultorio_privado,ips,centro_medico',
            'hora_backup'                     => 'nullable|regex:/^\d{2}:\d{2}$/',
            'tema_ui'                         => 'nullable|in:clasico,glass',
        ]);

        $config = Configuracion::obtener();

        $data = $request->only([
            'nombre_consultorio', 'slogan', 'nit', 'registro_medico',
            'telefono', 'telefono_whatsapp', 'email', 'direccion', 'ciudad', 'pais',
            'duracion_cita_minutos', 'formato_hora',
            'moneda', 'simbolo_moneda', 'horas_anticipacion_recordatorio',
            'firma_nombre_doctor', 'firma_cargo', 'firma_registro',
            // Datos del profesional (Res. 1995/1999)
            'nombre_doctor', 'tarjeta_profesional', 'especialidad_medica',
            'universidad', 'codigo_habilitacion', 'tipo_prestador',
            'hora_backup',
        ]);

        // Construir horario por día
        $horarioInput = $request->input('horario', []);
        $diasSemana   = [1, 2, 3, 4, 5, 6, 7];
        $diasLaborales = [];
        foreach ($diasSemana as $num) {
            $diaData = $horarioInput[$num] ?? [];
            $diasLaborales[$num] = [
                'activo'   => isset($diaData['activo']),
                'apertura' => $diaData['apertura'] ?? '08:00',
                'cierre'   => $diaData['cierre']   ?? '18:00',
            ];
        }
        $data['dias_laborales'] = $diasLaborales;

        // Mantener hora_apertura y hora_cierre con el primer día activo (compatibilidad)
        $primerActivo = collect($diasLaborales)->first(fn($d) => $d['activo']);
        $data['hora_apertura'] = ($primerActivo['apertura'] ?? '08:00') . ':00';
        $data['hora_cierre']   = ($primerActivo['cierre']   ?? '18:00') . ':00';

        $data['recordatorios_activos']  = $request->boolean('recordatorios_activos');
        $data['tema']                   = $request->input('tema', 'morado-elegante');
        $data['tema_ui']                = $request->input('tema_ui', 'clasico');
        $data['fuente_principal']       = $request->input('fuente_principal', 'DM Sans');
        $data['fuente_titulos']         = $request->input('fuente_titulos', 'Playfair Display');

        // Firma: imagen subida
        if ($request->hasFile('firma_imagen')) {
            if ($config->firma_path) {
                Storage::disk('public')->delete($config->firma_path);
            }
            $data['firma_path'] = $request->file('firma_imagen')->store('firmas', 'public');
        }
        // Firma: dibujada en canvas
        elseif ($request->filled('firma_canvas')) {
            $base64 = $request->input('firma_canvas');
            if (str_starts_with($base64, 'data:image/png;base64,')) {
                $imageData = base64_decode(explode(',', $base64)[1]);
                $filename  = 'firmas/firma_' . time() . '.png';
                if ($config->firma_path) {
                    Storage::disk('public')->delete($config->firma_path);
                }
                Storage::disk('public')->put($filename, $imageData);
                $data['firma_path'] = $filename;
            }
        }

        $config->update($data);
        Cache::forget('configuracion_consultorio');

        return back()->with('exito', 'Configuración actualizada correctamente.');
    }

    public function actualizarFirma(Request $request)
    {
        $request->validate([
            'firma_imagen'        => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'firma_canvas'        => 'nullable|string',
            'firma_nombre_doctor' => 'nullable|string|max:120',
            'firma_cargo'         => 'nullable|string|max:80',
            'firma_registro'      => 'nullable|string|max:60',
        ]);

        $config = Configuracion::obtener();
        $data   = $request->only(['firma_nombre_doctor', 'firma_cargo', 'firma_registro']);

        // Prioridad 1: imagen subida desde archivo
        if ($request->hasFile('firma_imagen')) {
            if ($config->firma_path) {
                Storage::disk('public')->delete($config->firma_path);
            }
            $path = $request->file('firma_imagen')->store('firmas', 'public');
            $data['firma_path'] = $path;
        }
        // Prioridad 2: firma dibujada en canvas (base64)
        elseif ($request->filled('firma_canvas')) {
            $base64 = $request->input('firma_canvas');
            // Validar que sea un data URL de imagen PNG
            if (str_starts_with($base64, 'data:image/png;base64,')) {
                $imageData = base64_decode(explode(',', $base64)[1]);
                $filename  = 'firmas/firma_' . time() . '.png';
                if ($config->firma_path) {
                    Storage::disk('public')->delete($config->firma_path);
                }
                Storage::disk('public')->put($filename, $imageData);
                $data['firma_path'] = $filename;
            }
        }

        $config->update($data);
        Cache::forget('configuracion_consultorio');

        return back()->with('exito', 'Firma digital guardada correctamente.');
    }

    public function eliminarFirma()
    {
        $config = Configuracion::obtener();
        if ($config->firma_path) {
            Storage::disk('public')->delete($config->firma_path);
            $config->update(['firma_path' => null]);
            Cache::forget('configuracion_consultorio');
        }
        return back()->with('exito', 'Firma eliminada correctamente.');
    }

    public function actualizarLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|file|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        $config = Configuracion::obtener();

        if ($config->logo_path) {
            Storage::disk('public')->delete($config->logo_path);
        }

        $path = $request->file('logo')->store('logos', 'public');
        $config->update(['logo_path' => $path]);
        Cache::forget('configuracion_consultorio');

        return back()->with('exito', 'Logo actualizado correctamente.');
    }
}