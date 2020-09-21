<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Centro;
use App\Sala;
use App\Disponibilidad;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use App\Conciliador;

class PopulateDisponibilidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //recorremos los centros
        DB::beginTransaction();
        DB::table('users')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('conciliadores')->truncate();
        $centros = Centro::all();
        foreach($centros as $centro){
            $centro->update(["duracionAudiencia"=>"01:00:00"]);
            //agregamos la disponibilidad del centro
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
//            creamos tres usuarios
            //Creamos al usuario orientador
            $persona1 = factory(App\Persona::class)->states('orientador')->create();
            $mail = "orientador.".mb_strtolower(str_replace(" ","",$centro->abreviatura))."@centrolaboral.gob.mx";
            
            DB::table('users')->insert(
                [
                    'name' => 'Orientador',
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
            $rolOrientador = Role::findById(2);
            $user1->assignRole($rolOrientador->name);
            //Creamos al usuario personal conciliador
            $persona2 = factory(App\Persona::class)->states('personal_conciliador')->create();
            $mailPersonal = "personal.conciliador.".mb_strtolower(str_replace(" ","",$centro->abreviatura))."@centrolaboral.gob.mx";
            DB::table('users')->insert(
                [
                    'name' => 'personal_conciliador',
                    'email' => $mailPersonal,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'remember_token' => Str::random(10),
                    'persona_id' => $persona2->id,
                    'centro_id' => $centro->id,
                    'created_at' => now(),
                    'created_at' => now()
                ]
            );
            $user2 = User::where("email",$mailPersonal)->first();
            $user2->update(["persona_id" => $persona2->id]);
            $rolPersonalConciliador = Role::find(3);
            $user2->assignRole($rolPersonalConciliador->name);
            $conciliadorPersonal = Conciliador::create([
                "persona_id" => $persona2->id,
                "centro_id" => $centro->id
            ]);
            self::agregarDisponibilidad($conciliadorPersonal);
            //Creamos al usuario personal conciliador
            $persona3 = factory(App\Persona::class)->states('supervisor_conciliacion')->create();
            $mailSupervisor = "supervisor.conciliacion.".mb_strtolower(str_replace(" ","",$centro->abreviatura))."@centrolaboral.gob.mx";
            DB::table('users')->insert(
                [
                    'name' => 'supervisor_conciliacion',
                    'email' => $mailSupervisor,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'remember_token' => Str::random(10),
                    'persona_id' => $persona3->id,
                    'centro_id' => $centro->id,
                    'created_at' => now(),
                    'created_at' => now()
                ]
            );
            $user3 = User::where("email",$mailSupervisor)->first();
            $user3->update(["persona_id" => $persona3->id]);
            $rolSupervisor = Role::findById(4);
            $user3->assignRole($rolSupervisor->name);
            $conciliadorSupervisor = Conciliador::create([
                "persona_id" => $persona3->id,
                "centro_id" => $centro->id
            ]);
            self::agregarDisponibilidad($conciliadorSupervisor);
        }
        $per = Permission::find(35);
        if($per == null){
            DB::table('permissions')->insert(
                [
                    'id' => 35,
                    'name' => 'Calendario de audiencias',
                    'description' => 'Calendario que contiene las audiencias a celebrar',
                    'ruta' => "/calendariocentro",
                    'padre_id' => 4,
                    'guard_name' => 'web',
                    'created_at' => date("Y-m-d H:d:s"),
                    'updated_at' => date("Y-m-d H:d:s")
                ]
            );
            DB::table('permissions')->insert(
                [
                    'id' => 36,
                    'name' => 'Agenda de conciliador',
                    'description' => 'Agenda que contiene las audiencias a celebrar de un conciliador',
                    'ruta' => "/agendaConciliador",
                    'padre_id' => 4,
                    'guard_name' => 'web',
                    'created_at' => date("Y-m-d H:d:s"),
                    'updated_at' => date("Y-m-d H:d:s")
                ]
            );
        }
        $rolPersonal = Role::findById(3);
        $rolSupervisor = Role::findById(4);
        $rolPersonal->givePermissionTo("Agenda de conciliador");
        $rolSupervisor->givePermissionTo("Agenda de conciliador");
        $rolSupervisor->givePermissionTo("Calendario de audiencias");
        DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('disponibilidades')->truncate();
        Sala::where("virtual",false)->delete();
        DB::table('users')->truncate();
        DB::table('conciliadores')->truncate();
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
