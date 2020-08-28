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

trait GenerateDocument
{
    /**
     * Generar documento a partir de un modelo y de una plantilla
     * @return mixed
     */
    public function generarConstancia($idAudiencia, $idSolicitud, $clasificacion_id,$plantilla_id, $idSolicitante = null, $idSolicitado = null, $idConciliador)
    {
        $plantilla = PlantillaDocumento::find($plantilla_id);
        if($plantilla != null){
            if($idAudiencia != ""){

                $padre = Audiencia::find($idAudiencia);
                $directorio = 'expedientes/' . $padre->expediente_id . '/audiencias/' . $idAudiencia;
                $algo = Storage::makeDirectory($directorio, 0775, true);
                
                $tipoArchivo = ClasificacionArchivo::find($clasificacion_id);
                
                $html = $this->renderDocumento($idAudiencia,$idSolicitud, $plantilla->id, $idSolicitante, $idSolicitado,$idConciliador);
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
                $directorio = 'expedientes/' . $padre->expediente->id . '/solicitud/' . $idSolicitud;
                $algo = Storage::makeDirectory($directorio, 0775, true);
                
                $tipoArchivo = ClasificacionArchivo::find($clasificacion_id);
                
                $html = $this->renderDocumento($idAudiencia,$idSolicitud, $plantilla->id, $idSolicitante, $idSolicitado,$idConciliador);
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
                        }
                      }
                    }elseif(gettype($val)== 'string'){
                      $pos = strpos($k,'fecha');
                      if ($pos !== false){
                        $val = $this->formatoFecha($val);
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
                  @page { margin: 165px 50px 39px 60px;
                        }
                  @media print {
                    table { border-collapse: collapse;
                          width: 59.1193%;
                          height: 122px;
                          border-color: #e61f0b;
                          border-style: solid;
                          float: right; }
                          tr:nth-child(even) {background-color: #f2f2f2;
                          }
                          p{
                            font-family: Montserrat, sans-serif; font-size: 10pt;
                          }
                    }
                  .header { position: fixed; top: -150px;}
                  .footer { position: fixed; bottom: 35px;}
                  #contenedor-firma {height: 60px;}
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
                    $data = ['solicitud' => $obj];
                  }elseif ($model == 'Parte') {
                    $partes = $model_name::with('nacionalidad','domicilios','lenguaIndigena','tipoDiscapacidad')->where('solicitud_id',intval($idBase))->get();
                    $objeto = new JsonResponse($partes);
                    $obj = json_decode($objeto->content(),true);
                    $parte2 = [];
                    $parte1 = [];
                    $countSolicitante = 0;
                    $countSolicitado = 0;
                    $datoLaboral="";
                      // $partes = $model_name::with('nacionalidad','domicilios','lenguaIndigena','tipoDiscapacidad')->findOrFail(1);
                    foreach ($obj as $parte ) {
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
                       $objeto = new JsonResponse($datoLaborales);
                        $datoLaboral = json_decode($objeto->content(),true);
                        $datoLaboral = Arr::except($datoLaboral, ['id','updated_at','created_at','deleted_at']);
                        $parte['datos_laborales'] = $datoLaboral;
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
                    $objeto = $model_name::find($centroId);
                    $objeto = new JsonResponse($objeto);
                    $centro = json_decode($objeto->content(),true);
                    $centro = Arr::except($centro, ['id','updated_at','created_at','deleted_at']);
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

                        $diasPeriodicidad = Periodicidad::where('id', $datoLaborales->periodicidad_id)->first();
                        $remuneracionDiaria = $datoLaborales->remuneracion / $diasPeriodicidad->dias;
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
                        $tablaConceptosConvenio = '<style> .tbl, .tbl th, .tbl td {border: .5px dotted black; border-collapse: collapse; padding:3px;} .amount{ text-align:right} </style>';
                        $tablaConceptosConvenio .= '<table class="tbl">';
                        $tablaConceptosConvenio .= '<tbody>';
                        $totalPercepciones = 0;
                        foreach ($resolucion_conceptos as $concepto ) {
                          $totalPercepciones += ($concepto->monto!= null ) ? floatval($concepto->monto) : 0;
                          $conceptoName = ConceptoPagoResolucion::select('nombre')->find($concepto->concepto_pago_resoluciones_id);
                          $tablaConceptosConvenio .= '<tr><td class="tbl"> '.$conceptoName->nombre.' </td><td style="text-align:right;">     $'.$concepto->monto.'</td></tr>';
                        }
                        $tablaConceptosConvenio .= '<tr><td> Total de percepciones </td><td>     $'.$totalPercepciones.'</td></tr>';
                        $tablaConceptosConvenio .= '</tbody>';
                        $tablaConceptosConvenio .= '</table>';
                        $datosResolucion['total_percepciones']= $totalPercepciones;
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
