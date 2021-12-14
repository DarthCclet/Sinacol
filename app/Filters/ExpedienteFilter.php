<?php


namespace App\Filters;

/**
 * Métodos para filtrar consultas mediante la petición HTTP de Expediente
 * @package App\Filters
 */

class ExpedienteFilter extends Filter
{
    /**
     * Campos por los que es válido ordenar las consultas
     * @var array
     */
    protected $valid_sort_by = [
        'anio','consecutivo','folio',
        'created_at',
        'updated_at',
    ];

}
