<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtapaResolucionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etapa_resoluciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/etapa_resoluciones.json"));
        //Se llena el catalogo desde el arvhivo json etapa_resoluciones.json
        foreach ($json->datos as $vialidad){
            DB::table('etapa_resoluciones')->insert(
                [
                    'id' => $vialidad->id,
                    'nombre' => $vialidad->nombre,
                    'descripcion' => $vialidad->descripcion,
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
        Schema::dropIfExists('etapa_resoluciones');
    }
}
