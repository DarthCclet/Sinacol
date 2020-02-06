<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesConciliadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles_conciliador', function (Blueprint $table) {
            // llave primaria
            $table->bigIncrements('id')->comment('Pk de la tabla roles_conciliador');
            // id de la persona con rol de conciliador 
            $table->integer('conciliador_id')->comment('FK de la tabla personas');
            $table->foreign('conciliador_id')->references('id')->on('conciliadores');
            // id del centro asignado
            $table->integer('rol_atencion_id')->comment('FK de la tabla roles_atencion');
            $table->foreign('rol_atencion_id')->references('id')->on('roles_atencion');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra logicamente.');
            $table->timestamps();
        });
        $tabla_nombre = 'roles_conciliador';
        $comentario_tabla = 'Tabla donde se almacenan los roles para cada conciliadores.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles_conciliador');
    }
}
