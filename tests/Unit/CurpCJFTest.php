<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ComunicacionCJF;
use GuzzleHttp\Client;
//use App\Http\Controllers\ServiciosCJFController;
use App\Services\ConsultaConciliacionesPorCurp;
use App\TipoParte;
use App\TipoPersona;

// use GuzzleHttp\Psr7\Response;
// use GuzzleHttp\Psr7\Request;
// use GuzzleHttp\Exception\RequestException;

class CurpCJFTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
     //Pruebas con archivo original
     public function testPost()
     {
        $audiencia = factory(\App\Audiencia::class)->create();
        $partes = $audiencia->expediente->solicitud->partes;

        $consultas = new ConsultaConciliacionesPorCurp();

        $tipoSolicitante = TipoParte::where('nombre', 'ilike', 'solicitante')->first();
        $tipoPersonaMoral = TipoPersona::where('nombre', 'ilike', 'MORAL')->first();
        $tipoPersonaFisica = TipoPersona::where('nombre', 'ilike', 'FISICA')->first();
        $solicitante =  $partes->where('tipo_parte_id', $tipoSolicitante->id)->first();

        if($solicitante->tipo_persona_id == $tipoPersonaFisica->id) {

            $res = $consultas->consulta(
                $solicitante->curp
            );

//            dd($res);
        }else{
        }
        $this->assertInstanceOf('App\Parte', $solicitante);
         
//       $contentType = $response->getHeaders()["Content-Type"][0];
//       $this->assertEquals("application/json; charset=utf-8", $contentType);

       // dd($recepcionDocumento);
     }


}
