<div class="row">
    <div class="col-md-offset-3 col-md-6 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Nombre de la periodicidad</label>
            <div class="col-sm-10">
                {!! Form::text('nombre', isset($periodicidad) ? $periodicidad->nombre : null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Nombre dela periodicidad', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre con el que se identificará la periodicidad</p>
            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-6 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Días</label>
            <div class="col-sm-10">
                {!! Form::text('dias', isset($periodicidad) ? $periodicidad->dias : null, ['class'=>'form-control', 'id'=>'dias', 'placeholder'=>'Número de días', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('dias', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Cantidad de días que tiene la periodicidad</p>
            </div>
        </div>
    </div>
</div>