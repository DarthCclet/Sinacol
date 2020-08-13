<?php

namespace App\Traits;

use App\Audiencia;
use App\ClasificacionArchivo;
use App\DatoLaboral;
use App\Expediente;
use App\PlantillaDocumento;
use App\Services\StringTemplate;
use App\Solicitud;
use App\TipoDocumento;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

trait GenerateDocument
{
    /**
     * Generar documento a partir de un modelo y de una plantilla
     * @return mixed
     */
    public function generarConstancia($idAudiencia, $idSolicitud, $clasificacion_id,$plantilla_id, $idSolicitante = null, $idSolicitado = null)
    {
        $plantilla = PlantillaDocumento::find($plantilla_id);
        if($plantilla != null){
            if($idAudiencia != ""){

                $padre = Audiencia::find($idAudiencia);
                $directorio = 'expedientes/' . $padre->expediente_id . '/audiencias/' . $idAudiencia;
                $algo = Storage::makeDirectory($directorio, 0775, true);
                
                $tipoArchivo = ClasificacionArchivo::find($clasificacion_id);
                
                $html = $this->renderDocumento($idSolicitud, $plantilla->id, $idSolicitante, $idSolicitado);
                $pdf = App::make('dompdf.wrapper');
                $pdf->getDomPDF();
                $pdf->loadHTML($html)->setPaper('A4');
                
                //al
                
                //al
                //Creamos el registro
                $archivo = $padre->documentos()->create(["descripcion" => "Documento de audiencia " . $tipoArchivo->nombre]);
                $plantilla = PlantillaDocumento::find($plantilla->id);
                $nombreArchivo = $plantilla->nombre_plantilla;
                $nombreArchivo = $this->eliminar_acentos(str_replace(" ","",$nombreArchivo));
                $path = $directorio . "/".$nombreArchivo . $archivo->id . '.pdf';
                $fullPath = storage_path('app/' . $directorio) . "/".$nombreArchivo . $archivo->id . '.pdf';
                
                $store = $pdf->save($fullPath);
                $archivo->update([
                    "nombre" => str_replace($directorio . "/", '', $path),
                    "nombre_original" => str_replace($directorio . "/", '', $path), //str_replace($directorio, '',$path->getClientOriginalName()),
                    "descripcion" => "Documento de audiencia " . $tipoArchivo->nombre,
                    "ruta" => $path,
                    "tipo_almacen" => "local",
                    "uri" => $path,
                    "longitud" => round(Storage::size($path) / 1024, 2),
                    "firmado" => "false",
                    "clasificacion_archivo_id" => $tipoArchivo->id,
                ]);
            }else{
                $padre = Solicitud::find($idSolicitud);
                $directorio = 'expedientes/' . $padre->expediente_id . '/solicitud/' . $idSolicitud;
                $algo = Storage::makeDirectory($directorio, 0775, true);
                
                $tipoArchivo = ClasificacionArchivo::find($clasificacion_id);
                
                $html = $this->renderDocumento($idSolicitud, $plantilla->id, $idSolicitante, $idSolicitado);
                $pdf = App::make('dompdf.wrapper');
                $pdf->getDomPDF();
                $pdf->loadHTML($html)->setPaper('A4');
                
                //al
                
                //al
                //Creamos el registro
                $archivo = $padre->documentos()->create(["descripcion" => "Documento de solicitud " . $tipoArchivo->nombre]);
                $plantilla = PlantillaDocumento::find($plantilla->id);
                $nombreArchivo = $plantilla->nombre_plantilla;
                $nombreArchivo = $this->eliminar_acentos(str_replace(" ","",$nombreArchivo));
                $path = $directorio . "/".$nombreArchivo . $archivo->id . '.pdf';
                $fullPath = storage_path('app/' . $directorio) . "/".$nombreArchivo . $archivo->id . '.pdf';
                
                $store = $pdf->save($fullPath);
                $archivo->update([
                    "nombre" => str_replace($directorio . "/", '', $path),
                    "nombre_original" => str_replace($directorio . "/", '', $path), //str_replace($directorio, '',$path->getClientOriginalName()),
                    "descripcion" => "Documento de solicitud " . $tipoArchivo->nombre,
                    "ruta" => $path,
                    "tipo_almacen" => "local",
                    "uri" => $path,
                    "longitud" => round(Storage::size($path) / 1024, 2),
                    "firmado" => "false",
                    "clasificacion_archivo_id" => $tipoArchivo->id,
                ]);
            }
            return 'Product saved successfully';
        }else{
            return 'No existe plantilla';
        }
    }

