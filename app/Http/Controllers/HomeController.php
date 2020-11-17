<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if($request->session()->get('rolActual') != null){
            if($request->session()->get('rolActual')->name == "Personal conciliador"){
                return redirect('agendaConciliador');
            }else if($request->session()->get('rolActual')->name == "Orientador" || $request->session()->get('rolActual')->name == "Orientador Central"){
                return redirect('solicitudes');
            }else if($request->session()->get('rolActual')->name == "Supervisor de conciliación" || $request->session()->get('rolActual')->name == "Administrador del centro"){
                return redirect('calendariocentro');
            }
            
        }
        return view('home');
    }
}
