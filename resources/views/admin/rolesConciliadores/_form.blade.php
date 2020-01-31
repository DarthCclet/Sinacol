<div class="row">
    <h2>Roles</h2>
    <div class="col-md-offset-3 col-md-6 ">



        <div class="form-group">
            <label for="nombre" class="col-sm-3 control-label">Nombre rol</label>
            <div class="col-sm-10">
                {!! Form::text('nombre', isset($rolConciliador) ? $rolConciliador->nombre : null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Nombre del rol conciliador', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('roles_conciliadores.nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre con el que se identificará el rol que ocupara el conciliador</p>
            </div>
        </div>
    </div>
</div>
<hr/>
