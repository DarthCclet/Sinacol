<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlantillasDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plantilla_documentos', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK de la tabla plantillas documentos');
            $table->string('nombre_plantilla')->comment('nombre de la plantilla');
            $table->string('descripcion')->nullable()->comment('Descripcion de la plantilla');
            $table->text('plantilla_header')->nullable()->comment('header');
            $table->text('plantilla_body')->comment('body');
            $table->text('plantilla_footer')->nullable()->comment('footer');

            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
        $tabla_nombre = 'plantilla_documentos';
        $comentario_tabla = 'Tabla donde se almacena las plantillas para los documentos.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plantilla_documentos');
    }
}
