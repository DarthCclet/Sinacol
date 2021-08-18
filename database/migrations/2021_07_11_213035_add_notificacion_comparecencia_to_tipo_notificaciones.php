<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificacionComparecenciaToTipoNotificaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        collect([
            ['nombre'=>'G) Notificado al comparecer'],
        ])->each(function ($item){
            $tipo = \App\TipoNotificacion::whereNombre($item["nombre"])->first();
            if($tipo == null) {
                \App\TipoNotificacion::create($item);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        collect([
            ['nombre'=>'G) Notificado al comparecer']
        ])->each(function ($item){
            $tipo = \App\TipoNotificacion::whereNombre($item["nombre"])->first();
            if($tipo != null) {
                $tipo->delete();
            }
        });
    }
}
