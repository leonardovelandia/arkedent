<?php

namespace App\Console\Commands;

use App\Models\Compra;
use App\Models\Egreso;
use App\Models\LibroContable;
use App\Models\OrdenLaboratorio;
use App\Models\Pago;
use Illuminate\Console\Command;

class MigrarLibroContable extends Command
{
    protected $signature   = 'libro:migrar';
    protected $description = 'Migra movimientos históricos al libro contable';

    public function handle(): int
    {
        $this->info('Migrando pagos de pacientes...');
        $contPagos = 0;
        Pago::where('anulado', false)->with('paciente')->each(function ($pago) use (&$contPagos) {
            if (!LibroContable::where('origen', 'pago_paciente')->where('origen_id', $pago->id)->exists()) {
                $nombrePaciente = $pago->paciente->nombre_completo ?? '';
                LibroContable::registrarMovimiento(
                    'ingreso', 'pago_paciente', $pago->id, 'App\Models\Pago',
                    "Pago paciente — {$nombrePaciente} — {$pago->concepto}",
                    (float) $pago->valor,
                    $pago->fecha_pago,
                    $pago->metodo_pago,
                    $pago->numero_recibo,
                    'Ingresos por servicios'
                );
                $contPagos++;
            }
        });
        $this->info("✓ {$contPagos} pago(s) migrado(s)");

        $this->info('Migrando egresos manuales...');
        $contEgresos = 0;
        Egreso::where('anulado', false)->with('categoria')->each(function ($egreso) use (&$contEgresos) {
            if (!LibroContable::where('origen', 'egreso_manual')->where('origen_id', $egreso->id)->exists()) {
                LibroContable::registrarMovimiento(
                    'egreso', 'egreso_manual', $egreso->id, 'App\Models\Egreso',
                    $egreso->concepto,
                    (float) $egreso->valor,
                    $egreso->fecha_egreso,
                    $egreso->metodo_pago,
                    $egreso->numero_comprobante,
                    $egreso->categoria?->nombre,
                    $egreso->descripcion
                );
                $contEgresos++;
            }
        });
        $this->info("✓ {$contEgresos} egreso(s) migrado(s)");

        $this->info('Migrando compras a proveedores...');
        $contCompras = 0;
        Compra::where('estado', 'pagada')->with('proveedor')->each(function ($compra) use (&$contCompras) {
            if (!LibroContable::where('origen', 'compra_proveedor')->where('origen_id', $compra->id)->exists()) {
                $nombreProveedor  = $compra->proveedor->nombre ?? '';
                $numeroFormateado = $compra->numero_formateado ?? $compra->numero_compra;
                LibroContable::registrarMovimiento(
                    'egreso', 'compra_proveedor', $compra->id, 'App\Models\Compra',
                    "Compra {$numeroFormateado} — {$nombreProveedor}",
                    (float) $compra->total,
                    $compra->fecha_compra,
                    $compra->metodo_pago,
                    $compra->numero_factura ?? $numeroFormateado,
                    'Compras a proveedores'
                );
                $contCompras++;
            }
        });
        $this->info("✓ {$contCompras} compra(s) migrada(s)");

        $this->info('Migrando gastos de laboratorio...');
        $contLab = 0;
        OrdenLaboratorio::whereIn('estado', ['recibido', 'instalado'])
            ->where('precio_laboratorio', '>', 0)
            ->with('laboratorio')
            ->each(function ($orden) use (&$contLab) {
                if (!LibroContable::where('origen', 'gasto_laboratorio')->where('origen_id', $orden->id)->exists()) {
                    $nombreLab    = $orden->laboratorio->nombre ?? '';
                    $numFormateado = $orden->numero_formateado;
                    $fechaMov = $orden->fecha_recepcion
                        ? $orden->fecha_recepcion->toDateString()
                        : $orden->updated_at->toDateString();
                    LibroContable::registrarMovimiento(
                        'egreso', 'gasto_laboratorio', $orden->id, 'App\Models\OrdenLaboratorio',
                        "Laboratorio {$numFormateado} — {$nombreLab} — {$orden->tipo_trabajo}",
                        (float) $orden->precio_laboratorio,
                        $fechaMov,
                        null,
                        $numFormateado,
                        'Gastos de laboratorio'
                    );
                    $contLab++;
                }
            });
        $this->info("✓ {$contLab} orden(es) de laboratorio migrada(s)");

        $this->newLine();
        $this->info('✅ Migración completada exitosamente.');

        return self::SUCCESS;
    }
}
