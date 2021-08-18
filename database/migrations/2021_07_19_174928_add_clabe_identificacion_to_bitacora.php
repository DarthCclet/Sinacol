<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClabeIdentificacionToBitacora extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bitacora_buzones', function (Blueprint $table) {
            $table->string('clabe_identificacion')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bitacora_buzones', function (Blueprint $table) {
            $table->string('clabe_identificacion')->nullable()->change();
        });
    }
}
