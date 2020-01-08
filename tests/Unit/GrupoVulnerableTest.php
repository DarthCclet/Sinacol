<?php

namespace Tests\Unit;

use Tests\TestCase;

class GrupoVulnerableTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCreateGrupoVulnerable()
    {
        $grupo = factory(\App\GrupoVulnerable::class)->create();
        $this->assertInstanceOf('\App\GrupoVulnerable',$grupo);
    }
}
