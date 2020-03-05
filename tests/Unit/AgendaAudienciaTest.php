<?php

namespace Tests\Unit;

use Tests\TestCase;

class AgendaAudienciaTest extends TestCase
{
    /**
     * test para provar las relaciones de la tabla audiencias
     *
     * @return void
     */
    public function testVerifyRelationAgendaAudiencia(){
        $audiencia = factory(AgendaAudiencia::class)->create();
        $this->assertInstanceOf(Conciliador::class,$audiencia->conciliador);
        $this->assertInstanceOf(Sala::class,$audiencia->sala);
        $this->assertInstanceOf(Audiencia::class,$audiencia->audiencia);
    }
}
