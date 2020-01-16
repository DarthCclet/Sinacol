<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos', function (Blueprint $table) {
            // llave del documento
            $table->bigIncrements('id')->comment('PK de la tabla documentos');
            // descripcion del documento 
            $table->string('descripcion')->comment('descripcion u observaciones del documento');
            // ruta de almacenamiento del archivo
            $table->string('ruta')->comment('ruta donde se almacena el documento');
            // LLave foranea que apunta al objeto que se asigna el documento
            $table->bigInteger('documentable_id')->comment('FK que apunta al objeto que se asigna el documento');
            // Clase del objeto al que se está asignando el documento
            $table->string('documentable_type')->comment('Nombre de la clase del objeto al que se esta asignando el documento');
            $table->index(['documentable_id', 'documentable_type']);
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
        Schema::dropIfExists('documentos');
    }
}
