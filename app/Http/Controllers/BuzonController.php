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


class BuzonController extends Controller
{
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
                $mail = $parte->contactos()->where("tipo_contacto_id",3)->orderBy('created_at', 'desc')->first();
                if($mail != null){
                    $correo = $mail->contacto;
                    $busqueda["correo"] = $correo;
                }
                if($correo != ""){
    //                Se crea el token
                    $token = app('hash')->make(str_random(8));
                    if (!Cache::has($token)) {
                        Cache::put($token, $busqueda,now()->addMinutes(50));
                    }
                    $respuesta = Cache::get($token);
    //                dd($respuesta);
                    $parte->token = $token;
                    $parte->email = $correo;
                    $composicion = base64_encode($token)."/".base64_encode($correo);
                    $liga = env('APP_URL')."/validar_token/".$composicion;
                    Mail::to($correo)->send(new AccesoBuzonMail($parte,$liga));
                    return array(["correo" => true,"mensaje" => 'Se envio un correo con el acceso a la dirección '.$correo]);
                }else{
                    return array(["correo" => false,"mensaje" => 'No hay correos registrados']);
                }
            }else{
                return $this->sendError('No hay registros con este dato', 'Error');
            }
        }else{
            return $this->sendError('No se encontró el expediente', 'Error');
        }
    	
    }
    public function AccesoBuzon(){
        $partesBusqueda = Parte::where("correo_buzon", $this->request->correo_buzon)->where("password_buzon",$this->request->password_buzon)->get();
        if($partesBusqueda != null){
            $identificador = "";
            if($partesBusqueda[0]->tipo_persona_id == 1){
                $identificador = $partesBusqueda[0]->curp;
                $partes = Parte::where("curp",$partesBusqueda[0]->curp)->get();
            }else{
                $identificador = $partesBusqueda[0]->rfc;
                $partes = Parte::where("rfc",$partesBusqueda[0]->rfc)->get();
            }
            $solicitudes = [];
            foreach($partes as $parte){         
                // BitacoraBuzon::create(['parte_id'=>$parte->id,'descripcion'=>'Se genera el documento (Nombre documento)','tipo_movimiento'=>'Documento']);
                BitacoraBuzon::create(['parte_id'=>$parte->id,'descripcion'=>'Consulta de buzón','tipo_movimiento'=>'Consulta','clabe_identificacion'=>$identificador]);
                $solicitud = $parte->solicitud;
                if($solicitud->expediente != null){
                    $solicitud->acciones = $this->getAcciones($solicitud, $solicitud->partes, $solicitud->expediente);
                    $solicitud->parte = $parte;
                    foreach($solicitud->expediente->audiencia as $audiencia){
                        $solicitud->documentos = $solicitud->documentos->merge($audiencia->documentos);
                    }
                    $solicitudes[]=$solicitud;
                }
            }

            return view("buzon.buzon", compact('solicitudes'));
        }else{
            return redirect()->back();
        }
    }
    Public function validar_token(){
        $token = base64_decode($this->request->token,true);
        $correo = base64_decode($this->request->correo);
        if($token != "" && $correo != ""){
            if(Cache::has($token)){
                $busqueda = Cache::get($token);
                if($correo == $busqueda["correo"]){
                    if($busqueda["tipo_persona_id"] == 1){
                        $partes = Parte::where("curp",$busqueda["busqueda"])->get();
                    }else{
                        $partes = Parte::where("rfc",$busqueda["busqueda"])->get();
                    }
                    $solicitudes = [];
                    foreach($partes as $parte){
                        BitacoraBuzon::create(['parte_id'=>$parte->id,'descripcion'=>'Consulta de buzón','tipo_movimiento'=>'Consulta','clabe_identificacion'=>$busqueda["busqueda"]]);
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
                    return view("buzon.solicitud")->with("Error","Correo del que ingresas no coincide con el token");
                }
            }else{
                return view("buzon.solicitud")->with("Error","El token es incorrecto solicita uno nuevo");
            }
        }else{
            return view("buzon.solicitud")->with("Error","La estructura de la liga no es correcta");
        }
    }
    public function BuzonElectronico(){
//        Obtenemos todas las solicitudes de la persona
        $partes = Parte::where("rfc","SAZE941229ED8")->get();
        $solicitudes = [];
        foreach($partes as $parte){
            $solicitud = $parte->solicitud;
            if($solicitud->expediente != null){
                $solicitud->acciones = $this->getAcciones($solicitud, $solicitud->partes, $solicitud->expediente);
                $solicitud->parte = $parte;
                $solicitudes[]=$solicitud;
            }
        }
        return view("buzon.buzon", compact('solicitudes'));
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
    public function generarConstanciaBuzon($parte_id){
        $parte = Parte::find($parte_id);
        $solicitud = $parte->solicitud;
        if($parte->tipo_parte_id == 1){
            event(new GenerateDocumentResolution("", $solicitud->id, 63, 25,$parte->id));
        }else{
            event(new GenerateDocumentResolution("", $solicitud->id, 63, 26,null,$parte->id));
        }
        
        return $this->sendResponse([], 'SUCCESS');
    }
}
