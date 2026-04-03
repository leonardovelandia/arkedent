<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('configuracion')) {
            Schema::table('configuracion', function (Blueprint $table) {
                if (!Schema::hasColumn('configuracion', 'nombre_doctor')) {
                    $table->string('nombre_doctor', 120)->nullable()->after('firma_registro');
                }
                if (!Schema::hasColumn('configuracion', 'tarjeta_profesional')) {
                    $table->string('tarjeta_profesional', 50)->nullable()->after('nombre_doctor');
                }
                if (!Schema::hasColumn('configuracion', 'especialidad_medica')) {
                    $table->string('especialidad_medica', 100)->nullable()->after('tarjeta_profesional');
                }
                if (!Schema::hasColumn('configuracion', 'universidad')) {
                    $table->string('universidad', 150)->nullable()->after('especialidad_medica');
                }
                if (!Schema::hasColumn('configuracion', 'codigo_habilitacion')) {
                    $table->string('codigo_habilitacion', 50)->nullable()->after('universidad');
                }
                if (!Schema::hasColumn('configuracion', 'tipo_prestador')) {
                    $table->enum('tipo_prestador', [
                        'consultorio_privado',
                        'ips',
                        'centro_medico',
                    ])->default('consultorio_privado')->after('codigo_habilitacion');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('configuracion')) {
            Schema::table('configuracion', function (Blueprint $table) {
                $cols = [
                    'nombre_doctor',
                    'tarjeta_profesional',
                    'especialidad_medica',
                    'universidad',
                    'codigo_habilitacion',
                    'tipo_prestador',
                ];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('configuracion', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
