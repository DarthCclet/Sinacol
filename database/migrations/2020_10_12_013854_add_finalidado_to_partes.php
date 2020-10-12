<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinalidadoToPartes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audiencias_partes', function (Blueprint $table) {
            $table->string("finalizado")->nullable();
            $table->bigInteger("finalizado_id")->nullable();
            $table->string("detalle")->nullable();
            $table->bigInteger("detalle_id")->nullable();
            $table->string("documento")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audiencias_partes', function (Blueprint $table) {
            $table->dropColumn("finalizado");
            $table->dropColumn("finalizado_id");
            $table->dropColumn("detalle");
            $table->dropColumn("detalle_id");
            $table->dropColumn("documento");
        });
    }
}
