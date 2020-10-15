<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\ParametroNoValidoException;
use App\Expediente;
use App\Parte;
use App\AudienciaParte;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class NotificacionServiceController extends Controller
{
     protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function actutalizarNotificacion(){
        DB::beginTransaction();
        try{
            $arreglo = $this->validaEstructuraParametros($this->request->getContent());
    //        Buscamos la audiencia
            $expediente = Expediente::where("folio",$arreglo->expediente)->first();
            foreach($expediente->audiencia as $audiencia){
                $folio = substr($arreglo->folio, 0,-5);
                if($folio == $audiencia->folio){
                    foreach($arreglo->Demandados as $demandado){
                        $parteDemandado = AudienciaParte::where("parte_id",$demandado->demandado_id)->where("audiencia_id",$audiencia->id)->first();
                        $parteDemandado->update([
                            "finalizado" => $demandado->finalizado,
                            "finalizado_id" => $demandado->finalizado_id,
                            "detalle" => $demandado->detalle,
                            "detalle_id" => $demandado->detalle_id,
//                            "documento" => $demandado->documento
                        ]);
                        $directorio = 'expedientes/'.$audiencia->expediente_id.'/audiencias/'.$audiencia->id;
                        Storage::makeDirectory($directorio);
//                        list($baseType, $image) = explode(';', $demandado->documento);
//                        list(, $image) = explode(',', $image);
                        $image = base64_decode($demandado->documento);
                        $fullPath = $directorio.'/notificacion'.$parteDemandado->id.'.pdf';
                        $dir = Storage::put($fullPath, $image);
                        $parteDemandado->documentos()->create([
                            "nombre" => "Notificacion".$parteDemandado->id,
                            "nombre_original" => "Notificacion".$parteDemandado->id,
                            "descripcion" => "documento generado en el sistema de notificaciones",
                            "nombre" => str_replace($directorio."/", '',$fullPath),
                            "nombre_original" => str_replace($directorio."/", '',$fullPath),
                            "descripcion" => "Documento de notificacion ",
                            "ruta" => $fullPath,
                            "tipo_almacen" => "local",
                            "uri" => $fullPath,
                            "longitud" => round(Storage::size($fullPath) / 1024, 2),
                            "firmado" => "false",
                            "clasificacion_archivo_id" => 45,
                        ]);
                    }
                }
            }
            DB::commit();
            return response('Se registraron las actualizaciones', 200);
        }catch(Exception $e){
            DB::rollBack();
            return response('Ocurrio un error al tratar de actualizar', 500);
            
        }
    }
    /**
     * Valida la estructura de los parametros enviados
     * @param $params
     * @return mixed|null
     * @throws ParametroNoValidoException
     */
    public function validaEstructuraParametros($params)
    {
        $paramsJSON = json_decode($params);
        if($paramsJSON === NULL){
            throw new ParametroNoValidoException("Los datos enviados no pueden interpretarse como una estructura JSON válida, favor de revisar.", 1000);
            return null;
        }
        if(!isset($paramsJSON->folio)){
            throw new ParametroNoValidoException("El folio de la audiencia es requerido.", 1010);
            return null;
        }
        if(!isset($paramsJSON->junta_id)){
            throw new ParametroNoValidoException("El id de la junta es requerido.", 1011);
            return null;
        }
        if(!isset($paramsJSON->Demandados)){
            throw new ParametroNoValidoException("Se deben agregar los demandados.", 1012);
            return null;
        }else{
            foreach($paramsJSON->Demandados as $demandado){
                if(!isset($demandado->demandado_id)){
                    throw new ParametroNoValidoException("El id del demandado es requerido.", 1013);
                    return null;
                }
                if(!isset($demandado->finalizado)){
                    throw new ParametroNoValidoException("El campo finalizado es requerido.", 1014);
                    return null;
                }
                if(!isset($demandado->finalizado_id)){
                    throw new ParametroNoValidoException("El campo finalizado_id es requerido.", 1015);
                    return null;
                }
                if(!isset($demandado->detalle)){
                    throw new ParametroNoValidoException("El campo detalle es requerido.", 1016);
                    return null;
                }
                if(!isset($demandado->detalle_id)){
                    throw new ParametroNoValidoException("El campo detalle_id es requerido.", 1017);
                    return null;
                }
                if(!isset($demandado->documento)){
                    throw new ParametroNoValidoException("El campo documento es requerido.", 1018);
                    return null;
                }
            }
        }
        return $paramsJSON;
    }
}
