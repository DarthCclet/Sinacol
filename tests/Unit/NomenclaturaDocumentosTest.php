<?php

namespace Tests\Unit;

use App\Traits\GenerateDocument;
use PHPUnit\Framework\TestCase;

class NomenclaturaDocumentosTest extends TestCase
{
    use GenerateDocument;

    /**
     * @test
     *
     * @return void
     */
    public function no_debe_duplicarse_el_folio_nomenclatura_de_documento()
    {
        $stack = [];
        for($i = 0; $i < 10000; $i++) {
            $nomenclatura = $this->nomenclaturaDocumento(1);
            $this->assertArrayNotHasKey($nomenclatura, $stack);
            $stack[$nomenclatura] = 1;
        }
    }
}
