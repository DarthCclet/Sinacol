<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_solicitudes', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('Llave primaria del catálogo de tipo de solicitud');
            $table->string('nombre')->comment('Nombre del tipo de solicitud');
            $table->softDeletes()->comment('Indica la fecha y hora en que fue borrado lóigcamente un registro');
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/tipo_solicitudes.json"));

        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $tipo_solicitudes){
            DB::table('tipo_solicitudes')->insert(
                [
                    'id' => $tipo_solicitudes->id,
                    'nombre' => $tipo_solicitudes->nombre,
                    'created_at' => date("Y-m-d H:d:s"),
                    'updated_at' => date("Y-m-d H:d:s")
                ]
            );
        }

        $tabla_nombre = 'tipo_solicitudes';
        $comentario_tabla = 'Tabla donde se almacenan el catalogo de tipo de solicitudes.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_solicitudes');
    }
}
