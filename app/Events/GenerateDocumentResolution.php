<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GenerateDocumentResolution
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $idAudiencia, $idSolicitud, $tipo_documento_id,$plantilla_id, $idSolicitante = null, $idSolicitado = null;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($idAudiencia, $idSolicitud, $tipo_documento_id,$plantilla_id, $idSolicitante = null, $idSolicitado = null)
    {
        $this->idAudiencia = $idAudiencia;
        $this->idSolicitud =$idSolicitud;
        $this->tipo_documento_id =$tipo_documento_id;
        $this->plantilla_id = $plantilla_id;
        $this->idSolicitante  = $idSolicitante;
        $this->idSolicitado = $idSolicitado;
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
