<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AddPermissionsToRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $role = Role::where("name","Administrador del centro")->first();
        $roleSup = Role::where("name","Super Usuario")->first();
        
        $permission = Permission::create(["name" => "Eliminar documento","description" => "Modulo para eliminar documentos","ruta" => "/eliminar_documentos","padre_id" => 1]);
        $role->givePermissionTo($permission);
        $roleSup->givePermissionTo($permission);

        $permissionAud = Permission::create(["name" => "Eliminar audiencia","description" => "Modulo para eliminar audiencias","ruta" => "/eliminar_audiencias","padre_id" => 1]);
        $role->givePermissionTo($permissionAud);
        $roleSup->givePermissionTo($permissionAud);
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
