<?php

namespace Tests\Unit;

use App\Jornada;
use Tests\TestCase;

class JornadaTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */

     public function testCreateJornada()
     {
        //  $jornada = factory(\App\Jornada::class)->create();
         $jornada = Jornada::inRandomOrder()->first();
         $this->assertInstanceOf('\App\Jornada',$jornada);
     }
}
