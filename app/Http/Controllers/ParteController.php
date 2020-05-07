<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Parte;
use App\Contacto;
use App\TipoContacto;
use App\AudienciaParte;
use App\Filters\ParteFilter;

class ParteController extends Controller
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
        $partes = Parte::all();
        
        // Filtramos los usuarios con los parametros que vengan en el request
        $partes = (new ParteFilter(Parte::query(), $this->request))
            ->searchWith(Parte::class)
            ->filter();

         // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $partes = $partes->get();
        } else {
            $partes = $partes->paginate($this->request->get('per_page', 10));
        }

        // // Para cada objeto obtenido cargamos sus relaciones.
        $partes = tap($partes)->each(function ($partes) {
            $partes->loadDataFromRequest();
        });

        return $this->sendResponse($partes, 'SUCCESS');
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
        $validator = Validator::make($request->all(), [
            'solicitud_id' => 'required|Integer',
            'tipo_parte_id' => 'required|Integer',
            'genero_id' => 'required|Integer',
            'tipo_persona_id' => 'required|Integer',
            'nacionalidad_id' => 'required|Integer',
            'entidad_nacimiento_id' => 'required|Integer',
            'fecha_nacimiento' => 'required|Date',
            'nombre' => 'required|max:500|String',
            'primer_apellido' => 'required|max:500|String',
            'segundo_apellido' => 'required|max:500|String',
            'nombre_comercial' => 'required|max:500|String',
            'edad' => 'required|max:500|String',
            'rfc' => 'required|max:500|String',
            'curp' => 'required|max:500|String',
        ]);

        if ($validator->fails()) {
            return response()->json($validator, 201);
                        // ->withInput();
        }
       $parte = Parte::create($request->all());

       return response()->json($parte, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Parte $parte)
    {
        return $parte;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Parte $parte)
    {
        return $parte;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Parte $parte)
    {
        $validator = Validator::make($request->all(), [
            'solicitud_id' => 'required|Integer',
            'tipo_parte_id' => 'required|Integer',
            'genero_id' => 'required|Integer',
            'tipo_persona_id' => 'required|Integer',
            'nacionalidad_id' => 'required|Integer',
            'entidad_nacimiento_id' => 'required|Integer',
            'fecha_nacimiento' => 'required|Date',
            'nombre' => 'required|max:500|String',
            'primer_apellido' => 'required|max:500|String',
            'segundo_apellido' => 'required|max:500|String',
            'nombre_comercial' => 'required|max:500|String',
            'edad' => 'required|max:500|String',
            'rfc' => 'required|max:500|String',
            'curp' => 'required|max:500|String',
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
                        // ->withInput();
        }
        $parte->update($request->all());
  
        return response()->json($parte, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Parte $parte)
    {
        $parte->delete();
      return response()->json(null,204);
    }
    
    /**
     * Funcion para obtener el representante legal de una parte
     * @param id $id
     * @return parte
     */
    public function GetRepresentanteLegal($id){
        $representantes = Parte::where("parte_representada_id",$id)->where("representante",true)->get();
        if($representantes != null && $representantes != ""){
            foreach($representantes as $key => $representante){
                foreach($representante->contactos as $key2 => $contactos){
                    $representantes[$key]->contactos[$key2]->tipo_contacto = $contactos->tipo_contacto;
                    
                }
            }
        }
        return $representantes;
    }
    
    
    function GuardarRepresentanteLegal(Request $request){
        if($request->parte_id != "" && $request->parte_id != null){
            $parte = Parte::find($request->parte_id);
            $parte->update([
                "curp" => $request->curp,
                "nombre" => $request->nombre,
                "primer_apellido" => $request->primer_apellido,
                "segundo_apellido" => $request->segundo_apellido,
                "fecha_nacimiento" => $request->fecha_nacimiento,
                "genero_id" => $request->genero_id,
                "instrumento" => $request->instrumento,
                "genero_id" => $request->genero_id,
                "feha_instrumento" => $request->feha_instrumento,
                "numero_notaria" => $request->numero_notaria,
                "nombre_notario" => $request->nombre_notario,
                "localidad_notaria" => $request->localidad_notaria
            ]);
        }else{
            $parte_representada = Parte::find($request->parte_representada_id);
            $parte = Parte::create([
                "solicitud_id" => $parte_representada->solicitud->id,
                "tipo_parte_id" => 3,
                "tipo_persona_id" => 1,
                "rfc" => "NOAPLICA",
                "curp" => $request->curp,
                "nombre" => $request->nombre,
                "primer_apellido" => $request->primer_apellido,
                "segundo_apellido" => $request->segundo_apellido,
                "fecha_nacimiento" => $request->fecha_nacimiento,
                "genero_id" => $request->genero_id,
                "instrumento" => $request->instrumento,
                "genero_id" => $request->genero_id,
                "feha_instrumento" => $request->feha_instrumento,
                "numero_notaria" => $request->numero_notaria,
                "nombre_notario" => $request->nombre_notario,
                "localidad_notaria" => $request->localidad_notaria,
                "parte_representada_id" => $request->parte_representada_id,
                "representante" => true
            ]);
            foreach($request->listaContactos as $contacto){
                $parte->contactos()->create([
                    "contacto" => $contacto["contacto"],
                    "tipo_contacto_id" => $contacto["tipo_contacto_id"],
                ]);
            }
            // Creamos la relacion en audiencias_partes
            AudienciaParte::create(["audiencia_id" => $request->audiencia_id,"parte_id" => $parte->id]);
        }
        return $parte;
    }
    
    public function AgregarContactoRepresentante(Request $request){
        $representante = Parte::find($request->parte_id);
        $representante->contactos()->create(["tipo_contacto_id" => $request->tipo_contacto_id,"contacto" => $request->contacto]);
        foreach($representante->contactos as $key2 => $contactos){
            $representante->contactos[$key2]->tipo_contacto = $contactos->tipo_contacto;
        }
        return $representante->contactos;
    }
    public function EliminarContactoRepresentante(Request $request){
        $contacto = Contacto::find($request->contacto_id)->delete();
        $representante = Parte::find($request->parte_id);
        foreach($representante->contactos as $key2 => $contactos){
            $representante->contactos[$key2]->tipo_contacto = $contactos->tipo_contacto;
        }
        return $representante->contactos;
    }
}
