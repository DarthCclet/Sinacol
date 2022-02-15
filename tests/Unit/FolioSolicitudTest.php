<?php

namespace Tests\Unit;


use App\Exceptions\FolioSolicitudExistenteException;
use App\Services\ContadorService;
use App\Solicitud;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\TestCase;

class FolioSolicitudTest extends TestCase
{
    use DatabaseTransactions;

    const TIPO_SOLICITUD_TRABAJADOR = 1;
    const TIPO_CONTADOR_SOLICITUD = 1;
    const CENTRO_ID_DEFAULT_CONTADOR_SOLICITUDES = 1;
    /**
     * @var ContadorService
     */
    protected $contadorService;

    public function setUp(): void
    {
        parent::setUp();
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
    public function no_debe_crear_solicitud_si_ya_existe_folio()
    {
        $this->expectException(FolioSolicitudExistenteException::class);

        $anio = date("Y");

        $folio = $this->contadorService->getContador($anio, self::TIPO_CONTADOR_SOLICITUD, self::CENTRO_ID_DEFAULT_CONTADOR_SOLICITUDES);

        $solicitud = factory(Solicitud::class)->create([
            'folio' => $folio,
            'anio' => $anio
        ]);

        // Aquí debe tirar la excepción dado que le pasamos el mismo folio
        $solicitud = factory(Solicitud::class)->create([
            'anio' => $anio,
            'folio' => $folio,
        ]);
    }
}
