<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\RolAtencion;

class InsertRolConciliadorApoyoToRolesAtencionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user = RolAtencion::where('nombre', 'Conciliador de Apoyo')->firstOrCreate(
          ['id' =>  4],
          ['nombre' => 'Conciliador de Apoyo']
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        RolAtencion::where('nombre', 'Conciliador de Apoyo')->delete();
    }
}
