<div class="row">
    <div class="col-md-offset-3 col-md-8 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Descripción del motivo</label>
            <div class="col-sm-10">
                {!! Form::text('descripcion', isset($motivo) ? $motivo->nombre : null, ['class'=>'form-control', 'id'=>'descripcion', 'placeholder'=>'Descripción del motivo', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es la descripcion que tendra el motivo</p>
            </div>
        </div>
    </div>
</div>