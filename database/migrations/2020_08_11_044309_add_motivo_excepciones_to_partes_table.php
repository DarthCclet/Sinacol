<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMotivoExcepcionesToPartesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partes', function (Blueprint $table) {
            $table->unsignedBigInteger('motivo_excepciones_id')->nullable()->comment('Llave foránea que relaciona con motivo excepciones');
            $table->foreign('motivo_excepciones_id')->references('id')->on('motivo_excepciones');
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
