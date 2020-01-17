<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\RolConciliador;

class RolConciliadorTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */

     public function testCreateRolConciliador()
     {
        $rol = RolConciliador::inRandomOrder()->first();
        //  $rol = factory(\App\RolConciliador::class)->create();
         $this->assertInstanceOf('\App\RolConciliador',$rol);
     }
}
