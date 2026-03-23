<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Material;
use App\Models\OrdenLaboratorio;
use App\Models\Paciente;
use App\Models\Pago;
use App\Models\Tratamiento;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();
        $mesAnterior = $hoy->copy()->subMonth();

        // Métricas estables: se cachean 5 minutos (no necesitan ser en tiempo real)
        $stats = Cache::remember('dashboard_stats_' . $hoy->format('Y-m-d'), 300, function () use ($hoy, $mesAnterior) {
            return [
                'totalPacientes'            => Paciente::where('activo', true)->count(),
                'nuevosEsteMes'             => Paciente::whereMonth('created_at', $hoy->month)->whereYear('created_at', $hoy->year)->count(),
                'ingresosDelMes'            => Pago::whereMonth('fecha_pago', $hoy->month)->whereYear('fecha_pago', $hoy->year)->where('anulado', false)->sum('valor'),
                'ingresosMesAnterior'       => Pago::whereMonth('fecha_pago', $mesAnterior->month)->whereYear('fecha_pago', $mesAnterior->year)->where('anulado', false)->sum('valor'),
                'saldoPendiente'            => Tratamiento::where('estado', 'activo')->where('saldo_pendiente', '>', 0)->sum('saldo_pendiente'),
                'pacientesConSaldo'         => Tratamiento::where('estado', 'activo')->where('saldo_pendiente', '>', 0)->distinct('paciente_id')->count('paciente_id'),
                'materialesStockBajo'       => Material::where('activo', true)->whereColumn('stock_actual', '<=', 'stock_minimo')->count(),
                'ordenesLaboratorioVencidas'=> OrdenLaboratorio::where('activo', true)->whereNotIn('estado', ['recibido', 'instalado', 'cancelado'])->whereDate('fecha_entrega_estimada', '<', $hoy)->count(),
            ];
        });

        // Citas de hoy: sin caché (cambian durante el día)
        $citasDeHoy         = Cita::with('paciente')->whereDate('fecha', $hoy)->where('activo', true)->orderBy('hora_inicio')->get();
        $citasHoy           = $citasDeHoy->count();
        $citasPendientesHoy = $citasDeHoy->whereIn('estado', ['pendiente', 'confirmada'])->count();

        $ingresosDelMes      = $stats['ingresosDelMes'];
        $ingresosMesAnterior = $stats['ingresosMesAnterior'];
        $variacionIngresos   = $ingresosMesAnterior > 0
            ? (($ingresosDelMes - $ingresosMesAnterior) / $ingresosMesAnterior) * 100
            : 0;

        return view('dashboard', [
            'totalPacientes'             => $stats['totalPacientes'],
            'nuevosEsteMes'              => $stats['nuevosEsteMes'],
            'citasHoy'                   => $citasHoy,
            'citasPendientesHoy'         => $citasPendientesHoy,
            'citasDeHoy'                 => $citasDeHoy,
            'ingresosDelMes'             => $ingresosDelMes,
            'ingresosMesAnterior'        => $ingresosMesAnterior,
            'variacionIngresos'          => $variacionIngresos,
            'saldoPendiente'             => $stats['saldoPendiente'],
            'pacientesConSaldo'          => $stats['pacientesConSaldo'],
            'materialesStockBajo'        => $stats['materialesStockBajo'],
            'ordenesLaboratorioVencidas' => $stats['ordenesLaboratorioVencidas'],
        ]);
    }
}
