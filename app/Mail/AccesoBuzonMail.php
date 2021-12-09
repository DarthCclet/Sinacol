<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Parte;

class AccesoBuzonMail extends Mailable
{
    use Queueable, SerializesModels;

    public $parte;
    public $subject;
    public $liga;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Parte $parte,$liga)
    {
        $this->parte = $parte;
        $this->liga = $liga;
        $this->subject = config('buzon.asunto');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $logo = config("logotipos.logotipo-encabezado");
        if (filter_var($logo, FILTER_VALIDATE_URL)) {
            $logoB64 = base64_encode(file_get_contents($logo));
        }
        else {
            $logoB64 = base64_encode(file_get_contents(public_path($logo)));
        }
        return $this->view('mail.accesoBuzon')->with(["parte" => $this->parte,"liga" => $this->liga,'logo' => $logoB64]);
    }
}
