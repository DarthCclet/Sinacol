<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtapaResolucionAudienciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etapa_resolucion_audiencias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('etapa_resolucion_id')->comment('Llave foránea que relaciona con el municipio');
            $table->foreign('etapa_resolucion_id')->references('id')->on('etapa_resoluciones');
            $table->unsignedBigInteger('audiencia_id')->comment('Llave foránea que relaciona con el municipio');
            $table->foreign('audiencia_id')->references('id')->on('audiencias');
            $table->string('evidencia');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etapa_resolucion_audiencias');
    }
}
