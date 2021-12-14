<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class TipoSolicitudNoValidaException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if (!$message) {
            $message = 'ERROR: EL TIPO SOLICITUD ID NO EXISTE';
        }
        parent::__construct($message, $code, $previous);
    }
}
