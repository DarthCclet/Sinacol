<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\Console\Output\ConsoleOutput;

class AddRoleObservadorEstadisticaToRolesTable extends Migration
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
        $rol = Role::whereName('Observador estadístico')->first();
        $permiso = Permission::whereName('Reporteador')->first();
        if(!$rol){
            $rol = Role::create([
                'name' => 'Observador estadístico',
                'guard_name' => 'web',
                'description' => 'Genera reportes y realiza extracciones de datos del sistema'
             ]);
            if($permiso) {
                $rol->givePermissionTo('Reporteador');
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $rol = Role::whereName('Observador estadístico')->first();
        if($rol){
            try {
                $rol->revokePermissionTo('Reporteador');
                $rol->delete();
            } catch (Exception $e) {
                $this->console->writeln('<error>Error. Se produjo un error al eliminar el Rol: Observador estadístico, favor de corregir manualmente como proceda: '.$e->getMessage().'</error>');
            }
        }
    }
}
