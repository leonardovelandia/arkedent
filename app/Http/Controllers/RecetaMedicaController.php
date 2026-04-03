<?php

namespace App\Http\Controllers;

use App\Models\RecetaMedica;
use App\Models\Paciente;
use App\Models\Evolucion;
use App\Models\User;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RecetaMedicaController extends Controller
{
    public function index(Request $request)
    {
        $query = RecetaMedica::with(['paciente', 'doctor'])
            ->where('activo', true);

        if ($request->filled('buscar')) {
            $q = $request->buscar;
            $query->where(function ($sq) use ($q) {
                $sq->where('numero_receta', 'like', "%{$q}%")
                   ->orWhereHas('paciente', fn($p) => $p->whereRaw("CONCAT(nombre,' ',apellido) LIKE ?", ["%{$q}%"]));
            });
        }

        if ($request->filled('firmado')) {
            $query->where('firmado', $request->firmado === '1');
        }

        if ($request->filled('doctor_id')) {
            $query->where('user_id', $request->doctor_id);
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }

        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50])
            ? (int) $request->input('per_page', 10) : 10;
        $recetas  = $query->orderBy('fecha', 'desc')->paginate($perPage)->withQueryString();
        $doctores = User::orderBy('name')->get();

        $totalHoy    = RecetaMedica::whereDate('fecha', today())->where('activo', true)->count();
        $totalMes    = RecetaMedica::whereYear('fecha', now()->year)->whereMonth('fecha', now()->month)->where('activo', true)->count();
        $pendientes  = RecetaMedica::where('firmado', false)->where('activo', true)->count();

        return view('recetas.index', compact('recetas', 'doctores', 'totalHoy', 'totalMes', 'pendientes'));
    }

    public function create(Request $request)
    {
        $pacientes  = Paciente::activos()->orderBy('nombre')->get(['id', 'nombre', 'apellido', 'numero_historia', 'numero_documento']);
        $doctores   = User::orderBy('name')->get();
        $paciente   = null;
        $evolucion  = null;

        if ($request->filled('paciente_id')) {
            $paciente = Paciente::find($request->paciente_id);
        }
        if ($request->filled('evolucion_id')) {
            $evolucion = Evolucion::with('paciente')->find($request->evolucion_id);
            if ($evolucion && !$paciente) {
                $paciente = $evolucion->paciente;
            }
        }

        return view('recetas.create', compact('pacientes', 'doctores', 'paciente', 'evolucion'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'paciente_id'           => 'required|exists:pacientes,id',
            'user_id'               => 'required|exists:users,id',
            'evolucion_id'          => 'nullable|exists:evoluciones,id',
            'fecha'                 => 'required|date',
            'diagnostico'           => 'nullable|string|max:500',
            'indicaciones_generales'=> 'nullable|string',
            'medicamentos'          => 'nullable|string',
        ]);

        if (!empty($validated['medicamentos'])) {
            $decoded = json_decode($validated['medicamentos'], true);
            $validated['medicamentos'] = $decoded ?: null;
        } else {
            $validated['medicamentos'] = null;
        }

        if ($request->input('firma_tipo') === 'con_firma') {
            $validated['firmado']    = true;
            $validated['fecha_firma'] = now();
            $validated['ip_firma']   = $request->getClientIp();
        }

        $receta = RecetaMedica::create($validated);

        return redirect()->route('recetas.show', $receta)
            ->with('exito', "Receta {$receta->numero_receta} creada correctamente.");
    }

    public function show($id)
    {
        $receta = RecetaMedica::with(['paciente', 'doctor', 'evolucion'])->findOrFail($id);
        return view('recetas.show', compact('receta'));
    }

    public function edit($id)
    {
        $receta    = RecetaMedica::with(['paciente'])->findOrFail($id);
        $pacientes = Paciente::activos()->orderBy('apellido')->get(['id', 'nombre', 'apellido', 'numero_historia']);
        $doctores  = User::orderBy('name')->get();

        $evoluciones = Evolucion::where('paciente_id', $receta->paciente_id)
            ->where('activo', true)
            ->orderBy('fecha', 'desc')
            ->limit(20)
            ->get();

        return view('recetas.edit', compact('receta', 'pacientes', 'doctores', 'evoluciones'));
    }

    public function update(Request $request, $id)
    {
        $receta = RecetaMedica::findOrFail($id);

        $validated = $request->validate([
            'paciente_id'           => 'required|exists:pacientes,id',
            'user_id'               => 'required|exists:users,id',
            'evolucion_id'          => 'nullable|exists:evoluciones,id',
            'fecha'                 => 'required|date',
            'diagnostico'           => 'nullable|string|max:500',
            'indicaciones_generales'=> 'nullable|string',
            'medicamentos'          => 'nullable|string',
        ]);

        if (!empty($validated['medicamentos'])) {
            $decoded = json_decode($validated['medicamentos'], true);
            $validated['medicamentos'] = $decoded ?: null;
        } else {
            $validated['medicamentos'] = null;
        }

        $receta->update($validated);

        return redirect()->route('recetas.show', $receta)
            ->with('exito', 'Receta actualizada correctamente.');
    }

    public function firmar(Request $request, $id)
    {
        $request->validate([
            'firma_data' => 'required|string',
        ]);

        $receta = RecetaMedica::findOrFail($id);

        if ($receta->firmado) {
            return redirect()->route('recetas.show', $receta)
                ->with('aviso', 'Esta receta ya fue firmada y no puede volver a firmarse.');
        }

        $receta->update([
            'firmado'    => true,
            'firma_data' => $request->firma_data,
            'fecha_firma'=> now(),
            'ip_firma'   => $request->getClientIp(),
        ]);

        return redirect()->route('recetas.show', $receta)
            ->with('exito', 'Receta firmada correctamente.');
    }

    public function pdf($id)
    {
        $receta        = RecetaMedica::with(['paciente', 'doctor'])->findOrFail($id);
        $configuracion = Configuracion::first();

        $pdf = Pdf::loadView('recetas.pdf', compact('receta', 'configuracion'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("receta-{$receta->numero_receta}.pdf");
    }

    public function duplicar($id)
    {
        $original = RecetaMedica::findOrFail($id);

        $nueva = RecetaMedica::create([
            'paciente_id'           => $original->paciente_id,
            'user_id'               => auth()->id(),
            'evolucion_id'          => null,
            'fecha'                 => today(),
            'diagnostico'           => $original->diagnostico,
            'medicamentos'          => $original->medicamentos,
            'indicaciones_generales'=> $original->indicaciones_generales,
        ]);

        return redirect()->route('recetas.edit', $nueva)
            ->with('exito', "Receta duplicada como {$nueva->numero_receta}. Revise y guarde los cambios.");
    }

    public function destroy($id)
    {
        $receta = RecetaMedica::findOrFail($id);
        $receta->update(['activo' => false]);

        return redirect()->route('recetas.index')
            ->with('exito', 'Receta anulada correctamente.');
    }
}
