<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contactos', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Llave primaria del registro de Contactos');

            // LLave foranea que apunta al objeto que se asigna el domicilio
            $table->bigInteger('contactable_id')->comment('Llave foránea que relaciona un contacto con un registro contactable');

            // Clase del objeto al que se está asignando el domicilio
            $table->string('contactable_type')->comment('Es la clase del tipo de objeto contactable');
            // Campo que indica el nombre del usuario
            $table->string('contacto')->comment('Campo que indica la informacion de contacto');
            // Campo que indica el nombre del usuario
            $table->unsignedBigInteger('tipo_contacto_id')->comment('FK que apunta al tipo Contaco');
            $table->foreign('tipo_contacto_id')->references('id')->on('tipo_contactos');
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
        Schema::dropIfExists('contactos');
    }
}
