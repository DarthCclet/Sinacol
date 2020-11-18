<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Audiencia;

class CambioFecha extends Mailable
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
        $this->subject = "Acceso al buzón";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.CambioFecha')->with(["audiencia" => $this->audiencia,"parte" => $this->parte]);
    }
}
