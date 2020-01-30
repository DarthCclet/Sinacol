<?php

namespace App\Http\Controllers;
use App\Filters\CatalogoFilter;
use App\Sala;
use Validator;
use Illuminate\Http\Request;

class SalaController extends Controller
{
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
        // return Sala::all();

        // Filtramos las salas con los parametros que vengan en el request
        $salas = (new CatalogoFilter(Sala::query(), $this->request))
            ->searchWith(Sala::class)
            ->filter();

        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $salas = $salas->get();
        } else {
            $salas = $salas->paginate($this->request->get('per_page', 10));
        }

        // Para cada objeto obtenido cargamos sus relaciones.
        // $salas = tap($salas)->each(function ($sala) {
        //     $sala->loadDataFromRequest();
        // });

        // Si el request solicita respuesta en JSON (es el caso de API y requests ajax)
        if ($this->request->wantsJson()) {
            return $this->sendResponse($salas, 'SUCCESS');
        }
        return view('centros.salas.index', compact('salas'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('centros.salas.create');
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
            'sala' => 'required|max:100',
            'centro_id' => 'required|Integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        $sala = Sala::create($request->all());
        // return response()->json($sala, 201);



      return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sala = Sala::find($id);
        return $sala;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $sala = Sala::find($id);

      return view('centros.salas.edit')->with('sala', $sala);
      // return view('centros.salas.edit', compact('sala'));

      // return view('centros.salas.edit')->withSala($sala);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sala $sala)
    {
        $validator = Validator::make($request->all(), [
            'sala' => 'required|max:100',
            'centro_id' => 'required|Integer'
        ]);
        if ($validator->fails()) {
          return Redirect::to('salas/' . $sala->id . '/edit')
                ->withErrors($validator);
            // return response()->json($validator, 201);
        }else{
          $sala->update($request->all());
          // $sala->fill($request->all())->save();
        }
        // return redirect()->back();

        return redirect('salas');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sala = Sala::findOrFail($id)->delete();
        return redirect('salas');
        // return 204;
    }
}
