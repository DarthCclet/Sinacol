<div class="row">

    <div class="col-md-6">

        <div class="form-group">
            <label for="usuario" class="control-label">Usuario</label>
            <div class="col-sm-10">
                {!! Form::text('users[name]', isset($user) ? $user->name : null, ['class'=>'form-control', 'id'=>'usuario', 'placeholder'=>'Usuario', 'maxlength'=>'30', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('users.name', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre de usuario que usará para acceder al sistema</p>
            </div>
        </div>

        <div class="form-group">
            <label for="email" class="control-label">Email</label>
            <div class="col-sm-10">
                {!! Form::text('users[email]', isset($user) ? $user->email : null, ['class'=>'form-control', 'id'=>'email', 'placeholder'=>'Email', 'maxlength'=>'100', 'size'=>'60',]) !!}
                {!! $errors->first('users.email', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el email de contacto del usuario.</p>
            </div>
        </div>
        <!-- / .form-group -->
        <div class="form-group">
            <label for="password" class="control-label">Contraseña</label>
            <div class="col-sm-10">
                {!! Form::password('users[password]', ['type'=>'password','class'=>'form-control', 'id'=>'password', 'placeholder'=>'']) !!}
                {!! $errors->first('users.password', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Mínimo seis caracteres alfanuméricos</p>
            </div>
        </div>
        <!-- / .form-group -->
        <div class="form-group">
            <label for="password-confirmation" class="control-label">Confirmar</label>
            <div class="col-sm-10">
                {!! Form::password('users[password_confirmation]', ['class'=>'form-control', 'id'=>'password_confirmation', 'placeholder'=>'']) !!}
                {!! $errors->first('users[password_confirmation]', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Confirme la contraseña</p>
            </div>
        </div>
        @if(auth()->user()->hasRole('Super Usuario'))
        <div class="form-group">
            <label for="centro_id" class="control-label">Centro</label>
            <div class="col-sm-10">
                {!! Form::select('users[centro_id]', isset($centros) ? $centros : [] ,isset($user) ? $user->centro_id : null , ['id'=>'centro_id', 'required','placeholder' => 'Seleccione una opción', 'class' => 'form-control']);  !!}
                {!! $errors->first('users[centro_id]', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Selecciona el centro al que pertenece</p>
            </div>
        </div>
        @else
        {!! Form::hidden('users[centro_id]',auth()->user()->centro_id ,[]) !!}
        @endif
    </div>

    <div class="col-md-6">

        <div class="form-group">
            <label for="nombre" class="control-label">Nombre</label>
            <div class="col-sm-10">
                {!! Form::text('personas[nombre]', isset($user->persona) ? $user->persona->nombre : null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Nombre']) !!}
                {!! $errors->first('personas.nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Nombre de la persona a la que pertenece esta cuenta</p>
            </div>
        </div>

        <div class="form-group">
            <label for="apepat" class="control-label">Primer Apellido</label>
            <div class="col-sm-10">
                {!! Form::text('personas[primer_apellido]', isset($user->persona) ? $user->persona->primer_apellido : null, ['class'=>'form-control', 'id'=>'primer_apellido', 'placeholder'=>'Primer apellido']) !!}
                {!! $errors->first('personas.primer_apellido', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Primer Apellido de la person a la que pertenece esta cuenta</p>
            </div>
        </div>

        <div class="form-group">
            <label for="apepat" class="control-label">Segundo Apellido</label>
            <div class="col-sm-10">
                {!! Form::text('personas[segundo_apellido]', isset($user->persona) ? $user->persona->segundo_apellido : null, ['class'=>'form-control', 'id'=>'segundo_apellido', 'placeholder'=>'Segundo apellido']) !!}
                {!! $errors->first('personas.segundo_apellido', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Segundo Apellido de la person a la que pertenece esta cuenta</p>
            </div>
        </div>
        <div class="form-group">
            <label for="apepat" class="control-label">CURP</label>
            <div class="col-sm-10">
                {!! Form::text('personas[curp]', isset($user->persona) ? $user->persona->curp : null, ['class'=>'form-control', 'id'=>'curp', 'placeholder'=>'CURP']) !!}
                {!! $errors->first('personas.curp', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Clave única de registro de población de la person a la que pertenece esta cuenta</p>
            </div>
        </div>
    </div>
</div>

<!-- / .form-group -->
@push('scripts')
    <script>
        $(document).ready(function(){
            $("#centro_id").select2({ placeholder: "Selecciona un centro" });
        });
    </script>
@endpush

