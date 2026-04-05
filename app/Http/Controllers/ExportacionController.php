<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExportacionService;

class ExportacionController extends Controller
{
    // ─── PACIENTES ────────────────────────────────────────────────

    public function pacientes(Request $request)
    {
        $this->autorizarModulo('pacientes');

        $incluyeSensibles = $request->boolean('incluir_sensibles');
        $formato          = $request->input('formato', 'excel');

        $query = \App\Models\Paciente::query()->where('activo', true);

        if ($request->filled('filtro_buscar')) {
            $b = $request->input('filtro_buscar');
            $query->where(function ($q) use ($b) {
                $q->where('nombre', 'like', "%{$b}%")
                  ->orWhere('apellido', 'like', "%{$b}%")
                  ->orWhere('numero_documento', 'like', "%{$b}%");
            });
        }

        $pacientes = $query->orderBy('apellido')->get();

        $headers = ['N°', 'N° Historia', 'Nombre', 'Apellido', 'Tipo Doc.', 'Número Doc.', 'Teléfono', 'Email', 'Ciudad', 'Fecha Registro'];
        $campos  = ['basicos'];

        if ($incluyeSensibles) {
            $headers = array_merge($headers, ['Dirección', 'Fecha Nacimiento', 'Nombre Acudiente', 'Observaciones']);
            $campos[] = 'sensibles';
        }

        $datos = $pacientes->map(function ($p, $i) use ($incluyeSensibles) {
            $fila = [
                $i + 1,
                $p->numero_historia,
                $p->nombre,
                $p->apellido,
                $p->tipo_documento,
                $p->numero_documento,
                $p->telefono,
                $p->email,
                $p->ciudad,
                $p->created_at?->format('d/m/Y'),
            ];
            if ($incluyeSensibles) {
                $fila = array_merge($fila, [
                    $p->direccion,
                    $p->fecha_nacimiento?->format('d/m/Y'),
                    $p->nombre_acudiente,
                    $p->observaciones,
                ]);
            }
            return $fila;
        })->toArray();

        ExportacionService::registrarLog('pacientes', $formato, $incluyeSensibles, $campos, $this->filtros($request), count($datos));

        $nombre = 'Pacientes_' . now()->format('Y-m-d_H-i');
        return match ($formato) {
            'csv'   => ExportacionService::generarCSV($headers, $datos, $nombre),
            'pdf'   => ExportacionService::generarPDF($headers, $datos, $nombre, 'Pacientes'),
            default => ExportacionService::generarExcel($headers, $datos, $nombre),
        };
    }

    // ─── HISTORIAS CLÍNICAS ───────────────────────────────────────

    public function historiasClinicas(Request $request)
    {
        $this->autorizarModulo('historias_clinicas');

        $incluyeSensibles = $request->boolean('incluir_sensibles');
        $formato          = $request->input('formato', 'excel');

        $historias = \App\Models\HistoriaClinica::with('paciente')
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = ['N°', 'N° Historia', 'Paciente', 'Documento', 'Fecha Apertura', 'Firmado'];
        $campos  = ['basicos'];

        if ($incluyeSensibles) {
            $headers = array_merge($headers, ['Motivo Consulta', 'Antecedentes Médicos', 'Antecedentes Odontológicos', 'Alergias', 'Medicamentos Actuales']);
            $campos[] = 'sensibles_clinicos';
        }

        $datos = $historias->map(function ($h, $i) use ($incluyeSensibles) {
            $fila = [
                $i + 1,
                $h->numero_historia,
                $h->paciente?->nombre_completo ?? '—',
                $h->paciente?->numero_documento ?? '—',
                $h->fecha_apertura?->format('d/m/Y'),
                $h->firmado ? 'Sí' : 'No',
            ];
            if ($incluyeSensibles) {
                $fila = array_merge($fila, [
                    $h->motivo_consulta,
                    $h->antecedentes_medicos,
                    $h->antecedentes_odontologicos,
                    $h->alergias,
                    $h->medicamentos_actuales,
                ]);
            }
            return $fila;
        })->toArray();

        ExportacionService::registrarLog('historias_clinicas', $formato, $incluyeSensibles, $campos, $this->filtros($request), count($datos));

        $nombre = 'HistoriasClinicas_' . now()->format('Y-m-d_H-i');
        return match ($formato) {
            'csv'   => ExportacionService::generarCSV($headers, $datos, $nombre),
            'pdf'   => ExportacionService::generarPDF($headers, $datos, $nombre, 'Historias Clínicas'),
            default => ExportacionService::generarExcel($headers, $datos, $nombre),
        };
    }

