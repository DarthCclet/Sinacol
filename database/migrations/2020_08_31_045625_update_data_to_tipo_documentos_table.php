<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateDataToTipoDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('tipo_documentos')->truncate();
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/tipo_documentos.json"));
        //Se llena el catalogo desde el arvhivo json concepto_pago_resoluciones.json
        foreach ($json->datos as $objeto){
            DB::table('tipo_documentos')->insert([
                'nombre' => $objeto->nombre,
                'objetos' => $objeto->nombre,
                'created_at' => date("Y-m-d H:d:s"),
                'updated_at' => date("Y-m-d H:d:s"),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tipo_documentos', function (Blueprint $table) {
            //
        });
    }
}
