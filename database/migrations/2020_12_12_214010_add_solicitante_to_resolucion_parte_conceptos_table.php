<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSolicitanteToResolucionParteConceptosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resolucion_parte_conceptos', function (Blueprint $table) {
            $table->unsignedBigInteger('solicitante_id')->nullable()->comment('Llave foránea que relaciona con parte solicitante');
            $table->unsignedBigInteger('resolucion_partes_id')->nullable()->comment('Indica la resolucion parte de audiencia')->change();
            $table->foreign('solicitante_id')->references('id')->on('partes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resolucion_parte_conceptos', function (Blueprint $table) {
            $table->dropForeign(['solicitante_id']);
            $table->dropColumn("solicitante_id");
        });
    }
}
