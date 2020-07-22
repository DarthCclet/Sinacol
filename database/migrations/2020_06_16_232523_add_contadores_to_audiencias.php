<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContadoresToAudiencias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audiencias', function (Blueprint $table) {
            $table->integer('folio')->comment('Folio de la solitud, se usa con el anio');
            $table->integer('anio')->comment('Anio de la solitud, se usa con el folio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audiencias', function (Blueprint $table) {
            $table->dropColumn('folio');
            $table->dropColumn('anio');
        });
    }
}
