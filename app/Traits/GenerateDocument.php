<?php
namespace App\Traits;

use App\Audiencia;
use App\ClasificacionArchivo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

trait GenerateDocument
{
    /**
     * Generar documento a partir de un modelo y de una plantilla
     * @return mixed
     */
    public function generarConstancia($idReferencia,$type,$tipo_documento_id)
    {
        if($type == "audiencia"){
            $audiencia = Audiencia::find($idReferencia);
            $directorio = 'expedientes/'.$audiencia->expediente_id.'/audiencias/'.$idReferencia;
            $algo = Storage::makeDirectory($directorio, 0775, true);
            
        }
        $tipoArchivo = ClasificacionArchivo::find($tipo_documento_id);
        
        // $path = $this->generarDocumento($idReferencia,$type,$tipo_documento_id,$directorio);
        $html = "hola";//$this->renderDocumento($idReferencia,$type,$tipo_documento_id);
        $pdf = App::make('dompdf.wrapper');
        $pdf->getDomPDF();
        // dd($html);
        $pdf->loadHTML($html)->setPaper('letter');
        $path = $directorio."/". $type.$idReferencia.'.pdf';
        $fullPath = storage_path('app/'.$directorio)."/".$type.$idReferencia.'.pdf';
        $store = $pdf->save($fullPath);
        if($type == "audiencia"){
            $audiencia->documentos()->create([
                "nombre" => str_replace($directorio."/", '',$path),
                "nombre_original" => str_replace($directorio."/", '',$path),//str_replace($directorio, '',$path->getClientOriginalName()),
                "descripcion" => "Documento de audiencia ".$tipoArchivo->nombre,
                "ruta" => $path,
                "tipo_almacen" => "local",
                "uri" => $path,
                "longitud" => round(Storage::size($path) / 1024, 2),
                "firmado" => "false",
                "clasificacion_archivo_id" => $tipoArchivo->id ,
            ]);
        }
        return 'Product saved successfully';
    }
    
}
