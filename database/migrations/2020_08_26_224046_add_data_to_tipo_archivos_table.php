<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataToTipoArchivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        collect([
            ['nombre'=>'Poder'],
        ])->each(function ($item){
            \App\TipoArchivo::create($item);
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
