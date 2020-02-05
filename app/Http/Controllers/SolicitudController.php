<?php

namespace App\Http\Controllers;

use App\Centro;
use App\EstatusSolicitud;
use Illuminate\Http\Request;
use \App\Solicitud;
use Validator;
use App\Filters\SolicitudFilter;
use App\ObjetoSolicitud;
use App\Parte;

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
        $objetoSolicitudes = ObjetoSolicitud::all();
        $estatusSolicitudes = EstatusSolicitud::all();
        $centros = Centro::all();
        // return $this->sendResponse($solicitud, 'SUCCESS');

        if ($this->request->wantsJson()) {
            return $this->sendResponse($solicitud, 'SUCCESS');
        }
        return view('expediente.solicitudes.index', compact('solicitud','objetoSolicitudes','estatusSolicitudes','centros'));
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
        return view('expediente.solicitudes.create', compact('objeto_solicitudes','estatus_solicitudes','centros'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    //     $validator = Validator::make($request->all(), [
    //         'ratificada' => 'Boolean',
    //         'fecha_ratificacion' => 'required|Date',
    //         'fecha_recepcion' => 'required|Date',
    //         'fecha_conflicto' => 'required|Date',
    //         'observaciones' => 'required|max:500',
    //         'estatus_solicitud_id' => 'required|Integer',
    //         'objeto_solicitud_id' => 'required|Integer',
    //         'centro_id' => 'required|Integer',
    //         'user_id' => 'required|Integer',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator, 201);
    //                     // ->withInput();
    //     }
    //    $solicitud = Solicitud::create($request->all());

    //    return response()->json($solicitud, 201);
        
        $solicitud = $request->input('solicitud');

        // Solicitud
        $solicitud['user_id'] = 1;
        
        if(!isset($solicitud['ratificada'])){
            $solicitud['ratificada'] = false;
        }
        $solicitud = Solicitud::create($solicitud);
        
        // solicitado
        $solicitado = $request->input('solicitado');
        $solicitado["solicitud_id"] = $solicitud['id'];
        $solicitado["tipo_parte_id"] = 2;
        $solicitado["genero_id"] = 1;
        $solicitado["nacionalidad_id"] = 1;
        $solicitado["entidad_nacimiento_id"] = '01';
        $solicitado["giro_comercial_id"] = 1;
        $solicitado["grupo_prioritario_id"] = 1;
        
        $solicitado = Parte::create($solicitado);
        // Solicitante 
        $solicitante = $request->input('solicitante');
        $solicitante["solicitud_id"] = $solicitud['id'];
        $solicitante["tipo_parte_id"] = 1;
        $solicitante["genero_id"] = 1;
        $solicitante["nacionalidad_id"] = 1;
        $solicitante["entidad_nacimiento_id"] = '01';
        $solicitante["giro_comercial_id"] = 1;
        $solicitante["grupo_prioritario_id"] = 1;

        $solicitante = Parte::create($solicitante);
        // dd($solicitante['id']);
        // dd($solicitud);
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

        // dd([$parte->first(),$solicitud->partes()->where('tipo_parte_id',3)->get()]);
        $partes = $solicitud->partes()->get();//->where('tipo_parte_id',3)->get()->first()
        
        $solicitud->solicitante = $partes->where('tipo_parte_id',1)->first();
        
        $solicitud->solicitado = $partes->where('tipo_parte_id',2)->first();
        $objeto_solicitudes = array_pluck(ObjetoSolicitud::all(),'nombre','id');
        $estatus_solicitudes = array_pluck(EstatusSolicitud::all(),'nombre','id');
        $centros = array_pluck(Centro::all(),'nombre','id');
        return view('expediente.solicitudes.edit', compact('solicitud','objeto_solicitudes','estatus_solicitudes','centros'));
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
