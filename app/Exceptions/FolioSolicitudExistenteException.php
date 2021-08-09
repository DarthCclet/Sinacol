<?php


namespace App\Exceptions;


use Exception;
use Throwable;

class FolioSolicitudExistenteException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if(!$message) $message = 'ERROR: FOLIO DE LA SOLICITUD YA EXISTE PARA OTRA SOLICITUD. INTENTE GUARDAR NUEVAMENTE';
        parent::__construct($message, $code, $previous);
    }
}
