<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAudienciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audiencias', function (Blueprint $table) {
            // llave primaria
            $table->bigIncrements('id'); 
            // id del expediente al que corresponde la audiencia
            $table->integer('expediente_id');
            $table->foreign('expediente_id')->references('id')->on('expedientes');
            // id del conciliador asignado a la audiencia
            $table->integer('conciliador_id');
            $table->foreign('conciliador_id')->references('id')->on('conciliadores');
            // id de la sala donde se celebrará la audiencia
            $table->integer('sala_id');     
            $table->foreign('sala_id')->references('id')->on('salas');
            // id de la resolución de la audiencia
            $table->integer('resolucion_id');
            $table->foreign('resolucion_id')->references('id')->on('resoluciones');
            // id de la parte que sera el responsable de cumplir los acuerdos
            $table->integer('parte_responsable_id');
            $table->foreign('parte_responsable_id')->references('id')->on('partes');
            // fecha en que se celebrará la audiencia
            $table->date('fecha_audiencia');
            // hora de inicio de la audiencia
            $table->time('hora_inicio');
            // hora fin de la audiencia
            $table->time('hora_fin');
            // numero consecutivo para las audiencias de un expediente
            $table->integer('numero_audiencia');
            // indicador de audiencia generada por reprogramacion
            $table->boolean('reprogramada');
            // desahgo de la resolucion
            $table->string('desahogo');
            // convenio de la resolucion
            $table->string('convenio');
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
        Schema::dropIfExists('audiencias');
    }
}
