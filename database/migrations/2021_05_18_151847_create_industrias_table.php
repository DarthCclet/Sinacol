<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateIndustriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tabla = 'industrias';
        Schema::create($tabla, function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK del registro');
            $table->string('nombre', 512)->comment('Nombre de la industria');
            $table->timestamps();
        });

        DB::statement("COMMENT ON COLUMN $tabla.created_at IS 'Fecha y hora de creación del registro'");
        DB::statement("COMMENT ON COLUMN $tabla.created_at IS 'Fecha y hora de actualización del registro'");
        DB::statement("COMMENT ON TABLE $tabla IS 'Catálogo de industrias que maneja el Poder Judicial'");

        $file_handle = fopen(database_path('datafiles/CatIndustriasPoderJudicial.csv'), 'r');
        while (!feof($file_handle)) {
            $item = fgetcsv($file_handle, 0);
            if(is_array($item) && isset($item[0])){
                DB::table('industrias')->insert(['nombre'=>$item[0]]);
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
        Schema::dropIfExists('industrias');
    }
}
