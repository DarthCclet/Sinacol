<?php

namespace App\Traits;

use App\Audiencia;
use App\AudienciaParte;
use App\Centro;
use App\ClasificacionArchivo;
use App\Compareciente;
use App\ConceptoPagoResolucion;
use App\DatoLaboral;
use App\Disponibilidad;
use App\Documento;
use App\EtapaResolucionAudiencia;
use App\Expediente;
use App\FirmaDocumento;
use App\Parte;
use App\Periodicidad;
use App\PlantillaDocumento;
use App\ResolucionPagoDiferido;
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
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Events\RatificacionRealizada;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait GenerateDocument
{
    /**
     * Generar documento a partir de un modelo y de una plantilla
     * @return mixed
     */
    public function generarConstancia($idAudiencia, $idSolicitud, $clasificacion_id,$plantilla_id, $idSolicitante = null, $idSolicitado = null,$idDocumento = null)
    {
    $plantilla = PlantillaDocumento::find($plantilla_id);
        if($plantilla != null){
            if($idAudiencia != ""){

                $padre = Audiencia::find($idAudiencia);
                $directorio = 'expedientes/' . $padre->expediente_id . '/audiencias/' . $idAudiencia;
                $algo = Storage::makeDirectory($directorio, 0775, true);

                $tipoArchivo = ClasificacionArchivo::find($clasificacion_id);

                //Creamos el registro
                $uuid = Str::uuid();
                if($idDocumento != null){
                  $archivo = Documento::find($idDocumento);
                  if(Storage::exists($archivo->ruta)){
                    Storage::delete($archivo->ruta.".old");
                    Storage::move($archivo->ruta, $archivo->ruta.".old");
                  }
                }else{
                  $archivo = $padre->documentos()->create(["descripcion" => "Documento de audiencia " . $tipoArchivo->nombre,"uuid"=>$uuid,"clasificacion_archivo_id" => $tipoArchivo->id]);
                }
                //generamos html del archivo
                $html = $this->renderDocumento($idAudiencia,$idSolicitud, $plantilla->id, $idSolicitante, $idSolicitado,$archivo->id);
                $firmantes = substr_count($html, 'class="qr"');
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
                    "total_firmantes" => $firmantes,
                ]);
                if($tipoArchivo->id == 18){
                    event(new RatificacionRealizada($padre->id,"multa"));
                }
            }else{
                $padre = Solicitud::find($idSolicitud);
                if($padre->expediente != null){
                  $directorio = 'expedientes/' . $padre->expediente->id . '/solicitud/' . $idSolicitud;
                }else{
                  $directorio = 'solicitudes/' . $idSolicitud;
                }
                $algo = Storage::makeDirectory($directorio, 0775, true);

                $tipoArchivo = ClasificacionArchivo::find($clasificacion_id);

                //Creamos el registro
                $uuid = Str::uuid();
                if($idDocumento != null){
                  $archivo = Documento::find($idDocumento);
                  if(Storage::exists($archivo->ruta)){
                    Storage::delete($archivo->ruta.".old");
                    Storage::move($archivo->ruta, $archivo->ruta.".old");
                  }
                }else{
                  $archivo = $padre->documentos()->create(["descripcion" => "Documento de audiencia " . $tipoArchivo->nombre,"uuid"=>$uuid,"clasificacion_archivo_id" => $tipoArchivo->id]);
                }
                $html = $this->renderDocumento($idAudiencia,$idSolicitud, $plantilla->id, $idSolicitante, $idSolicitado,$archivo->id);
                $firmantes = substr_count($html, 'class="qr"');
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
                    "total_firmantes" => $firmantes,
                ]);
            }
            return 'Guardado correctamente';
        }
        else{
            return 'No existe plantilla';
        }
    }

    public function renderDocumento($idAudiencia,$idSolicitud, $idPlantilla, $idSolicitante, $idSolicitado,$idDocumento)
    {
      $vars = [];
      $data = $this->getDataModelos($idAudiencia,$idSolicitud, $idPlantilla, $idSolicitante, $idSolicitado,$idDocumento);
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
                      if( !$isArrayAssoc ){//objeto_solicitudes
                        $names =[];
                        foreach ($val as $i => $v) {
                          if( isset($v['nombre'] ) ){
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
                      $val = ($val === null && $val !== false)? "--" : $val;
                      if(gettype($val)== "boolean"){
                        $val = ($val == false)? 'No' : 'Si';
                      }elseif(gettype($val)== 'array'){
                        $isArrayAssoc = Arr::isAssoc($val);
                        if( !$isArrayAssoc ){ // with
                          if($k == 'domicilios'){
                            $val = Arr::except($val[0],['id','updated_at','created_at','deleted_at','domiciliable_type','domiciliable_id','hora_atencion_de','hora_atencion_a','georeferenciable','tipo_vialidad_id','tipo_asentamiento_id']);
                            foreach ($val as $n =>$v) {
                              // $vars[strtolower($key.'_'.$k.'_'.$n)] = $v;
                              $vars[strtolower($key.'_'.$k.'_'.$n)] = ($v === null)? "" : $v;
                            }
                          }else if($k =='contactos'){
                            foreach ($val as $n =>$v) {
                              // dd($data);
                              $v = Arr::except($v,['id','updated_at','created_at','deleted_at','contactable_type','contactable_id']);
                              $vars[strtolower($key.'_'.$k.'_'.$v['tipo_contacto']['nombre'])] = ($v['contacto'] !== null)? $v['contacto'] :'-';
                              if($v['tipo_contacto_id'] == 3 && $data['correo_buzon'] == null){
                                $vars[$key.'_correo_buzon'] = $v['contacto'];
                                $vars[$key.'_password_buzon'] = '';
                              }
                            }
                          }else{
                            $names =[];
                            foreach ($val as $i => $v) {
                              if( isset($v['nombre'] ) ){
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
                                if($n == "comida_dentro"){
                                  $vars[strtolower($key.'_'.$k.'_'.$n)] = ($v) ? 'dentro':'fuera';
                                }
                                // $pos = strpos($n,'fecha');
                                // if ($pos !== false && $v != "--"){
                                //   $v = Carbon::createFromFormat('Y-m-d',$v)->format('d/m/Y');
                                // }
                                $vars[strtolower($key.'_'.$k.'_'.$n)]  = $v;
                             }
                          }elseif ($k == 'nombre_completo') {
                            $vars[strtolower($key.'_'.$k)] = $val;

                           }elseif ($k == 'representante_legal') {
                             foreach ($val as $n =>$v) {
                               $vars[strtolower($key.'_'.$k.'_'.$n)] = ($v!="") ? $v:'';
                             }
                           }
                        }
                      }elseif(gettype($val)== 'string'){
                        $pos = strpos($k,'fecha');
                        if ($pos !== false && $val != "--"){
                          $val = $this->formatoFecha($val,1);
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
          //dd($vars);
          $vars = Arr::except($vars, ['conciliador_persona']);
          $style = "<html xmlns=\"http://www.w3.org/1999/html\">
                  <head>
                  <style>
                  thead { display: table-header-group }
                  tfoot { display: table-row-group }
                  tr { page-break-inside: avoid }
                  #contenedor-firma {height: 5px;}
                  .firma-llave-publica {text-align: center; font-size: xx-small; max-width: 1024px; overflow-wrap: break-word;}
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

    private function getDataModelos($idAudiencia,$idSolicitud, $idPlantilla, $idSolicitante, $idSolicitado,$idDocumento)
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
            $solicitudVirtual = "";
            $conciliadorId = "";
            $centroId = "";
            $tipoSolicitud = "";
            foreach ($objetos as $objeto) {
              foreach ($jsonElementos['datos'] as $key=>$element) {
                if($element['id']==$objeto){
                  $model_name = 'App\\' . $element['objeto'];
                  $model = $element['objeto'];
                  $model_name = 'App\\' .$model;
                  if($model == 'Solicitud' ){
                    $solicitud = $model_name::with('estatusSolicitud','objeto_solicitudes')->find($idSolicitud);

                    // $solicitud = $model_name::with('estatusSolicitud','objeto_solicitudes')->first();
                    $solicitudVirtual = $solicitud->virtual;
                    $tipoSolicitud = $solicitud->tipo_solicitud_id;
                    $objeto = new JsonResponse($solicitud);
                    $obj = json_decode($objeto->content(),true);
                    $idBase = intval($obj['id']);
                    if($solicitud->resuelveOficinaCentral() && $idPlantilla != 6){
                      $centroId = Centro::where('central',true)->first()->id;
                    }else{
                      $centroId = intval($obj['centro_id']);
                    }
                    $obj['tipo_solicitud'] =  mb_strtoupper(($obj['tipo_solicitud_id'] == 1) ? "Individual" :  (($obj['tipo_solicitud_id'] == 2) ? "Patronal Individual" : (($obj['tipo_solicitud_id'] == 3) ? "Patronal Colectiva" : "Sindical")));
                    $obj['prescripcion'] = $this->calcularPrescripcion($solicitud->objeto_solicitudes, $solicitud->fecha_conflicto,$solicitud->fecha_ratificacion);
                    $obj['fecha_maxima_ratificacion'] = $this->calcularFechaMaximaRatificacion($solicitud->fecha_recepcion,$centroId);
                    $obj = Arr::except($obj, ['id','updated_at','created_at','deleted_at','tipo_solicitud_id']);
                    $data = ['solicitud' => $obj];
                  }elseif ($model == 'Parte') {
                    if($idSolicitante != "" && $idSolicitado != ""){
                      $partes = $model_name::with('nacionalidad','domicilios','lenguaIndigena','tipoDiscapacidad','documentos.clasificacionArchivo.entidad_emisora','contactos.tipo_contacto','tipoParte','compareciente')->where('solicitud_id',intval($idBase))->whereIn('id',[$idSolicitante,$idSolicitado])->get();
                    }else if($idSolicitante != "" && $idSolicitado == ""){
                      $partes = $model_name::with('nacionalidad','domicilios','lenguaIndigena','tipoDiscapacidad','documentos.clasificacionArchivo.entidad_emisora','contactos.tipo_contacto','tipoParte','compareciente')->where('solicitud_id',intval($idBase))->whereRaw('(id=? or tipo_parte_id<>?)',[$idSolicitante,1])->get();
                    }else if($idSolicitante == "" && $idSolicitado != ""){
                      $partes = $model_name::with('nacionalidad','domicilios','lenguaIndigena','tipoDiscapacidad','documentos.clasificacionArchivo.entidad_emisora','contactos.tipo_contacto','tipoParte','compareciente')->where('solicitud_id',intval($idBase))->whereRaw('(id=? or tipo_parte_id=?)',[$idSolicitado,1])->get();
                    }else{
                      $partes = $model_name::with('nacionalidad','domicilios','lenguaIndigena','tipoDiscapacidad','documentos.clasificacionArchivo.entidad_emisora','contactos.tipo_contacto','tipoParte','compareciente')->where('solicitud_id',intval($idBase))->get();
                    }
                    if($idDocumento){
                      foreach($partes as $parteaFirma){
                        if($idAudiencia){
                          $existe = $parteaFirma->firmas()->where('audiencia_id',$idAudiencia)->where('solicitud_id',$idSolicitud)->where('plantilla_id',$idPlantilla)->where('documento_id',$idDocumento)->first();
                        }else{
                          $existe = $parteaFirma->firmas()->where('solicitud_id',$idSolicitud)->where('plantilla_id',$idPlantilla)->where('documento_id',$idDocumento)->first();
                        }
                        if($existe == null){
                          if($idAudiencia){
                            $parteaFirma->firmas()->create(['audiencia_id'=>$idAudiencia,'solicitud_id'=>$idSolicitud,'plantilla_id'=>$idPlantilla,'documento_id'=>$idDocumento]);
                          }else{
                            $parteaFirma->firmas()->create(['solicitud_id'=>$idSolicitud,'plantilla_id'=>$idPlantilla,'documento_id'=>$idDocumento]);
                          }
                        }
                      }
                    }

                    $objeto = new JsonResponse($partes);
                    $obj = json_decode($objeto->content(),true);
                    $parte2 = [];
                    $parte1 = [];
                    $countSolicitante = 0;
                    $countSolicitado = 0;
                    $nombresSolicitantes = [];
                    $nombresSolicitados = [];
                    $solicitantesIdentificaciones = [];
                    $datoLaboral="";
                    $solicitanteIdentificacion = "";
                    $firmasPartesQR="";
                    // $partes = $model_name::with('nacionalidad','domicilios','lenguaIndigena','tipoDiscapacidad')->findOrFail(1);
                    foreach ($obj as $key=>$parte ) {
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
                      //$idAudiencia,$idSolicitud, $idPlantilla, $idSolicitante, $idSolicitado,$idConciliador
                      $tipoParte = ($parte['tipo_parte_id'] == 1) ? 'solicitante':'citado';
                      $firmaDocumento = null;
                      if($idDocumento){
                        if($idAudiencia =="" ){
                          $firmaDocumento = FirmaDocumento::where('firmable_id',$parteId)->where('plantilla_id',$idPlantilla)->where('solicitud_id',$idBase)->where('documento_id',$idDocumento)->first();
                        }else{
                          $firmaDocumento = FirmaDocumento::where('firmable_id',$parteId)->where('plantilla_id',$idPlantilla)->where('audiencia_id',$idAudiencia)->where('documento_id',$idDocumento)->first();
                        }
                      }
                      if($solicitudVirtual && $solicitudVirtual!="" && $idDocumento){
                        if($firmaDocumento && $firmaDocumento->firma != null && $firmaDocumento->tipo_firma == 'autografa'){
                          $parte['qr_firma'] = '<div style="text-align:center" class="qr"> <img style="max-height:80px" src="'.$firmaDocumento->firma.'" /></div>';
                        } elseif ($firmaDocumento && $firmaDocumento->firma != null && ($firmaDocumento->tipo_firma == 'llave-publica' || $firmaDocumento->tipo_firma == '' )){
                          $parte['qr_firma'] = '<div style="text-align:center" class="firma-llave-publica">Firma Digital: '.$this->splitFirma($firmaDocumento->firma).'</div>';
                        } else{
                          $parte['qr_firma'] = '<div style="text-align:center" class="qr">'.QrCode::errorCorrection('H')->size(100)->generate($parteId."/".$tipoParte."/".urlencode($parte['nombre_completo'])."/".$audienciaId."/".$idSolicitud."/".$idPlantilla."/".$idDocumento ."/".$idSolicitante ."/".$idSolicitado."/".$firmaDocumento->id).'</div>';
                        }
                        if($parte['tipo_persona_id']==1 && count($parte['compareciente']) > 0){
                          $siFirma= true;
                          if($idPlantilla == 2 && $parte['tipo_parte_id']!=1){
                            $parte_solicitada = $parteId;
                            if($parte['tipo_parte_id'] == 3){
                              $parte_solicitada = $parte['parte_representada_id'];
                            }
                            $resolucionParteRepresentada = ResolucionPartes::where('audiencia_id',$audienciaId)->where('parte_solicitada_id',$parte_solicitada)->first();
                            if($resolucionParteRepresentada && $resolucionParteRepresentada->terminacion_bilateral_id !=3){
                              $siFirma=false;
                            }
                          }
                          if($siFirma){
                            $firmasPartesQR .= '<p style="text-align: center;"><span style="font-size: 10pt;">'.$parte['qr_firma'].' </span></p>';
                            $firmasPartesQR .= '<p style="text-align: center;"><span style="font-size: 10pt;">_________________________________________</span></p>';
                            $firmasPartesQR .= '<p style="text-align: center;"><strong><span style="font-size: 10pt;">'.mb_strtoupper($parte['nombre_completo']).'</span></strong></p>';
                            $firmasPartesQR .= '<p style="text-align: center;">&nbsp;</p>';
                          }
                        }
                      }else{
                        $parte['qr_firma'] = "";
                      }
                      //domicilio de partes, excepto representante
                      if($parte['tipo_parte_id'] != 3 ){
                        $dom_parte = $parte['domicilios'][0];
                        $tipo_vialidad =  ($dom_parte['tipo_vialidad'] !== null)? $dom_parte['tipo_vialidad'] :"";
                        $vialidad =  ($dom_parte['vialidad'] !== null)? $dom_parte['vialidad'] :"";
                        $num_ext =  ($dom_parte['num_ext'] !== null)? "No. " . $dom_parte['num_ext'] :"";
                        $num_int =  ($dom_parte['num_int'] !== null)? " Int. " . $dom_parte['num_int'] :"";
                        $num =  $num_int.$num_ext;
                        $municipio =  ($dom_parte['municipio'] !== null)? $dom_parte['municipio'] :"";
                        $estado =  ($dom_parte['estado'] !== null)? $dom_parte['estado'] :"";
                        $parte['domicilios_completo'] = mb_strtoupper($tipo_vialidad.' '.$vialidad.' '.$num.', '.$municipio.', '.$estado);
                      }

                      // if($parte['tipo_parte_id'] == 1 ){//Solicitante
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
                          $salarioMensual = round( (($datoLaborales->remuneracion / $datoLaborales->periodicidad->dias)*30),2);
                          $salarioMensual =number_format($salarioMensual, 2, '.', '');
                          $salario = explode('.', $salarioMensual);
                          $intSalarioMensual = $salario[0];
                          $decSalarioMensual = $salario[1];
                          $intSalarioMensualTextual = (new NumberFormatter("es", NumberFormatter::SPELLOUT))->format((float)$intSalarioMensual);
                          $intSalarioMensualTextual = str_replace("uno","un",$intSalarioMensualTextual);
                          $salarioMensualTextual = $intSalarioMensualTextual.' pesos '. $decSalarioMensual.'/100';

                          // $salarioMensual = ($datoLaborales->remuneracion / $datoLaborales->periodicidad->dias)*30;
                          // $salarioMensualTextual = (new NumberFormatter("es", NumberFormatter::SPELLOUT))->format((float)$salarioMensual);
                          // $salarioMensualTextual = str_replace("uno","un",$salarioMensualTextual);
                          // $salarioMensualTextual = str_replace("coma","punto",$salarioMensualTextual);
                          $objeto = new JsonResponse($datoLaborales);
                          $datoLaboral = json_decode($objeto->content(),true);
                          $datoLaboral = Arr::except($datoLaboral, ['id','updated_at','created_at','deleted_at']);
                          $parte['datos_laborales'] = $datoLaboral;
                          $parte['datos_laborales_salario_mensual'] = $salarioMensual;
                          $parte['datos_laborales_salario_mensual_letra'] = $salarioMensualTextual;
                        }
                        $solicitanteIdentificacion = $parte['nombre_completo'] ." quien se identifica con " .$parte['identificacion_documento']." expedida a su favor por ". $parte['identificacion_expedida_por'];
                        // array_push($solicitantesIdentificaciones, $solicitanteIdentificacion);
                        // array_push($parte1, $parte);
                        // array_push($nombresSolicitantes, $parte['nombre_completo'] );
                        // $countSolicitante += 1;
                      // }elseif ($parte['tipo_parte_id'] == 2 ) {//Citado
                        //representante legal solicitado
                        $representanteLegal = Parte::with('documentos.clasificacionArchivo.entidad_emisora')->where('parte_representada_id', $parteId)->where('tipo_parte_id',3)->get();
                        if(count($representanteLegal) > 0){
                          $parte['asistencia'] =  (count($representanteLegal[0]->compareciente)>0) ? 'Si':'No';
                          $objeto = new JsonResponse($representanteLegal);
                          $representanteLegal = json_decode($objeto->content(),true);
                          $representanteLegal = Arr::except($representanteLegal[0], ['id','updated_at','created_at','deleted_at']);
                          $representanteLegal['nombre_completo'] = $representanteLegal['nombre'].' '.$representanteLegal['primer_apellido'].' '.$representanteLegal['segundo_apellido'];
                          if( sizeof($representanteLegal['documentos']) > 0 ){
                            foreach ($representanteLegal['documentos'] as $k => $docu) {
                              if($docu['clasificacion_archivo']['tipo_archivo_id'] == 1){ //tipo identificacion
                                $representanteLegal['identificacion_documento'] = ($docu['clasificacion_archivo']['nombre'] != null ) ? $docu['clasificacion_archivo']['nombre']: "--";
                                $representanteLegal['identificacion_expedida_por'] = ($docu['clasificacion_archivo']['entidad_emisora']['nombre']!= null ) ? $docu['clasificacion_archivo']['entidad_emisora']['nombre']: "---";
                              }
                            }
                          }else{
                            $representanteLegal['identificacion_documento'] = "---";
                            $representanteLegal['identificacion_expedida_por'] = "---";
                          }
                          $parte['representante_legal'] = $representanteLegal;
                        }else{
                          $parte['asistencia'] =  (count($parte['compareciente'])>0) ? 'Si':'No';
                        }
                          //tipoNotificacion solicitado
                        if($audienciaId!=""){
                          $audienciaParte = AudienciaParte::with('tipo_notificacion')->where('audiencia_id',$audienciaId)->where('parte_id',$parteId)->get();
                          if(count($audienciaParte)>0){
                            // $audienciaParte = AudienciaParte::with('tipo_notificacion')->where('audiencia_id',$audienciaId)->where('tipo_notificacion_id','<>',null)->get();
                            $parte['tipo_notificacion'] = $audienciaParte[0]->tipo_notificacion_id;
                            $parte['fecha_notificacion'] = $audienciaParte[0]->fecha_notificacion;
                            //$parte['fecha_notificacion'] = ($audienciaParte[0]->fecha_notificacion!= null) ? $audienciaParte[0]->fecha_notificacion : "--";
                          }else{
                            $parte['tipo_notificacion'] = null;
                            $parte['fecha_notificacion'] = "";
                          }
                        }
                        if($parte['tipo_parte_id'] == 1 ){//Solicitante
                          array_push($parte1, $parte);
                          array_push($nombresSolicitantes, $parte['nombre_completo'] );
                          array_push($solicitantesIdentificaciones, $solicitanteIdentificacion);
                          $countSolicitante += 1;
                        }

                        if ($parte['tipo_parte_id'] == 2 ) {//Citado
                          // $data = Arr::add( $data, 'solicitado', $parte );
                          $countSolicitado += 1;
                          array_push($nombresSolicitados, $parte['nombre_completo'] );
                          array_push($parte2, $parte);
                        }
                    }
                    $data = Arr::add( $data, 'solicitante', $parte1 );
                    $data = Arr::add( $data, 'solicitado', $parte2 );
                    $data = Arr::add( $data, 'total_solicitantes', $countSolicitante );
                    $data = Arr::add( $data, 'total_solicitados', $countSolicitado );
                    $data = Arr::add( $data, 'nombres_solicitantes', implode(", ",$nombresSolicitantes));
                    $data = Arr::add( $data, 'nombres_solicitados', implode(", ",$nombresSolicitados));
                    $data = Arr::add( $data, 'solicitantes_identificaciones', implode(", ",$solicitantesIdentificaciones));
                    $data = Arr::add( $data, 'firmas_partes_qr', $firmasPartesQR);
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
                    //$audiencias = $model_name::where('expediente_id',$expedienteId)->get();
                    $audiencia = $model_name::where('id',$audienciaId)->get();
                    $conciliadorId = $audiencia[0]->conciliador_id;
                    $objeto = new JsonResponse($audiencia);
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
                      $sala['nombre'] = $sala['sala']['sala'];
                      $sala = Arr::except($sala, ['id','updated_at','created_at','deleted_at','sala']);
                      array_push($salas,$sala);
                    }
                    $data = Arr::add( $data, 'sala', $salas );
                  }
                }elseif ($model == 'Conciliador') {
                    if($conciliadorId != ""){
                      $objeto = $model_name::with('persona')->find($conciliadorId);
                      if($idDocumento){
                        if($idAudiencia){
                          $existe = $objeto->firmas()->where('audiencia_id',$idAudiencia)->where('solicitud_id',$idSolicitud)->where('plantilla_id',$idPlantilla)->where('documento_id',$idDocumento)->first();
                        }else{
                          $existe = $objeto->firmas()->where('solicitud_id',$idSolicitud)->where('plantilla_id',$idPlantilla)->where('documento_id',$idDocumento)->first();
                        }
                        if($existe == null){
                          if($idAudiencia){
                            $objeto->firmas()->create(['audiencia_id'=>$idAudiencia,'solicitud_id'=>$idSolicitud,'plantilla_id'=>$idPlantilla,'documento_id'=>$idDocumento]);
                          }else{
                            $objeto->firmas()->create(['solicitud_id'=>$idSolicitud,'plantilla_id'=>$idPlantilla,'documento_id'=>$idDocumento]);
                          }
                        }
                      }
                      $objeto = new JsonResponse($objeto);
                      $conciliador = json_decode($objeto->content(),true);
                      $conciliador = Arr::except($conciliador, ['id','updated_at','created_at','deleted_at']);
                      $conciliador['persona'] = Arr::except($conciliador['persona'], ['id','updated_at','created_at','deleted_at']);
                      $nombreConciliador = $conciliador['persona']['nombre']." ".$conciliador['persona']['primer_apellido']." ".$conciliador['persona']['segundo_apellido'];
                      if($solicitudVirtual && $solicitudVirtual!="" && $idDocumento){
                        $firmaDocumento = FirmaDocumento::where('firmable_id',$conciliadorId)->where('plantilla_id',$idPlantilla)->where('audiencia_id',$idAudiencia)->where('documento_id',$idDocumento)->first();
                          if($firmaDocumento != null && $firmaDocumento->firma != null && $firmaDocumento->tipo_firma == 'autografa'){
                            $conciliador['qr_firma'] = '<div style="text-align:center" class="qr"> <img style="max-height:80px" src="'.$firmaDocumento->firma.'" /></div>';
                          } elseif ($firmaDocumento != null && $firmaDocumento->firma != null && ($firmaDocumento->tipo_firma == 'llave-publica' || $firmaDocumento->tipo_firma == '' )){
                            $conciliador['qr_firma'] = '<div style="text-align:center" class="firma-llave-publica">Firma Digital: '.$this->splitFirma($firmaDocumento->firma).'</div>';
                          }else{
                            $conciliador['qr_firma'] = '<div style="text-align:center" class="qr">'.QrCode::errorCorrection('H')->size(100)->generate($conciliadorId."/conciliador/".urlencode($nombreConciliador)."/".$audienciaId."/".$idSolicitud."/".$idPlantilla."/".$idDocumento ."/".$idSolicitante ."/".$idSolicitado."/".$firmaDocumento->id).'</div>';
                          }
                      }else{
                        $conciliador['qr_firma'] = '';
                      }
                      $data = Arr::add( $data, 'conciliador', $conciliador );
                    }
                  }elseif ($model == 'Centro') {
                    $objeto = $model_name::with('domicilio','disponibilidades','contactos')->find($centroId);
                    $dom_centro = $objeto->domicilio;
                    $usuarios_centro = $objeto->user;
                    $contacto_centro = $objeto->contactos;
                    $disponibilidad_centro = $objeto->disponibilidades;
                    $objeto = new JsonResponse($objeto);
                    $centro = json_decode($objeto->content(),true);
                    $centro = Arr::except($centro, ['id','updated_at','created_at','deleted_at']);
                    $dom_centro = new JsonResponse($dom_centro);
                    $dom_centro = json_decode($dom_centro->content(),true);
                    $centro['domicilio'] = Arr::except($dom_centro, ['id','updated_at','created_at','deleted_at','domiciliable_id','domiciliable_type']);
                    $tipo_vialidad =  ($dom_centro['tipo_vialidad'] !== null)? $dom_centro['tipo_vialidad'] :"";
                    $vialidad =  ($dom_centro['vialidad'] !== null)? $tipo_vialidad." ". $dom_centro['vialidad'] :"";
                    $num_ext =  ($dom_centro['num_ext'] !== null)? " No. " . $dom_centro['num_ext'] :"";
                    $num_int =  ($dom_centro['num_int'] !== null)? " Int. " . $dom_centro['num_int'] :"";
                    $num = $num_ext . $num_int;
                    $colonia =  ($dom_centro['asentamiento'] !== null)? $dom_centro['tipo_asentamiento']." ". $dom_centro['asentamiento']." "  :"";
                    $municipio =  ($dom_centro['municipio'] !== null)? $colonia . $dom_centro['municipio'] :"";
                    $estado =  ($dom_centro['estado'] !== null)? $dom_centro['estado'] :"";
                    $centro['domicilio_completo'] = mb_strtoupper($vialidad. $num.', '.$municipio.', '.$estado);
                    $contacto_centro = new JsonResponse($contacto_centro);
                    $contacto_centro = json_decode($contacto_centro->content(),true);
                    foreach ($contacto_centro as $contacto ) {
                      if($contacto['tipo_contacto_id'] == 1 || $contacto['tipo_contacto_id'] == 2 ){
                        $centro['telefono'] = $contacto['contacto'];
                      }else{
                        $centro['telefono'] = '--- -- -- ---';
                      }
                    }
                    $nombreAdministrador = "";
                    $personaId = "";
                    $userAdmin = null;
                    foreach ($usuarios_centro as $usuario ) {
                      if($usuario->hasRole('Administrador del centro')){
                        $userAdmin = $usuario->persona;
                        $personaId= $userAdmin->id;
                        $nombreAdministrador = $userAdmin['nombre'].' '.$userAdmin['primer_apellido'].' '.$userAdmin['segundo_apellido'];
                      }
                    }
                    $centro['nombre_administrador'] = $nombreAdministrador;
                    //Firma conciliador generico
                    $solicitudFirma = Solicitud::find($idSolicitud);
                    if($idDocumento){
                      if($idAudiencia){
                        $existe = $solicitudFirma->firmas()->where('audiencia_id',$idAudiencia)->where('solicitud_id',$idSolicitud)->where('plantilla_id',$idPlantilla)->where('documento_id',$idDocumento)->first();
                      }else{
                        $existe = $solicitudFirma->firmas()->where('solicitud_id',$idSolicitud)->where('plantilla_id',$idPlantilla)->where('documento_id',$idDocumento)->first();
                      }
                      if($existe == null){
                        if($idAudiencia){
                          $solicitudFirma->firmas()->create(['audiencia_id'=>$idAudiencia,'solicitud_id'=>$idSolicitud,'plantilla_id'=>$idPlantilla,'documento_id'=>$idDocumento]);
                        }else{
                          $solicitudFirma->firmas()->create(['solicitud_id'=>$idSolicitud,'plantilla_id'=>$idPlantilla,'documento_id'=>$idDocumento]);
                        }
                      }
                    }
                    if($solicitudVirtual && $solicitudVirtual!="" && $idDocumento){
                      if($idAudiencia){
                        $firmaDocumento = FirmaDocumento::where('firmable_id',$idSolicitud)->where('plantilla_id',$idPlantilla)->where('audiencia_id',$idAudiencia)->where('documento_id',$idDocumento)->first();
                      }else{
                        $firmaDocumento = FirmaDocumento::where('firmable_id',$idSolicitud)->where('plantilla_id',$idPlantilla)->where('documento_id',$idDocumento)->first();
                      }
                      if($firmaDocumento != null && $firmaDocumento->firma != null && $firmaDocumento->tipo_firma == 'autografa'){
                          $centro['conciliador_generico_qr_firma'] = '<div style="text-align:center" class="qr"> <img style="max-height:80px" src="'.$firmaDocumento->firma.'" /></div>';
                        } elseif ($firmaDocumento != null && $firmaDocumento->firma != null && ($firmaDocumento->tipo_firma == 'llave-publica' || $firmaDocumento->tipo_firma == '' )){
                          $centro['conciliador_generico_qr_firma'] = '<div style="text-align:center" class="firma-llave-publica">Firma Digital: '.$this->splitFirma($firmaDocumento->firma).'</div>';
                        }else{
                          $centro['conciliador_generico_qr_firma'] = '<div style="text-align:center" class="qr">'.QrCode::errorCorrection('H')->size(100)->generate($idSolicitud."/conciliador//".$audienciaId."/".$idSolicitud."/".$idPlantilla."/".$idDocumento ."/".$idSolicitante ."/".$idSolicitado."/".$firmaDocumento->id).'</div>';
                        }
                    }else{
                      $centro['conciliador_generico_qr_firma'] = '';
                    }

                    //Firma administrador centro
                    if($idDocumento && $userAdmin!=null){
                      if($idAudiencia){
                        $existe = $userAdmin->firmas()->where('audiencia_id',$idAudiencia)->where('solicitud_id',$idSolicitud)->where('plantilla_id',$idPlantilla)->where('documento_id',$idDocumento)->first();
                      }else{
                        $existe = $userAdmin->firmas()->where('solicitud_id',$idSolicitud)->where('plantilla_id',$idPlantilla)->where('documento_id',$idDocumento)->first();
                      }
                      if($existe == null){
                        if($idAudiencia){
                          $userAdmin->firmas()->create(['audiencia_id'=>$idAudiencia,'solicitud_id'=>$idSolicitud,'plantilla_id'=>$idPlantilla,'documento_id'=>$idDocumento]);
                        }else{
                          $userAdmin->firmas()->create(['solicitud_id'=>$idSolicitud,'plantilla_id'=>$idPlantilla,'documento_id'=>$idDocumento]);
                        }
                      }
                    }
                    if($solicitudVirtual && $solicitudVirtual!="" && $idDocumento){
                      if($idAudiencia){
                        $firmaDocumento = FirmaDocumento::where('firmable_id',$personaId)->where('plantilla_id',$idPlantilla)->where('audiencia_id',$idAudiencia)->where('documento_id',$idDocumento)->first();
                      }else{
                        $firmaDocumento = FirmaDocumento::where('firmable_id',$personaId)->where('plantilla_id',$idPlantilla)->where('documento_id',$idDocumento)->first();
                      }
                      //dd($firmaDocumento);
                      if($firmaDocumento != null && $firmaDocumento->firma != null && $firmaDocumento->tipo_firma == 'autografa'){
                          $centro['administrador_qr_firma'] = '<div style="text-align:center" class="qr"> <img style="max-height:80px" src="'.$firmaDocumento->firma.'" /></div>';
                        } elseif ($firmaDocumento != null && $firmaDocumento->firma != null && ($firmaDocumento->tipo_firma == 'llave-publica' || $firmaDocumento->tipo_firma == '' )){
                          $centro['administrador_qr_firma'] = '<div style="text-align:center" class="firma-llave-publica">Firma Digital: '.$this->splitFirma($firmaDocumento->firma).'</div>';
                        }else{
                          $centro['administrador_qr_firma'] = '<div style="text-align:center" class="qr">'.QrCode::errorCorrection('H')->size(100)->generate($personaId."/administrador/".urlencode($nombreAdministrador)."/".$audienciaId."/".$idSolicitud."/".$idPlantilla."/".$idDocumento ."/".$idSolicitante ."/".$idSolicitado."/".$firmaDocumento->id).'</div>';
                        }
                    }else{
                      $centro['administrador_qr_firma'] = '';
                    }
                    //Disponibilidad del centro horarios y dias
                    $disponibilidad_centro = new JsonResponse($disponibilidad_centro);
                    $disponibilidad_centro = json_decode($disponibilidad_centro->content(),true);
                    $centro['hora_inicio']= $this->formatoFecha($disponibilidad_centro[0]['hora_inicio'],3);
                    $centro['hora_fin']= $this->formatoFecha($disponibilidad_centro[0]['hora_fin'],3);
                    $data = Arr::add( $data, 'centro', $centro );
                  }elseif ($model == 'Resolucion') {
                    $objetoResolucion = $model_name::find($resolucionAudienciaId);
                    $datosResolucion=[];
                    $etapas_resolucion = EtapaResolucionAudiencia::where('audiencia_id',$audienciaId)->whereIn('etapa_resolucion_id',[3,4,5,6])->get();
                    $objeto = new JsonResponse($etapas_resolucion);
                    $etapas_resolucion = json_decode($objeto->content(),true);
                    $datosResolucion['resolucion']= $objetoResolucion->nombre;
                    $audiencia_partes = Audiencia::find($audienciaId)->audienciaParte;
                    foreach ($etapas_resolucion as $asd => $etapa ) {
                      if($etapa['etapa_resolucion_id'] == 3){
                        $datosResolucion['primera_manifestacion']= $etapa['evidencia'];
                      }else if($etapa['etapa_resolucion_id'] == 4){
                        $datosResolucion['justificacion_propuesta']= $etapa['evidencia'];
                        $tablaConceptos = '<style> .tbl, .tbl th, .tbl td {border: .5px dotted black; border-collapse: collapse; padding:3px;} .amount{ text-align:right} </style>';
                        $tablaConceptosConvenio = '<style> .tbl, .tbl th, .tbl td {border: .5px dotted black; border-collapse: collapse; padding:3px;} .amount{ text-align:right} </style>';
                        $tablaConceptosActa = '<style> .tbl, .tbl th, .tbl td {border: .5px dotted black; border-collapse: collapse; padding:3px;} .amount{ text-align:right} </style>';
                        $totalPercepciones = 0;
                        $parteID= "";
                        $totalPagosDiferidos = 0;
                        $tablaPagosDiferidos = '<style> .tbl, .tbl th, .tbl td {border: .5px dotted black; border-collapse: collapse; padding:3px;} .amount{ text-align:right} </style>';
                        foreach ($audiencia_partes as $key => $audiencia_parte) {
                          if ($audiencia_parte->parte->tipo_parte_id != 3) {
                            $parteID = $audiencia_parte->parte->id;

                            //datos laborales del solicitante
                            $datoLaborales = DatoLaboral::with('jornada','ocupacion')->where('parte_id', $parteID)->get();
                            $hayDatosLaborales = count($datoLaborales);
                            if($hayDatosLaborales>1){
                              $datoLaborales =$datoLaborales->where('resolucion',true)->first();
                            }else{
                              $datoLaborales =$datoLaborales->where('resolucion',false)->first();
                            }
                            // $datoLaboral = DatoLaboral::with('jornada','ocupacion')->where('parte_id', $parteId)->get();
                            if($hayDatosLaborales >0){  

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

                              //Propuesta de convenio al 100% y 50%
                              $prouestas = [];
                              array_push($prouestas,array("concepto_pago"=> 'Indemnización constitucional', "montoCompleta"=>round($remuneracionDiaria * 90,2), "montoAl50"=>round($remuneracionDiaria * 45,2) )); //Indemnizacion constitucional = gratificacion A
                              array_push($prouestas,array("concepto_pago"=> 'Aguinaldo', "montoCompleta"=>round($remuneracionDiaria * 15 * $propAguinaldo,2) ,  "montoAl50"=>round($remuneracionDiaria * 15 * $propAguinaldo,2) )); //Aguinaldo = dias de aguinaldo
                              array_push($prouestas,array("concepto_pago"=> 'Vacaciones', "montoCompleta"=>round($pagoVacaciones,2), "montoAl50"=>round($pagoVacaciones,2))); //Vacaciones = dias vacaciones
                              array_push($prouestas,array("concepto_pago"=> 'Prima vacacional', "montoCompleta"=>round($pagoVacaciones * 0.25,2), "montoAl50"=>round($pagoVacaciones * 0.25,2) )); //Prima Vacacional
                              array_push($prouestas,array("concepto_pago"=> 'Prima antigüedad', "montoCompleta"=>round($salarioTopado * $anios_antiguedad *12,2), "montoAl50"=>round($salarioTopado * $anios_antiguedad *6,2) )); //Prima antiguedad = gratificacion C

                              // $tablaConceptos = '<h4>Propuestas</h4>';
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

                              //Conceptos resolucion
                              // $tablaConceptos .= '<h4>Propuesta Configurada </h4>';
                              $resolucion_conceptos = ResolucionParteConcepto::where('audiencia_parte_id',$audiencia_parte->id)->get();
                              $tablaConceptosEConvenio = '';
                              $tablaConceptosEActa = '';
                              //$tablaConceptosConvenio = '<style> .tbl, .tbl th, .tbl td {border: .5px dotted black; border-collapse: collapse; padding:3px;} .amount{ text-align:right} </style>';
                              $tablaConceptosConvenio .= '<table class="tbl">';
                              $tablaConceptosConvenio .= '<tbody>';
                              $tablaConceptosActa .= '';
                              $parte = Parte::find($parteID);
                              if(sizeof($parte->compareciente)>0){
                                $nombreParte = $parte['nombre'].' '.$parte['primer_apellido'].' '.$parte['segundo_apellido'];
                                $tablaConceptosActa .= ' Propuesta para '.$nombreParte;
                                $tablaConceptosActa .= '<table class="tbl">';
                                $tablaConceptosActa .= '<tbody>';
                              }

                              $totalPercepciones = 0;
                              foreach ($resolucion_conceptos as $concepto ) {
                                $conceptoName = ConceptoPagoResolucion::select('nombre')->find($concepto->concepto_pago_resoluciones_id);
                                if($concepto->concepto_pago_resoluciones_id != 9){
                                  $totalPercepciones += ($concepto->monto!= null ) ? floatval($concepto->monto) : 0;
                                  if($tipoSolicitud == 1){ //solicitud individual
                                    if($parteID == $idSolicitante){ //si resolucion pertenece al solicitante
                                      $tablaConceptosConvenio .= '<tr><td class="tbl"> '.$conceptoName->nombre.' </td><td style="text-align:right;">     $'.number_format($concepto->monto, 2, '.', ',').'</td></tr>';
                                    }
                                  }else{
                                    if($parteID == $idSolicitado){ //si resolucion pertenece al citado
                                      $tablaConceptosConvenio .= '<tr><td class="tbl"> '.$conceptoName->nombre.' </td><td style="text-align:right;">     $'.number_format($concepto['monto'], 2, '.', ',').'</td></tr>';
                                    }
                                  }
                                  $tablaConceptosActa .= '<tr><td class="tbl"> '.$conceptoName->nombre.' </td><td style="text-align:right;">     $'.number_format($concepto->monto, 2, '.', ',').'</td></tr>';
                                }else{
                                  if($tipoSolicitud == 1){ //solicitud individual
                                    if($parteID == $idSolicitante){ //si resolucion pertenece al solicitante
                                      $tablaConceptosEConvenio .= $concepto->otro.' ';
                                    }
                                  }else{
                                    if($parteID == $idSolicitado){ //si resolucion pertenece al citado
                                      $tablaConceptosEConvenio .= $concepto->otro.' ';
                                    }
                                  }
                                  $tablaConceptosEActa .= $concepto->otro.' ';
                                }
                              }
                              if($tipoSolicitud == 1){ //solicitud individual
                                $tablaConceptosConvenio .= ($parteID == $idSolicitante)?'<tr><td> Total de percepciones </td><td>     $'.number_format($totalPercepciones, 2, '.', ',').'</td></tr>':"";
                              }else{
                                $tablaConceptosConvenio .= ($parteID == $idSolicitado)?'<tr><td> Total de percepciones </td><td>     $'.number_format($totalPercepciones, 2, '.', ',').'</td></tr>':"";
                              }
                              $tablaConceptosConvenio .= '</tbody>';
                              $tablaConceptosConvenio .= '</table>';
                              if($tipoSolicitud == 1){ //solicitud individual
                                if($parteID == $idSolicitante){ //si resolucion pertenece al solicitante
                                  $tablaConceptosConvenio .= ($tablaConceptosEConvenio!='') ? '<p>Adicionalmente las partes acordaron que la parte&nbsp;<b> EMPLEADORA</b> entregar&aacute; a la parte <b>TRABAJADORA</b> '.$tablaConceptosEConvenio.'.</p>':'';
                                }
                              }else{
                                if($parteID == $idSolicitado){ //si resolucion pertenece al citado
                                  $tablaConceptosConvenio .= ($tablaConceptosEConvenio!='') ? '<p>Adicionalmente las partes acordaron que la parte&nbsp;<b> EMPLEADORA</b> entregar&aacute; a la parte <b>TRABAJADORA</b> '.$tablaConceptosEConvenio.'.</p>':'';
                                }
                              }
                              if(sizeof($parte->compareciente)>0){
                                $tablaConceptosActa .= '<tr><td> Total de percepciones </td><td>     $'.number_format($totalPercepciones, 2, '.', ',').'</td></tr>';
                                $tablaConceptosActa .= '</tbody>';
                                $tablaConceptosActa .= '</table>';
                                $tablaConceptosActa .= ($tablaConceptosEActa!='') ? '<p>Adicionalmente las partes acordaron que la parte&nbsp;<b> EMPLEADORA</b> entregar&aacute; a la parte <b>TRABAJADORA</b> '.$nombreParte.' '.$tablaConceptosEActa.'.</p>':'';
                                $tablaConceptosActa .= '<br>';
                              }

                              $totalPercepciones = number_format($totalPercepciones, 2, '.', '');
                              $totalPercepcion = explode('.', $totalPercepciones);
                              $intTotalPercepciones = $totalPercepcion[0];
                              $decTotalPercepciones = $totalPercepcion[1];
                              $intTotalPercepciones = (new NumberFormatter("es", NumberFormatter::SPELLOUT))->format((float)$intTotalPercepciones);
                              $intTotalPercepciones = str_replace("uno","un",$intTotalPercepciones);
                              $cantidadTextual = $intTotalPercepciones.' pesos '. $decTotalPercepciones.'/100';
                              if($tipoSolicitud == 1){ //solicitud individual
                                if($parteID == $idSolicitante ){
                                  $datosResolucion['total_percepciones']= number_format($totalPercepciones, 2, '.', ',');//$totalPercepciones;
                                  $datosResolucion['total_percepciones_letra']= $cantidadTextual;
                                }
                              }else{
                                if($parteID == $idSolicitado ){
                                  $datosResolucion['total_percepciones']= number_format($totalPercepciones, 2, '.', ',');//$totalPercepciones;
                                  $datosResolucion['total_percepciones_letra']= $cantidadTextual;
                                }
                              }
                            }
                            //Fechas pago resolucion
                            $tablaPagosDiferidos .= '<table class="tbl">';
                            $tablaPagosDiferidos .= '<tbody>';
                            $resolucion_pagos = ResolucionPagoDiferido::where('audiencia_id',$audienciaId)->get();

                            foreach ($resolucion_pagos as $pago ) {
                              if($tipoSolicitud == 1){
                                if(($parteID == $pago->solicitante_id) && ($parteID == $idSolicitante)){
                                  $tablaPagosDiferidos .= '<tr><td class="tbl"> '.Carbon::createFromFormat('Y-m-d H:i:s',$pago->fecha_pago)->format('d/m/Y h:i').' horas </td><td style="text-align:right;">     $'.number_format($pago->monto, 2, '.', ',').'</td></tr>';
                                  $totalPagosDiferidos +=1;
                                }
                              }else{
                                if(($parteID == $pago['idCitado']) && ($parteID == $idSolicitado)){
                                  $tablaPagosDiferidos .= '<tr><td class="tbl"> '.$pago['fecha_pago'].' horas </td><td style="text-align:right;">     $'.number_format($pago['monto_pago'], 2, '.', ',').'</td></tr>';
                                  $totalPagosDiferidos +=1;
                                }
                              }
                            }
                            $tablaPagosDiferidos .= '</tbody>';
                            $tablaPagosDiferidos .= '</table>';

                            $datosResolucion['total_diferidos']= $totalPagosDiferidos;
                            $datosResolucion['pagos_diferidos']= $tablaPagosDiferidos;
                          }
                        }
                        // $salarioMensual = round( (($datoLaborales->remuneracion / $datoLaborales->periodicidad->dias)*30),2);
                        // $cantidadTextual = (new NumberFormatter("es", NumberFormatter::SPELLOUT))->format((float)$totalPercepciones);
                        // $cantidadTextual = str_replace("uno","un",$cantidadTextual);
                        // $cantidadTextual = str_replace("coma","punto",$cantidadTextual);
                        $datosResolucion['propuestas_conceptos']= $tablaConceptos;
                        $datosResolucion['propuestas_trabajadores']= $tablaConceptosActa;
                        $datosResolucion['propuesta_configurada']= $tablaConceptosConvenio;
                        $datosResolucion['propuestas_acta']= $tablaConceptosActa;
                      }else if($etapa['etapa_resolucion_id'] == 5){
                        $datosResolucion['segunda_manifestacion']= $etapa['evidencia'];
                      }else if($etapa['etapa_resolucion_id'] == 6){
                        $datosResolucion['descripcion_pagos']= $etapa['evidencia'];
                        //Fechas pago resolucion
                        // $tablaPagosDiferidos = '<style> .tbl, .tbl th, .tbl td {border: .5px dotted black; border-collapse: collapse; padding:3px;} .amount{ text-align:right} </style>';
                        // $tablaPagosDiferidos .= '<table class="tbl">';
                        // $tablaPagosDiferidos .= '<tbody>';
                        // $resolucion_pagos = ResolucionPagoDiferido::where('audiencia_id',$audienciaId)->get();
                        // $totalPagosDiferidos=0;
                        // foreach ($resolucion_pagos as $pago ) {
                        //    $tablaPagosDiferidos .= '<tr><td class="tbl"> '.Carbon::createFromFormat('Y-m-d H:i:s',$pago->fecha_pago)->format('d/m/Y').' </td><td style="text-align:right;">     $'.number_format($pago->monto, 2, '.', ',').'</td></tr>';
                        //    $totalPagosDiferidos +=1;
                        // }
                        // $tablaPagosDiferidos .= '</tbody>';
                        // $tablaPagosDiferidos .= '</table>';
                        // $datosResolucion['total_diferidos']= $totalPagosDiferidos;
                        // $datosResolucion['pagos_diferidos']= $tablaPagosDiferidos;
                      }
                    }

                    // citados que convinieron comparecieron
                    $partes_convenio = Compareciente::where('audiencia_id',$audienciaId)->get();
                    $hayPartesConvenio = count($partes_convenio);
                    if($hayPartesConvenio > 0){
                      $citadosConvenio = [];
                      $clausulacitadosConvenio = [];
                      $solicitantesComparecientes = [];
                      $citadosComparecientes = [];
                      $nombreCitadoConvenio = "";
                      $nombreCitadoComparecientes = "";
                      $nombreSolicitanteComparecientes = "";
                      $idParteCitada = "";
                      $clausula2citadosConvenio = ($hayPartesConvenio >1)? '' : "";
                      foreach ($partes_convenio as $key => $parteConvenio) { 
                        $nombreCitadoComparecientes = "";
                        $nombreSolicitanteComparecientes = "";
                        $nombreCitadoConvenio = "";
                        $clausulaCitadoConvenio = "";
                        //citados convenio
                        $parteC = $parteConvenio->parte;
                        if($parteC->id != $idParteCitada){
                          $idParteCitada = $parteC->id;
                          if($parteC->tipo_persona_id == 1){//fisica
                            if($parteC->tipo_parte_id == 3){//OTRO (representante)
                              $representanteLegalC = $parteC;
                              $parteRepresentada = Parte::find($representanteLegalC->parte_representada_id);
                              $segundo_apellido_representante = ($representanteLegalC['segundo_apellido']!="")?' '.$representanteLegalC['segundo_apellido']:"";
                              $nombreRepresentanteLegal = $representanteLegalC['nombre'].' '.$representanteLegalC['primer_apellido'].$segundo_apellido_representante;
                              $representanteIdentificacion = "--";
                              $documentoRep = $representanteLegalC->documentos;
                              $representanteInstrumento="";
                              $representantePoder="";
                              if( sizeof($documentoRep) > 0 ){
                                foreach ($documentoRep as $k => $docu) {

                                  if($docu->clasificacionArchivo->tipo_archivo_id == 1){ //tipo identificacion
                                    $representanteIdentificacion = ($docu->clasificacionArchivo->nombre != null ) ? " quien se identifica con " .$docu->clasificacionArchivo->nombre: "";
                                  }else if($docu->clasificacionArchivo->tipo_archivo_id == 9){
                                    $representantePoder = ($docu->clasificacionArchivo->nombre != null ) ? " en términos de " .$docu->clasificacionArchivo->nombre . ', poder que a la fecha de este convenio no le ha sido revocado. ' : "";
                                    $representanteInstrumento = ($docu->clasificacionArchivo->nombre != null ) ? " circunstancia que se acredita con " .$docu->clasificacionArchivo->nombre ." ". $representanteLegalC->detalle_instrumento : "";
                                  }
                                }
                              }
                              $nombreRepresentada = ($parteRepresentada['tipo_persona_id']== 2)? $parteRepresentada['nombre_comercial']: $parteRepresentada['nombre'].' '.$parteRepresentada['primer_apellido'] .' '.$parteRepresentada['segundo_apellido'];
                              $resolucionParteRepresentada = ResolucionPartes::where('audiencia_id',$audienciaId)->where('parte_solicitada_id',$parteRepresentada['id'])->first();
                              if($resolucionParteRepresentada && $resolucionParteRepresentada->terminacion_bilateral_id ==3){
                                if($parteRepresentada->tipo_parte_id == 2){ //si representante de citado
                                  $nombreCitadoConvenio = $nombreRepresentada .' representada por '.$nombreRepresentanteLegal .' en carácter de apoderado legal';
                                  $clausulaCitadoConvenio = $nombreRepresentanteLegal. $representanteIdentificacion .', que es apoderado legal de '. $nombreRepresentada .' y que cuenta con facultades suficientes para convenir a nombre de su representada'. $representantePoder ;
                                }
                              }
                              //$nombreCitadoComparecientes = $parteRepresentada['nombre_comercial'].' representada por '.$nombreRepresentanteLegal .' en carácter de apoderado legal';
                              $nombreCitadoComparecientes = ($parteRepresentada->tipo_parte_id == 2)? $nombreRepresentanteLegal .', en su carácter de representante legal de '. $nombreRepresentada . $representanteInstrumento .", ".$representanteIdentificacion:"";
                              $nombreSolicitanteComparecientes = ($parteRepresentada->tipo_parte_id == 1)? $nombreRepresentanteLegal .', en su carácter de representante legal de '. $nombreRepresentada . $representanteInstrumento .", ".$representanteIdentificacion:"";
                            }else{ // Solicitante o Citado
                              //if($parteC->tipo_parte_id == 2){
                                foreach ($parteC->documentos as $k => $docu) {
                                  if($docu->clasificacionArchivo->tipo_archivo_id == 1){ //tipo identificacion
                                    //$parteIdentificacion = ($docu->clasificacionArchivo->nombre != null ) ? " quien se identifica con " .$docu->clasificacionArchivo->nombre: "";
                                    $parteIdentificacion = ($docu->clasificacionArchivo->nombre != null ) ? " quien se identifica con " .$docu->clasificacionArchivo->nombre . " expedida a su favor por ". $docu->clasificacionArchivo->entidad_emisora->nombre: "";
                                  }
                                }
                                $segundo_apellido = ($parteC['segundo_apellido']!="")?' '.$parteC['segundo_apellido']:"";
                              if($parteC->tipo_parte_id == 2){//citados
                                $resolucionParteRepresentada = ResolucionPartes::where('audiencia_id',$audienciaId)->where('parte_solicitada_id',$parteC->id)->first();
                                if($resolucionParteRepresentada && $resolucionParteRepresentada->terminacion_bilateral_id ==3){
                                    $nombreCitadoConvenio = $parteC['nombre'].' '.$parteC['primer_apellido'].$segundo_apellido;
                                    $clausulaCitadoConvenio = $parteC['nombre'].' '.$parteC['primer_apellido'].$segundo_apellido . $parteIdentificacion . '  tener plenas capacidades de goce y ejercicio para convenir el presente instrumento. ';
                                }
                                $nombreCitadoComparecientes = $parteC['nombre'].' '.$parteC['primer_apellido'].$segundo_apellido. $parteIdentificacion;
                              }else{
                                $nombreSolicitanteComparecientes = $parteC['nombre'].' '.$parteC['primer_apellido'].$segundo_apellido . $parteIdentificacion;
                              }
                            }
                          }else{ //moral compareciente
                            $representanteLegalC = Parte::with('documentos.clasificacionArchivo.entidad_emisora')->where('parte_representada_id', $parteC->id)->where('tipo_parte_id',3)->get();
                            $representanteLegalC = $representanteLegalC[0];
                            $segundo_apellido_representante = ($representanteLegalC['segundo_apellido']!="")?' '.$representanteLegalC['segundo_apellido']:"";
                            $nombreRepresentanteLegal = $representanteLegalC['nombre'].' '.$representanteLegalC['primer_apellido'].$segundo_apellido_representante;
                            $representanteIdentificacion = "--";
                            if( sizeof($representanteLegalC['documentos']) > 0 ){
                              foreach ($representanteLegalC['documentos'] as $k => $docu) {
                                if($docu->clasificacionArchivo->tipo_archivo_id == 1){ //tipo identificacion
                                  $representanteIdentificacion = ($docu->clasificacionArchivo->nombre != null ) ? " quien se identifica con " .$docu->clasificacionArchivo->nombre: "";
                                }else if($docu->clasificacionArchivo->tipo_archivo_id == 9){
                                  $representantePoder = ($docu->clasificacionArchivo->nombre != null ) ? " en términos de " .$docu->clasificacionArchivo->nombre . ', poder que a la fecha de este convenio no le ha sido revocado. ' : "";
                                }
                              }
                            }
                            $resolucionParteRepresentada = ResolucionPartes::where('audiencia_id',$audienciaId)->where('parte_solicitada_id',$parteC['id'])->first();
                            if($resolucionParteRepresentada && $resolucionParteRepresentada->terminacion_bilateral_id ==3){
                              $nombreCitadoConvenio = $parteC['nombre_comercial'].' representada por '.$nombreRepresentanteLegal .' en carácter de apoderado legal';
                              $clausulaCitadoConvenio = $nombreRepresentanteLegal. $representanteIdentificacion .', que es apoderado legal de '. $parteC['nombre_comercial'] .' y que cuenta con facultades suficientes para convenir a nombre de su representada'. $representantePoder ;
                            }
                            $nombreCitadoComparecientes = $parteC['nombre_comercial'].' representada por '.$nombreRepresentanteLegal .' en carácter de apoderado legal' ; //$parteIdentificacion
                          }
                          if($clausulaCitadoConvenio != ""){
                            array_push($clausulacitadosConvenio, $clausulaCitadoConvenio );
                          }
                          if($nombreCitadoConvenio != ""){
                            array_push($citadosConvenio, $nombreCitadoConvenio );
                          }
                          if($nombreCitadoComparecientes != ""){
                              array_push($citadosComparecientes, $nombreCitadoComparecientes );
                          }
                          if($nombreSolicitanteComparecientes != ""){
                            array_push($solicitantesComparecientes, $nombreSolicitanteComparecientes );
                          }
                        }
                      }
                      if($hayPartesConvenio > 1){
                        $clausulacitadosConvenioA =  implode(", ",$clausulacitadosConvenio);
                        $clausula2citadosConvenio = $clausulacitadosConvenioA;//$this->lreplace(',', ' y', $citadosConvenioA);

                        $citadosConvenioA =  implode(", ",$citadosConvenio);
                        $nombreCitadosConvenio = $citadosConvenioA;//$this->lreplace(',', ' y', $citadosConvenioA);

                        $citadosConvenioB =  implode(", ",$citadosComparecientes);
                        $nombreCitadosComparecientes = $citadosConvenioB;//$this->lreplace(',', ' y', $citadosConvenioA);

                        $solicitantesB =  implode(", ",$solicitantesComparecientes);
                        $nombreSolicitanteComparecientes = $solicitantesB;//$this->lreplace(',', ' y', $citadosConvenioA);
                      }else{
                        $nombreCitadosConvenio = $nombreCitadoConvenio;
                        $nombreCitadosComparecientes = $nombreCitadoComparecientes;
                      }
                    }else{
                      $nombreCitadosConvenio = "-";
                      $nombreCitadosComparecientes = "";
                      $clausula2citadosConvenio = "";
					          }

                    $datosResolucion['citados_comparecientes'] = $nombreCitadosComparecientes;
                    $datosResolucion['solicitantes_comparecientes'] = $nombreSolicitanteComparecientes;
                    $datosResolucion['citados_convenio'] = $nombreCitadosConvenio;
                    $datosResolucion['segunda_declaracion_convenio'] = $clausula2citadosConvenio;
                    $datosResolucion['primera_manifestacion'] = (isset($datosResolucion['primera_manifestacion']))? $datosResolucion['primera_manifestacion'] :"";
                    $datosResolucion['segunda_manifestacion'] = (isset($datosResolucion['segunda_manifestacion']))? $datosResolucion['segunda_manifestacion'] :"";
                    $datosResolucion['total_percepciones'] = (isset($datosResolucion['total_percepciones']))? $datosResolucion['total_percepciones'] :"";
                    $datosResolucion['propuestas_conceptos'] = (isset($datosResolucion['propuestas_conceptos']))? $datosResolucion['propuestas_conceptos'] :"";
                    $datosResolucion['propuesta_configurada'] = (isset($datosResolucion['propuesta_configurada']))? $datosResolucion['propuesta_configurada'] :"";
                    $datosResolucion['pagos_diferidos'] = (isset($datosResolucion['pagos_diferidos']))? $datosResolucion['pagos_diferidos'] :"";
                    $datosResolucion['total_diferidos'] = (isset($datosResolucion['total_diferidos']))? $datosResolucion['total_diferidos'] :"";
                    $data = Arr::add( $data, $model, $datosResolucion );
                    // dd($data);
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
          } catch (\Throwable $e) {
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            return $data;
          }
    }
    function lreplace($search, $replace, $original){
      $pos = strrpos($original, $search);
      if($pos !== false){
          $subject = substr_replace($original, $replace, $pos, strlen($search));
      }
      return $subject;
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
        $centro = Centro::find($centroId);
        $disponibilidad_centro = $centro->disponibilidades;
        $incidencias_centro = $centro->incidencias;
        foreach ($disponibilidad_centro as $disponibilidad) { //dias de disponibilidad del centro
          array_push($diasDisponibilidad,$disponibilidad->dia);
        }
        while ($ndia <= 3) {
          $fechaRecepcion = Carbon::parse($fechaRecepcion);
          if($ndia<3){
            $fechaRecepcion = $fechaRecepcion->addDay();//sumar dia a fecha recepcion
            $dayOfTheWeek = $fechaRecepcion->dayOfWeek; //dia de la semana de la fecha de recepcion
          }
          $diaHabil = array_search($dayOfTheWeek, $diasDisponibilidad);
          foreach ($incidencias_centro as $incidencia) {
            $diaConIncidencia = $fechaRecepcion->between($incidencia->fecha_inicio,$incidencia->fecha_fin);
          }
          if (false !== $diaHabil && !$diaConIncidencia) { //si dia agregado es dia disponble en centro y no tiene incidencia
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
        if($tipo!=3){ //no es hora
          $monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio","Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
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
          // return $ddmmyy;
        }else{//recibe HH:mm:ss: devuelve hh:mm hr
          $hr = explode(':', $fecha);
          $hh = $hr[0].':'.$hr[1];
          $ddmmyy = $hh;
        }
        return $ddmmyy;
      } catch (\Throwable $th) {
        return "";
      }
    }

    public function splitFirma($firma)
    {
        $aFirma = str_split($firma,150);
        $firma = implode("\n", $aFirma);
        return $firma;
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
