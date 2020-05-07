<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAudienciaPartesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audiencias_partes', function (Blueprint $table) {
            // llave primaria
            $table->bigIncrements('id')->comment('PK de la tabla audiencias_partes');; 
            // id de la audiencia 
            $table->integer('audiencia_id')->comment('FK de la tabla audiencias');
            $table->foreign('audiencia_id')->references('id')->on('audiencias');
            // id parte que citada a la audiencia
            $table->integer('parte_id')->comment('FK de la parte');
            $table->foreign('parte_id')->references('id')->on('partes');
            // indicador de presencia en la audiencia
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra logicamente.');
            $table->timestamps();
        });
        $tabla_nombre = 'audiencias_partes';
        $comentario_tabla = 'Tabla donde se almacenan las partes que deberan asistir a la audiencia.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audiencias_partes');
    }
}
