<div class="row">
    <div class="col-md-offset-3 col-md-6 ">
        <div class="form-group">
            <label for="name" class="col-sm-3 control-label">Nombre</label>
            <div class="col-sm-10">
                {!! Form::text('name', isset($permiso) ? $permiso->name : null, ['class'=>'form-control', 'id'=>'name', 'placeholder'=>'Nombre del permiso', 'maxlength'=>'20', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('permission.name', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre con el que se identificará el permiso</p>
            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-6 ">
        <div class="form-group">
            <label for="description" class="col-sm-3 control-label">Descripción</label>
            <div class="col-sm-10">
                {!! Form::text('description', isset($permiso) ? $permiso->description : null, ['class'=>'form-control', 'id'=>'description', 'placeholder'=>'Descripción del permiso', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('permission.description', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Cual es la función del permiso</p>
            </div>
        </div>
    </div>
</div>