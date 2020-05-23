<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos', function (Blueprint $table) {
            // llave del documento
            $table->bigIncrements('id')->comment('PK de la tabla documentos');

            // LLave foranea que apunta al objeto que se asigna el documento
            $table->bigInteger('documentable_id')->comment('FK que apunta al objeto que se asigna el documento');
            // Clase del objeto al que se está asignando el documento
            $table->string('documentable_type')->comment('Nombre de la clase del objeto al que se esta asignando el documento');

            // Nombre del documento
            $table->string('nombre')->nullable()->comment('Nombre del documento');

            //Nombre original del documento
            $table->string('nombre_original')->nullable()->comment('Nombre original del documento en el storage');

            // Descripción del documento
            $table->text('descripcion')->comment('descripción u observaciones del documento');

            // ruta de almacenamiento del archivo
            $table->string('ruta')->nullable()->comment('ruta donde se almacena el documento en storage interno');
            $table->string('tipo_almacen')->nullable()->comment('Tipo de almacén: S3, Azure, etc.');
            $table->string('uri')->nullable()->comment('URI del documento');
            $table->double('longitud')->nullable()->comment('Tamaño en bytes del archivo');
            $table->boolean('firmado')->nullable()->comment('Indica si el documento está firmado');
            $table->string('pkcs7base64')->nullable()->comment('Empaquetado de la firma');
            $table->integer('clasificacion_archivo_id')->nullable()->comment('FK que apunta al catálogo de clasificaciones de archivos');
            $table->foreign('clasificacion_archivo_id')->references('id')->on('clasificacion_archivos');

            $table->timestampTz('fecha_firmado')->nullable()->comment('Fecha de firmado');

            $table->index(['documentable_id', 'documentable_type']);
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra logicamente.');
            $table->timestamps();
        });
        $tabla_nombre = 'documentos';
        $comentario_tabla = 'Tabla donde se almacenan los metadatos de documentos del sistema.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentos');
    }
}
