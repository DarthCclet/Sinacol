<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoPersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_personas', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('Llave primaria del catálogo de tipo de personas');
            $table->string('nombre')->comment('Nombre del tipo de persona');
            $table->string('abreviatura')->comment('Abreviatura del nombre de tipo de persona');
            $table->softDeletes()->comment('Indica la fecha y hora en que fue borrado lóigcamente un registro');
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/tipo_personas.json"));

        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $tipo_personas){
            DB::table('tipo_personas')->insert(
                [
                    'id' => $tipo_personas->id,
                    'nombre' => $tipo_personas->nombre,
                    'abreviatura' => $tipo_personas->abreviatura
                ]
            );
        }

        $tabla_nombre = 'tipo_personas';
        $comentario_tabla = 'Tabla donde se almacenan el catalogo de tipo de personas.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_personas');
    }
}
