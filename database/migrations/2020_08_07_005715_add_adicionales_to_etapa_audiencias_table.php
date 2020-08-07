<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdicionalesToEtapaAudienciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('etapa_resolucion_audiencias', function (Blueprint $table) {
            $table->boolean('elementos_adicionales')->default(0)->comment('Indicador elementos o prestaciones adicionales a la resolucion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('etapa_audiencias', function (Blueprint $table) {
            //
        });
    }
}
