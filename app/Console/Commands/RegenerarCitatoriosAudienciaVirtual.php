<?php

namespace App\Console\Commands;

use App\Events\GenerateDocumentResolution;
use App\Solicitud;
use Illuminate\Console\Command;
use App\Audiencia;

class RegenerarCitatoriosAudienciaVirtual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'correcciones:regenerar-citatorios
                                {--dry-run : Sólo muestra los datos sobre los que se va a regenerar pero no ejecuta la acción}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Este comando regenera los citatorios para las audiencias virtuales debidos a los cambios neesarios para identificar de forma única a a un documento con sus involucrados';

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
        $dry_run = $this->option('dry-run');
        if($dry_run){
            $dry_run = 'YES';
        }


        $parte_id = 2; //Parte solicitada
        $clasificacion_documento_id = 14; // clasificación citatorio
        $plantilla_id = 4; //Plantilla de citatorio

        $fecha_anterior_creacion = '2021-01-27 23:59:59';
        $audiencias = Audiencia::whereHas('expediente.solicitud', function ($query) use ($fecha_anterior_creacion){
            $query->where('virtual', true)->where('created_at','<=',$fecha_anterior_creacion);
        })->get()
        ;

        //Contador para ver cuantos son los documentos regenerados
        $c=0;
        foreach ($audiencias as $audiencia) {

            $solicitados = $audiencia->audienciaParte()->whereHas('parte', function ($query) use ($parte_id) {
                $query->where('tipo_parte_id', $parte_id);
            })->get();
            foreach ($solicitados as $id => $solicitado) {
                echo $id . "\n";
            $c++;
            echo "----$c\n";
                $audiencia = $solicitado->audiencia;
                $docs = $audiencia->documentos()->where('clasificacion_archivo_id', $clasificacion_documento_id)->get();

                $doc_id = null;
                if (isset($docs[$id])) {
                    $doc_id = $docs[$id]->id;
                }
                $audiencia_id = $solicitado->audiencia->id;
                $solicitud_id = $solicitado->audiencia->expediente->solicitud->id;
                $solicitado_id = $solicitado->parte_id;
                echo "$audiencia_id, $solicitud_id, $clasificacion_documento_id, $plantilla_id, null, $solicitado_id, $doc_id\n";
                if(!$dry_run == 'YES') {
                    event(
                        new GenerateDocumentResolution(
                            $audiencia_id,
                            $solicitud_id,
                            14,
                            4,
                            null,
                            $solicitado_id,
                            $doc_id)
                    );
                }

            }
        }

    }
}
