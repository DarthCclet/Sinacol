<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewDataToTipoArchivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipo_archivos', function (Blueprint $table) {
            collect([
                ['nombre'=>'representacion colectivo'],
            ])->each(function ($item){
                \App\TipoArchivo::create($item);
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tipo_archivos', function (Blueprint $table) {
            //
        });
    }
}