    // ─── EVOLUCIONES ──────────────────────────────────────────────

    public function evoluciones(Request $request)
    {
        $this->autorizarModulo('evoluciones');

        $incluyeSensibles = $request->boolean('incluir_sensibles');
        $formato          = $request->input('formato', 'excel');

        $evoluciones = \App\Models\Evolucion::with('paciente')
            ->orderBy('fecha', 'desc')
            ->get();

        $headers = ['N°', 'N° Evolución', 'Fecha', 'Hora Inicio', 'Hora Fin', 'Paciente', 'Procedimiento'];
        $campos  = ['basicos'];

        if ($incluyeSensibles) {
            $headers = array_merge($headers, ['Diagnóstico', 'Observaciones', 'Indicaciones']);
            $campos[] = 'sensibles_clinicos';
        }

        $datos = $evoluciones->map(function ($e, $i) use ($incluyeSensibles) {
            $fila = [
                $i + 1,
                $e->numero_evolucion ?? '—',
                $e->fecha?->format('d/m/Y'),
                $e->hora_inicio ? \Carbon\Carbon::parse($e->hora_inicio)->format('H:i') : '—',
                $e->hora_fin    ? \Carbon\Carbon::parse($e->hora_fin)->format('H:i')    : '—',
                $e->paciente?->nombre_completo ?? '—',
                $e->procedimiento ?? '—',
            ];
            if ($incluyeSensibles) {
                $fila = array_merge($fila, [
                    $e->diagnostico ?? '—',
                    $e->observaciones ?? '—',
                    $e->indicaciones ?? '—',
                ]);
            }
            return $fila;
        })->toArray();

        ExportacionService::registrarLog('evoluciones', $formato, $incluyeSensibles, $campos, $this->filtros($request), count($datos));

        $nombre = 'Evoluciones_' . now()->format('Y-m-d_H-i');
        return match ($formato) {
            'csv'   => ExportacionService::generarCSV($headers, $datos, $nombre),
            'pdf'   => ExportacionService::generarPDF($headers, $datos, $nombre, 'Evoluciones'),
            default => ExportacionService::generarExcel($headers, $datos, $nombre),
        };
    }

    // ─── CITAS ────────────────────────────────────────────────────

    public function citas(Request $request)
    {
        $this->autorizarModulo('citas');

        $incluyeSensibles = $request->boolean('incluir_sensibles');
        $formato          = $request->input('formato', 'excel');

        $citas = \App\Models\Cita::with('paciente')
            ->orderBy('fecha', 'desc')
            ->get();

        $headers = ['N°', 'Fecha', 'Hora', 'Estado', 'Procedimiento'];
        $campos  = ['basicos'];

        if ($incluyeSensibles) {
            $headers = array_merge($headers, ['Paciente', 'Documento', 'Observaciones']);
            $campos[] = 'sensibles';
        }

        $datos = $citas->map(function ($c, $i) use ($incluyeSensibles) {
            $fila = [
                $i + 1,
                $c->fecha?->format('d/m/Y'),
                $c->hora_inicio ? \Carbon\Carbon::parse($c->hora_inicio)->format('H:i') : '—',
                $c->estado,
                $c->procedimiento ?? '—',
            ];
            if ($incluyeSensibles) {
                $fila = array_merge($fila, [
                    $c->paciente?->nombre_completo ?? '—',
                    $c->paciente?->numero_documento ?? '—',
                    $c->observaciones ?? '—',
                ]);
            }
            return $fila;
        })->toArray();

        ExportacionService::registrarLog('citas', $formato, $incluyeSensibles, $campos, $this->filtros($request), count($datos));

        $nombre = 'Agenda_' . now()->format('Y-m-d_H-i');
        return match ($formato) {
            'csv'   => ExportacionService::generarCSV($headers, $datos, $nombre),
            'pdf'   => ExportacionService::generarPDF($headers, $datos, $nombre, 'Agenda de Citas'),
            default => ExportacionService::generarExcel($headers, $datos, $nombre),
        };
    }

