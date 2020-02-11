<?php

namespace App\Http\Controllers;

use App\Centro;
use App\Estado;
use App\EstatusSolicitud;
use Illuminate\Http\Request;
use \App\Solicitud;
use Validator;
use App\Filters\SolicitudFilter;
use App\Genero;
use App\GiroComercial;
use App\Jornada;
use App\Nacionalidad;
use App\ObjetoSolicitud;
use App\Parte;
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
        Solicitud::with('estatusSolicitud', 'objetoSolicitud')->get();
        // $solicitud = Solicitud::all();


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
        return view('expediente.solicitudes.create', compact('objeto_solicitudes','estatus_solicitudes','centros','tipos_vialidades','tipos_asentamientos','estados','jornadas','generos','nacionalidades','giros_comerciales'));
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
            'solicitante.*'
        ]);

        
        //    return response()->json($solicitud, 201);
        
        $solicitud = $request->input('solicitud');
        
        // // Solicitud
        $solicitud['user_id'] = 1;
        $solicitud['estatus_solicitud_id'] = 1;
        // dd($solicitud);
        $solicitudSaved = Solicitud::create($solicitud);
        $solicitantes = $request->input('solicitantes');
        foreach ($solicitantes as $key => $value) {
            $value['solicitud_id'] = $solicitudSaved['id'];
            $dato_laboral = $value['datos_laborales'];
            unset($value['datos_laborales']);
            if(isset($value["domicilios"])){
                $domicilios = $value["domicilios"];
                unset($value['domicilios']);
                
            }
            
            $parteSaved = ((Parte::create($value))->dato_laboral()->create($dato_laboral)->parte);
            foreach ($domicilios as $key => $domicilio) {
                unset($domicilio["tipoParteDomicilio"]);
                
                $domicilio["tipo_vialidad"] = "as";
                $domicilio["vialidad"] = "as";
                $domicilio["estado"] = "as";
                $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
            }
        }

        $solicitados = $request->input('solicitados');
        foreach ($solicitados as $key => $value) {
            if(isset($value["domicilios"])){
                $domicilios = $value["domicilios"];
                unset($value['domicilios']);
            }
            
            $value['solicitud_id'] = $solicitudSaved['id'];
            $parteSaved = Parte::create($value);  
            foreach ($domicilios as $key => $domicilio) {
                unset($domicilio["tipoParteDomicilio"]);
                $domicilio["tipo_vialidad"] = "as";
                $domicilio["vialidad"] = "as";
                $domicilio["estado"] = "as";
                $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
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
     * Display the specified resource.
     *
     * @param  Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function show(Solicitud $solicitud)
    {
        return response()->json( ['solicitudes' => $solicitud]);
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
        
        $solicitud->solicitante = $partes->where('tipo_parte_id',1)->first();
        
        $solicitud->solicitado = $partes->where('tipo_parte_id',2)->first();
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
        return view('expediente.solicitudes.edit', compact('solicitud','objeto_solicitudes','estatus_solicitudes','centros','tipos_vialidades','tipos_asentamientos','estados','jornadas','generos','nacionalidades','giros_comerciales'));
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
    //   $validator = Validator::make($request->all(), [
    //       'ratificada' => 'Boolean|required',
    //       'fecha_ratificacion' => 'required|Date',
    //       'fecha_recepcion' => 'required|Date',
    //       'fecha_conflicto' => 'required|Date',
    //       'observaciones' => 'required|max:500',
    //       'estatus_solicitud_id' => 'required|Integer',
    //       'objeto_solicitud_id' => 'required|Integer',
    //       'centro_id' => 'required|Integer',
    //       'user_id' => 'required|Integer',
    //   ]);
    //   if ($validator->fails()) {
    //       return response()->json($validator, 201);
    //                   // ->withInput();
    //   }
    //   $solicitud->update($request->all());

    //   return response()->json($solicitud, 200);
        $solicitudReq = $request->input('solicitud');
        
        $solicitanteReq = $request->input('solicitante');
        $solicitante = Parte::find($solicitanteReq['id']);
        $solicitante->update($solicitanteReq);
        
        $solicitadoReq = $request->input('solicitado');
        $solicitado = Parte::find($solicitadoReq['id']);
        $solicitado->update($solicitadoReq);
        
        // dd($solicitado );
        $solicitud->update($solicitudReq);
        return redirect('solicitudes')->with('success', 'Se actualizo la solicitud exitosamente');;
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
}
