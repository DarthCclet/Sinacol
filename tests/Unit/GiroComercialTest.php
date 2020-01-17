<?php

namespace Tests\Unit;

use App\GiroComercial;
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
        // $giro = factory(\App\GiroComercial::class)->create();
        $giro = GiroComercial::inRandomOrder()->first();
        $this->assertInstanceOf('\App\GiroComercial',$giro);
    }
}
