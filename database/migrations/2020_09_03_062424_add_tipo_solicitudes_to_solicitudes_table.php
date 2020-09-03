<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoSolicitudesToSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_solicitud_id')->nullable()->comment('FK de la tabla tipo_solicitudes');
            $table->foreign('tipo_solicitud_id')->references('id')->on('tipo_solicitudes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropForeign(['tipo_solicitud_id']);
            $table->dropColumn('tipo_solicitud_id');
        });
    }
}
