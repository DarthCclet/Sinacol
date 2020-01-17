<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\ObjetoCita;

class ObjetoCitaTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
     public function testCreateObjetoCita()
     {
         
        //  $objeto = factory(\App\ObjetoCita::class)->create();
        $objeto = ObjetoCita::inRandomOrder()->first();
         $this->assertInstanceOf('\App\ObjetoCita',$objeto);
     }

}
