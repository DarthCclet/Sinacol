<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTerminacionBilateralesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terminacion_bilaterales', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('PK de la tabla terminacion'); // descripcion de la resolucion
            $table->string('nombre')->comment('Descripcion de la terminacion');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra logicamente.');;
            $table->timestamps();
        });
        $tabla_nombre = 'terminacion_bilaterales';
        $comentario_tabla = 'Tabla que se almacenan las terminaciones posibles de las partes bilateralmente.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
        
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/terminacion_bilaterales.json"));

        foreach ($json->datos as $resolucion){
            DB::table('terminacion_bilaterales')->insert(
                [
                    'id' => $resolucion->id,
                    'nombre' => $resolucion->nombre,
                    'created_at' => date("Y-m-d H:d:s"),
                    'updated_at' => date("Y-m-d H:d:s")
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
        Schema::dropIfExists('terminacion_bilaterales');
    }
}
