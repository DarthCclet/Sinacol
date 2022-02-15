<?php

namespace Tests\Unit;

use App\Exceptions\FolioExpedienteExistenteException;
use App\Expediente;
use App\Services\ContadorService;
use App\Services\FolioService;
use App\Solicitud;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\TestCase;

class FolioExpedienteTest extends TestCase
{
    use DatabaseTransactions;

    const TIPO_SOLICITUD_TRABAJADOR = 1;
    const TIPO_CONTADOR_SOLICITUD = 1;
    const TIPO_CONTADOR_EXPEDIENTE = 2;

    /**
     * @var FolioService
     */
    protected $folioService;

    /**
     * @var ContadorService
     */
    protected $contadorService;

    public function setUp(): void
    {
        parent::setUp();
        $this->folioService = app(FolioService::class);
        $this->contadorService = app(ContadorService::class);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     * @throws \App\Exceptions\CentroNoValidoException
     * @throws \App\Exceptions\TipoSolicitudNoValidaException
     * @test
     */
    public function no_debe_crear_expediente_si_ya_existe_folio()
    {
        $this->expectException(FolioExpedienteExistenteException::class);
        $anio = date("Y");

        $solicitud = factory(Solicitud::class)->create([
            'tipo_solicitud_id' => self::TIPO_SOLICITUD_TRABAJADOR,
            'folio' => $this->contadorService->getContador($anio, self::TIPO_CONTADOR_SOLICITUD, 1),
            'anio' => $anio
        ]);

        $solicitud->tipo_contador_id = self::TIPO_CONTADOR_EXPEDIENTE;
        list($consecutivo, $folio) = $this->folioService->getFolio($solicitud);

        $expediente = Expediente::create([
            "solicitud_id" => $solicitud->id, "folio" => $folio, "anio" => $anio, "consecutivo" => $consecutivo
        ]);

        $solicitud = factory(Solicitud::class)->create([
            'tipo_solicitud_id' => self::TIPO_SOLICITUD_TRABAJADOR,
            'folio' => $this->contadorService->getContador($anio, self::TIPO_CONTADOR_SOLICITUD, 1),
            'anio' => $anio
        ]);

        // Generamos otro expediente, pero con los mismos datos de folio para que emita la excepción esperada
        $expediente = Expediente::create([
            "solicitud_id" => $solicitud->id, "folio" => $folio, "anio" => $anio, "consecutivo" => $consecutivo
        ]);

    }
}
