<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class AddRolJefeDepartamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rol = Role::whereName("Jefe de departamento")->first();
        if($rol == null){
            $new_rol = Role::create(["name" => "Jefe de departamento","description" => "Responsabilidades de jefe de área"]);
//            Buscamos el rol de supervisor y sus permisos
            $supervisor = Role::whereName("Supervisor de conciliación")->first();
            if($supervisor != null){
                foreach($supervisor->permissions as $permiso){
                    $new_rol->givePermissionTo($permiso->name);
                }
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
        $rol = Role::whereName("Jefe de departamento")->first();
        if($rol != null){
            if($rol != null){
                foreach($rol->permissions as $permiso){
                    $rol->revokePermissionTo($permiso->name);
                }
            }
            $rol->delete();
        }
    }
}
