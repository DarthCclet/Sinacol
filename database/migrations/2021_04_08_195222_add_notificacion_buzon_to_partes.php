<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificacionBuzonToPartes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partes', function (Blueprint $table) {
            $table->boolean("notificacion_buzon")->default(false);
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
            $table->dropColumn("notificacion_buzon");
        });
    }
}
