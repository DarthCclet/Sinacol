<?php

namespace Tests\Unit;

use App\Services\ConsultaConciliacionesPorNombre;
use App\TipoParte;
use App\TipoPersona;
use PHPUnit\Framework\TestCase;

class ConsultaAudienciasPorNombreParteTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function consultaPorNombreSolicitante()
    {
        $audiencia = factory(\App\Audiencia::class)->create();
        $partes = $audiencia->expediente->solicitud->partes;

        $consultas = new ConsultaConciliacionesPorNombre();

        $tipoSolicitante = TipoParte::where('nombre', 'ilike', 'solicitante')->first();
        $tipoPersonaMoral = TipoPersona::where('nombre', 'ilike', 'MORAL')->first();
        $tipoPersonaFisica = TipoPersona::where('nombre', 'ilike', 'FISICA')->first();
        $solicitante =  $partes->where('tipo_parte_id', $tipoSolicitante->id)->first();

        if($solicitante->tipo_persona_id == $tipoPersonaFisica->id) {

            $res = $consultas->consulta(
                mb_strtoupper($solicitante->nombre),
                $solicitante->primer_apellido,
                $solicitante->segundo_apellido,
                $solicitante->tipo_solicitante_id,
                $solicitante->tipo_parte_id
            );

            dd($res);
        }else{
        }
        //$this->assertInstanceOf('App\Parte', $persona);
    }
}
