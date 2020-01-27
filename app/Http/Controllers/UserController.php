<?php

namespace App\Http\Controllers;

use App\Filters\UserFilter;
use App\Persona;
use App\TipoPersona;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
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
     * Muestra un listado de usuarios
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        User::with('persona')->get();

        // Filtramos los usuarios con los parametros que vengan en el request
        $users = (new UserFilter(User::query(), $this->request))
            ->searchWith(User::class)
            ->filter();

        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $users = $users->get();
        } else {
            $users = $users->paginate($this->request->get('per_page', 10));
        }

        // Para cada objeto obtenido cargamos sus relaciones.
        $users = tap($users)->each(function ($user) {
            $user->loadDataFromRequest();
        });

        // Si el request solicita respuesta en JSON (es el caso de API y requests ajax)
        if ($this->request->wantsJson()) {
            return $this->sendResponse($users, 'SUCCESS');
        }

        // Si no se requiere respuesta JSON entonces regresamos la vista del admin de usuarios
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'users.name' => 'required|unique:users|max:128',
                'users.email' => 'required|unique:users|email',
                'users.password' => 'required|confirmed',
                'personas.nombre' => 'required|alpha|max:60',
                'personas.primer_apellido' => 'required|alpha|max:60',
                'personas.segundo_apellido' => 'alpha|nullable|max:60',
                'personas.rfc' => 'alphanum|required|max:12',
            ]
        );

        $persona = $request->input('personas');

        //Los usuarios web sólo pueden estar asociados a personas físicas
        $persona['tipo_persona_id'] = TipoPersona::where('abreviatura', 'F')->first()->id;

        $user = (Persona::create($persona))->user()->create($request->input('users'))->load('persona');

        if ($this->request->wantsJson()) {
            return $this->sendResponse($user, 'SUCCESS');
        }

        return redirect()->route('users.create')->with('success', 'Se ha creado el usuario exitosamente');
    }

    /**
     * Display the specified resource.
     *
     * @param int $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user->load('persona');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate(
            [
                'users.name' => 'required|max:128|'.Rule::unique('users')->ignore($user->id),
                'users.email' => 'required|email|'.Rule::unique('users')->ignore($user->id),
                'users.password' => 'confirmed|min:6|nullable',
                'personas.nombre' => 'required|alpha|max:60',
                'personas.primer_apellido' => 'required|alpha|max:60',
                'personas.segundo_apellido' => 'alpha|nullable|max:60',
                'personas.rfc' => 'alphanum|required|size:12',
            ]
        );

        $data = $request->input('users');

        //Sólo cuando hay cambios de password incluimos el campo de password
        if( empty( $data['password'] ) ){
            unset($data['password']);
        }

        $user->fill($data)->save();
        $user->persona->fill($request->input('personas'))->save();

        if ($this->request->wantsJson()) {
            return $this->sendResponse($user, 'SUCCESS');
        }

        return redirect()->route('users.edit',[$user])->with('success', 'Se ha actualizado el usuario exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::destroy($id);

        if ($this->request->wantsJson()) {
            return $this->sendResponse($id, 'SUCCESS');
        }

        return redirect()->route('users.index')->with('success', 'Se ha eliminado el usuario exitosamente');

    }
}