    // ─── PAGOS ────────────────────────────────────────────────────

    public function pagos(Request $request)
    {
        $this->autorizarModulo('pagos');

        $incluyeSensibles = $request->boolean('incluir_sensibles');
        $formato          = $request->input('formato', 'excel');

        $pagos = \App\Models\Pago::with('paciente')
            ->orderBy('fecha_pago', 'desc')
            ->get();

        $headers = ['N°', 'N° Recibo', 'Fecha', 'Valor', 'Método Pago', 'Anulado'];
        $campos  = ['basicos'];

        if ($incluyeSensibles) {
            $headers = array_merge($headers, ['Paciente', 'Documento', 'Concepto']);
            $campos[] = 'sensibles';
        }

        $datos = $pagos->map(function ($p, $i) use ($incluyeSensibles) {
            $fila = [
                $i + 1,
                $p->numero_recibo,
                $p->fecha_pago?->format('d/m/Y'),
                '$ ' . number_format($p->valor ?? 0, 0, ',', '.'),
                $p->metodo_pago_label ?? $p->metodo_pago,
                $p->anulado ? 'Sí' : 'No',
            ];
            if ($incluyeSensibles) {
                $fila = array_merge($fila, [
                    $p->paciente?->nombre_completo ?? '—',
                    $p->paciente?->numero_documento ?? '—',
                    $p->concepto ?? '—',
                ]);
            }
            return $fila;
        })->toArray();

        ExportacionService::registrarLog('pagos', $formato, $incluyeSensibles, $campos, $this->filtros($request), count($datos));

        $nombre = 'Pagos_' . now()->format('Y-m-d_H-i');
        return match ($formato) {
            'csv'   => ExportacionService::generarCSV($headers, $datos, $nombre),
            'pdf'   => ExportacionService::generarPDF($headers, $datos, $nombre, 'Pagos y Abonos'),
            default => ExportacionService::generarExcel($headers, $datos, $nombre),
        };
    }

    // ─── CONSENTIMIENTOS ──────────────────────────────────────────

    public function consentimientos(Request $request)
    {
        $this->autorizarModulo('consentimientos');

        $formato = $request->input('formato', 'excel');

        $consentimientos = \App\Models\Consentimiento::with('paciente')
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = ['N°', 'N° Consentimiento', 'Tipo', 'Paciente', 'Documento', 'Firmado', 'Fecha Firma', 'IP Firma', 'Token Verificación'];
        $campos  = ['todo_sensible'];

        $datos = $consentimientos->map(function ($c, $i) {
            return [
                $i + 1,
                $c->numero_consentimiento ?? '—',
                $c->tipo ?? '—',
                $c->paciente?->nombre_completo ?? '—',
                $c->paciente?->numero_documento ?? '—',
                $c->firmado ? 'Sí' : 'No',
                $c->fecha_firma?->format('d/m/Y H:i') ?? '—',
                $c->ip_firma ?? '—',
                $c->firma_verificacion_token ?? '—',
            ];
        })->toArray();

        ExportacionService::registrarLog('consentimientos', $formato, true, $campos, $this->filtros($request), count($datos));

        $nombre = 'Consentimientos_' . now()->format('Y-m-d_H-i');
        return match ($formato) {
            'csv'   => ExportacionService::generarCSV($headers, $datos, $nombre),
            'pdf'   => ExportacionService::generarPDF($headers, $datos, $nombre, 'Consentimientos Informados'),
            default => ExportacionService::generarExcel($headers, $datos, $nombre),
        };
    }

    // ─── INVENTARIO ───────────────────────────────────────────────

