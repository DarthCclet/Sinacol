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
            $table->unsignedBigInteger('audiencia_parte_id')->nullable()->comment('Llave foránea que relaciona con audiencia parte solicitante');
            $table->unsignedBigInteger('resolucion_partes_id')->nullable()->comment('Indica la resolucion parte de audiencia')->change();
            $table->foreign('audiencia_parte_id')->references('id')->on('audiencias_partes');
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
            $table->dropForeign(['audiencia_parte_id']);
            $table->dropColumn("audiencia_parte_id");
        });
    }
}
