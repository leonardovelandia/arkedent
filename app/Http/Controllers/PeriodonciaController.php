<?php
namespace App\Http\Controllers;

use App\Models\FichaPeriodontal;
use App\Models\ControlPeriodontal;
use App\Models\Paciente;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PeriodonciaController extends Controller
{
    public function index(Request $request)
    {
        $query = FichaPeriodontal::with(['paciente', 'periodoncista', 'controles', 'ultimoControl'])
            ->where('activo', true);

        if ($request->filled('paciente_id')) {
            $query->where('paciente_id', $request->paciente_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('desde')) {
            $query->whereDate('fecha_inicio', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha_inicio', '<=', $request->hasta);
        }

        $fichas = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $stats = [
            'activas'        => FichaPeriodontal::where('activo', true)->where('estado', 'activa')->count(),
            'en_tratamiento' => FichaPeriodontal::where('activo', true)->where('estado', 'en_tratamiento')->count(),
            'mantenimiento'  => FichaPeriodontal::where('activo', true)->where('estado', 'mantenimiento')->count(),
            'controles_mes'  => ControlPeriodontal::whereMonth('fecha_control', now()->month)
                ->whereYear('fecha_control', now()->year)->count(),
        ];

        $pacientes = Paciente::activos()->orderBy('apellido')->orderBy('nombre')->get();

        return view('periodoncia.index', compact('fichas', 'stats', 'pacientes'));
    }

    public function create(Request $request)
    {
        $pacientes = Paciente::activos()->orderBy('apellido')->orderBy('nombre')->get();
        $doctores  = \App\Models\User::orderBy('name')->get();
        $pacienteSeleccionado = null;
        $historiaClinica = null;

        if ($request->filled('paciente_id')) {
            $pacienteSeleccionado = Paciente::find($request->paciente_id);
            if ($pacienteSeleccionado) {
                $historiaClinica = $pacienteSeleccionado->historiaClinica;
            }
        }

        return view('periodoncia.create', compact('pacientes', 'doctores', 'pacienteSeleccionado', 'historiaClinica'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paciente_id'  => 'required|exists:pacientes,id',
            'user_id'      => 'required|exists:users,id',
            'fecha_inicio' => 'required|date',
        ]);

        $data = $request->only([
            'paciente_id','user_id','fecha_inicio',
            'historia_clinica_id',
            'indice_placa_porcentaje','fecha_indice_placa',
            'indice_gingival_porcentaje','fecha_indice_gingival',
            'fecha_sondaje',
            'clasificacion_periodontal','extension','severidad',
            'diagnostico_texto','plan_tratamiento','pronostico_general',
            'estado','notas',
        ]);

        if ($request->filled('indice_placa_datos')) {
            $data['indice_placa_datos'] = json_decode($request->indice_placa_datos, true);
        }
        if ($request->filled('indice_gingival_datos')) {
            $data['indice_gingival_datos'] = json_decode($request->indice_gingival_datos, true);
        }
        if ($request->filled('sondaje_datos')) {
            $data['sondaje_datos'] = json_decode($request->sondaje_datos, true);
        }
        if ($request->filled('factores_riesgo')) {
            $data['factores_riesgo'] = $request->factores_riesgo;
        }

        $data['activo'] = true;
        if (empty($data['estado'])) {
            $data['estado'] = 'activa';
        }

        $ficha = FichaPeriodontal::create($data);

        return redirect()->route('periodoncia.show', $ficha)
            ->with('exito', 'Ficha periodontal creada exitosamente.');
    }

    public function show($id)
    {
        $ficha = FichaPeriodontal::with([
            'paciente', 'periodoncista',
            'controles.periodoncista',
            'historiaClinica',
            'ultimoControl',
        ])->where('activo', true)->findOrFail($id);

        $doctores = \App\Models\User::orderBy('name')->get();

        return view('periodoncia.show', compact('ficha', 'doctores'));
    }

    public function edit($id)
    {
        $ficha     = FichaPeriodontal::where('activo', true)->findOrFail($id);
        $pacientes = Paciente::activos()->orderBy('apellido')->orderBy('nombre')->get();
        $doctores  = \App\Models\User::orderBy('name')->get();

        return view('periodoncia.edit', compact('ficha', 'pacientes', 'doctores'));
    }

    public function update(Request $request, $id)
    {
        $ficha = FichaPeriodontal::where('activo', true)->findOrFail($id);

        $request->validate([
            'paciente_id'  => 'required|exists:pacientes,id',
            'user_id'      => 'required|exists:users,id',
            'fecha_inicio' => 'required|date',
        ]);

        $data = $request->only([
            'paciente_id','user_id','fecha_inicio',
            'historia_clinica_id',
            'indice_placa_porcentaje','fecha_indice_placa',
            'indice_gingival_porcentaje','fecha_indice_gingival',
            'fecha_sondaje',
            'clasificacion_periodontal','extension','severidad',
            'diagnostico_texto','plan_tratamiento','pronostico_general',
            'estado','notas',
        ]);

        if ($request->filled('indice_placa_datos')) {
            $data['indice_placa_datos'] = json_decode($request->indice_placa_datos, true);
        }
        if ($request->filled('indice_gingival_datos')) {
            $data['indice_gingival_datos'] = json_decode($request->indice_gingival_datos, true);
        }
        if ($request->filled('sondaje_datos')) {
            $data['sondaje_datos'] = json_decode($request->sondaje_datos, true);
        }
        $data['factores_riesgo'] = $request->factores_riesgo ?? [];

        $ficha->update($data);

        return redirect()->route('periodoncia.show', $ficha)
            ->with('exito', 'Ficha periodontal actualizada exitosamente.');
    }

    public function cambiarEstado(Request $request, $id)
    {
        $ficha = FichaPeriodontal::findOrFail($id);
        $request->validate(['estado' => 'required|in:activa,en_tratamiento,mantenimiento,finalizada,abandonada']);
        $ficha->update(['estado' => $request->estado]);
        return back()->with('exito', 'Estado actualizado correctamente.');
    }

    public function pdf($id)
    {
        $ficha  = FichaPeriodontal::with([
            'paciente', 'periodoncista', 'controles.periodoncista', 'historiaClinica'
        ])->findOrFail($id);
        $config = Configuracion::obtener();
        $colorPDF = '#1E3A5F';

        $pdf = Pdf::loadView('periodoncia.pdf', compact('ficha', 'config', 'colorPDF'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('ficha-periodontal-' . $ficha->numero_ficha . '.pdf');
    }

    public function destroy($id)
    {
        $ficha = FichaPeriodontal::findOrFail($id);
        $ficha->update(['activo' => false]);
        return redirect()->route('periodoncia.index')
            ->with('exito', 'Ficha periodontal eliminada.');
    }
}
