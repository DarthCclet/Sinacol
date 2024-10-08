<?php

namespace Tests\Unit;

use Tests\TestCase;

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
     * Test for Verify relation on parte model
     */
    public function testVerifyRelationParte(){
        $parte = factory(\App\Parte::class)->create();
        if($parte->tipo_persona_id == 1){
            $this->assertInstanceOf('\App\Genero',$parte->genero);
        }
        $this->assertInstanceOf('\App\Solicitud',$parte->solicitud);
        $this->assertInstanceOf('\App\TipoParte',$parte->tipoParte);
        $this->assertInstanceOf('\App\TipoPersona',$parte->tipoPersona);
        $this->assertInstanceOf('\App\Nacionalidad',$parte->nacionalidad);
        $this->assertInstanceOf('\App\Estado',$parte->entidadNacimiento);
    }
}
