<?php

namespace Tests\Unit;

use App\Centro;
use App\Services\FolioService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FolioServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var FolioService
     */
    protected $folioService;

    /**
     * @var
     */
    protected $contadorService;

    /**
     * Tipo de contador
     */
    const EXPEDIENTE = 2;

    const TRABAJADOR_INDIVIDUAL = 1;
    const PATRON_INDIVIDUAL = 2;
    const SINDICATO = 4;

    public function setUp(): void
    {
        parent::setUp();
        $this->folioService = app(FolioService::class);
    }

    /**
     * @test
     * @return void
     */
    public function debe_crear_un_folio_con_estructura_determinada()
    {
        $anio = 1973;
        $folio_esperado = 'MEX/CI/'.$anio.'/000001';
        $mex = Centro::whereAbreviatura('MEX')->first();

        $params = (object)[
            'centro_id' => $mex->id,
            'anio' => $anio,
            'tipo_contador_id' => self::EXPEDIENTE,
            'tipo_solicitud_id' => self::TRABAJADOR_INDIVIDUAL,
        ];

        list($consecutivo, $folio) = $this->folioService->getFolio($params);
        $this->assertEquals($folio_esperado, $folio);
        $this->assertEquals($consecutivo, 1);

        list($consecutivo, $folio) = $this->folioService->getFolio($params);
        $this->assertEquals('MEX/CI/'.$anio.'/000002', $folio);
        $this->assertEquals($consecutivo, 2);

        list($consecutivo, $folio) = $this->folioService->getFolio($params);
        $this->assertEquals('MEX/CI/'.$anio.'/000003', $folio);
        $this->assertEquals($consecutivo, 3);
    }

    /**
     * @test
     */
    public function debe_parar_el_cambio_cuando_hay_excepcion()
    {

        $anio = 1994;
        $folio_esperado = 'MEX/CI/'.$anio.'/000001';
        $mex = Centro::whereAbreviatura('MEX')->first();

        $params = (object)[
            'centro_id' => $mex->id,
            'anio' => $anio,
            'tipo_contador_id' => self::EXPEDIENTE,
            'tipo_solicitud_id' => self::TRABAJADOR_INDIVIDUAL,
        ];

        DB::beginTransaction();
        list($consecutivo, $folio) = $this->folioService->getFolio($params);
            try {

                list($consecutivo, $folio) = $this->folioService->getFolio($params);
                list($consecutivo, $folio) = $this->folioService->getFolio($params);
                list($consecutivo, $folio) = $this->folioService->getFolio($params);
                $this->assertEquals(4, $consecutivo);
                $this->assertEquals('MEX/CI/'.$anio.'/000004', $folio);
                $this->assertDatabaseHas('contadores', ['anio'=>$anio, 'contador'=>4, 'centro_id'=>$params->centro_id]);
                throw new \Exception("Excepción de prueba");
            }
            catch (\Exception $e) {
                DB::rollBack();
                dump($e->getMessage());
            }
        $this->assertDatabaseMissing('contadores', ['anio'=>$anio]);
    }

    /**
     * @test
     */
    public function debe_generar_un_folio_de_colectivo()
    {
        $anio = 1950;
        $folio_esperado = 'MEX/CC/'.$anio.'/000001';
        $mex = Centro::whereAbreviatura('MEX')->first();

        $params = (object)[
            'centro_id' => $mex->id,
            'anio' => $anio,
            'tipo_contador_id' => self::EXPEDIENTE,
            'tipo_solicitud_id' => self::SINDICATO,
        ];

        list($consecutivo, $folio) = $this->folioService->getFolio($params);

        $this->assertEquals($folio_esperado, $folio);
        $this->assertDatabaseHas('contadores', ['anio'=>$anio, 'contador'=>1, 'centro_id'=>$params->centro_id]);

    }

    /**
     * @test
     */
    public function debe_tirar_excepcion_si_no_existe_el_tipo_de_solicitud()
    {
        $this->expectException(\App\Exceptions\TipoSolicitudNoValidaException::class);
        $mex = Centro::whereAbreviatura('MEX')->first();
        $params = (object)[
            'centro_id' => $mex->id,
            'anio' => 2020,
            'tipo_contador_id' => self::EXPEDIENTE,
            'tipo_solicitud_id' => 123456689,
        ];

        list($consecutivo, $folio) = $this->folioService->getFolio($params);
    }

    /**
     * @test
     */
    public function debe_tirar_excepcion_si_no_existe_el_centro()
    {
        $this->expectException(\App\Exceptions\CentroNoValidoException::class);

        $params = (object)[
            'centro_id' => 12345,
            'anio' => 2020,
            'tipo_contador_id' => self::EXPEDIENTE,
            'tipo_solicitud_id' => self::TRABAJADOR_INDIVIDUAL,
        ];

        list($consecutivo, $folio) = $this->folioService->getFolio($params);
    }
}
