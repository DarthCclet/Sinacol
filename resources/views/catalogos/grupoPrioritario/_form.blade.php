<div class="row">
    <h2>Grupo Prioritario</h2>
    <div class="col-md-offset-3 col-md-6 ">



        <div class="form-group">
            <label for="nombre" class="col-sm-6 control-label">Nombre del grupo prioritario</label>
            <div class="col-sm-10">
                {!! Form::text('nombre', isset($grupoPrioritario) ? $grupoPrioritario->nombre : null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Nombre del grupo prioritario', 'maxlength'=>'60', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('grupos.nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre con el que se identificará el grupo prioritario</p>
            </div>
        </div>
    </div>
</div>
