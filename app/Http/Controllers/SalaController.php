<?php

namespace App\Http\Controllers;
use App\Filters\CatalogoFilter;
use App\Sala;
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
        //
        // return Sala::all();


        // Filtramos los usuarios con los parametros que vengan en el request
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
        return Sala::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Sala::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $sala->fill($request->all())->save();
        return $sala;
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
        return 204;
    }
}
