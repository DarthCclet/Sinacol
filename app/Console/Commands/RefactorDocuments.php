<?php

namespace App\Console\Commands;

use App\Audiencia;
use App\ResolucionPartes;
use App\Traits\GenerateDocument;
use Illuminate\Console\Command;

class RefactorDocuments extends Command
{
    use GenerateDocument;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refactorDocuments {audiencia_id?} {solicitante_id?} {solicitado_id?}';

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
        
        $audiencia_id = $this->argument('audiencia_id');
        $solicitante_id = $this->argument('solicitante_id');
        $solicitado_id = $this->argument('solicitado_id');
        if($audiencia_id != ""){
            $audiencias= Audiencia::where("id",$audiencia_id)->get();
        }else{
            $audiencias= Audiencia::all();
        }
        foreach($audiencias as $key => $audiencia)
        {
            $documentos = $audiencia->documentos->where('clasificacion_archivo_id',3);
            if(count($documentos) > 0){
                foreach($documentos as $key => $documento){
                    $documento->delete();
                }
            }
            $resoluciones = ResolucionPartes::where('audiencia_id',$audiencia->id)->get();
            foreach($resoluciones as $key => $resolucion){
                
                if($resolucion->resolucion_id == 3){
                    $this->generarConstancia($audiencia->id,$audiencia->expediente->solicitud_id,3,1,$resolucion->parte_solicitante_id,$resolucion->parte_solicitada_id);
                    
                }else if($resolucion->resolucion_id == 1){
                    $this->generarConstancia($audiencia->id,$audiencia->expediente->solicitud_id,3,2,$resolucion->parte_solicitante_id,$resolucion->parte_solicitada_id);
                }
            }
        }
    }
}
