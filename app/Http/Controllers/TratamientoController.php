<?php

namespace App\Http\Controllers;

use App\Models\Tratamiento;
use App\Models\Paciente;
use App\Models\HistoriaClinica;
use App\Traits\FormateaCampos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TratamientoController extends Controller
{
    use FormateaCampos;

    public function index(Request $request)
    {
        $query = Tratamiento::with(['paciente', 'doctor'])
            ->where('activo', true)
            ->orderBy('created_at', 'desc');

        if ($buscar = $request->input('buscar')) {
            $query->whereHas('paciente', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%")
                  ->orWhere('numero_documento', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        $tratamientos = $query->paginate(15)->withQueryString();

        return view('tratamientos.index', compact('tratamientos'));
    }

    public function create(Request $request)
    {
        $pacientes = Paciente::activos()->orderBy('apellido')->get();
        $pacienteSeleccionado = $request->filled('paciente_id')
            ? Paciente::find($request->paciente_id)
            : null;

        $historias = $pacienteSeleccionado
            ? HistoriaClinica::where('paciente_id', $pacienteSeleccionado->id)->get()
            : collect();

        return view('tratamientos.create', compact('pacientes', 'pacienteSeleccionado', 'historias'));
    }

    public function store(Request $request)
    {
        $validado = $request->validate([
            'paciente_id'         => 'required|exists:pacientes,id',
            'historia_clinica_id' => 'nullable|exists:historias_clinicas,id',
            'nombre'              => 'required|string|max:255',
            'valor_total'         => 'required|numeric|min:0',
            'fecha_inicio'        => 'required|date',
            'fecha_fin'           => 'nullable|date|after_or_equal:fecha_inicio',
            'estado'              => 'required|in:activo,completado,cancelado',
            'notas'               => 'nullable|string',
        ]);

        $validado['user_id']          = Auth::id();
        $validado['saldo_pendiente']  = $validado['valor_total'];

        $tratamiento = Tratamiento::create($validado);

        return redirect()->route('tratamientos.show', $tratamiento)
                         ->with('exito', 'Tratamiento registrado correctamente.');
    }

    public function show(string $id)
    {
        $tratamiento = Tratamiento::with(['paciente', 'doctor', 'pagos.cajero'])
            ->findOrFail($id);

        return view('tratamientos.show', compact('tratamiento'));
    }

    public function edit(string $id)
    {
        $tratamiento = Tratamiento::findOrFail($id);
        $pacientes   = Paciente::activos()->orderBy('apellido')->get();
        $historias   = HistoriaClinica::where('paciente_id', $tratamiento->paciente_id)->get();

        return view('tratamientos.edit', compact('tratamiento', 'pacientes', 'historias'));
    }

    public function update(Request $request, string $id)
    {
        $tratamiento = Tratamiento::findOrFail($id);

        $validado = $request->validate([
            'paciente_id'         => 'required|exists:pacientes,id',
            'historia_clinica_id' => 'nullable|exists:historias_clinicas,id',
            'nombre'              => 'required|string|max:255',
            'valor_total'         => 'required|numeric|min:0',
            'fecha_inicio'        => 'required|date',
            'fecha_fin'           => 'nullable|date|after_or_equal:fecha_inicio',
            'estado'              => 'required|in:activo,completado,cancelado',
            'notas'               => 'nullable|string',
        ]);

        $tratamiento->update($validado);
        $tratamiento->recalcularSaldo();

        return redirect()->route('tratamientos.show', $tratamiento)
                         ->with('exito', 'Tratamiento actualizado correctamente.');
    }

    public function completar(string $id)
    {
        $tratamiento = Tratamiento::findOrFail($id);

        if ($tratamiento->saldo_pendiente > 0) {
            return back()->with('error', 'No se puede completar: aún tiene saldo pendiente de $' . number_format($tratamiento->saldo_pendiente, 0, ',', '.'));
        }

        $tratamiento->update(['estado' => 'completado']);

        return back()->with('exito', 'Tratamiento marcado como completado.');
    }

    public function cancelar(string $id)
    {
        $tratamiento = Tratamiento::findOrFail($id);
        $tratamiento->update(['estado' => 'cancelado']);

        return back()->with('exito', 'Tratamiento cancelado.');
    }
}
