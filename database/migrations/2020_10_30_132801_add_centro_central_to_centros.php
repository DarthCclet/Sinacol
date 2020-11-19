<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Centro;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;

class AddCentroCentralToCentros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Agregamos el campo a la tabla centro que será el indicador del centro central
        Schema::table('centros', function (Blueprint $table) {
            $table->boolean("central")->default(false);
        });
//        Creamos el centro central
        $centro = Centro::create([
            "nombre" => "Oficina Central del CFCRL",
            "duracionAudiencia" => "01:00:00",
            "abreviatura" => "OCCFCRL",
            "central" => true
        ]);
//        Creamos el Rol  del orientador central
        $rol = Role::create([
            "name" => "Orientador Central",
            "guard_name" => "web",
            "description" => "Riene acceso al registro de solicitudes centrales"
        ]);
        $persona = factory(App\Persona::class)->states('orientador')->create();
        DB::table('users')->insert(
            [
                'name' => 'Orientador central',
                'email' => 'orientador.central@centrolaboral.gob.mx',
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'persona_id' => $persona->id,
                'centro_id' => $centro->id,
                'created_at' => now(),
                'created_at' => now()
            ]
        );
        $rol->givePermissionTo("Solicitudes");
        DB::table('users')
                ->where("email","orientador.central@centrolaboral.gob.mx")
                ->update(["persona_id" => $persona->id,"centro_id" => $centro->id]);
        $user = User::where("email","orientador.central@centrolaboral.gob.mx")->first();
        $user->assignRole($rol->name);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('centros', function (Blueprint $table) {
            $table->dropColumn("central");
        });
        
    }
}
