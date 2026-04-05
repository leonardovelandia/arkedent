<?php

namespace App\Http\Controllers;

use App\Models\Presupuesto;
use App\Models\ItemPresupuesto;
use App\Models\Paciente;
use App\Traits\FormateaCampos;
use App\Traits\TrazabilidadFirma;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresupuestoController extends Controller
{
    use FormateaCampos;

    // ── Listado ─────────────────────────────────────────────────

    public function index(Request $request)
    {
        // Marcar vencidos automáticamente
        Presupuesto::whereIn('estado', ['borrador', 'enviado'])
            ->where('fecha_vencimiento', '<', Carbon::today())
            ->update(['estado' => 'vencido']);

        $query = Presupuesto::with(['paciente', 'doctor'])
            ->where('activo', true)
            ->orderBy('created_at', 'desc');

        if ($buscar = $request->input('buscar')) {
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_presupuesto', 'like', "%{$buscar}%")
                  ->orWhereHas('paciente', function ($qp) use ($buscar) {
                      $qp->where('nombre', 'like', "%{$buscar}%")
                         ->orWhere('apellido', 'like', "%{$buscar}%")
                         ->orWhere('numero_documento', 'like', "%{$buscar}%");
                  });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50])
            ? (int) $request->input('per_page', 10) : 10;

        $presupuestos = $query->paginate($perPage)->withQueryString();

        return view('presupuestos.index', compact('presupuestos'));
    }

    // ── Crear ───────────────────────────────────────────────────

    public function create(Request $request)
    {
        $pacientes = Paciente::activos()->orderBy('apellido')->get();
        $pacienteSeleccionado = null;
        $historiaClinica = null;

        if ($request->filled('paciente_id')) {
            $pacienteSeleccionado = Paciente::with('historiaClinica')->find($request->paciente_id);
            $historiaClinica = $pacienteSeleccionado?->historiaClinica;
        }

        $procedimientosPredefinidos = $this->procedimientosPredefinidos();

        return view('presupuestos.create', compact(
            'pacientes',
            'pacienteSeleccionado',
            'historiaClinica',
            'procedimientosPredefinidos'
        ));
    }

    // ── Guardar ─────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validado = $request->validate([
            'paciente_id'           => 'required|exists:pacientes,id',
            'fecha_generacion'      => 'required|date',
            'validez_dias'          => 'required|integer|min:1|max:365',
            'condiciones_pago'      => 'nullable|string',
            'observaciones'         => 'nullable|string',
            'descuento_porcentaje'  => 'nullable|numeric|min:0|max:100',
            'items'                 => 'required|array|min:1',
            'items.*.procedimiento' => 'required|string|max:255',
            'items.*.cantidad'      => 'required|integer|min:1',
            'items.*.valor_unitario'=> 'required|numeric|min:0',
            'items.*.diente'        => 'nullable|string|max:20',
            'items.*.cara'          => 'nullable|string|max:50',
            'items.*.notas'         => 'nullable|string|max:255',
        ]);

        $accion = $request->input('accion', 'borrador');

        DB::transaction(function () use ($validado, $accion, $request, &$presupuesto) {
            $paciente = Paciente::find($validado['paciente_id']);

            $fechaGeneracion = Carbon::parse($validado['fecha_generacion']);
            $validezDias     = (int) $validado['validez_dias'];
            $fechaVencimiento = $fechaGeneracion->copy()->addDays($validezDias);

            $descuentoPorcentaje = (float) ($validado['descuento_porcentaje'] ?? 0);

            $presupuesto = Presupuesto::create([
                'paciente_id'          => $validado['paciente_id'],
                'historia_clinica_id'  => $paciente->historiaClinica?->id,
                'user_id'              => Auth::id(),
                'fecha_generacion'     => $fechaGeneracion,
                'fecha_vencimiento'    => $fechaVencimiento,
                'validez_dias'         => $validezDias,
                'estado'               => $accion === 'enviar' ? 'enviado' : 'borrador',
                'descuento_porcentaje' => $descuentoPorcentaje,
                'condiciones_pago'     => $validado['condiciones_pago'] ?? null,
                'observaciones'        => $validado['observaciones'] ?? null,
                'subtotal'             => 0,
                'descuento_valor'      => 0,
                'total'                => 0,
            ]);

            foreach ($validado['items'] as $i => $itemData) {
                $cantidad       = (int) $itemData['cantidad'];
                $valorUnitario  = (float) $itemData['valor_unitario'];
                ItemPresupuesto::create([
                    'presupuesto_id'  => $presupuesto->id,
                    'numero_item'     => $i + 1,
                    'procedimiento'   => $itemData['procedimiento'],
                    'diente'          => $itemData['diente'] ?? null,
                    'cara'            => $itemData['cara'] ?? null,
                    'cantidad'        => $cantidad,
                    'valor_unitario'  => $valorUnitario,
                    'valor_total'     => $cantidad * $valorUnitario,
                    'notas'           => $itemData['notas'] ?? null,
                ]);
            }

            $presupuesto->calcularTotales();
        });

        return redirect()->route('presupuestos.show', $presupuesto)
            ->with('exito', 'Presupuesto ' . $presupuesto->numero_formateado . ' creado correctamente.');
    }

    // ── Mostrar ─────────────────────────────────────────────────

    public function show(string $id)
    {
        $presupuesto = Presupuesto::with(['paciente', 'doctor', 'items', 'tratamiento', 'historiaClinica'])
            ->porUuidOrFail($id);

        $config = \App\Models\Configuracion::first();

        return view('presupuestos.show', compact('presupuesto', 'config'));
    }

    // ── Editar ──────────────────────────────────────────────────

    public function edit(string $id)
    {
        $presupuesto = Presupuesto::with(['paciente', 'items'])->porUuidOrFail($id);

        if ($presupuesto->estado !== 'borrador') {
            return redirect()->route('presupuestos.show', $presupuesto)
                ->with('error', 'Solo se pueden editar presupuestos en estado borrador.');
        }

        $pacientes = Paciente::activos()->orderBy('apellido')->get();
        $procedimientosPredefinidos = $this->procedimientosPredefinidos();

        return view('presupuestos.edit', compact('presupuesto', 'pacientes', 'procedimientosPredefinidos'));
    }

    // ── Actualizar ──────────────────────────────────────────────

    public function update(Request $request, string $id)
    {
        $presupuesto = Presupuesto::porUuidOrFail($id);

        if ($presupuesto->estado !== 'borrador') {
            return redirect()->route('presupuestos.show', $presupuesto)
                ->with('error', 'Solo se pueden editar presupuestos en estado borrador.');
        }

        $validado = $request->validate([
            'fecha_generacion'      => 'required|date',
            'validez_dias'          => 'required|integer|min:1|max:365',
            'condiciones_pago'      => 'nullable|string',
            'observaciones'         => 'nullable|string',
            'descuento_porcentaje'  => 'nullable|numeric|min:0|max:100',
            'items'                 => 'required|array|min:1',
            'items.*.procedimiento' => 'required|string|max:255',
            'items.*.cantidad'      => 'required|integer|min:1',
            'items.*.valor_unitario'=> 'required|numeric|min:0',
            'items.*.diente'        => 'nullable|string|max:20',
            'items.*.cara'          => 'nullable|string|max:50',
            'items.*.notas'         => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($presupuesto, $validado, $request) {
            $fechaGeneracion  = Carbon::parse($validado['fecha_generacion']);
            $validezDias      = (int) $validado['validez_dias'];
            $fechaVencimiento = $fechaGeneracion->copy()->addDays($validezDias);
            $descuentoPorcentaje = (float) ($validado['descuento_porcentaje'] ?? 0);

            $presupuesto->update([
                'fecha_generacion'     => $fechaGeneracion,
                'fecha_vencimiento'    => $fechaVencimiento,
                'validez_dias'         => $validezDias,
                'descuento_porcentaje' => $descuentoPorcentaje,
                'condiciones_pago'     => $validado['condiciones_pago'] ?? null,
                'observaciones'        => $validado['observaciones'] ?? null,
            ]);

            // Reemplazar items
            $presupuesto->items()->delete();
            foreach ($validado['items'] as $i => $itemData) {
                $cantidad      = (int) $itemData['cantidad'];
                $valorUnitario = (float) $itemData['valor_unitario'];
                ItemPresupuesto::create([
                    'presupuesto_id' => $presupuesto->id,
                    'numero_item'    => $i + 1,
                    'procedimiento'  => $itemData['procedimiento'],
                    'diente'         => $itemData['diente'] ?? null,
                    'cara'           => $itemData['cara'] ?? null,
                    'cantidad'       => $cantidad,
                    'valor_unitario' => $valorUnitario,
                    'valor_total'    => $cantidad * $valorUnitario,
                    'notas'          => $itemData['notas'] ?? null,
                ]);
            }

            $presupuesto->calcularTotales();
        });

        return redirect()->route('presupuestos.show', $presupuesto)
            ->with('exito', 'Presupuesto actualizado correctamente.');
    }

    // ── Enviar ──────────────────────────────────────────────────

    public function enviar(Request $request, Presupuesto $presupuesto)
    {
        if ($presupuesto->estado !== 'borrador') {
            return back()->with('error', 'Solo se pueden enviar presupuestos en borrador.');
        }

        $presupuesto->update(['estado' => 'enviado']);

        return back()->with('exito', 'Presupuesto marcado como enviado al paciente.');
    }

    // ── Aprobar ─────────────────────────────────────────────────

    public function aprobar(Request $request, Presupuesto $presupuesto)
    {
        if (in_array($presupuesto->estado, ['rechazado', 'aprobado'])) {
            return back()->with('error', 'Este presupuesto no puede aprobarse.');
        }

        if (!$presupuesto->firmado) {
            return back()->with('error', 'El paciente debe firmar el presupuesto antes de aprobarlo.');
        }

        $presupuesto->aprobar();

        return back()->with('exito', 'Presupuesto aprobado. Se creó el tratamiento ' . ($presupuesto->tratamiento?->numero_formateado ?? '') . ' automáticamente.');
    }

    // ── Rechazar ────────────────────────────────────────────────

    public function rechazar(Request $request, Presupuesto $presupuesto)
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|max:500',
        ]);

        $presupuesto->update([
            'estado'          => 'rechazado',
            'motivo_rechazo'  => $request->motivo_rechazo,
        ]);

        return back()->with('exito', 'Presupuesto rechazado.');
    }

    // ── Firmar ──────────────────────────────────────────────────

    public function firmar(Request $request, Presupuesto $presupuesto)
    {
        $request->validate([
            'firma_data' => 'required|string',
        ]);

        if ($presupuesto->firmado) {
            return response()->json(['error' => 'Este presupuesto ya fue firmado.'], 422);
        }

        $firmaData    = $request->firma_data;
        $trazabilidad = TrazabilidadFirma::generarTrazabilidad(
            $request,
            $firmaData,
            [
                'id'       => (string) $presupuesto->id,
                'numero'   => $presupuesto->numero_presupuesto ?? '',
                'paciente' => $presupuesto->paciente->nombre_completo ?? '',
                'doc'      => $presupuesto->paciente->numero_documento ?? '',
                'total'    => (string) $presupuesto->total,
                'fecha'    => now()->toDateString(),
            ]
        );

        $presupuesto->update(array_merge(
            [
                'firmado'    => true,
                'firma_data' => $firmaData,
                'ip_firma'   => $request->getClientIp(),
            ],
            $trazabilidad
        ));

        $presupuesto->aprobar();

        \Log::channel('firmas')->info('Presupuesto firmado', [
            'modelo'   => 'Presupuesto',
            'id'       => $presupuesto->id,
            'numero'   => $presupuesto->numero_presupuesto,
            'paciente' => $presupuesto->paciente->nombre_completo ?? '',
            'ip'       => $request->getClientIp(),
            'hash'     => $trazabilidad['documento_hash'],
            'token'    => $trazabilidad['firma_verificacion_token'],
        ]);

        return response()->json(['success' => true]);
    }

    // ── PDF ─────────────────────────────────────────────────────

    public function pdf(Presupuesto $presupuesto)
    {
        $presupuesto->load(['paciente', 'doctor', 'items']);
        $config = \App\Models\Configuracion::first();

        $pdf = Pdf::loadView('presupuestos.pdf', compact('presupuesto', 'config'))
            ->setPaper('letter', 'portrait');

        if (request()->boolean('raw')) {
            return $pdf->stream('presupuesto-' . $presupuesto->numero_formateado . '.pdf');
        }

        $urlPdf = route('presupuestos.pdf', $presupuesto) . '?raw=1';
        $titulo = 'Presupuesto ' . $presupuesto->numero_formateado;
        return view('layouts.pdf-viewer', compact('urlPdf', 'titulo'));
    }

    // ── Eliminar (soft) ─────────────────────────────────────────

    public function destroy(string $id)
    {
        $presupuesto = Presupuesto::porUuidOrFail($id);

        if ($presupuesto->estado !== 'borrador') {
            return back()->with('error', 'Solo se pueden eliminar presupuestos en borrador.');
        }

        $presupuesto->update(['activo' => false]);

        return redirect()->route('presupuestos.index')
            ->with('exito', 'Presupuesto eliminado.');
    }

    // ── Helpers ─────────────────────────────────────────────────

    private function procedimientosPredefinidos(): array
    {
        return [
            'Consulta de valoración',
            'Profilaxis dental',
            'Sellante de fosas y fisuras',
            'Restauración en resina clase I',
            'Restauración en resina clase II',
            'Restauración en resina clase III',
            'Restauración en resina clase IV',
            'Restauración en resina clase V',
            'Restauración en amalgama',
            'Extracción simple',
            'Extracción quirúrgica',
            'Extracción de tercer molar',
            'Endodoncia unirradicular',
            'Endodoncia birradicular',
            'Endodoncia multirradicular',
            'Corona metal porcelana',
            'Corona zirconia',
            'Corona en resina',
            'Incrustación',
            'Carilla en resina',
            'Carilla en porcelana',
            'Implante dental',
            'Cirugía periodontal',
            'Raspado y alisado radicular',
            'Blanqueamiento dental',
            'Ortodoncia con brackets metálicos',
            'Ortodoncia con brackets estéticos',
            'Ortodoncia invisible',
            'Placa de descarga nocturna',
            'Radiografía periapical',
            'Radiografía panorámica',
        ];
    }
}
