<?php

namespace App\Http\Controllers;

use App\Models\ControlOrtodoncia;
use App\Models\FichaOrtodoncia;
use App\Models\Cita;
use App\Models\User;
use Illuminate\Http\Request;

class ControlOrtodonciaController extends Controller
{
    public function create(Request $request)
    {
        $fichaId = $request->ficha_ortodontica_id ?? $request->route('ortodoncia');
        $ficha   = FichaOrtodoncia::with(['paciente', 'controles', 'ultimoControl'])->findOrFail($fichaId);

        $numeroSesion = $ficha->controles()->count() + 1;

        // Cargar odontograma del último control como base
        $odontogramaBase = $ficha->ultimoControl?->odontograma_sesion
            ?? $ficha->odontograma_ortodoncia
            ?? [];

        // Citas recientes del paciente
        $citas = Cita::where('paciente_id', $ficha->paciente_id)
            ->where('activo', true)
            ->orderBy('fecha', 'desc')
            ->limit(20)
            ->get();

        $ortodoncistas = User::all();

        return view('ortodoncia.controles.create', compact(
            'ficha', 'numeroSesion', 'odontogramaBase', 'citas', 'ortodoncistas'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ficha_ortodontica_id'  => 'required|exists:fichas_ortodonticas,id',
            'paciente_id'           => 'required|exists:pacientes,id',
            'cita_id'               => 'nullable|exists:citas,id',
            'user_id'               => 'required|exists:users,id',
            'fecha_control'         => 'required|date',
            'hora_inicio'           => 'nullable|date_format:H:i',
            'hora_fin'              => 'nullable|date_format:H:i',
            'numero_sesion'         => 'required|integer|min:1',
            'arco_superior'         => 'nullable|string|max:100',
            'arco_inferior'         => 'nullable|string|max:100',
            'tipo_arco_superior'    => 'nullable|in:niti,acero,tma,fibra_vidrio,ninguno',
            'tipo_arco_inferior'    => 'nullable|in:niti,acero,tma,fibra_vidrio,ninguno',
            'calibre_superior'      => 'nullable|string|max:30',
            'calibre_inferior'      => 'nullable|string|max:30',
            'ligadura_superior'     => 'nullable|in:elastica,metalica,autoligado,ninguna',
            'ligadura_inferior'     => 'nullable|in:elastica,metalica,autoligado,ninguna',
            'color_ligadura'        => 'nullable|string|max:50',
            'tipo_elasticos'        => 'nullable|string|max:100',
            'progreso_porcentaje'   => 'nullable|integer|min:0|max:100',
            'observaciones'         => 'nullable|string',
            'proxima_cita_semanas'  => 'nullable|integer|min:1|max:52',
            'indicaciones_paciente' => 'nullable|string',
            'odontograma_sesion'    => 'nullable|string',
            'brackets_reemplazados' => 'nullable|string',
        ]);

        $validated['elasticos'] = $request->boolean('elasticos');

        if (!empty($validated['odontograma_sesion'])) {
            $decoded = json_decode($validated['odontograma_sesion'], true);
            $validated['odontograma_sesion'] = $decoded ?: null;
        }

        if (!empty($validated['brackets_reemplazados'])) {
            $decoded = json_decode($validated['brackets_reemplazados'], true);
            $validated['brackets_reemplazados'] = $decoded ?: null;
        }

        $control = ControlOrtodoncia::create($validated);

        // Actualizar odontograma en la ficha con el estado de esta sesión
        if (!empty($validated['odontograma_sesion'])) {
            FichaOrtodoncia::where('id', $validated['ficha_ortodontica_id'])
                ->update(['odontograma_ortodoncia' => $validated['odontograma_sesion']]);
        }

        // Cambiar estado de la ficha a 'activo' si estaba en diagnóstico
        $ficha = FichaOrtodoncia::find($validated['ficha_ortodontica_id']);
        if ($ficha && $ficha->estado === 'diagnostico') {
            $ficha->update(['estado' => 'activo']);
        }

        return redirect()->route('ortodoncia.show', $validated['ficha_ortodontica_id'])
            ->with('exito', "Control #{$control->numero_sesion} registrado correctamente.");
    }

    public function show($id)
    {
        $control = ControlOrtodoncia::with(['fichaOrtodoncia.paciente', 'ortodoncista', 'cita'])->findOrFail($id);
        return view('ortodoncia.controles.show', compact('control'));
    }

    public function edit($id)
    {
        $control       = ControlOrtodoncia::with(['fichaOrtodoncia.paciente'])->findOrFail($id);
        $ficha         = $control->fichaOrtodoncia;
        $ortodoncistas = User::all();
        $citas = Cita::where('paciente_id', $ficha->paciente_id)
            ->where('activo', true)
            ->orderBy('fecha', 'desc')
            ->limit(20)
            ->get();

        return view('ortodoncia.controles.edit', compact('control', 'ficha', 'ortodoncistas', 'citas'));
    }

    public function update(Request $request, $id)
    {
        $control = ControlOrtodoncia::findOrFail($id);

        $validated = $request->validate([
            'user_id'              => 'required|exists:users,id',
            'cita_id'              => 'nullable|exists:citas,id',
            'fecha_control'        => 'required|date',
            'hora_inicio'          => 'nullable|date_format:H:i',
            'hora_fin'             => 'nullable|date_format:H:i',
            'arco_superior'        => 'nullable|string|max:100',
            'arco_inferior'        => 'nullable|string|max:100',
            'tipo_arco_superior'   => 'nullable|in:niti,acero,tma,fibra_vidrio,ninguno',
            'tipo_arco_inferior'   => 'nullable|in:niti,acero,tma,fibra_vidrio,ninguno',
            'calibre_superior'     => 'nullable|string|max:30',
            'calibre_inferior'     => 'nullable|string|max:30',
            'ligadura_superior'    => 'nullable|in:elastica,metalica,autoligado,ninguna',
            'ligadura_inferior'    => 'nullable|in:elastica,metalica,autoligado,ninguna',
            'color_ligadura'       => 'nullable|string|max:50',
            'tipo_elasticos'       => 'nullable|string|max:100',
            'progreso_porcentaje'  => 'nullable|integer|min:0|max:100',
            'observaciones'        => 'nullable|string',
            'proxima_cita_semanas' => 'nullable|integer|min:1|max:52',
            'indicaciones_paciente'=> 'nullable|string',
            'odontograma_sesion'   => 'nullable|string',
        ]);

        $validated['elasticos'] = $request->boolean('elasticos');

        if (!empty($validated['odontograma_sesion'])) {
            $decoded = json_decode($validated['odontograma_sesion'], true);
            $validated['odontograma_sesion'] = $decoded ?: null;
        }

        $control->update($validated);

        return redirect()->route('ortodoncia.show', $control->ficha_ortodontica_id)
            ->with('exito', 'Control actualizado correctamente.');
    }
}
