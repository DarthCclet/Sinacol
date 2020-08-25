<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTipoArchivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_archivos', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK: Llave primaria');
            $table->string('nombre')->comment('Nombre del tipo archivo');
            $table->softDeletes()->comment('Indica la fecha y hora en que fue borrado lóigcamente un registro');
            $table->timestamps();
        });

        $tabla_nombre = 'tipo_archivos';
        $comentario_tabla = 'Catálogo de tipo de archivos o documentos del proceso de conciliacion';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");

        collect([
            ['nombre'=>'Identificación oficial'],
            ['nombre'=>'Documento de personalidad'],
            ['nombre'=>'Comprobante de pago'],
            ['nombre'=>'Documentos emitidos por el sistema'],
            ['nombre'=>'Documentos emitidos por el sistema con firma autógrafa'],
            ['nombre'=>'Oficio'],
            ['nombre'=>'Justificante médico'],
            ['nombre'=>'Otro'],
        ])->each(function ($item){
            \App\TipoArchivo::create($item);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_archivos');
    }
}
