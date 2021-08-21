<?php


namespace App\Filters;


class PlantillaDocumentoFilter extends Filter
{
    /**
     * Campos por los que es válido ordenar las consultas
     * @var array
     */
    protected $valid_sort_by = [
        'created_at',
        'updated_at',
        'id',
        'nombre_plantilla',
        'clave_nomenclatura'
    ];

    /**
     * Cuando se pasa el nombre_plantilla como parametro
     * @param string $nombre Nombre o fragmento del nombre de la plantilla a consultar
     */
    public function handleNombrePlantillaFilter($nombre)
    {
        if(empty($nombre)) return;
        $this->query->where('nombre_plantilla', 'ilike', '%'.$nombre.'%');
    }
}
