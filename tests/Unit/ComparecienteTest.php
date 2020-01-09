<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ComparecienteTest extends TestCase
{
    /**
     * test para provar las relaciones de la tabla Comparecientes
     *
     * @return void
     */
    public function testVerifyRelationCompareciente(){
        $compareciente = factory(\App\Compareciente::class)->create();
        $this->assertInstanceOf('\App\Audiencia',$compareciente->audiencia);
    }
}
