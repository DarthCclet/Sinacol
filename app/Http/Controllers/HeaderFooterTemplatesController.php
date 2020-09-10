<?php

namespace App\Http\Controllers;

use App\Traits\GenerateDocument;

class HeaderFooterTemplatesController extends Controller
{
    use GenerateDocument;


    public function debug()
    {
        $idAudiencia = 12;
        $idSolicitud = 12;
        $plantilla_id = 2;
        $idSolicitante = 23;
        $idSolicitado = 24;
        $idConciliador = null;

        $html = $this->renderDocumento(
            $idAudiencia,
            $idSolicitud,
            $plantilla_id,
            $idSolicitante,
            $idSolicitado,
            $idConciliador
        );

        return $this->renderPDF($html, $plantilla_id);
    }

}
