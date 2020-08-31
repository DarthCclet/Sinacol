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
                return view('asesoria.a10');
                break;
            case '1010':
                $max_paso = 1013;
                $paso = ($this->request->get('from',1010) + 1);
                $asset_paso = $paso.'.jpg';
                $paso_next = $paso;
                if($paso >= $max_paso){
                    $accion = '10101010';
                    $paso_next = '10101009';
                }
                return view('asesoria.a1010', compact('accion', 'asset_paso', 'paso_next'));
                break;
            case '101010':
                return view('asesoria.a101010');
                break;
            case '10101010':
                $max_paso = 10101017;
                $paso = ($this->request->get('from',10101009) + 1);
                $asset_paso = $paso.'.jpg';
                $paso_next = $paso;
                if($paso >= $max_paso){
                    $accion = '1010101010';
                }
                return view('asesoria.a10101010', compact('accion', 'asset_paso', 'paso_next'));
                break;

            case '1010101010':
                return view('asesoria.a1010101010');
                break;
            case '101010101010':
                return view('asesoria.a101010101010');
                break;
            case 'trabajador-2':
            case 'trabajador-3':
            case 'trabajador-4':
            case 'trabajador-5':
                return view('asesoria.trabajador-grupo-vulnerable');
                //return view('asesoria.trabajador-paso-3');
                break;
            case 'trabajador-asesoria-no-asesoria':
                return view('asesoria.trabajador-asesoria-no-asesoria');
                break;
            case 'trabajador-tipo-asesoria':
                return view('asesoria.trabajador-paso-4-tipo-asesoria');
                break;
            case 'patron':
                return view('asesoria.patron');
                break;
            case 'trabajador-asesoria-1':
                return view('asesoria.trabajador-asesoria-l1');
                break;
            case 'trabajador-asesoria-2':
                return view('asesoria.trabajador-asesoria-l2');
                break;
            case 'trabajador-asesoria-3':
                return view('asesoria.trabajador-asesoria-l3');
                break;
            case 'trabajador-asesoria-4':
                return view('asesoria.trabajador-asesoria-l4');
                break;
            case 'trabajador-asesoria-5':
                return view('asesoria.trabajador-asesoria-l5');
                break;
            case 'trabajador-asesoria-6':
                return view('asesoria.trabajador-asesoria-l6');
                break;
            case 'trabajador-asesoria-7':
                return view('asesoria.trabajador-asesoria-l7');
                break;
            case 'trabajador-asesoria-8':
                return view('asesoria.trabajador-asesoria-l8');
                break;
            case 'trabajador-asesoria-9':
                return view('asesoria.trabajador-asesoria-l9');
                break;
            case 'trabajador-asesoria-10':
                return view('asesoria.trabajador-asesoria-l10');
                break;
            case 'presolicitud':
                $jornadas = array_pluck(Jornada::all(),'nombre','id');
                $periodicidades = array_pluck(Periodicidad::all(),'nombre','id');
                $ocupaciones = array_pluck(Ocupacion::all(),'nombre','id');
                $origen = $this->request->origen;
                return view('asesoria.presolicitud', compact('jornadas','periodicidades','ocupaciones','origen'));
            break;
            default:
                return view('asesoria.default');
                break;
        }
    }
}
