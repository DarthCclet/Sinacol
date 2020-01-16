<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResolucionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("resoluciones",function(Blueprint $table){
            // llave primaria
            $table->bigIncrements('id')->comment('PK de la tabla resoluciones');;
            // descripcion de la resolucion
            $table->string('resolucion')->comment('Descripcion de la resolucion');;
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra logicamente.');;
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
        Schema::dropIfExists('resoluciones');
    }
}