    public function inventario(Request $request)
    {
        $this->autorizarModulo('inventario');

        $formato = $request->input('formato', 'excel');

        $items = \App\Models\Material::where('activo', true)->orderBy('nombre')->get();

        $headers = ['N°', 'Código', 'Nombre', 'Unidad', 'Stock Actual', 'Stock Mínimo', 'Precio Unitario'];
        $datos   = $items->map(function ($item, $i) {
            return [
                $i + 1,
                $item->codigo ?? '—',
                $item->nombre,
                $item->unidad_medida ?? '—',
                $item->stock_actual ?? 0,
                $item->stock_minimo ?? 0,
                '$ ' . number_format($item->precio_unitario ?? 0, 0, ',', '.'),
            ];
        })->toArray();

        ExportacionService::registrarLog('inventario', $formato, false, ['basicos'], $this->filtros($request), count($datos));

        $nombre = 'Inventario_' . now()->format('Y-m-d_H-i');
        return match ($formato) {
            'csv'   => ExportacionService::generarCSV($headers, $datos, $nombre),
            'pdf'   => ExportacionService::generarPDF($headers, $datos, $nombre, 'Inventario de Materiales'),
            default => ExportacionService::generarExcel($headers, $datos, $nombre),
        };
    }

    // ─── REPORTE DE INGRESOS ──────────────────────────────────

    public function reporteIngresos(Request $request)
    {
        $this->autorizarModulo('reportes');

        $incluyeSensibles = $request->boolean('incluir_sensibles');
        $formato          = $request->input('formato', 'excel');

        $desde     = $request->desde     ? \Carbon\Carbon::parse($request->desde)->startOfDay()  : \Carbon\Carbon::now()->startOfMonth();
        $hasta     = $request->hasta     ? \Carbon\Carbon::parse($request->hasta)->endOfDay()    : \Carbon\Carbon::now()->endOfDay();
        $metodoPago = $request->metodo_pago;

        $query = \App\Models\Pago::with('paciente')
            ->whereBetween('fecha_pago', [$desde, $hasta])
            ->where('anulado', false);

        if ($metodoPago) {
            $query->where('metodo_pago', $metodoPago);
        }

        $pagos = $query->orderBy('fecha_pago', 'desc')->get();

        $headers = ['N°', 'Fecha', 'N° Recibo', 'Concepto', 'Valor', 'Método de Pago', 'Referencia'];
        $campos  = ['basicos'];

        if ($incluyeSensibles) {
            $headers = array_merge($headers, ['Paciente', 'Documento']);
            $campos[] = 'sensibles';
        }

        $datos = $pagos->map(function ($p, $i) use ($incluyeSensibles) {
            $fila = [
                $i + 1,
                $p->fecha_pago?->format('d/m/Y'),
                $p->numero_recibo ?? '—',
                $p->concepto ?? '—',
                '$ ' . number_format($p->valor ?? 0, 0, ',', '.'),
                $p->metodo_pago_label ?? $p->metodo_pago ?? '—',
                $p->referencia ?? '—',
            ];
            if ($incluyeSensibles) {
                $fila = array_merge($fila, [
                    $p->paciente?->nombre_completo ?? '—',
                    $p->paciente?->numero_documento ?? '—',
                ]);
            }
            return $fila;
        })->toArray();

        ExportacionService::registrarLog('reporte_ingresos', $formato, $incluyeSensibles, $campos, $this->filtros($request), count($datos));

        $nombre = 'Reporte_Ingresos_' . now()->format('Y-m-d_H-i');
        return match ($formato) {
            'csv'   => ExportacionService::generarCSV($headers, $datos, $nombre),
            'pdf'   => ExportacionService::generarPDF($headers, $datos, $nombre, 'Reporte de Ingresos'),
            default => ExportacionService::generarExcel($headers, $datos, $nombre),
        };
    }

    // ─── REPORTE DE PACIENTES ATENDIDOS ───────────────────────

