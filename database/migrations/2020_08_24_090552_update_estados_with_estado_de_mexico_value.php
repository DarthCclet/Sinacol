<?php

use App\Estado;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEstadosWithEstadoDeMexicoValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $estado = Estado::where('id', '15')->first();
        if($estado){
            $estado->nombre = 'Estado de México';
            $estado->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $estado = Estado::where('id', '15')->first();
        if($estado){
            $estado->nombre = 'México';
            $estado->save();
        }
    }
}
