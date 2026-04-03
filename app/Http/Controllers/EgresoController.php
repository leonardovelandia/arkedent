<?php

namespace App\Http\Controllers;

use App\Models\CategoriaEgreso;
use App\Models\Egreso;
use App\Traits\FormateaCampos;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EgresoController extends Controller
{
    use FormateaCampos;

    public function index(Request $request)
    {
        $hoy = Carbon::today();

        $query = Egreso::with('categoria', 'registradoPor')->where('activo', true);

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha_egreso', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha_egreso', '<=', $request->hasta);
        }

        if ($request->filled('buscar')) {
            $query->where('concepto', 'like', '%' . $request->buscar . '%');
        }

        if ($request->boolean('solo_recurrentes')) {
            $query->where('es_recurrente', true);
        }

        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50])
            ? (int) $request->input('per_page', 10) : 10;

        $egresos = $query->orderBy('fecha_egreso', 'desc')->paginate($perPage)->withQueryString();

        // Resumen del mes actual
        $totalMes     = Egreso::activos()->delMes($hoy->month, $hoy->year)->sum('valor');
        $fijossMes    = Egreso::activos()->delMes($hoy->month, $hoy->year)
                              ->whereHas('categoria', fn($q) => $q->where('es_fijo', true))
                              ->sum('valor');
        $variablesMes = $totalMes - $fijossMes;
        $countMes     = Egreso::activos()->delMes($hoy->month, $hoy->year)->count();

        // Recurrentes pendientes este mes
        $recurrentesPendientes = $this->contarRecurrentesPendientes($hoy);

        $categorias = CategoriaEgreso::activas()->orderBy('nombre')->get();

        return view('egresos.index', compact(
            'egresos', 'categorias',
            'totalMes', 'fijossMes', 'variablesMes', 'countMes',
            'recurrentesPendientes'
        ));
    }

    public function create()
    {
        $categorias = CategoriaEgreso::activas()->orderBy('nombre')->get();
        return view('egresos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id'          => 'nullable|exists:categorias_egreso,id',
            'concepto'              => 'required|string|max:255',
            'descripcion'           => 'nullable|string',
            'valor'                 => 'required|numeric|min:1',
            'metodo_pago'           => 'required|string',
            'fecha_egreso'          => 'required|date',
            'numero_comprobante'    => 'nullable|string|max:100',
            'comprobante'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'es_recurrente'         => 'boolean',
            'frecuencia_recurrente' => 'nullable|required_if:es_recurrente,true',
            'dia_recurrente'        => 'nullable|integer|min:1|max:31',
            'notas'                 => 'nullable|string',
        ]);

        $datos = $this->formatearDatos($validated);
        $datos['user_id']       = auth()->id();
        $datos['numero_egreso'] = Egreso::generarNumero('EGR', 'numero_egreso');
        $datos['es_recurrente'] = $request->boolean('es_recurrente');

        if ($request->hasFile('comprobante')) {
            $ruta = $request->file('comprobante')->store('egresos/comprobantes', 'public');
            $datos['comprobante_path'] = $ruta;
        }

        $egreso = Egreso::create($datos);

        // Calcular próxima fecha si es recurrente
        if ($egreso->es_recurrente) {
            $egreso->proxima_fecha = $egreso->generarProximaFecha();
            $egreso->save();
        }

        return redirect()->route('egresos.show', $egreso)
            ->with('success', 'Egreso ' . $egreso->numero_egreso . ' registrado correctamente.');
    }

    public function show($id)
    {
        $egreso = Egreso::with('categoria', 'registradoPor')->findOrFail($id);
        return view('egresos.show', compact('egreso'));
    }

    public function edit($id)
    {
        $egreso = Egreso::findOrFail($id);

        if ($egreso->anulado) {
            return redirect()->route('egresos.show', $egreso)
                ->with('error', 'No se puede editar un egreso anulado.');
        }

        $categorias = CategoriaEgreso::activas()->orderBy('nombre')->get();
        return view('egresos.edit', compact('egreso', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $egreso = Egreso::findOrFail($id);

        if ($egreso->anulado) {
            return redirect()->route('egresos.show', $egreso)
                ->with('error', 'No se puede editar un egreso anulado.');
        }

        $validated = $request->validate([
            'categoria_id'          => 'nullable|exists:categorias_egreso,id',
            'concepto'              => 'required|string|max:255',
            'descripcion'           => 'nullable|string',
            'valor'                 => 'required|numeric|min:1',
            'metodo_pago'           => 'required|string',
            'fecha_egreso'          => 'required|date',
            'numero_comprobante'    => 'nullable|string|max:100',
            'comprobante'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'es_recurrente'         => 'boolean',
            'frecuencia_recurrente' => 'nullable|required_if:es_recurrente,true',
            'dia_recurrente'        => 'nullable|integer|min:1|max:31',
            'notas'                 => 'nullable|string',
        ]);

        $datos = $this->formatearDatos($validated);
        $datos['es_recurrente'] = $request->boolean('es_recurrente');

        if ($request->hasFile('comprobante')) {
            $ruta = $request->file('comprobante')->store('egresos/comprobantes', 'public');
            $datos['comprobante_path'] = $ruta;
        }

        $egreso->update($datos);

        if ($egreso->es_recurrente && !$egreso->proxima_fecha) {
            $egreso->proxima_fecha = $egreso->generarProximaFecha();
            $egreso->save();
        }

        return redirect()->route('egresos.show', $egreso)
            ->with('success', 'Egreso actualizado correctamente.');
    }

    public function anular(Request $request, $id)
    {
        $egreso = Egreso::findOrFail($id);

        $request->validate([
            'motivo_anulacion' => 'required|string|max:255',
        ]);

        $egreso->update([
            'anulado'          => true,
            'motivo_anulacion' => $request->motivo_anulacion,
        ]);

        return redirect()->route('egresos.show', $egreso)
            ->with('success', 'Egreso anulado correctamente.');
    }

    public function destroy($id)
    {
        $egreso = Egreso::findOrFail($id);
        $egreso->update(['activo' => false]);

        return redirect()->route('egresos.index')
            ->with('success', 'Egreso eliminado correctamente.');
    }

    public function recurrentes()
    {
        $hoy = Carbon::today();

        $recurrentes = Egreso::recurrentes()
            ->with('categoria')
            ->orderBy('proxima_fecha')
            ->get();

        // Separar pendientes y ya registrados este mes
        $yaRegistradosIds = Egreso::activos()
            ->delMes($hoy->month, $hoy->year)
            ->where('es_recurrente', false)
            ->pluck('concepto');

        return view('egresos.recurrentes', compact('recurrentes', 'hoy'));
    }

    public function registrarRecurrente($id)
    {
        $original = Egreso::findOrFail($id);
        $hoy      = Carbon::today();

        $nuevo = Egreso::create([
            'numero_egreso'         => Egreso::generarNumero('EGR', 'numero_egreso'),
            'categoria_id'          => $original->categoria_id,
            'user_id'               => auth()->id(),
            'concepto'              => $original->concepto,
            'descripcion'           => $original->descripcion,
            'valor'                 => $original->valor,
            'metodo_pago'           => $original->metodo_pago,
            'fecha_egreso'          => $hoy,
            'es_recurrente'         => false,
            'notas'                 => 'Pago recurrente generado desde EGR: ' . $original->numero_egreso,
        ]);

        // Actualizar la próxima fecha del original
        $original->proxima_fecha = $original->generarProximaFecha();
        $original->save();

        return redirect()->route('egresos.recurrentes')
            ->with('success', 'Pago recurrente registrado como ' . $nuevo->numero_egreso . '.');
    }

    // ─── Privado ────────────────────────────────────────────────────

    private function contarRecurrentesPendientes(Carbon $hoy): int
    {
        return Egreso::recurrentes()
            ->where(function ($q) use ($hoy) {
                $q->whereNull('proxima_fecha')
                  ->orWhereDate('proxima_fecha', '<=', $hoy->copy()->endOfMonth());
            })
            ->count();
    }
}
