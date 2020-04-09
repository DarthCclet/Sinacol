<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcupacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ocupaciones', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK del catálogo de ocupaciones laborales');
            $table->string('nombre')->comment('Nombre de la ocupacion');
            $table->decimal('salario_zona_libre', 8, 2)->comment('Salario minimo en zona libre de la frontera norte');
            $table->decimal('salario_resto_del_pais', 8, 2)->comment('Salario minimo en el resto del pais');
            $table->dateTime('vigencia_de')->comment('Indica la fecha de inicio de vigencia');
            $table->dateTime('vigencia_a')->nullable()->comment('Indica la fecha de fin de vigencia');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });

        $tabla_nombre = 'ocupaciones';
        $comentario_tabla = 'Tabla donde se almacena el catálogo de ocupaciones laborales.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");

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
                DB::table('ocupaciones')->insert(
                    [
                        'nombre' => $ocupacion[0],
                        'salario_zona_libre'=> $ocupacion[1],
                        'salario_resto_del_pais' => $ocupacion[2],
                        'vigencia_de' => '2020-01-01'
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
        Schema::dropIfExists('ocupaciones');
    }
}
