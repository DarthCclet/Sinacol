<?php

namespace App\Http\Controllers;

use App\Solicitud;
use App\Traits\GenerateDocument;
use Illuminate\Http\Request;


class HeaderFooterTemplatesController extends Controller
{
    use GenerateDocument;


    public function debug(Request $request)
    {
        $idSolicitud = $request->get('solicitud_id',1);

        $idAudiencia = $request->get('audiencia_id');
        $plantilla_id = $request->get('plantilla_id', 1);
        $idSolicitante = $request->get('solicitante_id');
        $idSolicitado = $request->get('solicitado_id');
        $idConciliador = $request->get('conciliador_id');
        $pdf = $request->exists('pdf');

        $solicitud = Solicitud::find($idSolicitud);
        if ($solicitud) {
            if (!$idAudiencia && isset($solicitud->expediente->audiencia->first()->id)) {
                $idAudiencia = $solicitud->expediente->audiencia->first()->id;
                if(!$idConciliador){
                    $idConciliador = $solicitud->expediente->audiencia->first()->conciliador->id;
                }
            }
            if(!$idSolicitante){
                $idSolicitante =$solicitud->solicitantes->first()->id;
            }
            if(!$idSolicitado){
                $idSolicitado =$solicitud->solicitados->first()->id;
            }
        }

        $html = $this->renderDocumento(
            $idAudiencia,
            $idSolicitud,
            $plantilla_id,
            $idSolicitante,
            $idSolicitado,
            $idConciliador
        );

        if($pdf) {
            return $this->renderPDF($html, $plantilla_id);
        }
        else{
            echo $html; exit;
        }
    }

}
