<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComparecientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comparecientes', function (Blueprint $table) {
            // llave primaria
            $table->bigIncrements('id'); 
            // id de la audiencia donde se comparece
			$table->integer('audiencia_id');
            $table->foreign('audiencia_id')->references('id')->on('audiencias');
            // id parte que comparece
            $table->integer('parte_id');
			$table->foreign('parte_id')->references('id')->on('partes');
            // indicador de presencia en la audiencia
            $table->boolean('presentado');
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
        Schema::dropIfExists('comparecientes');
    }
}
