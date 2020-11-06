<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class AddPerrmissionCalendarioToAdministrador extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rol = Role::where("name","Administrador del centro")->first();
        $rol->givePermissionTo(["Calendario de audiencias","Calendario colectivo"]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $rol = Role::where("name","Administrador del centro")->first();
        $rol->revokePermissionTo(["Calendario de audiencias"," Calendario colectivo"]);
    }
}
