<?php

namespace Tests\Unit;

use Tests\TestCase;

class ContadorTest extends TestCase
{
    /**
     * test para provar las relaciones de las tablas centros y tipos contadores
     *
     * @return void
     */
    public function testVerifyRelationContadores(){
        $contador = factory(\App\Contador::class)->create();
        $this->assertInstanceOf('\App\Centro',$contador->centro);
        $this->assertInstanceOf('\App\TipoContador',$contador->tipoContador);
    }
}
