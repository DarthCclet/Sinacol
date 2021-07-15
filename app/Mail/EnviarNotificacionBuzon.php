<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Audiencia;
use App\Parte;

class EnviarNotificacionBuzon extends Mailable
{
    use Queueable, SerializesModels;

    public $parte;
    public $subject;
    public $audiencia;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Audiencia $audiencia,Parte $parte)
    {
        $this->audiencia = $audiencia;
        $this->parte = $parte;
        if($parte->notificacion_buzon){
            $this->subject = "Notificación de buzón electrónico";
        }else{
            $this->subject = "Aviso de buzón electrónico";
        }
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.NotificacionBuzon')->with(["expediente" => $this->audiencia->expediente,"parte" => $this->parte]);
    }
}
