<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->string('twilio_account_sid', 60)->nullable()->after('ultramsg_token');
            $table->string('twilio_auth_token', 60)->nullable()->after('twilio_account_sid');
            $table->string('twilio_whatsapp_from', 30)->nullable()->after('twilio_auth_token');
            $table->string('whatsapp_provider', 10)->default('ultramsg')->after('twilio_whatsapp_from');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn(['twilio_account_sid', 'twilio_auth_token', 'twilio_whatsapp_from', 'whatsapp_provider']);
        });
    }
};
