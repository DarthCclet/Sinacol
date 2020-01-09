<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGruposVulnerablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupos_vulnerables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->softDeletes();
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/grupos_vulnerables.json"));
        //Se llena el catalogo desde el arvhivo json grupos_vulnerables.json
        foreach ($json->datos as $grupos_vulnerables){
            DB::table('grupos_vulnerables')->insert(
                [
                    'id' => $grupos_vulnerables->id,
                    'nombre' => $grupos_vulnerables->nombre
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
        Schema::dropIfExists('grupos_vulnerables');
    }
}
