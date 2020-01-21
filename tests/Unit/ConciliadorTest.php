<?php

namespace Tests\Unit;

use Tests\TestCase;

class ConciliadorTest extends TestCase
{
    /**
     * test para provar las relaciones de la tabla conciliadores
     *
     * @return void
     */
    public function testVerifyRelationConciliador(){
        $conciliador = factory(\App\Conciliador::class)->create();
        $this->assertInstanceOf('\App\Persona',$conciliador->persona);
        $this->assertInstanceOf('\App\Centro',$conciliador->centro);
    }
}
