<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFailedJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK del registro');
            $table->text('connection')->comment('Datos de conexión de la cola de trabajo');
            $table->text('queue')->comment('Nombre de la cola de trabajo');
            $table->longText('payload')->comment('Datos y argumentos de la ejecución');
            $table->longText('exception')->comment('Detalle del error o problema');
            $table->timestamp('failed_at')->useCurrent();
        });

        $tabla_nombre = 'failed_jobs';
        $comentario_tabla = 'Almacena datos de procesos que han fallado. Se usa principalmente para colas de trabajo y web services';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('failed_jobs');
    }
}
