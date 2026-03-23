<?php

namespace App\Http\Controllers;

use App\Models\Valoracion;
use App\Models\Paciente;
use App\Models\Cita;
use Illuminate\Http\Request;

class ValoracionController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $estado = $request->input('estado');
        $desde  = $request->input('desde');
        $hasta  = $request->input('hasta');

        $valoraciones = Valoracion::with(['paciente', 'doctor'])
            ->activas()
            ->when($buscar, function ($q) use ($buscar) {
                $q->whereHas('paciente', fn($p) =>
                    $p->where('nombre', 'like', "%{$buscar}%")
                      ->orWhere('apellido', 'like', "%{$buscar}%")
                      ->orWhere('numero_documento', 'like', "%{$buscar}%")
                );
            })
            ->when($estado, fn($q) => $q->where('estado', $estado))
            ->when($desde, fn($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta, fn($q) => $q->whereDate('fecha', '<=', $hasta))
            ->orderBy('fecha', 'desc')
            ->paginate(20)
            ->withQueryString();

        if ($request->ajax()) {
            return view('valoraciones._tabla', compact('valoraciones'));
        }

        return view('valoraciones.index', compact('valoraciones', 'buscar', 'estado', 'desde', 'hasta'));
    }

    public function create(Request $request)
    {
        $pacienteId = $request->input('paciente_id');
        $citaId     = $request->input('cita_id');
        $paciente   = null;
        $historia   = null;
        $cita       = null;

        if ($citaId) {
            $cita = Cita::with(['paciente.historiaClinica'])->find($citaId);
            if ($cita) {
                $paciente = $cita->paciente;
                $historia = $paciente?->historiaClinica;
            }
        }

        if ($pacienteId && !$paciente) {
            $paciente = Paciente::with('historiaClinica')->find($pacienteId);
            $historia = $paciente?->historiaClinica;
        }

        $pacientes = Paciente::activos()->orderBy('apellido')->get();

        return view('valoraciones.create', compact('paciente', 'historia', 'cita', 'pacientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paciente_id'     => 'required|exists:pacientes,id',
            'fecha'           => 'required|date',
            'motivo_consulta' => 'required|string',
            'diagnosticos'    => 'nullable|string',
            'plan_tratamiento'=> 'nullable|string',
            'pronostico'      => 'nullable|in:excelente,bueno,reservado,malo',
            'estado'          => 'required|in:en_proceso,completada,cancelada',
        ]);

        $paciente = Paciente::find($request->paciente_id);
        $historia = $paciente?->historiaClinica;

        $diagnosticos     = $request->diagnosticos     ? json_decode($request->diagnosticos, true)     : null;
        $planTratamiento  = $request->plan_tratamiento ? json_decode($request->plan_tratamiento, true)  : null;

        $valoracion = Valoracion::create([
            'paciente_id'             => $request->paciente_id,
            'historia_clinica_id'     => $historia?->id,
            'cita_id'                 => $request->cita_id ?: null,
            'user_id'                 => auth()->id(),
            'fecha'                   => $request->fecha,
            'motivo_consulta'         => $request->motivo_consulta,
            'extraoral_cara'          => $request->extraoral_cara,
            'extraoral_atm'           => $request->extraoral_atm,
            'extraoral_ganglios'      => $request->extraoral_ganglios,
            'extraoral_labios'        => $request->extraoral_labios,
            'extraoral_observaciones' => $request->extraoral_observaciones,
            'intraoral_encias'        => $request->intraoral_encias,
            'intraoral_mucosa'        => $request->intraoral_mucosa,
            'intraoral_lengua'        => $request->intraoral_lengua,
            'intraoral_paladar'       => $request->intraoral_paladar,
            'intraoral_higiene'       => $request->intraoral_higiene ?: null,
            'intraoral_observaciones' => $request->intraoral_observaciones,
            'diagnosticos'            => $diagnosticos,
            'plan_tratamiento'        => $planTratamiento,
            'pronostico'              => $request->pronostico ?: null,
            'observaciones_generales' => $request->observaciones_generales,
            'estado'                  => $request->estado,
        ]);

        return redirect()
            ->route('valoraciones.show', $valoracion)
            ->with('exito', 'Valoración guardada correctamente.');
    }

    public function show($id)
    {
        $valoracion = Valoracion::with(['paciente', 'historiaClinica', 'cita', 'doctor', 'presupuesto'])->findOrFail($id);
        return view('valoraciones.show', compact('valoracion'));
    }

    public function edit($id)
    {
        $valoracion = Valoracion::with(['paciente', 'historiaClinica', 'cita'])->findOrFail($id);

        if ($valoracion->estado !== 'en_proceso') {
            return redirect()
                ->route('valoraciones.show', $valoracion)
                ->with('aviso', 'Solo se pueden editar valoraciones en proceso.');
        }

        $pacientes = Paciente::activos()->orderBy('apellido')->get();

        return view('valoraciones.edit', compact('valoracion', 'pacientes'));
    }

    public function update(Request $request, $id)
    {
        $valoracion = Valoracion::findOrFail($id);

        $request->validate([
            'paciente_id'     => 'required|exists:pacientes,id',
            'fecha'           => 'required|date',
            'motivo_consulta' => 'required|string',
            'diagnosticos'    => 'nullable|string',
            'plan_tratamiento'=> 'nullable|string',
            'pronostico'      => 'nullable|in:excelente,bueno,reservado,malo',
            'estado'          => 'required|in:en_proceso,completada,cancelada',
        ]);

        $diagnosticos    = $request->diagnosticos     ? json_decode($request->diagnosticos, true)    : null;
        $planTratamiento = $request->plan_tratamiento ? json_decode($request->plan_tratamiento, true) : null;

        $valoracion->update([
            'fecha'                   => $request->fecha,
            'motivo_consulta'         => $request->motivo_consulta,
            'extraoral_cara'          => $request->extraoral_cara,
            'extraoral_atm'           => $request->extraoral_atm,
            'extraoral_ganglios'      => $request->extraoral_ganglios,
            'extraoral_labios'        => $request->extraoral_labios,
            'extraoral_observaciones' => $request->extraoral_observaciones,
            'intraoral_encias'        => $request->intraoral_encias,
            'intraoral_mucosa'        => $request->intraoral_mucosa,
            'intraoral_lengua'        => $request->intraoral_lengua,
            'intraoral_paladar'       => $request->intraoral_paladar,
            'intraoral_higiene'       => $request->intraoral_higiene ?: null,
            'intraoral_observaciones' => $request->intraoral_observaciones,
            'diagnosticos'            => $diagnosticos,
            'plan_tratamiento'        => $planTratamiento,
            'pronostico'              => $request->pronostico ?: null,
            'observaciones_generales' => $request->observaciones_generales,
            'estado'                  => $request->estado,
        ]);

        return redirect()
            ->route('valoraciones.show', $valoracion)
            ->with('exito', 'Valoración actualizada correctamente.');
    }

    public function completar(Valoracion $valoracion)
    {
        $valoracion->update(['estado' => 'completada']);

        return redirect()
            ->route('valoraciones.show', $valoracion)
            ->with('exito', 'Valoración marcada como completada.');
    }

    public function generarPresupuesto(Valoracion $valoracion)
    {
        $valoracion->load(['paciente', 'historiaClinica']);

        if ($valoracion->presupuesto_id) {
            return redirect()
                ->route('presupuestos.show', $valoracion->presupuesto_id)
                ->with('aviso', 'Esta valoración ya tiene un presupuesto generado.');
        }

        $planTratamiento = $valoracion->plan_tratamiento ?? [];

        if (empty($planTratamiento)) {
            return redirect()
                ->route('valoraciones.show', $valoracion)
                ->with('error', 'No hay procedimientos en el plan de tratamiento para generar el presupuesto.');
        }

        $presupuesto = \App\Models\Presupuesto::create([
            'paciente_id'          => $valoracion->paciente_id,
            'historia_clinica_id'  => $valoracion->historia_clinica_id,
            'user_id'              => auth()->id(),
            'fecha_generacion'     => now()->toDateString(),
            'fecha_vencimiento'    => now()->addDays(30)->toDateString(),
            'estado'               => 'borrador',
            'validez_dias'         => 30,
            'condiciones_pago'     => '50% al iniciar el tratamiento, 50% al finalizar.',
            'subtotal'             => 0,
            'descuento_porcentaje' => 0,
            'descuento_valor'      => 0,
            'total'                => 0,
        ]);

        $subtotal = 0;
        foreach ($planTratamiento as $index => $item) {
            $valorUnitario = (float)($item['valor_unitario'] ?? 0);
            $cantidad      = (int)($item['cantidad'] ?? 1);
            $valorTotal    = $valorUnitario * $cantidad;
            $subtotal     += $valorTotal;

            \App\Models\ItemPresupuesto::create([
                'presupuesto_id' => $presupuesto->id,
                'numero_item'    => $index + 1,
                'procedimiento'  => $item['procedimiento'] ?? '',
                'diente'         => $item['diente'] ?? null,
                'cara'           => $item['cara'] ?? null,
                'cantidad'       => $cantidad,
                'valor_unitario' => $valorUnitario,
                'valor_total'    => $valorTotal,
                'notas'          => $item['notas'] ?? null,
            ]);
        }

        $presupuesto->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal,
        ]);

        $valoracion->update(['presupuesto_id' => $presupuesto->id]);

        return redirect()
            ->route('presupuestos.show', $presupuesto)
            ->with('exito', 'Presupuesto generado correctamente desde el plan de tratamiento.');
    }

    public function destroy(Valoracion $valoracion)
    {
        $valoracion->update(['estado' => 'cancelada', 'activo' => false]);

        return redirect()
            ->route('valoraciones.index')
            ->with('exito', 'Valoración cancelada.');
    }
}
