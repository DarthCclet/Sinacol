<?php

namespace App\Http\Controllers;

use App\ClasificacionArchivo;
use App\Exceptions\PlantillaDocumentoInexistenteException;
use App\Expediente;
use App\PlantillaDocumento;
use App\Services\StringTemplate;
use App\Traits\GenerateDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\Config;

class OficiosDocumentosController extends Controller
{
    use GenerateDocument;

    /**
     * ID de clase de documento para el oficio libre (37 = Otros del catálogo "clasificacion_archivos")
     */
    const CLASE_DOCUMENTO_ID = 37;

    /**
     * @var PlantillaDocumento Contiene la plantilla correspondiente al oficio libre.
     */
    protected $plantillaDocumento;

    public function __construct() {
        $nombre_plantilla = config('folios.plantilla_oficio_libre_nombre', 'oficio libre');
        $this->plantillaDocumento = PlantillaDocumento::where('nombre_plantilla', 'ilike', $nombre_plantilla)->first();
    }

    /**
     * Muestra el formato de captura del oficio libre dado el id de expediente.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        $plantilla_documento = $this->plantillaDocumento;
        if($expediente = Expediente::find($id)){
            $plantilla['plantilla_header'] = "";
            $plantilla['plantilla_body'] = view('documentos._body_documentos_default');
            $plantilla['plantilla_footer'] = "";
            return view('documentos.oficios.index', compact('plantilla','id', 'plantilla_documento'));
        }
        return redirect()->route('solicitudes.index')->with('error', 'No existe el expediente');
    }

    /**
     * Imprime en PDF el oficio libre
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Symfony\Component\Debug\Exception\FatalThrowableError
     */
    public function imprimirPDF(Request $request)
    {
        $idExpediente =isset($request->id) ? $request->id :1;
        $nombre_documento =isset($request->nombre_documento) ? $request->nombre_documento : "Oficio";
        // $idExpediente =1;
        $datos = $request->all();

        $html = $this->renderDocumento($datos,$idExpediente);
        if($request->type == "generate"){
            $solicitud = $this->guardarDocumento($idExpediente,$html,self::CLASE_DOCUMENTO_ID,$nombre_documento);
            return redirect()->route('solicitudes.consulta', ['id' => $solicitud->id]);
        }
        return $this->sendResponse($html, "Correcto"); // plantilla 2 para header y footer
    }

    /**
     * Guarda el oficio libre en la BD
     * @param integer $idExpediente ID del expediente
     * @param string $html Cadena html del contenido del expediente
     * @param integer $tipo_archivo_id ID del tipo de archivo
     * @param string $nombre_documento Cadena del nombre del documento
     * @return mixed
     * @throws \Symfony\Component\Debug\Exception\FatalThrowableError
     */
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

        $plantilla = PlantillaDocumento::where('nombre_plantilla', 'OFICIO ESPECIAL')->first();
        $plantilla_id = ($plantilla) ? $plantilla->id : 1;

        $this->renderPDF($html, $plantilla_id, $fullPath);

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

    /**
     * Genera la cadena html correspondiente a la plantilla y el contenido del documento
     * @param array $request Arreglo de variables placeholder que componen el documento
     * @param integer $id ID del expediente
     * @return string Cadena HTML
     * @throws \Symfony\Component\Debug\Exception\FatalThrowableError
     */
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

            $body = '<div class="body">' . $request['oficio-body']. '</div>';

            $blade = $style . $body . $end;

            $html = StringTemplate::renderOficioPlaceholders($blade, $vars);
        }else{
            $html = 'No existe expediente';
        }
        return $html;
    }

}
