<div class="row">

    <div class="col-md-6">

        <div class="form-group">
            <label for="usuario" class="col-sm-2 control-label">Usuario</label>
            <div class="col-sm-10">
                {!! Form::text('users[username]', null, ['class'=>'form-control', 'id'=>'usuario', 'placeholder'=>'Usuario', 'maxlength'=>'30', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('username', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre de usuario que usará para acceder al sistema</p>
            </div>
        </div>
        <!-- / .form-group -->
        <div class="form-group">
            <label for="password" class="col-sm-2 control-label">Contraseña</label>
            <div class="col-sm-10">
                {!! Form::password('users[password]', ['type'=>'password','class'=>'form-control', 'id'=>'password', 'placeholder'=>'******']) !!}
                {!! $errors->first('password', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Mínimo seis caracteres alfanuméricos</p>
            </div>
        </div>
        <!-- / .form-group -->
        <div class="form-group">
            <label for="password-confirmation" class="col-sm-2 control-label">Confirmar</label>
            <div class="col-sm-10">
                {!! Form::password('users[password_confirmation]', ['class'=>'form-control', 'id'=>'password_confirmation', 'placeholder'=>'******']) !!}
                {!! $errors->first('password_confirmation', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Confirme la contraseña</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">

        <div class="form-group">
            <label for="titulo" class="col-sm-2 control-label">Título</label>
            <div class="col-sm-10">
                {!! Form::text('users[titulo]', null, ['class'=>'form-control', 'id'=>'titulo', 'placeholder'=>'Título', 'maxlength'=>'10','size'=>'10']) !!}
                {!! $errors->first('titulo', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Título que ostenta la persona, Lic., Dr., Ing., etc.</p>
            </div>
        </div>

        <div class="form-group">
            <label for="nombre" class="col-sm-2 control-label">Nombre</label>
            <div class="col-sm-10">
                {!! Form::text('users[nombre]', null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Nombre']) !!}
                {!! $errors->first('nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Nombre de la persona a la que pertenece esta cuenta</p>
            </div>
        </div>

        <div class="form-group">
            <label for="apepat" class="col-sm-2 control-label">Apellido paterno</label>
            <div class="col-sm-10">
                {!! Form::text('users[apepat]', null, ['class'=>'form-control', 'id'=>'apepat', 'placeholder'=>'Apellido paterno']) !!}
                {!! $errors->first('apepat', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Apellido paterno de la person a la que pertenece esta cuenta</p>
            </div>
        </div>

        <div class="form-group">
            <label for="apepat" class="col-sm-2 control-label">Apellido materno</label>
            <div class="col-sm-10">
                {!! Form::text('users[apemat]', null, ['class'=>'form-control', 'id'=>'apemat', 'placeholder'=>'Apellido materno']) !!}
                {!! $errors->first('apemat', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Apellido materno de la person a la que pertenece esta cuenta</p>
            </div>
        </div>

    </div>

</div>


<hr/>



<div class="form-group">
    <label class="col-sm-2 control-label">Rol</label>
    <div class="col-sm-10">
        @foreach($roles as $rol)
            <div class="radio">
                <label>
                    {!! Form::radio('users[rol_id]', $rol->id, isset($user) ? $user->roles->contains($rol->id) : null, ['class'=>'px']) !!}
                    <span class="lbl">{{$rol->display_name}} - {{$rol->description}}</span>
                </label>
            </div> <!-- / .radio -->
        @endforeach
        {!! $errors->first('rol_id', '<span class=text-danger>:message</span>') !!}
    </div> <!-- / .col-sm-10 -->
</div> <!-- / .form-group -->


<div class="form-group">
    <label for="apepat" class="col-sm-2 control-label">Estatus</label>
    <div class="col-sm-10">
        <div class="radio">
            <label>
                {!! Form::radio('users[activo]', 1, isset($user) ? $user->activo : null, ['class'=>'px']) !!}
                <span class="lbl">Activo</span>
            </label>
        </div> <!-- / .radio -->
        <div class="radio">
            <label>
                {!! Form::radio('users[activo]', 0, isset($user) ? $user->activo : 1, ['class'=>'px']) !!}
                <span class="lbl">Inactivo</span>
            </label>
        </div> <!-- / .radio -->

        {!! $errors->first('users.activo', '<span class=text-danger>:message</span>') !!}
        <p class="help-block">Indica si la cuenta del usuario está activa o inactiva</p>
    </div>
</div>

<div class="form-group">
    <label for="rotable" class="col-sm-2 control-label">Rotable</label>
    <div class="col-sm-10">
        <div class="radio">
            <label>
                {!! Form::radio('users[rotable]', 0, isset($user) ? $user->rotable: 0, ['class'=>'px']) !!}
                <span class="lbl">Fijo</span>
            </label>
        </div> <!-- / .radio -->
        <div class="radio">
            <label>
                {!! Form::radio('users[rotable]', 1, isset($user) ? $user->rotable : 1, ['class'=>'px']) !!}
                <span class="lbl">Rotador</span>
            </label>
        </div> <!-- / .radio -->

        {!! $errors->first('users.activo', '<span class=text-danger>:message</span>') !!}
        <p class="help-block">Indica si la cuenta del usuario está activa o inactiva</p>
    </div>
</div>


<div class="form-group">
    <label for="password" class="col-sm-2 control-label">Junta</label>
    <div class="col-sm-10">

        {!! Form::select('users[junta_id]', [null => '']+$juntas->toArray(), null, ['class'=>'form-control', 'id'=>'password', 'placeholder'=>'Contraseña']) !!}
        <p class="help-block">Junta especial a la que pertenece el usuario</p>
    </div>
</div>

<div class="form-group">
    <label for="region" class="col-sm-2 control-label">Región</label>
    <div class="col-sm-10">

        {!! Form::select('users[region]', [null => '',
        1=>1,
        2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,
        11=>11,12=>12,13=>13,14=>14,15=>15], null, ['class'=>'form-control', 'id'=>'region', 'placeholder'=>'Región']) !!}
        <p class="help-block">Región asignada al usuario</p>
    </div>
</div>
<!-- / .form-group -->

