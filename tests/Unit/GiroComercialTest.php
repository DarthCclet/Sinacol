<?php

namespace Tests\Unit;

use Tests\TestCase;

class GiroComercialTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCreateGiro()
    {
        $giro = factory(\App\GiroComercial::class)->create();
        $this->assertInstanceOf('\App\GiroComercial',$giro);
    }
}
