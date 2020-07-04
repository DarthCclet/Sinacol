<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAsentamientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asentamientos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('cp',5);
            $table->string('asentamiento');
            $table->string('tipo_asentamiento')->nullable();
            $table->string('municipio')->nullable();
            $table->string('estado')->nullable();
        });
        $path_archivo = base_path('database/datafiles/asentamientos.cpy');
        DB::statement("COPY asentamientos FROM '$path_archivo'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asentamientos');
    }
}
