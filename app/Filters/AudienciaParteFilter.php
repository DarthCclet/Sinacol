<?php


namespace App\Filters;


class AudienciaParteFilter extends Filter
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
     * Cuando se pasa el centro_id como parametro
     * @param $centro_id
     */
    public function handleCentroIdFilter($centro_id)
    {
        if(!trim($centro_id)) return;
        $this->query->whereHas('expediente.audiencia.solicitud', function($q) use($centro_id){
            //Solo se toman en cuenta los solicitantes
            $q->where('centro_id',$centro_id);
        });
    }

    /**
     * Cuando se pasa la abreviatura del centro como parametro
     * @param $abreviatura
     */
    public function handleCentroFilter($abreviatura)
    {
        if(!trim($abreviatura)) return;
        $this->query->where('centros.abreviatura', strtoupper($abreviatura));
    }

}
