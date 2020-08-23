<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPuestoToDatosLaboralesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datos_laborales', function (Blueprint $table) {
            $table->string('puesto')->nullable()->comment('Nombre del puesto laboral');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('datos_laborales', function (Blueprint $table) {
            $table->dropColumn('puesto');
        });
    }
}
