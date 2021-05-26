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
     * @param $abreviatura
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

}
