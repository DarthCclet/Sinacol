<?php

return [

    /*
     |------------------------------------------------------------------------------------------------------------------
     | Edición de las partes, tanto solicitantes como citados aún después de la ratificación (confirmación)
     | Posibles valores: 'SI',  'NO'
     | Default: 'NO'
     |------------------------------------------------------------------------------------------------------------------
     */
    'post-ratificacion' => env('EDITAR_PARTES_POST_RATIFICACION', 'NO'),
];
