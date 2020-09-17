<div class="row">
    <div class="col-md-offset-3 col-md-4 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Fecha de audiencia</label>
            <div class="col-sm-10">
                {!! Form::text('fecha_audiencia', isset($audiencia) ? $audiencia->fecha_audiencia : null, ['class'=>'form-control', 'id'=>'fecha_audiencia', 'placeholder'=>'Centro', 'maxlength'=>'30', 'size'=>'10', 'autofocus'=>true,'disabled'=>'disabled']) !!}
                {!! $errors->first('centro.nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Fecha en la que se programó la audiencia</p>
            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-4 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Hora de inicio</label>
            <div class="col-sm-10">
                {!! Form::text('hora_inicio', isset($audiencia) ? $audiencia->hora_inicio : null, ['class'=>'form-control', 'id'=>'hora_inicio', 'placeholder'=>'Duración', 'maxlength'=>'30', 'size'=>'10', 'autofocus'=>true,'disabled'=>'disabled']) !!}
                {!! $errors->first('centro.duracionAudiencia', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Hora de inicio en la que se programó la audiencia</p>
            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-4 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Hora de termino</label>
            <div class="col-sm-10">
                {!! Form::text('hora_fin', isset($audiencia) ? $audiencia->hora_fin : null, ['class'=>'form-control', 'id'=>'hora_fin', 'placeholder'=>'Duración', 'maxlength'=>'30', 'size'=>'10', 'autofocus'=>true,'disabled'=>'disabled']) !!}
                {!! $errors->first('centro.duracionAudiencia', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Hora de termino programada para la audiencia</p>
            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-12 ">
        <table class="table table-striped table-bordered table-td-valign-middle">
            <thead>
                <tr>
                    <th class="text-nowrap">Tipo Parte</th>
                    <th class="text-nowrap">Nombre de la parte</th>
                    <th class="text-nowrap">Conciliador</th>
                    <th class="text-nowrap">Sala</th>
                    <th class="text-nowrap" style="width: 10%;">Accion</th>
                </tr>
            </thead>
            <tbody>
            @foreach($audiencia->partes as $parte)
                @if($parte->tipo_parte_id != 3)
                    <tr>
                        <td class="text-nowrap">{{ $parte->tipoParte->nombre }}</td>
                        @if($parte->tipo_persona_id == 1)
                            <td class="text-nowrap">{{ $parte->nombre }} {{ $parte->primer_apellido }} {{ $parte->segundo_apellido }}</td>
                        @else
                            <td class="text-nowrap">{{ $parte->nombre_comercial }}</td>
                        @endif
                        @if(!$audiencia->multiple)
                            <td class="text-nowrap">{{ $audiencia->conciliadores[0]->conciliador->persona->nombre }} {{ $audiencia->conciliadores[0]->conciliador->persona->primer_apellido }} {{ $audiencia->conciliadores[0]->conciliador->persona->segundo_apellido }}</td>
                            <td class="text-nowrap">{{ $audiencia->salas[0]->sala->sala }}</td>
                        @elseif($audiencia->multiple && $audiencia->multiple != null)
                            @foreach($audiencia->conciliadores as $conciliador)
                                @if($conciliador->solicitante && $parte->tipoParte->id == 1)
                                    <td class="text-nowrap">{{ $conciliador->conciliador->persona->nombre }} {{ $conciliador->conciliador->persona->primer_apellido }} {{ $conciliador->conciliador->persona->segundo_apellido }}</td>
                                @elseif(!$conciliador->solicitante and $parte->tipo_parte_id != 1)
                                    <td class="text-nowrap">{{ $conciliador->conciliador->persona->nombre }} {{ $conciliador->conciliador->persona->primer_apellido }} {{ $conciliador->conciliador->persona->segundo_apellido }}</td>
                                @endif
                            @endforeach
                            @foreach($audiencia->salas as $sala)
                                @if($sala->solicitante and $parte->tipo_parte_id == 1)
                                    <td class="text-nowrap">{{ $sala->sala->sala }}</td>
                                @elseif(!$sala->solicitante and $parte->tipo_parte_id != 1)
                                    <td class="text-nowrap">{{ $sala->sala->sala }}</td>
                                @endif
                            @endforeach
                        @else
                            <td class="text-nowrap">No asignado</td>
                            <td class="text-nowrap">No asignado</td>
                        @endif
                        <td>
                            @if(($parte->tipo_persona_id == 2) || ($parte->tipo_parte_id == 2 && $parte->tipo_persona_id == 1))
                            <div class="md-2" style="display: inline-block;">
                                <button onclick="AgregarRepresentante({{$parte->id}})" class="btn btn-xs btn-primary btnAgregarRepresentante" title="Agregar Representante Legal" data-toggle="tooltip" data-placement="top">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            @endif
                            @if($parte->tipo_parte_id == 1)
                            <div class="md-2" style="display: inline-block;" >
                                <button onclick="DatosLaborales({{$parte->id}})" class="btn btn-xs btn-primary btnAgregarRepresentante" title="Verificar Datos Laborales" data-toggle="tooltip" data-placement="top">
                                    <i class="fa fa-briefcase"></i>
                                </button>
                            </div>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
</div>
