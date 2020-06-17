<?php

use App\Periodicidad;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiasToPeriodicidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('periodicidades', function (Blueprint $table) {
            $table->integer('dias')->nullable()->comment('Numero de dias que se van a pagar');
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/periodicidades.json"));
        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $periodicidad){
            $period = Periodicidad::find($periodicidad->id);
            $period->dias = $periodicidad->dias;
            $period->update();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('periodicidades', function (Blueprint $table) {
            $table->dropColumn('dias');
        });
    }
}
