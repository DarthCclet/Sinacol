<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorarioInhabilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horarios_inhabiles', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Llave primaria de la tabla');
            $table->unsignedBigInteger('inhabilitable_id')->comment('llave foranea de las tablas inhabilitables');
            $table->string('inhabilitable_type')->comment('nombre del modelo de la tabla que inhabilitable');
            $table->time('hora_inicio')->comment('hora de inicio de la inhabilitación');
            $table->time('hora_fin')->comment('hora de termino de la inhabilitación');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['inhabilitable_type','inhabilitable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('horarios_inhabiles');
    }
}
