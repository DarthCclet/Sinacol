<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTextoFirmadoToFirmasDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('firmas_documentos', function (Blueprint $table) {
            $table->text('texto_firmado')
                ->nullable()
                ->comment('Texo original que avala la firma contenida en el registro');
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
            $table->dropColumn('texto_firmado');
        });
    }
}
