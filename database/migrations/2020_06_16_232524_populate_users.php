<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;

class PopulateUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Obtenemos el Rol de superUsuario
        $rol = Role::findById(1);
//        Obtenemos todos los permisos
        $permissions = Permission::all();
//        Recorremos los permisos
        foreach($permissions as $permission){
            $rol->givePermissionTo($permission->name);
        }
        DB::table('users')->truncate();
        $persona = factory(App\Persona::class)->states('admin')->create();
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin.conciliacion@stps.gob.mx',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'persona_id' => $persona->id
        ]);
        $user->assignRole($rol->name);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('users')->truncate();
    }
}
