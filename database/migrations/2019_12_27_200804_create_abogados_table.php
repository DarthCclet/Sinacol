<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbogadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abogados', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK de la tabla abogados');
            $table->string('nombre')->comment('Nombre del abogado');
            $table->string('primer_apellido')->comment('Primer apellido del abogado');
            $table->string('segundo_apellido')->comment('Segundo apellido del abogado');
            $table->string('cedula_profesional')->comment('Cedula profesional del abogado');
            $table->string('numero_empleado')->comment('No. de empleado del abogado');
            $table->string('email')->comment('Email del abogado');
            $table->boolean('profedet')->comment('Indicador Booleano que indica si el defensor pertenece a la profedet');
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
        Schema::dropIfExists('abogados');
    }
}
