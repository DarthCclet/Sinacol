<?php

namespace App\Console\Commands;

use App\CanalFolio;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateCanalFolio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generateCanalFolio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Este comando permite generar un numero aleatorio para el canal de conexion de meeting virtual ';

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
        $year = date('Y');
        $canalFolio = CanalFolio::where('year',$year)->first();
        if(!$canalFolio){
            CanalFolio::truncate();
            $inicio = 10000000+10010000; 
            for($i=10010000;$i<=$inicio;$i++){ 
                
                $yearFolio = date("y");
                $hex = dechex($i);
                $folio = $yearFolio. $hex;
                DB::table('canal_folios')->insert(
                    [
                        'folio' => $folio,
                        'year' => $year
                    ]
                );
            }
        }else{
            echo "Error ya existen folios para el año actual";
        }
    }
}
