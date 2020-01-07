<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AudineciaTest extends TestCase
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
	public function testVerifyRelationSolicitud(){
        // $audiencia = factory(\App\Audiencia::class)->create();
        $audiencias = new \App\Audiencia ;
        $audiencia = $audiencias::find(1);
        $this->assertInstanceOf('\App\Expediente',$solicitud->expediente);
        $this->assertInstanceOf('\App\Conciliador',$solicitud->conciliador);
        $this->assertInstanceOf('\App\Sala',$solicitud->sala);
        $this->assertInstanceOf('\App\Resolucion',$solicitud->resolucion);
        $this->assertInstanceOf('\App\Parte',$solicitud->parte);
    }
}
