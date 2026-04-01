<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\CategoriaEgreso;
use App\Models\Egreso;
use App\Models\Paciente;
use App\Models\Cita;
use App\Models\Pago;
use App\Models\Tratamiento;
use App\Models\Evolucion;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();
        $inicioMes = $hoy->copy()->startOfMonth();
        $finMes = $hoy->copy()->endOfMonth();
        $inicioMesAnterior = $hoy->copy()->subMonth()->startOfMonth();
        $finMesAnterior = $hoy->copy()->subMonth()->endOfMonth();
        $inicioAno = $hoy->copy()->startOfYear();

        // Pacientes
        $totalPacientes = Paciente::where('activo', true)->count();
        $pacientesNuevosMes = Paciente::whereBetween('created_at', [$inicioMes, $finMes])->count();
        $pacientesNuevosMesAnterior = Paciente::whereBetween('created_at', [$inicioMesAnterior, $finMesAnterior])->count();

        // Ingresos
        $ingresosMes = Pago::whereBetween('fecha_pago', [$inicioMes, $finMes])->where('anulado', false)->sum('valor');
        $ingresosMesAnterior = Pago::whereBetween('fecha_pago', [$inicioMesAnterior, $finMesAnterior])->where('anulado', false)->sum('valor');
        $ingresosAno = Pago::whereBetween('fecha_pago', [$inicioAno, $finMes])->where('anulado', false)->sum('valor');

        // Citas
        $citasMes = Cita::whereBetween('fecha', [$inicioMes, $finMes])->where('activo', true)->count();
        $citasAtendidas = Cita::whereBetween('fecha', [$inicioMes, $finMes])->where('estado', 'atendida')->count();
        $citasCanceladas = Cita::whereBetween('fecha', [$inicioMes, $finMes])->where('estado', 'cancelada')->count();
        $citasNoAsistio = Cita::whereBetween('fecha', [$inicioMes, $finMes])->where('estado', 'no_asistio')->count();

        // Evoluciones
        $evolucionesMes = Evolucion::whereBetween('fecha', [$inicioMes, $finMes])->where('activo', true)->count();

        // Saldo pendiente
        $saldoPendienteTotal = Tratamiento::where('estado', 'activo')->where('saldo_pendiente', '>', 0)->sum('saldo_pendiente');

        // Ingresos por mes para grafico (ultimos 12 meses) — 1 query con GROUP BY
        $inicio12Meses = $hoy->copy()->subMonths(11)->startOfMonth();
        $ingresosPorMesRaw = Pago::where('anulado', false)
            ->whereBetween('fecha_pago', [$inicio12Meses, $finMes])
            ->selectRaw("DATE_FORMAT(fecha_pago, '%Y-%m') as periodo, SUM(valor) as valor")
            ->groupBy('periodo')
            ->pluck('valor', 'periodo');

        $ingresosPorMes = collect();
        for ($i = 11; $i >= 0; $i--) {
            $mes    = $hoy->copy()->subMonths($i);
            $periodo = $mes->format('Y-m');
            $ingresosPorMes->push([
                'mes'   => $mes->locale('es')->isoFormat('MMM YYYY'),
                'valor' => $ingresosPorMesRaw[$periodo] ?? 0,
            ]);
        }

        // Pacientes nuevos por mes (ultimos 6 meses) — 1 query con GROUP BY
        $inicio6Meses = $hoy->copy()->subMonths(5)->startOfMonth();
        $pacientesPorMesRaw = Paciente::whereBetween('created_at', [$inicio6Meses, $finMes])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as periodo, COUNT(*) as total")
            ->groupBy('periodo')
            ->pluck('total', 'periodo');

        $pacientesPorMes = collect();
        for ($i = 5; $i >= 0; $i--) {
            $mes     = $hoy->copy()->subMonths($i);
            $periodo = $mes->format('Y-m');
            $pacientesPorMes->push([
                'mes'   => $mes->locale('es')->isoFormat('MMM'),
                'total' => $pacientesPorMesRaw[$periodo] ?? 0,
            ]);
        }

        // Top 5 procedimientos mas realizados
        $topProcedimientos = Evolucion::where('activo', true)
            ->selectRaw('procedimiento, COUNT(*) as total')
            ->groupBy('procedimiento')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Top 5 metodos de pago
        $topMetodosPago = Pago::where('anulado', false)
            ->selectRaw('metodo_pago, COUNT(*) as total, SUM(valor) as suma')
            ->groupBy('metodo_pago')
            ->orderByDesc('suma')
            ->get();

        // Citas por estado del mes
        $citasPorEstado = Cita::whereBetween('fecha', [$inicioMes, $finMes])
            ->where('activo', true)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get();

        // Egresos del mes
        $egresosMes = Egreso::whereBetween('fecha_egreso', [$inicioMes, $finMes])
            ->where('anulado', false)
            ->sum('valor');

        $egresosPorCategoria = CategoriaEgreso::withSum(
            ['egresos' => function ($q) use ($inicioMes, $finMes) {
                $q->whereBetween('fecha_egreso', [$inicioMes, $finMes])
                  ->where('anulado', false);
            }], 'valor'
        )->having('egresos_sum_valor', '>', 0)->get();

        $utilidadNeta = $ingresosMes - $egresosMes;

        return view('reportes.index', compact(
            'totalPacientes', 'pacientesNuevosMes', 'pacientesNuevosMesAnterior',
            'ingresosMes', 'ingresosMesAnterior', 'ingresosAno',
            'citasMes', 'citasAtendidas', 'citasCanceladas', 'citasNoAsistio',
            'evolucionesMes', 'saldoPendienteTotal',
            'ingresosPorMes', 'pacientesPorMes',
            'topProcedimientos', 'topMetodosPago', 'citasPorEstado',
            'egresosMes', 'egresosPorCategoria', 'utilidadNeta'
        ));
    }

    public function ingresos(Request $request)
    {
        $desde = $request->desde ? Carbon::parse($request->desde) : Carbon::now()->startOfMonth();
        $hasta = $request->hasta ? Carbon::parse($request->hasta) : Carbon::now()->endOfMonth();
        $metodoPago = $request->metodo_pago;

        $query = Pago::with('paciente', 'tratamiento', 'cajero')
            ->whereBetween('fecha_pago', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
            ->where('anulado', false);

        if ($metodoPago) {
            $query->where('metodo_pago', $metodoPago);
        }

        $pagos = $query->orderBy('fecha_pago', 'desc')->paginate(20)->withQueryString();

        $queryTotal = Pago::whereBetween('fecha_pago', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
            ->where('anulado', false);

        if ($metodoPago) {
            $queryTotal->where('metodo_pago', $metodoPago);
        }

        $totalFiltrado = $queryTotal->sum('valor');
        $conteoFiltrado = $queryTotal->count();

        $porMetodo = Pago::whereBetween('fecha_pago', [$desde, $hasta])
            ->where('anulado', false)
            ->selectRaw('metodo_pago, COUNT(*) as total, SUM(valor) as suma')
            ->groupBy('metodo_pago')
            ->orderByDesc('suma')
            ->get();

        return view('reportes.ingresos', compact(
            'pagos', 'desde', 'hasta', 'metodoPago',
            'totalFiltrado', 'conteoFiltrado', 'porMetodo'
        ));
    }

    public function pacientes(Request $request)
    {
        $desde = $request->desde ? Carbon::parse($request->desde) : Carbon::now()->startOfYear();
        $hasta = $request->hasta ? Carbon::parse($request->hasta) : Carbon::now();
        $genero = $request->genero;
        $ciudad = $request->ciudad;

        $query = Paciente::where('activo', true)
            ->whereBetween('created_at', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()]);

        if ($genero) $query->where('genero', $genero);
        if ($ciudad) $query->where('ciudad', 'like', "%$ciudad%");

        $pacientes = $query->orderBy('apellido')->paginate(20)->withQueryString();

        $porGenero = Paciente::where('activo', true)
            ->selectRaw('genero, COUNT(*) as total')
            ->groupBy('genero')->get();

        $porCiudad = Paciente::where('activo', true)
            ->selectRaw('ciudad, COUNT(*) as total')
            ->groupBy('ciudad')
            ->orderByDesc('total')
            ->limit(10)->get();

        // Rango de edades — 1 query con CASE WHEN (antes 5 queries)
        $edadesRaw = Paciente::where('activo', true)
            ->selectRaw("
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 0  AND 18 THEN 1 ELSE 0 END) as r0_18,
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 19 AND 30 THEN 1 ELSE 0 END) as r19_30,
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 31 AND 45 THEN 1 ELSE 0 END) as r31_45,
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 46 AND 60 THEN 1 ELSE 0 END) as r46_60,
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) > 60             THEN 1 ELSE 0 END) as r60plus
            ")
            ->first();
        $rangoEdades = [
            '0-18'  => (int) ($edadesRaw->r0_18   ?? 0),
            '19-30' => (int) ($edadesRaw->r19_30  ?? 0),
            '31-45' => (int) ($edadesRaw->r31_45  ?? 0),
            '46-60' => (int) ($edadesRaw->r46_60  ?? 0),
            '60+'   => (int) ($edadesRaw->r60plus ?? 0),
        ];

        return view('reportes.pacientes', compact(
            'pacientes', 'desde', 'hasta', 'genero', 'ciudad',
            'porGenero', 'porCiudad', 'rangoEdades'
        ));
    }

    public function citas(Request $request)
    {
        $desde = $request->desde ? Carbon::parse($request->desde) : Carbon::now()->startOfMonth();
        $hasta = $request->hasta ? Carbon::parse($request->hasta) : Carbon::now()->endOfMonth();
        $estado = $request->estado;

        $query = Cita::with('paciente', 'doctor')
            ->whereBetween('fecha', [$desde, $hasta])
            ->where('activo', true);

        if ($estado) {
            $query->where('estado', $estado);
        }

        $citas = $query->orderBy('fecha', 'desc')->orderBy('hora_inicio')->paginate(20)->withQueryString();

        $porEstado = Cita::whereBetween('fecha', [$desde, $hasta])
            ->where('activo', true)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get();

        $totalCitas = $porEstado->sum('total');

        return view('reportes.citas', compact(
            'citas', 'desde', 'hasta', 'estado', 'porEstado', 'totalCitas'
        ));
    }

    public function exportarIngresos(Request $request)
    {
        $desde = $request->desde ? Carbon::parse($request->desde) : Carbon::now()->startOfMonth();
        $hasta = $request->hasta ? Carbon::parse($request->hasta) : Carbon::now()->endOfMonth();

        $pagos = Pago::with('paciente', 'tratamiento')
            ->whereBetween('fecha_pago', [$desde, $hasta])
            ->where('anulado', false)
            ->orderBy('fecha_pago')
            ->get();

        $nombreArchivo = 'ingresos-' . $desde->format('Y-m-d') . '-al-' . $hasta->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$nombreArchivo\"",
        ];

        $callback = function () use ($pagos) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['N° Recibo', 'Fecha', 'Paciente', 'Documento', 'Concepto', 'Tratamiento', 'Metodo Pago', 'Valor'], ';');
            foreach ($pagos as $pago) {
                fputcsv($file, [
                    $pago->numero_recibo,
                    $pago->fecha_pago->format('d/m/Y'),
                    $pago->paciente->nombre_completo ?? '',
                    $pago->paciente->numero_documento ?? '',
                    $pago->concepto,
                    $pago->tratamiento->nombre ?? 'Sin tratamiento',
                    $pago->metodo_pago_label ?? $pago->metodo_pago,
                    number_format($pago->valor, 0, ',', '.'),
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportarPacientes(Request $request)
    {
        $desde = $request->desde ? Carbon::parse($request->desde) : Carbon::now()->startOfYear();
        $hasta = $request->hasta ? Carbon::parse($request->hasta) : Carbon::now();

        $pacientes = Paciente::where('activo', true)
            ->whereBetween('created_at', [$desde, $hasta])
            ->orderBy('apellido')
            ->get();

        $nombreArchivo = 'pacientes-' . $desde->format('Y-m-d') . '-al-' . $hasta->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$nombreArchivo\"",
        ];

        $callback = function () use ($pacientes) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['N° Historia', 'Nombre', 'Apellido', 'Tipo Documento', 'Documento', 'Fecha Nacimiento', 'Edad', 'Genero', 'Telefono', 'Email', 'Ciudad', 'Fecha Registro'], ';');
            foreach ($pacientes as $p) {
                fputcsv($file, [
                    $p->numero_historia,
                    $p->nombre,
                    $p->apellido,
                    $p->tipo_documento,
                    $p->numero_documento,
                    $p->fecha_nacimiento ? $p->fecha_nacimiento->format('d/m/Y') : '',
                    $p->edad ?? '',
                    $p->genero,
                    $p->telefono,
                    $p->email,
                    $p->ciudad,
                    $p->created_at->format('d/m/Y'),
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function egresos(Request $request)
    {
        $desde = $request->desde ? Carbon::parse($request->desde) : Carbon::now()->startOfMonth();
        $hasta = $request->hasta ? Carbon::parse($request->hasta) : Carbon::now()->endOfMonth();
        $categoriaId = $request->categoria_id;

        $query = Egreso::with('categoria', 'registradoPor')
            ->whereBetween('fecha_egreso', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
            ->where('anulado', false);

        if ($categoriaId) {
            $query->where('categoria_id', $categoriaId);
        }

        $egresos = $query->orderBy('fecha_egreso', 'desc')->paginate(20)->withQueryString();

        $totalFiltrado = Egreso::whereBetween('fecha_egreso', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
            ->where('anulado', false)
            ->when($categoriaId, fn($q) => $q->where('categoria_id', $categoriaId))
            ->sum('valor');

        $conteoFiltrado = Egreso::whereBetween('fecha_egreso', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
            ->where('anulado', false)
            ->when($categoriaId, fn($q) => $q->where('categoria_id', $categoriaId))
            ->count();

        $porCategoria = CategoriaEgreso::withSum(
            ['egresos' => fn($q) => $q->whereBetween('fecha_egreso', [$desde, $hasta])->where('anulado', false)],
            'valor'
        )->having('egresos_sum_valor', '>', 0)->orderByDesc('egresos_sum_valor')->get();

        $categorias = CategoriaEgreso::orderBy('nombre')->get();

        return view('reportes.egresos', compact(
            'egresos', 'desde', 'hasta', 'categoriaId',
            'totalFiltrado', 'conteoFiltrado', 'porCategoria', 'categorias'
        ));
    }

    public function datosGraficas(Request $request)
    {
        $periodo = $request->periodo ?? 'mes';
        $hoy = Carbon::today();

        if ($periodo === 'dia') {
            $inicio = $hoy->copy()->subDays(29);
            $ingresosRaw = Pago::where('anulado', false)
                ->whereBetween('fecha_pago', [$inicio, $hoy->copy()->endOfDay()])
                ->selectRaw("DATE(fecha_pago) as periodo, SUM(valor) as valor")
                ->groupBy('periodo')->pluck('valor', 'periodo');
            $egresosRaw = Egreso::where('anulado', false)
                ->whereBetween('fecha_egreso', [$inicio, $hoy->copy()->endOfDay()])
                ->selectRaw("DATE(fecha_egreso) as periodo, SUM(valor) as valor")
                ->groupBy('periodo')->pluck('valor', 'periodo');
            $ingresos = collect(); $egresos = collect();
            for ($i = 29; $i >= 0; $i--) {
                $d = $hoy->copy()->subDays($i);
                $k = $d->format('Y-m-d');
                $ingresos->push(['label' => $d->format('d/m'), 'valor' => (float)($ingresosRaw[$k] ?? 0)]);
                $egresos->push(['label'  => $d->format('d/m'), 'valor' => (float)($egresosRaw[$k]  ?? 0)]);
            }
        } elseif ($periodo === 'ano') {
            $anoActual = (int)$hoy->format('Y');
            $ingresosRaw = Pago::where('anulado', false)
                ->whereBetween('fecha_pago', [Carbon::create($anoActual - 4, 1, 1), $hoy->copy()->endOfYear()])
                ->selectRaw("YEAR(fecha_pago) as periodo, SUM(valor) as valor")
                ->groupBy('periodo')->pluck('valor', 'periodo');
            $egresosRaw = Egreso::where('anulado', false)
                ->whereBetween('fecha_egreso', [Carbon::create($anoActual - 4, 1, 1), $hoy->copy()->endOfYear()])
                ->selectRaw("YEAR(fecha_egreso) as periodo, SUM(valor) as valor")
                ->groupBy('periodo')->pluck('valor', 'periodo');
            $ingresos = collect(); $egresos = collect();
            for ($i = 4; $i >= 0; $i--) {
                $ano = $anoActual - $i;
                $ingresos->push(['label' => (string)$ano, 'valor' => (float)($ingresosRaw[$ano] ?? 0)]);
                $egresos->push(['label'  => (string)$ano, 'valor' => (float)($egresosRaw[$ano]  ?? 0)]);
            }
        } else {
            $inicio12 = $hoy->copy()->subMonths(11)->startOfMonth();
            $finMes   = $hoy->copy()->endOfMonth();
            $ingresosRaw = Pago::where('anulado', false)
                ->whereBetween('fecha_pago', [$inicio12, $finMes])
                ->selectRaw("DATE_FORMAT(fecha_pago, '%Y-%m') as periodo, SUM(valor) as valor")
                ->groupBy('periodo')->pluck('valor', 'periodo');
            $egresosRaw = Egreso::where('anulado', false)
                ->whereBetween('fecha_egreso', [$inicio12, $finMes])
                ->selectRaw("DATE_FORMAT(fecha_egreso, '%Y-%m') as periodo, SUM(valor) as valor")
                ->groupBy('periodo')->pluck('valor', 'periodo');
            $ingresos = collect(); $egresos = collect();
            for ($i = 11; $i >= 0; $i--) {
                $m = $hoy->copy()->subMonths($i);
                $k = $m->format('Y-m');
                $ingresos->push(['label' => $m->locale('es')->isoFormat('MMM YY'), 'valor' => (float)($ingresosRaw[$k] ?? 0)]);
                $egresos->push(['label'  => $m->locale('es')->isoFormat('MMM YY'), 'valor' => (float)($egresosRaw[$k]  ?? 0)]);
            }
        }

        return response()->json(compact('ingresos', 'egresos'));
    }
}
