<?php

namespace Tests\Unit;

use Tests\TestCase;

class ObjetoCitaTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
     public function testCreateObjetoCita()
     {
         $objeto = factory(\App\ObjetoCita::class)->create();
         $this->assertInstanceOf('\App\ObjetoCita',$objeto);
     }

}
