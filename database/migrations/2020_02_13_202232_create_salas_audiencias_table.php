<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalasAudienciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salas_audiencias', function (Blueprint $table) {
            // llave primaria
            $table->bigIncrements('id')->comment('PK de la tabla salas_audiencias'); 
            // id del expediente al que corresponde la audiencia
            $table->integer('sala_id')->comment('FK de la tabla salas');
            $table->foreign('sala_id')->references('id')->on('salas');
            // id del expediente al que corresponde la audiencia
            $table->integer('audiencia_id')->comment('FK de la tabla audiencias');
            $table->foreign('audiencia_id')->references('id')->on('audiencias');
            //indicador de que atiende al solicitante
            $table->boolean('solicitante')->comment('indicador de que atiende al solicitante');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra logicamente.');
            $table->timestamps();
        });
        $tabla_nombre = 'salas_audiencias';
        $comentario_tabla = 'Tabla donde se asignan las salas a las audiencias.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salas_audiencias');
    }
}
