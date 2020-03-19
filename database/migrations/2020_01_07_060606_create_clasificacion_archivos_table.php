<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClasificacionArchivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clasificacion_archivos', function (Blueprint $table) {
            $table->increments('id')->comment('PK: Llave primaria');
            $table->string('nombre')->comment('Nombre de la clasifiación');
            $table->timestamps();
        });

        $tabla_nombre = 'clasificacion_archivos';
        $comentario_tabla = 'Catálogo de clasificación de archivos o documentos';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");

        collect([
            ['nombre'=>'Notificación'],
            ['nombre'=>'Intercambio CJF'],
            ['nombre'=>'Constancia'],
        ])->each(function ($item){
            \App\ClasificacionArchivo::create($item);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clasificacion_archivos');
    }
}
