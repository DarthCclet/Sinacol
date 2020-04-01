<?php


namespace App\Filters;

/**
 * Implementa las consultas a la base de datos correspondientes a usuario y sus relaciones
 * Class UserFilter
 * @package App\Filters
 */
class ParteFilter extends Filter
{
    /**
     * Campos por los que es válido ordenar las consultas
     * @var array
     */
    protected $valid_sort_by = [
        'tipo_parte_id',
        'fecha_ratificacion',
        'centro_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Se permite filtrar por el tipo_parte_id
     * @param $tipo_parte_id
     */
    public function handleTipoParteIdFilter($tipo_parte_id)
    {
        $this->query->where('tipo_parte_id',$tipo_parte_id);
    }

     /**
     * Se permite filtrar por el tipo_parte_id
     * @param $tipo_parte_id
     */
    public function handleTipoPersonaIdFilter($tipo_persona_id)
    {
        $this->query->where('tipo_persona_id',$tipo_persona_id);
    }
}
