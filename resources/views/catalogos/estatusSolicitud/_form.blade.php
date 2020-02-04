<div class="row">
    <h2>Estatus Solicitud</h2>
    <div class="col-md-offset-3 col-md-6 ">



        <div class="form-group">
            <label for="nombre" class="col-sm-6 control-label">Nombre estatus de solicitud</label>
            <div class="col-sm-10">
                {!! Form::text('nombre', isset($estatusSolicitud) ? $estatusSolicitud->nombre : null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Nombre del estatus de solicitud', 'maxlength'=>'60', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('estatus.nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre con el que se identificará el estatus de la solicitud</p>
            </div>
        </div>
    </div>
</div>
