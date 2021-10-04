<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIndustriaIdToGirosComercialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('giro_comerciales', function (Blueprint $table) {
            $table->integer('industria_id')->nullable()->comment('FK, que apunta al registro del catálogo de industrias');
            $table->foreign('industria_id')->references('id')->on('industrias');
        });

        $file_handle = fopen(database_path('datafiles/GirosIndustrias.csv'), 'r');
        while (!feof($file_handle)) {
            $item = fgetcsv($file_handle, 0, '|');
            if(is_array($item)){
                list($codigo, $industria) = $item;
                $ind = \App\Industria::where('nombre', mb_strtoupper($industria))->first();
                if(!$ind) continue;
                $sian = \App\GiroComercial::where('codigo', $codigo)->first();
                if(!$sian) continue;
                $sian->industria_id = $ind->id;
                $sian->save();
            }
        }
        fclose($file_handle);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('giros_comerciales', function (Blueprint $table) {
            $table->dropColumn('industria_id');
        });
    }
}
