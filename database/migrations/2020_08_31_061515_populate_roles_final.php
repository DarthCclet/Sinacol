<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;

class PopulateRolesFinal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('roles')->truncate();
        // Creamos los tres nuevos roles
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/roles.json"));

        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $rol){
            DB::table('roles')->insert(
                [
                    'name' => $rol->name,
                    'description' => $rol->description,
                    'guard_name' => 'web'
                ]
            );
        }        
        
        
        
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
        DB::table('users')->insert(
            [
                'name' => 'Admin',
                'email' => 'admin.conciliacion@stps.gob.mx',
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'persona_id' => $persona->id,
                'centro_id' => 1,
                'created_at' => now(),
                'created_at' => now()
            ]
        );
        $user = User::find(1);
        $user->assignRole($rol->name);
        
        
        
        $permisosOrientador=["Solicitudes"];
        $permisosPersonalConciliador=["Solicitudes","Audiencias"];
        $permisosSupervisorConciliador=["Solicitudes","Audiencias","Configuración de Centro","Centros","Salas","Conciliadores","Administración","Usuarios"];
        
        
        //Creamos al usuario orientador
        $persona = factory(App\Persona::class)->states('orientador')->create();
        DB::table('users')->insert(
            [
                'name' => 'Orientador',
                'email' => 'orientador@centrolaboral.gob.mx',
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'persona_id' => $persona->id,
                'centro_id' => 1,
                'created_at' => now(),
                'created_at' => now()
            ]
        );
        $rolOrientador = Role::findById(2);
        foreach($permisosOrientador as $permiso){
            $rolOrientador->givePermissionTo($permiso);
        }
        $user2 = User::find(2);
        $user2->assignRole($rolOrientador->name);
        
        //Creamos al usuario personal conciliador
        $persona = factory(App\Persona::class)->states('personal_conciliador')->create();
        DB::table('users')->insert(
            [
                'name' => 'personal_conciliador',
                'email' => 'personal.conciliador@centrolaboral.gob.mx',
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'persona_id' => $persona->id,
                'centro_id' => 1,
                'created_at' => now(),
                'created_at' => now()
            ]
        );
        $rolPersonalConciliador = Role::find(3);
        foreach($permisosPersonalConciliador as $permiso){
            $rolPersonalConciliador->givePermissionTo($permiso);
        }
        $user3 = User::find(3);
        $user3->assignRole($rolPersonalConciliador->name);
        
        //Creamos al usuario personal conciliador
        $persona = factory(App\Persona::class)->states('supervisor_conciliacion')->create();
        DB::table('users')->insert(
            [
                'name' => 'supervisor_conciliacion',
                'email' => 'supervisor.conciliacion@centrolaboral.gob.mx',
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'persona_id' => $persona->id,
                'centro_id' => 1,
                'created_at' => now(),
                'created_at' => now()
            ]
        );
        $rolSupervisor = Role::findById(4);
        foreach($permisosSupervisorConciliador as $permiso){
            $rolSupervisor->givePermissionTo($permiso);
        }
        $user4 = User::find(4);
        $user4->assignRole($rolSupervisor->name);
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
