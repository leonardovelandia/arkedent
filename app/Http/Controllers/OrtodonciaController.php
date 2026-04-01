<?php

namespace App\Http\Controllers;

use App\Models\FichaOrtodoncia;
use App\Models\Paciente;
use App\Models\HistoriaClinica;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrtodonciaController extends Controller
{
    public function index(Request $request)
    {
        $query = FichaOrtodoncia::with(['paciente', 'ultimoControl'])
            ->activos();

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->whereHas('paciente', function ($q) use ($b) {
                $q->where('nombre', 'like', "%{$b}%")
                  ->orWhere('apellido', 'like', "%{$b}%")
                  ->orWhere('numero_documento', 'like', "%{$b}%");
            });
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

        // Estadísticas
        $totalActivos    = FichaOrtodoncia::activos()->where('estado', 'activo')->count();
        $totalRetencion  = FichaOrtodoncia::activos()->where('estado', 'retencion')->count();
        $finalizadosAnio = FichaOrtodoncia::where('estado', 'finalizado')
            ->whereYear('fecha_fin_real', now()->year)->count();
        $controlesEsteMes = \App\Models\ControlOrtodoncia::whereMonth('fecha_control', now()->month)
            ->whereYear('fecha_control', now()->year)->count();

        return view('ortodoncia.index', compact(
            'fichas', 'totalActivos', 'totalRetencion', 'finalizadosAnio', 'controlesEsteMes'
        ));
    }

    public function create(Request $request)
    {
        $pacienteId  = $request->paciente_id;
        $paciente    = $pacienteId ? Paciente::findOrFail($pacienteId) : null;
        $pacientes   = Paciente::activos()->orderBy('apellido')->get();
        $historias   = $paciente ? HistoriaClinica::where('paciente_id', $pacienteId)->get() : collect();
        $ortodoncistas = User::all();

        return view('ortodoncia.create', compact('paciente', 'pacientes', 'historias', 'ortodoncistas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'paciente_id'          => 'required|exists:pacientes,id',
            'historia_clinica_id'  => 'nullable|exists:historias_clinicas,id',
            'user_id'              => 'required|exists:users,id',
            'fecha_inicio'         => 'required|date',
            'fecha_fin_estimada'   => 'nullable|date|after:fecha_inicio',
            'duracion_meses_estimada' => 'nullable|integer|min:1|max:120',
            'tipo_ortodoncia'      => 'nullable|in:fija_metal,fija_estetica,fija_autoligado,removible,alineadores',
            'marca_brackets'       => 'nullable|string|max:100',
            'costo_total'          => 'nullable|numeric|min:0',
            'perfil'               => 'nullable|in:convexo,recto,concavo',
            'simetria_facial'      => 'nullable|in:simetrica,asimetrica',
            'biotipo_facial'       => 'nullable|in:dolicofacial,mesofacial,braquifacial',
            'analisis_facial_notas'=> 'nullable|string',
            'clase_molar_derecha'  => 'nullable|in:clase_i,clase_ii,clase_iii',
            'clase_molar_izquierda'=> 'nullable|in:clase_i,clase_ii,clase_iii',
            'clase_canina_derecha' => 'nullable|in:clase_i,clase_ii,clase_iii',
            'clase_canina_izquierda'=> 'nullable|in:clase_i,clase_ii,clase_iii',
            'overjet'              => 'nullable|numeric',
            'overbite'             => 'nullable|numeric',
            'linea_media_superior' => 'nullable|in:centrada,desviada_derecha,desviada_izquierda',
            'linea_media_inferior' => 'nullable|in:centrada,desviada_derecha,desviada_izquierda',
            'desviacion_mm'        => 'nullable|numeric',
            'apinamiento_superior' => 'nullable|in:leve,moderado,severo,ninguno',
            'apinamiento_inferior' => 'nullable|in:leve,moderado,severo,ninguno',
            'arco_inicial_superior'=> 'nullable|string|max:50',
            'arco_inicial_inferior'=> 'nullable|string|max:50',
            'diagnostico'          => 'nullable|string',
            'plan_tratamiento'     => 'nullable|string',
            'pronostico'           => 'nullable|in:excelente,bueno,reservado',
            'odontograma_ortodoncia'=> 'nullable|string',
            'extracciones_indicadas'=> 'nullable|string',
        ]);

        // Booleanos
        foreach (['espaciamiento_superior','espaciamiento_inferior','mordida_cruzada_anterior','mordida_cruzada_posterior','mordida_abierta','mordida_profunda'] as $campo) {
            $validated[$campo] = $request->boolean($campo);
        }

        // Odontograma JSON
        if (!empty($validated['odontograma_ortodoncia'])) {
            $decoded = json_decode($validated['odontograma_ortodoncia'], true);
            $validated['odontograma_ortodoncia'] = $decoded ?: null;
        }

        // Extracciones como array
        if (!empty($validated['extracciones_indicadas'])) {
            $validated['extracciones_indicadas'] = array_map('trim',
                explode(',', $validated['extracciones_indicadas'])
            );
        }

        $ficha = FichaOrtodoncia::create($validated);

        return redirect()->route('ortodoncia.show', $ficha)
            ->with('exito', "Ficha ortodóntica {$ficha->numero_ficha} creada correctamente.");
    }

    public function show($id)
    {
        $ficha = FichaOrtodoncia::with([
            'paciente',
            'ortodoncista',
            'historiaClinica',
            'controles.ortodoncista',
            'retencion',
        ])->findOrFail($id);

        $controles = $ficha->controles()->with('ortodoncista')->orderBy('numero_sesion', 'desc')->get();

        return view('ortodoncia.show', compact('ficha', 'controles'));
    }

    public function edit($id)
    {
        $ficha       = FichaOrtodoncia::findOrFail($id);
        $pacientes   = Paciente::activos()->orderBy('apellido')->get();
        $historias   = HistoriaClinica::where('paciente_id', $ficha->paciente_id)->get();
        $ortodoncistas = User::all();

        return view('ortodoncia.edit', compact('ficha', 'pacientes', 'historias', 'ortodoncistas'));
    }

    public function update(Request $request, $id)
    {
        $ficha = FichaOrtodoncia::findOrFail($id);

        $validated = $request->validate([
            'user_id'              => 'required|exists:users,id',
            'fecha_inicio'         => 'required|date',
            'fecha_fin_estimada'   => 'nullable|date',
            'duracion_meses_estimada' => 'nullable|integer|min:1|max:120',
            'tipo_ortodoncia'      => 'nullable|in:fija_metal,fija_estetica,fija_autoligado,removible,alineadores',
            'marca_brackets'       => 'nullable|string|max:100',
            'costo_total'          => 'nullable|numeric|min:0',
            'perfil'               => 'nullable|in:convexo,recto,concavo',
            'simetria_facial'      => 'nullable|in:simetrica,asimetrica',
            'biotipo_facial'       => 'nullable|in:dolicofacial,mesofacial,braquifacial',
            'analisis_facial_notas'=> 'nullable|string',
            'clase_molar_derecha'  => 'nullable|in:clase_i,clase_ii,clase_iii',
            'clase_molar_izquierda'=> 'nullable|in:clase_i,clase_ii,clase_iii',
            'clase_canina_derecha' => 'nullable|in:clase_i,clase_ii,clase_iii',
            'clase_canina_izquierda'=> 'nullable|in:clase_i,clase_ii,clase_iii',
            'overjet'              => 'nullable|numeric',
            'overbite'             => 'nullable|numeric',
            'linea_media_superior' => 'nullable|in:centrada,desviada_derecha,desviada_izquierda',
            'linea_media_inferior' => 'nullable|in:centrada,desviada_derecha,desviada_izquierda',
            'desviacion_mm'        => 'nullable|numeric',
            'apinamiento_superior' => 'nullable|in:leve,moderado,severo,ninguno',
            'apinamiento_inferior' => 'nullable|in:leve,moderado,severo,ninguno',
            'arco_inicial_superior'=> 'nullable|string|max:50',
            'arco_inicial_inferior'=> 'nullable|string|max:50',
            'diagnostico'          => 'nullable|string',
            'plan_tratamiento'     => 'nullable|string',
            'pronostico'           => 'nullable|in:excelente,bueno,reservado',
            'notas'                => 'nullable|string',
            'odontograma_ortodoncia'=> 'nullable|string',
            'extracciones_indicadas'=> 'nullable|string',
        ]);

        foreach (['espaciamiento_superior','espaciamiento_inferior','mordida_cruzada_anterior','mordida_cruzada_posterior','mordida_abierta','mordida_profunda'] as $campo) {
            $validated[$campo] = $request->boolean($campo);
        }

        if (!empty($validated['odontograma_ortodoncia'])) {
            $decoded = json_decode($validated['odontograma_ortodoncia'], true);
            $validated['odontograma_ortodoncia'] = $decoded ?: null;
        }

        if (!empty($validated['extracciones_indicadas'])) {
            $validated['extracciones_indicadas'] = array_map('trim',
                explode(',', $validated['extracciones_indicadas'])
            );
        }

        $ficha->update($validated);

        return redirect()->route('ortodoncia.show', $ficha)
            ->with('exito', 'Ficha actualizada correctamente.');
    }

    public function cambiarEstado(Request $request, $id)
    {
        $ficha = FichaOrtodoncia::findOrFail($id);

        $request->validate([
            'estado' => 'required|in:diagnostico,activo,retencion,finalizado,cancelado',
        ]);

        $datos = ['estado' => $request->estado];

        if ($request->estado === 'finalizado' && !$ficha->fecha_fin_real) {
            $datos['fecha_fin_real'] = today();
        }

        $ficha->update($datos);

        return back()->with('exito', "Estado cambiado a «{$ficha->fresh()->estado_label}».");
    }

    public function destroy($id)
    {
        $ficha = FichaOrtodoncia::findOrFail($id);
        $ficha->update(['activo' => false, 'estado' => 'cancelado']);

        return redirect()->route('ortodoncia.index')
            ->with('exito', "Ficha {$ficha->numero_ficha} cancelada.");
    }
}
