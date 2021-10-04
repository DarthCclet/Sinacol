<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBitacoraBuzonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bitacora_buzones', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Llave primaria de la tabla.');
            $table->unsignedBigInteger("parte_id")->nullable()->comment('Llave foranea a tabla partes.');
            $table->foreign("parte_id")->references("id")->on('partes');
            $table->string("descripcion")->comment('Indica la descripción del movimiento a almacenar en bitacora');
            $table->string("tipo_movimiento")->comment('Indica el tipo de movimiento a registrar');
            $table->string("clabe_identificacion")->comment('Indica el la clabe que identifica a la parte (CURP o RFC)');
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
        Schema::dropIfExists('bitacora_buzones');
    }
}
