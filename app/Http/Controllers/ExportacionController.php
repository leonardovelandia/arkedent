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
