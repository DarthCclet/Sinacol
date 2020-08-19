<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntidadesEmisorasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entidades_emisoras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre')->comment('Nombre de la entidad emisora');
            $table->string('descripcion')->comment('Descripcion de la entidad emisora');
            $table->string('abreviatura')->comment('Siglas o abrevitura de la institucion que emite');
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
        Schema::dropIfExists('entidades_emisoras');
    }
}
