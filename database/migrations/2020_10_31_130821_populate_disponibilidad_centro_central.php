<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Centro;
use App\Persona;
use App\User;
use App\Conciliador;
use Spatie\Permission\Models\Role;


class PopulateDisponibilidadCentroCentral extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $centro = Centro::where("nombre","Oficina Central del CFCRL")->first();
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
