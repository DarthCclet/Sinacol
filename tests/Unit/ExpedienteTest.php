<?php

namespace Tests\Unit;

use Tests\TestCase;

class ExpedienteTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }
    /**
     * test para validar relaciones en el modelo Expediente
     * @return asserts
     */
    public function testVerifyRelationSolicitud(){
        $expediente =  factory(\App\Expediente::class)->create();
        $this->assertInstanceOf('\App\Solicitud',$expediente->solicitud);

    }
}
