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
     * Menaja el filtrado por tipo_parte_id cuando se pasa como parámetro
     * @param integer $tipo_parte_id ID del tipo parte
     */
    public function handleTipoParteIdFilter($tipo_parte_id)
    {
        $this->query->where('tipo_parte_id',$tipo_parte_id);
    }

    /**
     * Menaja el filtrado por tipo_persona_id cuando se pasa como parámetro
     * @param integer $tipo_persona_id ID del tipo de persona
     */
    public function handleTipoPersonaIdFilter($tipo_persona_id)
    {
        $this->query->where('tipo_persona_id',$tipo_persona_id);
    }

    /**
     * Menaja el filtrado por conciliadores cuando se pasa como parámetro
     * @param array|integer $conciliadores Arreglo de ID de conciliadores o un sólo ID de conciliador
     */
    public function handleConciliadoresFilter($conciliadores)
    {
        if (is_array($conciliadores)) {
            $this->query->whereIn('audiencias.conciliador_id', $conciliadores);
        } else {
            if (!trim($conciliadores)) {
                return;
            }
            $this->query->where('audiencias.conciliador_id', $conciliadores);
        }
    }

}
