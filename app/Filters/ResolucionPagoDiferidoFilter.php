<?php


namespace App\Filters;


class ResolucionPagoDiferidoFilter extends Filter
{
    /**
     * Campos por los que es válido ordenar las consultas
     * @var array
     */
    protected $valid_sort_by = [
        'fecha_pago',
        'created_at',
        'updated_at'
    ];
}
