<?php

namespace App\Http\Controllers;

use App\ClasificacionArchivo;
use App\Expediente;
use App\Services\StringTemplate;
use App\Traits\GenerateDocument;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\Cast\Object_;

class OficiosDocumentosController extends Controller
{
    use GenerateDocument;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $expediente = Expediente::find($id);
        if($expediente != null){

            $plantilla['plantilla_header'] = view('documentos._header_documentos_default');
            $plantilla['plantilla_body'] = view('documentos._body_documentos_default');
            $plantilla['plantilla_footer'] = view('documentos._footer_documentos_default');
            
            return view('documentos.oficios.index', compact('plantilla','id'));
        }else{
            return redirect()->route('solicitudes.index')->with('error', 'No existe el expediente');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function imprimirPDF(Request $request)
    {
        $idExpediente =isset($request->id) ? $request->id :1;
        $nombre_documento =isset($request->nombre_documento) ? $request->nombre_documento : "Oficio";
        // $idExpediente =1;
        $datos = $request->all();
        
        $html = $this->renderDocumento($datos,$idExpediente);
        if($request->type == "generate"){
            $solicitud = $this->guardarDocumento($idExpediente,$html,37,$nombre_documento);
            return redirect()->route('solicitudes.consulta', ['id' => $solicitud->id]);
        }
        return $this->sendResponse($html, "Correcto"); // plantilla 2 para header y footer
    }

    public function guardarDocumento($idExpediente, $html, $tipo_archivo_id,$nombre_documento) {
        $solicitud = Expediente::find($idExpediente)->solicitud;
        $tipoArchivo = ClasificacionArchivo::find($tipo_archivo_id);
        $uuid = Str ::uuid();
        $archivo = $solicitud->documentos()->create(["descripcion" => $nombre_documento , 'uuid' => $uuid]);
        $directorio = 'expedientes/' . $solicitud->expediente->id . '/solicitud/' . $solicitud->id;

        $nombreArchivo = $tipoArchivo->nombre;
        $nombreArchivo = $this->eliminar_acentos(str_replace(" ", "", $nombreArchivo));
        $path = $directorio . "/" . $nombreArchivo . $archivo->id . '.pdf';
        $fullPath = storage_path('app/' . $directorio) . "/" . $nombreArchivo . $archivo->id . '.pdf';
        $this->renderPDF($html,1, $fullPath);

        $archivo->update([
            "nombre" => $nombre_documento,
            "nombre_original" => str_replace($directorio . "/", '', $path), //str_replace($directorio, '',$path->getClientOriginalName()),
            "ruta" => $path,
            "tipo_almacen" => "local",
            "uri" => $path,
            "longitud" => round(Storage::size($path) / 1024, 2),
            "firmado" => "false",
            "clasificacion_archivo_id" => $tipoArchivo->id,
        ]);
        return $solicitud;
    }
    
    private function renderDocumento(array $request,$id)
    {
        $expediente = Expediente::find($id);
        if($expediente != null){
            $vars = [];
            $vars['centro_nombre'] = $expediente->solicitud->centro->nombre;
            $vars['centro_domicilio_estado'] = isset($expediente->solicitud->centro->domicilio->estado) ? $expediente->solicitud->centro->nombre : 'MEXICO';
            $vars['expediente_folio'] = $expediente->folio;
            $conciliador = $expediente->audiencia[0]->conciliador->persona;
            $vars['conciliador_nombre']= ($expediente->audiencia != null)  ? $conciliador->nombre.' '.$conciliador->primer_apellido : '';
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
    
            // $header = '<div class="header">' . $request['oficio-header'] . '</div>';
            $body = '<div class="body">' . $request['oficio-body']. '</div>';
            $footer = '<div class="footer">' . $request['oficio-footer'] . '</div>';
    
            $blade = $style . $body . $footer . $end;
            // $html = StringTemplate::renderPlantillaPlaceholders($blade, $vars);
            $html = StringTemplate::renderOficioPlaceholders($blade, $vars);
        }else{
            $html = 'No existe expediente';
        }
        return $html;
    }

}
