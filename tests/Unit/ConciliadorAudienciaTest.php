<?php
namespace Tests\Unit;
use Tests\TestCase;
class ConciliadorAudienciaTest extends TestCase
{
    /**
     * test para provar las relaciones de la tabla salas
     *
     * @return void
     */
    public function testVerifyRelationConciliadorAudiencia(){
        $sala = factory(\App\ConciliadorAudiencia::class)->create();
        $this->assertInstanceOf(\App\Conciliador::class,$sala->sala);
        $this->assertInstanceOf(\App\Audiencia::class,$sala->audiencia);
    }
}