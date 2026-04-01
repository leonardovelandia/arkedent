<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->string('modo_recordatorio', 20)->default('simple')->after('hora_envio_recordatorio');
            $table->text('plantilla_interactiva_whatsapp')->nullable()->after('plantilla_whatsapp');
            $table->text('plantilla_confirmacion_whatsapp')->nullable()->after('plantilla_interactiva_whatsapp');
            $table->text('plantilla_cancelacion_whatsapp')->nullable()->after('plantilla_confirmacion_whatsapp');
            $table->text('plantilla_reprogramacion_whatsapp')->nullable()->after('plantilla_cancelacion_whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn([
                'modo_recordatorio',
                'plantilla_interactiva_whatsapp',
                'plantilla_confirmacion_whatsapp',
                'plantilla_cancelacion_whatsapp',
                'plantilla_reprogramacion_whatsapp',
            ]);
        });
    }
};
