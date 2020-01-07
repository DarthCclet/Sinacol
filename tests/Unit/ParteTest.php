<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ParteTest extends TestCase
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
     * Test for Verify relation on parte
     */
    public function testVerifyRelationParte(){
        // $parte = factory(\App\parte::class)->create();
        $parte = factory(\App\Parte::class)->create();
        $this->assertInstanceOf('\App\Genero',$parte->genero);
        $this->assertInstanceOf('\App\Solicitud',$parte->solicitud);
        $this->assertInstanceOf('\App\TipoParte',$parte->tipo_parte);
        $this->assertInstanceOf('\App\TipoPersona',$parte->tipo_persona);
        $this->assertInstanceOf('\App\Nacionalidad',$parte->nacionalidad);
        $this->assertInstanceOf('\App\Estado',$parte->entidad_nacimiento);
    }
}
