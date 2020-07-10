<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

class ConsultaAsentamientosTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function debe_traer_asentamientos_por_similitud()
    {
        //Nombre de colonia con error, debería traer todas las "del valle"
        $asentamiento = 'del vale';
        $minSimilitud = strlen($asentamiento) > 8 ? 0.8 : 0.5 ;
        $limite = 20;

        $colonias = DB::table('asentamientos')
            ->select(DB::raw("id,asentamiento,municipio,estado,cp,strict_word_similarity(unaccent('{$asentamiento}'), unaccent(asentamiento)) as similarity"))
            ->whereRaw(DB::raw("strict_word_similarity(unaccent('{$asentamiento}'), unaccent(asentamiento)) between {$minSimilitud} and 1"))
            ->orderByRaw(DB::raw("estado = 'CIUDAD DE MEXICO',similarity desc"))
            ->limit($limite)
            ->get();

        //dd($colonias);

        $resultado = $colonias->where('asentamiento','=','DEL VALLE CENTRO')->first();

        $this->assertEquals('DEL VALLE CENTRO', $resultado->asentamiento);
    }
}
