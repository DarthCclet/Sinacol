<?php

namespace App\Http\Controllers;

use App\Expediente;
use App\Services\StringTemplate;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Cast\Object_;

class OficiosDocumentosController extends Controller
{
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
        // dd($idExpediente);
        // $idExpediente =1;
        $datos = $request->all();
        
        $html = $this->renderDocumento($datos,$idExpediente);
        $pdf = new Dompdf();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4');
        $pdf->render();
        
        $pdf->stream("carta.pdf", array("Attachment" => false));
        exit(0);
    }
    private function renderDocumento(array $request,$id)
    {
        $expediente = Expediente::find($id);
        if($expediente != null){
            $vars = [];
            $vars['centro_nombre'] = $expediente->solicitud->centro->nombre;
            $vars['centro_domicilio_estado'] = isset($expediente->solicitud->centro->domicilio->estado) ? $expediente->solicitud->centro : 'MEXICO';
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
    
            $header = '<div class="header">' . $request['oficio-header'] . '</div>';
            $body = '<div class="body">' . $request['oficio-body']. '</div>';
            $footer = '<div class="footer">' . $request['oficio-footer'] . '</div>';
    
            $blade = $style . $header . $body . $footer . $end;
            // $html = StringTemplate::renderPlantillaPlaceholders($blade, $vars);
            $html = StringTemplate::renderOficioPlaceholders($blade, $vars);
        }else{
            $html = 'No existe expediente';
        }
        return $html;
    }

}
