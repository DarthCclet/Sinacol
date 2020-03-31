<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ConfiguracionResponsivasRequest;
use App\Services\StringTemplate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use App\Filters\CatalogoFilter;
use Barryvdh\DomPDF\PDF;
use App\PlantillaDocumento;
use App\TipoDocumento;
use App\Parte;
use App\Centro;

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
       // $tipo_plantillaDoc = $tipo_plantillaDoc->where('id', $plantillaDocumento->tipo_documento_id)->first()->getAttributes();
       // $objetos = explode (",", $tipo_plantillaDoc['objetos']);

       $objetoDocumento = [];
       $path = base_path('database/datafiles');
       $json = json_decode(file_get_contents($path . "/elemento_documentos.json"));
       //Se llena el catalogo desde el arvhivo json elemento_documentos.json
       // foreach ($objetos as $key => $obj){
         foreach ($json->datos as $key => $value){
           // if($value->id == $obj){
             $columnNames = [];
             $columnNames [] = Schema::getColumnListing($value->tabla);
             $objetoDocumento [] =
                 [
                     'objeto' => $value->objeto,
                     'nombre' => $value->nombre,
                     'tabla' => $value->tabla,
                     'campos' => $columnNames[0]
                 ];
            // }
          }
        // }
    // return DB::getSchemaBuilder()->getColumnListing($table);
        return view('documentos.create', compact('objetoDocumento','tipo_plantilla'));
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

        // $datos = $request->all();
        // $datosP['nombre_plantilla'] = $datos["nombre-plantilla"];
        // $datosP['tipo_documento_id'] = 1;
        // $datosP['plantilla_header'] = $datos["plantilla-header"];
        // $datosP['plantilla_body'] = $datos["plantilla-body"];
        // $datosP['plantilla_footer'] = $datos["plantilla-footer"];

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
       $path = base_path('database/datafiles');
       $jsonElementos = json_decode(file_get_contents($path . "/elemento_documentos.json"));
       //Se llena el catalogo desde el arvhivo json elemento_documentos.json
       foreach ($objetos as $key => $obj){
         foreach ($jsonElementos->datos as $key => $value){
           if($value->id == $obj){
             $columnNames = [];
             $columnNames [] = Schema::getColumnListing($value->tabla);
             $objetoDocumento [] =
                 [
                     'objeto' => $value->objeto,
                     'nombre' => $value->nombre,
                     'tabla' => $value->tabla,
                     'campos' => $columnNames[0]
                 ];
           }
         }
       }
       return view('documentos.edit', compact('plantillaDocumento','objetoDocumento','tipo_plantilla'));
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
       * Remove the specified resource from storage.
       *
       * @param
       * @return \Illuminate\Http\Response
       */
      public function cargarDefault()
      {
        $plantillaDocumento = new PlantillaDocumento();
        $plantillaDocumento->plantilla_header = view('documentos._header_documentos_default');
        $plantillaDocumento->plantilla_body = view('documentos._body_documentos_default');
        $plantillaDocumento->plantilla_footer = view('documentos._footer_documentos_default');

        $objetoDocumento = [];
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/elemento_documentos.json"));
        //Se llena el catalogo desde el arvhivo json elemento_documentos.json
        foreach ($json->datos as $key => $value){
            $columnNames = [];
            $columnNames [] = Schema::getColumnListing($value->tabla);
            $objetoDocumento [] =
                [
                    'objeto' => $value->objeto,
                    'nombre' => $value->nombre,
                    'tabla' => $value->tabla,
                    'campos' => $columnNames[0]
                ];
        }
        return view('documentos.create', compact('plantillaDocumento','objetoDocumento'));
       }

       /**
        * Remove the specified resource from storage.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
       public function imprimirPDF($id)
       {
          $html = $this->renderDocumento($id);
          $pdf = App::make('dompdf.wrapper');
          $pdf->loadHTML($html)->setPaper('letter');
          return $pdf->stream('carta.pdf');
          // return $pdf->download('carta.pdf');
          // dd($pdf->save(storage_path('app/public/') . 'archivo.pdf') );
        }

        private function renderDocumento($id)
        {
         $vars = [];

         $plantilla = PlantillaDocumento::find($id);
         $tipo_plantilla = TipoDocumento::find($plantilla->tipo_documento_id);

         $objetos = explode (",", $tipo_plantilla->objetos);
         $path = base_path('database/datafiles');
         $jsonElementos = json_decode(file_get_contents($path . "/elemento_documentos.json"),true);
         foreach ($objetos as $objeto) {
           foreach ($jsonElementos['datos'] as $key=>$element) {
             if($element['id']==$objeto){
               // dd($clave);
               // dd($element);
             //si encuentra id tipo Objeto en elemento_documentos
             // if($clave != false){
                // dd($element);
                $model_name = 'App\\' . $element['objeto'];
                  // dd($element['id_tipo']);
                // dd($model_name);
                if($element['id_tipo']!= ""){
                  // dd('tipo_'.strtolower($element['objeto']).'_id');
                  // dd(intval($element['id_tipo']));
                  $tipo = 'tipo_'.strtolower($element['objeto']).'_id';
                  $Objeto = $model_name::all()->where($tipo,intval($element['id_tipo']) )->first();
                  // $Objeto = $model_name::first()->where('tipo_parte_id',2);
                  // dd($Objeto[1]);
                  // dd($obj);
                }else{
                    // dd($element['id_tipo']);
                  //pimer objeto encontrado para ejemplo de pdf
                  $Objeto = $model_name::first();
                }
                $obj = ($Objeto->getAttributes());
                // dd($obj);
                $count =0;
                foreach ($obj as $k => $val) {
                  $count ++;
                    // dd($k);
                    // dd($element['nombre']);
                  //   dd($val);
                  $vars[strtolower($element['nombre'].'_'.$k)] = $val;
                }
                // dd($obj);
             }
           }
           // dd($obj);
           // dd($element['nombre']);
         }
         // dd($vars);

        $style = "<html xmlns=\"http://www.w3.org/1999/html\">
                 <head>
                 <style>
                 @page { margin: 160px 60px 60px 80px;  }
                 .header { position: fixed; top: -150px;}
                 .footer { position: fixed; bottom: 20px;}
                 #contenedor-firma {height: 100px;}
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
 //echo $blade; exit;
         $html = StringTemplate::renderPlantillaPlaceholders($blade, $vars);
         return $html;
       }

}
