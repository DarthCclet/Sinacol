<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoObjetoSolicitudesToObjetoSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('objeto_solicitudes', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_objeto_solicitudes_id')->nullable()->comment('FK de la tabla tipo objeto solicitudes');
            $table->foreign('tipo_objeto_solicitudes_id')->references('id')->on('tipo_objeto_solicitudes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('objeto_solicitudes', function (Blueprint $table) {
            $table->dropForeign(['tipo_objeto_solicitudes_id']);
            $table->dropColumn('tipo_objeto_solicitudes_id');
        });
    }
}
