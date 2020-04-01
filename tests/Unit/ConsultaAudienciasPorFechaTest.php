<?php

namespace Tests\Unit;

use App\Services\ConsultaConciliacionesPorRangoFechas;
use Tests\TestCase;
use Faker\Generator as Faker;

class ConsultaAudienciasPorFechaTest extends TestCase
{
    /**
     * Debe emitir excepción si las fechas no son válidas
     * @test
     * @return void
     * @throws \App\Exceptions\FechaInvalidaException
     */
    public function fechasNoValidas()
    {
        $this->expectException(\App\Exceptions\FechaInvalidaException::class);
        $consultas = new ConsultaConciliacionesPorRangoFechas();
        $consultas->validaFechas("algo-que-no-es-fecha");
        $consultas->validaFechas("2345-123");
        $consultas->consulta('2020-02-30', '2020-02-12');
    }

    /**
     * Debe aceptar fechas válidas
     * @test
     * @return void
     * @throws \App\Exceptions\FechaInvalidaException
     */
    public function fechasValidas()
    {
        $conciliaciones = new ConsultaConciliacionesPorRangoFechas();

        //Timestamp milisegundos
        $fecha_inicial = $conciliaciones->validaFechas("/Date(1574962552000-0600)/");

        //Timestamp segundos
        $fecha_final = $conciliaciones->validaFechas("/Date(1574962552-0600)/");

        $this->assertInstanceOf('DateTime', $fecha_inicial);
        $this->assertInstanceOf('DateTime', $fecha_final);
    }

}
