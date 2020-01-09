<?php

namespace Tests\Unit;

use Tests\TestCase;

class DatoLaboralTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
     public function testCreateDatoLaboral()
     {
         $datoLaboral = factory(\App\DatoLaboral::class)->create();
         $this->assertInstanceOf('\App\DatoLaboral',$datoLaboral);
         $this->assertInstanceOf('\App\Jornada',$datoLaboral->jornada);
     }

}
