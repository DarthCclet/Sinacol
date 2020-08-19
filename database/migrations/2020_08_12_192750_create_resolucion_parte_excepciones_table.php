<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResolucionParteExcepcionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resolucion_parte_excepciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            // id parte solicitante
            $table->integer('parte_solicitante_id')->comment('FK de la parte solicitante');
            $table->foreign('parte_solicitante_id')->references('id')->on('partes');
            
            // id parte solicitada
            $table->integer('parte_solicitada_id')->comment('FK de la parte solicitada');
            $table->foreign('parte_solicitada_id')->references('id')->on('partes');

            
            // id de la resolucion
            $table->integer('resolucion_id')->comment('FK de la resolucion');
            $table->foreign('resolucion_id')->references('id')->on('resoluciones');
            
            $table->integer('conciliador_id')->nullable()->comment('FK de la tabla conciliadores');
            $table->foreign('conciliador_id')->references('id')->on('conciliadores');

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
        Schema::dropIfExists('resolucion_parte_excepciones');
    }
}
