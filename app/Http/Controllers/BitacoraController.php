<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;
use App\Filters\CatalogoFilter;
use App\User;
use App\Contador;

class BitacoraController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function index()
    {        
        // $solicitud = Solicitud::all();
        // Filtramos los usuarios con los parametros que vengan en el request
        $audits = (new CatalogoFilter(Audit::query(), $this->request))
            ->searchWith(Audit::class)
            ->filter(false);
        
        if ($this->request->get('all') ) {
            $audiencias = $audits->get();
        } else {
            $length = $this->request->get('length');
            $start = $this->request->get('start');
            
            $limSup = " 23:59:59";
            $limInf = " 00:00:00";
            if($this->request->get('fecha_inicio')){
                $audits->where('created_at',">=",$this->request->get('fecha_inicio').$limInf);
            }
            if($this->request->get('fecha_fin')){
                $audits->where('created_at',"<=",$this->request->get('fecha_fin').$limSup);
            }
            if($this->request->get('user_id')){
                $audits->where('user_id',"=",$this->request->get('user_id'));
            }
            if($this->request->get('event')){
                $audits->where('event',"=",$this->request->get('event'));
            }
            
            if($this->request->get('IsDatatableScroll')){
                $audits = $audits->take($length)->skip($start)->get();
                // $audiencias = $audiencias->select(['id','conciliador','numero_audiencia','fecha_audiencia','hora_inicio','hora_fin'])->orderBy("fecha_audiencia",'desc')->take($length)->skip($start)->get();
            }else{
                $audits = $audits->paginate($this->request->get('per_page', 10));
            }
        }
        if ($this->request->wantsJson()) {
            if ($this->request->get('all') || $this->request->get('paginate') ) {
                return $this->sendResponse($audits, 'SUCCESS');
            }else{
                $total = Audit::count();
                $draw = $this->request->get('draw');
                $acciones = $this->getAcciones($audits);
                return $this->sendResponseDatatable($total,$total,$draw,$acciones, null);
            }
        }
        $users = User::all();
        return view('admin.bitacora.index', compact('audits','users'));
    }
    
    private function getAcciones($auditsAll){
        $audits = array();
        foreach ($auditsAll as $audit) {
            if($audit->event == "Inserción" || $audit->event == "Modificación"){
                $stringMod = $this->modifiedToString($audit);
            }else{
                $stringMod = "No Aplica";
            }
            $nombre = "";
            if($audit->user_id != null){
                $user = User::find($audit->user_id);
                if($user){
                    $nombre = $user->persona->nombre." ".$user->persona->primer_apellido." ".$user->persona->segundo_apellido;
                }
            }
            $auditable_type = "";
            $modelo = $audit->auditable_type;
            if($modelo == "Spatie\Permission\Models\Permission"){
                $auditable_type = "Permiso";
            }else if($modelo == "Spatie\Permission\Models\Role"){
                $auditable_type = "Rol";
            }else{
                $auditable_type = substr($modelo, 4);
            }
            $audits[] = array(
                "user_id" => $nombre, 
                "elemento" => $auditable_type,
                "event" => $audit->event,
                "created_at" => \Carbon\Carbon::parse($audit->created_at)->isoFormat('LLL'), 
                "cambios" => $stringMod
            );
        }
        return $audits;
    }
    
    Private function modifiedToString($arreglo){
        $array = $arreglo->getModified();
        $list = "";
        if($arreglo->event == "Modificación"){
            $list .= "<ul>";
            foreach($array as $atributo => $valores){
                $list .= "<li>".$atributo." fue modificado de ".isset($valores["old"])? $valores["old"] : ''." a ".$valores["new"]."</li>";
            }
            $list .= "</ul>";
        }else{
            $list .= "<ul>";
            foreach($array as $atributo => $valores){
                $list .= "<li>".$atributo.": ".$valores["new"]."</li>";
            }
            $list .= "</ul>";
        }
        return $list;
    }    
}
