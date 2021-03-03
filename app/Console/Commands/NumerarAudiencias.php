<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NumerarAudiencias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audiencias:numerar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asignación de consecutivos a las audiencias';

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
        $solicitudes = \App\Solicitud::whereRatificada(true)->get();
        foreach($solicitudes as $solicitud){
            if(isset($solicitud->expediente->audiencia)){
                if(count($solicitud->expediente->audiencia) > 1){
                    $audiencias = $solicitud->expediente->audiencia()->orderBy("created_at", "ASC")->get();
                    $numero = 1;
                    foreach($audiencias as $audiencia){
                        $audiencia->update(["numero_audiencia" => $numero]);
                        dump("audiencia_id ".$audiencia->id." cambio numero de audiencia a ".$numero);
                        $numero++;
                    }
                }
            }
        }
        
    }
}
