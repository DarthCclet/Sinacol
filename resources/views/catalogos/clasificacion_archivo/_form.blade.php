<div class="row">
    <div class="col-md-offset-3 col-md-6 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Nombre de la clasificación</label>
            <div class="col-sm-10">
                {!! Form::text('nombre', isset($clasificacion) ? $clasificacion->nombre : null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Nombre de la clasificación', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre con el que se identificará la clasificación</p>
            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-6 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Entidad</label>
            <div class="col-sm-10">
                {!! Form::text('entidad', isset($clasificacion) ? $clasificacion->entidad : null, ['class'=>'form-control', 'id'=>'entidad', 'placeholder'=>'Entidad que emite el documento', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('entidad', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es la entidad que emite el documento</p>
            </div>
        </div>
    </div>
</div>