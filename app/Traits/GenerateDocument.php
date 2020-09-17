<?php

namespace App\Traits;

use App\Audiencia;
use App\AudienciaParte;
use App\ClasificacionArchivo;
use App\ConceptoPagoResolucion;
use App\DatoLaboral;
use App\EtapaResolucionAudiencia;
use App\Expediente;
use App\Parte;
use App\Periodicidad;
use App\PlantillaDocumento;
use App\ResolucionParteConcepto;
use App\ResolucionPartes;
use App\SalaAudiencia;
use App\SalarioMinimo;
use App\Services\StringTemplate;
use App\Solicitud;
use App\TipoDocumento;
use App\VacacionesAnio;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use NumberFormatter;

trait GenerateDocument
{
    /**
     * Generar documento a partir de un modelo y de una plantilla
     * @return mixed
     */
    public function generarConstancia($idAudiencia, $idSolicitud, $clasificacion_id,$plantilla_id, $idSolicitante = null, $idSolicitado = null, $idConciliador = null)
    {
		$plantilla = PlantillaDocumento::find($plantilla_id);
        if($plantilla != null){
            if($idAudiencia != ""){

                $padre = Audiencia::find($idAudiencia);
                $directorio = 'expedientes/' . $padre->expediente_id . '/audiencias/' . $idAudiencia;
                $algo = Storage::makeDirectory($directorio, 0775, true);

                $tipoArchivo = ClasificacionArchivo::find($clasificacion_id);

				$html = $this->renderDocumento($idAudiencia,$idSolicitud, $plantilla->id, $idSolicitante, $idSolicitado,$idConciliador);

                //Creamos el registro
                $archivo = $padre->documentos()->create(["descripcion" => "Documento de audiencia " . $tipoArchivo->nombre]);
                $plantilla = PlantillaDocumento::find($plantilla->id);
                $nombreArchivo = $plantilla->nombre_plantilla;
                $nombreArchivo = $this->eliminar_acentos(str_replace(" ","",$nombreArchivo));
                $path = $directorio . "/".$nombreArchivo . $archivo->id . '.pdf';
                $fullPath = storage_path('app/' . $directorio) . "/".$nombreArchivo . $archivo->id . '.pdf';

                //Hacemos el render del pdf y lo guardamos en $fullPath
                $this->renderPDF($html, $plantilla->id, $fullPath);

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
            }
            else{
                $padre = Solicitud::find($idSolicitud);
                if($padre->expediente != null){
                  $directorio = 'expedientes/' . $padre->expediente->id . '/solicitud/' . $idSolicitud;
                }else{
                  $directorio = 'solicitudes/' . $idSolicitud;
                }
                $algo = Storage::makeDirectory($directorio, 0775, true);

                $tipoArchivo = ClasificacionArchivo::find($clasificacion_id);

                $html = $this->renderDocumento($idAudiencia,$idSolicitud, $plantilla->id, $idSolicitante, $idSolicitado,$idConciliador);

                //Creamos el registro
                $archivo = $padre->documentos()->create(["descripcion" => "Documento de solicitud " . $tipoArchivo->nombre]);
                $plantilla = PlantillaDocumento::find($plantilla->id);
                $nombreArchivo = $plantilla->nombre_plantilla;
                $nombreArchivo = $this->eliminar_acentos(str_replace(" ","",$nombreArchivo));
                $path = $directorio . "/".$nombreArchivo . $archivo->id . '.pdf';
                $fullPath = storage_path('app/' . $directorio) . "/".$nombreArchivo . $archivo->id . '.pdf';

                //Hacemos el render del pdf y lo guardamos en $fullPath
                $this->renderPDF($html, $plantilla->id, $fullPath);

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
        }
        else{
            return 'No existe plantilla';
        }
    }

    public function renderDocumento($idAudiencia,$idSolicitud, $idPlantilla, $idSolicitante, $idSolicitado,$idConciliador)
    {

        $vars = [];
        $data = $this->getDataModelos($idAudiencia,$idSolicitud, $idPlantilla, $idSolicitante, $idSolicitado,$idConciliador);
        if($data!=null){
            $count =0;
            foreach ($data as $key => $dato) { //solicitud
              if( gettype($dato) == 'array'){
                 $isArrAssoc = Arr::isAssoc($dato);
                 if($isArrAssoc){ //si es un array asociativo
                  foreach ($dato as $k => $val) { // folio
                    $val = ($val === null && $val != false)? "" : $val;
                    if(gettype($val)== "boolean"){
                      $val = ($val == false)? 'No' : 'Si';
                    }elseif(gettype($val)== 'array'){
                      $isArrayAssoc = Arr::isAssoc($val);
                      if( !$isArrayAssoc ){
                        foreach ($val as $i => $v) {
                          if( isset($v['nombre'] ) ){
                            $names =[];
                            array_push($names,$v['nombre']);
                            // array_push($names,$v['nombre']);
                          }
                        }
                        $val = implode (", ", $names);
                      }else{
                        if( isset($val['nombre']) && $k !='persona' && $k !='nombre_completo' ){
                          $val = $val['nombre'];
                        }elseif ($k == 'persona') {
                          foreach ($val as $n =>$v) {
                            $vars[strtolower($key.'_'.$n)] = $v;
                          }
                          $vars[strtolower($key.'_nombre_completo')] = $val['nombre'].' '.$val['primer_apellido'].' '.$val['segundo_apellido'];
                        }else{
                          foreach ($val as $n =>$v) {
                            $vars[strtolower($key.'_'.$k.'_'.$n)] =$v;//($v !== null)? $v :"-";
                          }
                        }
                      }
                    }elseif(gettype($val)== 'string'){
                      $pos = strpos($k,'fecha');
                      if ($pos !== false){
                        $val = $this->formatoFecha($val,1);
                      }
                    }
                    $vars[strtolower($key.'_'.$k)] = $val;
                  }
                }else{//Si no es un array assoc (n solicitados, n solicitantes)
                  foreach ($dato as $data) {//sol[0]...
                    foreach ($data as $k => $val) { // folio, domicilios n
                      $val = ($val === null && $val != false)? "" : $val;
                      if(gettype($val)== "boolean"){
                        $val = ($val == false)? 'No' : 'Si';
                      }elseif(gettype($val)== 'array'){
                        $isArrayAssoc = Arr::isAssoc($val);
                      if( !$isArrayAssoc ){ // with
                        if($k == 'domicilios'){
                          $val = Arr::except($val[0],['id','updated_at','created_at','deleted_at','domiciliable_type','domiciliable_id','hora_atencion_de','hora_atencion_a','georeferenciable','tipo_vialidad_id','tipo_asentamiento_id']);
                          foreach ($val as $n =>$v) {
                            $vars[strtolower($key.'_'.$k.'_'.$n)] = $v;
                          }
                        }else{
                          foreach ($val as $i => $v) {
                            if( isset($v['nombre'] ) ){
                              $names =[];
                              array_push($names,$v['nombre']);
                            }
                          }
                          $val = implode (", ", $names);
                        }
                        }else{
                          if( isset($val['nombre']) && $k !='persona' && $k !='datos_laborales' && $k !='representante_legal'){ //catalogos
                            $val = $val['nombre']; //catalogos
                           }elseif ($k == 'datos_laborales') {
                             foreach ($val as $n =>$v) {
                               $vars[strtolower($key.'_'.$k.'_'.$n)] = $v;
                               if($n == "comida_dentro"){
                                $vars[strtolower($key.'_'.$k.'_'.$n)] = ($v) ? 'dentro':'fuera';
                              }
                             }
                          }elseif ($k == 'nombre_completo') {
                            $vars[strtolower($key.'_'.$k)] = $val;

                           }elseif ($k == 'representante_legal') {
                             foreach ($val as $n =>$v) {
                               $vars[strtolower($key.'_'.$k.'_'.$n)] = $v;
                             }
                           }
                        }
                      }elseif(gettype($val)== 'string'){
                        $pos = strpos($k,'fecha');
                        if ($pos !== false){
                          $val = $this->formatoFecha($val);
                        }
                      // }else{
                      }
                      $vars[strtolower($key.'_'.$k)] = $val;
                    }
                  }
                }
              }else{
                $vars[strtolower('solicitud_'.$key)] = $dato;
              }
            }
            $vars[strtolower('fecha_actual')] = $this->formatoFecha(Carbon::now(),1);
            $vars[strtolower('hora_actual')] = $this->formatoFecha(Carbon::now(),2);
          }
          $vars = Arr::except($vars, ['conciliador_persona']);
        $style = "<html xmlns=\"http://www.w3.org/1999/html\">
                  <head>
                  <style>
                  thead { display: table-header-group }
                  tfoot { display: table-row-group }
                  tr { page-break-inside: avoid }
                  #contenedor-firma {height: 60px;}
                  body {
                        margin-left: 1cm;
                        margin-right: 1cm;
                  }
                  </style>
                  </head>
                  <body>
                  ";
          $end = "</body></html>";

          // $config = PlantillaDocumento::orderBy('created_at', 'desc')->first();
          $config = PlantillaDocumento::find($idPlantilla);
          if (!$config) {
              $body = view('documentos._body_documentos_default');
              $body = '<div class="body">' . $body . '</div>';
          } else {
              $body = '<div class="body">' . $config->plantilla_body . '</div>';
          }
          $blade = $style . $body . $end;
          $html = StringTemplate::renderPlantillaPlaceholders($blade, $vars);
          return $html;
    }

    /**
     * Dado un ID de plantilla obtiene el header del documento
     * @param $id
     * @return string
     * @throws \Symfony\Component\Debug\Exception\FatalThrowableError
     */
    public function getHeader($id)
    {
        $html = '';
        $config = PlantillaDocumento::find($id);
        $html = '<!DOCTYPE html> <html> <head> <meta charset="utf-8"> </head> <body>';
        if(!$config){
            $html .= view('documentos._header_documentos_default');
        }
        else{
            // $html = '<!DOCTYPE html> <html> <head> <meta charset="utf-8"> </head> <body>';
            $html .= $config->plantilla_header;
            // $html .= "</body></html>";
        }
        $html .= "</body></html>";
        return StringTemplate::renderPlantillaPlaceholders($html,[]);
    }

    /**
     * Dado un ID de plantilla obtiene el footer del documento
     * @param $id
     * @return string
     * @throws \Symfony\Component\Debug\Exception\FatalThrowableError
     */
    public function getFooter($id)
    {
        $html = '';
        $config = PlantillaDocumento::find($id);
        $html = '<!DOCTYPE html> <html> <head> <meta charset="utf-8"> </head> <body>';
        if(!$config){
            $html .= view('documentos._footer_documentos_default');
        }
        else{
            $html .= $config->plantilla_footer;
          }
        $html .= "</body></html>";
        return StringTemplate::renderPlantillaPlaceholders($html,[]);
    }

    private function getDataModelos($idAudiencia,$idSolicitud, $idPlantilla, $idSolicitante, $idSolicitado,$idConciliador)
    {

        try {
            $plantilla = PlantillaDocumento::find($idPlantilla);
            $tipo_plantilla = TipoDocumento::find($plantilla->tipo_documento_id);
            $objetos = explode (",", $tipo_plantilla->objetos);
            $path = base_path('database/datafiles');
            $jsonElementos = json_decode(file_get_contents($path . "/elemento_documentos.json"),true);
            $idBase = "";
            $audienciaId = $idAudiencia;
            $data = [];
            $solicitud = "";
            foreach ($objetos as $objeto) {
              foreach ($jsonElementos['datos'] as $key=>$element) {
                if($element['id']==$objeto){
                  $model_name = 'App\\' . $element['objeto'];
                  $model = $element['objeto'];
                  $model_name = 'App\\' .$model;
                  if($model == 'Solicitud' ){
                    $solicitud = $model_name::with('estatusSolicitud','objeto_solicitudes')->find($idSolicitud);
                    // $solicitud = $model_name::with('estatusSolicitud','objeto_solicitudes')->first();
                    $objeto = new JsonResponse($solicitud);
                    $obj = json_decode($objeto->content(),true);
                    $idBase = intval($obj['id']);
                    $centroId = intval($obj['centro_id']);
                    $obj = Arr::except($obj, ['id','updated_at','created_at','deleted_at']);
                    $obj['prescripcion'] = $this->calcularPrescripcion($solicitud->objeto_solicitudes, $solicitud->fecha_conflicto,$solicitud->fecha_ratificacion);
                    $obj['fecha_maxima_ratificacion'] = $this->calcularFechaMaximaRatificacion($solicitud->fecha_recepcion,15);
                    $data = ['solicitud' => $obj];
                  }elseif ($model == 'Parte') {
                    if($idSolicitante != "" || $idSolicitado != ""){
                      $partes = $model_name::with('nacionalidad','domicilios','lenguaIndigena','tipoDiscapacidad','documentos.clasificacionArchivo.entidad_emisora')->where('solicitud_id',intval($idBase))->whereIn('id',[$idSolicitante, $idSolicitado])->get();
                    }else{
                      $partes = $model_name::with('nacionalidad','domicilios','lenguaIndigena','tipoDiscapacidad','documentos.clasificacionArchivo.entidad_emisora')->where('solicitud_id',intval($idBase))->get();
                    }
                    $objeto = new JsonResponse($partes);
                    $obj = json_decode($objeto->content(),true);
                    $parte2 = [];
                    $parte1 = [];
                    $countSolicitante = 0;
                    $countSolicitado = 0;
                    $datoLaboral="";
                      // $partes = $model_name::with('nacionalidad','domicilios','lenguaIndigena','tipoDiscapacidad')->findOrFail(1);
                    foreach ($obj as $parte ) {
                      if( sizeof($parte['documentos']) > 0 ){
                        foreach ($parte['documentos'] as $k => $docu) {
                          if($docu['clasificacion_archivo']['tipo_archivo_id'] == 1){ //tipo identificacion
                            $parte['identificacion_documento'] = ($docu['clasificacion_archivo']['nombre'] != null ) ? $docu['clasificacion_archivo']['nombre']: "--";
                            $parte['identificacion_expedida_por'] = ($docu['clasificacion_archivo']['entidad_emisora']['nombre']!= null ) ? $docu['clasificacion_archivo']['entidad_emisora']['nombre']: "---";
                          }
                        }
                      }else{
                        $parte['identificacion_documento'] = "---";
                        $parte['identificacion_expedida_por'] = "---";
                      }
                      //$parte['datos_laborales'] = $datoLaboral;
                      $parteId = $parte['id'];

                      $parte = Arr::except($parte, ['id','updated_at','created_at','deleted_at']);
                      $parte['datos_laborales'] = $datoLaboral;
                      if($parte['tipo_persona_id'] == 1){ //fisica
                        $parte['nombre_completo'] = $parte['nombre'].' '.$parte['primer_apellido'].' '.$parte['segundo_apellido'];
                      }else{//moral
                        $parte['nombre_completo'] = $parte['nombre_comercial'];
                      }
                      if($parte['tipo_parte_id'] == 1 ){//Solicitante
                        //datos laborales del solicitante
                        $datoLaborales = DatoLaboral::with('jornada','ocupacion')->where('parte_id', $parteId)->get();
                        $hayDatosLaborales = count($datoLaborales);
                        if($hayDatosLaborales>1){
                          $datoLaborales =$datoLaborales->where('resolucion',true)->first();
                        }else{
                          $datoLaborales =$datoLaborales->where('resolucion',false)->first();
                        }
                        // $datoLaboral = DatoLaboral::with('jornada','ocupacion')->where('parte_id', $parteId)->get();
                        if($hayDatosLaborales >0){
                          $salarioMensual = ($datoLaborales->remuneracion / $datoLaborales->periodicidad->dias)*30;
                          $objeto = new JsonResponse($datoLaborales);
                          $datoLaboral = json_decode($objeto->content(),true);
                          $datoLaboral = Arr::except($datoLaboral, ['id','updated_at','created_at','deleted_at']);
                          $parte['datos_laborales'] = $datoLaboral;
                          $parte['datos_laborales_salario_mensual'] = $salarioMensual;
                        }
                        array_push($parte1, $parte);
                        $countSolicitante += 1;
                      }elseif ($parte['tipo_parte_id'] == 2 ) {//Solicitado
                        //representante legal solicitado
                        $representanteLegal = Parte::where('parte_representada_id', $parteId)->where('tipo_parte_id',3)->get();
                        if(count($representanteLegal) > 0){
                          $objeto = new JsonResponse($representanteLegal);
                          $representanteLegal = json_decode($objeto->content(),true);
                          $representanteLegal = Arr::except($representanteLegal[0], ['id','updated_at','created_at','deleted_at']);
                          $representanteLegal['nombre_completo'] = $representanteLegal['nombre'].' '.$representanteLegal['primer_apellido'].' '.$representanteLegal['segundo_apellido'];
                          $parte['representante_legal'] = $representanteLegal;
                        }
                          //tipoNotificacion solicitado
                        if($audienciaId!=""){
                          $audienciaParte = AudienciaParte::with('tipo_notificacion')->where('audiencia_id',$audienciaId)->where('parte_id',$parteId)->get();
                          // $audienciaParte = AudienciaParte::with('tipo_notificacion')->where('audiencia_id',$audienciaId)->where('tipo_notificacion_id','<>',null)->get();
                          $parte['tipo_notificacion'] = $audienciaParte[0]->tipo_notificacion_id;
                        }
                          // $data = Arr::add( $data, 'solicitado', $parte );
                        $countSolicitado += 1;

                        array_push($parte2, $parte);
                      }
                    }
                    $data = Arr::add( $data, 'solicitante', $parte1 );
                    $data = Arr::add( $data, 'solicitado', $parte2 );
                    $data = Arr::add( $data, 'total_solicitantes', $countSolicitante );
                    $data = Arr::add( $data, 'total_solicitados', $countSolicitado );
                }elseif ($model == 'Expediente') {
                    $expediente = Expediente::where('solicitud_id', $idBase)->get();
                    $expedienteId = $expediente[0]->id;
                    $objeto = new JsonResponse($expediente);
                    $expediente = json_decode($objeto->content(),true);
                    $expediente = Arr::except($expediente[0], ['id','updated_at','created_at','deleted_at']);
                    $data = Arr::add( $data, 'expediente', $expediente );
                  }elseif ($model == 'Audiencia') {
                  if($solicitud!="" && $solicitud->estatus_solicitud_id != 1){
                    $expediente = Expediente::where('solicitud_id', $idBase)->get();
                    $expedienteId = $expediente[0]->id;

                    $objeto = new JsonResponse($expediente);
                    $expediente = json_decode($objeto->content(),true);
                    $expediente = Arr::except($expediente[0], ['id','updated_at','created_at','deleted_at']);
                    $data = Arr::add( $data, 'expediente', $expediente );

                    // $objeto = $model_name::with('conciliador')->findOrFail(1);
                    $audiencias = $model_name::where('expediente_id',$expedienteId)->get();
                    $conciliadorId = $audiencias[0]->conciliador_id;
                    $objeto = new JsonResponse($audiencias);
                    $audiencias = json_decode($objeto->content(),true);
                    $Audiencias = [];
                    foreach ($audiencias as $audiencia ) {
                      if($audienciaId == ""){
                        $audienciaId = $audiencia['id'];
                      }
                      $resolucionAudienciaId = $audiencia['resolucion_id'];
                      $audiencia = Arr::except($audiencia, ['id','updated_at','created_at','deleted_at']);
                      array_push($Audiencias,$audiencia);
                    }

                    $data = Arr::add( $data, 'audiencia', $Audiencias );
                    $salaAudiencia = SalaAudiencia::with('sala')->where('audiencia_id',$audienciaId)->get();
                    $objSala = new JsonResponse($salaAudiencia);
                    $salaAudiencia = json_decode($objSala->content(),true);
                    $salas = [];
                    foreach ($salaAudiencia as $sala ) {
                      $sala = Arr::except($sala, ['id','updated_at','created_at','deleted_at']);
                      $sala['nombre'] = $sala['sala']['sala'];
                      array_push($salas,$sala);
                    }
                    $data = Arr::add( $data, 'sala', $salas );
                  }
                }elseif ($model == 'Conciliador') {
                    $objeto = $model_name::with('persona')->find($conciliadorId);
                    $objeto = new JsonResponse($objeto);
                    $conciliador = json_decode($objeto->content(),true);
                    $conciliador = Arr::except($conciliador, ['id','updated_at','created_at','deleted_at']);
                    $conciliador['persona'] = Arr::except($conciliador['persona'], ['id','updated_at','created_at','deleted_at']);
                    $data = Arr::add( $data, 'conciliador', $conciliador );
                  }elseif ($model == 'Centro') {
                    $objeto = $model_name::with('domicilio')->find($centroId);
                    $dom_centro = $objeto->domicilio;
                    $objeto = new JsonResponse($objeto);
                    $centro = json_decode($objeto->content(),true);
                    $centro = Arr::except($centro, ['id','updated_at','created_at','deleted_at']);
                    $dom_centro = new JsonResponse($dom_centro);
                    $dom_centro = json_decode($dom_centro->content(),true);
                    $centro['domicilio'] = Arr::except($dom_centro, ['id','updated_at','created_at','deleted_at','domiciliable_id','domiciliable_type']); 
                    $tipo_vialidad =  ($dom_centro['tipo_vialidad'] !== null)? $dom_centro['tipo_vialidad'] :"";
                    $vialidad =  ($dom_centro['vialidad'] !== null)? $dom_centro['vialidad'] :"";
                    $num_ext =  ($dom_centro['num_ext'] !== null)? "No." . $dom_centro['num_ext'] :"";
                    $municipio =  ($dom_centro['municipio'] !== null)? $dom_centro['municipio'] :"";
                    $estado =  ($dom_centro['estado'] !== null)? $dom_centro['estado'] :"";
                    $centro['domicilio_completo'] = strtoupper($tipo_vialidad.' '.$vialidad.' '.$num_ext.', '.$municipio.', '.$estado);
                    $data = Arr::add( $data, 'centro', $centro );
                  }elseif ($model == 'Resolucion') {
                    $objetoResolucion = $model_name::find($resolucionAudienciaId);
                    $datosResolucion=[];
                    $etapas_resolucion = EtapaResolucionAudiencia::where('audiencia_id',$audienciaId)->whereIn('etapa_resolucion_id',[3,4,5])->get();
                    $objeto = new JsonResponse($etapas_resolucion);
                    $etapas_resolucion = json_decode($objeto->content(),true);
                    $datosResolucion['resolucion']= $objetoResolucion->nombre;
                    foreach ($etapas_resolucion as $asd => $etapa ) {
                      if($etapa['etapa_resolucion_id'] == 3){
                        $datosResolucion['primera_manifestacion']= $etapa['evidencia'];
                      }else if($etapa['etapa_resolucion_id'] == 4){
                        $datosResolucion['justificacion_propuesta']= $etapa['evidencia'];
                        $resolucion_partes = ResolucionPartes::where('audiencia_id',$audienciaId)->first();
                        $resolucionParteId = $resolucion_partes->id;

                        // $diasPeriodicidad = Periodicidad::where('id', $datoLaborales->periodicidad_id)->first();
                        // $remuneracionDiaria = $datoLaborales->remuneracion / $diasPeriodicidad->dias;
                        $remuneracionDiaria = $datoLaborales->remuneracion / $datoLaborales->periodicidad->dias;
                        $anios_antiguedad = Carbon::parse($datoLaborales->fecha_ingreso)->floatDiffInYears($datoLaborales->fecha_salida);
                        $propVacaciones = $anios_antiguedad - floor($anios_antiguedad);
                        $salarios = SalarioMinimo::get('salario_minimo');
                        $salarioMinimo = $salarios[0]->salario_minimo;
                        $anioSalida = Carbon::parse($datoLaborales->fecha_salida)->startOfYear();
                        $propAguinaldo = Carbon::parse($anioSalida)->floatDiffInYears($datoLaborales->fecha_salida);
                        $vacacionesPorAnio = VacacionesAnio::all();
                        $diasVacaciones = 0;
                        foreach ($vacacionesPorAnio as $key => $vacaciones) {
                            if($vacaciones->anios_laborados >= $anios_antiguedad ){
                                $diasVacaciones = $vacaciones->dias_vacaciones;
                                break;
                            }
                        }
                        $pagoVacaciones = $propVacaciones * $diasVacaciones * $remuneracionDiaria;
                        $salarioTopado = ($remuneracionDiaria > (2*$salarioMinimo) ? (2*$salarioMinimo) : $remuneracionDiaria);

                        //Propuesta de convenio al 100%
                        $prouestas = [];
                        array_push($prouestas,array("concepto_pago"=> 'Indemnización constitucional', "montoCompleta"=>round($remuneracionDiaria * 90,2), "montoAl50"=>round($remuneracionDiaria * 45,2) )); //Indemnizacion constitucional = gratificacion A
                        array_push($prouestas,array("concepto_pago"=> 'Aguinaldo', "montoCompleta"=>round($remuneracionDiaria * 15 * $propAguinaldo,2) ,  "montoAl50"=>round($remuneracionDiaria * 15 * $propAguinaldo,2) )); //Aguinaldo = dias de aguinaldo
                        array_push($prouestas,array("concepto_pago"=> 'Vacaciones', "montoCompleta"=>round($pagoVacaciones,2), "montoAl50"=>round($pagoVacaciones,2))); //Vacaciones = dias vacaciones
                        array_push($prouestas,array("concepto_pago"=> 'Prima vacacional', "montoCompleta"=>round($pagoVacaciones * 0.25,2), "montoAl50"=>round($pagoVacaciones * 0.25,2) )); //Prima Vacacional
                        array_push($prouestas,array("concepto_pago"=> 'Prima antigüedad', "montoCompleta"=>round($salarioTopado * $anios_antiguedad *12,2), "montoAl50"=>round($salarioTopado * $anios_antiguedad *6,2) )); //Prima antiguedad = gratificacion C

                        // $tablaConceptos = '<h4>Propuestas</h4>';
                        $tablaConceptos = '<style> .tbl, .tbl th, .tbl td {border: .5px dotted black; border-collapse: collapse; padding:3px;} .amount{ text-align:right} </style>';
                        $tablaConceptos .= '<table  class="tbl">';
                        $tablaConceptos .= '<thead><tr><th>Prestación</th><th>Propuesta completa</th><th>Propuesta 45 días</th></tr></thead>';
                        $tablaConceptos .= '<tbody >';

                        $total50 = 0;
                        $total100 = 0;
                        foreach ($prouestas as $concepto ) {
                          $tablaConceptos .= '<tr><td class="tbl">'.$concepto['concepto_pago'].'</td><td class="amount"> $'.$concepto['montoCompleta'].'</td><td class="amount"> $'.$concepto['montoAl50'].'</td> </tr>';
                          $total100 += floatval($concepto['montoCompleta'] );
                          $total50 += floatval($concepto['montoAl50'] );
                        }
                        $tablaConceptos .= '<tr ><th class="tbl"> TOTAL </th><td class="amount"> $'.$total100.'</td><td class="amount"> $'.$total50.'</td> </tr>';
                        $tablaConceptos .= '</tbody>';
                        $tablaConceptos .= '</table>';

                        // $tablaConceptos .= '<h4>Propuesta Configurada </h4>';
                        $resolucion_conceptos = ResolucionParteConcepto::where('resolucion_partes_id',$resolucionParteId)->get();
                        $tablaConceptosEConvenio = '';
                        $tablaConceptosConvenio = '<style> .tbl, .tbl th, .tbl td {border: .5px dotted black; border-collapse: collapse; padding:3px;} .amount{ text-align:right} </style>';
                        $tablaConceptosConvenio .= '<table class="tbl">';
                        $tablaConceptosConvenio .= '<tbody>';
                        $totalPercepciones = 0;
                        foreach ($resolucion_conceptos as $concepto ) {
                          $conceptoName = ConceptoPagoResolucion::select('nombre')->find($concepto->concepto_pago_resoluciones_id);
                          if($concepto->id != 9){
                            $totalPercepciones += ($concepto->monto!= null ) ? floatval($concepto->monto) : 0;
                            $tablaConceptosConvenio .= '<tr><td class="tbl"> '.$conceptoName->nombre.' </td><td style="text-align:right;">     $'.$concepto->monto.'</td></tr>';
                          }else{
                            $tablaConceptosEConvenio .= $concepto->otro;
                          }
                        }
                        $tablaConceptosConvenio .= '<tr><td> Total de percepciones </td><td>     $'.$totalPercepciones.'</td></tr>';
                        $tablaConceptosConvenio .= '</tbody>';
                        $tablaConceptosConvenio .= '</table>';
                        $tablaConceptosConvenio .= ($tablaConceptosEConvenio!='') ? '<p>Adicionalmente las partes acordaron que la parte <b>EMPLEADORA</b> entregar&aacute; a la parte <b>TRABAJADORA</b> '.$tablaConceptosEConvenio.'</p>':'';
                        $cantidadTextual = (new NumberFormatter("es", NumberFormatter::SPELLOUT))->format((float)$totalPercepciones);
                        $cantidadTextual = str_replace("uno","un",$cantidadTextual);
                        $cantidadTextual = str_replace("coma","punto",$cantidadTextual);
                        $datosResolucion['total_percepciones']= $totalPercepciones;
                        $datosResolucion['total_percepciones_letra']= $cantidadTextual;
                        $datosResolucion['propuestas_conceptos']= $tablaConceptos;
                        $datosResolucion['propuesta_configurada']= $tablaConceptosConvenio;
                      }else if($etapa['etapa_resolucion_id'] == 5){
                        $datosResolucion['segunda_manifestacion']= $etapa['evidencia'];
                      }
                    }
                    $datosResolucion['primera_manifestacion'] = (isset($datosResolucion['primera_manifestacion']))? $datosResolucion['primera_manifestacion'] :"";
                    $datosResolucion['segunda_manifestacion'] = (isset($datosResolucion['segunda_manifestacion']))? $datosResolucion['segunda_manifestacion'] :"";
                    $datosResolucion['total_percepciones'] = (isset($datosResolucion['total_percepciones']))? $datosResolucion['total_percepciones'] :"";
                    $datosResolucion['propuestas_conceptos'] = (isset($datosResolucion['propuestas_conceptos']))? $datosResolucion['propuestas_conceptos'] :"";
                    $datosResolucion['propuesta_configurada'] = (isset($datosResolucion['propuesta_configurada']))? $datosResolucion['propuesta_configurada'] :"";
                    $data = Arr::add( $data, $model, $datosResolucion );
                  }else{
                    $objeto = $model_name::first();
                    $objeto = new JsonResponse($objeto);
                    $otro = json_decode($objeto->content(),true);
                    $otro = Arr::except($otro, ['id','updated_at','created_at','deleted_at']);
                    $data = Arr::add( $data, $model , $otro );
                  }
                }
              }
            }
            // dd($data);
            return $data;
          } catch (\Throwable $th) {
            return $data;
          }
    }
    /*
    Calcular posible prescripcion de derechos 
      */
    private function calcularPrescripcion($objetoSolicitud,$fechaConflicto,$fechaRatificacion)
    {
      try {
        $prescripcion = 'N/A';
        foreach ($objetoSolicitud as $key => $objeto) {
          if($objeto->tipo_objeto_solicitudes_id == 1){
            $prescripcion = 'No';
            if($objeto->id == 1 || $objeto->id == 4) {//Despido o derechos de preferencia
                $meses = Carbon::parse($fechaConflicto)->diffInMonths($fechaRatificacion);
                $prescripcion = ($meses > 2) ? 'Si' : $prescripcion;
            }else if ($objeto->id == 2 || $objeto->id == 5 || $objeto->id == 6){//Pago prestaciones o derecho de antiguiedad o derecho de acenso
                $anios = Carbon::parse($fechaConflicto)->floatDiffInYears($fechaRatificacion);
                $prescripcion = ($anios > 1) ? 'Si': $prescripcion;
            }else if($objeto->id == 3){//Resicion de relacion laboral
                $meses = Carbon::parse($fechaConflicto)->diffInMonths($fechaRatificacion);
                $prescripcion = ($meses > 1) ? 'Si': $prescripcion;
            }
          }
        }
        return $prescripcion;
      } catch (\Throwable $th) {
        return "";
      }  
    }
    /*
    Calcular la fecha m'axima para ratificar la solicitud (3 dias maximo)
      */
    private function calcularFechaMaximaRatificacion($fechaRecepcion,$centroId)
    {
      try {
        $ndia=0;
        $diasDisponibilidad = [];
        $disponibilidad_centro = Disponibilidad::select('dia')->where('disponibilidad_type','App\\Centro')->where('disponibilidad_id',$centroId)->get();
        foreach ($disponibilidad_centro as $disponibilidad) { //dias de disponibilidad del centro
          array_push($diasDisponibilidad,$disponibilidad->dia);
        }
        while ($ndia <= 3) {
          $fechaRecepcion = Carbon::parse($fechaRecepcion); 
          if($ndia<3){ 
            $fechaRecepcion = $fechaRecepcion->addDay();//sumar dia a fecha recepcion
            $dayOfTheWeek = $fechaRecepcion->dayOfWeek; //dia de la semana de la fecha de recepcion
          }
          $k = array_search($dayOfTheWeek, $diasDisponibilidad);
          if (false !== $k) { //si dia agregado es dia disponble en centro
            $ndia+=1;
          }
        }
        //Do,lu,ma,mi,ju,vi,sa
        // 0,1,2,3,4,5,6 
        return $fechaRecepcion->toDateTimeString();
      } catch (\Throwable $th) {
        return "";
      }  
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
    private function formatoFecha($fecha,$tipo=null)
    {
      try {
        $monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio","Julio", "Agosto", "Septiembre", "Octubre", "Noivembre", "Diciembre"];
        $hh= "";
        if(strpos($fecha, " ") ){
          $date = explode(' ', $fecha);
          $fecha = $date[0];
          $hr = explode(':', $date[1]);
          $hh = $hr[0].':'.$hr[1];
        }
        $fecha = explode('-', $fecha);
        $dd = $fecha[2];
        $mm = $fecha[1];
        $yy = $fecha[0];
        if($tipo == 1){ //fecha sin hr
          $ddmmyy = $dd.' de '. $monthNames[intval($mm)-1]. ' de ' . $yy;
        }else if($tipo == 2){ //hr
          $ddmmyy = $hh;
        }else{ //fecha y hora
          $ddmmyy = $dd.' de '. $monthNames[intval($mm)-1]. ' de ' . $yy .' '. $hh;
        }
        // $ddmmyy = $dd.' de '. $monthNames[intval($mm)-1]. ' de ' . $yy .' '. $hh ;
        return $ddmmyy;
      } catch (\Throwable $th) {
        return "";
      }  
    }

    /**
     * Genera el archivo PDF.
     * @param $html string HTML fuente para generar el PDF
     * @param $plantilla_id integer ID de la plantilla en la BD
     * @param $path string Ruta del archivo a guardar. Si no existe entonces regresa el PDF inline para mostrar en browser
     * @ToDo  Agregar opciones desde variable de ambiente como tamaño de página, margen, etc.
     * @return mixed
     */
    public function renderPDF($html, $plantilla_id, $path=null){
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setOption('page-size', 'Letter')
            ->setOption('margin-top', '25mm')
            ->setOption('margin-bottom', '11mm')
            ->setOption('header-html', env('APP_URL').'/header/'.$plantilla_id)
            ->setOption('footer-html', env('APP_URL').'/footer/'.$plantilla_id)
        ;
        if($path){
            return $pdf->generateFromHtml($html, $path);
        }
        return $pdf->inline();
    }
}
