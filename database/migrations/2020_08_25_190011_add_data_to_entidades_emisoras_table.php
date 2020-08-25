<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDataToEntidadesEmisorasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('entidades_emisoras', function (Blueprint $table) {
        //     //
        // });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/entidades_emisoras.json"));

        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $entidad){
            DB::table('entidades_emisoras')->insert(
                [
                    'nombre' => $entidad->nombre,
                    'descripcion' => $entidad->descripcion,
                    'abreviatura' => $entidad->abreviatura
                ]
            );
        }
        $tabla_nombre = 'entidades_emisoras';
        $comentario_tabla = 'Tabla donde se almacena el catalogo de las entidades emisoras de identificaciones y documentos.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entidades_emisoras', function (Blueprint $table) {
            //
        });
    }
}
