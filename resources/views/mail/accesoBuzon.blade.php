<html>
    <head>
        <style type="text/css">
            p{
                text-align: justify;
                padding: 10px;
            }
            .btn{
                color: #fff !important;
                background-color: {{(config('colores.default')=='SI')?'#9D2449':config('colores.btn-primary-color')}} !important;
                border-color: {{(config('colores.default')=='SI')?'#9D2449':config('colores.btn-primary-color')}} !important;
                border: 2px solid {{(config('colores.default')=='SI')?'#9D2449':config('colores.btn-primary-color')}} !important;
                box-shadow: 0 0 0 0 {{(config('colores.default')=='SI')?'#9D2449':config('colores.btn-primary-color')}} !important;
                display: inline-block;
                text-align: center;
                vertical-align: middle;
                user-select: none;
                padding: 7px .75rem;
                font-size: .75rem;
                line-height: 1.5;
                border-radius: 4px;
            }
        </style>
    </head>
    <body style="margin-top: 5%;
                margin-left: 15%;
                margin-right: 15%;
                color: #4e5c68;
                font-family: 'Montserrat', sans-serif;
                font-size: .75rem;
                font-weight: 400;
                line-height: 1.5;
                text-align: center;">
        <div class="login login-v2" data-pageload-addclass="animated fadeIn" style="background: {{(config('colores.default')=='SI')?'#9d2449':config('colores.encabezado-color-fondo')}}; padding: 0px;">
            <!-- begin brand -->
            <div class="login-header" align="center" style="padding:10px;">
                <div class="brand">
                    <span>
                        <img width="220px" src="data:image/x-icon;base64,{{ $logo }}">
                    </span>
                </div>
            </div>
            <!-- end brand -->
            <!-- begin login-content -->
            <div class="login-content" style="background: #f2f4f5 !important;">
                <br>
                <h1>{{ config('buzon.institucion') }}<br><small>{{ config('buzon.sistema') }}</small></h1>
                <p>
                    Bienvenido
                    @if($parte->tipo_persona_id == 1)
                        {{$parte->nombre}} {{$parte->primer_apellido}} {{$parte->segundo_apellido}}
                    @else
                        {{$parte->nombre_comercial}}
                    @endif
                    <br><br>
                    Recibimos tu solicitud para ingresar a tu buzón electrónico sobre las conciliaciones realizadas.
                    <br> A través del siguiente botón podrás ingresar a tu buzón.
                </p>
                <center>
                    <a href="{{$liga}}" class="btn">BUZON ELECTRÓNICO</a>
                </center>
                    <small>
                        En caso de no poder ver el mensaje de forma correcta copia y pega la siguiente liga en tu navegador:
                        <a href="{{$liga}}">{{$liga}}</a>.
                    </small>
            </div>
            <!-- end login-content -->
        </div>

    </body>
</html>
