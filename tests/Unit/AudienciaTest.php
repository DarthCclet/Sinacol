<?php

namespace Tests\Unit;

use Tests\TestCase;

class AudineciaTest extends TestCase
{
    /**
     * test para provar las relaciones de la tabla audiencias
     *
     * @return void
     */
    public function testVerifyRelationAudiencia(){
        $audiencia = factory(\App\Audiencia::class)->create();
        $this->assertInstanceOf('\App\Conciliador',$audiencia->conciliador);
        $this->assertInstanceOf('\App\Expediente',$audiencia->expediente);
        $this->assertInstanceOf('\App\Resolucion',$audiencia->resolucion);
        $this->assertInstanceOf('\App\Parte',$audiencia->parte);
    }
}
