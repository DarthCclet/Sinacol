<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResolucionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("resoluciones",function(Blueprint $table){
            // llave primaria
            $table->bigInteger('id')->primary()->comment('PK de la tabla resoluciones');;
            // descripcion de la resolucion
            $table->string('nombre')->comment('Descripcion de la resolucion');;
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra logicamente.');;
            $table->timestamps();
        });
        $tabla_nombre = 'resoluciones';
        $comentario_tabla = 'Tabla que se almacenan las resoluciones posibles de las audiencias.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
        
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/resoluciones.json"));

        foreach ($json->datos as $resolucion){

            DB::table('resoluciones')->insert(
                [
                    'id' => $resolucion->id,
                    'nombre' => $resolucion->nombre
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
        Schema::dropIfExists('resoluciones');
    }
}
