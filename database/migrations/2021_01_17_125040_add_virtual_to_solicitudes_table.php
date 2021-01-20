<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVirtualToSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->boolean('virtual')->default(false)->comment("Indicador de que es un proceso virtual");
            $table->string('canal')->nullable()->comment('Canal de comunicación para la audiencia virtua ');
            $table->string('url_virtual')->nullable()->comment('Url de meeting virtual');
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
            $table->dropColumn('virtual');
            $table->dropColumn('canal');
            $table->dropColumn('url_virtual');
        });
    }
}
