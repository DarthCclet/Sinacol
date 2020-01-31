<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGruposPrioritariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupos_prioritarios', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK del catálogo de grupos prioritarios');
            $table->string('nombre')->comment('Nombre del grupo prioritarios');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/grupos_prioritarios.json"));
        //Se llena el catalogo desde el arvhivo json grupos_prioritarios.json
        foreach ($json->datos as $grupos_prioritarios){
            DB::table('grupos_prioritarios')->insert(
                [
                    'id' => $grupos_prioritarios->id,
                    'nombre' => $grupos_prioritarios->nombre
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
        Schema::dropIfExists('grupos_prioritarios');
    }
}
