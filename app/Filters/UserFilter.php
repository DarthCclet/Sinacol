<?php


namespace App\Filters;

/**
 * Implementa las consultas a la base de datos correspondientes a usuario y sus relaciones
 * Class UserFilter
 * @package App\Filters
 */
class UserFilter extends Filter
{
    /**
     * Campos por los que es válido ordenar las consultas
     * @var array
     */
    protected $valid_sort_by = [
        'name',
        'email',
        'created_at',
        'updated_at',
    ];

}
