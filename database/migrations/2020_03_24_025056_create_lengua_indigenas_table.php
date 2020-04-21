<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLenguaIndigenasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lengua_indigenas', function (Blueprint $table) {
            $table->integer('id')->primary()->comment('Llave primaria del registro');
            $table->string('nombre')->comment('Nombre de lengua indigena');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/lenguas_indigenas.json"));

        foreach ($json->datos as $vialidad){
            DB::table('lengua_indigenas')->insert(
                [
                    'id' => $vialidad->id,
                    'nombre' => $vialidad->nombre,
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
        Schema::dropIfExists('lengua_indigenas');
    }
}
