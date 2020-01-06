<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SolicitudTest extends TestCase
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
     * Test for Verify relation on Solicitud
     */
    public function testVerifyRelationSolicitud(){
        // $solicitud = factory(\App\Solicitud::class)->create();
        $solicitudes = new \App\Solicitud ;
        $solicitud = $solicitudes::find(1);
        $this->assertInstanceOf('\App\EstatusSolicitud',$solicitud->estatus_solicitud);
        $this->assertInstanceOf('\App\Abogado',$solicitud->abogado);
        $this->assertInstanceOf('\App\EstatusSolicitud',$solicitud->estatus_solicitud);
        $this->assertInstanceOf('\App\MotivoSolicitud',$solicitud->motivo_solicitud);
        $this->assertInstanceOf('\App\Centro',$solicitud->centro);
        $this->assertInstanceOf('\App\User',$solicitud->user);

    }
}
