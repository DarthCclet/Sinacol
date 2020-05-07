<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResolucionPartesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resolucion_partes', function (Blueprint $table) {
            // llave primaria
            $table->bigIncrements('id')->comment('PK de la tabla audiencias_partes');
            
            // id de la audiencia 
            $table->integer('audiencia_id')->comment('FK de la tabla audiencias');
            $table->foreign('audiencia_id')->references('id')->on('audiencias');
            
            // id parte solicitante
            $table->integer('parte_solicitante_id')->comment('FK de la parte solicitante');
            $table->foreign('parte_solicitante_id')->references('id')->on('partes');
            
            // id parte solicitada
            $table->integer('parte_solicitada_id')->comment('FK de la parte solicitada');
            $table->foreign('parte_solicitada_id')->references('id')->on('partes');
            
            // id de la resolucion
            $table->integer('resolucion_id')->comment('FK de la resolucion');
            $table->foreign('resolucion_id')->references('id')->on('resoluciones');
            
            // id de la resolucion
            $table->integer('motivo_archivado_id')->nullable()->comment('FK de la resolucion');
            $table->foreign('motivo_archivado_id')->references('id')->on('motivo_archivados');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
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
        Schema::dropIfExists('resolucion_partes');
    }
}
