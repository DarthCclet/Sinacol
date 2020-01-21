<?php


namespace App\Filters;

/**
 * Implementa las consultas a la base de datos correspondientes a salas y sus relaciones
 * Class SalasFilter
 * @package App\Filters
 */
class CatalogoFilter extends Filter
{
    /**
     * Campos por los que es válido ordenar las consultas
     * @var array
     */
    protected $valid_sort_by = [
        'nombre',
        'created_at',
        'updated_at',
    ];

}
