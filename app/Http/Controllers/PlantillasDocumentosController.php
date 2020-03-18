<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ConfiguracionResponsivasRequest;
use App\Services\StringTemplate;

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
        return view('documentos.create');
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
// dd($datos["plantilla-body"] == null);
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


      // dd($request);
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
       $datosP['tipo_documento_id'] = 1;
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
      $plantilla = PlantillaDocumento::find($id);
      // $config = PlantillaDocumento::orderBy('created_at', 'desc')->first();
      if (!$plantilla) {
          $header = view('documentos._header_documentos_default');
          $body = view('documentos._body_documentos_default');
          $footer = view('documentos._footer_documentos_default');
      }
      else
      {
          $header = $plantilla->plantilla_header;
          $body = $plantilla->plantilla_body;
          $footer = $plantilla->plantilla_footer;
          $nombre = $plantilla->nombre_plantilla;
      }
      return view('documentos.edit')->with('plantillaDocumento', $plantilla);
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
        $datosP['tipo_documento_id'] = 2;

        $plantilla->update($datosP);
        return redirect('plantilla-documentos')->with('success', 'Se ha actualizado exitosamente');
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
        // dd()
        $tipo_plantilla = TipoDocumento::find($plantilla->tipo_documento_id);
        $objetos = explode (",", $tipo_plantilla->objetos);
        foreach ($objetos as $key => $value) {
          $model_name = 'App\\' . $value;
          $Objeto = $model_name::first();
          foreach ($Objeto->getAttributes() as $k => $val) {
            $vars[$k] = $val;
          }
        }
        // dd($vars);
        // $vehiculo = Vehiculo::find($vehiculo_id);
        // $empresa = Empresa::first();

        $vars['nombre_empresa'] = "Empresa";
        $vars['nombre_legal_empresa'] = "legal";

        // $vars['nombre'] = "nombre";
        $vars['apellidos'] = "apellidos";
        $vars['num_empleado'] = 11;
        $vars['area'] = "area";
        $vars['email'] = "email";
        $vars['puesto'] = "puesto";

        $vars['placa'] = 11;
        $vars['niv'] = 11;
        $vars['numero_inventario'] = 11;
        $vars['numero_motor'] = 11;
        $vars['odometro'] = 11;

       $vars['marca'] = "marca";
       $vars['submarca'] = "subMarca";
       $vars['modelo'] = "2021";
       $vars['version'] = 1;
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
