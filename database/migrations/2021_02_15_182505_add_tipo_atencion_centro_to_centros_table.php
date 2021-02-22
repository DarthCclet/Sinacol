<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoAtencionCentroToCentrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('centros', function (Blueprint $table) {
            $table->dropColumn('atiende_virtual');
            $table->unsignedBigInteger('tipo_atencion_centro_id')->default(2)->comment('FK a catálogo ocupaciones');
            $table->foreign('tipo_atencion_centro_id')->references('id')->on('tipo_atencion_centros');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('centros', function (Blueprint $table) {
            $table->dropForeign('tipo_atencion_centro_id');
            $table->dropColumn('tipo_atencion_centro_id');
            $table->boolean('atiende_virtual')->default(false)->comment("Indicador de que el centro atiende casos virtuales");
        });
    }
}
