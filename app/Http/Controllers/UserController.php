<?php

namespace App\Http\Controllers;

use App\Filters\UserFilter;
use App\Persona;
use App\TipoPersona;
use App\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Traits\Menu;
use App\Centro;
use App\UsuarioCentro;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use Menu;
    /**
     * Instancia del request
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
//        $this->middleware('auth');
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
        if(!auth()->user()->hasRole('Super Usuario')){
            $users->where("centro_id", auth()->user()->centro_id);
        }

        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
//        if ($this->request->get('all')) {
//            $users = $users->get();
//        } else {
            $users = $users->paginate($this->request->get('per_page', 10));
//        }

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
        $roles = Role::all();
        $centros = Centro::pluck('nombre', 'id');
        return view('admin.users.create', compact('roles','centros'));
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
                'personas.nombre' => 'required|regex:/^[\pL\s\-]+$/u|max:60',
                'personas.primer_apellido' => 'required|regex:/^[\pL\s\-]+$/u|max:60',
                'personas.curp' => 'alphanum|required|max:18',
            ]
        );

        $persona = $request->input('personas');
        //Los usuarios web sólo pueden estar asociados a personas físicas
        $persona['tipo_persona_id'] = TipoPersona::where('abreviatura', 'F')->first()->id;
        $userRequest = $request->input('users');
        $userRequest['email_verified_at'] = now()->format('Y-m-d H:i:s');
        $persona = Persona::create($persona);
        $user = User::create($userRequest);
        $user->persona_id = $persona->id;
        $user->email_verified_at = now()->format('Y-m-d H:i:s');
        $user->remember_token = Str::random(10);;
        $user->save();
        if (auth()->user()->centro->nombre == "Oficina Central del CFCRL") {
            $user->assignRole("Orientador Central");
        }
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
        $roles = Role::all();
        $centros = Centro::pluck('nombre', 'id');
        return view('admin.users.edit', compact('user','roles','centros'));
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
                'personas.nombre' => 'required|regex:/^[\pL\s\-]+$/u|max:60',
                'personas.primer_apellido' => 'required|regex:/^[\pL\s\-]+$/u|max:60',
                'personas.curp' => 'alphanum|required|size:18',
            ]
        );
        $data = $request->input('users');

        //Sólo cuando hay cambios de password incluimos el campo de password
        if( empty( $data['password'] ) ){
            unset($data['password']);
        }

        $user->fill($data)->save();
        $user->save();
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

    public function AddRol(Request $request){
        $user = User::find($request->user_id);
        $user->assignRole($request->rol);
        return $user->roles;
    }
    public function GetRoles($id){
        $user = User::find($id);
        return $user->roles;
    }
    public function EliminarRol(Request $request){
        $user = User::find($request->user_id);
        $user->removeRole($request->rol);
        return $user->roles;
    }
    public function impersonate($id){
        $user = User::find($id);
        if(count($user->roles) > 0){
            try{
                auth()->user()->impersonate($user);
                //Creamos las sessiones que se usaran para el impersonate
                $rol = $user->roles->first->get();
                $menu = $this->construirMenu($rol->id);
                session(['menu' => $menu]);
                session(['roles' => $user->roles]);
                session(['rolActual' => $rol]);
                return redirect()->route('home');
            }catch(\Throwable $e){
                return redirect('users')->with('error', 'No se puede mostrar al usuario');
            }
        }
        return redirect('users')->with('error', 'El usuario no tiene roles asignados');
    }
    public function impersonate_leave(){
        try{
            auth()->user()->leaveImpersonation();
            //regresamos la sessiones al usuario principal
            $rol = auth()->user()->roles->first->get();
            $menu = $this->construirMenu($rol->id);
            session(['menu' => $menu]);
            session(['roles' => auth()->user()->roles]);
            session(['rolActual' => $rol]);
            return redirect()->route('home');
        }catch(\Throwable $e){
            $this->sendError("No se puede abandonar al usuario","Error");
        }
    }
    public function AgregarCentro(){
        $usuarios = UsuarioCentro::where("centro_id",$this->request->centro_id)->where("user_id", $this->request->user_id)->first();
        if($usuarios == null){
            $response = UsuarioCentro::create(["centro_id" => $this->request->centro_id,"user_id" => $this->request->user_id]);
        }else{
            $response = $usuarios;
        }
        return $response;
    }
    public function ObtenerCentros(){
        $user = User::find($this->request->user_id);
        $centros = $user->centros;
        foreach($centros as $centro){
            $centro->centro = $centro->centro;
        }
        return $centros;
    }
    public function EliminarCentro(){
        $usuario = UsuarioCentro::find($this->request->id);
        $usuario->delete();
        return $usuario;
    }
}
