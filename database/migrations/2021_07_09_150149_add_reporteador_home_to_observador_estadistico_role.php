<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Symfony\Component\Console\Output\ConsoleOutput;

class AddReporteadorHomeToObservadorEstadisticoRole extends Migration
{
    protected $console;

    public function __construct()
    {
        $this->console = new ConsoleOutput();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rol = Role::where('name', 'Observador estadístico')->first();
        if($rol){
            $rol->home = '/reportes';
            $rol->save();
        }
        else {
            $this->console->writeln('<error>Error. No existe el Rol: Observador estadístico, favor de corregir manualmente como proceda.</error>');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $rol = Role::where('name', 'Observador estadístico')->first();
        if($rol){
            $rol->home = '';
            $rol->save();
        }
        else {
            $this->console->writeln('<error>Error. No existe el Rol: Observador estadístico, favor de corregir manualmente como proceda.</error>');
        }
    }
}
