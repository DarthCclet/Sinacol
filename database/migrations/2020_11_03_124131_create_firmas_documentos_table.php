<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFirmasDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firmas_documentos', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK de la tabla firmas documentos');
            $table->string('firmable_id')->nullable()->comment('id de parte o conciliador que firma');
            $table->string('firmable_type')->nullable()->comment('Nombre de la clase del objeto al que se esta asignando la firma y el documento');
            $table->integer('plantilla_id')->nullable()->comment('FK que apunta al catálogo de plantillas documentos');
            $table->foreign('plantilla_id')->references('id')->on('plantilla_documentos');
            $table->integer('audiencia_id')->nullable()->comment('FK que apunta la audiencia');
            $table->foreign('audiencia_id')->references('id')->on('audiencias');
            $table->integer('solicitud_id')->nullable()->comment('FK que apunta la solicitud');
            $table->foreign('solicitud_id')->references('id')->on('solicitudes');
            $table->text('firma')->nullable()->comment('firma en base 64');
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
        Schema::dropIfExists('firmas_documentos');
    }
}
