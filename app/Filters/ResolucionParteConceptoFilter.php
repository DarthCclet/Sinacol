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

    /**
     * Cuando se pasa la abreviatura del centro como parametro
     * @param array|string $abreviatura Arreglo de abreviatura de centro o una sola abreviatura de centro
     */
    public function handleCentroFilter($abreviatura)
    {
        if(is_array($abreviatura)) {
            if(!count($abreviatura)) return;
            $this->query->whereIn('centros.abreviatura', $abreviatura);
        }
        else {
            if(!trim($abreviatura)) return;
            $this->query->where('centros.abreviatura', strtoupper($abreviatura));
        }
    }

    /**
     * Cuando filtran por conciliadores
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
