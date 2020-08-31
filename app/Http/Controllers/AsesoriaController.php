<?php

namespace App\Http\Controllers;

use App\Jornada;
use App\Ocupacion;
use App\Periodicidad;
use Illuminate\Http\Request;

class AsesoriaController extends Controller
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index($accion)
    {

        switch ($accion){
            case '10':
                //Soy trabajador
                return view('asesoria.a10');
                break;
            case '1020':
                //Soy patron
                return view('asesoria.a20');
            case '1030':
            case '1010':
                //Presentación excepciones
                $max_paso = 1014;
                $paso = ($this->request->get('from',1010) + 1);
                $asset_paso = $paso.'.jpg';
                $paso_next = $paso;
                if($paso >= $max_paso){
                    $accion = $accion.'1010';
                    $paso_next = '10101009';
                }
                return view('asesoria.a1010', compact('accion', 'asset_paso', 'paso_next'));
                break;
            case '101010':
                //Video Excepciones
                return view('asesoria.a101010');
                break;
            case '10101010':
            case '10201010':
            case '10301010':
                $max_paso = 10101017;
                $paso = ($this->request->get('from',10101009) + 1);
                $origen = $this->request->get('source', 10101010);
                $asset_paso = $paso.'.jpg';
                $paso_next = $paso;
                if($paso >= $max_paso){
                    $accion = '1010101010';
                }
                return view('asesoria.a10101010', compact('accion', 'asset_paso', 'paso_next', 'origen'));
                break;

            case '1010101010':
                //este es el calculo del pre registro
                $jornadas = array_pluck(Jornada::all(),'nombre','id');
                $periodicidades = array_pluck(Periodicidad::all(),'nombre','id');
                $ocupaciones = array_pluck(Ocupacion::all(),'nombre','id');
                $origen = $this->request->origen;
                return view('asesoria.presolicitud', compact('jornadas','periodicidades','ocupaciones','origen'));
                break;

            case '101010101010':
                $max_paso = 101010101019;
                $paso = ($this->request->get('from',101010101010) + 1);
                $origen = $this->request->get('source', 10101010);
                $asset_paso = $paso.'.jpg';
                $paso_next = $paso;
                if($paso >= $max_paso){
                    $accion = '../solicitudes/create-public';
                }
                return view('asesoria.a101010101010',  compact('accion', 'asset_paso', 'paso_next', 'origen'));
                break;
            case 'presolicitud':
                $jornadas = array_pluck(Jornada::all(),'nombre','id');
                $periodicidades = array_pluck(Periodicidad::all(),'nombre','id');
                $ocupaciones = array_pluck(Ocupacion::all(),'nombre','id');
                $origen = $this->request->origen;
                return view('asesoria.presolicitud', compact('jornadas','periodicidades','ocupaciones','origen'));
            break;


            //RUTA PATRON
            case '20':
                //Soy patron
                return view('asesoria.a20');
                break;
            case '2010':
                //Soy patron - conflicto individual
                return view('asesoria.a2010');
            case '201010':
                //Soy patron - conflicto individual
                return view('asesoria.a201010');
                break;
            case '202010':
                //Soy patron - conflicto individual
                return view('asesoria.a202010');
                break;
            case '2020':
                //Soy patron - conflicto colectivo
                return view('asesoria.a2020');
                break;

            default:
                return view('asesoria.default');
                break;
        }
    }
}
