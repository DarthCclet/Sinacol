<html>
    <head>
        <style type="text/css">
            p{
                text-align: justify;
                padding: 10px;
            }
            .btn{
                color: #fff !important;
                background-color: #9D2449 !important;
                border-color: #9D2449 !important;
                border: 2px solid #9D2449 !important;
                box-shadow: 0 0 0 0 #9D2449 !important;
                display: inline-block;
                text-align: center;
                vertical-align: middle;
                user-select: none;
                padding: 7px .75rem;
                font-size: .75rem;
                line-height: 1.5;
                border-radius: 4px;
            }
            .table td,.table th {
              border: 1px solid black;
            }

            .table {
              width: 60%;
              border-collapse: collapse;
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
        <div class="login login-v2" data-pageload-addclass="animated fadeIn" style="background: #9d2449; padding: 0px;">
            <!-- begin brand -->
            <div class="login-header" align="center" style="padding:10px;">
                <div class="brand">
                    <span>
                        <img src="https://framework-gb.cdn.gob.mx/landing/img/logoheader.svg" width="220px">
                    </span>
                </div>
            </div>
            <!-- end brand -->
            <!-- begin login-content -->
            <div class="login-content" style="background: #f2f4f5 !important;Margin:-10px;">
                <br>
                <h1>Centro Federal de Conciliación y Registro Laboral.<br><small>Sistema de conciliación</small></h1>
                <p>
                    Estimado
                    @if($parte->tipo_persona_id == 1)
                        {{$parte->nombre}} {{$parte->primer_apellido}} {{$parte->segundo_apellido}}
                    @else
                        {{$parte->nombre_comercial}}
                    @endif
                    <br><br>
                    Se ha realizado un ajuste a la fecha de tu audiencia previamente programada, la información de tu nueva cita se desglosa a continuación
                    <br>
                    <br>
                </p>
                <p>
                    <center>
                        <table style="width:60%">
                            <tr>
                                <td><strong>Folio: </strong>{{$audiencia->folio}}/{{$audiencia->anio}}</td>
                                <td><strong>Fecha de audiencia: </strong>{{$audiencia->fecha_audiencia}}</td>
                            </tr>
                            <tr>
                                <td><strong>Hora de inicio: </strong>{{$audiencia->hora_inicio}}</td>
                                <td><strong>Hora de t&eacute;rmino: </strong>{{$audiencia->hora_fin}}</td>
                            </tr>
                        </table>
                        <br><br>
                        <table class="table" style="border-collapse: collapse;border: 1px;">
                            <tr style="background-color: #9d244947">
                                <th>Tipo de parte</th>
                                <th>Conciliador</th>
                                <th>Sala</th>
                            </tr>
                            @if($audiencia->multiple){
                                @foreach($audiencia->conciliadoresAudiencias as $conciliador)
                                    <tr>
                                    @if($conciliador->solicitante){
                                        <td align="center">Solicitante(s)</td>
                                    @else
                                        <td align="center">Citado(s)</td>
                                    @endif
                                    <td align="center">{{$conciliador->conciliador->persona->nombre}} {{$conciliador->conciliador->persona->primer_apellido}} {{$conciliador->conciliador->persona->segundo_apellido}}</td>
                                    @foreach($audiencia->salasAudiencias as $salas)
                                        @if($salas->solicitante == $conciliador->solicitante){
                                            <td align="center">{{$sala->sala->sala}}</td>
                                        @endif
                                    @endforeach
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                   <td align="center">Solicitante(s) y citado(s)</td>
                                   <td align="center">{{$audiencia->conciliadoresAudiencias[0]->conciliador->persona->nombre}} {{$audiencia->conciliadoresAudiencias[0]->conciliador->persona->primer_apellido}} {{$audiencia->conciliadoresAudiencias[0]->conciliador->persona->segundo_apellido}}</td>
                                   <td align="center">{{$audiencia->salasAudiencias[0]->sala->sala}}</td>
                                </tr>
                            @endif
                        </table>
                    </center>
                </p>         
                <p>
                    <small>
                        En caso de no poder ver el mensaje de forma correcta te invitamos a consultar la información en tu buzón electrónico dando click <a href="{{route('solicitud_buzon')}}">Aqui</a>
                    </small>
                </p>
            </div>
            <!-- end login-content -->
        </div>

    </body>
</html>
