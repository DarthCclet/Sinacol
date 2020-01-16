<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('generos', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('PK del catálogo de géneros');
            $table->string('nombre')->comment('Nombre del género');
            $table->string('abreviatura')->comment('abreviatura del genero');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/generos.json"));
        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $genero){
            DB::table('generos')->insert(
                [
                    'id' => $genero->id,
                    'nombre' => $genero->nombre,
                    'abreviatura' => $genero->abreviatura,
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
        Schema::dropIfExists('generos');
    }
}
