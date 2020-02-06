<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolConciliadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles_atencion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->softDeletes();
            $table->timestamps();
        });

        $tabla_nombre = 'roles_atencion';
        $comentario_tabla = 'Tabla donde se almacena el catálogo de roles para conciliadores.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");

        //$path = base_path('database/datafiles');
        //$json = json_decode(file_get_contents($path . "/rol_conciliadores.json"));
        //Se llena el catalogo desde el arvhivo json rol_conciliadores.json
//        foreach ($json->datos as $rol_conciliadores){
//            DB::table('roles_atencion')->insert(
//                [
//                    'id' => $rol_conciliadores->id,
//                    'nombre' => $rol_conciliadores->nombre
//                ]
//            );
//        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles_atencion');
    }
}
