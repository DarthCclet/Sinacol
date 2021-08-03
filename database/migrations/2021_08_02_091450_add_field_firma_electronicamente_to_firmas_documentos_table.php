<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldFirmaElectronicamenteToFirmasDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('firmas_documentos', function (Blueprint $table) {
            $table->boolean('firma_electronicamente')->default(false)->comment('Nos indica si esta persona debe firmar electronicamente el documento');
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
            $table->dropColumn('firma_electronicamente');
        });
    }
}
