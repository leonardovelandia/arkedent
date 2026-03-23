<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Traits\FormateaCampos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitaController extends Controller
{
    use FormateaCampos;

    // ── Listado ───────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Cita::with(['paciente', 'doctor'])
            ->where('activo', true)
            ->orderByDesc('fecha')
            ->orderBy('hora_inicio');

        if ($buscar = $request->input('buscar')) {
            $query->whereHas('paciente', function ($q) use ($buscar) {
                $q->where('nombre',   'like', "%{$buscar}%")
                  ->orWhere('apellido','like', "%{$buscar}%")
                  ->orWhere('numero_documento', 'like', "%{$buscar}%");
            });
        }

        if ($fecha = $request->input('fecha')) {
            $query->whereDate('fecha', $fecha);
        }

        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        if ($pacienteId = $request->input('paciente_id')) {
            $query->where('paciente_id', $pacienteId);
        }

        $citas  = $query->paginate(15)->withQueryString();
        $colores = Cita::coloresPorEstado();

        return view('citas.index', compact('citas', 'colores'));
    }

    // ── Agenda semanal ────────────────────────────────────────
    public function agenda(Request $request)
    {
        $fechaBase = $request->input('fecha')
            ? Carbon::parse($request->input('fecha'))
            : Carbon::today();

        // Lunes de la semana actual
        $lunes   = $fechaBase->copy()->startOfWeek(Carbon::MONDAY);
        $sabado  = $lunes->copy()->addDays(5);
        $semanas = [];

        for ($i = 0; $i < 6; $i++) {
            $dia = $lunes->copy()->addDays($i);
            $semanas[$dia->format('Y-m-d')] = [
                'fecha'    => $dia,
                'esHoy'    => $dia->isToday(),
                'citas'    => [],
            ];
        }

        $citas = Cita::with('paciente')
            ->where('activo', true)
            ->whereBetween('fecha', [$lunes->format('Y-m-d'), $sabado->format('Y-m-d')])
            ->orderBy('hora_inicio')
            ->get();

        foreach ($citas as $cita) {
            $key = $cita->fecha->format('Y-m-d');
            if (isset($semanas[$key])) {
                $semanas[$key]['citas'][] = $cita;
            }
        }

        $colores       = Cita::coloresPorEstado();
        $semanaAnterior = $lunes->copy()->subWeek()->format('Y-m-d');
        $semanaSiguiente = $lunes->copy()->addWeek()->format('Y-m-d');

        return view('citas.agenda', compact(
            'semanas', 'lunes', 'sabado', 'colores',
            'semanaAnterior', 'semanaSiguiente'
        ));
    }

    // ── Formulario crear ──────────────────────────────────────
    public function create(Request $request)
    {
        $pacientes = Paciente::where('activo', true)->orderBy('apellido')->orderBy('nombre')->get();
        $paciente  = $request->filled('paciente_id')
            ? Paciente::find($request->input('paciente_id'))
            : null;

        return view('citas.create', compact('pacientes', 'paciente'));
    }

    // ── Detectar cruces de citas ──────────────────────────────
    private function detectarCruces(string $fecha, string $horaInicio, string $horaFin = null, int $excludeId = null): \Illuminate\Support\Collection
    {
        $horaFinCalc = $horaFin ?? date('H:i', strtotime($horaInicio . ' +30 minutes'));

        $query = Cita::with('paciente')
            ->whereDate('fecha', $fecha)
            ->where('activo', true)
            ->whereNotIn('estado', ['cancelada', 'no_asistio'])
            ->where(function ($q) use ($horaInicio, $horaFinCalc) {
                $q->where('hora_inicio', '<', $horaFinCalc)
                  ->where(function ($inner) use ($horaInicio) {
                      $inner->whereRaw('TIME(DATE_ADD(STR_TO_DATE(hora_inicio, "%H:%i"), INTERVAL 30 MINUTE)) > ?', [$horaInicio])
                            ->orWhereRaw('hora_fin > ?', [$horaInicio]);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }

    // ── Verificar disponibilidad (API) ────────────────────────
    public function verificarDisponibilidad(Request $request)
    {
        $request->validate([
            'fecha'       => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin'    => 'nullable',
            'exclude_id'  => 'nullable|integer',
        ]);

        $cruces = $this->detectarCruces(
            $request->fecha,
            $request->hora_inicio,
            $request->hora_fin,
            $request->exclude_id
        );

        return response()->json([
            'tiene_cruces' => $cruces->count() > 0,
            'cruces'       => $cruces->map(function ($cita) {
                return [
                    'id'            => $cita->id,
                    'paciente'      => $cita->paciente->nombre_completo,
                    'hora_inicio'   => $cita->hora_inicio,
                    'hora_fin'      => $cita->hora_fin ?? '--',
                    'procedimiento' => $cita->procedimiento,
                    'estado'        => ucfirst(str_replace('_', ' ', $cita->estado)),
                ];
            }),
        ]);
    }

    // ── Guardar ───────────────────────────────────────────────
    public function store(Request $request)
    {
        $validado = $request->validate([
            'paciente_id'  => 'required|exists:pacientes,id',
            'fecha'        => 'required|date|after_or_equal:today',
            'hora_inicio'  => 'required|date_format:H:i',
            'hora_fin'     => 'nullable|date_format:H:i|after:hora_inicio',
            'procedimiento'=> 'required|string|max:255',
            'estado'       => 'required|in:pendiente,confirmada,en_proceso,atendida,cancelada,no_asistio',
            'notas'        => 'nullable|string',
        ]);

        $datos = $this->formatearDatos($validado);
        $datos['user_id'] = Auth::id();

        $cita = Cita::create($datos);

        $cruces = $this->detectarCruces(
            $validado['fecha'],
            $validado['hora_inicio'],
            $validado['hora_fin'] ?? null,
            $cita->id
        );

        if ($cruces->count() > 0) {
            $nombresCruces = $cruces->pluck('paciente.nombre_completo')->join(', ');
            return redirect()->route('citas.index')
                ->with('exito', 'Cita registrada correctamente.')
                ->with('aviso_cruce', '⚠ Esta cita se cruza con: ' . $nombresCruces . '. Verifica la agenda.');
        }

        return redirect()->route('citas.index')
                         ->with('exito', 'Cita registrada correctamente.');
    }

    // ── Detalle ───────────────────────────────────────────────
    public function show(string $id)
    {
        $cita    = Cita::with(['paciente', 'doctor'])->findOrFail($id);
        $colores = Cita::coloresPorEstado();
        $otrasCitas = Cita::where('paciente_id', $cita->paciente_id)
            ->where('id', '!=', $cita->id)
            ->where('activo', true)
            ->orderByDesc('fecha')
            ->limit(3)
            ->get();

        return view('citas.show', compact('cita', 'colores', 'otrasCitas'));
    }

    // ── Formulario editar ─────────────────────────────────────
    public function edit(string $id)
    {
        $cita      = Cita::with('paciente')->findOrFail($id);
        $pacientes = Paciente::where('activo', true)->orderBy('apellido')->orderBy('nombre')->get();

        return view('citas.edit', compact('cita', 'pacientes'));
    }

    // ── Actualizar ────────────────────────────────────────────
    public function update(Request $request, string $id)
    {
        $cita = Cita::findOrFail($id);

        $validado = $request->validate([
            'paciente_id'  => 'required|exists:pacientes,id',
            'fecha'        => 'required|date',
            'hora_inicio'  => 'required|date_format:H:i',
            'hora_fin'     => 'nullable|date_format:H:i|after:hora_inicio',
            'procedimiento'=> 'required|string|max:255',
            'estado'       => 'required|in:pendiente,confirmada,en_proceso,atendida,cancelada,no_asistio',
            'notas'        => 'nullable|string',
        ]);

        $datos = $this->formatearDatos($validado);
        $cita->update($datos);

        $cruces = $this->detectarCruces(
            $validado['fecha'],
            $validado['hora_inicio'],
            $validado['hora_fin'] ?? null,
            $cita->id
        );

        if ($cruces->count() > 0) {
            $nombresCruces = $cruces->pluck('paciente.nombre_completo')->join(', ');
            return redirect()->route('citas.show', $cita)
                ->with('exito', 'Cita actualizada correctamente.')
                ->with('aviso_cruce', '⚠ Esta cita se cruza con: ' . $nombresCruces . '. Verifica la agenda.');
        }

        return redirect()->route('citas.show', $cita)
                         ->with('exito', 'Cita actualizada correctamente.');
    }

    // ── Confirmar ─────────────────────────────────────────────
    public function confirmar(string $id)
    {
        $cita = Cita::findOrFail($id);
        $cita->update(['estado' => 'confirmada']);

        return back()->with('exito', 'Cita confirmada correctamente.');
    }

    // ── Cancelar ──────────────────────────────────────────────
    public function cancelar(Request $request, string $id)
    {
        $request->validate([
            'motivo_cancelacion' => 'required|string|max:255',
        ]);

        $cita = Cita::findOrFail($id);
        $cita->update([
            'estado'              => 'cancelada',
            'motivo_cancelacion'  => $request->input('motivo_cancelacion'),
        ]);

        return back()->with('exito', 'Cita cancelada.');
    }

    // ── Eliminar (soft) ───────────────────────────────────────
    public function destroy(string $id)
    {
        Cita::findOrFail($id)->update(['activo' => false]);

        return redirect()->route('citas.index')
                         ->with('exito', 'Cita eliminada.');
    }
}
