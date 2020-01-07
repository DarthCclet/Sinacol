<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotivoSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motivo_solicitudes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->softDeletes();
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/motivo_solicitudes.json"));

        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $motivo_solicitud){
            DB::table('motivo_solicitudes')->insert(
                [
                    'id' => $motivo_solicitud->id,
                    'nombre' => $motivo_solicitud->nombre
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
        Schema::dropIfExists('motivo_solicitudes');
    }
}
