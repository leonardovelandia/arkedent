<?php

namespace App\Http\Controllers;

use App\Models\Evolucion;
use App\Models\CorreccionEvolucion;
use App\Models\Paciente;
use App\Models\HistoriaClinica;
use App\Traits\FormateaCampos;
use App\Traits\TrazabilidadFirma;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class EvolucionController extends Controller
{
    use FormateaCampos, AuthorizesRequests;


    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $pacienteId = $request->input('paciente_id');

        $evoluciones = Evolucion::with(['paciente', 'doctor'])
            ->activas()
            ->when($pacienteId, fn($q) => $q->where('paciente_id', $pacienteId))
            ->when($buscar, function ($q) use ($buscar) {
                $q->where('procedimiento', 'like', "%{$buscar}%")
                    ->orWhereHas(
                        'paciente',
                        fn($p) =>
                        $p->where('nombre', 'like', "%{$buscar}%")
                            ->orWhere('apellido', 'like', "%{$buscar}%")
                            ->orWhere('numero_documento', 'like', "%{$buscar}%")
                    );
            })
            ->orderBy('fecha', 'desc')
            ->paginate(in_array((int) $request->input('per_page', 10), [10, 25, 50]) ? (int) $request->input('per_page', 10) : 10)
            ->withQueryString();

        $pacienteFiltro = $pacienteId ? Paciente::find($pacienteId) : null;

        return view('evoluciones.index', compact('evoluciones', 'buscar', 'pacienteFiltro'));
    }

    public function create(Request $request)
    {
        $pacienteId = $request->input('paciente_id');
        $paciente = null;
        $historia = null;

        if ($pacienteId) {
            $paciente = Paciente::findOrFail($pacienteId);
            $historia = $paciente->historiaClinica;

            if (!$historia) {
                return redirect()->route('historias.create', ['paciente_id' => $pacienteId])
                    ->with('error', 'El paciente no tiene historia clínica. Cree una primero.');
            }
        }

        $pacientes = Paciente::with('historiaClinica')->activos()->orderBy('apellido')->get();

        // Mapa paciente_id → historia_clinica_id para el buscador
        $historiasMap = $pacientes->pluck('historiaClinica.id', 'id');

        $materialesInventario = \App\Models\Material::where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'unidad_medida', 'stock_actual']);

        return view('evoluciones.create', compact('paciente', 'historia', 'pacientes', 'historiasMap', 'materialesInventario'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'historia_clinica_id' => 'required|exists:historias_clinicas,id',
            'fecha' => 'required|date',
            'hora' => 'nullable|date_format:H:i',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i',
            'procedimiento' => 'required|string|max:300',
            'descripcion' => 'required|string|max:2000',
            'observaciones' => 'nullable|string|max:2000',
            'materiales' => 'nullable|array',
            'materiales.*.nombre' => 'required_with:materiales|string|max:200',
            'materiales.*.cantidad' => 'required_with:materiales|string|max:100',
            'presion_arterial' => 'nullable|string|max:30',
            'frecuencia_cardiaca' => 'nullable|string|max:30',
            'proxima_cita_fecha' => 'nullable|date',
            'proxima_cita_procedimiento' => 'nullable|string|max:300',
            'dientes_tratados' => 'nullable|string|max:150',
        ]);

        // Deserializar materiales si viene como JSON string desde el hidden input
        if ($request->filled('materiales_json')) {
            $decoded = json_decode($request->input('materiales_json'), true);
            $validated['materiales'] = is_array($decoded) ? $decoded : null;
        }

        $datos = $this->formatearDatos($validated);
        $datos['user_id'] = Auth::id();
        $datos['hora_inicio'] = $request->hora_inicio ?: null;
        $datos['hora_fin'] = $request->hora_fin ?: null;

        $evolucion = new Evolucion();

        $evolucion->paciente_id = $datos['paciente_id'];
        $evolucion->historia_clinica_id = $datos['historia_clinica_id'];
        $evolucion->fecha = $datos['fecha'];
        $evolucion->hora = $datos['hora'] ?? null;
        $evolucion->hora_inicio = $datos['hora_inicio'] ?? null;
        $evolucion->hora_fin = $datos['hora_fin'] ?? null;
        $evolucion->procedimiento = $datos['procedimiento'];
        $evolucion->descripcion = $datos['descripcion'];
        $evolucion->materiales = $datos['materiales'] ?? null;
        $evolucion->presion_arterial = $datos['presion_arterial'] ?? null;
        $evolucion->frecuencia_cardiaca = $datos['frecuencia_cardiaca'] ?? null;
        $evolucion->proxima_cita_fecha = $datos['proxima_cita_fecha'] ?? null;
        $evolucion->proxima_cita_procedimiento = $datos['proxima_cita_procedimiento'] ?? null;
        $evolucion->observaciones = $datos['observaciones'] ?? null;
        $evolucion->dientes_tratados = $datos['dientes_tratados'] ?? null;
        $evolucion->activo = true;

        // 🔐 IMPORTANTE
        $evolucion->user_id = Auth::id();

        $evolucion->save();

        // Descontar materiales del inventario automáticamente
        if (!empty($evolucion->materiales)) {
            foreach ($evolucion->materiales as $materialUsado) {
                $material = \App\Models\Material::where('activo', true)
                    ->where('nombre', 'like', '%' . trim($materialUsado['nombre']) . '%')
                    ->first();

                if ($material) {
                    $cantidadTexto = trim($materialUsado['cantidad'] ?? '1');
                    $cantidad = (float) filter_var($cantidadTexto, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    if ($cantidad <= 0)
                        $cantidad = 1;

                    if ($material->stock_actual >= $cantidad) {
                        $material->registrarSalida(
                            $cantidad,
                            "Usado en evolución {$evolucion->numero_evolucion} — Paciente: {$evolucion->paciente->nombre_completo} — Procedimiento: {$evolucion->procedimiento}",
                            auth()->id(),
                            $evolucion->id
                        );
                    }
                }
            }
        }

        return redirect()->route('evoluciones.show', $evolucion)
            ->with('exito', 'Evolución registrada correctamente.');
    }

    public function show($id)
    {
        $evolucion = Evolucion::with(['paciente', 'historiaClinica', 'doctor', 'correcciones.usuario'])->findOrFail($id);
        $this->authorize('view', $evolucion);
        return view('evoluciones.show', compact('evolucion'));
    }

    public function edit($id)
    {
        $evolucion = Evolucion::with(['paciente', 'historiaClinica'])->findOrFail($id);
        $this->authorize('update', $evolucion);
        // Firmada = no editable nunca
        if ($evolucion->firmado) {
            return redirect()
                ->route('evoluciones.show', $evolucion)
                ->with('aviso', 'Esta evolución está firmada. Puedes agregar una nota de corrección.');
        }

        // Sin firmar pero con más de 24 horas = no editable
        if ($evolucion->created_at->diffInHours(now()) >= 24) {
            return redirect()
                ->route('evoluciones.show', $evolucion)
                ->with('aviso', 'Esta evolución tiene más de 24 horas y no puede editarse. Puedes agregar una nota de corrección.');
        }

        $pacientes = Paciente::activos()->orderBy('nombre')->get();
        $materialesInventario = \App\Models\Material::where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'unidad_medida', 'stock_actual']);
        return view('evoluciones.edit', compact('evolucion', 'pacientes', 'materialesInventario'));
    }

    public function update(Request $request, $id)
    {
        $evolucion = Evolucion::findOrFail($id);
        $this->authorize('update', $evolucion);
        if ($evolucion->firmado || $evolucion->created_at->diffInHours(now()) >= 24) {
            return redirect()
                ->route('evoluciones.show', $evolucion)
                ->with('error', 'No se puede modificar esta evolución.');
        }

        $validated = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'historia_clinica_id' => 'required|exists:historias_clinicas,id',
            'fecha' => 'required|date',
            'hora' => 'nullable|date_format:H:i',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i',
            'procedimiento' => 'required|string|max:300',
            'descripcion' => 'required|string|max:2000',
            'materiales' => 'nullable|array',
            'materiales.*.nombre' => 'required_with:materiales|string|max:200',
            'materiales.*.cantidad' => 'required_with:materiales|string|max:100',
            'presion_arterial' => 'nullable|string|max:30',
            'frecuencia_cardiaca' => 'nullable|string|max:30',
            'proxima_cita_fecha' => 'nullable|date',
            'proxima_cita_procedimiento' => 'nullable|string|max:300',
            'observaciones' => 'nullable|string|max:2000',
            'dientes_tratados' => 'nullable|string|max:150',
        ]);

        if ($request->filled('materiales_json')) {
            $decoded = json_decode($request->input('materiales_json'), true);
            $validated['materiales'] = is_array($decoded) ? $decoded : null;
        }

        $datos = $this->formatearDatos($validated);
        $datos['hora_inicio'] = $request->hora_inicio ?: null;
        $datos['hora_fin'] = $request->hora_fin ?: null;

        $evolucion->paciente_id = $datos['paciente_id'];
        $evolucion->historia_clinica_id = $datos['historia_clinica_id'];
        $evolucion->fecha = $datos['fecha'];
        $evolucion->hora = $datos['hora'] ?? null;
        $evolucion->hora_inicio = $datos['hora_inicio'] ?? null;
        $evolucion->hora_fin = $datos['hora_fin'] ?? null;
        $evolucion->procedimiento = $datos['procedimiento'];
        $evolucion->descripcion = $datos['descripcion'];
        $evolucion->materiales = $datos['materiales'] ?? null;
        $evolucion->presion_arterial = $datos['presion_arterial'] ?? null;
        $evolucion->frecuencia_cardiaca = $datos['frecuencia_cardiaca'] ?? null;
        $evolucion->proxima_cita_fecha = $datos['proxima_cita_fecha'] ?? null;
        $evolucion->proxima_cita_procedimiento = $datos['proxima_cita_procedimiento'] ?? null;
        $evolucion->observaciones = $datos['observaciones'] ?? null;
        $evolucion->dientes_tratados = $datos['dientes_tratados'] ?? null;

        $evolucion->save();

        return redirect()->route('evoluciones.show', $evolucion)
            ->with('exito', 'Evolución actualizada correctamente.');
    }

    public function destroy($id)
    {
        $evolucion = Evolucion::findOrFail($id);
        $evolucion->update(['activo' => false]);

        return redirect()->route('evoluciones.index')
            ->with('exito', 'Evolución desactivada correctamente.');
    }

    // ── Firma: vista ──────────────────────────────────────────
    public function firmarVista($id)
    {
        $evolucion = Evolucion::with('paciente')->findOrFail($id);

        if ($evolucion->firmado) {
            return redirect()->route('evoluciones.show', $id)
                ->with('aviso', 'Esta evolución ya fue firmada.');
        }

        return view('evoluciones.firmar', compact('evolucion'));
    }

    // ── Firma: guardar ────────────────────────────────────────
    public function firmar(Request $request, $id)
    {
        $request->validate(['firma_data' => 'required|string']);

        $evolucion = Evolucion::findOrFail($id);

        if ($evolucion->firmado) {
            return response()->json(['error' => 'Esta evolución ya fue firmada.'], 422);
        }
        $firmaData = $request->firma_data;
        $trazabilidad = TrazabilidadFirma::generarTrazabilidad(
            $request,
            $firmaData,
            [
                'id' => (string) $evolucion->id,
                'numero' => $evolucion->numero_evolucion ?? '',
                'paciente' => $evolucion->paciente->nombre_completo ?? '',
                'doc' => $evolucion->paciente->numero_documento ?? '',
                'procedimiento' => $evolucion->procedimiento ?? '',
                'fecha' => $evolucion->fecha instanceof \Carbon\Carbon ? $evolucion->fecha->toDateString() : (string) $evolucion->fecha,
                'doctor' => auth()->user()->name ?? '',
            ]
        );

        $evolucion->update(array_merge(
            [
                'firmado' => true,
                'firma_data' => $firmaData,
                'fecha_firma' => now(),
                'ip_firma' => $request->getClientIp(),
            ],
            $trazabilidad
        ));

        \Log::channel('firmas')->info('Evolución firmada', [
            'modelo' => 'Evolucion',
            'id' => $evolucion->id,
            'numero' => $evolucion->numero_evolucion,
            'paciente' => $evolucion->paciente->nombre_completo ?? '',
            'ip' => $request->getClientIp(),
            'hash' => $trazabilidad['documento_hash'],
            'token' => $trazabilidad['firma_verificacion_token'],
        ]);

        return response()->json(['success' => true, 'message' => 'Evolución firmada correctamente']);
    }

    // ── PDF ───────────────────────────────────────────────────
    public function pdf($id)
    {
        $evolucion = Evolucion::with(['paciente', 'doctor', 'correcciones.usuario', 'historiaClinica'])->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('evoluciones.pdf', compact('evolucion'));
        $nombreArchivo = 'evolucion-' . $evolucion->paciente->numero_historia . '-' . $evolucion->fecha->format('Y-m-d') . '.pdf';

        if (request()->boolean('raw')) {
            return $pdf->stream($nombreArchivo);
        }

        $urlPdf = route('evoluciones.pdf', $id) . '?raw=1';
        $titulo = 'Evolución ' . $evolucion->paciente->numero_historia;
        return view('layouts.pdf-viewer', compact('urlPdf', 'titulo'));
    }

    // ── Vista corrección ──────────────────────────────────────
    public function correccionVista($id)
    {
        $evolucion = Evolucion::with(['paciente', 'correcciones.usuario'])->findOrFail($id);

        // Si es editable redirigir a edición normal
        if (!$evolucion->firmado && $evolucion->created_at->diffInHours(now()) < 24) {
            return redirect()
                ->route('evoluciones.edit', $evolucion)
                ->with('aviso', 'La evolución aún puede editarse directamente.');
        }

        $camposDisponibles = [
            'procedimiento' => 'Procedimiento',
            'descripcion' => 'Descripción clínica',
            'presion_arterial' => 'Presión arterial',
            'frecuencia_cardiaca' => 'Frecuencia cardiaca',
            'dientes_tratados' => 'Dientes tratados',
            'proxima_cita_procedimiento' => 'Próxima cita — procedimiento',
            'observaciones' => 'Observaciones',
        ];

        return view('evoluciones.correccion', compact('evolucion', 'camposDisponibles'));
    }

    // ── Firma corrección: vista ───────────────────────────────
    public function firmarCorreccionVista($correccionId)
    {
        $correccion = CorreccionEvolucion::with('evolucion.paciente', 'usuario')->findOrFail($correccionId);

        if ($correccion->firmado) {
            return redirect()
                ->route('evoluciones.show', $correccion->evolucion_id)
                ->with('aviso', 'Esta corrección ya fue firmada.');
        }

        return view('evoluciones.firmar_correccion', compact('correccion'));
    }

    // ── Firma corrección: guardar ─────────────────────────────
    public function firmarCorreccion(Request $request, $correccionId)
    {
        $request->validate(['firma_data' => 'required|string']);

        $correccion = CorreccionEvolucion::findOrFail($correccionId);

        if ($correccion->firmado) {
            return response()->json(['error' => 'Ya está firmada'], 400);
        }

        $firmaData = $request->firma_data;
        $trazabilidad = TrazabilidadFirma::generarTrazabilidad(
            $request,
            $firmaData,
            [
                'id' => (string) $correccion->id,
                'tipo' => 'correccion_evolucion',
                'campo' => $correccion->campo_corregido ?? '',
                'paciente' => $correccion->evolucion->paciente->nombre_completo ?? '',
                'doc' => $correccion->evolucion->paciente->numero_documento ?? '',
                'fecha' => now()->toDateString(),
            ]
        );

        $correccion->update(array_merge(
            [
                'firmado' => true,
                'firma_data' => $firmaData,
                'fecha_firma' => now(),
                'ip_firma' => $request->getClientIp(),
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
        $evolucion = Evolucion::findOrFail($id);

        $request->validate([
            'campo_corregido' => 'required|string',
            'valor_nuevo' => 'required|string',
            'motivo' => 'required|string|min:10',
        ]);
        $camposPermitidos = [
            'procedimiento',
            'descripcion',
            'presion_arterial',
            'frecuencia_cardiaca',
            'dientes_tratados',
            'proxima_cita_procedimiento',
            'observaciones',
        ];

        if (!in_array($request->campo_corregido, $camposPermitidos)) {
            abort(403, 'Campo no permitido');
        }

        $valorAnterior = $evolucion->{$request->campo_corregido} ?? '';

        CorreccionEvolucion::create([
            'evolucion_id' => $evolucion->id,
            'user_id' => auth()->id(),
            'campo_corregido' => $request->campo_corregido,
            'valor_anterior' => is_array($valorAnterior) ? json_encode($valorAnterior) : (string) $valorAnterior,
            'valor_nuevo' => $request->valor_nuevo,
            'motivo' => $request->motivo,
        ]);

        return redirect()
            ->route('evoluciones.show', $evolucion)
            ->with('exito', 'Corrección registrada. El documento original permanece intacto.');
    }
}
