<?php

use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Output\ConsoleOutput;

class UpdateGiroComercialesSep29Table extends Migration
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
        $workbook = SpreadsheetParser::open($path . "/ClasificacionSCIANporCompetencia_Resumen_V3.xlsx", 'xlsx');

        $totales = 0;
        foreach ($workbook->createRowIterator(0) as $rowIndex => $values) {
            //Saltamos las cabeceras del archivo que siempre deben estar en el rowindex=1
            if($rowIndex==1) continue;
            $totales++;
            if(strtolower($values[4]) == 'local'){
                $codigo = $values[0];
                //Cambiar el ámbito a local, el id del ámbito local en el catálogo es = 2
                $id = \App\GiroComercial::where('codigo', $codigo)->first()->id;
                //$out->writeln('Local:'.$codigo." ID: ".$id);
                \App\GiroComercial::CambiarAmbito($id, 2);
            }
            else {
                //$out->writeln('ESTE ES FEDERAL:'.$values[0]);
            }

        }//cierre del foreach
        //exit;

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
