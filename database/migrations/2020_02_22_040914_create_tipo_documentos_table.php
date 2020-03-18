<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_documentos', function (Blueprint $table) {

            $table->bigInteger('id')->primary()->comment('Llave primaria del catálogo de tipo de documentos');
            $table->string('nombre')->comment('Nombre del tipo de documento');
            $table->string('objetos')->comment('objetos relacionados en el documento ');
            $table->softDeletes()->comment('Indica la fecha y hora en que fue borrado lóigcamente un registro');
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/tipo_documentos.json"));

        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $tipo_parte){
            DB::table('tipo_documentos')->insert(
                [
                    'id' => $tipo_parte->id,
                    'nombre' => $tipo_parte->nombre,
                    'objetos' => $tipo_parte->objetos
                ]
            );
        }

        $tabla_nombre = 'tipo_documentos';
        $comentario_tabla = 'Tabla donde se almacenan los tipos de documentos que existen.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_documentos');
    }
}
