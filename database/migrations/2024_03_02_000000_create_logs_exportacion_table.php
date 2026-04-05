<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs_exportacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_nombre', 150)->nullable();
            $table->string('modulo', 100);
            $table->string('formato', 10);
            $table->boolean('incluyo_sensibles')->default(false);
            $table->json('campos_exportados')->nullable();
            $table->json('filtros_aplicados')->nullable();
            $table->integer('total_registros')->default(0);
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['user_id', 'created_at']);
            $table->index('modulo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_exportacion');
    }
};
