<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResolucionParteConceptosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resolucion_parte_conceptos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('resolucion_partes_id')->comment('FK de la tabla resolucion_partes');
            $table->foreign('resolucion_partes_id')->references('id')->on('resolucion_partes');
            $table->unsignedBigInteger('concepto_pago_resoluciones_id')->comment('FK de la tabla concepto_pago_resoluciones');
            $table->foreign('concepto_pago_resoluciones_id')->references('id')->on('concepto_pago_resoluciones');
            $table->integer('dias')->nullable()->comment('Numero de dias que se van a pagar');
            $table->decimal('monto', 10, 2)->nullable()->comment('Monto de pago calculado');
            $table->string('otro')->nullable()->comment('Campo para pagos en especie');
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
        Schema::dropIfExists('resolucion_parte_conceptos');
    }
}
