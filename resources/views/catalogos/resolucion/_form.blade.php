<div class="row">
    <h2>Resolución Audiencia</h2>
    <div class="col-md-offset-3 col-md-6 ">



        <div class="form-group">
            <label for="nombre" class="col-sm-6 control-label">Nombre resolución de audiencia</label>
            <div class="col-sm-10">
                {!! Form::text('nombre', isset($resolucion) ? $resolucion->nombre : null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Nombre de la resolución de audiencia', 'maxlength'=>'60', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('resolucion.nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre con el que se identificará la resolución de la audiencia</p>
            </div>
        </div>
    </div>
</div>
