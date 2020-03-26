<?php

namespace Tests\Unit;

use App\Expediente;
use App\Resolucion;
use App\Services\ConsultaConciliacionesPorExpediente;
use PHPUnit\Framework\TestCase;

class ConsultaConciliacionesPorExpedienteTest extends TestCase
{
    /**
     * @test void
     */
    public function consultaConciliacionesPorExpediente()
    {

        $consulta = new ConsultaConciliacionesPorExpediente();
        $expediente = "NAY/CJ/I/2020/5286s38";
        $tipoResolucion = 3;
        $resultado = $consulta->consulta($expediente, $tipoResolucion);

        $this->assertIsArray($resultado);
    }
}
