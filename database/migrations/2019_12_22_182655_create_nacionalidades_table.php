<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNacionalidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nacionalidades', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('PK de la tabla partes');
            $table->string('nombre')->comment('Nombre de la nacionalidad');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/nacionalidades.json"));

        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $nacionalidad){
            DB::table('nacionalidades')->insert(
                [
                    'id' => $nacionalidad->id,
                    'nombre' => $nacionalidad->nombre
                ]
            );
        }

        $tabla_nombre = 'nacionalidades';
        $comentario_tabla = 'Tabla donde se almacena el catalogo de nacionalidades.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nacionalidades');
    }
}
