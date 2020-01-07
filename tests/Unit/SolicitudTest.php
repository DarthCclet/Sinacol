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
     * Test for Verify relation on Solicitud model
     */
    public function testVerifyRelationSolicitud(){
        $solicitud =  factory(\App\Solicitud::class)->create();
        $this->assertInstanceOf('\App\Abogado',$solicitud->abogado);
        $this->assertInstanceOf('\App\EstatusSolicitud',$solicitud->estatusSolicitud);
        $this->assertInstanceOf('\App\MotivoSolicitud',$solicitud->motivoSolicitud);
        $this->assertInstanceOf('\App\Centro',$solicitud->centro);
        $this->assertInstanceOf('\App\User',$solicitud->user);

    }
}
