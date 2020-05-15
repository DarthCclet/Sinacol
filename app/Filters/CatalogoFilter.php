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

    /**
     * Se permite filtrar por updated_at >=
     * @param $fecha_de
     */
    public function handleFechaDeFilter($fecha_de)
    {
        $this->query->where('updated_at', '>=', $fecha_de);
    }

    /**
     * Se permite filtrar por updated_at <=
     * @param $fecha_a
     */
    public function handleFechaAFilter($fecha_a)
    {
        $this->query->where('updated_at', '<=',  $fecha_a);
    }

}
