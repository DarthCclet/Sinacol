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
            $table->bigIncrements('id')->comment('PK de la tabla estatus_solicitudes');
            $table->string('nombre')->comment('Nombre del Estatus en el que se encuentra la solicitud de conciliacon');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
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
        $tabla_nombre = 'estatus_solicitudes';
        $comentario_tabla = 'Tabla donde se almacena el catalogo de los estatus de la solicitud.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
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