    public function reportePacientesAtendidos(Request $request)
    {
        $this->autorizarModulo('reportes');

        $incluyeSensibles = $request->boolean('incluir_sensibles');
        $formato          = $request->input('formato', 'excel');

        $desde  = $request->desde  ? \Carbon\Carbon::parse($request->desde)->startOfDay()  : \Carbon\Carbon::now()->startOfYear();
        $hasta  = $request->hasta  ? \Carbon\Carbon::parse($request->hasta)->endOfDay()    : \Carbon\Carbon::now()->endOfDay();
        $genero = $request->genero;
        $ciudad = $request->ciudad;

        $query = \App\Models\Paciente::where('activo', true)
            ->whereBetween('created_at', [$desde, $hasta]);

        if ($genero) $query->where('genero', $genero);
        if ($ciudad) $query->where('ciudad', 'like', "%{$ciudad}%");

        $pacientes = $query->orderBy('apellido')->get();

        $headers = ['N°', 'Nombre Completo', 'Género', 'Ciudad', 'Teléfono', 'Email', 'Fecha Registro'];
        $campos  = ['basicos'];

        if ($incluyeSensibles) {
            $headers = array_merge($headers, ['Documento', 'Diagnóstico / Observaciones']);
            $campos[] = 'sensibles';
        }

        $datos = $pacientes->map(function ($p, $i) use ($incluyeSensibles) {
            $fila = [
                $i + 1,
                trim(($p->nombre ?? '') . ' ' . ($p->apellido ?? '')),
                $p->genero ?? '—',
                $p->ciudad ?? '—',
                $p->telefono ?? '—',
                $p->email ?? '—',
                $p->created_at?->format('d/m/Y'),
            ];
            if ($incluyeSensibles) {
                $fila = array_merge($fila, [
                    $p->numero_documento ?? '—',
                    $p->observaciones ?? '—',
                ]);
            }
            return $fila;
        })->toArray();

        ExportacionService::registrarLog('reporte_pacientes', $formato, $incluyeSensibles, $campos, $this->filtros($request), count($datos));

        $nombre = 'Pacientes_Atendidos_' . now()->format('Y-m-d_H-i');
        return match ($formato) {
            'csv'   => ExportacionService::generarCSV($headers, $datos, $nombre),
            'pdf'   => ExportacionService::generarPDF($headers, $datos, $nombre, 'Reporte de Pacientes'),
            default => ExportacionService::generarExcel($headers, $datos, $nombre),
        };
    }

    // ─── EGRESOS ──────────────────────────────────────────────

    public function egresos(Request $request)
    {
        $this->autorizarModulo('egresos');

        $formato = $request->input('formato', 'excel');

        $desde      = $request->desde      ? \Carbon\Carbon::parse($request->desde)->startOfDay()  : \Carbon\Carbon::now()->startOfMonth();
        $hasta      = $request->hasta      ? \Carbon\Carbon::parse($request->hasta)->endOfDay()    : \Carbon\Carbon::now()->endOfDay();
        $categoriaId = $request->categoria_id;
        $metodoPago  = $request->metodo_pago;

        $query = \App\Models\Egreso::with('categoria')
            ->whereBetween('fecha_egreso', [$desde, $hasta])
            ->where('anulado', false);

        if ($categoriaId) $query->where('categoria_id', $categoriaId);
        if ($metodoPago)  $query->where('metodo_pago', $metodoPago);

        $egresos = $query->orderBy('fecha_egreso', 'desc')->get();

        $headers = ['N°', 'Fecha', 'N° Egreso', 'Categoría', 'Descripción / Concepto', 'Valor', 'Método de Pago', 'Registrado por'];

        $datos = $egresos->map(function ($e, $i) {
            return [
                $i + 1,
                $e->fecha_egreso?->format('d/m/Y'),
                $e->numero_egreso ?? '—',
                $e->categoria?->nombre ?? '—',
                $e->concepto ?? '—',
                '$ ' . number_format($e->valor ?? 0, 0, ',', '.'),
                $e->metodo_pago_label ?? $e->metodo_pago ?? '—',
                $e->registradoPor?->name ?? '—',
            ];
        })->toArray();

        ExportacionService::registrarLog('egresos', $formato, false, ['basicos'], $this->filtros($request), count($datos));

        $nombre = 'Egresos_' . now()->format('Y-m-d_H-i');
        return match ($formato) {
            'csv'   => ExportacionService::generarCSV($headers, $datos, $nombre),
            'pdf'   => ExportacionService::generarPDF($headers, $datos, $nombre, 'Egresos'),
            default => ExportacionService::generarExcel($headers, $datos, $nombre),
        };
    }

    // ─── LIBRO DE CAJA ────────────────────────────────────────

