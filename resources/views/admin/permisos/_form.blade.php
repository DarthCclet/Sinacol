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
    <div class="col-md-offset-3 col-md-6 ">
        <div class="form-group">
            <label for="description" class="col-sm-3 control-label">Ruta</label>
            <div class="col-sm-10">
                {!! Form::text('ruta', isset($permiso) ? $permiso->ruta : null, ['class'=>'form-control', 'id'=>'ruta', 'placeholder'=>'Ruta del fomulario', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('permission.description', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Cual es la ruta del formulario al que de da el permiso</p>
            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-6">
        <div class="form-group">
            <label class="col-sm-3 col-form-label" for="padre_id">Formulario padre</label>
            <div class="col-sm-10">
                <select class="multiple-select2 form-control" name="padre_id" id="padre_id">
                    <option value=""> -- Selecciona el formulario padre</option>
                    @foreach($permisos as $per)
                        @if(isset($permission))
                            @if($per->id == $permission->padre_id)
                                <option value="{{$per->id}}" selected="selected">{{$per->name}}</option>
                            @else
                                <option value="{{$per->id}}">{{$per->name}}</option>
                            @endif
                        @else
                            <option value="{{$per->id}}">{{$per->name}}</option>
                        @endif
                    @endforeach
                </select>
                {!! $errors->first('permission.padre_id', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Cual es el fomulario Padre</p>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function(){
        $("#padre_id").select2();
    });
</script>
@endpush