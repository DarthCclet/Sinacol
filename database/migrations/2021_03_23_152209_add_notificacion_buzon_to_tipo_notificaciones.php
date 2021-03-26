<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\TipoNotificacion;

class AddNotificacionBuzonToTipoNotificaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tipo = TipoNotificacion::where("nombre" , "D) Notificado por buzón electrónico")->first();
        if($tipo == null){
            TipoNotificacion::create(["nombre" => "D) Notificado por buzón electrónico"]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tipo_notificaciones', function (Blueprint $table) {
            $tipo = TipoNotificacion::where("nombre" , "D) Notificado por buzón electrónico")->first();
            if($tipo != null){
                $tipo->delete();
            }
        });
    }
}
