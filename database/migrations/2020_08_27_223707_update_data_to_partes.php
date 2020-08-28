<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDataToPartes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partes', function (Blueprint $table) {
            $table->dropColumn('numero_notaria');
            $table->dropColumn('localidad_notaria');
            $table->dropColumn('nombre_notario');
            $table->string('detalle_instrumento')->nullable();
            $table->unsignedBigInteger('clasificacion_archivo_id')->nullable()->comment('FK de la tabla periodicidades');
            $table->foreign('clasificacion_archivo_id')->references('id')->on('clasificacion_archivos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partes', function (Blueprint $table) {
            $table->string('numero_notaria')->nullable();
            $table->string('localidad_notaria')->nullable();
            $table->string('nombre_notario')->nullable();
            $table->dropForeign(['clasificacion_archivo_id']);
            $table->dropColumn('clasificacion_archivo_id');
            $table->dropColumn('detalle_instrumento');
        });
    }
}
