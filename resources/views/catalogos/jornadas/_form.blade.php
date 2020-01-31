<div class="row">
    <div class="col-md-offset-3 col-md-6 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Nombre de la Jornada</label>
            <div class="col-sm-10">
                {!! Form::number('id', null, ['class'=>'form-control', 'id'=>'id', 'placeholder'=>'id de la jornada', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('id', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el id con el que se identificará la jornada</p>
                {!! Form::text('nombre', null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Nombre de la jornada', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre con el que se identificará la jornada</p>
            </div>
        </div>
    </div>
</div>
<hr/>
