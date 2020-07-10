<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyMunicipiosToDomiciliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domicilios', function (Blueprint $table) {
            // Identificador de entidad federativa
            $table->unsignedBigInteger('municipio_id')->comment('Llave foránea que relaciona con el municipio');
            $table->foreign('municipio_id')->references('id')->on('municipios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domicilios', function (Blueprint $table) {
            $table->dropForeign('municipio_id');
            $table->dropColumn('municipio_id');
        });
    }
}
