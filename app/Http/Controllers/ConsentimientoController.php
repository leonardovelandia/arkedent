<?php

namespace App\Http\Controllers;

use App\Models\Consentimiento;
use App\Models\Paciente;
use App\Models\PlantillaConsentimiento;
use App\Traits\FormateaCampos;
use App\Traits\TrazabilidadFirma;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsentimientoController extends Controller
{
    use FormateaCampos;

    // ── Listado ───────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Consentimiento::with(['paciente', 'doctor'])
            ->where('activo', true)
            ->orderByDesc('fecha_generacion')
            ->orderByDesc('created_at');

        if ($buscar = $request->input('buscar')) {
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhereHas('paciente', function ($qp) use ($buscar) {
                      $qp->where('nombre',   'like', "%{$buscar}%")
                         ->orWhere('apellido','like', "%{$buscar}%")
                         ->orWhere('numero_documento', 'like', "%{$buscar}%");
                  });
            });
        }

        if ($request->input('estado') === 'firmado') {
            $query->where('firmado', true);
        } elseif ($request->input('estado') === 'pendiente') {
            $query->where('firmado', false);
        }

        if ($pacienteId = $request->input('paciente_id')) {
            $query->where('paciente_id', $pacienteId);
        }

        $consentimientos = $query->paginate(15)->withQueryString();

        return view('consentimientos.index', compact('consentimientos'));
    }

    // ── Crear ─────────────────────────────────────────────────
    public function create(Request $request)
    {
        $pacientes  = Paciente::where('activo', true)->orderBy('apellido')->orderBy('nombre')->get();
        $plantillas = PlantillaConsentimiento::activas()->orderBy('nombre')->get();
        $paciente   = $request->filled('paciente_id')
            ? Paciente::find($request->input('paciente_id'))
            : null;

        return view('consentimientos.create', compact('pacientes', 'plantillas', 'paciente'));
    }

    // ── Guardar ───────────────────────────────────────────────
    public function store(Request $request)
    {
        $validado = $request->validate([
            'paciente_id'      => 'required|exists:pacientes,id',
            'plantilla_id'     => 'nullable|exists:plantillas_consentimiento,id',
            'nombre'           => 'required|string|max:150',
            'contenido'        => 'required|string',
            'fecha_generacion' => 'required|date',
            'observaciones'    => 'nullable|string',
        ]);

        $paciente = Paciente::findOrFail($validado['paciente_id']);
        $doctor   = Auth::user();

        $consentimiento = new Consentimiento($validado);
        $consentimiento->user_id  = $doctor->id;
        $consentimiento->contenido = $consentimiento->reemplazarVariables($paciente, $doctor);
        $consentimiento->save();

        return redirect()->route('consentimientos.show', $consentimiento)
                         ->with('exito', 'Consentimiento creado correctamente.');
    }

    // ── Detalle ───────────────────────────────────────────────
    public function show(string $id)
    {
        $consentimiento = Consentimiento::with(['paciente', 'doctor', 'plantilla'])->findOrFail($id);

        return view('consentimientos.show', compact('consentimiento'));
    }

    // ── Editar ────────────────────────────────────────────────
    public function edit(string $id)
    {
        $consentimiento = Consentimiento::with('paciente')->findOrFail($id);

        if ($consentimiento->firmado) {
            return redirect()->route('consentimientos.show', $consentimiento)
                             ->with('error', 'No se puede editar un consentimiento ya firmado.');
        }

        $pacientes  = Paciente::where('activo', true)->orderBy('apellido')->orderBy('nombre')->get();
        $plantillas = PlantillaConsentimiento::activas()->orderBy('nombre')->get();

        return view('consentimientos.edit', compact('consentimiento', 'pacientes', 'plantillas'));
    }

    // ── Actualizar ────────────────────────────────────────────
    public function update(Request $request, string $id)
    {
        $consentimiento = Consentimiento::findOrFail($id);

        if ($consentimiento->firmado) {
            return redirect()->route('consentimientos.show', $consentimiento)
                             ->with('error', 'No se puede modificar un consentimiento ya firmado.');
        }

        $validado = $request->validate([
            'nombre'           => 'required|string|max:150',
            'contenido'        => 'required|string',
            'fecha_generacion' => 'required|date',
            'observaciones'    => 'nullable|string',
        ]);

        $consentimiento->update($validado);

        return redirect()->route('consentimientos.show', $consentimiento)
                         ->with('exito', 'Consentimiento actualizado correctamente.');
    }

    // ── Firmar ────────────────────────────────────────────────
    public function firmar(Request $request, string $id)
    {
        $request->validate([
            'firma_data' => 'required|string',
        ]);

        $consentimiento = Consentimiento::findOrFail($id);

        if ($consentimiento->firmado) {
            return response()->json(['error' => 'Ya está firmado.'], 422);
        }

        $firmaData    = $request->input('firma_data');
        $trazabilidad = TrazabilidadFirma::generarTrazabilidad(
            $request,
            $firmaData,
            [
                'id'       => (string) $consentimiento->id,
                'numero'   => $consentimiento->numero_consentimiento ?? '',
                'paciente' => $consentimiento->paciente->nombre_completo ?? '',
                'doc'      => $consentimiento->paciente->numero_documento ?? '',
                'tipo'     => $consentimiento->tipo ?? '',
                'fecha'    => $consentimiento->fecha_generacion?->toDateString() ?? now()->toDateString(),
            ]
        );

        $consentimiento->update(array_merge(
            [
                'firmado'     => true,
                'firma_data'  => $firmaData,
                'fecha_firma' => now(),
                'ip_firma'    => $request->ip(),
            ],
            $trazabilidad
        ));

        \Log::channel('firmas')->info('Consentimiento firmado', [
            'modelo'   => 'Consentimiento',
            'id'       => $consentimiento->id,
            'numero'   => $consentimiento->numero_consentimiento,
            'paciente' => $consentimiento->paciente->nombre_completo ?? '',
            'ip'       => $trazabilidad['firma_ip'] ?? $request->ip(),
            'hash'     => $trazabilidad['documento_hash'],
            'token'    => $trazabilidad['firma_verificacion_token'],
        ]);

        return response()->json(['ok' => true, 'mensaje' => 'Firma registrada correctamente.']);
    }

    // ── PDF ───────────────────────────────────────────────────
    public function pdf(string $id)
    {
        $consentimiento = Consentimiento::with(['paciente', 'doctor'])->findOrFail($id);

        $pdf = Pdf::loadView('consentimientos.pdf', compact('consentimiento'))
                  ->setPaper('a4', 'portrait');

        if (request()->boolean('raw')) {
            return $pdf->stream('consentimiento-' . $consentimiento->id . '.pdf');
        }

        $urlPdf = route('consentimientos.pdf', $consentimiento) . '?raw=1';
        $titulo = 'Consentimiento ' . $consentimiento->numero_consentimiento;
        return view('layouts.pdf-viewer', compact('urlPdf', 'titulo'));
    }

    // ── Eliminar (soft) ───────────────────────────────────────
    public function destroy(string $id)
    {
        $consentimiento = Consentimiento::findOrFail($id);

        if ($consentimiento->firmado) {
            return redirect()->route('consentimientos.index')
                             ->with('error', 'No se puede eliminar un consentimiento firmado.');
        }

        $consentimiento->update(['activo' => false]);

        return redirect()->route('consentimientos.index')
                         ->with('exito', 'Consentimiento eliminado.');
    }
}
