<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSolicitanteToResolucionPagoDiferidoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resolucion_pagos_diferidos', function (Blueprint $table) {
            $table->bigInteger('solicitante_id')->nullable()->comment('FK de la parte solicitante');
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
        Schema::table('resolucion_pagos_diferidos', function (Blueprint $table) {
            $table->dropColumn('solicitante_id');
        });
    }
}
