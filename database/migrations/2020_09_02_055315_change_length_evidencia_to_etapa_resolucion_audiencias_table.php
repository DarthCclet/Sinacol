<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeLengthEvidenciaToEtapaResolucionAudienciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('etapa_resolucion_audiencias', function (Blueprint $table) {
            $table->longText('evidencia')->comment('Aqui se agregan las evidencias de cada etapa')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('etapa_resolucion_audiencias', function (Blueprint $table) {
            $table->string('evidencia')->comment('Aqui se agregan las evidencias de cada etapa')->change();
        });
    }
}
