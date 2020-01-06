<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class GeneroTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }
    /**
     * valida SoftDelete
     * @return assertSoftDelete valida softDelete
     */
    // public function testSoftDelete()
    // {
    //     $this->assertSoftDeleted("generos" , ['id'=> 1]);
    // }
    // /**
    //  * regresa true si se borra el registro
    //  * @return boolean regresa true cuando borra el registro
    //  */
    // public function testRestoreGenero()
    // {
    //     $genero = new \App\Genero;
    //     $resp = $genero::withTrashed()->get()[0]->restore();
    //     $this->assertTrue($resp);
    // }
}
