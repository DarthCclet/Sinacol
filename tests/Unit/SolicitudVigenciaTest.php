<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\DiasVigenciaSolicitudService;
use App\Solicitud;
use Illuminate\Foundation\Testing\DatabaseTransactions;


/**
 * Proporciona los metodos para validar la vigencia de solicitudes
 * Class SolicitudVigenciaTest
 * @package tests
 */
class SolicitudVigenciaTest extends TestCase
{
    use DatabaseTransactions;

    const TIPO_SOLICITUD_TRABAJADOR = 1;
    const CONTADOR = 1;

    protected $dias_solicitud;
    protected $contador_service;

    /**
     * Funcion para inicializar la clase
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->dias_solicitud = app(DiasVigenciaSolicitudService::class);
    }
    /**
     * Test para validar si se solicita una acción antes de 46 dias
     * @return void
     */
    public function testVerifyVigenciaSucess()
    {
        $anio = date("Y");
        $solicitud = factory(Solicitud::class)->create([
            'tipo_solicitud_id' => self::TIPO_SOLICITUD_TRABAJADOR,
            'folio' => self::CONTADOR,
            'anio' => $anio,
        ]);
        $fecha = new \Carbon\Carbon($solicitud->created_at);
        $fecha_nueva = $fecha->addDays(env("DIAS_VIGENCIA_SOLICITUD_FEDERAL",45));
        $validacion = $this->dias_solicitud->getSolicitudVigente($solicitud->id, $fecha_nueva->format('Y-m-d'));
        $this->assertTrue($validacion);
    }

    /**
     * Test para validar si se solicita una acción despues de 45 dias
     * @return void
     */
    public function testVerifyVigenciaError()
    {
        $anio = date("Y");
        $solicitud = factory(Solicitud::class)->create([
            'tipo_solicitud_id' => self::TIPO_SOLICITUD_TRABAJADOR,
            'folio' => self::CONTADOR,
            'anio' => $anio,
        ]);
        $fecha = new \Carbon\Carbon($solicitud->created_at);
        $fecha_nueva = $fecha->addDays(env("DIAS_VIGENCIA_SOLICITUD_FEDERAL",45) + 2);
        $validacion = $this->dias_solicitud->getSolicitudVigente($solicitud->id, $fecha_nueva->format('Y-m-d'));
        $this->assertFalse($validacion);
    }
}
