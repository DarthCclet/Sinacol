<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\Console\Output\ConsoleOutput;

class AddPermissionReportesToPermissionsTable extends Migration
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
        $administracion = Permission::whereName('Administración')->first();
        $reportes = Permission::whereName('Reporteador')->first();

        $rolSuperUsuario = Role::whereName('Super Usuario')->first();
        $rolAdminCentro = Role::whereName('Administrador del centro')->first();
        if(!$reportes)
        {
            Permission::create([
                'name' => 'Reporteador',
                'guard_name' => 'web',
                'description' => 'Reportes estadísticos',
                'padre_id' => $administracion->id,
                'ruta' => '/reportes'
           ]);
        }

        if($rolSuperUsuario) {
            $rolSuperUsuario->givePermissionTo('Reporteador');
        }
        if($rolAdminCentro) {
            $rolAdminCentro->givePermissionTo('Reporteador');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $reportes = Permission::whereName('Reporteador')->first();
        if($reportes)
        {
            try {
                $reportes->delete();
            } catch (Exception $e) {
                $this->console->writeln('<error>Error al ejecutar borrado de permiso Reporteador! Ejecutar manualmente lo que proceda:'
                                        .$e->getMessage().'</error>');
            }
        }
    }
}
