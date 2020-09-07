<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGirosComercialesToSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->unsignedBigInteger('giro_comercial_id')->nullable()->comment('FK a catálogo de giros comerciales');
            $table->foreign('giro_comercial_id')->references('id')->on('giro_comerciales');
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
            $table->dropForeign(['giro_comercial_id']);
            $table->dropColumn('giro_comercial_id');
        });
    }
}
