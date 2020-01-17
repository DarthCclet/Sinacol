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
            $table->Integer('id')->primary()->comment('PK de la tabla motivo_solicitudes');
            $table->string('nombre')->comment('Nombre del Motivo por el cual se dio la solicitud de conciliacion ');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
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
        $tabla_nombre = 'motivo_solicitudes';
        $comentario_tabla = 'Tabla donde se almacena el catalogo de motivo por el que se genera la solicitud.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
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
