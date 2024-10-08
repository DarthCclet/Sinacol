<?php

use App\TipoDocumento;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDocumentoToTipoDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipo_documentos', function (Blueprint $table) {
            $path = base_path('database/datafiles');
            $json = json_decode(file_get_contents($path . "/tipo_documentos.json"));
            //Se llena el catalogo desde el arvhivo json tipo_documentos.json
            foreach ($json->datos as $objeto){
                $tipoDoc = TipoDocumento::find($objeto->id);
                $maxId = $objeto->id;
                if($tipoDoc != null){
                    $tipoDoc->update(['nombre'=>$objeto->nombre,
                                    'objetos' => $objeto->objetos
                    ]);
                }else{
                    DB::table('tipo_documentos')->insert(
                        [
                            'id' => $objeto->id,
                            'nombre' => $objeto->nombre,
                            'objetos' => $objeto->objetos
                        ]
                    );
                }
            }
            $tipo_documentos = TipoDocumento::where('id','>',$maxId)->get();
            foreach ($tipo_documentos as $key => $objeto) {
                $objeto->delete();
            }
        });
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
