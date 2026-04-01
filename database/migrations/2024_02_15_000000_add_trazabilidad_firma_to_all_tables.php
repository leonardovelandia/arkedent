<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tablas = [
            'consentimientos',
            'autorizaciones_datos',
            'presupuestos',
            'evoluciones',
            'historias_clinicas',
            'correcciones_historia',
            'correcciones_evolucion',
        ];

        foreach ($tablas as $tabla) {
            if (!Schema::hasTable($tabla)) continue;
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                if (!Schema::hasColumn($tabla, 'firma_user_agent'))
                    $table->text('firma_user_agent')->nullable()->after('ip_firma');
                if (!Schema::hasColumn($tabla, 'firma_timestamp'))
                    $table->datetime('firma_timestamp')->nullable()->after('firma_user_agent');
                if (!Schema::hasColumn($tabla, 'firma_timezone'))
                    $table->string('firma_timezone', 50)->nullable()->default('America/Bogota')->after('firma_timestamp');
                if (!Schema::hasColumn($tabla, 'firma_hash'))
                    $table->string('firma_hash', 64)->nullable()->after('firma_timezone');
                if (!Schema::hasColumn($tabla, 'documento_hash'))
                    $table->string('documento_hash', 64)->nullable()->after('firma_hash');
                if (!Schema::hasColumn($tabla, 'firma_dispositivo'))
                    $table->string('firma_dispositivo', 100)->nullable()->after('documento_hash');
                if (!Schema::hasColumn($tabla, 'firma_navegador'))
                    $table->string('firma_navegador', 100)->nullable()->after('firma_dispositivo');
                if (!Schema::hasColumn($tabla, 'firma_verificacion_token'))
                    $table->string('firma_verificacion_token', 64)->nullable()->unique()->after('firma_navegador');
            });
        }
    }

    public function down(): void
    {
        $campos = [
            'firma_user_agent', 'firma_timestamp', 'firma_timezone',
            'firma_hash', 'documento_hash', 'firma_dispositivo',
            'firma_navegador', 'firma_verificacion_token',
        ];
        $tablas = [
            'consentimientos', 'autorizaciones_datos', 'presupuestos',
            'evoluciones', 'historias_clinicas', 'correcciones_historia', 'correcciones_evolucion',
        ];
        foreach ($tablas as $tabla) {
            if (!Schema::hasTable($tabla)) continue;
            Schema::table($tabla, function (Blueprint $table) use ($campos, $tabla) {
                $drop = array_filter($campos, fn($c) => Schema::hasColumn($tabla, $c));
                if ($drop) $table->dropColumn(array_values($drop));
            });
        }
    }
};
