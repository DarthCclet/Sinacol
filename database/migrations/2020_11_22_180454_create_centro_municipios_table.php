<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentroMunicipiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('centro_municipios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('centro_id')->comment('Fk de la tabla centros');;
            $table->foreign('centro_id')->references('id')->on('centros');
            $table->string('municipio')->comment('Municipio que cubre el centro');
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
        Schema::dropIfExists('centro_municipios');
    }
}
