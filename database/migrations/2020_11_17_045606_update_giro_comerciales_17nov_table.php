<?php

use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Output\ConsoleOutput;

class UpdateGiroComerciales17novTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $out = new ConsoleOutput();
        $path = base_path('database/datafiles');
        DB::table('giro_comerciales')->update(['ambito_id'=>1]);
        $workbook = SpreadsheetParser::open($path . "/ClasificacionSCIANporCompetencia_Resumen_V4.xlsx", 'xlsx');

        $totales = 0;
        foreach ($workbook->createRowIterator(0) as $rowIndex => $values) {
            //Saltamos las cabeceras del archivo que siempre deben estar en el rowindex=1
            if($rowIndex==1) continue;
            $totales++;
            $codigo = $values[0];
            $nombre = $values[1];
            $ambito = 1;
            if(strtolower($values[4]) == 'local'){
                $ambito = 2;
            }
            //Cambiar el ámbito a local, el id del ámbito local en el catálogo es = 2
            $giro = \App\GiroComercial::where('codigo', $codigo)->first();
            if($giro) {
                $id = $giro->id;
                $out->writeln('Local:'.$codigo." ID: ".$id." NOMBRE:".$nombre);
                \App\GiroComercial::CambiarAmbito($id, $ambito);
            }
            else{
                $d = \App\GiroComercial::create([
                    'codigo' => $codigo,
                    'nombre' => $nombre,
                    'ambito_id' => $ambito
               ]);

                $out->writeln('Nuevo!!:'.$codigo." NOMBRE: ".$nombre);
            }

        }//cierre del foreach

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
