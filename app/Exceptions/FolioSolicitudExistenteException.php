<?php


namespace App\Exceptions;


use Exception;
use Throwable;

class FolioSolicitudExistenteException extends Exception
{
    /**
     * @var object Objeto del contexto en el cual se emite la excepción
     */
    protected $context;

    public function __construct($context, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->context = $context;
        if(!$message) $message = 'ERROR: FOLIO DE LA SOLICITUD YA EXISTE PARA OTRA SOLICITUD. INTENTE GUARDAR NUEVAMENTE';
        parent::__construct($message, $code, $previous);
    }

    /**
     * Retorna el objeto de contexto
     * @return object
     */
    public function getContext() {
        return $this->context;
    }

}
