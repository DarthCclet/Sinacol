<?php

namespace Tests\Unit;

use App\GrupoPrioritario;
use Tests\TestCase;

class GrupoVulnerableTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCreateGrupoPrioritario()
    {
        $grupo = GrupoPrioritario::inRandomOrder()->first();
        // $grupo = factory(\App\GrupoPrioritario::class)->create();
        $this->assertInstanceOf(GrupoPrioritario::class,$grupo);
    }
}
