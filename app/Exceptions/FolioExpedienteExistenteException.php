<?php


namespace App\Exceptions;


use Exception;
use Throwable;

class FolioExpedienteExistenteException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if(!$message) $message = 'ERROR: FOLIO DEL EXPEDIENTE YA EXISTE PARA OTRO EXPEDIENTE. INTENTE GUARDAR NUEVAMENTE LA OPERACIÓN.A';
        parent::__construct($message, $code, $previous);
    }
}
