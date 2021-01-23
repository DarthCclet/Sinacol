<?php

namespace Tests\Unit;

//use EdgarOrozco\Docsigner;
use EdgarOrozco\Docsigner\Facades\Docsigner;
use PHPUnit\Framework\TestCase;

class DocsigningTest extends TestCase
{

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $texto = "LOREM IPSUM";
        $this->assertTrue(true);

        $certificado = base_path('tests/Unit/files/persona_moral.cer');
        $llave = base_path('tests/Unit/files/persona_moral.key');
        $clave = '12345678a';

        $docsigner = Docsigner::setCredenciales($certificado, $llave, $clave);
        echo $docsigner->firma($texto);
    }
}
