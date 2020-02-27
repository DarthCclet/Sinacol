<div class="row">
    <div class="col-md-offset-3 col-md-4 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Fecha de audiencia</label>
            <div class="col-sm-10">
                {!! Form::text('fecha_audiencia', isset($audiencia) ? $audiencia->fecha_audiencia : null, ['class'=>'form-control', 'id'=>'fecha_audiencia', 'placeholder'=>'Centro', 'maxlength'=>'30', 'size'=>'10', 'autofocus'=>true,'disabled'=>'disabled']) !!}
                {!! $errors->first('centro.nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Fecha en la que se programo la audiencia</p>
            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-4 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Hora inicio</label>
            <div class="col-sm-10">
                {!! Form::text('hora_inicio', isset($audiencia) ? $audiencia->hora_inicio : null, ['class'=>'form-control', 'id'=>'hora_inicio', 'placeholder'=>'Duración', 'maxlength'=>'30', 'size'=>'10', 'autofocus'=>true,'disabled'=>'disabled']) !!}
                {!! $errors->first('centro.duracionAudiencia', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Hora inicio en la que se programo la audiencia</p>
            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-4 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Hora de termino</label>
            <div class="col-sm-10">
                {!! Form::text('hora_fin', isset($audiencia) ? $audiencia->hora_fin : null, ['class'=>'form-control', 'id'=>'hora_fin', 'placeholder'=>'Duración', 'maxlength'=>'30', 'size'=>'10', 'autofocus'=>true,'disabled'=>'disabled']) !!}
                {!! $errors->first('centro.duracionAudiencia', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Hora fin en la que se programo la audiencia</p>
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
                </tr>
            </thead>
            <tbody>
            @foreach($audiencia->partes as $parte)
            <tr>
                <td class="text-nowrap">{{ $parte->nombreParte }}</td>
                <td class="text-nowrap">{{ $parte->nombrePersona }} {{ $parte->primer_apellido }} {{ $parte->segundo_apellido }}</td>
                @if(!$audiencia->multiple && $audiencia->multiple != null)
                    <td class="text-nowrap">{{ $audiencia->conciliadores[0]->nombre }} {{ $audiencia->conciliadores[0]->primer_apellido }} {{ $audiencia->conciliadores[0]->segundo_apellido }}</td>
                    <td class="text-nowrap">{{ $audiencia->salas[0]->sala }}</td>
                @elseif($audiencia->multiple && $audiencia->multiple != null)
                    @foreach($audiencia->conciliadores as $conciliador)
                        @if($conciliador->solicitante and $parte->tipo_parte_id == 1)
                            <td class="text-nowrap">{{ $conciliador->nombre }} {{ $conciliador->primer_apellido }} {{ $conciliador->segundo_apellido }}</td>
                        @elseif(!$conciliador->solicitante and $parte->tipo_parte_id != 1)
                            <td class="text-nowrap">{{ $conciliador->nombre }} {{ $conciliador->primer_apellido }} {{ $conciliador->segundo_apellido }}</td>
                        @endif
                    @endforeach
                    @foreach($audiencia->salas as $sala)
                        @if($sala->solicitante and $parte->tipo_parte_id == 1)
                            <td class="text-nowrap">{{ $sala->sala }}</td>
                        @elseif(!$sala->solicitante and $parte->tipo_parte_id != 1)
                            <td class="text-nowrap">{{ $sala->sala }}</td>
                        @endif
                    @endforeach
                @else
                    <td class="text-nowrap">No asignado</td>
                    <td class="text-nowrap">No asignado</td>
                @endif
                <!--<th class="text-nowrap">Conciliador</th>-->
                <!--<th class="text-nowrap">Sala</th>-->
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-md-offset-3 col-md-6 ">
        <!-- begin panel -->
	<div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Convenio</h4>
            </div>
            <!-- begin panel-body -->
            <div class="panel-body">
                    {!! Form::textarea('convenio', isset($audiencia) ? $audiencia->convenio : null, ['class'=>'form-control textarea', 'id'=>'convenio', 'placeholder'=>'Describir el convenio ...','rows'=>'12']) !!}
		</div>
	</div>
	<!-- end panel -->
    </div>
    <div class="col-md-offset-3 col-md-6 ">
        <!-- begin panel -->
	<div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Desahogo</h4>
            </div>
            <!-- begin panel-body -->
            <div class="panel-body">
                {!! Form::textarea('desahogo', isset($audiencia) ? $audiencia->desahogo : null, ['class'=>'form-control textarea', 'id'=>'desahogo', 'placeholder'=>'Describir el desahogo ...','rows'=>'12']) !!}
                <!--<textarea class="textarea form-control" id="desahogo" placeholder="Describir el desahogo ..." rows="12"></textarea>-->
            </div>
	</div>
	<!-- end panel -->
    </div>
    <div class="col-md-offset-3 col-md-6 ">
        <div class="form-group">
            <label for="resolucion_id" class="col-sm-6 control-label">Resolución</label>
            <div class="col-sm-10">
                <select id="resolucion_id" class="form-control">
                    <option value="">-- Selecciona una resolución</option>
                </select>
            </div>
        </div>
    </div>
</div>
