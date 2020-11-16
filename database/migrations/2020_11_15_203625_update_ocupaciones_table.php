<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateOcupacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $path = base_path('database/datafiles');

        if (($h = fopen($path."/ocupaciones.csv", "r")) !== FALSE)
        {
            $c = 0;
            while (($ocupacion = fgetcsv($h, 1000, "|")) !== FALSE)
            {
                $c++;
                if ($c == 1) {
                    continue;
                }
                DB::table('ocupaciones')->update(
                    [
                        'nombre' => $ocupacion[0],
                        'salario_zona_libre'=> $ocupacion[1],
                        'salario_resto_del_pais' => $ocupacion[2],
                    ]
                );
            }
            fclose($h);
        }
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
}
