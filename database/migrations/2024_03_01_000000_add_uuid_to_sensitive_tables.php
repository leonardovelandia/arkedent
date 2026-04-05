<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private array $tablas = [
        'pacientes',
        'historias_clinicas',
        'evoluciones',
        'citas',
        'presupuestos',
        'consentimientos',
        'autorizaciones_datos',
        'users',
        'fichas_periodontales',
        'fichas_ortodonticas',
        'controles_periodontales',
        'controles_ortodoncia',
        'recetas_medicas',
        'valoraciones',
        'imagenes_clinicas',
        'ordenes_laboratorio',
    ];

    private function getTablaPagos(): ?string
    {
        foreach (['pagos', 'abonos', 'pagos_abonos', 'recibos'] as $nombre) {
            if (Schema::hasTable($nombre)) return $nombre;
        }
        return null;
    }

    public function up(): void
    {
        foreach ($this->tablas as $tabla) {
            $this->agregarUuid($tabla);
        }

        $tablaPagos = $this->getTablaPagos();
        if ($tablaPagos) {
            $this->agregarUuid($tablaPagos);
        }
    }

    private function agregarUuid(string $tabla): void
    {
        if (!Schema::hasTable($tabla)) {
            \Log::info("UUID: tabla '{$tabla}' no existe, omitiendo.");
            return;
        }

        if (Schema::hasColumn($tabla, 'uuid')) {
            \Log::info("UUID: tabla '{$tabla}' ya tiene uuid, omitiendo.");
            return;
        }

        Schema::table($tabla, function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id')
                  ->comment('Identificador único para exposición externa y APIs');
        });

        \DB::table($tabla)->whereNull('uuid')->orderBy('id')->each(function ($row) use ($tabla) {
            \DB::table($tabla)->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
        });

        Schema::table($tabla, function (Blueprint $table) use ($tabla) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
        });

        \Log::info("UUID: agregado correctamente a tabla '{$tabla}'.");
    }

    public function down(): void
    {
        $todasTablas = array_merge($this->tablas, array_filter([$this->getTablaPagos()]));

        foreach ($todasTablas as $tabla) {
            if (Schema::hasTable($tabla) && Schema::hasColumn($tabla, 'uuid')) {
                Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                    try {
                        $table->dropUnique(["{$tabla}_uuid_unique"]);
                    } catch (\Exception $e) {
                        // el nombre del índice puede variar
                    }
                    $table->dropColumn('uuid');
                });
            }
        }
    }
};
