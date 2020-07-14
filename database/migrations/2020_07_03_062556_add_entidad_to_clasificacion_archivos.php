<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEntidadToClasificacionArchivos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clasificacion_archivos', function (Blueprint $table) {
            $table->string('entidad')->nullable()->comment('entidad que emite el documento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clasificacion_archivos', function (Blueprint $table) {
            $table->dropColumn('entidad');
        });
    }
}
