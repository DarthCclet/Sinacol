<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoFirmaToFirmasDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('firmas_documentos', function (Blueprint $table) {
            $table->string('tipo_firma')
                ->nullable()
                ->comment('Tipo de la firma del documento. Puede ser autografa, o llave-publica');
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
            $table->dropColumn('tipo_firma');
        });
    }
}
