<?php

namespace App\Http\Controllers;

use App\BitacoraBuzon;
use App\Estado;
use App\Events\GenerateDocumentResolution;
use Illuminate\Http\Request;
use App\Parte;
use App\Solicitud;
use App\User;
use App\Municipio;
use App\Expediente;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\AccesoBuzonMail;
use App\Traits\GenerateDocument;
use Exception;
use Illuminate\Support\Facades\Log;

class BuzonController extends Controller
{
    use GenerateDocument;
	private $request;
    public function __construct(Request $request){
    	$this->request = $request;
    }
    public function SolicitudBuzon(){
    	return view('buzon.solicitud');
    }
    public function SolicitarAcceso(){
        $busqueda = array("tipo_persona_id" => $this->request->tipo_persona_id);
        $expediente = Expediente::whereFolio($this->request->folio)->first();
        if($expediente != null){
            if($this->request->tipo_persona_id == 2){
                $parte = $expediente->solicitud->partes()->where("rfc",$this->request->rfc)->first();
                $busqueda["busqueda"] = $this->request->rfc;
            }else{
                $parte = $expediente->solicitud->partes()->where("curp",$this->request->curp)->first();
                $busqueda["busqueda"] = $this->request->curp;
            }
            $correo = "";
            if($parte != null){
                if($parte->password_buzon != null && $parte->password_buzon != ""){
                    return array(["correo" => false,"mensaje" => 'No hay correos registrados']);
                }else{
                    $correo = $parte->correo_buzon;
                    $busqueda["correo"] = $correo;
                    $busqueda["folio"] = $this->request->folio;

                    $token = app('hash')->make(str_random(8));
                    if (!Cache::has($token)) {
                        Cache::put($token, $busqueda,now()->addMinutes(50));
                    }
                    $respuesta = Cache::get($token);
                    $parte->token = $token;
                    $parte->email = $correo;
                    $composicion = base64_encode($token)."/".base64_encode($correo);
                    $liga = env('APP_URL')."/validar_token/".$composicion;
                    Mail::to($correo)->send(new AccesoBuzonMail($parte,$liga));
                    return array(["correo" => true,"mensaje" => 'Se envio un correo con el acceso a la dirección '.$correo]);
                }
            }else{
                return $this->sendError('No hay registros con este dato', 'Error');
            }
        }else{
            return $this->sendError('No se encontró el expediente', 'Error');
        }
    	
    }
    public function AccesoBuzon(){
        $expediente = Expediente::whereFolio($this->request->folio)->first();
        if($expediente != null){
            $partesBusqueda = $expediente->solicitud->partes()->where("correo_buzon", $this->request->correo_buzon)->where("password_buzon",$this->request->password_buzon)->first();
            if($partesBusqueda != null){
                $identificador = "";
                if($partesBusqueda->tipo_persona_id == 1){
                    $identificador = $partesBusqueda->curp;
                    $partes = $expediente->solicitud->partes()->where("curp",$partesBusqueda->curp)->get();
                }else{
                    $identificador = $partesBusqueda->rfc;
                    $partes = $expediente->solicitud->partes()->where("rfc",$partesBusqueda->rfc)->get();
                }
                $solicitudes = [];
                foreach($partes as $parte){         
                    BitacoraBuzon::create(['parte_id'=>$parte->id,'descripcion'=>'Constancia de consulta realizada','tipo_movimiento'=>'Consulta','clabe_identificacion'=>$identificador]);
                    $solicitud = $parte->solicitud;
                    if($solicitud->expediente != null){
                        $solicitud->acciones = $this->getAcciones($solicitud, $solicitud->partes, $solicitud->expediente);
                        $solicitud->parte = $parte;
                        $solicitud->acepto_buzon = "no";
                        if($parte->notificacion_buzon){
                            $solicitud->acepto_buzon = "si";
                        }
                        foreach($solicitud->expediente->audiencia as $audiencia){
                            $solicitud->documentos = $solicitud->documentos->merge($audiencia->documentos);
                            $audiencia->documentos_firmar = $parte->firmas()->where("audiencia_id",$audiencia->id)->get();
                        }
                        $solicitudes[]=$solicitud;
                    }
                }
                return view("buzon.buzon", compact('solicitudes'));
            }else{
                return view("buzon.solicitud")->with("Error","No se encontraron partes con los accesos proporcionados.");
            }
        }else{
            return view("buzon.solicitud")->with("Error","No se encontró el expediente.");
        }
    }
    Public function validar_token(){
        $token = base64_decode($this->request->token,true);
        $correo = base64_decode($this->request->correo);
        if($token != "" && $correo != ""){
            if(Cache::has($token)){
                $busqueda = Cache::get($token);
                if($correo == $busqueda["correo"]){
                    if(isset($busqueda["folio"]) && $busqueda["folio"] != "" && $busqueda["folio"] != null){
                        $expediente = Expediente::whereFolio($busqueda["folio"])->first();
                        if($expediente != null){
                            $solicitud = [];
                            $solicitud = $expediente->solicitud;
                            $solicitud->acciones = $this->getAcciones($solicitud, $solicitud->partes, $solicitud->expediente);
                            if($busqueda["tipo_persona_id"] == 1){
                                $parte = $solicitud->partes()->where("curp",$busqueda["busqueda"])->first();
                            }else{
                                $parte = $solicitud->partes()->where("rfc",$busqueda["busqueda"])->first();
                            }
                            $parte = $solicitud->partes()->where("curp",$busqueda["busqueda"])->first();
                            $solicitud->parte = $parte;
                            $solicitud->acepto_buzon = "no";
                            if($parte->notificacion_buzon){
                                $solicitud->acepto_buzon = "si";
                            }
                            foreach($solicitud->expediente->audiencia as $audiencia){
                                $audiencia->documentos_firmar = $parte->firmas()->where("audiencia_id",$audiencia->id)->get();
                                $audiencia->documentos()->with('clasificacionArchivo')->get();
                            }
                            $solicitudes[]=$solicitud;
                            return view("buzon.buzon", compact('solicitudes'));
                        }else{
                            return view("buzon.solicitud")->with("Error","No se encontró información del folio");
                        }
                    }else{
                        return view("buzon.solicitud")->with("Error","No se encontró información del folio");
                    }
                }else{
                    return view("buzon.solicitud")->with("Error","Correo del que ingresas no coincide con el token");
                }
            }else{
                return view("buzon.solicitud")->with("Error","El token es incorrecto solicita uno nuevo");
            }
        }else{
            return view("buzon.solicitud")->with("Error","La estructura de la liga no es correcta");
        }
    }

