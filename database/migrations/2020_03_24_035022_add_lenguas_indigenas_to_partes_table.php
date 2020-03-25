<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLenguasIndigenasToPartesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partes', function (Blueprint $table) {
            $table->boolean('solicita_traductor')->nullable()->comment('Indica si la parte solicita traductor');
            $table->unsignedBigInteger('lengua_indigena_id')->nullable()->comment('FK de la tabla lengua indigena');
            $table->foreign('lengua_indigena_id')->references('id')->on('lengua_indigenas');
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
            //
        });
    }
}
