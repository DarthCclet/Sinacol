<?php

use App\Solicitud;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateIncidenciaDefaultToSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $solicitudes = Solicitud::where('incidencia',null)->get();
        foreach($solicitudes as $solicitud){
            $solicitud->incidencia = false;
            $solicitud->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
