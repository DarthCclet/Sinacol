<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConciliadoresAudienciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conciliadores_audiencias', function (Blueprint $table) {
            // llave primaria
            $table->bigIncrements('id')->comment('PK de la tabla conciliadores_audiencias'); 
            // id del conciliador al que corresponde la audiencia
            $table->integer('conciliador_id')->comment('FK de la tabla conciliador');
            $table->foreign('conciliador_id')->references('id')->on('conciliadores');
            // id de la audiencia 
            $table->integer('audiencia_id')->comment('FK de la tabla audiencias');
            $table->foreign('audiencia_id')->references('id')->on('audiencias');
            // indicador de que atiende al solicitante
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
        Schema::dropIfExists('conciliadores_audiencias');
    }
}
