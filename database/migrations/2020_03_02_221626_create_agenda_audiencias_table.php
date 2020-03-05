<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgendaAudienciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agenda_audiencias', function (Blueprint $table) {
            // Llave primaria de la tabla
            $table->bigIncrements('id');
            // id de la audiencia 
            $table->integer('audiencia_id')->comment('FK de la tabla audiencias');
            $table->foreign('audiencia_id')->references('id')->on('audiencias');
            // id del conciliador al que corresponde la audiencia
            $table->integer('conciliador_id')->comment('FK de la tabla conciliador');
            $table->foreign('conciliador_id')->references('id')->on('conciliadores');
            // id de la sala donde se celebrará la audiencia 
            $table->integer('sala_id')->comment('FK de la tabla salas');
            $table->foreign('sala_id')->references('id')->on('salas');
            //indicador de que atiende al solicitante
            $table->boolean('solicitante')->comment('indicador de que atiende al solicitante');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra logicamente.');
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
        Schema::dropIfExists('agenda_audiencias');
    }
}
