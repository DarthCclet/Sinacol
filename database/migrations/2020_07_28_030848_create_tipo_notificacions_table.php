<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoNotificacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_notificaciones', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Llave primaria del catalogo');
            $table->string('nombre')->comment('Nombre del tipo de notificacion');
            $table->timestamps();
            $table->softDeletes()->comment('Campo para bajas logicas');
        });
        collect([
            ['nombre'=>'A) El solicitante entrega citatorio a solicitados'],
            ['nombre'=>'B) El actuario del centro entrega citatorio a solicitados'],
            ['nombre'=>'B) Agendar cita con actuario para entrega de citatorio'],
        ])->each(function ($item){
            \App\TipoNotificacion::create($item);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_notificaciones');
    }
}
