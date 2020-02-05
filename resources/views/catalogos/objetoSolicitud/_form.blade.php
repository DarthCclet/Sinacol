<div class="row">
    <h2>Objeto Solicitud</h2>
    <div class="col-md-offset-3 col-md-6 ">



        <div class="form-group">
            <label for="nombre" class="col-sm-6 control-label">Nombre objeto de solicitud</label>
            <div class="col-sm-10">
                {!! Form::text('nombre', isset($objetoSolicitud) ? $objetoSolicitud->nombre : null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Nombre del objeto de solicitud', 'maxlength'=>'60', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('objeto_solicitudes.nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre con el que se identificará el objeto de la solicitud</p>
            </div>
        </div>
    </div>
</div>
