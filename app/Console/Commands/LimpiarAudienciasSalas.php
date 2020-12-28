<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Audiencia;

class LimpiarAudienciasSalas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'correcciones:limpiarAudienciasSalas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Este comando nos permite quitar las salas extras de las audiencias';

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
        //Recorremos todas las audiencias
        $audiencias = Audiencia::all();
        foreach($audiencias as $audiencia){
            //Obtenemos las salas ordenadas por fecha de creacion
            $salas = $audiencia->salasAudiencias()->orderBy("created_at","desc")->get();
            if(!$audiencia->multiple){
                $salaReal = $salas->first();
                foreach($salas as $sala){
                    if($sala->id != $salaReal->id){
                        echo "Se elimino la sala "."$sala->id".": ".$sala->sala->nombre."\n";
                        $sala->delete();
                    }
                }
            }else{
                if(isset($salas[0]) && isset($salas[1])){
                    $salaReal1 = $salas[0];
                    $salaReal2 = $salas[1];
                    foreach($salas as $sala){
                        if($sala->id != $salaReal1->id && $sala->id != $salaReal2->id){
                            echo "Se elimino la sala "."$sala->id".": ".$sala->sala->nombre."\n";
                            $sala->delete();
                        }
                    }
                }
            }
        }
    }
}
