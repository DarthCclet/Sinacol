<?php

namespace App\Listeners;

use App\Events\GenerateDocumentResolution;
use App\Traits\GenerateDocument;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SaveResolution implements ShouldQueue
{
    use GenerateDocument;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  GenerateDocumentResolution  $event
     * @return void
     */
    public function handle(GenerateDocumentResolution $event)
    {
        $this->generarConstancia($event->idAudiencia,$event->idSolicitud,$event->tipo_documento_id,$event->plantilla_id,$event->idSolicitante,$event->idSolicitado);
    }
}
