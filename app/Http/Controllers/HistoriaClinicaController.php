<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
use App\Models\CorreccionHistoria;
use App\Models\Paciente;
use App\Traits\FormateaCampos;
use App\Traits\TrazabilidadFirma;
use Illuminate\Http\Request;

class HistoriaClinicaController extends Controller
{
    use FormateaCampos;

    // ── Listado de historias ──────────────────────────────────
    public function index(Request $request)
    {
        $query = HistoriaClinica::with('paciente')->latest();

        if ($buscar = $request->input('buscar')) {
            $query->whereHas('paciente', function ($q) use ($buscar) {
                $q->where('nombre',   'like', "%{$buscar}%")
                  ->orWhere('apellido','like', "%{$buscar}%")
                  ->orWhere('numero_documento', 'like', "%{$buscar}%");
            });
        }

        $historias = $query->paginate(15)->withQueryString();

        return view('historias.index', compact('historias'));
    }

    // ── Formulario de creación ────────────────────────────────
    public function create(Request $request)
    {
        $paciente = null;

        if ($request->filled('paciente_id')) {
            $paciente = Paciente::findOrFail($request->input('paciente_id'));

            if ($paciente->historiaClinica) {
                return redirect()->route('historias.show', $paciente->historiaClinica->id)
                                 ->with('info', 'Este paciente ya tiene una historia clínica.');
            }
        }

        $pacientes = Paciente::where('activo', true)->orderBy('apellido')->get();

        return view('historias.create', compact('paciente', 'pacientes'));
    }

    // ── Guardar nueva historia ────────────────────────────────
    public function store(Request $request)
    {
        $validado = $request->validate([
            'paciente_id'              => 'required|exists:pacientes,id',
            'fecha_apertura'           => 'required|date',
            'motivo_consulta'          => 'required|string',
            'enfermedad_actual'        => 'nullable|string',
            'antecedentes_medicos'     => 'nullable|string',
            'medicamentos_actuales'    => 'nullable|string',
            'alergias'                 => 'nullable|string',
            'antecedentes_familiares'  => 'nullable|string',
            'antecedentes_odontologicos' => 'nullable|string',
            'habitos'                  => 'nullable|string',
            'presion_arterial'         => 'nullable|string|max:20',
            'frecuencia_cardiaca'      => 'nullable|string|max:20',
            'temperatura'              => 'nullable|string|max:10',
            'peso'                     => 'nullable|numeric',
            'talla'                    => 'nullable|numeric',
            'odontograma'              => 'nullable|string',
            'hallazgos'                => 'nullable|string',
            'observaciones_generales'  => 'nullable|string',
        ]);

        // Verificar que el paciente no tenga ya una historia
        $existe = HistoriaClinica::where('paciente_id', $validado['paciente_id'])->exists();
        if ($existe) {
            return back()->with('error', 'Este paciente ya tiene una historia clínica registrada.');
        }

        $datos = $this->formatearDatos($validado);

        $datos['odontograma'] = $validado['odontograma']
            ? json_decode($validado['odontograma'], true)
            : null;

        $datos['hallazgos'] = $validado['hallazgos']
            ? json_decode($validado['hallazgos'], true)
            : null;

        $historia = HistoriaClinica::create($datos);

        return redirect()->route('historias.show', $historia)
                         ->with('exito', 'Historia clínica creada correctamente.');
    }

    // ── Detalle de historia ───────────────────────────────────
    public function show(string $id)
    {
        $historia = HistoriaClinica::with('paciente', 'correcciones.usuario')->findOrFail($id);

        return view('historias.show', compact('historia'));
    }

    // ── Formulario de edición ─────────────────────────────────
    public function edit(string $id)
    {
        $historia = HistoriaClinica::with('paciente')->findOrFail($id);

        // Si está firmada no se puede editar — redirigir a corrección
        if ($historia->firmado) {
            return redirect()
                ->route('historias.show', $historia)
                ->with('aviso', 'Esta historia clínica está firmada y no puede editarse. Puedes agregar una nota de corrección.');
        }

        return view('historias.edit', compact('historia'));
    }

