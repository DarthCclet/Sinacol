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
            $table->bigIncrements('id');
            // descripcion del documento 
            $table->string('descripcion');
            // ruta de almacenamiento del archivo
            $table->string('ruta');
            // LLave foranea que apunta al objeto que se asigna el documento
            $table->bigInteger('documentable_id');
            // Clase del objeto al que se está asignando el documento
            $table->string('documentable_type');
            $table->index(['documentable_id', 'documentable_type']);
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
        Schema::dropIfExists('documentos');
    }
}
