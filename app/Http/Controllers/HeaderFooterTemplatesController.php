<?php

namespace App\Http\Controllers;

use App\Solicitud;
use App\Traits\GenerateDocument;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


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
        $idDocumento = $request->get('documento_id');
        $pdf = $request->exists('pdf');

        $solicitud = Solicitud::find($idSolicitud);
        if ($solicitud) {
            if (!$idAudiencia && isset($solicitud->expediente->audiencia->first()->id)) {
                $idAudiencia = $solicitud->expediente->audiencia->first()->id;
                // if(!$idConciliador){
                //     $idConciliador = $solicitud->expediente->audiencia->first()->conciliador->id;
                // }
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
            $idConciliador,
            $idDocumento
        );

        if($pdf) {
            return $this->renderPDF($html, $plantilla_id);
        }
        else{
            echo $html; exit;
        }
    }
    public function preview(Request $request)
    {
        try{
            //$arrayPlantilla = [40=>6,18=>7,17=>1,16=>2,15=>3,14=>4,13=>10,19=>11,41=>8,43=>9,52=>14,54=>15,45=>12,49=>13,61=>24,62=>19,56=>18,63=>25,63=>26,64=>29,66=>30,65=>31];
            $arrayPlantilla = [6=>40,7=>18,17=>1,2=>16,16=>16,3=>15,4=>14,10=>13,11=>19,8=>41,9=>43,14=>52,15=>54,12=>45,13=>49,24=>61,18=>56,19=>62,20=>62,29=>64,30=>66,31=>65,21=>59,22=>60,23=>60,25=>63,26=>63];
            $idSolicitud = $request->get('solicitud_id',1);
            
            $idAudiencia = $request->get('audiencia_id');
            $plantilla_id = $request->get('plantilla_id');
            $idSolicitante = $request->get('solicitante_id');
            $idSolicitado = $request->get('solicitado_id');
            $clasificacion_archivo_id = $arrayPlantilla[$plantilla_id];
            $html = $this->renderDocumento(
                $idAudiencia,
                $idSolicitud,
                $plantilla_id,
                $idSolicitante,
                $idSolicitado,
                ""
            );
            //$html = file_get_contents(env('APP_URL').'/header/'.$plantilla_id) . $html . file_get_contents(env('APP_URL').'/footer/'.$plantilla_id);
            return $this->sendResponse($html, "Correcto");
        }catch(Exception $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
            " Se emitió el siguiente mensaje: ". $e->getMessage().
            " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            return $this->sendResponse("No se pudo generar documento", "Correcto");
        }
            //return $html;
    }

}