    // ── Actualizar historia ───────────────────────────────────
    public function update(Request $request, string $id)
    {
        $historia = HistoriaClinica::findOrFail($id);

        if ($historia->firmado) {
            return redirect()
                ->route('historias.show', $historia)
                ->with('error', 'No se puede modificar una historia clínica firmada.');
        }

        $validado = $request->validate([
            'fecha_apertura'           => 'required|date',
            'motivo_consulta'          => 'required|string',
            'enfermedad_actual'        => 'nullable|string',
            'antecedentes_medicos'     => 'nullable|string',
            'medicamentos_actuales'    => 'nullable|string',
            'alergias'                 => 'nullable|string',
            'antecedentes_familiares'  => 'nullable|string',
            'antecedentes_odontologicos' => 'nullable|string',
            'habitos'                  => 'nullable|string',
            'presion_arterial'         => 'nullable|string|max:20',
            'frecuencia_cardiaca'      => 'nullable|string|max:20',
            'temperatura'              => 'nullable|string|max:10',
            'peso'                     => 'nullable|numeric',
            'talla'                    => 'nullable|numeric',
            'odontograma'              => 'nullable|string',
            'hallazgos'                => 'nullable|string',
            'observaciones_generales'  => 'nullable|string',
        ]);

        $datos = $this->formatearDatos($validado);

        $datos['odontograma'] = $validado['odontograma']
            ? json_decode($validado['odontograma'], true)
            : $historia->odontograma;

        $datos['hallazgos'] = !empty($validado['hallazgos'])
            ? json_decode($validado['hallazgos'], true)
            : $historia->hallazgos;

        $historia->update($datos);

        return redirect()->route('historias.show', $historia)
                         ->with('exito', 'Historia clínica actualizada correctamente.');
    }

    // ── Firma: vista ──────────────────────────────────────────
    public function firmarVista($id)
    {
        $historia = HistoriaClinica::with('paciente')->findOrFail($id);
        return view('historias.firmar', compact('historia'));
    }

    // ── Firma: guardar ────────────────────────────────────────
    public function firmar(Request $request, $id)
    {
        $request->validate(['firma_data' => 'required|string']);
        $historia  = HistoriaClinica::with('paciente')->findOrFail($id);
        $firmaData = $request->firma_data;

        $trazabilidad = TrazabilidadFirma::generarTrazabilidad(
            $request,
            $firmaData,
            [
                'id'       => (string) $historia->id,
                'numero'   => $historia->numero_historia ?? '',
                'paciente' => $historia->paciente->nombre_completo ?? '',
                'doc'      => $historia->paciente->numero_documento ?? '',
                'fecha'    => $historia->fecha_apertura?->toDateString() ?? now()->toDateString(),
            ]
        );

        $historia->update(array_merge(
            [
                'firmado'     => true,
                'firma_data'  => $firmaData,
                'fecha_firma' => now(),
                'ip_firma'    => $request->ip(),
            ],
            $trazabilidad
        ));

        \Log::channel('firmas')->info('Historia Clínica firmada', [
            'modelo'   => 'HistoriaClinica',
            'id'       => $historia->id,
            'numero'   => $historia->numero_historia,
            'paciente' => $historia->paciente->nombre_completo ?? '',
            'ip'       => $request->ip(),
            'hash'     => $trazabilidad['documento_hash'],
            'token'    => $trazabilidad['firma_verificacion_token'],
        ]);

        return response()->json(['success' => true, 'message' => 'Historia firmada correctamente']);
    }

    // ── PDF ───────────────────────────────────────────────────
    public function pdf($id)
    {
        $historia = HistoriaClinica::with('paciente', 'correcciones.usuario')->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('historias.pdf', compact('historia'));
        $nombreArchivo = 'historia-clinica-' . $historia->paciente->numero_historia . '.pdf';

        if (request()->boolean('raw')) {
            return $pdf->stream($nombreArchivo);
        }

        $urlPdf = route('historias.pdf', $id) . '?raw=1';
        $titulo = 'Historia Clínica ' . $historia->paciente->numero_historia;
        return view('layouts.pdf-viewer', compact('urlPdf', 'titulo'));
    }

