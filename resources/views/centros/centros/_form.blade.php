<div class="row">
    <div class=" col-md-4 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Nombre del centro</label>
            {!! Form::text('centro[nombre]', isset($centro) ? $centro->nombre : null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Centro', 'maxlength'=>'30', 'size'=>'10', 'autofocus'=>true]) !!}
            {!! $errors->first('centro.nombre', '<span class=text-danger>:message</span>') !!}
            <p class="help-block">Es el nombre con el que se identificará el centro</p>
        </div>
    </div>
    <div class=" col-md-4 ">
        <div class="form-group">
            <label for="abreviatura" class="control-label">Abreviatura</label>
            {!! Form::text('centro[abreviatura]', isset($centro) ? $centro->abreviatura : null, ['class'=>'form-control', 'id'=>'abreviatura', 'placeholder'=>'Abreviatura', 'maxlength'=>'30', 'size'=>'10', 'autofocus'=>true]) !!}
            {!! $errors->first('centro.abreviatura', '<span class=text-danger>:message</span>') !!}
            <p class="help-block">Son las siglas con las que se identifica el centro</p>
        </div>
    </div>
    <div class=" col-md-4 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Duración de la audiencia</label>
            {!! Form::text('centro[duracionAudiencia]', isset($centro) ? $centro->duracionAudiencia : null, ['class'=>'form-control', 'id'=>'duracionAudiencia', 'placeholder'=>'Duración', 'maxlength'=>'30', 'size'=>'10', 'autofocus'=>true]) !!}
            {!! $errors->first('centro.duracionAudiencia', '<span class=text-danger>:message</span>') !!}
            <p class="help-block">Es la duración promedio de una audiencia</p>
        </div>
    </div>
    <div class=" col-md-4 ">
        <div class="form-group">
            <label for="centro[sedes_multiples]" class="control-label">Multiples sedes</label>
            <input type="checkbox" {{isset($centro) && $centro->sedes_multiples ? 'checked' : ''}} value="true" data-render="switchery" data-theme="default" id="centro[sedes_multiples]" name='centro[sedes_multiples]'/>
            {!! $errors->first('centro.sedes_multiples', '<span class=text-danger>:message</span>') !!}
            <p class="help-block">Es la duración promedio de una audiencia</p>
        </div>
    </div>
    <div class=" col-md-4 ">
        <div class="form-group">
            <label for="centro[tipo_atencion_centro_id]" class="control-label">Atencion centro</label>
            {!! Form::select('centro[tipo_atencion_centro_id]', isset($tipo_atencion_centro) ? $tipo_atencion_centro  : [] , isset($centro) && $centro->tipo_atencion_centro_id ? $centro->tipo_atencion_centro_id : null, ['id'=>'tipo_atencion_centro_id','required','placeholder' => 'Seleccione una opción', 'class' => 'form-control']);  !!}
            {!! $errors->first('centro.tipo_atencion_centro_id', '<span class=text-danger>:message</span>') !!}
            <p class="help-block">Tipo de atenci&oacute;n del centro</p>
        </div>
    </div>
    @include('includes.component.map',['identificador' => '', 'instancia' => '2','domicilio'=>isset($centro->domicilio) ? $centro->domicilio : null,'needsMaps'=>"true"])
</div>
