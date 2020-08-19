<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotivoExcepcionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motivo_excepciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/motivo_excepciones.json"));
        //Se llena el catalogo desde el arvhivo json etapa_resoluciones.json
        foreach ($json->datos as $motivo_excepcion){
            DB::table('motivo_excepciones')->insert(
                [
                    'id' => $motivo_excepcion->id,
                    'nombre' => $motivo_excepcion->nombre,
                    'descripcion' => $motivo_excepcion->descripcion,
                    'created_at' => date("Y-m-d H:d:s"),
                    'updated_at' => date("Y-m-d H:d:s")
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
        Schema::dropIfExists('motivo_excepciones');
    }
}
