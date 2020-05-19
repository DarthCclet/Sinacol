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
use App\PlantillaDocumento;
use App\TipoDocumento;
use App\Parte;
use App\Centro;
use App\Expediente;
use App\Audiencia;
use App\DatoLaboral;

use Illuminate\Support\Facades\App;

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
         //
     }

     /**
      * Show the form for editing the specified resource.
      *
      * @param  int  $id
      * @return \Illuminate\Http\Response
      */
     public function edit($id)
     {
       $tipo_plantillaDoc = TipoDocumento::all();
       $tipo_plantilla = array_pluck($tipo_plantillaDoc,'nombre','id');

       $plantillaDocumento = PlantillaDocumento::find($id);
       $tipo_plantillaDoc = $tipo_plantillaDoc->where('id', $plantillaDocumento->tipo_documento_id)->first()->getAttributes();
       $objetos = explode (",", $tipo_plantillaDoc['objetos']);
       // $config = PlantillaDocumento::orderBy('created_at', 'desc')->first();
       if (!$plantillaDocumento) {
           $header = view('documentos._header_documentos_default');
           $body = view('documentos._body_documentos_default');
           $footer = view('documentos._footer_documentos_default');
       }
       else
       {
           $header = $plantillaDocumento->plantilla_header;
           $body = $plantillaDocumento->plantilla_body;
           $footer = $plantillaDocumento->plantilla_footer;
           $nombre = $plantillaDocumento->nombre_plantilla;
       }

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
       private function getObjetoDocumento($value){
          $objetoDocumento = [];

       }
       private function getCondicionales(){
         $condicionales = [];
         $path = base_path('database/datafiles');
         $json = json_decode(file_get_contents($path . "/condiciones_documento.json"));
         $condicionales = $json->datos;
         return $condicionales;
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
          $pdf = App::make('dompdf.wrapper');
          $pdf->getDomPDF();//->setBasePath('/Users/yadira/Projects/STPS/public/assets/img/logo/');
          $pdf->loadHTML($html)->setPaper('letter');
          return $pdf->stream('carta.pdf');
          // return $pdf->download('carta.pdf');
          // dd($pdf->save(storage_path('app/public/') . 'archivo.pdf') );
        }

        private function getDataModelos($id)
        {
          $plantilla = PlantillaDocumento::find($id);
          $tipo_plantilla = TipoDocumento::find($plantilla->tipo_documento_id);
          $objetos = explode (",", $tipo_plantilla->objetos);
          $path = base_path('database/datafiles');
          $jsonElementos = json_decode(file_get_contents($path . "/elemento_documentos.json"),true);
          $idBase = "";
          $data = [];
          foreach ($objetos as $objeto) {
            foreach ($jsonElementos['datos'] as $key=>$element) {
              if($element['id']==$objeto){
                 $model_name = 'App\\' . $element['objeto'];
                   $model = $element['objeto'];
                   $model_name = 'App\\' .$model;
                   if($model == 'Solicitud' ){
                     $solicitud = $model_name::with('estatusSolicitud','objeto_solicitudes')->find(9);//first();
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
                     // $partes = $model_name::with('nacionalidad','domicilios','lenguaIndigena','tipoDiscapacidad')->findOrFail(1);
                     foreach ($obj as $parte ) {
                       $parteId = $parte['id'];
                       $parte = Arr::except($parte, ['id','updated_at','created_at','deleted_at']);
                       if($parte['tipo_parte_id'] == 1 ){//Solicitante
                         //datos laborales del solicitante
                         $datoLaboral = DatoLaboral::with('jornada','ocupacion')->where('parte_id', 8)->get();
                         // $datoLaboral = DatoLaboral::with('jornada','ocupacion')->where('parte_id', $parteId)->get();
                         $objeto = new JsonResponse($datoLaboral);
                         $datoLaboral = json_decode($objeto->content(),true);
                         $datoLaboral = Arr::except($datoLaboral[0], ['id','updated_at','created_at','deleted_at']);
                         $parte['datos_laborales'] = $datoLaboral;
                         array_push($parte1, $parte);
                         $countSolicitante += 1;
                       }elseif ($parte['tipo_parte_id'] == 2 ) {//Solicitado
                         $countSolicitado += 1;
                         array_push($parte2, $parte);
                         array_push($parte2, $parte);
                       }
                     }
                     $data = Arr::add( $data, 'solicitante', $parte1 );
                     $data = Arr::add( $data, 'solicitado', $parte2 );
                     $data = Arr::add( $data, 'total_solicitantes', $countSolicitante );
                     $data = Arr::add( $data, 'total_solicitados', $countSolicitado );
                   }elseif ($model == 'Audiencia') {
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
                       $audiencia = Arr::except($audiencia, ['id','updated_at','created_at','deleted_at']);
                       array_push($Audiencias,$audiencia);
                     }
                     $data = Arr::add( $data, 'audiencia', $Audiencias );
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
          return $data;
        }
        /*
        Convertir fechas yyyy-mm-dd hh to dd de Monthname de yyyy
         */
        private function formatoFecha($fecha)
        {
          $monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio","Julio", "Agosto", "Septiembre", "Octubre", "Noivembre", "Diciembre"];
          $hh= "";
          if(strpos($fecha, " ") ){
            $date = explode(' ', $fecha);
            $fecha = $date[0];
            $hh = $date[1];
          }
          $fecha = explode('-', $fecha);
          $dd = $fecha[2];
          $mm = $fecha[1];
          $yy = $fecha[0];
          $ddmmyy = $dd.' de '. $monthNames[intval($mm)-1]. ' de ' . $yy .' '. $hh ;
          return $ddmmyy;
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
                       if( isset($val['nombre']) && $k !='persona' ){
                         $val = $val['nombre'];
                       }elseif ($k == 'persona') {
                         foreach ($val as $n =>$v) {
                           $vars[strtolower($key.'_'.$n)] = $v;
                         }
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
               }else{//Si no es un array assoc (solicitados, solicitantes)
                 foreach ($dato as $data) {
                   foreach ($data as $k => $val) { // folio
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
                         if( isset($val['nombre']) && $k !='persona' ){
                           $val = $val['nombre'];
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
         $config = PlantillaDocumento::find($id);
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