    public function libroCaja(Request $request)
    {
        $this->autorizarModulo('libro_contable');

        $formato = $request->input('formato', 'excel');

        $desde = $request->desde ? \Carbon\Carbon::parse($request->desde)->startOfDay() : \Carbon\Carbon::now()->startOfMonth();
        $hasta = $request->hasta ? \Carbon\Carbon::parse($request->hasta)->endOfDay()   : \Carbon\Carbon::now()->endOfDay();
        $tipo  = $request->tipo;

        $query = \App\Models\LibroContable::whereBetween('fecha_movimiento', [$desde, $hasta])
            ->where('excluido', false)
            ->where('activo', true);

        if ($tipo) $query->where('tipo', $tipo);

        $movimientos = $query->orderBy('fecha_movimiento')->orderBy('id')->get();

        // Calcular saldo acumulado
        $saldo = \App\Models\LibroContable::where('fecha_movimiento', '<', $desde)
            ->where('excluido', false)->where('activo', true)
            ->selectRaw("SUM(CASE WHEN tipo='ingreso' THEN valor ELSE -valor END) as saldo")
            ->value('saldo') ?? 0;

        $headers = ['N° Asiento', 'Fecha', 'Tipo', 'Origen', 'Concepto', 'Categoría', 'Referencia', 'Método Pago', 'Ingreso', 'Egreso', 'Saldo Acumulado'];

        $datos = $movimientos->map(function ($mov) use (&$saldo) {
            if (!$mov->excluido) {
                $saldo += $mov->tipo === 'ingreso' ? $mov->valor : -$mov->valor;
            }
            return [
                $mov->numero_asiento ?? '—',
                $mov->fecha_movimiento?->format('d/m/Y'),
                ucfirst($mov->tipo),
                $mov->origen ?? '—',
                $mov->concepto ?? '—',
                $mov->categoria ?? '—',
                $mov->referencia ?? '—',
                $mov->metodo_pago ?? '—',
                $mov->tipo === 'ingreso' ? '$ ' . number_format($mov->valor, 0, ',', '.') : '',
                $mov->tipo === 'egreso'  ? '$ ' . number_format($mov->valor, 0, ',', '.') : '',
                '$ ' . number_format($saldo, 0, ',', '.'),
            ];
        })->toArray();

        ExportacionService::registrarLog('libro_caja', $formato, false, ['basicos'], $this->filtros($request), count($datos));

        $nombre = 'Libro_Caja_' . now()->format('Y-m-d_H-i');
        return match ($formato) {
            'csv'   => ExportacionService::generarCSV($headers, $datos, $nombre),
            'pdf'   => ExportacionService::generarPDF($headers, $datos, $nombre, 'Libro de Caja'),
            default => ExportacionService::generarExcel($headers, $datos, $nombre),
        };
    }

    // ─── ESTADO DE RESULTADOS ─────────────────────────────────

    public function estadoResultados(Request $request)
    {
        $this->autorizarModulo('libro_contable');

        $formato = $request->input('formato', 'excel');
        $mes     = $request->mes ?? now()->month;
        $ano     = $request->ano ?? now()->year;
        $fecha   = \Carbon\Carbon::createFromDate($ano, $mes, 1);

        $ingresosPorOrigen = \App\Models\LibroContable::where('tipo', 'ingreso')
            ->whereMonth('fecha_movimiento', $mes)
            ->whereYear('fecha_movimiento', $ano)
            ->where('excluido', false)
            ->selectRaw('origen, SUM(valor) as total')
            ->groupBy('origen')
            ->get();

        $egresosPorCategoria = \App\Models\LibroContable::where('tipo', 'egreso')
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
        $margen        = $totalIngresos > 0 ? round(($utilidad / $totalIngresos) * 100, 2) : 0;

        $nombreMes = $fecha->locale('es')->isoFormat('MMMM [de] YYYY');
        $headers   = ['Concepto', 'Categoría / Origen', 'Valor'];
        $datos     = [];

        $datos[] = ['--- INGRESOS ---', '', ''];
        foreach ($ingresosPorOrigen as $ing) {
            $datos[] = ['Ingreso', ucfirst($ing->origen ?? '—'), '$ ' . number_format($ing->total, 0, ',', '.')];
        }
        $datos[] = ['TOTAL INGRESOS', '', '$ ' . number_format($totalIngresos, 0, ',', '.')];
        $datos[] = ['', '', ''];
        $datos[] = ['--- EGRESOS ---', '', ''];
        foreach ($egresosPorCategoria as $egr) {
            $datos[] = [ucfirst($egr->categoria ?? 'Sin categoría'), ucfirst($egr->origen ?? '—'), '$ ' . number_format($egr->total, 0, ',', '.')];
        }
        $datos[] = ['TOTAL EGRESOS', '', '$ ' . number_format($totalEgresos, 0, ',', '.')];
        $datos[] = ['', '', ''];
        $datos[] = ['UTILIDAD NETA', '', '$ ' . number_format($utilidad, 0, ',', '.')];
        $datos[] = ['MARGEN (%)', '', $margen . '%'];

        ExportacionService::registrarLog('estado_resultados', $formato, false, ['basicos'], $this->filtros($request), count($datos));

        $nombre = 'Estado_Resultados_' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '_' . $ano;
        return match ($formato) {
            'csv'   => ExportacionService::generarCSV($headers, $datos, $nombre),
            'pdf'   => ExportacionService::generarPDF($headers, $datos, $nombre, 'Estado de Resultados — ' . $nombreMes),
            default => ExportacionService::generarExcel($headers, $datos, $nombre),
        };
    }

