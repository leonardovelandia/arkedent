<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla evoluciones
        if (Schema::hasTable('evoluciones')) {
            Schema::table('evoluciones', function (Blueprint $table) {
                if (!Schema::hasColumn('evoluciones', 'hora_inicio')) {
                    $table->time('hora_inicio')->nullable()->after('fecha');
                }
                if (!Schema::hasColumn('evoluciones', 'hora_fin')) {
                    $table->time('hora_fin')->nullable()->after('hora_inicio');
                }
            });
        }

        // Tabla citas
        if (Schema::hasTable('citas')) {
            Schema::table('citas', function (Blueprint $table) {
                if (!Schema::hasColumn('citas', 'hora_fin')) {
                    $table->time('hora_fin')->nullable()->after('hora_inicio');
                }
                if (!Schema::hasColumn('citas', 'hora_atencion_real')) {
                    $table->time('hora_atencion_real')->nullable()->after('hora_fin');
                }
            });
        }

        // Tabla valoraciones
        if (Schema::hasTable('valoraciones')) {
            Schema::table('valoraciones', function (Blueprint $table) {
                if (!Schema::hasColumn('valoraciones', 'hora_inicio')) {
                    $table->time('hora_inicio')->nullable()->after('fecha');
                }
                if (!Schema::hasColumn('valoraciones', 'hora_fin')) {
                    $table->time('hora_fin')->nullable()->after('hora_inicio');
                }
            });
        }

        // Tabla controles_ortodoncia
        if (Schema::hasTable('controles_ortodoncia')) {
            Schema::table('controles_ortodoncia', function (Blueprint $table) {
                if (!Schema::hasColumn('controles_ortodoncia', 'hora_inicio')) {
                    $table->time('hora_inicio')->nullable()->after('fecha_control');
                }
                if (!Schema::hasColumn('controles_ortodoncia', 'hora_fin')) {
                    $table->time('hora_fin')->nullable()->after('hora_inicio');
                }
            });
        }

        // Tabla controles_periodontales
        if (Schema::hasTable('controles_periodontales')) {
            Schema::table('controles_periodontales', function (Blueprint $table) {
                if (!Schema::hasColumn('controles_periodontales', 'hora_inicio')) {
                    $table->time('hora_inicio')->nullable()->after('fecha_control');
                }
                if (!Schema::hasColumn('controles_periodontales', 'hora_fin')) {
                    $table->time('hora_fin')->nullable()->after('hora_inicio');
                }
            });
        }

        // Tabla historias_clinicas
        if (Schema::hasTable('historias_clinicas')) {
            Schema::table('historias_clinicas', function (Blueprint $table) {
                if (!Schema::hasColumn('historias_clinicas', 'hora_apertura')) {
                    $table->time('hora_apertura')->nullable()->after('fecha_apertura');
                }
            });
        }
    }

    public function down(): void
    {
        $cambios = [
            'evoluciones'             => ['hora_inicio', 'hora_fin'],
            'citas'                   => ['hora_atencion_real'],
            'valoraciones'            => ['hora_inicio', 'hora_fin'],
            'controles_ortodoncia'    => ['hora_inicio', 'hora_fin'],
            'controles_periodontales' => ['hora_inicio', 'hora_fin'],
            'historias_clinicas'      => ['hora_apertura'],
        ];

        foreach ($cambios as $tabla => $columnas) {
            if (Schema::hasTable($tabla)) {
                Schema::table($tabla, function (Blueprint $table) use ($columnas, $tabla) {
                    foreach ($columnas as $col) {
                        if (Schema::hasColumn($tabla, $col)) {
                            $table->dropColumn($col);
                        }
                    }
                });
            }
        }
    }
};
