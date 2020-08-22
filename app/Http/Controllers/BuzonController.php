<?php

namespace App\Http\Controllers;

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
    	if($this->request->tipo_persona_id == 2){
            $parte = Parte::where("rfc",$this->request->rfc)->orderBy('created_at', 'desc')->first();
            $busqueda["busqueda"] = $this->request->rfc;
        }else{
            $parte = Parte::where("curp",$this->request->curp)->orderBy('created_at', 'desc')->first();
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
                $token = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

                if (!Cache::has($token)) {
                    Cache::put($token, $busqueda,now()->addMinutes(50));
                }
                $respuesta = Cache::get($token);
                $parte->token = $token;
                $parte->email = $correo;
                $composicion = base64_encode($token)."/".base64_encode($correo);
                $liga = "http://conciliacion.test/validar_token/".$composicion;
                Mail::to($correo)->send(new AccesoBuzonMail($parte,$liga));
                
                return array("mensaje" => 'Se envio un correo con el acceso a la dirección '.$correo);
            }else{
                return $this->sendError('No hay correos registrados ', 'Error');
            }
        }else{
            return $this->sendError('No hay registros con este dato', 'Error');
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
                    $tipos_asentamientos = $this->cacheModel('tipos_asentamientos',TipoAsentamiento::class);
                    $estados = $this->cacheModel('estados',Estado::class);
                    $tipos_vialidades = $this->cacheModel('tipos_vialidades',TipoVialidad::class);
                    $municipios = array_pluck(Municipio::all(),'municipio','id');
                    return view("buzon.buzon", compact('solicitudes','tipos_asentamientos','estados','tipos_vialidades','municipios'));
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
    
}
