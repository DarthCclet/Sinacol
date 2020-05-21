<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        return view("admin.role.index",compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::all();
        return view("admin.role.create",compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = Role::create(["name" => $request->name,"description" => $request->description]);
        foreach ($request->permisos as $permission){
            $role->givePermissionTo($permission);
        }

        if ($request->wantsJson()) {
            return $this->sendResponse($permission, 'SUCCESS');
        }

        return redirect()->route('roles.create')->with('success', 'Se ha creado el rol exitosamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
//        return $role->load('role');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('admin.role.edit', compact('role','permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        $role->update(["name" => $request->name,"description" => $request->description]);
//        Recorremos los permisos recibidos
        foreach ($request->permisos as $permission){
//            validamos si ya tiene ese permiso
            if(!$role->hasPermissionTo($permission)){
//                Si no lo tiene lo guardamos
                $role->givePermissionTo($permission);
            }
        }
        
                
        if ($request->wantsJson()) {
            return $this->sendResponse($role, 'SUCCESS');
        }

        return redirect()->route('roles.edit',$id)->with('success', 'Se ha modificado el rol exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Role::destroy($id);
        return redirect()->route('roles.index')->with('success', 'Se ha eliminado el rol exitosamente');
    }
    
    public function GetPermisosRol($id){
        $role = Role::find($id);
        return $role->permissions;
    }
    public function DeletePermisosRol(Request $request, $id){
        $role = Role::find($id);
        $role->revokePermissionTo($request->permiso);
        return $role->permissions;
    }
}
