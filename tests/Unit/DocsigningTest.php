<?php

namespace Tests\Unit;

use EdgarOrozco\Docsigner\Facades\Docsigner;
use PHPUnit\Framework\TestCase;

class DocsigningTest extends TestCase
{

    /**
     * @test
     *
     * @return void
     */
    public function debe_generar_una_firma_de_una_cadena_de_texto()
    {
        $texto = "LOREM IPSUM SIT DOLOR ETC. ETC.";

        //Estas son llaves de prueba del SAT

        //Para persona moral
        $certificado = base_path('tests/Unit/files/persona_moral.cer');
        $llave = base_path('tests/Unit/files/persona_moral.key');
        $clave = '12345678a';
        $texto_firmado = Docsigner::setCredenciales($certificado, $llave, $clave)->firma($texto);
        $this->assertIsString($texto_firmado);

        //Para persona física
        $certificado = base_path('tests/Unit/files/persona_fisica.cer');
        $llave = base_path('tests/Unit/files/persona_fisica.key');
        $clave = '12345678a';
        $texto_firmado = Docsigner::setCredenciales($certificado, $llave, $clave)->firma($texto);
        $this->assertIsString($texto_firmado);
    }
}
