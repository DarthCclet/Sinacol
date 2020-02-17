<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('centros', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK de la tabla estatus_solicitudes');
            $table->string('nombre')->comment('Nombre del centro de trabajo');
            $table->time('duracionAudiencia')->comment('Tiempo promedio que dura una audiencia en el centro');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
        $tabla_nombre = 'centros';
        $comentario_tabla = 'Tabla donde se almacenan los centros de conciliacion.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
        // $path = base_path('database/datafiles');
        // $json = json_decode(file_get_contents($path . "/centros.json"));
        //
        // //Se llena el catalogo desde el arvhivo json generos.json
        // foreach ($json->datos as $centro){
        //     DB::table('centros')->insert(
        //         [
        //             'id' => $centro->id,
        //             'nombre' => $centro->nombre
        //         ]
        //     );
        // }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('centros');
    }
}
