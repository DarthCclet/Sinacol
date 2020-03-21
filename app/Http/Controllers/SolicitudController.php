<?php

namespace App\Http\Controllers;

use App\Centro;
use App\DatoLaboral;
use App\Domicilio;
use App\Estado;
use App\Expediente;
use App\Audiencia;
use App\EstatusSolicitud;
use App\Expediente;
use Illuminate\Http\Request;
use \App\Solicitud;
use Validator;
use App\Filters\SolicitudFilter;
use App\Genero;
use App\GiroComercial;
use App\Jornada;
use App\Nacionalidad;
use App\ObjetoSolicitud;
use App\Ocupacion;
use App\Parte;
use App\Rules\Curp;
use App\TipoAsentamiento;
use App\TipoVialidad;
use Illuminate\Support\Facades\Auth;


class SolicitudController extends Controller
{

    /**
     * Instancia del request
     * @var Request
     */
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
        // Filtramos los usuarios con los parametros que vengan en el request
        $solicitud = (new SolicitudFilter(Solicitud::query(), $this->request))
            ->searchWith(Solicitud::class)
            ->filter();

         // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('paginate')) {
            $solicitud = $solicitud->paginate($this->request->get('per_page', 10));
            
        } else {
            $solicitud = $solicitud->get();
        }

        // // Para cada objeto obtenido cargamos sus relaciones.
        $solicitud = tap($solicitud)->each(function ($solicitud) {
            $solicitud->loadDataFromRequest();
        });
        // return $this->sendResponse($solicitud, 'SUCCESS');

        if ($this->request->wantsJson()) {
            return $this->sendResponse($solicitud, 'SUCCESS');
        }
        return view('expediente.solicitudes.index', compact('solicitud'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $objeto_solicitudes = array_pluck(ObjetoSolicitud::all(),'nombre','id');
        $estatus_solicitudes = array_pluck(EstatusSolicitud::all(),'nombre','id');
        $centros = array_pluck(Centro::all(),'nombre','id');
        $tipos_vialidades = array_pluck(TipoVialidad::all(),'nombre','id');
        $tipos_asentamientos = array_pluck(TipoAsentamiento::all(),'nombre','id');
        $estados = array_pluck(Estado::all(),'nombre','id');
        $jornadas = array_pluck(Jornada::all(),'nombre','id');
        $generos = array_pluck(Genero::all(),'nombre','id');
        $nacionalidades = array_pluck(Nacionalidad::all(),'nombre','id');
        $giros_comerciales = array_pluck(GiroComercial::all(),'nombre','id');
        $ocupaciones = array_pluck(Ocupacion::all(),'nombre','id');
        return view('expediente.solicitudes.create', compact('objeto_solicitudes','estatus_solicitudes','centros','tipos_vialidades','tipos_asentamientos','estados','jornadas','generos','nacionalidades','giros_comerciales','ocupaciones'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'solicitud.observaciones' => 'required|max:500',
            'solicitud.estatus_solicitud_id' => 'required',
            'solicitud.fecha_conflicto' => 'required',
            'solicitud.fecha_ratificacion' => 'required',
            'solicitud.fecha_recepcion' => 'required',
            'solicitud.ratificada' => 'required',
            'solicitud.solicita_excepcion' => 'required',

            'solicitantes.*.nombre' => 'required',
            'solicitantes.*.primer_apellido' => 'required',
            'solicitantes.*.rfc' => 'required',
            'solicitantes.*.tipo_parte_id' => 'required',
            'solicitantes.*.tipo_persona_id' => 'required',
            'solicitantes.*.curp' => ['required',new Curp],
            'solicitantes.*.edad' => 'required',
            'solicitantes.*.entidad_nacimiento_id' => 'required',
            'solicitantes.*.fecha_nacimiento' => 'required',
            'solicitantes.*.genero_id' => 'required',
            'solicitantes.*.giro_comercial_id' => 'required',
            'solicitantes.*.nacionalidad_id' => 'required',

            'solicitados.*.nombre' => 'required',
            'solicitados.*.primer_apellido' => 'required',
            'solicitados.*.rfc' => 'required',
            'solicitados.*.tipo_parte_id' => 'required',
            'solicitados.*.tipo_persona_id' => 'required',
            'solicitados.*.curp' => ['required',new Curp],
            'solicitados.*.edad' => 'required',
            'solicitados.*.entidad_nacimiento_id' => 'required',
            'solicitados.*.fecha_nacimiento' => 'required',
            'solicitados.*.genero_id' => 'required',
            'solicitados.*.giro_comercial_id' => 'required',
            'solicitados.*.nacionalidad_id' => 'required',
        ]);
        
        $solicitud = $request->input('solicitud');
        
        // // Solicitud
        $solicitud['user_id'] = 1;
        $solicitud['estatus_solicitud_id'] = 1;
        $solicitud['centro_id'] = $this->getCentroId();
        // dd($solicitud);
        $solicitudSaved = Solicitud::create($solicitud);

        $objeto_solicitudes = $request->input('objeto_solicitudes');
        
        foreach ($objeto_solicitudes as $key => $value) {
            $solicitudSaved->objeto_solicitudes()->attach($value['objeto_solicitud_id']);   
        }
        
        $solicitantes = $request->input('solicitantes');
        
        foreach ($solicitantes as $key => $value) {
            $value['solicitud_id'] = $solicitudSaved['id'];
            unset($value['activo']);
            $dato_laboral = $value['dato_laboral'];
            
            unset($value['dato_laboral']);
            if(isset($value["domicilios"])){
                $domicilio = $value["domicilios"][0];
                unset($value['domicilios']);
            }
            
            // dd($value);
            $parteSaved = (Parte::create($value)->dato_laboral()->create($dato_laboral)->parte);
            // dd($domicilio);
            // foreach ($domicilios as $key => $domicilio) {
                $domicilio["tipo_vialidad"] = "as";
                $domicilio["vialidad"] = "as";
                $domicilio["estado"] = "as";
                $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
            // }
        }
        
        $solicitados = $request->input('solicitados');
        
        foreach ($solicitados as $key => $value) {
            unset($value['activo']);
            $domicilios = Array();
            if(isset($value["domicilios"])){
                $domicilios = $value["domicilios"];
                unset($value['domicilios']);
            }
            
            $value['solicitud_id'] = $solicitudSaved['id'];
            $parteSaved = Parte::create($value);  
            if(count($domicilios) > 0){
                foreach ($domicilios as $key => $domicilio) {
                    $domicilio["tipo_vialidad"] = "as";
                    $domicilio["vialidad"] = "as";
                    $domicilio["estado"] = "as";
                    $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
                }
            } 
        }

        // // Para cada objeto obtenido cargamos sus relaciones.
        $solicitudSaved = tap($solicitudSaved)->each(function ($solicitudSaved) {
            $solicitudSaved->loadDataFromRequest();
        });
        if ($this->request->wantsJson()) {
            return $this->sendResponse($solicitudSaved, 'SUCCESS');
        }
        return redirect('solicitudes')->with('success', 'Se ha creado la solicitud exitosamente');
    }

    /**
     * Funcion para obtener el centro asignado
     *
     * @return int
     */
    private function getCentroId(){
        $centro = Centro::inRandomOrder()->first();
        return $centro->id;
    }

    /**
     * Display the specified resource.
     *
     * @param  Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $solicitud = Solicitud::find($id);
        $parte = Parte::all()->where('solicitud_id',$solicitud->id);

        $partes = $solicitud->partes()->get();//->where('tipo_parte_id',3)->get()->first()
        
        $solicitantes = $partes->where('tipo_parte_id',1);
        
        foreach ($solicitantes as $key => $value) {
            $value->dato_laboral;
            $value->domicilios;
            $solicitantes[$key]["activo"] = 1;
        }
        $solicitados = $partes->where('tipo_parte_id',2);
        foreach ($solicitados as $key => $value) {
            $value->domicilios;
            $solicitados[$key]["activo"] = 1;
        }
        $solicitud->objeto_solicitudes;
        $solicitud["solicitados"] = $solicitados;
        $solicitud["solicitantes"] = $solicitantes;
        return $solicitud;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $solicitud = Solicitud::find($id);
        $parte = Parte::all()->where('solicitud_id',$solicitud->id);

        $partes = $solicitud->partes()->get();//->where('tipo_parte_id',3)->get()->first()
        $expediente = Expediente::where("solicitud_id" ,"=",$solicitud->id)->get();
        if(count($expediente) > 0){
            $audiencias = Audiencia::where("expediente_id" ,"=",$expediente[0]->id)->get();
        }else{
            $audiencias = array();
        }
        
        
        $objeto_solicitudes = array_pluck(ObjetoSolicitud::all(),'nombre','id');
        $estatus_solicitudes = array_pluck(EstatusSolicitud::all(),'nombre','id');
        $centros = array_pluck(Centro::all(),'nombre','id');
        $giros_comerciales = array_pluck(GiroComercial::all(),'nombre','id');
        $tipos_vialidades = array_pluck(TipoVialidad::all(),'nombre','id');
        $tipos_asentamientos = array_pluck(TipoAsentamiento::all(),'nombre','id');
        $estados = array_pluck(Estado::all(),'nombre','id');
        $jornadas = array_pluck(Jornada::all(),'nombre','id');
        $generos = array_pluck(Genero::all(),'nombre','id');
        $nacionalidades = array_pluck(Nacionalidad::all(),'nombre','id');
        $ocupaciones = array_pluck(Ocupacion::all(),'nombre','id');
//        dd($solicitud);
        return view('expediente.solicitudes.edit', compact('solicitud','objeto_solicitudes','estatus_solicitudes','centros','tipos_vialidades','tipos_asentamientos','estados','jornadas','generos','nacionalidades','giros_comerciales','ocupaciones','expediente','audiencias'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'solicitud.observaciones' => 'required|max:500',
            'solicitud.estatus_solicitud_id' => 'required',
            'solicitud.fecha_conflicto' => 'required',
            'solicitud.fecha_ratificacion' => 'required',
            'solicitud.fecha_recepcion' => 'required',
            'solicitud.ratificada' => 'required',
            'solicitud.solicita_excepcion' => 'required',

            'solicitantes.*.nombre' => 'required',
            'solicitantes.*.primer_apellido' => 'required',
            'solicitantes.*.rfc' => 'required',
            'solicitantes.*.tipo_parte_id' => 'required',
            'solicitantes.*.tipo_persona_id' => 'required',
            'solicitantes.*.curp' => ['required',new Curp],
            'solicitantes.*.edad' => 'required',
            'solicitantes.*.entidad_nacimiento_id' => 'required',
            'solicitantes.*.fecha_nacimiento' => 'required',
            'solicitantes.*.genero_id' => 'required',
            'solicitantes.*.giro_comercial_id' => 'required',
            'solicitantes.*.nacionalidad_id' => 'required',
            
            'solicitados.*.nombre' => 'required',
            'solicitados.*.primer_apellido' => 'required',
            'solicitados.*.rfc' => 'required',
            'solicitados.*.tipo_parte_id' => 'required',
            'solicitados.*.tipo_persona_id' => 'required',
            'solicitados.*.curp' => ['required',new Curp],
            'solicitados.*.edad' => 'required',
            'solicitados.*.entidad_nacimiento_id' => 'required',
            'solicitados.*.fecha_nacimiento' => 'required',
            'solicitados.*.genero_id' => 'required',
            'solicitados.*.giro_comercial_id' => 'required',
            'solicitados.*.nacionalidad_id' => 'required',
        ]);
        $solicitud = $request->input('solicitud');
        
        // // Solicitud
        $solicitud['user_id'] = 1;
        $solicitud['estatus_solicitud_id'] = 1;
        // dd($solicitud);
        $solicitudUp = Solicitud::find($solicitud['id']);
        $exito = $solicitudUp->update($solicitud);
        if($exito){
            $solicitudSaved = Solicitud::find($solicitud['id']);
        }
        

        $objeto_solicitudes = $request->input('objeto_solicitudes');
        
        $arrObjetoSolicitudes = [];
        foreach ($objeto_solicitudes as $key => $value) {
            array_push($arrObjetoSolicitudes,$value['objeto_solicitud_id']);
        }
        $solicitudSaved->objeto_solicitudes()->sync($arrObjetoSolicitudes);
        
        $solicitantes = $request->input('solicitantes');
        
        foreach ($solicitantes as $key => $value) {
            $value['solicitud_id'] = $solicitudSaved['id'];
            unset($value['activo']);
            $dato_laboral = $value['dato_laboral'];
            
            unset($value['dato_laboral']);
            if(isset($value["domicilios"])){
                $domicilio = $value["domicilios"][0];
                unset($value['domicilios']);
            }
            
            // dd($value);
            if(!isset($value["id"]) || $value["id"] == ""){
                $parteSaved = (Parte::create($value)->dato_laboral()->create($dato_laboral)->parte);
            // dd($domicilio);
            // foreach ($domicilios as $key => $domicilio) {
                $domicilio["tipo_vialidad"] = "as";
                $domicilio["estado"] = "as";
                $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
            }else{
                $parteSaved = Parte::find($value['id']);
                $parteSaved = $parteSaved->update($value);
                $dato_laboralUp =  DatoLaboral::find($dato_laboral["id"]);
                $dato_laboralUp->update($dato_laboral);
                $domicilioUp =  Domicilio::find($domicilio["id"]);
                $domicilioUp->update($domicilio);
            }
            
            // }
        }
        
        $solicitados = $request->input('solicitados');
        
        foreach ($solicitados as $key => $value) {
            unset($value['activo']);
            $domicilios = Array();
            if(isset($value["domicilios"])){
                $domicilios = $value["domicilios"];
                unset($value['domicilios']);
            }
            
            $value['solicitud_id'] = $solicitudSaved['id'];
            if(!isset($value["id"]) || $value["id"] == ""){
                $parteSaved = Parte::create($value);  
                if(count($domicilios) > 0){
                    foreach ($domicilios as $key => $domicilio) {
                        $domicilio["tipo_vialidad"] = "as";
                        $domicilio["vialidad"] = "as";
                        $domicilio["estado"] = "as";
                        $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
                    }
                }     
            }else{
                $parteSaved = Parte::find($value['id']);
                $parteSaved = $parteSaved->update($value);
                foreach ($domicilios as $key => $domicilio) {
                    $domicilioUp =  Domicilio::find($domicilio["id"]);
                    $domicilioUp->update($domicilio);
                }
            }
            
        }

        // // Para cada objeto obtenido cargamos sus relaciones.
        $solicitudSaved = tap($solicitudSaved)->each(function ($solicitudSaved) {
            $solicitudSaved->loadDataFromRequest();
        });
        if ($this->request->wantsJson()) {
            return $this->sendResponse($solicitudSaved, 'SUCCESS');
        }
        return redirect('solicitudes')->with('success', 'Se ha creado la solicitud exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function destroy(Solicitud $solicitud)
    {
      $solicitud->delete();
      return response()->json(null,204);
    }
    
    public function Ratificar(Request $request){
        $solicitud= Solicitud::find($request->id);
        //Indicamos que la solicitud ha sido ratificada
        $solicitud->update(["estatus_solicitud_id" => 2,"ratificada" => true]);
        //Creamos el expediente de la solicitud
        $expediente = Expediente::create(["solicitud_id" => $request->id,"folio" => "2","anio" => "2020","consecutivo" => "1"]);
        return $solicitud;
    }
}
