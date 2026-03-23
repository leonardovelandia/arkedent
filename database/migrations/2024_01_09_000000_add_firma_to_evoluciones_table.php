<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('evoluciones', function (Blueprint $table) {
            $table->boolean('firmado')->default(false)->after('activo');
            $table->longText('firma_data')->nullable()->after('firmado');
            $table->datetime('fecha_firma')->nullable()->after('firma_data');
            $table->string('ip_firma', 45)->nullable()->after('fecha_firma');
        });
    }
    public function down(): void {
        Schema::table('evoluciones', function (Blueprint $table) {
            $table->dropColumn(['firmado','firma_data','fecha_firma','ip_firma']);
        });
    }
};
