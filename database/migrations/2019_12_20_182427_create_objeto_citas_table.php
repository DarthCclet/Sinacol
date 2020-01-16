<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjetoCitasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('objeto_citas', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK del catálogo de objeto citas');
            $table->string('nombre')->comment('Nombre del objeto de cita');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/objeto_citas.json"));
        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $objeto_citas){
            DB::table('objeto_citas')->insert(
                [
                    'id' => $objeto_citas->id,
                    'nombre' => $objeto_citas->nombre
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
        Schema::dropIfExists('objeto_citas');
    }
}
