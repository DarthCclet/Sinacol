<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReagendadoToResolucionesPartes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resolucion_partes', function (Blueprint $table) {
            $table->boolean('nuevaAudiencia')->default(false)->nullable()->comment('indicador de que las partes ya se asignaron a una audiencia procedente de otra audiencia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resoluciones_partes', function (Blueprint $table) {
            $table->dropColumn('nuevaAudiencia');
        });
    }
}
