<?php


namespace App\Filters;

/**
 * Métodos para filtrar consultas mediante la petición HTTP de todos los modelos que hereden la clase Catalogo
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
     * @param string $fecha_de Fecha en formato Y-m-d H:i:s
     */
    public function handleFechaDeFilter($fecha_de)
    {
        $this->query->where('updated_at', '>=', $fecha_de);
    }

    /**
     * Se permite filtrar por updated_at <=
     * @param string $fecha_a Fecha en formato Y-m-d H:i:s
     */
    public function handleFechaAFilter($fecha_a)
    {
        $this->query->where('updated_at', '<=',  $fecha_a);
    }

}
