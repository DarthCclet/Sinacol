<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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
     * Muestra los datos de hash de commit, fecha, tag, etc. que conforman una versión.
     */
    public function version(){
        //Hash de commit:
        $hash = '';
        $fecha = '';
        $rama = '';
        $process = new Process(['git', 'log', '--pretty="%h"','-n1', 'HEAD']);
        try {
            $process->mustRun();
            $hash =  trim($process->getOutput());
        } catch (ProcessFailedException $exception) {
            echo $exception->getMessage();
        }

        //Rama
        $args = explode(" ", 'git rev-parse --abbrev-ref HEAD');
        $process = new Process($args);
        try {
            $process->mustRun();
            $rama =  trim($process->getOutput());
        } catch (ProcessFailedException $exception) {
            echo $exception->getMessage();
        }

        //fecha
        $args = explode(" ", 'git log -n1 --pretty=%ci HEAD');
        $process = new Process($args);
        try {
            $process->mustRun();
            $fecha =  trim($process->getOutput());
        } catch (ProcessFailedException $exception) {
            echo $exception->getMessage();
        }

        return response()->json(['hash' => $hash, 'fecha' => $fecha, 'rama' => $rama]);
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
                return redirect('agendaConciliador?alert=true');
            }else if($request->session()->get('rolActual')->name == "Orientador" || $request->session()->get('rolActual')->name == "Orientador Central"){
                return redirect('solicitudes');
            }else if($request->session()->get('rolActual')->name == "Supervisor de conciliación" || $request->session()->get('rolActual')->name == "Administrador del centro"){
                return redirect('calendariocentro?alert=true');
            }

        }
        return view('home');
    }
}
