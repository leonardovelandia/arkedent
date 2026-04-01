<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->boolean('recordatorios_email_activo')->default(false)->after('recordatorios_activos');
            $table->boolean('recordatorios_whatsapp_activo')->default(false)->after('recordatorios_email_activo');
            $table->integer('horas_anticipacion')->default(24)->after('recordatorios_whatsapp_activo');
            $table->string('ultramsg_token', 100)->nullable()->after('horas_anticipacion');
            $table->string('ultramsg_instance', 50)->nullable()->after('ultramsg_token');
            $table->string('mail_from_name', 100)->nullable()->after('ultramsg_instance');
            $table->string('mail_from_address', 120)->nullable()->after('mail_from_name');
            $table->text('plantilla_email')->nullable()->after('mail_from_address');
            $table->text('plantilla_whatsapp')->nullable()->after('plantilla_email');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn([
                'recordatorios_email_activo',
                'recordatorios_whatsapp_activo',
                'horas_anticipacion',
                'ultramsg_token',
                'ultramsg_instance',
                'mail_from_name',
                'mail_from_address',
                'plantilla_email',
                'plantilla_whatsapp',
            ]);
        });
    }
};
