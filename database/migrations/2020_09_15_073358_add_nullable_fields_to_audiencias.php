<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNullableFieldsToAudiencias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audiencias', function (Blueprint $table) {
            $table->boolean("encontro_audiencia")->nullable()->default(true);
            $table->date('fecha_audiencia')->nullable()->change();
            $table->time('hora_inicio')->nullable()->change();
            $table->time('hora_fin')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audiencias', function (Blueprint $table) {
            $table->date('fecha_audiencia')->nullable()->change();
            $table->time('hora_inicio')->nullable()->change();
            $table->time('hora_fin')->nullable()->change();
            $table->dropColumn("encontro_audiencia");
        });
    }
}
