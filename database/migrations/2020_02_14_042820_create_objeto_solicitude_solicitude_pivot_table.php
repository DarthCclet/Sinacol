<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateObjetoSolicitudeSolicitudePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('objeto_solicitud_solicitud', function (Blueprint $table) {
            $table->integer('objeto_solicitud_id')->unsigned()->index();
            $table->foreign('objeto_solicitud_id')->references('id')->on('objeto_solicitudes')->onDelete('cascade');
            $table->integer('solicitud_id')->unsigned()->index();
            $table->foreign('solicitud_id')->references('id')->on('solicitudes')->onDelete('cascade');
            $table->primary(['objeto_solicitud_id', 'solicitud_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('objeto_solicitud_solicitud');
    }
}
