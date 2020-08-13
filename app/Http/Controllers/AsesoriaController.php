<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AsesoriaController extends Controller
{
    public function index($accion)
    {

        switch ($accion){
            case 'trabajador':
                return view('asesoria.trabajador');
                break;
            case 'trabajador-1':
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
            default:
                return view('asesoria.default');
                break;
        }
    }
}
