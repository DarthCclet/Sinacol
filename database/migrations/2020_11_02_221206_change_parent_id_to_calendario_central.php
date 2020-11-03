<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use App\User;

class ChangeParentIdToCalendarioCentral extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //buscamos los permisos y les quitamos el padre
        $permiso = Permission::where("name","Agenda de conciliador")->first();
        $permiso->update(["padre_id" => null]);
        $permiso = Permission::where("name","Calendario colectivo")->first();
        $permiso->update(["padre_id" => null]);
        $permiso = Permission::where("name","Calendario de audiencias")->first();
        $permiso->update(["padre_id" => null]);
        //eliminamos el permiso de configuración del centro
        $permisoEliminar = Permission::where("name","Configuración de Centro")->first();
        $permisoEliminar->delete();
        // cambiamos los permisos de configuración del centro a administración
        $permisoAdministracion = Permission::where("name","Administración")->first();
        $permiso = Permission::where("name","Centros")->first();
        $permiso->update(["padre_id" => $permisoAdministracion->id]);
        $permiso = Permission::where("name","Salas")->first();
        $permiso->update(["padre_id" => $permisoAdministracion->id]);
        $permiso = Permission::where("name","Conciliadores")->first();
        $permiso->update(["padre_id" => $permisoAdministracion->id]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //buscamos el permiso
        $permisoEliminar = Permission::create([
            "name" => "Configuración de Centro",
            "guard_name" => "web",
            "description" => "Contiene las configuraciones del centro",
            "padre_id" => null,
            "ruta" => "#"
        ]);
        $permiso = Permission::where("name","Agenda de conciliador")->first();
        $permiso->update(["padre_id" => $permisoEliminar->id]);
        $permiso = Permission::where("name","Calendario colectivo")->first();
        $permiso->update(["padre_id" => $permisoEliminar->id]);
        $permiso = Permission::where("name","Calendario de audiencias")->first();
        $permiso->update(["padre_id" => $permisoEliminar->id]);
        $permiso = Permission::where("name","Centros")->first();
        $permiso->update(["padre_id" => $permisoEliminar->id]);
        $permiso = Permission::where("name","Salas")->first();
        $permiso->update(["padre_id" => $permisoEliminar->id]);
        $permiso = Permission::where("name","Conciliadores")->first();
        $permiso->update(["padre_id" => $permisoEliminar->id]);
    }
}
