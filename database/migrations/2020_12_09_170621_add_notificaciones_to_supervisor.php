<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AddNotificacionesToSupervisor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Creamos el permiso de notificaciones
        DB::table('permissions')->insert([
            'id' => 38,
            'name' => 'Notificaciones',
            'description' => 'Acceso a la lista de notificaciones',
            'ruta' => "/notificaciones",
            'padre_id' => 1,
            'guard_name' => 'web',
            'created_at' => date("Y-m-d H:d:s"),
            'updated_at' => date("Y-m-d H:d:s")
        ]);
        $permiso = Permission::where("name","Notificaciones")->first();
        //Agregamos el permiso al rol de supervisor y a root
        $rolS = Role::where("name","Supervisor de conciliación")->first();
        $rolA = Role::where("name","Administrador del centro")->first();
        $rolR = Role::where("name","Super Usuario")->first();
        $rolS->givePermissionTo($permiso->name);
        $rolA->givePermissionTo($permiso->name);
        $rolR->givePermissionTo($permiso->name);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permiso = Permission::where("name","Notificaciones")->first();
        //Agregamos el permiso al rol de supervisor y a root
        $rolS = Role::where("name","Supervisor de conciliación")->first();
        $rolA = Role::where("name","Administrador del centro")->first();
        $rolR = Role::where("name","Super Usuario")->first();
        $rolS->revokePermissionTo($permiso->name);
        $rolA->revokePermissionTo($permiso->name);
        $rolR->revokePermissionTo($permiso->name);
        $permiso->forceDelete();
    }
}
