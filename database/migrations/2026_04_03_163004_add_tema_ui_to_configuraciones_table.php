<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->string('tema_ui', 20)->default('clasico')->after('tema');
        });
    }

    public function down()
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn('tema_ui');
        });
    }
};
