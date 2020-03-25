<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contadores', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK de la tabla tipo_contadores');
            $table->integer('anio')->comment('Nombre del tipo del contador  ');
            $table->integer('contador')->comment('Nombre del tipo del contador  ');
            $table->integer('centro_id')->nullable()->comment('Fk de la tabla centros');
            $table->foreign('centro_id')->references('id')->on('centros');
            $table->integer('tipo_contador_id')->comment('Fk de la tabla tipo_contadores');
            $table->foreign('tipo_contador_id')->references('id')->on('tipo_contadores');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro, modifica y se borra lógicamente.');
            $table->timestamps();
        });
        $tabla_nombre = 'contadores';
        $comentario_tabla = 'Tabla donde se almacenan Los contadores';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contadores');
    }
}