    public function renderDocumento($idSolicitud, $idPlantilla, $idSolicitante, $idSolicitado)
    {
        
        $vars = [];
        $data = $this->getDataModelos($idSolicitud, $idPlantilla, $idSolicitante, $idSolicitado);
        if ($data != null) {
            $count = 0;
            foreach ($data as $key => $dato) { //solicitud
                if (gettype($dato) == 'array') {
                    $isArrAssoc = Arr::isAssoc($dato);
                    if ($isArrAssoc) { //si es un array asociativo
                        foreach ($dato as $k => $val) { // folio
                            $val = ($val === null && $val != false) ? "" : $val;
                            if (gettype($val) == "boolean") {
                                $val = ($val == false) ? 'No' : 'Si';
                            } elseif (gettype($val) == 'array') {
                                $isArrayAssoc = Arr::isAssoc($val);
                                if (!$isArrayAssoc) {
                                    foreach ($val as $i => $v) {
                                        if (isset($v['nombre'])) {
                                            $names = [];
                                            array_push($names, $v['nombre']);
                                            // array_push($names,$v['nombre']);
                                        }
                                    }
                                    $val = implode(", ", $names);
                                } else {
                                    if (isset($val['nombre']) && $k != 'persona') {
                                        $val = $val['nombre'];
                                    } elseif ($k == 'persona') {
                                        foreach ($val as $n => $v) {
                                            $vars[strtolower($key . '_' . $n)] = $v;
                                        }
                                    }
                                }
                            } elseif (gettype($val) == 'string') {
                                $pos = strpos($k, 'fecha');
                                if ($pos !== false) {
                                    $val = $this->formatoFecha($val);
                                }
                            }
                            $vars[strtolower($key . '_' . $k)] = $val;
                        }
                    } else { //Si no es un array assoc (solicitados, solicitantes)
                        foreach ($dato as $data) {
                            foreach ($data as $k => $val) { // folio
                                $val = ($val === null && $val != false) ? "" : $val;
                                if (gettype($val) == "boolean") {
                                    $val = ($val == false) ? 'No' : 'Si';
                                } elseif (gettype($val) == 'array') {
                                    $isArrayAssoc = Arr::isAssoc($val);
                                    if (!$isArrayAssoc) {
                                        foreach ($val as $i => $v) {
                                            if (isset($v['nombre'])) {
                                                $names = [];
                                                array_push($names, $v['nombre']);
                                                // array_push($names,$v['nombre']);
                                            }
                                        }
                                        $val = implode(", ", $names);
                                    } else {
                                        if (isset($val['nombre']) && $k != 'persona') {
                                            $val = $val['nombre'];
                                        }
                                    }
                                } elseif (gettype($val) == 'string') {
                                    $pos = strpos($k, 'fecha');
                                    if ($pos !== false) {
                                        $val = $this->formatoFecha($val);
                                    }
                                    // }else{
                                }
                                $vars[strtolower($key . '_' . $k)] = $val;
                            }
                        }
                    }
                } else {
                    $vars[strtolower('solicitud_' . $key)] = $dato;
                }
            }
        }
        $vars = Arr::except($vars, ['conciliador_persona']);
        $style = "<html xmlns=\"http://www.w3.org/1999/html\">
                <head>
                <style>
                @page { margin: 150px 50px 40px 60px;}
                @media print {
                  table { border-collapse: collapse;
                         width: 59.1193%;
                         height: 122px;
                         border-color: #e61f0b;
                         border-style: solid;
                         float: right; }
                         tr:nth-child(even) {background-color: #f2f2f2;}
                  }
                .header { position: fixed; top: -150px;}
                .footer { position: fixed; bottom: 20px;}
                #contenedor-firma {height: 80px;}
                </style>
                </head>
                <body>
                ";
        $end = "</body></html>";

        // $config = PlantillaDocumento::orderBy('created_at', 'desc')->first();
        $config = PlantillaDocumento::find($idPlantilla);
        if (!$config) {
            $header = view('documentos._header_documentos_default');
            $body = view('documentos._body_documentos_default');
            $footer = view('documentos._footer_documentos_default');

            $header = '<div class="header">' . $header . '</div>';
            $body = '<div class="body">' . $body . '</div>';
            $footer = '<div class="footer">' . $footer . '</div>';
        } else {
            $header = '<div class="header">' . $config->plantilla_header . '</div>';
            $body = '<div class="body">' . $config->plantilla_body . '</div>';
            $footer = '<div class="footer">' . $config->plantilla_footer . '</div>';
        }
        $blade = $style . $header . $footer . $body . $end;
        $html = StringTemplate::renderPlantillaPlaceholders($blade, $vars);
        return $html;
    }
    private function getDataModelos($idSolicitud, $idPlantilla, $idSolicitante, $idSolicitado)
    {
        $plantilla = PlantillaDocumento::find($idPlantilla);
        $tipo_plantilla = TipoDocumento::find($plantilla->tipo_documento_id);
        $objetos = explode(",", $tipo_plantilla->objetos);
        $path = base_path('database/datafiles');
        $jsonElementos = json_decode(file_get_contents($path . "/elemento_documentos.json"), true);
        $idBase = "";
        $data = [];
        foreach ($objetos as $objeto) {
            foreach ($jsonElementos['datos'] as $key => $element) {
                if ($element['id'] == $objeto) {
                    $model_name = 'App\\' . $element['objeto'];
                    $model = $element['objeto'];
                    $model_name = 'App\\' . $model;
                    if ($model == 'Solicitud') {
                        $solicitud = $model_name::with('estatusSolicitud', 'objeto_solicitudes')->find($idSolicitud); //first();
                        $objeto = new JsonResponse($solicitud);
                        $obj = json_decode($objeto->content(), true);

                        $idBase = intval($obj['id']);
                        $centroId = intval($obj['centro_id']);
                        $obj = Arr::except($obj, ['id', 'updated_at', 'created_at', 'deleted_at']);
                        $data = ['solicitud' => $obj];
                    } elseif ($model == 'Parte') {
                        $partes = $model_name::with('nacionalidad', 'domicilios', 'lenguaIndigena', 'tipoDiscapacidad')->where('solicitud_id', intval($idBase))->get();
                        $objeto = new JsonResponse($partes);
                        $obj = json_decode($objeto->content(), true);
                        
                        $parte2 = [];
                        $parte1 = [];
                        $countSolicitante = 0;
                        $countSolicitado = 0;
                        // $partes = $model_name::with('nacionalidad','domicilios','lenguaIndigena','tipoDiscapacidad')->findOrFail(1);
                        foreach ($obj as $parte) {
                            $parteId = $parte['id'];
                            $parte = Arr::except($parte, [ 'updated_at', 'created_at', 'deleted_at']);
                            if ($parte['tipo_parte_id'] == 1) { //Solicitante
                                //datos laborales del solicitante
                                //  $datoLaboral = DatoLaboral::with('jornada','ocupacion')->where('parte_id', 8)->get();
                                $datoLaboral = DatoLaboral::with('jornada', 'ocupacion')->where('parte_id', $parteId)->get();
                                $objeto = new JsonResponse($datoLaboral);
                                $datoLaboral = json_decode($objeto->content(), true);
                                $datoLaboral = Arr::except($datoLaboral[0], ['id', 'updated_at', 'created_at', 'deleted_at']);
                                $parte['datos_laborales'] = $datoLaboral;
                                if ($idSolicitante != null) {
                                    if ($idSolicitante == $parte['id']) {
                                        array_push($parte1, $parte);
                                    }
                                } else {
                                    array_push($parte1, $parte);
                                }
                                $countSolicitante += 1;
                            } elseif ($parte['tipo_parte_id'] == 2) { //Solicitado
                                $countSolicitado += 1;
                                if ($idSolicitado != null) {
                                    if ($idSolicitado == $parte['id']) {
                                        array_push($parte2, $parte);
                                    }
                                } else {
                                    array_push($parte2, $parte);
                                }
                            }
                        }
                        
                        
                        $data = Arr::add($data, 'solicitante', $parte1);
                        $data = Arr::add($data, 'solicitado', $parte2);
                        $data = Arr::add($data, 'total_solicitantes', $countSolicitante);
                        $data = Arr::add($data, 'total_solicitados', $countSolicitado);
                    } elseif ($model == 'Audiencia') {
                        $expediente = Expediente::where('solicitud_id', $idBase)->get();
                        $expedienteId = $expediente[0]->id;
                        $objeto = new JsonResponse($expediente);
                        $expediente = json_decode($objeto->content(), true);
                        $expediente = Arr::except($expediente[0], ['id', 'updated_at', 'created_at', 'deleted_at']);
                        $data = Arr::add($data, 'expediente', $expediente);
                        // $objeto = $model_name::with('conciliador')->findOrFail(1);
                        $audiencias = $model_name::where('expediente_id', $expedienteId)->get();
                        if($audiencias->first() == null){
                            $audiencias = Audiencia::where('id','>',1)->get();//$model_name::where('expediente_id', $expedienteId)->get();
                        }
                        $conciliadorId = $audiencias[0]->conciliador_id;
                        $objeto = new JsonResponse($audiencias);
                        $audiencias = json_decode($objeto->content(), true);
                        $Audiencias = [];
                        foreach ($audiencias as $audiencia) {
                            $audiencia = Arr::except($audiencia, ['id', 'updated_at', 'created_at', 'deleted_at']);
                            array_push($Audiencias, $audiencia);
                        }
                        $data = Arr::add($data, 'audiencia', $Audiencias);
                    } elseif ($model == 'Conciliador') {
                        $objeto = $model_name::with('persona')->find($conciliadorId);
                        $objeto = new JsonResponse($objeto);
                        $conciliador = json_decode($objeto->content(), true);
                        $conciliador = Arr::except($conciliador, ['id', 'updated_at', 'created_at', 'deleted_at']);
                        $conciliador['persona'] = Arr::except($conciliador['persona'], ['id', 'updated_at', 'created_at', 'deleted_at']);
                        $data = Arr::add($data, 'conciliador', $conciliador);
                    } elseif ($model == 'Centro') {
                        $objeto = $model_name::find($centroId);
                        $objeto = new JsonResponse($objeto);
                        $centro = json_decode($objeto->content(), true);
                        $centro = Arr::except($centro, ['id', 'updated_at', 'created_at', 'deleted_at']);
                        $data = Arr::add($data, 'centro', $centro);
                    } else {
                        $objeto = $model_name::first();
                        $objeto = new JsonResponse($objeto);
                        $otro = json_decode($objeto->content(), true);
                        $otro = Arr::except($otro, ['id', 'updated_at', 'created_at', 'deleted_at']);
                        $data = Arr::add($data, $model, $otro);
                    }
                }
            }
        }
        return $data;
    }

    function eliminar_acentos($cadena){
		
		//Reemplazamos la A y a
		$cadena = str_replace(
		array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
		array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
		$cadena
		);
 
		//Reemplazamos la E y e
		$cadena = str_replace(
		array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
		array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
		$cadena );
 
		//Reemplazamos la I y i
		$cadena = str_replace(
		array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
		array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
		$cadena );
 
		//Reemplazamos la O y o
		$cadena = str_replace(
		array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
		array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
		$cadena );
 
		//Reemplazamos la U y u
		$cadena = str_replace(
		array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
		array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
		$cadena );
 
		//Reemplazamos la N, n, C y c
		$cadena = str_replace(
		array('Ñ', 'ñ', 'Ç', 'ç'),
		array('N', 'n', 'C', 'c'),
		$cadena
		);
		
		return $cadena;
	}
    /*
        Convertir fechas yyyy-mm-dd hh to dd de Monthname de yyyy
         */
    private function formatoFecha($fecha)
    {
        $monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noivembre", "Diciembre"];
        $hh = "";
        if (strpos($fecha, " ")) {
            $date = explode(' ', $fecha);
            $fecha = $date[0];
            $hh = $date[1];
        }
        $fecha = explode('-', $fecha);
        $dd = $fecha[2];
        $mm = $fecha[1];
        $yy = $fecha[0];
        $ddmmyy = $dd . ' de ' . $monthNames[intval($mm) - 1] . ' de ' . $yy . ' ' . $hh;
        return $ddmmyy;
    }
}
