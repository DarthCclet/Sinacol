<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariosMinimosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salarios_minimos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('salario_minimo', 10, 2)->comment('Salario minimo');
            $table->decimal('salario_minimo_zona_libre', 10, 2)->comment('Salario minimo zona libre de la frontera norte');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salarios_minimos');
    }
}
