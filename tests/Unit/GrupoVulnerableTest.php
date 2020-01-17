<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\GrupoVulnerable;

class GrupoVulnerableTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCreateGrupoVulnerable()
    {
        $grupo = GrupoVulnerable::inRandomOrder()->first();
        // $grupo = factory(\App\GrupoVulnerable::class)->create();
        $this->assertInstanceOf(GrupoVulnerable::class,$grupo);
    }
}
