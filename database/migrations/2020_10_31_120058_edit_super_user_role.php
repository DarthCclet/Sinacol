<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use App\User;
use App\Centro;
use App\Sala;

class EditSuperUserRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        // creamos el nuevo rol
        $rol = Role::create([
            "name" => "Administrador del centro",
            "guard_name" => "web",
            "description" => "Tiene acceso a toda la configuración del centro"
        ]);
//        Le asignamos los permisos al rol
        $permisosSupervisorConciliador=["Solicitudes","Audiencias","Configuración de Centro","Centros","Salas","Conciliadores","Administración","Usuarios"];
        foreach($permisosSupervisorConciliador as $permiso){
            $rol->givePermissionTo($permiso);
        }
        
        //obtenemos todos los super usuarios
        $rolEliminar = Role::find(1);
        // eliminamos todos los superusuarios
        foreach($rolEliminar->users as $user){
            $user->removeRole($rolEliminar->name);
            $user->assignRole($rol->name);
        }
        
        
        
//        Recorremos los centros para crear el usuario en cada uno
        $centro = Centro::where("nombre","Oficina Central del CFCRL")->first();
        $persona = factory(App\Persona::class)->states('admin')->create();
        $mail = "admin.".mb_strtolower(str_replace(" ","",$centro->abreviatura))."@centrolaboral.gob.mx";
        DB::table('users')->insert(
            [
                'name' => 'Administrador del centro',
                'email' => $mail,
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'persona_id' => $persona->id,
                'centro_id' => $centro->id,
                'created_at' => now(),
                'created_at' => now()
            ]
        );
        $user = User::where("email",$mail)->first();
        DB::table('users')
                ->where("email",$mail)
                ->update(["persona_id" => $persona->id,"centro_id" => $centro->id]);
        $user->assignRole($rol->name);
//      Creamos la disponibilidad del centro recien creado 
        if($centro->nombre == "Oficina Central del CFCRL"){
            self::agregarDisponibilidad($centro);
//            Creamos tres salas
            for($i=1;$i<=3;$i++){
                $nombre = $centro->abreviatura."-".$i;
                $sala = Sala::create([
                    "sala" => $nombre,
                    "virtual" => false,
                    "centro_id" => $centro->id
                ]);
                self::agregarDisponibilidad($sala);
            }
        }
//        Creamos el usuario super usuario
        $persona = factory(App\Persona::class)->states('root')->create();
        $mail = "root@centrolaboral.gob.mx";
        DB::table('users')->insert(
            [
                'name' => 'Super usuario',
                'email' => $mail,
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'persona_id' => $persona->id,
                'created_at' => now(),
                'created_at' => now()
            ]
        );
        $rolSuperUser = Role::find(1);
        $userSU = User::where("email",$mail)->first();
        DB::table('users')
                ->where("email",$mail)
                ->update(["persona_id" => $persona->id]);
        $userSU->update(["persona_id" => $persona->id]);
        $userSU->assignRole($rolSuperUser->name);        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
    public function agregarDisponibilidad($coleccion){
        for($j=1;$j<=5;$j++){
            $coleccion->disponibilidades()->create([
                "dia" => $j,
                "hora_inicio" => "09:00:00",
                "hora_fin" => "18:00:00",
            ]);
        }
    }
}
