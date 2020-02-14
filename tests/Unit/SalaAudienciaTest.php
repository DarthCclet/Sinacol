<?php

namespace Tests\Unit;

use Tests\TestCase;

class SalaAudienciaTest extends TestCase
{
    /**
     * test para provar las relaciones de la tabla salas
     *
     * @return void
     */
    public function testVerifyRelationSalaAudiencia(){
        $sala = factory(\App\SalaAudiencia::class)->create();
        $this->assertInstanceOf(\App\Sala::class,$sala->sala);
        $this->assertInstanceOf(\App\Audiencia::class,$sala->audiencia);
    }
}
