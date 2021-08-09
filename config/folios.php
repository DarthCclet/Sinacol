<?php

return [
    // Clase que provee los folios con las reglas de Centro Federal
    'proveedor_folio_service' => env('PROVEEDOR_FOLIO_SERVICE',App\Services\FolioServiceFederal::class),

    // Clase que provee los contadores con las reglas de Centro Federal
    'proveedor_contador_service' => env('PROVEEDOR_CONTADOR_SERVICE',App\Services\ContadorServiceFederal::class),
];
