<?php

namespace App\Console\Commands;

use App\Audiencia;
use App\Events\GenerateDocumentResolution;
use App\ResolucionPartes;
use Illuminate\Console\Command;

class RefactorDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refactorDocuments {solicitud_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina y crea un nuevo documento en las audiencias';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $solicitud_id = $this->argument('solicitud_id');
        
        // if($audiencia_id != ""){
        //     $audiencias= Audiencia::where("id",$audiencia_id)->get();
        // }else{
        //     $audiencias= Audiencia::all();
        // }
        // foreach($audiencias as $key => $audiencia)
        // {
            // $documentos = $audiencia->documentos->where('clasificacion_archivo_id',3);
            // if(count($documentos) > 0){
            //     foreach($documentos as $key => $documento){
            //         $documento->delete();
            //     }
            // }
            // $resoluciones = ResolucionPartes::where('audiencia_id',$audiencia->id)->get();
            // foreach($resoluciones as $key => $resolucion){
                
            //     if($resolucion->resolucion_id == 3){
                    event(new GenerateDocumentResolution("",$solicitud_id,40,6));
                    
            //     }else if($resolucion->resolucion_id == 1){
            //         event(new GenerateDocumentResolution($audiencia->id,$audiencia->expediente->solicitud_id,3,2,$resolucion->parte_solicitante_id,$resolucion->parte_solicitada_id));
            //     }
            // }
        // }
    }
}
