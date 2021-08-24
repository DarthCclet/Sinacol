<?php
/***
 * Configuración de proveedores de servicios para validación de días de vida de una solicitud
 *
 */
return [
    // Clase que provee los días con las reglas de Centro Federal
    'proveedor_dias_solicitud' => env('PROVEEDOR_DIAS_SOLICITUD',App\Services\DiasVigenciaSolicitudServiceFederal::class)
];