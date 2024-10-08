<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaNotificacionToAudienciasPartes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audiencias_partes', function (Blueprint $table) {
            $table->date("fecha_notificacion")->nullable();
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
            $table->dropColumn("fecha_notificacion");
        });
    }
}