    private function getAcciones(Solicitud $solicitud,$partes,$expediente){
//         Obtenemos las acciones de la solicitud
        $SolicitudAud = $solicitud->audits()->get();
//        Obtenemos las acciones de las partes
        foreach($partes as $parte){
            $SolicitudAud = $SolicitudAud->merge($parte->audits()->get());
        }
//        Obtenemos las acciones de las audiencias
        if ($expediente != null) {
            $SolicitudAud = $SolicitudAud->merge($expediente->audits()->get());
            if ($expediente->audiencias != null) {
                foreach($expediente->audiencias as $audiencia){
                    $SolicitudAud = $SolicitudAud->merge($audiencia->audits()->get());
                }
            }
        }
        $SolicitudAud = $SolicitudAud->sortBy('created_at');
        $audits = array();
        foreach ($SolicitudAud as $audit) {
            $table = "Solicitud";
            $extra = "";
            if($audit->auditable_type == 'App\Parte'){
                $table = "Parte";
                $parte = Parte::find($audit->auditable_id);
                if($parte->tipo_persona_id == 1){
                    $extra = $parte->nombre." ".$parte->primer_apellido." ".$parte->segundo_apellido;
                }else{
                    $extra = $parte->nombre_comercial;
                }
            }else if($audit->auditable_type == 'App\Audiencia'){
                $table = "Audiencia";
            }else if($audit->auditable_type == 'App\Expediente'){
                $table = "Expediente";
                $expediente = Expediente::find($audit->auditable_id);
                $extra = $expediente->folio."/".$expediente->anio;
            }
            $nombre = "Sin dato";
            if($audit->user_id != null){
                $user = User::find($audit->user_id);
                $nombre = $user->persona->nombre." ".$user->persona->primer_apellido." ".$user->persona->segundo_apellido;
            }
            $audits[] = array("user" => $nombre, "elemento" => $table,"extra" => $extra,"event" => $audit->event, "created_at" => $audit->created_at, "cambios" => $audit->getModified());
        }
        return $audits;
     }
     /**
     * Función para almacenar catalogos (nombre,id) en cache
     *
     * @param [string] $nombre
     * @param [Model] $modelo
     * @return void
     */
    private function cacheModel($nombre,$modelo,$campo = 'nombre' ){
        if (!Cache::has($nombre)) {
            $respuesta = array_pluck($modelo::all(),$campo,'id');
            Cache::forever($nombre, $respuesta);
        } else {
            $respuesta = Cache::get($nombre);
        }
        return $respuesta;
    }

    public function getBitacoraBuzonParte($parte_id){
        $bitacora = BitacoraBuzon::where('parte_id',$parte_id)->get();
        return $this->sendResponse($bitacora, 'SUCCESS');
    }
    public function generarConstanciaBuzonInterno($parte_id){
        try{
            $parte = Parte::find($parte_id);
            $solicitud = $parte->solicitud;
            $audiencia = $solicitud->expediente->audiencia->last();
            $html = "";
            if($parte->tipo_parte_id == 1){
                event(new GenerateDocumentResolution($audiencia->id, $solicitud->id, 63, 25,$parte->id));
            }else{
                event(new GenerateDocumentResolution($audiencia->id, $solicitud->id, 63, 26,null,$parte->id));
            }
            return $this->sendResponse($html, 'SUCCESS');
        }catch(Exception $e){
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensale: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'No se encontraron datos relacionados', 'data' => null], 200);
        }
    }
    public function generarConstanciaBuzon(Request $request){
        try{
            $parte_id=$request->parte_id;
            $parte = Parte::find($parte_id);
            $solicitud = $parte->solicitud;
            $audiencia = $parte->solicitud->expediente->audiencia->last();
            $html = "";
            if($parte->tipo_parte_id == 1){
                $plantilla_id = 27;
                $html = $this->renderDocumento($audiencia->id,$solicitud->id,$plantilla_id,$parte->id,null,"");
            }else{
                $plantilla_id = 28;
                $html = $this->renderDocumento($audiencia->id,$solicitud->id,$plantilla_id,null,$parte->id,"");
            }
            return $this->renderPDF($html,$plantilla_id );
        }catch(Exception $e){
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensale: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'No se encontraron datos relacionados', 'data' => null], 200);
        }
    }
}
