<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\RolAtencion;

class AddRoleConciliadorToConciliadores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Creamos el nuevo rol
        $rol = RolAtencion::create(["nombre" => "Conciliador virtual"]);
        $rolConciliador = RolAtencion::where("nombre","Conciliador en sala")->first();
        // Asignamos el rol de conciliador a todos los conciliadores actuales
        foreach(App\Conciliador::all() as $conciliador){
            if(count($conciliador->rolesConciliador) == 0){
                \App\RolConciliador::create([
                    "conciliador_id" => $conciliador->id,
                    "rol_atencion_id" => $rolConciliador->id
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $rolConciliador = RolAtencion::where("nombre","Conciliador virtual")->first()->delete();
    }
}