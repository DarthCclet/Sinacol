<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpedientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expedientes', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK de la tabla expedientes');
            $table->string('folio')->comment('Folio de identificacion del expediente');
            $table->string('anio')->comment('Anio del expediente');
            $table->string('consecutivo')->comment('Numero de idenficicacion del expediente');
            $table->unsignedBigInteger('solicitud_id')->comment('FK de la tabla solicitudes');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
        $tabla_nombre = 'expedientes';
        $comentario_tabla = 'Tabla donde se almacenan los expedientes que generan las solicitudes.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expedientes');
    }
}
