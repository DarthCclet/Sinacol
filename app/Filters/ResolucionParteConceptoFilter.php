<?php


namespace App\Filters;


class ResolucionParteConceptoFilter extends Filter
{
    /**
     * Campos por los que es válido ordenar las consultas
     * @var array
     */
    protected $valid_sort_by = [
        'created_at',
        'updated_at',
    ];

}
