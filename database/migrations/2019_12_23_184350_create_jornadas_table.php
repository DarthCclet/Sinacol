<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJornadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jornadas', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('PK del catálogo de jornadas');
            $table->string('nombre')->comment('Nombre de la jornada');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });

        $tabla_nombre = 'jornadas';
        $comentario_tabla = 'Tabla donde se almacena el catálogo de tipos de jornadas laborales.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");

        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/jornadas.json"));
        //Se llena el catalogo desde el arvhivo json jornadas.json
        foreach ($json->datos as $jornadas){
            DB::table('jornadas')->insert(
                [
                    'id' => $jornadas->id,
                    'nombre' => $jornadas->nombre
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
        Schema::dropIfExists('jornadas');
    }
}
