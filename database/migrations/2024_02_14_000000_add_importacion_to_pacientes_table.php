<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->boolean('importado')->default(false)->after('activo');
            $table->string('fuente_importacion', 50)->nullable()->after('importado');
        });
    }
    public function down(): void {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn(['importado', 'fuente_importacion']);
        });
    }
};
