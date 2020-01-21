<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salas', function (Blueprint $table) {
            // llave primaria
            $table->bigIncrements('id')->comment('Llave primaria de la tabla salas');;
            // nombre de la sala
            $table->string('sala')->comment('nombre de la sala');;
            // id del centro donde esta la sala
            $table->integer('centro_id')->comment('Fk de la tabla centros');;
            $table->foreign('centro_id')->references('id')->on('centros');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra logicamente.');;
            $table->timestamps();
        });
        $tabla_nombre = 'salas';
        $comentario_tabla = 'Tabla que se almacenan los nombres de las salas donde se celebran audiencias.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salas');
    }
}
