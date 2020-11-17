<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ConfiguracionResponsivasRequest;
use App\Services\StringTemplate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

use App\Filters\CatalogoFilter;
use Barryvdh\DomPDF\PDF;
use Dompdf\Dompdf;
use App\PlantillaDocumento;
use App\TipoDocumento;
use App\Parte;
use App\Centro;
use App\Expediente;
use App\Audiencia;
use App\AudienciaParte;
use App\ClasificacionArchivo;
use App\ConceptoPagoResolucion;
use App\DatoLaboral;
use App\Disponibilidad;
use App\Domicilio;
use App\EtapaResolucion;
use App\EtapaResolucionAudiencia;
use App\Periodicidad;
use App\ResolucionPagoDiferido;
use App\ResolucionParteConcepto;
use App\ResolucionPartes;
use App\SalaAudiencia;
use App\SalarioMinimo;
use App\VacacionesAnio;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use NumberFormatter;

class PlantillasDocumentosController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index()
     {
         $plantilla = (new CatalogoFilter(PlantillaDocumento::query(), $this->request))
             ->searchWith(PlantillaDocumento::class)
             ->filter();
         // Si en el request viene el parametro all entonces regresamos todos los elementos de lo contrario paginamos
         if ($this->request->get('all')) {
             $plantilla = $plantilla->get();
         } else {
             $plantilla = $plantilla->paginate($this->request->get('per_page', 10));
         }

         if ($this->request->wantsJson()) {
             return $this->sendResponse($plantilla, 'SUCCESS');
         }
         return view('documentos.index', compact('plantilla'));
     }

     /**
      * Show the form for creating a new resource.
      *
      * @return \Illuminate\Http\Response
      */
     public function create()
     {
       $tipo_plantillaDoc = TipoDocumento::all();
       $tipo_plantilla = array_pluck($tipo_plantillaDoc,'nombre','id');
       $objetoDocumento = [];
       $path = base_path('database/datafiles');
       $json = json_decode(file_get_contents($path . "/elemento_documentos.json"));
         foreach ($json->datos as $key => $value){
            $columnNames = Schema::getColumnListing($value->tabla);
            $guarded = ['id','updated_at','created_at','deleted_at'];
            foreach ( $guarded as $guard ){
              $k = array_search($guard, $columnNames);
              if (false !== $k) {
                unset($columnNames[$k]);
              }
            }
            $objetoDocumento [] =
              [
                  'objeto' => $value->objeto,
                  'nombre' => $value->nombre,
                  'tabla' => $value->tabla,
                  'campos' => $columnNames
              ];
          }
        $condicionales = $this->getCondicionales();
        return view('documentos.create', compact('objetoDocumento','tipo_plantilla','condicionales'));
     }

     /**
      * Store a newly created resource in storage.
      *
      * @param  \Illuminate\Http\Request  $request
      * @return \Illuminate\Http\Response
      */
     public function store(Request $request)
     {
        $datos = $request->all();
        if ($datos["plantilla-body"]== null) {
            $header = view('documentos._header_documentos_default');
            $body = view('documentos._body_documentos_default');
            $footer = view('documentos._footer_documentos_default');
        }
        else
        {
            $header = $datos["plantilla-header"];
            $body = $datos["plantilla-body"];
            $footer = $datos["plantilla-footer"];
        }
       // $user = Auth::user();
        // $user_id = $user->id;

        $datos["nombre-plantilla"] = $datos["nombre-plantilla"] == "" ? "Plantilla default" : $datos["nombre-plantilla"];
        $datosP['nombre_plantilla'] = $datos["nombre-plantilla"];
        $datosP['tipo_documento_id'] = $datos['tipo-plantilla-id'];
        $datosP['plantilla_header'] = $header;
        $datosP['plantilla_body'] = $body;
        $datosP['plantilla_footer'] = $footer;
        // $datos['user_id'] = $user_id;
        PlantillaDocumento::create($datosP);
        // return response('OK',200);
        return redirect('plantilla-documentos')->with('success', 'Se ha guardado exitosamente');
     }

     /**
      * Display the specified resource.
      *
      * @param  int  $id
      * @return \Illuminate\Http\Response
      */
     public function show($id)
     {
        
     }

     /**
      * Show the form for editing the specified resource.
      *
      * @param  int  $id
      * @return \Illuminate\Http\Response
      */
     public function edit($id)
     {
       $objetoDocumento = [];
       $tipo_plantillaDoc = TipoDocumento::all();
       $tipo_plantilla = array_pluck($tipo_plantillaDoc,'nombre','id');

       $plantillaDocumento = PlantillaDocumento::find($id);
       // $config = PlantillaDocumento::orderBy('created_at', 'desc')->first();
       if (!$plantillaDocumento) {
           $header = view('documentos._header_documentos_default');
           $body = view('documentos._body_documentos_default');
           $footer = view('documentos._footer_documentos_default');

           $plantilla['plantilla_body'] = $body;
       }
       else
       {
          $tipo_plantillaDoc = $tipo_plantillaDoc->where('id', $plantillaDocumento->tipo_documento_id)->first()->getAttributes();
          $objetos = explode (",", $tipo_plantillaDoc['objetos']);
           $header = $plantillaDocumento->plantilla_header;
           $body = $plantillaDocumento->plantilla_body;
           $footer = $plantillaDocumento->plantilla_footer;
           $nombre = $plantillaDocumento->nombre_plantilla;
// dd($plantillaDocumento);
          $objetoDocumento = $this->getObjetoDocumento($objetos);
       }

      //  $objetoDocumento = [];
      //  //Se llena el catalogo desde el arvhivo json elemento_documentos.json
      //  $path = base_path('database/datafiles');
      //  $jsonElementos = json_decode(file_get_contents($path . "/elemento_documentos.json"));
      //  foreach ($objetos as $key => $obj){
      //    foreach ($jsonElementos->datos as $key => $value){
      //      if($value->id == $obj){
      //        $columnNames = Schema::getColumnListing($value->tabla);
      //        $guarded = ['id','updated_at','created_at','deleted_at'];
      //        foreach ( $guarded as $guard ){
      //          $k = array_search($guard, $columnNames);
      //          if (false !== $k) {
      //             unset($columnNames[$k]);
      //          }
      //        }
      //        if($value->nombre =='Solicitud'){
      //          array_push($columnNames,'total_solicitados');
      //          array_push($columnNames,'total_solicitantes');
      //          array_push($columnNames,'objeto_solicitudes');
      //        }
      //        if($value->nombre =='Conciliador'){
      //          $columnPersona = Schema::getColumnListing('personas');
      //          $guarded = ['id','updated_at','created_at','deleted_at'];
      //          foreach ( $guarded as $guard ){
      //            $k = array_search($guard, $columnPersona);
      //            if (false !== $k) {
      //               unset($columnPersona[$k]);
      //            }
      //          }
      //          foreach ($columnPersona as $k => $valPersona) {
      //            array_push($columnNames,$valPersona);
      //          }
      //        }
      //        $objetoDocumento [] =
      //            [
      //                'objeto' => $value->objeto,
      //                'nombre' => $value->nombre,
      //                'tabla' => $value->tabla,
      //                'campos' =>$columnNames
      //            ];
      //      }
      //    }
      //  }
       $condicionales = $this->getCondicionales();
       return view('documentos.edit', compact('plantillaDocumento','objetoDocumento','tipo_plantilla','condicionales'));
       // return view('documentos.edit')->with('plantillaDocumento', $plantilla);
       // return view('documentos.edit',compact('header','body', 'footer','nombre'))->with('plantillaDocumento', $config);
       // return view('documentos.edit', compact('header','body', 'footer','nombre'));
     }

     /**
      * Update the specified resource in storage.
      *
      * @param  \Illuminate\Http\Request  $request
      * @param  int  $id
      * @return \Illuminate\Http\Response
      */
     public function update(Request $request,$id)
     {
         $plantilla = PlantillaDocumento::find($id);
         $datos = $request->all();
         $datos["nombre-plantilla"] = $datos["nombre-plantilla"] == "" ? "Plantilla default" : $datos["nombre-plantilla"];
         $datosP['nombre_plantilla'] = $datos["nombre-plantilla"];
         $datosP['plantilla_header'] = $datos["plantilla-header"];
         $datosP['plantilla_body'] = $datos["plantilla-body"];
         $datosP['plantilla_footer'] = $datos["plantilla-footer"];
         $datosP['tipo_documento_id'] = $datos['tipo-plantilla-id'];

         $plantilla->update($datosP);
         return redirect('plantilla-documentos/'.$id.'/edit')->with('success', 'Se ha actualizado exitosamente');
     }

     /**
      * Remove the specified resource from storage.
      *
      * @param  int  $id
      * @return \Illuminate\Http\Response
      */
     public function destroy($id)
     {
       PlantillaDocumento::find($id)->delete();
       if ($this->request->wantsJson()) {
           return $this->sendResponse($id, 'SUCCESS');
       }
       return redirect()->route('plantilla-documentos.index')->with('success', 'Se ha eliminado exitosamente');
     }

      /**
       * Cargar html default para plantillas.
       *
       * @param
       * @return \Illuminate\Http\Response
       */
      public function cargarDefault()
      {
        // $tipo_plantillaDoc = TipoDocumento::all();
        $tipo_plantilla = array_pluck(TipoDocumento::all(),'nombre','id');

        $plantillaDocumento = new PlantillaDocumento();
        $plantillaDocumento->plantilla_header = view('documentos._header_documentos_default');
        $plantillaDocumento->plantilla_body = view('documentos._body_documentos_default');
        $plantillaDocumento->plantilla_footer = view('documentos._footer_documentos_default');

        $objetoDocumento = [];
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/elemento_documentos.json"));
        //Se llena el catalogo desde el arvhivo json elemento_documentos.json
        foreach ($json->datos as $key => $value){
            $columnNames = Schema::getColumnListing($value->tabla);
            $guarded = ['id','updated_at','created_at','deleted_at'];
            foreach ( $guarded as $guard ){
              $k = array_search($guard, $columnNames);
              if (false !== $k) {
                 unset($columnNames[$k]);
              }
            }
            $objetoDocumento [] =
                [
                    'objeto' => $value->objeto,
                    'nombre' => $value->nombre,
                    'tabla' => $value->tabla,
                    'campos' => $columnNames
                ];
        }
        $condicionales = $this->getCondicionales();
        return view('documentos.create', compact('plantillaDocumento','objetoDocumento','tipo_plantilla','condicionales'));
       }
      /**
       * Funcion para obtener variables de condiciones en el editor
       *
       * @param
       * @return \Illuminate\Http\Response
       */
       private function getCondicionales(){
         $condicionales = [];
         $path = base_path('database/datafiles');
         $json = json_decode(file_get_contents($path . "/condiciones_documento.json"));
         $condicionales = $json->datos;
         return $condicionales;
       }
       /**
       * Obtener las variables de los modelos usados en las plantillas
       * @param
       * @return \Illuminate\Http\Response
       */

       private function getObjetoDocumento($objetos){
        
        $objetoDocumento = [];
        //Se llena el catalogo desde el arvhivo json elemento_documentos.json
        $path = base_path('database/datafiles');
        $jsonElementos = json_decode(file_get_contents($path . "/elemento_documentos.json"));
        foreach ($objetos as $key => $obj){
          foreach ($jsonElementos->datos as $key => $value){
            if($value->id == $obj){
              $columnNames = Schema::getColumnListing($value->tabla);
              $guarded = ['id','updated_at','created_at','deleted_at'];
              foreach ( $guarded as $guard ){
                $k = array_search($guard, $columnNames);
                if (false !== $k) {
                  unset($columnNames[$k]);
                }
              }
              if($value->nombre =='Solicitud'){
                array_push($columnNames,'total_solicitados');
                array_push($columnNames,'total_solicitantes');
                array_push($columnNames,'nombres_solicitados');
                array_push($columnNames,'nombres_solicitantes');
                array_push($columnNames,'objeto_solicitudes');
                array_push($columnNames,'prescripcion');
                array_push($columnNames,'fecha_maxima_ratificacion');
              }
              if($value->objeto =='Parte'){
                if($value->id_tipo  =='1'){ // campos de datos laborales de solicitante
                  $columnDatosLaborales = Schema::getColumnListing('datos_laborales');
                  $guarded = ['id','updated_at','created_at','deleted_at'];
                  foreach ( $guarded as $guard ){
                    $k = array_search($guard, $columnDatosLaborales);
                    if (false !== $k) {
                      unset($columnDatosLaborales[$k]);
                    }
                  }
                  array_push($columnDatosLaborales,'salario_mensual');
                  array_push($columnDatosLaborales,'salario_mensual_letra');
                  //datos laborales de solicitante
                  array_push($columnNames,['nombre'=>'datos_laborales', 'columns'=>$columnDatosLaborales]);
                }
                //representante legal de partes
                $columnPersona = Schema::getColumnListing('personas');
                $guarded = ['id','updated_at','created_at','deleted_at'];
                foreach ( $guarded as $guard ){
                  $k = array_search($guard, $columnPersona);
                  if (false !== $k) {
                     unset($columnPersona[$k]);
                  }
                }
                array_push($columnPersona,'nombre_completo'); //nombre completo representante legal
                //domicilios partes
                $columnDomicilio = Schema::getColumnListing('domicilios');
                $exclude = ['id','updated_at','created_at','deleted_at','domiciliable_type','domiciliable_id','hora_atencion_de','hora_atencion_a','georeferenciable','tipo_vialidad_id','tipo_asentamiento_id'];
                foreach ( $exclude as $exclu ){
                  $k = array_search($exclu, $columnDomicilio);
                  if (false !== $k) {
                     unset($columnDomicilio[$k]);
                  }
                }
                array_push($columnDomicilio,'completo');
                //documentos de identificacion parte
                $columnDocumento = [];
                array_push($columnDocumento,'documento');
                array_push($columnDocumento,'numero');
                array_push($columnDocumento,'expedida_por');
                array_push($columnNames,['nombre'=>'identificacion', 'columns'=>$columnDocumento]);

                // representante_legal de citado
                $colDocumentoRepresentante = [];
                array_push($colDocumentoRepresentante,'identificacion_documento');
                array_push($colDocumentoRepresentante,'identificacion_numero');
                array_push($colDocumentoRepresentante,'identificacion_expedida_por');
                array_push($colDocumentoRepresentante,'detalle_instrumento');
                array_push($columnNames,['nombre'=>'representante_legal', 'columns'=>array_merge($columnPersona,$colDocumentoRepresentante) ]);

                // array_push($columnNames,['nombre'=>'representante_legal', 'columns'=>$columnDocumento]);
                //domicilio de partes
                array_push($columnNames,['nombre'=>'domicilios', 'columns'=>$columnDomicilio]);
                // $representante = Parte::where("parte_representada_id",$id)->where("representante",true)->get();
                array_push($columnNames,'nombre_completo');
                array_push($columnNames,'qr_firma');
                array_push($columnNames,'fecha_notificacion');

              }
              if($value->nombre =='Conciliador'){
                $columnPersona = Schema::getColumnListing('personas');
                $guarded = ['id','updated_at','created_at','deleted_at'];
                foreach ( $guarded as $guard ){
                  $k = array_search($guard, $columnPersona);
                  if (false !== $k) {
                     unset($columnPersona[$k]);
                  }
                }
                foreach ($columnPersona as $k => $valPersona) {
                  array_push($columnNames,$valPersona);
                }
                array_push($columnNames,'nombre_completo');
                array_push($columnNames,'qr_firma');
              }
              if($value->nombre =='Resolucion'){
                array_push($columnNames,'total_percepciones');
                array_push($columnNames,'total_percepciones_letra');
                array_push($columnNames,'propuestas_conceptos');
                array_push($columnNames,'propuesta_configurada');
                array_push($columnNames,'pagos_diferidos');
                array_push($columnNames,'total_diferidos');
                array_push($columnNames,'justificacion_propuesta');
                array_push($columnNames,'primera_manifestacion');
                array_push($columnNames,'segunda_manifestacion');

                array_push($columnNames,'citados_convenio');
                array_push($columnNames,'segunda_declaracion_convenio');
              }

              if($value->nombre =='Sala'){
                array_push($columnNames,'nombre');
              }
              if($value->nombre =='Audiencia'){
                array_push($columnNames,'comparecen_interesados');
              }
              if($value->nombre =='Centro'){
                $columnDomicilio = Schema::getColumnListing('domicilios');
                $exclude = ['id','updated_at','created_at','deleted_at','domiciliable_type','domiciliable_id','georeferenciable','tipo_vialidad_id','tipo_asentamiento_id'];
                foreach ( $exclude as $exclu ){
                  $k = array_search($exclu, $columnDomicilio);
                  if (false !== $k) {
                     unset($columnDomicilio[$k]);
                  }
                }
                array_push($columnNames,'hora_inicio');
                array_push($columnNames,'hora_fin');
                array_push($columnNames,'dias');
                array_push($columnNames,'domicilio_completo');
                array_push($columnNames,['nombre'=>'domicilio', 'columns'=>$columnDomicilio]);
              }
              $objetoDocumento [] =
              [
                  'objeto' => $value->objeto,
                  'nombre' => $value->nombre,
                  'tabla' => $value->tabla,
                  'campos' =>$columnNames
              ];
            }
          }
        }
        // dd($objetoDocumento);
        return $objetoDocumento; 
       }

       /**
        * display PDF file.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
       public function imprimirPDF($id)
       {
         $html = $this->renderDocumento($id);
         return $this->renderPDF($html, $id);
        // $pdf = new Dompdf();
        // //  $pdf->set_option('defaultFont', 'Montserrat');
        // $pdf->loadHtml($html);
        // $pdf->setPaper('A4');
        // $pdf->render();
        // // return $pdf->stream('carta.pdf');
        // //  return $pdf->stream('carta.pdf');
        // $pdf->stream("carta.pdf", array("Attachment" => false));
        // exit(0);
      //  return $pdf->download('constancia.pdf');

          // $pdf = App::make('dompdf.wrapper');
          // dd($pdf->save(storage_path('app/public/') . 'archivo.pdf') );
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

        private function getDataModelos($id)
        {
          try {
            $plantilla = PlantillaDocumento::find($id);
            $tipo_plantilla = TipoDocumento::find($plantilla->tipo_documento_id);
            $objetos = explode (",", $tipo_plantilla->objetos);
            $path = base_path('database/datafiles');
            $jsonElementos = json_decode(file_get_contents($path . "/elemento_documentos.json"),true);
            $idBase = "";
            $audienciaId = "";
            $data = [];
            $solicitud = "";
            foreach ($objetos as $objeto) {
              foreach ($jsonElementos['datos'] as $key=>$element) {
                if($element['id']==$objeto){
                  $model_name = 'App\\' . $element['objeto'];
                  $model = $element['objeto'];
                  $model_name = 'App\\' .$model;
                  if($model == 'Solicitud' ){
                    $solicitud = $model_name::with('estatusSolicitud','objeto_solicitudes')->find(2);//8 //24
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
                    $partes = $model_name::with('nacionalidad','domicilios','lenguaIndigena','tipoDiscapacidad','documentos.clasificacionArchivo.entidad_emisora','contactos.tipo_contacto')->where('solicitud_id',intval($idBase))->get();
                    $objeto = new JsonResponse($partes);
                    $obj = json_decode($objeto->content(),true);
                    $parte2 = [];
                    $parte1 = [];
                    $countSolicitante = 0;
                    $countSolicitado = 0;
                    $nombresSolicitantes = [];
                    $nombresSolicitados = [];
                    $datoLaboral="";
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
                      
                      $parte['datos_laborales'] = $datoLaboral;
                      $parteId = $parte['id'];
                      
                      $parte = Arr::except($parte, ['id','updated_at','created_at','deleted_at']);
                      $parte['datos_laborales'] = $datoLaboral;
                      if($parte['tipo_persona_id'] == 1){ //fisica
                        $parte['nombre_completo'] = $parte['nombre'].' '.$parte['primer_apellido'].' '.$parte['segundo_apellido'];
                      }else{//moral
                        $parte['nombre_completo'] = $parte['nombre_comercial'];
                      }
                      if($parte['tipo_parte_id'] != 3 ){
                        $dom_parte = $parte['domicilios'][0];
                        // dd($dom_parte);
                        $tipo_vialidad =  ($dom_parte['tipo_vialidad'] !== null)? $dom_parte['tipo_vialidad'] :"";
                        $vialidad =  ($dom_parte['vialidad'] !== null)? $dom_parte['vialidad'] :"";
                        $num_ext =  ($dom_parte['num_ext'] !== null)? "No." . $dom_parte['num_ext'] :"";
                        $municipio =  ($dom_parte['municipio'] !== null)? $dom_parte['municipio'] :"";
                        $estado =  ($dom_parte['estado'] !== null)? $dom_parte['estado'] :"";
                        $parte['domicilios_completo'] = mb_strtoupper($tipo_vialidad.' '.$vialidad.' '.$num_ext.', '.$municipio.', '.$estado);
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
                          // $salarioMensual = round( (($datoLaborales->remuneracion / $datoLaborales->periodicidad->dias)*30),2);
                          $salarioMensual = 1982.56;
                          $salario = explode('.', $salarioMensual);
                          $intSalarioMensual = explode('.', $salario[0]);
                          $decSalarioMensual = explode('.', $salario[1]);

                          $intSalarioMensualTextual = (new NumberFormatter("es", NumberFormatter::SPELLOUT))->format((float)$intSalarioMensual);
                          $intSalarioMensualTextual = str_replace("uno","un",$intSalarioMensualTextual);
                          $salarioMensualTextual = $intSalarioMensualTextual.' pesos '. $decSalarioMensual.'/100';
                        
                          $objeto = new JsonResponse($datoLaborales);
                          $datoLaboral = json_decode($objeto->content(),true);
                          $datoLaboral = Arr::except($datoLaboral, ['id','updated_at','created_at','deleted_at']);
                          $parte['datos_laborales'] = $datoLaboral;
                          $parte['datos_laborales_salario_mensual'] = $salarioMensual;
                          $parte['datos_laborales_salario_mensual_letra'] = $salarioMensualTextual;
                        }
                        
                        array_push($parte1, $parte);
                        $countSolicitante += 1;
                        array_push($nombresSolicitantes, $parte['nombre_completo'] );
                      }elseif ($parte['tipo_parte_id'] == 2 ) {//Solicitado
                        //representante legal solicitado
                        $representanteLegal = Parte::with('documentos.clasificacionArchivo.entidad_emisora')->where('parte_representada_id', $parteId)->where('tipo_parte_id',3)->get();
                        if(count($representanteLegal)>0){
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
                        }
                          //tipoNotificacion solicitado
                        if($audienciaId!=""){
                          $audienciaParte = AudienciaParte::with('tipo_notificacion')->where('audiencia_id',$audienciaId)->where('parte_id',$parteId)->get();
                          // $audienciaParte = AudienciaParte::with('tipo_notificacion')->where('audiencia_id',$audienciaId)->where('tipo_notificacion_id','<>',null)->get();
                          $parte['tipo_notificacion'] = $audienciaParte[0]->tipo_notificacion_id;
                          $parte['fecha_notificacion'] = $audienciaParte[0]->fecha_notificacion;
                        }
                          // $data = Arr::add( $data, 'solicitado', $parte );
                        $countSolicitado += 1;

                        array_push($parte2, $parte);
                        array_push($nombresSolicitados, $parte['nombre_completo'] );
                        // $nombresSolicitados += $parte['nombre_completo'].', ';
                      }
                    }
                    $data = Arr::add( $data, 'solicitante', $parte1 );
                    $data = Arr::add( $data, 'solicitado', $parte2 );
                    $data = Arr::add( $data, 'total_solicitantes', $countSolicitante );
                    $data = Arr::add( $data, 'total_solicitados', $countSolicitado );
                    $data = Arr::add( $data, 'nombres_solicitantes', implode(", ",$nombresSolicitantes));
                    $data = Arr::add( $data, 'nombres_solicitados', implode(", ",$nombresSolicitados));
                    // dd($data);
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
                        $audienciaId = $audiencia['id'];
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
                    $objeto = $model_name::with('persona')->find($conciliadorId);
                    $objeto = new JsonResponse($objeto);
                    $conciliador = json_decode($objeto->content(),true);
                    $conciliador = Arr::except($conciliador, ['id','updated_at','created_at','deleted_at']);
                    $conciliador['persona'] = Arr::except($conciliador['persona'], ['id','updated_at','created_at','deleted_at']);
                    $data = Arr::add( $data, 'conciliador', $conciliador );
                  }elseif ($model == 'Centro') {
                    $objeto = $model_name::with('domicilio','disponibilidades')->find($centroId);
                    $dom_centro = $objeto->domicilio;
                    $disponibilidad_centro = $objeto->disponibilidades;
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
                    $centro['domicilio_completo'] = mb_strtoupper($tipo_vialidad.' '.$vialidad.' '.$num_ext.', '.$municipio.', '.$estado);
                    //Disponibilidad del centro horarios y dias
                    $disponibilidad_centro = new JsonResponse($disponibilidad_centro);
                    $disponibilidad_centro = json_decode($disponibilidad_centro->content(),true);
                    $centro['hora_inicio']= $this->formatoFecha($disponibilidad_centro[0]['hora_inicio'],3);
                    $centro['hora_fin']= $this->formatoFecha($disponibilidad_centro[0]['hora_fin'],3);
                    $data = Arr::add( $data, 'centro', $centro );
                  }elseif ($model == 'Resolucion') {
                    $objetoResolucion = $model_name::find($resolucionAudienciaId);
                    $datosResolucion=[];
                    $etapas_resolucion = EtapaResolucionAudiencia::where('audiencia_id',$audienciaId)->whereIn('etapa_resolucion_id',[3,4,5])->get();
                    $objeto = new JsonResponse($etapas_resolucion);
                    $etapas_resolucion = json_decode($objeto->content(),true);
                    $datosResolucion['resolucion']= $objetoResolucion->nombre;
                    $resolucion_partes = ResolucionPartes::where('audiencia_id',$audienciaId)->first();
                    $resolucionParteId = $resolucion_partes->id;
                    foreach ($etapas_resolucion as $asd => $etapa ) {
                      if($etapa['etapa_resolucion_id'] == 3){
                        $datosResolucion['primera_manifestacion']= $etapa['evidencia'];
                      }else if($etapa['etapa_resolucion_id'] == 4){
                        $datosResolucion['justificacion_propuesta']= $etapa['evidencia'];
                        // $diasPeriodicidad = Periodicidad::where('id', $datoLaborales->periodicidad_id)->first();
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
                        // $tablaConceptos .= '<div style="page-break-before:always" >';
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
                        $tablaConceptos .= '</div>';

                        // $tablaConceptos .= '<h4>Propuesta Configurada </h4>';
                        $resolucion_conceptos = ResolucionParteConcepto::where('resolucion_partes_id',$resolucionParteId)->get();
                        $tablaConceptosEConvenio = '';
                        $tablaConceptosConvenio = '<style> .tbl, .tbl th, .tbl td {border: .5px dotted black; border-collapse: collapse; padding:3px;} .amount{ text-align:right} </style>';
                        $tablaConceptosConvenio .= '<div style="page-break-before:always" >';
                        $tablaConceptosConvenio .= '<table class="tbl">';
                        $tablaConceptosConvenio .= '<tbody>';
                        $totalPercepciones = 0;
                        foreach ($resolucion_conceptos as $concepto ) {
                          $conceptoName = ConceptoPagoResolucion::select('nombre')->find($concepto->concepto_pago_resoluciones_id);
                          if($concepto->id != 9){
                            $totalPercepciones += ($concepto->monto!= null ) ? floatval($concepto->monto) : 0;
                            $tablaConceptosConvenio .= '<tr><td class="tbl"> '.$conceptoName->nombre.' </td><td style="text-align:right;">     $'.$concepto->monto.'</td></tr>';
                          }else{
                            $tablaConceptosEConvenio .= '<tr><td class="tbl"> '.$conceptoName->nombre.' </td><td>'.$concepto->otro.'</td></tr>';
                          }
                        }
                        $tablaConceptosConvenio .= $tablaConceptosEConvenio;
                        $tablaConceptosConvenio .= '<tr><td> Total de percepciones </td><td>     $'.$totalPercepciones.'</td></tr>';
                        $tablaConceptosConvenio .= '</tbody>';
                        $tablaConceptosConvenio .= '</table>';
                        $tablaConceptosConvenio .= '</div>';
                        // $totalPercepciones = 2531.2;
                        $cantidadTextual = (new NumberFormatter("es", NumberFormatter::SPELLOUT))->format((float)$totalPercepciones);
                        $cantidadTextual = str_replace("uno","un",$cantidadTextual);
                        $cantidadTextual = str_replace("coma","punto",$cantidadTextual);
                        $datosResolucion['total_percepciones']= $totalPercepciones;
                        $datosResolucion['total_percepciones_letra']= $cantidadTextual;
                        $datosResolucion['propuestas_conceptos']= $tablaConceptos;
                        $datosResolucion['propuesta_configurada']= $tablaConceptosConvenio;
                      }else if($etapa['etapa_resolucion_id'] == 5){
                        $datosResolucion['segunda_manifestacion']= $etapa['evidencia'];
                      }else if($etapa['etapa_resolucion_id'] == 6){
                        $datosResolucion['descripcion_pagos']= $etapa['evidencia'];
                        //Fechas pago resolucion
                        $tablaPagosDiferidos = '<style> .tbl, .tbl th, .tbl td {border: .5px dotted black; border-collapse: collapse; padding:3px;} .amount{ text-align:right} </style>';
                        $tablaPagosDiferidos .= '<table class="tbl">';
                        $tablaPagosDiferidos .= '<tbody>';
                        $resolucion_pagos = ResolucionPagoDiferido::where('resolucion_parte_id',$resolucionParteId)->get();
                        $totalPagosDiferidos=0;
                        foreach ($resolucion_pagos as $pago ) {
                            $tablaPagosDiferidos .= '<tr><td class="tbl"> '.$pago->fecha_pago.' </td><td style="text-align:right;">     $'.$pago->monto_pago.'</td></tr>';
                            $totalPagosDiferidos +=1;
                        }
                        $tablaPagosDiferidos .= '</tbody>';
                        $tablaPagosDiferidos .= '</table>';

                        $datosResolucion['total_diferidos']= $totalPagosDiferidos;
                        $datosResolucion['pagos_diferidos']= $tablaPagosDiferidos;
                      }
                    }
                    $datosResolucion['primera_manifestacion'] = (isset($datosResolucion['primera_manifestacion']))? $datosResolucion['primera_manifestacion'] :"";
                    $datosResolucion['segunda_manifestacion'] = (isset($datosResolucion['segunda_manifestacion']))? $datosResolucion['segunda_manifestacion'] :"";
                    $datosResolucion['total_percepciones'] = (isset($datosResolucion['total_percepciones']))? $datosResolucion['total_percepciones'] :"";
                    $datosResolucion['propuestas_conceptos'] = (isset($datosResolucion['propuestas_conceptos']))? $datosResolucion['propuestas_conceptos'] :"";
                    $datosResolucion['propuesta_configurada'] = (isset($datosResolucion['propuesta_configurada']))? $datosResolucion['propuesta_configurada'] :"";
                    $datosResolucion['total_diferidos'] = (isset($datosResolucion['total_diferidos']))? $datosResolucion['total_diferidos'] :"";
                    $datosResolucion['pagos_diferidos'] = (isset($datosResolucion['pagos_diferidos']))? $datosResolucion['pagos_diferidos'] :"";
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
        Calcular posible prescripcion de derechos 
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
            // return $this->formatoFecha($fechaRecepcion,1);
            return $fechaRecepcion->toDateTimeString();
          } catch (\Throwable $th) {
            return "";
          }  
        }

        /*
        Convertir fechas yyyy-mm-dd hh to dd de Monthname de yyyy
         */
        private function formatoFecha($fecha,$tipo=null)
        {
          try {
            if($tipo!=3){ //no es hora
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

        private function renderDocumento($id)
        {
         $vars = [];
         $data = $this->getDataModelos($id);
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
                     if( !$isArrayAssoc ){ //objeto_solicitudes
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
                           $vars[strtolower($key.'_'.$n)] = ($v !== null)? $v :"-";
                          }
                          $vars[strtolower($key.'_nombre_completo')] = $val['nombre'].' '.$val['primer_apellido'].' '.(($val['segundo_apellido'] !="")?$val['segundo_apellido']: "");
                       }else{
                          foreach ($val as $n =>$v) {
                            $vars[strtolower($key.'_'.$k.'_'.$n)] =($v !== null)? $v :"-";
                          }
                       }
                     }
                   }elseif(gettype($val)== 'string'){
                     $pos = strpos($k,'fecha');
                     if ($pos !== false){
                       $val = $this->formatoFecha($val,1);
                     }
                   }
                   $vars[strtolower($key.'_'.$k)] = ($val !== null)? $val :"-";
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
                            $val = Arr::except($val[0],['id','updated_at','created_at','deleted_at','domiciliable_type','domiciliable_id','georeferenciable','tipo_vialidad_id','tipo_asentamiento_id']);
                            foreach ($val as $n =>$v) {
                              $vars[strtolower($key.'_'.$k.'_'.$n)] = ($v !== null)? $v :'-';
                            }
                          }else if($k =='contactos'){
                            foreach ($val as $n =>$v) {
                              $v = Arr::except($v,['id','updated_at','created_at','deleted_at','contactable_type','contactable_id']);
                              $vars[strtolower($key.'_'.$k.'_'.$v['tipo_contacto']['nombre'])] = ($v['contacto'] !== null)? $v['contacto'] :'-';
                              if($v['tipo_contacto_id'] == 3 && $data['correo_buzon'] == null){
                                // dd($data['correo_buzon']);
                                $vars[$key.'_correo_buzon'] = $v['contacto'];
                                $vars[$key.'_password_buzon'] = '';
                                // $data['correo_buzon'] = $v['contacto'];
                              }
                            }
                          }else{
                            foreach ($val as $i => $v) {
                              if( isset($v['nombre'] ) ){
                                $names =[];
                                array_push($names,$v['nombre']);
                              }
                              //  if(isset( $v['domiciliable_id'] )){
                              //  }
                            }
                            $val = implode (", ", $names);
                          }
                       }else{
                         if( isset($val['nombre']) && $k !='persona' && $k !='datos_laborales' && $k !='representante_legal'){ //catalogos
                           $val = $val['nombre']; //catalogos
                          }elseif ($k == 'datos_laborales') {
                            foreach ($val as $n =>$v) {
                              $vars[strtolower($key.'_'.$k.'_'.$n)] = ($v !== null)? $v :"-";
                              if($n == "comida_dentro"){
                                $vars[strtolower($key.'_'.$k.'_'.$n)] = ($v) ? 'dentro':'fuera';
                              }
                            }
                          }elseif ($k == 'nombre_completo') {
                            $vars[strtolower($key.'_'.$k)] =($val !== null)? $val :"-";

                          }elseif ($k == 'representante_legal') {
                            foreach ($val as $n =>$v) {
                              $vars[strtolower($key.'_'.$k.'_'.$n)] = ($v !== null)? $v :"-";//$v;
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
                     $vars[strtolower($key.'_'.$k)] = ($val !== null)? $val :"-";//$val;
                   }
                 }
               }
             }else{
               $vars[strtolower('solicitud_'.$key)] =$dato;//($dato!=null)? $dato : "-";
             }
           }
           $vars[strtolower('fecha_actual')] = $this->formatoFecha(Carbon::now(),1);
           $vars[strtolower('hora_actual')] = $this->formatoFecha(Carbon::now(),2);
          //  dd($vars);
         }
         $vars = Arr::except($vars, ['conciliador_persona']);
        $style = "<html xmlns=\"http://www.w3.org/1999/html\">
                 <head>
                 <style>
                 @page { margin-top: 80px; margin-bottom: 60px;
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
                 .header { position: fixed; top: -80px;}
                 .footer { position: fixed; bottom: -60px;}
                 #contenedor-firma {height: 60px;}
                 </style>
                 </head>
                 <body>
                 ";
         $end = "</body></html>";

         // $config = PlantillaDocumento::orderBy('created_at', 'desc')->first();
         $config = PlantillaDocumento::find($id);
         if (!$config) {
            //  $header = view('documentos._header_documentos_default');
             $body = view('documentos._body_documentos_default');
            //  $footer = view('documentos._footer_documentos_default');

            //  $header = '<div class="header">' . $header . '</div>';
            //  $body = '<div class="body">' . $body . '</div>';
            //  $footer = '<div class="footer">' . $footer . '</div>';
         } else {
            //  $header = '<div class="header">' . $config->plantilla_header . '</div>';
             $body = '<div class="body">' . $config->plantilla_body . '</div>';
            //  $footer = '<div class="footer">' . $config->plantilla_footer . '</div>';
         }
         $blade = $style . $body . $end;
         $html = StringTemplate::renderPlantillaPlaceholders($blade, $vars);
         return $html;
       }

       /**
        * Show the form for editing the specified resource.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
       public function cargarVariables(Request $request)
       {
         $tipo_plantilla = TipoDocumento::find($request->id);
         // $plantillaDocumento = PlantillaDocumento::find($id);
         // $tipo_plantillaDoc = TipoDocumento::all();
         // $tipo_plantillaDoc = $tipo_plantillaDoc->where('id', $plantillaDocumento->tipo_documento_id)->first()->getAttributes();
         $objetos = explode (",", $tipo_plantilla['objetos']);
         $objetoDocumento = [];
         //Se llena el catalogo desde el arvhivo json elemento_documentos.json
         $path = base_path('database/datafiles');
         $jsonElementos = json_decode(file_get_contents($path . "/elemento_documentos.json"));
         foreach ($objetos as $key => $obj){
           foreach ($jsonElementos->datos as $key => $value){
             if($value->id == $obj){
               $columnNames = Schema::getColumnListing($value->tabla);
               $guarded = ['id','updated_at','created_at','deleted_at'];
               foreach ( $guarded as $guard ){
                 $k = array_search($guard, $columnNames);
                 if (false !== $k) {
                    unset($columnNames[$k]);
                 }
               }
               $columnNames = str_replace("_id", "", $columnNames);
               $objetoDocumento [] =
                   [
                       'objeto' => $value->objeto,
                       'nombre' => $value->nombre,
                       'tabla' => $value->tabla,
                       'campos' => $columnNames
                   ];
             }
           }
         }
         return $objetoDocumento;
       }

}
