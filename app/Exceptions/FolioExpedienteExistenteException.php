<?php


namespace App\Exceptions;


use Exception;
use Throwable;

class FolioExpedienteExistenteException extends Exception
{
    /**
     * @var object Objeto del contexto en el cual se emite la excepción
     */
    protected $context;
    public function __construct($context, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->context = $context;
        if(!$message) $message = 'ERROR: FOLIO DEL EXPEDIENTE YA EXISTE PARA OTRO EXPEDIENTE. INTENTE GUARDAR NUEVAMENTE LA OPERACIÓN.';
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
