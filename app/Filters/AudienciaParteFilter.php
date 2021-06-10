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
        if(empty($centro_id)) return;
        $this->query->whereHas('expediente.audiencia.solicitud', function($q) use($centro_id){
            //Solo se toman en cuenta los solicitantes
            if(is_array($centro_id)) {
                $q->whereIn('centro_id', $centro_id);
            }
            else {
                $q->where('centro_id', $centro_id);
            }
        });
    }

    /**
     * Cuando se pasa la abreviatura del centro como parametro
     * @param $abreviatura
     */
    public function handleCentroFilter($abreviatura)
    {
        if(empty($abreviatura)) return;
        if(is_array($abreviatura)) {
            $centros = collect($abreviatura)->map(function ($item, $key) {
                return strtoupper($item);
            });
            $this->query->whereIn('centros.abreviatura', $centros->all());
        }
        else{
            $this->query->where('centro_id', strtoupper($abreviatura));
        }
    }

    public function handleObjetoSolicitudIdFilter($objeto_solicitud_id)
    {
        if(is_array($objeto_solicitud_id)){
            if(empty($objeto_solicitud_id)) return;
            $this->query->whereHas(
                'audiencia.expediente.solicitud.objeto_solicitudes',
                function ($q) use ($objeto_solicitud_id) {
                    $q->whereIn('objeto_solicitud_id', $objeto_solicitud_id);
                }
            );
        }
        else {
            if (!trim($objeto_solicitud_id)) {
                return;
            }
            $this->query->whereHas(
                'audiencia.expediente.solicitud.objeto_solicitudes',
                function ($q) use ($objeto_solicitud_id) {
                    $q->where('objeto_solicitud_id', $objeto_solicitud_id);
                }
            );
        }
    }


}
