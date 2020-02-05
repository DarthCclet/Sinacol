<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjetoSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('objeto_solicitudes', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK de la tabla objeto_solicitudes');
            $table->string('nombre')->comment('Nombre del objeto por el cual se dio la solicitud de conciliacion ');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/objeto_solicitudes.json"));

        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $objeto_solicitud){
            DB::table('objeto_solicitudes')->insert(
                [
                    'id' => $objeto_solicitud->id,
                    'nombre' => $objeto_solicitud->nombre
                ]
            );
        }
        $tabla_nombre = 'objeto_solicitudes';
        $comentario_tabla = 'Tabla donde se almacena el catalogo de objeto por el que se genera la solicitud.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('objeto_solicitudes');
    }
}
