<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoContadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_contadores', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('PK de la tabla tipo_contadores');
            $table->string('nombre')->comment('Nombre del tipo del contador  ');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro, modifica y se borra lógicamente.');
            $table->timestamps();
        });
        $tabla_nombre = 'tipo_contadores';
        $comentario_tabla = 'Tabla donde se almacenan los tipos de contadores que existen.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_contadores');
    }
}
