<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiroComercialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('giro_comerciales', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK del catálogo de giro comercial');
            $table->string('nombre')->comment('Nombre del giro comercial');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/giro_comerciales.json"));
        //Se llena el catalogo desde el arvhivo json giro_comerciales.json
        foreach ($json->datos as $giro_comerciales){
            DB::table('giro_comerciales')->insert(
                [
                    'id' => $giro_comerciales->id,
                    'nombre' => $giro_comerciales->nombre
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
        Schema::dropIfExists('giro_comerciales');
    }
}
