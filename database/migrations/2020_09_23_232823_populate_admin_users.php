<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Centro;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PopulateAdminUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();
//        User::where("name","Admin")->forceDelete();
        $centros = Centro::all();
        foreach($centros as $centro){
            $persona1 = factory(App\Persona::class)->states('admin')->create();
            $mail = "admin.".mb_strtolower(str_replace(" ","",$centro->abreviatura))."@centrolaboral.gob.mx";
            
            DB::table('users')->insert(
                [
                    'name' => 'Admin',
                    'email' => $mail,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'remember_token' => Str::random(10),
                    'persona_id' => $persona1->id,
                    'centro_id' => $centro->id,
                    'created_at' => now(),
                    'created_at' => now()
                ]
            );
            $user1 = User::where("email",$mail)->first();
            $user1->update(["persona_id" => $persona1->id]);
            $rolAdmin = Role::findById(1);
            $rolAdmin->givePermissionTo("Agenda de conciliador");
            $rolAdmin->givePermissionTo("Calendario de audiencias");
            $rolPersonal = Role::findById(3);
            $rolSupervisor = Role::findById(4);
            $rolPersonal->givePermissionTo("Configuración de Centro");
            $rolSupervisor->givePermissionTo("Configuración de Centro");
            $user1->assignRole($rolAdmin->name);
        }
        DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $users = User::where("name","Admin")->forceDelete();
    }
}
