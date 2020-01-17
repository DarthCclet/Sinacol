<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePuestosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('puestos', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('PK del catálogo de puestos laborales');
            $table->string('nombre')->comment('Nombre del puesto laboral');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
        $tabla_nombre = 'puestos';
        $comentario_tabla = 'Tabla donde se almacena el catálogo de puestos laborales.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");

        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/puestos.json"));
        //Se llena el catalogo desde el arvhivo json giro_comerciales.json
        foreach ($json->datos as $puestos){
            DB::table('puestos')->insert(
                [
                    'id' => $puestos->id,
                    'nombre' => $puestos->nombre
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
        Schema::dropIfExists('puestos');
    }
}
