<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ComunicacionCJF;
use GuzzleHttp\Client;

// use GuzzleHttp\Psr7\Response;
// use GuzzleHttp\Psr7\Request;
// use GuzzleHttp\Exception\RequestException;

class DocumentosCJFTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
     //Pruebas con archivo original
     public function testPost()
     {
       $comunicacionCJF = new ComunicacionCJF();
       $envioDocumento = $comunicacionCJF->crearDocumentoCJF();
       $response = $comunicacionCJF->enviaDocumentoCJF($envioDocumento);

       $this->assertEquals(200, $response->getStatusCode());

       $contentType = $response->getHeaders()["Content-Type"][0];
       $this->assertEquals("application/json; charset=utf-8", $contentType);

       $recepcionDocumento = json_decode($response->getBody());
       // dd($recepcionDocumento);
     }


}
