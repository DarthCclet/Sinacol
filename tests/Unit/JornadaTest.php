<?php

namespace Tests\Unit;

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
         $jornada = factory(\App\Jornada::class)->create();
         $this->assertInstanceOf('\App\Jornada',$jornada);
     }
}
