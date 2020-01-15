<?php

namespace Tests\Unit;

use App\Persona;
use App\TipoPersona;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    //use DatabaseTransactions;

    /**
     * Un usuario debe pertenecer a una persona física
     * @test
     */
    public function unUsuarioDebePertenecerAPersonaFisica()
    {
        $usuario = factory(User::class)->create();
        $this->assertInstanceOf(Persona::class, $usuario->persona);
        $this->assertInstanceOf(TipoPersona::class, $usuario->persona->tipoPersona);
        $this->assertEquals("F", $usuario->persona->tipoPersona->abreviatura);
    }
}
