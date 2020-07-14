<div class="row">
    <div class="col-md-offset-3 col-md-6 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Nombre del ámbito</label>
            <div class="col-sm-10">
                {!! Form::text('nombre', isset($ambito) ? $ambito->nombre : null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Nombre del ámbito', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre con el que se identificará el ámbito</p>
            </div>
        </div>
    </div>
</div>