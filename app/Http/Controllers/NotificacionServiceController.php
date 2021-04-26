<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\ParametroNoValidoException;
use App\Expediente;
use App\Parte;
use App\AudienciaParte;
use App\ClasificacionArchivo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\HistoricoNotificacion;
use App\HistoricoNotificacionRespuesta;

class NotificacionServiceController extends Controller {

    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function actualizarNotificacion() {
        DB::beginTransaction();
        try {
            $arreglo = $this->validaEstructuraParametros($this->request->getContent());
//        Buscamos la audiencia
            $expediente = Expediente::where("folio", $arreglo->expediente)->first();
            foreach ($expediente->audiencia as $audiencia) {
                $folio = $arreglo->folio;
                if ($folio == $audiencia->folio) {
                    foreach ($arreglo->Demandados as $demandado) {
                        $parteDemandado = AudienciaParte::where("parte_id", $demandado->demandado_id)->where("audiencia_id", $audiencia->id)->with(['documentos'])->first();
                        $parteDemandado->update([
                            "finalizado" => $demandado->finalizado,
                            "finalizado_id" => $demandado->finalizado_id,
                            "detalle" => $demandado->detalle,
                            "detalle_id" => $demandado->detalle_id,
                            "fecha_notificacion" => $demandado->fecha_notificacion
                        ]);
                        $image = base64_decode($demandado->documento);
                        $imgMd5 = md5($image);
                        if ($arreglo->tipo_notificacion == "citatorio") {
                            $clasificacion = ClasificacionArchivo::where("nombre", "Razón de notificación citatorio")->first();
                        } else {
                            $clasificacion = ClasificacionArchivo::where("nombre", "Razón de notificación multa")->first();
                        }
                        $encontro_identico = false;
                        foreach ($parteDemandado->documentos as $documento) {
                            if ($documento->clasificacion_archivo_id == $clasificacion->id) {
                                $archivo_existente = Storage::get($documento->ruta);
                                if ($imgMd5 == md5($archivo_existente)) {
                                    $encontro_identico = true;
                                }
                            }
                        }
                        if (!$encontro_identico) {
                            $directorio = 'expedientes/' . $audiencia->expediente_id . '/audiencias/' . $audiencia->id;
                            Storage::makeDirectory($directorio);

                            $uuid = Str::uuid();
                            $fullPath = $directorio . '/notificacion' . $uuid . '.pdf';
                            $dir = Storage::put($fullPath, $image);
                            $parteDemandado->documentos()->create([
                                "nombre" => "Notificacion" . $uuid,
                                "nombre_original" => "Notificacion" . $uuid,
                                "descripcion" => "documento generado en el sistema de notificaciones",
                                "nombre" => str_replace($directorio . "/", '', $fullPath),
                                "nombre_original" => str_replace($directorio . "/", '', $fullPath),
                                "descripcion" => "Documento de notificacion ",
                                "ruta" => $fullPath,
                                "uuid" => $uuid,
                                "tipo_almacen" => "local",
                                "uri" => $fullPath,
                                "longitud" => round(Storage::size($fullPath) / 1024, 2),
                                "firmado" => "false",
                                "clasificacion_archivo_id" => $clasificacion->id,
                            ]);
                        }
                        $documentoResolucion = $parteDemandado->documentos()->where("clasificacion_archivo_id", $clasificacion->id)->orderBy("id", "desc")->first();

//                        Guardamos la información en el historico
                        $historico = HistoricoNotificacion::where("audiencia_parte_id", $parteDemandado->id)->where("tipo_notificacion", $arreglo->tipo_notificacion)->first();
                        if ($historico == null) {
                            $historico = HistoricoNotificacion::create([
                                        "audiencia_parte_id" => $parteDemandado->id,
                                        "tipo_notificacion" => $arreglo->tipo_notificacion
                            ]);
                        }
                        HistoricoNotificacionRespuesta::create([
                            "historico_notificacion_id" => $historico->id,
                            "finalizado_id" => $demandado->finalizado_id,
                            "finalizado" => $demandado->finalizado,
                            "detalle_id" => $demandado->detalle_id,
                            "detalle" => $demandado->detalle,
                            "fecha_notificacion" => $demandado->fecha_notificacion,
                            "documento_id" => $documentoResolucion->id
                        ]);
                    }
                }
            }
            DB::commit();
//Log::info($this->request->getContent());
            return response('Se registraron las actualizaciones', 200);
        } catch (Exception $e) {
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
    public function validaEstructuraParametros($params) {
        $paramsJSON = json_decode($params);
        if ($paramsJSON === NULL) {
            throw new ParametroNoValidoException("Los datos enviados no pueden interpretarse como una estructura JSON válida, favor de revisar.", 1000);
            return null;
        }
        if (!isset($paramsJSON->folio)) {
            throw new ParametroNoValidoException("El folio de la audiencia es requerido.", 1010);
            return null;
        }
        if (!isset($paramsJSON->junta_id)) {
            throw new ParametroNoValidoException("El id de la junta es requerido.", 1011);
            return null;
        }
        if (!isset($paramsJSON->tipo_notificacion)) {
            throw new ParametroNoValidoException("El tipo de notificación es requerido.", 1020);
            return null;
        }
        if (!isset($paramsJSON->Demandados)) {
            throw new ParametroNoValidoException("Se deben agregar los demandados.", 1012);
            return null;
        } else {
            foreach ($paramsJSON->Demandados as $demandado) {
                if (!isset($demandado->demandado_id)) {
                    throw new ParametroNoValidoException("El id del demandado es requerido.", 1013);
                    return null;
                }
                if (!isset($demandado->finalizado)) {
                    throw new ParametroNoValidoException("El campo finalizado es requerido.", 1014);
                    return null;
                }
                if (!isset($demandado->finalizado_id)) {
                    throw new ParametroNoValidoException("El campo finalizado_id es requerido.", 1015);
                    return null;
                }
                if (!isset($demandado->detalle)) {
                    throw new ParametroNoValidoException("El campo detalle es requerido.", 1016);
                    return null;
                }
                if (!isset($demandado->detalle_id)) {
                    throw new ParametroNoValidoException("El campo detalle_id es requerido.", 1017);
                    return null;
                }
                if (!isset($demandado->documento)) {
                    throw new ParametroNoValidoException("El campo documento es requerido.", 1018);
                    return null;
                }
                if (!isset($demandado->fecha_notificacion)) {
                    throw new ParametroNoValidoException("El campo fecha_notificacion es requerido.", 1018);
                    return null;
                } else {
                    $fecha = new \Carbon\Carbon($demandado->fecha_notificacion);
                    if ($fecha > now()) {
                        throw new ParametroNoValidoException("La fecha no puede ser mayor a la fecha actual.", 1019);
                        return null;
                    }
                }
            }
        }
        return $paramsJSON;
    }

    public function recuperacionDocumentos() {
        $arreglo = $this->validaEstructuraParametros($this->request->getContent());
        try {
            DB::beggintransaction();
//        Buscamos la audiencia
            $expediente = Expediente::where("folio", $arreglo->expediente)->first();
            foreach ($expediente->audiencia as $audiencia) {
                $folio = $arreglo->folio;
                if ($folio == $audiencia->folio) {
                    foreach ($arreglo->Demandados as $demandado) {
                        $parteDemandado = AudienciaParte::where("parte_id", $demandado->demandado_id)->where("audiencia_id", $audiencia->id)->with(['documentos'])->first();
                        $image = base64_decode($demandado->documento);
                        if ($arreglo->tipo_notificacion == "citatorio") {
                            $clasificacion = ClasificacionArchivo::where("nombre", "Razón de notificación citatorio")->first();
                        } else {
                            $clasificacion = ClasificacionArchivo::where("nombre", "Razón de notificación multa")->first();
                        }
//                        Eliminalmos el documento actual 
                        foreach($parteDemandado->documentos()->where("clasificacion_archivo_id", $clasificacion->id) as $doc){
                            $doc->delete();
                        }
                        $directorio = 'expedientes/' . $audiencia->expediente_id . '/audiencias/' . $audiencia->id;
                        Storage::makeDirectory($directorio);

                        $uuid = Str::uuid();
                        $fullPath = $directorio . '/notificacion' . $uuid . '.pdf';
                        $dir = Storage::put($fullPath, $image);
                        $parteDemandado->documentos()->create([
                            "nombre" => "Notificacion" . $uuid,
                            "nombre_original" => "Notificacion" . $uuid,
                            "descripcion" => "documento generado en el sistema de notificaciones",
                            "nombre" => str_replace($directorio . "/", '', $fullPath),
                            "nombre_original" => str_replace($directorio . "/", '', $fullPath),
                            "descripcion" => "Documento de notificacion ",
                            "ruta" => $fullPath,
                            "uuid" => $uuid,
                            "tipo_almacen" => "local",
                            "uri" => $fullPath,
                            "longitud" => round(Storage::size($fullPath) / 1024, 2),
                            "firmado" => "false",
                            "clasificacion_archivo_id" => $clasificacion->id,
                        ]);
                    }
                }
            }
            DB::commit();
            return response('Se registraron las actualizaciones', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response('Ocurrio un error al tratar de actualizar', 500);
        }
    }

}
