<div class="row">
    <div class="col-md-offset-3 col-md-6 ">
        <div class="form-group">
            <label for="name" class="col-sm-3 control-label">Nombre</label>
            <div class="col-sm-10">
                {!! Form::text('name', isset($role) ? $role->name : null, ['class'=>'form-control', 'id'=>'name', 'placeholder'=>'Nombre del rol', 'maxlength'=>'20', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('role.name', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre con el que se identificará el rol</p>
            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-6 ">
        <div class="form-group">
            <label for="description" class="col-sm-3 control-label">Descripción</label>
            <div class="col-sm-10">
                {!! Form::text('description', isset($role) ? $role->description : null, ['class'=>'form-control', 'id'=>'description', 'placeholder'=>'Descripción del rol', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('role.description', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Cual es la función del rol</p>
            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-12">
        <div class="form-group">
            <label class="col-form-label">Permisos</label>
            <div class="col-lg-12">
                <select class="multiple-select2 form-control" multiple="multiple" name="permisos[]">
                    @foreach($permissions as $permission)
                    <option value="{{$permission->name}}">{{$permission->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
