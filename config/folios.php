<?php
/***
 * Configuración de proveedores de servicios para contadores de secuencias y folios y nomenclatura de documentos
 *
 */
return [
    // Clase que provee los folios con las reglas de Centro Federal
    'proveedor_folio_service' => env('PROVEEDOR_FOLIO_SERVICE',App\Services\FolioServiceFederal::class),

    // Clase que provee los contadores con las reglas de Centro Federal
    'proveedor_contador_service' => env('PROVEEDOR_CONTADOR_SERVICE',App\Services\ContadorServiceFederal::class),

    // Nombre de la plantilla default del proceso de Oficio Libre
    'plantilla_oficio_libre_nombre' => env('PLANTILLA_OFICIO_LIBRE_NOMBRE','OFICIO LIBRE'),
];
