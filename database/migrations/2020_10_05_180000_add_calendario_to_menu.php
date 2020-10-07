<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AddCalendarioToMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->insert(
            [
                'id' => 37,
                'name' => 'Calendario colectivo',
                'description' => 'Calendario que contiene las audiencias colectivas',
                'ruta' => "/calendariocolectivas",
                'padre_id' => 4,
                'guard_name' => 'web',
                'created_at' => date("Y-m-d H:d:s"),
                'updated_at' => date("Y-m-d H:d:s")
            ]
        );
        $rolPersonal = Role::findById(1);
        $rolSupervisor = Role::findById(4);
        $rolPersonal->givePermissionTo("Calendario colectivo");
        $rolSupervisor->givePermissionTo("Calendario colectivo");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $rolPersonal = Role::findById(1);
        $rolSupervisor = Role::findById(4);
        $rolPersonal->revokePermissionTo("Calendario colectivo");
        $rolSupervisor->revokePermissionTo("Calendario colectivo");
        DB::table('permissions')->where("id",37)->delete();
    }
}
