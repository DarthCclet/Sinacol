<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentoToFirmasDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('firmas_documentos', function (Blueprint $table) {
            $table->unsignedBigInteger('documento_id')->nullable()->comment('FK a catálogo ocupaciones');
            $table->foreign('documento_id')->references('id')->on('documentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('firmas_documentos', function (Blueprint $table) {
            //
        });
    }
}
