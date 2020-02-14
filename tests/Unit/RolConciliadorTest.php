<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\RolConciliador;
use App\RolAtencion;

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
//        dd($rol);
        $this->assertInstanceOf(\App\RolAtencion::class,$rol->rolAtencion);
     }
}
