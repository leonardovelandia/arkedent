<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Paciente;
use App\Models\Tratamiento;
use App\Traits\FormateaCampos;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PagoController extends Controller
{
    use FormateaCampos;

    public function index(Request $request)
    {
        $hoy = Carbon::today();

        $query = Pago::with(['paciente', 'tratamiento', 'cajero'])
            ->orderBy('fecha_pago', 'desc')
            ->orderBy('created_at', 'desc');

        if ($buscar = $request->input('buscar')) {
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_recibo', 'like', "%{$buscar}%")
                  ->orWhere('concepto', 'like', "%{$buscar}%")
                  ->orWhereHas('paciente', function ($qp) use ($buscar) {
                      $qp->where('nombre', 'like', "%{$buscar}%")
                         ->orWhere('apellido', 'like', "%{$buscar}%")
                         ->orWhere('numero_documento', 'like', "%{$buscar}%");
                  });
            });
        }

        if ($request->filled('paciente_id')) {
            $query->where('paciente_id', $request->input('paciente_id'));
        }

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->input('metodo_pago'));
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_pago', '>=', $request->input('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_pago', '<=', $request->input('fecha_hasta'));
        }

        $pagos = $query->paginate(15)->withQueryString();

        // Resumen
        $totalMes  = Pago::whereMonth('fecha_pago', $hoy->month)
            ->whereYear('fecha_pago', $hoy->year)
            ->where('anulado', false)->sum('valor');

        $totalHoy  = Pago::whereDate('fecha_pago', $hoy)
            ->where('anulado', false)->sum('valor');

        $pagosHoy  = Pago::whereDate('fecha_pago', $hoy)
            ->where('anulado', false)->count();

        $pacientes = \App\Models\Paciente::activos()->orderBy('apellido')->get();
        $pacienteFiltro = $request->filled('paciente_id')
            ? \App\Models\Paciente::find($request->input('paciente_id'))
            : null;

        return view('pagos.index', compact('pagos', 'totalMes', 'totalHoy', 'pagosHoy', 'pacientes', 'pacienteFiltro'));
    }

    public function create(Request $request)
    {
        $pacientes = Paciente::activos()->orderBy('apellido')->get();

        $pacienteSeleccionado = $request->filled('paciente_id')
            ? Paciente::find($request->paciente_id)
            : null;

        $tratamientos = $pacienteSeleccionado
            ? Tratamiento::where('paciente_id', $pacienteSeleccionado->id)
                ->where('estado', 'activo')
                ->where('saldo_pendiente', '>', 0)
                ->get()
            : collect();

        $tratamientoSeleccionado = $request->filled('tratamiento_id')
            ? Tratamiento::find($request->tratamiento_id)
            : null;

        return view('pagos.create', compact(
            'pacientes', 'pacienteSeleccionado',
            'tratamientos', 'tratamientoSeleccionado'
        ));
    }

    public function store(Request $request)
    {
        $validado = $request->validate([
            'paciente_id'     => 'required|exists:pacientes,id',
            'tratamiento_id'  => 'nullable|exists:tratamientos,id',
            'concepto'        => 'required|string|max:255',
            'valor'           => 'required|numeric|min:1',
            'metodo_pago'     => 'required|in:efectivo,transferencia,tarjeta_credito,tarjeta_debito,cheque,otro',
            'referencia_pago' => 'nullable|string|max:100',
            'fecha_pago'      => 'required|date',
            'observaciones'   => 'nullable|string',
        ]);

        // Verificar que el valor no supere el saldo pendiente
        if (!empty($validado['tratamiento_id'])) {
            $tratamiento = Tratamiento::find($validado['tratamiento_id']);
            if ($tratamiento && $validado['valor'] > $tratamiento->saldo_pendiente) {
                return back()->withInput()
                    ->withErrors(['valor' => 'El valor supera el saldo pendiente del tratamiento ($' . number_format($tratamiento->saldo_pendiente, 0, ',', '.') . ').']);
            }
        }

        $validado['user_id'] = Auth::id();

        $pago = Pago::create($validado);

        return redirect()->route('pagos.show', $pago)
                         ->with('exito', 'Pago registrado correctamente. Recibo: ' . $pago->numero_recibo);
    }

    public function show(string $id)
    {
        $pago = Pago::with(['paciente', 'tratamiento', 'cajero'])->findOrFail($id);

        return view('pagos.show', compact('pago'));
    }

    public function recibo(string $id)
    {
        $pago = Pago::with(['paciente', 'tratamiento', 'cajero'])->findOrFail($id);
        $configuracion = \App\Models\Configuracion::first();

        $pdf = Pdf::loadView('pagos.recibo', compact('pago', 'configuracion'))
            ->setPaper([0, 0, 226.77, 500], 'portrait'); // ~80mm recibo

        if (request()->boolean('raw')) {
            return $pdf->stream('recibo-' . $pago->numero_recibo . '.pdf');
        }

        $urlPdf = route('pagos.recibo', $id) . '?raw=1';
        $titulo = 'Recibo ' . $pago->numero_recibo;
        return view('layouts.pdf-viewer', compact('urlPdf', 'titulo'));
    }

    public function anular(Request $request, string $id)
    {
        $request->validate([
            'motivo_anulacion' => 'required|string|max:255',
        ]);

        $pago = Pago::findOrFail($id);

        if ($pago->anulado) {
            return back()->with('error', 'Este pago ya está anulado.');
        }

        $pago->update([
            'anulado'          => true,
            'motivo_anulacion' => $request->motivo_anulacion,
        ]);

        return back()->with('exito', 'Pago anulado correctamente.');
    }
}
