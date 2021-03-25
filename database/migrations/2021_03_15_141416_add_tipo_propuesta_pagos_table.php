<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTipoPropuestaPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_propuesta_pagos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre')->comment('Nombre del tipo de propuesta de pago ');
            $table->text('descripcion')->comment('Descripcion del tipo de propuesta de pago');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/tipo_propuesta_pagos.json"));
        foreach ($json->datos as $tipo_propuesta_pagos){
            DB::table('tipo_propuesta_pagos')->insert(
                [
                    'nombre' => $tipo_propuesta_pagos->nombre,
                    'descripcion' => $tipo_propuesta_pagos->descripcion,
                    'created_at' => date("Y-m-d H:d:s"),
                    'updated_at' => date("Y-m-d H:d:s")
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_propuesta_pagos');
    }
}
