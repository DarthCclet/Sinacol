<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SalaTest extends TestCase
{
    /**
     * test para provar las relaciones de la tabla salas
     *
     * @return void
     */
    public function testVerifyRelationSala(){
        $sala = factory(\App\Sala::class)->create();
        $this->assertInstanceOf('\App\Centro',$sala->centro);
    }
}
