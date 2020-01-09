<?php

namespace Tests\Unit;

use Tests\TestCase;

class RolConciliadorTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */

     public function testCreateRolConciliador()
     {
         $rol = factory(\App\RolConciliador::class)->create();
         $this->assertInstanceOf('\App\RolConciliador',$rol);
     }
}
