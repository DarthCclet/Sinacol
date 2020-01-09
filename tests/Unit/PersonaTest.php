<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class PersonaTest extends TestCase
{
    /**
     * test para provar las relaciones de la tabla personas
     *
     * @return void
     */
    public function testVerifyRelationPersona(){
        $persona = factory(\App\Persona::class)->create();
        $this->assertInstanceOf('\App\TipoPersona',$persona->tipoPersona);
    }
}
