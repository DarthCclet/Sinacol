<?php

namespace Tests\Unit;

use App\Centro;
use App\Services\ContadorServiceFederal;
use App\Services\ContadorService;
use App\TipoContador;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContadorServiceTest extends \Tests\TestCase
{
    use DatabaseTransactions;

    protected $contadorService;

    public function setUp() : void
    {
        parent::setUp();
        $this->contadorService = app(ContadorServiceFederal::class);
    }
    /**
     * @test
     *
     * @return void
     */
    public function debe_crear_un_contador_para_un_anio_dado_no_existente()
    {
        $tipo = TipoContador::first();
        $centro = Centro::first();
        $anio = 1970;
        // Para un contador de un año que no existe
        $this->assertDatabaseMissing('contadores',['anio'=>$anio, 'tipo_contador_id'=>$tipo->id, 'centro_id'=> $centro->id]);

        $contador = $this->contadorService->getContador($anio, $tipo->id, $centro->id);
        $this->assertEquals($contador, 1);
        $this->assertDatabaseHas('contadores',['anio'=>$anio, 'tipo_contador_id'=>$tipo->id, 'centro_id'=>$centro->id]);

        // Para un segundo llamado debe regresar 2
        $contador = $this->contadorService->getContador(1970, $tipo->id, $centro->id);
        $this->assertEquals($contador, 2);
        $this->assertDatabaseHas('contadores',['anio'=>$anio, 'tipo_contador_id'=>$tipo->id, 'centro_id'=>$centro->id, 'contador'=>2]);
    }

}
