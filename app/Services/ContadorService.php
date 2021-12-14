<?php
namespace App\Services;

/**
 * Interfase que define el contrato que deben cumplir los servicios proveedores de folios y cuentas
 * Interface ContadorServiceInterface
 */
interface ContadorService {

    /**
     * Obtiene el contador
     *
     * @param integer $anio Año para el cual se genera el contador
     * @param mixed $tipo_contador_id Tipo de contador (contexto)
     * @param integer $centro_id Centro u oficina para la cual se genera el contador
     * @return mixed
     */
    public function getContador($anio, $tipo_contador_id, $centro_id);

}
