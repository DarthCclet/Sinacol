<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNombreComercialToPartesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partes', function (Blueprint $table) {
            $table->text('nombre_comercial')->nullable()->comment('Nombre de persona moral')->change();
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
            $table->string('nombre_comercial')->nullable()->comment('Nombre de persona moral')->change();
        });
    }
}
