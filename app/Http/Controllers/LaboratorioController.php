<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use App\Models\Laboratorio;
use App\Models\OrdenLaboratorio;
use App\Models\Paciente;
use App\Traits\FormateaCampos;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaboratorioController extends Controller
{
    use FormateaCampos;

    public function index(Request $request)
    {
        $query = OrdenLaboratorio::with(['paciente', 'laboratorio'])
            ->where('activo', true);

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('numero_orden', 'like', "%$b%")
                  ->orWhereHas('paciente', fn($q2) => $q2->where('nombre', 'like', "%$b%")->orWhere('apellido', 'like', "%$b%"));
            });
        }

        if ($request->filled('laboratorio_id')) {
            $query->where('laboratorio_id', $request->laboratorio_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50])
            ? (int) $request->input('per_page', 10) : 10;
        $ordenes = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        $laboratorios    = Laboratorio::activos()->orderBy('nombre')->get();
        $ordenesVencidas = OrdenLaboratorio::where('activo', true)->vencidas()->count();

        // Resumen cards
        $totalActivas    = OrdenLaboratorio::where('activo', true)->count();
        $enProceso       = OrdenLaboratorio::where('activo', true)->pendientes()->count();
        $recibidas       = OrdenLaboratorio::where('activo', true)->where('estado', 'recibido')->count();
        $vencidas        = $ordenesVencidas;

        return view('laboratorio.index', compact(
            'ordenes', 'laboratorios', 'ordenesVencidas',
            'totalActivas', 'enProceso', 'recibidas', 'vencidas'
        ));
    }

    public function create(Request $request)
    {
        $pacientes    = Paciente::where('activo', true)->orderBy('apellido')->orderBy('nombre')->get();
        $laboratorios = Laboratorio::activos()->orderBy('nombre')->get();

        $pacienteSeleccionado = $request->filled('paciente_id')
            ? Paciente::find($request->paciente_id)
            : null;

        $evolucionId = $request->evolucion_id;

        return view('laboratorio.create', compact('pacientes', 'laboratorios', 'pacienteSeleccionado', 'evolucionId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'paciente_id'            => 'required|exists:pacientes,id',
            'laboratorio_id'         => 'required|exists:laboratorios,id',
            'tipo_trabajo'           => 'required|string|max:150',
            'descripcion'            => 'required|string',
            'dientes'                => 'nullable|string|max:100',
            'color_diente'           => 'nullable|string|max:50',
            'material'               => 'nullable|string|max:100',
            'fecha_envio'            => 'nullable|date',
            'fecha_entrega_estimada' => 'nullable|date',
            'precio_laboratorio'     => 'nullable|numeric|min:0',
            'evolucion_id'           => 'nullable|exists:evoluciones,id',
            'observaciones_envio'    => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        // Si hay fecha de envío, el estado inicial es enviado
        if (!empty($validated['fecha_envio'])) {
            $validated['estado'] = 'enviado';
        }

        // Limpiar precio si viene con puntos
        if (isset($validated['precio_laboratorio'])) {
            $validated['precio_laboratorio'] = str_replace('.', '', $validated['precio_laboratorio']);
        }

        $orden = OrdenLaboratorio::create($validated);

        return redirect()->route('laboratorio.show', $orden)
            ->with('exito', 'Orden ' . $orden->numero_orden . ' creada correctamente.');
    }

    public function show($id)
    {
        $orden = OrdenLaboratorio::with(['paciente', 'laboratorio', 'doctor', 'evolucion'])
            ->where('activo', true)->where('uuid', $id)
            ->firstOrFail();

        return view('laboratorio.show', compact('orden'));
    }

    public function pdf($id)
    {
        $orden  = OrdenLaboratorio::with(['paciente', 'laboratorio', 'doctor'])->where('uuid', $id)->firstOrFail();
        $config = Configuracion::obtener();

        $pdf = Pdf::loadView('laboratorio.pdf', compact('orden', 'config'))
                  ->setPaper('a4', 'portrait');

        if (request()->boolean('raw')) {
            return $pdf->stream('orden-' . $orden->numero_orden . '.pdf');
        }

        $urlPdf = route('laboratorio.pdf', $id) . '?raw=1';
        $titulo = 'Orden ' . $orden->numero_orden;
        return view('layouts.pdf-viewer', compact('urlPdf', 'titulo'));
    }

    public function edit($id)
    {
        $orden = OrdenLaboratorio::where('activo', true)->where('uuid', $id)->firstOrFail();

        if (in_array($orden->estado, ['instalado', 'cancelado'])) {
            return redirect()->route('laboratorio.show', $orden)
                ->with('aviso', 'No se puede editar una orden instalada o cancelada.');
        }

        $pacientes    = Paciente::where('activo', true)->orderBy('apellido')->orderBy('nombre')->get();
        $laboratorios = Laboratorio::activos()->orderBy('nombre')->get();

        return view('laboratorio.edit', compact('orden', 'pacientes', 'laboratorios'));
    }

    public function update(Request $request, $id)
    {
        $orden = OrdenLaboratorio::where('activo', true)->where('uuid', $id)->firstOrFail();

        $validated = $request->validate([
            'paciente_id'            => 'required|exists:pacientes,id',
            'laboratorio_id'         => 'required|exists:laboratorios,id',
            'tipo_trabajo'           => 'required|string|max:150',
            'descripcion'            => 'required|string',
            'dientes'                => 'nullable|string|max:100',
            'color_diente'           => 'nullable|string|max:50',
            'material'               => 'nullable|string|max:100',
            'fecha_envio'            => 'nullable|date',
            'fecha_entrega_estimada' => 'nullable|date',
            'precio_laboratorio'     => 'nullable|numeric|min:0',
            'observaciones_envio'    => 'nullable|string',
        ]);

        if (isset($validated['precio_laboratorio'])) {
            $validated['precio_laboratorio'] = str_replace('.', '', $validated['precio_laboratorio']);
        }

        $orden->update($validated);

        return redirect()->route('laboratorio.show', $orden)
            ->with('exito', 'Orden actualizada correctamente.');
    }

    public function enviar(Request $request, $id)
    {
        $orden = OrdenLaboratorio::where('activo', true)->where('uuid', $id)->firstOrFail();

        $request->validate([
            'fecha_envio'         => 'nullable|date',
            'observaciones_envio' => 'nullable|string',
        ]);

        $orden->update([
            'estado'              => 'enviado',
            'fecha_envio'         => $request->fecha_envio ?? today(),
            'observaciones_envio' => $request->observaciones_envio,
        ]);

        return redirect()->route('laboratorio.show', $orden)
            ->with('exito', 'Orden marcada como enviada.');
    }

    public function recibirTrabajo(Request $request, $id)
    {
        $orden = OrdenLaboratorio::where('activo', true)->where('uuid', $id)->firstOrFail();

        $request->validate([
            'fecha_recepcion'          => 'nullable|date',
            'calidad_recibida'         => 'required|in:excelente,buena,regular,mala',
            'requiere_ajuste'          => 'nullable|boolean',
            'observaciones_recepcion'  => 'nullable|string',
        ]);

        $orden->update([
            'estado'                  => 'recibido',
            'fecha_recepcion'         => $request->fecha_recepcion ?? today(),
            'calidad_recibida'        => $request->calidad_recibida,
            'requiere_ajuste'         => $request->boolean('requiere_ajuste'),
            'observaciones_recepcion' => $request->observaciones_recepcion,
        ]);

        return redirect()->route('laboratorio.show', $orden)
            ->with('exito', 'Trabajo recibido registrado correctamente.');
    }

    public function instalar(Request $request, $id)
    {
        $orden = OrdenLaboratorio::where('activo', true)->where('uuid', $id)->firstOrFail();

        $request->validate([
            'fecha_instalacion' => 'nullable|date',
        ]);

        $orden->update([
            'estado'            => 'instalado',
            'fecha_instalacion' => $request->fecha_instalacion ?? today(),
        ]);

        return redirect()->route('laboratorio.show', $orden)
            ->with('exito', 'Instalación registrada. ¡Orden completada!');
    }

    public function cancelar(Request $request, $id)
    {
        $orden = OrdenLaboratorio::where('activo', true)->where('uuid', $id)->firstOrFail();

        $request->validate([
            'motivo_cancelacion' => 'required|string|max:255',
        ]);

        $orden->update([
            'estado'              => 'cancelado',
            'motivo_cancelacion'  => $request->motivo_cancelacion,
        ]);

        return redirect()->route('laboratorio.show', $orden)
            ->with('exito', 'Orden cancelada.');
    }

    public function destroy($id)
    {
        $orden = OrdenLaboratorio::porUuidOrFail($id);
        $orden->update(['activo' => false]);

        return redirect()->route('laboratorio.index')
            ->with('exito', 'Orden eliminada.');
    }
}
