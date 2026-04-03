<?php

namespace App\Http\Controllers;

use App\Models\AutorizacionDatos;
use App\Traits\TrazabilidadFirma;
use App\Models\Configuracion;
use App\Models\Paciente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AutorizacionDatosController extends Controller
{
    public function create(Request $request)
    {
        $paciente = Paciente::findOrFail($request->input('paciente_id'));

        $existente = $paciente->autorizacionDatos;
        if ($existente) {
            $msg = $existente->firmado
                ? 'Este paciente ya tiene una autorización firmada.'
                : 'Este paciente ya tiene una autorización pendiente de firma.';
            return redirect()->route('autorizacion.show', $existente->id)
                ->with('aviso', $msg);
        }

        $config = Configuracion::obtener();
        return view('autorizacion.create', compact('paciente', 'config'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paciente_id'                => 'required|exists:pacientes,id',
            'acepta_almacenamiento'      => 'nullable|boolean',
            'acepta_contacto_whatsapp'   => 'nullable|boolean',
            'acepta_contacto_email'      => 'nullable|boolean',
            'acepta_contacto_llamada'    => 'nullable|boolean',
            'acepta_recordatorios'       => 'nullable|boolean',
            'acepta_compartir_entidades' => 'nullable|boolean',
            'firma_data'                 => 'nullable|string',
            'observaciones'              => 'nullable|string|max:1000',
        ]);

        $paciente  = Paciente::findOrFail($request->input('paciente_id'));

        $existente = $paciente->autorizacionDatos;
        if ($existente?->firmado) {
            return redirect()->route('autorizacion.show', $existente->id)
                ->with('aviso', 'Este paciente ya tiene una autorización firmada.');
        }

        $firmar    = $request->input('accion') === 'firmar' && $request->filled('firma_data');

        $firmaData    = $firmar ? $request->input('firma_data') : null;
        $trazabilidad = [];

        if ($firmar) {
            $trazabilidad = TrazabilidadFirma::generarTrazabilidad(
                $request,
                $firmaData,
                [
                    'paciente' => $paciente->nombre_completo ?? '',
                    'doc'      => $paciente->numero_documento ?? '',
                    'fecha'    => now()->toDateString(),
                ]
            );
        }

        $autorizacion = AutorizacionDatos::create(array_merge(
            [
                'paciente_id'                => $paciente->id,
                'user_id'                    => auth()->id(),
                'fecha_autorizacion'         => today(),
                'acepta_almacenamiento'      => $request->boolean('acepta_almacenamiento'),
                'acepta_contacto_whatsapp'   => $request->boolean('acepta_contacto_whatsapp'),
                'acepta_contacto_email'      => $request->boolean('acepta_contacto_email'),
                'acepta_contacto_llamada'    => $request->boolean('acepta_contacto_llamada'),
                'acepta_recordatorios'       => $request->boolean('acepta_recordatorios'),
                'acepta_compartir_entidades' => $request->boolean('acepta_compartir_entidades'),
                'firmado'                    => $firmar,
                'firma_data'                 => $firmaData,
                'fecha_firma'                => $firmar ? now() : null,
                'ip_firma'                   => $firmar ? $request->getClientIp() : null,
                'metodo_firma'               => $firmar ? 'digital' : null,
                'observaciones'              => $request->input('observaciones'),
            ],
            $trazabilidad
        ));

        // Actualizar paciente
        $paciente->update([
            'autorizacion_datos'       => $firmar,
            'fecha_autorizacion_datos' => $firmar ? now() : null,
        ]);

        $msg = $firmar
            ? 'Autorización creada y firmada digitalmente.'
            : 'Autorización guardada. Recuerda obtener la firma del paciente.';

        return redirect()->route('autorizacion.show', $autorizacion->id)
            ->with('exito', $msg);
    }

    public function show($id)
    {
        $autorizacion = AutorizacionDatos::with(['paciente', 'registradoPor'])->findOrFail($id);
        $config       = Configuracion::obtener();

        return view('autorizacion.show', compact('autorizacion', 'config'));
    }

    public function firmar(Request $request, $id)
    {
        $request->validate([
            'firma_data'   => 'required|string',
            'metodo_firma' => 'nullable|in:digital,impresa',
        ]);

        $autorizacion = AutorizacionDatos::findOrFail($id);

        if ($autorizacion->firmado) {
            return redirect()->route('autorizacion.show', $id)
                ->with('aviso', 'Esta autorización ya fue firmada y no puede volver a firmarse.');
        }

        $firmaData    = $request->input('firma_data');

        $trazabilidad = TrazabilidadFirma::generarTrazabilidad(
            $request,
            $firmaData,
            [
                'id'       => (string) $autorizacion->id,
                'numero'   => $autorizacion->numero_autorizacion ?? '',
                'paciente' => $autorizacion->paciente->nombre_completo ?? '',
                'doc'      => $autorizacion->paciente->numero_documento ?? '',
                'fecha'    => now()->toDateString(),
            ]
        );

        $autorizacion->update(array_merge(
            [
                'firmado'      => true,
                'firma_data'   => $firmaData,
                'fecha_firma'  => now(),
                'ip_firma'     => $request->getClientIp(),
                'metodo_firma' => $request->input('metodo_firma', 'digital'),
            ],
            $trazabilidad
        ));

        $autorizacion->paciente->update([
            'autorizacion_datos'       => true,
            'fecha_autorizacion_datos' => now(),
        ]);

        \Log::channel('firmas')->info('Autorización datos firmada', [
            'modelo'   => 'AutorizacionDatos',
            'id'       => $autorizacion->id,
            'paciente' => $autorizacion->paciente->nombre_completo ?? '',
            'ip'       => $request->getClientIp(),
            'hash'     => $trazabilidad['documento_hash'],
            'token'    => $trazabilidad['firma_verificacion_token'],
        ]);

        Cache::forget('configuracion_consultorio');

        return redirect()->route('autorizacion.show', $id)
            ->with('exito', 'Firma registrada correctamente.');
    }

    public function pdf($id)
    {
        $autorizacion = AutorizacionDatos::with(['paciente', 'registradoPor'])->findOrFail($id);
        $config       = Configuracion::obtener();

        $pdf = Pdf::loadView('autorizacion.pdf', compact('autorizacion', 'config'))
            ->setPaper('letter', 'portrait');

        if (request()->boolean('raw')) {
            return $pdf->stream("autorizacion-{$autorizacion->numero_autorizacion}.pdf");
        }

        $urlPdf = route('autorizacion.pdf', $id) . '?raw=1';
        $titulo = 'Autorización ' . $autorizacion->numero_autorizacion;
        return view('layouts.pdf-viewer', compact('urlPdf', 'titulo'));
    }

    public function destroy($id)
    {
        $autorizacion = AutorizacionDatos::findOrFail($id);
        $autorizacion->update(['activo' => false]);

        return redirect()->route('pacientes.show', $autorizacion->paciente_id)
            ->with('exito', 'Autorización desactivada.');
    }
}
