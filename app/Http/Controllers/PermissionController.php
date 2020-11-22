<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Traits\Menu;
use App\User;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    use Menu;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::all();
        return view("admin.permisos.index",compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permisos = Permission::all();
        return view("admin.permisos.create",compact('permisos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $permission = Permission::create(["name" => $request->name,"description" => $request->description,"ruta" => $request->ruta,"padre_id" => $request->padre_id]);

        if ($request->wantsJson()) {
            return $this->sendResponse($permission, 'SUCCESS');
        }

        return redirect()->route('permisos.create')->with('success', 'Se ha creado el usuario exitosamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Permission::find($id);
        $permisos = Permission::all();
        return view('admin.permisos.edit', compact('permission','permisos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::find($id);
        $permission->update($request->all());

        if ($request->wantsJson()) {
            return $this->sendResponse($permission, 'SUCCESS');
        }

        return redirect()->route('permisos.index')->with('success', 'Se ha modificado el permiso exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Permission::destroy($id);

        return redirect()->route('permisos.index')->with('success', 'Se ha eliminado el permiso exitosamente');
    }
    
    public function getMenu(Request $request){
        $nombre = auth()->user()->persona->nombre;
        $centros = auth()->user()->centros;
        $centroResponse = [];
        foreach($centros as $centro){
            $centro->centro->usuario_centro_id = $centro->id;
            $centroResponse[] =$centro->centro;
        }
        $arreglo = array("menu" => $request->session()->get('menu'), "roles" => $request->session()->get('roles'),"rolActual" => $request->session()->get('rolActual'),"nombre" => $nombre,"centros" => $centroResponse,"centroActual" => auth()->user()->centro_id);
        return $arreglo;
    }
    
    public function cambiarRol(Request $request,$id){
        $menu = $this->construirMenu($id);
        $request->session()->put('menu', $menu);
        $request->session()->put('rolActual', Role::find($id));
        return $menu;
    }
    public function cambiarCentro($centro_id){
        DB::table('users')
              ->where('id', auth()->user()->id)
              ->update(['centro_id' => (int)$centro_id]);
        $usuario = User::find(auth()->user()->id);
        return $usuario;
    }
}
