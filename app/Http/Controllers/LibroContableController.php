<?php

namespace App\Http\Controllers;

use App\Models\LibroContable;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LibroContableController extends Controller
{
    public function index(Request $request)
    {
        $desde = $request->desde
            ? Carbon::parse($request->desde)->startOfDay()
            : Carbon::now()->startOfMonth();

        $hasta = $request->hasta
            ? Carbon::parse($request->hasta)->endOfDay()
            : Carbon::now()->endOfDay();

        $tipo = $request->tipo;

        $query = LibroContable::with('registradoPor')
            ->whereBetween('fecha_movimiento', [$desde, $hasta])
            ->where('activo', true);

        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        if (!$request->boolean('incluir_excluidos')) {
            $query->where('excluido', false);
        }

        $movimientos = $query->orderBy('fecha_movimiento')->orderBy('id')->get();

        // Saldo acumulado anterior al período
        $saldoInicial = LibroContable::where('fecha_movimiento', '<', $desde)
            ->where('excluido', false)
            ->where('activo', true)
            ->selectRaw("SUM(CASE WHEN tipo='ingreso' THEN valor ELSE -valor END) as saldo")
            ->value('saldo') ?? 0;

        $saldoAcumulado = $saldoInicial;
        $movimientosConSaldo = $movimientos->map(function ($mov) use (&$saldoAcumulado) {
            if (!$mov->excluido) {
                $saldoAcumulado += $mov->tipo === 'ingreso' ? $mov->valor : -$mov->valor;
            }
            $mov->saldo_acumulado = $saldoAcumulado;
            return $mov;
        });

        $totalIngresos = $movimientos->where('tipo', 'ingreso')->where('excluido', false)->sum('valor');
        $totalEgresos  = $movimientos->where('tipo', 'egreso')->where('excluido', false)->sum('valor');
        $utilidad      = $totalIngresos - $totalEgresos;

        return view('libro-contable.index', compact(
            'movimientosConSaldo', 'desde', 'hasta',
            'totalIngresos', 'totalEgresos', 'utilidad',
            'saldoInicial', 'tipo'
        ));
    }

    public function estadoResultados(Request $request)
    {
        $mes = $request->mes ?? now()->month;
        $ano = $request->ano ?? now()->year;
        $fecha = Carbon::createFromDate($ano, $mes, 1);

        $ingresosPorOrigen = LibroContable::where('tipo', 'ingreso')
            ->whereMonth('fecha_movimiento', $mes)
            ->whereYear('fecha_movimiento', $ano)
            ->where('excluido', false)
            ->selectRaw('origen, SUM(valor) as total')
            ->groupBy('origen')
            ->get();

        $egresosPorCategoria = LibroContable::where('tipo', 'egreso')
            ->whereMonth('fecha_movimiento', $mes)
            ->whereYear('fecha_movimiento', $ano)
            ->where('excluido', false)
            ->selectRaw('categoria, origen, SUM(valor) as total')
            ->groupBy('categoria', 'origen')
            ->orderByDesc('total')
            ->get();

        $totalIngresos = $ingresosPorOrigen->sum('total');
        $totalEgresos  = $egresosPorCategoria->sum('total');
        $utilidad      = $totalIngresos - $totalEgresos;
        $margen        = $totalIngresos > 0 ? ($utilidad / $totalIngresos) * 100 : 0;

        $mesAnterior = $fecha->copy()->subMonth();
        $ingresosMesAnterior = LibroContable::where('tipo', 'ingreso')
            ->whereMonth('fecha_movimiento', $mesAnterior->month)
            ->whereYear('fecha_movimiento', $mesAnterior->year)
            ->where('excluido', false)->sum('valor');
        $egresosMesAnterior = LibroContable::where('tipo', 'egreso')
            ->whereMonth('fecha_movimiento', $mesAnterior->month)
            ->whereYear('fecha_movimiento', $mesAnterior->year)
            ->where('excluido', false)->sum('valor');

        return view('libro-contable.estado-resultados', compact(
            'ingresosPorOrigen', 'egresosPorCategoria',
            'totalIngresos', 'totalEgresos', 'utilidad', 'margen',
            'ingresosMesAnterior', 'egresosMesAnterior',
            'mes', 'ano', 'fecha'
        ));
    }

    public function comparativo(Request $request)
    {
        $meses = collect();
        for ($i = 11; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $ingresos = LibroContable::where('tipo', 'ingreso')
                ->whereMonth('fecha_movimiento', $fecha->month)
                ->whereYear('fecha_movimiento', $fecha->year)
                ->where('excluido', false)->sum('valor');
            $egresos = LibroContable::where('tipo', 'egreso')
                ->whereMonth('fecha_movimiento', $fecha->month)
                ->whereYear('fecha_movimiento', $fecha->year)
                ->where('excluido', false)->sum('valor');
            $meses->push([
                'mes'      => $fecha->locale('es')->isoFormat('MMM YY'),
                'ingresos' => (float) $ingresos,
                'egresos'  => (float) $egresos,
                'utilidad' => (float) $ingresos - (float) $egresos,
            ]);
        }

        $mejorMes         = $meses->sortByDesc('utilidad')->first();
        $peorMes          = $meses->sortBy('utilidad')->first();
        $promedioIngresos = $meses->avg('ingresos');
        $promedioEgresos  = $meses->avg('egresos');

        return view('libro-contable.comparativo', compact(
            'meses', 'mejorMes', 'peorMes',
            'promedioIngresos', 'promedioEgresos'
        ));
    }

    public function excluir(Request $request, $id)
    {
        $asiento = LibroContable::findOrFail($id);
        $asiento->update([
            'excluido'         => true,
            'motivo_exclusion' => $request->motivo ?? 'Excluido manualmente',
        ]);
        return response()->json(['success' => true]);
    }

    public function incluir($id)
    {
        $asiento = LibroContable::findOrFail($id);
        $asiento->update([
            'excluido'         => false,
            'motivo_exclusion' => null,
        ]);
        return response()->json(['success' => true]);
    }

    public function ajuste(Request $request)
    {
        $request->validate([
            'tipo'             => 'required|in:ingreso,egreso',
            'concepto'         => 'required|string|max:255',
            'valor'            => 'required|numeric|min:0.01',
            'fecha_movimiento' => 'required|date',
            'descripcion'      => 'nullable|string',
        ]);

        LibroContable::registrarMovimiento(
            tipo:            $request->tipo,
            origen:          'ajuste_manual',
            origenId:        0,
            origenTipo:      'ajuste',
            concepto:        $request->concepto,
            valor:           (float) $request->valor,
            fechaMovimiento: $request->fecha_movimiento,
            categoria:       $request->categoria ?? 'Ajuste manual',
            descripcion:     $request->descripcion,
        );

        return redirect()->route('libro-contable.index')
            ->with('success', 'Ajuste manual registrado correctamente.');
    }

    public function exportar(Request $request)
    {
        $desde = $request->desde ? Carbon::parse($request->desde) : now()->startOfMonth();
        $hasta = $request->hasta ? Carbon::parse($request->hasta) : now();

        $movimientos = LibroContable::with('registradoPor')
            ->whereBetween('fecha_movimiento', [$desde, $hasta])
            ->where('excluido', false)
            ->where('activo', true)
            ->orderBy('fecha_movimiento')
            ->get();

        $nombreArchivo = "libro-contable-{$desde->format('Y-m-d')}-al-{$hasta->format('Y-m-d')}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$nombreArchivo\"",
        ];

        $saldoAcumulado = 0;
        $callback = function () use ($movimientos, &$saldoAcumulado) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, [
                'N° Asiento', 'Fecha', 'Tipo', 'Origen',
                'Concepto', 'Categoría', 'Referencia',
                'Método Pago', 'Ingreso', 'Egreso', 'Saldo',
            ], ';');

            foreach ($movimientos as $mov) {
                $saldoAcumulado += $mov->tipo === 'ingreso' ? $mov->valor : -$mov->valor;
                fputcsv($file, [
                    $mov->numero_formateado,
                    $mov->fecha_movimiento->format('d/m/Y'),
                    ucfirst($mov->tipo),
                    $mov->origen_label,
                    $mov->concepto,
                    $mov->categoria ?? '',
                    $mov->referencia ?? '',
                    $mov->metodo_pago ?? '',
                    $mov->tipo === 'ingreso' ? number_format($mov->valor, 0, ',', '.') : '',
                    $mov->tipo === 'egreso'  ? number_format($mov->valor, 0, ',', '.') : '',
                    number_format($saldoAcumulado, 0, ',', '.'),
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