    // ── Vista corrección ──────────────────────────────────────
    public function correccionVista($id)
    {
        $historia = HistoriaClinica::with('paciente', 'correcciones.usuario')->findOrFail($id);

        if (!$historia->firmado) {
            return redirect()
                ->route('historias.edit', $historia)
                ->with('aviso', 'La historia no está firmada aún. Puedes editarla directamente.');
        }

        $camposDisponibles = [
            'motivo_consulta'            => 'Motivo de consulta',
            'enfermedad_actual'          => 'Enfermedad actual',
            'antecedentes_medicos'       => 'Antecedentes médicos',
            'medicamentos_actuales'      => 'Medicamentos actuales',
            'alergias'                   => 'Alergias',
            'antecedentes_familiares'    => 'Antecedentes familiares',
            'antecedentes_odontologicos' => 'Antecedentes odontológicos',
            'habitos'                    => 'Hábitos',
            'presion_arterial'           => 'Presión arterial',
            'frecuencia_cardiaca'        => 'Frecuencia cardiaca',
            'temperatura'                => 'Temperatura',
            'peso'                       => 'Peso',
            'talla'                      => 'Talla',
            'observaciones_generales'    => 'Observaciones generales',
        ];

        return view('historias.correccion', compact('historia', 'camposDisponibles'));
    }

    // ── Firma corrección: vista ───────────────────────────────
    public function firmarCorreccionVista($correccionId)
    {
        $correccion = CorreccionHistoria::with('historia.paciente', 'usuario')->findOrFail($correccionId);

        if ($correccion->firmado) {
            return redirect()
                ->route('historias.show', $correccion->historia_clinica_id)
                ->with('aviso', 'Esta corrección ya fue firmada.');
        }

        return view('historias.firmar_correccion', compact('correccion'));
    }

    // ── Firma corrección: guardar ─────────────────────────────
    public function firmarCorreccion(Request $request, $correccionId)
    {
        $request->validate(['firma_data' => 'required|string']);

        $correccion = CorreccionHistoria::with('historia.paciente')->findOrFail($correccionId);

        if ($correccion->firmado) {
            return response()->json(['error' => 'Ya está firmada'], 400);
        }

        $firmaData    = $request->firma_data;
        $trazabilidad = TrazabilidadFirma::generarTrazabilidad(
            $request,
            $firmaData,
            [
                'id'     => (string) $correccion->id,
                'numero' => $correccion->numero_correccion ?? '',
                'campo'  => $correccion->campo_corregido ?? '',
                'motivo' => $correccion->motivo ?? '',
            ]
        );

        $correccion->update(array_merge(
            [
                'firmado'     => true,
                'firma_data'  => $firmaData,
                'fecha_firma' => now(),
                'ip_firma'    => $request->ip(),
            ],
            $trazabilidad
        ));

        return response()->json([
            'success' => true,
            'message' => 'Corrección firmada correctamente',
        ]);
    }

    // ── Guardar corrección ────────────────────────────────────
    public function correccion(Request $request, $id)
    {
        $historia = HistoriaClinica::findOrFail($id);

        if (!$historia->firmado) {
            return redirect()->route('historias.edit', $historia);
        }

        $request->validate([
            'campo_corregido' => 'required|string',
            'valor_nuevo'     => 'required|string',
            'motivo'          => 'required|string|min:10',
        ]);

        $valorAnterior = $historia->{$request->campo_corregido} ?? '';

        CorreccionHistoria::create([
            'historia_clinica_id' => $historia->id,
            'user_id'             => auth()->id(),
            'campo_corregido'     => $request->campo_corregido,
            'valor_anterior'      => is_array($valorAnterior) ? json_encode($valorAnterior) : (string) $valorAnterior,
            'valor_nuevo'         => $request->valor_nuevo,
            'motivo'              => $request->motivo,
        ]);

        return redirect()
            ->route('historias.show', $historia)
            ->with('exito', 'Corrección registrada correctamente. El documento original permanece intacto.');
    }
}
