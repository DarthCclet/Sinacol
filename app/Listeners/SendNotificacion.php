<?php

namespace App\Listeners;

use App\Events\RatificacionRealizada;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNotificacion
{
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
     * @param  RatificacionRealizada  $event
     * @return void
     */
    public function handle(RatificacionRealizada $event)
    {
        //
//        echo $event->audiencia_id;
    }
}
