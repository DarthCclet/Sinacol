<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstatusSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estatus_solicitudes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->softDeletes();
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/estatus_solicitudes.json"));

        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $estatus_solicitud){
            DB::table('estatus_solicitudes')->insert(
                [
                    'id' => $estatus_solicitud->id,
                    'nombre' => $estatus_solicitud->nombre
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estatus_solicitudes');
    }
}
