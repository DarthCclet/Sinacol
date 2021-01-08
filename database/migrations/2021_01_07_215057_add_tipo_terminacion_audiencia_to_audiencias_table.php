<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoTerminacionAudienciaToAudienciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audiencias', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_terminacion_audiencia_id')->nullable()->comment('Indica el tipo de terminacion de la audiencia');
            $table->foreign('tipo_terminacion_audiencia_id')->references('id')->on('tipo_terminacion_audiencias');
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
            $table->dropForeign(['tipo_terminacion_audiencia_id']);
            $table->dropColumn("tipo_terminacion_audiencia_id");
        });
    }
}
