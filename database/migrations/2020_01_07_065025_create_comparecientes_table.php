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
            $table->bigIncrements('id')->comment('PK de la tabla comparecientes');; 
            // id de la audiencia donde se comparece
            $table->integer('audiencia_id')->comment('FK de la tabla audiencias');
            $table->foreign('audiencia_id')->references('id')->on('audiencias');
            // id parte que comparece
            $table->integer('parte_id')->comment('FK de la parte');
            $table->foreign('parte_id')->references('id')->on('partes');
            // indicador de presencia en la audiencia
            $table->boolean('presentado')->comment('Indicador de precencia en la audiencia');
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
        Schema::dropIfExists('comparecientes');
    }
}
