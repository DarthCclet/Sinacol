<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTipoObjetoSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_objeto_solicitudes', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK del catálogo de tipo objeto solicitud');
            $table->string('nombre')->comment('Nombre del tipo objeto solicitud');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
        $tabla_nombre = 'tipo_objeto_solicitudes';
        $comentario_tabla = 'Tabla donde se almacena el catálogo de tipo objeto solicitud de conciliación.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");

        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/tipo_objeto_solicitudes.json"));
        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $tipo_objeto_solicitudes){
            DB::table('tipo_objeto_solicitudes')->insert(
                [
                    'id' => $tipo_objeto_solicitudes->id,
                    'nombre' => $tipo_objeto_solicitudes->nombre
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
        Schema::dropIfExists('tipo_objeto_solicitudes');
    }
}
