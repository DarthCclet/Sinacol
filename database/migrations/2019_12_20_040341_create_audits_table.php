<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK de los registros de auditoría');
            $table->string('user_type')->nullable()->comment('Tipo de usuario que realiza la acción');
            $table->unsignedBigInteger('user_id')->nullable()->comment('FK ID del usuario que realiza el movimiento');
            $table->string('event')->comment('Evento o acción realizada');
            $table->morphs('auditable');
            $table->text('old_values')->nullable()->comment('Valor anterior al movimiento');
            $table->text('new_values')->nullable()->comment('Nuevo valor, después del movimiento');
            $table->text('url')->nullable()->comment('URL que ejecuta el movimiento');
            $table->ipAddress('ip_address')->nullable()->comment('IP de la máquina cliente que realiza el movimiento');
            $table->string('user_agent', 1023)->nullable()->comment('Navegador con el que se realiza el movimiento');
            $table->string('tags')->nullable()->comment('Etiqueta para agrupar movimientos');
            $table->timestamps();

            $table->index(['user_id', 'user_type']);
        });

        $tabla_nombre = 'audits';
        $comentario_tabla = 'Tabla donde se almacenan todas las acciones auditables.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('audits');
    }
}
