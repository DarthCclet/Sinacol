<?php
return [
    /*
     |---------------------------------------------------------
     | Logotipo encabezado general
     |---------------------------------------------------------
    */
    'logotipo-encabezado' => env('LOGOTIPO_ENCABEZADO', 'assets/img/logo/LogoEncabezado.png'),
    'logotipo-encabezado-width' => env('LOGOTIPO_ENCABEZADO_WIDTH','404'),
    'logotipo-encabezado-height' => env('LOGOTIPO_ENCABEZADO_HEIGHT','58'),

    /*
     |---------------------------------------------------------
     | Imagen background lado izquierdo login
     |---------------------------------------------------------
    */
    'imagen-background-login' => env('IMAGEN_BACKGROUND_LOGIN','assets/img/logo/fondo-verde.jpg'),
    /*
     |---------------------------------------------------------
     | Logotipo encabezado plantillas
     |---------------------------------------------------------
    */
    'header-plantilla' => env('HEADER_PLANTILLA', '/assets/img/logo/LOGO_cfcrl.png'),
    'footer-plantilla' => env('FOOTER_PLANTILLA', '/assets/img/logo/footer_logo.png'),
];