    // ─── COMPARATIVO 12 MESES ────────────────────────────────

    public function comparativo12Meses(Request $request)
    {
        $this->autorizarModulo('libro_contable');

        $formato = $request->input('formato', 'excel');

        $meses    = collect();
        $prevUtil = null;
        for ($i = 11; $i >= 0; $i--) {
            $fecha    = now()->subMonths($i);
            $ingresos = (float) \App\Models\LibroContable::where('tipo', 'ingreso')
                ->whereMonth('fecha_movimiento', $fecha->month)
                ->whereYear('fecha_movimiento', $fecha->year)
                ->where('excluido', false)->sum('valor');
            $egresos  = (float) \App\Models\LibroContable::where('tipo', 'egreso')
                ->whereMonth('fecha_movimiento', $fecha->month)
                ->whereYear('fecha_movimiento', $fecha->year)
                ->where('excluido', false)->sum('valor');
            $utilidad = $ingresos - $egresos;
            $margen   = $ingresos > 0 ? round(($utilidad / $ingresos) * 100, 1) : 0;
            $variacion = ($prevUtil !== null && $prevUtil != 0)
                ? round((($utilidad - $prevUtil) / abs($prevUtil)) * 100, 1) . '%'
                : '—';
            $meses->push([
                'mes'       => $fecha->locale('es')->isoFormat('MMMM YYYY'),
                'ingresos'  => $ingresos,
                'egresos'   => $egresos,
                'utilidad'  => $utilidad,
                'margen'    => $margen,
                'variacion' => $variacion,
            ]);
            $prevUtil = $utilidad;
        }

        $headers = ['Mes', 'Ingresos', 'Egresos', 'Utilidad', 'Margen %', 'Variación vs Mes Anterior'];
        $datos   = $meses->map(function ($m) {
            return [
                $m['mes'],
                '$ ' . number_format($m['ingresos'], 0, ',', '.'),
                '$ ' . number_format($m['egresos'],  0, ',', '.'),
                '$ ' . number_format($m['utilidad'], 0, ',', '.'),
                $m['margen'] . '%',
                $m['variacion'],
            ];
        })->toArray();

        ExportacionService::registrarLog('comparativo_12_meses', $formato, false, ['basicos'], $this->filtros($request), count($datos));

        $nombre = 'Comparativo_12_Meses_' . now()->format('Y');
        return match ($formato) {
            'csv'   => ExportacionService::generarCSV($headers, $datos, $nombre),
            'pdf'   => ExportacionService::generarPDF($headers, $datos, $nombre, 'Comparativo 12 Meses'),
            default => ExportacionService::generarExcel($headers, $datos, $nombre),
        };
    }

    // ─── HELPERS ──────────────────────────────────────────────────

    private function autorizarModulo(string $modulo): void
    {
        if (auth()->user()->hasRole('asistente')) {
            $permitidos = ['citas', 'inventario'];
            if (!in_array($modulo, $permitidos)) {
                abort(403, 'No tienes permiso para exportar este módulo.');
            }
        }
    }

    private function filtros(Request $request): array
    {
        return collect($request->all())
            ->except(['_token', 'formato', 'incluir_sensibles', 'modulo'])
            ->filter(fn($v) => $v !== null && $v !== '')
            ->toArray();
    }
}
