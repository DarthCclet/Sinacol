<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResolucionPagosDiferidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resolucion_pagos_diferidos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('resolucion_parte_id')->comment('FK de resolucion partes');
            $table->foreign('resolucion_parte_id')->references('id')->on('resolucion_partes');
            $table->decimal('monto', 10, 2)->comment('Monto de pago calculado');
            $table->dateTime('fecha_pago')->comment('Fecha de pago');
            $table->boolean('pagado')->comment('Indica si el pago establecido ha sido liquidado');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('convenio_pagos_diferidos');
    }
}
